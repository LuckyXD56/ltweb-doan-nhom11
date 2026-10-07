<?php
/**
 * quan-tri.php — Trang quản trị
 */
$tieuDeTrang = 'Trang quản trị';
require __DIR__ . '/inc/header.php';

$soDoSan = [
    'Sân A1 – loại 5 người – khu vực phía Đông',
    'Sân A2 – loại 5 người – khu vực phía Đông',
    'Sân B2 – loại 7 người – khu vực trung tâm',
];
$soDoSanNoiBat = 'Sân C1 – loại 11 người – khu vực phía Tây, có khán đài';

$bangGia = [
    ['loai' => '5 người', 'thuong' => '200.000đ/giờ', 'vang' => '250.000đ/giờ'],
    ['loai' => '7 người', 'thuong' => '300.000đ/giờ', 'vang' => '350.000đ/giờ'],
    ['loai' => '11 người', 'thuong' => '600.000đ/giờ', 'vang' => '700.000đ/giờ'],
];

$thongKe = [
    ['chiSo' => 'Tổng lượt đặt sân', 'giaTri' => '128 lượt'],
    ['chiSo' => 'Doanh thu ước tính', 'giaTri' => '34.500.000đ'],
    ['chiSo' => 'Tỷ lệ lấp đầy sân giờ vàng', 'giaTri' => '87%'],
];
?>

<main class="khu-vuc-chinh">
  <h1>Trang quản trị hệ thống</h1>
  <p>Khu vực dành riêng cho quản lý sân, dùng để cập nhật sơ đồ sân, bảng giá và xem báo cáo hoạt động.</p>

  <h2>Sơ đồ sân thực tế</h2>
  <ul class="danh-sach-the">
    <?php foreach ($soDoSan as $san): ?>
      <li class="the"><?= htmlspecialchars($san) ?></li>
    <?php endforeach; ?>
    <li class="the the--noi-bat"><?= htmlspecialchars($soDoSanNoiBat) ?></li>
  </ul>

  <h2>Bảng giá hiện hành</h2>
  <div class="bang-chua">
    <table class="bang">
      <caption class="bang__tieu-de">Bảng giá áp dụng từ 01/09/2026</caption>
      <thead>
        <tr>
          <th scope="col">Loại sân</th>
          <th scope="col">Giờ thường (06:00–18:00)</th>
          <th scope="col">Giờ vàng (18:00–22:00)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($bangGia as $dong): ?>
          <tr>
            <td><?= htmlspecialchars($dong['loai']) ?></td>
            <td><?= htmlspecialchars($dong['thuong']) ?></td>
            <td><?= htmlspecialchars($dong['vang']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h2>Thống kê tháng 09/2026</h2>
  <div class="bang-chua">
    <table class="bang">
      <caption class="bang__tieu-de">Số liệu thống kê hoạt động</caption>
      <thead>
        <tr>
          <th scope="col">Chỉ số</th>
          <th scope="col">Giá trị</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($thongKe as $tk): ?>
          <tr>
            <td><?= htmlspecialchars($tk['chiSo']) ?></td>
            <td><?= htmlspecialchars($tk['giaTri']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h2>Video hướng dẫn sử dụng hệ thống quản trị</h2>
  <iframe class="khung-nhung"
          src="https://www.youtube.com/embed/dQw4w9WgXcQ"
          title="Video hướng dẫn sử dụng trang quản trị Sân Bóng Thắng Lợi"
          loading="lazy" allowfullscreen></iframe>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>