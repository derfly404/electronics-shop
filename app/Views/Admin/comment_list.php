<div class="page-header">
    <h2>Quản lý bình luận Tin tức</h2>
</div>

<div class="search-box-admin" style="margin-bottom: 20px; background: #fff; padding: 15px;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="module" value="Admin">
        <input type="hidden" name="controller" value="Comment">
        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo bài viết, người dùng..." value="<?= isset($keyword) ? $keyword : '' ?>" style="flex: 1;">
        <button type="submit" class="btn btn-secondary">Tìm</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Người bình luận</th>
            <th>Bài viết</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $c): ?>
            <tr>
                <td><?= $c['MA_BINH_LUAN'] ?></td>
                <td><?= $c['TEN'] ?></td>
                <td>
                    <a href="index.php?controller=News&action=detail&id=<?= $c['MA_TIN_TUC'] ?>" target="_blank">
                        <?= substr($c['TIEU_DE'], 0, 30) ?>...
                    </a>
                </td>
                <td><?= $c['NOI_DUNG'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($c['NGAY_BINH_LUAN'])) ?></td>
                <td>
                    <a href="index.php?module=Admin&controller=Comment&action=delete&id=<?= $c['MA_BINH_LUAN'] ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Xóa bình luận này?')">
                        <i class="fa fa-trash"></i> Xóa
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>