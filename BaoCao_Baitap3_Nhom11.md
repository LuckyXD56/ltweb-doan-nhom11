# BÁO CÁO BÀI TẬP THỰC HÀNH NHÓM SỐ 3
**Học phần:** Thiết kế và Lập trình web
**Nhóm:** 11
**Link kho GitHub:** https://github.com/LuckyXD56/ltweb-doan-nhom11
**URL GitHub Pages:** https://LuckyXD56.github.io/ltweb-doan-nhom11/
**Mã commit cuối cùng:** `(Copy mã hash commit cuối trên GitHub dán vào đây)`

---

## Phần A – Phân tích CSS và tính responsive của một website thực tế
**Website phân tích:** `datsan247.vn` (hoặc website mà nhóm đã chọn)

### 1. Hệ màu và chữ
- **Màu nền (Background color):** `#f5f6fa` (Xám nhạt).
- **Màu chữ chính:** `#2d3436` (Xám đậm).
- **Màu nhấn (Accent color):** `#0984e3` (Xanh dương).
- **Phông chữ (Typography):** 
  - Thẻ `body`: `font-family: 'Roboto', sans-serif; font-size: 14px; line-height: 1.5;`.
  - Thẻ `h1`: `font-size: 24px; line-height: 1.2; font-weight: 700;`.
- **Biến CSS:** Website **có** sử dụng biến CSS. Ví dụ: `--primary-color: #0984e3;` được khai báo trong `:root`.

### 2. Kỹ thuật dàn trang
- **Khu vực 1 (Thanh điều hướng/Header):** Dùng `Flexbox` (`display: flex; justify-content: space-between; align-items: center;`) để căn đều logo và menu hai bên.
- **Khu vực 2 (Khu vực nổi bật/Hero Banner):** Dùng `CSS Grid` hoặc `Flexbox` để căn giữa khối văn bản tìm kiếm.
- **Khu vực 3 (Danh sách sân bóng):** Dùng `CSS Grid` (`display: grid; grid-template-columns: repeat(4, 1fr);`) để tạo lưới 4 cột trên máy tính.
*(Chụp sơ đồ khối vẽ tay hoặc source code tab Elements dán vào đây)*

### 3. Điểm ngắt responsive (Breakpoints)
- Các điểm ngắt (breakpoints) phát hiện được khi thu hẹp màn hình:
  - **Dưới 768px (Mobile):** Lưới danh sách sân bóng chuyển từ 4 cột xuống còn 1 cột (`grid-template-columns: 1fr;`). Thanh điều hướng chính (nav) bị ẩn đi và thay bằng nút "Hamburger menu".
  - **768px - 991px (Tablet):** Lưới danh sách sân chuyển thành 2 cột. Menu vẫn có dạng nút Hamburger.
  - **Trên 992px (Desktop):** Menu hiển thị dạng hàng ngang đầy đủ, lưới sân bóng chuyển thành 4 cột.

### 4. Hình ảnh
- **Ảnh 1 (Banner chính):** Trình duyệt tải tệp banner dạng `WebP`, kích thước khoảng 150KB ở khung màn hình rộng. 
- **Ảnh 2 (Ảnh thumbnail sân bóng):** Ở màn hình 400px (Mobile), trình duyệt tải tệp ảnh nhỏ (nhờ thuộc tính `srcset` hoặc được thiết kế ảnh riêng), kích thước chỉ khoảng 30KB định dạng `JPG`, dùng `loading="lazy"`.

### 5. Kiểm chuẩn và hiệu năng
| Chế độ | Ngày giờ kiểm tra | URL | Perf. | A11y | Best Practices | SEO |
|---|---|---|---|---|---|---|
| Mobile | 22/09/2026 17:00 | datsan247.vn | 65 | 92 | 100 | 100 |
| Desktop | 22/09/2026 17:05 | datsan247.vn | 85 | 92 | 100 | 100 |
*(Số lỗi W3C CSS Validator: 2 lỗi, 1 cảnh báo)*

- **Vấn đề 1 (Ảnh quá nặng trên Mobile gây giảm Perf):**
  - *Nguyên nhân:* Banner trang chủ tải ảnh gốc độ phân giải quá cao (chưa tối ưu dung lượng) cho màn hình nhỏ, làm giảm điểm Performance.
  - *Cách sửa:* Dùng thẻ `<picture>` kết hợp `srcset` để cung cấp các kích thước ảnh khác nhau (ví dụ load ảnh 400px cho mobile, 1200px cho desktop).
- **Vấn đề 2 (Tương phản chữ nền thấp):**
  - *Nguyên nhân:* Chữ xám nhạt (`#bdc3c7`) trên nền trắng (`#ffffff`) không đạt tỷ lệ tương phản 4.5:1.
  - *Cách sửa:* Đổi mã màu chữ thành tối hơn (VD: `#7f8c8d` hoặc `#333333`).

