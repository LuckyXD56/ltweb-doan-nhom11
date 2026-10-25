/**
 * api-danh-sach.js — Gọi REST API và render danh sách sân
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.bo-loc-san');
    const danhSachUl = document.querySelector('.danh-sach-the');
    const thongBaoP = document.querySelector('.vung-thong-bao');

    if (!form || !danhSachUl) return;

    // Hàm định dạng tiền tệ
    function formatTien(so) {
        return new Intl.NumberFormat('vi-VN').format(so) + 'đ';
    }

    // Hàm render HTML
    function renderDanhSach(danhSach) {
        if (danhSach.length === 0) {
            danhSachUl.innerHTML = `
                <div class="the" style="text-align: center; padding: 2rem; grid-column: 1 / -1;">
                    <p style="font-size: 2rem; margin: 0;">🔍</p>
                    <p><strong>Không tìm thấy sân nào phù hợp.</strong></p>
                </div>`;
            return;
        }

        let html = '';
        danhSach.forEach(san => {
            html += `
                <li class="the">
                    <h3>${san.ten} (${san.loai_san} người)</h3>
                    <p>
                        Khu vực: ${san.khu_vuc}<br>
                        Giá thường: ${formatTien(san.gia_thuong)}/giờ<br>
                        Giá vàng: ${formatTien(san.gia_vang)}/giờ<br>
                        Đánh giá: ${san.danh_gia}/5
                    </p>
                    <a class="nut nut--chinh" href="chi-tiet.php?id=${encodeURIComponent(san.id)}">Xem chi tiết</a>
                </li>
            `;
        });
        danhSachUl.innerHTML = html;
    }

    // Hàm gọi API
    async function taiDuLieu() {
        // Lấy giá trị từ form
        const q = document.getElementById('q').value;
        const dm = document.getElementById('dm').value;
        const sx = document.getElementById('sx').value;

        // Tạo URL API
        const url = new URL('api/san.php', window.location.href);
        if (q) url.searchParams.append('tu_khoa', q);
        if (dm && dm !== 'tat-ca') url.searchParams.append('loai_san', dm);
        if (sx) url.searchParams.append('sap_xep', sx);

        try {
            danhSachUl.style.opacity = '0.5'; // Hiệu ứng đang tải

            const response = await fetch(url);
            const ketQua = await response.json();

            if (ketQua.status === 'success') {
                renderDanhSach(ketQua.data);
                thongBaoP.innerHTML = `Tìm thấy <strong>${ketQua.total}</strong> sân.`;
            }
        } catch (error) {
            console.error('Lỗi API:', error);
            thongBaoP.innerHTML = `<span style="color: red;">Có lỗi xảy ra khi tải dữ liệu.</span>`;
        } finally {
            danhSachUl.style.opacity = '1';
        }
    }

    // Bắt sự kiện submit form (thay vì tải lại trang, ta dùng JS)
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Ngăn trình duyệt tải lại trang
        taiDuLieu();
    });

    // (Tùy chọn) Tự động tải lại khi đổi select box
    document.getElementById('dm').addEventListener('change', taiDuLieu);
    document.getElementById('sx').addEventListener('change', taiDuLieu);
});