<?php
/**
 * san-lich.php — Danh sách sân & lịch trống
 */
$tieuDeTrang = 'Sân & lịch trống';
require __DIR__ . '/inc/header.php';
?>

<main class="khu-vuc-chinh">
  <h1>Danh sách sân và lịch trống hôm nay</h1>
  <p>Bảng dưới đây hiển thị tình trạng các sân theo từng khung giờ trong ngày.
    Khung giờ còn trống có thể được đặt trực tiếp tại trang Đặt sân.</p>

  <h2>Tìm sân theo loại hoặc từ khoá</h2>
  <div class="bo-loc-san">
    <div>
      <label class="truong__nhan" for="loc-loai-san">Loại sân</label>
      <select id="loc-loai-san" class="truong__nhap">
        <option value="tat-ca">Tất cả loại sân</option>
        <option value="5">5 người</option>
        <option value="7">7 người</option>
        <option value="11">11 người</option>
      </select>
    </div>
    <div>
      <label class="truong__nhan" for="tim-kiem-san">Tìm theo tên hoặc khu vực</label>
      <input type="search" id="tim-kiem-san" class="truong__nhap" placeholder="Ví dụ: A1, Trung tâm…">
    </div>
  </div>
  <p id="thong-bao-danh-sach-san" class="vung-thong-bao" aria-live="polite"></p>

  <div class="bang-chua">
    <table class="bang">
      <caption class="bang__tieu-de">Lịch sân ngày 11/09/2026</caption>
      <thead>
        <tr>
          <th scope="col">Tên sân</th>
          <th scope="col">Loại sân</th>
          <th scope="col">Giá/giờ</th>
          <th scope="col">Khu vực</th>
          <th scope="col">Trạng thái</th>
          <th scope="col">Chi tiết</th>
        </tr>
      </thead>
      <tbody id="than-bang-san">
        <tr><td>Sân A1</td><td>5 người</td><td>200.000đ</td><td>Phía Đông</td><td>Đang hoạt động</td><td>—</td></tr>
        <tr><td>Sân A1</td><td>5 người</td><td>250.000đ</td><td>Phía Đông</td><td>Đã đặt</td><td>—</td></tr>
        <tr><td>Sân B2</td><td>7 người</td><td>350.000đ</td><td>Trung tâm</td><td>Đang hoạt động</td><td>—</td></tr>
        <tr><td>Sân B2</td><td>7 người</td><td>400.000đ</td><td>Trung tâm</td><td>Đang hoạt động</td><td>—</td></tr>
        <tr><td>Sân C1</td><td>11 người</td><td>700.000đ</td><td>Phía Tây</td><td>Đã đặt</td><td>—</td></tr>
      </tbody>
    </table>
  </div>
  <noscript>
    <p>Bảng trên đang hiển thị dữ liệu mẫu tĩnh vì JavaScript đang tắt.</p>
  </noscript>

  <h2>Ghi chú về bảng giá</h2>
  <ul>
    <li>Giá khung giờ vàng (18:00 – 21:00) cao hơn khung giờ ban ngày</li>
    <li>Khách đặt từ 3 giờ trở lên được giảm 10% tổng hoá đơn</li>
  </ul>
</main>

<dialog id="hop-thoai-chi-tiet-san" class="hop-thoai" aria-labelledby="hop-thoai-tieu-de">
  <div class="hop-thoai__noi-dung"></div>
  <button type="button" class="hop-thoai__nut-dong nut nut--phu">Đóng</button>
</dialog>

<?php
$scriptRieng = '<script type="module" src="js/trang-san-lich.js"></script>';
require __DIR__ . '/inc/footer.php';
?>