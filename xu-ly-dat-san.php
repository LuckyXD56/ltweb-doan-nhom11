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

// 3. Lấy dữ liệu
$sanBongId   = (int) postChuoi('ma_san'); // Ép kiểu về số nguyên
$ngayDat     = postChuoi('ngay_dat');
$gioBatDau   = postChuoi('gio_bat_dau');
$gioKetThuc  = postChuoi('gio_ket_thuc');
$nguoiDungId = (int) ($_SESSION['nguoi_dung_id'] ?? 1); 

// 4. Tính tiền
$kho = new KhoSanPham();
$san = $kho->timTheoId((string) $sanBongId);
if (!$san) {
    datFlash('loi', 'Mã sân không hợp lệ.');
    chuyenHuong('danh-sach.php');
}

$start = strtotime($gioBatDau);
$end = strtotime($gioKetThuc);
$soGio = ($end - $start) / 3600;
if ($soGio <= 0) {
    datFlash('loi', 'Giờ kết thúc phải sau giờ bắt đầu.');
    chuyenHuong('chi-tiet.php?id=' . $sanBongId);
}

$tongTien = $san->getGiaThuong() * $soGio;

// 5. Gọi KhoDatSan xử lý (Transaction)
$khoDatSan = new KhoDatSan();
$ketQua = $khoDatSan->datSan($nguoiDungId, $sanBongId, $ngayDat, $gioBatDau, $gioKetThuc, $tongTien);

if ($ketQua === true) {
    datFlash('thanh-cong', 'Đặt sân thành công! Vui lòng chờ xác nhận.');
    chuyenHuong('lich-su.php');
} else {
    datFlash('loi', 'Lỗi đặt sân: ' . $ketQua);
    chuyenHuong('chi-tiet.php?id=' . $sanBongId);
}