<?php
/**
 * dauTrang.php — Phần đầu trang cố định
 * Bao gồm: TopBar + Header (logo, tìm kiếm, nút user/giỏ hàng) + Nav danh mục
 * Yêu cầu: $isLoggedIn (bool), $duong_dan_goc (string) đã được khai báo
 */
?>
<div id="dau-trang-co-dinh">
<div id="app">
    <!-- ── Thanh trên cùng ── -->
    <div class="top-bar">
        <div class="container">
            <marquee width="100%" behavior="alternate" scrollamount="2">
                <p style="font-size: 11pt;">Sách không tự mất đi, nó chỉ chuyển từ chỗ này sang chỗ khác (nếu chúng tôi có)</p>
            </marquee>
            <div class="top-bar-links">
                <a href="javascript:void(0)" onclick="moTraCuuDonHang()">
                    <!-- fas fa-box-open: Class của thư viện FontAwesome (Giữ nguyên tiếng Anh) -->
                    <i class="fas fa-box-open" style="margin-right:4px;"></i>Theo dõi đơn hàng
                </a>
                <a href="javascript:void(0)" onclick="moHoTro()">
                    <i class="fas fa-headset" style="margin-right:4px;"></i>Hỗ trợ khách hàng
                </a>
            </div>
        </div>
    </div>

    <!-- ── Header chính ── -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <div class="logo-icon">
                        <!-- fas fa-book-open: Class của thư viện FontAwesome -->
                        <!-- <i class="fas fa-book-open"></i> -->
                        <svg width="45" height="60" viewBox="0 0 183 159" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_9_38)">
                            <path d="M173 9L163.93 66.5C163.88 66.53 154.09 34.33 152.56 29.75C152.45 29.42 152.15 29.2 151.8 29.21C146.63 29.29 107.36 30.53 107.36 30.5C121.27 26.1 156.29 11.57 173 9Z" fill="#F7911E"/>
                            <path d="M135.11 37.3L109.17 36.81C94.12 43.88 85 53 85 53C46 20 10 30 10 30V129.76L65.88 73.88L66 74L66.22 73.78L88.1 95.66L136.97 41.3C138.43 39.74 137.25 37.19 135.11 37.3Z" fill="#3D6BE3"/>
                            <path d="M159.4 75.1L158 126C158 126 113 127 87 151C87 151 54.24 126.64 19.51 128.73L66.1 84.74L87.98 108L146.08 46.03C147.36 44.7 149.61 45.28 150.09 47.06L159.4 75.1Z" fill="#2851EF"/>
                            </g>
                            <mask id="mask0_9_38" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="9" y="28" width="151" height="123">
                            <path d="M160 74.7429L158.591 125.882C158.591 125.882 113.305 126.887 87.1402 151C87.1402 151 54.1722 126.525 19.2216 128.625L66.1075 84.4282L88.1264 107.798L146.595 45.5361C147.884 44.1999 150.148 44.7826 150.631 46.571L160 74.7429Z" fill="#253574"/>
                            <path d="M135.556 36.765L109.451 36.2727C94.3054 43.376 85.1275 52.5389 85.1275 52.5389C45.8798 19.3836 9.65118 29.4307 9.65118 29.4307V129.66L65.8861 73.5171L66.0068 73.6377L66.2282 73.4167L88.2472 95.3996L137.428 40.7839C138.897 39.2165 137.709 36.6545 135.556 36.765Z" fill="#3D5BA9"/>
                            </mask>
                            <g mask="url(#mask0_9_38)">
                            <path d="M85.54 41.6107L159.567 17.4139L179.207 167.131H87.5544L88 108L85.54 41.6107Z" fill="url(#paint0_linear_9_38)"/>
                            </g>
                            <defs>
                            <linearGradient id="paint0_linear_9_38" x1="70.5" y1="95" x2="182" y2="70" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#131313" stop-opacity="0.34"/>
                            <stop offset="0.328397" stop-opacity="0"/>
                            </linearGradient>
                            <clipPath id="clip0_9_38">
                            <rect width="163" height="142" fill="white" transform="translate(10 9)"/>
                            </clipPath>
                            </defs>
                        </svg>

                         
                    </div>
                    <div class="logo-text">
                        <a href="<?= $duong_dan_goc ?>index.php" style="text-decoration:none; color:inherit;"><h1>BOOK SALES</h1></a>
                        <p>STOREFRONT</p>
                    </div>
                </div>

                <!-- Ô tìm kiếm nhanh -->
                <div class="search-box" id="khung-tim-kiem" style="position: relative;">
                    <input type="text" id="o-nhap-tu-khoa" placeholder="Tìm kiếm tựa sách, tác giả..." onkeyup="timKiemNhanh(this.value)" autocomplete="off">
                    <button>
                        <!-- fas fa-search: Class của thư viện FontAwesome -->
                        <i class="fas fa-search"></i>
                    </button>
                    <div id="danh-sach-ket-qua" class="khung-goi-y-tim-kiem" style="display: none;"></div>
                </div>

                <!-- Nút hành động: đăng nhập / user / giỏ hàng -->
                <div class="header-actions">
                    <?php if ($isLoggedIn): ?>
                    <div class="user-dropdown-container">
                        <button class="action-btn profile-ring-btn" id="btn-user-profile" onclick="toggleUserMenu(event)">
                            <div class="profile-avatar">
                                <!-- fas fa-user: Class của thư viện FontAwesome -->
                                <i class="fas fa-user"></i>
                            </div>
                            <span><?= htmlspecialchars($_SESSION['ten_nguoi_dung'] ?? 'Tài khoản') ?></span>
                        </button>

                        <div class="user-dropdown-menu" id="userDropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-avatar"><i class="fas fa-user"></i></div>
                                <div class="dropdown-user-info">
                                    <strong><?= htmlspecialchars($_SESSION['ten_nguoi_dung'] ?? 'Khách hàng') ?></strong>
                                    <p><?= htmlspecialchars($_SESSION['vaitro'] ?? 'Khách hàng') ?></p>
                                </div>
                            </div>
                            <ul class="dropdown-list">
                                <li><a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/taiKhoan/capNhat.php"><i class="fas fa-user-edit"></i> Sửa thông tin</a></li>
                                <li><a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/donHang/theoDoiDonHang.php"><i class="fas fa-box"></i> Theo dõi đơn hàng</a></li>
                                <li>
                                    <a href="<?= $duong_dan_goc ?>CuaHang/TrangBanHang/taiKhoan/sachYeuThich.php">
                                        <i class="fas fa-heart" style="color: #ef4444;"></i> Sách yêu thích
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0)" onclick="openLogout()" class="text-danger"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php else: ?>
                    <button class="action-btn" onclick="openLogin()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Đăng nhập</span>
                    </button>
                    <?php endif; ?>

                    <!-- Nút giỏ hàng -->
                    <button class="action-btn" id="btn-cart">
                        <div class="cart-icon-wrapper">
                            <!-- fas fa-shopping-cart: Class của thư viện FontAwesome -->
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count hidden" id="cart-count">0</span>
                        </div>
                        <span>Giỏ hàng</span>
                    </button>
                </div>
            </div>
