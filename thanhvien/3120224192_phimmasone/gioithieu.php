<?php
/**
 * gioithieu.php — Trang cá nhân Phimmasone Khamphouvanh (Nhóm 11)
 *
 * Chức năng xử lý ở máy chủ (PHP):
 *   1. Danh sách công việc (TodoList) lưu trong $_SESSION['todo_phim'], PRG pattern.
 *   2. Tính chỉ số thể trạng BMI gửi qua biểu mẫu POST, kiểm tra số thực dương.
 */

session_start();

// Hàm mã hóa an toàn dữ liệu in ra HTML
if (!function_exists('e')) {
    function e(?string $s): string {
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// -------------------------------------------------------------
// 1. CHỨC NĂNG 1: TODOLIST LƯU SESSION
// -------------------------------------------------------------
if (!isset($_SESSION['todo_phim'])) {
    $_SESSION['todo_phim'] = [];
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['viec_moi'])) {
    $viecMoi = trim((string) $_POST['viec_moi']);
    if ($viecMoi !== '') {
        $_SESSION['todo_phim'][] = $viecMoi;
    }
    header('Location: gioithieu.php');
    exit;
}

if (isset($_GET['xoa_viec'])) {
    $viTri = (int) $_GET['xoa_viec'];
    if (isset($_SESSION['todo_phim'][$viTri])) {
        unset($_SESSION['todo_phim'][$viTri]);
        $_SESSION['todo_phim'] = array_values($_SESSION['todo_phim']);
    }
    header('Location: gioithieu.php');
    exit;
}

if (isset($_GET['xoa_het'])) {
    $_SESSION['todo_phim'] = [];
    header('Location: gioithieu.php');
    exit;
}

// -------------------------------------------------------------
// 2. CHỨC NĂNG 2: MÁY TÍNH CHỈ SỐ BMI (POST)
// -------------------------------------------------------------
$bmi = null;
$phanLoai = '';
$chieuCao = '';
$canNang = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['chieucao'], $_POST['cannang'])) {
    $chieuCao = (float) $_POST['chieucao'];
    $canNang  = (float) $_POST['cannang'];

    if ($chieuCao > 0 && $canNang > 0) {
        $m = $chieuCao / 100;
        $bmi = round($canNang / ($m * $m), 1);

        if ($bmi < 18.5) {
            $phanLoai = 'Gầy (Thiếu cân)';
        } elseif ($bmi < 24.9) {
            $phanLoai = 'Bình thường (Lý tưởng)';
        } elseif ($bmi < 29.9) {
            $phanLoai = 'Tiền béo phì (Hơi thừa cân)';
        } else {
            $phanLoai = 'Béo phì';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Trang giới thiệu thành viên Phimmasone Khamphouvanh của Nhóm 11.">
    <title>Phimmasone Khamphouvanh | Nhóm 11</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="css/profile.css">
</head>

<body class="trang">
    <header class="trang__header">
        <h1>Sân Bóng Thắng Lợi</h1>
        <p>Uy tín - Tiện lợi - Sức khỏe là vàng</p>
    </header>

    <nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="../../index.php">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="../../danh-sach.php">Danh sách sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="../../chi-tiet.php?id=1">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket active" href="../../thanh-vien.php">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="../../lien-he.php">Liên hệ / Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main profile-main">
        <h2 class="chu-canh-giua mb-1">Giới thiệu thành viên Nhóm 11</h2>

        <!-- Thẻ hồ sơ thành viên -->
        <section class="profile-card">
            <div class="profile-card__img-container">
                <img src="../../images/phimmasone.jpg" alt="Ảnh chân dung của Phimmasone Khamphouvanh" class="profile-card__img" width="170" height="170">
            </div>
            
            <div class="profile-card__info">
                <h3 class="profile-card__name">Phimmasone Khamphouvanh</h3>
                <p class="profile-card__role"><strong>Vai trò:</strong> Thành viên nhóm</p>
                <p class="profile-card__id"><strong>Mã sinh viên:</strong> 3120224192</p>
                <p><strong>Nhiệm vụ:</strong> Phụ trách nội dung HTML và trang giới thiệu cá nhân.</p>
            </div>
        </section>

        <!-- Lưới 2 cột: Thông tin cá nhân & Kỹ năng -->
        <section class="info-skills-grid">
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">Thông tin cá nhân</h4>
                <ul>
                    <li><strong>Họ và tên:</strong> Phimmasone Khamphouvanh</li>
                    <li><strong>MSSV:</strong> 3120224192</li>
                    <li><strong>Khoa:</strong> Toán - Tin</li>
                    <li><strong>Chuyên ngành:</strong> Công nghệ thông tin</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">Kỹ năng</h4>
                <label for="skill-search" style="display:block; font-weight: bold; margin-bottom: 5px;">Tìm kiếm kỹ năng:</label>
                <input type="text" id="skill-search" placeholder="Ví dụ: HTML, CSS, Responsive..." class="bieu-mau__o-nhap" style="margin-bottom:15px; width:100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                <ul class="ky-nang-list" id="skill-list">
                    <li>HTML5 Semantic</li>
                    <li>CSS3 Responsive</li>
                    <li>JavaScript</li>
                    <li>DevTools</li>
                    <li>Git &amp; GitHub</li>
                </ul>
            </div>
        </section>

        <!-- Sở thích và mục tiêu -->
        <section class="hobbies-section">
            <h3 class="text-primary mt-0">Sở thích và mục tiêu</h3>
            <p class="text-secondary">Phimmasone yêu thích bóng đá, công nghệ và thiết kế website. Mục tiêu là nâng cao kỹ năng lập trình web và hoàn thành xuất sắc dự án website đặt sân bóng của nhóm.</p>
        </section>

        <!-- HAI CHỨC NĂNG XỬ LÝ MÁY CHỦ BẰNG PHP -->
        <section class="info-skills-grid" style="margin-top: 2rem;">
            <!-- 1. Danh sách công việc (TodoList) -->
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">📝 Danh sách công việc (TodoList - Session)</h4>
                <form method="post" action="gioithieu.php" style="display: flex; gap: 8px; margin-bottom: 12px;">
                    <input type="text" name="viec_moi" placeholder="Nhập việc cần làm..." required style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    <button class="nut nut--chinh" type="submit">Thêm</button>
                </form>

                <?php if (empty($_SESSION['todo_phim'])): ?>
                    <p style="color: #64748b; font-style: italic; margin: 0;">Chưa có công việc nào.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px;">
                        <?php foreach ($_SESSION['todo_phim'] as $i => $viec): ?>
                            <li style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border-left: 3px solid var(--mau-chinh);">
                                <span>📌 <?= e($viec) ?></span>
                                <a href="gioithieu.php?xoa_viec=<?= $i ?>" style="color: #ef4444; font-weight: bold; text-decoration: none;" onclick="return confirm('Xoá việc này?');">✕</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p style="text-align: right; margin-top: 8px; margin-bottom: 0;">
                        <a href="gioithieu.php?xoa_het=1" style="color: #dc2626; font-size: 0.85rem;" onclick="return confirm('Xoá toàn bộ?');">Xóa hết</a>
                    </p>
                <?php endif; ?>
            </div>

            <!-- 2. Máy tính chỉ số BMI -->
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">💪 Máy tính chỉ số BMI (POST)</h4>
                <form method="post" action="gioithieu.php" style="display: flex; flex-direction: column; gap: 10px;">
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 4px;">Chiều cao (cm):</label>
                        <input type="number" name="chieucao" step="0.1" min="50" max="250" value="<?= e($chieuCao) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 4px;">Cân nặng (kg):</label>
                        <input type="number" name="cannang" step="0.1" min="10" max="300" value="<?= e($canNang) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <button class="nut nut--chinh" type="submit" style="align-self: flex-start;">Tính BMI</button>
                </form>

                <?php if ($bmi !== null): ?>
                    <div style="margin-top: 10px; padding: 10px 14px; background: #ecfdf5; border-radius: 6px; border-left: 4px solid var(--mau-chinh);">
                        <p style="margin: 0; color: #065f46;"><strong>BMI của bạn:</strong> <span style="font-size: 1.2rem; font-weight: bold; color: var(--mau-chinh);"><?= $bmi ?></span></p>
                        <p style="margin: 4px 0 0; color: #047857; font-size: 0.9rem;"><strong>Đánh giá:</strong> <?= e($phanLoai) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Thời khóa biểu tuần -->
        <section class="profile-schedule">
            <h3 class="chu-canh-giua mb-1 text-primary">Thời khóa biểu tuần</h3>
            <p class="chu-canh-giua" style="margin-top: -0.5rem; margin-bottom: 1rem; color: #64748b; font-size: 0.9rem;">Lịch học và hoạt động trong tuần của Phimmasone Khamphouvanh</p>
            <div style="text-align: center; margin-bottom: 15px;"><button id="btn-toggle-schedule" aria-expanded="true" class="nut nut--phu" style="padding: 4px 12px; font-size: 14px;">Thu gọn / Mở rộng</button></div>
            
            <div class="bang-cuon-ngang">
                <div id="schedule-container" style="overflow-x: auto;">
                <div class="bang-chua"><table class="bang-du-lieu bang-thoi-khoa-bieu" style="min-width: 800px; font-size: 0.85rem; border-collapse: collapse;">
                    <caption>Lịch học và hoạt động trong tuần của Phimmasone Khamphouvanh</caption>
                    <thead>
                        <tr style="background-color: #059669; color: white;">
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 5%; text-align: center;">Tiết</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 2</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 3</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 4</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 5</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 6</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 7</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Chủ nhật</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: left; vertical-align: top;">
                        <!-- Tiết 1 -->
                        <tr>
                            <th scope="row" style="border: 1px solid #ccc; text-align: center;">1</th>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31241283 - 24-0102<br><strong>Hệ quản trị cơ sở dữ liệu</strong><br>Phòng: B3-402</td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231398 - 24-0101<br><strong>Lập trình mạng</strong><br>Phòng: B3-303</td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231755 - 24-0102<br><strong>Thiết kế và lập trình web</strong><br>Phòng: B3-303</td>
                            <td style="border: 1px solid #ccc;"></td>
                        </tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">2</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">3</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <!-- Tiết 4 -->
                        <tr>
                            <th scope="row" style="border: 1px solid #ccc; text-align: center;">4</th>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">21221904 - 24-0315<br><strong>Lịch sử Đảng Cộng sản Việt Nam</strong><br>Phòng: A6-502</td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231330 - 24-0103<br><strong>Khai phá dữ liệu</strong><br>Phòng: A5-209</td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                        </tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">5</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">6</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <!-- Break -->
                        <tr style="height: 10px; background-color: #f1f5f9;"><td colspan="8" style="border: 1px solid #ccc;"></td></tr>
                        <!-- Tiết 7 -->
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">7</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">8</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">9</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <!-- Tiết 10 -->
                        <tr>
                            <th scope="row" style="border: 1px solid #ccc; text-align: center;">10</th>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231016 - 24-0103<br><strong>Công nghệ phần mềm</strong><br>Phòng: B3-304</td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31221010 - 24-0103<br><strong>An toàn thông tin</strong><br>Phòng: B3-506</td>
                            <td style="border: 1px solid #ccc;"></td>
                        </tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">11</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">12</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                    </tbody>
                </table></div>
                </div>
            </div>
        </section>
        
        <p class="chu-canh-giua mt-1">
            <a href="../../thanh-vien.php" class="nut nut--phu">← Quay lại danh sách nhóm</a>
        </p>
    </main>

    <footer class="trang__footer">
        <p>© 2026 Nhóm 11 — Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng</p>
    </footer>
    <script type="module" src="js/canhan.js"></script>
</body>
</html>
