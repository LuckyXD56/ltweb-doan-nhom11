<?php
/**
 * Tệp: thanhvien/3120224060_hoang/gioithieu.php
 * Tác giả: Nguyễn Quý Minh Hoàng (MSSV: 3120224060) - Nhóm 11
 * Mô tả: Trang giới thiệu cá nhân, thời khóa biểu, kỹ năng và các chức năng tích hợp CSDL
 *        (Thống kê lượt xem và sổ lưu bút phân trang có phân quyền xóa phía máy chủ).
 */

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/ham.php';

$baseUrl = '../../';
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
        $sqlLX = "INSERT INTO tv_luot_xem (mssv, ngay, luot_xem) 
                  VALUES (?, CURDATE(), 1) 
                  ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1";
        $stmtLX = db()->prepare($sqlLX);
        $stmtLX->execute([$mssv]);
    } catch (Exception $ex) {}
}

$luotXemHomNay = 0;
$tongLuotXemAll = 0;
$bangLuotXem7Ngay = [];
try {
    $stmtHN = db()->prepare("SELECT luot_xem FROM tv_luot_xem WHERE mssv = ? AND ngay = CURDATE()");
    $stmtHN->execute([$mssv]);
    $luotXemHomNay = (int)($stmtHN->fetchColumn() ?: 0);

    $stmtTong = db()->prepare("SELECT SUM(luot_xem) FROM tv_luot_xem WHERE mssv = ?");
    $stmtTong->execute([$mssv]);
    $tongLuotXemAll = (int)($stmtTong->fetchColumn() ?: 0);

    $stmt7 = db()->prepare("SELECT DATE_FORMAT(ngay, '%d/%m/%Y') AS ngay, luot_xem AS luot_xem 
                            FROM tv_luot_xem WHERE mssv = ? 
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
$laChuTrang = (isset($_SESSION['nguoi_dung_id']) || isset($_SESSION['uid'])) && (($_SESSION['vai_tro'] ?? '') === 'quan_tri' || ($_SESSION['mssv'] ?? '') === $mssv);

// -------------------------------------------------------------------------
// CHỨC NĂNG 2: XỬ LÝ SỔ LƯU BÚT (XÓA & THÊM THEO MÔ HÌNH PRG VÀ POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hanh_dong']) && $_POST['hanh_dong'] === 'xoa') {
    kiemTraCsrf();
    if (!$laChuTrang) {
        $_SESSION['flash_loi'] = 'Bạn không có quyền thực hiện thao tác này!';
    } else {
        $idXoa = (int)($_POST['id'] ?? 0);
        $stmtDel = db()->prepare("DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?");
        $stmtDel->execute([$idXoa, $mssv]);
        $_SESSION['flash_thanh_cong'] = 'Đã xóa lời nhắn thành công!';
    }
    header('Location: gioithieu.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    kiemTraCsrf();
    $hoTen = trim(chuoi($_POST['ho_ten'] ?? ''));
    $loiNhan = trim(chuoi($_POST['loi_nhan'] ?? ''));

    if ($hoTen === '' || $loiNhan === '') {
        $_SESSION['flash_loi'] = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        try {
            $stmtIns = db()->prepare("INSERT INTO tv_luu_but (mssv, ho_ten, loi_nhan) VALUES (?, ?, ?)");
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

    $stmtLB = db()->prepare("SELECT id, ho_ten, loi_nhan AS noi_dung, DATE_FORMAT(ngay_tao, '%d/%m/%Y %H:%i') AS thoi_gian 
                             FROM tv_luu_but WHERE mssv = ? 
                             ORDER BY ngay_tao DESC LIMIT ? OFFSET ?");
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
require_once __DIR__ . '/../../inc/header.php';
?>

<main class="trang__main">

    <h1 class="tieu-de-trang">Hồ sơ cá nhân - Sân Bóng Thắng Lợi</h1>

    <!-- THANH CÔNG CỤ -->
    <div class="thanh-cong-cu">
      <button type="button" id="nut-doi-giao-dien" class="nut-cong-cu">🌙 Chế độ tối</button>
      <button type="button" id="nut-sao-chep-email" class="nut-cong-cu">📧 Sao chép email</button>
      <span class="nut-cong-cu h-s7" >👁️ Lượt xem: <strong><?= $luotXemHomNay ?></strong> (Tổng: <strong><?= $tongLuotXemAll ?></strong>)</span>
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
          <?= $laChuTrang ? '<span class="chu-chinh dam">[Chủ trang - Có quyền quản lý]</span>' : '<span class="chu-phu">[Khách xem trang]</span>' ?>
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
        <div class="danh-sach-nhan h-s10" >
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
                <th class="h-s4"><?= e($row['ngay']) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php foreach ($bangLuotXem7Ngay as $row): ?>
                <td class="h-s6"><?= $row['luot_xem'] ?></td>
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
        <p class="h-s1"><?= e($thongBaoThanhCong) ?></p>
      <?php endif; ?>

      <?php if ($thongBaoLoi !== ''): ?>
        <p class="h-s21"><?= e($thongBaoLoi) ?></p>
      <?php endif; ?>

      <form action="gioithieu.php" method="POST" class="h-s2">
        <?= csrfField() ?>
        <div class="h-s20">
          <input type="text" name="ho_ten" placeholder="Họ và tên của bạn..." required 
                 class="h-s3">
        </div>
        <div class="h-s20">
          <textarea name="loi_nhan" rows="3" placeholder="Viết lời nhắn cho Hoàng..." required 
                    class="h-s18"></textarea>
        </div>
        <button type="submit" name="gui_luu_but" class="nut-cong-cu h-s19" >
          ✉️ Gửi lời nhắn
        </button>
      </form>

      <h4 class="h-s5">Danh sách lời nhắn (Tổng số: <?= $tongSoLuuBut ?>):</h4>
      <?php if (empty($danhSachLuuButDB)): ?>
        <p class="h-s15">Chưa có lời nhắn nào được gửi.</p>
      <?php else: ?>
        <ul class="h-s17">
          <?php foreach ($danhSachLuuButDB as $item): ?>
            <li class="h-s14">
              <div class="h-s8">
                <strong class="chu-dam"><?= e($item['ho_ten']) ?></strong>
                <span class="chu-nho chu-phu"><?= e($item['thoi_gian']) ?></span>
              </div>
              <p class="noi-dung-luu-but"><?= e($item['noi_dung']) ?></p>
              <?php if ($laChuTrang): ?>
                <div class="vung-xoa-luu-but">
                  <form method="post" action="gioithieu.php" class="form-xoa-tin-nhan" data-xac-nhan="Bạn có chắc chắn muốn xóa lời nhắn này không?">
                    <?= csrfField() ?>
                    <input type="hidden" name="hanh_dong" value="xoa">
                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                    <button type="submit" class="h-s9">[❌ Xóa lời nhắn]</button>
                  </form>
                </div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- PHÂN TRANG -->
        <?php if ($tongSoTrang > 1): ?>
          <div class="h-s12">
            <span class="h-s11">Trang:</span>
            <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
              <a href="gioithieu.php?trang=<?= $i ?>" 
                 class="link-trang-h <?= $i === $trangHienTai ? 'link-trang-h--active' : '' ?>">
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
              <th scope="col" class="h-s16">Thứ</th>
              <th scope="col" class="h-s16">Nội dung công việc / học tập</th>
              <th scope="col" class="h-s16">Thời gian</th>
            </tr>
          </thead>
          <tbody>
            <tr><th scope="row" class="h-s13">Thứ Hai</th><td class="h-s13">Học lý thuyết chuyên ngành</td><td class="h-s13">07:00 – 11:00</td></tr>
            <tr><th scope="row" class="h-s13">Thứ Ba</th><td class="h-s13">Thực hành Lập trình Web</td><td class="h-s13">07:00 – 11:00</td></tr>
            <tr><th scope="row" class="h-s13">Thứ Tư</th><td class="h-s13">Học cơ sở dữ liệu nâng cao</td><td class="h-s13">07:00 – 11:00</td></tr>
            <tr><th scope="row" class="h-s13">Thứ Năm</th><td class="h-s13">Họp nhóm đồ án (Nhóm 11)</td><td class="h-s13">13:30 – 17:00</td></tr>
            <tr><th scope="row" class="h-s13">Thứ Sáu</th><td class="h-s13">Học trên lớp</td><td class="h-s13">07:00 – 11:00</td></tr>
            <tr><th scope="row" class="h-s13">Thứ Bảy</th><td class="h-s13">Làm việc tại dịch vụ &amp; vận hành POS365</td><td class="h-s13">Theo ca</td></tr>
            <tr><th scope="row" class="h-s13">Chủ Nhật</th><td class="h-s13">Ôn tập &amp; tổng kết tiến độ tuần</td><td class="h-s13">19:00 – 21:00</td></tr>
          </tbody>
        </table>
      </div>
    </article>

</main>

<?php
// Nạp script riêng đúng chuẩn W3C thông qua biến của footer chung
$scriptRieng = '<script src="js/canhan.js"></script>';
require_once __DIR__ . '/../../inc/footer.php';
?>