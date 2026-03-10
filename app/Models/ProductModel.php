<?php
class ProductModel extends BaseModel
{
    const TABLE = 'SAN_PHAM';

    // =========================================================================
    // ADMIN (QUẢN TRỊ) - QUẢN LÝ TOÀN BỘ SẢN PHẨM
    // =========================================================================

    // Lấy tất cả sản phẩm (Bao gồm cả ẩn và hiện để Admin quản lý)
    public function getAllProducts($keyword = null)
    {
        $sql = "SELECT p.*, c.TEN_DANH_MUC 
                FROM " . self::TABLE . " p 
                LEFT JOIN DANH_MUC c ON p.MA_DANH_MUC = c.MA_DANH_MUC";

        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE p.TEN_SAN_PHAM LIKE :kw OR p.MA_SAN_PHAM = :id";
            $params[':kw'] = "%$keyword%";
            $params[':id'] = intval($keyword);
        }

        $sql .= " ORDER BY p.MA_SAN_PHAM DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm sản phẩm mới
    public function addProduct($data)
    {
        $sql = "INSERT INTO " . self::TABLE . " (TEN_SAN_PHAM, MA_DANH_MUC, GIA, GIAM_GIA, SO_LUONG_TON, HINH_ANH, GALLERY, MO_TA, NOI_BAT, TRANG_THAI) 
                VALUES (:ten, :dm, :gia, :giam_gia, :sl, :hinh, :gallery, :mota, :noibat, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Lấy thông tin 1 sản phẩm theo ID (Dùng cho cả Admin sửa và User xem chi tiết)
    public function getProductById($id)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE MA_SAN_PHAM = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật thông tin sản phẩm
    public function updateProduct($id, $data)
    {
        $sql = "UPDATE " . self::TABLE . " 
                SET TEN_SAN_PHAM = :ten, MA_DANH_MUC = :dm, GIA = :gia, 
                    GIAM_GIA = :giam_gia, SO_LUONG_TON = :sl, 
                    MO_TA = :mota, NOI_BAT = :noibat";

        if (!empty($data[':hinh'])) {
            $sql .= ", HINH_ANH = :hinh";
        }

        if (!empty($data[':gallery'])) {
            $sql .= ", GALLERY = :gallery";
        }

        $sql .= " WHERE MA_SAN_PHAM = :id";

        // Thêm ID vào mảng data
        $data[':id'] = $id;

        // Loại bỏ key không dùng nếu rỗng để tránh lỗi bind param
        if (empty($data[':hinh'])) unset($data[':hinh']);
        if (empty($data[':gallery'])) unset($data[':gallery']);

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Xóa sản phẩm (Có bắt lỗi khóa ngoại Foreign Key)
    public function deleteProduct($id)
    {
        try {
            $sql = "DELETE FROM " . self::TABLE . " WHERE MA_SAN_PHAM = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            // Mã lỗi 23000: Integrity constraint violation (Khóa ngoại)
            if ($e->getCode() == '23000') {
                return 'locked';
            }
            throw $e;
        }
    }

    // Đổi trạng thái Ẩn/Hiện (Ngừng kinh doanh)
    public function toggleStatus($id, $status)
    {
        $sql = "UPDATE " . self::TABLE . " SET TRANG_THAI = :status WHERE MA_SAN_PHAM = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // Lấy danh sách danh mục (Hỗ trợ dropdown chọn danh mục)
    public function getCategories()
    {
        return $this->all('DANH_MUC');
    }

    // =========================================================================
    // USER (KHÁCH HÀNG) - CHỈ HIỆN SẢN PHẨM ĐANG BÁN (TRANG_THAI = 1)
    // =========================================================================

    // Lấy sản phẩm nổi bật (Cho trang chủ)
    public function getFeaturedProducts($limit = 4)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE NOI_BAT = 1 AND TRANG_THAI = 1 LIMIT $limit";
        return $this->query($sql);
    }

    // Lấy sản phẩm mới nhất (Cho trang chủ)
    public function getNewProducts($limit = 8)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE TRANG_THAI = 1 ORDER BY MA_SAN_PHAM DESC LIMIT $limit";
        return $this->query($sql);
    }

    // Lấy sản phẩm liên quan (Cùng danh mục)
    public function getRelatedProducts($categoryId, $excludeId, $limit = 4)
    {
        $sql = "SELECT * FROM " . self::TABLE . " 
                WHERE MA_DANH_MUC = :dm AND MA_SAN_PHAM != :id AND TRANG_THAI = 1
                LIMIT $limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':dm' => $categoryId, ':id' => $excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lọc sản phẩm đa năng (Danh mục + Từ khóa + Giá + Phân trang)
    public function filterProducts($categoryId = null, $keyword = null, $minPrice = null, $maxPrice = null, $limit = 12, $offset = 0)
    {
        // Luôn có điều kiện TRANG_THAI = 1
        $sql = "SELECT * FROM " . self::TABLE . " WHERE TRANG_THAI = 1";
        $params = [];

        if (!empty($categoryId)) {
            $sql .= " AND MA_DANH_MUC = :cat";
            $params[':cat'] = $categoryId;
        }
        if (!empty($keyword)) {
            $sql .= " AND TEN_SAN_PHAM LIKE :kw";
            $params[':kw'] = "%$keyword%";
        }
        if (!empty($minPrice)) {
            $sql .= " AND GIA >= :min";
            $params[':min'] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $sql .= " AND GIA <= :max";
            $params[':max'] = $maxPrice;
        }

        $sql .= " ORDER BY MA_SAN_PHAM DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số sản phẩm sau khi lọc (Để tính số trang)
    public function countFilteredProducts($categoryId = null, $keyword = null, $minPrice = null, $maxPrice = null)
    {
        $sql = "SELECT COUNT(*) as total FROM " . self::TABLE . " WHERE TRANG_THAI = 1";
        $params = [];

        if (!empty($categoryId)) {
            $sql .= " AND MA_DANH_MUC = :cat";
            $params[':cat'] = $categoryId;
        }
        if (!empty($keyword)) {
            $sql .= " AND TEN_SAN_PHAM LIKE :kw";
            $params[':kw'] = "%$keyword%";
        }
        if (!empty($minPrice)) {
            $sql .= " AND GIA >= :min";
            $params[':min'] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $sql .= " AND GIA <= :max";
            $params[':max'] = $maxPrice;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Tìm kiếm nhanh (Cho gợi ý hoặc search đơn giản)
    public function searchProducts($keyword)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE TEN_SAN_PHAM LIKE :kw AND TRANG_THAI = 1 ORDER BY MA_SAN_PHAM DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':kw' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================================
    // PHẦN 3: DASHBOARD STATS (THỐNG KÊ)
    // =========================================================================

    // Đếm tổng sản phẩm trong kho (Không quan tâm ẩn hiện)
    public function countProducts()
    {
        $sql = "SELECT COUNT(*) as total FROM " . self::TABLE;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Top sản phẩm bán chạy
    public function getTopSellingProducts($limit = 5, $fromDate = null, $toDate = null)
    {
        // Cần JOIN với bảng DON_HANG để lọc theo NGAY_GIO
        $sql = "SELECT p.TEN_SAN_PHAM, p.HINH_ANH, SUM(ct.SO_LUONG) as total_sold
            FROM CHI_TIET_DON_HANG ct
            JOIN SAN_PHAM p ON ct.MA_SAN_PHAM = p.MA_SAN_PHAM
            JOIN DON_HANG dh ON ct.MA_DON_HANG = dh.MA_DON_HANG
            WHERE dh.TRANG_THAI = 'da_giao'"; // Chỉ tính đơn thành công

        $params = [];

        if (!empty($fromDate) && !empty($toDate)) {
            $sql .= " AND dh.NGAY_GIO >= :from AND dh.NGAY_GIO <= :to";
            $params[':from'] = $fromDate . ' 00:00:00';
            $params[':to']   = $toDate . ' 23:59:59';
        }

        $sql .= " GROUP BY ct.MA_SAN_PHAM ORDER BY total_sold DESC LIMIT $limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- Xóa ép buộc (Xóa cả lịch sử đơn hàng và đánh giá liên quan) ---
    // --- CẬP NHẬT: Xóa ép buộc (Xử lý sạch sẽ đơn hàng rỗng) ---
    public function forceDeleteProduct($id)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Tìm các đơn hàng bị ảnh hưởng trước khi xóa chi tiết
            $sqlGetOrders = "SELECT DISTINCT MA_DON_HANG FROM CHI_TIET_DON_HANG WHERE MA_SAN_PHAM = :id";
            $stmtOrders = $this->conn->prepare($sqlGetOrders);
            $stmtOrders->execute([':id' => $id]);
            $affectedOrders = $stmtOrders->fetchAll(PDO::FETCH_COLUMN); // Lấy danh sách ID đơn hàng

            // 2. Xóa đánh giá
            $sqlReview = "DELETE FROM DANH_GIA WHERE MA_SAN_PHAM = :id";
            $stmt1 = $this->conn->prepare($sqlReview);
            $stmt1->execute([':id' => $id]);

            // 3. Xóa chi tiết đơn hàng chứa sản phẩm này
            $sqlDetail = "DELETE FROM CHI_TIET_DON_HANG WHERE MA_SAN_PHAM = :id";
            $stmt2 = $this->conn->prepare($sqlDetail);
            $stmt2->execute([':id' => $id]);

            // 4. Xóa sản phẩm
            $sqlProduct = "DELETE FROM " . self::TABLE . " WHERE MA_SAN_PHAM = :id";
            $stmt3 = $this->conn->prepare($sqlProduct);
            $stmt3->execute([':id' => $id]);

            // 5. [QUAN TRỌNG] Dọn dẹp các đơn hàng bị ảnh hưởng
            if (!empty($affectedOrders)) {
                foreach ($affectedOrders as $orderId) {
                    // Đếm xem đơn hàng này còn sản phẩm nào khác không
                    $sqlCount = "SELECT COUNT(*) FROM CHI_TIET_DON_HANG WHERE MA_DON_HANG = :oid";
                    $stmtCount = $this->conn->prepare($sqlCount);
                    $stmtCount->execute([':oid' => $orderId]);
                    $count = $stmtCount->fetchColumn();

                    if ($count == 0) {
                        // A. Nếu đơn hàng rỗng -> Xóa luôn đơn hàng (DON_HANG)
                        $sqlDelOrder = "DELETE FROM DON_HANG WHERE MA_DON_HANG = :oid";
                        $stmtDelOrder = $this->conn->prepare($sqlDelOrder);
                        $stmtDelOrder->execute([':oid' => $orderId]);
                    } else {
                        // B. Nếu còn sản phẩm khác -> Tính lại tổng tiền cho đúng
                        $sqlUpdateTotal = "UPDATE DON_HANG 
                                           SET TONG_TIEN = (
                                               SELECT SUM(SO_LUONG * DON_GIA) 
                                               FROM CHI_TIET_DON_HANG 
                                               WHERE MA_DON_HANG = :oid
                                           ) 
                                           WHERE MA_DON_HANG = :oid";
                        $stmtUpdateTotal = $this->conn->prepare($sqlUpdateTotal);
                        $stmtUpdateTotal->execute([':oid' => $orderId]);
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
