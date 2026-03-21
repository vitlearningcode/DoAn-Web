<?php

readonly class Sach_DTO 
{
    public function __construct(
        public string $maSach, 
        public string $tenSach, 
        public int $maNXB, 
        public ?int $namSX,
        public string $loaiBia, 
        public float $giaBan, 
        public int $soLuongTon,
        public ?string $moTa, 
        public string $trangThai,
        // THÊM 2 TRƯỜNG NÀY (Cho phép null)
        public ?string $urlAnh = null,
        public ?string $tenTG = null
    ) {}

    public static function fromArray(array $data): self 
    {
        return new self(
            maSach: (string) $data['maSach'], 
            tenSach: (string) $data['tenSach'], 
            // Thêm isset cho maNXB để an toàn nếu DAO quên select
            maNXB: isset($data['maNXB']) ? (int) $data['maNXB'] : 0,
            namSX: isset($data['namSX']) ? (int) $data['namSX'] : null, 
            loaiBia: $data['loaiBia'] ?? 'Bìa Mềm',
            giaBan: (float) $data['giaBan'], 
            soLuongTon: isset($data['soLuongTon']) ? (int) $data['soLuongTon'] : 0,
            moTa: $data['moTa'] ?? null, 
            trangThai: $data['trangThai'] ?? 'DangKD',
            // MAP THÊM 2 DỮ LIỆU NÀY TỪ CSDL VÀO DTO:
            urlAnh: $data['urlAnh'] ?? null,
            tenTG: $data['tenTG'] ?? null
        );
    }
}
?>