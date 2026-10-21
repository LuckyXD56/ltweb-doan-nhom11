<?php
/**
 * gioithieu.php — Trang cá nhân Trương Nguyễn Ngọc Phú
 * Sử dụng require khung chung, giữ nguyên giao diện cũ bằng style.css
 * Có xử lý form gửi lời nhắn ở máy chủ (lưu JSONL)
 */

require __DIR__ . '/../../inc/config.php';
require __DIR__ . '/../../inc/ham.php';

// Thiết lập đường dẫn và CSS riêng
$baseUrl = '../../';
$tieuDeTrang = 'Trang cá nhân Trương Nguyễn Ngọc Phú';
$customCss = '<link rel="stylesheet" href="style.css">'; // Nạp lại style.css cũ

// ==========================================
// XỬ LÝ GỬI LỜI NHẮN (SERVER-SIDE)
// ==========================================
$loi = [];
$du = ['ho_ten' => '', 'noi_dung' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $du['ho_ten']   = postChuoi('ho_ten');
    $du['noi_dung'] = postChuoi('noi_dung');

    if ($du['ho_ten'] === '') {
        $loi['ho_ten'] = 'Vui lòng nhập họ tên.';
    }
    if ($du['noi_dung'] === '') {
        $loi['noi_dung'] = 'Vui lòng nhập nội dung.';
    } elseif (mb_strlen($du['noi_dung'], 'UTF-8') < 10) {
        $loi['noi_dung'] = 'Nội dung phải có ít nhất 10 ký tự.';
    }

    if (empty($loi)) {
        $banGhi = [
            'thoi_gian' => date('c'),
            'ho_ten'    => $du['ho_ten'],
            'noi_dung'  => $du['noi_dung'],
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
        ];
        
        $fileLuu = DUONG_DAN_STORAGE . '/loi-nhan-phu.jsonl';
        file_put_contents(
            $fileLuu,
            json_encode($banGhi, JSON_UNESCAPED_UNICODE) . "\n",
            FILE_APPEND | LOCK_EX
        );

        datFlash('thanh-cong', 'Đã gửi lời nhắn. Cảm ơn bạn!');
        chuyenHuong('gioithieu.php');
    }
}

