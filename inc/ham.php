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
        $val = $_POST[$khoa] ?? null;
        return is_string($val) ? trim($val) : $macDinh;
    }
}

if (!function_exists('getChuoi')) {
    function getChuoi(string $khoa, string $macDinh = ''): string
    {
        $val = $_GET[$khoa] ?? null;
        return is_string($val) ? trim($val) : $macDinh;
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

if (!function_exists('chuoi')) {
    // ?q[]=ao gửi lên MẢNG: chỉ nhận chuỗi, mảng → ''
    function chuoi(mixed $v): string
    {
        return is_string($v) ? trim($v) : '';
    }
}

if (!function_exists('traJson')) {
    function traJson(array $duLieu, int $ma = 200): void
    {
        http_response_code($ma);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($duLieu, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('ghiLogBaoMat')) {
    // Một sự kiện bảo mật = một dòng trong logs/bao-mat.log
    function ghiLogBaoMat(string $suKien, array $chiTiet = []): void
    {
        $chiTiet += [
            'ip'  => $_SERVER['REMOTE_ADDR'] ?? '-',
            'uid' => $_SESSION['uid'] ?? null
        ];
        $json = json_encode($chiTiet, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $dong = date('Y-m-d H:i:s') . " $suKien $json";
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        error_log($dong . PHP_EOL, 3, $logDir . '/bao-mat.log');
    }
}