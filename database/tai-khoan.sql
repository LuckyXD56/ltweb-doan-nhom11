-- database/tai-khoan.sql â€” cháº¡y Má»˜T láº§n báº±ng root (phpMyAdmin â†’ SQL hoáº·c dÃ²ng lá»‡nh)
-- á»¨ng dá»¥ng káº¿t ná»‘i báº±ng nhom11_app: chá»‰ Ä‘á» c, thÃªm, sá»­a, xÃ³a Dá»® LIá»†U (nguyÃªn táº¯c quyá» n tá»‘i thiá»ƒu)

CREATE USER IF NOT EXISTS 'nhom11_app'@'localhost'
  IDENTIFIED BY 'thay-bang-mat-khau-dai-ngau-nhien';

GRANT SELECT, INSERT, UPDATE, DELETE
  ON ltweb_nhom11.* TO 'nhom11_app'@'localhost';

FLUSH PRIVILEGES;
