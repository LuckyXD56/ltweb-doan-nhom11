<?php
/*
 * Trang cá nhân - Bài tập nhóm 5.
 * Sử dụng header và footer dùng chung.
 * Chức năng: sổ lưu bút và tính điểm học phần.
 * Kiểm thử bằng trình duyệt trên localhost.
 */

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/ham.php';

$goc = '../../';
$tieuDeTrang = 'Lê Nguyễn Gia Nhân';
$trang = 'thanh-vien';
$cssRieng = 'css/canhan.css';

// --- XỬ LÝ SỔ LƯU BÚT ---
$fileLuubut = DUONG_DAN_STORAGE . '/3120224105_luubut.jsonl';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && is_string($_POST['action']) && $_POST['action'] === 'luubut') {
    $ten = $_POST['ten'] ?? '';
    $loi_nhan = $_POST['loi_nhan'] ?? '';

    if (is_array($ten) || is_array($loi_nhan)) {
        datFlash('danger', 'Dữ liệu không hợp lệ.');
    } else {
        $ten = trim((string)$ten);
        $loi_nhan = trim((string)$loi_nhan);

        if (
            $ten === '' ||
            mb_strlen($ten) > 100 ||
            $loi_nhan === '' ||
            mb_strlen($loi_nhan) > 500
        ) {
            datFlash(
                'danger',
                'Tên tối đa 100 ký tự, lời nhắn tối đa 500 ký tự.'
            );
        } else {
            $entry = json_encode([
                'ten' => $ten,
                'loi_nhan' => $loi_nhan,
                'thoi_gian' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE) . PHP_EOL;
            if (file_put_contents($fileLuubut, $entry, FILE_APPEND | LOCK_EX) !== false) {
                datFlash('success', 'Đã lưu lời nhắn của bạn.');
            } else {
                datFlash('danger', 'Lỗi hệ thống: Không thể lưu lời nhắn.');
            }
        }
    }
    if (!headers_sent()) {
        chuyenHuong('gioithieu.php');
    } else {
        echo "<script>window.location.href='gioithieu.php';</script>";
        exit;
    }
}

// Đọc sổ lưu bút
$dsLuubut = [];
if (file_exists($fileLuubut)) {
    $lines = file($fileLuubut, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data) {
            $dsLuubut[] = $data;
        }
    }
}
$dsLuubut = array_reverse($dsLuubut);
$dsLuubut = array_slice($dsLuubut, 0, 5);

// --- XỬ LÝ MÁY TÍNH ĐIỂM ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && is_string($_POST['action']) && $_POST['action'] === 'tinhdiem') {
    $a1 = $_POST['a1'] ?? '';
    $a2 = $_POST['a2'] ?? '';
    $a3 = $_POST['a3'] ?? '';

    if (is_array($a1) || is_array($a2) || is_array($a3)) {
        datFlash('danger', 'Dữ liệu không hợp lệ.');
    } else {
        $a1 = trim((string)$a1);
        $a2 = trim((string)$a2);
        $a3 = trim((string)$a3);

        if ($a1 === '' || $a2 === '' || $a3 === '') {
            datFlash('danger', 'Vui lòng nhập đủ 3 cột điểm.');
        } elseif (!is_numeric($a1) || !is_numeric($a2) || !is_numeric($a3) || $a1 < 0 || $a1 > 10 || $a2 < 0 || $a2 > 10 || $a3 < 0 || $a3 > 10) {
            datFlash('danger', 'Điểm phải là số từ 0 đến 10.');
        } else {
            $diem_tb = 0.2 * (float)$a1 + 0.3 * (float)$a2 + 0.5 * (float)$a3;
            datFlash('success', 'Điểm học phần của bạn là: ' . number_format($diem_tb, 2, ',', '.'));
        }
    }
    if (!headers_sent()) {
        chuyenHuong('gioithieu.php');
    } else {
        echo "<script>window.location.href='gioithieu.php';</script>";
        exit;
    }
}

$flash = layFlash();

require __DIR__ . '/../../inc/header.php';
?>



<div class="theme-control">
    <button
        type="button"
        id="theme-toggle"
        aria-pressed="false">
        🌙 Chế độ tối
    </button>
</div>

