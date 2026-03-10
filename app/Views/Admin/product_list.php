<div class="page-header">
    <h2>Danh sách sản phẩm</h2>
    <a href="index.php?module=Admin&controller=Product&action=create" class="btn btn-primary">Thêm mới</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php
    // Xác định kiểu thông báo (error, success, warning)
    $type = $_GET['type'] ?? 'success';

    // Class CSS tương ứng
    $alertClass = ($type == 'error') ? 'alert-error' : (($type == 'warning') ? 'alert-info' : 'alert-success');

    // Style riêng cho cảnh báo xóa (Màu vàng cam)
    $style = ($type == 'warning') ? 'background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba;' : '';
    ?>

    <div class="<?= $alertClass ?>" style="<?= $style ?>">
        <?= htmlspecialchars($_GET['msg']) ?>

        <?php if ($type == 'warning' && isset($_GET['error_id'])): ?>
            <div style="margin-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 10px;">
                <p style="font-size: 13px; margin-bottom: 10px;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <b>Lưu ý:</b> Hành động này sẽ xóa cả lịch sử đơn hàng và đánh giá liên quan đến sản phẩm này. Doanh thu tổng có thể bị thay đổi.
                </p>

                <a href="index.php?module=Admin&controller=Product&action=force_delete&id=<?= $_GET['error_id'] ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('XÁC NHẬN CUỐI CÙNG:\n\nBạn đang thực hiện xóa vĩnh viễn sản phẩm và toàn bộ dữ liệu lịch sử liên quan.\n\nHành động này KHÔNG THỂ HOÀN TÁC.\nBạn chắc chắn muốn tiếp tục?')">
                    <i class="fa fa-trash"></i> Đồng ý xóa tất cả dữ liệu
                </a>

                <a href="index.php?module=Admin&controller=Product" class="btn btn-secondary btn-sm" style="margin-left: 5px;">Hủy bỏ</a>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="search-box-admin" style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="module" value="Admin">
        <input type="hidden" name="controller" value="Product">

        <input type="text" name="keyword" class="form-control"
            placeholder="Tìm theo tên sản phẩm hoặc mã ID..."
            value="<?= isset($keyword) ? $keyword : '' ?>" style="flex: 1;">

        <button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm kiếm</button>

        <?php if (!empty($keyword)): ?>
            <a href="index.php?module=Admin&controller=Product" class="btn btn-danger" title="Xóa bộ lọc">X</a>
        <?php endif; ?>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th style="width: 25%;">Tên sản phẩm</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Tồn kho</th>
            <th>Trạng thái</th>
            <th style="min-width: 150px;">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td>#<?= $p['MA_SAN_PHAM'] ?></td>
                <td>
                    <?php if (!empty($p['HINH_ANH'])): ?>
                        <img src="./public/uploads/<?= $p['HINH_ANH'] ?>" width="50" height="50" style="border-radius:4px; border:1px solid #ddd; object-fit: cover;">
                    <?php else: ?>
                        <span style="font-size:12px; color:#999;">No Image</span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= $p['TEN_SAN_PHAM'] ?></strong>
                    <?php if ($p['NOI_BAT'] == 1): ?>
                        <span style="color: #ffc107; font-size: 12px; margin-left: 5px;" title="Sản phẩm nổi bật"><i class="fa fa-star"></i> Hot</span>
                    <?php endif; ?>
                </td>
                <td><?= $p['TEN_DANH_MUC'] ?? '<span style="color:#999;">Chưa phân loại</span>' ?></td>
                <td style="color: #d70018; font-weight: bold;"><?= number_format($p['GIA']) ?>đ</td>

                <td style="<?= $p['SO_LUONG_TON'] <= 5 ? 'color:red; font-weight:bold;' : '' ?>">
                    <?= $p['SO_LUONG_TON'] ?>
                </td>

                <td>
                    <?php if ($p['TRANG_THAI'] == 1): ?>
                        <span class="badge" style="background:#28a745; color: white;">Đang bán</span>
                    <?php else: ?>
                        <span class="badge" style="background:#6c757d; color: white;">Đã ẩn</span>
                    <?php endif; ?>
                </td>

                <td>
                    <div style="display: flex; gap: 5px;">
                        <a href="index.php?module=Admin&controller=Product&action=edit&id=<?= $p['MA_SAN_PHAM'] ?>"
                            class="btn btn-warning btn-sm" title="Chỉnh sửa">
                            <i class="fa fa-edit"></i>
                        </a>

                        <?php if ($p['TRANG_THAI'] == 1): ?>
                            <a href="index.php?module=Admin&controller=Product&action=toggle_status&id=<?= $p['MA_SAN_PHAM'] ?>&status=0"
                                class="btn btn-secondary btn-sm" title="Ngừng kinh doanh (Ẩn khỏi trang chủ)"
                                onclick="return confirm('Bạn muốn ẩn sản phẩm này? Khách hàng sẽ không nhìn thấy nữa.')">
                                <i class="fa fa-eye-slash"></i>
                            </a>
                        <?php else: ?>
                            <a href="index.php?module=Admin&controller=Product&action=toggle_status&id=<?= $p['MA_SAN_PHAM'] ?>&status=1"
                                class="btn btn-success btn-sm" title="Mở bán lại"
                                onclick="return confirm('Mở bán lại sản phẩm này?')">
                                <i class="fa fa-eye"></i>
                            </a>
                        <?php endif; ?>

                        <a href="index.php?module=Admin&controller=Product&action=delete&id=<?= $p['MA_SAN_PHAM'] ?>"
                            class="btn btn-danger btn-sm" title="Xóa vĩnh viễn"
                            onclick="return confirm('CẢNH BÁO: Bạn đang chọn xóa sản phẩm.\n\n- Sản phẩm ĐÃ BÁN có trong lịch sử đơn hàng: bạn có muốn xóa lịch sử đơn hàng của sản phẩm này không.\n\nTiếp tục?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>