<?php
namespace App\Data;

use App\Models\SanPham;
use PDO;

class KhoSanPham
{
    private PDO $pdo;
    private ?array $cache = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function tatCa(): array
    {
        if ($this->cache !== null) return $this->cache;

        $sql = "SELECT s.*, d.ten_danh_muc FROM san_bong s LEFT JOIN danh_muc d ON s.danh_muc_id = d.id";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        $ketQua = [];
        foreach ($rows as $row) {
            $loaiSan = 5;
            if (str_contains($row['ten_danh_muc'] ?? '', '7')) $loaiSan = 7;
            if (str_contains($row['ten_danh_muc'] ?? '', '11')) $loaiSan = 11;

            $ketQua[] = SanPham::tuMang([
                'id'           => (string) $row['id'],
                'ten'          => $row['ten_san'],
                'loaiSan'      => $loaiSan,
                'khuVuc'       => 'Khu vực ' . ($row['ten_danh_muc'] ?? 'chung'),
                'giaThuong'    => (int) $row['gia_thue'],
                'giaVang'      => (int) $row['gia_thue'], 
                'danhGia'      => 5.0,
                'hinhAnh'      => $row['hinh_anh'] ?? '',
                'dangHoatDong' => ($row['trang_thai'] === 'hoat_dong'),
            ]);
        }
        return $this->cache = $ketQua;
    }

    public function timTheoId(string $id): ?SanPham
    {
        $idInt = (int) $id;
        $sql = "SELECT s.*, d.ten_danh_muc FROM san_bong s LEFT JOIN danh_muc d ON s.danh_muc_id = d.id WHERE s.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idInt]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $loaiSan = 5;
        if (str_contains($row['ten_danh_muc'] ?? '', '7')) $loaiSan = 7;
        if (str_contains($row['ten_danh_muc'] ?? '', '11')) $loaiSan = 11;

        return SanPham::tuMang([
            'id'           => (string) $row['id'],
            'ten'          => $row['ten_san'],
            'loaiSan'      => $loaiSan,
            'khuVuc'       => 'Khu vực ' . ($row['ten_danh_muc'] ?? 'chung'),
            'giaThuong'    => (int) $row['gia_thue'],
            'giaVang'      => (int) $row['gia_thue'],
            'danhGia'      => 5.0,
            'hinhAnh'      => $row['hinh_anh'] ?? '',
            'dangHoatDong' => ($row['trang_thai'] === 'hoat_dong'),
        ]);
    }

    public function timKiem(string $tuKhoa = '', int $loaiSan = 0): array
    {
        $sql = "SELECT s.*, d.ten_danh_muc FROM san_bong s LEFT JOIN danh_muc d ON s.danh_muc_id = d.id WHERE 1=1";
        $params = [];

        if ($loaiSan > 0) {
            $sql .= " AND d.ten_danh_muc LIKE ?";
            $params[] = '%' . $loaiSan . '%';
        }
        if (trim($tuKhoa) !== '') {
            $sql .= " AND (s.ten_san LIKE ? OR d.ten_danh_muc LIKE ?)";
            $params[] = '%' . trim($tuKhoa) . '%';
            $params[] = '%' . trim($tuKhoa) . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $ketQua = [];
        foreach ($rows as $row) {
            $loai = 5;
            if (str_contains($row['ten_danh_muc'] ?? '', '7')) $loai = 7;
            if (str_contains($row['ten_danh_muc'] ?? '', '11')) $loai = 11;

            $ketQua[] = SanPham::tuMang([
                'id'           => (string) $row['id'],
                'ten'          => $row['ten_san'],
                'loaiSan'      => $loai,
                'khuVuc'       => 'Khu vực ' . ($row['ten_danh_muc'] ?? 'chung'),
                'giaThuong'    => (int) $row['gia_thue'],
                'giaVang'      => (int) $row['gia_thue'],
                'danhGia'      => 5.0,
                'hinhAnh'      => $row['hinh_anh'] ?? '',
                'dangHoatDong' => ($row['trang_thai'] === 'hoat_dong'),
            ]);
        }
        return $ketQua;
    }

    public function sapXep(array $danhSach, string $tieuChi): array
    {
        switch ($tieuChi) {
            case 'gia-giam': usort($danhSach, fn($a, $b) => $b->getGiaThuong() <=> $a->getGiaThuong()); break;
            case 'ten': usort($danhSach, fn($a, $b) => strcmp($a->getTen(), $b->getTen())); break;
            default: usort($danhSach, fn($a, $b) => $a->getGiaThuong() <=> $b->getGiaThuong()); break;
        }
        return $danhSach;
    }
}