<?php
/**
 * Tệp: thanhvien/3120224111_phu/gioithieu.php
 * Tác giả: Trương Nguyễn Ngọc Phú (MSSV: 3120224111) — Nhóm 11
 *
 * Hai chức năng xử lý phía máy chủ (PHP Server-side):
 *   1. Chế độ giao diện Sáng / Tối ghi nhớ bằng Cookie (Server-side Dark Mode Cookie):
 *      - Xử lý biểu mẫu POST chuyển đổi chế độ giao diện, thiết lập Cookie 'phu_theme' 30 ngày (HttpOnly, SameSite=Lax).
 *      - Phía máy chủ đọc cookie và gán class giao diện tương ứng vào thẻ body, hoạt động hoàn hảo cả khi tắt JavaScript.
 *   2. Lọc danh sách kỹ năng chuyên môn từ mảng PHP bằng tham số GET (?nhom=...):
 *      - Danh sách kỹ năng lưu trong mảng PHP, lọc theo nhóm (frontend, backend, congcu, mem) qua Whitelist an toàn.
 *      - Hiển thị kết quả lọc động bằng PHP, giữ lại trạng thái active của nút nhóm được chọn.
 *
 * Cách thử:
 *   - Thử chức năng 1: Nhấn nút "Đổi giao diện Sáng / Tối" (gửi POST và máy chủ ghi nhớ trạng thái qua Cookie).
 *   - Thử chức năng 2: Nhấn các thẻ lọc kỹ năng hoặc mở: http://localhost:8000/thanhvien/3120224111_phu/gioithieu.php?nhom=backend
 */

declare(strict_types=1);

$goc = '../../';
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/ham.php';

