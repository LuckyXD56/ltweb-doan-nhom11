<?php
/**
 * inc/ham.php — Các hàm tiện ích dùng chung
 */

declare(strict_types=1);

function e(?string $chuoi): string
{
    return htmlspecialchars((string) $chuoi, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function vnd(int|float $so): string
{
    return number_format((float) $so, 0, ',', '.') . 'đ';
}

function postChuoi(string $khoa, string $macDinh = ''): string
{
    return trim((string) ($_POST[$khoa] ?? $macDinh));
}

function getChuoi(string $khoa, string $macDinh = ''): string
{
    return trim((string) ($_GET[$khoa] ?? $macDinh));
}

function getInt(string $khoa, int $macDinh = 0): int
{
    $gia = $_GET[$khoa] ?? null;
    return is_numeric($gia) ? (int) $gia : $macDinh;
}

function chuyenHuong(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function datFlash(string $loai, string $noiDung): void
{
    $_SESSION['flash'] = ['loai' => $loai, 'noiDung' => $noiDung];
}

function layFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function danhSachDaXem(): array
{
    if (!isset($_COOKIE['da_xem'])) {
        return [];
    }
    $ds = json_decode((string) $_COOKIE['da_xem'], true);
    return is_array($ds) ? $ds : [];
}

function themDaXem(string $idSan): void
{
    $ds = danhSachDaXem();
    $ds = array_values(array_diff($ds, [$idSan]));
    array_unshift($ds, $idSan);
    $ds = array_slice($ds, 0, 5);

    setcookie('da_xem', json_encode($ds), [
        'expires'  => time() + 86400 * 7,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}