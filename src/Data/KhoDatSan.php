<?php
namespace App\Data;

use PDO;
use Exception;

class KhoDatSan
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function datSan(int $nguoiDungId, int $sanBongId, string $ngayDat, string $gioBatDau, string $gioKetThuc, int $tongTien): string|bool
    {
        try {
            $this->pdo->beginTransaction();

            $sqlCheck = "SELECT ct.san_bong_id FROM chi_tiet_dat_san ct JOIN dat_san d ON ct.dat_san_id = d.id WHERE ct.san_bong_id = ? AND ct.ngay_thue = ? AND d.trang_thai != 'da_huy' AND (ct.gio_bat_dau < ? AND ct.gio_ket_thuc > ?) FOR UPDATE";
            $stmt = $this->pdo->prepare($sqlCheck);
            $stmt->execute([$sanBongId, $ngayDat, $gioKetThuc, $gioBatDau]);
            if ($stmt->fetch()) throw new Exception("Khung giờ này đã có người đặt.");

            $stmtHD = $this->pdo->prepare("INSERT INTO dat_san (nguoi_dung_id, tong_tien, trang_thai) VALUES (?, ?, 'cho_xac_nhan')");
            $stmtHD->execute([$nguoiDungId, $tongTien]);
            $datSanId = $this->pdo->lastInsertId();

            $stmtGia = $this->pdo->prepare("SELECT gia_thue FROM san_bong WHERE id = ?");
            $stmtGia->execute([$sanBongId]);
            $donGia = $stmtGia->fetchColumn();

            $stmtCT = $this->pdo->prepare("INSERT INTO chi_tiet_dat_san (dat_san_id, san_bong_id, ngay_thue, gio_bat_dau, gio_ket_thuc, don_gia, thanh_tien) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtCT->execute([$datSanId, $sanBongId, $ngayDat, $gioBatDau, $gioKetThuc, $donGia, $tongTien]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return $e->getMessage();
        }
    }
}