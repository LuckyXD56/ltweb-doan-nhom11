// trangChu.js
// Chức năng 6 (Bảng 1): Sân nổi bật trên trang chủ, lấy từ dữ liệu động (fetch),
// có thể sắp xếp theo giá tăng dần hoặc theo đánh giá cao nhất.
// Người phụ trách: ______ (điền tên thành viên)
// Khi tắt JavaScript: danh sách tĩnh "Các loại sân hiện có" đã có sẵn trong
// index.html vẫn hiển thị nguyên vẹn; khối sân nổi bật chỉ xuất hiện thêm khi
// fetch thành công (không thay thế nội dung tĩnh cũ).

import { layDuLieuJSON, hienDangTai, hienLoi, hienRong, xoaThongBao, dinhDangTien } from "./tienIch.js";

const DUONG_DAN_DU_LIEU = "data/san.json";
let danhSachSan = [];

function taoTheSan(san) {
  const the = document.createElement("li");
  the.className = "the";

  const tieuDe = document.createElement("strong");
  tieuDe.textContent = `${san.ten} – sân ${san.loaiSan} người`;

  const anh = document.createElement("img");
  anh.src = san.hinhAnh;
  anh.alt = `Hình ảnh ${san.ten}`;
  anh.width = 280;
  anh.height = 158;
  anh.loading = "lazy";

  const gia = document.createElement("p");
  gia.textContent = `${dinhDangTien(san.giaThuong)}/giờ · ${san.khuVuc} · ${san.danhGia.toFixed(1)}★`;

  the.append(tieuDe, anh, gia);
  return the;
}

/**
 * Khởi tạo khối "Sân nổi bật" trên trang chủ.
 * @param {object} tuyChon
 * @param {HTMLUListElement} tuyChon.danhSachNoiBat
 * @param {HTMLElement} tuyChon.vungThongBao
 * @param {HTMLSelectElement} [tuyChon.oSapXep]
 */
export async function khoiTaoTrangChu({ danhSachNoiBat, vungThongBao, oSapXep }) {
  if (!danhSachNoiBat) return;

  function sapXep(tieuChi) {
    const banSaoChep = [...danhSachSan].filter((san) => san.dangHoatDong);
    if (tieuChi === "danh-gia") {
      banSaoChep.sort((a, b) => b.danhGia - a.danhGia);
    } else {
      banSaoChep.sort((a, b) => a.giaThuong - b.giaThuong);
    }
    return banSaoChep.slice(0, 3);
  }

  function ve() {
    const tieuChi = oSapXep ? oSapXep.value : "gia";
    const noiBat = sapXep(tieuChi);
    danhSachNoiBat.replaceChildren();
    if (noiBat.length === 0) {
      hienRong(vungThongBao, "Hiện chưa có sân nào để gợi ý.");
      return;
    }
    xoaThongBao(vungThongBao);
    noiBat.forEach((san) => danhSachNoiBat.append(taoTheSan(san)));
  }

  async function taiDuLieu() {
    hienDangTai(vungThongBao, "Đang tải sân nổi bật…");
    try {
      danhSachSan = await layDuLieuJSON(DUONG_DAN_DU_LIEU);
      ve();
    } catch (loi) {
      hienLoi(vungThongBao, `Không tải được sân nổi bật: ${loi.message}`, taiDuLieu);
    }
  }

  if (oSapXep) oSapXep.addEventListener("change", ve);
  await taiDuLieu();
}
