<?php
/**
 * 500.php — Trang báo lỗi máy chủ nội bộ (không truy vấn CSDL)
 */
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 — Lỗi máy chủ nội bộ | Sân Bóng Thắng Lợi</title>
  <link rel="stylesheet" href="css/chung.css">
  <link rel="stylesheet" href="css/thanh-phan.css">
</head>
<body>
  <main class="khu-vuc-chinh trang-loi-500">
    <h1>500 — Lỗi máy chủ nội bộ</h1>
    <p>Hệ thống đang gặp sự cố kỹ thuật hoặc đang được bảo trì. Chúng tôi xin lỗi vì sự bất tiện này.</p>
    <p class="bieu-tuong-loi">⚠️</p>
    <p class="hang-nut-loi">
      <a class="nut nut--chinh" href="index.php">Về trang chủ</a>
    </p>
  </main>
</body>
</html>
