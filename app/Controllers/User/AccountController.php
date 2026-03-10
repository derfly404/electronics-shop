<?php
class AccountController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index()
    {
        // 1. Lấy thông tin mới nhất từ DB (bao gồm SDT, DiaChi vừa thêm)
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->getUserById($userId); // Dùng hàm mới viết ở Model

        // 2. Load view
        $this->loadView('User', 'account_info', [
            'user' => $user
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_SESSION['user']['id'];
            $name = $_POST['ten'];
            $phone = $_POST['sdt'];       // Nhận SĐT
            $address = $_POST['dia_chi']; // Nhận Địa chỉ
            $newPass = $_POST['new_password'];

            $hashedPass = null;
            if (!empty($newPass)) {
                $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
            }

            // Gọi hàm Model đã cập nhật
            $this->userModel->updateProfile($id, $name, $phone, $address, $hashedPass);

            // Cập nhật lại session tên (để hiển thị trên Header ngay lập tức)
            $_SESSION['user']['TEN'] = $name;

            header("Location: index.php?controller=Account&msg=updated");
        }
    }
}
