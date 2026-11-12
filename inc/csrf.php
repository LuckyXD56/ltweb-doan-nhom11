<?php
/**
 * inc/csrf.php — Quản lý mã chống giả mạo yêu cầu chéo trang (CSRF Protection)
 *
 * Tiêu chuẩn bảo mật Chương 7:
 * - Sinh mã ngẫu nhiên an toàn bằng random_bytes(32)
 * - Kiểm tra khớp mã bằng hash_equals() chống tấn công Timing Attack
 * - Cung cấp hàm sinh trường ẩn HTML csrfField() và hàm tự động kiểm tra kiemTraCsrf()
 */

declare(strict_types=1);

if (!function_exists('csrfToken')) {
    function csrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string
    {
        return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES | ENT_HTML5, 'UTF-8') . '">';
    }
}

if (!function_exists('csrfHopLe')) {
    function csrfHopLe(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('kiemTraCsrf')) {
    function kiemTraCsrf(): void
    {
        $token = is_string($_POST['csrf'] ?? null) ? $_POST['csrf'] : '';
        if ($token === '' && !empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = (string) $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        if (!csrfHopLe($token)) {
            http_response_code(403);
            if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Lỗi bảo mật CSRF: Mã bảo vệ không hợp lệ hoặc đã hết hạn.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $file403 = dirname(__DIR__) . '/403.php';
            if (file_exists($file403)) {
                require $file403;
            } else {
                echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1><p>Yêu cầu bị từ chối: Mã bảo vệ CSRF không hợp lệ hoặc đã hết hạn.</p><p><a href="index.php">Quay lại trang chủ</a></p></body></html>';
            }
            exit;
        }
    }
}
