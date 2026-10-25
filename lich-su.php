<?php
/**
 * lich-su.php — Lịch sử đặt sân
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$baseUrl = '';
$tieuDeTrang = 'Lịch sử đặt sân';

// Lấy ID người dùng từ session (nếu có)
$nguoiDungId = (int) ($_SESSION['nguoi_dung_id'] ?? 0);

$lichSu = [];
if ($nguoiDungId > 0) {
    // Join bảng dat_san, chi_tiet_dat_san và san_bong để lấy đủ thông tin
    $sql = "SELECT d.id AS ma_don, d.ngay_dat, d.tong_tien, d.trang_thai,
                   s.ten_san, ct.ngay_thue, ct.gio_bat_dau, ct.gio_ket_thuc
            FROM dat_san d
            JOIN chi_tiet_dat_san ct ON d.id = ct.dat_san_id
            JOIN san_bong s ON ct.san_bong_id = s.id
            WHERE d.nguoi_dung_id = ?
            ORDER BY d.id DESC";
            
    $stmt = db()->prepare($sql);
    $stmt->execute([$nguoiDungId]);
    $lichSu = $stmt->fetchAll();
}

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Lịch sử đặt sân của tôi</h1>
  <p>Danh sách dưới đây liệt kê các đơn đặt sân gần nhất gắn với tài khoản của bạn.</p>

  <?php if (count($lichSu) === 0): ?>
    <div class="the" style="text-align: center; padding: 2rem;">
      <p>Bạn chưa có đơn đặt sân nào.</p>
      <a class="nut nut--chinh" href="danh-sach.php">Đặt sân ngay</a>
    </div>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <caption class="bang__tieu-de">Lịch sử đặt sân của bạn</caption>
        <thead>
          <tr>
            <th scope="col">Mã đơn</th>
            <th scope="col">Sân</th>
            <th scope="col">Ngày đặt</th>
            <th scope="col">Khung giờ</th>
            <th scope="col">Trạng thái</th>
            <th scope="col">Tổng tiền</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lichSu as $don): ?>
            <tr>
              <td>#<?= e((string)$don['ma_don']) ?></td>
              <td><?= e($don['ten_san']) ?></td>
              <td><?= date('d/m/Y', strtotime($don['ngay_thue'])) ?></td>
              <td><?= substr($don['gio_bat_dau'], 0, 5) ?> - <?= substr($don['gio_ket_thuc'], 0, 5) ?></td>
              <td>
                <?php
                  // Hiển thị trạng thái bằng tiếng Việt
                  $mau = '#f59e0b'; // Vàng
                  $text = 'Chờ xác nhận';
                  if ($don['trang_thai'] === 'da_coc') { $text = 'Đã cọc'; $mau = '#3b82f6'; }
                  if ($don['trang_thai'] === 'hoan_tat') { $text = 'Hoàn tất'; $mau = '#10b981'; }
                  if ($don['trang_thai'] === 'da_huy') { $text = 'Đã huỷ'; $mau = '#ef4444'; }
                ?>
                <span style="color: <?= $mau ?>; font-weight: 600;"><?= $text ?></span>
              </td>
              <td><?= vnd((int)$don['tong_tien']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>