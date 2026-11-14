<?php
/**
 * 404.php — Trang không tìm thấy
 */
http_response_code(404);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/ham.php';

$tieuDeTrang = '404 — Không tìm thấy trang';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh trang-loi-404">
  <h1>404 — Không tìm thấy trang</h1>
  <p>Trang hoặc sân bóng bạn tìm không tồn tại hoặc đã bị di chuyển.</p>
  <p class="bieu-tuong-loi">🔍</p>
  <p class="hang-nut-loi">
    <a class="nut nut--chinh" href="index.php">Về trang chủ</a>
    <a class="nut nut--phu" href="danh-sach.php">Xem danh sách sân</a>
  </p>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
