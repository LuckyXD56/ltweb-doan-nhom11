
<?php
/**
 * inc/ham.php — Các hàm dùng chung
 */

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }
}

if (!function_exists('datFlash')) {
    function datFlash(
        string $noiDung,
        string $loai = 'info'
    ): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $loaiHopLe = ['success', 'error', 'warning', 'info'];

        $_SESSION['_flash'] = [
            'loai' => in_array($loai, $loaiHopLe, true)
                ? $loai
                : 'info',
            'noiDung' => $noiDung,
        ];
    }
}

if (!function_exists('layFlash')) {
    function layFlash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return null;
        }

        if (!isset($_SESSION['_flash']) || !is_array($_SESSION['_flash'])) {
            return null;
        }

        $flash = $_SESSION['_flash'];
        unset($_SESSION['_flash']);

        if (
            !isset($flash['loai'], $flash['noiDung'])
            || !is_string($flash['loai'])
            || !is_string($flash['noiDung'])
        ) {
            return null;
        }

        return $flash;
    }
}
    