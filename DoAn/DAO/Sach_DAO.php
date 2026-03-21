<?php
require_once '../config/db.php'; 

class Sach_DAO 
{
    private PDO $conn;

    public function __construct() 
    {
        global $pdo; 
        if (isset($pdo)) {
            $this->conn = $pdo;
        } else {
            die("Lỗi kết nối CSDL trong Sach_DAO.");
        }
    }

    public function getSachTheoBoLoc($mangTheLoai = [], $khoangGia = '', $sort = 'newest') 
    {
        // 1. Dùng s.* để lấy đủ dữ liệu cho DTO
        // 2. Dùng Subquery để lấy ảnh và ghép tên tác giả (tránh làm hỏng GROUP BY đếm Thể loại ở dưới)
        $sql = "SELECT s.*, 
                       (SELECT urlAnh FROM HinhAnhSach WHERE maSach = s.maSach LIMIT 1) as urlAnh,
                       (SELECT GROUP_CONCAT(tg.tenTG SEPARATOR ', ') 
                        FROM Sach_TacGia stg 
                        JOIN TacGia tg ON stg.maTG = tg.maTG 
                        WHERE stg.maSach = s.maSach) as tenTG
                FROM Sach s ";
        
        $conditions = [];
        $params = [];
        $havingClause = ""; 

        // 1. Lọc theo Thể loại (LOGIC: AND)
        if (!empty($mangTheLoai)) {
            $sql .= " JOIN Sach_TheLoai st ON s.maSach = st.maSach ";
            
            $placeholders = implode(',', array_fill(0, count($mangTheLoai), '?'));
            $conditions[] = "st.maTL IN ($placeholders)";
            
            foreach ($mangTheLoai as $maTL) {
                $params[] = $maTL;
            }

            $soLuongTheLoaiYeuCau = count($mangTheLoai);
            $havingClause = " HAVING COUNT(DISTINCT st.maTL) = $soLuongTheLoaiYeuCau ";
        }

        // 2. Lọc theo Giá
        if (!empty($khoangGia)) {
            switch ($khoangGia) {
                case 'duoi_100': $conditions[] = "s.giaBan < 100000"; break;
                case '100_500': $conditions[] = "s.giaBan >= 100000 AND s.giaBan <= 500000"; break;
                case '500_1000': $conditions[] = "s.giaBan > 500000 AND s.giaBan <= 1000000"; break;
                case 'tren_1000': $conditions[] = "s.giaBan > 1000000"; break;
            }
        }

        $conditions[] = "s.trangThai = 'DangKD'"; // Chỉ lấy sách đang bán

        // Gắn WHERE vào SQL
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Bắt buộc phải có GROUP BY cho logic lọc
        $sql .= " GROUP BY s.maSach";

        // Gắn HAVING vào sau GROUP BY
        if (!empty($havingClause)) {
            $sql .= $havingClause;
        }

        // ====== THÊM LOGIC SẮP XẾP VÀO ĐÂY ======
        switch ($sort) {
            case 'price-asc':
                $sql .= " ORDER BY s.giaBan ASC"; // Giá thấp đến cao
                break;
            case 'price-desc':
                $sql .= " ORDER BY s.giaBan DESC"; // Giá cao đến thấp
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY s.namSX DESC, s.maSach DESC"; // Mới nhất (dựa vào năm sản xuất)
                break;
        }

        // Thực thi
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>