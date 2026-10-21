<?php
/**
 * inc/footer.php — Phần chân trang dùng chung cho mọi trang PHP
 *
 * Biến cần truyền (nếu có):
 * - $baseUrl (string)     : Đường dẫn tương đối về gốc (mặc định '')
 * - $scriptRieng (string) : Thẻ <script> riêng của từng trang
 */
if (!isset($baseUrl)) $baseUrl = '';
?>
  <footer class="chan-trang">
    <p class="chan-trang__dong">Sân Bóng Thắng Lợi – 123 Đường Ngô Quyền, Quận Sơn Trà, Đà Nẵng</p>
    <p class="chan-trang__dong">Điện thoại: 0905 123 456 · Email: lienhe@sanbongthangloi.vn</p>
    <ul class="chan-trang__lien-ket">
<<<<<<< HEAD
      <li><a href="<?= e($goc) ?>index.php">Trang chủ</a></li>
      <li><a href="<?= e($goc) ?>thanh-vien.php">Thành viên</a></li>
      <li><a href="<?= e($goc) ?>lien-he.php">Liên hệ</a></li>
      <li><a href="<?= e($goc) ?>quan-tri.php">Quản trị</a></li>
=======
      <li><a href="<?= $baseUrl ?>index.php">Trang chủ</a></li>
      <li><a href="<?= $baseUrl ?>thanh-vien.php">Thành viên</a></li>
      <li><a href="<?= $baseUrl ?>lien-he.php">Liên hệ</a></li>
      <li><a href="<?= $baseUrl ?>quan-tri.php">Quản trị</a></li>
>>>>>>> b09556e (Cap nhat footer)
    </ul>
    <p class="chan-trang__dong">&copy; 2026 Sân Bóng Thắng Lợi. Đồ án học phần Lập trình Web.</p>
  </footer>

<<<<<<< HEAD
  <script type="module" src="<?= e($goc) ?>js/trang-chung.js"></script>
  <script type="module" src="<?= e($goc) ?>js/modules/yeuThich.js"></script>
=======
  <!-- Script chung -->
  <script type="module" src="<?= $baseUrl ?>js/trang-chung.js"></script>
  <script type="module" src="<?= $baseUrl ?>js/modules/yeuThich.js"></script>
  
  <!-- Script riêng của trang (nếu có) -->
>>>>>>> b09556e (Cap nhat footer)
  <?php if (!empty($scriptRieng)) echo $scriptRieng; ?>
</body>
</html>