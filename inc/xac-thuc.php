<?php
/**
 * inc/xac-thuc.php â€” Quáº£n lÃ½ xÃ¡c thá»±c, phÃ¢n quyá» n, phiÃªn vÃ  chá»‘ng Ä‘oÃ¡n máº­t kháº©u
 */

declare(strict_types=1);

if (!function_exists('dangNhap')) {
    function dangNhap(int|string $uid): void
    {
        session_regenerate_id(true);
        $_SESSION['uid'] = (int) $uid;
        $_SESSION['da_dang_nhap'] = true;
        
        $st = db()->prepare('SELECT id, ho_ten, email, vai_tro FROM nguoi_dung WHERE id = ?');
        $st->execute([(int) $uid]);
        $nd = $st->fetch();
        if ($nd) {
            $_SESSION['ho_ten'] = $nd['ho_ten'];
            $_SESSION['email'] = $nd['email'];
            $_SESSION['vai_tro'] = $nd['vai_tro'];
        }
        $_SESSION['hoat_dong'] = time();
    }
}

if (!function_exists('dangXuat')) {
    function dangXuat(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 3600,
                'path'     => $params['path'] ?? '/',
                'domain'   => $params['domain'] ?? '',
                'secure'   => $params['secure'] ?? false,
                'httponly' => $params['httponly'] ?? true,
                'samesite' => $params['samesite'] ?? 'Lax'
            ]);
        }
        session_destroy();
    }
}

if (!function_exists('nguoiDung')) {
    function nguoiDung(): ?array
    {
        if (empty($_SESSION['uid']) || empty($_SESSION['da_dang_nhap'])) {
            return null;
        }
        static $ndHienTai = null;
        if ($ndHienTai === null) {
            $st = db()->prepare('SELECT id, ho_ten, email, vai_tro FROM nguoi_dung WHERE id = ?');
            $st->execute([(int) $_SESSION['uid']]);
            $ndHienTai = $st->fetch() ?: null;
        }
        return $ndHienTai;
    }
}

if (!function_exists('yeuCauDangNhap')) {
    function yeuCauDangNhap(): array
    {
        $nd = nguoiDung();
        if ($nd === null) {
            if (function_exists('datFlash')) {
                datFlash('loi', 'Vui lÃ²ng Ä‘Äƒng nháº­p Ä‘á»ƒ tiáº¿p tá»¥c.');
            }
            header('Location: dang-nhap.php');
            exit;
        }
        return $nd;
    }
}

if (!function_exists('yeuCauQuyen')) {
    function yeuCauQuyen(string $vaiTro): array
    {
        $nd = yeuCauDangNhap();
        if (($nd['vai_tro'] ?? '') !== $vaiTro) {
            if (function_exists('ghiLogBaoMat')) {
                ghiLogBaoMat('cam_truy_cap', ['trang' => $_SERVER['REQUEST_URI'] ?? '']);
            }
            http_response_code(403);
            $file403 = dirname(__DIR__) . '/403.php';
            if (file_exists($file403)) {
                require $file403;
            } else {
                echo '403 Forbidden: Báº¡n khÃ´ng cÃ³ quyá» n truy cáº­p trang nÃ y.';
            }
            exit;
        }
        return $nd;
    }
}

// ChÆ°Æ¡ng 7 â€” chá»‘ng Ä‘oÃ¡n máº­t kháº©u: sá»‘ láº§n sai trong 15 phÃºt
if (!function_exists('soLanSai')) {
    function soLanSai(string $email): int
    {
        $st = db()->prepare('SELECT COUNT(*) FROM dang_nhap_sai WHERE email = ? AND thoi_gian > NOW() - INTERVAL 15 MINUTE');
        $st->execute([$email]);
        return (int) $st->fetchColumn();
    }
}

if (!function_exists('ghiLanSai')) {
    function ghiLanSai(string $email): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
        $st = db()->prepare('INSERT INTO dang_nhap_sai (email, ip) VALUES (?, ?)');
        $st->execute([$email, $ip]);
        if (function_exists('ghiLogBaoMat')) {
            ghiLogBaoMat('dang_nhap_sai', ['email' => $email]);
        }
    }
}
