<?php
/**
 * KhoSanPham — Lớp truy cập dữ liệu sân bóng (Sử dụng PDO & CSDL mới)
 * 
 * Đã cập nhật để tương thích với cấu trúc 6 bảng chuẩn hóa 3NF.
 */

namespace App\Data;

use App\Models\SanPham;
use PDO;

class KhoSanPham
{
    private ?array $cache = null;

    public function __construct() {}

    /**
     * Lấy tất cả sân (kèm theo tên danh mục).
     * @return SanPham[]
     */
    public function tatCa(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        // JOIN với bảng danh_muc để lấy tên loại sân
        $sql = "SELECT s.*, d.ten_danh_muc 
                FROM san_bong s 
                LEFT JOIN danh_muc d ON s.danh_muc_id = d.id";
        
        $stmt = db()->query($sql);
        $rows = $stmt->fetchAll();

        $ketQua = [];
        foreach ($rows as $row) {
            // Map ID danh mục sang số người tương ứng (để giao diện cũ hiển thị đẹp)
            $loaiSan = 5;
            if (str_contains($row['ten_danh_muc'] ?? '', '7')) $loaiSan = 7;
            if (str_contains($row['ten_danh_muc'] ?? '', '11')) $loaiSan = 11;

            $ketQua[] = SanPham::tuMang([
                'id'           => (string) $row['id'], // Ép kiểu về string để khớp với class SanPham cũ
                'ten'          => $row['ten_san'],
                'loaiSan'      => $loaiSan, 
                'khuVuc'       => 'Khu vực ' . ($row['ten_danh_muc'] ?? 'chung'), // Tạm map
                'giaThuong'    => (int) $row['gia_thue'],
                'giaVang'      => (int) $row['gia_thue'], 
                'danhGia'      => 5.0, // Tạm fix cứng
                'hinhAnh'      => $row['hinh_anh'] ?? 'images/san-a1.jpg',
                'dangHoatDong' => ($row['trang_thai'] === 'hoat_dong'),
            ]);
        }

        $this->cache = $ketQua;
        return $this->cache;
    }

    /**
     * Tìm 1 sân theo ID.
     */
    public function timTheoId(string $id): ?SanPham
    {
        // Ép kiểu ID về int để truy vấn an toàn
        $idInt = (int) $id;

        $sql = "SELECT s.*, d.ten_danh_muc 
                FROM san_bong s 
                LEFT JOIN danh_muc d ON s.danh_muc_id = d.id
                WHERE s.id = ?";
        
        $stmt = db()->prepare($sql);
        $stmt->execute([$idInt]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

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
            'hinhAnh'      => $row['hinh_anh'] ?? 'images/san-a1.jpg',
            'dangHoatDong' => ($row['trang_thai'] === 'hoat_dong'),
        ]);
    }

    /**
     * Tìm kiếm, lọc danh sách sân.
     * 
     * @param string $tuKhoa Từ khóa (tên sân hoặc tên danh mục)
     * @param int    $loaiSan 5, 7, 11 (0 = bỏ qua)
     * @return SanPham[]
     */
    public function timKiem(string $tuKhoa = '', int $loaiSan = 0): array
    {
        $sql = "SELECT s.*, d.ten_danh_muc 
                FROM san_bong s 
                LEFT JOIN danh_muc d ON s.danh_muc_id = d.id 
                WHERE 1=1";
        $params = [];

        if ($loaiSan > 0) {
            // Map số người (5, 7, 11) ngược lại thành tên danh mục để lọc
            $sql .= " AND d.ten_danh_muc LIKE ?";
            $params[] = '%' . $loaiSan . '%';
        }

        if (trim($tuKhoa) !== '') {
            $sql .= " AND (s.ten_san LIKE ? OR d.ten_danh_muc LIKE ?)";
            $tuKhoaLike = '%' . trim($tuKhoa) . '%';
            $params[] = $tuKhoaLike;
            $params[] = $tuKhoaLike;
        }

        $stmt = db()->prepare($sql);
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
                'hinhAnh'      => $row['hinh_anh'] ?? 'images/san-a1.jpg',
                'dangHoatDong' => ($row['trang_thai'] === 'hoat_dong'),
            ]);
        }

        return $ketQua;
    }

    /**
     * Sắp xếp danh sách sân.
     */
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