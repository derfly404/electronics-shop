<?php
class ProductController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = $this->loadModel('ProductModel');
    }

    // Hiển thị chi tiết sản phẩm
    // --- CẬP NHẬT HÀM DETAIL ---
    public function detail()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $product = $this->productModel->getProductById($id);
        $relatedProducts = $this->productModel->getRelatedProducts($product['MA_DANH_MUC'], $id);

        // -- MỚI: Load thêm ReviewModel để lấy đánh giá --
        $reviewModel = $this->loadModel('ReviewModel');
        $reviews = $reviewModel->getReviewsByProduct($id);

        $this->loadView('User', 'product_detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews // Truyền biến reviews sang View
        ]);
    }

    // --- MỚI: HÀM GỬI ĐÁNH GIÁ ---
    public function submit_review()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra đăng nhập
            if (!isset($_SESSION['user'])) {
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            $reviewModel = $this->loadModel('ReviewModel');

            $data = [
                ':noidung' => $_POST['noi_dung'],
                ':sosao' => $_POST['so_sao'],
                ':masp' => $_POST['ma_san_pham'],
                ':mauid' => $_SESSION['user']['id']
            ];

            // (Tuỳ chọn) Kiểm tra đã mua hàng chưa
            if (!$reviewModel->checkPurchased($data[':mauid'], $data[':masp'])) {
                echo "<script>
            alert('Bạn phải mua sản phẩm này mới được đánh giá!');
            window.history.back();
          </script>";
                exit;
            }

            $reviewModel->addReview($data);

            // Quay lại trang chi tiết sản phẩm
            header("Location: index.php?controller=Product&action=detail&id=" . $_POST['ma_san_pham']);
        }
    }

    // Chức năng tìm kiếm (Bổ sung cho thanh tìm kiếm ở Header)
    public function search()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        // Tạm thời dùng lại hàm getAllProducts hoặc viết hàm search riêng trong Model sau
        // Ở đây tôi demo đơn giản, bạn có thể nâng cấp sau
        echo "Chức năng tìm kiếm: " . htmlspecialchars($keyword);
    }

    public function index()
    {
        $categoryModel = $this->loadModel('CategoryModel');
        $categories = $categoryModel->getAllCategories();

        // Lấy tham số lọc
        $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : null;
        $maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : null;

        // Cấu hình Phân trang
        $limit = 12; // Số sản phẩm mỗi trang
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // Lấy dữ liệu
        // Lấy danh sách sản phẩm cho trang hiện tại
        $products = $this->productModel->filterProducts($categoryId, $keyword, $minPrice, $maxPrice, $limit, $offset);

        // Đếm tổng số kết quả để tính tổng số trang
        $totalRecords = $this->productModel->countFilteredProducts($categoryId, $keyword, $minPrice, $maxPrice);
        $totalPages = ceil($totalRecords / $limit);

        // Tiêu đề trang
        $pageTitle = "Tất cả sản phẩm";
        if ($categoryId) {
            foreach ($categories as $c) {
                if ($c['MA_DANH_MUC'] == $categoryId) {
                    $pageTitle = "Danh mục: " . $c['TEN_DANH_MUC'];
                    break;
                }
            }
        } elseif ($keyword) {
            $pageTitle = "Tìm kiếm: " . htmlspecialchars($keyword);
        }

        // 5. Truyền sang View
        $this->loadView('User', 'product_list', [
            'products' => $products,
            'categories' => $categories,
            'pageTitle' => $pageTitle,
            'currentCat' => $categoryId,
            'currentMin' => $minPrice,
            'currentMax' => $maxPrice,

            // Dữ liệu phân trang
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // Hàm phụ để lấy tên danh mục từ ID
    private function getCategoryName($categories, $id)
    {
        foreach ($categories as $c) {
            if ($c['MA_DANH_MUC'] == $id) return $c['TEN_DANH_MUC'];
        }
        return "Sản phẩm";
    }

    // --- MỚI: Xóa đánh giá ---
    public function delete_review()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }

        $reviewId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
        $userId = $_SESSION['user']['id'];

        // Gọi Model (ReviewModel cần được load trong construct hoặc tại đây)
        $reviewModel = $this->loadModel('ReviewModel');
        $reviewModel->deleteReviewByUser($reviewId, $userId);

        // Quay lại trang chi tiết sản phẩm
        header("Location: index.php?controller=Product&action=detail&id=" . $productId . "#review");
    }

    // --- MỚI: Sửa đánh giá ---
    public function update_review()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user'])) {
                header("Location: index.php?controller=Auth&action=login");
                exit;
            }

            $reviewId = $_POST['review_id'];
            $productId = $_POST['product_id'];
            $content = $_POST['noi_dung'];
            $rating = $_POST['so_sao'];
            $userId = $_SESSION['user']['id'];

            $reviewModel = $this->loadModel('ReviewModel');
            $reviewModel->updateReview($reviewId, $userId, $content, $rating);

            header("Location: index.php?controller=Product&action=detail&id=" . $productId . "#review");
        }
    }
}
