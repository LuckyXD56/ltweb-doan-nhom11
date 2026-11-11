<?php
/**
 * gioithieu.php — Trang cá nhân Phimmasone Khamphouvanh (MSSV: 3120224192) - Nhóm 11
 *
 * Bài tập thực hành nhóm số 7 - Bảo mật ứng dụng web (Phần B: Trang cá nhân):
 *   1. Chuyển toàn bộ thao tác đổi dữ liệu (thêm, đổi trạng thái, xóa, xóa hết) sang POST.
 *   2. Phòng vệ CSRF tập trung bằng token bí mật kiểm tra qua csrfHopLe() và csrfField().
 *   3. Kiểm tra phân quyền chủ trang ($laChuTrang) ở phía máy chủ trước khi cho phép sửa/xóa.
 *   4. Bảo vệ truy vấn CSDL: prepared statements kết hợp WHERE mssv = ? chống tấn công sửa/xóa chéo.
 *   5. Tuân thủ Content Security Policy: loại bỏ 100% inline CSS (style=) và inline JS (onclick=).
 *   6. Kiểm tra tham số mảng, lọc dữ liệu đầu vào và mã hóa an toàn đầu ra HTML bằng e().
 */

session_start();

$baseUrl = '../../';
require_once __DIR__ . '/../../inc/ham.php';
require_once __DIR__ . '/../../inc/db.php';
if (file_exists(__DIR__ . '/../../inc/csrf.php')) {
    require_once __DIR__ . '/../../inc/csrf.php';
}

$mssv = '3120224192';

