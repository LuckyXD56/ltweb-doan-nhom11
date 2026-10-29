<?php
/**
 * Tệp: thanhvien/3120224060_hoang/gioithieu.php
 * Tác giả: Nguyễn Quý Minh Hoàng (MSSV: 3120224060) - Nhóm 11
 * Mô tả: Trang giới thiệu cá nhân, thời khóa biểu, kỹ năng và các chức năng tích hợp CSDL
 *        (Thống kê lượt xem và sổ lưu bút phân trang có phân quyền xóa phía máy chủ).
 */

session_start();

// Thiết lập đường dẫn tương đối ngược về thư mục gốc của đồ án nhóm
$baseUrl = '../../';

// Nạp kết nối CSDL và các hàm dùng chung
require_once $baseUrl . 'inc/db.php';
require_once $baseUrl . 'inc/ham.php';

$mssv = '3120224060';

if (!function_exists('e')) {
    function e(string $chuoi): string {
        return htmlspecialchars($chuoi, ENT_QUOTES, 'UTF-8');
    }
}

// -------------------------------------------------------------------------
// CHỨC NĂNG 1: THỐNG KÊ LƯỢT XEM THEO NGÀY
// -------------------------------------------------------------------------
if (empty($_SESSION['da_xem_3120224060_db'])) {
    $_SESSION['da_xem_3120224060_db'] = true;
    try {
        $sqlLX = "INSERT INTO tv_luot_xem_ngay (mssv, ngay, so_luot_xem) 
                  VALUES (?, CURDATE(), 1) 
                  ON DUPLICATE KEY UPDATE so_luot_xem = so_luot_xem + 1";
        $stmtLX = db()->prepare($sqlLX);
        $stmtLX->execute([$mssv]);
    } catch (Exception $ex) {}
}

