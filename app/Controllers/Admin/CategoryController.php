<?php
class CategoryController extends BaseController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = $this->loadModel('CategoryModel');
    }

    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $categories = $this->categoryModel->getAllCategories($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'category_list',
            'categories' => $categories,
            'keyword' => $keyword // Truyền lại view
        ]);
    }

    // --- CẬP NHẬT: Xử lý upload ảnh khi thêm mới ---
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['ten_danh_muc'];

            // Xử lý ảnh
            $imageName = "";
            if (!empty($_FILES["hinh_anh"]["name"])) {
                $target_dir = "./public/uploads/";
                $imageName = time() . "_cat_" . basename($_FILES["hinh_anh"]["name"]);
                move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $imageName);
            }

            $this->categoryModel->addCategory($name, $imageName);
            header("Location: index.php?module=Admin&controller=Category");
        }
    }

    public function edit()
    {
        $id = $_GET['id'];
        $category = $this->categoryModel->getCategoryById($id);
        $categories = $this->categoryModel->getAllCategories();

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'category_list',
            'categories' => $categories,
            'editCategory' => $category
        ]);
    }

    // --- CẬP NHẬT: Xử lý upload ảnh khi sửa ---
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $name = $_POST['ten_danh_muc'];

            // Kiểm tra có upload ảnh mới không
            $imageName = null;
            if (!empty($_FILES["hinh_anh"]["name"])) {
                $target_dir = "./public/uploads/";
                $imageName = time() . "_cat_" . basename($_FILES["hinh_anh"]["name"]);
                move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $imageName);
            }

            $this->categoryModel->updateCategory($id, $name, $imageName);
            header("Location: index.php?module=Admin&controller=Category");
        }
    }

    // --- CẬP NHẬT HÀM DELETE ---
    public function delete()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        if ($id) {
            $result = $this->categoryModel->deleteCategory($id);

            if ($result === 'locked') {
                $msg = "Không thể xóa danh mục này vì đang chứa sản phẩm! Hãy xóa hoặc chuyển sản phẩm sang danh mục khác trước.";
                $type = "error";
            } elseif ($result) {
                $msg = "Xóa danh mục thành công!";
                $type = "success";
            } else {
                $msg = "Lỗi hệ thống.";
                $type = "error";
            }

            header("Location: index.php?module=Admin&controller=Category&msg=" . urlencode($msg) . "&type=" . $type);
        } else {
            header("Location: index.php?module=Admin&controller=Category");
        }
    }
}
