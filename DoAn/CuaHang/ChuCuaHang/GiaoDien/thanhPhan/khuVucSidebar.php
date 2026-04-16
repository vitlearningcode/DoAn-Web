<?php
/**
 * khuVucSidebar.php — HTML Sidebar admin: logo + nav items + link thoát
 * Yêu cầu: $trangHienTai, $soDonChoDuyet, $adminUrl đã khai báo
 */
?>
<aside class="adm-sidebar" id="adm-sidebar">

    <!-- Logo -->
    <div class="adm-sidebar-logo">
        <div class="logo-icon">
            <!-- <i class="fas fa-book-open"></i> -->
              <svg width="50" height="70" viewBox="0 0 183 159" fill="none" xmlns="http://www.w3.org/2000/svg">
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
            <h2>BOOK SALES</h2>
            <p>MANAGEMENT</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="adm-sidebar-nav">

        <div class="adm-nav-section">
            <p class="adm-nav-section-label">Tổng quan</p>
            <a href="<?= $adminUrl ?>?trang=tongQuan"
               class="adm-nav-item<?= navActive('tongQuan', $trangHienTai) ?>">
                <i class="fas fa-chart-pie"></i>
                <span>Tổng quan</span>
            </a>
        </div>

        <div class="adm-nav-section">
            <p class="adm-nav-section-label">Kinh doanh</p>
            <a href="<?= $adminUrl ?>?trang=donHang"
               class="adm-nav-item<?= navActive('donHang', $trangHienTai) ?>">
                <i class="fas fa-shopping-bag"></i>
                <span>Đơn hàng</span>
                <?php if ($soDonChoDuyet > 0): ?>
                    <span class="adm-nav-badge"><?= $soDonChoDuyet ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $adminUrl ?>?trang=sachVaTonKho"
               class="adm-nav-item<?= navActive('sachVaTonKho', $trangHienTai) ?>">
                <i class="fas fa-book"></i>
                <span>Sách & Tồn kho</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=nhapHang"
               class="adm-nav-item<?= navActive('nhapHang', $trangHienTai) ?>">
                <i class="fas fa-truck"></i>
                <span>Nhập hàng</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=nhaCungCap"
               class="adm-nav-item<?= navActive('nhaCungCap', $trangHienTai) ?>">
                <i class="fas fa-handshake"></i>
                <span>Nhà cung cấp</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=khuyenMai"
               class="adm-nav-item<?= navActive('khuyenMai', $trangHienTai) ?>">
                <i class="fas fa-tags"></i>
                <span>Khuyến mãi</span>
            </a>
            <a href="<?= $adminUrl ?>?trang=baoCaoDoanhThu"
               class="adm-nav-item<?= navActive('baoCaoDoanhThu', $trangHienTai) ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Báo cáo DT</span>
            </a>
        </div>

        <div class="adm-nav-section">
            <p class="adm-nav-section-label">Hệ thống</p>
            <a href="<?= $adminUrl ?>?trang=taiKhoan"
               class="adm-nav-item<?= navActive('taiKhoan', $trangHienTai) ?>">
                <i class="fas fa-users"></i>
                <span>Tài khoản</span>
            </a>
        </div>

    </nav>

    <!-- Footer sidebar -->
    <div class="adm-sidebar-footer">
        <a href="../../index.php" class="adm-nav-item" title="Xem trang bán hàng">
            <i class="fas fa-store"></i>
            <span>Trang bán hàng</span>
        </a>
        <a href="../../CuaHang/PhienDangNhap/xuly_dangxuat.php" class="adm-nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Đăng xuất</span>
        </a>
    </div>
</aside>
