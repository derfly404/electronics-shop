<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Đăng ký tài khoản</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form action="index.php?controller=Auth&action=register" method="POST">
            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="ten" required class="form-control" placeholder="Nhập họ tên của bạn">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required class="form-control" placeholder="Nhập địa chỉ email">
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required class="form-control" placeholder="Tạo mật khẩu">
            </div>

            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required class="form-control" placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Đăng ký ngay</button>
        </form>

        <div class="auth-footer">
            <p>Bạn đã có tài khoản? <a href="index.php?controller=Auth&action=login">Đăng nhập tại đây</a></p>
        </div>
    </div>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>