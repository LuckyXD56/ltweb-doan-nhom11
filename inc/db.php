<?php
/**
 * inc/db.php — Kết nối cơ sở dữ liệu bằng PDO
 */

function db(): PDO
{
    // Cấu hình DSN (Data Source Name)
    $dsn = 'mysql:host=localhost;dbname=ltweb_nhom11;charset=utf8mb4';
    $username = 'root';
    $password = ''; // Mặc định XAMPP để trống

    // Cấu hình PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Báo lỗi bằng Exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mặc định trả về mảng kết hợp
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Tắt giả lập prepare (bảo mật hơn)
    ];

    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        // Nếu kết nối thất bại, ghi log và hiển thị lỗi 500 thân thiện
        if (defined('DUONG_DAN_LOG') && is_dir(DUONG_DAN_LOG)) {
            $log = date('c') . " | LỖI KẾT NỐI DB: " . $e->getMessage() . "\n";
            file_put_contents(DUONG_DAN_LOG . '/error.log', $log, FILE_APPEND);
        }
        
        http_response_code(500);
        if (file_exists(__DIR__ . '/../500.php')) {
            require __DIR__ . '/../500.php';
        } else {
            echo "Lỗi kết nối cơ sở dữ liệu.";
        }
        exit;
    }
}