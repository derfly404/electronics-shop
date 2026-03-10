<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="checkout-container">
    <h2 class="page-title">Thanh toán đơn hàng</h2>

    <form action="index.php?controller=Order&action=store" method="POST" class="row">

        <?php if (isset($isDirect) && $isDirect): ?>
            <input type="hidden" name="is_direct" value="1">
        <?php endif; ?>

        <?php if (isset($selectedItemsStr)): ?>
            <input type="hidden" name="selected_items" value="<?= $selectedItemsStr ?>">
        <?php endif; ?>

        <div class="col-6">
            <div class="checkout-box">
                <h3>Thông tin người nhận</h3>

                <div class="form-group">
                    <label>Họ và tên</label>
                    <input type="text" name="ten_nguoi_nhan" value="<?= $user['TEN'] ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="number" name="sdt_nguoi_nhan"
                        value="<?= isset($user['SDT']) ? $user['SDT'] : '' ?>"
                        required class="form-control" placeholder="Nhập số điện thoại">
                </div>

                <div class="form-group">
                    <label>Địa chỉ giao hàng</label>
                    <textarea name="dia_chi" rows="3" required class="form-control" placeholder="Số nhà, Tên đường, Phường/Xã..."><?= isset($user['DIA_CHI']) ? $user['DIA_CHI'] : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label>Phương thức thanh toán</label>
                    <select name="payment_method" class="form-control">
                        <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                        <option value="momo">Thanh toán Online qua MoMo</option>
                    </select>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="checkout-box summary-box">
                <h3>Đơn hàng của bạn (<?= count($cartItems) ?> sản phẩm)</h3>
                <table class="summary-table">
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td style="width: 60px;">
                                <img src="./public/uploads/<?= $item['HINH_ANH'] ?>" alt="" class="checkout-product-img">
                            </td>
                            <td>
                                <b><?= $item['TEN_SAN_PHAM'] ?></b> <br>
                                <small style="color: #666;">Số lượng: <?= $item['buy_qty'] ?></small>
                            </td>
                            <td class="text-right" style="vertical-align: middle; color: #d0011b; font-weight: bold;">
                                <?= number_format($item['display_price'] * $item['buy_qty']) ?>đ
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2"><strong>Tổng thanh toán</strong></td>
                        <td class="text-right" style="font-size: 20px;"><strong><?= number_format($totalMoney) ?>đ</strong></td>
                    </tr>
                </table>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px; font-size: 16px; text-transform: uppercase;">Xác nhận đặt hàng</button>
            </div>
        </div>
    </form>
</div>

<?php require_once './app/Views/Layouts/footer.php'; ?>