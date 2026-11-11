<?php
/**
 * danh-sach.php — Danh sách sân có lọc (từ khóa, danh mục, khoảng giá), sắp xếp và phân trang
 *
 * URL: danh-sach.php?q=từ+khóa&dm=5|7|11&min_gia=200000&max_gia=600000&sx=gia-tang|gia-giam|ten&trang=1
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$baseUrl = '';
$tieuDeTrang = 'Danh sách sân';

// ---------- 1. Đọc tham số GET ----------
$tuKhoa  = getChuoi('q');
$danhMuc = getChuoi('dm', 'tat-ca');
$sapXep  = getChuoi('sx', 'gia-tang');
$minGia  = getInt('min_gia', 0);
$maxGia  = getInt('max_gia', 0);
$trang   = getInt('trang', 1);

$loaiSan = is_numeric($danhMuc) ? (int) $danhMuc : 0;

$dsSapXep = ['gia-tang', 'gia-giam', 'ten'];
if (!in_array($sapXep, $dsSapXep, true)) {
    $sapXep = 'gia-tang';
}

// ---------- 2. Lọc + sắp xếp ----------
$ketQua = $kho->timKiem($tuKhoa, $loaiSan);

// Lọc theo khoảng giá
if ($minGia > 0) {
    $ketQua = array_filter($ketQua, fn($s) => $s->getGiaThuong() >= $minGia);
}
if ($maxGia > 0 && $maxGia >= $minGia) {
    $ketQua = array_filter($ketQua, fn($s) => $s->getGiaThuong() <= $maxGia);
}
$ketQua = array_values($ketQua);

$ketQua = $kho->sapXep($ketQua, $sapXep);

// ---------- 3. Phân trang ----------
$tongSoSan = count($ketQua);
$soSanMoiTrang = 9;
$tongTrang = max(1, (int) ceil($tongSoSan / $soSanMoiTrang));
$trangHienTai = max(1, min($tongTrang, $trang));
$viTriBatDau = ($trangHienTai - 1) * $soSanMoiTrang;
$sanHienThi = array_slice($ketQua, $viTriBatDau, $soSanMoiTrang);

// ---------- 4. Hiển thị ----------
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Danh sách sân bóng</h1>
  <p class="gioi-thieu">Hệ thống Sân Bóng Thắng Lợi hiện có nhiều sân cỏ nhân tạo đạt chuẩn,
    đáp ứng nhu cầu tập luyện và thi đấu của mọi đội bóng phong trào.</p>

  <h2>Tìm sân theo loại, từ khoá và khoảng giá</h2>
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
      <label class="truong__nhan" for="min_gia">Giá từ (VNĐ)</label>
      <input type="number" id="min_gia" name="min_gia" class="truong__nhap"
             value="<?= $minGia > 0 ? $minGia : '' ?>" placeholder="Ví dụ: 200000" step="50000" min="0">
    </div>
    <div>
      <label class="truong__nhan" for="max_gia">Đến giá (VNĐ)</label>
      <input type="number" id="max_gia" name="max_gia" class="truong__nhap"
             value="<?= $maxGia > 0 ? $maxGia : '' ?>" placeholder="Ví dụ: 600000" step="50000" min="0">
    </div>
    <div>
      <label class="truong__nhan" for="sx">Sắp xếp theo</label>
      <select id="sx" name="sx" class="truong__nhap">
        <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá thấp → cao</option>
        <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá cao → thấp</option>
        <option value="ten"      <?= $sapXep === 'ten'      ? 'selected' : '' ?>>Theo tên A → Z</option>
      </select>
    </div>
    <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
      <button type="submit" class="nut nut--chinh">Lọc sân</button>
      <a class="nut nut--phu" href="danh-sach.php">Xoá lọc</a>
    </div>
  </form>

  <p class="vung-thong-bao" aria-live="polite">
    Tìm thấy <strong><?= $tongSoSan ?></strong> sân bóng phù hợp (Trang <?= $trangHienTai ?>/<?= $tongTrang ?>).
  </p>

  <?php if ($tongSoSan === 0): ?>
    <div class="the" style="text-align: center; padding: 2rem;">
      <p style="font-size: 2rem; margin: 0;">🔍</p>
      <p><strong>Không tìm thấy sân nào phù hợp.</strong></p>
      <p>Hãy thử xoá từ khoá hoặc điều chỉnh lại khoảng giá.</p>
      <a class="nut nut--chinh" href="danh-sach.php">Xem tất cả sân</a>
    </div>
  <?php else: ?>
    <ul class="danh-sach-the">
      <?php foreach ($sanHienThi as $san): ?>
        <li class="the">
          <h3><?= e($san->getTen()) ?> (<?= $san->getLoaiSan() ?> người)</h3>
          <p>
            Khu vực: <?= e($san->getKhuVuc()) ?><br>
            Giá thường: <?= vnd($san->getGiaThuong()) ?>/giờ<br>
            Giá vàng: <?= vnd($san->getGiaVang()) ?>/giờ<br>
            Trạng thái: <strong><?= e($san->getTrangThai()) ?></strong>
          </p>
          <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap;">
            <a class="nut nut--chinh" href="chi-tiet.php?id=<?= urlencode($san->getId()) ?>">Chi tiết &amp; Đặt</a>
            <form method="post" action="them-gio.php" style="display: inline;">
              <input type="hidden" name="action" value="them">
              <input type="hidden" name="id" value="<?= e($san->getId()) ?>">
              <input type="hidden" name="so_luong" value="1">
              <button type="submit" class="nut nut--phu" title="Thêm vào giỏ">🛒 Giỏ hàng</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Thanh phân trang -->
    <?php if ($tongTrang > 1): ?>
      <nav class="phan-trang" style="display: flex; justify-content: center; gap: 0.5rem; margin: 2rem 0; flex-wrap: wrap;">
        <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
          <?php
            $queryParam = $_GET;
            $queryParam['trang'] = $i;
            $link = 'danh-sach.php?' . http_build_query($queryParam);
          ?>
          <a class="nut <?= $i === $trangHienTai ? 'nut--chinh' : 'nut--phu' ?>" href="<?= e($link) ?>" style="padding: 6px 12px; min-width: 38px; text-align: center;"><?= $i ?></a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>

  <h2>Ghi chú về bảng giá</h2>
  <ul class="danh-sach-tien-ich">
    <li>Giá khung giờ vàng (18:00 – 21:00) cao hơn khung giờ ban ngày</li>
    <li>Khách đặt từ 3 giờ trở lên được giảm 10% tổng hoá đơn</li>
    <li>Đặt sân trực tuyến để xem lịch trống theo thời gian thực</li>
  </ul>
</main>
<script src="js/api-danh-sach.js"></script>
<?php require __DIR__ . '/inc/footer.php'; ?>