// ==========================================
// ĐỌC LỜI NHẮN GẦN ĐÂY
// ==========================================
$loiNhan = [];
$fileLuu = DUONG_DAN_STORAGE . '/loi-nhan-phu.jsonl';
if (is_file($fileLuu)) {
    $dong = file($fileLuu, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $dong = array_reverse($dong);
    $dong = array_slice($dong, 0, 5);
    foreach ($dong as $line) {
        $obj = json_decode($line, true);
        if (is_array($obj)) {
            $loiNhan[] = $obj;
        }
    }
}

// ==========================================
// HIỂN THỊ HTML (GIỮ NGUYÊN CẤU TRÚC CŨ)
// ==========================================
require __DIR__ . '/../../inc/header.php';
?>

<main class="trang__main">

  <h1 class="tieu-de-trang">Giới thiệu thành viên Nhóm 11</h1>

  <!-- Thanh công cụ: 2 nút -->
  <div class="thanh-cong-cu">
    <button type="button" id="nut-doi-giao-dien" class="nut-cong-cu"
            aria-pressed="false">🌙 Chế độ tối</button>

    <button type="button" id="nut-sao-chep-email" class="nut-cong-cu"
            aria-label="Sao chép địa chỉ email">📧 Sao chép email</button>

    <span id="thong-bao-sao-chep" class="thong-bao-copy" role="status" aria-live="polite"></span>
  </div>

  <!-- Card hồ sơ chính -->
  <article class="the-ho-so">
    <div class="the-ho-so__avatar">
      <img src="avatar.jpg"
           alt="Ảnh chân dung của Trương Nguyễn Ngọc Phú"
           class="the-ho-so__anh">
    </div>
    <div class="the-ho-so__thong-tin">
      <h2 class="the-ho-so__ten">Trương Nguyễn Ngọc Phú</h2>
      <p class="the-ho-so__dong"><strong>Vai trò:</strong> Thành viên nhóm</p>
      <p class="the-ho-so__dong"><strong>Mã sinh viên:</strong> 3120224111</p>
      <p class="the-ho-so__dong">
        <strong>Email:</strong>
        <span id="email-ca-nhan">truongnguyenngocphu@gmail.com</span>
      </p>
      <p class="the-ho-so__dong"><strong>Nhiệm vụ:</strong> Phụ trách giao diện CSS responsive và trang giới thiệu cá nhân.</p>
    </div>
  </article>

  <!-- 2 cột: Giới thiệu + Kỹ năng -->
  <div class="luoi-hai-cot">
    <article class="the-nho">
      <h3 class="the-nho__tieu-de">Giới thiệu bản thân</h3>
      <p class="the-nho__doan">Xin chào, tôi là Trương Nguyễn Ngọc Phú. Tôi sinh ngày 28-04-2006 và hiện đang là sinh viên Công nghệ thông tin tại Trường Đại học Bách khoa - Đại học Đà Nẵng. Tôi là người yêu thích lập trình và đặc biệt quan tâm đến phát triển web.</p>
    </article>

    <article class="the-nho">
      <h3 class="the-nho__tieu-de">Kỹ năng</h3>
      <div class="danh-sach-nhan">
        <span class="nhan">HTML5 &amp; CSS3</span>
        <span class="nhan">JavaScript cơ bản</span>
        <span class="nhan">Git &amp; GitHub</span>
        <span class="nhan">Responsive Design</span>
      </div>
    </article>
  </div>

  <!-- FORM GỬI LỜI NHẮN (Phần thêm mới để đáp ứng yêu cầu của thầy) -->
  <article class="the-nho the-nho--full" style="margin-bottom: 1.5rem;">
    <h3 class="the-nho__tieu-de">📝 Gửi lời nhắn cho tôi</h3>
    <form class="bieu-mau" method="post" action="gioithieu.php" novalidate style="margin-top: 1rem;">
      <label class="truong__nhan" for="ho_ten">Họ và tên (*)</label>
      <input class="truong__nhap" type="text" id="ho_ten" name="ho_ten" value="<?= e($du['ho_ten']) ?>" required>
      <?php if (!empty($loi['ho_ten'])): ?>
        <span style="color: #b91c1c; font-size: 0.9em; display: block; margin-bottom: 0.5rem;"><?= e($loi['ho_ten']) ?></span>
      <?php endif; ?>

      <label class="truong__nhan" for="noi_dung">Nội dung (*)</label>
      <textarea class="truong__nhap" id="noi_dung" name="noi_dung" rows="3" required minlength="10"><?= e($du['noi_dung']) ?></textarea>
      <?php if (!empty($loi['noi_dung'])): ?>
        <span style="color: #b91c1c; font-size: 0.9em; display: block; margin-bottom: 0.5rem;"><?= e($loi['noi_dung']) ?></span>
      <?php endif; ?>

      <button class="nut nut--chinh" type="submit">Gửi lời nhắn</button>
    </form>

    <?php if (count($loiNhan) > 0): ?>
      <div style="margin-top: 1.5rem; border-top: 1px dashed #ccc; padding-top: 1rem;">
        <p style="font-weight: 600; margin-bottom: 0.5rem;">Lời nhắn mới nhất:</p>
        <?php foreach ($loiNhan as $ln): ?>
          <div style="background: var(--mau-nen); padding: 0.75rem 1rem; margin-bottom: 0.5rem; border-left: 4px solid var(--mau-xanh); border-radius: 4px;">
            <p style="margin: 0; font-weight: 600;"><?= e($ln['ho_ten'] ?? 'Ẩn danh') ?></p>
            <p style="margin: 0.25rem 0 0 0; font-size: 0.85em; color: var(--mau-chu-nhat);"><?= date('H:i d/m/Y', strtotime($ln['thoi_gian'] ?? 'now')) ?></p>
            <p style="margin: 0.5rem 0 0 0;"><?= e($ln['noi_dung'] ?? '') ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </article>

  <!-- Thời khóa biểu -->
  <article class="the-nho the-nho--full">
    <h3 class="the-nho__tieu-de">Thời khóa biểu</h3>
    <div class="bang-bao">
      <table class="bang-thoi-khoa-bieu">
        <thead>
          <tr><th>Thứ</th><th>Tiết 1-3</th><th>Tiết 4-6</th></tr>
        </thead>
        <tbody>
          <tr><td>Thứ 2</td><td>Lập trình Web</td><td>Toán rời rạc</td></tr>
          <tr><td>Thứ 3</td><td>Cấu trúc dữ liệu</td><td>Tiếng Anh</td></tr>
          <tr><td>Thứ 4</td><td>Lập trình Web</td><td>Thể dục</td></tr>
          <tr><td>Thứ 5</td><td>Hệ điều hành</td><td>Toán rời rạc</td></tr>
          <tr><td>Thứ 6</td><td>Tiếng Anh</td><td>Tự học</td></tr>
        </tbody>
      </table>
    </div>
  </article>

</main>

<?php
$scriptRieng = '<script src="js/canhan.js"></script>';
require __DIR__ . '/../../inc/footer.php';
?>
