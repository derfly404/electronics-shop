<?php
class OrderController extends BaseController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = $this->loadModel('OrderModel');
    }

    // 1. Danh sách đơn hàng
    public function index()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : null;
        $orders = $this->orderModel->getAllOrders($keyword);

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'order_list',
            'orders' => $orders,
            'keyword' => $keyword
        ]);
    }

    // 2. Chi tiết đơn hàng
    public function detail()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $order = $this->orderModel->getOrderById($id);
        $details = $this->orderModel->getOrderDetails($id);

        if (!$order) die("Đơn hàng không tồn tại");

        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'order_detail',
            'order' => $order,
            'details' => $details
        ]);
    }

    // 3. Cập nhật trạng thái
    // --- CẬP NHẬT HÀM update_status ---
    public function update_status()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['order_id'];
            $status = $_POST['status'];

            $result = $this->orderModel->updateStatus($id, $status);

            if ($result === 'locked') {
                // Nếu bị khóa -> Báo lỗi
                header("Location: index.php?module=Admin&controller=Order&action=detail&id=$id&msg=locked");
            } elseif ($result === true) {
                // Thành công
                header("Location: index.php?module=Admin&controller=Order&action=detail&id=$id&msg=success");
            } else {
                // Lỗi hệ thống
                header("Location: index.php?module=Admin&controller=Order&action=detail&id=$id&msg=error");
            }
        }
    }

    // --- MỚI: Action Xóa đơn hàng ---
    public function delete()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id) {
            if ($this->orderModel->deleteOrder($id)) {
                $msg = "Đã xóa vĩnh viễn đơn hàng #$id";
                $type = "success";
            } else {
                $msg = "Lỗi: Không thể xóa đơn hàng.";
                $type = "error";
            }
            // Quay lại trang danh sách kèm thông báo
            header("Location: index.php?module=Admin&controller=Order&msg=" . urlencode($msg) . "&type=" . $type);
        } else {
            header("Location: index.php?module=Admin&controller=Order");
        }
    }
}
