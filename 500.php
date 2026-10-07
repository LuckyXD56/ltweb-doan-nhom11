<?php
/**
 * 500.php — Trang lỗi hệ thống
 */
http_response_code(500);

require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$tieuDeTrang = 'Lỗi hệ thống';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh" style="text-align: center;">
  <h1>500 — Lỗi hệ thống</h1>
  <p>Đã xảy ra lỗi. Chúng tôi xin lỗi vì sự bất tiện.</p>
  <p style="font-size: 3rem; margin: 2rem 0;">⚠️</p>
  <p>
    <a class="nut nut--chinh" href="index.php">Về trang chủ</a>
    <a class="nut nut--phu" href="lien-he.php">Liên hệ hỗ trợ</a>
  </p>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>