<?php
class CommentModel extends BaseModel
{
    const TABLE = 'BINH_LUAN';

    // Lấy bình luận của một bài viết
    public function getCommentsByNews($newsId)
    {
        $sql = "SELECT bl.*, u.TEN 
                FROM " . self::TABLE . " bl
                JOIN NGUOI_DUNG u ON bl.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG
                WHERE bl.MA_TIN_TUC = :nid AND bl.TRANG_THAI = 1
                ORDER BY bl.NGAY_BINH_LUAN DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':nid' => $newsId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm bình luận mới
    public function addComment($data)
    {
        $sql = "INSERT INTO " . self::TABLE . " (NOI_DUNG, MA_TIN_TUC, MA_NGUOI_DUNG) 
                VALUES (:noidung, :matin, :mauid)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    // Đếm số bình luận của bài viết
    public function countComments($newsId)
    {
        $sql = "SELECT COUNT(*) as total FROM " . self::TABLE . " WHERE MA_TIN_TUC = :nid AND TRANG_THAI = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':nid' => $newsId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // --- MỚI: Lấy tất cả bình luận tin tức (Cho Admin) ---
    public function getAllComments($keyword = null)
    {
        $sql = "SELECT bl.*, u.TEN, t.TIEU_DE 
                FROM " . self::TABLE . " bl
                JOIN NGUOI_DUNG u ON bl.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG
                JOIN TIN_TUC t ON bl.MA_TIN_TUC = t.MA_TIN_TUC";

        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE t.TIEU_DE LIKE :kw OR u.TEN LIKE :kw OR bl.NOI_DUNG LIKE :kw";
            $params[':kw'] = "%$keyword%";
        }

        $sql .= " ORDER BY bl.NGAY_BINH_LUAN DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- MỚI: Xóa bình luận tin tức ---
    public function deleteComment($id)
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE MA_BINH_LUAN = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    /// chức năng user
    // --- MỚI: Lấy thông tin 1 bình luận (Để kiểm tra quyền) ---
    public function getCommentById($id)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE MA_BINH_LUAN = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- MỚI: Người dùng cập nhật bình luận của chính mình ---
    public function updateComment($id, $userId, $content)
    {
        $sql = "UPDATE " . self::TABLE . " 
                SET NOI_DUNG = :content 
                WHERE MA_BINH_LUAN = :id AND MA_NGUOI_DUNG = :uid";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':content' => $content,
            ':id' => $id,
            ':uid' => $userId
        ]);
    }

    // --- MỚI: Người dùng xóa bình luận của chính mình ---
    public function deleteCommentByUser($id, $userId)
    {
        $sql = "DELETE FROM " . self::TABLE . " 
                WHERE MA_BINH_LUAN = :id AND MA_NGUOI_DUNG = :uid";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id, ':uid' => $userId]);
    }
}
