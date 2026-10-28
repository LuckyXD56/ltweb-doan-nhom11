USE ltweb_nhom11;

-- 1. Nạp Danh mục (Có 1 danh mục chưa có dữ liệu là "Sân Futsal")
INSERT INTO danh_muc (ten_danh_muc, mo_ta) VALUES
('Sân 5 người', 'Sân bóng mini 5v5'),
('Sân 7 người', 'Sân bóng 7v7 phong trào'),
('Sân 11 người', 'Sân bóng 11v11 tiêu chuẩn'),
('Sân Futsal', 'Sân futsal trong nhà (Sắp ra mắt)');

-- 2. Nạp 30 Sân bóng
-- (Có 1 tên chứa dấu nháy đơn 'Levi''s Arena', có 1 sân 'het_cho' hoặc 'bao_tri')
INSERT INTO san_bong (danh_muc_id, ten_san, gia_thue, trang_thai, hinh_anh) VALUES
-- Sân 5 người (danh_muc_id = 1)
(1, 'Sân A1', 200000, 'hoat_dong', 'images/san-a1.jpg'),
(1, 'Sân A2', 200000, 'hoat_dong', 'images/san-a2.jpg'),
(1, 'Sân A3', 200000, 'hoat_dong', 'images/san-a3.jpg'),
(1, 'Sân A4', 190000, 'hoat_dong', 'images/san-a4.jpg'),
(1, 'Sân A5', 200000, 'hoat_dong', 'images/san-a5.jpg'),
(1, 'Sân A6', 180000, 'hoat_dong', 'images/san-a6.jpg'),
(1, 'Sân A7', 200000, 'hoat_dong', 'images/san-a7.jpg'),
(1, 'Sân A8', 200000, 'hoat_dong', 'images/san-a8.jpg'),
(1, 'Sân A9', 190000, 'hoat_dong', 'images/san-a9.jpg'),
(1, 'Sân A10', 200000, 'het_cho', 'images/san-a10.jpg'),
-- Sân 7 người (danh_muc_id = 2)
(2, 'Sân B1', 300000, 'hoat_dong', 'images/san-b1.jpg'),
(2, 'Sân B2', 350000, 'hoat_dong', 'images/san-b2.jpg'),
(2, 'Sân B3', 320000, 'hoat_dong', 'images/san-b3.jpg'),
(2, 'Sân B4', 300000, 'hoat_dong', 'images/san-b4.jpg'),
(2, 'Sân B5', 330000, 'hoat_dong', 'images/san-b5.jpg'),
(2, 'Sân B6', 300000, 'hoat_dong', 'images/san-b6.jpg'),
(2, 'Sân B7', 350000, 'hoat_dong', 'images/san-b7.jpg'),
(2, 'Sân B8', 320000, 'hoat_dong', 'images/san-b8.jpg'),
(2, 'Sân B9', 300000, 'hoat_dong', 'images/san-b9.jpg'),
(2, 'Sân B10', 350000, 'bao_tri', 'images/san-b10.jpg'),
-- Sân 11 người (danh_muc_id = 3)
(3, 'Sân C1', 600000, 'hoat_dong', 'images/san-c1.jpg'),
(3, 'Sân C2', 580000, 'hoat_dong', 'images/san-c2.jpg'),
(3, 'Sân C3', 650000, 'hoat_dong', 'images/san-c3.jpg'),
(3, 'Sân C4', 600000, 'hoat_dong', 'images/san-c4.jpg'),
(3, 'Sân C5', 700000, 'hoat_dong', 'images/san-c5.jpg'),
(3, 'Sân C6', 600000, 'hoat_dong', 'images/san-c6.jpg'),
(3, 'Sân C7', 650000, 'hoat_dong', 'images/san-c7.jpg'),
(3, 'Sân C8', 600000, 'hoat_dong', 'images/san-c8.jpg'),
(3, 'Sân C9', 700000, 'hoat_dong', 'images/san-c9.jpg'),
(3, 'Levi''s Arena', 800000, 'hoat_dong', 'images/san-levis.jpg');

