<?php
declare(strict_types=1);

/**
 * inc/config.php — File nạp đầu tiên cho mọi trang
 */

use App\Data\KhoSanPham;
use App\GioHang;

// Đường dẫn gốc dự án
if (!defined('DUONG_DAN_GOC')) {
    define('DUONG_DAN_GOC', dirname(__DIR__));
    define('DUONG_DAN_DATA', DUONG_DAN_GOC . '/data');
    define('DUONG_DAN_UPLOAD', DUONG_DAN_GOC . '/uploads');
    define('DUONG_DAN_STORAGE', DUONG_DAN_GOC . '/storage');
}

// Nạp Composer autoload
require_once DUONG_DAN_GOC . '/vendor/autoload.php';

// Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp hàm dùng chung
require_once __DIR__ . '/ham.php';

// Khởi tạo kho sân
$kho = new KhoSanPham(DUONG_DAN_DATA . '/san.json');

// Khởi tạo giỏ hàng
$gio = new GioHang($kho);
