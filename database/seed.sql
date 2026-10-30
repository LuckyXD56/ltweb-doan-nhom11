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

-- 3. Nạp 5 Người dùng (3 quản trị gồm admin, thành viên Nhân, thành viên Silaphet; 2 khách hàng)
-- (Mật khẩu mẫu đều là Admin@123, hash bằng password_hash)
INSERT INTO nguoi_dung (vai_tro, ho_ten, email, mat_khau) VALUES
('quan_tri', 'Quản trị viên', 'admin@sanbong.vn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e'),
('khach_hang', 'Nguyễn Văn Khách', 'khach1@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e'),
('khach_hang', 'Trần Thị Khách', 'khach2@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e'),
('quan_tri', 'Lê Nguyễn Gia Nhân', 'nhan@sanbong.vn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e'),
('quan_tri', 'Silaphet Thit', 'silaphet@sanbong.vn', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1HlCS4bZ08nBIdb2x20V4gT0hC3aO6e');

-- 4. Thành viên có trang cá nhân và dữ liệu mẫu (Sổ lưu bút, Lượt xem 7 ngày)
INSERT INTO thanh_vien (mssv, ho_ten, nguoi_dung_id) VALUES
('3120224105', 'Lê Nguyễn Gia Nhân', 4),
('3120224192', 'Phimmasone Khamphouvanh', NULL),
('3120224188', 'Silaphet Thit', 5);

-- Dữ liệu mẫu danh sách công việc cá nhân của Phimmasone (tv_todo_phim)
INSERT INTO tv_todo_phim (mssv, tieu_de, hoan_thanh, ngay_tao) VALUES
('3120224192', 'Hoàn thành bài tập 6 môn Lập trình Web', 1, '2026-10-25 08:30:00'),
('3120224192', 'Thiết kế cơ sở dữ liệu MySQL và PDO Prepared Statements', 1, '2026-10-26 10:15:00'),
('3120224192', 'Kiểm thử 0 lỗi W3C Markup Validator và responsive 360px', 0, '2026-10-27 15:00:00'),
('3120224192', 'Ôn tập kiến thức Transaction và REST API', 0, '2026-10-28 09:00:00');

-- Dữ liệu mẫu lịch sử đo chỉ số BMI của Phimmasone (tv_bmi_phim)
INSERT INTO tv_bmi_phim (mssv, chieu_cao, can_nang, chi_so_bmi, phan_loai, ngay_tao) VALUES
('3120224192', 170.0, 65.0, 22.5, 'Bình thường (Lý tưởng)', '2026-10-24 14:00:00'),
('3120224192', 170.0, 64.5, 22.3, 'Bình thường (Lý tưởng)', '2026-10-27 08:30:00');

INSERT INTO tv_luu_but (mssv, ho_ten, loi_nhan, ngay_tao) VALUES
('3120224105', 'Nguyễn Quý Minh Hoàng', 'Giao diện trang cá nhân và thống kê đẹp lắm Nhân!', '2026-10-25 09:15:00'),
('3120224105', 'Phimmasone', 'Chúc mừng bạn đã hoàn thành chức năng lượt xem theo ngày!', '2026-10-26 14:20:00'),
('3120224105', 'Trương Nguyễn Ngọc Phú', 'Thống kê 7 ngày chạy rất mượt mà.', '2026-10-27 10:30:00'),
('3120224105', 'Silaphet Thit', 'Website rất trực quan, cố lên nhé!', '2026-10-28 08:00:00'),
-- Dữ liệu sổ lưu bút của Silaphet Thit (6 lời nhắn để thử nghiệm phân trang 5 mục/trang)
('3120224188', 'Lê Nguyễn Gia Nhân', 'Chào Silaphet, chúc bạn hoàn thành xuất sắc đồ án nhé!', '2026-10-25 09:30:00'),
('3120224188', 'Phimmasone Khamphouvanh', 'Giao diện trang cá nhân và phần liên hệ của bạn rất trực quan!', '2026-10-26 11:20:00'),
('3120224188', 'Nguyễn Quý Minh Hoàng', 'Hẹn bạn trận đá bóng giao lưu cuối tuần này!', '2026-10-27 16:45:00'),
('3120224188', 'Trương Nguyễn Ngọc Phú', 'Cố gắng cùng nhóm đạt điểm tối đa Bài tập 6 nhé.', '2026-10-28 08:15:00'),
('3120224188', 'Khách hàng Đà Nẵng', 'Hệ thống website sân bóng Thắng Lợi chạy rất mượt và tiện lợi!', '2026-10-29 10:00:00'),
('3120224188', 'Thầy giáo hướng dẫn', 'Trang web trình bày chỉn chu, cơ sở dữ liệu thiết kế chuẩn hóa.', '2026-10-30 08:00:00');

INSERT INTO tv_luot_xem (mssv, ngay, luot_xem) VALUES
('3120224105', CURRENT_DATE - INTERVAL 6 DAY, 3),
('3120224105', CURRENT_DATE - INTERVAL 5 DAY, 5),
('3120224105', CURRENT_DATE - INTERVAL 4 DAY, 2),
('3120224105', CURRENT_DATE - INTERVAL 3 DAY, 7),
('3120224105', CURRENT_DATE - INTERVAL 2 DAY, 4),
('3120224105', CURRENT_DATE - INTERVAL 1 DAY, 6),
('3120224105', CURRENT_DATE, 8),
-- Lượt xem 7 ngày gần nhất của Silaphet Thit
('3120224188', CURRENT_DATE - INTERVAL 6 DAY, 5),
('3120224188', CURRENT_DATE - INTERVAL 5 DAY, 8),
('3120224188', CURRENT_DATE - INTERVAL 4 DAY, 4),
('3120224188', CURRENT_DATE - INTERVAL 3 DAY, 11),
('3120224188', CURRENT_DATE - INTERVAL 2 DAY, 7),
('3120224188', CURRENT_DATE - INTERVAL 1 DAY, 12),
('3120224188', CURRENT_DATE, 15);

-- Dữ liệu mẫu bảng dự án cá nhân của Silaphet Thit (tv_du_an)
INSERT INTO tv_du_an (mssv, ten_du_an, loai_du_an, mo_ta, cong_nghe, tien_do, trang_thai, ngay_tao) VALUES
('3120224188', 'Website Đặt Sân Bóng Thắng Lợi', 'Web', 'Xây dựng website đặt sân bóng trực tuyến với PHP, MySQL, PDO và Bootstrap 5.', 'PHP 8, MySQL, PDO, HTML5/CSS3, JavaScript', 100, 'hoan_thanh', '2026-09-15 08:00:00'),
('3120224188', 'Ứng dụng Quản lý Chi tiêu Cá nhân', 'Mobile', 'App di động theo dõi thu chi hàng ngày, hỗ trợ đa tiền tệ VND và LAK.', 'Flutter, Dart, SQLite, REST API', 85, 'dang_lam', '2026-10-01 09:30:00'),
('3120224188', 'Hệ thống Bản đồ Sân bóng Đà Nẵng', 'Web', 'Tích hợp Google Maps API và Geolocation định vị các cụm sân bóng phong trào.', 'JavaScript ES6, Leaflet, Google Maps API', 90, 'dang_lam', '2026-10-10 14:15:00'),
('3120224188', 'Thiết kế Bộ nhận diện Thương hiệu Thắng Lợi Sport', 'Design', 'Thiết kế logo, banner, áo thi đấu và poster quảng bá giải bóng đá mini.', 'Figma, Adobe Photoshop, Illustrator', 100, 'hoan_thanh', '2026-09-20 10:00:00'),
('3120224188', 'Phân tích Dữ liệu Lượt đặt Sân theo Khung giờ', 'Data', 'Thu thập và phân tích dữ liệu đặt sân theo giờ vàng để tối ưu hóa giá thuê.', 'Python, Pandas, Matplotlib, MySQL', 60, 'dang_lam', '2026-10-18 16:00:00');

-- 5. Nạp Đơn hàng (Mỗi khách hàng có ít nhất 1 đơn)
INSERT INTO dat_san (nguoi_dung_id, ngay_dat, tong_tien, trang_thai) VALUES
(2, '2026-10-20 10:00:00', 500000, 'hoan_tat'), -- Đơn của Khách 1
(3, '2026-10-21 14:30:00', 700000, 'da_coc');    -- Đơn của Khách 2

-- 6. Nạp Chi tiết đơn (Bảng trung gian n-n)
INSERT INTO chi_tiet_dat_san (dat_san_id, san_bong_id, ngay_thue, gio_bat_dau, gio_ket_thuc, don_gia, thanh_tien) VALUES
(1, 1, '2026-10-22', '18:00:00', '20:00:00', 200000, 400000), -- Khách 1 đặt Sân A1 (2 tiếng)
(1, 10, '2026-10-23', '17:00:00', '17:30:00', 200000, 100000), -- Khách 1 đặt thêm Sân A10 (nửa tiếng - ví dụ)
(2, 11, '2026-10-24', '19:00:00', '21:00:00', 350000, 700000); -- Khách 2 đặt Sân B1 (2 tiếng)

-- 7. Nạp dữ liệu mẫu Bảng LIÊN HỆ (Phần A do Silaphet phụ trách, có ảnh và không có ảnh)
INSERT INTO lien_he (ho_ten, email, noi_dung, hinh_anh, ngay_gui) VALUES
('Nguyễn Văn Tuấn', 'tuan.nguyen@gmail.com', 'Tôi muốn đặt sân cố định vào tối thứ 4 và thứ 6 hàng tuần từ 18h đến 20h. Vui lòng báo giá ưu đãi tháng.', NULL, '2026-10-25 10:15:00'),
('Trần Quốc Bảo', 'baotq@company.vn', 'Công ty chúng tôi dự kiến tổ chức giải bóng mini 8 đội vào chủ nhật tuần tới. Gửi kèm sơ đồ dự kiến giải đấu.', 'lien-he-giai-dau.jpg', '2026-10-26 14:30:00'),
('Lê Thị Mai', 'maile@hotmail.com', 'Sân có dịch vụ cho thuê trọng tài và nước uống đóng chai không ạ? Xin cảm ơn.', NULL, '2026-10-27 09:00:00'),
('Hoàng Nam', 'namhoang.fc@gmail.com', 'Hôm qua đội tôi đá ở Sân A1 có để quên 1 quả bóng thi đấu Mikasa màu cam, nhờ sân giữ giúp.', 'lien-he-bong-mikasa.jpg', '2026-10-28 17:20:00');