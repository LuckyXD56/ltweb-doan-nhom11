<?php
/**
 * api/san.php — REST API lấy danh sách sân bóng hoặc chi tiết 1 sân
 * 
 * Phương thức: GET
 * Tham số (tùy chọn): 
 *   - id (string/int): Lấy chi tiết sân theo ID
 *   - loai_san (int): 5, 7, 11
 *   - tu_khoa (string): Tìm theo tên hoặc khu vực
 *   - sap_xep (string): gia-tang, gia-giam, ten
 */

require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/ham.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Chỉ hỗ trợ phương thức GET'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 1. Kiểm tra tham số ?id= để lấy chi tiết 1 sân
$id = getChuoi('id');
if ($id !== '') {
    $san = $kho->timTheoId($id);
    if ($san === null) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Không tìm thấy sân bóng với mã ID: ' . $id
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Lấy chi tiết sân thành công',
        'data' => [
            'id'          => $san->getId(),
            'ten'         => $san->getTen(),
            'loai_san'    => $san->getLoaiSan(),
            'khu_vuc'     => $san->getKhuVuc(),
            'gia_thuong'  => $san->getGiaThuong(),
            'gia_vang'    => $san->getGiaVang(),
            'trang_thai'  => $san->getTrangThai(),
            'danh_gia'    => $san->getDanhGia(),
            'hinh_anh'    => $san->getHinhAnh()
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 2. Nhận và xử lý tham số danh sách
$loaiSan = isset($_GET['loai_san']) ? (int)$_GET['loai_san'] : 0;
$tuKhoa  = isset($_GET['tu_khoa'])  ? getChuoi('tu_khoa')     : '';
$sapXep  = isset($_GET['sap_xep'])  ? getChuoi('sap_xep')     : 'gia-tang';

$danhSach = $kho->timKiem($tuKhoa, $loaiSan);
$danhSach = $kho->sapXep($danhSach, $sapXep);

$duLieuTraVe = [];
foreach ($danhSach as $s) {
    $duLieuTraVe[] = [
        'id'          => $s->getId(),
        'ten'         => $s->getTen(),
        'loai_san'    => $s->getLoaiSan(),
        'khu_vuc'     => $s->getKhuVuc(),
        'gia_thuong'  => $s->getGiaThuong(),
        'gia_vang'    => $s->getGiaVang(),
        'trang_thai'  => $s->getTrangThai(),
        'danh_gia'    => $s->getDanhGia(),
        'hinh_anh'    => $s->getHinhAnh()
    ];
}

echo json_encode([
    'status' => 'success',
    'message' => 'Lấy dữ liệu thành công',
    'total' => count($duLieuTraVe),
    'data' => $duLieuTraVe
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
