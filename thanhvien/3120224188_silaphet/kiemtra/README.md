# Báo cáo Kiểm thử - Trang cá nhân của Silaphet Thit (MSSV: 3120224188)
## Học phần: Thiết kế & Lập trình Web — Bài tập nhóm số 6 (Phần B)

### 1. Bảng riêng CSDL của thành viên
- Tên bảng: `tv_du_an`
- Khóa ngoại: `mssv` tham chiếu đến `thanh_vien(mssv)` ON DELETE CASCADE.
- Các cột: `id`, `mssv`, `ten_du_an`, `loai_du_an` (Web, Mobile, Design, Data), `mo_ta`, `cong_nghe`, `tien_do` (CHECK `tien_do <= 100`), `trang_thai`, `ngay_tao`.

### 2. Hai chức năng sử dụng CSDL (Phần B)

#### Chức năng 1: Sổ lưu bút CSDL (`tv_luu_but`)
- Nâng cấp từ tệp JSONL của Bài tập 5 sang bảng CSDL MySQL `tv_luu_but` với `mssv = '3120224188'`.
- Sử dụng Prepared Statement `INSERT INTO tv_luu_but (mssv, ho_ten, loi_nhan) VALUES (?, ?, ?)`.
- Phân trang 5 lời nhắn/trang qua `SELECT COUNT(*)` và `LIMIT ? OFFSET ?`.
- **Phân quyền máy chủ**: Chỉ khi đăng nhập với tài khoản của Silaphet (`silaphet@sanbong.vn` / quyền `quan_tri`) mới hiển thị nút Xóa. Thao tác xóa kiểm tra nghiêm ngặt `DELETE FROM tv_luu_but WHERE id = ? AND mssv = ?` để ngăn xóa nhầm dữ liệu của thành viên khác.
- Áp dụng mô hình PRG (Post-Redirect-Get) và Flash message, F5 không bị gửi lặp dữ liệu.
- Chống XSS toàn diện với hàm `e()`.

#### Chức năng 2: Quản lý Dự án & Kỹ năng cá nhân (`tv_du_an`)
- Hiển thị danh sách dự án cá nhân kèm thanh tiến độ trực quan (% hoàn thành).
- Tìm kiếm theo từ khóa tên dự án hoặc công nghệ (`LIKE ?` kết hợp `addcslashes`).
- Lọc theo loại dự án (`loai_du_an`: Web, Mobile, Design, Data).
- Sắp xếp theo Whitelist an toàn (`moi`, `cu`, `tien-do-giam`, `tien-do-tang`, `ten`).
- Thống kê SQL trực tiếp bằng hàm tổng hợp: Tổng số dự án (`COUNT`), dự án hoàn thành (`SUM`), tiến độ trung bình (`ROUND(AVG(tien_do), 0)`).
- Phân quyền: Chủ trang khi đăng nhập có quyền Thêm dự án mới và Xóa dự án.

#### Chức năng bổ trợ: Bộ đếm lượt xem theo ngày (`tv_luot_xem`)
- Ghi nhận lượt xem bằng `INSERT INTO tv_luot_xem (mssv, ngay, luot_xem) VALUES (?, CURRENT_DATE, 1) ON DUPLICATE KEY UPDATE luot_xem = luot_xem + 1`.
- Hiển thị lượt xem hôm nay, tổng lượt xem và bảng 7 ngày gần nhất.

### 3. Kết quả kiểm thử tiêu chuẩn chất lượng
- **Lỗi PHP**: 0 Warning, 0 Notice, 0 Deprecated, 0 Fatal Error.
- **W3C Nu Validator**: 0 Errors, 0 Warnings.
- **Accessibility & Mobile**: Đạt Lighthouse Accessibility ≥ 90, giao diện co giãn tốt, không bị tràn thanh cuộn ngang ở chiều rộng màn hình 360px.