-- 3. Nạp 3 Người dùng (1 admin, 2 khách)
-- (Mật khẩu mẫu đều là Admin@123, hash bằng password_hash)
INSERT INTO nguoi_dung (vai_tro, ho_ten, email, mat_khau) VALUES
('quan_tri', 'Quản trị viên', 'admin@sanbong.vn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e'),
('khach_hang', 'Nguyễn Văn Khách', 'khach1@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e'),
('khach_hang', 'Trần Thị Khách', 'khach2@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e');

-- 4. Nạp Đơn hàng (Mỗi khách hàng có ít nhất 1 đơn)
INSERT INTO dat_san (nguoi_dung_id, ngay_dat, tong_tien, trang_thai) VALUES
(2, '2026-10-20 10:00:00', 500000, 'hoan_tat'), -- Đơn của Khách 1
(3, '2026-10-21 14:30:00', 700000, 'da_coc');    -- Đơn của Khách 2

-- 5. Nạp Chi tiết đơn (Bảng trung gian n-n)
INSERT INTO chi_tiet_dat_san (dat_san_id, san_bong_id, ngay_thue, gio_bat_dau, gio_ket_thuc, don_gia, thanh_tien) VALUES
(1, 1, '2026-10-22', '18:00:00', '20:00:00', 200000, 400000), -- Khách 1 đặt Sân A1 (2 tiếng)
(1, 10, '2026-10-23', '17:00:00', '17:30:00', 200000, 100000), -- Khách 1 đặt thêm Sân A10 (nửa tiếng - ví dụ)
(2, 11, '2026-10-24', '19:00:00', '21:00:00', 350000, 700000); -- Khách 2 đặt Sân B1 (2 tiếng)

-- =========================================================
-- DỮ LIỆU MẪU CÁ NHÂN (MSSV: 3120224060 - Nguyễn Quý Minh Hoàng)
-- =========================================================

-- Thêm tài khoản quản trị/thành viên liên kết với bạn vào bảng nguoi_dung
INSERT INTO nguoi_dung (id, vai_tro, ho_ten, email, mat_khau) VALUES 
(4, 'quan_tri', 'Nguyễn Quý Minh Hoàng', 'hoang3120224060@st.duytan.edu.vn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e')
ON DUPLICATE KEY UPDATE ho_ten = VALUES(ho_ten);

-- Thêm thông tin sinh viên vào bảng thanh_vien
INSERT INTO thanh_vien (mssv, ho_ten, nguoi_dung_id) VALUES 
('3120224060', 'Nguyễn Quý Minh Hoàng', 4)
ON DUPLICATE KEY UPDATE ho_ten = VALUES(ho_ten);

-- Dữ liệu mẫu cho bảng lưu bút cá nhân (tv_luu_but)
INSERT INTO tv_luu_but (mssv, ten_nguoi_gui, noi_dung) VALUES
('3120224060', 'Trần Văn Nam', 'Trang cá nhân thiết kế giao diện đặt sân bóng rất trực quan và mượt mà!'),
('3120224060', 'Võ Hoàng Vũ', 'Chúc Hoàng hoàn thành xuất sắc đồ án môn Lập trình Web nhóm 11 nhé.');

-- Dữ liệu mẫu cho bảng dự án cá nhân (tv_du_an)
INSERT INTO tv_du_an (mssv, ten_du_an, mo_ta, link_demo) VALUES
('3120224060', 'Sân Bóng Thắng Lợi', 'Website đặt sân bóng đá trực tuyến (Đồ án Nhóm 11 - Lập trình Web)', 'https://github.com/LuckyXD56/ltweb-doan-nhom11');
-- Dữ liệu mẫu lượt xem 7 ngày gần nhất cho MSSV 3120224060
INSERT INTO tv_luot_xem (mssv, ngay_xem, luot_xem) VALUES
('3120224060', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 12),
('3120224060', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 25),
('3120224060', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 18),
('3120224060', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 30),
('3120224060', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 42),
('3120224060', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 35),
('3120224060', CURDATE(), 15)
ON DUPLICATE KEY UPDATE luot_xem = VALUES(luot_xem);