<?php
declare(strict_types=1);

namespace App\Data;

use PDO;

class KhoLienHe
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Thêm một liên hệ mới (kèm ảnh đính kèm nếu có)
     */
    public function them(string $hoTen, string $email, string $noiDung, ?string $hinhAnh = null): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO lien_he (ho_ten, email, noi_dung, hinh_anh) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$hoTen, $email, $noiDung, $hinhAnh]);
    }

    /**
     * Lấy tất cả các liên hệ, mới nhất xếp trước
     */
    public function tatCa(): array
    {
        return $this->pdo->query("SELECT * FROM lien_he ORDER BY id DESC")->fetchAll();
    }

    /**
     * Đếm tổng số liên hệ
     */
    public function dem(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM lien_he")->fetchColumn();
    }

    /**
     * Lấy một trang danh sách liên hệ (hỗ trợ phân trang)
     */
    public function trang(int $soDong, int $boQua): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM lien_he ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->execute([$soDong, $boQua]);
        return $stmt->fetchAll();
    }
}