// Hàm mã hóa an toàn dữ liệu in ra HTML (Chống XSS)
if (!function_exists('e')) {
    function e(?string $s): string {
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

// Tiện ích kiểm tra và làm sạch chuỗi
if (!function_exists('chuoi')) {
    function chuoi(mixed $v, string $macDinh = ''): string {
        return is_string($v) ? trim($v) : $macDinh;
    }
}

// Hàm sinh và kiểm tra mã CSRF (Tương thích bài học Chương 7)
if (!function_exists('csrfToken')) {
    function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string {
        return '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">';
    }
}

if (!function_exists('csrfHopLe')) {
    function csrfHopLe(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
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

// -------------------------------------------------------------
// XÁC THỰC VÀ PHÂN QUYỀN CHỦ TRANG PHÍA MÁY CHỦ ($laChuTrang)
// -------------------------------------------------------------
$laChuTrang = false;
$tvRow = null;
if ($pdo) {
    try {
        $stTV = $pdo->prepare('SELECT mssv, ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
        $stTV->execute([$mssv]);
        $tvRow = $stTV->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        // bỏ qua
    }
}

if (!empty($_SESSION['da_dang_nhap'])) {
    $currentUid   = (int)($_SESSION['nguoi_dung_id'] ?? $_SESSION['uid'] ?? 0);
    $currentRole  = (string)($_SESSION['vai_tro'] ?? '');
    $currentEmail = (string)($_SESSION['email'] ?? '');
    $currentMssv  = (string)($_SESSION['mssv'] ?? '');
    
    if (
        $currentMssv === $mssv
        || $currentEmail === 'phimmasone@sanbong.vn'
        || $currentEmail === 'phimmasone@gmail.com'
        || $currentRole === 'quan_tri'
        || ($tvRow && !empty($tvRow['nguoi_dung_id']) && $currentUid === (int)$tvRow['nguoi_dung_id'])
    ) {
        $laChuTrang = true;
    }
}

// Lấy thông báo Flash từ Session (PRG)
$thongBaoThanhCong = $_SESSION['flash_tb'] ?? null;
unset($_SESSION['flash_tb']);

$thongBaoLoi = $_SESSION['flash_loi'] ?? null;
unset($_SESSION['flash_loi']);

$bmiKetQua = $_SESSION['flash_bmi'] ?? null;
unset($_SESSION['flash_bmi']);

// =============================================================
// XỬ LÝ CÁC HÀNH ĐỘNG POST (MÔ HÌNH PRG & GIA CỐ BẢO MẬT)
// =============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Kiểm tra mã bảo vệ CSRF
    $csrfGuiLen = is_string($_POST['csrf'] ?? null) ? $_POST['csrf'] : '';
    if (!csrfHopLe($csrfGuiLen)) {
        http_response_code(403);
        if (file_exists(__DIR__ . '/../../403.php')) {
            require __DIR__ . '/../../403.php';
        } else {
            echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>403 Forbidden</title></head><body><h1>403 Forbidden</h1><p>Yêu cầu bị từ chối: Mã bảo vệ CSRF không hợp lệ hoặc đã hết hạn.</p><p><a href="gioithieu.php">Quay lại trang cá nhân</a></p></body></html>';
        }
        exit;
    }

    // 2. Kiểm tra tham số action
    $action = $_POST['action'] ?? '';
    if (!is_string($action)) {
        http_response_code(400);
        exit('Bad Request: Tham số action không hợp lệ.');
    }

    // ---------------------------------------------------------
    // CHỨC NĂNG 1: TODOLIST CÁ NHÂN (tv_todo_phim)
    // ---------------------------------------------------------

    // 1.1 Thêm công việc mới (POST)
    if ($action === 'them_todo') {
        if (!$laChuTrang) {
            $_SESSION['flash_loi'] = 'Bảo mật: Bạn không có quyền thêm công việc vào danh sách của thành viên này!';
        } else {
            $rawViec = $_POST['viec_moi'] ?? '';
            $viecMoi = is_string($rawViec) ? trim($rawViec) : '';

            if ($viecMoi === '') {
                $_SESSION['flash_loi'] = 'Vui lòng nhập nội dung công việc.';
            } elseif (mb_strlen($viecMoi) > 255) {
                $_SESSION['flash_loi'] = 'Nội dung công việc không được vượt quá 255 ký tự.';
            } elseif ($pdo) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO tv_todo_phim (mssv, tieu_de, hoan_thanh) VALUES (?, ?, 0)");
                    $stmt->execute([$mssv, $viecMoi]);
                    $_SESSION['flash_tb'] = 'Đã thêm công việc mới vào CSDL!';
                } catch (PDOException $e) {
                    $_SESSION['flash_loi'] = 'Lỗi lưu công việc: ' . $e->getMessage();
                }
            }
        }
        header('Location: gioithieu.php');
        exit;
    }

    // 1.2 Đổi trạng thái hoàn thành (POST)
    elseif ($action === 'toggle_todo') {
        if (!$laChuTrang) {
            $_SESSION['flash_loi'] = 'Bảo mật: Bạn không có quyền đổi trạng thái công việc của thành viên này!';
        } else {
            $idTodo = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if ($idTodo && $pdo) {
                try {
                    // WHERE id = ? AND mssv = ? chống can thiệp trái phép việc người khác
                    $stmt = $pdo->prepare("UPDATE tv_todo_phim SET hoan_thanh = 1 - hoan_thanh WHERE id = ? AND mssv = ?");
                    $stmt->execute([$idTodo, $mssv]);
                    $_SESSION['flash_tb'] = 'Đã cập nhật trạng thái công việc!';
                } catch (PDOException $e) {
                    $_SESSION['flash_loi'] = 'Lỗi cập nhật trạng thái: ' . $e->getMessage();
                }
            }
        }
        header('Location: gioithieu.php');
        exit;
    }

    // 1.3 Xóa 1 công việc (POST)
    elseif ($action === 'xoa_todo') {
        if (!$laChuTrang) {
            $_SESSION['flash_loi'] = 'Bảo mật: Bạn không có quyền xóa công việc của thành viên này!';
        } else {
            $idTodo = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            if ($idTodo && $pdo) {
                try {
                    // WHERE id = ? AND mssv = ? bảo vệ xóa chéo dữ liệu giữa các thành viên
                    $stmt = $pdo->prepare("DELETE FROM tv_todo_phim WHERE id = ? AND mssv = ?");
                    $stmt->execute([$idTodo, $mssv]);
                    if ($stmt->rowCount() > 0) {
                        $_SESSION['flash_tb'] = 'Đã xóa công việc khỏi CSDL!';
                    } else {
                        $_SESSION['flash_loi'] = 'Không tìm thấy công việc cần xóa hoặc công việc không thuộc về bạn.';
                    }
                } catch (PDOException $e) {
                    $_SESSION['flash_loi'] = 'Lỗi xóa công việc: ' . $e->getMessage();
                }
            }
        }
        header('Location: gioithieu.php');
        exit;
    }

    // 1.4 Xóa toàn bộ công việc (POST)
    elseif ($action === 'xoa_het_todo') {
        if (!$laChuTrang) {
            $_SESSION['flash_loi'] = 'Bảo mật: Bạn không có quyền xóa danh sách của thành viên này!';
        } elseif ($pdo) {
            try {
                $stmt = $pdo->prepare("DELETE FROM tv_todo_phim WHERE mssv = ?");
                $stmt->execute([$mssv]);
                $_SESSION['flash_tb'] = 'Đã xóa tất cả công việc của bạn!';
            } catch (PDOException $e) {
                $_SESSION['flash_loi'] = 'Lỗi xóa danh sách: ' . $e->getMessage();
            }
        }
        header('Location: gioithieu.php');
        exit;
    }

    // ---------------------------------------------------------
    // CHỨC NĂNG 2: MÁY TÍNH & LỊCH SỬ BMI (tv_bmi_phim)
    // ---------------------------------------------------------
    elseif ($action === 'tinh_bmi') {
        // Kiểm tra an toàn trước tham số mảng
        $rawCao  = $_POST['chieucao'] ?? null;
        $rawNang = $_POST['cannang'] ?? null;

        if (is_array($rawCao) || is_array($rawNang)) {
            $_SESSION['flash_loi'] = 'Lỗi: Tham số chiều cao hoặc cân nặng không đúng định dạng (mảng tham số).';
            header('Location: gioithieu.php');
            exit;
        }

        $chieuCao = filter_var($rawCao, FILTER_VALIDATE_FLOAT);
        $canNang  = filter_var($rawNang, FILTER_VALIDATE_FLOAT);

        if ($chieuCao !== false && $canNang !== false && $chieuCao >= 50 && $chieuCao <= 250 && $canNang >= 10 && $canNang <= 300) {
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
            $_SESSION['flash_loi'] = 'Vui lòng nhập chiều cao (50-250cm) và cân nặng (10-300kg) hợp lệ.';
        }
        header('Location: gioithieu.php');
        exit;
    }
}

// -------------------------------------------------------------
// ĐỌC DỮ LIỆU HIỂN THỊ TỪ CSDL
// -------------------------------------------------------------
// 1. Đọc danh sách công việc
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
        // bỏ qua
    }
}

