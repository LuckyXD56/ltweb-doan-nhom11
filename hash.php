<?php
$hash = password_hash('Admin@123', PASSWORD_DEFAULT);
echo "Chuỗi hash: " . $hash . "<br>";
echo "Kiểm tra: " . (password_verify('Admin@123', $hash) ? "ĐÚNG" : "SAI");
?>