<?php
/**
 * lien-he.php — Liên hệ
 */
$tieuDeTrang = 'Liên hệ';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Liên hệ với chúng tôi</h1>
  <p>Mọi thắc mắc về đặt sân, huỷ lịch hoặc hợp tác giải đấu, vui lòng liên hệ theo thông tin bên dưới.</p>

  <h2>Thông tin liên hệ</h2>
  <ul class="danh-sach-tien-ich">
    <li>Địa chỉ: 123 Đường Ngô Quyền, Quận Sơn Trà, Đà Nẵng</li>
    <li>Điện thoại: 0905 123 456</li>
    <li>Email: lienhe@sanbongthangloi.vn</li>
    <li>Giờ mở cửa: 06:00 – 23:00 tất cả các ngày trong tuần</li>
  </ul>

  <h2>Gửi câu hỏi cho chúng tôi</h2>
  <form class="bieu-mau" id="form-lien-he" action="#" method="post" novalidate>
    <label class="truong__nhan" for="lh-ho-ten">Họ và tên (*)</label>
    <input class="truong__nhap" type="text" id="lh-ho-ten" name="ho_ten" required minlength="2">
    <span id="loi-ho-ten" style="color: red; font-size: 0.9em; display: block; margin-bottom: 10px;"></span>

    <label class="truong__nhan" for="lh-email">Email (*)</label>
    <input class="truong__nhap" type="email" id="lh-email" name="email" required>
    <span id="loi-email" style="color: red; font-size: 0.9em; display: block; margin-bottom: 10px;"></span>

    <label class="truong__nhan" for="lh-noi-dung">Nội dung (*)</label>
    <textarea class="truong__nhap" id="lh-noi-dung" name="noi_dung" rows="4" required minlength="10"></textarea>
    <span id="loi-noi-dung" style="color: red; font-size: 0.9em; display: block; margin-bottom: 10px;"></span>

    <p id="thong-bao-lien-he" class="vung-thong-bao" aria-live="polite"></p>

    <button class="nut nut--chinh" type="submit">Gửi liên hệ</button>
  </form>

  <h2>Nhân viên tư vấn</h2>
  <figure class="khoi-anh">
    <img class="khoi-anh__anh" src="images/nhan-vien-tu-van.jpg"
         alt="Nhân viên quầy lễ tân đang hỗ trợ khách hàng đặt sân"
         width="480" height="320" loading="lazy">
    <figcaption class="khoi-anh__chu-thich">Đội ngũ lễ tân sẵn sàng hỗ trợ đặt sân trực tiếp tại quầy.</figcaption>
  </figure>

  <h2>Bản đồ đường đi</h2>
  <iframe class="khung-nhung"
          src="https://www.google.com/maps?q=Da+Nang&output=embed"
          title="Bản đồ vị trí Sân Bóng Thắng Lợi tại Đà Nẵng"
          loading="lazy"></iframe>
</main>

<?php
$scriptRieng = '<script type="module" src="js/trang-lien-he.js"></script>';
require __DIR__ . '/inc/footer.php';
?>