<?php
/**
 * taiKhoan/capNhat.php — Trang cập nhật thông tin tài khoản (Entry Point)
 * Thuần PHP form POST → PRG (Post-Redirect-Get) — không AJAX.
 *
 * Tham số GET:
 *   ?trang=thong-tin | dia-chi   (section đang active)
 *   ?tb=...                       (thông báo sau redirect)
 */
session_start();
require_once '../../../KetNoi/config/db.php';

$isLoggedIn = isset($_SESSION['nguoi_dung_id']);
if (!$isLoggedIn) {
    header('Location: ../../../index.php');
    exit;
}

$maND = (int)$_SESSION['nguoi_dung_id'];

// ── Xử lý POST (các handler tự redirect về đây với ?trang= ) ──────────────────
require_once 'xuLyCapNhatThongTin.php';
require_once 'xuLyDiaChi.php';

// ── Đọc tham số GET ───────────────────────────────────────────────────────────
$danhSachTrang = ['thong-tin', 'dia-chi'];
$trangHienTai  = $_GET['trang'] ?? 'thong-tin';
if (!in_array($trangHienTai, $danhSachTrang)) {
    $trangHienTai = 'thong-tin';
}

// Giải mã thông báo từ ?tb=
$tbRaw        = $_GET['tb'] ?? '';
$thongBao     = '';
$loaiThongBao = '';

if ($tbRaw !== '') {
    // Mã kết quả thành công đã được định sẵn
    $mucThanhCong = ['cap_nhat_ok', 'them_ok', 'mac_dinh_ok', 'xoa_ok'];
    if (in_array($tbRaw, $mucThanhCong)) {
        $loaiThongBao = 'success';
        $banDoThongBao = [
            'cap_nhat_ok'  => 'Cập nhật thông tin thành công!',
            'them_ok'      => 'Đã thêm địa chỉ mới thành công!',
            'mac_dinh_ok'  => 'Đã đặt làm địa chỉ mặc định.',
            'xoa_ok'       => 'Đã xóa địa chỉ thành công.',
        ];
        $thongBao = $banDoThongBao[$tbRaw];
    } else {
        // Thông báo lỗi được encode vào URL
        $loaiThongBao = 'error';
        $thongBao     = htmlspecialchars(urldecode($tbRaw));
    }
}

// ── Lấy dữ liệu hiển thị ─────────────────────────────────────────────────────
require_once 'layThongTinTaiKhoan.php';

$duong_dan_goc = '/DoAn-Web/DoAn/';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản - Book Sales</title>
    <link rel="stylesheet" href="../../../GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <script>const dangDangNhap = true;</script>
    <script>var cartServerData = <?= json_encode($_SESSION['cart'] ?? [], JSON_UNESCAPED_UNICODE) ?>;</script>
    <?php require_once 'cssDanhSachTaiKhoan.php'; ?>
    <style>
    /* ── Tab điều hướng tài khoản ── */
    .cn-tabs {
        display: flex;
        gap: 4px;
        background: #fff;
        border-radius: 12px;
        padding: 6px;
        box-shadow: 0 1px 8px rgba(0,0,0,.08);
        margin-bottom: 24px;
    }
    .cn-tab {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: .88rem;
        font-weight: 600;
        color: #6b7280;
        text-decoration: none;
        transition: all .2s;
    }
    .cn-tab:hover { background: #f3f4f6; color: #374151; }
    .cn-tab.active {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        box-shadow: 0 2px 8px rgba(99,102,241,.35);
    }
    .cn-tab i { font-size: .85rem; }
    </style>
</head>
<body>
<?php include_once '../../../CuaHang/TrangBanHang/GiaoDien/header.php'; ?>

<div class="cn-trang">
    <a href="<?= $duong_dan_goc ?>index.php" class="cn-quay-lai">
        <i class="fas fa-arrow-left"></i> Quay lại cửa hàng
    </a>

    <!-- Tab điều hướng: ?trang=thong-tin | ?trang=dia-chi -->
    <div class="cn-tabs" role="tablist">
        <a href="?trang=thong-tin"
           class="cn-tab <?= $trangHienTai === 'thong-tin' ? 'active' : '' ?>"
           role="tab"
           aria-selected="<?= $trangHienTai === 'thong-tin' ? 'true' : 'false' ?>">
            <i class="fas fa-user-edit"></i> Thông tin cá nhân
        </a>
        <a href="?trang=dia-chi"
           class="cn-tab <?= $trangHienTai === 'dia-chi' ? 'active' : '' ?>"
           role="tab"
           aria-selected="<?= $trangHienTai === 'dia-chi' ? 'true' : 'false' ?>">
            <i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng
        </a>
    </div>

    <!-- Thông báo sau redirect -->
    <?php if ($thongBao): ?>
    <div class="cn-thong-bao <?= $loaiThongBao ?>">
        <i class="fas fa-<?= $loaiThongBao === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
        <?= $thongBao ?>
    </div>
    <?php endif; ?>

    <!-- Nội dung theo tab -->
    <?php if ($trangHienTai === 'thong-tin'): ?>
        <?php require_once 'formThongTinCaNhan.php'; ?>

    <?php elseif ($trangHienTai === 'dia-chi'): ?>
        <div class="cn-the">
            <p class="cn-tieu-muc">
                <i class="fas fa-map-marker-alt"></i> Địa Chỉ Giao Hàng
            </p>
            <p style="font-size:.85rem;color:#6b7280;margin:-10px 0 18px;">
                Địa chỉ mặc định sẽ được điền sẵn khi thanh toán.
            </p>
            <?php require_once 'danhSachDiaChi.php'; ?>
            <?php require_once 'formThemDiaChi.php'; ?>
        </div>
    <?php endif; ?>

</div>

<?php include_once '../../../CuaHang/TrangBanHang/GioHang/formGioHang.php'; ?>
<script src="../../../PhuongThuc/cart.js"></script>
<script src="../../../PhuongThuc/components/xacNhanDangXuat.js"></script>
<script src="../../../PhuongThuc/components/xacThuc.js"></script>
<script>
function toggleUserMenu(e) {
    e.stopPropagation();
    var menuNguoiDung = document.getElementById('userDropdown');
    if (menuNguoiDung) menuNguoiDung.classList.toggle('show');
}
document.addEventListener('click', function() {
    var menuNguoiDung = document.getElementById('userDropdown');
    if (menuNguoiDung) menuNguoiDung.classList.remove('show');
});
</script>
<script src="<?= $duong_dan_goc ?>PhuongThuc/app.js"></script>
</body>
</html>
