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
    // Hash của "Admin@123" — sinh bằng password_hash('Admin@123', PASSWORD_DEFAULT)
    // Đây là hash mẫu, bạn có thể sinh lại bằng password_hash() nếu muốn.
    $hashAdmin = password_hash('Admin@123', PASSWORD_DEFAULT);

    return [
        'admin' => $hashAdmin,
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