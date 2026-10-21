<?php
/**
 * inc/config.php — File nạp đầu tiên cho mọi trang
 *
 * - Nạp Composer autoload
 * - Khởi tạo đối tượng $kho (KhoSanPham) và $gio (GioHang)
 * - Đặt các hằng số đường dẫn
 */

// 1. Dòng này BẮT BUỘC phải nằm đầu tiên (trước mọi lệnh khác)
declare(strict_types=1);

use App\Data\KhoSanPham;
use App\GioHang;

// 2. Định nghĩa các hằng số đường dẫn
define('MOI_TRUONG', 'development'); // Thêm hằng MOI_TRUONG theo yêu cầu của thầy
define('DUONG_DAN_GOC', dirname(__DIR__));
define('DUONG_DAN_DATA', DUONG_DAN_GOC . '/data');
define('DUONG_DAN_UPLOAD', DUONG_DAN_GOC . '/uploads');
define('DUONG_DAN_STORAGE', DUONG_DAN_GOC . '/storage');
define('DUONG_DAN_LOG', DUONG_DAN_GOC . '/logs');

// 3. Nạp Composer autoload
require DUONG_DAN_GOC . '/vendor/autoload.php';

// 4. Khởi động session (chỉ 1 lần)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. Khởi tạo kho sân
$kho = new KhoSanPham(DUONG_DAN_DATA . '/san.json');

// 6. Khởi tạo giỏ hàng
$gio = new GioHang($kho);