<?php
/**
 * dang-nhap.php — Form đăng nhập quản trị (Sử dụng Database MySQL)
 */

require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';
require __DIR__ . '/inc/tai-khoan.php';

$loi = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = postChuoi('username');
    $password = postChuoi('password');

    if ($username === '' || $password === '') {
        $loi = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        // Gọi hàm kiểm tra từ file inc/tai-khoan.php (đã kết nối DB)
        $user = kiemTraDangNhap($username, $password);
        
        if ($user !== null) {
            // Đăng nhập thành công
            session_regenerate_id(true); // Bảo mật session
            $_SESSION['da_dang_nhap'] = true;
            $_SESSION['username']    = $user['username'];
            $_SESSION['vai_tro']     = $user['vai_tro']; // 'admin' hoặc 'khach'
            $_SESSION['thoi_gian_dang_nhap'] = time();

            datFlash('thanh-cong', 'Đăng nhập thành công. Xin chào ' . e($user['username']) . '!');

            // Chuyển hướng quay lại trang trước đó hoặc vào quản trị
            $quayLai = $_SESSION['quay_lai_sau_dang_nhap'] ?? 'quan-tri.php';
            unset($_SESSION['quay_lai_sau_dang_nhap']);
            chuyenHuong($quayLai);
        } else {
            // Đăng nhập sai → ghi log
            $loi = 'Sai tên đăng nhập hoặc mật khẩu.';
            $banGhi = [
                'thoi_gian' => date('c'),
                'username'  => $username,
                'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent'=> $_SERVER['HTTP_USER_AGENT'] ?? '',
            ];
            file_put_contents(
                DUONG_DAN_STORAGE . '/dang-nhap-sai.jsonl',
                json_encode($banGhi, JSON_UNESCAPED_UNICODE) . "\n",
                FILE_APPEND | LOCK_EX
            );
        }
    }
}

// Khai báo biến cho Header
$baseUrl = '';
$tieuDeTrang = 'Đăng nhập quản trị';

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Đăng nhập quản trị</h1>
  <p>Đăng nhập để truy cập trang quản trị hệ thống.</p>

  <?php if ($loi !== ''): ?>
    <div class="vung-thong-bao vung-thong-bao--loi" role="alert"
         style="padding: 0.75rem 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
      <?= e($loi) ?>
    </div>
  <?php endif; ?>

  <form class="bieu-mau" method="post" action="dang-nhap.php">
    <label class="truong__nhan" for="username">Tên đăng nhập (*)</label>
    <input class="truong__nhap" type="text" id="username" name="username"
           value="<?= e($username) ?>" required autofocus>

    <label class="truong__nhan" for="password">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="password" name="password" required>

    <button class="nut nut--chinh" type="submit">Đăng nhập</button>
  </form>

  <div style="margin-top: 2rem; padding: 1rem; background: #f0fdf4; border-left: 4px solid #0b6e3c; border-radius: 4px;">
    <p><strong>💡 Tài khoản thử:</strong></p>
    <ul style="margin: 0.5rem 0;">
      <li>Tên đăng nhập: <code>admin</code></li>
      <li>Mật khẩu: <code>Admin@123</code></li>
    </ul>
  </div>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>