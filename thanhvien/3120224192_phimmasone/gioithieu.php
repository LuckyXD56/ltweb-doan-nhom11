<?php
/**
 * gioithieu.php — Trang cá nhân Phimmasone Khamphouvanh (MSSV: 3120224192) - Nhóm 11
 *
 * Bài tập thực hành nhóm số 6 - Phần B:
 *   1. Quản lý công việc cá nhân (tv_todo_phim) qua PDO Prepared Statements: thêm, đổi trạng thái hoàn thành, xóa việc.
 *   2. Máy tính & lưu lịch sử chỉ số BMI (tv_bmi_phim) qua PDO: tính toán, phân loại, lưu lịch sử và thống kê trung bình.
 * Cách thử: Thêm công việc, đánh dấu hoàn thành; tính BMI để lưu vào CSDL và xem 5 lần đo gần nhất; tất cả dùng PRG và chống XSS bằng e().
 */

session_start();

$baseUrl = '../../';
require_once __DIR__ . '/../../inc/ham.php';
require_once __DIR__ . '/../../inc/db.php';

$mssv = '3120224192';

if (!function_exists('e')) {
    function e(?string $s): string {
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// Khởi tạo kết nối CSDL an toàn
$dbLoi = null;
$pdo = null;
try {
    $pdo = db();
} catch (Throwable $e) {
    $dbLoi = 'Chưa thể kết nối CSDL MySQL: ' . $e->getMessage();
}

// Lấy thông báo Flash từ Session (PRG)
$thongBaoThanhCong = $_SESSION['flash_tb'] ?? null;
unset($_SESSION['flash_tb']);

$thongBaoLoi = $_SESSION['flash_loi'] ?? null;
unset($_SESSION['flash_loi']);

$bmiKetQua = $_SESSION['flash_bmi'] ?? null;
unset($_SESSION['flash_bmi']);

// -------------------------------------------------------------
// 1. CHỨC NĂNG 1: TODOLIST LƯU CSDL (tv_todo_phim)
// -------------------------------------------------------------
// 1.1 Thêm công việc mới (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'them_todo') {
    $viecMoi = trim((string)($_POST['viec_moi'] ?? ''));
    if ($viecMoi === '') {
        $_SESSION['flash_loi'] = 'Vui lòng nhập nội dung công việc.';
    } elseif ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tv_todo_phim (mssv, tieu_de, hoan_thanh) VALUES (?, ?, 0)");
            $stmt->execute([$mssv, $viecMoi]);
            $_SESSION['flash_tb'] = 'Đã thêm công việc mới vào CSDL!';
        } catch (PDOException $e) {
            $_SESSION['flash_loi'] = 'Lỗi lưu công việc: ' . $e->getMessage();
        }
    }
    header('Location: gioithieu.php');
    exit;
}

// 1.2 Đổi trạng thái hoàn thành (GET)
if (isset($_GET['toggle_todo']) && $pdo) {
    $idTodo = (int)$_GET['toggle_todo'];
    try {
        $stmt = $pdo->prepare("UPDATE tv_todo_phim SET hoan_thanh = 1 - hoan_thanh WHERE id = ? AND mssv = ?");
        $stmt->execute([$idTodo, $mssv]);
    } catch (PDOException $e) {
        // bỏ qua
    }
    header('Location: gioithieu.php');
    exit;
}

// 1.3 Xóa 1 công việc (GET)
if (isset($_GET['xoa_todo']) && $pdo) {
    $idTodo = (int)$_GET['xoa_todo'];
    try {
        $stmt = $pdo->prepare("DELETE FROM tv_todo_phim WHERE id = ? AND mssv = ?");
        $stmt->execute([$idTodo, $mssv]);
        $_SESSION['flash_tb'] = 'Đã xóa công việc khỏi CSDL!';
    } catch (PDOException $e) {
        $_SESSION['flash_loi'] = 'Lỗi xóa công việc: ' . $e->getMessage();
    }
    header('Location: gioithieu.php');
    exit;
}

// 1.4 Xóa toàn bộ công việc (GET)
if (isset($_GET['xoa_het_todo']) && $pdo) {
    try {
        $stmt = $pdo->prepare("DELETE FROM tv_todo_phim WHERE mssv = ?");
        $stmt->execute([$mssv]);
        $_SESSION['flash_tb'] = 'Đã xóa tất cả công việc của bạn!';
    } catch (PDOException $e) {
        $_SESSION['flash_loi'] = 'Lỗi xóa danh sách: ' . $e->getMessage();
    }
    header('Location: gioithieu.php');
    exit;
}

// 1.5 Đọc danh sách công việc từ CSDL
$danhSachTodo = [];
$tongTodo = 0;
$soHoanThanh = 0;
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, tieu_de, hoan_thanh, ngay_tao FROM tv_todo_phim WHERE mssv = ? ORDER BY hoan_thanh ASC, id DESC");
        $stmt->execute([$mssv]);
        $danhSachTodo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tongTodo = count($danhSachTodo);
        foreach ($danhSachTodo as $t) {
            if ((int)$t['hoan_thanh'] === 1) {
                $soHoanThanh++;
            }
        }
    } catch (Throwable $e) {
        // bảng chưa có hoặc lỗi
    }
}

