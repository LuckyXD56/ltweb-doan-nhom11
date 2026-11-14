<?php
/**
 * gioithieu.php — Trang cá nhân của Silaphet Thit (MSSV: 3120224188 - Nhóm 11).
 * 
 * Bài tập thực hành nhóm số 6 - Phần B:
 *   1. Sổ lưu bút (CSDL MySQL: tv_luu_but): Lưu trữ CSDL, phân trang 5 mục/trang, phân quyền máy chủ
 *      chỉ chủ trang (đăng nhập silaphet@sanbong.vn hoặc admin) mới có quyền xóa (DELETE ... WHERE id = ? AND mssv = ?).
 *   2. Quản lý Dự án & Kỹ năng cá nhân (Bảng riêng CSDL: tv_du_an): Tìm kiếm theo từ khóa (LIKE), lọc theo loại,
 *      sắp xếp (whitelist an toàn), thống kê tiến độ trung bình (SQL AVG/COUNT), chủ trang có quyền thêm & xóa dự án.
 *   3. Bộ đếm lượt xem theo ngày (CSDL: tv_luot_xem): INSERT ... ON DUPLICATE KEY UPDATE và bảng 7 ngày gần nhất.
 * 
 * Cách thử:
 *   - Mở http://localhost:8000/thanhvien/3120224188_silaphet/gioithieu.php
 *   - Thử gửi lưu bút (POST), phân trang sang trang 2; đăng nhập silaphet@sanbong.vn (mật khẩu: Admin@123) để hiện nút Xóa.
 *   - Thử tìm kiếm dự án, lọc theo loại Web/Mobile/Design/Data, đổi sắp xếp; đăng nhập để thêm dự án mới.
 */

declare(strict_types=1);

$baseUrl = '../../';
$goc = '../../';

// Nạp cấu hình, hàm dùng chung và kết nối CSDL
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/ham.php';

const MSSV_SILAPHET = '3120224188';
$mssv = MSSV_SILAPHET;

// Kiểm tra kết nối CSDL PDO
$dbError = null;
if (!isset($pdo) || !$pdo) {
    try {
        $pdo = db();
    } catch (Throwable $ex) {
        $dbError = 'Chưa thể kết nối CSDL: ' . $ex->getMessage();
    }
}

// -------------------------------------------------------------
// XÁC THỰC VÀ PHÂN QUYỀN CHỦ TRANG PHÍA MÁY CHỦ
// -------------------------------------------------------------
$laChuTrang = false;
$thongTinTV = ['ho_ten' => 'Silaphet Thit', 'mssv' => $mssv];

if ($pdo) {
    try {
        $stTV = $pdo->prepare('SELECT mssv, ho_ten, nguoi_dung_id FROM thanh_vien WHERE mssv = ?');
        $stTV->execute([$mssv]);
        $tvRow = $stTV->fetch();
        if ($tvRow) {
            $thongTinTV = $tvRow;
        }
    } catch (Throwable $e) {}
}

if (!empty($_SESSION['da_dang_nhap'])) {
    $currentUid = (int)($_SESSION['nguoi_dung_id'] ?? 0);
    $currentRole = (string)($_SESSION['vai_tro'] ?? '');
    $currentEmail = (string)($_SESSION['email'] ?? '');
    
    if (
        ($tvRow && !empty($tvRow['nguoi_dung_id']) && $currentUid === (int)$tvRow['nguoi_dung_id'])
        || $currentEmail === 'silaphet@sanbong.vn'
        || $currentRole === 'quan_tri'
    ) {
        $laChuTrang = true;
    }
}

