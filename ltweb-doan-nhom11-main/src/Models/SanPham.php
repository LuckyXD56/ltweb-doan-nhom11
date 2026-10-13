<?php
/**
 * SanPham — Lớp thực thể đại diện cho một sân bóng
 *
 * Chỉ chứa dữ liệu + phương thức đọc dữ liệu (readonly).
 * Không đọc file, không truy vấn DB — việc đó do KhoSanPham đảm nhiệm.
 */

namespace App\Models;

class SanPham
{
    private string $id;
    private string $ma;
    private string $ten;
    private int $loaiSan;
    private string $khuVuc;
    private int $giaThuong;
    private int $giaVang;
    private float $danhGia;
    private string $hinhAnh;
    private bool $dangHoatDong;

    public function __construct(array $duLieu)
    {
        $this->id            = (string) ($duLieu['id']            ?? '');
        $this->ma            = (string) ($duLieu['ma']            ?? $duLieu['id'] ?? '');
        $this->ten           = (string) ($duLieu['ten']           ?? '');
        $this->loaiSan       = (int)    ($duLieu['loaiSan']       ?? 0);
        $this->khuVuc        = (string) ($duLieu['khuVuc']        ?? '');
        $this->giaThuong     = (int)    ($duLieu['giaThuong']     ?? 0);
        $this->giaVang       = (int)    ($duLieu['giaVang']       ?? 0);
        $this->danhGia       = (float)  ($duLieu['danhGia']       ?? 0);
        $this->hinhAnh       = (string) ($duLieu['hinhAnh']       ?? '');
        $this->dangHoatDong  = (bool)   ($duLieu['dangHoatDong']  ?? true);
    }

    public static function tuMang(array $duLieu): self
    {
        return new self($duLieu);
    }

    public function getId(): string { return $this->id; }
    public function getMa(): string { return $this->ma; }
    public function getTen(): string { return $this->ten; }
    public function getLoaiSan(): int { return $this->loaiSan; }
    public function getKhuVuc(): string { return $this->khuVuc; }
    public function getGiaThuong(): int { return $this->giaThuong; }
    public function getGiaVang(): int { return $this->giaVang; }
    public function getDanhGia(): float { return $this->danhGia; }
    public function getHinhAnh(): string { return $this->hinhAnh; }
    public function isDangHoatDong(): bool { return $this->dangHoatDong; }

    public function toMang(): array
    {
        return [
            'id'           => $this->id,
            'ma'           => $this->ma,
            'ten'          => $this->ten,
            'loaiSan'      => $this->loaiSan,
            'khuVuc'       => $this->khuVuc,
            'giaThuong'    => $this->giaThuong,
            'giaVang'      => $this->giaVang,
            'danhGia'      => $this->danhGia,
            'hinhAnh'      => $this->hinhAnh,
            'dangHoatDong' => $this->dangHoatDong,
        ];
    }
}