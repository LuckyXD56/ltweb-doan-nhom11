<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

// Nếu người dùng đang có session cũ, nhưng lại truy cập vào trang đăng nhập (phương thức GET)
// -> Tự động hủy session cũ để cho phép đăng nhập tài khoản mới tinh.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!empty($_SESSION['da_dang_nhap'])) {
        $_SESSION = [];
        session_destroy();
        session_start();
        datFlash('thanh-cong', 'Bạn đã đăng xuất khỏi tài khoản cũ. Vui lòng đăng nhập lại.');
    }
}

$loi = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = postChuoi('email');
    $password = postChuoi('password');

    if ($email === '' || $password === '') {
        $loi = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $user = $khoNguoiDung->timTheoEmail($email);
        
        if ($user && password_verify($password, $user['mat_khau'])) {
            // Xóa sạch session cũ trước khi ghi session mới (tránh lẫn lộn quyền)
            $_SESSION = [];
            session_regenerate_id(true); 
            
            $_SESSION['da_dang_nhap'] = true;
            $_SESSION['nguoi_dung_id'] = $user['id'];
            $_SESSION['email']       = $user['email'];
            $_SESSION['ho_ten']      = $user['ho_ten'];
            $_SESSION['vai_tro']     = $user['vai_tro'];

            datFlash('thanh-cong', 'Đăng nhập thành công. Xin chào ' . e($user['ho_ten']) . '!');

            // Điều hướng: Nếu là khách hàng thì về trang chủ/lịch sử, nếu là admin thì vào quản trị
            if ($user['vai_tro'] === 'quan_tri') {
                chuyenHuong('quan-tri.php');
            } else {
                chuyenHuong('index.php');
            }
        } else {
            $loi = 'Sai email hoặc mật khẩu.';
        }
    }
}

$baseUrl = '';
$tieuDeTrang = 'Đăng nhập';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Đăng nhập hệ thống</h1>
  <p>Đăng nhập để đặt sân hoặc truy cập trang quản trị.</p>

  <?php if ($loi !== ''): ?>
    <div class="vung-thong-bao vung-thong-bao--loi" role="alert" style="padding: 0.75rem 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 1rem;">
      <?= e($loi) ?>
    </div>
  <?php endif; ?>

  <form class="bieu-mau" method="post" action="dang-nhap.php">
    <label class="truong__nhan" for="email">Email (*)</label>
    <input class="truong__nhap" type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>

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