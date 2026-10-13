import { khoiTaoYeuThich } from "./modules/yeuThich.js";
khoiTaoYeuThich();
// trang-tai-khoan.js
// Điểm nạp JavaScript cho tai-khoan.html.
import { khoiTaoMenu } from "./modules/menu.js";
import { khoiTaoDangNhap, khoiTaoDangKy } from "./modules/taiKhoan.js";

khoiTaoMenu(document.querySelector(".dau-trang"));

khoiTaoDangNhap({
  form: document.querySelector("#form-dang-nhap"),
  vungThongBao: document.querySelector("#thong-bao-dang-nhap"),
});

khoiTaoDangKy({
  form: document.querySelector("#form-dang-ky"),
  vungThongBao: document.querySelector("#thong-bao-dang-ky"),
});
