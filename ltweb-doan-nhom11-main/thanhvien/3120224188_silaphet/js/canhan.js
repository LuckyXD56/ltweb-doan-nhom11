/*
 * Tệp JavaScript tạo các tương tác cho trang cá nhân của Silaphet.
 * Chức năng 1: Đồng hồ đếm ngược.
 * Chức năng 2: Slideshow tự chuyển ảnh.
 */

document.addEventListener('DOMContentLoaded', () => {
    // =========================
    // 1. Đồng hồ đếm ngược
    // =========================
    const dongHo = document.getElementById('dong-ho');
    // Đặt ngày đích (ví dụ: ngày thi)
    const ngayThi = new Date('2026-10-31T00:00:00').getTime();

    if (dongHo) {
        setInterval(() => {
            const bayGio = new Date().getTime();
            const khoangCach = ngayThi - bayGio;

            if (khoangCach < 0) {
                dongHo.textContent = "Đã qua ngày thi!";
                return;
            }

            const ngay = Math.floor(khoangCach / (1000 * 60 * 60 * 24));
            const gio = Math.floor((khoangCach % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const phut = Math.floor((khoangCach % (1000 * 60 * 60)) / (1000 * 60));
            const giay = Math.floor((khoangCach % (1000 * 60)) / 1000);

            dongHo.textContent = ${ngay} ngày  giờ  phút  giây;
        }, 1000);
    }

    // =========================
    // 2. Slideshow Ảnh tự động
    // =========================
    const anhSlide = document.getElementById('anh-slide');
    const mangAnh = [
        '../../images/san-a1.jpg',
        '../../images/san-bong-chinh.jpg',
        '../../images/san-b1.jpg'
    ];
    let viTriHienTai = 0;

    if (anhSlide) {
        setInterval(() => {
            viTriHienTai++;
            if (viTriHienTai >= mangAnh.length) {
                viTriHienTai = 0; // Quay lại ảnh đầu tiên
            }
            anhSlide.src = mangAnh[viTriHienTai];
        }, 3000); // 3 giây chuyển 1 lần
    }
});
