<?php
class OrderController extends BaseController
{
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        $this->orderModel = $this->loadModel('OrderModel');
        $this->productModel = $this->loadModel('ProductModel');
    }

    // 1. Hiển thị trang Checkout (Có lọc sản phẩm được chọn)
    public function checkout()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }

        // Load thông tin User mới nhất
        $userModel = $this->loadModel('UserModel');
        $user = $userModel->getUserById($_SESSION['user']['id']);

        $cartItems = [];
        $totalMoney = 0;

        // A. Trường hợp Mua ngay (Direct Buy)
        if (isset($_GET['mode']) && $_GET['mode'] == 'direct' && isset($_SESSION['direct_buy'])) {
            $item = $_SESSION['direct_buy'];
            $p = $this->productModel->getProductById($item['id']);
            if ($p) {
                $price = $p['GIA'] * (100 - $p['GIAM_GIA']) / 100;
                $p['buy_qty'] = $item['qty'];
                $p['display_price'] = $price;
                $cartItems[] = $p;
                $totalMoney = $price * $item['qty'];
            }
        }
        // B. Trường hợp Mua từ Giỏ hàng (Có lọc theo items)
        elseif (!empty($_SESSION['cart'])) {
            // Lấy danh sách ID từ URL
            $selectedIds = [];
            if (isset($_GET['items']) && !empty($_GET['items'])) {
                $selectedIds = explode(',', $_GET['items']);
            }

            foreach ($_SESSION['cart'] as $pid => $qty) {
                // CHỈ LẤY SẢN PHẨM NẰM TRONG DANH SÁCH ĐƯỢC CHỌN
                if (in_array($pid, $selectedIds)) {
                    $p = $this->productModel->getProductById($pid);
                    if ($p) {
                        $price = $p['GIA'] * (100 - $p['GIAM_GIA']) / 100;
                        $p['buy_qty'] = $qty;
                        $p['display_price'] = $price;
                        $cartItems[] = $p;
                        $totalMoney += $price * $qty;
                    }
                }
            }
        } else {
            header("Location: index.php");
            exit;
        }

        // Nếu không có sản phẩm nào hợp lệ (do user gõ URL sai)
        if (empty($cartItems)) {
            echo "<script>alert('Không có sản phẩm nào được chọn!'); window.location.href='index.php?controller=Cart';</script>";
            exit;
        }

        // Chuẩn bị chuỗi items để truyền vào form ẩn
        $selectedItemsStr = isset($_GET['items']) ? $_GET['items'] : '';

        $this->loadView('User', 'checkout', [
            'cartItems' => $cartItems,
            'totalMoney' => $totalMoney,
            'user' => $user,
            'isDirect' => (isset($_GET['mode']) && $_GET['mode'] == 'direct'),
            'selectedItemsStr' => $selectedItemsStr
        ]);
    }

    // NGUYEN VAN A 	STK 9704 0000 0000 0018   ngay 03/07	OTP	

    // Xử lý Lưu đơn hàng
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $isDirect = isset($_POST['is_direct']) && $_POST['is_direct'] == 1;

            // Lấy danh sách ID đã chọn từ Input ẩn
            $selectedIds = [];
            if (isset($_POST['selected_items']) && !empty($_POST['selected_items'])) {
                $selectedIds = explode(',', $_POST['selected_items']);
            }

            $cartItems = [];
            $totalMoney = 0;

            // TÁI TẠO LẠI GIỎ HÀNG ĐỂ TÍNH TIỀN (BẢO MẬT)
            if ($isDirect && isset($_SESSION['direct_buy'])) {
                $item = $_SESSION['direct_buy'];
                $p = $this->productModel->getProductById($item['id']);
                if ($p) {
                    $price = $p['GIA'] * (100 - $p['GIAM_GIA']) / 100;
                    $p['buy_qty'] = $item['qty'];
                    $p['display_price'] = $price;
                    $cartItems[] = $p;
                    $totalMoney = $price * $item['qty'];
                }
            } elseif (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    // Lọc lại lần nữa khi submit
                    if (in_array($pid, $selectedIds)) {
                        $p = $this->productModel->getProductById($pid);
                        if ($p) {
                            $price = $p['GIA'] * (100 - $p['GIAM_GIA']) / 100;
                            $p['buy_qty'] = $qty;
                            $p['display_price'] = $price;
                            $cartItems[] = $p;
                            $totalMoney += $price * $qty;
                        }
                    }
                }
            }

            if (empty($cartItems)) die("Lỗi: Giỏ hàng trống hoặc không hợp lệ.");

            // Dữ liệu đơn hàng
            $orderData = [
                'ten_nguoi_nhan' => $_POST['ten_nguoi_nhan'],
                'sdt_nguoi_nhan' => $_POST['sdt_nguoi_nhan'],
                'dia_chi' => $_POST['dia_chi'],
                'tong_tien' => $totalMoney
            ];

            $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';

            if ($paymentMethod == 'momo') {
                // === XỬ LÝ MOMO ===

                // Đóng gói dữ liệu cần thiết để gửi đi và nhận lại sau khi thanh toán
                // Ta cần lưu: thông tin người nhận, danh sách ID sản phẩm đã chọn, cờ mua ngay
                $extraDataArr = [
                    'orderData' => $orderData,
                    'selectedIds' => $selectedIds,
                    'isDirect' => $isDirect
                ];
                $extraDataBase64 = base64_encode(json_encode($extraDataArr));

                // Gọi hàm xử lý thanh toán MoMo
                $this->momo_payment($totalMoney, $extraDataBase64);
            } else {
                // === XỬ LÝ COD (Thanh toán khi nhận hàng) ===

                $result = $this->orderModel->createOrder($_SESSION['user']['id'], $orderData, $cartItems);

                if ($result) {
                    // Xóa giỏ hàng sau khi mua thành công
                    if ($isDirect) {
                        unset($_SESSION['direct_buy']);
                    } else {
                        foreach ($selectedIds as $id) {
                            if (isset($_SESSION['cart'][$id])) {
                                unset($_SESSION['cart'][$id]);
                            }
                        }
                    }
                    header("Location: index.php?controller=Order&action=success&id=$result");
                } else {
                    echo "Đặt hàng thất bại. Vui lòng thử lại!";
                }
            }
        }
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function momo_payment($amount, $extraData)
    {

        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

        $orderInfo = "Thanh toán qua ATM MoMo";
        $amount = (string)$amount; // Lấy từ tham số truyền vào, KHÔNG lấy từ $_POST
        $orderId = time() . "";

        $domain = "http://localhost/Electronics%20shop";
        $redirectUrl = $domain . "/index.php?controller=Order&action=momoReturn";
        $ipnUrl = $domain . "/index.php?controller=Order&action=momoReturn";
        //$extraData = "";

        $requestId = time() . "";
        $requestType = "payWithATM";
        // $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);

        if (isset($jsonResult['payUrl'])) {
            header('Location: ' . $jsonResult['payUrl']);
            exit;
        } else {
            // In lỗi ra màn hình để debug
            echo "Lỗi MoMo: " . (isset($jsonResult['message']) ? $jsonResult['message'] : 'Không xác định');
            die();
        }
    }

    // --- Xử lý khi MoMo trả về ---
    public function momoReturn()
    {
        // 1. Kiểm tra trạng thái giao dịch (Hỗ trợ cả V2 và Legacy)
        $code = isset($_GET['resultCode']) ? $_GET['resultCode'] : (isset($_GET['errorCode']) ? $_GET['errorCode'] : -1);

        if ($code == '0') {

            // --- XỬ LÝ LỖI EXTRA DATA (QUAN TRỌNG) ---
            $rawExtra = isset($_GET['extraData']) ? $_GET['extraData'] : '';

            // Thử giải mã lần 1: Chuẩn
            $jsonString = base64_decode($rawExtra);
            $dataArr = json_decode($jsonString, true);

            // Thử giải mã lần 2: Nếu lỗi, thay khoảng trắng bằng dấu + (Lỗi phổ biến nhất khi qua URL)
            if (!$dataArr) {
                $fixedExtra = str_replace(' ', '+', $rawExtra);
                $jsonString = base64_decode($fixedExtra);
                $dataArr = json_decode($jsonString, true);
            }

            // Thử giải mã lần 3: Dùng urldecode phòng trường hợp bị encode 2 lần
            if (!$dataArr) {
                $jsonString = base64_decode(urldecode($rawExtra));
                $dataArr = json_decode($jsonString, true);
            }

            // Nếu vẫn không được thì in ra để kiểm tra
            if (!$dataArr) {
                echo "<h3 style='color:red'>Lỗi dữ liệu nghiêm trọng!</h3>";
                echo "<p>Web không thể đọc thông tin đơn hàng trả về từ MoMo.</p>";
                echo "<strong>Dữ liệu nhận được:</strong> <br><textarea rows='5' cols='100'>$rawExtra</textarea>";
                die();
            }

            // --- NẾU ĐÃ CÓ DATA THÌ TIẾP TỤC ---
            $orderData = $dataArr['orderData'];
            $selectedIds = $dataArr['selectedIds'];
            $isDirect = $dataArr['isDirect'];

            // Tái tạo giỏ hàng
            $cartItems = [];
            if ($isDirect && isset($_SESSION['direct_buy'])) {
                $item = $_SESSION['direct_buy'];
                $p = $this->productModel->getProductById($item['id']);
                if ($p) {
                    $price = $p['GIA'] * (100 - $p['GIAM_GIA']) / 100;
                    $p['buy_qty'] = $item['qty'];
                    $p['display_price'] = $price;
                    $cartItems[] = $p;
                }
            } elseif (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    if (in_array($pid, $selectedIds)) {
                        $p = $this->productModel->getProductById($pid);
                        if ($p) {
                            $price = $p['GIA'] * (100 - $p['GIAM_GIA']) / 100;
                            $p['buy_qty'] = $qty;
                            $p['display_price'] = $price;
                            $cartItems[] = $p;
                        }
                    }
                }
            }

            // Lưu đơn hàng
            $result = $this->orderModel->createOrder($_SESSION['user']['id'], $orderData, $cartItems);

            if ($result) {
                if ($isDirect) {
                    unset($_SESSION['direct_buy']);
                } else {
                    foreach ($selectedIds as $id) {
                        if (isset($_SESSION['cart'][$id])) unset($_SESSION['cart'][$id]);
                    }
                }
                header("Location: index.php?controller=Order&action=success&id=$result");
            } else {
                echo "Thanh toán thành công nhưng lỗi lưu database. Mã đơn: " . (isset($_GET['orderId']) ? $_GET['orderId'] : 'N/A');
            }
        } else {
            // Thanh toán thất bại
            $message = isset($_GET['message']) ? $_GET['message'] : 'Giao dịch bị từ chối';
            echo "<script>alert('Thanh toán thất bại: $message'); window.location.href='index.php?controller=Cart';</script>";
        }
    }

    public function success()
    {
        $this->loadView('User', 'order_success');
    }

    public function history()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }
        $orders = $this->orderModel->getOrdersByUserId($_SESSION['user']['id']);
        $this->loadView('User', 'order_history', ['orders' => $orders]);
    }

    // --- MỚI: Khách hàng hủy đơn ---
    public function cancel()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }

        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // 1. Kiểm tra đơn hàng có tồn tại và thuộc về user này không
        $order = $this->orderModel->getOrderById($orderId);

        if ($order && $order['MA_NGUOI_DUNG'] == $_SESSION['user']['id']) {

            // 2. Chỉ cho hủy khi đơn hàng đang "Chờ xác nhận"
            if ($order['TRANG_THAI'] == 'cho_xac_nhan') {
                $this->orderModel->updateStatus($orderId, 'da_huy');
                $msg = "Đã hủy đơn hàng #$orderId thành công!";
            } else {
                $msg = "Không thể hủy đơn hàng đã được xử lý!";
            }
        } else {
            $msg = "Đơn hàng không hợp lệ!";
        }

        // Quay lại lịch sử đơn hàng kèm thông báo
        header("Location: index.php?controller=Order&action=history&msg=" . urlencode($msg));
    }

    // --- MỚI: Xem chi tiết đơn hàng (User) ---
    public function detail()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=Auth&action=login");
            exit;
        }

        $orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // 1. Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($orderId);

        // 2. BẢO MẬT: Kiểm tra quyền sở hữu
        // Nếu đơn hàng không tồn tại HOẶC không phải của người này -> Chặn
        if (!$order || $order['MA_NGUOI_DUNG'] != $_SESSION['user']['id']) {
            die("Bạn không có quyền xem đơn hàng này!"); // Hoặc chuyển hướng về trang chủ
        }

        // 3. Lấy chi tiết sản phẩm
        $details = $this->orderModel->getOrderDetails($orderId);

        $this->loadView('User', 'order_detail', [
            'order' => $order,
            'details' => $details
        ]);
    }
}
