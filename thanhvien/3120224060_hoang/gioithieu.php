<?php
/**
 * Tệp: thanhvien/3120224060_hoang/gioithieu.php
 * Tác giả: Nguyễn Quý Minh Hoàng (MSSV: 3120224060) - Nhóm 11
 * Chức năng CSDL:
 *   1. Đếm lượt xem theo ngày dùng INSERT ... ON DUPLICATE KEY UPDATE & Thống kê 7 ngày gần nhất.
 *   2. Sổ lưu bút CSDL: Thêm mới, Phân trang (5 item/trang), Chủ trang mới xóa được lời nhắn.
 */

session_start();

// 1. KẾT NỐI CSDL (Sử dụng hàm db() từ file inc/db.php của nhóm)
require_once '../../inc/db.php';

$mssv = '3120224060';

if (!function_exists('e')) {
    function e(string $chuoi): string {
        return htmlspecialchars($chuoi, ENT_QUOTES, 'UTF-8');
    }
}

// -------------------------------------------------------------------------
// CHỨC NĂNG 1: ĐẾM LƯỢT XEM THEO NGÀY TRONG CSDL (INSERT ... ON DUPLICATE KEY)
// -------------------------------------------------------------------------
if (empty($_SESSION['da_xem_3120224060_db'])) {
    $_SESSION['da_xem_3120224060_db'] = true;
    try {
        $sqlLuotXem = "INSERT INTO tv_luot_xem (mssv, ngay_xem, luot_xem) 
                       VALUES (?, CURDATE(), 1) 
                       ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1";
        $stmtLX = db()->prepare($sqlLuotXem);
        $stmtLX->execute([$mssv]);
    } catch (Exception $ex) {
        // Bỏ qua lỗi nếu chưa nâng cấp bảng
    }
}

