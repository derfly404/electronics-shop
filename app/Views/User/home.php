<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="banner-slider-container">
    <div class="slider-wrapper">
        <div class="slide">
            <img src="./public/images/banner1.png" alt="Banner 1">
        </div>
        <div class="slide">
            <img src="./public/images/banner2.png" alt="Banner 2">
        </div>
        <div class="slide">
            <img src="./public/images/banner3.png" alt="Banner 3">
        </div>
    </div>

    <button class="slider-btn prev-btn" onclick="moveSlide(-1)"><i class="fa fa-chevron-left"></i></button>
    <button class="slider-btn next-btn" onclick="moveSlide(1)"><i class="fa fa-chevron-right"></i></button>

    <div class="slider-dots">
        <span class="dot active" onclick="currentSlide(0)"></span>
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
    </div>
</div>

<section class="category-section">
    <div class="section-title">
        <h2>Danh mục nổi bật</h2>
    </div>

    <div class="category-grid">
        <?php if (!empty($categories) && count($categories) > 0): ?>
            <?php
            // YÊU CẦU: Chỉ lấy 6 danh mục đầu tiên
            $limitCategories = array_slice($categories, 0, 6);
            foreach ($limitCategories as $c):
            ?>
                <a href="index.php?controller=Product&category_id=<?= $c['MA_DANH_MUC'] ?>" class="cat-card">
                    <div class="cat-icon">
                        <?php if (!empty($c['HINH_ANH'])): ?>
                            <img src="./public/uploads/<?= $c['HINH_ANH'] ?>" alt="<?= $c['TEN_DANH_MUC'] ?>">
                        <?php else: ?>
                            <i class="fa fa-folder"></i>
                        <?php endif; ?>
                    </div>
                    <p><?= $c['TEN_DANH_MUC'] ?></p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-message" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                <i class="fa fa-folder-open" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Trống</h3>
                <p>Chưa có danh mục nào</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="product-section">
    <div class="section-title">
        <h2>Sản phẩm nổi bật <i class="fa fa-fire" style="color: #ffc107;"></i></h2>
    </div>

    <div class="product-grid-3-col" style="grid-template-columns: repeat(4, 1fr);">
        <?php if (!empty($featuredProducts) && count($featuredProducts) > 0): ?>
            <?php foreach ($featuredProducts as $p): ?>
                <div class="product-card">
                    <div class="product-img">
                        <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>">
                            <img src="./public/uploads/<?= $p['HINH_ANH'] ?>" alt="<?= $p['TEN_SAN_PHAM'] ?>">
                        </a>
                        <?php if ($p['SO_LUONG_TON'] <= 0): ?>
                            <span class="badge-out">HẾT HÀNG</span>
                        <?php elseif ($p['GIAM_GIA'] > 0): ?>
                            <span class="badge-sale">-<?= (float)$p['GIAM_GIA'] ?>%</span>
                        <?php endif; ?>

                        <div class="fire-badge" style="
                            position: absolute;
                            top: 10px;
                            left: 10px;
                            background: linear-gradient(135deg, #ff5722 0%, #ff9800 100%);
                            color: white;
                            padding: 6px 12px;
                            border-radius: 20px;
                            font-size: 12px;
                            font-weight: bold;
                            display: flex;
                            align-items: center;
                            gap: 5px;
                            box-shadow: 0 4px 8px rgba(255, 87, 34, 0.3);
                            z-index: 10;
                            animation: pulse-fire 2s infinite;
                        ">
                            <i class="fa fa-fire" style="color: #ffeb3b;"></i>
                            <span>Nổi bật</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>"><?= $p['TEN_SAN_PHAM'] ?></a></h3>
                        <div class="price">
                            <?php if ($p['GIAM_GIA'] > 0): ?>
                                <span class="curr-price"><?= number_format($p['GIA'] * (100 - $p['GIAM_GIA']) / 100) ?>đ</span>
                                <span class="old-price"><?= number_format($p['GIA']) ?>đ</span>
                            <?php else: ?>
                                <span class="curr-price"><?= number_format($p['GIA']) ?>đ</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>" class="btn-detail">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-message" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                <i class="fa fa-box-open" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Trống</h3>
                <p>Chưa có sản phẩm nổi bật nào</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- <div class="christmas-theme-2025-embed"
    data-page-type="homepage"
    data-show-effects="true">
</div> -->


<!-- ///////// -->
<!-- Xmas Countdown Card - đặt chỗ nào cũng được -->
<div class="xmas-countdown-card" id="xmas-countdown-card" data-target="2025-12-25T00:00:00">
    <div class="xcard-header">
        <div class="xcard-title">🎄 ĐẾM NGƯỢC GIÁNG SINH</div>
        <div class="xcard-sub">Sắp đến rồi — chuẩn bị quà nhé!</div>
    </div>

    <div class="xcard-body">
        <div class="xcard-countdown" aria-label="Countdown to Christmas">
            <div class="xcount-item">
                <div class="xcount-number" data-type="days">00</div>
                <div class="xcount-label">Ngày</div>
            </div>
            <div class="xcount-item">
                <div class="xcount-number" data-type="hours">00</div>
                <div class="xcount-label">Giờ</div>
            </div>
            <div class="xcount-item">
                <div class="xcount-number" data-type="minutes">00</div>
                <div class="xcount-label">Phút</div>
            </div>
            <div class="xcount-item">
                <div class="xcount-number" data-type="seconds">00</div>
                <div class="xcount-label">Giây</div>
            </div>
        </div>

        <!-- Cập nhật HTML cho phần hoạt ảnh -->
        <div class="xcard-sleigh-wrap" aria-hidden="true">
            <div class="xcard-sleigh" role="img" aria-label="Two Santas pulling a ribbon animation">
                <!-- ÔNG SAU -->
                <svg class="santa-icon back" viewBox="0 0 120 120">
                    <use href="#xsvg-santa"></use>
                </svg>

                <!-- DẢI RUY BĂNG -->
                <div class="ribbon">
                    <div class="ribbon-text">GIÁNG SINH ƯU ĐÃI GIẢM 2% CHO TẤT CẢ SẢN PHẨM</div>
                </div>

                <!-- ÔNG TRƯỚC -->
                <svg class="santa-icon front" viewBox="0 0 120 120">
                    <use href="#xsvg-santa"></use>
                </svg>
            </div>
        </div>
    </div>

    <div class="xcard-footer">
        <button class="xcard-toggle-anim" type="button" aria-pressed="true">OK!!!</button>
    </div>
</div>


<!-- 1 -->

<!-- Cập nhật phần SVG cho ông già Noel -->
<svg aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden" focusable="false">
    <symbol id="xsvg-santa" viewBox="0 0 120 120">
        <g>
            <!-- Thân -->
            <ellipse cx="60" cy="75" rx="25" ry="20" fill="#c62828" />

            <!-- Đầu -->
            <circle cx="60" cy="45" r="20" fill="#ffcfcc" />

            <!-- Râu -->
            <path d="M40 50 Q60 70 80 50" fill="#f5f5f5" />
            <path d="M45 55 Q60 65 75 55" fill="#f5f5f5" />

            <!-- Mũ -->
            <path d="M40 25 Q60 5 80 25 L80 35 L40 35 Z" fill="#c62828" />
            <circle cx="80" cy="30" r="8" fill="#fff" />

            <!-- Mắt -->
            <circle cx="55" cy="40" r="3" fill="#333" />
            <circle cx="65" cy="40" r="3" fill="#333" />

            <!-- Miệng -->
            <path d="M55 55 Q60 60 65 55" stroke="#333" stroke-width="2" fill="none" />

            <!-- Túi quà trên lưng -->
            <rect x="70" y="60" width="20" height="15" rx="3" fill="#4caf50" />
            <rect x="72" y="62" width="16" height="5" rx="2" fill="#81c784" />

            <!-- Tay cầm dải ruy băng -->
            <path d="M85 60 L95 50" stroke="#ffcc80" stroke-width="4" stroke-linecap="round" />
            <path d="M35 60 L25 50" stroke="#ffcc80" stroke-width="4" stroke-linecap="round" />
        </g>
    </symbol>
</svg>


<!-- ///////// -->

<section class="product-section">
    <div class="section-title">
        <h2>Sản phẩm mới về <span class="badge badge-success" style="font-size:12px; vertical-align: middle;">NEW</span></h2>
    </div>

    <div class="product-grid-3-col" style="grid-template-columns: repeat(4, 1fr);">
        <?php if (!empty($newProducts) && count($newProducts) > 0): ?>
            <?php foreach ($newProducts as $p): ?>
                <div class="product-card">
                    <div class="product-img">
                        <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>">
                            <img src="./public/uploads/<?= $p['HINH_ANH'] ?>" alt="<?= $p['TEN_SAN_PHAM'] ?>">
                        </a>
                        <?php if ($p['SO_LUONG_TON'] <= 0): ?>
                            <span class="badge-out">HẾT HÀNG</span>
                        <?php elseif ($p['GIAM_GIA'] > 0): ?>
                            <span class="badge-sale">-<?= (float)$p['GIAM_GIA'] ?>%</span>
                        <?php endif; ?>

                        <!-- Ruy băng NEW kiểu vắt chéo màu đỏ cho sản phẩm mới -->
                        <div class="new-ribbon">
                            <span>NEW</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <h3><a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>"><?= $p['TEN_SAN_PHAM'] ?></a></h3>
                        <div class="price">
                            <?php if ($p['GIAM_GIA'] > 0): ?>
                                <span class="curr-price"><?= number_format($p['GIA'] * (100 - $p['GIAM_GIA']) / 100) ?>đ</span>
                                <span class="old-price"><?= number_format($p['GIA']) ?>đ</span>
                            <?php else: ?>
                                <span class="curr-price"><?= number_format($p['GIA']) ?>đ</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>" class="btn-detail">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-message" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
                <i class="fa fa-box-open" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                <h3>Trống</h3>
                <p>Chưa có sản phẩm mới nào</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    let slideIndex = 0;
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");
    const wrapper = document.querySelector(".slider-wrapper");
    const totalSlides = slides.length;

    // Tự động chạy sau 3 giây
    let slideInterval = setInterval(() => {
        moveSlide(1)
    }, 4000);

    function moveSlide(n) {
        slideIndex += n;
        if (slideIndex >= totalSlides) {
            slideIndex = 0;
        }
        if (slideIndex < 0) {
            slideIndex = totalSlides - 1;
        }
        updateSlider();
        resetTimer();
    }

    function currentSlide(n) {
        slideIndex = n;
        updateSlider();
        resetTimer();
    }

    function updateSlider() {
        // Dịch chuyển wrapper
        wrapper.style.transform = `translateX(-${slideIndex * 100}%)`;

        // Cập nhật dots
        dots.forEach(dot => dot.classList.remove("active"));
        if (dots[slideIndex]) dots[slideIndex].classList.add("active");
    }

    function resetTimer() {
        clearInterval(slideInterval);
        slideInterval = setInterval(() => {
            moveSlide(1)
        }, 4000);
    }

    // end script
</script>

<?php require_once './app/Views/Layouts/footer.php'; ?>