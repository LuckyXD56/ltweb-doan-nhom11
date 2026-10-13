<?php
/**
 * dang-xuat.php — Đăng xuất
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

// Xoá toàn bộ session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Khởi động session mới để hiện flash
session_start();
datFlash('thanh-cong', 'Đã đăng xuất.');

chuyenHuong('index.php');