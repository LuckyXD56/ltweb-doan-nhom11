<?php
/**
 * inc/config.php — File nạp đầu tiên cho mọi trang (theo Slide 51 Chương 5)
 *
 * - Nạp Composer autoload (kèm fallback PSR-4 nếu chưa chạy composer install)
 * - Nạp hàm tiện ích inc/ham.php
 * - Cấu hình môi trường (MOI_TRUONG), báo lỗi (error_reporting), ghi log và trang 500
 * - Khởi tạo session an toàn
 * - Khởi tạo $kho (KhoSanPham) và $gio (GioHang)
 */

declare(strict_types=1);

// 1. Đường dẫn gốc dự án
define('DUONG_DAN_GOC', dirname(__DIR__));
define('DUONG_DAN_DATA', DUONG_DAN_GOC . '/data');
define('DUONG_DAN_UPLOAD', DUONG_DAN_GOC . '/uploads');
define('DUONG_DAN_STORAGE', DUONG_DAN_GOC . '/storage');
define('DUONG_DAN_LOGS', DUONG_DAN_GOC . '/logs');

// Tự động tạo các thư mục lưu trữ nếu chưa có
foreach ([DUONG_DAN_UPLOAD, DUONG_DAN_STORAGE, DUONG_DAN_LOGS] as $thuMuc) {
    if (!is_dir($thuMuc)) {
        mkdir($thuMuc, 0777, true);
    }
}

// 2. Nạp Autoload: Composer autoload kèm fallback PSR-4 cho App\ -> src/
$fileAutoload = DUONG_DAN_GOC . '/vendor/autoload.php';
if (file_exists($fileAutoload)) {
    require_once $fileAutoload;
} else {
    spl_autoload_register(function (string $lop): void {
        $prefix = 'App\\';
        $baseDir = DUONG_DAN_GOC . '/src/';
        $doDai = strlen($prefix);
        if (strncmp($prefix, $lop, $doDai) !== 0) {
            return;
        }
        $lopTuongDoi = substr($lop, $doDai);
        $tep = $baseDir . str_replace('\\', '/', $lopTuongDoi) . '.php';
        if (file_exists($tep)) {
            require $tep;
        }
    });
}

// 3. Nạp hàm tiện ích dùng chung
require_once __DIR__ . '/ham.php';

// 4. Cấu hình môi trường và xử lý lỗi (Slide 51 Chương 5)
const MOI_TRUONG = 'dev'; // máy phát triển: 'dev', máy thật: 'prod'

error_reporting(E_ALL);
ini_set('display_errors', MOI_TRUONG === 'dev' ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', DUONG_DAN_LOGS . '/php-error.log');

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);
    if (MOI_TRUONG === 'prod') {
        http_response_code(500);
        require DUONG_DAN_GOC . '/500.php';
        exit;
    }
    echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
});

// 5. Khởi động session (chỉ 1 lần)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 6. Khởi tạo kho sân bóng và giỏ hàng
use App\Data\KhoSanPham;
use App\GioHang;

$kho = new KhoSanPham(DUONG_DAN_DATA . '/san.json');
$gio = new GioHang($kho);
