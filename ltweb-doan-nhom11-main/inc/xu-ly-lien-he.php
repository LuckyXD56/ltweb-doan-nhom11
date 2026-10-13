<?php
/**
 * inc/xu-ly-lien-he.php — Xử lý form liên hệ
 *
 * Được include từ lien-he.php khi POST.
 * - Kiểm tra mọi ô ở máy chủ
 * - Upload 1 ảnh (không bắt buộc, tối đa 2MB)
 * - Lưu vào storage/lien-he.jsonl
 * - Redirect (PRG) + flash message
 */

declare(strict_types=1);

function xuLyLienHe(array &$du, array &$loi): bool
{
    // ---------- 1. Lấy dữ liệu POST ----------
    $du = [
        'ho_ten'   => postChuoi('ho_ten'),
        'email'    => postChuoi('email'),
        'noi_dung' => postChuoi('noi_dung'),
    ];

    // ---------- 2. Kiểm tra từng ô ----------
    if ($du['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ và tên.';
    } elseif (mb_strlen($du['ho_ten'], 'UTF-8') < 2) {
        $loi['ho_ten'] = 'Họ tên phải có ít nhất 2 ký tự.';
    }

    if ($du['email'] === '') {
        $loi['email'] = 'Vui lòng nhập email.';
    } elseif (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) {
        $loi['email'] = 'Email không hợp lệ. Ví dụ: ten@example.com';
    }

    if ($du['noi_dung'] === '') {
        $loi['noi_dung'] = 'Vui lòng nhập nội dung.';
    } elseif (mb_strlen($du['noi_dung'], 'UTF-8') < 10) {
        $loi['noi_dung'] = 'Nội dung phải có ít nhất 10 ký tự.';
    }

    // ---------- 3. Upload ảnh ----------
    $tenAnh = null;
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['anh'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $loi['anh'] = 'Lỗi upload ảnh (mã ' . $file['error'] . ').';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $loi['anh'] = 'Ảnh vượt quá 2 MB.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $mimeChoPhep = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($mime, $mimeChoPhep, true)) {
                $loi['anh'] = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WEBP.';
            } else {
                $duoi = match ($mime) {
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'image/webp' => 'webp',
                    default      => 'bin',
                };
                $tenAnh = 'lien-he-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $duoi;
                $dich = DUONG_DAN_UPLOAD . '/' . $tenAnh;

                if (!move_uploaded_file($file['tmp_name'], $dich)) {
                    $loi['anh'] = 'Không lưu được ảnh. Vui lòng thử lại.';
                    $tenAnh = null;
                }
            }
        }
    }

    // ---------- 4. Nếu có lỗi → dừng ----------
    if (!empty($loi)) {
        return false;
    }

    // ---------- 5. Lưu vào storage/lien-he.jsonl ----------
    $banGhi = [
        'thoi_gian' => date('c'),
        'ho_ten'    => $du['ho_ten'],
        'email'     => $du['email'],
        'noi_dung'  => $du['noi_dung'],
        'anh'       => $tenAnh,
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    $dong = json_encode($banGhi, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents(DUONG_DAN_STORAGE . '/lien-he.jsonl', $dong, FILE_APPEND | LOCK_EX);

    return true;
}