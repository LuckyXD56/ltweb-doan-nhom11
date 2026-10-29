<?php
/**
 * Tệp: thanhvien/3120224105_nhan/gioithieu.php
 * Mục đích: Trang cá nhân của Lê Nguyễn Gia Nhân (MSSV 3120224105 - Nhóm 11).
 * Chức năng CSDL:
 *   1. Chuyển đổi sổ lưu bút từ tệp JSONL sang bảng CSDL (tv_luu_but), có phân trang và xóa lời nhắn (chủ trang).
 *   2. Đếm lượt xem theo ngày (INSERT ... ON DUPLICATE KEY UPDATE) vào tv_luot_xem và thống kê 7 ngày gần nhất.
 * Cách thử: Truy cập http://localhost:8000/thanhvien/3120224105_nhan/gioithieu.php
 */

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/ham.php';

$goc = '../../';
$baseUrl = '../../';
$tieuDeTrang = 'Lê Nguyễn Gia Nhân';
$trang = 'thanh-vien';
$cssRieng = 'css/canhan.css';
$customCss = '<link rel="stylesheet" href="css/canhan.css">';

$mssv = '3120224105';

// Kiểm tra quyền chủ trang (nếu đăng nhập bằng tài khoản của Nhân hoặc quản trị viên)
$laChuTrang = false;
if (!empty($_SESSION['da_dang_nhap'])) {
    if (($_SESSION['email'] ?? '') === 'nhan@sanbong.vn' || ($_SESSION['vai_tro'] ?? '') === 'quan_tri') {
        $laChuTrang = true;
    }
}

// --- CHỨC NĂNG 1: GHI NHẬN VÀ THỐNG KÊ LƯỢT XEM THEO NGÀY (CSDL) ---
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && !isset($_GET['page'])) {
    $stmt = $pdo->prepare(
        'INSERT INTO tv_luot_xem (mssv, ngay, luot_xem)
         VALUES (:mssv, CURRENT_DATE, 1)
         ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1'
    );
    $stmt->execute(['mssv' => $mssv]);
}

$stmt = $pdo->prepare(
    'SELECT COALESCE(SUM(luot_xem), 0)
     FROM tv_luot_xem
     WHERE mssv = :mssv'
);
$stmt->execute(['mssv' => $mssv]);
$tongLuotXem = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare(
    'SELECT ngay, SUM(luot_xem) AS luot_xem
     FROM tv_luot_xem
     WHERE mssv = :mssv
       AND ngay >= DATE_SUB(CURRENT_DATE, INTERVAL 6 DAY)
       AND ngay <= CURRENT_DATE
     GROUP BY ngay
     ORDER BY ngay DESC
     LIMIT 7'
);
$stmt->execute(['mssv' => $mssv]);
$luotXemTheoNgay = [];
foreach ($stmt->fetchAll() as $dongLuotXem) {
    $luotXemTheoNgay[$dongLuotXem['ngay']] = (int) $dongLuotXem['luot_xem'];
}

$thongKeBayNgay = [];
$homNay = new DateTimeImmutable('today');
for ($i = 0; $i < 7; $i++) {
    $ngay = $homNay->modify('-' . $i . ' days');
    $khoaNgay = $ngay->format('Y-m-d');
    $thongKeBayNgay[] = [
        'ngay' => $ngay->format('d/m/Y'),
        'luot_xem' => $luotXemTheoNgay[$khoaNgay] ?? 0,
    ];
}

// --- CHỨC NĂNG 2: SỔ LƯU BÚT DÙNG CSDL (tv_luu_but) ---
// Thêm lời nhắn mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'luubut') {
    $ten = trim((string) ($_POST['ten'] ?? ''));
    $loi_nhan = trim((string) ($_POST['loi_nhan'] ?? ''));

    if ($ten === '' || mb_strlen($ten) > 100 || $loi_nhan === '' || mb_strlen($loi_nhan) > 500) {
        datFlash('danger', 'Vui lòng nhập tên (tối đa 100 ký tự) và lời nhắn (tối đa 500 ký tự).');
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO tv_luu_but (mssv, ho_ten, loi_nhan, ngay_tao)
             VALUES (:mssv, :ho_ten, :loi_nhan, NOW())'
        );
        $stmt->execute([
            'mssv' => $mssv,
            'ho_ten' => $ten,
            'loi_nhan' => $loi_nhan,
        ]);
        datFlash('success', 'Đã lưu lời nhắn của bạn vào sổ lưu bút thành công.');
    }
    chuyenHuong('gioithieu.php#so-luu-but');
}

// Xóa lời nhắn (chỉ chủ trang đăng nhập mới được làm)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'xoa_luubut') {
    if (!$laChuTrang) {
        datFlash('danger', 'Chỉ chủ trang mới có quyền xóa lời nhắn.');
    } else {
        $idXoa = (int) ($_POST['id_luubut'] ?? 0);
        if ($idXoa > 0) {
            $stmt = $pdo->prepare('DELETE FROM tv_luu_but WHERE id = :id AND mssv = :mssv');
            $stmt->execute(['id' => $idXoa, 'mssv' => $mssv]);
            if ($stmt->rowCount() > 0) {
                datFlash('success', 'Đã xóa lời nhắn thành công.');
            } else {
                datFlash('danger', 'Không tìm thấy lời nhắn cần xóa.');
            }
        }
    }
    chuyenHuong('gioithieu.php#so-luu-but');
}

