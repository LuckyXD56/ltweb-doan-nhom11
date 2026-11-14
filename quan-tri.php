<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/ham.php';

// Kiểm tra quyền Admin bằng yeuCauQuyen (sẽ ghi log cam_truy_cap nếu sai vai trò)
$nd = yeuCauQuyen('quan_tri');

// Xử lý các thao tác quản trị POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    kiemTraCsrf();
    $action = postChuoi('action');

    // 1. Đổi trạng thái đơn đặt sân
    if ($action === 'doi_trang_thai_don') {
        $idDon = (int) postChuoi('id_don');
        $trangThaiMoi = postChuoi('trang_thai');
        $dsHopLe = ['cho_xac_nhan', 'da_coc', 'hoan_tat', 'da_huy'];

        if ($idDon > 0 && in_array($trangThaiMoi, $dsHopLe, true)) {
            try {
                $stmt = $pdo->prepare("UPDATE dat_san SET trang_thai = ? WHERE id = ?");
                $stmt->execute([$trangThaiMoi, $idDon]);
                datFlash('thanh-cong', "Đã cập nhật đơn đặt #$idDon sang trạng thái '$trangThaiMoi'!");
            } catch (Exception $e) {
                datFlash('loi', 'Lỗi cập nhật trạng thái đơn: ' . $e->getMessage());
            }
        }
        chuyenHuong('quan-tri.php');
    }

    // 2. Thêm sân bóng mới
    elseif ($action === 'them_san') {
        $tenSan    = postChuoi('ten_san');
        $danhMucId = (int) postChuoi('danh_muc_id');
        $giaThue   = (float) postChuoi('gia_thue');
        $trangThai = postChuoi('trang_thai', 'hoat_dong');
        $hinhAnh   = postChuoi('hinh_anh', 'images/san-a1.jpg');

        if ($tenSan !== '' && $danhMucId > 0 && $giaThue > 0) {
            try {
                $stmt = $pdo->prepare("INSERT INTO san_bong (ten_san, danh_muc_id, gia_thue, trang_thai, hinh_anh) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$tenSan, $danhMucId, $giaThue, $trangThai, $hinhAnh]);
                datFlash('thanh-cong', "Đã thêm sân bóng '$tenSan' thành công!");
            } catch (Exception $e) {
                datFlash('loi', 'Lỗi thêm sân: ' . $e->getMessage());
            }
        } else {
            datFlash('loi', 'Vui lòng điền đầy đủ và chính xác thông tin sân.');
        }
        chuyenHuong('quan-tri.php');
    }

    // 3. Sửa sân bóng
    elseif ($action === 'sua_san') {
        $idSan     = (int) postChuoi('id_san');
        $tenSan    = postChuoi('ten_san');
        $giaThue   = (float) postChuoi('gia_thue');
        $trangThai = postChuoi('trang_thai');

        if ($idSan > 0 && $tenSan !== '' && $giaThue > 0) {
            try {
                $stmt = $pdo->prepare("UPDATE san_bong SET ten_san = ?, gia_thue = ?, trang_thai = ? WHERE id = ?");
                $stmt->execute([$tenSan, $giaThue, $trangThai, $idSan]);
                datFlash('thanh-cong', "Đã cập nhật thông tin sân #$idSan ($tenSan)!");
            } catch (Exception $e) {
                datFlash('loi', 'Lỗi cập nhật sân: ' . $e->getMessage());
            }
        }
        chuyenHuong('quan-tri.php');
    }

    // 4. Xóa sân bóng
    elseif ($action === 'xoa_san') {
        $idSan = (int) postChuoi('id_san');
        if ($idSan > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM san_bong WHERE id = ?");
                $stmt->execute([$idSan]);
                datFlash('thanh-cong', "Đã xóa sân bóng #$idSan!");
            } catch (Exception $e) {
                datFlash('loi', 'Lỗi xóa sân (có thể do sân đã có đơn đặt): ' . $e->getMessage());
            }
        }
        chuyenHuong('quan-tri.php');
    }
}

$baseUrl = '';
$tieuDeTrang = 'Trang quản trị';

$dsDonHang = $pdo->query("SELECT d.id, d.ngay_dat, d.tong_tien, d.trang_thai, n.ho_ten, s.ten_san, ct.ngay_thue, ct.gio_bat_dau, ct.gio_ket_thuc FROM dat_san d JOIN nguoi_dung n ON d.nguoi_dung_id = n.id JOIN chi_tiet_dat_san ct ON d.id = ct.dat_san_id JOIN san_bong s ON ct.san_bong_id = s.id ORDER BY d.id DESC LIMIT 15")->fetchAll();

