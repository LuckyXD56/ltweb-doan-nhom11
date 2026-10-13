// chiTietSan.js
// Chức năng 2 (Bảng 1): Xem chi tiết một sân trong hộp thoại (dialog).
// Người phụ trách: ______ (điền tên thành viên)
// Dùng lại dữ liệu đã fetch ở danhSachSan.js (không gọi fetch trùng lặp);
// mọi nội dung hiển thị dùng textContent/createElement/thuộc tính ảnh,
// không dùng innerHTML, để an toàn với dữ liệu động.

import { dinhDangTien } from "./tienIch.js";

/**
 * Khởi tạo hộp thoại chi tiết sân.
 * @param {HTMLDialogElement} hopThoai Thẻ <dialog id="hop-thoai-chi-tiet-san">
 * @returns {(san: object) => void} Hàm mở hộp thoại với dữ liệu một sân
 */
export function khoiTaoChiTietSan(hopThoai) {
  if (!hopThoai) return () => {};

  const noiDung = hopThoai.querySelector(".hop-thoai__noi-dung");
  const nutDong = hopThoai.querySelector(".hop-thoai__nut-dong");

  function dong() {
    if (typeof hopThoai.close === "function") {
      hopThoai.close();
    } else {
      hopThoai.removeAttribute("open");
    }
  }

  if (nutDong) nutDong.addEventListener("click", dong);

  // Hỗ trợ trình duyệt chưa có <dialog> gốc: đóng bằng Esc thủ công.
  hopThoai.addEventListener("keydown", (suKien) => {
    if (suKien.key === "Escape") dong();
  });

  return function moChiTiet(san) {
    if (!san || !noiDung) return;
    noiDung.replaceChildren();

    const anh = document.createElement("img");
    anh.src = san.hinhAnh;
    anh.alt = `Hình ảnh ${san.ten}, loại sân ${san.loaiSan} người`;
    anh.width = 320;
    anh.height = 180;
    anh.className = "hop-thoai__anh";

    const tieuDe = document.createElement("h3");
    tieuDe.id = "hop-thoai-tieu-de";
    tieuDe.textContent = `${san.ten} – sân ${san.loaiSan} người`;

    const moTa = document.createElement("p");
    moTa.textContent = san.moTa;

    const danhSachThongTin = document.createElement("ul");
    const muc = (nhan, giaTri) => {
      const li = document.createElement("li");
      li.textContent = `${nhan}: ${giaTri}`;
      return li;
    };
    danhSachThongTin.append(
      muc("Khu vực", san.khuVuc),
      muc("Địa chỉ", san.diaChi),
      muc("Giá giờ thường", dinhDangTien(san.giaThuong)),
      muc("Giá giờ vàng", dinhDangTien(san.giaVang)),
      muc("Mái che", san.coMaiChe ? "Có" : "Không"),
      muc("Đèn chiếu sáng", san.coDenChieuSang ? "Có" : "Không"),
      muc("Trạng thái", san.dangHoatDong ? "Đang hoạt động" : "Đang bảo trì")
    );

    noiDung.append(anh, tieuDe, moTa, danhSachThongTin);

    if (typeof hopThoai.showModal === "function") {
      hopThoai.showModal();
    } else {
      hopThoai.setAttribute("open", "");
    }
  };
}
