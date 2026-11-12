<?php
/**
 * inc/header.php — Phần đầu trang dùng chung
 */
if (!isset($tieuDeTrang)) $tieuDeTrang = 'Sân Bóng Thắng Lợi';
if (!isset($baseUrl)) $baseUrl = '';
if (!isset($customCss)) $customCss = '';

$goc ??= '';
$tieuDeTrang ??= $tieuDe ?? 'Sân Bóng Thắng Lợi';
$trangHienTai = basename($_SERVER['PHP_SELF'] ?? '');
if ($trangHienTai === 'gioithieu.php') {
    $trangHienTai = 'thanh-vien.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= csrfToken() ?>">
  <title><?= e($tieuDeTrang) ?> | Sân Bóng Thắng Lợi</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>css/chung.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>css/thanh-phan.css">
  <?= $customCss ?> <!-- Nạp CSS riêng (style.css của bạn) -->
</head>
<body>
  <header class="dau-trang">
    <p class="ten-he-thong"><a href="<?= $baseUrl ?>index.php">Sân Bóng Thắng Lợi</a></p>
    <button type="button" class="nut-mo-menu" aria-expanded="false" aria-controls="danh-sach-dieu-huong-chinh">
      <span class="nut-mo-menu__nhan">Mở menu</span>
    </button>
    <nav class="dieu-huong" aria-label="Điều hướng chính">
      <ul class="dieu-huong__danh-sach" id="danh-sach-dieu-huong-chinh">
        <?php
        $menu = [
            'index.php'      => 'Trang chủ',
            'san-lich.php'   => 'Sân & lịch trống',
            'danh-sach.php'  => 'Danh sách sân',
            'dat-san.php'    => 'Đặt sân',
            'gio-hang.php'   => 'Giỏ hàng',
            'lich-su.php'    => 'Lịch sử đặt sân',
            'quan-tri.php'   => 'Quản trị',
            'thanh-vien.php' => 'Thành viên',
            'lien-he.php'    => 'Liên hệ',
        ];
        foreach ($menu as $file => $nhan):
            $active = ($trangHienTai === $file) ? ' aria-current="page"' : '';
        ?>
          <li class="dieu-huong__muc">
            <a class="dieu-huong__lien-ket" href="<?= $baseUrl . $file ?>"<?= $active ?>><?= e($nhan) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <!-- KHỐI HIỂN THỊ ĐĂNG NHẬP / ĐĂNG XUẤT -->
    <div class="trang-thai-tai-khoan">
      <?php if (!empty($_SESSION['da_dang_nhap'])): ?>
        <!-- Nếu đã đăng nhập: Hiện tên và nút Đăng xuất -->
        Xin chào, <strong><?= e($_SESSION['ho_ten'] ?? 'Người dùng') ?></strong>!
        <?php if (($_SESSION['vai_tro'] ?? '') === 'quan_tri'): ?>
          · <a href="<?= $baseUrl ?>quan-tri.php">Trang quản trị</a>
        <?php endif; ?>
        · <form method="post" action="<?= $baseUrl ?>dang-xuat.php" class="dang-xuat form-dang-xuat">
            <?= csrfField() ?>
            <button type="submit" class="nut-dang-xuat">Đăng xuất</button>
          </form>
      <?php else: ?>
        <!-- Nếu chưa đăng nhập: Hiện link Đăng nhập / Đăng ký -->
        <a href="<?= $baseUrl ?>dang-nhap.php">Đăng nhập</a> 
        | 
        <a href="<?= $baseUrl ?>tai-khoan.php">Đăng ký</a>
      <?php endif; ?>
    </div>
  </header>

  <?php $flashHeader = layFlash(); if ($flashHeader): ?>
    <div class="vung-thong-bao vung-thong-bao--<?= e($flashHeader['loai']) ?>" role="alert">
      <?= e($flashHeader['noiDung']) ?>
    </div>
  <?php endif; ?>