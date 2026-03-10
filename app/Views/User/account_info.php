<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container" style="padding: 30px 0;">
    <div class="row">
        <div class="col-4">
            <div class="checkout-box">
                <h3>Tài khoản</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <a href="index.php?controller=Account" style="color: #d0011b; font-weight: bold;">Thông tin cá nhân</a>
                    </li>
                    <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                        <a href="index.php?controller=Order&action=history" style="color: #333; text-decoration: none;">Lịch sử đơn hàng</a>
                    </li>
                    <li style="padding: 10px 0;">
                        <a href="index.php?controller=Auth&action=logout" style="color: #333; text-decoration: none;">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-6" style="flex: 0 0 65%;">
            <div class="checkout-box">
                <h3>Cập nhật thông tin</h3>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                    <div class="alert-success">Cập nhật thông tin thành công!</div>
                <?php endif; ?>

                <form action="index.php?controller=Account&action=update" method="POST">

                    <div class="form-group">
                        <label>Email <small style="color:red;">(Không thể thay đổi)</small></label>
                        <input type="text" value="<?= $user['EMAIL'] ?>" class="form-control" disabled style="background: #e9ecef; cursor: not-allowed;">
                    </div>

                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="ten" value="<?= $user['TEN'] ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="number" name="sdt" value="<?= isset($user['SDT']) ? $user['SDT'] : '' ?>"
                            class="form-control" placeholder="Nhập số điện thoại">
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ</label>
                        <textarea name="dia_chi" rows="3" class="form-control" placeholder="Nhập địa chỉ của bạn"><?= isset($user['DIA_CHI']) ? $user['DIA_CHI'] : '' ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu mới (Để trống nếu không đổi)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="******">
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>