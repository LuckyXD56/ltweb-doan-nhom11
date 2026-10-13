<?php
/**
 * Tệp: thanhvien/3120224060_hoang/gioithieu.php
 * Mục đích: Trang giới thiệu cá nhân của Nguyễn Quý Minh Hoàng (Nhóm 11).
 * Chức năng server:
 *   1. Đếm số lượt xem trang (mỗi phiên session chỉ tính 1 lần, lưu ở storage/3120224060_luotxem.txt).
 *   2. Sổ lưu bút (POST-Redirect-GET, kiểm tra dữ liệu server, lưu storage/3120224060_luubut.jsonl).
 * Cách thử: Truy cập http://localhost:8000/thanhvien/3120224060_hoang/gioithieu.php
 */

session_start();

// Hàm mã hóa an toàn dữ liệu xuất ra HTML (Khai báo Type Hint tránh cảnh báo Intelephense)
if (!function_exists('e')) {
    function e(string $chuoi): string {
        return htmlspecialchars($chuoi, ENT_QUOTES, 'UTF-8');
    }
}

// Chuẩn bị thư mục lưu trữ storage
$thuMucStorage = __DIR__ . '/../../storage/';
if (!file_exists($thuMucStorage)) {
    mkdir($thuMucStorage, 0777, true);
}

// -------------------------------------------------------------------------
// CHỨC NĂNG 1: ĐẾM LƯỢT XEM TRANG (1 lần / session)
// -------------------------------------------------------------------------
$tepLuotXem = $thuMucStorage . '3120224060_luotxem.txt';
if (!file_exists($tepLuotXem)) {
    file_put_contents($tepLuotXem, '0');
}

$tongLuotXem = (int)file_get_contents($tepLuotXem);
if (empty($_SESSION['da_xem_3120224060'])) {
    $_SESSION['da_xem_3120224060'] = true;
    $tongLuotXem++;
    file_put_contents($tepLuotXem, (string)$tongLuotXem);
}

// -------------------------------------------------------------------------
// CHỨC NĂNG 2: XỬ LÝ BIỂU MẪU SỔ LƯU BÚT (Mô hình PRG - Post/Redirect/Get)
// -------------------------------------------------------------------------
$tepLuuBut = $thuMucStorage . '3120224060_luubut.jsonl';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $loiNhan = trim($_POST['loi_nhan'] ?? '');

    if ($hoTen === '' || $loiNhan === '') {
        $_SESSION['flash_loi'] = 'Vui lòng nhập đầy đủ Họ tên và Lời nhắn!';
    } elseif (mb_strlen($hoTen) > 50) {
        $_SESSION['flash_loi'] = 'Họ tên không được vượt quá 50 ký tự!';
    } elseif (mb_strlen($loiNhan) > 300) {
        $_SESSION['flash_loi'] = 'Lời nhắn không được vượt quá 300 ký tự!';
    } else {
        $data = [
            'ho_ten'    => $hoTen,
            'loi_nhan'  => $loiNhan,
            'thoi_gian' => date('d/m/Y H:i')
        ];
        file_put_contents($tepLuuBut, json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
        $_SESSION['flash_thanh_cong'] = 'Cảm ơn bạn đã gửi lời nhắn thành công!';
    }

    // Chuyển hướng theo chuẩn PRG
    header('Location: gioithieu.php');
    exit;
}

// Lấy thông báo flash
$thongBaoThanhCong = $_SESSION['flash_thanh_cong'] ?? '';
$thongBaoLoi = $_SESSION['flash_loi'] ?? '';
unset($_SESSION['flash_thanh_cong'], $_SESSION['flash_loi']);

