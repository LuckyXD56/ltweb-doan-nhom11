<?php
/**
 * inc/tai-khoan.php — Xác thực người dùng qua Database
 */

declare(strict_types=1);

/**
 * Kiểm tra đăng nhập.
 * @return array|null Trả về thông tin user (id, username, vai_tro) nếu đúng, null nếu sai.
 */
function kiemTraDangNhap(string $username, string $password): ?array
{
    // 1. Tìm user trong DB
    $sql = "SELECT id, username, password, vai_tro FROM nguoi_dung WHERE username = ?";
    $stmt = db()->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. Kiểm tra mật khẩu
    if ($user && password_verify($password, $user['password'])) {
        // Đăng nhập thành công
        return $user;
    }

    // Đăng nhập thất bại
    return null;
}