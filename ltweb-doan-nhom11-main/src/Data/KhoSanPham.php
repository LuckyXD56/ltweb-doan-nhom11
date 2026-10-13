<?php
/**
 * KhoSanPham — Lớp truy cập dữ liệu sân bóng
 *
 * Lớp này là NƠI DUY NHẤT đọc file data/san.json.
 * Khi chuyển sang PDO (Chương 6), chỉ cần sửa các phương thức
 * trong lớp này — các lớp khác không đổi.
 *
 * Các phương thức chính:
 *   - tatCa(): array             — lấy tất cả sân
 *   - timTheoId(string): ?SanPham — tìm 1 sân theo id
 *   - timKiem(...): array        — lọc theo từ khóa, danh mục
 */

namespace App\Data;

use App\Models\SanPham;

class KhoSanPham
{
    /** Đường dẫn tuyệt đối tới file JSON dữ liệu */
    private string $duongDanFile;

    /** Cache mảng đối tượng SanPham, tránh đọc file nhiều lần */
    private ?array $cache = null;

    public function __construct(string $duongDanFile)
    {
        $this->duongDanFile = $duongDanFile;
    }

    /**
     * Đọc file JSON và trả về mảng SanPham.
     *
     * @return SanPham[]
     */
    public function tatCa(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        if (!is_file($this->duongDanFile)) {
            $this->cache = [];
            return $this->cache;
        }

        $noiDung = file_get_contents($this->duongDanFile);
        if ($noiDung === false || $noiDung === '') {
            $this->cache = [];
            return $this->cache;
        }

        $duLieu = json_decode($noiDung, true);
        if (!is_array($duLieu)) {
            $this->cache = [];
            return $this->cache;
        }

        $this->cache = array_map(
            fn(array $dong) => SanPham::tuMang($dong),
            $duLieu
        );

        return $this->cache;
    }

    /**
     * Tìm 1 sân theo id. Trả về null nếu không tìm thấy.
     */
    public function timTheoId(string $id): ?SanPham
    {
        foreach ($this->tatCa() as $san) {
            if ($san->getId() === $id) {
                return $san;
            }
        }
        return null;
    }

    /**
     * Lọc danh sách sân theo từ khóa (tên + khu vực) và loại sân.
     *
     * @param string $tuKhoa Từ khóa tìm kiếm (rỗng = bỏ qua)
     * @param int    $loaiSan 5, 7, 11 (0 = bỏ qua)
     * @return SanPham[]
     */
    public function timKiem(string $tuKhoa = '', int $loaiSan = 0): array
    {
        $tuKhoa = $this->chuanHoaKhongDau(trim($tuKhoa));

        $ketQua = array_filter(
            $this->tatCa(),
            function (SanPham $san) use ($tuKhoa, $loaiSan): bool {
                if ($loaiSan > 0 && $san->getLoaiSan() !== $loaiSan) {
                    return false;
                }

                if ($tuKhoa !== '') {
                    $ten = $this->chuanHoaKhongDau($san->getTen());
                    $khu = $this->chuanHoaKhongDau($san->getKhuVuc());
                    if (!str_contains($ten, $tuKhoa) && !str_contains($khu, $tuKhoa)) {
                        return false;
                    }
                }

                return true;
            }
        );

        return array_values($ketQua);
    }

    /**
     * Sắp xếp danh sách sân theo tiêu chí: "gia-tang", "gia-giam", "ten".
     *
     * @param SanPham[] $danhSach
     * @param string    $tieuChi
     * @return SanPham[]
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

    /**
     * Chuẩn hóa chuỗi tiếng Việt thành không dấu, chữ thường.
     * Dùng để tìm kiếm "san a1" khớp với "Sân A1".
     */
    private function chuanHoaKhongDau(string $chuoi): string
    {
        $coDau = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
            'ì','í','ị','ỉ','ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
            'ỳ','ý','ỵ','ỷ','ỹ',
            'đ',
        ];
        $khongDau = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
            'e','e','e','e','e','e','e','e','e','e','e',
            'i','i','i','i','i',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
            'u','u','u','u','u','u','u','u','u','u','u',
            'y','y','y','y','y',
            'd',
        ];

        $chuoi = mb_strtolower($chuoi, 'UTF-8');
        $chuoi = str_replace($coDau, $khongDau, $chuoi);

        return $chuoi;
    }
}