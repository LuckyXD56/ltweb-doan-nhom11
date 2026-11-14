<?php
/**
 * 403.php — Trang thông báo lỗi 403 Forbidden (Quyền truy cập bị từ chối)
 */
http_response_code(403);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/ham.php';

$tieuDeTrang = '403 — Quyền truy cập bị từ chối';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh trang-loi-403">
  <h1>403 — Quyền truy cập bị từ chối</h1>
  <p>Yêu cầu của bạn bị máy chủ từ chối: Mã bảo vệ CSRF không hợp lệ hoặc bạn không đủ quyền hạn để thực hiện thao tác này.</p>
  <p class="bieu-tuong-loi">🛡️</p>
  <p class="hang-nut-loi">
    <a class="nut nut--chinh" href="index.php">Về trang chủ</a>
    <a class="nut nut--phu" href="dang-nhap.php">Đăng nhập tài khoản</a>
    <a class="nut nut--phu" href="danh-sach.php">Xem danh sách sân</a>
  </p>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>
