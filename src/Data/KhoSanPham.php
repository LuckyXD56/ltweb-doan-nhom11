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
     * Có bẫy lỗi 500 nếu file không tồn tại hoặc hỏng.
     *
     * @return SanPham[]
     */
    public function tatCa(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        // Bẫy 1: File không tồn tại
        if (!is_file($this->duongDanFile)) {
            $this->ghiLogVaBaoLoi500("Mất file dữ liệu: " . $this->duongDanFile);
        }

        $noiDung = file_get_contents($this->duongDanFile);
        
        // Bẫy 2: File rỗng hoặc không có quyền đọc
        if ($noiDung === false || $noiDung === '') {
            $this->ghiLogVaBaoLoi500("Tệp dữ liệu trống hoặc không đọc được: " . $this->duongDanFile);
        }

        $duLieu = json_decode($noiDung, true);
        
        // Bẫy 3: Dữ liệu JSON bị lỗi định dạng
        if (!is_array($duLieu)) {
            $this->ghiLogVaBaoLoi500("Dữ liệu JSON không hợp lệ trong tệp: " . $this->duongDanFile);
        }

        $this->cache = array_map(
            fn(array $dong) => SanPham::tuMang($dong),
            $duLieu
        );

        return $this->cache;
    }

    /**
     * Hàm hỗ trợ: Ghi log lỗi vào file và hiển thị trang 500 thân thiện.
     */
    private function ghiLogVaBaoLoi500(string $thongDiep): void
    {
        // 1. Ghi log
        $logDir = defined('DUONG_DAN_LOG') ? DUONG_DAN_LOG : dirname(__DIR__, 2) . '/logs';
        if (is_dir($logDir)) {
            $logMsg = date('c') . " | LỖI KHO SẢN PHẨM: " . $thongDiep . "\n";
            file_put_contents($logDir . '/error.log', $logMsg, FILE_APPEND | LOCK_EX);
        }

        // 2. Trả về mã 500 và hiển thị giao diện lỗi
        http_response_code(500);
        
        $path500 = defined('DUONG_DAN_GOC') ? DUONG_DAN_GOC . '/500.php' : dirname(__DIR__, 2) . '/500.php';
        if (file_exists($path500)) {
            require $path500;
        } else {
            // Fallback nếu không tìm thấy file 500.php
            echo "<h1>500 - Lỗi hệ thống</h1><p>Đã xảy ra lỗi máy chủ. Vui lòng thử lại sau.</p>";
        }
        
        exit; // Dừng toàn bộ chương trình ngay lập tức
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