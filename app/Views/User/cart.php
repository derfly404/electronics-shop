<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="cart-page">
    <h2 class="page-title">Giỏ hàng của bạn</h2>

    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <p>Giỏ hàng đang trống!</p>
            <a href="index.php?controller=Product" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <form action="index.php?controller=Cart&action=update" method="POST" id="cart-form">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">
                            <input type="checkbox" id="select-all" onclick="toggleSelectAll()">
                        </th>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item):
                        $lineTotal = $item['display_price'] * $item['buy_qty'];
                    ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" class="cart-item-check"
                                    value="<?= $item['MA_SAN_PHAM'] ?>"
                                    data-price="<?= $lineTotal ?>"
                                    onclick="calculateTotal()">
                            </td>

                            <td class="product-col">
                                <img src="./public/uploads/<?= $item['HINH_ANH'] ?>" alt="" width="60">
                                <a href="index.php?controller=Product&action=detail&id=<?= $item['MA_SAN_PHAM'] ?>">
                                    <?= $item['TEN_SAN_PHAM'] ?>
                                </a>
                            </td>
                            <td><?= number_format($item['display_price']) ?>đ</td>
                            <td>
                                <input type="number" name="qty[<?= $item['MA_SAN_PHAM'] ?>]"
                                    value="<?= $item['buy_qty'] ?>" min="1" max="<?= $item['SO_LUONG_TON'] ?>"
                                    class="qty-input">
                            </td>
                            <td><?= number_format($lineTotal) ?>đ</td>
                            <td>
                                <a href="index.php?controller=Cart&action=delete&id=<?= $item['MA_SAN_PHAM'] ?>"
                                    class="btn-delete" onclick="return confirm('Xóa sản phẩm này?')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-actions">
                <a href="index.php" class="btn btn-secondary">Tiếp tục mua hàng</a>
                <button type="submit" class="btn btn-warning">Cập nhật số lượng</button>
            </div>
        </form>

        <div class="cart-summary sticky-bottom">
            <div class="summary-info">
                <span>Đã chọn: <b id="selected-count">0</b> sản phẩm</span>
                <h3>Tổng thanh toán: <span id="total-display">0đ</span></h3>
            </div>

            <button type="button" onclick="goToCheckout()" class="btn btn-checkout" id="btn-checkout">
                Mua hàng
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
    // Hàm chọn tất cả
    function toggleSelectAll() {
        var selectAll = document.getElementById('select-all');
        var checkboxes = document.getElementsByClassName('cart-item-check');

        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = selectAll.checked;
        }
        calculateTotal();
    }

    // Hàm tính tổng tiền dựa trên các ô đã tích
    function calculateTotal() {
        var checkboxes = document.getElementsByClassName('cart-item-check');
        var total = 0;
        var count = 0;
        var selectAll = document.getElementById('select-all');
        var allChecked = true;

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                // Lấy giá trị tiền từ thuộc tính data-price
                total += parseFloat(checkboxes[i].getAttribute('data-price'));
                count++;
            } else {
                allChecked = false;
            }
        }

        // Cập nhật trạng thái nút Select All
        if (checkboxes.length > 0) {
            selectAll.checked = allChecked;
        }

        // Hiển thị ra màn hình (Format tiền Việt)
        document.getElementById('selected-count').innerText = count;
        document.getElementById('total-display').innerText = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(total);
    }

    // Hàm chuyển sang trang Checkout với các ID đã chọn
    function goToCheckout() {
        var checkboxes = document.getElementsByClassName('cart-item-check');
        var selectedIds = [];

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                selectedIds.push(checkboxes[i].value);
            }
        }

        if (selectedIds.length === 0) {
            alert("Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!");
            return;
        }

        // Kiểm tra đăng nhập trước khi chuyển (Dùng logic PHP check session user)
        <?php if (!isset($_SESSION['user'])): ?>
            window.location.href = "index.php?controller=Auth&action=login";
        <?php else: ?>
            // Chuyển hướng kèm theo danh sách ID: controller=Order&action=checkout&items=1,2,5
            window.location.href = "index.php?controller=Order&action=checkout&items=" + selectedIds.join(',');
        <?php endif; ?>
    }
</script>

<?php require_once './app/Views/Layouts/footer.php'; ?>