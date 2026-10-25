<?php
/**
 * inc/tai-khoan.php — Xác thực người dùng qua Database
 */

declare(strict_types=1);

/**
 * Kiểm tra đăng nhập bằng Email.
 * @return array|null Trả về thông tin user nếu đúng, null nếu sai.
 */
function kiemTraDangNhap(string $email, string $password): ?array
{
    // 1. Tìm user trong DB bằng email
    $sql = "SELECT id, email, mat_khau, vai_tro, ho_ten FROM nguoi_dung WHERE email = ?";
    $stmt = db()->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 2. Kiểm tra mật khẩu (so sánh với cột mat_khau)
    if ($user && password_verify($password, $user['mat_khau'])) {
        return $user;
    }

    return null;
}