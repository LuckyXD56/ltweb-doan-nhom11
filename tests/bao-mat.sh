#!/usr/bin/env bash
# tests/bao-mat.sh — Chạy kiểm thử bảo mật 14 ca bằng curl (Chương 7)
BASE_URL="http://localhost:8000"

echo "=== CA 1: Kiểm tra Header bảo mật và Cookie ==="
curl -s -I "$BASE_URL/index.php" | grep -Ei "content-security-policy|x-content-type-options|referrer-policy|set-cookie|x-powered-by"

echo -e "\n=== CA 2: POST form không có mã CSRF ==="
curl -s -o /dev/null -w "HTTP Code: %{http_code}\n" -X POST "$BASE_URL/dang-nhap.php" -d "email=test@test.com&password=123"

echo -e "\n=== CA 3: POST API giỏ hàng không có CSRF token ==="
curl -s -w "\nHTTP Code: %{http_code}\n" -X POST "$BASE_URL/api/gio-hang.php" \
  -H "Content-Type: application/json" \
  -d '{"action":"them","id":"san-a1","so_luong":1}'

echo -e "\n=== CA 4: Thao tác GET với xoa-san-pham.php và dang-xuat.php ==="
curl -s -o /dev/null -w "xoa-san-pham GET Code: %{http_code}\n" "$BASE_URL/xoa-san-pham.php?id=1"
curl -s -o /dev/null -w "dang-xuat GET Code: %{http_code}\n" "$BASE_URL/dang-xuat.php"

echo -e "\n=== CA 5: Session Fixation test ==="
curl -s -I -b "PHPSESSID=attacker_fixed_session" "$BASE_URL/index.php" | grep -i "set-cookie"

echo -e "\n=== CA 8: Tham số dạng mảng ==="
curl -s -o /dev/null -w "Array param Code: %{http_code}\n" "$BASE_URL/danh-sach.php?q[]=ao&min_gia[]=100"

echo -e "\n=== CA 9: Kiểm tra .env trong Git ==="
echo "git ls-files .env:"
git ls-files .env
echo "git log --all --oneline -- .env:"
git log --all --oneline -- .env

echo -e "\nKiểm thử hoàn tất!"
