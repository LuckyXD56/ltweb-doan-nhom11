<?php
/**
 * inc/bao-ve.php — Bảo vệ trang yêu cầu đăng nhập
 *
 * Include ở đầu các trang cần đăng nhập (quan-tri.php).
 * Nếu chưa đăng nhập → chuyển hướng về dang-nhap.php
 */

declare(strict_types=1);

// Session đã khởi động trong config.php
if (empty($_SESSION['da_dang_nhap'])) {
    // Lưu URL muốn quay lại sau khi đăng nhập
    $_SESSION['quay_lai_sau_dang_nhap'] = $_SERVER['REQUEST_URI'] ?? 'quan-tri.php';
    header('Location: dang-nhap.php');
    exit;
}