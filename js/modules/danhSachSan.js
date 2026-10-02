// danhSachSan.js
// Chức năng 1 (Bảng 1): Danh sách sân + lọc theo loại sân + tìm kiếm theo tên/khu vực.
// Người phụ trách: ______ (điền tên thành viên)
// Dữ liệu lấy bằng fetch + async/await, có đủ 3 trạng thái đang tải/lỗi/rỗng.
// Khi tắt JavaScript: bảng tĩnh có sẵn trong HTML (5 dòng mẫu) vẫn hiển thị
// nguyên vẹn vì module này chỉ thay thế nội dung <tbody> khi fetch thành công.

import { layDuLieuJSON, hienDangTai, hienLoi, hienRong, xoaThongBao, dinhDangTien } from "./tienIch.js";

const DUONG_DAN_DU_LIEU = "data/san.json";

let danhSachSanGoc = [];

/**
 * Tạo một hàng <tr> hiển thị thông tin sân, có nút "Xem chi tiết".
 * @param {object} san
 * @param {(idSan: string) => void} khiXemChiTiet
 */
function taoHangSan(san, khiXemChiTiet) {
  const hang = document.createElement("tr");

  const oTen = document.createElement("td");
  oTen.textContent = san.ten;

  const oLoai = document.createElement("td");
  oLoai.textContent = `${san.loaiSan} người`;

  const oGia = document.createElement("td");
  oGia.textContent = `${dinhDangTien(san.giaThuong)} (giờ vàng ${dinhDangTien(san.giaVang)})`;

  const oKhuVuc = document.createElement("td");
  oKhuVuc.textContent = san.khuVuc;

  const oTrangThai = document.createElement("td");
  oTrangThai.textContent = san.dangHoatDong ? "Đang hoạt động" : "Đang bảo trì";

  const oChiTiet = document.createElement("td");
  const nutChiTiet = document.createElement("button");
  nutChiTiet.type = "button";
  nutChiTiet.className = "nut nut--phu nut--nho";
  nutChiTiet.textContent = "Xem chi tiết";
  nutChiTiet.addEventListener("click", () => khiXemChiTiet(san.id));
  oChiTiet.append(nutChiTiet);

  hang.append(oTen, oLoai, oGia, oKhuVuc, oTrangThai, oChiTiet);
  return hang;
}

/**
 * Lọc danh sách theo loại sân và từ khoá tìm kiếm (tên hoặc khu vực).
 */
function locDanhSach(loaiSan, tuKhoa) {
  const tuKhoaChuanHoa = tuKhoa.trim().toLowerCase();
  return danhSachSanGoc.filter((san) => {
    const khopLoai = loaiSan === "tat-ca" || String(san.loaiSan) === loaiSan;
    const khopTuKhoa =
      tuKhoaChuanHoa === "" ||
      san.ten.toLowerCase().includes(tuKhoaChuanHoa) ||
      san.khuVuc.toLowerCase().includes(tuKhoaChuanHoa);
    return khopLoai && khopTuKhoa;
  });
}

/**
 * Khởi tạo chức năng danh sách & lọc/tìm kiếm sân cho trang san-lich.html.
 * @param {object} tuyChon
 * @param {HTMLTableSectionElement} tuyChon.thanBang Thẻ <tbody> của bảng lịch sân
 * @param {HTMLElement} tuyChon.vungThongBao Vùng aria-live hiển thị trạng thái
 * @param {HTMLSelectElement} tuyChon.oChonLoai Select lọc theo loại sân
 * @param {HTMLInputElement} tuyChon.oTimKiem Input tìm kiếm theo tên/khu vực
 * @param {(idSan: string) => void} tuyChon.khiXemChiTiet Hàm mở hộp thoại chi tiết
 */
export async function khoiTaoDanhSachSan({ thanBang, vungThongBao, oChonLoai, oTimKiem, khiXemChiTiet }) {
  if (!thanBang) return;

  async function taiDuLieu() {
    hienDangTai(vungThongBao, "Đang tải danh sách sân…");
    try {
      danhSachSanGoc = await layDuLieuJSON(DUONG_DAN_DU_LIEU);
      xoaThongBao(vungThongBao);
      capNhatBang();
    } catch (loi) {
      hienLoi(vungThongBao, `Không tải được danh sách sân: ${loi.message}`, taiDuLieu);
    }
  }

  function capNhatBang() {
    const loaiDangChon = oChonLoai ? oChonLoai.value : "tat-ca";
    const tuKhoa = oTimKiem ? oTimKiem.value : "";
    const ketQua = locDanhSach(loaiDangChon, tuKhoa);

    thanBang.replaceChildren();
    if (ketQua.length === 0) {
      hienRong(vungThongBao, "Không có sân nào phù hợp với bộ lọc hiện tại.");
      return;
    }
    xoaThongBao(vungThongBao);
    const manhGhep = document.createDocumentFragment();
    ketQua.forEach((san) => manhGhep.append(taoHangSan(san, khiXemChiTiet)));
    thanBang.append(manhGhep);
    vungThongBao.textContent = `Tìm thấy ${ketQua.length} sân phù hợp.`;
  }

  if (oChonLoai) oChonLoai.addEventListener("change", capNhatBang);
  if (oTimKiem) oTimKiem.addEventListener("input", capNhatBang);

  await taiDuLieu();
}

/** Lấy một sân theo id từ dữ liệu đã tải (dùng cho chức năng xem chi tiết). */
export function laySanTheoId(idSan) {
  return danhSachSanGoc.find((san) => san.id === idSan);
}
