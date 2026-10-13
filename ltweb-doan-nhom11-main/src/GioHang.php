<?php
/**
 * GioHang — Lớp bọc $_SESSION['gio'] cho chức năng giỏ hàng
 *
 * Cấu trúc: $_SESSION['gio'] = [idSan => soLuong, ...]
 * Ví dụ: ['A1' => 2, 'B1' => 1]
 */

namespace App;

use App\Data\KhoSanPham;

class GioHang
{
    private const KHOA_SESSION = 'gio';
    private const MIN = 1;
    private const MAX = 5;

    private KhoSanPham $kho;

    public function __construct(KhoSanPham $kho)
    {
        $this->kho = $kho;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::KHOA_SESSION])) {
            $_SESSION[self::KHOA_SESSION] = [];
        }
    }

    public function duLieuTho(): array
    {
        return $_SESSION[self::KHOA_SESSION];
    }

    public function them(string $idSan, int $soLuong = 1): bool
    {
        if ($this->kho->timTheoId($idSan) === null) {
            return false;
        }

        $soLuong = max(self::MIN, min(self::MAX, $soLuong));
        $hienTai = $_SESSION[self::KHOA_SESSION][$idSan] ?? 0;
        $moi = max(self::MIN, min(self::MAX, $hienTai + $soLuong));

        $_SESSION[self::KHOA_SESSION][$idSan] = $moi;
        return true;
    }

    public function doiSoLuong(string $idSan, int $soLuong): void
    {
        if ($soLuong <= 0) {
            $this->xoaMot($idSan);
            return;
        }

        $soLuong = max(self::MIN, min(self::MAX, $soLuong));
        $_SESSION[self::KHOA_SESSION][$idSan] = $soLuong;
    }

    public function xoaMot(string $idSan): void
    {
        unset($_SESSION[self::KHOA_SESSION][$idSan]);
    }

    public function xoaHet(): void
    {
        $_SESSION[self::KHOA_SESSION] = [];
    }

    public function demSoMuc(): int
    {
        return count($_SESSION[self::KHOA_SESSION]);
    }

    public function tongSoLuong(): int
    {
        return array_sum($_SESSION[self::KHOA_SESSION]);
    }

    public function chiTiet(): array
    {
        $ketQua = [];
        foreach ($_SESSION[self::KHOA_SESSION] as $idSan => $soLuong) {
            $san = $this->kho->timTheoId((string) $idSan);
            if ($san === null) {
                continue;
            }
            $ketQua[] = [
                'san'       => $san,
                'soLuong'   => $soLuong,
                'thanhTien' => $san->getGiaThuong() * $soLuong,
            ];
        }
        return $ketQua;
    }

    public function tongTien(): int
    {
        $tong = 0;
        foreach ($this->chiTiet() as $muc) {
            $tong += $muc['thanhTien'];
        }
        return $tong;
    }
}