// Lấy lượt xem hôm nay & tổng lượt xem
$luotXemHomNay = 0;
$tongLuotXemAll = 0;
$bangLuotXem7Ngay = [];
try {
    $stmtHN = db()->prepare("SELECT luot_xem FROM tv_luot_xem WHERE mssv = ? AND ngay_xem = CURDATE()");
    $stmtHN->execute([$mssv]);
    $luotXemHomNay = (int)($stmtHN->fetchColumn() ?: 0);

    $stmtTong = db()->prepare("SELECT SUM(luot_xem) FROM tv_luot_xem WHERE mssv = ?");
    $stmtTong->execute([$mssv]);
    $tongLuotXemAll = (int)($stmtTong->fetchColumn() ?: 0);

    // Lấy thống kê 7 ngày gần nhất
    $stmt7 = db()->prepare("SELECT DATE_FORMAT(ngay_xem, '%d/%m/%Y') AS ngay, luot_xem 
                            FROM tv_luot_xem WHERE mssv = ? 
                            ORDER BY ngay_xem DESC LIMIT 7");
    $stmt7->execute([$mssv]);
    $bangLuotXem7Ngay = array_reverse($stmt7->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $ex) {}

// -------------------------------------------------------------------------
// KIỂM TRA QUYỀN CHỦ TRANG (Dùng session tài khoản quản trị/chủ trang)
// -------------------------------------------------------------------------
$laChuTrang = isset($_SESSION['user']) && ($_SESSION['user']['vai_tro'] === 'quan_tri' || ($_SESSION['user']['mssv'] ?? '') === $mssv);

// -------------------------------------------------------------------------
// CHỨC NĂNG 2: XỬ LÝ LƯU BÚT (THÊM & XÓA & PHÂN TRANG)
// -------------------------------------------------------------------------

// A. Xử lý Xóa lưu bút (Chỉ chủ trang mới thực hiện được)
if (isset($_GET['hanh_dong']) && $_GET['hanh_dong'] === 'xoa' && isset($_GET['id'])) {
    if (!$laChuTrang) {
        $_SESSION['flash_loi'] = 'Bạn phải là chủ trang mới có quyền xóa lời nhắn!';
    } else {
        $idXoa = (int)$_GET['id'];
        $stmtDelete = db()->prepare("DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?");
        $stmtDelete->execute([$idXoa, $mssv]);
        $_SESSION['flash_thanh_cong'] = 'Đã xóa lời nhắn thành công!';
    }
    header('Location: gioithieu.php');
    exit;
}

// B. Xử lý Thêm lưu bút mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gui_luu_but'])) {
    $hoTen = trim($_POST['ho_ten'] ?? '');
    $loiNhan = trim($_POST['loi_nhan'] ?? '');

    if ($hoTen === '' || $loiNhan === '') {
        $_SESSION['flash_loi'] = 'Vui lòng nhập đầy đủ Họ tên và Lời nhắn!';
    } elseif (mb_strlen($hoTen) > 50) {
        $_SESSION['flash_loi'] = 'Họ tên không được vượt quá 50 ký tự!';
    } elseif (mb_strlen($loiNhan) > 300) {
        $_SESSION['flash_loi'] = 'Lời nhắn không được vượt quá 300 ký tự!';
    } else {
        try {
            $stmtInsert = db()->prepare("INSERT INTO tv_luu_but (mssv, ten_nguoi_gui, noi_dung) VALUES (?, ?, ?)");
            $stmtInsert->execute([$mssv, $hoTen, $loiNhan]);
            $_SESSION['flash_thanh_cong'] = 'Gửi lưu bút thành công!';
        } catch (Exception $e) {
            $_SESSION['flash_loi'] = 'Lỗi CSDL: ' . $e->getMessage();
        }
    }
    header('Location: gioithieu.php');
    exit;
}

// C. Phân trang Sổ lưu bút (5 lời nhắn/trang)
$soLuongMoiTrang = 5;
$trangHienTai = isset($_GET['trang']) ? max(1, (int)$_GET['trang']) : 1;
$viTriBatDau = ($trangHienTai - 1) * $soLuongMoiTrang;

$tongSoLuuBut = 0;
$danhSachLuuButDB = [];
$tongSoTrang = 1;

try {
    // Đếm tổng số dòng
    $stmtCount = db()->prepare("SELECT COUNT(*) FROM tv_luu_but WHERE mssv = ?");
    $stmtCount->execute([$mssv]);
    $tongSoLuuBut = (int)$stmtCount->fetchColumn();
    $tongSoTrang = ceil($tongSoLuuBut / $soLuongMoiTrang);

    // Lấy dữ liệu cho trang hiện tại
    $stmtLB = db()->prepare("SELECT id, ten_nguoi_gui AS ho_ten, noi_dung, DATE_FORMAT(ngay_gui, '%d/%m/%Y %H:%i') AS thoi_gian 
                             FROM tv_luu_but WHERE mssv = ? 
                             ORDER BY ngay_gui DESC LIMIT :limit OFFSET :offset");
    $stmtLB->bindValue(':limit', $soLuongMoiTrang, PDO::PARAM_INT);
    $stmtLB->bindValue(':offset', $viTriBatDau, PDO::PARAM_INT);
    $stmtLB->execute([$mssv]);
    $danhSachLuuButDB = $stmtLB->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

// Thông báo flash
$thongBaoThanhCong = $_SESSION['flash_thanh_cong'] ?? '';
$thongBaoLoi = $_SESSION['flash_loi'] ?? '';
unset($_SESSION['flash_thanh_cong'], $_SESSION['flash_loi']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giới thiệu bản thân - Nguyễn Quý Minh Hoàng</title>
  <link rel="stylesheet" href="../../css/chung.css">
  <link rel="stylesheet" href="profile.css">
</head>
<body class="trang">

  <nav class="trang__nav">
    <ul class="menu__danh-sach">
      <li class="menu__muc"><a class="menu__lien-ket" href="../../index.php">Trang chủ</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../danh-sach.php">Danh sách sân</a></li>
      <li class="menu__muc"><a class="menu__lien-ket active" href="../../thanh-vien.php">Giới thiệu nhóm</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../lien-he.php">Liên hệ / Đặt sân</a></li>
    </ul>
  </nav>

  <main class="trang__main">

    <h1 class="tieu-de-trang">Giới thiệu thành viên Nhóm 11</h1>

    <div class="thanh-cong-cu">
      <button type="button" id="nut-doi-giao-dien" class="nut-cong-cu">🌙 Chế độ tối</button>
      <button type="button" id="nut-sao-chep-email" class="nut-cong-cu">📧 Sao chép email</button>
      <span class="nut-cong-cu" style="cursor: default;">👁️ Hôm nay: <strong><?= $luotXemHomNay ?></strong> | Tổng: <strong><?= $tongLuotXemAll ?></strong></span>
    </div>

    <article class="the-ho-so">
      <div class="the-ho-so__avatar">
        <img src="../../images/hoang.jpg" alt="Nguyễn Quý Minh Hoàng" class="the-ho-so__anh">
      </div>
      <div class="the-ho-so__thong-tin">
        <h2 class="the-ho-so__ten">Nguyễn Quý Minh Hoàng</h2>
        <p class="the-ho-so__dong"><strong>Vai trò:</strong> Thành viên nhóm</p>
        <p class="the-ho-so__dong"><strong>Mã sinh viên:</strong> 3120224060</p>
        <p class="the-ho-so__dong"><strong>Email:</strong> nguyenquyminhhoang@gmail.com</p>
        <p class="the-ho-so__dong"><strong>Quyền xem trang:</strong> 
          <?= $laChuTrang ? '<span style="color: green; font-weight: bold;">[Chủ trang - Đã đăng nhập]</span>' : '<span style="color: gray;">[Khách xem trang]</span>' ?>
        </p>
      </div>
    </article>

    <!-- KHỐI 1: THỐNG KÊ LƯỢT XEM 7 NGÀY GẦN NHẤT (CSDL) -->
    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">📊 Thống kê lượt xem 7 ngày gần nhất (CSDL)</h3>
      <div class="bang-bao">
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
          <thead>
            <tr style="background: #f3f4f6;">
              <?php foreach ($bangLuotXem7Ngay as $row): ?>
                <th style="padding: 8px; border: 1px solid #ddd;"><?= e($row['ngay']) ?></th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php foreach ($bangLuotXem7Ngay as $row): ?>
                <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #0284c7;"><?= $row['luot_xem'] ?></td>
              <?php endforeach; ?>
            </tr>
          </tbody>
        </table>
      </div>
    </article>

    <!-- KHỐI 2: SỔ LƯU BÚT CSDL (PHÂN TRANG & XÓA) -->
    <article class="the-nho the-nho--full">
      <h3 class="the-nho__tieu-de">📝 Sổ lưu bút (CSDL - Phân trang & Chủ trang xóa)</h3>
      
      <?php if ($thongBaoThanhCong !== ''): ?>
        <p style="color: #0f5b38; background: #ecfdf5; padding: 0.6rem; border-radius: 8px;"><?= e($thongBaoThanhCong) ?></p>
      <?php endif; ?>
      <?php if ($thongBaoLoi !== ''): ?>
        <p style="color: #dc2626; background: #fef2f2; padding: 0.6rem; border-radius: 8px;"><?= e($thongBaoLoi) ?></p>
      <?php endif; ?>

      <!-- Form gửi -->
      <form action="gioithieu.php" method="POST" style="margin-bottom: 1.5rem;">
        <div style="margin-bottom: 0.75rem;">
          <input type="text" name="ho_ten" placeholder="Họ và tên của bạn..." required 
                 style="width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 8px;">
        </div>
        <div style="margin-bottom: 0.75rem;">
          <textarea name="loi_nhan" rows="3" placeholder="Để lại lời nhắn cho Hoàng..." required 
                    style="width: 100%; padding: 0.6rem; border: 1px solid #ccc; border-radius: 8px;"></textarea>
        </div>
        <button type="submit" name="gui_luu_but" class="nut-cong-cu" style="background-color: #0284c7; color: #fff; border: none;">✉️ Gửi lời nhắn</button>
      </form>

      <!-- Danh sách lời nhắn -->
      <h4 style="margin: 1rem 0 0.5rem 0; color: #0284c7;">Lời nhắn (Tổng số: <?= $tongSoLuuBut ?>):</h4>
      <?php if (empty($danhSachLuuButDB)): ?>
        <p style="color: #666;">Chưa có lời nhắn nào.</p>
      <?php else: ?>
        <ul style="list-style: none; padding: 0;">
          <?php foreach ($danhSachLuuButDB as $item): ?>
            <li style="border-bottom: 1px solid #eee; padding: 0.75rem 0;">
              <strong style="color: #333;"><?= e($item['ho_ten']) ?></strong>
              <small style="color: #888; margin-left: 10px;"><?= e($item['thoi_gian']) ?></small>
              
              <!-- Nút Xóa (Chỉ hiện khi chủ trang đã đăng nhập) -->
              <?php if ($laChuTrang): ?>
                <a href="gioithieu.php?hanh_dong=xoa&id=<?= $item['id'] ?>" 
                   onclick="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này?');"
                   style="color: red; float: right; font-size: 0.85rem; text-decoration: none; font-weight: bold;">[❌ Xóa]</a>
              <?php endif; ?>

              <p style="margin: 0.4rem 0 0 0; color: #555;"><?= e($item['noi_dung']) ?></p>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- PHÂN TRANG -->
        <?php if ($tongSoTrang > 1): ?>
          <div style="margin-top: 1rem; text-align: center;">
            <span style="margin-right: 10px;">Trang:</span>
            <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
              <a href="gioithieu.php?trang=<?= $i ?>" 
                 style="padding: 5px 10px; margin: 0 2px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; <?= $i === $trangHienTai ? 'background: #0284c7; color: white;' : 'color: #333;' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </article>

  </main>

  <footer class="trang__footer">
    <p>&copy; 2026 Nguyễn Quý Minh Hoàng – Nhóm 11, Khoa Toán Tin, ĐH Sư phạm Đà Nẵng.</p>
  </footer>

</body>
</html>