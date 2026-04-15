<?php
// ══════════════════════════════════════════════════════
//  nhaCungCap.php — Quản lý đối tác và nhà cung cấp
// ══════════════════════════════════════════════════════

require_once __DIR__ . '/../_kiemTraQuyen.php';

try {
    $dsNCC = $pdo->query("
        SELECT * FROM NhaCungCap ORDER BY maNCC DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $dsNCC = [];
}

// Lấy thông tin nhà cung cấp cần sửa
$suaNCC = null;
$suaMaNCC = (int)($_GET['sua'] ?? 0);
if ($suaMaNCC > 0) {
    try {
        $stmtSua = $pdo->prepare("SELECT * FROM NhaCungCap WHERE maNCC = ?");
        $stmtSua->execute([$suaMaNCC]);
        $suaNCC = $stmtSua->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) { $suaNCC = null; }
}

$baseUrl = 'index.php?trang=nhaCungCap';

// ── Xem chi tiết công nợ NCC ───────────────────────────────
$xemNCC = (int)($_GET['xem_no'] ?? 0);
$congNoNCC = null;
$dsPhieuNo = [];
$dsLichSu  = [];
if ($xemNCC > 0) {
    try {
        // Thông tin NCC + tổng công nợ
        $stmtNCC = $pdo->prepare("SELECT n.*, COALESCE(c.tongNo, 0) AS tongNo
            FROM NhaCungCap n LEFT JOIN CongNo c ON c.maNCC = n.maNCC
            WHERE n.maNCC = ?");
        $stmtNCC->execute([$xemNCC]);
        $congNoNCC = $stmtNCC->fetch(PDO::FETCH_ASSOC);

        // Danh sách phiếu nhập thuộc NCC (cột ngày là ngayLap)
        $stmtPN = $pdo->prepare("
            SELECT maPN, ngayLap, tongTien, soTienDaThanhToan,
                   (tongTien - soTienDaThanhToan) AS conNo, trangThai
            FROM PhieuNhap WHERE maNCC = ?
            ORDER BY ngayLap DESC");
        $stmtPN->execute([$xemNCC]);
        $dsPhieuNo = $stmtPN->fetchAll(PDO::FETCH_ASSOC);

        // Lịch sử thanh toán — thử cột ngayTra trước, fallback nếu không tồn tại
        try {
            $stmtLS = $pdo->prepare("
                SELECT ls.maPN, ls.soTienTra, ls.hinhThucTra, ls.ghiChu, ls.ngayTra
                FROM LichSuThanhToanPN ls
                JOIN PhieuNhap pn ON ls.maPN = pn.maPN
                WHERE pn.maNCC = ?
                ORDER BY ls.ngayTra DESC");
            $stmtLS->execute([$xemNCC]);
            $dsLichSu = $stmtLS->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e2) {
            $stmtLS2 = $pdo->prepare("
                SELECT ls.maPN, ls.soTienTra, ls.hinhThucTra, ls.ghiChu,
                       NOW() AS ngayTra
                FROM LichSuThanhToanPN ls
                JOIN PhieuNhap pn ON ls.maPN = pn.maPN
                WHERE pn.maNCC = ?");
            $stmtLS2->execute([$xemNCC]);
            $dsLichSu = $stmtLS2->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Throwable $e) { $congNoNCC = null; }
}
?>

<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Quản lý Nhà Cung Cấp</div>
        <div class="adm-section-subtitle">Danh sách các đối tác cung cấp sách và chiết khấu mặc định</div>
    </div>
    <a href="<?= $baseUrl ?>&them=1" class="adm-btn adm-btn-primary">
        <i class="fas fa-plus"></i> Thêm nhà cung cấp
    </a>
</div>

<div class="adm-card">
    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Mã NCC</th>
                    <th>Tên đối tác</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Chiết khấu MĐ</th>
                    <th>Công nợ</th>
                    <th style="text-align:center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($dsNCC)): ?>
                <tr><td colspan="6"><div class="adm-empty"><i class="fas fa-handshake"></i><p>Chưa có nhà cung cấp nào.</p></div></td></tr>
            <?php else: ?>
                <?php foreach ($dsNCC as $ncc): ?>
                <tr>
                    <td><strong>#<?= htmlspecialchars($ncc['maNCC']) ?></strong></td>
                    <td style="font-weight:500;color:#1e293b"><i class="far fa-building" style="margin-right:6px;color:#94a3b8"></i><?= htmlspecialchars($ncc['tenNCC']) ?></td>
                    <td><?= htmlspecialchars($ncc['sdt'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($ncc['email'] ?? '—') ?></td>
                    <td><span class="adm-badge adm-badge-info"><?= (float)($ncc['chietKhauMacDinh'] ?? 0) ?>%</span></td>
                    <td>
                        <?php
                        // Lấy nhanh tổng nợ để hiển thị
                        try {
                            $stmtNo = $pdo->prepare("SELECT COALESCE(tongNo,0) FROM CongNo WHERE maNCC=?");
                            $stmtNo->execute([$ncc['maNCC']]);
                            $tongNo = (float)($stmtNo->fetchColumn() ?: 0);
                        } catch(Throwable $e) { $tongNo = 0; }
                        ?>
                        <?php if ($tongNo > 0): ?>
                            <a href="<?= $baseUrl ?>&xem_no=<?= $ncc['maNCC'] ?>"
                               style="color:#dc2626;font-weight:600;font-size:13px;text-decoration:none">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= number_format($tongNo,0,',','.') ?>₫
                            </a>
                        <?php else: ?>
                            <span style="color:#16a34a;font-size:13px"><i class="fas fa-check-circle"></i> Không nợ</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;white-space:nowrap">
                        <a href="<?= $baseUrl ?>&xem_no=<?= $ncc['maNCC'] ?>" class="adm-btn adm-btn-outline adm-btn-sm" title="Xem công nợ">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </a>
                        <a href="<?= $baseUrl ?>&sua=<?= $ncc['maNCC'] ?>" class="adm-btn adm-btn-outline adm-btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form method="POST" action="XuLy/nhaCungCap.php" style="display:inline"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này không? (Nếu NCC đã có phiếu nhập, sẽ không thể xóa)')">
                            <input type="hidden" name="maNCC" value="<?= $ncc['maNCC'] ?>">
                            <input type="hidden" name="hanh_dong" value="xoa">
                            <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= POPUP THÊM / SỬA ================= -->
<?php if (isset($_GET['them']) || $suaNCC): 
    $laSua = $suaNCC !== null;
    $tieude = $laSua ? "Sửa Nhà Cung Cấp #{$suaNCC['maNCC']}" : "Thêm Nhà Cung Cấp Mới";
    $hanh_dong = $laSua ? 'sua' : 'them';
?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-size:16px;font-weight:700"><?= $tieude ?></h3>
        <a href="<?= $baseUrl ?>" style="color:#94a3b8;font-size:20px;text-decoration:none"><i class="fas fa-times"></i></a>
    </div>
    <form method="POST" action="XuLy/nhaCungCap.php" style="padding:24px">
        <input type="hidden" name="hanh_dong" value="<?= $hanh_dong ?>">
        <?php if ($laSua): ?>
            <input type="hidden" name="maNCC" value="<?= $suaNCC['maNCC'] ?>">
        <?php endif; ?>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Tên Nhà Cung Cấp <span style="color:#ef4444">*</span></label>
            <input class="adm-input" type="text" name="tenNCC" value="<?= $laSua ? htmlspecialchars($suaNCC['tenNCC']) : '' ?>" placeholder="VD: Công ty TNHH Sách..." required>
        </div>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Số Điện Thoại</label>
            <input class="adm-input" type="text" name="sdt" value="<?= $laSua ? htmlspecialchars($suaNCC['sdt'] ?? '') : '' ?>" placeholder="VD: 0912345678">
        </div>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Email</label>
            <input class="adm-input" type="email" name="email" value="<?= $laSua ? htmlspecialchars($suaNCC['email'] ?? '') : '' ?>" placeholder="VD: contact@company.com">
        </div>
        
        <div class="adm-form-group" style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:600">Chiết Khấu Mặc Định (%)</label>
            <input class="adm-input" type="number" step="0.1" name="chietKhauMacDinh" value="<?= $laSua ? (float)($suaNCC['chietKhauMacDinh'] ?? 0) : '0' ?>" required>
            <small style="color:#64748b;font-size:11px;margin-top:4px;display:block;">Chiết khấu mặc định để tính giá nhập sách. 0-100%.</small>
        </div>
        
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px">
            <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline">Hủy</a>
            <button type="submit" class="adm-btn adm-btn-primary">
                <i class="fas fa-save"></i> <?= $laSua ? 'Lưu thay đổi' : 'Thêm mới' ?>
            </button>
        </div>
    </form>
</div>
</div>
<?php endif; ?>

<!-- ═══ POPUP: XEM CHI TIẾT CÔNG NỢ ═══ -->
<?php if ($xemNCC > 0 && $congNoNCC): ?>
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:600;display:flex;align-items:center;justify-content:center;padding:20px;overflow-y:auto">
<div style="background:#fff;border-radius:16px;width:100%;max-width:720px;box-shadow:0 20px 60px rgba(0,0,0,0.25);max-height:90vh;overflow-y:auto">

    <!-- Header -->
    <div style="padding:18px 24px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;z-index:1">
        <div>
            <h3 style="font-size:16px;font-weight:700;margin:0">
                <i class="fas fa-file-invoice-dollar" style="color:#2563eb;margin-right:8px"></i>
                Công nợ: <?= htmlspecialchars($congNoNCC['tenNCC']) ?>
            </h3>
            <div style="font-size:12px;color:#64748b;margin-top:3px">Mã NCC: #<?= $congNoNCC['maNCC'] ?></div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <div style="text-align:right">
                <div style="font-size:11px;color:#64748b">Tổng còn nợ</div>
                <div style="font-size:20px;font-weight:800;color:<?= $congNoNCC['tongNo'] > 0 ? '#dc2626' : '#16a34a' ?>">
                    <?= number_format((float)$congNoNCC['tongNo'], 0, ',', '.') ?>₫
                </div>
            </div>
            <a href="<?= $baseUrl ?>" style="color:#94a3b8;font-size:22px;text-decoration:none;line-height:1">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </div>

    <div style="padding:20px 24px">

        <!-- PHẦN 1: Danh sách phiếu nhập -->
        <h4 style="font-size:13px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
            <i class="fas fa-receipt" style="margin-right:6px"></i>Phiếu nhập hàng
        </h4>
        <?php if (empty($dsPhieuNo)): ?>
            <p style="color:#94a3b8;font-size:13px">Chưa có phiếu nhập nào.</p>
        <?php else: ?>
        <div style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:20px">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead>
                    <tr style="background:#f8fafc">
                        <th style="padding:8px 12px;text-align:left;font-weight:600;color:#64748b;border-bottom:1px solid #e2e8f0">Mã phiếu</th>
                        <th style="padding:8px 12px;text-align:left;font-weight:600;color:#64748b;border-bottom:1px solid #e2e8f0">Ngày nhập</th>
                        <th style="padding:8px 12px;text-align:right;font-weight:600;color:#64748b;border-bottom:1px solid #e2e8f0">Tổng tiền</th>
                        <th style="padding:8px 12px;text-align:right;font-weight:600;color:#64748b;border-bottom:1px solid #e2e8f0">Đã trả</th>
                        <th style="padding:8px 12px;text-align:right;font-weight:600;color:#64748b;border-bottom:1px solid #e2e8f0">Còn nợ</th>
                        <th style="padding:8px 12px;text-align:center;font-weight:600;color:#64748b;border-bottom:1px solid #e2e8f0">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dsPhieuNo as $i => $pn):
                    $conNo = (float)$pn['tongTien'] - (float)$pn['soTienDaThanhToan'];
                ?>
                <tr style="<?= $i % 2 === 1 ? 'background:#fafafa' : '' ?>">
                    <td style="padding:8px 12px;font-weight:600"><?= htmlspecialchars($pn['maPN']) ?></td>
                    <td style="padding:8px 12px;color:#64748b"><?= date('d/m/Y', strtotime($pn['ngayLap'])) ?></td>
                    <td style="padding:8px 12px;text-align:right"><?= number_format((float)$pn['tongTien'],0,',','.') ?>₫</td>
                    <td style="padding:8px 12px;text-align:right;color:#16a34a;font-weight:600"><?= number_format((float)$pn['soTienDaThanhToan'],0,',','.') ?>₫</td>
                    <td style="padding:8px 12px;text-align:right;color:<?= $conNo > 0 ? '#dc2626' : '#16a34a' ?>;font-weight:700">
                        <?= $conNo > 0 ? number_format($conNo,0,',','.').'₫' : '—' ?>
                    </td>
                    <td style="padding:8px 12px;text-align:center">
                        <?php if ($pn['trangThai'] === 'Completed'): ?>
                            <span style="background:#dcfce7;color:#16a34a;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700">Đã thanh toán</span>
                        <?php else: ?>
                            <span style="background:#fee2e2;color:#dc2626;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700">Còn nợ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- PHẦN 2: Lịch sử từng đợt thanh toán -->
        <h4 style="font-size:13px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
            <i class="fas fa-history" style="margin-right:6px"></i>Lịch sử thanh toán từng đợt
        </h4>
        <?php if (empty($dsLichSu)): ?>
            <p style="color:#94a3b8;font-size:13px">Chưa có đợt thanh toán nào.</p>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px">
            <?php foreach ($dsLichSu as $ls): ?>
            <div style="border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px;display:flex;align-items:center;gap:14px;background:#fff">
                <!-- Icon -->
                <div style="width:38px;height:38px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas fa-money-bill-wave" style="color:#2563eb;font-size:15px"></i>
                </div>
                <!-- Nội dung -->
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;color:#1e293b">
                        <?= number_format((float)$ls['soTienTra'],0,',','.') ?>₫
                        <span style="font-weight:400;color:#64748b"> — Phiếu <strong><?= htmlspecialchars($ls['maPN']) ?></strong></span>
                    </div>
                    <div style="font-size:12px;color:#94a3b8;margin-top:2px">
                        <?= htmlspecialchars($ls['hinhThucTra']) ?>
                        <?php if ($ls['ghiChu']): ?> · <?= htmlspecialchars($ls['ghiChu']) ?><?php endif; ?>
                    </div>
                </div>
                <!-- Ngày -->
                <div style="text-align:right;flex-shrink:0">
                    <div style="font-size:12px;color:#64748b"><?= date('d/m/Y', strtotime($ls['ngayTra'])) ?></div>
                    <div style="font-size:11px;color:#94a3b8"><?= date('H:i', strtotime($ls['ngayTra'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div><!-- /padding -->

    <div style="padding:14px 24px;border-top:1px solid #f1f5f9;text-align:right">
        <a href="<?= $baseUrl ?>" class="adm-btn adm-btn-outline">Đóng</a>
    </div>
</div>
</div>
<?php endif; /* endif xem_no */ ?>

