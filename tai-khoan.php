<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$baseUrl = '';
$tieuDeTrang = 'Đăng nhập / Đăng ký';

$loiDangNhap = '';
$loiDangKy = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    kiemTraCsrf();
    $action = postChuoi('action');

    // 1. Xử lý ĐĂNG NHẬP (Gom về logic xác thực và đếm lần sai duy nhất)
    if ($action === 'dang_nhap') {
        $email = postChuoi('tai_khoan');
        $matKhau = postChuoi('mat_khau');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        if ($email === '' || $matKhau === '') {
            $loiDangNhap = 'Vui lòng nhập đầy đủ tài khoản/email và mật khẩu.';
        } elseif (soLanSai($email) >= 5 || ($ip !== '' && function_exists('soLanSaiIp') && soLanSaiIp($ip) >= 10)) {
            http_response_code(429);
            $loiDangNhap = 'Sai quá nhiều lần, hãy thử lại sau 15 phút';
        } else {
            $user = $khoNguoiDung->timTheoEmail($email);
            if ($user && password_verify($matKhau, $user['mat_khau'])) {
                // Xóa các lần đăng nhập sai của email này
                try {
                    db()->prepare('DELETE FROM dang_nhap_sai WHERE email = ?')->execute([$email]);
                } catch (Throwable) {}

                dangNhap((int) $user['id']);

                datFlash('thanh-cong', 'Đăng nhập thành công. Xin chào ' . e($user['ho_ten']) . '!');
                if ($user['vai_tro'] === 'quan_tri') {
                    chuyenHuong('quan-tri.php');
                } else {
                    chuyenHuong('index.php');
                }
            } else {
                ghiLanSai($email);
                if (soLanSai($email) >= 5 || ($ip !== '' && function_exists('soLanSaiIp') && soLanSaiIp($ip) >= 10)) {
                    http_response_code(429);
                    $loiDangNhap = 'Sai quá nhiều lần, hãy thử lại sau 15 phút';
                } else {
                    $loiDangNhap = 'Sai thông tin đăng nhập hoặc mật khẩu.';
                }
            }
        }
    }

    // 2. Xử lý ĐĂNG KÝ
    elseif ($action === 'dang_ky') {
        $hoTen = postChuoi('ho_ten');
        $email = postChuoi('email');
        $sdt   = postChuoi('so_dien_thoai');
        $mk    = postChuoi('mat_khau');
        $xnMk  = postChuoi('xac_nhan_mat_khau');

        if ($hoTen === '' || $email === '' || $mk === '' || $xnMk === '') {
            $loiDangKy = 'Vui lòng điền đầy đủ các trường bắt buộc.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loiDangKy = 'Địa chỉ email không đúng định dạng.';
        } elseif (mb_strlen($mk) < 6) {
            $loiDangKy = 'Mật khẩu phải có độ dài ít nhất 6 ký tự.';
        } elseif ($mk !== $xnMk) {
            $loiDangKy = 'Mật khẩu xác nhận không khớp.';
        } else {
            // Kiểm tra email đã tồn tại chưa
            $daCo = $khoNguoiDung->timTheoEmail($email);
            if ($daCo) {
                $loiDangKy = 'Email này đã được sử dụng. Vui lòng chọn email khác hoặc đăng nhập.';
            } else {
                try {
                    $hash = password_hash($mk, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO nguoi_dung (vai_tro, ho_ten, email, mat_khau) VALUES ('khach_hang', ?, ?, ?)");
                    $stmt->execute([$hoTen, $email, $hash]);
                    $newId = (int)$pdo->lastInsertId();

                    // Tự động đăng nhập phiên cho tài khoản mới qua hàm chuẩn
                    dangNhap((int) $newId);

                    datFlash('thanh-cong', 'Đăng ký tài khoản thành công! Chào mừng ' . e($hoTen) . ' đến với Sân Bóng Thắng Lợi.');
                    chuyenHuong('index.php');
                } catch (Exception $e) {
                    $loiDangKy = 'Lỗi hệ thống khi đăng ký: ' . $e->getMessage();
                }
            }
        }
    }
}

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Đăng nhập hoặc tạo tài khoản</h1>
  <p>Tài khoản giúp bạn lưu lịch sử đặt sân, nhận thông báo lịch trống và tích điểm đổi ưu đãi.</p>

  <h2>Đăng nhập</h2>
  <?php if ($loiDangNhap !== ''): ?>
    <div class="vung-thong-bao vung-thong-bao--loi" role="alert">
      <?= e($loiDangNhap) ?>
    </div>
  <?php endif; ?>

  <form class="bieu-mau" id="form-dang-nhap" action="tai-khoan.php" method="post">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="dang_nhap">
    <label class="truong__nhan" for="dn-email">Email (*)</label>
    <input class="truong__nhap" type="email" id="dn-email" name="tai_khoan" required>

    <label class="truong__nhan" for="dn-mat-khau">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="dn-mat-khau" name="mat_khau" required minlength="6">

    <button class="nut nut--chinh" type="submit">Đăng nhập</button>
  </form>

  <h2>Đăng ký tài khoản mới</h2>
  <?php if ($loiDangKy !== ''): ?>
    <div class="vung-thong-bao vung-thong-bao--loi" role="alert">
      <?= e($loiDangKy) ?>
    </div>
  <?php endif; ?>

  <form class="bieu-mau" id="form-dang-ky" action="tai-khoan.php" method="post">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="dang_ky">
    <label class="truong__nhan" for="dk-ho-ten">Họ và tên (*)</label>
    <input class="truong__nhap" type="text" id="dk-ho-ten" name="ho_ten" required minlength="2">

    <label class="truong__nhan" for="dk-email">Email (*)</label>
    <input class="truong__nhap" type="email" id="dk-email" name="email" required>

    <label class="truong__nhan" for="dk-sdt">Số điện thoại</label>
    <input class="truong__nhap" type="tel" id="dk-sdt" name="so_dien_thoai" pattern="0[0-9]{9}"
           title="Nhập số điện thoại Việt Nam gồm 10 chữ số, bắt đầu bằng 0">

    <label class="truong__nhan" for="dk-mat-khau">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="dk-mat-khau" name="mat_khau" required minlength="6">

    <label class="truong__nhan" for="dk-xac-nhan">Xác nhận mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="dk-xac-nhan" name="xac_nhan_mat_khau" required minlength="6">

    <p>
      <input class="truong__chon" type="checkbox" id="dk-dong-y" name="dong_y_dieu_khoan" required>
      <label class="truong__nhan" for="dk-dong-y">Tôi đồng ý với điều khoản sử dụng dịch vụ (*)</label>
    </p>

    <button class="nut nut--chinh" type="submit">Tạo tài khoản</button>
  </form>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
