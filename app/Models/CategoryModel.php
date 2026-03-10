<?php
class CategoryModel extends BaseModel
{
    const TABLE = 'DANH_MUC';

    // --- CẬP NHẬT: Lấy danh mục KÈM SỐ LƯỢNG SẢN PHẨM ---
    public function getAllCategories($keyword = null)
    {
        $params = [];

        // Sử dụng LEFT JOIN và GROUP BY để đếm sản phẩm
        $sql = "SELECT c.*, COUNT(p.MA_SAN_PHAM) as SO_LUONG_SP 
                FROM " . self::TABLE . " c
                LEFT JOIN SAN_PHAM p ON c.MA_DANH_MUC = p.MA_DANH_MUC";

        if (!empty($keyword)) {
            $sql .= " WHERE c.TEN_DANH_MUC LIKE :kw";
            $params[':kw'] = "%$keyword%";
        }

        $sql .= " GROUP BY c.MA_DANH_MUC ORDER BY c.MA_DANH_MUC DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE MA_DANH_MUC = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name, $image)
    {
        $sql = "INSERT INTO " . self::TABLE . " (TEN_DANH_MUC, HINH_ANH) VALUES (:name, :img)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':name' => $name, ':img' => $image]);
    }

    public function updateCategory($id, $name, $image = null)
    {
        $sql = "UPDATE " . self::TABLE . " SET TEN_DANH_MUC = :name";
        $params = [':id' => $id, ':name' => $name];

        if (!empty($image)) {
            $sql .= ", HINH_ANH = :img";
            $params[':img'] = $image;
        }

        $sql .= " WHERE MA_DANH_MUC = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // --- CẬP NHẬT: Xóa danh mục (Bắt lỗi khóa ngoại) ---
    public function deleteCategory($id)
    {
        try {
            $sql = "DELETE FROM " . self::TABLE . " WHERE MA_DANH_MUC = :id";
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
}
