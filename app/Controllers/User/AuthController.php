<?php
class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->loadModel('UserModel');
    }

    // 1. Đăng ký
    public function register()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ten = $_POST['ten'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password != $confirm_password) {
                $error = "Mật khẩu xác nhận không khớp!";
            } elseif ($this->userModel->checkEmail($email)) {
                $error = "Email này đã được sử dụng!";
            } else {
                // Mã hóa mật khẩu trước khi lưu
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $data = [
                    ':ten' => $ten,
                    ':email' => $email,
                    ':mat_khau' => $hashed_password
                ];

                if ($this->userModel->register($data)) {
                    // Đăng ký thành công -> Chuyển sang đăng nhập
                    header("Location: index.php?controller=Auth&action=login&msg=registered");
                    exit;
                } else {
                    $error = "Đã có lỗi xảy ra, vui lòng thử lại!";
                }
            }
        }

        $this->loadView('User', 'register', ['error' => $error]);
    }

    // 2. Đăng nhập
    public function login()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = trim($_POST['password']);

            // Gọi hàm checkLogin vừa sửa
            $user = $this->userModel->checkLogin($email, $password);

            if ($user) {
                // --- MỚI: KIỂM TRA TRẠNG THÁI TẠI ĐÂY ---
                if ($user['TRANG_THAI'] == 0) {
                    $error = "Tài khoản của bạn đã bị khóa! Vui lòng liên hệ Admin.";
                } else {
                    // Nếu tài khoản hoạt động bình thường (TRANG_THAI = 1) -> Cho đăng nhập
                    $_SESSION['user'] = [
                        'id' => $user['MA_NGUOI_DUNG'],
                        'TEN' => $user['TEN'],
                        'EMAIL' => $user['EMAIL'],
                        'ROLE' => $user['VAI_TRO']
                    ];

                    if ($user['VAI_TRO'] == 1) {
                        header("Location: index.php?module=Admin&controller=Dashboard");
                    } else {
                        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            header("Location: index.php?controller=Cart");
                        } else {
                            header("Location: index.php");
                        }
                    }
                    exit;
                }
                // ----------------------------------------
            } else {
                $error = "Email hoặc mật khẩu không đúng!";
            }
        }

        $msg = isset($_GET['msg']) ? "Đăng ký thành công! Vui lòng đăng nhập." : "";
        $this->loadView('User', 'login', ['error' => $error, 'msg' => $msg]);
    }

    // 3. Đăng xuất
    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        header("Location: index.php?controller=Auth&action=login");
    }

    // 4. Trang nhập Email (QUÊN MẬT KHẨU - PHIÊN BẢN CẢI TIẾN)
    public function forgot_password()
    {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];

            if ($this->userModel->checkEmail($email)) {
                $token = bin2hex(random_bytes(32));

                // Đặt timezone cho PHP
                date_default_timezone_set('Asia/Ho_Chi_Minh');

                // Tạo token với thời gian hết hạn (sử dụng MySQL để tính toán)
                if ($this->userModel->createResetToken($email, $token)) {
                    $baseUrl = $this->getBaseUrl();
                    $resetLink = $baseUrl . "index.php?controller=Auth&action=reset_password_form&token=" . $token;

                    // GỬI EMAIL THẬT - BỎ PHẦN DEBUG
                    if ($this->sendResetEmail($email, $resetLink)) {
                        $success = "✅ Email đặt lại mật khẩu đã được gửi! Vui lòng kiểm tra hộp thư đến của bạn.";

                        // Log thành công
                        error_log("Reset password email sent successfully to: $email");
                    } else {
                        // Nếu gửi email thất bại, hiển thị thông báo lỗi thân thiện
                        $error = "Không thể gửi email. Vui lòng thử lại sau hoặc liên hệ quản trị viên.";

                        // Vẫn log link để admin có thể hỗ trợ
                        error_log("Email sending failed for: $email");
                        error_log("Reset link: $resetLink");

                        // Có thể hiển thị link trong môi trường development
                        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
                            $success = "<div class='alert alert-warning'>
                            <p>⚠️ <strong>Development Mode:</strong> Không thể gửi email trên localhost.</p>
                            <p>Reset link: <a href='$resetLink'>$resetLink</a></p>
                            <p><small>Trên server thật, email sẽ được gửi tự động.</small></p>
                        </div>";
                        }
                    }
                } else {
                    $error = "Lỗi khi tạo liên kết đặt lại mật khẩu! Vui lòng thử lại.";
                    error_log("Failed to create reset token for email: $email");
                }
            } else {
                $error = "Email không tồn tại trong hệ thống!";
            }
        }

        // Hiển thị view với thông báo
        $this->loadView('User', 'forgot_password', [
            'error' => $error,
            'success' => $success
        ]);
    }

    // Thêm method này vào AuthController
    private function getBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];

        // Lấy đường dẫn từ document root đến thư mục gốc của ứng dụng
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = rtrim(str_replace('\\', '/', $scriptDir), '/');

        return $protocol . "://" . $host . $basePath . "/";
    }

    // 5. Trang nhập mật khẩu mới với TOKEN (PHIÊN BẢN CẢI TIẾN)
    public function reset_password_form()
    {
        $token = $_GET['token'] ?? '';
        $error = '';

        if (empty($token)) {
            $error = "Token không hợp lệ!";
            $this->loadView('User', 'reset_password', ['error' => $error]);
            return;
        }

        // Validate Token
        $user = $this->userModel->validateResetToken($token);

        if (!$user) {
            $error = 'Token không hợp lệ hoặc đã hết hạn!';
            $this->loadView('User', 'reset_password', ['error' => $error]);
            return;
        }

        // Lưu thông tin vào session để dùng trong reset_password()
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $user['EMAIL'];

        $this->loadView('User', 'reset_password', [
            'reset_email' => $user['EMAIL'],
            'error' => $error
        ]);
    }

    ////////////////////////////////////////////////////
    // Thêm vào AuthController.php sau các method khác
    public function debug_token($token)
    {
        echo "<h3>Debug Token: $token</h3>";

        // Kiểm tra trong database
        $user = $this->userModel->getUserByResetToken($token);

        echo "<pre>";
        if ($user) {
            echo "User found:\n";
            print_r($user);
            echo "\nToken expires at: " . $user['RESET_TOKEN_EXPIRES'];
            echo "\nCurrent time: " . date('Y-m-d H:i:s');

            // Kiểm tra thời gian
            $expires = strtotime($user['RESET_TOKEN_EXPIRES']);
            $now = time();
            echo "\nToken valid: " . (($expires > $now) ? "YES" : "NO");
        } else {
            echo "User NOT found with this token!";
        }
        echo "</pre>";

        // Gọi hàm validate để kiểm tra
        $validUser = $this->userModel->validateResetToken($token);
        echo "<h4>validateResetToken result:</h4>";
        echo "<pre>";
        print_r($validUser ? $validUser : "Invalid or expired token");
        echo "</pre>";
    }
    ////////////////////////////////////////////////////


    // 6. Xử lý đặt lại mật khẩu với TOKEN (PHIÊN BẢN CẢI TIẾN)
    public function reset_password()
    {
        // Chỉ xử lý nếu có Token hợp lệ
        if (!isset($_SESSION['reset_token'])) {
            header("Location: index.php?controller=Auth&action=forgot_password");
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
            $token = $_SESSION['reset_token'];

            if (strlen($password) < 6) {
                $error = "Mật khẩu quá ngắn (tối thiểu 6 ký tự)!";
            } elseif ($password != $confirm_password) {
                $error = "Mật khẩu xác nhận không khớp!";
            } else {
                // Gọi Model cập nhật
                if ($this->userModel->updatePasswordByToken($token, $password)) {

                    // Dọn dẹp sạch sẽ session
                    unset($_SESSION['reset_token']);
                    unset($_SESSION['reset_email']);
                    // Xóa luôn cái cũ nếu còn sót lại
                    if (isset($_SESSION['demo_reset_email'])) unset($_SESSION['demo_reset_email']);

                    header("Location: index.php?controller=Auth&action=login&msg=reset_success");
                    exit;
                } else {
                    $error = "Lỗi: Token hết hạn hoặc không tìm thấy! Vui lòng gửi lại yêu cầu.";
                }
            }
        }

        $this->loadView('User', 'reset_password', ['error' => $error]);
    }

    // 7. Hàm gửi email reset password (ĐÃ SỬA LỖI)
    private function sendResetEmail($toEmail, $resetLink)
    {
        try {
            // ĐƯỜNG DẪN PHPMailer
            $phpmailerPath = __DIR__ . '/../PHPMailer/';
            if (!file_exists($phpmailerPath . 'PHPMailer.php')) {
                $phpmailerPath = './app/PHPMailer/';
            }

            require_once $phpmailerPath . 'Exception.php';
            require_once $phpmailerPath . 'PHPMailer.php';
            require_once $phpmailerPath . 'SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // === CẤU HÌNH GMAIL - ĐÃ SỬA LỖI ===
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            // Tùy chọn SSL cho localhost
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // QUAN TRỌNG: Dùng email thật của bạn làm From address
            // Gmail sẽ chặn email nếu From address không trùng với username
            $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']); // DÙNG EMAIL THẬT
            $mail->addAddress($toEmail);

            // Thêm Reply-To address
            $mail->addReplyTo('no-reply@electronicshop.com', 'Do Not Reply');

            // Headers để tránh spam
            $mail->addCustomHeader('Precedence', 'bulk');
            $mail->addCustomHeader('X-Priority', '3');
            $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Reset Password - Electronic Shop';

            // Nội dung email đẹp
            $mail->Body = $this->getEmailTemplate($resetLink);
            $mail->AltBody = "Reset password link: $resetLink\nLink expires in 1 hour.";

            // Tắt debug để tránh hiển thị lỗi ra màn hình
            $mail->SMTPDebug = 0;

            // Gửi email
            $result = $mail->send();

            // Ghi log kết quả
            $this->logEmailAttempt($toEmail, $result, 'Email sent with SMTP');

            return $result;
        } catch (Exception $e) {
            // Ghi lỗi chi tiết
            $errorMessage = "EMAIL ERROR: " . $e->getMessage();
            error_log($errorMessage);

            $logFile = __DIR__ . '/../../email_failures.log';
            file_put_contents(
                $logFile,
                date('Y-m-d H:i:s') . " | To: $toEmail | Error: " . $e->getMessage() . "\n",
                FILE_APPEND
            );

            return false;
        }
    }

    private function getEmailTemplate($resetLink)
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                    background-color: #f5f5f5;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                }
                .header p {
                    margin: 10px 0 0 0;
                    opacity: 0.9;
                }
                .content { 
                    padding: 40px 30px; 
                }
                .btn { 
                    display: inline-block; 
                    background: #4CAF50; 
                    color: white; 
                    padding: 15px 40px; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    font-weight: bold; 
                    margin: 20px 0; 
                    font-size: 16px;
                    transition: background 0.3s;
                }
                .btn:hover {
                    background: #45a049;
                }
                .link-box { 
                    background: #f8f9fa; 
                    padding: 15px; 
                    border: 1px dashed #dee2e6; 
                    border-radius: 5px; 
                    margin: 25px 0; 
                    word-break: break-all;
                    font-size: 14px;
                    color: #495057;
                }
                .footer { 
                    text-align: center; 
                    color: #6c757d; 
                    font-size: 12px; 
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #dee2e6;
                }
                .note {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                ul {
                    padding-left: 20px;
                }
                li {
                    margin-bottom: 8px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 Electronic Shop</h1>
                    <p>Đặt Lại Mật Khẩu</p>
                </div>
                
                <div class="content">
                    <p>Xin chào,</p>
                    <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại Electronic Shop.</p>
                    
                    <div style="text-align: center;">
                        <a href="' . $resetLink . '" class="btn">
                            📧 ĐẶT LẠI MẬT KHẨU
                        </a>
                    </div>
                    
                    <p>Hoặc copy link dưới đây vào trình duyệt:</p>
                    <div class="link-box">' . $resetLink . '</div>
                    
                    <div class="note">
                        <p><strong>⚠️ Lưu ý quan trọng:</strong></p>
                        <ul>
                            <li>Link có hiệu lực trong <strong>1 giờ</strong></li>
                            <li>Không chia sẻ link này với bất kỳ ai</li>
                            <li>Nếu không phải bạn yêu cầu, hãy bỏ qua email này</li>
                            <li>Để bảo mật, sau khi đặt lại mật khẩu, vui lòng đăng xuất khỏi tất cả thiết bị</li>
                        </ul>
                    </div>
                    
                    <p>Trân trọng,<br>
                    <strong>Đội ngũ Electronic Shop</strong></p>
                    
                    <div class="footer">
                        <p>Đây là email tự động, vui lòng không trả lời email này.</p>
                        <p>© ' . date('Y') . ' Electronic Shop. All rights reserved.</p>
                        <p>
                            <a href="http://localhost/unsubscribe" style="color: #6c757d; text-decoration: none;">Unsubscribe</a> | 
                            <a href="http://localhost/privacy" style="color: #6c757d; text-decoration: none;">Privacy Policy</a>
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
    }

    private function logEmailAttempt($email, $success, $debugInfo)
    {
        $logFile = __DIR__ . '/../../email_send_log.log';
        $status = $success ? "✅ SUCCESS" : "❌ FAILED";

        $logContent = "=== " . date('Y-m-d H:i:s') . " ===\n" .
            "To: $email\n" .
            "Status: $status\n" .
            "Info: $debugInfo\n" .
            "------------------------\n\n";

        file_put_contents($logFile, $logContent, FILE_APPEND);

        // Cũng ghi vào error log của PHP
        if ($success) {
            error_log("Email sent successfully to: $email");
        } else {
            error_log("Failed to send email to: $email");
        }
    }

    // 8. Hàm xử lý khi người dùng click link từ email (giữ cho tương thích)
    public function process_reset()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            header("Location: index.php?controller=Auth&action=forgot_password&error=invalid_token");
            exit;
        }

        // Kiểm tra token
        $user = $this->userModel->validateResetToken($token);

        if ($user) {
            // Token hợp lệ, chuyển đến trang reset password
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $user['EMAIL'];
            header("Location: index.php?controller=Auth&action=reset_password");
            exit;
        } else {
            // Token không hợp lệ
            header("Location: index.php?controller=Auth&action=forgot_password&error=expired_token");
            exit;
        }
    }

    // 9. Hàm kiểm tra cấu hình email (debug)
    public function test_email_config()
    {
        echo "<h2>Testing Email Configuration</h2>";

        // Test 1: Kiểm tra file PHPMailer
        $phpmailerPath = __DIR__ . '/../PHPMailer/';
        if (!file_exists($phpmailerPath . 'PHPMailer.php')) {
            $phpmailerPath = './app/PHPMailer/';
        }

        echo "<p>PHPMailer Path: " . realpath($phpmailerPath) . "</p>";
        echo "<p>PHPMailer.php exists: " . (file_exists($phpmailerPath . 'PHPMailer.php') ? '✅ Yes' : '❌ No') . "</p>";

        // Test 2: Kiểm tra kết nối SMTP
        try {
            require_once $phpmailerPath . 'Exception.php';
            require_once $phpmailerPath . 'PHPMailer.php';
            require_once $phpmailerPath . 'SMTP.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'trong123hgz@gmail.com';
            $mail->Password = 'asxw qoco zpqp ofjk';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Timeout = 10;

            echo "<p>SMTP Configuration: ✅ OK</p>";

            // Test connection
            if ($mail->smtpConnect()) {
                echo "<p>SMTP Connection: ✅ Connected successfully</p>";
                $mail->smtpClose();
            } else {
                echo "<p>SMTP Connection: ❌ Failed to connect</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }

        // Test 3: Send test email
        echo '<form method="POST">';
        echo '<input type="email" name="test_email" placeholder="Email to test..." required>';
        echo '<input type="submit" name="send_test" value="Send Test Email">';
        echo '</form>';

        if (isset($_POST['send_test'])) {
            $testEmail = $_POST['test_email'];
            $testLink = "http://localhost/test-reset-link";

            echo "<h3>Sending test email to: $testEmail</h3>";

            if ($this->sendResetEmail($testEmail, $testLink)) {
                echo "<p style='color: green;'>✅ Test email sent successfully!</p>";
                echo "<p>Please check your inbox and spam folder.</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to send test email.</p>";
                echo "<p>Check the email_send_log.log and email_failures.log files for details.</p>";
            }
        }
    }
}
