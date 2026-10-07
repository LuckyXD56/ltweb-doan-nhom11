<?php
/**
 * inc/header.php — Phần đầu trang dùng chung
 */
if (!isset($tieuDeTrang)) {
    $tieuDeTrang = 'Sân Bóng Thắng Lợi';
}

$trangHienTai = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tieuDeTrang) ?> | Sân Bóng Thắng Lợi</title>
  <link rel="stylesheet" href="css/chung.css">
  <link rel="stylesheet" href="css/thanh-phan.css">
</head>
<body>
  <header class="dau-trang">
    <p class="ten-he-thong"><a href="index.php">Sân Bóng Thắng Lợi</a></p>
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
            <a class="dieu-huong__lien-ket" href="<?= $file ?>"<?= $active ?>><?= e($nhan) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </header>

  <?php $flash = layFlash(); if ($flash): ?>
    <div class="vung-thong-bao vung-thong-bao--<?= e($flash['loai']) ?>" role="alert"
         style="max-width: 1100px; margin: 1rem auto; padding: 0.75rem 1rem;">
      <?= e($flash['noiDung']) ?>
    </div>
  <?php endif; ?>