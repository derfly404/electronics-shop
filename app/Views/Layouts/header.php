<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronics Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./public/css/style.css">
    <link rel="stylesheet" href="./public/css/christmas-embed.css" id="christmas-effects-css">
</head>

<body>
    <header class="header-wrapper">
        <div class="top-bar-header">
            <div class="container top-bar-inner">
                <div class="top-left">
                    <span><i class="fa fa-phone-alt"></i> Hotline: <strong>0949713958</strong></span>
                    <span class="divider">|</span>
                    <span><i class="fa fa-map-marker-alt"></i> Đường Hoàng Văn Thái, TP. Đà Nẵng</span>
                </div>
                <div class="top-right">
                    <a href="#"></a>
                </div>
            </div>
        </div>

        <div class="header-main">
            <div class="container header-inner">

                <div class="logo">
                    <a href="index.php">
                        <i class="fa fa-bolt"></i> Electronics<span class="logo-suffix">Shop</span>
                    </a>
                </div>

                <div class="header-category">
                    <button class="btn-header-cat">
                        <i class="fa fa-bars"></i> Danh mục
                    </button>

                    <ul class="header-cat-dropdown">
                        <?php if (!empty($globalCategories)): ?>
                            <?php foreach ($globalCategories as $cat): ?>
                                <li>
                                    <a href="index.php?controller=Product&category_id=<?= $cat['MA_DANH_MUC'] ?>">
                                        <?php if (!empty($cat['HINH_ANH'])): ?>
                                            <img src="./public/uploads/<?= $cat['HINH_ANH'] ?>" alt="" class="menu-cat-icon">
                                        <?php else: ?>
                                            <i class="fa fa-folder"></i>
                                        <?php endif; ?>
                                        <?= $cat['TEN_DANH_MUC'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="divider"></li>
                        <li><a href="index.php?controller=Product" style="font-weight:bold; color: var(--primary-red);">Xem tất cả <i class="fa fa-arrow-right"></i></a></li>
                    </ul>
                </div>
                <div class="search-bar">
                    <form action="index.php" method="GET">
                        <input type="hidden" name="controller" value="Product">
                        <input type="text" name="keyword" placeholder="Bạn cần tìm gì hôm nay?" required>
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </div>

                <div class="header-actions">
                    <a href="index.php" class="action-simple" title="Trang chủ"><i class="fa fa-home"></i></a>

                    <a href="index.php?controller=News" class="action-simple" title="Tin tức"><i class="fa fa-newspaper"></i></a>

                    <a href="index.php?controller=Cart" class="action-btn cart-btn">
                        <div class="icon-box">
                            <i class="fa fa-shopping-cart"></i>
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="badge-count"><?= array_sum($_SESSION['cart']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="text-box"><span>Giỏ hàng</span></div>
                    </a>

                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="user-action-container">
                            <div class="action-btn user-btn">
                                <div class="icon-box"><i class="fa fa-user-circle"></i></div>
                                <div class="text-box">
                                    <small>Hi,</small>
                                    <span><?= substr($_SESSION['user']['TEN'], 0, 6) ?>..</span>
                                </div>
                            </div>
                            <div class="header-dropdown-menu user-menu">
                                <ul>
                                    <li><a href="index.php?controller=Account"><i class="fa fa-id-card"></i> Hồ sơ cá nhân</a></li>
                                    <li><a href="index.php?controller=Order&action=history"><i class="fa fa-box-open"></i> Đơn mua</a></li>
                                    <?php if ($_SESSION['user']['ROLE'] == 1): ?>
                                        <li class="admin-link"><a href="index.php?module=Admin&controller=Dashboard"><i class="fa fa-tachometer-alt"></i> Quản trị</a></li>
                                    <?php endif; ?>
                                    <li class="divider"></li>
                                    <li><a href="index.php?controller=Auth&action=logout" class="logout-link"><i class="fa fa-sign-out-alt"></i> Đăng xuất</a></li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="index.php?controller=Auth&action=login" class="action-btn user-btn">
                            <div class="icon-box"><i class="fa fa-user"></i></div>
                            <div class="text-box"><span>Đăng nhập</span></div>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container content-wrapper">