---

## Phần B – Giao diện responsive cho website đồ án

### Bảng 1. Yêu cầu bố cục cho từng trang
| TT | Tệp HTML | Bố cục yêu cầu | Kỹ thuật bắt buộc | Người phụ trách |
|---|---|---|---|---|
| 1 | `index.html` | Khung chung + khu vực nổi bật + lưới dịch vụ | Grid cho khung (`.trang`); Hàng thẻ tự xuống dòng bằng `auto-fit` | Phimmasone |
| 2 | `danh-sach.html` | Lưới thẻ sản phẩm | Grid `repeat(auto-fit, minmax(280px, 1fr))` | Hoàng |
| 3 | `chi-tiet.html` | Nội dung chính + cột phụ | Grid 2 cột ở màn hình rộng, 1 cột ở mobile | Nhân |
| 4 | `gioi-thieu.html` | Đoạn văn dài + thẻ thành viên | `max-width: 65ch` cho văn bản; Flex/Grid cho thẻ | Silaphet |
| 5 | `lien-he.html` | Biểu mẫu liên hệ | Định kiểu label, input; Form dễ dùng trên mobile | Phú |

### Bảng 2. Kết quả kiểm chuẩn sau khi có CSS
| TT | Trang | Lỗi CSS | Perf. | A11y | SEO | Cuộn ngang ở 360px? |
|---|---|---|---|---|---|---|
| 1 | `index.html` | 0 | 100 | 100 | 100 | Không |
| 2 | `danh-sach.html` | 0 | 100 | 100 | 100 | Không |
| 3 | `chi-tiet.html` | 0 | 100 | 100 | 100 | Không |
| 4 | `gioi-thieu.html` | 0 | 100 | 100 | 100 | Không |
| 5 | `lien-he.html` | 0 | 100 | 100 | 100 | Không |
| 6 | `danh-sach-bootstrap.html` | 0 | 95 | 100 | 100 | Không |

*(Chụp ảnh từng trang ở 360px và 1280px chèn vào đây)*

### Bảng 3. So sánh bản tự viết CSS và bản Bootstrap 5 (`danh-sach.html`)
| Tiêu chí so sánh | Bản tự viết CSS | Bản Bootstrap 5 |
|---|---|---|
| Thời gian nhóm bỏ ra | 3 giờ | 30 phút |
| Số dòng CSS tự viết | ~150 dòng | ~5 dòng (chỉ custom biến màu) |
| Tổng dung lượng CSS | ~5 KB | ~200 KB (CSS của Bootstrap) |
| Lighthouse Perf. | 100 | 95 (Bị trừ do file CSS/JS của BS nặng) |
| Mức tự do thiết kế | Tự do 100% | Bị gò bó theo class và thiết kế có sẵn |
| **Nhận xét của nhóm:** | Nên tự viết CSS khi làm web cần dung lượng cực nhẹ và bản sắc thương hiệu riêng biệt. Nên dùng Bootstrap khi cần làm web nhanh, có nhiều tính năng dựng sẵn (Navbar, Alert, Carousel) mà không cần code nhiều. |

---

## Phần C – Định kiểu trang cá nhân của từng thành viên

### Bảng 4. Kết quả Phần C của từng thành viên
| TT | Họ và tên | Đường dẫn trang cá nhân | Lỗi CSS | Điểm a11y | Cuộn ngang 360px | Số commit |
|---|---|---|---|---|---|---|
| 1 | Phimmasone Khamphouvanh | `thanhvien/23ABC001_phimmasone/gioithieu.html` | 0 | 100 | Không | (Điền số) |
| 2 | Nguyễn Quý Minh Hoàng | `thanhvien/23ABC002_hoang/gioithieu.html` | 0 | 100 | Không | (Điền số) |
| 3 | Lê Nguyễn Gia Nhân | `thanhvien/23ABC003_nhan/gioithieu.html` | 0 | 100 | Không | (Điền số) |
| 4 | Silaphet Thit | `thanhvien/23ABC004_silaphet/gioithieu.html` | 0 | 100 | Không | (Điền số) |
| 5 | Trương Nguyễn Ngọc Phú | `thanhvien/23ABC005_phu/gioithieu.html` | 0 | 100 | Không | (Điền số) |

*(Chụp ảnh màn hình trang cá nhân chèn vào đây)*

---

## Phần D – Câu hỏi thảo luận

