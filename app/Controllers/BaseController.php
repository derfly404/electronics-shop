<?php
class BaseController
{
    // Hàm gọi View (folder: 'User' hoặc 'Admin')
    // --- CẬP NHẬT HÀM LOADVIEW ---
    public function loadView($folder, $viewName, $data = [])
    {
        // 1. Tự động lấy danh mục cho Header (Dữ liệu tươi từ SQL)
        // Kiểm tra xem biến categories đã có chưa, nếu chưa thì lấy
        if (!isset($data['globalCategories'])) {
            // Load model thủ công tại đây để tránh vòng lặp vô hạn
            require_once './app/Models/CategoryModel.php';
            $catModel = new CategoryModel();
            $data['globalCategories'] = $catModel->getAllCategories();
        }

        // 2. Giải nén dữ liệu ra biến
        extract($data);

        $viewPath = "./app/Views/$folder/$viewName.php";

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die("View '$viewName' không tồn tại trong thư mục '$folder'");
        }
    }

    // Hàm gọi Model
    public function loadModel($modelName)
    {
        $modelPath = "./app/Models/$modelName.php";
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $modelName();
        } else {
            die("Model '$modelName' không tồn tại");
        }
    }
}
