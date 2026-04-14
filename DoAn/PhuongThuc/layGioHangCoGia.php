<?php
/**
 * layGioHangCoGia.php — Helper dùng chung: lấy cartServerData, __giaSach, __tonKhoMap từ DB
 *
 * BẢO MẬT: Giá luôn được lấy từ DB, không tin giá từ $_SESSION['cart'] (client-sent).
 *
 * Output:
 *   $cartServerDataArr  — mảng PHP [{maSach, soLuong, giaBan(DB), tenSach, hinhAnh, tacGia}]
 *   $giaSachMapJson     — JSON string {maSach: giaChinh} cho __giaSach JS variable
 *   $tonKhoMapJson      — JSON string {maSach: soLuongTon} cho __tonKhoMap JS variable
 *
 * Yêu cầu: $pdo đã được khởi tạo, session_start() đã gọi.
 */

// ── 1. Xây dựng cartServerData với giá từ DB ─────────────────────────────────
$cartServerDataArr = [];

if (!empty($_SESSION['cart'])) {
    $maSachCartList = [];
    $soLuongCartMap = [];

    foreach ($_SESSION['cart'] as $item) {
        $ms = trim($item['maSach'] ?? '');
        $sl = max(1, (int)($item['soLuong'] ?? 1));
        if ($ms !== '') {
            $maSachCartList[] = $ms;
            $soLuongCartMap[$ms] = $sl;
        }
    }

    if (!empty($maSachCartList)) {
        $inPh = implode(',', array_fill(0, count($maSachCartList), '?'));
        $stmtCart = $pdo->prepare("
            SELECT
                s.maSach,
                s.tenSach,
                s.giaBan,
                s.soLuongTon,
                COALESCE(ha.urlAnh, '') AS hinhAnh,
                COALESCE(
                    GROUP_CONCAT(DISTINCT tg.tenTG ORDER BY tg.maTG SEPARATOR ', '),
                    'Đang cập nhật'
                ) AS tacGia,
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
                FROM HinhAnhSach GROUP BY maSach
            ) ha ON ha.maSach = s.maSach
            LEFT JOIN Sach_TacGia stg ON stg.maSach = s.maSach
            LEFT JOIN TacGia tg ON tg.maTG = stg.maTG
            WHERE s.maSach IN ($inPh)
            GROUP BY s.maSach, s.tenSach, s.giaBan, s.soLuongTon, ha.urlAnh
        ");
        $stmtCart->execute($maSachCartList);
        $sachCartInfo = [];
        foreach ($stmtCart->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $sachCartInfo[$row['maSach']] = $row;
        }

        foreach ($maSachCartList as $ms) {
            if (!isset($sachCartInfo[$ms])) continue;
            $info = $sachCartInfo[$ms];
            // BẢO MẬT: giá từ DB, không từ session
            $giaChinh = ($info['giaSau'] !== null)
                ? (float)$info['giaSau']
                : (float)$info['giaBan'];
            // Số lượng giỏ hàng không được vượt tồn kho
            $tonKho = (int)$info['soLuongTon'];
            $slHopLe = min($soLuongCartMap[$ms], max(0, $tonKho));

            $cartServerDataArr[] = [
                'maSach'    => $ms,
                'tenSach'   => $info['tenSach'],
                'giaBan'    => $giaChinh,   // ← giá thật từ DB
                'hinhAnh'   => $info['hinhAnh'],
                'tacGia'    => $info['tacGia'],
                'soLuong'   => $slHopLe,
                'soLuongTon'=> $tonKho,     // ← giới hạn hiển thị trong giỏ
            ];
        }
    }
}

// ── 2. Xây dựng __giaSach + __tonKhoMap cho TẤT CẢ sách đang KD ─────────────────
// (Dùng nhẹ: chỉ lấy maSach + giá + tồn kho, không JOIN ảnh/tác giả)
$giaSachMapArr = [];
$tonKhoMapArr  = [];
try {
    $stmtMap = $pdo->query("
        SELECT
            s.maSach,
            s.soLuongTon,
            COALESCE(
                (
                    SELECT ROUND(s.giaBan * (1 - ckm.phanTramGiam / 100))
                    FROM ChiTietKhuyenMai ckm
                    JOIN KhuyenMai km ON km.maKM = ckm.maKM
                    WHERE ckm.maSach = s.maSach
                      AND NOW() BETWEEN km.ngayBatDau AND km.ngayKetThuc
                    ORDER BY ckm.phanTramGiam DESC
                    LIMIT 1
                ),
                s.giaBan
            ) AS giaChinh
        FROM Sach s
        WHERE s.trangThai = 'DangKD'
    ");
    foreach ($stmtMap->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $giaSachMapArr[$row['maSach']] = (float)$row['giaChinh'];
        $tonKhoMapArr[$row['maSach']]  = (int)$row['soLuongTon'];
    }
} catch (PDOException $e) {
    $giaSachMapArr = [];
    $tonKhoMapArr  = [];
}

$giaSachMapJson     = json_encode($giaSachMapArr, JSON_UNESCAPED_UNICODE);
$tonKhoMapJson      = json_encode($tonKhoMapArr,  JSON_UNESCAPED_UNICODE);
$cartServerDataJson = json_encode($cartServerDataArr, JSON_UNESCAPED_UNICODE);
