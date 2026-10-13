<?php
/**
 * gio-hang.php — Trang giỏ hàng
 */
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

$tieuDeTrang = 'Giỏ hàng';
require __DIR__ . '/inc/header.php';

$chiTiet   = $gio->chiTiet();
$tongTien  = $gio->tongTien();
$demSoMuc  = $gio->demSoMuc();
?>

<main class="khu-vuc-chinh">
  <h1>Giỏ hàng của bạn</h1>

  <?php if ($demSoMuc === 0): ?>
    <div class="the" style="text-align: center; padding: 2rem;">
      <p style="font-size: 3rem; margin: 0;">🛒</p>
      <p><strong>Giỏ hàng đang trống.</strong></p>
      <p>Hãy chọn sân và bấm "Thêm vào giỏ".</p>
      <a class="nut nut--chinh" href="danh-sach.php">Xem danh sách sân</a>
    </div>
  <?php else: ?>
    <p>Bạn có <strong><?= $demSoMuc ?></strong> sân trong giỏ.</p>

    <div class="bang-chua">
      <table class="bang">
        <caption class="bang__tieu-de">Chi tiết giỏ hàng</caption>
        <thead>
          <tr>
            <th scope="col">Sân</th>
            <th scope="col">Loại</th>
            <th scope="col">Đơn giá/giờ</th>
            <th scope="col">Số giờ</th>
            <th scope="col">Thành tiền</th>
            <th scope="col">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($chiTiet as $muc): $san = $muc['san']; ?>
            <tr>
              <td><?= e($san->getTen()) ?></td>
              <td><?= $san->getLoaiSan() ?> người</td>
              <td><?= vnd($san->getGiaThuong()) ?></td>
              <td>
                <form method="post" action="them-gio.php" style="display:flex; gap: 0.25rem;">
                  <input type="hidden" name="action" value="doi">
                  <input type="hidden" name="id" value="<?= e($san->getId()) ?>">
                  <input type="hidden" name="quay_lai" value="gio-hang.php">
                  <input type="number" name="so_luong" min="1" max="5"
                         value="<?= $muc['soLuong'] ?>" style="width: 4rem;">
                  <button type="submit" class="nut nut--phu" style="padding: 0.25rem 0.5rem;">Đổi</button>
                </form>
              </td>
              <td><?= vnd($muc['thanhTien']) ?></td>
              <td>
                <form method="post" action="them-gio.php" style="display:inline;">
                  <input type="hidden" name="action" value="xoa-mot">
                  <input type="hidden" name="id" value="<?= e($san->getId()) ?>">
                  <input type="hidden" name="quay_lai" value="gio-hang.php">
                  <button type="submit" class="nut nut--phu" style="padding: 0.25rem 0.5rem;">Xoá</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th scope="row" colspan="4" style="text-align: right;">Tổng tiền:</th>
            <th colspan="2"><?= vnd($tongTien) ?></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <p class="mt-2">
      <a class="nut nut--chinh" href="dat-san.php">Tiến hành đặt sân</a>
      <form method="post" action="them-gio.php" style="display:inline;">
        <input type="hidden" name="action" value="xoa-het">
        <input type="hidden" name="quay_lai" value="gio-hang.php">
        <button type="submit" class="nut nut--phu">Xoá toàn bộ giỏ</button>
      </form>
    </p>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>