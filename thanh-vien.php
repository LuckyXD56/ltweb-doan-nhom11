<?php
/**
 * thanh-vien.php — Trang hub giới thiệu nhóm
 */
$tieuDeTrang = 'Thành viên nhóm';
require __DIR__ . '/inc/header.php';

$thanhVien = [
    ['ten' => 'Phimmasone Khamphouvanh', 'mssv' => '3120224192', 'vaiTro' => 'Trưởng nhóm', 'thuMuc' => '3120224192_phimmasone', 'noiBat' => true],
    ['ten' => 'Nguyễn Quý Minh Hoàng',   'mssv' => '3120224060', 'vaiTro' => 'Thành viên',  'thuMuc' => '3120224060_hoang',      'noiBat' => false],
    ['ten' => 'Lê Nguyễn Gia Nhân',      'mssv' => '3120224105', 'vaiTro' => 'Thành viên',  'thuMuc' => '3120224105_nhan',       'noiBat' => false],
    ['ten' => 'Silaphet Thit',           'mssv' => '3120224188', 'vaiTro' => 'Thành viên',  'thuMuc' => '3120224188_silaphet',   'noiBat' => false],
    ['ten' => 'Trương Nguyễn Ngọc Phú',  'mssv' => '3120224111', 'vaiTro' => 'Thành viên',  'thuMuc' => '3120224111_phu',        'noiBat' => false],
];
?>

<main class="khu-vuc-chinh">
  <h1>Thành viên nhóm thực hiện</h1>
  <p class="gioi-thieu">Đồ án học phần <strong>Thiết kế và Lập trình Web</strong> – Website đặt sân
    bóng đá trực tuyến “Sân Bóng Thắng Lợi”, thực hiện bởi Nhóm 11 – Khoa Toán Tin,
    Trường Đại học Sư phạm Đà Nẵng.</p>

  <h2>Danh sách thành viên</h2>
  <ul class="danh-sach-the">
    <?php foreach ($thanhVien as $tv): ?>
      <li class="the<?= $tv['noiBat'] ? ' the--noi-bat' : '' ?>">
        <strong><?= htmlspecialchars($tv['ten']) ?></strong><br>
        MSSV: <?= htmlspecialchars($tv['mssv']) ?> · <?= htmlspecialchars($tv['vaiTro']) ?><br>
        <a class="nut nut--chinh" href="thanhvien/<?= htmlspecialchars($tv['thuMuc']) ?>/gioithieu.html">Xem trang cá nhân</a>
      </li>
    <?php endforeach; ?>
  </ul>

  <h2>Quy ước nộp trang cá nhân</h2>
  <p>Mỗi thành viên đặt file <code>gioithieu.html</code> trong thư mục
    <code>thanhvien/&lt;MSV&gt;_&lt;tên&gt;/</code>, kèm <code>style.css</code>,
    <code>avatar.jpg</code> và thư mục <code>kiemtra/</code>.</p>
</main>

<?php require __DIR__ . '/inc/footer.php'; ?>