<?php
/**
 * inc/header.php — Phần đầu trang dùng chung
 */
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
  <link rel="stylesheet"
        href="<?= e($goc ?? '') ?>css/style.css">

  <link rel="stylesheet"
        href="<?= e($goc ?? '') ?>css/thanh-phan.css">

  <?php if (!empty($cssRieng)): ?>
      <link rel="stylesheet" href="<?= e($cssRieng) ?>">
  <?php endif; ?>
</head>
<body>
  <header class="dau-trang">
    <p class="ten-he-thong"><a href="<?= e($goc) ?>index.php">Sân Bóng Thắng Lợi</a></p>
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
            'tai-khoan.php'  => 'Đăng nhập / Đăng ký',
            'lich-su.php'    => 'Lịch sử đặt sân',
            'quan-tri.php'   => 'Quản trị',
            'thanh-vien.php' => 'Thành viên',
            'lien-he.php'    => 'Liên hệ',
        ];
        foreach ($menu as $file => $nhan):
            $active = ($trangHienTai === $file) ? ' aria-current="page"' : '';
        ?>
          <li class="dieu-huong__muc">
            <a class="dieu-huong__lien-ket" href="<?= e($goc . $file) ?>"<?= $active ?>><?= e($nhan) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
      <p class="trang-thai-tai-khoan"
     style="text-align:center; padding: 0.5rem; font-size: 0.9rem;">
    <?php if (!empty($_SESSION['da_dang_nhap'])): ?>
      Xin chào <strong><?= e($_SESSION['username'] ?? '') ?></strong> ·
      <a href="<?= e($goc) ?>quan-tri.php">Quản trị</a> ·
      <a href="<?= e($goc) ?>dang-xuat.php">Đăng xuất</a>
    <?php else: ?>
      <a href="<?= e($goc) ?>dang-nhap.php">Đăng nhập quản trị</a>
    <?php endif; ?>
    </p>
  </header>

  <?php $flashHeader = layFlash(); if ($flashHeader): ?>
    <div class="vung-thong-bao vung-thong-bao--<?= e($flashHeader['loai']) ?>" role="alert"
         style="max-width: 1100px; margin: 1rem auto; padding: 0.75rem 1rem;">
      <?= e($flashHeader['noiDung']) ?>
    </div>
  <?php endif; ?>