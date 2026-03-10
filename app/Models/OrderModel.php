<?php
class OrderModel extends BaseModel
{

    // --- PHẦN 1: MUA HÀNG (USER) ---

    // Lưu đơn hàng (Sử dụng Transaction)
    public function createOrder($userId, $orderData, $cartItems)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Insert bảng DON_HANG
            $sqlOrder = "INSERT INTO DON_HANG (MA_NGUOI_DUNG, TEN_NGUOI_NHAN, SDT_NGUOI_NHAN, DIA_CHI_GIAO_HANG, TONG_TIEN, TRANG_THAI) 
                         VALUES (:uid, :ten, :sdt, :diachi, :tongtien, 'cho_xac_nhan')";

            $stmt = $this->conn->prepare($sqlOrder);
            $stmt->execute([
                ':uid' => $userId,
                ':ten' => $orderData['ten_nguoi_nhan'],
                ':sdt' => $orderData['sdt_nguoi_nhan'],
                ':diachi' => $orderData['dia_chi'],
                ':tongtien' => $orderData['tong_tien']
            ]);

            $orderId = $this->conn->lastInsertId();

            // 2. Insert bảng CHI_TIET_DON_HANG và Trừ tồn kho
            $sqlDetail = "INSERT INTO CHI_TIET_DON_HANG (MA_DON_HANG, MA_SAN_PHAM, SO_LUONG, DON_GIA) 
                          VALUES (:madon, :masp, :soluong, :dongia)";

            $sqlUpdateStock = "UPDATE SAN_PHAM SET SO_LUONG_TON = SO_LUONG_TON - :soluong 
                               WHERE MA_SAN_PHAM = :masp";

            $stmtDetail = $this->conn->prepare($sqlDetail);
            $stmtStock = $this->conn->prepare($sqlUpdateStock);

