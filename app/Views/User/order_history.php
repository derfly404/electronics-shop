<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container" style="padding: 30px 0; min-height: 500px;">
    <h2 class="page-title">Lịch sử đơn hàng của bạn</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-info" style="margin-bottom: 20px; color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 10px; border-radius: 4px;">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <p>Bạn chưa có đơn hàng nào. <a href="index.php">Mua sắm ngay</a></p>
    <?php else: ?>
        <table class="table" style="background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <thead>
                <tr>
                    <th>Mã đơn</th>
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
                        <td><?= date('d/m/Y H:i', strtotime($o['NGAY_GIO'])) ?></td>
                        <td style="color: #d0011b; font-weight: bold;"><?= number_format($o['TONG_TIEN']) ?>đ</td>
                        <td>
                            <?php
                            $status = $o['TRANG_THAI'];
                            $color = 'black';
                            $text = $status;
                            switch ($status) {
                                case 'cho_xac_nhan':
                                    $text = 'Chờ xác nhận';
                                    $color = '#ffc107';
                                    break; // Vàng
                                case 'dang_giao':
                                    $text = 'Đang giao hàng';
                                    $color = '#17a2b8';
                                    break; // Xanh dương
                                case 'da_giao':
                                    $text = 'Đã giao';
                                    $color = '#28a745';
                                    break; // Xanh lá
                                case 'da_huy':
                                    $text = 'Đã hủy';
                                    $color = '#dc3545';
                                    break; // Đỏ
                            }
                            echo "<span style='color: $color; font-weight: bold;'>$text</span>";
                            ?>
                        </td>
                        <td>
                            <a href="index.php?controller=Order&action=detail&id=<?= $o['MA_DON_HANG'] ?>"
                                class="btn btn-secondary btn-sm" title="Xem chi tiết">
                                <i class="fa fa-eye"></i>
                            </a>

                            <?php if ($o['TRANG_THAI'] == 'cho_xac_nhan'): ?>
                                <a href="index.php?controller=Order&action=cancel&id=<?= $o['MA_DON_HANG'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Hành động này không thể hoàn tác.')">
                                    Hủy đơn
                                </a>
                            <?php elseif ($o['TRANG_THAI'] == 'da_huy'): ?>
                                <span style="color: #999; font-size: 12px;">Đã hủy</span>
                            <?php elseif ($o['TRANG_THAI'] == 'da_giao'): ?>
                                <span style="color: #28a745; font-size: 12px; font-weight: bold; margin-left: 5px;">
                                    <i class="fa fa-check-circle"></i> Hoàn thành
                                </span>

                            <?php else: ?>
                                <span style="color: #17a2b8; font-size: 12px; margin-left: 5px;">
                                    <i class="fa fa-truck"></i> Đang giao
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>