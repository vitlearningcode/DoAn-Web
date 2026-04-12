<?php
/**
 * xuLyDiaChi.php — Xử lý POST form quản lý địa chỉ giao hàng
 * Kích hoạt khi: $_POST['hanh_dong_dia_chi'] được gửi
 * Hành động: them_moi | dat_mac_dinh | xoa_dia_chi
 *
 * PRG Pattern: sau khi xử lý → redirect về capNhat.php?trang=dia-chi&tb=...
 * Yêu cầu: $pdo, $maND đã khai báo
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hanh_dong_dia_chi'])) {
    $hanhDong = $_POST['hanh_dong_dia_chi'];

    // ── Thêm địa chỉ mới ──────────────────────────────────────────────────────
    if ($hanhDong === 'them_moi') {
        $diaChiMoi = trim($_POST['dia_chi_moi'] ?? '');
        $laMacDinh = isset($_POST['la_mac_dinh']) ? 1 : 0;

        if (!empty($diaChiMoi)) {
            if ($laMacDinh) {
                $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 0 WHERE maND = ?")->execute([$maND]);
            }
            $pdo->prepare("INSERT INTO DiaChiGiaoHang (maND, diaChiChiTiet, laMacDinh) VALUES (?, ?, ?)")
                ->execute([$maND, $diaChiMoi, $laMacDinh]);
            header('Location: capNhat.php?trang=dia-chi&tb=them_ok');
        } else {
            header('Location: capNhat.php?trang=dia-chi&tb=' . urlencode('Địa chỉ không được để trống.'));
        }
        exit;

    // ── Đặt địa chỉ mặc định ──────────────────────────────────────────────────
    } elseif ($hanhDong === 'dat_mac_dinh') {
        $maDiaChi    = (int)($_POST['ma_dc'] ?? 0);
        $stmtKiemTra = $pdo->prepare("SELECT maDC FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?");
        $stmtKiemTra->execute([$maDiaChi, $maND]);

        if ($stmtKiemTra->fetch()) {
            $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 0 WHERE maND = ?")->execute([$maND]);
            $pdo->prepare("UPDATE DiaChiGiaoHang SET laMacDinh = 1 WHERE maDC = ? AND maND = ?")->execute([$maDiaChi, $maND]);
            header('Location: capNhat.php?trang=dia-chi&tb=mac_dinh_ok');
        } else {
            header('Location: capNhat.php?trang=dia-chi&tb=' . urlencode('Không tìm thấy địa chỉ.'));
        }
        exit;

    // ── Xóa địa chỉ ───────────────────────────────────────────────────────────
    } elseif ($hanhDong === 'xoa_dia_chi') {
        $maDiaChi = (int)($_POST['ma_dc'] ?? 0);

        // Bước 1: Xác minh địa chỉ thuộc về người dùng này
        $stmtKT = $pdo->prepare("SELECT maDC FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?");
        $stmtKT->execute([$maDiaChi, $maND]);

        if (!$stmtKT->fetch()) {
            header('Location: capNhat.php?trang=dia-chi&tb=' . urlencode('Không tìm thấy địa chỉ hoặc bạn không có quyền xóa.'));
            exit;
        }

        // Bước 2: Kiểm tra địa chỉ có đang dùng trong đơn hàng không
        $stmtDH = $pdo->prepare("SELECT COUNT(*) FROM DonHang WHERE maDC = ?");
        $stmtDH->execute([$maDiaChi]);
        $soLuongDH = (int)$stmtDH->fetchColumn();

        if ($soLuongDH > 0) {
            $tb = 'Không thể xóa — địa chỉ đang dùng trong ' . $soLuongDH . ' đơn hàng.';
            header('Location: capNhat.php?trang=dia-chi&tb=' . urlencode($tb));
        } else {
            $pdo->prepare("DELETE FROM DiaChiGiaoHang WHERE maDC = ? AND maND = ?")
                ->execute([$maDiaChi, $maND]);
            header('Location: capNhat.php?trang=dia-chi&tb=xoa_ok');
        }
        exit;
    }
}
