<?php
class ReviewModel extends BaseModel
{
    const TABLE = 'DANH_GIA';

    // Lấy đánh giá theo ID sản phẩm (kèm tên người đánh giá)
    public function getReviewsByProduct($productId)
    {
        $sql = "SELECT d.*, u.TEN 
                FROM " . self::TABLE . " d
                JOIN NGUOI_DUNG u ON d.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG
                WHERE d.MA_SAN_PHAM = :pid
                ORDER BY d.NGAY_GIO DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':pid' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm đánh giá mới
    public function addReview($data)
    {
        $sql = "INSERT INTO " . self::TABLE . " (NOI_DUNG, SO_SAO, MA_SAN_PHAM, MA_NGUOI_DUNG) 
                VALUES (:noidung, :sosao, :masp, :mauid)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Lấy tất cả đánh giá (Cho Admin)
    public function getAllReviews($keyword = null)
    {
        $sql = "SELECT d.*, u.TEN as NGUOI_DUNG, s.TEN_SAN_PHAM 
                FROM " . self::TABLE . " d
                JOIN NGUOI_DUNG u ON d.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG
                JOIN SAN_PHAM s ON d.MA_SAN_PHAM = s.MA_SAN_PHAM";

        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE s.TEN_SAN_PHAM LIKE :kw OR u.TEN LIKE :kw OR d.NOI_DUNG LIKE :kw";
            $params[':kw'] = "%$keyword%";
        }

        $sql .= " ORDER BY d.NGAY_GIO DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa đánh giá
    public function deleteReview($id)
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE MA_DANH_GIA = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Kiểm tra xem người dùng đã mua sản phẩm này chưa (Logic nâng cao)
    // Theo UseCase: "Đánh giá cho sản phẩm đã mua" [cite: 309]
    public function checkPurchased($userId, $productId)
    {
        $sql = "SELECT * FROM DON_HANG dh
                JOIN CHI_TIET_DON_HANG ct ON dh.MA_DON_HANG = ct.MA_DON_HANG
                WHERE dh.MA_NGUOI_DUNG = :uid 
                AND ct.MA_SAN_PHAM = :pid 
                AND dh.TRANG_THAI = 'da_giao'"; // Chỉ cho đánh giá khi đã giao hàng thành công

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $userId, ':pid' => $productId]);
        return $stmt->rowCount() > 0;
    }

    // --- MỚI: Người dùng xóa đánh giá của chính mình ---
    public function deleteReviewByUser($id, $userId)
    {
        $sql = "DELETE FROM " . self::TABLE . " 
                WHERE MA_DANH_GIA = :id AND MA_NGUOI_DUNG = :uid";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id, ':uid' => $userId]);
    }

    // --- MỚI: Người dùng sửa đánh giá (Nội dung + Số sao) ---
    public function updateReview($id, $userId, $content, $rating)
    {
        $sql = "UPDATE " . self::TABLE . " 
                SET NOI_DUNG = :content, SO_SAO = :rating
                WHERE MA_DANH_GIA = :id AND MA_NGUOI_DUNG = :uid";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':content' => $content,
            ':rating' => $rating,
            ':id' => $id,
            ':uid' => $userId
        ]);
    }
}
