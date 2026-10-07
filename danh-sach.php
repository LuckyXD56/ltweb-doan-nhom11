<?php
/**
 * danh-sach.php — Danh sách sân bóng
 */
$tieuDeTrang = 'Danh sách sân bóng';
require __DIR__ . '/inc/header.php';

$danhSachSan = [
    ['id' => 'A1', 'ten' => 'Sân A1', 'loai' => 5, 'khuVuc' => 'Khu A', 'giaTruoc17' => '150.000đ', 'giaSau17' => '300.000đ'],
    ['id' => 'A2', 'ten' => 'Sân A2', 'loai' => 5, 'khuVuc' => 'Khu A', 'giaTruoc17' => '150.000đ', 'giaSau17' => '300.000đ'],
    ['id' => 'B1', 'ten' => 'Sân B1', 'loai' => 7, 'khuVuc' => 'Khu B', 'giaTruoc17' => '250.000đ', 'giaSau17' => '450.000đ'],
    ['id' => 'B2', 'ten' => 'Sân B2', 'loai' => 7, 'khuVuc' => 'Khu B', 'giaTruoc17' => '250.000đ', 'giaSau17' => '450.000đ'],
    ['id' => 'C1', 'ten' => 'Sân C1', 'loai' => 11, 'khuVuc' => 'Khu C', 'giaTruoc17' => '600.000đ', 'giaSau17' => '700.000đ'],
];
?>

<main class="khu-vuc-chinh">
  <h1>Danh sách sân bóng</h1>
  <p class="gioi-thieu">Hệ thống Sân Bóng Thắng Lợi hiện có nhiều sân cỏ nhân tạo đạt chuẩn,
    đáp ứng nhu cầu tập luyện và thi đấu của mọi đội bóng phong trào.</p>

  <h2>Các sân hiện có</h2>
  <ul class="danh-sach-the">
    <?php foreach ($danhSachSan as $i => $san): ?>
      <li class="the<?= $i === 2 ? ' the--noi-bat' : '' ?>">
        <h3><?= htmlspecialchars($san['ten']) ?> (<?= $san['loai'] ?> người)</h3>
        <p>Khu vực: <?= htmlspecialchars($san['khuVuc']) ?><br>
           Giá trước 17h: <?= $san['giaTruoc17'] ?>/giờ<br>
           Giá sau 17h: <?= $san['giaSau17'] ?>/giờ</p>
        <a class="nut nut--chinh" href="chi-tiet.php?id=<?= urlencode($san['id']) ?>">Xem chi tiết</a>
      </li>
    <?php endforeach; ?>
  </ul>

  <h2>Ghi chú về bảng giá</h2>
  <ul class="danh-sach-tien-ich">
    <li>Giá khung giờ vàng (18:00 – 21:00) cao hơn khung giờ ban ngày</li>
    <li>Khách đặt từ 3 giờ trở lên được giảm 10% tổng hoá đơn</li>
    <li>Đặt sân trực tuyến để xem lịch trống theo thời gian thực</li>
  </ul>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>