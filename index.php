<?php
/**
 * index.php — Trang chủ Sân Bóng Thắng Lợi
 */
$tieuDeTrang = 'Đặt sân bóng đá trực tuyến';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <section class="thoi-tiet-container mt-2 mb-2 p-2" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; text-align: center;">
    <h2>🌤️ Thời tiết Đà Nẵng hiện tại</h2>
    <div id="thoi-tiet-widget">
      <p>Đang tải thông tin thời tiết...</p>
    </div>
  </section>

  <h1>Đặt sân bóng đá trực tuyến – nhanh chóng, tiện lợi</h1>

  <p class="gioi-thieu">Sân Bóng Thắng Lợi cung cấp dịch vụ cho thuê sân cỏ nhân tạo với ba loại sân 5 người,
    7 người và 11 người, phục vụ nhu cầu tập luyện và thi đấu giao hữu của các đội bóng phong trào.
    Khách hàng có thể xem lịch trống theo thời gian thực và đặt sân trực tuyến chỉ trong vài bước,
    không cần gọi điện đặt chỗ như trước đây.</p>

  <h2>Các loại sân hiện có</h2>
  <ul class="danh-sach-the">
    <li class="the">Sân 5 người – phù hợp nhóm bạn, giá từ 200.000đ/giờ</li>
    <li class="the">Sân 7 người – phù hợp giải phong trào, giá từ 350.000đ/giờ</li>
    <li class="the the--noi-bat">Sân 11 người – phù hợp thi đấu chính thức, giá từ 700.000đ/giờ</li>
  </ul>

  <h2>Sân nổi bật</h2>
  <div class="khung-sap-xep">
    <label class="truong__nhan" for="sap-xep-noi-bat">Sắp xếp theo</label>
    <select id="sap-xep-noi-bat" class="truong__nhap">
      <option value="gia">Giá thấp nhất</option>
      <option value="danh-gia">Đánh giá cao nhất</option>
    </select>
  </div>
  <p id="thong-bao-noi-bat" class="vung-thong-bao" aria-live="polite"></p>
  <ul id="danh-sach-noi-bat" class="danh-sach-the"></ul>
  <noscript>
    <p>Danh sách sân nổi bật cần JavaScript để tải tự động. Bạn vẫn có thể xem đầy đủ
      thông tin các sân tại trang <a href="san-lich.php">Sân &amp; lịch trống</a>.</p>
  </noscript>

  <h2>Hình ảnh sân bóng</h2>
  <figure class="khoi-anh">
    <img class="khoi-anh__anh" src="images/san-co-nhan-tao-5-nguoi.jpg"
         alt="Sân cỏ nhân tạo 5 người với hệ thống đèn chiếu sáng ban đêm"
         width="640" height="360" loading="lazy">
    <figcaption class="khoi-anh__chu-thich">Sân 5 người tại cơ sở Sân Bóng Thắng Lợi, có đèn chiếu sáng phục vụ khung giờ tối.</figcaption>
  </figure>

  <h2>Tiện ích đi kèm</h2>
  <ul class="danh-sach-tien-ich">
    <li>Phòng thay đồ và tủ khóa cá nhân</li>
    <li>Bãi giữ xe miễn phí cho khách đặt sân</li>
    <li>Quầy nước giải khát ngay tại sân</li>
    <li>Cho thuê áo bib, bóng và cọc tiêu tập luyện</li>
  </ul>
</main>

<?php
$scriptRieng = '<script type="module" src="js/trang-index.js"></script>';
require __DIR__ . '/inc/footer.php';
?>