<!-- ================= MAIN ================= -->
    <main class="page-shell content">

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['loai']) ?>" style="padding:15px; margin-bottom:20px; border-radius:5px; background:<?= $flash['loai'] === 'danger' ? '#f8d7da' : '#d4edda' ?>; color:<?= $flash['loai'] === 'danger' ? '#721c24' : '#155724' ?>; border: 1px solid <?= $flash['loai'] === 'danger' ? '#f5c6cb' : '#c3e6cb' ?>;">
                <?= e($flash['noiDung']) ?>
            </div>
        <?php endif; ?>

        <h1>
            Giới thiệu thành viên Lê Nguyễn Gia Nhân
        </h1>


        <!-- ================= PROFILE ================= -->
        <section
            class="profile-card"
            aria-labelledby="profile-heading">

            <!-- ẢNH CÁ NHÂN -->
            <figure class="profile-figure">

                <img
                    class="avatar"
                    src="images/nhan.jpg"
                    alt="Ảnh chân dung của Lê Nguyễn Gia Nhân"
                    width="300"
                    height="300">

                <figcaption>
                    Lê Nguyễn Gia Nhân
                </figcaption>

            </figure>


            <!-- THÔNG TIN CHÍNH -->
            <div class="profile-meta">

                <span class="label">
                    Thành viên Nhóm 11
                </span>

                <h2 id="profile-heading">
                    Lê Nguyễn Gia Nhân
                </h2>

                <p>
                    <strong>Mã sinh viên:</strong>
                    3120224105
                </p>

                <p>
                    <strong>Vai trò:</strong>
                    Thành viên nhóm
                </p>

                <p>
                    <strong>Nhiệm vụ:</strong>
                    Tham gia xây dựng giao diện,
                    phân tích cấu trúc HTML,
                    thiết kế CSS responsive
                    và trả lời câu hỏi thảo luận.
                </p>

            </div>

        </section>


        <!-- ================= THÔNG TIN + KỸ NĂNG ================= -->
        <div class="info-grid">

            <!-- THÔNG TIN CÁ NHÂN -->
            <article class="info-box">

                <h2>Thông tin cá nhân</h2>

                <ul class="personal-list">

                    <li>
                        <strong>Họ và tên:</strong>
                        Lê Nguyễn Gia Nhân
                    </li>

                    <li>
                        <strong>MSSV:</strong>
                        3120224105
                    </li>

                    <li>
                        <strong>Khoa:</strong>
                        Toán - Tin
                    </li>

                    <li>
                        <strong>Chuyên ngành:</strong>
                        Công nghệ thông tin
                    </li>

                </ul>

            </article>


            <!-- KỸ NĂNG -->
            <article class="info-box">

                <h2>Kỹ năng</h2>

                <div class="skill-search">
                    <label for="skill-filter">
                        Tìm kiếm kỹ năng:
                    </label>

                    <input
                        type="search"
                        id="skill-filter"
                        placeholder="Ví dụ: HTML, CSS, Git..."
                        autocomplete="off">
                </div>

                <ul class="skill-list" id="skill-list">
                    <li>HTML5 Semantic</li>
                    <li>CSS3 Responsive</li>
                    <li>JavaScript</li>
                    <li>DevTools</li>
                    <li>W3C Validator</li>
                    <li>Git &amp; GitHub</li>
                </ul>

                <p
                    id="skill-message"
                    class="skill-message"
                    aria-live="polite">
                </p>

            </article>

        </div> <!-- Đã thêm thẻ đóng </div> cho .info-grid -->


        <!-- ================= SỞ THÍCH ================= -->
        <article class="full-box">

            <h2>Sở thích và mục tiêu</h2>

            <p>
                Nhân yêu thích bóng đá, đọc sách công nghệ
                và tìm hiểu về phát triển website. Bên cạnh đó,
                Nhân quan tâm đến thiết kế giao diện responsive
                và khả năng truy cập web (Accessibility).
            </p>

            <p>
                Mục tiêu của Nhân là nâng cao kỹ năng HTML,
                CSS, JavaScript và GitHub, đồng thời áp dụng
                những kiến thức đã học vào các dự án website
                thực tế.
            </p>

        </article>


        <!-- ================= THỜI KHÓA BIỂU ================= -->
        <section
            class="full-box schedule-section"
            aria-labelledby="schedule-heading">

            <h2 id="schedule-heading">
                Thời khóa biểu tuần
            </h2>

            <div
                class="table-wrapper"
                tabindex="0"
                aria-label="Bảng thời khóa biểu có thể cuộn ngang">

                <table class="schedule-table">

                    <caption>
                        Lịch học và hoạt động trong tuần
                        của Lê Nguyễn Gia Nhân
                    </caption>

                    <thead>
                        <tr>
                            <th scope="col">Ngày</th>
                            <th scope="col">Hoạt động</th>
                            <th scope="col">Thời gian</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <th scope="row">Thứ Hai</th>
                            <td>Học tập trên lớp</td>
                            <td>07:00 - 11:00</td>
                        </tr>

                        <tr>
                            <th scope="row">Thứ Sáu</th>
                            <td>Phân tích và kiểm tra website</td>
                            <td>13:30 - 17:00</td>
                        </tr>

                        <tr>
                            <th scope="row">Chủ Nhật</th>
                            <td>Họp nhóm và ôn tập</td>
                            <td>19:00 - 21:00</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </section>
        <!-- CHỨC NĂNG 2: MÁY TÍNH ĐIỂM HỌC PHẦN -->
        <section class="full-box grade-section" id="tinh-diem">
            <h2>Máy tính điểm học phần</h2>

            <p class="grade-description">
                Nhập điểm A1, A2, A3 từ 0 đến 10 để tính điểm tổng kết.
            </p>

            <form action="gioithieu.php" method="POST" class="grade-form">
                <input type="hidden" name="action" value="tinhdiem">

                <div class="grade-grid">
                    <div class="grade-field">
                        <label for="a1">Điểm A1 (20%)</label>
                        <input type="number" name="a1" id="a1"
                               min="0" max="10" step="0.1"
                               placeholder="Ví dụ: 8" required>
                    </div>

                    <div class="grade-field">
                        <label for="a2">Điểm A2 (30%)</label>
                        <input type="number" name="a2" id="a2"
                               min="0" max="10" step="0.1"
                               placeholder="Ví dụ: 7" required>
                    </div>

                    <div class="grade-field">
                        <label for="a3">Điểm A3 (50%)</label>
                        <input type="number" name="a3" id="a3"
                               min="0" max="10" step="0.1"
                               placeholder="Ví dụ: 9" required>
                    </div>
                </div>

                <button type="submit" class="grade-submit">
                    Tính điểm học phần
                </button>
            </form>

            <p class="grade-formula">
                Công thức: 0,2 × A1 + 0,3 × A2 + 0,5 × A3
            </p>
        </section>

        <!-- ================= SỔ LƯU BÚT ================= -->
        <section class="full-box guestbook-section" id="so-luu-but">
            <h2>Sổ lưu bút</h2>
            <p class="guestbook-description">
                Hãy để lại lời nhắn, góp ý hoặc lời chúc của bạn!
            </p>
            <form action="gioithieu.php" method="POST" class="guestbook-form">
                <input type="hidden" name="action" value="luubut">
                <div class="guestbook-field">
                    <label for="ten">Họ và tên</label>
                    <input type="text" name="ten" id="ten" maxlength="100" placeholder="Nhập họ và tên của bạn" required>
                </div>
                <div class="guestbook-field">
                    <label for="loi_nhan">Lời nhắn</label>
                    <textarea name="loi_nhan" id="loi_nhan" rows="5" maxlength="500" placeholder="Viết lời nhắn của bạn tại đây..." required></textarea>
                </div>
                <button type="submit" class="guestbook-submit">Gửi lời nhắn</button>
            </form>
            <div class="guestbook-history">
                <h3>5 lời nhắn mới nhất</h3>
                <?php if (empty($dsLuubut)): ?>
                    <p>Chưa có lời nhắn nào.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($dsLuubut as $lb): ?>
                            <li>
                                <strong><?= e($lb['ten']) ?></strong>
                                <span class="guestbook-time"> (<?= e($lb['thoi_gian']) ?>)</span>
                                <p class="guestbook-message"><?= nl2br(e($lb['loi_nhan'])) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

        <!-- ================= QUAY LẠI ================= -->
        <p class="back-link">
            <a href="../../thanh-vien.php">
                ← Quay lại trang giới thiệu nhóm
            </a>
        </p>

    </main>


<!-- JavaScript riêng của trang cá nhân (chỉ cần nhúng 1 lần tại đây) -->
<script src="js/canhan.js"></script>

<?php
require __DIR__ . '/../../inc/footer.php';
?>
