<?php
class CartController extends BaseController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = $this->loadModel('ProductModel');
    }

    // Xem giỏ hàng
    public function index()
    {
        $cartItems = [];
        $totalMoney = 0;

        // Kiểm tra nếu giỏ hàng có sản phẩm
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                // Lấy thông tin sản phẩm từ DB
                $product = $this->productModel->getProductById($productId);

                if ($product) {
                    // Tính giá sau giảm
                    $price = $product['GIA'] * (100 - $product['GIAM_GIA']) / 100;
                    $totalMoney += $price * $quantity;

                    // Gộp thông tin sản phẩm và số lượng vào mảng hiển thị
                    $product['buy_qty'] = $quantity;
                    $product['display_price'] = $price;
                    $cartItems[] = $product;
                }
            }
        }

        $this->loadView('User', 'cart', [
            'cartItems' => $cartItems,
            'totalMoney' => $totalMoney
        ]);
    }

    // Thêm sản phẩm vào giỏ
    // Thêm sản phẩm vào giỏ (Hỗ trợ cả GET từ trang danh sách và POST từ trang chi tiết)
    public function add()
    {
        $id = 0;
        $qty = 1;

        // Kiểm tra phương thức gửi dữ liệu
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Nếu từ Form chi tiết (có chọn số lượng)
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        } else {
            // Nếu bấm link <a> từ trang danh sách (Mặc định số lượng là 1)
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $qty = 1;
        }

        // Xử lý thêm vào session
        if ($id > 0) {
            $product = $this->productModel->getProductById($id);

            if ($product) {
                // Tính tổng số lượng sẽ có sau khi thêm
                $currentQtyInCart = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] : 0;
                $newTotalQty = $currentQtyInCart + $qty;

                // Kiểm tra tổng số lượng không vượt quá tồn kho
                if ($product['SO_LUONG_TON'] >= $newTotalQty) {
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }

                    $_SESSION['cart'][$id] = $newTotalQty;
                } else {
                    // Có thể thêm thông báo lỗi ở đây
                    $_SESSION['error'] = "Số lượng vượt quá tồn kho. Tồn kho hiện có: " . $product['SO_LUONG_TON'];
                }
            }
        }

        //  Quay lại trang giỏ hàng
        header("Location: index.php?controller=Cart");
    }

    // Cập nhật số lượng
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['qty'])) {
            foreach ($_POST['qty'] as $productId => $newQty) {
                $newQty = intval($newQty);
                if ($newQty <= 0) {
                    unset($_SESSION['cart'][$productId]); // Xóa nếu số lượng <= 0
                } else {
                    // Kiểm tra tồn kho thực tế (Optional)
                    $product = $this->productModel->getProductById($productId);
                    if ($product && $newQty <= $product['SO_LUONG_TON']) {
                        $_SESSION['cart'][$productId] = $newQty;
                    } else {
                        // Nếu vượt quá tồn kho, set bằng tồn kho tối đa
                        $_SESSION['cart'][$productId] = $product['SO_LUONG_TON'];
                    }
                }
            }
        }
        header("Location: index.php?controller=Cart");
    }

    // Xóa sản phẩm
    public function delete()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0 && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header("Location: index.php?controller=Cart");
    }

    // --- MỚI: Xử lý Mua Ngay (Không lưu vào giỏ hàng chính) ---
    public function buyNow()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = intval($_POST['id']);
            $qty = intval($_POST['quantity']);

            // Lưu sản phẩm này vào session đặc biệt
            $_SESSION['direct_buy'] = [
                'id' => $id,
                'qty' => $qty
            ];

            // Chuyển hướng thẳng đến trang thanh toán
            header("Location: index.php?controller=Order&action=checkout&mode=direct");
        }
    }
}
