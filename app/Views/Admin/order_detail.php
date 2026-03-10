<div class="page-header">
    <h2>Chi tiết đơn hàng #<?= $order['MA_DON_HANG'] ?></h2>
    <a href="index.php?module=Admin&controller=Order" class="btn btn-secondary">Quay lại</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'success'): ?>
        <div class="alert-success">
            <i class="fa fa-check-circle"></i> Cập nhật trạng thái thành công!
            <?php if ($order['TRANG_THAI'] == 'da_huy'): ?> (Đã hoàn kho) <?php endif; ?>
        </div>
    <?php elseif ($_GET['msg'] == 'locked'): ?>
        <div class="alert-error">
            <i class="fa fa-lock"></i> <strong>Không thể cập nhật!</strong> Đơn hàng này đã hoàn tất hoặc đã hủy.
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <h4>Thông tin khách hàng</h4>
            <p><strong>Họ tên:</strong> <?= $order['TEN_NGUOI_NHAN'] ?></p>
            <p><strong>SĐT:</strong> <?= $order['SDT_NGUOI_NHAN'] ?></p>
            <p><strong>Địa chỉ:</strong> <?= $order['DIA_CHI_GIAO_HANG'] ?></p>
            <p><strong>Ngày đặt:</strong> <?= $order['NGAY_GIO'] ?></p>
        </div>
    </div>

    <div class="col-6">
        <div class="form-group" style="background: #e9ecef;">
            <h4>Cập nhật trạng thái</h4>

            <?php
            $isLocked = ($order['TRANG_THAI'] == 'da_huy' || $order['TRANG_THAI'] == 'da_giao');
            ?>

            <form action="index.php?module=Admin&controller=Order&action=update_status" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['MA_DON_HANG'] ?>">

                <select name="status" class="form-control" style="margin-bottom: 10px;" <?= $isLocked ? 'disabled' : '' ?>>
                    <option value="cho_xac_nhan" <?= $order['TRANG_THAI'] == 'cho_xac_nhan' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="dang_giao" <?= $order['TRANG_THAI'] == 'dang_giao' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="da_giao" <?= $order['TRANG_THAI'] == 'da_giao' ? 'selected' : '' ?>>Đã giao thành công</option>
                    <option value="da_huy" <?= $order['TRANG_THAI'] == 'da_huy' ? 'selected' : '' ?>>Hủy đơn hàng</option>
                </select>

                <?php if ($isLocked): ?>
                    <div style="color: #dc3545; font-weight: bold; margin-top: 10px;">
                        <i class="fa fa-ban"></i> Đơn hàng đã đóng, không thể chỉnh sửa.
                    </div>
                <?php else: ?>
                    <button type="submit" class="btn btn-success">Cập nhật ngay</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="form-group">
    <h4>Danh sách sản phẩm</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($details as $item): ?>
                <tr>
                    <td><?= $item['TEN_SAN_PHAM'] ?></td>
                    <td><img src="./public/uploads/<?= $item['HINH_ANH'] ?>" width="50"></td>
                    <td><?= $item['SO_LUONG'] ?></td>
                    <td><?= number_format($item['DON_GIA']) ?>đ</td>
                    <td><?= number_format($item['SO_LUONG'] * $item['DON_GIA']) ?>đ</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Tổng tiền thanh toán:</td>
                <td style="color: red; font-weight: bold; font-size: 18px;"><?= number_format($order['TONG_TIEN']) ?>đ</td>
            </tr>
        </tfoot>
    </table>
</div>