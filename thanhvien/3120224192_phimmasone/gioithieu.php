<?php
/**
 * gioithieu.php — Trang cá nhân Phimmasone Khamphouvanh
 */
session_start();

// ============================================================
// 1. TODOLIST
// ============================================================
if (!isset($_SESSION['todo_phim'])) {
    $_SESSION['todo_phim'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['viec_moi'])) {
    $viecMoi = trim((string) $_POST['viec_moi']);
    if ($viecMoi !== '') {
        $_SESSION['todo_phim'][] = $viecMoi;
    }
    header('Location: gioithieu.php');
    exit;
}

if (isset($_GET['xoa_viec'])) {
    $viTri = (int) $_GET['xoa_viec'];
    if (isset($_SESSION['todo_phim'][$viTri])) {
        unset($_SESSION['todo_phim'][$viTri]);
        $_SESSION['todo_phim'] = array_values($_SESSION['todo_phim']);
    }
    header('Location: gioithieu.php');
    exit;
}

if (isset($_GET['xoa_het'])) {
    $_SESSION['todo_phim'] = [];
    header('Location: gioithieu.php');
    exit;
}

// ============================================================
// 2. BMI
// ============================================================
$bmi = null;
$phanLoai = '';
$chieuCao = '';
$canNang = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chieucao'], $_POST['cannang'])) {
    $chieuCao = (float) $_POST['chieucao'];
    $canNang  = (float) $_POST['cannang'];

    if ($chieuCao > 0 && $canNang > 0) {
        $chieuCaoM = $chieuCao / 100;
        $bmi = round($canNang / ($chieuCaoM * $chieuCaoM), 1);

        if ($bmi < 18.5)      $phanLoai = 'Gầy (thiếu cân)';
        elseif ($bmi < 23)    $phanLoai = 'Bình thường';
        elseif ($bmi < 25)    $phanLoai = 'Thừa cân';
        elseif ($bmi < 30)    $phanLoai = 'Béo phì độ I';
        else                  $phanLoai = 'Béo phì độ II';
    }
}

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giới thiệu bản thân - Phimmasone Khamphouvanh</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang">

  <nav class="trang__nav">
    <ul class="menu__danh-sach">
      <li class="menu__muc"><a class="menu__lien-ket" href="../../index.php">Trang chủ</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../danh-sach.php">Danh sách sân</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../chi-tiet.php?id=A1">Chi tiết sân</a></li>
      <li class="menu__muc"><a class="menu__lien-ket active" href="../../thanh-vien.php">Giới thiệu nhóm</a></li>
      <li class="menu__muc"><a class="menu__lien-ket" href="../../lien-he.php">Liên hệ / Đặt sân</a></li>
    </ul>
  </nav>

  <main class="trang__main">

    <h1 class="tieu-de-trang">Giới thiệu thành viên Nhóm 11</h1>

    <!-- Thanh công cụ -->
    <div class="thanh-cong-cu">
      <button type="button" id="nut-doi-giao-dien" class="nut-cong-cu"
              aria-pressed="false">🌙 Chế độ tối</button>
      <button type="button" id="nut-sao-chep-email" class="nut-cong-cu"
              aria-label="Sao chép địa chỉ email">📧 Sao chép email</button>
      <span id="thong-bao-sao-chep" class="thong-bao-copy" role="status" aria-live="polite"></span>
    </div>

    <!-- Card hồ sơ -->
    <article class="the-ho-so">
      <div class="the-ho-so__avatar">
        <img src="../../images/phimmasone.jpg" alt="Ảnh chân dung của Phimmasone Khamphouvanh" class="the-ho-so__anh">
      </div>
      <div class="the-ho-so__thong-tin">
        <h2 class="the-ho-so__ten">Phimmasone Khamphouvanh</h2>
        <p class="the-ho-so__dong"><strong>Vai trò:</strong> Trưởng nhóm</p>
        <p class="the-ho-so__dong"><strong>Mã sinh viên:</strong> 3120224192</p>
        <p class="the-ho-so__dong">
          <strong>Email:</strong>
          <span id="email-ca-nhan">phimmasone@sv.ued.udn.vn</span>
        </p>
        <p class="the-ho-so__dong"><strong>Nhiệm vụ:</strong> Phụ trách nội dung HTML và điều phối nhóm.</p>
      </div>
    </article>

    <!-- 2 cột: Giới thiệu + Kỹ năng -->
    <div class="luoi-hai-cot">
      <article class="the-nho">
        <h3 class="the-nho__tieu-de">Giới thiệu bản thân</h3>
        <p class="the-nho__doan">Xin chào, tôi là Phimmasone Khamphouvanh, sinh viên Khoa Toán – Tin,
          Trường Đại học Sư phạm – Đại học Đà Nẵng. Tôi là trưởng nhóm 11, phụ trách điều phối công việc,
          tổng hợp nội dung HTML và hỗ trợ các thành viên trong nhóm hoàn thành đồ án.</p>
      </article>

      <article class="the-nho">
        <h3 class="the-nho__tieu-de">Kỹ năng</h3>
        <div class="danh-sach-nhan">
          <span class="nhan">HTML5 Semantic</span>
          <span class="nhan">CSS3 Responsive</span>
          <span class="nhan">JavaScript</span>
          <span class="nhan">Git &amp; GitHub</span>
          <span class="nhan">Quản lý nhóm</span>
        </div>
      </article>
    </div>

    <!-- TodoList -->
    <article class="the-nho">
      <h3 class="the-nho__tieu-de">📋 Danh sách công việc (TodoList)</h3>

      <form method="post" action="gioithieu.php" style="display:flex; gap:0.5rem; margin-bottom:1rem;">
        <input type="text" name="viec_moi" placeholder="Nhập việc cần làm..." required>
        <button class="nut nut--chinh" type="submit">Thêm</button>
      </form>

      <?php if (count($_SESSION['todo_phim']) === 0): ?>
        <p style="color: var(--mau-chu-nhat); font-style: italic;">Chưa có công việc nào.</p>
      <?php else: ?>
        <?php foreach ($_SESSION['todo_phim'] as $i => $viec): ?>
          <div class="viec-item">
            <span>📌 <?= e($viec) ?></span>
            <a class="viec-item__xoa" href="gioithieu.php?xoa_viec=<?= $i ?>"
               onclick="return confirm('Xoá việc này?');">✕</a>
          </div>
        <?php endforeach; ?>
        <p style="text-align:right; margin-top:0.5rem;">
          <a class="viec-item__xoa" href="gioithieu.php?xoa_het=1"
             onclick="return confirm('Xoá toàn bộ danh sách?');">Xoá hết</a>
        </p>
      <?php endif; ?>
    </article>

    <!-- BMI -->
    <article class="the-nho">
      <h3 class="the-nho__tieu-de">💪 Tính chỉ số BMI</h3>

      <form method="post" action="gioithieu.php" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:flex-end;">
        <div style="flex:1; min-width:140px;">
          <label style="display:block; font-weight:600; margin-bottom:0.25rem;">Chiều cao (cm):</label>
          <input type="number" name="chieucao" step="0.1" min="50" max="250"
                 value="<?= e($chieuCao) ?>" required style="margin-bottom:0;">
        </div>
        <div style="flex:1; min-width:140px;">
          <label style="display:block; font-weight:600; margin-bottom:0.25rem;">Cân nặng (kg):</label>
          <input type="number" name="cannang" step="0.1" min="10" max="300"
                 value="<?= e($canNang) ?>" required style="margin-bottom:0;">
        </div>
        <button class="nut nut--chinh" type="submit">Tính BMI</button>
      </form>

      <?php if ($bmi !== null): ?>
        <div class="ket-qua-bmi">
          <p style="margin:0;">
            <strong>BMI của bạn:</strong>
            <span class="ket-qua-bmi__so"><?= $bmi ?></span>
          </p>
          <p style="margin:0.5rem 0 0 0;">
            <strong>Phân loại:</strong> <?= e($phanLoai) ?>
          </p>
        </div>
      <?php endif; ?>
    </article>

  </main>

  <footer class="trang__footer">
    <p>&copy; 2026 Phimmasone Khamphouvanh – Nhóm 11, Khoa Toán Tin, ĐH Sư phạm Đà Nẵng.</p>
    <p><a href="../../thanh-vien.php">← Về danh sách thành viên</a></p>
  </footer>

  <script>
  (function () {
    'use strict';

    var nutDoiGiaoDien = document.getElementById('nut-doi-giao-dien');
    var KHOA_LUU = 'phim-giao-dien';

    function apDungGiaoDien(trangThai) {
      var laToi = trangThai === 'toi';
      document.body.classList.toggle('giao-dien-toi', laToi);
      if (nutDoiGiaoDien) {
        nutDoiGiaoDien.textContent = laToi ? '☀️ Chế độ sáng' : '🌙 Chế độ tối';
        nutDoiGiaoDien.setAttribute('aria-pressed', String(laToi));
      }
    }

    if (nutDoiGiaoDien) {
      var trangThaiBanDau = localStorage.getItem(KHOA_LUU) || 'sang';
      apDungGiaoDien(trangThaiBanDau);
      nutDoiGiaoDien.addEventListener('click', function () {
        var dangToi = document.body.classList.contains('giao-dien-toi');
        var trangThaiMoi = dangToi ? 'sang' : 'toi';
        localStorage.setItem(KHOA_LUU, trangThaiMoi);
        apDungGiaoDien(trangThaiMoi);
      });
    }

    var nutSaoChep = document.getElementById('nut-sao-chep-email');
    var emailEl = document.getElementById('email-ca-nhan');
    var thongBao = document.getElementById('thong-bao-sao-chep');

    if (nutSaoChep && emailEl && thongBao) {
      nutSaoChep.addEventListener('click', async function () {
        var email = emailEl.textContent.trim();
        try {
          await navigator.clipboard.writeText(email);
          thongBao.textContent = '✅ Đã sao chép: ' + email;
          thongBao.classList.add('hien');
          setTimeout(function () {
            thongBao.textContent = '';
            thongBao.classList.remove('hien');
          }, 2000);
        } catch (loi) {
          thongBao.textContent = '❌ Không sao chép được.';
          thongBao.classList.add('hien');
          setTimeout(function () {
            thongBao.textContent = '';
            thongBao.classList.remove('hien');
          }, 2500);
        }
      });
    }
  })();
  </script>

</body>
</html>