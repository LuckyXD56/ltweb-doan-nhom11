<?php
/**
 * inc/tai-khoan.php — Tài khoản thử cho trang quản trị
 *
 * Trong đồ án, tài khoản được hard-code.
 * Khi chuyển sang PDO có thể thay bằng truy vấn bảng `nguoi_dung`.
 *
 * Tài khoản mặc định:
 *   username: admin
 *   password: Admin@123
 */

declare(strict_types=1);

/**
 * Trả về [username => password_hash] của các tài khoản.
 */
function danhSachTaiKhoan(): array
{
    // Mật khẩu được lưu sẵn dạng băm (password_hash) cho tài khoản "admin" (Admin@123)
    // Tuyệt đối không gọi password_hash() trong thời gian chạy request
    return [
        'admin' => '$2y$12$QQ9TUPLVOCSFir0ZT1SXMOllEs7Q4rm7NQlt6yP5IdVAFZYJ0A.9G',
    ];
}

/**
 * Kiểm tra đăng nhập.
 * @return bool true nếu hợp lệ
 */
function kiemTraDangNhap(string $username, string $password): bool
{
    $ds = danhSachTaiKhoan();
    if (!isset($ds[$username])) {
        return false;
    }

    return password_verify($password, $ds[$username]);
}