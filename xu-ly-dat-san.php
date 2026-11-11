<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

use App\Data\KhoDatSan;
use App\Data\KhoSanPham;

// 1. Kiểm tra đăng nhập
if (empty($_SESSION['da_dang_nhap'])) {
    datFlash('loi', 'Bạn cần đăng nhập để đặt sân.');
    chuyenHuong('dang-nhap.php');
}

// 2. Chỉ nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    chuyenHuong('index.php');
}

// Kiểm tra CSRF nếu có hàm
if (function_exists('csrfHopLe') && !csrfHopLe($_POST['csrf'] ?? '')) {
    http_response_code(403);
    datFlash('loi', 'Mã bảo mật CSRF không hợp lệ hoặc đã hết hạn.');
    chuyenHuong('danh-sach.php');
}

// 3. Lấy dữ liệu
$sanBongId   = (int) postChuoi('ma_san'); // Ép kiểu về số nguyên
$ngayDat     = postChuoi('ngay_dat');
$gioBatDau   = postChuoi('gio_bat_dau');
$gioKetThuc  = postChuoi('gio_ket_thuc');
$nguoiDungId = (int) ($_SESSION['nguoi_dung_id'] ?? $_SESSION['uid'] ?? 1); 

// 4. Tìm sân và kiểm tra trạng thái
$san = $kho->timTheoId((string) $sanBongId);
if (!$san) {
    datFlash('loi', 'Mã sân không hợp lệ.');
    chuyenHuong('danh-sach.php');
}

// Thêm kiểm tra trạng thái hoạt động của sân
if ($san->getTrangThai() !== 'hoat_dong') {
    datFlash('loi', 'Sân hiện không ở trạng thái sẵn sàng (bảo trì hoặc hết chỗ).');
    chuyenHuong('chi-tiet.php?id=' . $sanBongId);
}

$start = strtotime($gioBatDau);
$end = strtotime($gioKetThuc);
$soGio = ($end - $start) / 3600;
if ($soGio <= 0) {
    datFlash('loi', 'Giờ kết thúc phải sau giờ bắt đầu.');
    chuyenHuong('chi-tiet.php?id=' . $sanBongId);
}

$tongTien = (int) ($san->getGiaThuong() * $soGio);

// 5. Gọi KhoDatSan xử lý với PDO (Transaction)
$khoDatSan = new KhoDatSan($pdo);
$ketQua = $khoDatSan->datSan($nguoiDungId, $sanBongId, $ngayDat, $gioBatDau, $gioKetThuc, $tongTien);

if ($ketQua === true) {
    datFlash('thanh-cong', 'Đặt sân thành công! Vui lòng chờ xác nhận.');
    chuyenHuong('lich-su.php');
} else {
    datFlash('loi', 'Lỗi đặt sân: ' . $ketQua);
    chuyenHuong('chi-tiet.php?id=' . $sanBongId);
}
