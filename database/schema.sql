-- Xóa CSDL nếu đã tồn tại và tạo lại từ đầu
DROP DATABASE IF EXISTS ltweb_nhom11;
CREATE DATABASE ltweb_nhom11 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ltweb_nhom11;

-- 1. Bảng Người dùng (Quản trị & Khách)
CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL COMMENT 'Lưu mật khẩu dạng hash',
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    vai_tro ENUM('admin', 'khach') DEFAULT 'khach',
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Bảng Sân bóng (Thay cho san.json)
CREATE TABLE san_bong (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_san VARCHAR(10) NOT NULL UNIQUE,
    ten_san VARCHAR(100) NOT NULL,
    loai_san INT NOT NULL COMMENT '5, 7, hoặc 11',
    khu_vuc VARCHAR(100),
    gia_thuong INT NOT NULL,
    gia_vang INT NOT NULL,
    danh_gia DECIMAL(2,1) DEFAULT 0.0,
    hinh_anh VARCHAR(255),
    dang_hoat_dong TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

-- 3. Bảng Phiếu đặt sân (Giao dịch)
CREATE TABLE dat_san (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    san_bong_id INT NOT NULL,
    ngay_dat DATE NOT NULL,
    gio_bat_dau TIME NOT NULL,
    gio_ket_thuc TIME NOT NULL,
    tong_tien INT NOT NULL,
    trang_thai ENUM('cho_xac_nhan', 'da_coc', 'hoan_tat', 'huy') DEFAULT 'cho_xac_nhan',
    thoi_gian_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_dat_san_nguoi_dung FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT fk_dat_san_san_bong FOREIGN KEY (san_bong_id) REFERENCES san_bong(id) ON DELETE CASCADE
) ENGINE=InnoDB;