$dsDanhMuc = $pdo->query("SELECT id, ten_danh_muc FROM danh_muc ORDER BY id ASC")->fetchAll();
$dsSan = $pdo->query("SELECT s.id, s.ten_san, s.gia_thue, s.trang_thai, s.hinh_anh, d.ten_danh_muc FROM san_bong s JOIN danh_muc d ON s.danh_muc_id = d.id ORDER BY s.id DESC LIMIT 15")->fetchAll();

$dsLienHe = $khoLienHe->tatCa();

$tongDon = $pdo->query("SELECT COUNT(*) FROM dat_san")->fetchColumn();
$tongTien = $pdo->query("SELECT SUM(tong_tien) FROM dat_san WHERE trang_thai != 'da_huy'")->fetchColumn();

require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Trang quản trị hệ thống</h1>
  <p>Xin chào <strong><?= e($_SESSION['ho_ten'] ?? 'Admin') ?></strong>. Dưới đây là bảng điều khiển quản lý sân bóng và đơn đặt.</p>

  <div class="hang-thong-ke">
    <div class="the the-thong-ke">
      <h3 class="the-thong-ke__tieu-de">Tổng số đơn đặt</h3>
      <p class="the-thong-ke__so"><?= $tongDon ?></p>
    </div>
    <div class="the the-thong-ke">
      <h3 class="the-thong-ke__tieu-de">Doanh thu dự kiến</h3>
      <p class="the-thong-ke__so"><?= vnd((int)$tongTien) ?></p>
    </div>
  </div>

  <!-- 1. QUẢN LÝ ĐƠN ĐẶT SÂN & ĐỔI TRẠNG THÁI -->
  <h2>Đơn đặt sân gần đây (Cập nhật trạng thái)</h2>
  <?php if (count($dsDonHang) === 0): ?>
    <p>Chưa có đơn đặt sân nào.</p>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <thead>
          <tr>
            <th>Mã đơn</th>
            <th>Khách hàng</th>
            <th>Sân</th>
            <th>Ngày chơi</th>
            <th>Giờ chơi</th>
            <th>Tổng tiền</th>
            <th>Trạng thái hiện tại</th>
            <th>Đổi trạng thái</th>
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
              <td>
                <span class="the-trang-thai the-trang-thai--<?= e($don['trang_thai']) ?>">
                  <?= e($don['trang_thai']) ?>
                </span>
              </td>
              <td>
                <form method="post" action="quan-tri.php" class="form-doi-trang-thai">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="doi_trang_thai_don">
                  <input type="hidden" name="id_don" value="<?= $don['id'] ?>">
                  <select name="trang_thai" class="truong__nhap nhap-nho">
                    <option value="cho_xac_nhan" <?= $don['trang_thai'] === 'cho_xac_nhan' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="da_coc" <?= $don['trang_thai'] === 'da_coc' ? 'selected' : '' ?>>Đã cọc</option>
                    <option value="hoan_tat" <?= $don['trang_thai'] === 'hoan_tat' ? 'selected' : '' ?>>Hoàn tất</option>
                    <option value="da_huy" <?= $don['trang_thai'] === 'da_huy' ? 'selected' : '' ?>>Hủy đơn</option>
                  </select>
                  <button type="submit" class="nut nut--phu nut-luu-nho">Lưu</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <!-- 2. QUẢN LÝ SÂN BÓNG (THÊM, SỬA, XÓA) -->
  <h2 class="muc-quan-tri-cach">Quản lý Sân bóng</h2>
  
  <!-- Form Thêm Sân Mới -->
  <div class="the khung-them-san">
    <h3 class="khung-them-san__tieu-de">➕ Thêm sân bóng mới</h3>
    <form method="post" action="quan-tri.php" class="form-them-san">
      <?= csrfField() ?>
      <input type="hidden" name="action" value="them_san">
      
      <div class="cot-them-san">
        <label class="truong__nhan" for="ten_san">Tên sân (*)</label>
        <input type="text" id="ten_san" name="ten_san" required class="truong__nhap" placeholder="Ví dụ: Sân D1">
      </div>

      <div class="cot-them-san">
        <label class="truong__nhan" for="danh_muc_id">Loại sân (*)</label>
        <select id="danh_muc_id" name="danh_muc_id" class="truong__nhap" required>
          <?php foreach ($dsDanhMuc as $dm): ?>
            <option value="<?= $dm['id'] ?>"><?= e($dm['ten_danh_muc']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="cot-them-san">
        <label class="truong__nhan" for="gia_thue">Giá thuê (VNĐ/h) (*)</label>
        <input type="number" id="gia_thue" name="gia_thue" required step="10000" min="50000" class="truong__nhap" placeholder="Ví dụ: 300000">
      </div>

      <div class="cot-them-san--nho">
        <label class="truong__nhan" for="trang_thai_moi">Trạng thái</label>
        <select id="trang_thai_moi" name="trang_thai" class="truong__nhap">
          <option value="hoat_dong">Hoạt động</option>
          <option value="bao_tri">Bảo trì</option>
          <option value="het_cho">Hết chỗ</option>
        </select>
      </div>

      <div>
        <button type="submit" class="nut nut--chinh">Thêm sân</button>
      </div>
    </form>
  </div>

  <!-- Bảng Danh Sách Sân Hiện Tại -->
  <div class="bang-chua">
    <table class="bang">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên sân</th>
          <th>Danh mục</th>
          <th>Giá thuê</th>
          <th>Trạng thái</th>
          <th>Cập nhật / Xóa</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($dsSan as $san): ?>
          <tr>
            <td>#<?= $san['id'] ?></td>
            <td><strong><?= e($san['ten_san']) ?></strong></td>
            <td><?= e($san['ten_danh_muc']) ?></td>
            <td><?= vnd((float)$san['gia_thue']) ?>/h</td>
            <td>
              <span class="the-trang-thai"><?= e($san['trang_thai']) ?></span>
            </td>
            <td>
              <div class="nhom-nut-san">
                <!-- Form sửa nhanh giá & trạng thái -->
                <form method="post" action="quan-tri.php" class="form-sua-san">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="sua_san">
                  <input type="hidden" name="id_san" value="<?= $san['id'] ?>">
                  <input type="hidden" name="ten_san" value="<?= e($san['ten_san']) ?>">
                  <input type="number" name="gia_thue" value="<?= (int)$san['gia_thue'] ?>" step="10000" class="truong__nhap nhap-gia-san" title="Giá mới">
                  <select name="trang_thai" class="truong__nhap nhap-nho">
                    <option value="hoat_dong" <?= $san['trang_thai'] === 'hoat_dong' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="bao_tri" <?= $san['trang_thai'] === 'bao_tri' ? 'selected' : '' ?>>Bảo trì</option>
                    <option value="het_cho" <?= $san['trang_thai'] === 'het_cho' ? 'selected' : '' ?>>Hết chỗ</option>
                  </select>
                  <button type="submit" class="nut nut--phu nut-luu-nho">Sửa</button>
                </form>

                <!-- Form xóa sân -->
                <form method="post" action="quan-tri.php" data-xac-nhan="Bạn có chắc chắn muốn xóa sân <?= e($san['ten_san']) ?>?">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="xoa_san">
                  <input type="hidden" name="id_san" value="<?= $san['id'] ?>">
                  <button type="submit" class="nut nut-xoa-san">Xóa</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- 3. LIÊN HỆ GẦN ĐÂY -->
  <h2 class="muc-quan-tri-cach">Tin nhắn liên hệ gần đây</h2>
  <?php if (count($dsLienHe) === 0): ?>
    <p>Chưa có liên hệ nào.</p>
  <?php else: ?>
    <div class="bang-chua">
      <table class="bang">
        <thead>
          <tr><th>Ngày gửi</th><th>Họ tên</th><th>Email</th><th>Nội dung</th><th>Ảnh đính kèm</th></tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($dsLienHe, 0, 10) as $lh): ?>
            <tr>
              <td><?= date('d/m/Y H:i', strtotime($lh['ngay_gui'])) ?></td>
              <td><?= e($lh['ho_ten']) ?></td>
              <td><?= e($lh['email']) ?></td>
              <td><?= e($lh['noi_dung']) ?></td>
              <td>
                <?php if (!empty($lh['hinh_anh'])): ?>
                  <a href="uploads/<?= e($lh['hinh_anh']) ?>" target="_blank" rel="noopener">
                    <img src="uploads/<?= e($lh['hinh_anh']) ?>" alt="Ảnh liên hệ" class="anh-lien-he-thumb">
                  </a>
                <?php else: ?>
                  <span class="chu-khong-co">Không có</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
