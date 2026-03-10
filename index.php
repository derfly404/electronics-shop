<?php
// Đặt timezone cho toàn bộ ứng dụng
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bật error reporting để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// 1. Load cấu hình Database trước (Vì BaseModel cần kết nối DB)
require_once './app/Config/Database.php';

require_once './app/Config/reader.php';

// 2. Load BaseModel (QUAN TRỌNG: Phải load cái này trước khi Controller gọi bất kỳ Model con nào)
require_once './app/Models/BaseModel.php';

// 3. Load BaseController
require_once './app/Controllers/BaseController.php';

// --- Phần xử lý Router bên dưới giữ nguyên ---
$module = isset($_GET['module']) ? $_GET['module'] : 'User';
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'HomeController';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';

$controllerPath = "./app/Controllers/$module/$controllerName.php";

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
        } else {
            die("Lỗi: Action '$actionName' không tồn tại.");
        }
    } else {
        die("Lỗi: Class '$controllerName' không tìm thấy.");
    }
} else {
    die("Lỗi: Controller '$controllerName' không tồn tại. (Path: $controllerPath)");
}
