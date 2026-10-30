# ⚽ Sân Bóng Thắng Lợi - Nhóm 11 (Phiên bản Bài tập nhóm số 6 - PHP & MySQL)

Website giới thiệu và đặt sân bóng đá Thắng Lợi được xây dựng trong khuôn khổ học phần: **Thiết kế và Lập trình Web** (Bài tập nhóm số 6 – Đưa dữ liệu website đồ án vào MySQL: thiết kế CSDL, tìm kiếm – phân trang, tài khoản, phân quyền, giao dịch và REST API).

## 👥 Thông tin nhóm

- **Trường:** Trường Đại học Sư phạm - Đại học Đà Nẵng
- **Khoa:** Toán - Tin
- **Nhóm:** 11
- **Học kỳ:** 1, Năm học: 2026 – 2027
- **Kho GitHub:** [https://github.com/LuckyXD56/ltweb-doan-nhom11](https://github.com/LuckyXD56/ltweb-doan-nhom11)

### Danh sách thành viên

| STT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Vai trò | Chức năng phụ trách (Phần A) |
|---|---|---|---|---|---|
| 1 | **Phimmasone Khamphouvanh** | **3120224192** | `LuckXD56` | **Trưởng nhóm** | Chức năng 5 (Giỏ hàng & Giao dịch đặt sân) & Bảng 3 (Kiểm thử) |
| 2 | Nguyễn Quý Minh Hoàng | 3120224060 | `nguyenquyminhhoang` | Thành viên | Chức năng 4 (Đăng ký, Đăng nhập, Quản trị, Đăng xuất) |
| 3 | Lê Nguyễn Gia Nhân | 3120224105 | `Gianhanle999` | Thành viên | Chức năng 1 (Chi tiết sân kèm danh mục & 4 sân cùng loại, Đã xem gần đây) |
| 4 | **Silaphet Thit** | **3120224188** | `Silaphet` | **Thành viên** | **Chức năng 1 (Biểu mẫu liên hệ kèm upload ảnh lưu vào bảng `lien_he`, hiển thị quản trị)** |
| 5 | Trương Nguyễn Ngọc Phú | 3120224111 | `phutruong317-cloud` | Thành viên | Chức năng 2 (Tìm kiếm, lọc danh mục & giá, sắp xếp, phân trang) |

---

## 🛠 Yêu cầu môi trường

Để chạy dự án này, môi trường cần cài đặt:
- **PHP** phiên bản ≥ 8.1 (có bật extension `pdo_mysql`, `fileinfo`, `mbstring`, `session`)
- **MySQL / MariaDB** (trong XAMPP hoặc Laragon, collation `utf8mb4_unicode_ci`)
- **Composer** (để quản lý autoload PSR-4)
- Web server: PHP built-in server (`php -S`) hoặc Apache XAMPP / Laragon

---

## 🚀 Các lệnh cài đặt và chạy

Mở Terminal / PowerShell tại thư mục gốc của dự án và thực hiện theo 3 bước:

**1. Cài đặt các phụ thuộc (Autoload PSR-4):**
```bash
composer dump-autoload
```

**2. Tạo cơ sở dữ liệu `ltweb_nhom11`:**
- Mở **phpMyAdmin** → Tạo CSDL `ltweb_nhom11` (hoặc import trực tiếp `database/schema.sql`, sau đó import `database/seed.sql`).
- Hoặc chạy qua dòng lệnh (MySQL của XAMPP tại `C:\xampp\mysql\bin`):
```bash
mysql -u root < database/schema.sql
mysql -u root ltweb_nhom11 < database/seed.sql
```

**3. Khởi động Web Server cục bộ của PHP:**
```bash
php -S localhost:8000
```
Mở trình duyệt và truy cập: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Tài khoản thử nghiệm (Mật khẩu: `Admin@123`)

- **admin@sanbong.vn**: Quản trị viên hệ thống (quyền `quan_tri` — truy cập `quan-tri.php`).
- **silaphet@sanbong.vn**: Tài khoản chủ trang Silaphet Thit (quyền `quan_tri` — có toàn quyền quản lý, thêm/xóa dự án và xóa lưu bút tại trang cá nhân).
- **khach1@gmail.com**, **khach2@gmail.com**: Tài khoản khách hàng (quyền `khach_hang` — mỗi khách đã có ít nhất 1 đơn đặt sân).

---

## 🗺 Bảng Chức năng – URL – Tệp PHP (Bảng 1)

| TT | Chức năng | URL truy cập | Tệp PHP xử lý chính | Người phụ trách |
|:---|:---|:---|:---|:---|
| 1 | Trang chủ, chi tiết sân, giỏ hàng, liên hệ (kèm ảnh) lưu bảng `lien_he` | `/index.php`, `/chi-tiet.php`, `/lien-he.php` | `lien-he.php`, `inc/xu-ly-lien-he.php`, `src/Data/KhoLienHe.php` | **Silaphet Thit** (phụ trách Liên hệ & CSDL `lien_he`), Lê Nguyễn Gia Nhân |
| 2 | Danh sách sân bóng (tìm kiếm LIKE, lọc loại sân/giá, sắp xếp, phân trang) | `/danh-sach.php` | `danh-sach.php`, `src/Data/KhoSanPham.php` | Trương Nguyễn Ngọc Phú |
| 3 | Quản trị sân bóng & xem danh sách liên hệ đã nhận kèm ảnh | `/quan-tri.php` | `quan-tri.php` | Nguyễn Quý Minh Hoàng, Silaphet Thit |
| 4 | Đăng ký, đăng nhập (password_hash), đăng xuất với `nguoi_dung` | `/dang-nhap.php`, `/tai-khoan.php`, `/dang-xuat.php` | `dang-nhap.php`, `tai-khoan.php`, `dang-xuat.php` | Nguyễn Quý Minh Hoàng |
| 5 | Đặt sân từ giỏ hàng trong giao dịch (Transaction), lịch sử đơn | `/dat-san.php`, `/lich-su.php` | `dat-san.php`, `lich-su.php`, `src/Data/KhoDatSan.php` | Phimmasone Khamphouvanh |
| 6 | REST API trả JSON: tìm kiếm, xem sân, giỏ hàng | `/api/san-bong.php`, `/api/gio-hang.php` | `api/san-bong.php`, `api/gio-hang.php`, `js/tim-kiem.js` | Phimmasone Khamphouvanh |

---

## 📋 Bảng 5. Kết quả Phần B của từng thành viên

| TT | Họ và tên | Bảng riêng (`tv_...`) | Hai chức năng dùng CSDL (Phần B) | Chức năng phụ trách ở Bảng 1 | Lỗi PHP | Số commit |
|:---|:---|:---|:---|:---|:---|:---|
| 1 | Phimmasone Khamphouvanh | `tv_todo_phim`, `tv_bmi_phim` | 1. Quản lý công việc cá nhân; 2. Tính và lưu lịch sử chỉ số BMI | Chức năng 5 (Giỏ hàng & Đặt sân) | 0 | 5 |
| 2 | Nguyễn Quý Minh Hoàng | `tv_luu_but` | 1. Sổ lưu bút CSDL; 2. Thống kê lượt xem theo ngày | Chức năng 4 (Xác thực, Quản trị) | 0 | 4 |
| 3 | Lê Nguyễn Gia Nhân | `tv_luot_xem` | 1. Sổ lưu bút CSDL; 2. Đếm lượt xem theo ngày 7 ngày gần nhất | Chức năng 1 (Chi tiết & Đã xem gần đây) | 0 | 4 |
| 4 | **Silaphet Thit** | **`tv_du_an`** | **1. Sổ lưu bút CSDL (`tv_luu_but`) có phân trang 5 mục/trang, phân quyền máy chủ chỉ chủ trang xóa (`DELETE ... WHERE id = ? AND mssv = ?`); 2. Quản lý Dự án & Kỹ năng cá nhân (`tv_du_an`): tìm kiếm từ khóa (LIKE), lọc theo loại, sắp xếp Whitelist, tính tiến độ trung bình bằng SQL (AVG), chủ trang có quyền thêm & xóa dự án** | **Chức năng 1 (Liên hệ lưu bảng `lien_he` kèm upload ảnh an toàn, hiển thị trong quản trị)** | **0** | **4** |
| 5 | Trương Nguyễn Ngọc Phú | `tv_ky_nang` | 1. Sổ lưu bút CSDL; 2. Danh mục kỹ năng | Chức năng 2 (Lọc & Phân trang) | 0 | 3 |

---

## 🌟 Chi tiết thực hiện của thành viên Silaphet Thit (MSSV: 3120224188)

### 1. Phần A (Chức năng Liên hệ & Quản trị liên hệ)
- **Cơ sở dữ liệu**: Bảng `lien_he` trong `database/schema.sql` có cột `hinh_anh VARCHAR(255) NULL`, nạp sẵn dữ liệu mẫu trong `database/seed.sql`.
- **Lớp truy cập dữ liệu**: `src/Data/KhoLienHe.php` sử dụng Prepared Statement để thêm liên hệ (`them`), lấy danh sách mới nhất (`tatCa`), đếm số lượng (`dem`) và hỗ trợ phân trang (`trang`).
- **Xử lý Upload ảnh an toàn**: `inc/xu-ly-lien-he.php` kiểm tra mã lỗi, kiểm tra kích thước ≤ 2MB, xác minh MIME Type chuẩn qua extension `fileinfo` (`finfo`), sinh tên ngẫu nhiên tránh ghi đè và lưu tệp vào thư mục `uploads/`.
- **Hiển thị Quản trị**: `quan-tri.php` hiển thị danh sách 10 liên hệ mới nhất kèm hình ảnh đính kèm (nếu có).

### 2. Phần B (Trang cá nhân `thanhvien/3120224188_silaphet/gioithieu.php`)
- **Bảng riêng `tv_du_an`**:
  + Thiết kế bảng riêng cho Silaphet với các cột `id`, `mssv`, `ten_du_an`, `loai_du_an` (Web, Mobile, Design, Data), `mo_ta`, `cong_nghe`, `tien_do` (CHECK `tien_do <= 100`), `trang_thai`, `ngay_tao`. Khóa ngoại `mssv` tham chiếu tới `thanh_vien(mssv)` ON DELETE CASCADE.
  + Chức năng quản lý dự án có tìm kiếm từ khóa với `LIKE %...%` và `addcslashes`, lọc theo loại dự án, sắp xếp bằng Whitelist an toàn chống SQL Injection.
  + Thống kê trực tiếp bằng hàm tổng hợp SQL: Tổng số dự án (`COUNT`), dự án hoàn thành (`SUM`), tiến độ trung bình (`ROUND(AVG(tien_do), 0)`).
  + Phân quyền máy chủ: Chỉ khi đăng nhập với tài khoản của Silaphet (`silaphet@sanbong.vn` / quyền `quan_tri`) mới xuất hiện biểu mẫu Thêm dự án và nút Xóa dự án.
- **Sổ lưu bút CSDL (`tv_luu_but`)**:
  + Chuyển đổi toàn diện từ file JSONL cũ sang bảng CSDL `tv_luu_but` với `mssv = '3120224188'`.
  + Phân trang 5 lời nhắn mỗi trang qua Prepared Statement `LIMIT 5 OFFSET ?`, giữ nguyên trạng thái khi đổi trang.
  + Phân quyền máy chủ: Nút xóa chỉ hiển thị cho chủ trang/quản trị viên, câu truy vấn xóa bảo vệ chặt chẽ `DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?` để ngăn xóa nhầm dữ liệu của thành viên khác.
  + Áp dụng triệt để mô hình PRG (Post-Redirect-Get) và Flash message, F5 không bị gửi lại dữ liệu.
- **Bộ đếm lượt xem theo ngày (`tv_luot_xem`)**:
  + Ghi nhận lượt xem bằng `INSERT INTO tv_luot_xem (mssv, ngay, luot_xem) VALUES (?, CURRENT_DATE, 1) ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1`.
  + Hiển thị số lượt xem hôm nay, tổng số lượt xem và bảng thống kê 7 ngày gần nhất.
- **Tiêu chuẩn chất lượng**:
  + 100% Prepared Statement với PDO, không nối chuỗi biến người dùng vào SQL.
  + Toàn bộ dữ liệu in ra màn hình đều qua hàm `e()` để chống XSS.
  + 0 lỗi Warning, Notice, Deprecated trong PHP.
  + Responsive mượt mà trên thiết bị di động (chiều rộng 360px không bị cuộn ngang).
