
    <?php
    /*
    * Trang cá nhân Silaphet Thit, Nhóm 11.
    * Chức năng: sổ lưu bút và bộ đếm lượt xem bằng PHP.
    * Cách thử: mở gioithieu.php qua máy chủ PHP, gửi lưu bút và tải lại trang.
    * Header/footer dùng chung được nạp từ thư mục inc của dự án.
    */

    session_start();

    $goc = '../../';
    $storageDir = __DIR__ . '/../../storage';
    $guestbookFile = $storageDir . '/3120224188_luubut.jsonl';
    $counterFile = $storageDir . '/3120224188_luotxem.txt';

    if (!is_dir($storageDir)) {
        if (!@mkdir($storageDir, 0755, true) && !is_dir($storageDir)) {
            error_log('Khong the tao thu muc storage.');
        }
    }

    /**
     * Escape dữ liệu trước khi in vào HTML.
     */
    function e($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );
    }

    /**
     * Đọc các dòng JSONL, chỉ lấy 5 lời nhắn mới nhất.
     */
    function docLuuBut($file)
    {
        if (!is_file($file) || !is_readable($file)) {
            return [];
        }

        $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!is_array($lines)) {
            return [];
        }

        $messages = [];

        foreach ($lines as $line) {
            $item = json_decode($line, true);

            if (
                is_array($item)
                && isset($item['ten'], $item['loi_nhan'], $item['thoi_gian'])
                && is_string($item['ten'])
                && is_string($item['loi_nhan'])
                && is_string($item['thoi_gian'])
            ) {
                $messages[] = $item;
            }
        }

        return array_slice(array_reverse($messages), 0, 5);
    }

    /**
     * Tăng bộ đếm một cách an toàn khi nhiều yêu cầu cùng lúc.
     */
    function tangLuotXem($file)
    {
        $fp = @fopen($file, 'c+');

        if ($fp === false) {
            return null;
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return null;
        }

        rewind($fp);
        $value = trim((string) stream_get_contents($fp));
        $count = ctype_digit($value) ? (int) $value : 0;
        $count++;

        rewind($fp);
        ftruncate($fp, 0);
        fwrite($fp, (string) $count);
        fflush($fp);

        flock($fp, LOCK_UN);
        fclose($fp);

        return $count;
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /*
    * Mỗi phiên chỉ cộng lượt xem một lần.
    */
    if (empty($_SESSION['silaphet_da_tinh_luot_xem'])) {
        $_SESSION['silaphet_da_tinh_luot_xem'] = true;
        $ketQuaDem = tangLuotXem($counterFile);

        if ($ketQuaDem !== null) {
            $_SESSION['silaphet_luot_xem'] = $ketQuaDem;
        }
    }

    $luotXem = null;

    if (is_file($counterFile) && is_readable($counterFile)) {
        $giaTriDem = trim((string) @file_get_contents($counterFile));

        if (ctype_digit($giaTriDem)) {
            $luotXem = (int) $giaTriDem;
        }
    }

    $loi = '';
    $thanhCong = isset($_GET['luu']) && $_GET['luu'] === 'thanhcong';

    /*
    * Xử lý sổ lưu bút bằng POST.
    */
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
            ? $_POST['csrf_token']
            : '';

        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            $loi = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
        } elseif (
            !isset($_POST['ten'], $_POST['loi_nhan'])
            || !is_string($_POST['ten'])
            || !is_string($_POST['loi_nhan'])
        ) {
            $loi = 'Dữ liệu gửi lên không hợp lệ.';
        } else {
            $ten = trim($_POST['ten']);
            $loiNhan = trim($_POST['loi_nhan']);

            $doDaiTen = function_exists('mb_strlen')
                ? mb_strlen($ten, 'UTF-8')
                : strlen($ten);

            $doDaiLoiNhan = function_exists('mb_strlen')
                ? mb_strlen($loiNhan, 'UTF-8')
                : strlen($loiNhan);

            if ($ten === '' || $doDaiTen > 50) {
                $loi = 'Tên không được để trống và tối đa 50 ký tự.';
            } elseif ($loiNhan === '' || $doDaiLoiNhan > 500) {
                $loi = 'Lời nhắn không được để trống và tối đa 500 ký tự.';
            } elseif (!is_dir($storageDir) || !is_writable($storageDir)) {
                $loi = 'Không thể ghi dữ liệu. Vui lòng kiểm tra quyền thư mục storage.';
            } else {
                $duLieu = [
                    'ten' => $ten,
                    'loi_nhan' => $loiNhan,
                    'thoi_gian' => date('Y-m-d H:i:s')
                ];

                $json = json_encode(
                    $duLieu,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );

                if ($json === false) {
                    $loi = 'Không thể xử lý nội dung lời nhắn.';
                } else {
                    $daLuu = @file_put_contents(
                        $guestbookFile,
                        $json . PHP_EOL,
                        FILE_APPEND | LOCK_EX
                    );

                    if ($daLuu === false) {
                        $loi = 'Lưu lời nhắn thất bại. Vui lòng thử lại.';
                    } else {
                        header(
                            'Location: gioithieu.php?luu=thanhcong#so-luu-but',
                            true,
                            303
                        );
                        exit;
                    }
                }
            }
        }
    }

    $danhSachLuuBut = docLuuBut($guestbookFile);

    /*
    * Header/footer dưới đây là các tệp dùng chung của nhóm.
    * Giả định header.php mở khung HTML và footer.php đóng khung HTML.
    */
    require_once __DIR__ . '/../../inc/header.php';
    ?>

    <link rel="stylesheet" href="css/profile.css">

    <main class="trang__main profile-main">
        <h2 class="chu-canh-giua mb-1">Giới thiệu thành viên Nhóm 11</h2>

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
                <h3 class="profile-card__name">Silaphet Thit</h3>
                <p class="profile-card__role">
                    <strong>Vai trò:</strong> Thành viên nhóm
                </p>
                <p class="profile-card__id">
                    <strong>Mã sinh viên:</strong> 3120224188
                </p>
                <p>
                    <strong>Nhiệm vụ:</strong>
                    Phụ trách trang liên hệ, bản đồ và nội dung hỗ trợ người dùng.
                </p>

                <div class="d-flex gap-1 flex-wrap mt-1-5">
                    <div class="flex-1 min-w-200">
                        <h4 class="mt-0 text-primary mb-0-5">Thông tin cá nhân</h4>
                        <ul class="ky-nang-list">
                            <li>Toán - Tin</li>
                            <li>CNTT</li>
                        </ul>
                    </div>

                    <div class="flex-1 min-w-200">
                        <h4 class="mt-0 text-primary mb-0-5">Kỹ năng</h4>
                        <ul class="ky-nang-list">
                            <li>HTML5</li>
                            <li>CSS Layout</li>
                            <li>Google Maps</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="hobbies-section">
            <h3 class="text-primary mt-0">Sở thích và mục tiêu</h3>
            <p class="text-secondary">
                Silaphet yêu thích bóng đá, du lịch và tìm hiểu văn hóa các nước.
                Mục tiêu là hoàn thành tốt trang liên hệ và hỗ trợ người dùng
                trong dự án website Sân Bóng Thắng Lợi.
            </p>
        </section>

        <section class="profile-schedule">
            <h3 class="chu-canh-giua mb-1 text-primary">Thời khóa biểu tuần</h3>

            <div class="bang-cuon-ngang">
                <table class="bang-du-lieu">
                    <caption>Lịch học và hoạt động trong tuần của Silaphet Thit</caption>
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
                            <th scope="row">Thứ Năm</th>
                            <td>Thiết kế nội dung website</td>
                            <td>13:30 - 17:00</td>
                        </tr>
                        <tr>
                            <th scope="row">Thứ Bảy</th>
                            <td>Họp nhóm và kiểm tra tiến độ</td>
                            <td>19:00 - 21:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <p class="chu-canh-giua mt-1">
            <a href="../../gioi-thieu.html" class="nut nut--phu">
                ← Quay lại danh sách nhóm
            </a>
        </p>

        <!-- Chức năng PHP 1: Bộ đếm lượt xem -->
        <section class="profile-schedule" aria-labelledby="dem-luot-xem">
            <h3 id="dem-luot-xem" class="text-primary">Lượt xem trang cá nhân</h3>

            <p>
                Tổng lượt xem:
                <strong>
                    <?php if ($luotXem !== null): ?>
                        <?= e($luotXem) ?>
                    <?php else: ?>
                        Chưa đọc được dữ liệu
                    <?php endif; ?>
                </strong>
            </p>

            <p class="text-secondary">
                Mỗi phiên truy cập chỉ được tính một lần.
            </p>
        </section>

        <!-- Chức năng PHP 2: Sổ lưu bút -->
        <section class="profile-schedule" id="so-luu-but">
            <h3 class="text-primary">Sổ lưu bút</h3>
            <p>Hãy để lại lời nhắn cho Silaphet Thit.</p>

            <?php if ($thanhCong): ?>
                <p role="status">Lời nhắn đã được lưu thành công!</p>
            <?php endif; ?>

            <?php if ($loi !== ''): ?>
                <p role="alert"><?= e($loi) ?></p>
            <?php endif; ?>

            <form action="gioithieu.php#so-luu-but" method="post">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>"
                >

                <p>
                    <label for="ten">Tên của bạn</label><br>
                    <input
                        type="text"
                        id="ten"
                        name="ten"
                        maxlength="50"
                        required
                    >
                </p>

                <p>
                    <label for="loi_nhan">Lời nhắn</label><br>
                    <textarea
                        id="loi_nhan"
                        name="loi_nhan"
                        rows="4"
                        maxlength="500"
                        required
                    ></textarea>
                </p>

                <button type="submit" class="nut nut--phu">
                    Gửi lời nhắn
                </button>
            </form>

            <h4>5 lời nhắn mới nhất</h4>

            <?php if (count($danhSachLuuBut) > 0): ?>
                <ul class="ky-nang-list">
                    <?php foreach ($danhSachLuuBut as $tinNhan): ?>
                        <li>
                            <strong><?= e($tinNhan['ten']) ?></strong>
                            <p><?= nl2br(e($tinNhan['loi_nhan'])) ?></p>
                            <small><?= e($tinNhan['thoi_gian']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Chưa có lời nhắn nào. Hãy là người đầu tiên!</p>
            <?php endif; ?>
        </section>

        <!-- Giữ lại các tương tác JavaScript ở Phần C -->
        <article class="full-box" style="text-align: center;">
            <h2>Đếm ngược đến ngày thi</h2>
            <div
                id="dong-ho"
                style="font-size: 1.5rem; font-weight: bold; color: #e11d48; margin-bottom: 20px;"
            >
                Đang tính toán thời gian...
            </div>

            <h2>Slideshow Ảnh</h2>
            <div
                class="slideshow-container"
                style="max-width: 300px; margin: auto; position: relative;"
            >
                <img
                    id="anh-slide"
                    src="../../images/san-a1.jpg"
                    alt="Ảnh sân bóng trong slideshow"
                    style="width: 100%; border-radius: 8px;"
                >
            </div>
            <p style="font-size: 0.9rem; color: gray;">
                (Ảnh sẽ tự động chuyển sau mỗi 3 giây)
            </p>
        </article>
    </main>

    <script type="module" src="js/canhan.js"></script>

    <?php
    require_once __DIR__ . '/../../inc/footer.php';
    ?>
