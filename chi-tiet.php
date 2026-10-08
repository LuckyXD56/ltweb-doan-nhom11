<?php
/**
 * chi-tiet.php — Chi tiết sân, đọc ?id= trên URL
 *
 * Kỹ thuật:
 * - filter_var kiểm tra id là số nguyên (nhưng id ở đây là chuỗi "A1")
 * - Nếu id sai → chuyển hướng 404
 * - Đọc cookie "da_xem", cập nhật danh sách 5 sân vừa xem
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

// ---------- 1. Đọc ?id= ----------
$idSan = getChuoi('id');

if ($idSan === '') {
    // Không có id → 404
    header('Location: 404.php');
    exit;
}

// ---------- 2. Tìm sân ----------
$san = $kho->timTheoId($idSan);

if ($san === null) {
    // Không tìm thấy → 404
    header('Location: 404.php');
    exit;
}

// ---------- 3. Cập nhật cookie "đã xem gần đây" ----------
themDaXem($san->getId());

// ---------- 4. Đọc danh sách "đã xem gần đây" (bỏ id hiện tại) ----------
$daXem = array_diff(danhSachDaXem(), [$san->getId()]);
$daXem = array_slice($daXem, 0, 4);

// ---------- 5. Hiển thị ----------
$tieuDeTrang = 'Chi tiết ' . $san->getTen();
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1 id="chi-tiet-tieu-de">Chi tiết <?= e($san->getTen()) ?></h1>

  <div id="thong-tin-san">
    <figure class="khoi-anh">
      <img class="khoi-anh__anh" src="<?= e($san->getHinhAnh() ?: 'images/san-a1.jpg') ?>"
           alt="Hình ảnh <?= e($san->getTen()) ?>"
           width="800" height="400" loading="lazy">
      <figcaption class="khoi-anh__chu-thich"><?= e($san->getKhuVuc()) ?></figcaption>
    </figure>

    <h2>Thông số kỹ thuật</h2>
    <div class="bang-chua">
      <table class="bang">
        <caption class="bang__tieu-de">Thông tin chi tiết <?= e($san->getTen()) ?></caption>
        <thead>
          <tr><th scope="col">Hạng mục</th><th scope="col">Chi tiết</th></tr>
        </thead>
        <tbody>
          <tr><td>Mã sân</td><td><?= e($san->getId()) ?></td></tr>
          <tr><td>Loại sân</td><td><?= $san->getLoaiSan() ?> người</td></tr>
          <tr><td>Khu vực</td><td><?= e($san->getKhuVuc()) ?></td></tr>
          <tr><td>Giá giờ thường</td><td><?= vnd($san->getGiaThuong()) ?>/giờ</td></tr>
          <tr><td>Giá giờ vàng</td><td><?= vnd($san->getGiaVang()) ?>/giờ</td></tr>
          <tr><td>Đánh giá</td><td><?= $san->getDanhGia() ?>/5</td></tr>
          <tr><td>Trạng thái</td><td><?= $san->isDangHoatDong() ? 'Đang hoạt động' : 'Tạm đóng' ?></td></tr>
        </tbody>
      </table>
    </div>

    <p class="chu-canh-giua mt-2">
      <form method="post" action="them-gio.php" style="display:inline;">
  <input type="hidden" name="id" value="<?= e($san->getId()) ?>">
  <input type="hidden" name="so_luong" value="1">
  <input type="hidden" name="quay_lai" value="chi-tiet.php?id=<?= urlencode($san->getId()) ?>">
  <button type="submit" class="nut nut--chinh">🛒 Thêm vào giỏ</button>
</form>
      <a class="nut nut--phu" href="danh-sach.php">← Về danh sách sân</a>
    </p>
  </div>

  <?php if (count($daXem) > 0): ?>
    <h2>Đã xem gần đây</h2>
    <ul class="danh-sach-the">
      <?php foreach ($daXem as $idCu): $sanCu = $kho->timTheoId((string) $idCu); if ($sanCu === null) continue; ?>
        <li class="the">
          <h3><?= e($sanCu->getTen()) ?> (<?= $sanCu->getLoaiSan() ?> người)</h3>
          <p>Khu vực: <?= e($sanCu->getKhuVuc()) ?><br>
             Giá: <?= vnd($sanCu->getGiaThuong()) ?>/giờ</p>
          <a class="nut nut--phu" href="chi-tiet.php?id=<?= urlencode($sanCu->getId()) ?>">Xem lại</a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>