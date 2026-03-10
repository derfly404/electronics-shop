<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container product-page-container">
    <div class="row">

        <div class="col-4 sidebar-filter">

            <div class="filter-box">
                <h3>Danh mục</h3>
                <ul class="category-list">
                    <li>
                        <a href="index.php?controller=Product" class="<?= !$currentCat ? 'active' : '' ?>">
                            <i class="fa fa-th-large" style="width: 20px;"></i> Tất cả
                        </a>
                    </li>
                    <?php foreach ($categories as $c): ?>
                        <li>
                            <a href="index.php?controller=Product&category_id=<?= $c['MA_DANH_MUC'] ?>"
                                class="<?= ($currentCat == $c['MA_DANH_MUC']) ? 'active' : '' ?>">

                                <?php if (!empty($c['HINH_ANH'])): ?>
                                    <img src="./public/uploads/<?= $c['HINH_ANH'] ?>" alt="" class="cat-thumb">
                                <?php else: ?>
                                    <i class="fa fa-folder" style="width: 20px;"></i>
                                <?php endif; ?>

                                <?= $c['TEN_DANH_MUC'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="filter-box" style="margin-top: 20px;">
                <h3>Lọc theo giá</h3>
                <form action="index.php" method="GET">
                    <input type="hidden" name="controller" value="Product">

                    <?php if ($currentCat): ?>
                        <input type="hidden" name="category_id" value="<?= $currentCat ?>">
                    <?php endif; ?>

                    <div class="price-input-group">
                        <label>Từ:</label>
                        <input type="number" name="min_price" value="<?= $currentMin ?>" placeholder="0đ" class="form-control" required>
                    </div>

                    <div class="price-input-group">
                        <label>Đến:</label>
                        <input type="number" name="max_price" value="<?= $currentMax ?>" placeholder="10.000.000đ" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-filter">
                        <i class="fa fa-filter"></i> Áp dụng
                    </button>

                    <a href="index.php?controller=Product" class="btn-reset">Xóa bộ lọc</a>
                </form>
            </div>

            <div class="sidebar-banner">
                <img src="./public/images/sidebar-banner.jpg" alt="" style="width:100%; border-radius:5px; margin-top:20px;">
            </div>
        </div>

        <div class="col-6 main-product-list">
            <h2 class="page-header-title"><?= $pageTitle ?> <small style="font-size:14px; font-weight:normal;">(Trang <?= $currentPage ?>/<?= $totalPages > 0 ? $totalPages : 1 ?>)</small></h2>

            <?php if (empty($products)): ?>
                <div class="alert-info">
                    <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                </div>
            <?php else: ?>
                <div class="product-grid-3-col">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card">

                            <div class="product-img">
                                <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>">
                                    <img src="./public/uploads/<?= $p['HINH_ANH'] ?>" alt="<?= $p['TEN_SAN_PHAM'] ?>">
                                </a>

                                <?php if ($p['SO_LUONG_TON'] <= 0): ?>
                                    <span class="badge-out">HẾT HÀNG</span>
                                <?php elseif ($p['GIAM_GIA'] > 0): ?>
                                    <span class="badge-sale">-<?= (float)$p['GIAM_GIA'] ?>%</span>
                                <?php endif; ?>
                            </div>

                            <div class="product-info">
                                <h3><a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>"><?= $p['TEN_SAN_PHAM'] ?></a></h3>
                                <div class="price">
                                    <?php if ($p['GIAM_GIA'] > 0): ?>
                                        <del><?= number_format($p['GIA']) ?>đ</del>
                                        <span class="curr-price"><?= number_format($p['GIA'] * (100 - $p['GIAM_GIA']) / 100) ?>đ</span>
                                    <?php else: ?>
                                        <span class="curr-price"><?= number_format($p['GIA']) ?>đ</span>
                                    <?php endif; ?>
                                </div>

                                <div class="product-actions">
                                    <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>" class="btn-detail">
                                        Xem chi tiết
                                    </a>

                                    <?php if ($p['SO_LUONG_TON'] > 0): ?>
                                        <a href="index.php?controller=Cart&action=add&id=<?= $p['MA_SAN_PHAM'] ?>"
                                            class="btn-cart-small" title="Thêm vào giỏ">
                                            <i class="fa fa-cart-plus"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="btn-cart-small disabled" title="Tạm hết hàng">
                                            <i class="fa fa-ban"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 0): ?>
                    <div class="pagination">
                        <?php
                        // Giữ lại bộ lọc khi chuyển trang
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = http_build_query($queryParams);
                        $prefix = "index.php?" . ($queryString ? $queryString . "&" : "controller=Product&");
                        ?>

                        <?php if ($currentPage > 1): ?>
                            <a href="<?= $prefix ?>page=<?= $currentPage - 1 ?>">&laquo; Trước</a>
                        <?php else: ?>
                            <a href="#" class="disabled">&laquo; Trước</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= $prefix ?>page=<?= $i ?>" class="<?= ($i == $currentPage) ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= $prefix ?>page=<?= $currentPage + 1 ?>">Sau &raquo;</a>
                        <?php else: ?>
                            <a href="#" class="disabled">Sau &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>