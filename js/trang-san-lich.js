// trang-san-lich.js
// Điểm nạp JavaScript cho san-lich.html.
import { khoiTaoMenu } from "./modules/menu.js";
import { khoiTaoDanhSachSan, laySanTheoId } from "./modules/danhSachSan.js";
import { khoiTaoChiTietSan } from "./modules/chiTietSan.js";

khoiTaoMenu(document.querySelector(".dau-trang"));

const moChiTiet = khoiTaoChiTietSan(document.querySelector("#hop-thoai-chi-tiet-san"));

khoiTaoDanhSachSan({
  thanBang: document.querySelector("#than-bang-san"),
  vungThongBao: document.querySelector("#thong-bao-danh-sach-san"),
  oChonLoai: document.querySelector("#loc-loai-san"),
  oTimKiem: document.querySelector("#tim-kiem-san"),
  khiXemChiTiet(idSan) {
    moChiTiet(laySanTheoId(idSan));
  },
});
