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

-- 3. Thành viên có trang cá nhân
CREATE TABLE thanh_vien (
    mssv VARCHAR(10) NOT NULL PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    nguoi_dung_id INT UNSIGNED NULL,
    CONSTRAINT fk_thanh_vien_nguoi_dung FOREIGN KEY (nguoi_dung_id)
        REFERENCES nguoi_dung(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. Bảng THỰC THỂ CHÍNH (Sân bóng)
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

-- 5. Sổ lưu bút trang cá nhân (Chuyển đổi từ tệp JSONL sang bảng CSDL)
CREATE TABLE tv_luu_but (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(10) NOT NULL,
    ho_ten VARCHAR(100) NOT NULL,
    loi_nhan TEXT NOT NULL,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_luu_but_thanh_vien FOREIGN KEY (mssv)
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Lượt xem trang cá nhân theo ngày
CREATE TABLE tv_luot_xem (
    mssv VARCHAR(10) NOT NULL,
    ngay DATE NOT NULL,
    luot_xem INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (mssv, ngay),
    CONSTRAINT fk_tv_luot_xem_thanh_vien FOREIGN KEY (mssv)
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 7. Danh sách công việc cá nhân của Phimmasone (Chuyển đổi từ Session sang CSDL)
CREATE TABLE tv_todo_phim (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(10) NOT NULL,
    tieu_de VARCHAR(255) NOT NULL,
    hoan_thanh TINYINT(1) NOT NULL DEFAULT 0,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_todo_thanh_vien FOREIGN KEY (mssv)
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 8. Lịch sử tính chỉ số BMI của Phimmasone (Lưu vào CSDL)
CREATE TABLE tv_bmi_phim (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mssv VARCHAR(10) NOT NULL,
    chieu_cao DECIMAL(5, 1) NOT NULL,
    can_nang DECIMAL(5, 1) NOT NULL,
    chi_so_bmi DECIMAL(4, 1) NOT NULL,
    phan_loai VARCHAR(50) NOT NULL,
    ngay_tao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tv_bmi_thanh_vien FOREIGN KEY (mssv)
        REFERENCES thanh_vien(mssv) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 6. Bảng ĐƠN HÀNG (Đặt sân)
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

-- 7. Bảng TRUNG GIAN (Chi tiết đơn hàng) - Quan hệ n-n
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

-- 8. Bảng LIÊN HỆ
CREATE TABLE lien_he (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    noi_dung TEXT NOT NULL,
    ngay_gui DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;