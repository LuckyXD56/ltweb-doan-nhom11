<?php
declare(strict_types=1);

function xuLyLienHe(array &$du, array &$loi): bool
{
    $du = [
        'ho_ten'   => postChuoi('ho_ten'),
        'email'    => postChuoi('email'),
        'noi_dung' => postChuoi('noi_dung'),
    ];

    if ($du['ho_ten'] === '') $loi['ho_ten'] = 'Vui lòng nhập họ và tên.';
    elseif (mb_strlen($du['ho_ten']) < 2 || mb_strlen($du['ho_ten']) > 100) $loi['ho_ten'] = 'Họ tên từ 2 đến 100 ký tự.';

    if ($du['email'] === '') $loi['email'] = 'Vui lòng nhập email.';
    elseif (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) $loi['email'] = 'Email không hợp lệ.';

    if ($du['noi_dung'] === '') $loi['noi_dung'] = 'Vui lòng nhập nội dung.';
    elseif (mb_strlen($du['noi_dung']) < 10) $loi['noi_dung'] = 'Nội dung tối thiểu 10 ký tự.';

    $tenAnh = null;
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['anh'];
        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] <= 2 * 1024 * 1024) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $mimeChoPhep = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];

            if (isset($mimeChoPhep[$mime])) {
                if (!is_dir(DUONG_DAN_UPLOAD)) {
                    @mkdir(DUONG_DAN_UPLOAD, 0755, true);
                }
                $ext = $mimeChoPhep[$mime];
                $tenAnh = 'lien-he-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], DUONG_DAN_UPLOAD . '/' . $tenAnh)) {
                    $loi['anh'] = 'Không thể lưu tệp ảnh lên máy chủ.';
                    $tenAnh = null;
                }
            } else {
                $loi['anh'] = 'Chỉ chấp nhận ảnh JPG, PNG, GIF, WEBP.';
            }
        } else {
            $loi['anh'] = 'Ảnh vượt quá 2 MB hoặc lỗi upload.';
        }
    }

    if (!empty($loi)) return false;

    // Gọi lớp KhoLienHe để lưu vào DB (kèm ảnh đính kèm nếu có)
    global $khoLienHe;
    return $khoLienHe->them($du['ho_ten'], $du['email'], $du['noi_dung'], $tenAnh);
}