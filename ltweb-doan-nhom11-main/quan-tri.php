<?php
/**
 * quan-tri.php — Trang quản trị (yêu cầu đăng nhập)
 *
 * Hiển thị:
 * - Danh sách liên hệ đã nhận từ storage/lien-he.jsonl
 * - Danh sách lần đăng nhập sai từ storage/dang-nhap-sai.jsonl
 * - Bảng giá + thống kê (như trước)
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';
require __DIR__ . '/inc/bao-ve.php';   // chặn nếu chưa đăng nhập

$tieuDeTrang = 'Trang quản trị';

// ---------- Đọc liên hệ ----------
$lienHe = [];
$fileLienHe = DUONG_DAN_STORAGE . '/lien-he.jsonl';
if (is_file($fileLienHe)) {
    $dong = file($fileLienHe, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($dong) as $line) {
        $obj = json_decode($line, true);
        if (is_array($obj)) {
            $lienHe[] = $obj;
        }
    }
}

// ---------- Đọc log đăng nhập sai ----------
$dangNhapSai = [];
$fileLogSai = DUONG_DAN_STORAGE . '/dang-nhap-sai.jsonl';
if (is_file($fileLogSai)) {
    $dong = file($fileLogSai, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($dong) as $line) {
        $obj = json_decode($line, true);
        if (is_array($obj)) {
            $dangNhapSai[] = $obj;
        }
    }
    $dangNhapSai = array_slice($dangNhapSai, 0, 10);
}

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Trang quản trị hệ thống</h1>
  <p>Xin chào <strong><?= e($_SESSION['username'] ?? '') ?></strong>.</p>

  <h2>Liên hệ đã nhận (mới nhất trước)</h2>
  <?php if (count($lienHe) === 0): ?>
    <p>Chưa có liên hệ nào.</p>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <caption class="bang__tieu-de">Tổng số <?= count($lienHe) ?> liên hệ</caption>
        <thead>
          <tr>
            <th scope="col">Thời gian</th>
            <th scope="col">Họ tên</th>
            <th scope="col">Email</th>
            <th scope="col">Nội dung</th>
            <th scope="col">Ảnh</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lienHe as $lh): ?>
            <tr>
              <td><?= e($lh['thoi_gian'] ?? '') ?></td>
              <td><?= e($lh['ho_ten'] ?? '') ?></td>
              <td><?= e($lh['email'] ?? '') ?></td>
              <td><?= e($lh['noi_dung'] ?? '') ?></td>
              <td>
                <?php if (!empty($lh['anh'])): ?>
                  <a href="uploads/<?= e($lh['anh']) ?>" target="_blank">Xem ảnh</a>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <h2>Đăng nhập sai gần đây (10 lần cuối)</h2>
  <?php if (count($dangNhapSai) === 0): ?>
    <p>Không có lần đăng nhập sai nào.</p>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <caption class="bang__tieu-de">Nhật ký đăng nhập sai</caption>
        <thead>
          <tr>
            <th scope="col">Thời gian</th>
            <th scope="col">Tên đăng nhập</th>
            <th scope="col">IP</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dangNhapSai as $log): ?>
            <tr>
              <td><?= e($log['thoi_gian'] ?? '') ?></td>
              <td><?= e($log['username'] ?? '') ?></td>
              <td><?= e($log['ip'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <h2>Bảng giá hiện hành</h2>
  <div class="bang-chua">
    <table class="bang">
      <caption class="bang__tieu-de">Bảng giá áp dụng từ 01/09/2026</caption>
      <thead>
        <tr>
          <th scope="col">Loại sân</th>
          <th scope="col">Giờ thường</th>
          <th scope="col">Giờ vàng</th>
        </tr>
      </thead>
      <tbody>
        <tr><td>5 người</td><td>200.000đ/giờ</td><td>250.000đ/giờ</td></tr>
        <tr><td>7 người</td><td>300.000đ/giờ</td><td>350.000đ/giờ</td></tr>
        <tr><td>11 người</td><td>600.000đ/giờ</td><td>700.000đ/giờ</td></tr>
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
        <tr><td>Tổng lượt đặt sân</td><td>128 lượt</td></tr>
        <tr><td>Doanh thu ước tính</td><td>34.500.000đ</td></tr>
        <tr><td>Tỷ lệ lấp đầy sân giờ vàng</td><td>87%</td></tr>
      </tbody>
    </table>
  </div>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>