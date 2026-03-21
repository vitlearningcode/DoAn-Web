<?php
// test_sach.php

// 1. Nạp file cấu hình DB (Nó sẽ tạo ra biến $pdo)
require_once __DIR__ . '/config/db.php';

// 2. Nạp file DAO
require_once __DIR__ . '/DAO/SachDAO.php';

try {
    // Khởi tạo DAO và truyền kết nối $pdo vào
    $sachDAO = new SachDAO($pdo);

    // Lấy toàn bộ sách
    $danhSachSPSach = $sachDAO->getAll();

    echo "<h1>Danh mục sách của cửa hàng</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Mã SP</th><th>Tên Sách</th><th>Loại Bìa</th><th>Giá Bán</th><th>Tồn Kho</th></tr>";

    // Duyệt qua mảng Object và in ra màn hình
    foreach ($danhSachSPSach as $sach) {
        // Bạn sẽ thấy VS Code tự động gợi ý $sach->tenSach, $sach->giaBan,... rất mượt
        echo "<tr>";
        echo "<td>{$sach->maSach}</td>";
        echo "<td><b>{$sach->tenSach}</b></td>";
        echo "<td>{$sach->loaiBia}</td>";
        echo "<td>" . number_format($sach->giaBan, 0, ',', '.') . " đ</td>";
        echo "<td>{$sach->soLuongTon} cuốn</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Test thử lấy 1 cuốn sách cụ thể
    echo "<h2>Test tìm sách cụ thể:</h2>";
    $sachS001 = $sachDAO->getById('S001');
    if ($sachS001) {
        echo "<p>Đã tìm thấy: {$sachS001->tenSach} - Giá: " . number_format($sachS001->giaBan) . "đ</p>";
    }

} catch (Exception $e) {
    echo "Đã xảy ra lỗi: " . $e->getMessage();
}
?>