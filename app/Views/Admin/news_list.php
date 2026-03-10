<div class="page-header">
    <h2>Quản lý bài viết</h2>
    <a href="index.php?module=Admin&controller=News&action=create" class="btn btn-primary">Viết bài mới</a>
</div>

<div class="search-box-admin" style="margin-bottom: 20px; background: #fff; padding: 15px;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="module" value="Admin">
        <input type="hidden" name="controller" value="News">
        <input type="text" name="keyword" class="form-control" placeholder="Tìm tiêu đề hoặc người đăng..." value="<?= isset($keyword) ? $keyword : '' ?>" style="flex: 1;">
        <button type="submit" class="btn btn-secondary">Tìm kiếm</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tiêu đề</th>
            <th>Người đăng</th>
            <th>Ngày đăng</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($newsList as $n): ?>
            <tr>
                <td><?= $n['MA_TIN_TUC'] ?></td>
                <td><img src="./public/uploads/<?= $n['HINH_DAI_DIEN'] ?>" width="60"></td>
                <td><?= $n['TIEU_DE'] ?></td>
                <td><?= $n['NGUOI_DANG'] ?></td>
                <td><?= date('d/m/Y', strtotime($n['NGAY_DANG'])) ?></td>
                <td><?= ($n['TRANG_THAI'] == 1) ? 'Hiển thị' : 'Ẩn' ?></td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="index.php?module=Admin&controller=News&action=edit&id=<?= $n['MA_TIN_TUC'] ?>"
                            class="btn btn-warning btn-sm" title="Sửa bài viết">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="index.php?module=Admin&controller=News&action=delete&id=<?= $n['MA_TIN_TUC'] ?>"
                            class="btn btn-danger" onclick="return confirm('Xóa bài viết này?')">Xóa</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>