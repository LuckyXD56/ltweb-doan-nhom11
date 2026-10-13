<?php
/**
 * dat-san.php — Form đặt sân
 */
$tieuDeTrang = 'Đặt sân';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Đặt sân bóng đá</h1>
  <p class="gioi-thieu">Vui lòng điền đầy đủ thông tin bên dưới. Các trường có dấu (*) là bắt buộc.
    Đơn đặt sân sẽ được xác nhận qua số điện thoại hoặc email trong vòng 30 phút.</p>

  <form class="bieu-mau" id="form-dat-san" action="#" method="post">
    <fieldset class="nhom-truong">
      <legend class="nhom-truong__tieu-de">Thông tin khách hàng</legend>

      <label class="truong__nhan" for="ho-ten">Họ và tên (*)</label>
      <input class="truong__nhap" type="text" id="ho-ten" name="ho_ten" required minlength="2" maxlength="60">

      <label class="truong__nhan" for="sdt">Số điện thoại (*)</label>
      <input class="truong__nhap" type="tel" id="sdt" name="so_dien_thoai" required pattern="0[0-9]{9}"
             title="Nhập số điện thoại Việt Nam gồm 10 chữ số, bắt đầu bằng 0">

      <label class="truong__nhan" for="email">Email</label>
      <input class="truong__nhap" type="email" id="email" name="email">
    </fieldset>

    <fieldset class="nhom-truong">
      <legend class="nhom-truong__tieu-de">Thông tin đặt sân</legend>

      <label class="truong__nhan" for="loai-san">Loại sân (*)</label>
      <select class="truong__nhap" id="loai-san" name="loai_san" required>
        <option value="">-- Chọn loại sân --</option>
        <option value="5">Sân 5 người</option>
        <option value="7">Sân 7 người</option>
        <option value="11">Sân 11 người</option>
      </select>

      <label class="truong__nhan" for="san-cu-the">Sân cụ thể</label>
      <select class="truong__nhap" id="san-cu-the" name="san_cu_the" disabled>
        <option value="">-- Vui lòng chọn loại sân trước --</option>
      </select>

      <label class="truong__nhan" for="ngay-dat">Ngày đặt (*)</label>
      <input class="truong__nhap" type="date" id="ngay-dat" name="ngay_dat" required min="2026-09-11">

      <label class="truong__nhan" for="gio-bat-dau">Giờ bắt đầu (*)</label>
      <input class="truong__nhap" type="time" id="gio-bat-dau" name="gio_bat_dau" required>

      <label class="truong__nhan" for="gio-ket-thuc">Giờ kết thúc (*)</label>
      <input class="truong__nhap" type="time" id="gio-ket-thuc" name="gio_ket_thuc" required>

      <p class="truong__tieu-de">Phương thức đặt cọc (*)</p>
      <input class="truong__chon" type="radio" id="coc-chuyen-khoan" name="phuong_thuc_coc" value="chuyen_khoan" required>
      <label class="truong__nhan" for="coc-chuyen-khoan">Chuyển khoản ngân hàng</label><br>
      <input class="truong__chon" type="radio" id="coc-tien-mat" name="phuong_thuc_coc" value="tien_mat" required>
      <label class="truong__nhan" for="coc-tien-mat">Tiền mặt tại sân</label>

      <label class="truong__nhan" for="ghi-chu">Ghi chú thêm</label>
      <textarea class="truong__nhap" id="ghi-chu" name="ghi_chu" rows="4" cols="40" maxlength="300"></textarea>
    </fieldset>

    <p id="thong-bao-dat-san" class="vung-thong-bao" aria-live="polite"></p>

    <button class="nut nut--chinh" type="submit">Xác nhận đặt sân</button>
    <button class="nut nut--phu" type="reset">Nhập lại</button>
  </form>
</main>

<?php
$scriptRieng = '<script type="module" src="js/trang-dat-san.js"></script>';
require __DIR__ . '/inc/footer.php';
?>