<?php
/**
 * inc/xu-ly-lien-he.php — Xử lý form liên hệ (Lưu vào MySQL)
 */

declare(strict_types=1);

function xuLyLienHe(array &$du, array &$loi): bool
{
    $du = [
        'ho_ten'   => postChuoi('ho_ten'),
        'email'    => postChuoi('email'),
        'noi_dung' => postChuoi('noi_dung'),
    ];

    if ($du['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên.';
    }
    if ($du['email'] === '') {
        $loi['email'] = 'Vui lòng nhập email.';
    } elseif (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không hợp lệ.';
    }
    if ($du['noi_dung'] === '') {
        $loi['noi_dung'] = 'Vui lòng nhập nội dung.';
    }

    // Xử lý upload ảnh (giữ nguyên logic cũ nếu có)
    $tenAnh = null;
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['anh'];
        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= 2 * 1024 * 1024) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $mimeChoPhep = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (in_array($mime, $mimeChoPhep, true)) {
                $tenAnh = 'lien-he-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . explode('/', $mime)[1];
                move_uploaded_file($file['tmp_name'], DUONG_DAN_UPLOAD . '/' . $tenAnh);
            } else {
                $loi['anh'] = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WEBP.';
            }
        } else {
            $loi['anh'] = 'Ảnh vượt quá 2 MB hoặc lỗi upload.';
        }
    }

    if (!empty($loi)) {
        return false;
    }

    // LƯU VÀO DATABASE MYSQL
    try {
        $pdo = db();
        $sql = "INSERT INTO lien_he (ho_ten, email, noi_dung) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$du['ho_ten'], $du['email'], $du['noi_dung']]);
        return true;
    } catch (Exception $e) {
        $loi['he_thong'] = 'Lỗi lưu Database: ' . $e->getMessage();
        return false;
    }
}