<?php
/**
 * tai-khoan.php — Đăng nhập / Đăng ký
 */
$tieuDeTrang = 'Đăng nhập / Đăng ký';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Đăng nhập hoặc tạo tài khoản</h1>
  <p>Tài khoản giúp bạn lưu lịch sử đặt sân, nhận thông báo lịch trống và tích điểm đổi ưu đãi.</p>

  <h2>Đăng nhập</h2>
  <form class="bieu-mau" id="form-dang-nhap" action="#" method="post">
    <label class="truong__nhan" for="dn-email">Email hoặc số điện thoại (*)</label>
    <input class="truong__nhap" type="text" id="dn-email" name="tai_khoan" required>

    <label class="truong__nhan" for="dn-mat-khau">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="dn-mat-khau" name="mat_khau" required minlength="6">

    <p id="thong-bao-dang-nhap" class="vung-thong-bao" aria-live="polite"></p>

    <button class="nut nut--chinh" type="submit">Đăng nhập</button>
  </form>

  <h2>Đăng ký tài khoản mới</h2>
  <form class="bieu-mau" id="form-dang-ky" action="#" method="post">
    <label class="truong__nhan" for="dk-ho-ten">Họ và tên (*)</label>
    <input class="truong__nhap" type="text" id="dk-ho-ten" name="ho_ten" required minlength="2">

    <label class="truong__nhan" for="dk-email">Email (*)</label>
    <input class="truong__nhap" type="email" id="dk-email" name="email" required>

    <label class="truong__nhan" for="dk-sdt">Số điện thoại (*)</label>
    <input class="truong__nhap" type="tel" id="dk-sdt" name="so_dien_thoai" required pattern="0[0-9]{9}"
           title="Nhập số điện thoại Việt Nam gồm 10 chữ số, bắt đầu bằng 0">

    <label class="truong__nhan" for="dk-mat-khau">Mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="dk-mat-khau" name="mat_khau" required minlength="6">

    <label class="truong__nhan" for="dk-xac-nhan">Xác nhận mật khẩu (*)</label>
    <input class="truong__nhap" type="password" id="dk-xac-nhan" name="xac_nhan_mat_khau" required minlength="6">

    <p>
      <input class="truong__chon" type="checkbox" id="dk-dong-y" name="dong_y_dieu_khoan" required>
      <label class="truong__nhan" for="dk-dong-y">Tôi đồng ý với điều khoản sử dụng dịch vụ (*)</label>
    </p>

    <p id="thong-bao-dang-ky" class="vung-thong-bao" aria-live="polite"></p>

    <button class="nut nut--chinh" type="submit">Tạo tài khoản</button>
  </form>
</main>

<?php
$scriptRieng = '<script type="module" src="js/trang-tai-khoan.js"></script>';
require __DIR__ . '/inc/footer.php';
?>