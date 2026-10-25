<?php
namespace App\Data;

use PDO;

class KhoLienHe
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function them(string $hoTen, string $email, string $noiDung): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO lien_he (ho_ten, email, noi_dung) VALUES (?, ?, ?)");
        return $stmt->execute([$hoTen, $email, $noiDung]);
    }

    public function tatCa(): array
    {
        return $this->pdo->query("SELECT * FROM lien_he ORDER BY id DESC")->fetchAll();
    }
}