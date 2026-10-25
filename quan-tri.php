<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

// Kiểm tra quyền Admin trực tiếp
if (empty($_SESSION['da_dang_nhap']) || ($_SESSION['vai_tro'] ?? '') !== 'quan_tri') {
    datFlash('loi', 'Bạn cần đăng nhập bằng tài khoản quản trị.');
    chuyenHuong('dang-nhap.php');
}

$baseUrl = '';
$tieuDeTrang = 'Trang quản trị';

$dsDonHang = $pdo->query("SELECT d.id, d.ngay_dat, d.tong_tien, d.trang_thai, n.ho_ten, s.ten_san, ct.ngay_thue, ct.gio_bat_dau, ct.gio_ket_thuc FROM dat_san d JOIN nguoi_dung n ON d.nguoi_dung_id = n.id JOIN chi_tiet_dat_san ct ON d.id = ct.dat_san_id JOIN san_bong s ON ct.san_bong_id = s.id ORDER BY d.id DESC LIMIT 10")->fetchAll();

// Sử dụng KhoLienHe thay vì đọc file JSONL
$dsLienHe = $khoLienHe->tatCa();

$tongDon = $pdo->query("SELECT COUNT(*) FROM dat_san")->fetchColumn();
$tongTien = $pdo->query("SELECT SUM(tong_tien) FROM dat_san WHERE trang_thai != 'da_huy'")->fetchColumn();

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Trang quản trị hệ thống</h1>
  <p>Xin chào <strong><?= e($_SESSION['ho_ten'] ?? 'Admin') ?></strong>. Dưới đây là báo cáo hoạt động mới nhất.</p>

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
          <tr><th>Mã đơn</th><th>Khách hàng</th><th>Sân</th><th>Ngày chơi</th><th>Giờ chơi</th><th>Tổng tiền</th><th>Trạng thái</th></tr>
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
          <tr><th>Ngày gửi</th><th>Họ tên</th><th>Email</th><th>Nội dung</th></tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($dsLienHe, 0, 10) as $lh): ?>
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
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>