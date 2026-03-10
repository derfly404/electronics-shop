<div class="row">
    <div class="col-4">
        <div class="form-group">
            <h3><?= isset($editCategory) ? 'Sửa danh mục' : 'Thêm danh mục' ?></h3>

            <form action="index.php?module=Admin&controller=Category&action=<?= isset($editCategory) ? 'update' : 'store' ?>" method="POST" enctype="multipart/form-data">

                <?php if (isset($editCategory)): ?>
                    <input type="hidden" name="id" value="<?= $editCategory['MA_DANH_MUC'] ?>">
                <?php endif; ?>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Tên danh mục</label>
                    <input type="text" name="ten_danh_muc" class="form-control" required
                        value="<?= isset($editCategory) ? $editCategory['TEN_DANH_MUC'] : '' ?>">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Hình ảnh</label>
                    <input type="file" name="hinh_anh" class="form-control">
                    <?php if (isset($editCategory) && !empty($editCategory['HINH_ANH'])): ?>
                        <div style="margin-top: 10px;">
                            <img src="./public/uploads/<?= $editCategory['HINH_ANH'] ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <?= isset($editCategory) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>

                <?php if (isset($editCategory)): ?>
                    <a href="index.php?module=Admin&controller=Category" class="btn btn-secondary btn-block" style="text-align:center; margin-top:5px;">Hủy</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-6" style="flex: 0 0 64%;">
        <div class="form-group">
            <h3>Danh sách danh mục</h3>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert-<?= (isset($_GET['type']) && $_GET['type'] == 'error') ? 'error' : 'success' ?>" style="margin-bottom: 15px; font-size: 13px;">
                    <?= htmlspecialchars($_GET['msg']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="GET" style="display: flex; gap: 5px; margin-bottom: 15px;">
                <input type="hidden" name="module" value="Admin">
                <input type="hidden" name="controller" value="Category">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm tên danh mục..." value="<?= isset($keyword) ? $keyword : '' ?>">
                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-search"></i></button>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình</th>
                        <th>Tên danh mục</th>
                        <th>Số SP</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                        <tr>
                            <td><?= $c['MA_DANH_MUC'] ?></td>
                            <td>
                                <?php if (!empty($c['HINH_ANH'])): ?>
                                    <img src="./public/uploads/<?= $c['HINH_ANH'] ?>" width="40" height="40" style="border-radius: 4px; object-fit:contain;">
                                <?php else: ?>
                                    <i class="fa fa-image" style="color:#ccc;"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= $c['TEN_DANH_MUC'] ?></td>

                            <td style="font-weight: bold; color: <?= $c['SO_LUONG_SP'] > 0 ? '#d70018' : '#ccc' ?>">
                                <?= $c['SO_LUONG_SP'] ?>
                            </td>

                            <td>
                                <a href="index.php?module=Admin&controller=Category&action=edit&id=<?= $c['MA_DANH_MUC'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>

                                <?php if ($c['SO_LUONG_SP'] > 0): ?>
                                    <span class="btn btn-secondary btn-sm" title="Không thể xóa vì có <?= $c['SO_LUONG_SP'] ?> sản phẩm" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa fa-trash"></i>
                                    </span>
                                <?php else: ?>
                                    <a href="index.php?module=Admin&controller=Category&action=delete&id=<?= $c['MA_DANH_MUC'] ?>"
                                        class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>