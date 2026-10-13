<?php
/**
 * danh-sach.php — Danh sách sân có lọc và sắp xếp
 *
 * URL: danh-sach.php?q=từ+khóa&dm=5|7|11&sx=gia-tang|gia-giam|ten
 * - q  : từ khóa tìm theo tên/khu vực (không dấu)
 * - dm : danh mục loại sân (5, 7, 11). "tat-ca" = bỏ lọc
 * - sx : tiêu chí sắp xếp
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$tieuDeTrang = 'Danh sách sân';

// ---------- 1. Đọc tham số GET ----------
$tuKhoa  = getChuoi('q');
$danhMuc = getChuoi('dm', 'tat-ca');   // "tat-ca" hoặc "5", "7", "11"
$sapXep  = getChuoi('sx', 'gia-tang');

// Chuẩn hóa danh mục thành số (0 = tất cả)
$loaiSan = is_numeric($danhMuc) ? (int) $danhMuc : 0;

// Whitelist tiêu chí sắp xếp
$dsSapXep = ['gia-tang', 'gia-giam', 'ten'];
if (!in_array($sapXep, $dsSapXep, true)) {
    $sapXep = 'gia-tang';
}

// ---------- 2. Lọc + sắp xếp ----------
$ketQua = $kho->timKiem($tuKhoa, $loaiSan);
$ketQua = $kho->sapXep($ketQua, $sapXep);

// ---------- 3. Hiển thị ----------
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Danh sách sân bóng</h1>
  <p class="gioi-thieu">Hệ thống Sân Bóng Thắng Lợi hiện có nhiều sân cỏ nhân tạo đạt chuẩn,
    đáp ứng nhu cầu tập luyện và thi đấu của mọi đội bóng phong trào.</p>

  <h2>Tìm sân theo loại hoặc từ khoá</h2>
  <form class="bo-loc-san" method="get" action="danh-sach.php">
    <div>
      <label class="truong__nhan" for="q">Từ khóa (tên hoặc khu vực)</label>
      <input type="search" id="q" name="q" class="truong__nhap"
             value="<?= e($tuKhoa) ?>"
             placeholder="Ví dụ: A1, Phía Đông…">
    </div>
    <div>
      <label class="truong__nhan" for="dm">Loại sân</label>
      <select id="dm" name="dm" class="truong__nhap">
        <option value="tat-ca" <?= $danhMuc === 'tat-ca' ? 'selected' : '' ?>>Tất cả loại sân</option>
        <option value="5"      <?= $danhMuc === '5'      ? 'selected' : '' ?>>5 người</option>
        <option value="7"      <?= $danhMuc === '7'      ? 'selected' : '' ?>>7 người</option>
        <option value="11"     <?= $danhMuc === '11'     ? 'selected' : '' ?>>11 người</option>
      </select>
    </div>
    <div>
      <label class="truong__nhan" for="sx">Sắp xếp theo</label>
      <select id="sx" name="sx" class="truong__nhap">
        <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá thấp → cao</option>
        <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá cao → thấp</option>
        <option value="ten"      <?= $sapXep === 'ten'      ? 'selected' : '' ?>>Theo tên A → Z</option>
      </select>
    </div>
    <div>
      <button type="submit" class="nut nut--chinh">Tìm kiếm</button>
      <a class="nut nut--phu" href="danh-sach.php">Xoá lọc</a>
    </div>
  </form>

  <p class="vung-thong-bao" aria-live="polite">
    <?php if ($tuKhoa !== '' || $loaiSan > 0): ?>
      Kết quả:
      <?php if ($tuKhoa !== ''): ?>
        từ khoá "<strong><?= e($tuKhoa) ?></strong>"
      <?php endif; ?>
      <?php if ($loaiSan > 0): ?>
        · loại <strong><?= $loaiSan ?></strong> người
      <?php endif; ?>
      — tìm thấy <strong><?= count($ketQua) ?></strong> sân.
    <?php else: ?>
      Hiển thị <strong><?= count($ketQua) ?></strong> sân, sắp xếp theo
      <strong><?= e(['gia-tang' => 'giá tăng', 'gia-giam' => 'giá giảm', 'ten' => 'tên A-Z'][$sapXep]) ?></strong>.
    <?php endif; ?>
  </p>

  <?php if (count($ketQua) === 0): ?>
    <div class="the" style="text-align: center; padding: 2rem;">
      <p style="font-size: 2rem; margin: 0;">🔍</p>
      <p><strong>Không tìm thấy sân nào phù hợp.</strong></p>
      <p>Hãy thử xoá từ khoá hoặc đổi loại sân.</p>
      <a class="nut nut--chinh" href="danh-sach.php">Xem tất cả sân</a>
    </div>
  <?php else: ?>
    <ul class="danh-sach-the">
      <?php foreach ($ketQua as $san): ?>
        <li class="the">
          <h3><?= e($san->getTen()) ?> (<?= $san->getLoaiSan() ?> người)</h3>
          <p>
            Khu vực: <?= e($san->getKhuVuc()) ?><br>
            Giá thường: <?= vnd($san->getGiaThuong()) ?>/giờ<br>
            Giá vàng: <?= vnd($san->getGiaVang()) ?>/giờ<br>
            Đánh giá: <?= $san->getDanhGia() ?>/5
          </p>
          <a class="nut nut--chinh" href="chi-tiet.php?id=<?= urlencode($san->getId()) ?>">Xem chi tiết</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <h2>Ghi chú về bảng giá</h2>
  <ul class="danh-sach-tien-ich">
    <li>Giá khung giờ vàng (18:00 – 21:00) cao hơn khung giờ ban ngày</li>
    <li>Khách đặt từ 3 giờ trở lên được giảm 10% tổng hoá đơn</li>
    <li>Đặt sân trực tuyến để xem lịch trống theo thời gian thực</li>
  </ul>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>