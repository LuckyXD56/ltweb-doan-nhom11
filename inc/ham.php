<?php
/**
 * inc/ham.php — Các hàm tiện ích dùng chung
 */

declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $chuoi): string
    {
        return htmlspecialchars((string) $chuoi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('vnd')) {
    function vnd(int|float $so): string
    {
        return number_format((float) $so, 0, ',', '.') . 'đ';
    }
}

if (!function_exists('postChuoi')) {
    function postChuoi(string $khoa, string $macDinh = ''): string
    {
        return trim((string) ($_POST[$khoa] ?? $macDinh));
    }
}

if (!function_exists('getChuoi')) {
    function getChuoi(string $khoa, string $macDinh = ''): string
    {
        return trim((string) ($_GET[$khoa] ?? $macDinh));
    }
}

if (!function_exists('getInt')) {
    function getInt(string $khoa, int $macDinh = 0): int
    {
        $gia = $_GET[$khoa] ?? null;
        return is_numeric($gia) ? (int) $gia : $macDinh;
    }
}

if (!function_exists('chuyenHuong')) {
    function chuyenHuong(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('datFlash')) {
    function datFlash(string $loai, string $noiDung): void
    {
        $_SESSION['flash'] = ['loai' => $loai, 'noiDung' => $noiDung];
    }
}

if (!function_exists('layFlash')) {
    function layFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
}

if (!function_exists('danhSachDaXem')) {
    function danhSachDaXem(): array
    {
        if (!isset($_COOKIE['da_xem'])) {
            return [];
        }
        $raw = (string) $_COOKIE['da_xem'];
        $ds = json_decode($raw, true);
        if (is_array($ds)) {
            return $ds;
        }
        // Hỗ trợ cả định dạng phân cách bởi dấu phẩy
        return array_filter(array_map('trim', explode(',', $raw)));
    }
}

if (!function_exists('themDaXem')) {
    function themDaXem(string $idSan): void
    {
        $ds = danhSachDaXem();
        $ds = array_values(array_diff($ds, [$idSan]));
        array_unshift($ds, $idSan);
        $ds = array_slice($ds, 0, 4);

        setcookie('da_xem', json_encode($ds), [
            'expires'  => time() + 86400 * 30, // 30 ngày theo yêu cầu
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}