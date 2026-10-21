<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$baseUrl = '';
$tieuDeTrang = 'Lịch sử đặt sân';

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Lịch sử đặt sân của tôi</h1>
  <p>Danh sách dưới đây liệt kê các đơn đặt sân gần nhất.</p>
  
  <div class="bang-chua">
    <table class="bang">
      <thead>
        <tr><th>Mã đơn</th><th>Sân</th><th>Ngày</th><th>Giờ</th><th>Trạng thái</th><th>Số tiền</th></tr>
      </thead>
      <tbody>
        <tr><td>DH0231</td><td>Sân A1 (5 người)</td><td>05/09/2026</td><td>19:00 – 20:00</td><td>Hoàn tất</td><td>250.000đ</td></tr>
        <tr><td>DH0245</td><td>Sân B2 (7 người)</td><td>08/09/2026</td><td>18:00 – 19:00</td><td>Hoàn tất</td><td>350.000đ</td></tr>
      </tbody>
    </table>
  </div>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>