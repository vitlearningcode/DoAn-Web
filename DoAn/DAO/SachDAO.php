<?php
// DAO/SachDAO.php

// Gọi file DTO tương ứng để sử dụng
require_once __DIR__ . '/../DTO/Sach_DTO.php';

class SachDAO 
{
    private PDO $pdo;

    // Truyền kết nối Database vào DAO khi khởi tạo
    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách toàn bộ sách trong cửa hàng
     * @return Sach_DTO[]
     */
    public function getAll(): array 
    {
        // Viết câu lệnh SQL (Chỉ lấy những sách đang kinh doanh)
        $sql = "SELECT * FROM Sach WHERE trangThai = 'DangKD'";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();
        
        $danhSach = [];
        foreach ($rows as $row) {
            // Biến mảng dữ liệu thô thành Object SachDTO
            $danhSach[] = Sach_DTO::fromArray($row);
        }
        
        return $danhSach;
    }

    /**
     * Lấy chi tiết 1 cuốn sách theo mã sách
     * @param string $maSach
     * @return Sach_DTO|null
     */
    public function getById(string $maSach): ?Sach_DTO 
    {
        // Dùng prepare để chống lỗi bảo mật SQL Injection
        $sql = "SELECT * FROM Sach WHERE maSach = :maSach";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['maSach' => $maSach]);
        $row = $stmt->fetch();

        if ($row) {
            return Sach_DTO::fromArray($row);
        }
        
        return null; // Trả về null nếu không tìm thấy sách
    }
}
?>