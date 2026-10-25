<?php
namespace App\Data;
use PDO;
use Exception;

class KhoDatSan
{
    public function datSan(int $nguoiDungId, int $sanBongId, string $ngayDat, string $gioBatDau, string $gioKetThuc, int $tongTien): string|bool
    {
        $pdo = \db();
        
        try {
            $pdo->beginTransaction();

            // 1. Kiểm tra trùng lịch trong bảng chi_tiet_dat_san
            $sqlCheck = "SELECT ct.san_bong_id FROM chi_tiet_dat_san ct
                         JOIN dat_san d ON ct.dat_san_id = d.id
                         WHERE ct.san_bong_id = ? AND ct.ngay_thue = ? AND d.trang_thai != 'da_huy'
                         AND (ct.gio_bat_dau < ? AND ct.gio_ket_thuc > ?)
                         FOR UPDATE";
            $stmt = $pdo->prepare($sqlCheck);
            $stmt->execute([$sanBongId, $ngayDat, $gioKetThuc, $gioBatDau]);
            if ($stmt->fetch()) {
                throw new Exception("Khung giờ này đã có người đặt.");
            }

            // 2. Insert vào bảng dat_san (Hóa đơn tổng)
            $stmtHD = $pdo->prepare("INSERT INTO dat_san (nguoi_dung_id, tong_tien, trang_thai) VALUES (?, ?, 'cho_xac_nhan')");
            $stmtHD->execute([$nguoiDungId, $tongTien]);
            $datSanId = $pdo->lastInsertId();

            // 3. Insert vào bảng chi_tiet_dat_san (Chi tiết)
            // Lấy giá thuê hiện tại của sân để chốt giá
            $stmtGia = $pdo->prepare("SELECT gia_thue FROM san_bong WHERE id = ?");
            $stmtGia->execute([$sanBongId]);
            $donGia = $stmtGia->fetchColumn();

            $stmtCT = $pdo->prepare("INSERT INTO chi_tiet_dat_san (dat_san_id, san_bong_id, ngay_thue, gio_bat_dau, gio_ket_thuc, don_gia, thanh_tien) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtCT->execute([$datSanId, $sanBongId, $ngayDat, $gioBatDau, $gioKetThuc, $donGia, $tongTien]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }
}