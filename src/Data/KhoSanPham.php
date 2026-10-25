<?php
/**
 * KhoSanPham — Lớp truy cập dữ liệu sân bóng (Đã chuyển sang PDO)
 */

namespace App\Data;

use App\Models\SanPham;
use PDO;

class KhoSanPham
{
    private ?array $cache = null;

    public function __construct() {}

    public function tatCa(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $sql = "SELECT * FROM san_bong WHERE dang_hoat_dong = 1";
        $stmt = db()->query($sql);
        $rows = $stmt->fetchAll();

        $ketQua = [];
        foreach ($rows as $row) {
            $ketQua[] = SanPham::tuMang([
                'id'           => $row['ma_san'],
                'ten'          => $row['ten_san'],
                'loaiSan'      => (int) $row['loai_san'],
                'khuVuc'       => $row['khu_vuc'],
                'giaThuong'    => (int) $row['gia_thuong'],
                'giaVang'      => (int) $row['gia_vang'],
                'danhGia'      => (float) $row['danh_gia'],
                'hinhAnh'      => $row['hinh_anh'],
                'dangHoatDong' => (bool) $row['dang_hoat_dong'],
            ]);
        }

        $this->cache = $ketQua;
        return $this->cache;
    }

    public function timTheoId(string $id): ?SanPham
    {
        foreach ($this->tatCa() as $san) {
            if ($san->getId() === $id) {
                return $san;
            }
        }
        return null;
    }

    public function timKiem(string $tuKhoa = '', int $loaiSan = 0): array
    {
        $sql = "SELECT * FROM san_bong WHERE dang_hoat_dong = 1";
        $params = [];

        if ($loaiSan > 0) {
            $sql .= " AND loai_san = ?";
            $params[] = $loaiSan;
        }

        if (trim($tuKhoa) !== '') {
            $sql .= " AND (ten_san LIKE ? OR khu_vuc LIKE ?)";
            $tuKhoaLike = '%' . trim($tuKhoa) . '%';
            $params[] = $tuKhoaLike;
            $params[] = $tuKhoaLike;
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $ketQua = [];
        foreach ($rows as $row) {
            $ketQua[] = SanPham::tuMang([
                'id'           => $row['ma_san'],
                'ten'          => $row['ten_san'],
                'loaiSan'      => (int) $row['loai_san'],
                'khuVuc'       => $row['khu_vuc'],
                'giaThuong'    => (int) $row['gia_thuong'],
                'giaVang'      => (int) $row['gia_vang'],
                'danhGia'      => (float) $row['danh_gia'],
                'hinhAnh'      => $row['hinh_anh'],
                'dangHoatDong' => (bool) $row['dang_hoat_dong'],
            ]);
        }

        return $ketQua;
    }

    public function sapXep(array $danhSach, string $tieuChi): array
    {
        switch ($tieuChi) {
            case 'gia-giam':
                usort($danhSach, fn($a, $b) => $b->getGiaThuong() <=> $a->getGiaThuong());
                break;
            case 'ten':
                usort($danhSach, fn($a, $b) => strcmp($a->getTen(), $b->getTen()));
                break;
            case 'gia-tang':
            default:
                usort($danhSach, fn($a, $b) => $a->getGiaThuong() <=> $b->getGiaThuong());
                break;
        }

        return $danhSach;
    }
}