// -------------------------------------------------------------
// 2. CHỨC NĂNG 2: MÁY TÍNH & LỊCH SỬ CHỈ SỐ BMI (tv_bmi_phim)
// -------------------------------------------------------------
// 2.1 Xử lý tính toán & lưu kết quả (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tinh_bmi') {
    $chieuCao = (float)($_POST['chieucao'] ?? 0);
    $canNang  = (float)($_POST['cannang'] ?? 0);

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

        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tv_bmi_phim (mssv, chieu_cao, can_nang, chi_so_bmi, phan_loai) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$mssv, $chieuCao, $canNang, $bmi, $phanLoai]);
                $_SESSION['flash_tb'] = 'Đã tính BMI và lưu lịch sử vào CSDL thành công!';
            } catch (PDOException $e) {
                $_SESSION['flash_loi'] = 'Lỗi lưu CSDL BMI: ' . $e->getMessage();
            }
        }

        $_SESSION['flash_bmi'] = [
            'bmi' => $bmi,
            'phan_loai' => $phanLoai,
            'chieu_cao' => $chieuCao,
            'can_nang' => $canNang
        ];
    } else {
        $_SESSION['flash_loi'] = 'Vui lòng nhập chiều cao và cân nặng hợp lệ lớn hơn 0.';
    }
    header('Location: gioithieu.php');
    exit;
}

