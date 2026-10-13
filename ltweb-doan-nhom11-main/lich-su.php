<?php
/**
 * lich-su.php — Lịch sử đặt sân
 */
$tieuDeTrang = 'Lịch sử đặt sân';
require __DIR__ . '/inc/header.php';

$lichSuDatSan = [
    ['maDon' => 'DH0231', 'san' => 'Sân A1 (5 người)', 'ngay' => '05/09/2026', 'gio' => '19:00 – 20:00', 'trangThai' => 'Hoàn tất', 'soTien' => '250.000đ'],
    ['maDon' => 'DH0245', 'san' => 'Sân B2 (7 người)', 'ngay' => '08/09/2026', 'gio' => '18:00 – 19:00', 'trangThai' => 'Hoàn tất', 'soTien' => '350.000đ'],
    ['maDon' => 'DH0259', 'san' => 'Sân A1 (5 người)', 'ngay' => '14/09/2026', 'gio' => '17:00 – 18:00', 'trangThai' => 'Đã đặt cọc', 'soTien' => '200.000đ'],
];
?>

<main class="khu-vuc-chinh">
  <h1>Lịch sử đặt sân của tôi</h1>
  <p>Danh sách dưới đây liệt kê các đơn đặt sân gần nhất gắn với tài khoản của bạn.</p>

  <div class="bang-chua">
    <table class="bang">
      <caption class="bang__tieu-de">Lịch sử đặt sân từ 01/09/2026 đến nay</caption>
      <thead>
        <tr>
          <th scope="col">Mã đơn</th>
          <th scope="col">Sân</th>
          <th scope="col">Ngày</th>
          <th scope="col">Khung giờ</th>
          <th scope="col">Trạng thái</th>
          <th scope="col">Số tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($lichSuDatSan as $don): ?>
          <tr>
            <td><?= htmlspecialchars($don['maDon']) ?></td>
            <td><?= htmlspecialchars($don['san']) ?></td>
            <td><?= htmlspecialchars($don['ngay']) ?></td>
            <td><?= htmlspecialchars($don['gio']) ?></td>
            <td><?= htmlspecialchars($don['trangThai']) ?></td>
            <td><?= htmlspecialchars($don['soTien']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>