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
        $soLuongThem = (int) postChuoi('so_luong', '1');
        
        if ($idSan === '') {
            datFlash('loi', 'Vui lòng chọn một sân hợp lệ.');
        } elseif ($gio->them($idSan, $soLuongThem)) {
            datFlash('thanh-cong', 'Đã thêm sân vào giỏ hàng.');
        } else {
            // Hiện lỗi đỏ nếu sân không tồn tại hoặc số lượng sai
            datFlash('loi', 'Không thể thêm sân. Mã sân không tồn tại hoặc số lượng vượt quá quy định (1-5).');
        }
        break;

    case 'doi':
        $rawSl = postChuoi('so_luong', '1');
        if ($idSan === '' || $kho->timTheoId($idSan) === null) {
            datFlash('loi', 'Sân không tồn tại trong hệ thống.');
        } elseif (!ctype_digit($rawSl) || (int) $rawSl < 1 || (int) $rawSl > 10) {
            datFlash('loi', 'Số lượng cập nhật không hợp lệ (phải là số nguyên từ 1 đến 10).');
        } elseif (!$gio->doiSoLuong($idSan, (int) $rawSl)) {
            datFlash('loi', 'Không thể cập nhật số lượng sân.');
        } else {
            datFlash('thanh-cong', 'Đã cập nhật số lượng thành công.');
        }
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