// -------------------------------------------------------------
// XỬ LÝ CÁC HÀNH ĐỘNG POST (MÔ HÌNH PRG - POST-REDIRECT-GET)
// -------------------------------------------------------------
$loiForm = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = postChuoi('action');

    // 1. Thêm lưu bút mới
    if ($action === 'them_luubut') {
        $ten = postChuoi('ten');
        $loiNhan = postChuoi('loi_nhan');

        if ($ten === '' || mb_strlen($ten) > 50) {
            $loiForm['lb_ten'] = 'Tên không được để trống và tối đa 50 ký tự.';
        }
        if ($loiNhan === '' || mb_strlen($loiNhan) > 500) {
            $loiForm['lb_loi_nhan'] = 'Lời nhắn không được để trống và tối đa 500 ký tự.';
        }

        if (empty($loiForm) && $pdo) {
            try {
                $st = $pdo->prepare('INSERT INTO tv_luu_but (mssv, ho_ten, loi_nhan) VALUES (?, ?, ?)');
                $st->execute([$mssv, $ten, $loiNhan]);
                $_SESSION['flash_msg'] = ['loai' => 'thanh_cong', 'noi_dung' => 'Cảm ơn bạn đã gửi lời nhắn lưu bút!'];
            } catch (Throwable $e) {
                $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Lỗi lưu dữ liệu: ' . $e->getMessage()];
            }
            chuyenHuong('gioithieu.php#so-luu-but');
        }
    }

    // 2. Xóa lưu bút (CHỈ CHỦ TRANG MỚI ĐƯỢC XÓA)
    elseif ($action === 'xoa_luubut') {
        $idXoa = getInt('id_xoa', 0);
        if (!$idXoa) {
            $idXoa = (int)($_POST['id_xoa'] ?? 0);
        }

        if (!$laChuTrang) {
            $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Bảo mật: Bạn không có quyền xóa lời nhắn này!'];
        } elseif ($idXoa > 0 && $pdo) {
            try {
                // Ràng buộc WHERE id = ? AND mssv = ? đảm bảo không bao giờ xóa nhầm trang khác
                $st = $pdo->prepare('DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?');
                $st->execute([$idXoa, $mssv]);
                $_SESSION['flash_msg'] = ['loai' => 'thanh_cong', 'noi_dung' => 'Đã xóa lời nhắn thành công.'];
            } catch (Throwable $e) {
                $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Lỗi xóa lời nhắn: ' . $e->getMessage()];
            }
        }
        chuyenHuong('gioithieu.php#so-luu-but');
    }

    // 3. Thêm dự án mới (BẢNG RIÊNG tv_du_an - CHỈ CHỦ TRANG)
    elseif ($action === 'them_du_an') {
        if (!$laChuTrang) {
            $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Chỉ chủ trang mới có quyền thêm dự án.'];
            chuyenHuong('gioithieu.php#quan-ly-du-an');
        }

        $tenDuAn = postChuoi('ten_du_an');
        $loaiDuAn = postChuoi('loai_du_an', 'Web');
        $congNghe = postChuoi('cong_nghe');
        $moTa = postChuoi('mo_ta');
        $tienDo = filter_var($_POST['tien_do'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]);
        $trangThai = postChuoi('trang_thai', 'dang_lam');

        $loaiHopLe = ['Web', 'Mobile', 'Design', 'Data'];
        $trangThaiHopLe = ['dang_lam', 'hoan_thanh', 'tam_dung'];

        if ($tenDuAn === '' || mb_strlen($tenDuAn) > 150) {
            $loiForm['da_ten'] = 'Tên dự án từ 1 đến 150 ký tự.';
        }
        if (!in_array($loaiDuAn, $loaiHopLe, true)) {
            $loiForm['da_loai'] = 'Loại dự án không hợp lệ.';
        }
        if ($congNghe === '' || mb_strlen($congNghe) > 200) {
            $loiForm['da_cong_nghe'] = 'Công nghệ từ 1 đến 200 ký tự.';
        }
        if ($moTa === '') {
            $loiForm['da_mo_ta'] = 'Vui lòng nhập mô tả dự án.';
        }
        if ($tienDo === false) {
            $loiForm['da_tien_do'] = 'Tiến độ phải là số nguyên từ 0 đến 100.';
        }
        if (!in_array($trangThai, $trangThaiHopLe, true)) {
            $loiForm['da_trang_thai'] = 'Trạng thái không hợp lệ.';
        }

        if (empty($loiForm) && $pdo) {
            try {
                $st = $pdo->prepare(
                    'INSERT INTO tv_du_an (mssv, ten_du_an, loai_du_an, mo_ta, cong_nghe, tien_do, trang_thai)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $st->execute([$mssv, $tenDuAn, $loaiDuAn, $moTa, $congNghe, $tienDo, $trangThai]);
                $_SESSION['flash_msg'] = ['loai' => 'thanh_cong', 'noi_dung' => 'Đã thêm dự án mới thành công vào CSDL!'];
            } catch (Throwable $e) {
                $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Lỗi thêm dự án: ' . $e->getMessage()];
            }
            chuyenHuong('gioithieu.php#quan-ly-du-an');
        }
    }

    // 4. Xóa dự án (BẢNG RIÊNG tv_du_an - CHỈ CHỦ TRANG)
    elseif ($action === 'xoa_du_an') {
        $idDuAnXoa = (int)($_POST['id_du_an'] ?? 0);
        if (!$laChuTrang) {
            $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Bạn không có quyền xóa dự án này!'];
        } elseif ($idDuAnXoa > 0 && $pdo) {
            try {
                $st = $pdo->prepare('DELETE FROM tv_du_an WHERE id = ? AND mssv = ?');
                $st->execute([$idDuAnXoa, $mssv]);
                $_SESSION['flash_msg'] = ['loai' => 'thanh_cong', 'noi_dung' => 'Đã xóa dự án thành công.'];
            } catch (Throwable $e) {
                $_SESSION['flash_msg'] = ['loai' => 'loi', 'noi_dung' => 'Lỗi xóa dự án: ' . $e->getMessage()];
            }
        }
        chuyenHuong('gioithieu.php#quan-ly-du-an');
    }
}

