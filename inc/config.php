<?php
/**
 * inc/config.php — File nạp đầu tiên cho mọi trang
 */
declare(strict_types=1);

use App\Data\KhoSanPham;
use App\Data\KhoNguoiDung;
use App\Data\KhoLienHe;
use App\GioHang;

define('MOI_TRUONG', 'development');
define('DUONG_DAN_GOC', dirname(__DIR__));
define('DUONG_DAN_DATA', DUONG_DAN_GOC . '/data');
define('DUONG_DAN_UPLOAD', DUONG_DAN_GOC . '/uploads');
define('DUONG_DAN_STORAGE', DUONG_DAN_GOC . '/storage');
define('DUONG_DAN_LOG', DUONG_DAN_GOC . '/logs');

require DUONG_DAN_GOC . '/vendor/autoload.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Khởi tạo kết nối PDO duy nhất
$pdo = db();

// 2. Truyền PDO vào các lớp (Dependency Injection)
$kho = new KhoSanPham($pdo);
$gio = new GioHang($kho);
$khoNguoiDung = new KhoNguoiDung($pdo);
$khoLienHe = new KhoLienHe($pdo);