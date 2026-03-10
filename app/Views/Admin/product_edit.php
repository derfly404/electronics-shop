<h2>Cập nhật sản phẩm: <?= $product['TEN_SAN_PHAM'] ?></h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert-<?= (isset($_GET['type']) && $_GET['type'] == 'error') ? 'error' : 'success' ?>">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<form action="index.php?module=Admin&controller=Product&action=update&id=<?= $product['MA_SAN_PHAM'] ?>" method="POST" enctype="multipart/form-data" class="form-group">
    <div class="row">
        <div class="col-6">
            <label>Tên sản phẩm</label>
            <input type="text" name="ten_san_pham" value="<?= $product['TEN_SAN_PHAM'] ?>" required class="form-control">
        </div>
        <div class="col-6">
            <label>Danh mục</label>
            <select name="ma_danh_muc" class="form-control">
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['MA_DANH_MUC'] ?>" <?= ($c['MA_DANH_MUC'] == $product['MA_DANH_MUC']) ? 'selected' : '' ?>>
                        <?= $c['TEN_DANH_MUC'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label>Giá gốc</label>
            <input type="number" name="gia" value="<?= $product['GIA'] ?>" required class="form-control">
        </div>
        <div class="col-4">
            <label>Giảm giá (%)</label>
            <input type="number" name="giam_gia"
                value="<?= isset($product) ? $product['GIAM_GIA'] : 0 ?>"
                required
                class="form-control"
                min="0" max="100" step="1">
        </div>
        <div class="col-4">
            <label>Số lượng tồn</label>
            <input type="number" name="so_luong_ton" value="<?= $product['SO_LUONG_TON'] ?>" required class="form-control">
        </div>
    </div>

    <div>
        <label>Hình ảnh hiện tại</label> <br>
        <img src="./public/uploads/<?= $product['HINH_ANH'] ?>" width="100" style="margin-bottom: 10px;">
        <br>
        <label>Chọn hình mới (Nếu muốn thay đổi)</label>
        <input type="file" name="hinh_anh" class="form-control">
    </div>

    <div class="form-group mb-3" style="background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
        <label class="form-label" style="font-weight: bold; color: #856404;">Bộ sưu tập ảnh (Gallery)</label>

        <?php
        $gallery = !empty($product['GALLERY']) ? json_decode($product['GALLERY'], true) : [];
        ?>

        <?php if (!empty($gallery)): ?>
            <div style="margin-bottom: 15px; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px;">
                <p style="font-size: 13px; font-weight: bold; margin-bottom: 8px;">Ảnh hiện có (Tích vào ô muốn thay thế ảnh):</p>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px;">
                    <?php foreach ($gallery as $img): ?>
                        <div style="position: relative; border: 1px solid #ddd; background: #fff; border-radius: 3px;">
                            <img src="./public/uploads/<?= $img ?>" style="width: 100%; height: auto; object-fit: cover;">
                            <div style="position: absolute; top: 0; right: 0; background: rgba(255,255,255,0.8); padding: 2px;">
                                <input type="checkbox" name="delete_gallery[]" value="<?= $img ?>" title="Xóa ảnh này" style="cursor: pointer; width: 16px; height: 16px;">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <label class="form-label" style="font-size: 13px;">Thêm ảnh mới vào Gallery:</label>

        <input type="file" name="gallery[]" class="form-control" multiple accept="image/*">
    </div>

    <div>
        <label>Mô tả chi tiết</label>
        <textarea name="mo_ta" rows="5" class="form-control"><?= $product['MO_TA'] ?></textarea>
    </div>

    <div class="checkbox-group">
        <input type="checkbox" name="noi_bat" id="hot" <?= ($product['NOI_BAT'] == 1) ? 'checked' : '' ?>>
        <label for="hot">Sản phẩm nổi bật?</label>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="index.php?module=Admin&controller=Product" class="btn btn-secondary">Hủy</a>
    </div>
</form>