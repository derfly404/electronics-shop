<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Electronics Shop</title>
    <link rel="stylesheet" href="./public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="brand">
                <h3><i class="fa fa-cogs"></i> Quản Trị</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php?module=Admin&controller=Dashboard"><i class="fa fa-tachometer-alt"></i> Tổng quan</a></li>
                    <li><a href="index.php?module=Admin&controller=Category"><i class="fa fa-list"></i> Danh mục</a></li>
                    <li><a href="index.php?module=Admin&controller=Product"><i class="fa fa-box"></i> Sản phẩm</a></li>
                    <li><a href="index.php?module=Admin&controller=Order"><i class="fa fa-shopping-bag"></i> Đơn hàng</a></li>
                    <li><a href="index.php?module=Admin&controller=User"><i class="fa fa-users"></i> Người dùng</a></li>

                    <li>
                        <a href="index.php?module=Admin&controller=News">
                            <i class="fa fa-newspaper"></i> QL Tin tức
                        </a>
                    </li>

                    <li>
                        <a href="index.php?module=Admin&controller=Review">
                            <i class="fa fa-star"></i> Đánh giá SP
                        </a>
                    </li>

                    <li>
                        <a href="index.php?module=Admin&controller=Comment">
                            <i class="fa fa-comments"></i> Bình luận Tin tức
                        </a>
                    </li>

                    <li class="logout"><a href="index.php"><i class="fa fa-arrow-left"></i> Về Website</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <span>Xin chào, <b>Admin</b></span>
                <a href="index.php?controller=Auth&action=logout" class="btn-logout">Đăng xuất</a>
            </header>

            <div class="content-body">
                <?php
                if (isset($content_view)) {
                    require_once "./app/Views/Admin/" . $content_view . ".php";
                }
                ?>
            </div>
        </main>
    </div>
</body>

</html>