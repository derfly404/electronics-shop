<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Quên mật khẩu</h2>
        <p style="text-align:center; color:#666; margin-bottom:20px;">
            Nhập email để nhận link đặt lại mật khẩu.
        </p>

        <!-- HIỂN THỊ STATUS THÀNH CÔNG (MÀU XANH) -->
        <?php if (!empty($success)): ?>
            <div class="alert-success">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <!-- HIỂN THỊ STATUS LỖI (MÀU ĐỎ) -->
        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- FORM LUÔN HIỂN THỊ (DÙ CÓ STATUS HAY KHÔNG) -->
        <form action="index.php?controller=Auth&action=forgot_password" method="POST">
            <div class="form-group">
                <label>Email của bạn</label>
                <input type="email" name="email" required class="form-control"
                    placeholder="Nhập email đăng ký tài khoản..."
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                Gửi link đặt lại mật khẩu
            </button>
        </form>

        <div class="auth-footer" style="margin-top:20px; text-align:center;">
            <a href="index.php?controller=Auth&action=login">
                ← Quay lại đăng nhập
            </a>
        </div>
    </div>
</div>

<!-- CSS ĐƠN GIẢN -->
<style>
    .auth-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
    }

    .auth-box {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 5px;
        border: 1px solid #c3e6cb;
        margin-bottom: 20px;
        text-align: center;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 5px;
        border: 1px solid #f5c6cb;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        margin-top: 5px;
    }

    .btn-primary {
        background: #d0011b;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
    }

    .btn-primary:hover {
        background: #b30000;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: 600;
        color: #333;
    }

    .auth-footer a {
        color: #666;
        text-decoration: none;
    }

    .auth-footer a:hover {
        color: #d0011b;
        text-decoration: underline;
    }
</style>

<?php require_once './app/Views/Layouts/footer.php'; ?>