// -------------------------------------------------------------
// CHỨC NĂNG 3: BỘ ĐẾM LƯỢT XEM THEO NGÀY (CSDL tv_luot_xem)
// -------------------------------------------------------------
if (empty($_SESSION['da_xem_silaphet_db']) && $pdo) {
    $_SESSION['da_xem_silaphet_db'] = true;
    try {
        $stLX = $pdo->prepare(
            'INSERT INTO tv_luot_xem (mssv, ngay, luot_xem) 
             VALUES (?, CURRENT_DATE, 1) 
             ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1'
        );
        $stLX->execute([$mssv]);
    } catch (Throwable $e) {}
}

$tongLuotXem = 0;
$luotXemHomNay = 0;
$ds7Ngay = [];
if ($pdo) {
    try {
        $stTong = $pdo->prepare('SELECT COALESCE(SUM(luot_xem), 0) FROM tv_luot_xem WHERE mssv = ?');
        $stTong->execute([$mssv]);
        $tongLuotXem = (int)$stTong->fetchColumn();

        $stHN = $pdo->prepare('SELECT luot_xem FROM tv_luot_xem WHERE mssv = ? AND ngay = CURRENT_DATE');
        $stHN->execute([$mssv]);
        $luotXemHomNay = (int)($stHN->fetchColumn() ?: 0);

        $st7 = $pdo->prepare("SELECT DATE_FORMAT(ngay, '%d/%m/%Y') AS ngay_dmy, luot_xem FROM tv_luot_xem WHERE mssv = ? ORDER BY ngay DESC LIMIT 7");
        $st7->execute([$mssv]);
        $ds7Ngay = $st7->fetchAll();
    } catch (Throwable $e) {}
}

// -------------------------------------------------------------
// CHỨC NĂNG 1: SỔ LƯU BÚT CÓ PHÂN TRANG (CSDL tv_luu_but)
// -------------------------------------------------------------
$moiTrangLB = 5;
$tongLuuBut = 0;
$soTrangLB = 1;
$trangHienTaiLB = 1;
$dsLuuBut = [];

if ($pdo) {
    try {
        $stDem = $pdo->prepare('SELECT COUNT(*) FROM tv_luu_but WHERE mssv = ?');
        $stDem->execute([$mssv]);
        $tongLuuBut = (int)$stDem->fetchColumn();
        $soTrangLB = max(1, (int)ceil($tongLuuBut / $moiTrangLB));

        $trangHienTaiLB = filter_var($_GET['trang_lb'] ?? 1, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'default' => 1]
        ]);
        $trangHienTaiLB = min($trangHienTaiLB, $soTrangLB);

        $stLB = $pdo->prepare(
            'SELECT id, ho_ten, loi_nhan, ngay_tao 
             FROM tv_luu_but 
             WHERE mssv = ? 
             ORDER BY ngay_tao DESC, id DESC 
             LIMIT ? OFFSET ?'
        );
        $stLB->execute([$mssv, $moiTrangLB, ($trangHienTaiLB - 1) * $moiTrangLB]);
        $dsLuuBut = $stLB->fetchAll();
    } catch (Throwable $e) {}
}

// -------------------------------------------------------------
// CHỨC NĂNG 2: QUẢN LÝ DỰ ÁN CÁ NHÂN (BẢNG RIÊNG tv_du_an)
// Tìm kiếm theo từ khóa + Lọc theo loại + Sắp xếp với Whitelist
// -------------------------------------------------------------
$q_da = trim((string)($_GET['q_da'] ?? ''));
$loai_da = trim((string)($_GET['loai_da'] ?? ''));
$sx_da = trim((string)($_GET['sx_da'] ?? 'moi'));

$dsDuAn = [];
$thongKeDuAn = ['tong' => 0, 'hoan_thanh' => 0, 'tien_do_tb' => 0];

