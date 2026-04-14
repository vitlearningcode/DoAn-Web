<?php
/**
 * kiemTraGioHang.php — Kiểm tra giỏ hàng hợp lệ + lấy giá từ DB + tính tổng tiền
 *
 * Output: $gioHang (array với giaBan đã được xác thực từ DB), $tongTien (int)
 * Nếu giỏ trống → redirect và thoát.
 *
 * BẢO MẬT: giaBan KHÔNG bao giờ được lấy từ client (session['cart'] hay POST).
 * Giá luôn được truy vấn trực tiếp từ bảng Sach (có áp dụng khuyến mãi nếu có).
 */

// ── 1. Lấy danh sách maSach + soLuong từ session ────────────────────────────
$cartRaw = [];
if (!empty($_SESSION['cart'])) {
    $cartRaw = $_SESSION['cart'];
} elseif (!empty($_SESSION['cart_temp'])) {
    $cartRaw = $_SESSION['cart_temp'];
}

if (empty($cartRaw)) {
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// ── 2. Trích xuất danh sách maSach (chỉ tin soLuong từ session) ─────────────
$dsMaSach = [];
$mapSoLuong = [];   // maSach => soLuong
foreach ($cartRaw as $item) {
    $ms = trim($item['maSach'] ?? '');
    $sl = max(1, (int)($item['soLuong'] ?? 1));
    if ($ms !== '') {
        $dsMaSach[] = $ms;
        $mapSoLuong[$ms] = $sl;
    }
}

if (empty($dsMaSach)) {
    echo "<script>alert('Giỏ hàng trống hoặc phiên giao dịch đã hết hạn!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// ── 3. Query DB: lấy thông tin sách + giá thật (có khuyến mãi flash sale) ──
$inPlaceholders = implode(',', array_fill(0, count($dsMaSach), '?'));

$sqlLayGia = "
    SELECT
        s.maSach,
        s.tenSach,
        s.giaBan,
        COALESCE(ha.urlAnh, '') AS hinhAnh,
        COALESCE(
            GROUP_CONCAT(DISTINCT tg.tenTG ORDER BY tg.maTG SEPARATOR ', '),
            'Đang cập nhật'
        ) AS tacGia,
        -- Áp dụng flash sale nếu còn hiệu lực
        (
            SELECT ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100))
            FROM ChiTietKhuyenMai ckm
            JOIN KhuyenMai km ON km.maKM = ckm.maKM
            WHERE ckm.maSach = s.maSach
              AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
            ORDER BY ckm.phanTramGiam DESC
            LIMIT 1
        ) AS giaSau
    FROM Sach s
    LEFT JOIN (
        SELECT maSach, MIN(urlAnh) AS urlAnh
        FROM HinhAnhSach
        GROUP BY maSach
    ) ha ON ha.maSach = s.maSach
    LEFT JOIN Sach_TacGia stg ON stg.maSach = s.maSach
    LEFT JOIN TacGia tg       ON tg.maTG   = stg.maTG
    WHERE s.maSach IN ($inPlaceholders)
    GROUP BY s.maSach, s.tenSach, s.giaBan, ha.urlAnh
";

$stmtGia = $pdo->prepare($sqlLayGia);
$stmtGia->execute($dsMaSach);
$sachDB = $stmtGia->fetchAll(PDO::FETCH_ASSOC);

// Lập map maSach => dữ liệu DB
$mapSachDB = [];
foreach ($sachDB as $row) {
    $mapSachDB[$row['maSach']] = $row;
}

// ── 4. Xây dựng $gioHang với giá TỪ DB (không từ client) ───────────────────
$gioHang = [];
foreach ($dsMaSach as $ms) {
    if (!isset($mapSachDB[$ms])) {
        continue; // Bỏ qua sách không tìm thấy trong DB
    }
    $dbRow = $mapSachDB[$ms];

    // Giá thực tế: ưu tiên giá sau khuyến mãi, không thì lấy giaBan gốc
    $giaChinh = ($dbRow['giaSau'] !== null)
        ? (float)$dbRow['giaSau']
        : (float)$dbRow['giaBan'];

    $gioHang[] = [
        'maSach'  => $ms,
        'tenSach' => $dbRow['tenSach'],
        'giaBan'  => $giaChinh,   // ← Giá đã được xác thực từ DB
        'hinhAnh' => $dbRow['hinhAnh'],
        'tacGia'  => $dbRow['tacGia'],
        'soLuong' => $mapSoLuong[$ms],
    ];
}

if (empty($gioHang)) {
    echo "<script>alert('Không tìm thấy sản phẩm hợp lệ trong giỏ hàng!'); window.location.href='/DoAn-Web/DoAn/index.php';</script>";
    exit;
}

// ── 5. Lưu tạm vào session (với giá đã được xác thực từ DB) ─────────────────
$_SESSION['cart_temp'] = $gioHang;

// ── 6. Tính tổng tiền (dùng giá từ DB) ──────────────────────────────────────
$tongTien = 0;
foreach ($gioHang as $sanPham) {
    $tongTien += $sanPham['giaBan'] * $sanPham['soLuong'];
}
