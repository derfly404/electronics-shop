<?php require_once './app/Views/Layouts/header.php'; ?>

<div class="product-detail-container">
    <div class="row detail-top">
        <div class="col-6 product-gallery">
            <div class="main-image">
                <img id="mainImg" src="./public/uploads/<?= $product['HINH_ANH'] ?>" alt="<?= $product['TEN_SAN_PHAM'] ?>">
            </div>

            <?php
            $gallery = !empty($product['GALLERY']) ? json_decode($product['GALLERY'], true) : [];
            // Thêm ảnh chính vào đầu danh sách gallery để khách có thể bấm quay lại
            array_unshift($gallery, $product['HINH_ANH']);

            // Loại bỏ trùng lặp nếu có
            $gallery = array_unique($gallery);
            ?>

            <?php if (count($gallery) > 1): ?>
                <div class="thumb-list">
                    <?php foreach ($gallery as $img): ?>
                        <div class="thumb-item" onclick="changeImage(this, './public/uploads/<?= $img ?>')">
                            <img src="./public/uploads/<?= $img ?>" alt="">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-6 product-summary">
            <h1 class="p-title"><?= $product['TEN_SAN_PHAM'] ?></h1>

            <div class="p-meta">
                <span>Mã SP: #<?= $product['MA_SAN_PHAM'] ?></span>
                <span class="divider">|</span>
                <span class="stock-status">
                    <?php if ($product['SO_LUONG_TON'] > 0): ?>
                        <i class="fa fa-check-circle" style="color: #28a745;"></i>
                        Còn lại: <strong><?= $product['SO_LUONG_TON'] ?></strong> sản phẩm
                    <?php else: ?>
                        <i class="fa fa-times-circle" style="color: #dc3545;"></i>
                        <span style="color: #dc3545; font-weight: bold;">Hết hàng</span>
                    <?php endif; ?>
                </span>
            </div>

            <div class="p-price-box">
                <?php if ($product['GIAM_GIA'] > 0): ?>
                    <span class="p-old-price"><?= number_format($product['GIA']) ?>đ</span>
                    <span class="p-current-price"><?= number_format($product['GIA'] * (100 - $product['GIAM_GIA']) / 100) ?>đ</span>
                    <span class="p-discount">-<?= (float)$product['GIAM_GIA'] ?>%</span>
                <?php else: ?>
                    <span class="p-current-price"><?= number_format($product['GIA']) ?>đ</span>
                <?php endif; ?>
            </div>

            <div class="p-short-desc">
                <ul style="list-style: circle; padding-left: 20px; color: #555;">
                    <li>Cam kết chính hãng 100%</li>
                    <li>Bảo hành 12 tháng tại trung tâm ủy quyền</li>
                    <li>Đổi trả trong 30 ngày đầu nếu lỗi</li>
                </ul>
            </div>

            <?php if ($product['SO_LUONG_TON'] > 0): ?>
                <div class="purchase-actions">
                    <form id="add-to-cart-form" method="POST" action="index.php?controller=Cart&action=add">
                        <input type="hidden" name="id" value="<?= $product['MA_SAN_PHAM'] ?>">
                        <div class="qty-control">
                            <label>Số lượng:</label>
                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['SO_LUONG_TON'] ?>" class="form-control" style="width: 70px; display:inline-block;">
                        </div>

                        <div class="btn-group-action">
                            <button type="submit" class="btn btn-cart-outline">
                                <i class="fa fa-cart-plus"></i> Thêm vào giỏ
                            </button>

                            <button type="button" onclick="buyNow()" class="btn btn-buy-now">
                                Mua ngay
                            </button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <button type="button" class="btn btn-disabled" disabled>TẠM HẾT HÀNG</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="product-tabs-wrapper">
        <div class="tab-header">
            <button class="tab-link active" onclick="openTab(event, 'desc')">Mô tả sản phẩm</button>
            <button class="tab-link" onclick="openTab(event, 'review')">Đánh giá (<?= count($reviews) ?>)</button>
        </div>

        <div id="desc" class="tab-content" style="display: block;">
            <div class="description-content collapsed" id="desc-box">
                <?= $product['MO_TA'] ? nl2br($product['MO_TA']) : '<p>Đang cập nhật...</p>' ?>
            </div>
            <button id="btn-toggle-desc" onclick="toggleDescription()" class="btn-show-more">
                Xem thêm nội dung <i class="fa fa-caret-down"></i>
            </button>
        </div>

        <div id="review" class="tab-content" style="display: none;">
            <?php
            // 1. Tính toán số liệu
            $totalReviews = count($reviews);
            $sumRating = 0;
            $starCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

            foreach ($reviews as $r) {
                $star = (int)$r['SO_SAO'];
                if (isset($starCounts[$star])) {
                    $starCounts[$star]++;
                }
                $sumRating += $star;
            }

            $avgRating = $totalReviews > 0 ? round($sumRating / $totalReviews, 1) : 0;
            ?>

            <?php if ($totalReviews > 0): ?>
                <div class="rating-overview">
                    <div class="rating-left">
                        <div class="big-score"><?= $avgRating ?></div>
                        <div class="stars" style="color: #ffc107; margin: 5px 0;">
                            <?php
                            // Logic hiển thị sao lẻ (ví dụ 4.5)
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avgRating) {
                                    echo '<i class="fa fa-star"></i>';
                                } elseif ($i - 0.5 <= $avgRating) {
                                    echo '<i class="fa fa-star-half-o"></i>';
                                } else {
                                    echo '<i class="fa fa-star-o" style="color:#ccc;"></i>';
                                }
                            }
                            ?>
                        </div>
                        <div style="font-size: 13px; color: #666;"><?= $totalReviews ?> đánh giá</div>
                    </div>

                    <div class="rating-right">
                        <?php foreach ([5, 4, 3, 2, 1] as $star): ?>
                            <?php
                            $count = $starCounts[$star];
                            $percent = ($totalReviews > 0) ? ($count / $totalReviews) * 100 : 0;
                            ?>
                            <div class="star-bar">
                                <span class="star-label"><?= $star ?> <i class="fa fa-star" style="font-size: 10px; color:#999;"></i></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= $percent ?>%;"></div>
                                </div>
                                <span class="count-label"><?= $count ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="review-list">
                <?php if (empty($reviews)): ?>
                    <p style="color:#777;">Chưa có đánh giá nào cho sản phẩm này.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                        <div class="review-item">
                            <div class="review-avatar">
                                <img src="https://ui-avatars.com/api/?name=<?= $r['TEN'] ?>&background=random&color=fff" alt="">
                            </div>
                            <div class="review-body">
                                <strong><?= $r['TEN'] ?></strong>
                                <div id="review-display-<?= $r['MA_DANH_GIA'] ?>">
                                    <span class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++) echo ($i <= $r['SO_SAO']) ? '<i class="fa fa-star text-warning"></i>' : '<i class="fa fa-star-o text-muted"></i>'; ?>
                                    </span>
                                    <span class="review-date"><?= date('d/m/Y', strtotime($r['NGAY_GIO'])) ?></span>
                                    <p style="margin-top: 8px;"><?= nl2br(htmlspecialchars($r['NOI_DUNG'])) ?></p>

                                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $r['MA_NGUOI_DUNG']): ?>
                                        <div class="cmt-actions" style="margin-top: 10px; font-size: 12px;">
                                            <a href="javascript:void(0)" onclick="showReviewEdit(<?= $r['MA_DANH_GIA'] ?>)" style="color:#666; font-weight:600;">Chỉnh sửa</a>
                                            <span style="margin: 0 5px; color:#ccc;">•</span>
                                            <a href="index.php?controller=Product&action=delete_review&id=<?= $r['MA_DANH_GIA'] ?>&product_id=<?= $product['MA_SAN_PHAM'] ?>"
                                                onclick="return confirm('Bạn chắc chắn muốn xóa đánh giá này?')"
                                                style="color: #dc3545; font-weight:600;">Xóa</a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $r['MA_NGUOI_DUNG']): ?>
                                    <form action="index.php?controller=Product&action=update_review" method="POST"
                                        id="review-edit-<?= $r['MA_DANH_GIA'] ?>" style="display: none; margin-top: 10px;">

                                        <input type="hidden" name="review_id" value="<?= $r['MA_DANH_GIA'] ?>">
                                        <input type="hidden" name="product_id" value="<?= $product['MA_SAN_PHAM'] ?>">

                                        <div class="rating-css" style="font-size: 10px; margin-bottom: 5px;">
                                            <?php for ($s = 5; $s >= 1; $s--): ?>
                                                <input type="radio" value="<?= $s ?>" id="rating-<?= $s ?>-<?= $r['MA_DANH_GIA'] ?>"
                                                    name="so_sao" <?= ($r['SO_SAO'] == $s) ? 'checked' : '' ?>>
                                                <label for="rating-<?= $s ?>-<?= $r['MA_DANH_GIA'] ?>" style="font-size: 20px;"></label>
                                            <?php endfor; ?>
                                        </div>

                                        <textarea name="noi_dung" class="form-control" rows="2"><?= $r['NOI_DUNG'] ?></textarea>

                                        <div style="margin-top: 10px; text-align: right;">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="cancelReviewEdit(<?= $r['MA_DANH_GIA'] ?>)">Hủy</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="review-form-box">
                <?php if (isset($_SESSION['user'])): ?>
                    <form action="index.php?controller=Product&action=submit_review" method="POST">
                        <input type="hidden" name="ma_san_pham" value="<?= $product['MA_SAN_PHAM'] ?>">
                        <div class="form-group">
                            <div class="form-group">
                                <label>Đánh giá của bạn:</label>
                                <div class="rating-css">
                                    <input type="radio" value="5" id="rating5" name="so_sao">
                                    <label for="rating5" title="Tuyệt vời"></label>

                                    <input type="radio" value="4" id="rating4" name="so_sao">
                                    <label for="rating4" title="Tốt"></label>

                                    <input type="radio" value="3" id="rating3" name="so_sao">
                                    <label for="rating3" title="Bình thường"></label>

                                    <input type="radio" value="2" id="rating2" name="so_sao">
                                    <label for="rating2" title="Tệ"></label>

                                    <input type="radio" value="1" id="rating1" name="so_sao">
                                    <label for="rating1" title="Rất tệ"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea name="noi_dung" class="form-control" rows="3" placeholder="Sản phẩm dùng thế nào?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </form>
                <?php else: ?>
                    <p class="alert-info">Vui lòng <a href="index.php?controller=Auth&action=login">đăng nhập</a> để đánh giá.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="related-products">
        <h3>Sản phẩm liên quan</h3>
        <div class="product-grid-3-col"> <?php foreach ($relatedProducts as $p): ?>
                <div class="product-card">
                    <div class="product-img">
                        <a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>">
                            <img src="./public/uploads/<?= $p['HINH_ANH'] ?>" alt="<?= $p['TEN_SAN_PHAM'] ?>">
                        </a>
                    </div>
                    <div class="product-info">
                        <h3><a href="index.php?controller=Product&action=detail&id=<?= $p['MA_SAN_PHAM'] ?>"><?= $p['TEN_SAN_PHAM'] ?></a></h3>
                        <div class="price">
                            <span class="curr-price"><?= number_format($p['GIA']) ?>đ</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    // Script chuyển Tab
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        // Ẩn tất cả nội dung
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        // Bỏ active các nút
        tablinks = document.getElementsByClassName("tab-link");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        // Hiện tab được chọn
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Script Thu gọn / Xem thêm mô tả
    function toggleDescription() {
        var descBox = document.getElementById("desc-box");
        var btn = document.getElementById("btn-toggle-desc");

        if (descBox.classList.contains("collapsed")) {
            descBox.classList.remove("collapsed");
            descBox.classList.add("expanded");
            btn.innerHTML = 'Thu gọn <i class="fa fa-caret-up"></i>';
        } else {
            descBox.classList.remove("expanded");
            descBox.classList.add("collapsed");
            btn.innerHTML = 'Xem thêm nội dung <i class="fa fa-caret-down"></i>';
            // Cuộn lại lên đầu phần mô tả
            document.querySelector('.tab-header').scrollIntoView({
                behavior: 'smooth'
            });
        }
    }

    // Script xử lý MUA NGAY (Không thêm vào giỏ hàng chính)
    function buyNow() {

        var quantityInput = document.querySelector('input[name="quantity"]');
        var maxStock = <?= $product['SO_LUONG_TON'] ?>;
        var quantity = parseInt(quantityInput.value);

        // Kiểm tra tồn kho trước khi mua ngay
        if (quantity > maxStock) {
            // Hiển thị thông báo lỗi
            alert('Số lượng vượt quá tồn kho! Tồn kho hiện có: ' + maxStock);
            quantityInput.value = maxStock;
            quantityInput.focus();
            return false;
        }

        if (quantity < 1) {
            alert('Số lượng phải lớn hơn 0!');
            quantityInput.value = 1;
            quantityInput.focus();
            return false;
        }


        if (confirm('Bạn có muốn mua ngay sản phẩm này? (Chuyển đến thanh toán ngay)')) {
            var form = document.getElementById('add-to-cart-form');
            // Đổi action sang hàm xử lý mua ngay
            form.action = "index.php?controller=Cart&action=buyNow";
            form.submit();
        }
    }

    // Hàm đổi ảnh khi click thumbnail
    function changeImage(element, src) {
        // Đổi ảnh chính
        document.getElementById('mainImg').src = src;

        // Xử lý active class
        var thumbs = document.querySelectorAll('.thumb-item');
        thumbs.forEach(t => t.classList.remove('active'));
        element.classList.add('active');
    }

    // Script Sửa Đánh Giá
    function showReviewEdit(id) {
        document.getElementById('review-display-' + id).style.display = 'none';
        document.getElementById('review-edit-' + id).style.display = 'block';
    }

    function cancelReviewEdit(id) {
        document.getElementById('review-display-' + id).style.display = 'block';
        document.getElementById('review-edit-' + id).style.display = 'none';
    }
</script>

<?php require_once './app/Views/Layouts/footer.php'; ?>