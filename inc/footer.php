<?php
/**
 * inc/footer.php — Phần chân trang dùng chung
 *
 * Biến $scriptRieng (tùy chọn) — HTML script riêng của từng trang.
 */
?>
  <footer class="chan-trang">
    <p class="chan-trang__dong">Sân Bóng Thắng Lợi – 123 Đường Ngô Quyền, Quận Sơn Trà, Đà Nẵng</p>
    <p class="chan-trang__dong">Điện thoại: 0905 123 456 · Email: lienhe@sanbongthangloi.vn</p>
    <ul class="chan-trang__lien-ket">
      <li><a href="index.php">Trang chủ</a></li>
      <li><a href="thanh-vien.php">Thành viên</a></li>
      <li><a href="lien-he.php">Liên hệ</a></li>
      <li><a href="quan-tri.php">Quản trị</a></li>
    </ul>
    <p class="chan-trang__dong">&copy; 2026 Sân Bóng Thắng Lợi. Đồ án học phần Lập trình Web.</p>
  </footer>

  <script type="module" src="js/trang-chung.js"></script>
  <script type="module" src="js/modules/yeuThich.js"></script>
  <?php if (!empty($scriptRieng)) echo $scriptRieng; ?>
</body>
</html>