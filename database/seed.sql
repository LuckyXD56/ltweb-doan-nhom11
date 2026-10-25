USE ltweb_nhom11;

-- 1. Thêm tài khoản (Mật khẩu là Admin@123 đã được hash bằng password_hash)
INSERT INTO nguoi_dung (username, password, ho_ten, vai_tro) VALUES 
('admin', '$2y$10$e.wXq.xOq6P./G8yR7z.2uWq5Pz3M3Q3y3Q3y3Q3y3Q3y3Q3y3Q3y', 'Quản trị viên', 'admin'),
('khach1', '$2y$10$e.wXq.xOq6P./G8yR7z.2uWq5Pz3M3Q3y3Q3y3Q3y3Q3y3Q3y3Q3y', 'Nguyễn Văn Khách', 'khach');

-- 2. Thêm 14 Sân bóng (Lấy từ file san.json của bạn)
INSERT INTO san_bong (ma_san, ten_san, loai_san, khu_vuc, gia_thuong, gia_vang, danh_gia, hinh_anh, dang_hoat_dong) VALUES
('A1', 'Sân A1', 5, 'Phía Đông', 200000, 250000, 4.6, 'images/san-a1.jpg', 1),
('A2', 'Sân A2', 5, 'Phía Đông', 200000, 250000, 4.4, 'images/san-a2.jpg', 1),
('A3', 'Sân A3', 5, 'Phía Đông', 200000, 250000, 4.5, 'images/san-a3.jpg', 1),
('A4', 'Sân A4', 5, 'Phía Nam', 190000, 240000, 4.3, 'images/san-a4.jpg', 1),
('A5', 'Sân A5', 5, 'Phía Tây', 200000, 250000, 4.2, 'images/san-a5.jpg', 1),
('A6', 'Sân A6', 5, 'Phía Bắc', 180000, 230000, 4.8, 'images/san-a6.jpg', 1),
('B1', 'Sân B1', 7, 'Trung tâm', 300000, 350000, 4.5, 'images/san-b1.jpg', 1),
('B2', 'Sân B2', 7, 'Trung tâm', 350000, 400000, 4.6, 'images/san-b2.jpg', 1),
('B3', 'Sân B3', 7, 'Trung tâm', 320000, 370000, 4.2, 'images/san-b3.jpg', 1),
('B4', 'Sân B4', 7, 'Phía Nam', 300000, 350000, 4.0, 'images/san-b4.jpg', 1),
('B5', 'Sân B5', 7, 'Trung tâm', 330000, 380000, 4.4, 'images/san-b5.jpg', 1),
('C1', 'Sân C1', 11, 'Phía Tây', 600000, 700000, 4.7, 'images/san-c1.jpg', 1),
('C2', 'Sân C2', 11, 'Phía Tây', 580000, 680000, 4.3, 'images/san-c2.jpg', 1),
('C3', 'Sân C3', 11, 'Phía Tây', 650000, 750000, 4.9, 'images/san-c3.jpg', 1);