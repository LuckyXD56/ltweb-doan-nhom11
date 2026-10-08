<?php
/**
 * them-gio.php — Xử lý thêm/đổi/xoá giỏ hàng (POST)
 *
 * Nhận các action:
 * - action=them      : thêm sân vào giỏ
 * - action=doi       : đổi số lượng 1 mục
 * - action=xoa-mot   : xoá 1 mục
 * - action=xoa-het   : xoá toàn bộ giỏ
 *
 * Sau khi xử lý → redirect về $quay_lai (mặc định gio-hang.php)
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    chuyenHuong('gio-hang.php');
}

$action  = postChuoi('action', 'them');
$idSan   = postChuoi('id');
$quayLai = postChuoi('quay_lai', 'gio-hang.php');

// Whitelist đích quay lại để tránh open redirect
$dsQuayLai = ['gio-hang.php', 'chi-tiet.php', 'danh-sach.php'];
if (str_starts_with($quayLai, 'chi-tiet.php')) {
    // cho phép chi-tiet.php?id=...
} elseif (!in_array($quayLai, $dsQuayLai, true)) {
    $quayLai = 'gio-hang.php';
}

switch ($action) {
    case 'them':
        if ($idSan === '' || !$gio->them($idSan, max(1, (int) postChuoi('so_luong', '1')))) {
            datFlash('loi', 'Không thể thêm sân vào giỏ.');
        } else {
            datFlash('thanh-cong', 'Đã thêm sân vào giỏ hàng.');
        }
        break;

    case 'doi':
        $soLuong = (int) postChuoi('so_luong', '1');
        $gio->doiSoLuong($idSan, $soLuong);
        datFlash('thanh-cong', 'Đã cập nhật số lượng.');
        break;

    case 'xoa-mot':
        $gio->xoaMot($idSan);
        datFlash('thanh-cong', 'Đã xoá sân khỏi giỏ.');
        break;

    case 'xoa-het':
        $gio->xoaHet();
        datFlash('thanh-cong', 'Đã xoá toàn bộ giỏ hàng.');
        break;
}

chuyenHuong($quayLai);