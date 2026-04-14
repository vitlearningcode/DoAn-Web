<?php
/**
 * layDuongDanAnh.php — Hàm trợ giúp phân giải đường dẫn ảnh
 *
 * Quy tắc xử lý urlAnh từ CSDL:
 *   - Nếu bắt đầu bằng http:// hay https://:
 *       → Link online, giữ nguyên và dùng trực tiếp.
 *   - Nếu bắt đầu bằng / (ví dụ /DoAn/HinhAnh/sach/s1.jpg):
 *       → Đường dẫn tuyệt đối trên server, giữ nguyên.
 *   - Nếu chuỗi trống / null:
 *       → Dùng ảnh placeholder mặc định.
 *
 * Quy ước đặt tên file ảnh local:
 *   - Sách    : DoAn/HinhAnh/sach/s1.jpg   (s + số thứ tự của maSach, ví dụ S001 → s1.jpg)
 *   - Banner  : DoAn/HinhAnh/banner/b1.jpg
 *
 * Cách dùng trong PHP:
 *   require_once '.../PhuongThuc/layDuongDanAnh.php';
 *   $src = layDuongDanAnh($sach['hinhAnh']);
 *   echo '<img src="' . $src . '">';
 */

/**
 * Phân giải urlAnh thành đường dẫn sử dụng được trong thẻ <img src="...">.
 *
 * @param string|null $urlAnh   Giá trị cột urlAnh lấy từ CSDL
 * @param string      $anhMacDinh  Ảnh hiển thị khi urlAnh trống (placeholder)
 * @return string     URL/đường dẫn đã sẵn sàng dùng trong thuộc tính src
 */
function layDuongDanAnh(?string $urlAnh, string $anhMacDinh = ''): string
{
    // Nếu urlAnh rỗng → trả về placeholder
    if (empty($urlAnh)) {
        return $anhMacDinh ?: 'https://placehold.co/300x400/eff6ff/2563eb?text=📚';
    }

    $url = trim($urlAnh);

    // Nếu là link online (http / https) → dùng trực tiếp
    if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        return htmlspecialchars($url, ENT_QUOTES);
    }

    // Sửa nhanh lỗi sai thư mục gốc: do project nằm trong DoAn-Web 
    // nhưng đường dẫn CSDL lưu là /DoAn/...
    if (str_starts_with($url, '/DoAn/') && strpos($url, '/DoAn-Web/') === false) {
        $url = '/DoAn-Web' . $url;
    }

    // Nếu là đường dẫn tĩnh hợp lệ (bắt đầu bằng /) → dùng trực tiếp
    if (str_starts_with($url, '/')) {
        return htmlspecialchars($url, ENT_QUOTES);
    }

    // Trường hợp còn lại (đường dẫn tương đối không mong muốn) → trả về placeholder
    return $anhMacDinh ?: 'https://placehold.co/300x400/eff6ff/2563eb?text=📚';
}

/**
 * Hàm rút gọn chuyên dụng cho ảnh sách.
 * Placeholder dành riêng cho sách (icon cuốn sách).
 *
 * @param string|null $urlAnh  Giá trị urlAnh từ CSDL
 * @return string              Đường dẫn ảnh sẵn sàng dùng trong src
 */
function anhSach(?string $urlAnh): string
{
    return layDuongDanAnh($urlAnh, 'https://placehold.co/300x400/eff6ff/2563eb?text=📚');
}

/**
 * Hàm rút gọn chuyên dụng cho ảnh banner quảng cáo.
 *
 * @param string|null $urlAnh  Giá trị hinhAnh từ CSDL
 * @return string              Đường dẫn ảnh sẵn sàng dùng trong src
 */
function anhBanner(?string $urlAnh): string
{
    return layDuongDanAnh($urlAnh, 'https://placehold.co/1200x450/1e3a8a/ffffff?text=Banner');
}

/**
 * Hàm tạo slug thân thiện cho tên sách (không dấu, cách bằng dấu gạch ngang)
 */
function taoSlugSach($chuoi) {
    if (!$chuoi) return 'sach';
    $chuoi = mb_strtolower($chuoi, 'UTF-8');
    $chuoi = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $chuoi);
    $chuoi = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $chuoi);
    $chuoi = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $chuoi);
    $chuoi = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $chuoi);
    $chuoi = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $chuoi);
    $chuoi = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $chuoi);
    $chuoi = preg_replace('/(đ)/', 'd', $chuoi);
    $chuoi = preg_replace('/[^a-z0-9\-]/', '-', $chuoi);
    $chuoi = preg_replace('/-+/', '-', $chuoi);
    return trim($chuoi, '-') ?: 'sach';
}
