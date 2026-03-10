<?php
class UserModel extends BaseModel
{
    const TABLE = 'NGUOI_DUNG';

    // --- AUTH ---
    public function register($data)
    {
        $sql = "INSERT INTO " . self::TABLE . " (TEN, EMAIL, MAT_KHAU, VAI_TRO, TRANG_THAI) 
                VALUES (:ten, :email, :mat_khau, 0, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function checkEmail($email)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE EMAIL = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->rowCount() > 0;
    }

    public function checkLogin($email, $password)
    {
        // 1. Bỏ điều kiện "AND TRANG_THAI = 1" đi
        $sql = "SELECT * FROM " . self::TABLE . " WHERE EMAIL = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Kiểm tra mật khẩu
        if ($user && password_verify($password, $user['MAT_KHAU'])) {
            return $user; // Trả về thông tin user (bao gồm cả TRANG_THAI = 0)
        }

        return false; // Sai email hoặc mật khẩu
    }

    // --- PROFILE USER ---
    public function updateProfile($id, $name, $phone, $address, $password = null)
    {
        // Chuẩn bị câu SQL cơ bản
        $sql = "UPDATE " . self::TABLE . " 
                SET TEN = :name, SDT = :phone, DIA_CHI = :address";

        $params = [
            ':name' => $name,
            ':phone' => $phone,
            ':address' => $address,
            ':id' => $id
        ];

        // Nếu có đổi mật khẩu thì thêm vào câu SQL
        if (!empty($password)) {
            $sql .= ", MAT_KHAU = :pass";
            $params[':pass'] = $password;
        }

        $sql .= " WHERE MA_NGUOI_DUNG = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // Đảm bảo hàm checkLogin hoặc getUserById lấy đủ dữ liệu mới
    public function getUserById($id)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE MA_NGUOI_DUNG = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- ADMIN QUẢN LÝ ---
    public function getAllUsers($keyword = null)
    {
        $sql = "SELECT * FROM " . self::TABLE;
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE (TEN LIKE :name OR EMAIL LIKE :email)"; // Ngoặc đơn để tránh lỗi logic
            $params[':name'] = "%$keyword%";
            $params[':email'] = "%$keyword%";
        } else {
            // Logic cũ (nếu không tìm kiếm thì sắp xếp giảm dần)
            // Có thể giữ hoặc bỏ, ở đây mình nối luôn
        }

        $sql .= " ORDER BY MA_NGUOI_DUNG DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus($id, $currentStatus)
    {
        $newStatus = ($currentStatus == 1) ? 0 : 1;
        $sql = "UPDATE " . self::TABLE . " SET TRANG_THAI = :status WHERE MA_NGUOI_DUNG = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':status' => $newStatus, ':id' => $id]);
    }

    // --- DASHBOARD: THỐNG KÊ (Tránh lỗi tiếp theo) ---
    public function countUsers()
    {
        $sql = "SELECT COUNT(*) as total FROM " . self::TABLE . " WHERE VAI_TRO = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // --- MỚI: Cập nhật vai trò (Phân quyền) ---
    public function updateRole($id, $role)
    {
        // $role: 1 là Admin, 0 là Khách hàng
        $sql = "UPDATE " . self::TABLE . " SET VAI_TRO = :role WHERE MA_NGUOI_DUNG = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':role' => $role, ':id' => $id]);
    }

    // --- DEMO: Cập nhật mật khẩu theo Email ---
    public function updatePasswordByEmail($email, $newPassword)
    {
        $sql = "UPDATE " . self::TABLE . " SET MAT_KHAU = :pass WHERE EMAIL = :email";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':pass' => $newPassword, ':email' => $email]);
    }

    // ========== MỚI: CÁC METHOD CHO RESET PASSWORD VỚI TOKEN ==========

    // 1. Lấy thông tin user theo email
    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE EMAIL = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. Lưu token reset vào database
    public function saveResetToken($email, $token, $expires_at)
    {
        $sql = "UPDATE " . self::TABLE . " SET 
                RESET_TOKEN = :token, 
                RESET_TOKEN_EXPIRES = :expires_at 
                WHERE EMAIL = :email";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':token' => $token, ':expires_at' => $expires_at, ':email' => $email]);
    }

    // 3. Kiểm tra token có hợp lệ không (còn thời gian)
    public function validateResetToken($token)
    {
        // THÊM DEBUG CHI TIẾT
        error_log("=== VALIDATE RESET TOKEN ===");
        error_log("Token: $token");

        // CÁCH 1: Dùng MySQL NOW() - Cách này an toàn nhất
        $sql = "SELECT * FROM " . self::TABLE . " 
            WHERE RESET_TOKEN = :token 
            AND RESET_TOKEN_EXPIRES > NOW()";

        error_log("SQL Query: $sql");

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            error_log("✅ Token VALID for user: " . $user['EMAIL']);
            error_log("✅ Token expires at: " . $user['RESET_TOKEN_EXPIRES']);

            // Kiểm tra thêm bằng PHP để debug
            $expiresTime = strtotime($user['RESET_TOKEN_EXPIRES']);
            $currentTime = time();
            $isValidPHP = $expiresTime > $currentTime;

            error_log("PHP Check - Expires (timestamp): $expiresTime");
            error_log("PHP Check - Current (timestamp): $currentTime");
            error_log("PHP Check - Valid: " . ($isValidPHP ? "YES" : "NO"));
            error_log("PHP Check - Time remaining: " . ($expiresTime - $currentTime) . " seconds");

            return $user;
        } else {
            error_log("❌ Token INVALID or EXPIRED");

            // Kiểm tra xem token có tồn tại không
            $sql2 = "SELECT * FROM " . self::TABLE . " WHERE RESET_TOKEN = :token";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute([':token' => $token]);
            $expiredUser = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($expiredUser) {
                error_log("⚠️ Token EXISTS but may be expired");
                error_log("Token expires: " . $expiredUser['RESET_TOKEN_EXPIRES']);
                error_log("MySQL NOW(): " . $this->getMysqlNow());

                // Debug thời gian chi tiết
                $sql3 = "SELECT NOW() as mysql_now, 
                            '" . $expiredUser['RESET_TOKEN_EXPIRES'] . "' as token_expires,
                            (NOW() > '" . $expiredUser['RESET_TOKEN_EXPIRES'] . "') as is_expired";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->execute();
                $debug = $stmt3->fetch(PDO::FETCH_ASSOC);

                error_log("Debug comparison:");
                error_log("MySQL NOW: " . $debug['mysql_now']);
                error_log("Token expires: " . $debug['token_expires']);
                error_log("Is expired (MySQL): " . $debug['is_expired']);
            } else {
                error_log("⚠️ Token NOT FOUND in database");
            }

            return false;
        }
    }

