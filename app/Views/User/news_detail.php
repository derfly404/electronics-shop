<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container news-detail-page">
    <div class="row">
        <div class="col-12 news-wrapper">
            <h1 class="news-title"><?= $news['TIEU_DE'] ?></h1>

            <div class="news-meta-detail">
                <span><i class="fa fa-user"></i> Đăng bởi: <strong><?= $news['NGUOI_DANG'] ?></strong></span>
                <span><i class="fa fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($news['NGAY_DANG'])) ?></span>
                <span><i class="fa fa-eye"></i> <?= $news['LUOT_XEM'] ?> lượt xem</span>
            </div>

            <div class="news-body">
                <p class="news-summary"><strong><?= $news['TOM_TAT'] ?></strong></p>

                <?php if (!empty($news['HINH_DAI_DIEN'])): ?>
                    <div class="news-main-img">
                        <img src="./public/uploads/<?= $news['HINH_DAI_DIEN'] ?>" alt="<?= $news['TIEU_DE'] ?>">
                    </div>
                <?php endif; ?>

                <div class="content-text">
                    <?= nl2br($news['NOI_DUNG']) ?>
                </div>
            </div>

            <div id="comments" class="news-comments-section" style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 30px;">
                <h3>Bình luận (<?= $commentCount ?>)</h3>

                <div class="comment-form-box" style="margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 5px;">
                    <?php if (isset($_SESSION['user'])): ?>
                        <form action="index.php?controller=News&action=submit_comment" method="POST">
                            <input type="hidden" name="news_id" value="<?= $news['MA_TIN_TUC'] ?>">
                            <div class="form-group" style="display: flex; gap: 10px;">
                                <img src="https://ui-avatars.com/api/?name=<?= $_SESSION['user']['TEN'] ?>&background=random"
                                    style="width: 40px; height: 40px; border-radius: 50%;">
                                <div style="flex: 1;">
                                    <textarea name="noi_dung" class="form-control" rows="2" placeholder="Viết bình luận của bạn..." required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                                    <button type="submit" class="btn btn-primary" style="margin-top: 10px; padding: 5px 15px;">Gửi bình luận</button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <p>Vui lòng <a href="index.php?controller=Auth&action=login" style="color: #d0011b; font-weight: bold;">đăng nhập</a> để tham gia bình luận.</p>
                    <?php endif; ?>
                </div>

                <div class="comment-list">
                    <?php if (empty($comments)): ?>
                        <p style="color: #777; font-style: italic;">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                    <?php else: ?>
                        <?php foreach ($comments as $cmt): ?>
                            <div class="comment-item" id="comment-row-<?= $cmt['MA_BINH_LUAN'] ?>">
                                <div class="cmt-avatar">
                                    <img src="https://ui-avatars.com/api/?name=<?= $cmt['TEN'] ?>&background=random" alt="">
                                </div>

                                <div class="cmt-content">
                                    <div class="cmt-header">
                                        <strong><?= $cmt['TEN'] ?></strong>
                                        <span class="cmt-time">
                                            <?= date('d/m/Y H:i', strtotime($cmt['NGAY_BINH_LUAN'])) ?>
                                        </span>
                                    </div>

                                    <div class="cmt-text" id="cmt-text-<?= $cmt['MA_BINH_LUAN'] ?>">
                                        <?= nl2br(htmlspecialchars($cmt['NOI_DUNG'])) ?>
                                    </div>

                                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $cmt['MA_NGUOI_DUNG']): ?>
                                        <form action="index.php?controller=News&action=update_comment" method="POST"
                                            class="cmt-edit-form" id="cmt-edit-<?= $cmt['MA_BINH_LUAN'] ?>" style="display: none;">
                                            <input type="hidden" name="comment_id" value="<?= $cmt['MA_BINH_LUAN'] ?>">
                                            <input type="hidden" name="news_id" value="<?= $news['MA_TIN_TUC'] ?>">

                                            <textarea name="noi_dung" class="form-control" rows="2"><?= $cmt['NOI_DUNG'] ?></textarea>

                                            <div style="margin-top: 5px; text-align: right;">
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit(<?= $cmt['MA_BINH_LUAN'] ?>)">Hủy</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Lưu</button>
                                            </div>
                                        </form>

                                        <div class="cmt-actions" id="cmt-actions-<?= $cmt['MA_BINH_LUAN'] ?>">
                                            <a href="javascript:void(0)" onclick="showEdit(<?= $cmt['MA_BINH_LUAN'] ?>)">Chỉnh sửa</a>
                                            <span class="dot">•</span>
                                            <a href="index.php?controller=News&action=delete_comment&id=<?= $cmt['MA_BINH_LUAN'] ?>&news_id=<?= $news['MA_TIN_TUC'] ?>"
                                                onclick="return confirm('Xóa bình luận này?')" style="color: #dc3545;">Xóa</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="news-footer-action">
                <a href="index.php?controller=News" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại danh sách tin tức
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function showEdit(id) {
        // Ẩn nội dung cũ và nút thao tác
        document.getElementById('cmt-text-' + id).style.display = 'none';
        document.getElementById('cmt-actions-' + id).style.display = 'none';

        // Hiện form sửa
        document.getElementById('cmt-edit-' + id).style.display = 'block';
    }

    function cancelEdit(id) {
        // Hiện lại nội dung cũ và nút thao tác
        document.getElementById('cmt-text-' + id).style.display = 'block';
        document.getElementById('cmt-actions-' + id).style.display = 'block';

        // Ẩn form sửa
        document.getElementById('cmt-edit-' + id).style.display = 'none';
    }
</script>

<?php require_once './app/Views/Layouts/footer.php'; ?>