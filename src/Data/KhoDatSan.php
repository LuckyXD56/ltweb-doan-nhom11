<?php
/**
 * KhoDatSan — Lớp xử lý giao dịch đặt sân
 */

namespace App\Data;

use PDO;
use Exception;

class KhoDatSan
{
    public function datSan(int $nguoiDungId, string $maSan, string $ngayDat, string $gioBatDau, string $gioKetThuc, int $tongTien): string|bool
    {
        $pdo = \db();
        
        try {
            // 1. Bắt đầu Transaction
            $pdo->beginTransaction();

            // 2. Lấy ID của sân
            $stmt = $pdo->prepare("SELECT id FROM san_bong WHERE ma_san = ?");
            $stmt->execute([$maSan]);
            $sanBongId = $stmt->fetchColumn();
            
            if (!$sanBongId) {
                throw new Exception("Mã sân không tồn tại.");
            }

            // 3. Kiểm tra trùng lịch (Sử dụng FOR UPDATE để lock dòng)
            $sqlCheck = "SELECT id FROM dat_san 
                         WHERE san_bong_id = ? 
                           AND ngay_dat = ? 
                           AND trang_thai != 'huy'
                           AND (gio_bat_dau < ? AND gio_ket_thuc > ?)
                         FOR UPDATE";
            
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->execute([$sanBongId, $ngayDat, $gioKetThuc, $gioBatDau]);

            if ($stmtCheck->fetch()) {
                throw new Exception("Khung giờ này đã có người đặt. Vui lòng chọn giờ khác.");
            }

            // 4. Insert phiếu đặt sân mới
            $sqlInsert = "INSERT INTO dat_san (nguoi_dung_id, san_bong_id, ngay_dat, gio_bat_dau, gio_ket_thuc, tong_tien, trang_thai) 
                          VALUES (?, ?, ?, ?, ?, ?, 'cho_xac_nhan')";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([$nguoiDungId, $sanBongId, $ngayDat, $gioBatDau, $gioKetThuc, $tongTien]);

            // 5. Commit
            $pdo->commit();
            return true;

        } catch (Exception $e) {
            $pdo->rollBack();
            return $e->getMessage();
        }
    }
}