// Đọc 5 lời nhắn mới nhất
$danhSachLuuBut = [];
if (file_exists($tepLuuBut)) {
    $cacDong = file($tepLuuBut, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($cacDong !== false) {
        $cacDong = array_reverse($cacDong);
        $top5 = array_slice($cacDong, 0, 5);
        foreach ($top5 as $dong) {
            $parsed = json_decode($dong, true);
            if (is_array($parsed)) {
                $danhSachLuuBut[] = $parsed;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giới thiệu bản thân - Nguyễn Quý Minh Hoàng</title>
  <meta name="description" content="Trang giới thiệu cá nhân của Nguyễn Quý Minh Hoàng, thành viên Nhóm 11.">
  <link rel="stylesheet" href="../../css/chung.css">
  <link rel="stylesheet" href="profile.css">
</head>
<body class="trang">

  <nav class="trang__nav">
    <ul class="menu__danh-sach">
      <li class="menu__muc"><a class="menu__lien-ket" href="../../index.php">Trang chủ</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../danh-sach.php">Danh sách sân</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../chi-tiet.php">Chi tiết sân</a></li>
      <li class="menu__muc"><a class="menu__lien-ket active" href="../../thanh-vien.php">Giới thiệu nhóm</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../lien-he.php">Liên hệ / Đặt sân</a></li>
    </ul>
  </nav>

  <main class="trang__main">

    <h1 class="tieu-de-trang">Giới thiệu thành viên Nhóm 11</h1>

    <div class="thanh-cong-cu">
      <button type="button" id="nut-doi-giao-dien" class="nut-cong-cu" aria-pressed="false">🌙 Chế độ tối</button>
      <button type="button" id="nut-sao-chep-email" class="nut-cong-cu" aria-label="Sao chép địa chỉ email">📧 Sao chép email</button>
      <span class="nut-cong-cu" style="cursor: default;">👁️ Lượt xem: <strong><?= e((string)$tongLuotXem) ?></strong></span>
      <span id="thong-bao-sao-chep" class="thong-bao-copy" role="status" aria-live="polite"></span>
    </div>

    <article class="the-ho-so">
      <div class="the-ho-so__avatar">
        <img src="../../images/hoang.jpg" alt="Ảnh chân dung của Nguyễn Quý Minh Hoàng" class="the-ho-so__anh">
      </div>
      <div class="the-ho-so__thong-tin">
        <h2 class="the-ho-so__ten">Nguyễn Quý Minh Hoàng</h2>
        <p class="the-ho-so__dong"><strong>Vai trò:</strong> Thành viên nhóm</p>
        <p class="the-ho-so__dong"><strong>Mã sinh viên:</strong> 3120224060</p>
        <p class="the-ho-so__dong">
          <strong>Email:</strong>
          <span id="email-ca-nhan">nguyenquyminhhoang@gmail.com</span>
        </p>
        <p class="the-ho-so__dong"><strong>Nhiệm vụ:</strong> Phụ trách trang tài khoản, đăng nhập và lịch sử đặt sân.</p>
      </div>
    </article>

    <div class="luoi-hai-cot">
      <article class="the-nho">
        <h3 class="the-nho__tieu-de">Giới thiệu bản thân</h3>
        <p class="the-nho__doan">
          Xin chào, tôi là Nguyễn Quý Minh Hoàng. Tôi sinh ngày 13-07-2006 và hiện đang là sinh viên Công nghệ thông tin tại Trường Đại học Sư phạm – Đại học Đà Nẵng. Tôi là người yêu thích lập trình và đặc biệt quan tâm đến phát triển web.
        </p>
      </article>

      <article class="the-nho">
        <h3 class="the-nho__tieu-de">Kỹ năng</h3>
        <div class="danh-sach-nhan">
          <span class="nhan">HTML5 &amp; CSS3</span>
          <span class="nhan">JavaScript cơ bản</span>
          <span class="nhan">Git &amp; GitHub</span>
          <span class="nhan">Responsive Design</span>
        </div>
      </article>
    </div>

    <!-- SỔ LƯU BÚT -->
    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">📝 Sổ lưu bút</h3>
      
      <?php if ($thongBaoThanhCong !== ''): ?>
        <p style="color: #0f5b38; background: #ecfdf5; padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.9rem;">
          <?= e($thongBaoThanhCong) ?>
        </p>
      <?php endif; ?>

      <?php if ($thongBaoLoi !== ''): ?>
        <p style="color: #dc2626; background: #fef2f2; padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.9rem;">
          <?= e($thongBaoLoi) ?>
        </p>
      <?php endif; ?>

      <form action="gioithieu.php" method="POST" style="margin-bottom: 1.5rem;">
        <div style="margin-bottom: 0.75rem;">
          <input type="text" name="ho_ten" placeholder="Họ và tên của bạn..." required 
                 style="width: 100%; padding: 0.6rem; border: 1px solid var(--mau-vien); border-radius: 8px; font-size: 0.9rem;">
        </div>
        <div style="margin-bottom: 0.75rem;">
          <textarea name="loi_nhan" rows="3" placeholder="Để lại lời nhắn cho Hoàng..." required 
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--mau-vien); border-radius: 8px; font-size: 0.9rem; resize: vertical;"></textarea>
        </div>
        <button type="submit" name="gui_luu_but" class="nut-cong-cu" style="background-color: var(--mau-chinh); color: #fff; border: none;">
          ✉️ Gửi lời nhắn
        </button>
      </form>

      <h4 style="margin: 1rem 0 0.5rem 0; color: var(--mau-chinh);">Lời nhắn mới nhất:</h4>
      <?php if (empty($danhSachLuuBut)): ?>
        <p style="font-size: 0.875rem; color: var(--mau-chu-phu);">Chưa có lời nhắn nào. Hãy là người đầu tiên để lại lời nhắn!</p>
      <?php else: ?>
        <ul style="list-style: none; padding: 0; margin: 0;">
          <?php foreach ($danhSachLuuBut as $item): ?>
            <li style="border-bottom: 1px solid var(--mau-vien); padding: 0.6rem 0;">
              <strong style="color: var(--mau-chu);"><?= e($item['ho_ten'] ?? '') ?></strong>
              <small style="color: var(--mau-chu-phu); float: right;"><?= e($item['thoi_gian'] ?? '') ?></small>
              <p style="margin: 0.25rem 0 0 0; font-size: 0.9rem; color: var(--mau-chu-phu);"><?= e($item['loi_nhan'] ?? '') ?></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </article>

    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">Thời khóa biểu tuần</h3>
      <div class="bang-bao">
        <table class="bang-thoi-khoa-bieu">
          <caption>Lịch học và hoạt động trong tuần của Nguyễn Quý Minh Hoàng</caption>
          <thead>
            <tr>
              <th scope="col">Ngày</th>
              <th scope="col">Hoạt động</th>
              <th scope="col">Thời gian</th>
            </tr>
          </thead>
          <tbody>
            <tr><th scope="row">Thứ Hai</th><td>Học trên lớp</td><td>07:00 – 11:00</td></tr>
            <tr><th scope="row">Thứ Ba</th><td>Học chuyên ngành</td><td>07:00 – 11:00</td></tr>
            <tr><th scope="row">Thứ Tư</th><td>Học chuyên ngành</td><td>07:00 – 11:00</td></tr>
            <tr><th scope="row">Thứ Năm</th><td>Làm dự án nhóm</td><td>13:30 – 17:00</td></tr>
            <tr><th scope="row">Thứ Sáu</th><td>Học trên lớp</td><td>07:00 – 11:00</td></tr>
            <tr><th scope="row">Thứ Bảy</th><td>Học chuyên ngành</td><td>07:00 – 11:00</td></tr>
            <tr><th scope="row">Chủ Nhật</th><td>Ôn tập và kiểm tra tiến độ</td><td>19:00 – 21:00</td></tr>
          </tbody>
        </table>
      </div>
    </article>

    <article class="the-nho the-nho--full giua">
      <button type="button" id="btn-like" class="nut-like">
        ❤️ Thích trang này (<span id="like-count">0</span>)
      </button>
    </article>

  </main>

  <footer class="trang__footer">
    <p>&copy; 2026 Nguyễn Quý Minh Hoàng – Nhóm 11, Khoa Toán Tin, ĐH Sư phạm Đà Nẵng.</p>
    <p><a href="../../thanh-vien.php">← Về danh sách thành viên</a></p>
  </footer>

  <script src="js/canhan.js"></script>
</body>
</html>