// Phân trang sổ lưu bút
$stmt = $pdo->prepare('SELECT COUNT(*) FROM tv_luu_but WHERE mssv = :mssv');
$stmt->execute(['mssv' => $mssv]);
$tongLuuBut = (int) $stmt->fetchColumn();

$soMoiTrang = 5;
$tongTrang = max(1, (int) ceil($tongLuuBut / $soMoiTrang));
$trangHienTai = max(1, min($tongTrang, getInt('page', 1)));
$offset = ($trangHienTai - 1) * $soMoiTrang;

$stmt = $pdo->prepare(
    'SELECT id, ho_ten, loi_nhan, DATE_FORMAT(ngay_tao, "%d/%m/%Y %H:%i") AS thoi_gian
     FROM tv_luu_but
     WHERE mssv = :mssv
     ORDER BY ngay_tao DESC, id DESC
     LIMIT :limit OFFSET :offset'
);
$stmt->bindValue(':mssv', $mssv, PDO::PARAM_STR);
$stmt->bindValue(':limit', $soMoiTrang, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$dsLuubut = $stmt->fetchAll();

// --- XỬ LÝ MÁY TÍNH ĐIỂM HỌC PHẦN (PHP) ---
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

        <!-- ================= THỐNG KÊ LƯỢT XEM ================= -->
        <section class="full-box view-stats-section" aria-labelledby="view-stats-heading">
            <h2 id="view-stats-heading">Thống kê lượt xem trang cá nhân</h2>
            <p>
                <strong>Tổng lượt xem từ trước đến nay:</strong>
                <?= e((string) $tongLuotXem) ?>
            </p>
            <div class="table-wrapper">
                <table class="schedule-table">
                    <caption>Lượt xem từng ngày trong 7 ngày gần nhất</caption>
                    <thead>
                        <tr>
                            <th scope="col">Ngày</th>
                            <th scope="col">Lượt xem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($thongKeBayNgay as $dong): ?>
                            <tr>
                                <th scope="row"><?= e($dong['ngay']) ?></th>
                                <td><?= e((string) $dong['luot_xem']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ================= SỔ LƯU BÚT ================= -->
        <section class="full-box guestbook-section" id="so-luu-but">
            <h2>Sổ lưu bút (Lưu trữ CSDL)</h2>
            <p class="guestbook-description">
                Hãy để lại lời nhắn, góp ý hoặc lời chúc của bạn! Dữ liệu được lưu trữ an toàn trong MySQL.
                <?php if ($laChuTrang): ?>
                    <span style="display:inline-block; margin-left: 8px; color: #007f51; font-weight: bold;">(Bạn đang đăng nhập với tư cách chủ trang - có quyền quản lý và xóa lời nhắn)</span>
                <?php endif; ?>
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
                <h3>Danh sách lời nhắn (Trang <?= e((string)$trangHienTai) ?>/<?= e((string)$tongTrang) ?> - Tổng: <?= e((string)$tongLuuBut) ?>)</h3>
                <?php if (empty($dsLuubut)): ?>
                    <p>Chưa có lời nhắn nào trong cơ sở dữ liệu.</p>
                <?php else: ?>
                    <ul class="guestbook-list" style="list-style: none; padding: 0;">
                        <?php foreach ($dsLuubut as $lb): ?>
                            <li style="border-bottom: 1px solid #e2e8f0; padding: 12px 0;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <strong><?= e($lb['ho_ten']) ?></strong>
                                        <span class="guestbook-time" style="color: #64748b; font-size: 0.85rem;"> (<?= e($lb['thoi_gian']) ?>)</span>
                                    </div>
                                    <?php if ($laChuTrang): ?>
                                        <form action="gioithieu.php" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lời nhắn này?');">
                                            <input type="hidden" name="action" value="xoa_luubut">
                                            <input type="hidden" name="id_luubut" value="<?= e((string)$lb['id']) ?>">
                                            <button type="submit" style="background:#ef4444; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:12px; cursor:pointer;">Xóa</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <p class="guestbook-message" style="margin: 8px 0 0; color: #334155;"><?= nl2br(e($lb['loi_nhan'])) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($tongTrang > 1): ?>
                        <nav aria-label="Phân trang sổ lưu bút" style="display:flex; gap:8px; justify-content:center; margin-top:20px;">
                            <?php for ($p = 1; $p <= $tongTrang; $p++): ?>
                                <a href="gioithieu.php?page=<?= $p ?>#so-luu-but"
                                   style="display:inline-block; padding:6px 12px; border:1px solid #cbd5e1; border-radius:6px; text-decoration:none; <?= $p === $trangHienTai ? 'background:#00a86b; color:#fff; font-weight:bold;' : 'background:#fff; color:#334155;' ?>">
                                    <?= $p ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    <?php endif; ?>
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