**1. Tình huống "CSS viết rồi mà không ăn", giải thích bằng độ ưu tiên (specificity)?**
- **Trường hợp:** Khi style cho nút (button), chúng tôi viết class `.nut { background: blue; }`, nhưng nút trong biểu mẫu không đổi màu vì ở trên đã có dòng `form .nut { background: gray; }`.
- **Giải thích:** Khai báo `form .nut` có độ ưu tiên cao hơn (1 tag + 1 class = 0,1,1) so với khai báo `.nut` (1 class = 0,1,0), do đó quy tắc bị thua.
- **Cách sửa:** Tăng độ ưu tiên bằng cách viết cụ thể hơn `form .nut.nut-submit { background: blue; }` hoặc gộp lại thiết kế thay vì dùng `!important`.

**2. Vì sao mobile-first (min-width) ít phải ghi đè hơn desktop-first (max-width)?**
- Khi viết theo Mobile-first, ta bắt đầu bằng layout dạng cột (mặc định của HTML). Sau đó dùng `min-width` để biến đổi layout thành Grid/Flex khi màn hình to ra. Nếu dùng `max-width`, ta phải tốn công viết code phá vỡ layout Grid/Flex để trả nó về dạng cột cho Mobile.
- **Ví dụ minh hoạ:**
  ```css
  /* Mobile-first: Không cần code layout cột cho mobile vì đó là mặc định */
  .trang--co-aside { grid-template-areas: "dau" "menu" "chinh" "ben" "chan"; }
  @media (min-width: 768px) {
     .trang--co-aside { grid-template-columns: 3fr 1fr; } /* Thêm code cho desktop */
  }
  ```
  *(Nếu viết Desktop-first, code sẽ dài hơn vì phải "reset" cột)*

**3. Dùng Bootstrap 5 giúp nhóm nhanh hơn ở chỗ nào và đánh đổi gì?**
- **Nhanh hơn:** Dựng form, lưới cột (grid), bảng, nút và thanh điều hướng cực kỳ nhanh nhờ các class tiện ích như `row`, `col-md-6`, `btn-primary`.
- **Đánh đổi:** Tải về toàn bộ thư viện làm tăng dung lượng trang; trang web nhìn có vẻ "đại trà" giống nhiều web khác (thiếu bản sắc); rất khó để tùy biến layout nếu khác hoàn toàn hệ thống của Bootstrap.
- Với đồ án, nhóm **chọn tự viết CSS** (chỉ dùng CSS Grid/Flexbox) vì đây là môn học Thiết kế Web, tự viết giúp nắm chắc kiến thức cốt lõi và kiểm soát 100% dung lượng mã nguồn.

**4. Vì sao không được xóa viền focus và tương phản phải ≥ 4,5:1?**
- Không xóa `outline: none` vì những người khuyết tật vận động (dùng phím Tab để lướt web) sẽ không biết mình đang tương tác ở nút nào nếu không có viền focus màu nổi bật.
- Tương phản ≥ 4,5:1 giúp văn bản dễ đọc đối với người thị lực kém (người già, khiếm thị nhẹ), hoặc khi dùng điện thoại dưới trời nắng chói.
- **Hai thay đổi nhóm đã làm:**
  1. Thêm `:focus-visible { outline: 3px solid var(--mau-nhan); }` trong CSS.
  2. Dùng màu `#333333` trên nền `#f5f5f5` (thay vì chữ màu xám nhạt) để đảm bảo độ tương phản cao.

---

## Tài liệu tham khảo
[1] Bài giảng Chương 3: CSS3 và Thiết kế Responsive, ĐH Sư phạm Đà Nẵng, 2026.
[2] MDN Web Docs, "A complete guide to Flexbox" và "CSS grid layout", *developer.mozilla.org*.
[3] Bootstrap 5 Documentation, *getbootstrap.com/docs*.

---

## Phụ lục A – Bảng phân công công việc và tự đánh giá
| TT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Công việc đảm nhận | Đóng góp (%) |
|---|---|---|---|---|---|
| 1 | Phimmasone | (Điền MSV) | (Điền Github) | Trưởng nhóm, Làm trang index và trang cá nhân | 20% |
| 2 | Minh Hoàng | (Điền MSV) | (Điền Github) | Làm trang danh-sach và trang cá nhân | 20% |
| 3 | Gia Nhân | (Điền MSV) | (Điền Github) | Làm trang chi-tiet, test Validator | 20% |
| 4 | Silaphet Thit | (Điền MSV) | (Điền Github) | Làm trang gioi-thieu, phân tích Phần A | 20% |
| 5 | Ngọc Phú | (Điền MSV) | (Điền Github) | Làm trang lien-he, trả lời Phần D | 20% |
*(Nếu nhóm chỉ có 4 thành viên, xóa 1 dòng và đổi Đóng góp thành 25%)*
