<?php
/**
 * inc/config.php â€” require Ä‘áº§u tiÃªn á»Ÿ má» i trang (báº£n ChÆ°Æ¡ng 7)
 */

declare(strict_types=1);

use App\Data\KhoSanPham;
use App\Data\KhoNguoiDung;
use App\Data\KhoLienHe;
use App\GioHang;

require_once __DIR__ . '/../vendor/autoload.php';

// Náº¡p biáº¿n mÃ´i trÆ°á» ng tá»« tá»‡p .env
$envDir = dirname(__DIR__);
if (file_exists($envDir . '/.env')) {
    $env = Dotenv\Dotenv::createImmutable($envDir);
    $env->load();
    $env->required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME']);
} else {
    $_ENV['APP_DEBUG'] = 'true';
    $_ENV['DB_HOST'] = 'localhost';
    $_ENV['DB_DATABASE'] = 'ltweb_nhom11';
    $_ENV['DB_USERNAME'] = 'root';
    $_ENV['DB_PASSWORD'] = '';
}

define('DEBUG', ($_ENV['APP_DEBUG'] ?? '') === 'true');   // thiáº¿u: táº¯t
define('MOI_TRUONG', DEBUG ? 'development' : 'production');
define('DUONG_DAN_GOC', dirname(__DIR__));
define('DUONG_DAN_DATA', DUONG_DAN_GOC . '/data');
define('DUONG_DAN_UPLOAD', DUONG_DAN_GOC . '/uploads');
define('DUONG_DAN_STORAGE', DUONG_DAN_GOC . '/storage');
define('DUONG_DAN_LOG', DUONG_DAN_GOC . '/logs');

error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? '1' : '0');
ini_set('log_errors', '1');
if (!is_dir(DUONG_DAN_LOG)) {
    mkdir(DUONG_DAN_LOG, 0755, true);
}
ini_set('error_log', DUONG_DAN_LOG . '/php-error.log');

require_once __DIR__ . '/ham.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/xac-thuc.php';
require_once __DIR__ . '/csrf.php';

// Header báº£o máº­t gá»­i kÃ¨m má» i trang
header("Content-Security-Policy: default-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; connect-src 'self' https://api.open-meteo.com; frame-src 'self' https://www.google.com; img-src 'self' data:;");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header_remove('X-Powered-By');
if (!empty($_SERVER['HTTPS'])) {
    header('Strict-Transport-Security: max-age=31536000');
}

// Cookie phiÃªn an toÃ n â€” Ä‘áº·t TRÆ¯á»šC session_start()
ini_set('session.use_strict_mode', '1');      // bá»  qua mÃ£ phiÃªn láº¡
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'secure'   => !empty($_SERVER['HTTPS']),  // chá»‰ gá»­i qua HTTPS
        'httponly' => true,                       // JS khÃ´ng Ä‘á» c Ä‘Æ°á»£c
        'samesite' => 'Lax',                      // háº¡n cháº¿ gá»­i tá»« site khÃ¡c
    ]);
    session_start();
}

// 30 phÃºt khÃ´ng hoáº¡t Ä‘á»™ng: bá»  phiÃªn cÅ©, cáº¥p mÃ£ má»›i â†’ Ä‘Äƒng nháº­p láº¡i
if (isset($_SESSION['hoat_dong']) && (time() - $_SESSION['hoat_dong'] > 30 * 60)) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['hoat_dong'] = time();

// Má» i request POST (form hoáº·c fetch) pháº£i mang mÃ£ CSRF Ä‘Ãºng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrfHopLe()) {
    ghiLogBaoMat('csrf_sai', ['trang' => $_SERVER['REQUEST_URI'] ?? '']);
    $loi = 'Trang Ä‘Ã£ háº¿t háº¡n, hÃ£y táº£i láº¡i trang rá»“i gá»­i láº¡i';
    if ((isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'json')) || 
        (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
        (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], 'api/'))) {
        traJson(['loi' => $loi], 403);
    }
    http_response_code(403);
    $file403 = dirname(__DIR__) . '/403.php';
    if (file_exists($file403)) {
        require $file403;
    } else {
        echo $loi;
    }
    exit;
}

set_exception_handler(function (Throwable $e) {
    error_log((string) $e);                   // chi tiáº¿t chá»‰ vÃ o log
    http_response_code(500);
    if (DEBUG) {
        echo '<pre>' . e((string) $e) . '</pre>';
    } else {
        $file500 = dirname(__DIR__) . '/500.php';
        if (file_exists($file500)) {
            require $file500;
        } else {
            echo "Xin lá»—i, Ä‘Ã£ cÃ³ lá»—i xáº£y ra.";
        }
    }
});

// Khá»Ÿi táº¡o cÃ¡c Ä‘á»‘i tÆ°á»£ng kho vÃ  giá»  hÃ ng dÃ¹ng chung
$pdo = db();
$kho = new KhoSanPham($pdo);
$gio = new GioHang($kho);
$khoNguoiDung = new KhoNguoiDung($pdo);
$khoLienHe = new KhoLienHe($pdo);
