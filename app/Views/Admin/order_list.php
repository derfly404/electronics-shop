<div class="page-header">
    <h2>Quản lý đơn hàng</h2>
</div>

<div class="search-box-admin" style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="module" value="Admin">
        <input type="hidden" name="controller" value="Order">

        <input type="text" name="keyword" class="form-control"
            placeholder="Nhập mã đơn, tên khách hoặc SĐT..."
            value="<?= isset($keyword) ? $keyword : '' ?>" style="flex: 1;">

        <button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm</button>

        <?php if (!empty($keyword)): ?>
            <a href="index.php?module=Admin&controller=Order" class="btn btn-danger">X</a>
        <?php endif; ?>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Khách hàng</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?= $o['MA_DON_HANG'] ?></td>
                <td>
                    <?= $o['TEN_NGUOI_NHAN'] ?> <br>
                    <small><?= $o['SDT_NGUOI_NHAN'] ?></small>
                </td>
                <td><?= date('d/m/Y', strtotime($o['NGAY_GIO'])) ?></td>
                <td><?= number_format($o['TONG_TIEN']) ?>đ</td>
                <td>
                    <span class="badge badge-<?= $o['TRANG_THAI'] ?>"><?= $o['TRANG_THAI'] ?></span>
                </td>
                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="index.php?module=Admin&controller=Order&action=detail&id=<?= $o['MA_DON_HANG'] ?>"
                            class="btn btn-primary btn-sm" title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="index.php?module=Admin&controller=Order&action=delete&id=<?= $o['MA_DON_HANG'] ?>"
                            class="btn btn-danger btn-sm"
                            title="Xóa vĩnh viễn đơn hàng này"
                            onclick="return confirm('CẢNH BÁO QUAN TRỌNG:\n\nBạn đang chọn xóa vĩnh viễn đơn hàng #<?= $o['MA_DON_HANG'] ?>.\n\n- Dữ liệu doanh thu của đơn này sẽ biến mất.\n- Khách hàng sẽ không còn thấy đơn này trong lịch sử.\n\nBạn có chắc chắn muốn tiếp tục?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>