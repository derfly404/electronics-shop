<?php
class NewsModel extends BaseModel
{
    const TABLE = 'TIN_TUC';

    // Lấy tất cả tin tức (Cho Admin quản lý)
    public function getAllNews($keyword = null)
    {
        $sql = "SELECT t.*, u.TEN as NGUOI_DANG 
                FROM " . self::TABLE . " t
                LEFT JOIN NGUOI_DUNG u ON t.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG";

        $params = [];
        if (!empty($keyword)) {
            $sql .= " WHERE t.TIEU_DE LIKE :kw OR u.TEN LIKE :kw";
            $params[':kw'] = "%$keyword%";
        }

        $sql .= " ORDER BY t.NGAY_DANG DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tin tức đang hiển thị (Cho User xem)
    public function getActiveNews($limit = 10)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE TRANG_THAI = 1 ORDER BY NGAY_DANG DESC LIMIT $limit";
        return $this->query($sql);
    }

    public function getNewsById($id)
    {
        $sql = "SELECT t.*, u.TEN as NGUOI_DANG 
                FROM " . self::TABLE . " t
                LEFT JOIN NGUOI_DUNG u ON t.MA_NGUOI_DUNG = u.MA_NGUOI_DUNG
                WHERE t.MA_TIN_TUC = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createNews($data)
    {
        $sql = "INSERT INTO " . self::TABLE . " (TIEU_DE, TOM_TAT, HINH_DAI_DIEN, NOI_DUNG, MA_NGUOI_DUNG, TRANG_THAI) 
                VALUES (:tieu_de, :tom_tat, :hinh, :noi_dung, :uid, :status)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function updateNews($id, $data)
    {
        $sql = "UPDATE " . self::TABLE . " 
                SET TIEU_DE = :tieu_de, TOM_TAT = :tom_tat, NOI_DUNG = :noi_dung, TRANG_THAI = :status";

        if (!empty($data[':hinh'])) {
            $sql .= ", HINH_DAI_DIEN = :hinh";
        }

        $sql .= " WHERE MA_TIN_TUC = :id";

        // Thêm ID vào data binding
        $data[':id'] = $id;

        if (empty($data[':hinh'])) {
            unset($data[':hinh']);
        }

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function deleteNews($id)
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE MA_TIN_TUC = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Tăng lượt xem
    public function increaseView($id)
    {
        $sql = "UPDATE " . self::TABLE . " SET LUOT_XEM = LUOT_XEM + 1 WHERE MA_TIN_TUC = :id";

        // Thay dòng $this->query($sql); bằng đoạn code dưới đây:
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
    }
}
