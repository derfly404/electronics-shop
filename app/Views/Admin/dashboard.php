<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-header">
    <h2>Dashboard - Thống kê kinh doanh</h2>
</div>

<div class="row" style="margin-bottom: 20px;">
    <div class="col-12">
        <div class="checkout-box" style="padding: 15px; display: flex; align-items: center; gap: 15px; background: #fff; border-radius: 8px;">
            <strong style="white-space: nowrap;"><i class="fa fa-filter"></i> Lọc thời gian:</strong>

            <form action="" method="GET" style="display: flex; gap: 10px; width: 100%; align-items: center;">
                <input type="hidden" name="module" value="Admin">
                <input type="hidden" name="controller" value="Dashboard">
                <input type="hidden" name="action" value="index">

                <input type="date" name="from_date" class="form-control" style="width: auto;"
                    value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">

                <span>đến</span>

                <input type="date" name="to_date" class="form-control" style="width: auto;"
                    value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">

                <button type="submit" class="btn btn-primary btn-sm">Xem dữ liệu</button>

                <?php if (isset($_GET['from_date'])): ?>
                    <a href="index.php?module=Admin&controller=Dashboard" class="btn btn-secondary btn-sm">Mặc định (30 ngày)</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-content">
            <h3>Doanh thu</h3>
            <p><?= number_format($revenue) ?>đ</p>
            <small style="font-size: 12px; opacity: 0.8;">
                <?php
                if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
                    echo date('d/m', strtotime($_GET['from_date'])) . ' - ' . date('d/m', strtotime($_GET['to_date']));
                } else {
                    echo "30 ngày gần nhất";
                }
                ?>
            </small>
        </div>
        <div class="stat-icon"><i class="fa fa-money-bill-wave"></i></div>
    </div>
    <div class="stat-card orange">
        <div class="stat-content">
            <h3>Đơn hàng</h3>
            <p><?= $totalOrders ?></p>
        </div>
        <div class="stat-icon"><i class="fa fa-shopping-bag"></i></div>
    </div>
    <div class="stat-card green">
        <div class="stat-content">
            <h3>Sản phẩm</h3>
            <p><?= $totalProducts ?></p>
        </div>
        <div class="stat-icon"><i class="fa fa-box-open"></i></div>
    </div>
    <div class="stat-card red">
        <div class="stat-content">
            <h3>Khách hàng</h3>
            <p><?= $totalUsers ?></p>
        </div>
        <div class="stat-icon"><i class="fa fa-users"></i></div>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <div class="col-12" style="width: 100%;">
        <div class="checkout-box">
            <h3>
                <i class="fa fa-chart-line"></i>
                Biểu đồ doanh thu
                <small style="font-size: 14px; color: #666; font-weight: normal;">
                    (<?php echo (!empty($_GET['from_date'])) ? "Lọc theo ngày chọn" : "30 ngày gần nhất"; ?>)
                </small>
            </h3>
            <canvas id="revenueChart" style="width: 100%; height: 300px;"></canvas>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 20px; display: flex; gap: 20px;">

    <div class="col-6" style="flex: 1;">
        <div class="checkout-box">
            <h3><i class="fa fa-trophy" style="color: gold;"></i> Top Sản Phẩm Bán Chạy</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đã bán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $p): ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 10px; border:none;">
                                <img src="./public/uploads/<?= $p['HINH_ANH'] ?>" width="40" style="border-radius:4px;">
                                <span><?= $p['TEN_SAN_PHAM'] ?></span>
                            </td>
                            <td><b><?= $p['total_sold'] ?></b></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-6" style="flex: 1;">
        <div class="checkout-box">
            <h3><i class="fa fa-crown" style="color: orange;"></i> Khách Hàng VIP</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Chi tiêu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topCustomers as $c): ?>
                        <tr>
                            <td>
                                <b><?= $c['TEN'] ?></b><br>
                                <small><?= $c['EMAIL'] ?></small>
                            </td>
                            <td style="color: #d0011b; font-weight: bold;"><?= number_format($c['total_spent']) ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dashboard-section" style="margin-top: 30px;">
    <div class="section-header">
        <h3>Đơn hàng mới nhất</h3>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Người nhận</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $o): ?>
                <tr>
                    <td>#<?= $o['MA_DON_HANG'] ?></td>
                    <td><?= $o['TEN_NGUOI_NHAN'] ?></td>
                    <td><?= number_format($o['TONG_TIEN']) ?>đ</td>
                    <td><span class="badge badge-<?= $o['TRANG_THAI'] ?>"><?= $o['TRANG_THAI'] ?></span></td>
                    <td><a href="index.php?module=Admin&controller=Order&action=detail&id=<?= $o['MA_DON_HANG'] ?>" class="btn btn-sm btn-secondary">Xem</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // 1. Chuẩn bị dữ liệu từ PHP sang JS
    const rawData = <?= json_encode($chartData) ?>;

    // Tạo mảng Labels (Ngày) và Data (Doanh thu)
    const labels = rawData.map(item => {
        // Format ngày từ YYYY-MM-DD sang DD/MM
        const d = new Date(item.date);
        return d.getDate() + '/' + (d.getMonth() + 1);
    });

    const dataValues = rawData.map(item => item.total);

    // 2. Vẽ biểu đồ
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: dataValues,
                backgroundColor: 'rgba(208, 1, 27, 0.2)',
                borderColor: 'rgba(208, 1, 27, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('vi-VN') + 'đ';
                        }
                    }
                }
            }
        }
    });
</script>