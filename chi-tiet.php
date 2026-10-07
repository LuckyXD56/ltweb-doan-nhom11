<?php
/**
 * chi-tiet.php — Chi tiết sân (đọc ?id=)
 */
$tieuDeTrang = 'Chi tiết sân';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1 id="chi-tiet-tieu-de">Chi tiết sân bóng</h1>
  <div id="thong-tin-san">
    <p class="chu-canh-giua">Đang tải dữ liệu...</p>
  </div>
  <p class="chu-canh-giua mt-2">
    <a class="nut nut--phu" href="danh-sach.php">← Về danh sách sân</a>
  </p>
</main>

<?php
$scriptRieng = '<script type="module" src="js/trang-chi-tiet.js"></script>';
require __DIR__ . '/inc/footer.php';
?>