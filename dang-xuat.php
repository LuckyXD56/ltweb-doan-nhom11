<?php
/**
 * dang-xuat.php â€” chá»‰ nháº­n POST tá»« nÃºt "Ä Äƒng xuáº¥t" (form cÃ³ mÃ£ CSRF)
 */

declare(strict_types=1);

require __DIR__ . '/inc/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>405 Method Not Allowed</title></head><body><h1>405 Method Not Allowed</h1><p>Ä Äƒng xuáº¥t chá»‰ Ä‘Æ°á»£c thá»±c hiá»‡n thÃ´ng qua phÆ°Æ¡ng thá»©c POST cÃ³ mÃ£ báº£o vá»‡ CSRF.</p><p><a href="index.php">Vá»  trang chá»§</a></p></body></html>';
    exit;
}

dangXuat();
header('Location: index.php');
exit;
