// trang-index.js
// Điểm nạp JavaScript cho index.html — chỉ import và khởi động các module cần dùng.
import { khoiTaoMenu } from "./modules/menu.js";
import { khoiTaoTrangChu } from "./modules/trangChu.js";

khoiTaoMenu(document.querySelector(".dau-trang"));

khoiTaoTrangChu({
  danhSachNoiBat: document.querySelector("#danh-sach-noi-bat"),
  vungThongBao: document.querySelector("#thong-bao-noi-bat"),
  oSapXep: document.querySelector("#sap-xep-noi-bat"),
});
