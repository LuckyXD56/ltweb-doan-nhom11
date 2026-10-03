import { khoiTaoYeuThich } from "./modules/yeuThich.js";
khoiTaoYeuThich();
// trang-chung.js
// Điểm nạp JavaScript cho các trang chỉ cần menu điều hướng có trạng thái
// (lich-su.html, quan-tri.html, thanh-vien.html) — chưa có chức năng dữ liệu
// động riêng theo Bảng 1.
import { khoiTaoMenu } from "./modules/menu.js";

khoiTaoMenu(document.querySelector(".dau-trang"));
