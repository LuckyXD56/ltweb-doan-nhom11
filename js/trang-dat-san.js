// trang-dat-san.js
// Điểm nạp JavaScript cho dat-san.html.
import { khoiTaoMenu } from "./modules/menu.js";
import { khoiTaoDatSan } from "./modules/datSan.js";

khoiTaoMenu(document.querySelector(".dau-trang"));

khoiTaoDatSan({
  form: document.querySelector("#form-dat-san"),
  oChonLoai: document.querySelector("#loai-san"),
  oChonSanCuThe: document.querySelector("#san-cu-the"),
  oGioBatDau: document.querySelector("#gio-bat-dau"),
  oGioKetThuc: document.querySelector("#gio-ket-thuc"),
  vungThongBao: document.querySelector("#thong-bao-dat-san"),
});
