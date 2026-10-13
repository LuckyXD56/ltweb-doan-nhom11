import { khoiTaoYeuThich } from "./modules/yeuThich.js";
khoiTaoYeuThich();
https://www.ldoceonline.com/
/**
 * trang-thanh-vien.js — Tự động đọc danh sách thành viên từ JSON
 *
 * File này đọc datathanhvien/thanh-vien.json và render danh sách
 * thành viên vào <ul id="danh-sach-thanh-vien"> trên trang thanh-vien.html.
 *
 * Cách thử: Mở thanh-vien.html bằng Live Server, phải thấy đủ 5 thành viên
 * kèm nút "Xem trang cá nhân" trỏ đến đúng thư mục từng người.
 */

const ulDanhSach = document.getElementById('danh-sach-thanh-vien');
const thongBao = document.getElementById('thong-bao-thanh-vien');

/**
 * Tạo HTML cho 1 thẻ thành viên
 */
function taoTheThanhVien(tv) {
  const lopNoiBat = tv.noi_bat ? ' the--noi-bat' : '';
  const dongMSSV = tv.mssv ? `Thành viên · MSSV ${tv.mssv}<br>` : 'Thành viên<br>';
  const duongDan = `thanhvien/${tv.thu_muc}/gioithieu.html`;

  return `
    <li class="the${lopNoiBat}">
      <strong>${tv.ten}</strong><br>
      ${dongMSSV}
      <a class="nut nut--chinh" href="${duongDan}">Xem trang cá nhân</a>
    </li>
  `;
}

/**
 * Tải danh sách từ JSON
 */
async function taiDanhSachThanhVien() {
  try {
    const phanHoi = await fetch('datathanhvien/thanh-vien.json');

    if (!phanHoi.ok) {
      throw new Error(`Không tải được JSON (mã ${phanHoi.status})`);
    }

    const danhSach = await phanHoi.json();

    if (!Array.isArray(danhSach) || danhSach.length === 0) {
      ulDanhSach.innerHTML = '<li class="the">Chưa có thành viên nào.</li>';
      return;
    }

    ulDanhSach.innerHTML = danhSach.map(taoTheThanhVien).join('');

    if (thongBao) {
      thongBao.textContent = `Đã tải ${danhSach.length} thành viên.`;
    }
  } catch (loi) {
    console.error('Lỗi tải danh sách thành viên:', loi);
    ulDanhSach.innerHTML =
      '<li class="the">Không thể tải danh sách thành viên. Vui lòng thử lại sau.</li>';
    if (thongBao) {
      thongBao.textContent = 'Lỗi: ' + loi.message;
    }
  }
}

taiDanhSachThanhVien();