<?php
class UserController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $users = $this->userModel->getAllUsers($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'user_list',
            'users' => $users,
            'keyword' => $keyword
        ]);
    }

    public function toggle()
    {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $this->userModel->toggleStatus($_GET['id'], $_GET['status']);
        }
        header("Location: index.php?module=Admin&controller=User");
    }

    // --- MỚI: Thay đổi quyền hạn ---
    public function change_role()
    {
        if (isset($_GET['id']) && isset($_GET['role'])) {
            $id = intval($_GET['id']);
            $role = intval($_GET['role']);

            // 1. BẢO MẬT: Không cho phép tự thay đổi quyền của chính mình (Giữ nguyên)
            if ($id == $_SESSION['user']['id']) {
                echo "<script>alert('Bạn không thể tự thay đổi quyền của chính mình!'); window.location.href='index.php?module=Admin&controller=User';</script>";
                exit;
            }

            // 2. BẢO MẬT MỚI: Không cho phép tác động vào tài khoản ROOT ADMIN (ID = 1)
            // Đây là tài khoản tạo ra đầu tiên trong database, được coi là chủ sở hữu tối cao
            if ($id == 1) {
                echo "<script>alert('CẢNH BÁO: Đây là tài khoản Quản trị cao nhất (Root), không thể bị thay đổi quyền hạn!'); window.location.href='index.php?module=Admin&controller=User';</script>";
                exit;
            }

            $this->userModel->updateRole($id, $role);
        }
        header("Location: index.php?module=Admin&controller=User");
    }
}
