<?php
require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/ham.php';
 = '../../';
 = 'Trang cá nhân Phimmasone';
 = 'gioi-thieu';

// --- 1. DANH SÁCH VIỆC (Lưu Session) ---
if (!isset(['todo_phim'])) ['todo_phim'] = [];
if (['REQUEST_METHOD'] === 'POST' && isset(['viec_moi'])) {
     = e(['viec_moi']);
    if ( !== '') ['todo_phim'][] = ;
    header("Location: gioithieu.php");
    exit;
}

// --- 2. TÍNH CHỈ SỐ BMI (Xử lý POST) ---
 = null;
if (['REQUEST_METHOD'] === 'POST' && isset(['chieucao'], ['cannang'])) {
     = (float)['chieucao'] / 100;
     = (float)['cannang'];
    if ( > 0 &&  > 0)  = round( / ( * ), 1);
}

require __DIR__ . '/../../inc/header.php';
?>

<div style="max-width: 800px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
    <h3 style="color: #2e7d32; border-bottom: 2px solid #4caf50; padding-bottom: 5px;">✅ 1. Danh sách công việc (Todo List - Session)</h3>
    <form method="post" style="margin-bottom: 15px; display: flex; gap: 10px;">
        <input type="text" name="viec_moi" placeholder="Nhập việc cần làm..." required style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        <button class="nut nut--chinh" type="submit">Thêm</button>
    </form>
    <ul style="list-style-type: none; padding: 0;">
        <?php foreach(['todo_phim'] as ): ?>
            <li style="background: #f9f9f9; padding: 10px; margin-bottom: 5px; border-left: 4px solid #4caf50;">📝 <?= e() ?></li>
        <?php endforeach; ?>
        <?php if(empty(['todo_phim'])): ?>
            <li style="color: #888; font-style: italic;">Chưa có công việc nào.</li>
        <?php endif; ?>
    </ul>

    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

    <h3 style="color: #1565c0; border-bottom: 2px solid #2196f3; padding-bottom: 5px;">⚖️ 2. Máy tính chỉ số BMI (Xử lý Form POST)</h3>
    <form method="post" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end;">
        <div>
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Chiều cao (cm):</label>
            <input type="number" name="chieucao" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Cân nặng (kg):</label>
            <input type="number" name="cannang" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <button class="nut nut--chinh" type="submit" style="height: 35px;">Tính BMI</button>
    </form>
    <?php if ( !== null): ?>
        <div style="margin-top: 15px; padding: 15px; background: #e3f2fd; border-radius: 4px; color: #0d47a1; font-weight: bold;">
            👉 Chỉ số BMI của bạn là: <span style="font-size: 1.2em;"><?=  ?></span>
        </div>
    <?php endif; ?>
