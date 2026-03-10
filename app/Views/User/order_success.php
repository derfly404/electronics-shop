<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container" style="text-align: center; padding: 50px 20px;">
    <i class="fa fa-check-circle" style="font-size: 80px; color: #28a745; margin-bottom: 20px;"></i>
    <h2 style="color: #28a745;">Đặt hàng thành công!</h2>
    <p>Cảm ơn bạn đã mua sắm tại Electronics Shop.</p>
    <p>Mã đơn hàng của bạn là: <strong>#<?= $_GET['id'] ?></strong></p>
    <p>Chúng tôi sẽ sớm liên hệ để xác nhận đơn hàng.</p>

    <div style="margin-top: 30px;">
        <a href="index.php" class="btn btn-secondary">Về trang chủ</a>
        <a href="index.php?controller=Order&action=history" class="btn btn-primary">Xem lịch sử đơn hàng</a>
    </div>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>