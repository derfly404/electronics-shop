<?php
class Database
{
    private $host = "localhost";
    private $db_name = "Electronics_shop1";
    private $username = "root";
    private $password = "123456";

    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", $this->username, $this->password);
            // QUAN TRỌNG: Thiết lập timezone cho MySQL
            $this->conn->exec("SET time_zone = '+07:00'"); // GMT+7 cho Việt Nam
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            error_log("Database connected with timezone +07:00");
        } catch (PDOException $exception) {
            echo "Kết nối thất bại: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