// =========================================================================
// CHỨC NĂNG 1: XỬ LÝ CHẾ ĐỘ SÁNG / TỐI QUA COOKIE PHÍA MÁY CHỦ (POST + PRG)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doi_giao_dien'])) {
    $themeHienTai = $_COOKIE['phu_theme'] ?? 'sang';
    $themeMoi = ($themeHienTai === 'toi') ? 'sang' : 'toi';

    setcookie('phu_theme', $themeMoi, [
        'expires'  => time() + 30 * 86400, // 30 ngày
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    header('Location: gioithieu.php' . (!empty($_GET['nhom']) ? '?nhom=' . urlencode((string)$_GET['nhom']) : ''));
    exit;
}

$cheDoToi = (isset($_COOKIE['phu_theme']) && $_COOKIE['phu_theme'] === 'toi');

// =========================================================================
// CHỨC NĂNG 2: LỌC DANH SÁCH KỸ NĂNG CHUYÊN MÔN QUA THAM SỐ GET
// =========================================================================
$nhomChon = getChuoi('nhom', 'tat-ca');
$nhomHopLe = ['tat-ca', 'frontend', 'backend', 'congcu', 'mem'];
if (!in_array($nhomChon, $nhomHopLe, true)) {
    $nhomChon = 'tat-ca';
}

$dsKyNang = [
    ['ten' => 'HTML5 & CSS3', 'nhom' => 'frontend', 'nhan' => 'Frontend', 'mucDo' => 'Thành thạo'],
    ['ten' => 'JavaScript (ES6+)', 'nhom' => 'frontend', 'nhan' => 'Frontend', 'mucDo' => 'Khá'],
    ['ten' => 'Responsive Web Design', 'nhom' => 'frontend', 'nhan' => 'Frontend', 'mucDo' => 'Thành thạo'],
    ['ten' => 'Bootstrap 5', 'nhom' => 'frontend', 'nhan' => 'Frontend', 'mucDo' => 'Khá'],
    ['ten' => 'PHP 8 Phía máy chủ', 'nhom' => 'backend', 'nhan' => 'Backend', 'mucDo' => 'Khá'],
    ['ten' => 'Composer & PSR-4 Autoload', 'nhom' => 'backend', 'nhan' => 'Backend', 'mucDo' => 'Khá'],
    ['ten' => 'Quản lý Session & Cookie', 'nhom' => 'backend', 'nhan' => 'Backend', 'mucDo' => 'Thành thạo'],
    ['ten' => 'Kiến trúc MVC & OOP', 'nhom' => 'backend', 'nhan' => 'Backend', 'mucDo' => 'Khá'],
    ['ten' => 'Git & GitHub', 'nhom' => 'congcu', 'nhan' => 'Công cụ', 'mucDo' => 'Thành thạo'],
    ['ten' => 'Visual Studio Code', 'nhom' => 'congcu', 'nhan' => 'Công cụ', 'mucDo' => 'Thành thạo'],
    ['ten' => 'Làm việc nhóm & Giao tiếp', 'nhom' => 'mem', 'nhan' => 'Kỹ năng mềm', 'mucDo' => 'Tốt'],
];

$kyNangLoc = ($nhomChon === 'tat-ca')
    ? $dsKyNang
    : array_filter($dsKyNang, fn(array $kn): bool => $kn['nhom'] === $nhomChon);

// =========================================================================
// BỘ ĐẾM LƯỢT XEM TRANG CÁ NHÂN (1 LẦN / SESSION)
// =========================================================================
$tepDem = DUONG_DAN_STORAGE . '/3120224111_luotxem.txt';
if (!file_exists($tepDem)) {
    file_put_contents($tepDem, '0');
}
$tongLuotXem = (int) file_get_contents($tepDem);
if (empty($_SESSION['da_xem_phu'])) {
    $_SESSION['da_xem_phu'] = true;
    $tongLuotXem++;
    file_put_contents($tepDem, (string) $tongLuotXem);
}

// Thiết lập tiêu đề và CSS riêng
$tieuDeTrang = 'Giới thiệu - Trương Nguyễn Ngọc Phú';
$cssRieng = 'style.css';
require __DIR__ . '/../../inc/header.php';
?>

<div class="<?= $cheDoToi ? 'giao-dien-toi dark-mode' : '' ?>" style="padding-bottom: 2rem;">
  <main class="trang__main" style="max-width: 1000px; margin: 0 auto; padding: 1.5rem 1rem;">

    <h1 class="tieu-de-trang" style="text-align: center; margin-bottom: 1rem;">Trang Cá Nhân — Trương Nguyễn Ngọc Phú</h1>

    <!-- Thanh công cụ máy chủ: Chế độ Sáng / Tối và Lượt xem -->
    <div class="thanh-cong-cu" style="display: flex; justify-content: center; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
      <!-- Biểu mẫu POST chuyển đổi chế độ giao diện -->
      <form method="post" action="gioithieu.php<?= $nhomChon !== 'tat-ca' ? '?nhom=' . urlencode($nhomChon) : '' ?>" style="display: inline;">
        <input type="hidden" name="doi_giao_dien" value="1">
        <button type="submit" class="nut-cong-cu" style="padding: 0.5rem 1rem; border-radius: 999px; cursor: pointer; border: 1px solid var(--mau-vien, #ccc); background: var(--mau-card, #fff); font-weight: 600;">
          <?= $cheDoToi ? '☀️ Chuyển sang Chế độ Sáng' : '🌙 Chuyển sang Chế độ Tối (Cookie)' ?>
        </button>
      </form>

      <span style="font-size: 0.9rem; padding: 0.4rem 0.8rem; background: var(--mau-card, #fff); border-radius: 999px; border: 1px solid var(--mau-vien, #ccc);">
        👁️ Lượt xem trang: <strong><?= $tongLuotXem ?></strong>
      </span>
      <span style="font-size: 0.85rem; color: #059669; font-weight: 500;">
        🍪 Trạng thái Cookie: <strong><?= $cheDoToi ? 'Đang bật Dark Mode' : 'Chế độ Sáng' ?></strong>
      </span>
    </div>

    <!-- Thẻ hồ sơ cá nhân -->
    <article class="the-ho-so" style="margin-bottom: 2rem;">
      <div class="the-ho-so__avatar">
        <img src="avatar.jpg" alt="Ảnh chân dung Trương Nguyễn Ngọc Phú" class="the-ho-so__anh" style="width: 180px; height: 180px; object-fit: cover; border-radius: 50%; border: 4px solid #10b981; margin: 0 auto;">
      </div>
      <div class="the-ho-so__thong-tin">
        <h2 class="the-ho-so__ten" style="font-size: 1.6rem; margin-bottom: 0.5rem;">Trương Nguyễn Ngọc Phú</h2>
        <p class="the-ho-so__dong"><strong>Vai trò:</strong> Thành viên Nhóm 11</p>
        <p class="the-ho-so__dong"><strong>Mã sinh viên:</strong> 3120224111</p>
        <p class="the-ho-so__dong"><strong>Ngành học:</strong> Sư phạm Tin học / Công nghệ thông tin</p>
        <p class="the-ho-so__dong"><strong>Email:</strong> <span id="email-ca-nhan">truongnguyenngocphu@gmail.com</span></p>
        <p class="the-ho-so__dong"><strong>Phụ trách đồ án (Phần A):</strong> Chức năng 1 (Khung trang dùng chung, Autoload PSR-4) &amp; Chức năng 2 (Lọc danh sách sân bóng).</p>
      </div>
    </article>

    <!-- Khối 2 cột: Giới thiệu & Kỹ năng có bộ lọc GET -->
    <div class="luoi-hai-cot" style="margin-bottom: 2rem;">
      <article class="the-nho" style="background: var(--mau-card, #fff); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--mau-vien, #e5e7eb);">
        <h3 class="the-nho__tieu-de" style="font-size: 1.25rem; color: #059669; margin-bottom: 0.75rem;">Giới thiệu bản thân</h3>
        <p class="the-nho__doan">
          Xin chào! Tôi là Trương Nguyễn Ngọc Phú, sinh viên Khoa Toán - Tin, Trường Đại học Sư phạm - Đại học Đà Nẵng.
          Tôi đam mê nghiên cứu công nghệ web, đặc biệt là kiến trúc mã nguồn mở PHP 8 hiện đại, lập trình hướng đối tượng (OOP) và xây dựng giao diện người dùng thân thiện, chuẩn SEO &amp; Accessibility.
        </p>
      </article>

      <!-- CHỨC NĂNG 2: BỘ LỌC KỸ NĂNG BẰNG GET TỪ MÁY CHỦ -->
      <article class="the-nho" style="background: var(--mau-card, #fff); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--mau-vien, #e5e7eb);">
        <h3 class="the-nho__tieu-de" style="font-size: 1.25rem; color: #059669; margin-bottom: 0.5rem;">
          Kỹ năng chuyên môn (Lọc máy chủ PHP)
        </h3>
        <p style="font-size: 0.85rem; color: var(--mau-chu-nhat, #6b7280); margin-bottom: 0.75rem;">
          Chọn chuyên mục để máy chủ PHP lọc danh sách kỹ năng qua tham số GET:
        </p>

        <!-- Bộ nút lọc GET -->
        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 1rem;">
          <?php
          $boLoc = [
              'tat-ca'   => 'Tất cả (' . count($dsKyNang) . ')',
              'frontend' => 'Frontend',
              'backend'  => 'Backend PHP',
              'congcu'   => 'Công cụ',
              'mem'      => 'Kỹ năng mềm',
          ];
          foreach ($boLoc as $ma => $ten):
              $dangChon = ($nhomChon === $ma);
          ?>
            <a href="gioithieu.php?nhom=<?= urlencode($ma) ?>"
               style="padding: 0.3rem 0.65rem; font-size: 0.85rem; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s;
                      <?= $dangChon ? 'background: #059669; color: #fff;' : 'background: var(--mau-vien, #f3f4f6); color: var(--mau-chu, #374151);' ?>">
              <?= e($ten) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Danh sách kỹ năng do PHP render -->
        <div class="danh-sach-nhan" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
          <?php if (empty($kyNangLoc)): ?>
            <p>Không có kỹ năng nào thuộc nhóm này.</p>
          <?php else: ?>
            <?php foreach ($kyNangLoc as $kn): ?>
              <span class="nhan" style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.85rem; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0;">
                <strong><?= e($kn['ten']) ?></strong>
                <small style="opacity: 0.75;">(<?= e($kn['mucDo']) ?>)</small>
              </span>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </article>
    </div>

    <!-- Thời khóa biểu học tập -->
    <article class="the-nho the-nho--full" style="background: var(--mau-card, #fff); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--mau-vien, #e5e7eb); margin-bottom: 2rem;">
      <h3 class="the-nho__tieu-de" style="font-size: 1.25rem; color: #059669; margin-bottom: 1rem;">Thời khóa biểu học phần Học kỳ 1 (2026 – 2027)</h3>
      <div class="bang-bao" style="overflow-x: auto;">
        <table class="bang-thoi-khoa-bieu" style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr style="background: #059669; color: #fff;">
              <th style="padding: 0.75rem 1rem;">Thứ</th>
              <th style="padding: 0.75rem 1rem;">Tiết 1 – 3</th>
              <th style="padding: 0.75rem 1rem;">Tiết 4 – 6</th>
              <th style="padding: 0.75rem 1rem;">Địa điểm / Phòng</th>
            </tr>
          </thead>
          <tbody>
            <tr style="border-bottom: 1px solid var(--mau-vien, #e5e7eb);">
              <td style="padding: 0.75rem 1rem;"><strong>Thứ 2</strong></td>
              <td style="padding: 0.75rem 1rem;">Thiết kế &amp; Lập trình Web</td>
              <td style="padding: 0.75rem 1rem;">Toán rời rạc</td>
              <td style="padding: 0.75rem 1rem;">B3-303</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--mau-vien, #e5e7eb);">
              <td style="padding: 0.75rem 1rem;"><strong>Thứ 3</strong></td>
              <td style="padding: 0.75rem 1rem;">Cơ sở dữ liệu &amp; SQL</td>
              <td style="padding: 0.75rem 1rem;">Khai phá dữ liệu</td>
              <td style="padding: 0.75rem 1rem;">A5-209</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--mau-vien, #e5e7eb);">
              <td style="padding: 0.75rem 1rem;"><strong>Thứ 4</strong></td>
              <td style="padding: 0.75rem 1rem;">Lập trình mạng</td>
              <td style="padding: 0.75rem 1rem;">Lịch sử Đảng</td>
              <td style="padding: 0.75rem 1rem;">A6-502</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--mau-vien, #e5e7eb);">
              <td style="padding: 0.75rem 1rem;"><strong>Thứ 5</strong></td>
              <td style="padding: 0.75rem 1rem;">Công nghệ phần mềm</td>
              <td style="padding: 0.75rem 1rem;">Tự học / Làm đồ án</td>
              <td style="padding: 0.75rem 1rem;">B3-304</td>
            </tr>
            <tr>
              <td style="padding: 0.75rem 1rem;"><strong>Thứ 6</strong></td>
              <td style="padding: 0.75rem 1rem;">An toàn thông tin</td>
              <td style="padding: 0.75rem 1rem;">Thực hành Web Nhóm 11</td>
              <td style="padding: 0.75rem 1rem;">B3-506</td>
            </tr>
          </tbody>
        </table>
      </div>
    </article>

  </main>
</div>

<script src="js/canhan.js"></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>