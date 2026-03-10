<div class="page-header">
    <h2>Quản lý bình luận & Đánh giá</h2>
</div>

<div class="search-box-admin" style="margin-bottom: 20px; background: #fff; padding: 15px;">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="module" value="Admin">
        <input type="hidden" name="controller" value="Review">
        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm, người dùng..." value="<?= isset($keyword) ? $keyword : '' ?>" style="flex: 1;">
        <button type="submit" class="btn btn-secondary">Tìm</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Khách hàng</th>
            <th>Sản phẩm</th>
            <th>Đánh giá</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reviews as $r): ?>
            <tr>
                <td><?= $r['MA_DANH_GIA'] ?></td>
                <td><?= $r['NGUOI_DUNG'] ?></td>
                <td>
                    <a href="index.php?controller=Product&action=detail&id=<?= $r['MA_SAN_PHAM'] ?>" target="_blank">
                        <?= $r['TEN_SAN_PHAM'] ?>
                    </a>
                </td>
                <td>
                    <span style="color: #ffc107; font-weight: bold;">
                        <?= $r['SO_SAO'] ?> <i class="fa fa-star"></i>
                    </span>
                </td>
                <td><?= $r['NOI_DUNG'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['NGAY_GIO'])) ?></td>
                <td>
                    <a href="index.php?module=Admin&controller=Review&action=delete&id=<?= $r['MA_DANH_GIA'] ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Bạn có chắc muốn xóa bình luận này?')">
                        <i class="fa fa-trash"></i> Xóa
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>