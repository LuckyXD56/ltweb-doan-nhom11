// datSan.js
// Chức năng 3 (Bảng 1): Đặt sân trực tuyến.
// Người phụ trách: ______ (điền tên thành viên)
// - fetch data/san.json để đổ danh sách "sân cụ thể" tương ứng với loại sân đã chọn
// - kiểm tra giờ bắt đầu phải trước giờ kết thúc
// - khi JS bật: chặn submit mặc định, hiển thị tóm tắt đơn trong vùng aria-live
// - khi JS tắt: không có script nào chạy, form vẫn gửi theo kiểu thông thường
//   nhờ action="#" method="post" đã có sẵn trong HTML (nội dung dự phòng).

import { layDuLieuJSON, hienLoi, dinhDangTien } from "./tienIch.js";

const DUONG_DAN_DU_LIEU = "data/san.json";
let danhSachSan = [];

/**
 * Đổ danh sách sân cụ thể vào select con theo loại sân đã chọn.
 */
function capNhatOChonSanCuThe(oChonLoai, oChonSanCuThe) {
  const loaiDaChon = oChonLoai.value;
  oChonSanCuThe.replaceChildren();

  const tuyChonMacDinh = document.createElement("option");
  tuyChonMacDinh.value = "";
  tuyChonMacDinh.textContent = loaiDaChon
    ? "-- Chọn sân cụ thể --"
    : "-- Vui lòng chọn loại sân trước --";
  oChonSanCuThe.append(tuyChonMacDinh);

  if (!loaiDaChon) {
    oChonSanCuThe.disabled = true;
    return;
  }

  const sanPhuHop = danhSachSan.filter(
    (san) => String(san.loaiSan) === loaiDaChon && san.dangHoatDong
  );
  sanPhuHop.forEach((san) => {
    const tuyChon = document.createElement("option");
    tuyChon.value = san.id;
    tuyChon.textContent = `${san.ten} – ${san.khuVuc} (${dinhDangTien(san.giaThuong)}/giờ)`;
    oChonSanCuThe.append(tuyChon);
  });
  oChonSanCuThe.disabled = sanPhuHop.length === 0;
}

/** Kiểm tra giờ bắt đầu phải trước giờ kết thúc. */
function kiemTraKhungGio(gioBatDau, gioKetThuc) {
  if (!gioBatDau || !gioKetThuc) return "Vui lòng nhập đầy đủ giờ bắt đầu và giờ kết thúc.";
  if (gioBatDau >= gioKetThuc) return "Giờ bắt đầu phải trước giờ kết thúc.";
  return "";
}

/**
 * Khởi tạo chức năng đặt sân cho trang dat-san.html.
 * @param {object} tuyChon
 * @param {HTMLFormElement} tuyChon.form
 * @param {HTMLSelectElement} tuyChon.oChonLoai
 * @param {HTMLSelectElement} tuyChon.oChonSanCuThe Select con sẽ được thêm động
 * @param {HTMLInputElement} tuyChon.oGioBatDau
 * @param {HTMLInputElement} tuyChon.oGioKetThuc
 * @param {HTMLElement} tuyChon.vungThongBao
 */
export async function khoiTaoDatSan({ form, oChonLoai, oChonSanCuThe, oGioBatDau, oGioKetThuc, vungThongBao }) {
  if (!form || !oChonLoai || !oChonSanCuThe) return;

  try {
    danhSachSan = await layDuLieuJSON(DUONG_DAN_DU_LIEU);
  } catch (loi) {
    hienLoi(vungThongBao, `Không tải được danh sách sân cụ thể: ${loi.message}`, () =>
      khoiTaoDatSan({ form, oChonLoai, oChonSanCuThe, oGioBatDau, oGioKetThuc, vungThongBao })
    );
    return;
  }

  capNhatOChonSanCuThe(oChonLoai, oChonSanCuThe);
  oChonLoai.addEventListener("change", () => capNhatOChonSanCuThe(oChonLoai, oChonSanCuThe));

  form.addEventListener("submit", (suKien) => {
    suKien.preventDefault();
    vungThongBao.textContent = "";
    delete vungThongBao.dataset.trangThai;

    const thongDiepLoiGio = kiemTraKhungGio(oGioBatDau.value, oGioKetThuc.value);
    if (!oChonSanCuThe.value) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = "Vui lòng chọn một sân cụ thể trước khi xác nhận.";
      return;
    }
    if (thongDiepLoiGio) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = thongDiepLoiGio;
      return;
    }

    const sanDaChon = danhSachSan.find((san) => san.id === oChonSanCuThe.value);
    vungThongBao.dataset.trangThai = "thanh-cong";
    vungThongBao.textContent = sanDaChon
      ? `Đã ghi nhận yêu cầu đặt ${sanDaChon.ten} (${oGioBatDau.value} – ${oGioKetThuc.value}). Nhân viên sẽ liên hệ xác nhận trong 30 phút.`
      : "Đã ghi nhận yêu cầu đặt sân. Nhân viên sẽ liên hệ xác nhận trong 30 phút.";
    form.reset();
    capNhatOChonSanCuThe(oChonLoai, oChonSanCuThe);
  });
}
