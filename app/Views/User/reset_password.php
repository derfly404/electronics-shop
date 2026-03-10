<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Reset Password</h2>

        <?php if (isset($reset_email)): ?>
            <div class="alert alert-info">
                Email reset: <strong><?= htmlspecialchars($reset_email) ?></strong>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert alert-success">
                ✅ Đặt lại mật khẩu thành công! Vui lòng đăng nhập.
            </div>
        <?php endif; ?>

        <form action="index.php?controller=Auth&action=reset_password" method="POST">
            <div class="form-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="password" required class="form-control"
                    placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" minlength="6">
            </div>

            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required class="form-control"
                    placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Đặt lại mật khẩu</button>
        </form>

        <div style="text-align:center; margin-top:15px;">
            <a href="index.php?controller=Auth&action=login">Quay lại đăng nhập</a>
        </div>
    </div>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>