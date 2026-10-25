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
    <noscript>
      <style>@scope { .dieu-huong__danh-sach { display: flex !important; } }</style>
    </noscript>
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
    <div class="trang-thai-tai-khoan" style="text-align: center; padding: 0.5rem; font-size: 0.9rem; background: #f0fdf4; border-bottom: 1px solid #bbf7d0; margin-bottom: 1rem; color: #064e3b;">
      <?php if (!empty($_SESSION['da_dang_nhap'])): ?>
        <!-- Nếu đã đăng nhập: Hiện tên và nút Đăng xuất -->
        Xin chào, <strong><?= e($_SESSION['ho_ten'] ?? 'Người dùng') ?></strong>!
        <?php if (($_SESSION['vai_tro'] ?? '') === 'quan_tri'): ?>
          · <a href="<?= $baseUrl ?>quan-tri.php" style="font-weight: bold; color: #0b6e3c;">Trang quản trị</a>
        <?php endif; ?>
        · <a href="<?= $baseUrl ?>dang-xuat.php" style="font-weight: bold; color: #b91c1c;">Đăng xuất</a>
      <?php else: ?>
        <!-- Nếu chưa đăng nhập: Hiện link Đăng nhập / Đăng ký -->
        <a href="<?= $baseUrl ?>dang-nhap.php" style="font-weight: bold; color: #0b6e3c;">Đăng nhập</a> 
        | 
        <a href="<?= $baseUrl ?>tai-khoan.php" style="font-weight: bold; color: #0b6e3c;">Đăng ký</a>
      <?php endif; ?>
    </div>
  </header>

  <?php $flashHeader = layFlash(); if ($flashHeader): ?>
    <div class="vung-thong-bao vung-thong-bao--<?= e($flashHeader['loai']) ?>" role="alert"
         style="max-width: 1100px; margin: 1rem auto; padding: 0.75rem 1rem;">
      <?= e($flashHeader['noiDung']) ?>
    </div>
  <?php endif; ?>