            foreach ($cartItems as $item) {
                $stmtDetail->execute([
                    ':madon' => $orderId,
                    ':masp' => $item['MA_SAN_PHAM'],
                    ':soluong' => $item['buy_qty'],
                    ':dongia' => $item['display_price']
                ]);

                $stmtStock->execute([
                    ':soluong' => $item['buy_qty'],
                    ':masp' => $item['MA_SAN_PHAM']
                ]);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Lấy đơn hàng của người dùng
    public function getOrdersByUserId($userId)
    {
        $sql = "SELECT * FROM DON_HANG WHERE MA_NGUOI_DUNG = :uid ORDER BY MA_DON_HANG DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- PHẦN 2: QUẢN LÝ ĐƠN HÀNG (ADMIN) ---

    public function getAllOrders($keyword = null)
    {
        $sql = "SELECT * FROM DON_HANG";
        $params = [];

        if (!empty($keyword)) {
            // Tìm theo Mã đơn OR Tên người nhận OR SĐT
            $sql .= " WHERE MA_DON_HANG = :id OR TEN_NGUOI_NHAN LIKE :name OR SDT_NGUOI_NHAN LIKE :sdt";
            $params[':id'] = intval($keyword);
            $params[':name'] = "%$keyword%";
            $params[':sdt'] = "%$keyword%";
        }

        $sql .= " ORDER BY MA_DON_HANG DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id)
    {
        $sql = "SELECT * FROM DON_HANG WHERE MA_DON_HANG = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($orderId)
    {
        $sql = "SELECT c.*, s.TEN_SAN_PHAM, s.HINH_ANH 
                FROM CHI_TIET_DON_HANG c
                JOIN SAN_PHAM s ON c.MA_SAN_PHAM = s.MA_SAN_PHAM
                WHERE c.MA_DON_HANG = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- CẬP NHẬT: Hàm đổi trạng thái & Hoàn kho tự động ---
    public function updateStatus($orderId, $newStatus)
    {
        try {
            // 1. Lấy trạng thái hiện tại trong DB
            $currentOrder = $this->getOrderById($orderId);
            $oldStatus = $currentOrder['TRANG_THAI'];

            // 2. [LOGIC CHẶN]: Nếu đơn đã "Hủy" hoặc "Đã giao" thì KHÔNG ĐƯỢC sửa nữa
            if ($oldStatus == 'da_huy' || $oldStatus == 'da_giao') {
                return 'locked'; // Trả về mã lỗi riêng để Controller biết
            }

            // 3. Nếu trạng thái hợp lệ, bắt đầu giao dịch
            $this->conn->beginTransaction();

            // Cập nhật trạng thái mới
            $sql = "UPDATE DON_HANG SET TRANG_THAI = :status WHERE MA_DON_HANG = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $orderId, ':status' => $newStatus]);

            // 4. Nếu trạng thái MỚI là "Hủy" -> Hoàn kho
            if ($newStatus == 'da_huy') {
                $details = $this->getOrderDetails($orderId);
                $sqlRestore = "UPDATE SAN_PHAM SET SO_LUONG_TON = SO_LUONG_TON + :qty WHERE MA_SAN_PHAM = :pid";
                $stmtRestore = $this->conn->prepare($sqlRestore);

                foreach ($details as $item) {
                    $stmtRestore->execute([
                        ':qty' => $item['SO_LUONG'],
                        ':pid' => $item['MA_SAN_PHAM']
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // --- PHẦN 3: THỐNG KÊ DASHBOARD (ADMIN) ---

    // Tính tổng doanh thu (Chỉ tính đơn đã giao)
    public function getRevenue($fromDate = null, $toDate = null)
    {
        // Giả sử cột tổng tiền là TONG_TIEN và bảng là DON_HANG (bạn tự chỉnh nếu tên khác)
        // TRANG_THAI = 3 là ví dụ cho đơn hàng thành công
        $sql = "SELECT SUM(TONG_TIEN) as total FROM DON_HANG WHERE TRANG_THAI = 3";
        $params = [];

        // TRƯỜNG HỢP 1: Có chọn ngày bắt đầu và kết thúc
        if (!empty($fromDate) && !empty($toDate)) {
            // Thay NGAY_TAO bằng NGAY_GIO
            $sql .= " AND NGAY_GIO >= :from AND NGAY_GIO <= :to";

            // Thêm giờ để lấy trọn vẹn ngày
            $params[':from'] = $fromDate . ' 00:00:00';
            $params[':to']   = $toDate . ' 23:59:59';
        }
        // TRƯỜNG HỢP 2: Mặc định (30 ngày gần nhất)
        else {
            // Thay NGAY_TAO bằng NGAY_GIO
            $sql .= " AND NGAY_GIO >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ? $result['total'] : 0;
    }

    // Đếm tổng số đơn hàng
    public function countOrders($fromDate = null, $toDate = null)
    {
        $sql = "SELECT COUNT(*) as total FROM DON_HANG WHERE 1=1";
        $params = [];

        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND NGAY_GIO >= :from AND NGAY_GIO <= :to";
            $params[':from'] = $fromDate . ' 00:00:00';
            $params[':to']   = $toDate . ' 23:59:59';
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Lấy 5 đơn hàng mới nhất
    public function getRecentOrders($limit = 5, $fromDate = null, $toDate = null)
    {
        $sql = "SELECT * FROM DON_HANG WHERE 1=1";
        $params = [];

        // Thêm điều kiện lọc ngày
        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND NGAY_GIO >= :from AND NGAY_GIO <= :to";
            $params[':from'] = $fromDate . ' 00:00:00';
            $params[':to']   = $toDate . ' 23:59:59';
        }

        // Sắp xếp và giới hạn
        // Lưu ý: Ép kiểu (int)$limit để an toàn khi nối chuỗi
        $sql .= " ORDER BY MA_DON_HANG DESC LIMIT " . (int)$limit;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dữ liệu biểu đồ doanh thu theo ngày
    public function getRevenueChartData($fromDate = null, $toDate = null)
    {
        $sql = "SELECT DATE(NGAY_GIO) as date, SUM(TONG_TIEN) as total 
            FROM DON_HANG 
            WHERE TRANG_THAI = 'da_giao'"; // Hoặc trạng thái thành công của bạn

        $params = [];

        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND NGAY_GIO >= :from AND NGAY_GIO <= :to";
            $params[':from'] = $fromDate . ' 00:00:00';
            $params[':to']   = $toDate . ' 23:59:59';
        } else {
            // Mặc định 30 ngày nếu không chọn
            $sql .= " AND NGAY_GIO >= DATE(NOW()) - INTERVAL 30 DAY";
        }

        $sql .= " GROUP BY DATE(NGAY_GIO) ORDER BY date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top khách hàng mua nhiều nhất
    public function getTopCustomers($limit = 5, $fromDate = null, $toDate = null)
    {
        $sql = "SELECT u.TEN, u.EMAIL, SUM(dh.TONG_TIEN) as total_spent
            FROM DON_HANG dh
            JOIN NGUOI_DUNG u ON dh.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG
            WHERE dh.TRANG_THAI = 'da_giao'"; // Hoặc trạng thái thành công

        $params = [];

        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND dh.NGAY_GIO >= :from AND dh.NGAY_GIO <= :to";
            $params[':from'] = $fromDate . ' 00:00:00';
            $params[':to']   = $toDate . ' 23:59:59';
        }

        $sql .= " GROUP BY dh.MA_NGUOI_DUNG ORDER BY total_spent DESC LIMIT $limit";

        $stmt = $this->conn->prepare($sql);
        // Bind cứng limit vì PDO đôi khi lỗi limit string
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        // Riêng LIMIT phải bind số nguyên
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        // Vì bindValue phức tạp, ta dùng cách query string đơn giản cho LIMIT ở đây:
        // (Sửa lại đoạn execute cho an toàn và đơn giản hơn):
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- MỚI: Xóa vĩnh viễn đơn hàng ---
    public function deleteOrder($id)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Xóa chi tiết đơn hàng trước
            $sqlDetail = "DELETE FROM CHI_TIET_DON_HANG WHERE MA_DON_HANG = :id";
            $stmt1 = $this->conn->prepare($sqlDetail);
            $stmt1->execute([':id' => $id]);

            // 2. Xóa đơn hàng chính
            $sqlOrder = "DELETE FROM DON_HANG WHERE MA_DON_HANG = :id";
            $stmt2 = $this->conn->prepare($sqlOrder);
            $stmt2->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
