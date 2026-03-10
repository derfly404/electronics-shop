<?php
class ProductController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = $this->loadModel('ProductModel');
    }

    // 1. Danh sách sản phẩm (Có tìm kiếm)
    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $products = $this->productModel->getAllProducts($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'product_list',
            'products' => $products,
            'keyword' => $keyword
        ]);
    }

    // 2. Hiển thị form thêm mới
    public function create()
    {
        $categories = $this->productModel->getCategories();
        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'product_form',
            'categories' => $categories
        ]);
    }

    // 3. Xử lý lưu sản phẩm mới
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // 1. VALIDATE: Kiểm tra giảm giá hợp lệ (MỚI)
            $giam_gia = isset($_POST['giam_gia']) ? floatval($_POST['giam_gia']) : 0;

            if ($giam_gia < 0 || $giam_gia > 100) {
                // Nếu sai, quay lại trang thêm mới và báo lỗi
                $msg = "Lỗi: Giảm giá phải nằm trong khoảng từ 0% đến 100%!";
                header("Location: index.php?module=Admin&controller=Product&action=create&msg=" . urlencode($msg) . "&type=error");
                exit; // Dừng code ngay lập tức
            }

            // Xử lý ảnh đại diện
            $target_dir = "./public/uploads/";
            $imageName = time() . "_" . basename($_FILES["hinh_anh"]["name"]);
            move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $imageName);

            // Xử lý Gallery (Nhiều ảnh)
            $galleryJson = null;
            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $galleryFiles = [];
                $totalFiles = count($_FILES['gallery']['name']);

                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['gallery']['error'][$i] == 0) {
                        $gName = time() . "_" . $i . "_" . basename($_FILES['gallery']['name'][$i]);
                        // Lưu ý: Có [$i] ở tmp_name
                        move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_dir . $gName);
                        $galleryFiles[] = $gName;
                    }
                }
                $galleryJson = json_encode($galleryFiles);
            }

            $data = [
                ':ten' => $_POST['ten_san_pham'],
                ':dm' => $_POST['ma_danh_muc'],
                ':gia' => $_POST['gia'],
                ':giam_gia' => $_POST['giam_gia'] ?? 0,
                ':sl' => $_POST['so_luong_ton'],
                ':hinh' => $imageName,
                ':gallery' => $galleryJson,
                ':mota' => $_POST['mo_ta'],
                ':noibat' => isset($_POST['noi_bat']) ? 1 : 0
            ];

            $this->productModel->addProduct($data);
            header("Location: index.php?module=Admin&controller=Product");
        }
    }

    // 4. Hiển thị form sửa
    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) die("ID không hợp lệ");

        $product = $this->productModel->getProductById($id);
        $categories = $this->productModel->getCategories();

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'product_edit',
            'product' => $product,
            'categories' => $categories
        ]);
    }

    // 5. Xử lý cập nhật sản phẩm
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_GET['id'];
            $target_dir = "./public/uploads/";

            // 1. VALIDATE: Kiểm tra giảm giá hợp lệ (MỚI)
            $giam_gia = isset($_POST['giam_gia']) ? floatval($_POST['giam_gia']) : 0;

            if ($giam_gia < 0 || $giam_gia > 100) {
                // Nếu sai, quay lại trang sửa và báo lỗi
                $msg = "Lỗi: Giảm giá phải nằm trong khoảng từ 0% đến 100%!";
                header("Location: index.php?module=Admin&controller=Product&action=edit&id=$id&msg=" . urlencode($msg) . "&type=error");
                exit;
            }

            // Xử lý ảnh đại diện mới
            $imageName = null;
            if (!empty($_FILES["hinh_anh"]["name"])) {
                $imageName = time() . "_" . basename($_FILES["hinh_anh"]["name"]);
                move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_dir . $imageName);
            }

            // Xử lý Gallery mới
            $galleryJson = null;
            if (isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
                $galleryFiles = [];
                $totalFiles = count($_FILES['gallery']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['gallery']['error'][$i] == 0) {
                        $gName = time() . "_" . $i . "_" . basename($_FILES['gallery']['name'][$i]);
                        move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $target_dir . $gName);
                        $galleryFiles[] = $gName;
                    }
                }
                $galleryJson = json_encode($galleryFiles);
            }

            $data = [
                ':ten' => $_POST['ten_san_pham'],
                ':dm' => $_POST['ma_danh_muc'],
                ':gia' => $_POST['gia'],
                ':giam_gia' => $_POST['giam_gia'] ?? 0,
                ':sl' => $_POST['so_luong_ton'],
                ':mota' => $_POST['mo_ta'],
                ':noibat' => isset($_POST['noi_bat']) ? 1 : 0,
                ':hinh' => $imageName,
                ':gallery' => $galleryJson
            ];

            $this->productModel->updateProduct($id, $data);
            header("Location: index.php?module=Admin&controller=Product");
        }
    }

    // 6. Xóa sản phẩm (Có xử lý lỗi)
    // --- CẬP NHẬT LẠI HÀM delete ---
    public function delete()
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $result = $this->productModel->deleteProduct($id);

            if ($result === 'locked') {
                // Trả về mã lỗi đặc biệt kèm ID để View tạo link xóa ép buộc
                $msg = "Sản phẩm này đã bán, không thể xóa thường! Bạn có muốn xóa cả lịch sử đơn hàng liên quan không?";
                // Truyền thêm biến error_id để view hiển thị nút
                header("Location: index.php?module=Admin&controller=Product&msg=" . urlencode($msg) . "&type=warning&error_id=" . $id);
                exit;
            } elseif ($result) {
                $msg = "Xóa sản phẩm thành công!";
                $type = "success";
            } else {
                $msg = "Đã có lỗi xảy ra.";
                $type = "error";
            }

            header("Location: index.php?module=Admin&controller=Product&msg=" . urlencode($msg) . "&type=" . $type);
        } else {
            header("Location: index.php?module=Admin&controller=Product");
        }
    }

    // --- MỚI: Action Xóa ép buộc ---
    public function force_delete()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            if ($this->productModel->forceDeleteProduct($id)) {
                $msg = "Đã xóa vĩnh viễn sản phẩm và toàn bộ dữ liệu liên quan!";
                $type = "success";
            } else {
                $msg = "Lỗi hệ thống: Không thể xóa dữ liệu.";
                $type = "error";
            }
            header("Location: index.php?module=Admin&controller=Product&msg=" . urlencode($msg) . "&type=" . $type);
        } else {
            header("Location: index.php?module=Admin&controller=Product");
        }
    }

    // 7. Thay đổi trạng thái Ẩn/Hiện
    public function toggle_status()
    {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id = $_GET['id'];
            $status = $_GET['status']; // 0 hoặc 1

            $this->productModel->toggleStatus($id, $status);

            $msg = ($status == 1) ? "Đã mở bán lại sản phẩm!" : "Đã ẩn sản phẩm khỏi trang chủ!";
            header("Location: index.php?module=Admin&controller=Product&msg=" . urlencode($msg) . "&type=success");
        } else {
            header("Location: index.php?module=Admin&controller=Product");
        }
    }
}
