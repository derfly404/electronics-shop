<?php
// Kiểm tra xem đang ở chế độ Sửa hay Thêm
$isEdit = isset($news);
$title = $isEdit ? "Cập nhật bài viết" : "Thêm bài viết mới";
$action = $isEdit ? "update" : "store";
?>

<div class="page-header">
    <h2><?= $title ?></h2>
    <a href="index.php?module=Admin&controller=News" class="btn btn-secondary">Quay lại</a>
</div>

<div class="row">
    <div class="col-12" style="width: 100%;">
        <form action="index.php?module=Admin&controller=News&action=<?= $action ?>" method="POST" enctype="multipart/form-data" class="form-group">

            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $news['MA_TIN_TUC'] ?>">
            <?php endif; ?>

            <div class="row">
                <div class="col-8" style="flex: 0 0 65%;">
                    <div class="form-group mb-3">
                        <label class="form-label">Tiêu đề bài viết</label>
                        <input type="text" name="tieu_de" required class="form-control"
                            placeholder="Nhập tiêu đề tin tức..."
                            value="<?= $isEdit ? $news['TIEU_DE'] : '' ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Tóm tắt ngắn</label>
                        <textarea name="tom_tat" rows="3" class="form-control"
                            placeholder="Mô tả ngắn gọn nội dung..."><?= $isEdit ? $news['TOM_TAT'] : '' ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Nội dung chi tiết</label>
                        <textarea name="noi_dung" rows="15" class="form-control" required
                            placeholder="Nội dung bài viết..."><?= $isEdit ? $news['NOI_DUNG'] : '' ?></textarea>
                    </div>
                </div>

                <div class="col-4" style="flex: 0 0 32%;">
                    <div class="form-group mb-3" style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
                        <label class="form-label">Hình ảnh đại diện</label>

                        <?php if ($isEdit && !empty($news['HINH_DAI_DIEN'])): ?>
                            <div class="mb-3">
                                <img src="./public/uploads/<?= $news['HINH_DAI_DIEN'] ?>" style="width: 100%; border-radius: 5px; border: 1px solid #ddd;">
                                <p style="font-size: 12px; color: #666; margin-top: 5px;">Ảnh hiện tại</p>
                            </div>
                        <?php endif; ?>

                        <input type="file" name="hinh_anh" class="form-control" <?= $isEdit ? '' : 'required' ?>>
                        <?php if ($isEdit): ?>
                            <small style="color: #888;">Để trống nếu không muốn thay đổi ảnh.</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label">Trạng thái</label>
                        <div class="checkbox-group">
                            <input type="checkbox" name="trang_thai" id="status"
                                <?= ($isEdit && $news['TRANG_THAI'] == 1) || !$isEdit ? 'checked' : '' ?>>
                            <label for="status" style="cursor: pointer;">Hiển thị bài viết</label>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            <?= $isEdit ? 'Lưu thay đổi' : 'Đăng bài viết' ?>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>