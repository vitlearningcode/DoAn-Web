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

$baseUrl = 'index.php?trang=nhaCungCap';
?>

<div class="adm-section-header">
    <div>
        <div class="adm-section-title">Quản lý Nhà Cung Cấp</div>
        <div class="adm-section-subtitle">Danh sách các đối tác cung cấp sách và chiết khấu mặc định</div>
    </div>
    <a href="#" class="adm-btn adm-btn-primary" onclick="alert('Tính năng thêm nhà cung cấp đang phát triển!'); return false;">
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
                    <td style="font-weight: 500; color: #1e293b;"><i class="far fa-building" style="margin-right:6px; color:#94a3b8"></i><?= htmlspecialchars($ncc['tenNCC']) ?></td>
                    <td><?= htmlspecialchars($ncc['sdt'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($ncc['email'] ?? '—') ?></td>
                    <td>
                        <span class="adm-badge adm-badge-info"><?= (float)($ncc['chietKhauMacDinh'] ?? 0) ?>%</span>
                    </td>
                    <td style="text-align:center">
                        <a href="#" class="adm-btn adm-btn-outline adm-btn-sm" onclick="alert('Chức năng chỉnh sửa đang phát triển!'); return false;">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
