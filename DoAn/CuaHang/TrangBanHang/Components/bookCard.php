<?php
/**
 * bookCard.php
 * Chứa hàm hienThiTheSach() — render card hiển thị thông tin một cuốn sách.
 * Được include từ index.php; dùng chung scope $pdo, không require DB ở đây.
 */

/**
 * hienThiTheSach()
 *
 * @param array $sach      Dữ liệu một cuốn sách từ DB
 * @param array $nhanHieu  Danh sách nhãn [['class'=>'...','label'=>'...']]
 * @return string          HTML card sách
 */
function hienThiTheSach(array $sach, array $nhanHieu = []): string
{
    $anh        = !empty($sach['hinhAnh'])  ? htmlspecialchars($sach['hinhAnh'])
                                            : 'https://placehold.co/300x400/eff6ff/2563eb?text=📚';
    $ten        = htmlspecialchars($sach['tenSach']);
    $tacGia     = htmlspecialchars(!empty($sach['tacGia'])  ? $sach['tacGia']  : 'Đang cập nhật');
    $theLoai    = htmlspecialchars(!empty($sach['theLoai']) ? $sach['theLoai'] : '');
    $giaBan     = (float)($sach['giaBan']   ?? 0);
    $giaSau     = isset($sach['giaSau'])     ? (float)$sach['giaSau'] : null;
    $diem       = (float)($sach['diemTB']   ?? 0);
    $soLuotDG   = (int)($sach['soReview']   ?? 0);
    $maSach     = htmlspecialchars($sach['maSach']);
    $giaHienTai = ($giaSau !== null) ? $giaSau : $giaBan;

    /* Nhãn góc trái dọc */
    $nhanHtml = '';
    foreach ($nhanHieu as $nhan) {
        $nhanHtml .= "<span class=\"book-badge {$nhan['class']}\">{$nhan['label']}</span>\n";
    }

    /* Điểm đánh giá */
    if ($diem > 0) {
        $danhGiaHtml = "
            <div class=\"book-rating\">
                <i class=\"fas fa-star star-icon\"></i>
                <span class=\"rating-score\">{$diem}</span>
                <span class=\"rating-dot\"></span>
                <span class=\"rating-count\">({$soLuotDG})</span>
            </div>";
    } else {
        $danhGiaHtml = "
            <div class=\"book-rating\">
                <i class=\"far fa-star star-icon\"></i>
                <span class=\"rating-count\">Chưa có đánh giá</span>
            </div>";
    }

    /* Giá */
    $giaHienThi = number_format($giaHienTai, 0, ',', '.');
    $giaGocHtml = ($giaSau !== null)
        ? '<span class="original-price">' . number_format($giaBan, 0, ',', '.') . ' ₫</span>'
        : '';

    $danhMucHtml = $theLoai ? "<span class=\"book-category\">{$theLoai}</span>" : '';

    return "
    <div class=\"book-card\"
     data-id=\"{$maSach}\"
     data-name=\"{$ten}\"
     data-price=\"{$giaHienTai}\"
     data-image=\"{$anh}\">

    <div class=\"book-image\">
        " . ($nhanHtml ? "<div class=\"book-badges\">{$nhanHtml}</div>" : '') . "
        <img src=\"{$anh}\" alt=\"{$ten}\" loading=\"lazy\">

        <!-- Nút phải: tim + mắt — ẩn, slide-in khi hover -->
        <div class=\"book-actions-right\">
            <button class=\"btn-action-icon btn-wishlist\" title=\"Yêu thích\">
                <i class=\"far fa-heart\"></i>
            </button>
            <button class=\"btn-action-icon btn-quickview\" title=\"Xem nhanh\">
                <i class=\"fas fa-eye\"></i>
            </button>
        </div>

        <!-- Nút dưới: Thêm Nhanh — ẩn, slide-up khi hover -->
        <div class=\"book-add-quick\">
            <button class=\"btn-add-quick\" onclick=\"themVaoGioHang(this)\">
                <i class=\"fas fa-shopping-cart\"></i> Thêm Nhanh
            </button>
        </div>
    </div>

    <div class=\"book-info\">
        {$danhMucHtml}
        <h4 class=\"book-title\">{$ten}</h4>
        <p class=\"book-author\">{$tacGia}</p>
        {$danhGiaHtml}
        <div class=\"book-footer\">
            <div class=\"book-price-block\">
                <span class=\"current-price\">{$giaHienThi} ₫</span>
                {$giaGocHtml}
            </div>
            <button class=\"btn-add-to-cart\" onclick=\"themVaoGioHang(this)\" title=\"Thêm vào giỏ\">
                <i class=\"fas fa-shopping-cart\"></i>
            </button>
        </div>
    </div>
</div>";
}
