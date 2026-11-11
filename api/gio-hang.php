<?php
/**
 * api/gio-hang.php — REST API quản lý giỏ hàng
 * 
 * - GET: Lấy danh sách sân trong giỏ và tổng tiền
 * - POST: Thêm sân vào giỏ (action=them, id_san, so_luong) hoặc xóa (action=xoa, id_san)
 */

require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/ham.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $items = [];
    foreach ($gio->layDanhSach() as $item) {
        $san = $item['san'];
        $items[] = [
            'id_san'    => $san->getId(),
            'ten_san'   => $san->getTen(),
            'don_gia'   => $san->getGiaThuong(),
            'so_luong'  => $item['so_luong'],
            'thanh_tien'=> $item['thanh_tien'],
            'hinh_anh'  => $san->getHinhAnh()
        ];
    }

    echo json_encode([
        'status'        => 'success',
        'tong_so_luong' => $gio->tongSoLuong(),
        'tong_tien'     => $gio->tongTien(),
        'items'         => $items
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($method === 'POST') {
    $action   = postChuoi('action', 'them');
    $idSan    = postChuoi('id_san', postChuoi('id'));
    $soLuong  = max(1, (int) postChuoi('so_luong', '1'));

    if ($action === 'them') {
        if ($idSan === '' || $kho->timTheoId($idSan) === null) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Mã sân không tồn tại'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $ok = $gio->them($idSan, $soLuong);
        if ($ok) {
            echo json_encode([
                'status'        => 'success',
                'message'       => 'Đã thêm vào giỏ hàng thành công',
                'tong_so_luong' => $gio->tongSoLuong(),
                'tong_tien'     => $gio->tongTien()
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Không thể thêm vào giỏ'], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    if ($action === 'xoa') {
        $gio->xoa($idSan);
        echo json_encode([
            'status'        => 'success',
            'message'       => 'Đã xóa khỏi giỏ hàng',
            'tong_so_luong' => $gio->tongSoLuong(),
            'tong_tien'     => $gio->tongTien()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'xoa_het') {
        $gio->xoaHet();
        echo json_encode([
            'status'        => 'success',
            'message'       => 'Đã làm trống giỏ hàng',
            'tong_so_luong' => 0,
            'tong_tien'     => 0
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Action không hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