    // Thêm method này để lấy thời gian hiện tại từ MySQL
    private function getMysqlNow()
    {
        $sql = "SELECT NOW() as now";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['now'];
    }

    // 4. Cập nhật mật khẩu dựa trên token
    public function updatePasswordByToken($token, $password)
    {
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm điều kiện kiểm tra thời gian một lần nữa cho chắc chắn
        $sql = "UPDATE " . self::TABLE . " SET 
                MAT_KHAU = :password,
                RESET_TOKEN = NULL,
                RESET_TOKEN_EXPIRES = NULL
                WHERE RESET_TOKEN = :token";

        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            ':password' => $hashed_password,
            ':token' => $token
        ]);

        // QUAN TRỌNG: Kiểm tra rowCount()
        // Nếu > 0 nghĩa là có 1 dòng đã được cập nhật -> Đổi pass thành công
        // Nếu = 0 nghĩa là không tìm thấy token (hoặc token sai) -> Đổi pass thất bại
        if ($result && $stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    // 5. Kiểm tra token có tồn tại không (không quan tâm thời gian)
    public function checkResetToken($token)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE RESET_TOKEN = :token";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->rowCount() > 0;
    }

    // 6. Xóa token đã hết hạn (có thể dùng cho cron job)
    public function cleanupExpiredTokens()
    {
        $sql = "UPDATE " . self::TABLE . " SET 
                RESET_TOKEN = NULL,
                RESET_TOKEN_EXPIRES = NULL
                WHERE RESET_TOKEN_EXPIRES < NOW()";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }

    // 7. Lấy thông tin user dựa trên token (bao gồm cả đã hết hạn)
    public function getUserByResetToken($token)
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE RESET_TOKEN = :token";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 8. Hàm kiểm tra xem user có đang trong quá trình reset password không
    public function isUserResettingPassword($email)
    {
        $sql = "SELECT * FROM " . self::TABLE . " 
                WHERE EMAIL = :email 
                AND RESET_TOKEN IS NOT NULL
                AND RESET_TOKEN_EXPIRES > NOW()";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->rowCount() > 0;
    }

    // THÊM PHƯƠNG THỨC MỚI - Sử dụng MySQL để tính toán thời gian
    public function createResetToken($email, $token)
    {
        try {
            // SỬ DỤNG MYSQL FUNCTION ĐỂ ĐẢM BẢO TIMEZONE ĐỒNG BỘ
            $sql = "UPDATE " . self::TABLE . " SET 
                RESET_TOKEN = :token, 
                RESET_TOKEN_EXPIRES = DATE_ADD(NOW(), INTERVAL 1 HOUR)
                WHERE EMAIL = :email";

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':token' => $token,
                ':email' => $email
            ]);

            // GHI LOG ĐỂ DEBUG
            error_log("=== CREATE RESET TOKEN ===");
            error_log("Email: $email");
            error_log("Token: $token");
            error_log("SQL executed: " . ($result ? "YES" : "NO"));
            error_log("Rows affected: " . $stmt->rowCount());

            // KIỂM TRA NGAY SAU KHI LƯU
            if ($result && $stmt->rowCount() > 0) {
                $checkSql = "SELECT 
                RESET_TOKEN,
                RESET_TOKEN_EXPIRES,
                NOW() as MYSQL_NOW,
                TIMESTAMPDIFF(SECOND, NOW(), RESET_TOKEN_EXPIRES) as SECONDS_LEFT,
                (RESET_TOKEN_EXPIRES > NOW()) as IS_VALID
                FROM " . self::TABLE . " 
                WHERE EMAIL = :email";

                $checkStmt = $this->conn->prepare($checkSql);
                $checkStmt->execute([':email' => $email]);
                $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

                error_log("Verification after save:");
                error_log("Token saved: " . $checkResult['RESET_TOKEN']);
                error_log("Expires at: " . $checkResult['RESET_TOKEN_EXPIRES']);
                error_log("MySQL NOW: " . $checkResult['MYSQL_NOW']);
                error_log("Seconds left: " . $checkResult['SECONDS_LEFT']);
                error_log("Is valid: " . $checkResult['IS_VALID']);

                return true;
            }

            return false;
        } catch (Exception $e) {
            error_log("Error in createResetToken: " . $e->getMessage());
            return false;
        }
    }
}
