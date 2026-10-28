-- Xóa CSDL nếu tồn tại và tạo mới
DROP DATABASE IF EXISTS ltweb_nhom11;
CREATE DATABASE ltweb_nhom11 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ltweb_nhom11;

-- Bật chế độ kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Bảng DANH MỤC (Loại sân)
CREATE TABLE danh_muc (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten_danh_muc VARCHAR(100) NOT NULL UNIQUE,
    mo_ta VARCHAR(255) NULL
) ENGINE=InnoDB;

-- 2. Bảng NGƯỜI DÙNG
CREATE TABLE nguoi_dung (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vai_tro ENUM('quan_tri', 'khach_hang') NOT NULL DEFAULT 'khach_hang',
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Bảng THỰC THỂ CHÍNH (Sân bóng)
CREATE TABLE san_bong (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    danh_muc_id INT UNSIGNED NOT NULL,
    ten_san VARCHAR(100) NOT NULL UNIQUE,
    -- Tiền dùng INT UNSIGNED hoặc DECIMAL, không dùng FLOAT
    gia_thue DECIMAL(12, 0) NOT NULL CHECK (gia_thue >= 0),
    -- Bản đồ trạng thái (Case 4 BT5)
    trang_thai ENUM('hoat_dong', 'bao_tri', 'het_cho') NOT NULL DEFAULT 'hoat_dong',
    hinh_anh VARCHAR(255) NULL,
    CONSTRAINT fk_san_bong_danh_muc FOREIGN KEY (danh_muc_id) 
        REFERENCES danh_muc(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Bảng ĐƠN HÀNG (Đặt sân)
CREATE TABLE dat_san (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT UNSIGNED NOT NULL,
    ngay_dat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tong_tien DECIMAL(12, 0) NOT NULL CHECK (tong_tien >= 0),
    trang_thai ENUM('cho_xac_nhan', 'da_coc', 'hoan_tat', 'da_huy') NOT NULL DEFAULT 'cho_xac_nhan',
    -- ON DELETE CASCADE: Xóa người dùng thì xóa luôn đơn của họ
    CONSTRAINT fk_dat_san_nguoi_dung FOREIGN KEY (nguoi_dung_id) 
        REFERENCES nguoi_dung(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Bảng TRUNG GIAN (Chi tiết đơn hàng) - Quan hệ n-n
CREATE TABLE chi_tiet_dat_san (
    dat_san_id INT UNSIGNED NOT NULL,
    san_bong_id INT UNSIGNED NOT NULL,
    ngay_thue DATE NOT NULL,
    gio_bat_dau TIME NOT NULL,
    gio_ket_thuc TIME NOT NULL,
    -- CỐ Ý LƯU LẶP ĐƠN GIÁ: Để chốt giá tại thời điểm đặt, tránh việc giá sân thay đổi làm sai lệch hóa đơn cũ
    don_gia DECIMAL(12, 0) NOT NULL CHECK (don_gia >= 0),
    thanh_tien DECIMAL(12, 0) NOT NULL CHECK (thanh_tien >= 0),
    -- KHÓA CHÍNH GHÉP (Composite Primary Key)
    PRIMARY KEY (dat_san_id, san_bong_id, ngay_thue, gio_bat_dau),
    -- ON DELETE CASCADE: Xóa đơn hoặc xóa sân thì chi tiết cũng biến mất
    CONSTRAINT fk_ctds_dat_san FOREIGN KEY (dat_san_id) 
        REFERENCES dat_san(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ctds_san_bong FOREIGN KEY (san_bong_id) 
        REFERENCES san_bong(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Bảng LIÊN HỆ
CREATE TABLE lien_he (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    noi_dung TEXT NOT NULL,
    ngay_gui DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- BỔ SUNG THÀNH VIÊN VÀ BẢNG RIÊNG CÁ NHÂN (MSSV: 3120224060 - Hoàng)
-- =========================================================

-- 1. Bảng THÀNH VIÊN nhóm
CREATE TABLE IF NOT EXISTS thanh_vien (
    mssv VARCHAR(20) PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    nguoi_dung_id INT UNSIGNED DEFAULT NULL,
    CONSTRAINT fk_thanh_vien_nguoi_dung FOREIGN KEY (nguoi_dung_id) 
        REFERENCES nguoi_dung(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 2. Bảng riêng cá nhân 1: Lưu bút / Góp ý (tv_luu_but)
CREATE TABLE IF NOT EXISTS tv_luu_but (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_nguoi_gui VARCHAR(100) NOT NULL,
    noi_dung TEXT NOT NULL,
    ngay_gui DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_luu_but_thanh_vien FOREIGN KEY (mssv) 
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. Bảng riêng cá nhân 2: Dự án cá nhân (tv_du_an)
CREATE TABLE IF NOT EXISTS tv_du_an (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ten_du_an VARCHAR(150) NOT NULL,
    mo_ta TEXT NULL,
    link_demo VARCHAR(255) NULL,
    CONSTRAINT fk_tv_du_an_thanh_vien FOREIGN KEY (mssv) 
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
-- Bảng riêng cá nhân 3: Đếm lượt xem theo ngày (tv_luot_xem)
CREATE TABLE IF NOT EXISTS tv_luot_xem (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(20) NOT NULL,
    ngay_xem DATE NOT NULL,
    luot_xem INT UNSIGNED DEFAULT 1,
    UNIQUE KEY idx_mssv_ngay (mssv, ngay_xem),
    CONSTRAINT fk_tv_luot_xem_thanh_vien FOREIGN KEY (mssv) 
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;