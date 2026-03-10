<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Đăng nhập</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert-success"><?= $msg ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form action="index.php?controller=Auth&action=login" method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required class="form-control" placeholder="Nhập email của bạn">
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required class="form-control" placeholder="Nhập mật khẩu">
                <div style="text-align: right; margin-top: 8px;">
                    <a href="index.php?controller=Auth&action=forgot_password"
                        style="font-size: 14px; color: #d0011b; text-decoration: none;">
                        Quên mật khẩu?
                    </a>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
        </form>

        <div class="auth-footer">
            <p>Chưa có tài khoản? <a href="index.php?controller=Auth&action=register">Đăng ký miễn phí</a></p>
        </div>
    </div>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>