<?php
// 1. Khởi động Session và Kết nối Database
session_start();
require_once "KetNoi/config/db.php"; 

// 2. Tạo biến Cảm biến đăng nhập cho JS
$isLoggedIn = isset($_SESSION['nguoi_dung_id']) ? true : false;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Sales Management - Cửa hàng sách trực tuyến</title>
   <link rel="stylesheet" href="GiaoDien/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        const dangDangNhap = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    </script>
</head>
<body>
    <?php include_once "CuaHang/TrangBanHang/GiaoDien/header.php"; ?>

    <main class="main-content container">
        <div id="home-content">
            
            <section class="hero-banner">
                <div id="hero-slider" class="hero-slider">
                    <?php
                    // Lấy Banner từ bảng QuangCao
                    $sql_banner = "SELECT * FROM QuangCao WHERE trangThai = 1 ORDER BY maQC ASC";
                    $stmt_banner = $pdo->query($sql_banner);
                    $demBanner = 0;
                    
                    if ($stmt_banner->rowCount() > 0) {
                        while ($banner = $stmt_banner->fetch(PDO::FETCH_ASSOC)) {
                            $classActive = ($demBanner === 0) ? 'active' : '';
                            echo '<div class="hero-slide ' . htmlspecialchars($banner['mauNen']) . ' ' . $classActive . '">';
                            echo '  <div class="hero-slide-bg">';
                            echo '      <img src="' . htmlspecialchars($banner['hinhAnh']) . '" alt="Banner">';
                            echo '      <div class="gradient-overlay"></div>';
                            echo '  </div>';
                            echo '  <div class="hero-content">';
                            echo '      <span class="hero-badge">' . htmlspecialchars($banner['nhan']) . '</span>';
                            echo '      <h2>' . $banner['tieuDe'] . '</h2>'; // TieuDe có chứa <br> nên không escape html
                            echo '      <p>' . htmlspecialchars($banner['moTa']) . '</p>';
                            echo '      <button class="hero-btn">' . htmlspecialchars($banner['chuNut']) . ' <i class="fas fa-arrow-right"></i></button>';
                            echo '  </div>';
                            echo '</div>';
                            $demBanner++;
                        }
                    } else {
                        echo '<p style="padding:20px; text-align:center;">Chưa có banner quảng cáo nào.</p>';
                    }
                    ?>
                </div>
                <button class="hero-nav prev" id="hero-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="hero-nav next" id="hero-next"><i class="fas fa-chevron-right"></i></button>
                <div class="hero-indicators" id="hero-indicators"></div>
            </section>

            <section class="categories-section">
                <div class="section-header">
                    <h3>Khám Phá Theo Danh Mục</h3>
                    <a href="#">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                </div>
                <div class="categories-grid">
                    <?php
                    // Lấy 6 thể loại đầu tiên từ bảng TheLoai
                    $sql_theloai = "SELECT * FROM TheLoai LIMIT 6";
                    $stmt_tl = $pdo->query($sql_theloai);
                    
                    // Mảng icon ngẫu nhiên vì bảng TheLoai của ông không có cột icon
                    $icons = ['📚', '📈', '🧠', '🧸', '🔬', '🌍', '🎨', '💻']; 
                    $i = 0;
                    
                    while($tl = $stmt_tl->fetch(PDO::FETCH_ASSOC)) {
                        $icon = $icons[$i % count($icons)];
                        echo '<a href="#" class="category-card">';
                        echo '  <div class="category-icon">' . $icon . '</div>';
                        echo '  <span>' . htmlspecialchars($tl['tenTL']) . '</span>';
                        echo '</a>';
                        $i++;
                    }
                    ?>
                </div>
            </section>

            <section class="featured-books">
                <div class="section-header">
                    <div>
                        <h3><i class="fas fa-star"></i> Sách Bán Chạy Nhất</h3>
                        <p>Những tựa sách được độc giả yêu thích nhất tuần qua</p>
                    </div>
                    <a href="#" class="view-all-btn">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                </div>
                
                <div class="books-grid" id="featured-books">
                    <?php
                    // CÂU SQL CỰC MẠNH: Gom Tác Giả, Lấy 1 Ảnh, Tính Tổng số lượng bán
                    $sql_banchay = "
                        SELECT 
                            s.maSach, 
                            s.tenSach, 
                            s.giaBan, 
                            s.moTa,
                            (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) as hinhAnh,
                            (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ') 
                             FROM Sach_TacGia stg 
                             JOIN TacGia tg ON stg.maTG = tg.maTG 
                             WHERE stg.maSach = s.maSach) as tacGia,
                            IFNULL((SELECT SUM(soLuong) FROM ChiTietDH WHERE maSach = s.maSach), 0) as tongBan
                        FROM Sach s
                        WHERE s.trangThai = 'DangKD'
                        ORDER BY tongBan DESC
                        LIMIT 8
                    ";
                    
                    $stmt_bc = $pdo->query($sql_banchay);
                    while($row = $stmt_bc->fetch(PDO::FETCH_ASSOC)) {
                        // Xử lý giá trị rỗng nếu sách chưa có ảnh hoặc tác giả
                        $anhSach = $row['hinhAnh'] ? $row['hinhAnh'] : 'https://via.placeholder.com/300x400?text=No+Image';
                        $tacGia = $row['tacGia'] ? $row['tacGia'] : 'Đang cập nhật';
                        ?>
                        
                        <div class="book-card" 
                             data-id="<?= htmlspecialchars($row['maSach']) ?>" 
                             data-name="<?= htmlspecialchars($row['tenSach']) ?>" 
                             data-price="<?= $row['giaBan'] ?>"
                             data-image="<?= htmlspecialchars($anhSach) ?>">
                             
                            <div class="book-image">
                                <img src="<?= htmlspecialchars($anhSach) ?>" alt="<?= htmlspecialchars($row['tenSach']) ?>" loading="lazy">
                                <div class="book-overlay">
                                    <button class="btn-icon btn-quick-view" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-add-cart" title="Thêm vào giỏ" onclick="themVaoGioHang(this)"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="book-info">
                                <h3 class="book-title"><?= htmlspecialchars($row['tenSach']) ?></h3>
                                <p class="book-author"><?= htmlspecialchars($tacGia) ?></p>
                                <div class="book-price">
                                    <span class="current-price"><?= number_format($row['giaBan'], 0, ',', '.') ?>đ</span>
                                </div>
                                <p style="font-size: 12px; color: var(--gray-500); margin-top: 5px;">Đã bán: <?= $row['tongBan'] ?></p>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </section>

            <section class="new-releases">
                <div class="section-header">
                    <div>
                        <h3><i class="fas fa-sparkles"></i> Sách Mới Phát Hành</h3>
                        <p>Cập nhật những tựa sách mới nhất từ các nhà xuất bản</p>
                    </div>
                    <a href="#" class="view-all-btn light">Xem tất cả <i class="fas fa-chevron-right"></i></a>
                </div>
                
                <div class="books-grid" id="new-releases">
                    <?php
                    // SQL lấy sách mới nhất dựa vào năm sản xuất và mã sách
                    $sql_sachmoi = "
                        SELECT 
                            s.maSach, 
                            s.tenSach, 
                            s.giaBan, 
                            s.moTa,
                            s.namSX,
                            (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) as hinhAnh,
                            (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ') 
                             FROM Sach_TacGia stg 
                             JOIN TacGia tg ON stg.maTG = tg.maTG 
                             WHERE stg.maSach = s.maSach) as tacGia
                        FROM Sach s
                        WHERE s.trangThai = 'DangKD'
                        ORDER BY s.namSX DESC, s.maSach DESC
                        LIMIT 8
                    ";
                    
                    $stmt_sm = $pdo->query($sql_sachmoi);
                    while($row = $stmt_sm->fetch(PDO::FETCH_ASSOC)) {
                        $anhSach = $row['hinhAnh'] ? $row['hinhAnh'] : 'https://via.placeholder.com/300x400?text=No+Image';
                        $tacGia = $row['tacGia'] ? $row['tacGia'] : 'Đang cập nhật';
                        ?>
                        
                        <div class="book-card" 
                             data-id="<?= htmlspecialchars($row['maSach']) ?>" 
                             data-name="<?= htmlspecialchars($row['tenSach']) ?>" 
                             data-price="<?= $row['giaBan'] ?>"
                             data-image="<?= htmlspecialchars($anhSach) ?>">
                             
                            <div class="book-image">
                                <span class="book-badge">Mới (<?= $row['namSX'] ?>)</span>
                                <img src="<?= htmlspecialchars($anhSach) ?>" alt="<?= htmlspecialchars($row['tenSach']) ?>" loading="lazy">
                                <div class="book-overlay">
                                    <button class="btn-icon btn-quick-view" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                                    <button class="btn-icon btn-add-cart" title="Thêm vào giỏ" onclick="themVaoGioHang(this)"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="book-info">
                                <h3 class="book-title"><?= htmlspecialchars($row['tenSach']) ?></h3>
                                <p class="book-author"><?= htmlspecialchars($tacGia) ?></p>
                                <div class="book-price">
                                    <span class="current-price"><?= number_format($row['giaBan'], 0, ',', '.') ?>đ</span>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </section>
        </div>
    </main>

    <?php include_once "CuaHang/TrangBanHang/GiaoDien/footer.php"; ?>

    <script src="PhuongThuc/thongBao.js"></script>
    <script src="PhuongThuc/trinhChieuBanner.js"></script>
    <script src="PhuongThuc/bookCard.js"></script>
    <script src="PhuongThuc/cart.js"></script>
    <script src="PhuongThuc/xacThuc.js"></script>
    <script src="PhuongThuc/xacNhanDangXuat.js"></script>
    <script src="PhuongThuc/chatbot.js"></script>
    
    <script src="PhuongThuc/btnDanhMuc.js"></script>
    <script src="PhuongThuc/btnThemGiohang.js"></script>
    <script src="PhuongThuc/app.js"></script>

</body>
</html>