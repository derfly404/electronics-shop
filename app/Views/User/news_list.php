<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container news-page">
    <div class="page-header-title">
        <h2>Tin tức công nghệ</h2>
        <p>Cập nhật xu hướng, đánh giá sản phẩm và mẹo hay mỗi ngày</p>
    </div>

    <?php if (empty($newsList)): ?>
        <p class="alert-info">Chưa có bài viết nào được đăng.</p>
    <?php else: ?>
        <div class="news-grid">
            <?php foreach ($newsList as $news): ?>
                <div class="news-card">
                    <div class="news-img">
                        <a href="index.php?controller=News&action=detail&id=<?= $news['MA_TIN_TUC'] ?>">
                            <?php if (!empty($news['HINH_DAI_DIEN'])): ?>
                                <img src="./public/uploads/<?= $news['HINH_DAI_DIEN'] ?>" alt="<?= $news['TIEU_DE'] ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x200?text=News" alt="No Image">
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="news-content">
                        <div class="news-meta">
                            <i class="fa fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($news['NGAY_DANG'])) ?>
                        </div>
                        <h3>
                            <a href="index.php?controller=News&action=detail&id=<?= $news['MA_TIN_TUC'] ?>">
                                <?= $news['TIEU_DE'] ?>
                            </a>
                        </h3>
                        <p class="news-desc">
                            <?= substr($news['TOM_TAT'], 0, 100) ?>...
                        </p>
                        <a href="index.php?controller=News&action=detail&id=<?= $news['MA_TIN_TUC'] ?>" class="read-more">
                            Xem chi tiết <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>