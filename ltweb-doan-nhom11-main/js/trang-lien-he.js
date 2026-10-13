import { khoiTaoYeuThich } from "./modules/yeuThich.js";
khoiTaoYeuThich();
// trang-lien-he.js
// Điểm nạp JavaScript cho lien-he.html.
import { khoiTaoMenu } from "./modules/menu.js";
import { khoiTaoLienHe } from "./modules/lienHe.js";

khoiTaoMenu(document.querySelector(".dau-trang"));

khoiTaoLienHe({
  form: document.querySelector("#form-lien-he"),
  vungThongBao: document.querySelector("#thong-bao-lien-he"),
});