<!-- ===========================================================================danh mục trượt============================================== -->
            <nav class="categories-nav">
                
                <div class="category-dropdown-wrapper" style="position: relative;">
                    <button class="category-btn" id="nut-danh-muc-bay" style="display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20" fill="currentColor">
                            <path d="M34.283,384c17.646,30.626,56.779,41.148,87.405,23.502c0.021-0.012,0.041-0.024,0.062-0.036l9.493-5.483 c17.92,15.332,38.518,27.222,60.757,35.072V448c0,35.346,28.654,64,64,64s64-28.654,64-64v-10.944 c22.242-7.863,42.841-19.767,60.757-35.115l9.536,5.504c30.633,17.673,69.794,7.167,87.467-23.467 c17.673-30.633,7.167-69.794-23.467-87.467l0,0l-9.472-5.461c4.264-23.201,4.264-46.985,0-70.187l9.472-5.461 c30.633-17.673,41.14-56.833,23.467-87.467c-17.673-30.633-56.833-41.14-87.467-23.467l-9.493,5.483 C362.862,94.638,342.25,82.77,320,74.944V64c0-35.346-28.654-64-64-64s-64,28.654-64,64v10.944 c-22.242,7.863-42.841,19.767-60.757,35.115l-9.536-5.525C91.073,86.86,51.913,97.367,34.24,128s-7.167,69.794,23.467,87.467l0,0 l9.472,5.461c-4.264,23.201-4.264,46.985,0,70.187l-9.472,5.461C27.158,314.296,16.686,353.38,34.283,384z M256,170.667 c47.128,0,85.333,38.205,85.333,85.333S303.128,341.333,256,341.333S170.667,303.128,170.667,256S208.872,170.667,256,170.667z"/>
                        </svg>
                        Danh mục sách |
                    </button>

                    <div id="menu-bay-the-loai" class="fly-menu-container">
                        <ul class="fly-menu-list">
                            <?php
                            try {
                                // Gọi DB lấy danh sách thể loại
                                $stmtTheLoai = $pdo->query("SELECT maTL, tenTL FROM TheLoai ORDER BY tenTL ASC");
                                $dsTheLoai = $stmtTheLoai->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($dsTheLoai as $index => $tl) {
                                    $delay = ($index * 0.05) . 's';
                                    // Gắn link trỏ về trang trangTheLoai.php nằm trong thư mục GiaoDien
                                    $linkLoc = $duong_dan_goc . "CuaHang/TrangBanHang/GiaoDien/trangTheLoai.php?theloai=" . urlencode($tl['tenTL']);
                                    
                                    echo "<li style='transition-delay: {$delay}'>
                                            <a href='{$linkLoc}'>" . htmlspecialchars($tl['tenTL']) . "</a>
                                          </li>";
                                }
                            } catch (Exception $e) {
                                echo "<li><a href='#'>Lỗi tải danh mục</a></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div> 
            </nav>
        </div>
    </header>
</div><!-- /app -->
</div><!-- /dau-trang-co-dinh -->
