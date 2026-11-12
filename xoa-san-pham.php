<?php
/**
 * xoa-san-pham.php â€” XÃ³a sÃ¢n bÃ³ng (chá»‰ nháº­n POST vÃ  yÃªu cáº§u quyá» n quan_tri)
 */

declare(strict_types=1);

require __DIR__ . '/inc/config.php';

// Kiá»ƒm tra phÆ°Æ¡ng thá»©c: chá»‰ cho phÃ©p POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>405 Method Not Allowed</title></head><body><h1>405 Method Not Allowed</h1><p>Thao tÃ¡c xÃ³a sáº£n pháº©m / sÃ¢n bÃ³ng chá»‰ Ä‘Æ°á»£c thá»±c hiá»‡n qua phÆ°Æ¡ng thá»©c POST.</p><p><a href="quan-tri.php">Quay láº¡i</a></p></body></html>';
    exit;
}

// Kiá»ƒm tra quyá» n quáº£n trá»‹ (ngÆ°á» i dÃ¹ng thÆ°á» ng sáº½ nháº­n 403 vÃ  ghi log cam_truy_cap)
yeuCauQuyen('quan_tri');

// MÃ£ CSRF Ä‘Ã£ Ä‘Æ°á»£c kiá»ƒm tra táº­p trung á»Ÿ inc/config.php
$idSan = (int) ($_POST['id'] ?? $_POST['id_san'] ?? 0);
if ($idSan > 0) {
    $stmt = db()->prepare('DELETE FROM san_bong WHERE id = ?');
    $stmt->execute([$idSan]);
    datFlash('thanh-cong', "Ä Ã£ xÃ³a sÃ¢n bÃ³ng #$idSan thÃ nh cÃ´ng.");
} else {
    datFlash('loi', 'MÃ£ sÃ¢n khÃ´ng há»£p lá»‡.');
}

chuyenHuong('quan-tri.php');
