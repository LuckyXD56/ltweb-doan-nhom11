// =====================================================
// Trang thành viên — tự động load danh sách từ JSON
// =====================================================

const ulDanhSach = document.getElementById('danh-sach-thanh-vien');
const thongBao   = document.getElementById('thong-bao-thanh-vien');

/**
 * Tạo HTML cho 1 thành viên
 */
function taoTheThanhVien(tv) {
  const lopNoiBat = tv.noi_bat ? ' the--noi-bat' : '';
  const dongMSSV  = tv.mssv ? `Thành viên · MSSV ${tv.mssv}<br>` : 'Thành viên<br>';
  const duongDan  = `thanhvien/${tv.thu_muc}/gioithieu.html`;

  return `
    <li class="the${lopNoiBat}">
      <strong>${tv.ten}</strong><br>
      ${dongMSSV}
      <a class="nut nut--chinh" href="${duongDan}">Xem trang cá nhân</a>
    </li>
  `;
}

/**
 * Tải danh sách từ data/thanh-vien.json
 */
async function taiDanhSachThanhVien() {
  try {
    const phanHoi = await fetch('data/thanh-vien.json');

    if (!phanHoi.ok) {
      throw new Error(`Không tải được file JSON (mã lỗi ${phanHoi.status})`);
    }

    const danhSach = await phanHoi.json();

    if (!Array.isArray(danhSach) || danhSach.length === 0) {
      ulDanhSach.innerHTML = '<li class="the">Chưa có thành viên nào trong danh sách.</li>';
      return;
    }

    // Render từng thành viên
    ulDanhSach.innerHTML = danhSach.map(taoTheThanhVien).join('');

    // Thông báo thành công
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

// Chạy khi trang đã sẵn sàng
taiDanhSachThanhVien();