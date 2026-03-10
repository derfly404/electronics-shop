<h2>Thêm sản phẩm mới</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert-<?= (isset($_GET['type']) && $_GET['type'] == 'error') ? 'error' : 'success' ?>">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>

<form action="index.php?module=Admin&controller=Product&action=store" method="POST" enctype="multipart/form-data" class="form-group">
    <div class="row">
        <div class="col-6">
            <label>Tên sản phẩm</label>
            <input type="text" name="ten_san_pham" required class="form-control">
        </div>
        <div class="col-6">
            <label>Danh mục</label>
            <select name="ma_danh_muc" class="form-control">
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['MA_DANH_MUC'] ?>"><?= $c['TEN_DANH_MUC'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <label>Giá gốc</label>
            <input type="number" name="gia" required class="form-control">
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
            <input type="number" name="so_luong_ton" required class="form-control">
        </div>
    </div>

    <div>
        <label>Hình ảnh</label>
        <input type="file" name="hinh_anh" required class="form-control">
    </div>

    <div class="form-group" style="margin-top: 15px;">
        <label>Chọn nhiều hình ảnh</label>
        <input type="file" name="gallery[]" class="form-control" multiple>

        <?php if (isset($product) && !empty($product['GALLERY'])): ?>
            <div class="gallery-preview" style="margin-top:10px; display:flex; gap:5px; flex-wrap:wrap;">
                <?php
                $gallery = json_decode($product['GALLERY'], true);
                if (is_array($gallery)):
                    foreach ($gallery as $img):
                ?>
                        <img src="./public/uploads/<?= $img ?>" width="50" height="50" style="object-fit:cover; border:1px solid #ddd;">
                <?php endforeach;
                endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <label>Mô tả chi tiết</label>
        <textarea name="mo_ta" rows="5" class="form-control"></textarea>
    </div>

    <div class="checkbox-group">
        <input type="checkbox" name="noi_bat" id="hot">
        <label for="hot">Sản phẩm nổi bật?</label>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
        <a href="index.php?module=Admin&controller=Product" class="btn btn-secondary">Hủy</a>
    </div>
</form>