</div>
<nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="../../index.html">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="../../danh-sach.html">Danh sách sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="../../chi-tiet.html">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket active" href="../../gioi-thieu.html">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="../../lien-he.html">Liên hệ / Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main profile-main">
        <h2 class="chu-canh-giua mb-1">Giới thiệu thành viên Nhóm 11</h2>

        <section class="profile-card">
            <div class="profile-card__img-container">
                <img src="../../images/phimmasone.jpg" alt="Ảnh chân dung của Phimmasone Khamphouvanh" class="profile-card__img" width="170" height="170">
            </div>
            
            <div class="profile-card__info">
                <h3 class="profile-card__name">Phimmasone Khamphouvanh</h3>
                <p class="profile-card__role"><strong>Vai trò:</strong> Thành viên nhóm</p>
                <p class="profile-card__id"><strong>Mã sinh viên:</strong> 3120224192</p>
                <p><strong>Nhiệm vụ:</strong> Phụ trách nội dung HTML và trang giới thiệu cá nhân.</p>
                
                </section>

        <section class="info-skills-grid">
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">Thông tin cá nhân</h4>
                <ul>
                    <li><strong>Họ và tên:</strong> Phimmasone Khamphouvanh</li>
                    <li><strong>MSSV:</strong> 3120224192</li>
                    <li><strong>Khoa:</strong> Toán - Tin</li>
                    <li><strong>Chuyên ngành:</strong> Công nghệ thông tin</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h4 class="mt-0 text-primary mb-0-5">Kỹ năng</h4>
                <label for="skill-search" style="display:block; font-weight: bold; margin-bottom: 5px;">Tìm kiếm kỹ năng:</label>
                <input type="text" id="skill-search" placeholder="Ví dụ: HTML, CSS, Responsive..." class="bieu-mau__o-nhap" style="margin-bottom:15px; width:100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                <ul class="ky-nang-list" id="skill-list">
                    <li>HTML5 Semantic</li>
                    <li>CSS3 Responsive</li>
                    <li>JavaScript</li>
                    <li>DevTools</li>
                    <li>Git & GitHub</li>
                </ul>
            </div>
        </section>

        <section class="hobbies-section">
            <h3 class="text-primary mt-0">Sở thích và mục tiêu</h3>
            <p class="text-secondary">Phimmasone yêu thích bóng đá, công nghệ và thiết kế website. Mục tiêu là nâng cao kỹ năng lập trình web và hoàn thành xuất sắc dự án website đặt sân bóng của nhóm.</p>
        </section>

        <section class="profile-schedule">
            <h3 class="chu-canh-giua mb-1 text-primary">Thời khóa biểu tuần</h3> <div style="text-align: center; margin-bottom: 15px;"><button id="btn-toggle-schedule" aria-expanded="true" class="nut nut--phu" style="padding: 4px 12px; font-size: 14px;">Thu gọn / Mở rộng</button></div>
            
            <div class="bang-cuon-ngang">
                                <div id="schedule-container" style="overflow-x: auto;">
                <div class="bang-chua"><table class="bang-du-lieu bang-thoi-khoa-bieu" style="min-width: 800px; font-size: 0.85rem; border-collapse: collapse;">
                    <caption>Lịch học và hoạt động trong tuần của Phimmasone Khamphouvanh</caption>
                    <thead>
                        <tr style="background-color: #059669; color: white;">
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 5%; text-align: center;">Tiết</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 2</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 3</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 4</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 5</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 6</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Thứ 7</th>
                            <th scope="col" style="border: 1px solid #ccc; padding: 4px; width: 13%;">Chủ nhật</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: left; vertical-align: top;">
                        <!-- Tiết 1 -->
                        <tr>
                            <th scope="row" style="border: 1px solid #ccc; text-align: center;">1</th>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31241283 - 24-0102<br><strong>Hệ quản trị cơ sở dữ liệu</strong><br>Phòng: B3-402</td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231398 - 24-0101<br><strong>Lập trình mạng</strong><br>Phòng: B3-303</td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231755 - 24-0102<br><strong>Thiết kế và lập trình web</strong><br>Phòng: B3-303</td>
                            <td style="border: 1px solid #ccc;"></td>
                        </tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">2</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">3</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <!-- Tiết 4 -->
                        <tr>
                            <th scope="row" style="border: 1px solid #ccc; text-align: center;">4</th>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">21221904 - 24-0315<br><strong>Lịch sử Đảng Cộng sản Việt Nam</strong><br>Phòng: A6-502</td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231330 - 24-0103<br><strong>Khai phá dữ liệu</strong><br>Phòng: A5-209</td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                        </tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">5</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">6</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <!-- Break -->
                        <tr style="height: 10px; background-color: #f1f5f9;"><td colspan="8" style="border: 1px solid #ccc;"></td></tr>
                        <!-- Tiết 7 -->
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">7</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">8</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">9</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <!-- Tiết 10 -->
                        <tr>
                            <th scope="row" style="border: 1px solid #ccc; text-align: center;">10</th>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31231016 - 24-0103<br><strong>Công nghệ phần mềm</strong><br>Phòng: B3-304</td>
                            <td style="border: 1px solid #ccc;"></td>
                            <td rowspan="3" style="border: 1px solid #ccc; padding: 4px;">31221010 - 24-0103<br><strong>An toàn thông tin</strong><br>Phòng: B3-506</td>
                            <td style="border: 1px solid #ccc;"></td>
                        </tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">11</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                        <tr><th scope="row" style="border: 1px solid #ccc; text-align: center;">12</th><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td><td style="border: 1px solid #ccc;"></td></tr>
                    </tbody>
                </table></div>
                </div>
            </div>
        </section>
        
        <p class="chu-canh-giua mt-1">
            <a href="../../gioi-thieu.html" class="nut nut--phu">← Quay lại danh sách nhóm</a>
        </p>
    </main>
<?php require __DIR__ . '/../../inc/footer.php'; ?>