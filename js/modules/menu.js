// menu.js
// Điều khiển nút mở/đóng menu điều hướng trên màn hình nhỏ.
// Khi JavaScript tắt: nút không có script nên không bấm được, nhưng menu
// (ul.dieu-huong__danh-sach) vẫn hiển thị bình thường nhờ CSS mặc định
// (không bị ẩn bởi thuộc tính hidden/class), nên người dùng vẫn điều hướng
// được bằng chuột/bàn phím như danh sách liên kết thông thường.

const LOP_DANG_MO = "dieu-huong--dang-mo";

/**
 * Khởi tạo hành vi cho nút mở menu trong một header cho trước.
 * @param {HTMLElement} dauTrang Phần tử <header class="dau-trang">
 */
export function khoiTaoMenu(dauTrang) {
  if (!dauTrang) return;

  const nutMoMenu = dauTrang.querySelector(".nut-mo-menu");
  const danhSachDieuHuong = dauTrang.querySelector(".dieu-huong__danh-sach");
  if (!nutMoMenu || !danhSachDieuHuong) return;

  const dong = () => {
    danhSachDieuHuong.classList.remove(LOP_DANG_MO);
    nutMoMenu.setAttribute("aria-expanded", "false");
  };

  const moHoacDong = () => {
    const dangMo = danhSachDieuHuong.classList.toggle(LOP_DANG_MO);
    nutMoMenu.setAttribute("aria-expanded", String(dangMo));
  };

  nutMoMenu.addEventListener("click", moHoacDong);

  // Đóng menu bằng phím Esc, trả tiêu điểm về nút mở menu.
  danhSachDieuHuong.addEventListener("keydown", (suKien) => {
    if (suKien.key === "Escape") {
      dong();
      nutMoMenu.focus();
    }
  });

  // Đóng menu khi bấm ra ngoài (chỉ áp dụng lúc menu đang mở).
  document.addEventListener("click", (suKien) => {
    const dangMo = danhSachDieuHuong.classList.contains(LOP_DANG_MO);
    const bamTrongHeader = dauTrang.contains(suKien.target);
    if (dangMo && !bamTrongHeader) {
      dong();
    }
  });
}
