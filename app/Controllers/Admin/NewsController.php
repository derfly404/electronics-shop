<?php
class NewsController extends BaseController
{
    private $newsModel;

    public function __construct()
    {
        $this->newsModel = $this->loadModel('NewsModel');
    }

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $newsList = $this->newsModel->getAllNews($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'news_list',
            'newsList' => $newsList,
            'keyword' => $keyword
        ]);
    }

    public function create()
    {
        $this->loadView('Layouts', 'admin_layout', ['content_view' => 'news_form']);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý upload ảnh
            $imageName = "";
            if (!empty($_FILES["hinh_anh"]["name"])) {
                $target_dir = "./public/uploads/";
                $imageName = time() . "_news_" . basename($_FILES["hinh_anh"]["name"]);
                move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $imageName);
            }

            $data = [
                ':tieu_de' => $_POST['tieu_de'],
                ':tom_tat' => $_POST['tom_tat'],
                ':hinh' => $imageName,
                ':noi_dung' => $_POST['noi_dung'],
                ':uid' => $_SESSION['user']['id'], // Lấy ID admin đang đăng nhập
                ':status' => isset($_POST['trang_thai']) ? 1 : 0
            ];

            $this->newsModel->createNews($data);
            header("Location: index.php?module=Admin&controller=News");
        }
    }

    // --- MỚI: Hiển thị form sửa bài viết ---
    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $news = $this->newsModel->getNewsById($id);

        if (!$news) {
            die("Bài viết không tồn tại!");
        }

        // Tái sử dụng view news_form nhưng truyền thêm dữ liệu bài viết cũ
        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'news_form',
            'news' => $news
        ]);
    }

    // --- MỚI: Xử lý cập nhật bài viết ---
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];

            // Xử lý upload ảnh mới (nếu có)
            $imageName = null;
            if (!empty($_FILES["hinh_anh"]["name"])) {
                $target_dir = "./public/uploads/";
                $imageName = time() . "_news_" . basename($_FILES["hinh_anh"]["name"]);
                move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $imageName);
            }

            $data = [
                ':tieu_de' => $_POST['tieu_de'],
                ':tom_tat' => $_POST['tom_tat'],
                ':hinh' => $imageName, // Nếu null, Model sẽ giữ ảnh cũ
                ':noi_dung' => $_POST['noi_dung'],
                ':status' => isset($_POST['trang_thai']) ? 1 : 0
            ];

            $this->newsModel->updateNews($id, $data);

            header("Location: index.php?module=Admin&controller=News&msg=Cập nhật thành công&type=success");
        }
    }

    public function delete()
    {
        if (isset($_GET['id'])) {
            $this->newsModel->deleteNews($_GET['id']);
        }
        header("Location: index.php?module=Admin&controller=News");
    }
}
