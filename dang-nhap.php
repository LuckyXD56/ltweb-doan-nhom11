<?php
/**
 * dang-nhap.php — Form đăng nhập quản trị (Sử dụng Email)
 */

require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';
require __DIR__ . '/inc/tai-khoan.php';

$loi = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = postChuoi('email');
    $password = postChuoi('password');

    if ($email === '' || $password === '') {
        $loi = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $user = kiemTraDangNhap($email, $password);
        
        if ($user !== null) {
            // Đăng nhập thành công
            session_regenerate_id(true);
            $_SESSION['da_dang_nhap'] = true;
            $_SESSION['nguoi_dung_id'] = $user['id']; // Lưu ID để đặt sân
            $_SESSION['email']       = $user['email'];
            $_SESSION['ho_ten']      = $user['ho_ten'];
            $_SESSION['vai_tro']     = $user['vai_tro'];

            datFlash('thanh-cong', 'Đăng nhập thành công. Xin chào ' . e($user['ho_ten']) . '!');

            $quayLai = $_SESSION['quay_lai_sau_dang_nhap'] ?? 'quan-tri.php';
            unset($_SESSION['quay_lai_sau_dang_nhap']);
            chuyenHuong($quayLai);
        } else {
            $loi = 'Sai email hoặc mật khẩu.';
            $banGhi = [
                'thoi_gian' => date('c'),
                'email'     => $email,
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
            ];
            file_put_contents(
                DUONG_DAN_STORAGE . '/dang-nhap-sai.jsonl',
                json_encode($banGhi, JSON_UNESCAPED_UNICODE) . "\n",
                FILE_APPEND | LOCK_EX
            );
        }
    }
}

$baseUrl = '';
$tieuDeTrang = 'Đăng nhập';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Đăng nhập hệ thống</h1>
  <p>Đăng nhập để truy cập trang quản trị hoặc đặt sân.</p>

  <?php if ($loi !== ''): ?>
    <div class="vung-thong-bao vung-thong-bao--loi" role="alert"
         style="padding: 0.75rem 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
      <?= e($loi) ?>
    </div>
  <?php endif; ?>

  <form class="bieu-mau" method="post" action="dang-nhap.php">
    <label class="truong__nhan" for="email">Email (*)</label>
    <input class="truong__nhap" type="email" id="email" name="email"
           value="<?= e($email) ?>" required autofocus>

    <label class="truong__nhan" for="password">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="password" name="password" required>

    <button class="nut nut--chinh" type="submit">Đăng nhập</button>
  </form>

  <div style="margin-top: 2rem; padding: 1rem; background: #f0fdf4; border-left: 4px solid #0b6e3c; border-radius: 4px;">
    <p><strong>💡 Tài khoản thử:</strong></p>
    <ul style="margin: 0.5rem 0;">
      <li>Quản trị: <code>admin@sanbong.vn</code> / <code>Admin@123</code></li>
      <li>Khách hàng: <code>khach1@gmail.com</code> / <code>Admin@123</code></li>
    </ul>
  </div>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>