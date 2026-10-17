<?php
/**
 * 500.php — Trang lỗi hệ thống
 */
http_response_code(500);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/ham.php';

$tieuDeTrang = '500 — Lỗi hệ thống';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh" style="text-align: center; padding: 2rem 1rem;">
  <h1>500 — Lỗi máy chủ nội bộ</h1>
  <p>Hệ thống đang gặp sự cố kỹ thuật hoặc đang được bảo trì. Chúng tôi xin lỗi vì sự bất tiện này.</p>
  <p style="font-size: 3rem; margin: 1.5rem 0;">⚠️</p>
  <p style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
    <a class="nut nut--chinh" href="index.php">Về trang chủ</a>
    <a class="nut nut--phu" href="lien-he.php">Liên hệ hỗ trợ</a>
  </p>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>