// 2. Đọc 5 lần đo BMI gần nhất & thống kê trung bình
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
        // bỏ qua
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
                
                <?php if ($laChuTrang): ?>
                    <span class="vai-tro-badge vai-tro-badge--chu-trang">🛡 Chủ trang (Phimmasone) - Có quyền quản lý</span>
                <?php else: ?>
                    <span class="vai-tro-badge vai-tro-badge--khach">👤 Khách xem trang (Chỉ xem)</span>
                <?php endif; ?>
                
                <p><strong>Nhiệm vụ:</strong> Phụ trách nội dung HTML, trang cá nhân và gia cố bảo mật CSRF / CSP.</p>
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
                <label for="skill-search" class="skill-search-label">Tìm kiếm kỹ năng:</label>
                <input type="text" id="skill-search" placeholder="Ví dụ: HTML, CSS, Responsive..." class="bieu-mau__o-nhap skill-search-input">
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
            <div class="thong-bao thong-bao--thanh-cong">
                ✓ <?= e($thongBaoThanhCong) ?>
            </div>
        <?php endif; ?>
        <?php if ($thongBaoLoi): ?>
            <div class="thong-bao thong-bao--loi">
                ⚠ <?= e($thongBaoLoi) ?>
            </div>
        <?php endif; ?>
        <?php if ($dbLoi): ?>
            <div class="thong-bao thong-bao--canh-bao">
                ℹ <?= e($dbLoi) ?>
            </div>
        <?php endif; ?>

        <!-- HAI CHỨC NĂNG XỬ LÝ MÁY CHỦ BẰNG PHP & CSDL MYSQL (ĐÃ GIA CỐ BẢO MẬT) -->
        <section class="info-skills-grid mt-1-5">
            <!-- 1. Danh sách công việc (tv_todo_phim) -->
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">📝 Danh sách công việc (CSDL: tv_todo_phim)</h4>
                <p class="todo-tien-do">
                    Tiến độ: <strong><?= $soHoanThanh ?>/<?= $tongTodo ?></strong> hoàn thành
                </p>

                <?php if ($laChuTrang): ?>
                    <!-- Form thêm việc qua POST có CSRF token -->
                    <form method="post" action="gioithieu.php" class="todo-form">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="them_todo">
                        <input type="text" name="viec_moi" placeholder="Nhập việc cần làm..." required class="todo-form__input">
                        <button class="nut nut--chinh" type="submit">Thêm việc</button>
                    </form>
                <?php else: ?>
                    <p class="che-do-xem-ghi-chu">🔒 Đang xem ở chế độ khách. Chỉ chủ trang (Phimmasone) mới có quyền thêm/xóa công việc.</p>
                <?php endif; ?>

                <?php if (empty($danhSachTodo)): ?>
                    <p class="todo-trong">Chưa có công việc nào trong CSDL.</p>
                <?php else: ?>
                    <ul class="todo-danh-sach">
                        <?php foreach ($danhSachTodo as $item): ?>
                            <?php $daXong = ((int)$item['hoan_thanh'] === 1); ?>
                            <li class="todo-item <?= $daXong ? 'todo-item--xong' : '' ?>">
                                <div class="todo-item__trai">
                                    <?php if ($laChuTrang): ?>
                                        <!-- Form đổi trạng thái qua POST có CSRF token -->
                                        <form method="post" action="gioithieu.php" class="todo-action-form">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="toggle_todo">
                                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                            <button type="submit" class="nut-icon-toggle" title="Nhấn để đổi trạng thái" aria-label="Đổi trạng thái">
                                                <?= $daXong ? '✅' : '⬜' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span aria-hidden="true"><?= $daXong ? '✅' : '⬜' ?></span>
                                    <?php endif; ?>

                                    <span class="todo-item__tieu-de">
                                        <?= e($item['tieu_de']) ?>
                                    </span>
                                </div>
                                <div class="todo-item__phai">
                                    <span class="todo-item__thoi-gian"><?= date('d/m H:i', strtotime($item['ngay_tao'])) ?></span>
                                    
                                    <?php if ($laChuTrang): ?>
                                        <!-- Form xóa việc qua POST có CSRF token và xác nhận unobtrusive -->
                                        <form method="post" action="gioithieu.php" class="todo-action-form" data-confirm="Xoá công việc này?">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="xoa_todo">
                                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                            <button type="submit" class="nut-icon-xoa" title="Xóa" aria-label="Xóa">✕</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($laChuTrang): ?>
                        <div class="todo-xoa-het-wrap">
                            <!-- Form xóa toàn bộ qua POST có CSRF token -->
                            <form method="post" action="gioithieu.php" class="todo-action-form" data-confirm="Xoá toàn bộ công việc trong CSDL?">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="xoa_het_todo">
                                <button type="submit" class="nut-lien-ket-xoa">🗑 Xóa hết danh sách</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- 2. Máy tính & Lịch sử BMI (tv_bmi_phim) -->
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">💪 Máy tính &amp; Lịch sử BMI (CSDL: tv_bmi_phim)</h4>
                
                <form method="post" action="gioithieu.php" class="bmi-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="tinh_bmi">
                    <div class="bmi-form__nhom">
                        <label for="chieu-cao-nhap" class="bmi-form__nhan">Chiều cao (cm):</label>
                        <input type="number" id="chieu-cao-nhap" name="chieucao" step="0.1" min="50" max="250" placeholder="Ví dụ: 170" required class="bmi-form__input">
                    </div>
                    <div class="bmi-form__nhom">
                        <label for="can-nang-nhap" class="bmi-form__nhan">Cân nặng (kg):</label>
                        <input type="number" id="can-nang-nhap" name="cannang" step="0.1" min="10" max="300" placeholder="Ví dụ: 65" required class="bmi-form__input">
                    </div>
                    <button class="nut nut--chinh" type="submit">Tính BMI &amp; Lưu CSDL</button>
                </form>

                <?php if ($bmiKetQua): ?>
                    <div class="bmi-ket-qua">
                        <p class="bmi-ket-qua__dong1"><strong>Kết quả vừa tính:</strong> BMI <span class="bmi-ket-qua__so"><?= (float)$bmiKetQua['bmi'] ?></span></p>
                        <p class="bmi-ket-qua__dong2"><strong>Đánh giá:</strong> <?= e($bmiKetQua['phan_loai']) ?> (<?= (float)$bmiKetQua['chieu_cao'] ?> cm, <?= (float)$bmiKetQua['can_nang'] ?> kg)</p>
                    </div>
                <?php endif; ?>

                <!-- Lịch sử đo gần đây -->
                <div class="bmi-lich-su">
                    <div class="bmi-lich-su__dau">
                        <span class="bmi-lich-su__tieu-de">📊 Lịch sử đo gần đây</span>
                        <?php if ($thongKeBmi['tong_lan'] > 0): ?>
                            <span class="bmi-lich-su__thong-ke">(TB: <strong><?= (float)$thongKeBmi['bmi_tb'] ?></strong> - <?= (int)$thongKeBmi['tong_lan'] ?> lần)</span>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($lichSuBmi)): ?>
                        <p class="todo-trong">Chưa có dữ liệu lịch sử đo BMI.</p>
                    <?php else: ?>
                        <div class="bmi-lich-su__bang-wrap">
                            <table class="bmi-bang">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Cao/Nặng</th>
                                        <th>BMI</th>
                                        <th>Đánh giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lichSuBmi as $itemBmi): ?>
                                        <tr>
                                            <td class="bmi-bang__ngay"><?= date('d/m/Y', strtotime($itemBmi['ngay_tao'])) ?></td>
                                            <td><?= (float)$itemBmi['chieu_cao'] ?>cm / <?= (float)$itemBmi['can_nang'] ?>kg</td>
                                            <td class="bmi-bang__so"><?= (float)$itemBmi['chi_so_bmi'] ?></td>
                                            <td class="bmi-bang__danh-gia"><?= e($itemBmi['phan_loai']) ?></td>
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
            <p class="chu-canh-giua tkb-phu-de">Lịch học và hoạt động trong tuần của Phimmasone Khamphouvanh</p>
            <div class="tkb-nut-wrap">
                <button id="btn-toggle-schedule" aria-expanded="true" class="nut nut--phu">Thu gọn / Mở rộng</button>
            </div>
            
            <div class="bang-cuon-ngang">
                <div id="schedule-container" class="tkb-container">
                    <div class="bang-chua">
                        <table class="bang-du-lieu tkb-table">
                            <caption>Lịch học và hoạt động trong tuần của Phimmasone Khamphouvanh</caption>
                            <thead>
                                <tr>
                                    <th scope="col" class="tkb-col-tiet">Tiết</th>
                                    <th scope="col" class="tkb-col-ngay">Thứ 2</th>
                                    <th scope="col" class="tkb-col-ngay">Thứ 3</th>
                                    <th scope="col" class="tkb-col-ngay">Thứ 4</th>
                                    <th scope="col" class="tkb-col-ngay">Thứ 5</th>
                                    <th scope="col" class="tkb-col-ngay">Thứ 6</th>
                                    <th scope="col" class="tkb-col-ngay">Thứ 7</th>
                                    <th scope="col" class="tkb-col-ngay">Chủ nhật</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Tiết 1 -->
                                <tr>
                                    <th scope="row" class="tkb-cell-center">1</th>
                                    <td></td>
                                    <td></td>
                                    <td rowspan="3" class="tkb-cell-mon">31241283 - 24-0102<br><strong>Hệ quản trị cơ sở dữ liệu</strong><br>Phòng: B3-402</td>
                                    <td></td>
                                    <td rowspan="3" class="tkb-cell-mon">31231398 - 24-0101<br><strong>Lập trình mạng</strong><br>Phòng: B3-303</td>
                                    <td rowspan="3" class="tkb-cell-mon">31231755 - 24-0102<br><strong>Thiết kế và lập trình web</strong><br>Phòng: B3-303</td>
                                    <td></td>
                                </tr>
                                <tr><th scope="row" class="tkb-cell-center">2</th><td></td><td></td><td></td><td></td></tr>
                                <tr><th scope="row" class="tkb-cell-center">3</th><td></td><td></td><td></td><td></td></tr>
                                <!-- Tiết 4 -->
                                <tr>
                                    <th scope="row" class="tkb-cell-center">4</th>
                                    <td></td>
                                    <td></td>
                                    <td rowspan="3" class="tkb-cell-mon">21221904 - 24-0315<br><strong>Lịch sử Đảng Cộng sản Việt Nam</strong><br>Phòng: A6-502</td>
                                    <td rowspan="3" class="tkb-cell-mon">31231330 - 24-0103<br><strong>Khai phá dữ liệu</strong><br>Phòng: A5-209</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr><th scope="row" class="tkb-cell-center">5</th><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><th scope="row" class="tkb-cell-center">6</th><td></td><td></td><td></td><td></td><td></td></tr>
                                <!-- Nghỉ trưa -->
                                <tr class="tkb-hang-nghi"><td colspan="8"></td></tr>
                                <!-- Tiết 7 -->
                                <tr><th scope="row" class="tkb-cell-center">7</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><th scope="row" class="tkb-cell-center">8</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><th scope="row" class="tkb-cell-center">9</th><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                <!-- Tiết 10 -->
                                <tr>
                                    <th scope="row" class="tkb-cell-center">10</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td rowspan="3" class="tkb-cell-mon">31231016 - 24-0103<br><strong>Công nghệ phần mềm</strong><br>Phòng: B3-304</td>
                                    <td></td>
                                    <td rowspan="3" class="tkb-cell-mon">31221010 - 24-0103<br><strong>An toàn thông tin</strong><br>Phòng: B3-506</td>
                                    <td></td>
                                </tr>
                                <tr><th scope="row" class="tkb-cell-center">11</th><td></td><td></td><td></td><td></td><td></td></tr>
                                <tr><th scope="row" class="tkb-cell-center">12</th><td></td><td></td><td></td><td></td><td></td></tr>
                            </tbody>
                        </table>
                    </div>
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
