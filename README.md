# ⚽ Sân Bóng Thắng Lợi - Nhóm 11

**🌐 Xem trang web trực tiếp tại đây:** [https://LuckyXD56.github.io/ltweb-doan-nhom11](https://LuckyXD56.github.io/ltweb-doan-nhom11)

Website giới thiệu và đặt sân bóng Thắng Lợi được xây dựng trong khuôn khổ học phần: **Thiết kế và Lập trình Web**.

## 👥 Thông tin nhóm

- **Trường:** Trường Đại học Sư phạm – Đại học Đà Nẵng
- **Khoa:** Toán – Tin
- **Nhóm:** 11
- **Năm học:** 2026–2027

### Thành viên

| STT | Họ và tên | MSSV | Vai trò |
|---|---|---|---|
| 1 | Phimmasone Khamphouvanh | 3120224192 | Trưởng nhóm |
| 2 | Nguyễn Quý Minh Hoàng | 3120224060 | Thành viên |
| 3 | Lê Nguyễn Gia Nhân | 3120224105 | Thành viên |
| 4 | Silaphet Thit | 3120224188 | Thành viên |
| 5 | Trương Nguyễn Ngọc Phú | 3120224111 | Thành viên |

## 🎯 Giới thiệu dự án

Website cung cấp thông tin và chức năng đặt sân bóng đá Thắng Lợi:

- Trang chủ giới thiệu các loại sân và tiện ích
- Danh sách sân bóng và trang chi tiết sân
- Lịch trống theo từng khung giờ (2 phiên bản: CSS thuần + Bootstrap 5)
- Form đặt sân trực tuyến có kiểm tra hợp lệ HTML5
- Đăng nhập / Đăng ký tài khoản
- Lịch sử đặt sân
- Trang quản trị (bảng giá, thống kê doanh thu)
- Giới thiệu nhóm và trang cá nhân từng thành viên
- Form liên hệ có validation và gửi `fetch POST`
- Tính năng yêu thích sân bằng `localStorage`
- Chế độ tối (dark mode) và sao chép email trên trang cá nhân

## 🛠️ Công nghệ sử dụng

- **HTML5** (Semantic HTML)
- **CSS3** (Flexbox, CSS Grid, BEM, CSS Variables)
- **JavaScript ES Modules** (`import`/`export`)
- **Responsive Web Design** (Mobile-first)
- **Bootstrap 5** (trang `san-lich-bootstrap.html` để so sánh)
- **Fetch API + JSON** (`data/san.json`)
- **localStorage** (lưu chế độ tối, danh sách yêu thích)
- **Git & GitHub** (làm việc nhóm)
- **GitHub Pages** (hosting)

## 📁 Cấu trúc thư mục

```text
ltweb-doan-nhom11/
├── index.html                  # Trang chủ
├── san-lich.html               # Sân & lịch trống (CSS thuần)
├── san-lich-bootstrap.html     # Sân & lịch trống (Bootstrap 5)
├── danh-sach.html              # Danh sách sân
├── chi-tiet.html               # Chi tiết sân (đọc ?id=)
├── dat-san.html                # Form đặt sân
├── tai-khoan.html              # Đăng nhập / Đăng ký
├── lich-su.html                # Lịch sử đặt sân
├── quan-tri.html               # Trang quản trị
├── lien-he.html                # Liên hệ (form + bản đồ)
├── thanh-vien.html             # Trang hub giới thiệu nhóm
├── README.md
├── css/
│   ├── chung.css               # Biến, reset, layout chính
│   ├── thanh-phan.css          # Header, nav, form, bảng, thẻ
│   └── bootstrap-tuy-chinh.css # Tùy biến Bootstrap 5
├── js/
│   ├── trang-chung.js          # Nạp menu + yêu thích cho mọi trang
│   ├── trang-index.js          # Trang chủ
│   ├── trang-chi-tiet.js       # Đọc ?id= và render chi tiết sân
│   ├── trang-dat-san.js        # Xử lý form đặt sân
│   ├── trang-san-lich.js       # Lọc & tìm kiếm sân
│   ├── trang-tai-khoan.js      # Đăng nhập / Đăng ký
│   ├── trang-lien-he.js        # Form liên hệ
│   ├── trang-thanh-vien.js     # Trang hub thành viên
│   └── modules/
│       ├── menu.js             # Menu mobile
│       ├── yeuThich.js         # Quản lý yêu thích
│       ├── lienHe.js           # Validation + fetch POST form liên hệ
│       ├── tienIch.js          # Hàm tiện ích (định dạng tiền, ...)
│       ├── trangChu.js         # Logic trang chủ
│       ├── danhSachSan.js      # Logic danh sách sân
│       ├── chiTietSan.js       # Logic chi tiết sân
│       ├── datSan.js           # Logic đặt sân
│       ├── taiKhoan.js         # Logic tài khoản
│       └── trang-thanh-vien.js # Logic trang hub thành viên
├── data/
│   ├── san.json                # Dữ liệu các sân (dùng cho chi tiết + danh sách)
│   └── san-bong.json           # Dữ liệu bổ sung
├── images/                     # Ảnh sân, nhân viên, avatar
├── kiemtra/                    # Minh chứng bài tập
└── thanhvien/                  # Trang cá nhân từng thành viên
    ├── 3120224060_hoang/
    │   ├── gioithieu.html
    │   ├── profile.css
    │   ├── avatar.jpg
    │   ├── js/canhan.js
    │   └── kiemtra/
    ├── 3120224105_nhan/
    ├── 3120224111_phu/
    ├── 3120224188_silaphet/
    └── 3120224192_phimmasone/
```

## ✨ Chức năng JavaScript chính

| Chức năng | File xử lý | Mô tả |
|---|---|---|
| Chi tiết sân theo `?id=` | `js/trang-chi-tiet.js` | Đọc `URLSearchParams`, fetch `data/san.json`, đổi `document.title`, báo "Không tìm thấy" khi id sai |
| Yêu thích sân | `js/modules/yeuThich.js` | Lưu mảng id vào `localStorage`, đếm số lượng trên header, ủy quyền sự kiện |
| Form liên hệ | `js/modules/lienHe.js` | `setCustomValidity`, báo lỗi từng ô khi blur + submit, `fetch POST`, khoá nút khi chờ |
| Lọc + tìm sân | `js/trang-san-lich.js` | Lọc theo loại sân, tìm theo tên/khu vực (không dấu) |
| Menu mobile | `js/modules/menu.js` | Đóng/mở menu, hỗ trợ phím Esc |
| Chế độ tối trang cá nhân | `thanhvien/*/js/canhan.js` | Lưu lựa chọn vào `localStorage` |
| Sao chép email | `thanhvien/*/js/canhan.js` | Dùng `navigator.clipboard.writeText` |

## 📱 Responsive

- **Mobile-first:** điểm ngắt `360px`, `600px`, `768px`, `960px`
- **Bảng:** bọc trong `.bang-chua` với `overflow-x: auto` để cuộn ngang trên mobile 360px
- **Menu:** nút "Mở menu" cho mobile, có `noscript` fallback khi tắt JavaScript

## ♿ Khả năng truy cập (Accessibility)

- HTML5 semantic: `<header>`, `<nav>`, `<main>`, `<footer>`, `<article>`, `<section>`
- `aria-label`, `aria-current="page"` trên menu
- `aria-live="polite"` trên các vùng thông báo
- Focus dùng `outline` (không tắt outline)
- Tương phản chữ–nền ≥ 4.5:1

## 🚀 Chạy dự án cục bộ

Dự án dùng ES Modules và `fetch`, nên cần chạy qua HTTP server (không mở trực tiếp bằng `file://`).

**Cách 1 — Live Server (khuyến nghị):**
1. Cài extension **Live Server** trong VSCode
2. Mở `index.html` → chuột phải → **Open with Live Server**

**Cách 2 — Python:**

```bash
python -m http.server 8000
```

Rồi mở `http://localhost:8000`.

## 📚 Tài liệu tham khảo

- [MDN — URLSearchParams](https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams)
- [MDN — Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
- [MDN — localStorage](https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage)
- [MDN — Constraint Validation](https://developer.mozilla.org/en-US/docs/Web/HTML/Constraint_validation)
- [W3C Validator](https://validator.w3.org/)


