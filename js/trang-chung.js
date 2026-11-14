import { khoiTaoYeuThich } from "./modules/yeuThich.js";
khoiTaoYeuThich();
// trang-chung.js
// Điểm nạp JavaScript cho các trang chỉ cần menu điều hướng có trạng thái
// (lich-su.html, quan-tri.html, thanh-vien.html) — chưa có chức năng dữ liệu
// động riêng theo Bảng 1.
import { khoiTaoMenu } from "./modules/menu.js";

khoiTaoMenu(document.querySelector(".dau-trang"));


// Xử lý xác nhận form hành động nguy hiểm tuân thủ tuyệt đối Content Security Policy
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form && form.hasAttribute('data-xac-nhan')) {
        const msg = form.getAttribute('data-xac-nhan');
        if (msg && !confirm(msg)) {
            e.preventDefault();
        }
    }
});
