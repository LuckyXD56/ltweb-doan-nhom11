<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

use App\Data\KhoDatSan;

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
$maSan       = postChuoi('ma_san');
$ngayDat     = postChuoi('ngay_dat');
$gioBatDau   = postChuoi('gio_bat_dau');
$gioKetThuc  = postChuoi('gio_ket_thuc');
$nguoiDungId = (int) ($_SESSION['nguoi_dung_id'] ?? 1); // Tạm dùng 1 nếu chưa lưu ID vào session

// 4. Tính tiền (tạm tính đơn giản theo giờ)
// (Sau này bạn có thể làm logic tính tiền phức tạp hơn)
$kho = new \App\Data\KhoSanPham();
$san = $kho->timTheoId($maSan);
if (!$san) {
    datFlash('loi', 'Mã sân không hợp lệ.');
    chuyenHuong('danh-sach.php');
}

// Tính số giờ (đơn giản hóa)
$start = strtotime($gioBatDau);
$end = strtotime($gioKetThuc);
$soGio = ($end - $start) / 3600;
if ($soGio <= 0) {
    datFlash('loi', 'Giờ kết thúc phải sau giờ bắt đầu.');
    chuyenHuong('chi-tiet.php?id=' . urlencode($maSan));
}

$tongTien = $san->getGiaThuong() * $soGio;

// 5. Gọi KhoDatSan xử lý
$khoDatSan = new KhoDatSan();
$ketQua = $khoDatSan->datSan($nguoiDungId, $maSan, $ngayDat, $gioBatDau, $gioKetThuc, $tongTien);

if ($ketQua === true) {
    datFlash('thanh-cong', 'Đặt sân thành công! Vui lòng chờ xác nhận.');
    chuyenHuong('lich-su.php'); // Về trang lịch sử
} else {
    datFlash('loi', 'Lỗi đặt sân: ' . $ketQua);
    chuyenHuong('chi-tiet.php?id=' . urlencode($maSan));
}