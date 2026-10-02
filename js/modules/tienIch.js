// tienIch.js
// Các hàm dùng chung cho toàn bộ trang: gọi API bằng fetch (kiểm tra res.ok,
// bắt lỗi bằng try/catch) và hiển thị 3 trạng thái "đang tải / lỗi / rỗng"
// bên trong một vùng thông báo có aria-live="polite".

/**
 * Lấy dữ liệu JSON từ một đường dẫn, luôn kiểm tra res.ok và bắt lỗi.
 * @param {string} duongDan Đường dẫn tới tệp/API JSON
 * @returns {Promise<any>} Dữ liệu đã parse
 */
export async function layDuLieuJSON(duongDan) {
  const res = await fetch(duongDan);
  if (!res.ok) {
    throw new Error(`Không tải được dữ liệu (mã lỗi ${res.status})`);
  }
  return res.json();
}

/**
 * Hiển thị trạng thái "đang tải" vào vùng thông báo (aria-live).
 * @param {HTMLElement} vungThongBao
 * @param {string} [noiDung]
 */
export function hienDangTai(vungThongBao, noiDung = "Đang tải dữ liệu…") {
  if (!vungThongBao) return;
  vungThongBao.textContent = noiDung;
  vungThongBao.dataset.trangThai = "dang-tai";
}

/**
 * Hiển thị trạng thái lỗi kèm nút "Thử lại" vào vùng thông báo.
 * @param {HTMLElement} vungThongBao
 * @param {string} thongDiepLoi
 * @param {() => void} khiThuLai Hàm gọi lại khi người dùng bấm "Thử lại"
 */
export function hienLoi(vungThongBao, thongDiepLoi, khiThuLai) {
  if (!vungThongBao) return;
  vungThongBao.textContent = "";
  vungThongBao.dataset.trangThai = "loi";

  const doan = document.createElement("p");
  doan.className = "thong-bao__noi-dung thong-bao__noi-dung--loi";
  doan.textContent = thongDiepLoi || "Đã có lỗi xảy ra, vui lòng thử lại.";
  vungThongBao.append(doan);

  if (typeof khiThuLai === "function") {
    const nutThuLai = document.createElement("button");
    nutThuLai.type = "button";
    nutThuLai.className = "nut nut--phu";
    nutThuLai.textContent = "Thử lại";
    nutThuLai.addEventListener("click", khiThuLai);
    vungThongBao.append(nutThuLai);
  }
}

/**
 * Hiển thị trạng thái "không có dữ liệu".
 * @param {HTMLElement} vungThongBao
 * @param {string} [noiDung]
 */
export function hienRong(vungThongBao, noiDung = "Không tìm thấy kết quả phù hợp.") {
  if (!vungThongBao) return;
  vungThongBao.textContent = noiDung;
  vungThongBao.dataset.trangThai = "rong";
}

/**
 * Xoá nội dung vùng thông báo khi đã hiển thị dữ liệu thành công.
 * @param {HTMLElement} vungThongBao
 */
export function xoaThongBao(vungThongBao) {
  if (!vungThongBao) return;
  vungThongBao.textContent = "";
  delete vungThongBao.dataset.trangThai;
}

/**
 * Định dạng số tiền VND, ví dụ 200000 -> "200.000đ".
 * @param {number} soTien
 */
export function dinhDangTien(soTien) {
  return `${Number(soTien).toLocaleString("vi-VN")}đ`;
}
