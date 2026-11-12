<?php
/**
 * inc/csrf.php â€” Quáº£n lÃ½ mÃ£ chá»‘ng giáº£ máº¡o yÃªu cáº§u chÃ©o trang (CSRF Protection)
 *
 * TiÃªu chuáº©n báº£o máº­t ChÆ°Æ¡ng 7:
 * - Sinh mÃ£ ngáº«u nhiÃªn an toÃ n báº±ng random_bytes(32)
 * - Kiá»ƒm tra khá»›p mÃ£ báº±ng hash_equals() chá»‘ng táº¥n cÃ´ng Timing Attack
 * - Cung cáº¥p hÃ m sinh trÆ°á» ng áº©n HTML csrfField() vÃ  hÃ m kiá»ƒm tra csrfHopLe()
 */

declare(strict_types=1);

if (!function_exists('csrfToken')) {
    function csrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        $_SESSION['csrf_token'] = $_SESSION['csrf']; // Ä á»“ng bá»™ cáº£ 2 key
        return $_SESSION['csrf'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string
    {
        return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES | ENT_HTML5, 'UTF-8') . '">';
    }
}

if (!function_exists('csrfHopLe')) {
    function csrfHopLe(?string $token = null): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $gui = $token ?? $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($gui) || $gui === '') {
            return false;
        }
        $ssToken = $_SESSION['csrf'] ?? $_SESSION['csrf_token'] ?? '';
        if ($ssToken === '') {
            return false;
        }
        return hash_equals($ssToken, $gui);
    }
}

if (!function_exists('kiemTraCsrf')) {
    function kiemTraCsrf(): void
    {
        if (!csrfHopLe()) {
            if (function_exists('ghiLogBaoMat')) {
                ghiLogBaoMat('csrf_sai', ['trang' => $_SERVER['REQUEST_URI'] ?? '']);
            }
            http_response_code(403);
            if ((isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'json')) || 
                (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'status'  => 'error',
                    'loi'     => 'Trang đã hết hạn, hãy tải lại trang rồi gửi lại',
                    'message' => 'Lỗi bảo mật CSRF: Mã bảo vệ không hợp lệ hoặc đã hết hạn.'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $file403 = dirname(__DIR__) . '/403.php';
            if (file_exists($file403)) {
                require $file403;
            } else {
                echo 'Trang đã hết hạn, hãy tải lại trang rồi gửi lại';
            }
            exit;
        }
    }
}
