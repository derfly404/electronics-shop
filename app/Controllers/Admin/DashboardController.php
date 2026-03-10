<?php
class DashboardController extends BaseController
{
    private $orderModel;
    private $productModel;
    private $userModel;

    public function __construct()
    {
        $this->orderModel = $this->loadModel('OrderModel');
        $this->productModel = $this->loadModel('ProductModel');
        $this->userModel = $this->loadModel('UserModel');
    }

    public function index()
    {
        // Lấy ngày từ URL
        $fromDate = isset($_GET['from_date']) ? $_GET['from_date'] : null;
        $toDate   = isset($_GET['to_date'])   ? $_GET['to_date']   : null;

        // Gọi Model và TRUYỀN NGÀY VÀO TẤT CẢ CÁC HÀM

        // Tổng doanh thu
        $revenue = $this->orderModel->getRevenue($fromDate, $toDate);

        // Tổng đơn hàng
        $totalOrders = $this->orderModel->countOrders($fromDate, $toDate);

        // Biểu đồ
        $chartData = $this->orderModel->getRevenueChartData($fromDate, $toDate);

        // Top khách hàng
        $topCustomers = $this->orderModel->getTopCustomers(5, $fromDate, $toDate);

        // Top sản phẩm
        $topProducts = $this->productModel->getTopSellingProducts(5, $fromDate, $toDate);

        // (Thường user và sản phẩm tính tổng toàn hệ thống, không lọc theo ngày)
        $totalProducts = $this->productModel->countProducts();
        $totalUsers = $this->userModel->countUsers();
        $recentOrders = $this->orderModel->getRecentOrders(5, $fromDate, $toDate);

        // Load View
        $this->loadView('Layouts', 'admin_layout', [
            'content_view' => 'dashboard',
            'revenue'      => $revenue,
            'totalOrders'  => $totalOrders, // Số này sẽ thay đổi theo ngày
            'chartData'    => $chartData,   // Biểu đồ sẽ thay đổi theo ngày
            'topCustomers' => $topCustomers, // List này sẽ thay đổi
            'topProducts'  => $topProducts, // List này sẽ thay đổi

            'totalProducts' => $totalProducts,
            'totalUsers'    => $totalUsers,
            'recentOrders'  => $recentOrders
        ]);
    }
}
