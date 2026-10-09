# ⚽ Sân Bóng Thắng Lợi - Nhóm 11 (Phiên bản PHP)

Website giới thiệu và đặt sân bóng đá Thắng Lợi được xây dựng trong khuôn khổ học phần: **Thiết kế và Lập trình Web** (Bài tập 5 - Lập trình PHP phía máy chủ).

## 👥 Thông tin nhóm

- **Trường:** Trường Đại học Sư phạm - Đại học Đà Nẵng
- **Khoa:** Toán - Tin
- **Nhóm:** 11
- **Năm học:** 2026-2027

### Thành viên

| STT | Họ và tên | MSSV | Vai trò | Chức năng phụ trách (Phần A) |
|---|---|---|---|---|
| 1 | Phimmasone Khamphouvanh | 3120224192 | Trưởng nhóm | Kiểm thử phía máy chủ (Bảng 3) |
| 2 | Nguyễn Quý Minh Hoàng | 3120224060 | Thành viên | Kiểm thử phía máy chủ (Bảng 3) |
| 3 | Lê Nguyễn Gia Nhân | 3120224105 | Thành viên | Báo cáo thảo luận (Phần C) |
| 4 | Silaphet Thit | 3120224188 | Thành viên | Lớp PHP truy cập dữ liệu (Bảng 2) |
| 5 | Trương Nguyễn Ngọc Phú | 3120224111 | Thành viên | Cấu trúc khung trang dùng chung, giao diện (Bảng 1) |

---

## 🛠 Yêu cầu môi trường

Để chạy dự án này, máy tính của bạn cần cài đặt:
- **PHP** phiên bản ≥ 8.1
- **Composer** (để quản lý autoload thư viện/class)
- Môi trường: XAMPP, Laragon hoặc chạy trực tiếp bằng PHP built-in server.

## 🚀 Các lệnh cài đặt và chạy

Mở Terminal / PowerShell tại thư mục gốc của dự án và chạy các lệnh sau:

**1. Cài đặt các phụ thuộc (Autoload PSR-4):**
`ash
 composer install
`
*(Hoặc composer dump-autoload nếu không có file composer.lock)*

**2. Chạy Server cục bộ của PHP:**
`ash
 php -S localhost:8000
`

**3. Xem trang web:**
Mở trình duyệt và truy cập: [http://localhost:8000](http://localhost:8000)

---

## 🔐 Tài khoản thử trang quản trị

Để kiểm tra trang Quản trị viên (quan-tri.php), vui lòng sử dụng tài khoản sau:
- **Tên đăng nhập:** dmin
- **Mật khẩu:** 123456

---

## 🗺 Bảng Chức năng – URL – Tệp PHP

| Chức năng | URL truy cập | Tệp PHP xử lý chính |
| :--- | :--- | :--- |
| **Trang chủ** | / hoặc /index.php | index.php |
| **Danh sách sân bóng** (có lọc GET) | /danh-sach.php | danh-sach.php |
| **Chi tiết sân** | /chi-tiet.php?id=1 | chi-tiet.php |
| **Trang liên hệ** (có biểu mẫu, upload) | /lien-he.php | lien-he.php và inc/xu-ly-lien-he.php |
| **Giỏ hàng / Đặt sân** | /gio-hang.php | gio-hang.php và src/Services/GioHang.php |
| **Đăng nhập** | /dang-nhap.php | dang-nhap.php và inc/tai-khoan.php |
| **Đăng xuất** | /dang-xuat.php | dang-xuat.php |
| **Quản trị (danh sách liên hệ)** | /quan-tri.php | quan-tri.php và inc/bao-ve.php |
| **Báo lỗi 404** (Không tìm thấy) | Các URL không tồn tại | 404.php |
| **Báo lỗi 500** (Lỗi Server) | Sinh lỗi hệ thống (ví dụ: mất DB) | 500.php |
| **Trang cá nhân Phimmasone** | /thanhvien/3120224192_phimmasone/gioithieu.php | 	hanhvien/3120224192_phimmasone/gioithieu.php |
| **Trang cá nhân Hoàng** | /thanhvien/3120224060_hoang/gioithieu.php | 	hanhvien/3120224060_hoang/gioithieu.php |
| **Trang cá nhân Phú** | /thanhvien/3120224111_phu/gioithieu.php | 	hanhvien/3120224111_phu/gioithieu.php |
| **Trang cá nhân Silaphet** | /thanhvien/3120224188_silaphet/gioithieu.php | 	hanhvien/3120224188_silaphet/gioithieu.php |
| **Trang cá nhân Nhân** | /thanhvien/3120224105_nhan/gioithieu.php | 	hanhvien/3120224105_nhan/gioithieu.php |

---
*Dự án Bài tập 5 tập trung vào khả năng chuyển đổi HTML tĩnh sang Web động PHP, xử lý Autoload (PSR-4), Cookie/Session, Validation phía máy chủ, PRG Pattern và viết mã an toàn chống XSS/Upload dỏm.*
