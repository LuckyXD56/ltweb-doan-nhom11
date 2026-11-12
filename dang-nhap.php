<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$loi = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    kiemTraCsrf();
    $email = chuoi($_POST['email'] ?? '');
    $password = chuoi($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $loi = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } elseif (soLanSai($email) >= 5) {
        http_response_code(429);
        $loi = 'Sai quá nhiều lần, hãy thử lại sau 15 phút';
    } else {
        $user = $khoNguoiDung->timTheoEmail($email);
        
        if ($user && password_verify($password, $user['mat_khau'])) {
            // Đúng: Xóa các lần đăng nhập sai của email này
            try {
                db()->prepare('DELETE FROM dang_nhap_sai WHERE email = ?')->execute([$email]);
            } catch (Throwable) {}

            dangNhap($user['id']);
            datFlash('thanh-cong', 'Đăng nhập thành công. Xin chào ' . e($user['ho_ten']) . '!');

            // Điều hướng: Nếu là khách hàng thì về trang chủ/lịch sử, nếu là admin thì vào quản trị
            if ($user['vai_tro'] === 'quan_tri') {
                chuyenHuong('quan-tri.php');
            } else {
                chuyenHuong('index.php');
            }
        } else {
            ghiLanSai($email);
            $loi = 'Sai email hoặc mật khẩu';
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
    <div class="vung-thong-bao vung-thong-bao--loi" role="alert">
      <?= e($loi) ?>
    </div>
  <?php endif; ?>

  <form class="bieu-mau" method="post" action="dang-nhap.php">
    <?= csrfField() ?>
    <label class="truong__nhan" for="email">Email (*)</label>
    <input class="truong__nhap" type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>

    <label class="truong__nhan" for="password">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="password" name="password" required>

    <button class="nut nut--chinh" type="submit">Đăng nhập</button>
  </form>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>