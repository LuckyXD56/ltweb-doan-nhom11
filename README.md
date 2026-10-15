# ⚽ Sân Bóng Thắng Lợi - Nhóm 11 (Phiên bản PHP)

Website giới thiệu và đặt sân bóng đá Thắng Lợi được xây dựng trong khuôn khổ học phần: **Thiết kế và Lập trình Web** (Bài tập nhóm số 5 - Lập trình PHP phía máy chủ).

## 👥 Thông tin nhóm

- **Trường:** Trường Đại học Sư phạm - Đại học Đà Nẵng
- **Khoa:** Toán - Tin
- **Nhóm:** 11
- **Học kỳ:** 1, Năm học: 2026 – 2027
- **Kho GitHub:** [https://github.com/LuckyXD56/ltweb-doan-nhom11](https://github.com/LuckyXD56/ltweb-doan-nhom11)

### Danh sách thành viên

| STT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Vai trò | Chức năng phụ trách (Phần A) |
|---|---|---|---|---|---|
| 1 | **Phimmasone Khamphouvanh** | **3120224192** | `LuckXD56` | **Trưởng nhóm** | Chức năng 5 (Giỏ hàng) & Bảng 3 (Kiểm thử) |
| 2 | Nguyễn Quý Minh Hoàng | 3120224060 | `nguyenquyminhhoang` | Thành viên | Chức năng 6 (Đăng nhập, Quản trị, Đăng xuất) |
| 3 | Lê Nguyễn Gia Nhân | 3120224105 | `Gianhanle999` | Thành viên | Chức năng 3 (Chi tiết theo id, Cookie đã xem gần đây) |
| 4 | Silaphet Thit | 3120224188 | `Silaphet` | Thành viên | Chức năng 4 (Biểu mẫu liên hệ, Upload ảnh, JSONL) |
| 5 | Trương Nguyễn Ngọc Phú | 3120224111 | `phutruong317-cloud` | Thành viên | Chức năng 1 (Khung trang dùng chung) & Chức năng 2 (Lọc danh sách) |

---

## 🛠 Yêu cầu môi trường

Để chạy dự án này, môi trường cần cài đặt:
- **PHP** phiên bản ≥ 8.1 (có bật extension `fileinfo`, `mbstring`, `session`)
- **Composer** (để quản lý autoload PSR-4)
- Web server: PHP built-in server (`php -S`) hoặc XAMPP / Laragon

---

## 🚀 Các lệnh cài đặt và chạy

Mở Terminal / PowerShell tại thư mục gốc của dự án và chạy các lệnh sau:

**1. Cài đặt các phụ thuộc (Autoload PSR-4):**
```bash
composer install
```
*(Hoặc `composer dump-autoload` nếu không có file composer.lock)*

**2. Khởi động Web Server cục bộ của PHP:**
```bash
php -S localhost:8000
```

**3. Xem trang web trên trình duyệt:**
Mở trình duyệt và truy cập: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Tài khoản thử trang quản trị

Để kiểm tra trang Quản trị viên (`quan-tri.php`), vui lòng sử dụng tài khoản sau:
- **Tên đăng nhập:** `admin`
- **Mật khẩu:** `Admin@123`

---

## 🗺 Bảng Chức năng – URL – Tệp PHP

| Chức năng | URL truy cập | Tệp PHP xử lý chính |
| :--- | :--- | :--- |
| **Trang chủ** | `/` hoặc `/index.php` | `index.php` |
| **Danh sách sân bóng** (có lọc GET) | `/danh-sach.php` | `danh-sach.php` |
| **Chi tiết sân** | `/chi-tiet.php?id=A1` | `chi-tiet.php` |
| **Trang liên hệ** (có biểu mẫu, upload) | `/lien-he.php` | `lien-he.php` và `inc/xu-ly-lien-he.php` |
| **Giỏ hàng / Đặt sân** | `/gio-hang.php` | `gio-hang.php` và `src/Services/GioHang.php` |
| **Đăng nhập** | `/dang-nhap.php` | `dang-nhap.php` và `inc/tai-khoan.php` |
| **Đăng xuất** | `/dang-xuat.php` | `dang-xuat.php` |
| **Quản trị (danh sách liên hệ)** | `/quan-tri.php` | `quan-tri.php` và `inc/bao-ve.php` |
| **Báo lỗi 404** (Không tìm thấy) | `/404.php` (hoặc truy cập URL sai) | `404.php` |
| **Báo lỗi 500** (Lỗi Server) | `/500.php` | `500.php` |
| **Trang cá nhân Phimmasone** | `/thanhvien/3120224192_phimmasone/gioithieu.php` | `thanhvien/3120224192_phimmasone/gioithieu.php` |
| **Trang cá nhân Hoàng** | `/thanhvien/3120224060_hoang/gioithieu.php` | `thanhvien/3120224060_hoang/gioithieu.php` |
| **Trang cá nhân Phú** | `/thanhvien/3120224111_phu/gioithieu.php` | `thanhvien/3120224111_phu/gioithieu.php` |
| **Trang cá nhân Silaphet** | `/thanhvien/3120224188_silaphet/gioithieu.php` | `thanhvien/3120224188_silaphet/gioithieu.php` |
| **Trang cá nhân Nhân** | `/thanhvien/3120224105_nhan/gioithieu.php` | `thanhvien/3120224105_nhan/gioithieu.php` |

---
*Dự án Bài tập nhóm số 5 chuyển đổi toàn bộ website sang PHP 8 động, tổ chức mã theo PSR-4 Autoload, lưu trữ JSON/JSONL an toàn, quản lý trạng thái bằng Cookie & Session, xác thực mật khẩu với password_hash và phòng chống triệt để các lỗ hổng XSS, Path Traversal, File Upload rác.*
