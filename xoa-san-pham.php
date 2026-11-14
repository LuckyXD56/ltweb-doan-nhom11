<?php
/**
 * xoa-san-pham.php — Xóa sân bóng (chỉ nhận POST và yêu cầu quyền quan_tri)
 */

declare(strict_types=1);

require __DIR__ . '/inc/config.php';

// Kiểm tra phương thức: chỉ cho phép POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>405 Method Not Allowed</title><link rel="stylesheet" href="css/chung.css"><link rel="stylesheet" href="css/thanh-phan.css"></head><body><main class="khu-vuc-chinh trang-loi-500"><h1>405 Method Not Allowed</h1><p>Thao tác xóa sản phẩm / sân bóng chỉ được thực hiện qua phương thức POST.</p><p><a class="nut nut--chinh" href="quan-tri.php">Quay lại</a></p></main></body></html>';
    exit;
}

// Kiểm tra quyền quản trị (người dùng thường sẽ nhận 403 và ghi log cam_truy_cap)
yeuCauQuyen('quan_tri');

// Mã CSRF đã được kiểm tra tập trung ở inc/config.php
$idSan = (int) ($_POST['id'] ?? $_POST['id_san'] ?? 0);
if ($idSan > 0) {
    $stmt = db()->prepare('DELETE FROM san_bong WHERE id = ?');
    $stmt->execute([$idSan]);
    datFlash('thanh-cong', "Đã xóa sân bóng #$idSan thành công.");
} else {
    datFlash('loi', 'Mã sân không hợp lệ.');
}

chuyenHuong('quan-tri.php');
