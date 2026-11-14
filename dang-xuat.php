<?php
/**
 * dang-xuat.php — chỉ nhận POST từ nút "Đăng xuất" (form có mã CSRF)
 */

declare(strict_types=1);

require __DIR__ . '/inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>405 Method Not Allowed</title><link rel="stylesheet" href="css/chung.css"><link rel="stylesheet" href="css/thanh-phan.css"></head><body><main class="khu-vuc-chinh trang-loi-500"><h1>405 Method Not Allowed</h1><p>Đăng xuất chỉ được thực hiện thông qua phương thức POST có mã bảo vệ CSRF.</p><p><a class="nut nut--chinh" href="index.php">Về trang chủ</a></p></main></body></html>';
    exit;
}

dangXuat();
header('Location: index.php');
exit;