if ($pdo) {
    try {
        // Thống kê SQL tổng hợp
        $stTK = $pdo->prepare(
            "SELECT 
                COUNT(*) AS tong,
                COALESCE(SUM(CASE WHEN trang_thai = 'hoan_thanh' THEN 1 ELSE 0 END), 0) AS hoan_thanh,
                ROUND(COALESCE(AVG(tien_do), 0), 0) AS tien_do_tb
             FROM tv_du_an 
             WHERE mssv = ?"
        );
        $stTK->execute([$mssv]);
        $thongKeDuAn = $stTK->fetch() ?: $thongKeDuAn;

        // Xây dựng điều kiện WHERE động an toàn
        $whereSql = ['mssv = ?'];
        $paramsSql = [$mssv];

        if ($q_da !== '') {
            $whereSql[] = '(ten_du_an LIKE ? OR cong_nghe LIKE ?)';
            $likeStr = '%' . addcslashes($q_da, '%_\\') . '%';
            $paramsSql[] = $likeStr;
            $paramsSql[] = $likeStr;
        }

        if ($loai_da !== '' && in_array($loai_da, ['Web', 'Mobile', 'Design', 'Data'], true)) {
            $whereSql[] = 'loai_du_an = ?';
            $paramsSql[] = $loai_da;
        }

        // Whitelist sắp xếp an toàn
        $orderBySql = match ($sx_da) {
            'cu'            => 'ngay_tao ASC, id ASC',
            'tien-do-giam'  => 'tien_do DESC, id DESC',
            'tien-do-tang'  => 'tien_do ASC, id ASC',
            'ten'           => 'ten_du_an ASC, id ASC',
            default         => 'ngay_tao DESC, id DESC',
        };

        $whereClause = 'WHERE ' . implode(' AND ', $whereSql);
        $stDA = $pdo->prepare("SELECT * FROM tv_du_an $whereClause ORDER BY $orderBySql");
        $stDA->execute($paramsSql);
        $dsDuAn = $stDA->fetchAll();
    } catch (Throwable $e) {}
}

// Lấy thông báo Flash
$flash = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_msg']);

// Tiêu đề trang cho header dùng chung
$tieuDeTrang = 'Silaphet Thit - Trang cá nhân';
$customCss = '<link rel="stylesheet" href="css/profile.css">';
require_once __DIR__ . '/../../inc/header.php';
?>

