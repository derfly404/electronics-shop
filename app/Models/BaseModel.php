<?php
class BaseModel extends Database
{
    // XÓA dòng 'protected $conn;' đi vì đã có sẵn bên Database rồi

    public function __construct()
    {
        // Gọi hàm kết nối từ class cha (Database)
        $this->getConnection();
    }

    // Hàm lấy tất cả dữ liệu
    public function all($table)
    {
        $sql = "SELECT * FROM $table";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hàm lấy 1 dòng dữ liệu theo ID
    // Giả sử bảng nào cũng có cột khóa chính là MA_...
    // Tạm thời ta dùng query tay ở Model con cho linh hoạt, hàm này để giữ khung
    public function find($table, $id)
    {
        return [];
    }

    // Thực thi câu lệnh SQL tùy ý (SELECT, INSERT, UPDATE, DELETE)
    public function query($sql)
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        // Nếu là SELECT thì trả về kết quả, còn INSERT/UPDATE thì trả về true/false nếu muốn
        // Ở đây mặc định trả về mảng cho các lệnh SELECT
        if (stripos($sql, 'SELECT') === 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return true;
    }
}
