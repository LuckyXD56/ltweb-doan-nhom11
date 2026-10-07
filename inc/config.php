<?php
/**
 * inc/config.php — File nạp đầu tiên cho mọi trang
 *
 * - Nạp Composer autoload
 * - Khởi tạo đối tượng $kho (KhoSanPham) và $gio (GioHang)
 * - Đặt các hằng số đường dẫn
 */

declare(strict_types=1);

use App\Data\KhoSanPham;
use App\GioHang;

// Đường dẫn gốc dự án
define('DUONG_DAN_GOC', dirname(__DIR__));
define('DUONG_DAN_DATA', DUONG_DAN_GOC . '/data');
define('DUONG_DAN_UPLOAD', DUONG_DAN_GOC . '/uploads');
define('DUONG_DAN_STORAGE', DUONG_DAN_GOC . '/storage');

// Nạp Composer autoload
require DUONG_DAN_GOC . '/vendor/autoload.php';

// Khởi động session (chỉ 1 lần)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Khởi tạo kho sân
$kho = new KhoSanPham(DUONG_DAN_DATA . '/san.json');

// Khởi tạo giỏ hàng
$gio = new GioHang($kho);