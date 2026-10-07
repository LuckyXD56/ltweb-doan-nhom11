<?php
/**
 * 404.php — Trang không tìm thấy
 */
http_response_code(404);

require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$tieuDeTrang = 'Không tìm thấy trang';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh" style="text-align: center;">
  <h1>404 — Không tìm thấy trang</h1>
  <p>Trang bạn tìm không tồn tại hoặc đã bị di chuyển.</p>
  <p style="font-size: 3rem; margin: 2rem 0;">🔍</p>
  <p>
    <a class="nut nut--chinh" href="index.php">Về trang chủ</a>
    <a class="nut nut--phu" href="danh-sach.php">Xem danh sách sân</a>
  </p>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>