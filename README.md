# ⚽ Sân Bóng Thắng Lợi - Nhóm 11 (Bài tập nhóm số 7: Bảo mật ứng dụng web)

Website quản lý và đặt sân bóng đá Thắng Lợi được gia cố bảo mật toàn diện trong khuôn khổ học phần: **Thiết kế và Lập trình Web** (Bài tập thực hành nhóm số 7 – Chương 7: Bảo mật ứng dụng web).

---

## 👥 Thông tin nhóm 11

- **Trường:** Trường Đại học Sư phạm - Đại học Đà Nẵng
- **Khoa:** Toán - Tin
- **Nhóm:** 11
- **Học kỳ:** 1, Năm học: 2026 – 2027
- **Kho GitHub:** [https://github.com/LuckyXD56/ltweb-doan-nhom11](https://github.com/LuckyXD56/ltweb-doan-nhom11)

### Danh sách thành viên và phân công Biện pháp bảo mật (Bảng 1)

| TT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Vai trò | Biện pháp phụ trách (Chương 7) |
|:---|:---|:---|:---|:---|:---|
| 1 | **Phimmasone Khamphouvanh** | **3120224192** | `LuckyXD56` | **Trưởng nhóm** | Biện pháp 1 (Mã CSRF) & Biện pháp 2 (Cookie phiên an toàn) |
| 2 | Nguyễn Quý Minh Hoàng | 3120224060 | `nguyenquyminhhoang` | Thành viên | Biện pháp 7 (Chống đoán mật khẩu & ghi log bảo mật) |
| 3 | Lê Nguyễn Gia Nhân | 3120224105 | `Gianhanle999` | Thành viên | Biện pháp 3 (Header bảo mật & Content Security Policy) |
| 4 | **Silaphet Thit** | **3120224188** | `Silaphet` | **Thành viên** | Biện pháp 5 (Chặn tệp nội bộ Apache qua `.htaccess`) |
| 5 | Trương Nguyễn Ngọc Phú | 3120224111 | `phutruong317-cloud` | Thành viên | Biện pháp 4 (Cấu hình tệp `.env`) & Biện pháp 6 (Quyền CSDL tối thiểu) |

---

## 🛡️ Bảng 1. Bảy biện pháp bảo mật bắt buộc đã triển khai

| TT | Biện pháp, tệp chính | Yêu cầu bắt buộc | Người phụ trách, mã commit |
|:---|:---|:---|:---|
| 1 | **Mã CSRF cho mọi form POST và fetch**<br>`inc/csrf.php`, `inc/config.php`, `inc/header.php`, `js/gio-hang.js`, `dang-xuat.php` | `csrfToken()`, `csrfField()`, `csrfHopLe()` sinh bằng `random_bytes(32)`, so khớp `hash_equals()`; `inc/config.php` chặn tập trung mọi POST thiếu mã (trả 403 HTML hoặc JSON cho API); mọi form `method="post"` có `csrfField()`; thẻ `<meta name="csrf-token">` và header `X-CSRF-Token` cho AJAX fetch; Đăng xuất là form POST; không thao tác đổi dữ liệu nào dùng GET. | Phimmasone Khamphouvanh (`8e86b11`) |
| 2 | **Cookie phiên và phiên an toàn**<br>`inc/config.php`, `inc/xac-thuc.php` | `session.use_strict_mode = 1`; cookie `HttpOnly`, `SameSite=Lax`, `Secure` khi có HTTPS, đặt trước `session_start()`; tự hủy và cấp mã mới sau 30 phút không hoạt động; `dangNhap()` đổi mã phiên (`session_regenerate_id(true)`); `dangXuat()` xóa sạch session và cookie phiên. | Phimmasone Khamphouvanh (`2805259`) |
| 3 | **Header bảo mật và CSP**<br>`inc/config.php`, `css/*.css`, `js/*.js` | `Content-Security-Policy: default-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; connect-src 'self' https://api.open-meteo.com; frame-src 'self' https://www.google.com; img-src 'self' data:;`; `X-Content-Type-Options: nosniff`; `Referrer-Policy: strict-origin-when-cross-origin`; xóa bỏ header `X-Powered-By`; loại bỏ toàn bộ inline script/style; Console không vi phạm CSP. | Lê Nguyễn Gia Nhân (`1060bf5`) |
| 4 | **Cấu hình và bí mật trong tệp `.env`**<br>`.env.example`, `.gitignore`, `composer.json`, `inc/config.php`, `inc/db.php` | Cài đặt `vlucas/phpdotenv` qua Composer; biến `APP_DEBUG`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`; `required(['DB_HOST', 'DB_DATABASE', 'DB_USERNAME'])` báo lỗi khi thiếu; `APP_DEBUG=false` tắt chế độ gỡ lỗi; tệp `.env` được ignore hoàn toàn, không có trong kho và lịch sử Git; chỉ commit `.env.example`. | Trương Nguyễn Ngọc Phú (`0015d20`) |
| 5 | **Chặn tệp nội bộ trên Apache**<br>`.htaccess`, `uploads/.htaccess` | Root `.htaccess`: `Options -Indexes`, trả 404 cho tệp/thư mục ẩn (`.env`, `.git/`), thư mục nội bộ (`inc/`, `src/`, `vendor/`, `database/`, `logs/`, `storage/`); chặn tải tệp `.sql`, `.log`, `.bak`, `.ini`, `.lock`, `composer.json` (403); `uploads/.htaccess` cấm thực thi script (`.php`, `.phtml`, `.sh`...), cấm liệt kê thư mục, bổ sung `nosniff`. Chú thích trên dòng riêng. | Silaphet Thit (`4de48f7`) |
| 6 | **Tài khoản CSDL quyền tối thiểu**<br>`database/tai-khoan.sql`, `.env` | Tạo tài khoản `nhom11_app` chỉ có quyền `SELECT, INSERT, UPDATE, DELETE` trên `ltweb_nhom11.*`; website chạy đầy đủ chức năng khi `.env` trỏ về tài khoản này; ngăn chặn các lệnh phá hoại như `DROP TABLE`, `CREATE TABLE` (lỗi MariaDB 1142). | Trương Nguyễn Ngọc Phú (`9e7f9c7`) |
| 7 | **Chống đoán mật khẩu & ghi log bảo mật**<br>`database/schema.sql`, `inc/xac-thuc.php`, `inc/ham.php`, `dang-nhap.php` | Bảng `dang_nhap_sai`; đếm số lần sai trong 15 phút: quá 5 lần -> HTTP 429 Too Many Requests, khóa tạm 15 phút, trả thông báo chung; `ghiLogBaoMat()` ghi nhận các sự kiện `csrf_sai`, `cam_truy_cap`, `dang_nhap_sai` vào `logs/bao-mat.log` (tuyệt đối không ghi mật khẩu hay mã phiên). | Nguyễn Quý Minh Hoàng (`bba99dc`) |

---

## 🛠 Yêu cầu môi trường

- **PHP** phiên bản ≥ 8.1 (bật extension `pdo_mysql`, `fileinfo`, `mbstring`, `session`)
- **MySQL / MariaDB** (trong XAMPP hoặc Laragon, collation `utf8mb4_unicode_ci`)
- **Composer** (để cài đặt và autoload `vlucas/phpdotenv`)
- **Web Server:** PHP built-in server (`php -S`) hoặc Apache (XAMPP / Laragon)

---

## 🚀 Hướng dẫn Cài đặt và Chạy

Thực hiện các lệnh sau tại thư mục gốc của dự án:

```bash
# 1. Cài đặt các thư viện phụ thuộc (phpdotenv, autoload)
composer install

# 2. Tạo cơ sở dữ liệu và nạp dữ liệu mẫu
mysql -u root < database/schema.sql
mysql -u root ltweb_nhom11 < database/seed.sql

# 3. Tạo tệp cấu hình môi trường từ mẫu
cp .env.example .env
# (Trên Windows PowerShell: Copy-Item .env.example .env)

# 4. Khởi động máy chủ cục bộ PHP
php -S localhost:8000
```
Truy cập website tại: [http://localhost:8000](http://localhost:8000)

### Kích hoạt tài khoản quyền tối thiểu (Tùy chọn)
Để chạy website dưới tài khoản bảo mật `nhom11_app`:
1. Chạy tệp phân quyền bằng tài khoản root:
   ```bash
   mysql -u root ltweb_nhom11 < database/tai-khoan.sql
   ```
2. Mở tệp `.env` và cập nhật:
   ```ini
   DB_USERNAME=nhom11_app
   DB_PASSWORD=thay-bang-mat-khau-dai-ngau-nhien
   ```
*Lưu ý: Tệp `.htaccess` chỉ có tác dụng khi chạy trên Apache (htdocs của XAMPP). Máy chủ `php -S` bỏ qua các tệp `.htaccess`.*

---

## 🔐 Tài khoản thử nghiệm (Mật khẩu dùng chung: Admin@123)

Dữ liệu tài khoản thử nghiệm được khởi tạo trong database/seed.sql (mật khẩu đã băm an toàn qua password_hash):
- **admin@sanbongthangloi.vn** / Admin@123: Quản trị viên (vai trò quan_tri — truy cập quan-tri.php).
- **khach1@sanbongthangloi.vn** / Admin@123: Khách hàng 1 (vai trò khach_hang — đặt sân, giỏ hàng, xem lich-su.php).
- **khach2@sanbongthangloi.vn** / Admin@123: Khách hàng 2 (vai trò khach_hang).
- **silaphet@sanbongthangloi.vn** / Admin@123: Tài khoản thành viên Silaphet.

---

## 🧪 Kiểm thử bảo mật (Regression Tests)

- Tệp kiểm thử REST Client: `tests/bao-mat.http`
- Tệp kịch bản kiểm thử curl: `tests/bao-mat.sh`
- Tệp kịch bản API hệ thống: `api.http`
- Toàn bộ nhật ký bảo mật được lưu tại: `logs/bao-mat.log`