$luotXemHomNay = 0;
$tongLuotXemAll = 0;
$bangLuotXem7Ngay = [];
try {
    $stmtHN = db()->prepare("SELECT so_luot_xem FROM tv_luot_xem_ngay WHERE mssv = ? AND ngay = CURDATE()");
    $stmtHN->execute([$mssv]);
    $luotXemHomNay = (int)($stmtHN->fetchColumn() ?: 0);

    $stmtTong = db()->prepare("SELECT SUM(so_luot_xem) FROM tv_luot_xem_ngay WHERE mssv = ?");
    $stmtTong->execute([$mssv]);
    $tongLuotXemAll = (int)($stmtTong->fetchColumn() ?: 0);

    $stmt7 = db()->prepare("SELECT DATE_FORMAT(ngay, '%d/%m/%Y') AS ngay, so_luot_xem AS luot_xem 
                            FROM tv_luot_xem_ngay WHERE mssv = ? 
                            ORDER BY ngay DESC LIMIT 7");
    $stmt7->execute([$mssv]);
    $bangLuotXem7Ngay = array_reverse($stmt7->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $ex) {}

// Lấy thông tin thành viên từ CSDL
$thongTinThanhVien = ['ho_ten' => 'Nguyễn Quý Minh Hoàng', 'mssv' => $mssv];
try {
    $stmtTV = db()->prepare("SELECT * FROM thanh_vien WHERE mssv = ?");
    $stmtTV->execute([$mssv]);
    $rowTV = $stmtTV->fetch(PDO::FETCH_ASSOC);
    if ($rowTV) $thongTinThanhVien = $rowTV;
} catch (Exception $e) {}

// Phân quyền chủ trang phía máy chủ
$laChuTrang = isset($_SESSION['nguoi_dung_id']) && (($_SESSION['vai_tro'] ?? '') === 'quan_tri' || ($_SESSION['mssv'] ?? '') === $mssv);

// -------------------------------------------------------------------------
// CHỨC NĂNG 2: XỬ LÝ SỔ LƯU BÚT (XÓA & THÊM THEO MÔ HÌNH PRG)
// -------------------------------------------------------------------------
if (isset($_GET['hanh_dong']) && $_GET['hanh_dong'] === 'xoa' && isset($_GET['id'])) {
    if (!$laChuTrang) {
        $_SESSION['flash_loi'] = 'Bạn không có quyền thực hiện thao tác này!';
    } else {
        $idXoa = (int)$_GET['id'];
        $stmtDel = db()->prepare("DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?");
        $stmtDel->execute([$idXoa, $mssv]);
        $_SESSION['flash_thanh_cong'] = 'Đã xóa lời nhắn thành công!';
    }
    header('Location: gioithieu.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $loiNhan = trim($_POST['loi_nhan'] ?? '');

    if ($hoTen === '' || $loiNhan === '') {
        $_SESSION['flash_loi'] = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        try {
            $stmtIns = db()->prepare("INSERT INTO tv_luu_but (mssv, ten_nguoi_gui, noi_dung) VALUES (?, ?, ?)");
            $stmtIns->execute([$mssv, $hoTen, $loiNhan]);
            $_SESSION['flash_thanh_cong'] = 'Gửi lời nhắn thành công!';
        } catch (Exception $e) {
            $_SESSION['flash_loi'] = 'Lỗi hệ thống cơ sở dữ liệu.';
        }
    }
    header('Location: gioithieu.php');
    exit;
}

// Phân trang lưu bút
$soLuongMoiTrang = 5;
$trangHienTai = isset($_GET['trang']) ? max(1, (int)$_GET['trang']) : 1;
$viTriBatDau = ($trangHienTai - 1) * $soLuongMoiTrang;

$tongSoLuuBut = 0;
$danhSachLuuButDB = [];
$tongSoTrang = 1;

try {
    $stmtC = db()->prepare("SELECT COUNT(*) FROM tv_luu_but WHERE mssv = ?");
    $stmtC->execute([$mssv]);
    $tongSoLuuBut = (int)$stmtC->fetchColumn();
    $tongSoTrang = ceil($tongSoLuuBut / $soLuongMoiTrang);

    $stmtLB = db()->prepare("SELECT id, ten_nguoi_gui AS ho_ten, noi_dung, DATE_FORMAT(ngay_gui, '%d/%m/%Y %H:%i') AS thoi_gian 
                             FROM tv_luu_but WHERE mssv = ? 
                             ORDER BY ngay_gui DESC LIMIT ? OFFSET ?");
    $stmtLB->bindValue(1, $mssv, PDO::PARAM_STR);
    $stmtLB->bindValue(2, $soLuongMoiTrang, PDO::PARAM_INT);
    $stmtLB->bindValue(3, $viTriBatDau, PDO::PARAM_INT);
    $stmtLB->execute();
    $danhSachLuuButDB = $stmtLB->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$thongBaoThanhCong = $_SESSION['flash_thanh_cong'] ?? '';
$thongBaoLoi = $_SESSION['flash_loi'] ?? '';
unset($_SESSION['flash_thanh_cong'], $_SESSION['flash_loi']);

// Nạp Header chung của nhóm kèm custom CSS riêng
$tieuDeTrang = 'Giới thiệu thành viên - Nguyễn Quý Minh Hoàng';
$customCss = '<link rel="stylesheet" href="profile.css">';
require_once $baseUrl . 'inc/header.php';
?>

<main class="trang__main">

    <h1 class="tieu-de-trang">Hồ sơ cá nhân - Sân Bóng Thắng Lợi</h1>

    <!-- THANH CÔNG CỤ -->
    <div class="thanh-cong-cu">
      <button type="button" id="nut-doi-giao-dien" class="nut-cong-cu">🌙 Chế độ tối</button>
      <button type="button" id="nut-sao-chep-email" class="nut-cong-cu">📧 Sao chép email</button>
      <span class="nut-cong-cu" style="cursor: default;">👁️ Lượt xem: <strong><?= $luotXemHomNay ?></strong> (Tổng: <strong><?= $tongLuotXemAll ?></strong>)</span>
      <span id="thong-bao-sao-chep" class="thong-bao-copy" role="status" aria-live="polite"></span>
    </div>

    <!-- THẺ HỒ SƠ CHÍNH -->
    <section class="the-ho-so">
      <div>
        <img src="../../images/hoang.jpg" alt="Ảnh Nguyễn Quý Minh Hoàng" class="the-ho-so__anh">
      </div>
      <div>
        <h2 class="the-ho-so__ten"><?= e($thongTinThanhVien['ho_ten'] ?? 'Nguyễn Quý Minh Hoàng') ?></h2>
        <p class="the-ho-so__dong"><strong>Mã sinh viên:</strong> <?= e($thongTinThanhVien['mssv'] ?? $mssv) ?></p>
        <p class="the-ho-so__dong"><strong>Email:</strong> <span id="email-ca-nhan">nguyenquyminhhoang@gmail.com</span></p>
        <p class="the-ho-so__dong"><strong>Nhiệm vụ đồ án:</strong> Phát triển trang tài khoản, đăng nhập &amp; quản lý lịch đặt sân.</p>
        <p class="the-ho-so__dong"><strong>Trạng thái:</strong> 
          <?= $laChuTrang ? '<span style="color: var(--mau-chinh); font-weight: bold;">[Chủ trang - Có quyền quản lý]</span>' : '<span style="color: var(--mau-chu-phu);">[Khách xem trang]</span>' ?>
        </p>
      </div>
    </section>

    <!-- GIỚI THIỆU & KỸ NĂNG (LƯỚI 2 CỘT) -->
    <div class="luoi-hai-cot">
      <article class="the-nho">
        <h3 class="the-nho__tieu-de">📌 Giới thiệu bản thân</h3>
        <p class="the-nho__doan">
          Xin chào! Tôi là sinh viên ngành Công nghệ thông tin tại Trường Đại học Sư phạm – Đại học Đà Nẵng, thành viên Nhóm 11 chuyên thực hiện đồ án hệ thống đặt sân bóng.
        </p>
      </article>

      <article class="the-nho">
        <h3 class="the-nho__tieu-de">⚡ Kỹ năng công nghệ</h3>
        <div class="danh-sach-nhan" style="margin-top: 0.75rem;">
          <span class="nhan">HTML5 / CSS3</span>
          <span class="nhan">JavaScript (ES6)</span>
          <span class="nhan">PHP &amp; PDO</span>
          <span class="nhan">MySQL Database</span>
        </div>
      </article>
    </div>

    <!-- KHỐI 1: THỐNG KÊ LƯỢT XEM 7 NGÀY -->
    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">📊 Thống kê lượt xem trang (7 ngày gần nhất)</h3>
      <div class="bang-bao">
        <table class="bang-thoi-khoa-bieu">
          <thead>
            <tr>
              <?php foreach ($bangLuotXem7Ngay as $row): ?>
                <th style="text-align: center; color: #ffffff !important; background-color: var(--mau-chinh);"><?= e($row['ngay']) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php foreach ($bangLuotXem7Ngay as $row): ?>
                <td style="text-align: center; font-weight: bold; color: var(--mau-chinh); background-color: var(--mau-the);"><?= $row['luot_xem'] ?></td>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    </article>

    <!-- KHỐI 2: SỔ LƯU BÚT -->
    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">📝 Sổ lưu bút trực tuyến</h3>

      <?php if ($thongBaoThanhCong !== ''): ?>
        <p style="color: var(--mau-chinh); background: rgba(16,185,129,0.1); padding: 0.6rem 1rem; border-radius: var(--bo-goc-sm); font-size: 0.9rem;"><?= e($thongBaoThanhCong) ?></p>
      <?php endif; ?>

      <?php if ($thongBaoLoi !== ''): ?>
        <p style="color: #dc2626; background: rgba(220,38,38,0.1); padding: 0.6rem 1rem; border-radius: var(--bo-goc-sm); font-size: 0.9rem;"><?= e($thongBaoLoi) ?></p>
      <?php endif; ?>

      <form action="gioithieu.php" method="POST" style="margin-top: 1rem;">
        <div style="margin-bottom: 0.75rem;">
          <input type="text" name="ho_ten" placeholder="Họ và tên của bạn..." required 
                 style="width: 100%; padding: 0.6rem; border: 1px solid var(--mau-vien); background: var(--mau-the); color: var(--mau-chu); border-radius: var(--bo-goc-sm); font-size: 0.9rem;">
        </div>
        <div style="margin-bottom: 0.75rem;">
          <textarea name="loi_nhan" rows="3" placeholder="Viết lời nhắn cho Hoàng..." required 
                    style="width: 100%; padding: 0.6rem; border: 1px solid var(--mau-vien); background: var(--mau-the); color: var(--mau-chu); border-radius: var(--bo-goc-sm); font-size: 0.9rem; resize: vertical;"></textarea>
        </div>
        <button type="submit" name="gui_luu_but" class="nut-cong-cu" style="background: var(--mau-chinh); color: white; border: none; font-weight: bold;">
          ✉️ Gửi lời nhắn
        </button>
      </form>

      <h4 style="margin: 1.5rem 0 0.75rem 0; color: var(--mau-chu);">Danh sách lời nhắn (Tổng số: <?= $tongSoLuuBut ?>):</h4>
      <?php if (empty($danhSachLuuButDB)): ?>
        <p style="font-size: 0.9rem; color: var(--mau-chu-phu); font-style: italic;">Chưa có lời nhắn nào được gửi.</p>
      <?php else: ?>
        <ul style="list-style: none; padding: 0; margin: 0;">
          <?php foreach ($danhSachLuuButDB as $item): ?>
            <li style="border-bottom: 1px solid var(--mau-vien); padding: 0.75rem 0;">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <strong style="color: var(--mau-chu);"><?= e($item['ho_ten']) ?></strong>
                <span style="font-size: 0.8rem; color: var(--mau-chu-phu);"><?= e($item['thoi_gian']) ?></span>
              </div>
              <p style="margin: 0.3rem 0 0 0; font-size: 0.9rem; color: var(--mau-chu-phu);"><?= e($item['noi_dung']) ?></p>
              <?php if ($laChuTrang): ?>
                <div style="margin-top: 0.4rem; text-align: right;">
                  <a href="gioithieu.php?hanh_dong=xoa&id=<?= $item['id'] ?>" 
                     onclick="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này không?');"
                     style="color: #dc2626; font-size: 0.8rem; text-decoration: none; font-weight: bold;">[❌ Xóa lời nhắn]</a>
                </div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- PHÂN TRANG -->
        <?php if ($tongSoTrang > 1): ?>
          <div style="margin-top: 1rem; text-align: center;">
            <span style="font-size: 0.9rem; margin-right: 5px; color: var(--mau-chu-phu);">Trang:</span>
            <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
              <a href="gioithieu.php?trang=<?= $i ?>" 
                 style="display: inline-block; padding: 4px 10px; margin: 0 2px; border: 1px solid var(--mau-vien); border-radius: 4px; text-decoration: none; font-size: 0.85rem; <?= $i === $trangHienTai ? 'background: var(--mau-chinh); color: white; border-color: var(--mau-chinh);' : 'color: var(--mau-chu); background: var(--mau-the);' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </article>

    <!-- THỜI KHÓA BIỂU -->
    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">📅 Thời khóa biểu cá nhân</h3>
      <div class="bang-bao">
        <table class="bang-thoi-khoa-bieu">
          <caption>Lịch học tập và hoạt động trong tuần</caption>
          <thead>
            <tr>
              <th scope="col" style="color: #ffffff !important; background-color: var(--mau-chinh);">Thứ</th>
              <th scope="col" style="color: #ffffff !important; background-color: var(--mau-chinh);">Nội dung công việc / học tập</th>
              <th scope="col" style="color: #ffffff !important; background-color: var(--mau-chinh);">Thời gian</th>
            </tr>
          </thead>
          <tbody>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Thứ Hai</th><td style="color: var(--mau-chu); background: var(--mau-the);">Học lý thuyết chuyên ngành</td><td style="color: var(--mau-chu); background: var(--mau-the);">07:00 – 11:00</td></tr>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Thứ Ba</th><td style="color: var(--mau-chu); background: var(--mau-the);">Thực hành Lập trình Web</td><td style="color: var(--mau-chu); background: var(--mau-the);">07:00 – 11:00</td></tr>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Thứ Tư</th><td style="color: var(--mau-chu); background: var(--mau-the);">Học cơ sở dữ liệu nâng cao</td><td style="color: var(--mau-chu); background: var(--mau-the);">07:00 – 11:00</td></tr>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Thứ Năm</th><td style="color: var(--mau-chu); background: var(--mau-the);">Họp nhóm đồ án (Nhóm 11)</td><td style="color: var(--mau-chu); background: var(--mau-the);">13:30 – 17:00</td></tr>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Thứ Sáu</th><td style="color: var(--mau-chu); background: var(--mau-the);">Học trên lớp</td><td style="color: var(--mau-chu); background: var(--mau-the);">07:00 – 11:00</td></tr>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Thứ Bảy</th><td style="color: var(--mau-chu); background: var(--mau-the);">Làm việc tại dịch vụ &amp; vận hành POS365</td><td style="color: var(--mau-chu); background: var(--mau-the);">Theo ca</td></tr>
            <tr><th scope="row" style="color: var(--mau-chu); background: var(--mau-the);">Chủ Nhật</th><td style="color: var(--mau-chu); background: var(--mau-the);">Ôn tập &amp; tổng kết tiến độ tuần</td><td style="color: var(--mau-chu); background: var(--mau-the);">19:00 – 21:00</td></tr>
          </tbody>
        </table>
      </div>
    </article>

</main>

<?php
// Nạp script riêng đúng chuẩn W3C thông qua biến của footer chung
$scriptRieng = '<script src="js/canhan.js"></script>';
require_once $baseUrl . 'inc/footer.php';
?>