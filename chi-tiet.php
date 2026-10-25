<?php
/**
 * chi-tiet.php — Chi tiết sân, đọc ?id= trên URL
 */

require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$baseUrl = '';

// ---------- 1. Đọc ?id= trên URL ----------
$idSan = getChuoi('id');

if ($idSan === '') {
    // Không có id → 404
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

// ---------- 2. Tìm sân từ Database ----------
$san = $kho->timTheoId($idSan);

if ($san === null) {
    // Không tìm thấy → 404
    http_response_code(404);
    require __DIR__ . '/404.php';
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
          <tr><td>Mã sân (ID)</td><td><?= e($san->getId()) ?></td></tr>
          <tr><td>Loại sân</td><td><?= $san->getLoaiSan() ?> người</td></tr>
          <tr><td>Khu vực</td><td><?= e($san->getKhuVuc()) ?></td></tr>
          <tr><td>Giá giờ thường</td><td><?= vnd($san->getGiaThuong()) ?>/giờ</td></tr>
          <tr><td>Giá giờ vàng</td><td><?= vnd($san->getGiaVang()) ?>/giờ</td></tr>
          <tr><td>Đánh giá</td><td><?= $san->getDanhGia() ?>/5</td></tr>
          <tr><td>Trạng thái</td><td><?= $san->isDangHoatDong() ? 'Đang hoạt động' : 'Tạm đóng' ?></td></tr>
        </tbody>
      </table>
    </div>

    <!-- FORM ĐẶT SÂN -->
    <?php if (!empty($_SESSION['da_dang_nhap'])): ?>
      <form method="post" action="xu-ly-dat-san.php" style="margin-top: 1.5rem; background: #f8faf9; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb;">
        <h3 style="margin-top: 0; color: #0b6e3c;">Đặt sân này</h3>
        
        <!-- Truyền ID số của sân (VD: 1, 2, 3...) -->
        <input type="hidden" name="ma_san" value="<?= e($san->getId()) ?>">
        
        <div style="margin-bottom: 1rem;">
            <label class="truong__nhan" for="ngay_dat">Ngày đặt (*):</label>
            <input class="truong__nhap" type="date" id="ngay_dat" name="ngay_dat" required min="<?= date('Y-m-d') ?>">
        </div>
        
        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
            <div style="flex: 1;">
                <label class="truong__nhan" for="gio_bat_dau">Giờ bắt đầu (*):</label>
                <input class="truong__nhap" type="time" id="gio_bat_dau" name="gio_bat_dau" required>
            </div>
            <div style="flex: 1;">
                <label class="truong__nhan" for="gio_ket_thuc">Giờ kết thúc (*):</label>
                <input class="truong__nhap" type="time" id="gio_ket_thuc" name="gio_ket_thuc" required>
            </div>
        </div>
        
        <button type="submit" class="nut nut--chinh">Xác nhận Đặt sân</button>
      </form>
    <?php else: ?>
      <div style="margin-top: 1.5rem; padding: 1rem; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
        <p style="margin: 0;">Vui lòng <a href="dang-nhap.php" style="font-weight: bold; color: #b45309;">đăng nhập</a> để tiến hành đặt sân.</p>
      </div>
    <?php endif; ?>

    <p class="chu-canh-giua mt-2" style="margin-top: 1.5rem;">
      <a class="nut nut--phu" href="danh-sach.php">← Về danh sách sân</a>
    </p>
  </div>

  <!-- KHỐI ĐÃ XEM GẦN ĐÂY -->
  <?php if (count($daXem) > 0): ?>
    <h2 style="margin-top: 2rem;">Đã xem gần đây</h2>
    <ul class="danh-sach-the">
      <?php foreach ($daXem as $idCu): 
          $sanCu = $kho->timTheoId((string) $idCu); 
          if ($sanCu === null) continue; 
      ?>
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