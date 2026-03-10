<?php
class HomeController extends BaseController
{
    private $productModel;
    private $categoryModel; // 1. Khai báo thêm

    public function __construct()
    {
        $this->productModel = $this->loadModel('ProductModel');
        $this->categoryModel = $this->loadModel('CategoryModel'); // 2. Load Model
    }

    public function index()
    {
        // Lấy sản phẩm nổi bật & mới
        // YÊU CẦU: Lấy tối đa 8 sản phẩm cho mỗi mục
        $featuredProducts = $this->productModel->getFeaturedProducts(8); // Sửa thành 8
        $newProducts = $this->productModel->getNewProducts(8);

        // 3. Lấy danh sách danh mục
        $categories = $this->categoryModel->getAllCategories(); // Lấy 6 danh mục đầu tiên

        // 4. Truyền sang View
        $this->loadView('User', 'home', [
            'featuredProducts' => $featuredProducts,
            'newProducts' => $newProducts,
            'categories' => $categories // Truyền biến này
        ]);
    }
}
