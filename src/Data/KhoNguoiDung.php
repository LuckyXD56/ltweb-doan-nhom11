<?php
namespace App\Data;

use PDO;

class KhoNguoiDung
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function timTheoEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
}