<?php
/**
 * api/san.php — REST API lấy danh sách sân bóng
 * 
 * Phương thức: GET
 * Tham số (tùy chọn): 
 *   - loai_san (int): 5, 7, 11
 *   - tu_khoa (string): Tìm theo tên hoặc khu vực
 *   - sap_xep (string): gia-tang, gia-giam, ten
 */

// Lùi 1 cấp thư mục để nạp file cấu hình (vì file này nằm trong /api/)
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/ham.php';

// Báo cho trình duyệt biết đây là dữ liệu JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Cho phép gọi API từ mọi nơi (CORS)

// Chỉ cho phép phương thức GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Chỉ hỗ trợ phương thức GET']);
    exit;
}

// 1. Nhận và xử lý tham số
$loaiSan = isset($_GET['loai_san']) ? (int)$_GET['loai_san'] : 0;
$tuKhoa  = isset($_GET['tu_khoa'])  ? getChuoi('tu_khoa')     : '';
$sapXep  = isset($_GET['sap_xep'])  ? getChuoi('sap_xep')     : 'gia-tang';

// 2. Lấy dữ liệu từ KhoSanPham
$danhSach = $kho->timKiem($tuKhoa, $loaiSan);
$danhSach = $kho->sapXep($danhSach, $sapXep);

// 3. Chuyển đổi mảng đối tượng (Object) sang mảng (Array) để dễ dàng encode JSON
$duLieuTraVe = [];
foreach ($danhSach as $san) {
    $duLieuTraVe[] = [
        'id'          => $san->getId(),
        'ten'         => $san->getTen(),
        'loai_san'    => $san->getLoaiSan(),
        'khu_vuc'     => $san->getKhuVuc(),
        'gia_thuong'  => $san->getGiaThuong(),
        'gia_vang'    => $san->getGiaVang(),
        'danh_gia'    => $san->getDanhGia(),
        'hinh_anh'    => $san->getHinhAnh()
    ];
}

// 4. Trả về JSON
echo json_encode([
    'status' => 'success',
    'message' => 'Lấy dữ liệu thành công',
    'total' => count($duLieuTraVe),
    'data' => $duLieuTraVe
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);