<main class="trang__main profile-main">
    <h1 class="chu-canh-giua mb-1">Trang cá nhân thành viên Silaphet Thit</h1>

    <!-- Thông báo Flash (PRG) -->
    <?php if ($flash): ?>
        <div class="vung-thong-bao vung-thong-bao--<?= $flash['loai'] === 'thanh_cong' ? 'thanh-cong' : 'loi' ?>" 
             role="status" 
             class="thong-bao-flash thong-bao-flash--<?= $flash['loai'] ?>">
            <?= e($flash['noi_dung']) ?>
        </div>
    <?php endif; ?>

    <?php if ($dbError): ?>
        <div class="slp-s51">
            <strong>Thông báo cơ sở dữ liệu:</strong> <?= e($dbError) ?><br>
            <small>Vui lòng đảm bảo dịch vụ MySQL đang chạy và đã nạp <code>schema.sql</code>, <code>seed.sql</code>.</small>
        </div>
    <?php endif; ?>

    <!-- THÔNG TIN CÁ NHÂN -->
    <section class="profile-card">
        <div class="profile-card__img-container">
            <img
                src="../../images/silaphet.jpg"
                alt="Ảnh chân dung của Silaphet Thit"
                class="profile-card__img"
                width="170"
                height="170"
            >
        </div>

        <div class="profile-card__info">
            <h2 class="profile-card__name">Silaphet Thit</h2>
            <p class="profile-card__role">
                <strong>Vai trò:</strong> Thành viên Nhóm 11
            </p>
            <p class="profile-card__id">
                <strong>Mã sinh viên:</strong> <?= e($mssv) ?>
            </p>
            <p>
                <strong>Nhiệm vụ đồ án:</strong>
                Phụ trách chức năng Liên hệ (lưu CSDL kèm upload ảnh), bảng quản trị liên hệ và trang cá nhân.
            </p>

            <div class="slp-s22">
                <?php if ($laChuTrang): ?>
                    <span class="slp-s12">🔑 Bạn đang đăng nhập với tư cách: Chủ trang (quyền Quản trị viên)</span>
                <?php else: ?>
                    <span class="slp-s50">👤 Khách truy cập (Chế độ xem). <a href="../../dang-nhap.php" class="slp-s47">Đăng nhập</a> bằng <code>silaphet@sanbong.vn</code> để quản trị trang.</span>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-1 flex-wrap mt-1-5">
                <div class="flex-1 min-w-200">
                    <h3 class="mt-0 text-primary mb-0-5 slp-s57" >Chuyên ngành</h3>
                    <ul class="ky-nang-list">
                        <li>Khoa Toán - Tin</li>
                        <li>CNTT - Đại học Sư phạm Đà Nẵng</li>
                    </ul>
                </div>

                <div class="flex-1 min-w-200">
                    <h3 class="mt-0 text-primary mb-0-5 slp-s57" >Kỹ năng công nghệ</h3>
                    <ul class="ky-nang-list">
                        <li>PHP 8 &amp; PDO</li>
                        <li>MySQL Database</li>
                        <li>HTML5 / CSS3</li>
                        <li>JavaScript ES6</li>
                        <li>Google Maps API</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- SỞ THÍCH & MỤC TIÊU -->
    <section class="hobbies-section">
        <h3 class="text-primary mt-0">Sở thích và mục tiêu</h3>
        <p class="text-secondary">
            Silaphet yêu thích thể thao bóng đá, tìm hiểu công nghệ web, lập trình cơ sở dữ liệu và giao lưu văn hóa quốc tế giữa Lào và Việt Nam.
            Mục tiêu là xây dựng website Sân Bóng Thắng Lợi hoạt động mượt mà, bảo mật dữ liệu, chống SQL Injection bằng PDO Prepared Statements và đạt điểm tối đa bài tập nhóm số 6.
        </p>
    </section>

    <!-- THỜI KHÓA BIỂU -->
    <section class="profile-schedule">
        <h3 class="chu-canh-giua mb-1 text-primary">Thời khóa biểu tuần</h3>
        <div class="bang-cuon-ngang">
            <table class="bang-du-lieu">
                <caption>Lịch học tập và làm việc trong tuần của Silaphet Thit</caption>
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
                        <td>Học tập học phần Thiết kế &amp; Lập trình Web</td>
                        <td>07:00 - 11:00</td>
                    </tr>
                    <tr>
                        <th scope="row">Thứ Năm</th>
                        <td>Thiết kế CSDL MySQL, chuẩn hóa lược đồ 3NF</td>
                        <td>13:30 - 17:00</td>
                    </tr>
                    <tr>
                        <th scope="row">Thứ Bảy</th>
                        <td>Họp Nhóm 11 kiểm tra tiến độ và kiểm thử ca Bảng 3</td>
                        <td>19:00 - 21:00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- ========================================================= -->
    <!-- PHẦN B - CHỨC NĂNG 3: BỘ ĐẾM LƯỢT XEM THEO NGÀY (CSDL)    -->
    <!-- ========================================================= -->
    <section class="profile-schedule" aria-labelledby="tieu-de-luot-xem">
        <h3 id="tieu-de-luot-xem" class="text-primary">Thống kê lượt xem trang cá nhân (CSDL <code>tv_luot_xem</code>)</h3>
        <p class="text-secondary">
            Chức năng đếm lượt xem theo ngày sử dụng câu lệnh <code>INSERT INTO tv_luot_xem (mssv, ngay, luot_xem) VALUES (?, CURRENT_DATE, 1) ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1</code>.
        </p>

        <div class="slp-s27">
            <div class="slp-s16">
                <span class="slp-s2">Lượt xem hôm nay</span>
                <p class="slp-s83"><?= $luotXemHomNay ?></p>
            </div>
            <div class="slp-s37">
                <span class="slp-s36">Tổng số lượt xem</span>
                <p class="slp-s44"><?= $tongLuotXem ?></p>
            </div>
        </div>

        <?php if (!empty($ds7Ngay)): ?>
            <h4 class="slp-s31">Chi tiết 7 ngày gần nhất:</h4>
            <div class="bang-cuon-ngang">
                <table class="bang-du-lieu slp-s74" >
                    <thead>
                        <tr>
                            <th scope="col">Ngày ghi nhận</th>
                            <th scope="col">Số lượt xem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ds7Ngay as $dongLX): ?>
                            <tr>
                                <td><?= e($dongLX['ngay_dmy']) ?></td>
                                <td><strong><?= (int)$dongLX['luot_xem'] ?></strong> lượt</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <!-- ========================================================= -->
    <!-- PHẦN B - CHỨC NĂNG 2: QUẢN LÝ DỰ ÁN CÁ NHÂN (BẢNG RIÊNG)   -->
    <!-- Bảng tv_du_an: Tìm kiếm, lọc loại, sắp xếp whitelist, CRUD -->
    <!-- ========================================================= -->
    <section class="profile-schedule" id="quan-ly-du-an">
        <div class="slp-s79">
            <h3 class="text-primary slp-s55" >Quản lý Dự án &amp; Sản phẩm cá nhân (Bảng riêng <code>tv_du_an</code>)</h3>
            <?php if ($laChuTrang): ?>
                <button type="button" class="nut nut--chinh slp-s80"  id="nut-cuon-them-du-an" >
                    + Thêm dự án mới
                </button>
            <?php endif; ?>
        </div>

        <p class="text-secondary">
            Bảng riêng <code>tv_du_an</code> thuộc Phần B của Silaphet Thit. Có khóa ngoại <code>mssv</code> liên kết với <code>thanh_vien(mssv)</code>, hỗ trợ tìm kiếm từ khóa với <code>LIKE</code>, lọc danh mục, sắp xếp theo Whitelist và thống kê tiến độ bằng hàm tổng hợp SQL.
        </p>

        <!-- Khối thống kê SQL trực tiếp từ tv_du_an -->
        <div class="slp-s27">
            <div class="slp-s43">
                <span class="slp-s10">Tổng số dự án</span>
                <p class="slp-s11"><?= (int)$thongKeDuAn['tong'] ?></p>
            </div>
            <div class="slp-s61">
                <span class="slp-s72">Đã hoàn thành</span>
                <p class="slp-s82"><?= (int)$thongKeDuAn['hoan_thanh'] ?></p>
            </div>
            <div class="slp-s41">
                <span class="slp-s30">Tiến độ trung bình (SQL AVG)</span>
                <p class="slp-s5"><?= (int)$thongKeDuAn['tien_do_tb'] ?>%</p>
            </div>
        </div>

        <!-- Biểu mẫu Tìm kiếm, Lọc và Sắp xếp Dự án -->
        <form method="get" action="gioithieu.php#quan-ly-du-an" class="slp-s21">
            <div class="slp-s68">
                <div class="slp-s54">
                    <label for="q_da" class="slp-s56">Tìm từ khóa</label>
                    <input type="text" id="q_da" name="q_da" value="<?= e($q_da) ?>" placeholder="Tên dự án hoặc công nghệ..." class="truong__nhap" class="slp-s15">
                </div>

                <div class="slp-s77">
                    <label for="loai_da" class="slp-s56">Loại dự án</label>
                    <select id="loai_da" name="loai_da" class="truong__nhap slp-s15" >
                        <option value="">-- Tất cả loại --</option>
                        <option value="Web" <?= $loai_da === 'Web' ? 'selected' : '' ?>>Web App</option>
                        <option value="Mobile" <?= $loai_da === 'Mobile' ? 'selected' : '' ?>>Mobile App</option>
                        <option value="Design" <?= $loai_da === 'Design' ? 'selected' : '' ?>>UI/UX Design</option>
                        <option value="Data" <?= $loai_da === 'Data' ? 'selected' : '' ?>>Data Analysis</option>
                    </select>
                </div>

                <div class="slp-s33">
                    <label for="sx_da" class="slp-s56">Sắp xếp (Whitelist)</label>
                    <select id="sx_da" name="sx_da" class="truong__nhap slp-s15" >
                        <option value="moi" <?= $sx_da === 'moi' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="cu" <?= $sx_da === 'cu' ? 'selected' : '' ?>>Cũ nhất</option>
                        <option value="tien-do-giam" <?= $sx_da === 'tien-do-giam' ? 'selected' : '' ?>>Tiến độ cao nhất</option>
                        <option value="tien-do-tang" <?= $sx_da === 'tien-do-tang' ? 'selected' : '' ?>>Tiến độ thấp nhất</option>
                        <option value="ten" <?= $sx_da === 'ten' ? 'selected' : '' ?>>Tên A – Z</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="nut nut--chinh slp-s13" >Lọc dữ liệu</button>
                    <?php if ($q_da !== '' || $loai_da !== '' || $sx_da !== 'moi'): ?>
                        <a href="gioithieu.php#quan-ly-du-an" class="nut nut--phu slp-s60" >Đặt lại</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Danh sách thẻ Dự án -->
        <?php if (empty($dsDuAn)): ?>
            <div class="slp-s70">
                Không tìm thấy dự án nào khớp với điều kiện tìm kiếm.
            </div>
        <?php else: ?>
            <div class="slp-s38">
                <?php foreach ($dsDuAn as $da): ?>
                    <article class="slp-s19" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
                        <div>
                            <div class="slp-s64">
                                <h4 class="slp-s73"><?= e($da['ten_du_an']) ?></h4>
                                <span class="slp-s6">
                                    <?= e($da['loai_du_an']) ?>
                                </span>
                            </div>

                            <p class="slp-s53">
                                <?= e($da['mo_ta']) ?>
                            </p>

                            <p class="slp-s9">
                                <strong>Công nghệ:</strong> <span class="slp-s25"><?= e($da['cong_nghe']) ?></span>
                            </p>
                        </div>

                        <div>
                            <!-- Thanh tiến độ hoàn thành -->
                            <div class="slp-s58">
                                <div class="slp-s26">
                                    <span class="slp-s62">Tiến độ</span>
                                    <span class="chu-tien-do <?= (int)$da['tien_do'] === 100 ? 'chu-tien-do--xong' : 'chu-tien-do--dang-lam' ?>"><?= (int)$da['tien_do'] ?>%</span>
                                </div>
                                <div class="slp-s40">
                                    <div class="thanh-tien-do-ruot <?= (int)$da['tien_do'] === 100 ? 'thanh-tien-do-ruot--xong' : '' ?>" data-tien-do="<?= (int)$da['tien_do'] ?>"></div>
                                </div>
                            </div>

                            <div class="slp-s20">
                                <span>Trạng thái: 
                                    <strong class="chu-trang-thai-da chu-trang-thai-da--<?= e($da['trang_thai']) ?>">
                                        <?= $da['trang_thai'] === 'hoan_thanh' ? 'Hoàn thành' : ($da['trang_thai'] === 'dang_lam' ? 'Đang thực hiện' : 'Tạm dừng') ?>
                                    </strong>
                                </span>

                                <?php if ($laChuTrang): ?>
                                    <form method="post" action="gioithieu.php#quan-ly-du-an" data-xac-nhan="Bạn có chắc chắn muốn xóa dự án này?"  class="slp-s55">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="xoa_du_an">
                                        <input type="hidden" name="id_du_an" value="<?= (int)$da['id'] ?>">
                                        <button type="submit" class="slp-s14">
                                            Xóa
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Form thêm dự án mới (chỉ dành cho chủ trang) -->
        <?php if ($laChuTrang): ?>
            <div id="form-them-du-an" class="slp-s4">
                <h4 class="slp-s29">Thêm dự án cá nhân mới vào CSDL (Quyền chủ trang)</h4>

                <form method="post" action="gioithieu.php#quan-ly-du-an">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="them_du_an">

                    <div class="slp-s81">
                        <div>
                            <label for="ten_du_an" class="slp-s3">Tên dự án (*)</label>
                            <input type="text" id="ten_du_an" name="ten_du_an" class="truong__nhap slp-s7"  required maxlength="150" placeholder="VD: Ứng dụng Quản lý...">
                            <?php if (!empty($loiForm['da_ten'])): ?>
                                <span class="slp-s52"><?= e($loiForm['da_ten']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="loai_du_an_them" class="slp-s3">Phân loại (*)</label>
                            <select id="loai_du_an_them" name="loai_du_an" class="truong__nhap slp-s7" >
                                <option value="Web">Web Application</option>
                                <option value="Mobile">Mobile Application</option>
                                <option value="Design">UI/UX Design</option>
                                <option value="Data">Data Analysis</option>
                            </select>
                        </div>
                    </div>

                    <div class="slp-s81">
                        <div>
                            <label for="cong_nghe" class="slp-s3">Công nghệ sử dụng (*)</label>
                            <input type="text" id="cong_nghe" name="cong_nghe" class="truong__nhap slp-s7"  required maxlength="200" placeholder="VD: PHP 8, MySQL, Flutter...">
                        </div>

                        <div>
                            <label for="tien_do" class="slp-s3">Tiến độ hoàn thành (0 – 100%)</label>
                            <input type="number" id="tien_do" name="tien_do" class="truong__nhap slp-s7"  min="0" max="100" value="50" required>
                        </div>

                        <div>
                            <label for="trang_thai_them" class="slp-s3">Trạng thái</label>
                            <select id="trang_thai_them" name="trang_thai" class="truong__nhap slp-s7" >
                                <option value="dang_lam">Đang thực hiện</option>
                                <option value="hoan_thanh">Đã hoàn thành</option>
                                <option value="tam_dung">Tạm dừng</option>
                            </select>
                        </div>
                    </div>

                    <div class="slp-s46">
                        <label for="mo_ta" class="slp-s3">Mô tả ngắn dự án (*)</label>
                        <textarea id="mo_ta" name="mo_ta" class="truong__nhap slp-s7" rows="3"  required placeholder="Mô tả mục tiêu, tính năng chính của sản phẩm..."></textarea>
                    </div>

                    <button type="submit" class="nut nut--chinh">Lưu dự án mới</button>
                </form>
            </div>
        <?php endif; ?>
    </section>

    <!-- ========================================================= -->
    <!-- PHẦN B - CHỨC NĂNG 1: SỔ LƯU BÚT PHÂN TRANG (CSDL tv_luu_but) -->
    <!-- Chuyển đổi từ JSONL cũ sang MySQL, phân quyền xóa ở server -->
    <!-- ========================================================= -->
    <section class="profile-schedule" id="so-luu-but">
        <h3 class="text-primary">Sổ lưu bút thành viên (CSDL <code>tv_luu_but</code>)</h3>
        <p class="text-secondary">
            Chức năng sổ lưu bút được nâng cấp từ tệp JSONL của Bài tập 5 sang bảng cơ sở dữ liệu MySQL <code>tv_luu_but</code> với Prepared Statements. Hỗ trợ phân trang 5 lời nhắn/trang và kiểm tra quyền máy chủ: chỉ chủ trang (hoặc quản trị viên) mới có quyền xóa.
        </p>

        <!-- Form gửi lưu bút -->
        <form action="gioithieu.php#so-luu-but" method="post" class="slp-s17">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="them_luubut">

            <div class="slp-s46">
                <label for="ten_khach" class="slp-s3">Tên của bạn (*)</label>
                <input
                    type="text"
                    id="ten_khach"
                    name="ten"
                    class="truong__nhap slp-s7"
                    
                    maxlength="50"
                    placeholder="Nhập họ tên của bạn..."
                    required
                >
                <?php if (!empty($loiForm['lb_ten'])): ?>
                    <span class="slp-s52"><?= e($loiForm['lb_ten']) ?></span>
                <?php endif; ?>
            </div>

            <div class="slp-s46">
                <label for="loi_nhan_khach" class="slp-s3">Lời nhắn gửi tới Silaphet Thit (*)</label>
                <textarea
                    id="loi_nhan_khach"
                    name="loi_nhan"
                    class="truong__nhap slp-s7"
                    rows="3"
                    
                    maxlength="500"
                    placeholder="Để lại lời chúc hoặc góp ý cho trang web..."
                    required
                ></textarea>
                <?php if (!empty($loiForm['lb_loi_nhan'])): ?>
                    <span class="slp-s52"><?= e($loiForm['lb_loi_nhan']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="nut nut--chinh">
                Gửi lời nhắn lưu bút
            </button>
        </form>

        <h4 class="slp-s78">
            Danh sách lời nhắn (Tổng cộng: <?= $tongLuuBut ?> lời nhắn – Trang <?= $trangHienTaiLB ?>/<?= $soTrangLB ?>)
        </h4>

        <?php if (!empty($dsLuuBut)): ?>
            <div class="slp-s34">
                <?php foreach ($dsLuuBut as $tin): ?>
                    <div class="slp-s35">
                        <div class="slp-s49">
                            <strong class="slp-s28"><?= e($tin['ho_ten']) ?></strong>
                            <span class="slp-s42"><?= date('d/m/Y H:i', strtotime($tin['ngay_tao'])) ?></span>
                        </div>
                        <p class="slp-s65"><?= nl2br(e($tin['loi_nhan'])) ?></p>

                        <!-- NÚT XÓA: CHỈ HIỂN THỊ KHI ĐĂNG NHẬP LÀ CHỦ TRANG / QUẢN TRỊ VIÊN -->
                        <?php if ($laChuTrang): ?>
                            <div class="slp-s48">
                                <form method="post" action="gioithieu.php#so-luu-but" class="slp-s75" data-xac-nhan="Bạn có chắc muốn xóa lời nhắn này khỏi CSDL?" >
                                    <?= csrfField() ?>
                                    <input type="hidden" name="action" value="xoa_luubut">
                                    <input type="hidden" name="id_xoa" value="<?= (int)$tin['id'] ?>">
                                    <button type="submit" class="nut nut--phu slp-s8" >
                                        🗑️ Xóa lời nhắn
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- PHÂN TRANG SỔ LƯU BÚT -->
            <?php if ($soTrangLB > 1): ?>
                <nav aria-label="Phân trang lưu bút" class="slp-s76">
                    <?php if ($trangHienTaiLB > 1): ?>
                        <a href="?trang_lb=<?= $trangHienTaiLB - 1 ?>#so-luu-but" class="nut nut--phu" class="slp-s39">&laquo; Trước</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $soTrangLB; $i++): ?>
                        <a href="?trang_lb=<?= $i ?>#so-luu-but" 
                           class="nut <?= $i === $trangHienTaiLB ? 'nut--chinh' : 'nut--phu' ?> nut-phan-trang-lb <?= $i === $trangHienTaiLB ? 'nut-phan-trang-lb--hien-tai' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($trangHienTaiLB < $soTrangLB): ?>
                        <a href="?trang_lb=<?= $trangHienTaiLB + 1 ?>#so-luu-but" class="nut nut--phu" class="slp-s39">Sau &raquo;</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <p class="slp-s23">Chưa có lời nhắn lưu bút nào. Hãy là người đầu tiên để lại lời chúc!</p>
        <?php endif; ?>
    </section>

    <!-- TƯƠNG TÁC JAVASCRIPT GIỮ LẠI TỪ BÀI TẬP TRƯỚC -->
    <article class="profile-schedule slp-s18" >
        <h3 class="text-primary">Tiện ích tương tác bổ trợ</h3>
        
        <h4>Đếm ngược đến ngày bảo vệ đồ án</h4>
        <div id="dong-ho" class="slp-s71">
            Đang tính toán thời gian...
        </div>

        <h4>Hình ảnh sân bóng tiêu biểu</h4>
        <div class="slideshow-container slp-s69" >
            <img
                id="anh-slide"
                src="../../images/san-a1.jpg"
                alt="Ảnh sân bóng trong slideshow"
                class="slp-s59"
            >
        </div>
        <p class="slp-s24">
            (Hình ảnh tự động chuyển động sau mỗi 3 giây)
        </p>
    </article>

    <p class="chu-canh-giua mt-1 slp-s67" >
        <a href="../../thanh-vien.php" class="nut nut--phu">
            ← Quay lại danh sách thành viên nhóm
        </a>
    </p>
</main>

<script type="module" src="js/canhan.js"></script>

<?php
require_once __DIR__ . '/../../inc/footer.php';
?>