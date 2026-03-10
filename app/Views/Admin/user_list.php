<div class="page-header">
    <h2>Quản lý người dùng</h2>
</div>

<div class="search-box-admin" style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <form action="index.php" method="GET" style="display: flex; gap: 10px;">
        <input type="hidden" name="module" value="Admin">
        <input type="hidden" name="controller" value="User">

        <input type="text" name="keyword" class="form-control"
            placeholder="Tìm theo tên hoặc email..."
            value="<?= isset($keyword) ? $keyword : '' ?>" style="flex: 1;">

        <button type="submit" class="btn btn-secondary"><i class="fa fa-search"></i> Tìm</button>

        <?php if (!empty($keyword)): ?>
            <a href="index.php?module=Admin&controller=User" class="btn btn-danger">X</a>
        <?php endif; ?>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th style="min-width: 150px;">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['MA_NGUOI_DUNG'] ?></td>
                <td><strong><?= $u['TEN'] ?></strong></td>
                <td><?= $u['EMAIL'] ?></td>

                <td>
                    <?php if ($u['VAI_TRO'] == 1): ?>
                        <span class="badge" style="background-color: #6610f2; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px;">Quản trị viên</span>
                    <?php else: ?>
                        <span class="badge" style="background-color: #6c757d; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px;">Khách hàng</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?= ($u['TRANG_THAI'] == 1) ? '<span style="color:green; font-weight:bold;">Hoạt động</span>' : '<span style="color:red; font-weight:bold;">Đã khóa</span>' ?>
                </td>

                <td>
                    <?php if ($u['MA_NGUOI_DUNG'] != $_SESSION['user']['id']): ?>

                        <?php if ($u['MA_NGUOI_DUNG'] == 1): ?>
                            <span style="color:#d0011b; font-weight:bold; font-size:11px;">
                                <i class="fa fa-shield-alt"></i> Super Admin
                            </span>
                        <?php else: ?>

                            <div style="display: flex; gap: 5px;">
                                <?php if ($u['TRANG_THAI'] == 1): ?>
                                    <a href="index.php?module=Admin&controller=User&action=toggle&id=<?= $u['MA_NGUOI_DUNG'] ?>&status=1"
                                        class="btn btn-danger btn-sm" onclick="return confirm('Bạn muốn khóa tài khoản này?')"><i class="fa fa-lock"></i></a>
                                <?php else: ?>
                                    <a href="index.php?module=Admin&controller=User&action=toggle&id=<?= $u['MA_NGUOI_DUNG'] ?>&status=0"
                                        class="btn btn-success btn-sm"><i class="fa fa-unlock"></i></a>
                                <?php endif; ?>

                                <?php if ($u['VAI_TRO'] == 0): ?>
                                    <a href="index.php?module=Admin&controller=User&action=change_role&id=<?= $u['MA_NGUOI_DUNG'] ?>&role=1"
                                        class="btn btn-primary btn-sm" onclick="return confirm('Cấp quyền Admin?')"><i class="fa fa-arrow-up"></i></a>
                                <?php else: ?>
                                    <a href="index.php?module=Admin&controller=User&action=change_role&id=<?= $u['MA_NGUOI_DUNG'] ?>&role=0"
                                        class="btn btn-secondary btn-sm" onclick="return confirm('Hủy quyền Admin?')"><i class="fa fa-arrow-down"></i></a>
                                <?php endif; ?>
                            </div>

                        <?php endif; ?> <?php else: ?>
                        <span style="color:#999; font-size:12px; font-style:italic;">(Tài khoản của bạn)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>