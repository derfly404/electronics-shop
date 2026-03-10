<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="container" style="margin-top: 40px; margin-bottom: 60px;">

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="page-title" style="margin: 0; border: none;">Chi tiết đơn hàng #<?= $order['MA_DON_HANG'] ?></h2>
        <a href="index.php?controller=Order&action=history" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Quay lại</a>
    </div>

    <div class="checkout-box" style="margin-bottom: 20px;">
        <h3>Trạng thái đơn hàng</h3>

        <div class="vertical-timeline">
            <?php
            $status = $order['TRANG_THAI'];
            // Xác định level hiện tại
            $currentStep = 0;
            if ($status == 'cho_xac_nhan') $currentStep = 1;
            if ($status == 'dang_giao') $currentStep = 2;
            if ($status == 'da_giao') $currentStep = 3;
            if ($status == 'da_huy') $currentStep = -1;
            ?>

            <?php if ($currentStep == -1): ?>
                <div class="timeline-item active cancel">
                    <div class="time-icon">
                        <div class="icon-circle"><i class="fa fa-times"></i></div>
                    </div>
                    <div class="time-content">
                        <h4>Đơn hàng đã bị hủy</h4>
                        <p class="time-sub">Vào lúc: <?= date('H:i d/m/Y', strtotime($order['NGAY_GIO'])) ?></p>
                    </div>
                </div>
            <?php else: ?>

                <div class="timeline-item <?= $currentStep >= 1 ? 'active' : '' ?>">
                    <div class="time-icon">
                        <div class="icon-circle"><i class="fa fa-file-alt"></i></div>
                        <div class="line"></div>
                    </div>
                    <div class="time-content">
                        <h4>Đặt hàng thành công đơn hàng của bạn đang được xử lý</h4>
                        <p class="time-sub">Thời gian: <?= date('H:i d/m/Y', strtotime($order['NGAY_GIO'])) ?></p>
                    </div>
                </div>

                <div class="timeline-item <?= $currentStep >= 2 ? 'active' : '' ?>">
                    <div class="time-icon">
                        <div class="icon-circle"><i class="fa fa-truck"></i></div>
                        <div class="line"></div>
                    </div>
                    <div class="time-content">
                        <h4>Đang vận chuyển</h4>
                        <p class="time-sub">Đơn hàng đang được giao bởi đơn vị vận chuyển</p>
                    </div>
                </div>

                <div class="timeline-item <?= $currentStep >= 3 ? 'active' : '' ?>">
                    <div class="time-icon">
                        <div class="icon-circle"><i class="fa fa-check"></i></div>
                        <div class="line"></div>
                    </div>
                    <div class="time-content">
                        <h4>Đã giao hàng thành công</h4>
                        <p class="time-sub">Đơn hàng đã được giao đến bạn</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="checkout-box" style="margin-top: 30px;">
        <h3>Thông tin nhận hàng</h3>
        <p><strong>Người nhận:</strong> <?= $order['TEN_NGUOI_NHAN'] ?></p>
        <p><strong>Số điện thoại:</strong> <?= $order['SDT_NGUOI_NHAN'] ?></p>
        <p><strong>Địa chỉ:</strong> <?= $order['DIA_CHI_GIAO_HANG'] ?></p>
        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['NGAY_GIO'])) ?></p>
    </div>

    <div class="checkout-box" style="margin-top: 20px;">
        <h3>Sản phẩm đã mua</h3>
        <table class="table" style="box-shadow: none;">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th class="text-right">Tạm tính</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $item): ?>
                    <tr>
                        <td class="product-col" style="border-bottom: 1px solid #eee;">
                            <img src="./public/uploads/<?= $item['HINH_ANH'] ?>" alt="" width="60">
                            <div>
                                <a href="index.php?controller=Product&action=detail&id=<?= $item['MA_SAN_PHAM'] ?>">
                                    <?= $item['TEN_SAN_PHAM'] ?>
                                </a>
                            </div>
                        </td>
                        <td><?= number_format($item['DON_GIA']) ?>đ</td>
                        <td>x<?= $item['SO_LUONG'] ?></td>
                        <td class="text-right" style="font-weight: bold;">
                            <?= number_format($item['DON_GIA'] * $item['SO_LUONG']) ?>đ
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr style="background: #f9f9f9;">
                    <td colspan="3" class="text-right" style="padding: 15px;"><strong>Tổng tiền thanh toán:</strong></td>
                    <td class="text-right" style="padding: 15px;">
                        <span style="color: var(--primary-red); font-size: 20px; font-weight: 900;">
                            <?= number_format($order['TONG_TIEN']) ?>đ
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if ($order['TRANG_THAI'] == 'cho_xac_nhan'): ?>
            <div style="margin-top: 20px; text-align: right;">
                <a href="index.php?controller=Order&action=cancel&id=<?= $order['MA_DON_HANG'] ?>"
                    class="btn btn-danger"
                    onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                    <i class="fa fa-times"></i> Hủy đơn hàng
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>