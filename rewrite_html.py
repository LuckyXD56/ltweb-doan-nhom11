import os

files = {
    'index.html': '''<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Trang chủ Hệ thống Quản lý sân bóng mini, cung cấp dịch vụ đặt sân bóng cỏ nhân tạo chuyên nghiệp, uy tín và nhanh chóng.">
    <title>Trang chủ | Sân Bóng Thắng Lợi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang">
    <header class="trang__header">
        <h1>Sân Bóng Thắng Lợi</h1>
        <p>Uy tín - Tiện lợi - Sức khỏe là vàng</p>
    </header>

    <nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="danh-sach.html">Danh sách sân bóng</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="chi-tiet.html">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="gioi-thieu.html">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="lien-he.html">Liên hệ & Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main">
        <section class="chu-canh-giua mb-1">
            <h2>Chào mừng đến với hệ thống đặt sân bóng</h2>
            <p class="gioi-han-dong">Chúng tôi cung cấp hệ thống sân bóng cỏ nhân tạo đạt chuẩn quốc tế, giúp bạn có những giây phút thể thao tuyệt vời cùng bạn bè và đồng nghiệp.</p>
            <img src="images/san-bong-chinh.jpg" alt="Toàn cảnh sân bóng cỏ nhân tạo nhìn từ trên cao" width="1200" height="600" loading="lazy" class="hinh-anh-chinh mt-1">
        </section>

        <section>
            <h2 class="chu-canh-giua mt-1">Các dịch vụ nổi bật</h2>
            <div class="luoi-the">
                <article class="the">
                    <img class="the__anh" src="images/dich-vu-1.jpg" alt="Sân bóng 5 người và 7 người" width="400" height="300" loading="lazy">
                    <div class="the__noi-dung">
                        <h3 class="the__tieu-de">Sân 5 và 7 người</h3>
                        <p>Hệ thống 10 sân bóng hiện đại, mặt cỏ chất lượng cao.</p>
                    </div>
                </article>
                <article class="the">
                    <img class="the__anh" src="images/dich-vu-2.jpg" alt="Hệ thống chiếu sáng LED" width="400" height="300" loading="lazy">
                    <div class="the__noi-dung">
                        <h3 class="the__tieu-de">Chiếu sáng chuẩn thi đấu</h3>
                        <p>Hệ thống đèn LED 400W đảm bảo độ sáng ban đêm.</p>
                    </div>
                </article>
                <article class="the">
                    <img class="the__anh" src="images/dich-vu-3.jpg" alt="Cho thuê dụng cụ thi đấu" width="400" height="300" loading="lazy">
                    <div class="the__noi-dung">
                        <h3 class="the__tieu-de">Thuê dụng cụ</h3>
                        <p>Dịch vụ cho thuê bóng, áo bib và trọng tài chuyên nghiệp.</p>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <footer class="trang__footer">
        <p>© 2026 Nhóm 11 — Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng</p>
    </footer>
</body>
</html>''',
    
    'danh-sach.html': '''<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Danh sách các sân bóng mini cỏ nhân tạo hiện có tại hệ thống, phân loại theo sân 5 người và 7 người kèm bảng giá chi tiết.">
    <title>Danh sách sân bóng | Sân Bóng Thắng Lợi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang">
    <header class="trang__header">
        <h1>Sân Bóng Thắng Lợi</h1>
        <p>Uy tín - Tiện lợi - Sức khỏe là vàng</p>
    </header>

    <nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="danh-sach.html">Danh sách sân bóng</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="chi-tiet.html">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="gioi-thieu.html">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="lien-he.html">Liên hệ & Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main">
        <h2 class="chu-canh-giua">Danh sách sân bóng</h2>
        <p class="chu-canh-giua">Hệ thống chúng tôi hiện cung cấp các loại sân đa dạng, đáp ứng mọi nhu cầu.</p>

        <div class="luoi-the">
            <article class="the">
                <img class="the__anh" src="images/san-a1.jpg" alt="Sân A1" width="400" height="300" loading="lazy">
                <div class="the__noi-dung">
                    <h3 class="the__tieu-de">Sân A1 (5 người)</h3>
                    <p>Khu vực: Khu A <br> Giá trước 17h: 150k <br> Giá sau 17h: 300k</p>
                    <a href="chi-tiet.html" class="nut">Xem chi tiết</a>
                </div>
            </article>
            <article class="the">
                <img class="the__anh" src="images/san-a2.jpg" alt="Sân A2" width="400" height="300" loading="lazy">
                <div class="the__noi-dung">
                    <h3 class="the__tieu-de">Sân A2 (5 người)</h3>
                    <p>Khu vực: Khu A <br> Giá trước 17h: 150k <br> Giá sau 17h: 300k</p>
                    <a href="chi-tiet.html" class="nut">Xem chi tiết</a>
                </div>
            </article>
            <article class="the">
                <img class="the__anh" src="images/san-b1.jpg" alt="Sân B1" width="400" height="300" loading="lazy">
                <div class="the__noi-dung">
                    <h3 class="the__tieu-de">Sân B1 (7 người)</h3>
                    <p>Khu vực: Khu B <br> Giá trước 17h: 250k <br> Giá sau 17h: 450k</p>
                    <a href="chi-tiet.html" class="nut">Xem chi tiết</a>
                </div>
            </article>
        </div>
    </main>

    <footer class="trang__footer">
        <p>© 2026 Nhóm 11 — Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng</p>
    </footer>
</body>
</html>''',

    'chi-tiet.html': '''<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Chi tiết Sân A1 - Sân bóng mini 5 người với mặt cỏ nhân tạo chất lượng cao, hệ thống chiếu sáng chuẩn.">
    <title>Chi tiết Sân A1 | Sân Bóng Thắng Lợi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang trang--co-aside">
    <header class="trang__header">
        <h1>Sân Bóng Thắng Lợi</h1>
        <p>Uy tín - Tiện lợi - Sức khỏe là vàng</p>
    </header>

    <nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="danh-sach.html">Danh sách sân bóng</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="chi-tiet.html">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="gioi-thieu.html">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="lien-he.html">Liên hệ & Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main">
        <article>
            <h2>Chi tiết Sân A1 (Sân 5 người)</h2>
            <p>Sân A1 là một trong những sân có chất lượng mặt cỏ tốt nhất của hệ thống, thường xuyên được bảo trì và nâng cấp.</p>
            
            <figure class="chi-tiet__hinh-anh">
                <img src="images/san-a1.jpg" alt="Mặt cỏ nhân tạo và khung thành sân A1" width="800" height="400" loading="lazy">
                <figcaption>Khung cảnh mặt cỏ và hệ thống lưới bao quanh Sân A1</figcaption>
            </figure>

            <h3 class="mt-1">Thông số kỹ thuật</h3>
            <table class="bang-du-lieu">
                <caption>Thông số kỹ thuật chi tiết Sân A1</caption>
                <thead>
                    <tr>
                        <th scope="col">Hạng mục</th>
                        <th scope="col">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Kích thước</th>
                        <td>20m x 40m</td>
                    </tr>
                    <tr>
                        <th scope="row">Loại cỏ</th>
                        <td>Cỏ nhân tạo 50mm, đạt chuẩn FIFA Quality</td>
                    </tr>
                    <tr>
                        <th scope="row">Chiếu sáng</th>
                        <td>6 trụ đèn LED 400W</td>
                    </tr>
                </tbody>
            </table>

            <h3 class="mt-1">Video highlight tại Sân A1</h3>
            <div class="chi-tiet__video-bao">
                <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video highlight trận đấu tại sân bóng mini A1" allowfullscreen></iframe>
            </div>
            
        </article>
    </main>

    <aside class="trang__aside">
        <h3>Đặt sân này</h3>
        <p>Sân A1 hiện đang có nhiều khung giờ trống trong tuần này.</p>
        <a href="lien-he.html" class="nut nut--block mb-1">>> Đặt sân ngay <<</a>
        
        <h3>Các sân tương tự</h3>
        <ul class="danh-sach-phu">
            <li><a href="chi-tiet.html">Sân A2 (5 người)</a></li>
            <li><a href="chi-tiet.html">Sân A3 (5 người)</a></li>
        </ul>
    </aside>

    <footer class="trang__footer">
        <p>© 2026 Nhóm 11 — Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng</p>
    </footer>
</body>
</html>''',

    'gioi-thieu.html': '''<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Giới thiệu các thành viên Nhóm 11 - Khoa Toán Tin, những người xây dựng và phát triển hệ thống Quản lý sân bóng mini.">
    <title>Giới thiệu Nhóm 11 | Sân Bóng Thắng Lợi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang">
    <header class="trang__header">
        <h1>Sân Bóng Thắng Lợi</h1>
        <p>Uy tín - Tiện lợi - Sức khỏe là vàng</p>
    </header>

    <nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="danh-sach.html">Danh sách sân bóng</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="chi-tiet.html">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="gioi-thieu.html">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="lien-he.html">Liên hệ & Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main">
        <h2 class="chu-canh-giua">Giới thiệu Nhóm 11</h2>
        <p class="gioi-han-dong chu-canh-giua">Chúng tôi là nhóm sinh viên đến từ Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng. Đồ án này được xây dựng với mục tiêu ứng dụng kiến thức môn Thiết kế và Lập trình web vào thực tế để phục vụ cộng đồng.</p>
        
        <h3 class="chu-canh-giua mt-1">Danh sách thành viên</h3>
        <div class="luoi-the luoi-the--nho">
            <article class="the chu-canh-giua">
                <div class="the__noi-dung">
                    <h4 class="the__tieu-de">Phimmasone Khamphouvanh</h4>
                    <p>Trưởng nhóm</p>
                    <a href="thanhvien/phimmasone/gioithieu.html" class="nut">Trang cá nhân</a>
                </div>
            </article>
            <article class="the chu-canh-giua">
                <div class="the__noi-dung">
                    <h4 class="the__tieu-de">Nguyễn Quý Minh Hoàng</h4>
                    <p>Thành viên</p>
                    <a href="thanhvien/hoang/gioithieu.html" class="nut">Trang cá nhân</a>
                </div>
            </article>
            <article class="the chu-canh-giua">
                <div class="the__noi-dung">
                    <h4 class="the__tieu-de">Lê Nguyễn Gia Nhân</h4>
                    <p>Thành viên</p>
                    <a href="thanhvien/nhan/gioithieu.html" class="nut">Trang cá nhân</a>
                </div>
            </article>
            <article class="the chu-canh-giua">
                <div class="the__noi-dung">
                    <h4 class="the__tieu-de">Silaphet Thit</h4>
                    <p>Thành viên</p>
                    <a href="thanhvien/silaphet/gioithieu.html" class="nut">Trang cá nhân</a>
                </div>
            </article>
            <article class="the chu-canh-giua">
                <div class="the__noi-dung">
                    <h4 class="the__tieu-de">Trương Nguyễn Ngọc Phú</h4>
                    <p>Thành viên</p>
                    <a href="thanhvien/phu/gioithieu.html" class="nut">Trang cá nhân</a>
                </div>
            </article>
        </div>
    </main>

    <footer class="trang__footer">
        <p>© 2026 Nhóm 11 — Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng</p>
    </footer>
</body>
</html>''',

    'lien-he.html': '''<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Liên hệ và đặt sân bóng trực tuyến. Điền thông tin cá nhân và thời gian để hệ thống ghi nhận đơn đặt sân của bạn.">
    <title>Liên hệ & Đặt sân | Sân Bóng Thắng Lợi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="trang">
    <header class="trang__header">
        <h1>Sân Bóng Thắng Lợi</h1>
        <p>Uy tín - Tiện lợi - Sức khỏe là vàng</p>
    </header>

    <nav class="trang__nav">
        <ul class="menu__danh-sach">
            <li class="menu__muc"><a class="menu__lien-ket" href="index.html">Trang chủ</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="danh-sach.html">Danh sách sân bóng</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="chi-tiet.html">Chi tiết sân</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="gioi-thieu.html">Giới thiệu nhóm</a></li>
            <li class="menu__muc"><a class="menu__lien-ket" href="lien-he.html">Liên hệ & Đặt sân</a></li>
        </ul>
    </nav>

    <main class="trang__main gioi-han-form">
        <h2 class="chu-canh-giua">Biểu mẫu Đặt sân</h2>
        <p class="chu-canh-giua">Vui lòng điền đầy đủ thông tin dưới đây để gửi yêu cầu đặt sân. Chúng tôi sẽ liên hệ lại để xác nhận.</p>

        <form action="#" method="post" class="bieu-mau">
            <fieldset>
                <legend>Thông tin khách hàng</legend>
                
                <div class="bieu-mau__nhom">
                    <label class="bieu-mau__nhan" for="hoten">Họ và tên (*):</label>
                    <input class="bieu-mau__o-nhap" type="text" id="hoten" name="hoten" required minlength="3" maxlength="50" placeholder="VD: Nguyễn Văn A">
                </div>
                
                <div class="bieu-mau__nhom">
                    <label class="bieu-mau__nhan" for="sdt">Số điện thoại (*):</label>
                    <input class="bieu-mau__o-nhap" type="tel" id="sdt" name="sdt" required pattern="[0-9]{10}" placeholder="VD: 0901234567">
                </div>
            </fieldset>

            <fieldset>
                <legend>Thông tin đặt sân</legend>

                <div class="bieu-mau__nhom">
                    <label class="bieu-mau__nhan" for="loaisan">Chọn loại sân:</label>
                    <select class="bieu-mau__lua-chon" id="loaisan" name="loaisan">
                        <option value="5nguoi">Sân 5 người</option>
                        <option value="7nguoi">Sân 7 người</option>
                    </select>
                </div>
                
                <div class="bieu-mau__nhom">
                    <label class="bieu-mau__nhan" for="ngaydat">Ngày đặt (*):</label>
                    <input class="bieu-mau__o-nhap" type="date" id="ngaydat" name="ngaydat" required min="2026-09-01" max="2027-12-31">
                </div>
                
                <div class="bieu-mau__nhom">
                    <label class="bieu-mau__nhan" for="giothue">Số giờ thuê (1 - 3 giờ):</label>
                    <input class="bieu-mau__o-nhap" type="number" id="giothue" name="giothue" min="1" max="3" value="1">
                </div>
                
                <div class="bieu-mau__nhom">
                    <label class="bieu-mau__nhan" for="yeucau">Yêu cầu khác:</label>
                    <textarea class="bieu-mau__o-nhap" id="yeucau" name="yeucau" rows="4" placeholder="Cần thuê trọng tài, mua nước..."></textarea>
                </div>
            </fieldset>
            
            <div class="d-flex gap-1 justify-center mt-1">
                <button class="nut" type="submit">Gửi yêu cầu đặt sân</button>
                <button class="nut nut--phu" type="reset">Nhập lại</button>
            </div>
        </form>
    </main>

    <footer class="trang__footer">
        <p>© 2026 Nhóm 11 — Khoa Toán - Tin, Đại học Sư phạm Đà Nẵng</p>
    </footer>
</body>
</html>'''
}

for name, content in files.items():
    with open(name, 'w', encoding='utf-8') as f:
        f.write(content)