// 2.2 Đọc 5 lần đo BMI gần nhất & thống kê trung bình
$lichSuBmi = [];
$thongKeBmi = ['tong_lan' => 0, 'bmi_tb' => 0];
if ($pdo) {
    try {
        $stmtBmi = $pdo->prepare("SELECT id, chieu_cao, can_nang, chi_so_bmi, phan_loai, ngay_tao FROM tv_bmi_phim WHERE mssv = ? ORDER BY id DESC LIMIT 5");
        $stmtBmi->execute([$mssv]);
        $lichSuBmi = $stmtBmi->fetchAll(PDO::FETCH_ASSOC);

        $stmtTk = $pdo->prepare("SELECT COUNT(*) AS tong_lan, ROUND(AVG(chi_so_bmi), 1) AS bmi_tb FROM tv_bmi_phim WHERE mssv = ?");
        $stmtTk->execute([$mssv]);
        $rowTk = $stmtTk->fetch(PDO::FETCH_ASSOC);
        if ($rowTk && $rowTk['tong_lan'] > 0) {
            $thongKeBmi = $rowTk;
        }
    } catch (Throwable $e) {
        // bảng chưa có hoặc lỗi
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
            <li class="menu__muc"><a class="menu__lien-ket" href="../../chi-tiet.php?id=A1">Chi tiết sân</a></li>
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

        <!-- THÔNG BÁO FLASH HỆ THỐNG -->
        <?php if ($thongBaoThanhCong): ?>
            <div style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-left: 4px solid #10b981; padding: 10px 14px; border-radius: 6px; margin: 1rem 0;">
                ✓ <?= e($thongBaoThanhCong) ?>
            </div>
        <?php endif; ?>
        <?php if ($thongBaoLoi): ?>
            <div style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-left: 4px solid #ef4444; padding: 10px 14px; border-radius: 6px; margin: 1rem 0;">
                ⚠ <?= e($thongBaoLoi) ?>
            </div>
        <?php endif; ?>
        <?php if ($dbLoi): ?>
            <div style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; padding: 10px 14px; border-radius: 6px; margin: 1rem 0;">
                ℹ <?= e($dbLoi) ?>
            </div>
        <?php endif; ?>

        <!-- HAI CHỨC NĂNG XỬ LÝ MÁY CHỦ BẰNG PHP & CSDL MYSQL -->
        <section class="info-skills-grid" style="margin-top: 1.5rem;">
            <!-- 1. Danh sách công việc (tv_todo_phim) -->
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">📝 Danh sách công việc (CSDL: tv_todo_phim)</h4>
                <p style="font-size: 0.85rem; color: #64748b; margin-top: -0.25rem; margin-bottom: 0.75rem;">
                    Tiến độ: <strong><?= $soHoanThanh ?>/<?= $tongTodo ?></strong> hoàn thành
                </p>

                <form method="post" action="gioithieu.php" style="display: flex; gap: 8px; margin-bottom: 12px;">
                    <input type="hidden" name="action" value="them_todo">
                    <input type="text" name="viec_moi" placeholder="Nhập việc cần làm..." required style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    <button class="nut nut--chinh" type="submit">Thêm việc</button>
                </form>

                <?php if (empty($danhSachTodo)): ?>
                    <p style="color: #64748b; font-style: italic; margin: 0;">Chưa có công việc nào trong CSDL.</p>
                <?php else: ?>
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($danhSachTodo as $item): ?>
                            <?php $daXong = ((int)$item['hoan_thanh'] === 1); ?>
                            <li style="display: flex; justify-content: space-between; align-items: center; background: <?= $daXong ? '#f1f5f9' : '#f8fafc' ?>; padding: 8px 12px; border-radius: 6px; border-left: 3px solid <?= $daXong ? '#10b981' : 'var(--mau-chinh)' ?>;">
                                <div style="display: flex; align-items: center; gap: 8px; flex: 1;">
                                    <a href="gioithieu.php?toggle_todo=<?= (int)$item['id'] ?>" title="Nhấn để đổi trạng thái" style="text-decoration: none; font-size: 1.1rem;">
                                        <?= $daXong ? '✅' : '⬜' ?>
                                    </a>
                                    <span style="<?= $daXong ? 'text-decoration: line-through; color: #64748b;' : 'font-weight: 500;' ?>">
                                        <?= e($item['tieu_de']) ?>
                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 0.75rem; color: #94a3b8;"><?= date('d/m H:i', strtotime($item['ngay_tao'])) ?></span>
                                    <a href="gioithieu.php?xoa_todo=<?= (int)$item['id'] ?>" style="color: #ef4444; font-weight: bold; text-decoration: none; padding: 2px 6px;" onclick="return confirm('Xoá công việc này?');" title="Xóa">✕</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p style="text-align: right; margin-top: 10px; margin-bottom: 0;">
                        <a href="gioithieu.php?xoa_het_todo=1" style="color: #dc2626; font-size: 0.85rem; text-decoration: none;" onclick="return confirm('Xoá toàn bộ công việc trong CSDL?');">🗑 Xóa hết danh sách</a>
                    </p>
                <?php endif; ?>
            </div>

            <!-- 2. Máy tính & Lịch sử BMI (tv_bmi_phim) -->
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">💪 Máy tính &amp; Lịch sử BMI (CSDL: tv_bmi_phim)</h4>
                
                <form method="post" action="gioithieu.php" style="display: flex; flex-direction: column; gap: 10px;">
                    <input type="hidden" name="action" value="tinh_bmi">
                    <div>
                        <label for="chieu-cao-nhap" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 4px;">Chiều cao (cm):</label>
                        <input type="number" id="chieu-cao-nhap" name="chieucao" step="0.1" min="50" max="250" placeholder="Ví dụ: 170" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label for="can-nang-nhap" style="display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 4px;">Cân nặng (kg):</label>
                        <input type="number" id="can-nang-nhap" name="cannang" step="0.1" min="10" max="300" placeholder="Ví dụ: 65" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <button class="nut nut--chinh" type="submit" style="align-self: flex-start;">Tính BMI &amp; Lưu CSDL</button>
                </form>

                <?php if ($bmiKetQua): ?>
                    <div style="margin-top: 12px; padding: 10px 14px; background: #ecfdf5; border-radius: 6px; border-left: 4px solid var(--mau-chinh);">
                        <p style="margin: 0; color: #065f46;"><strong>Kết quả vừa tính:</strong> BMI <span style="font-size: 1.2rem; font-weight: bold; color: var(--mau-chinh);"><?= $bmiKetQua['bmi'] ?></span></p>
                        <p style="margin: 4px 0 0; color: #047857; font-size: 0.9rem;"><strong>Đánh giá:</strong> <?= e($bmiKetQua['phan_loai']) ?> (<?= $bmiKetQua['chieu_cao'] ?> cm, <?= $bmiKetQua['can_nang'] ?> kg)</p>
                    </div>
                <?php endif; ?>

                <!-- Lịch sử đo gần đây -->
                <div style="margin-top: 16px; border-top: 1px dashed #cbd5e1; padding-top: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 0.9rem; font-weight: bold; color: #334155;">📊 Lịch sử đo gần đây</span>
                        <?php if ($thongKeBmi['tong_lan'] > 0): ?>
                            <span style="font-size: 0.8rem; color: #64748b;">(TB: <strong><?= $thongKeBmi['bmi_tb'] ?></strong> - <?= $thongKeBmi['tong_lan'] ?> lần)</span>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($lichSuBmi)): ?>
                        <p style="color: #64748b; font-style: italic; font-size: 0.85rem; margin: 0;">Chưa có dữ liệu lịch sử đo BMI.</p>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="background: #f1f5f9; color: #475569; border-bottom: 1px solid #cbd5e1;">
                                        <th style="padding: 4px 6px;">Ngày</th>
                                        <th style="padding: 4px 6px;">Cao/Nặng</th>
                                        <th style="padding: 4px 6px;">BMI</th>
                                        <th style="padding: 4px 6px;">Đánh giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lichSuBmi as $itemBmi): ?>
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 4px 6px; color: #64748b;"><?= date('d/m/Y', strtotime($itemBmi['ngay_tao'])) ?></td>
                                            <td style="padding: 4px 6px;"><?= (float)$itemBmi['chieu_cao'] ?>cm / <?= (float)$itemBmi['can_nang'] ?>kg</td>
                                            <td style="padding: 4px 6px; font-weight: bold; color: var(--mau-chinh);"><?= (float)$itemBmi['chi_so_bmi'] ?></td>
                                            <td style="padding: 4px 6px; color: #334155;"><?= e($itemBmi['phan_loai']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
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
