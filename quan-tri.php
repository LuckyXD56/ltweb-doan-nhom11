<?php
/**
 * quan-tri.php — Trang quản trị (Hiển thị dữ liệu từ MySQL)
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';
require __DIR__ . '/inc/bao-ve.php'; // Chặn nếu chưa đăng nhập

$baseUrl = '';
$tieuDeTrang = 'Trang quản trị';

$pdo = db();

// 1. Lấy danh sách Đơn đặt sân
$sqlDon = "SELECT d.id, d.ngay_dat, d.tong_tien, d.trang_thai, n.ho_ten, s.ten_san, ct.ngay_thue, ct.gio_bat_dau, ct.gio_ket_thuc
           FROM dat_san d
           JOIN nguoi_dung n ON d.nguoi_dung_id = n.id
           JOIN chi_tiet_dat_san ct ON d.id = ct.dat_san_id
           JOIN san_bong s ON ct.san_bong_id = s.id
           ORDER BY d.id DESC LIMIT 10";
$dsDonHang = $pdo->query($sqlDon)->fetchAll();

// 2. Lấy danh sách Liên hệ
$sqlLienHe = "SELECT * FROM lien_he ORDER BY id DESC LIMIT 10";
$dsLienHe = $pdo->query($sqlLienHe)->fetchAll();

// 3. Thống kê
$tongDon = $pdo->query("SELECT COUNT(*) FROM dat_san")->fetchColumn();
$tongTien = $pdo->query("SELECT SUM(tong_tien) FROM dat_san WHERE trang_thai != 'da_huy'")->fetchColumn();

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Trang quản trị hệ thống</h1>
  <p>Xin chào <strong><?= e($_SESSION['ho_ten'] ?? 'Admin') ?></strong>. Dưới đây là báo cáo hoạt động mới nhất.</p>

  <!-- THỐNG KÊ NHANH -->
  <div style="display: flex; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="the" style="flex: 1; text-align: center;">
      <h3 style="color: #4b5563; margin-bottom: 0.5rem; font-size: 1rem;">Tổng số đơn đặt</h3>
      <p style="font-size: 2rem; font-weight: 800; color: #0b6e3c; margin: 0;"><?= $tongDon ?></p>
    </div>
    <div class="the" style="flex: 1; text-align: center;">
      <h3 style="color: #4b5563; margin-bottom: 0.5rem; font-size: 1rem;">Doanh thu dự kiến</h3>
      <p style="font-size: 2rem; font-weight: 800; color: #0b6e3c; margin: 0;"><?= vnd((int)$tongTien) ?></p>
    </div>
  </div>

  <h2>Đơn đặt sân gần đây (10 đơn mới nhất)</h2>
  <?php if (count($dsDonHang) === 0): ?>
    <p>Chưa có đơn đặt sân nào.</p>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <thead>
          <tr>
            <th scope="col">Mã đơn</th>
            <th scope="col">Khách hàng</th>
            <th scope="col">Sân</th>
            <th scope="col">Ngày chơi</th>
            <th scope="col">Giờ chơi</th>
            <th scope="col">Tổng tiền</th>
            <th scope="col">Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dsDonHang as $don): ?>
            <tr>
              <td>#<?= $don['id'] ?></td>
              <td><?= e($don['ho_ten']) ?></td>
              <td><?= e($don['ten_san']) ?></td>
              <td><?= date('d/m/Y', strtotime($don['ngay_thue'])) ?></td>
              <td><?= substr($don['gio_bat_dau'], 0, 5) ?> - <?= substr($don['gio_ket_thuc'], 0, 5) ?></td>
              <td><?= vnd((int)$don['tong_tien']) ?></td>
              <td><strong><?= e($don['trang_thai']) ?></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <h2 style="margin-top: 2rem;">Liên hệ mới nhất (10 tin nhắn)</h2>
  <?php if (count($dsLienHe) === 0): ?>
    <p>Chưa có liên hệ nào.</p>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <thead>
          <tr>
            <th scope="col">Ngày gửi</th>
            <th scope="col">Họ tên</th>
            <th scope="col">Email</th>
            <th scope="col">Nội dung</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dsLienHe as $lh): ?>
            <tr>
              <td><?= date('d/m/Y H:i', strtotime($lh['ngay_gui'])) ?></td>
              <td><?= e($lh['ho_ten']) ?></td>
              <td><?= e($lh['email']) ?></td>
              <td><?= e($lh['noi_dung']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <h2 style="margin-top: 2rem;">Bảng giá hiện hành</h2>
  <div class="bang-chua">
    <table class="bang">
      <thead>
        <tr><th scope="col">Loại sân</th><th scope="col">Giá thuê cơ bản</th></tr>
      </thead>
      <tbody>
        <?php
        $sqlGia = "SELECT d.ten_danh_muc, MIN(s.gia_thue) as gia_min FROM danh_muc d JOIN san_bong s ON d.id = s.danh_muc_id GROUP BY d.id";
        $dsGia = $pdo->query($sqlGia)->fetchAll();
        foreach ($dsGia as $gia):
        ?>
          <tr><td><?= e($gia['ten_danh_muc']) ?></td><td><?= vnd((int)$gia['gia_min']) ?>/giờ</td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>