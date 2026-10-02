/**
 * canhan.js — Trang cá nhân Trương Nguyễn Ngọc Phú (MSSV 3120224111)
 *
 * Tương tác 1: Nút "Chế độ tối" bật/tắt giao diện sáng-tối, ghi nhớ lựa chọn
 *              bằng localStorage để lần sau mở trang vẫn giữ nguyên.
 * Tương tác 2: Nút "Sao chép email" dùng navigator.clipboard.writeText để
 *              sao chép email, hiện thông báo và tự tắt sau 2 giây.
 *
 * Cách thử: Mở gioithieu.html bằng Live Server → bấm "Chế độ tối" → tải lại (F5)
 *           → giao diện vẫn tối. Nhấn Tab để focus, Enter/Space để kích hoạt.
 *           Bấm "Sao chép email" → dán vào Notepad để kiểm tra.
 */

// ============ TƯƠNG TÁC 1: ĐỔI GIAO DIỆN SÁNG / TỐI ============

const nutDoiGiaoDien = document.getElementById('nut-doi-giao-dien');
const KHOA_LUU = 'phu-giao-dien';

function apDungGiaoDien(trangThai) {
  const laToi = trangThai === 'toi';
  document.body.classList.toggle('giao-dien-toi', laToi);
  nutDoiGiaoDien.textContent = laToi ? '☀️ Chế độ sáng' : '🌙 Chế độ tối';
  nutDoiGiaoDien.setAttribute('aria-pressed', String(laToi));
}

const trangThaiBanDau = localStorage.getItem(KHOA_LUU) || 'sang';
apDungGiaoDien(trangThaiBanDau);

nutDoiGiaoDien.addEventListener('click', () => {
  const dangToi = document.body.classList.contains('giao-dien-toi');
  const trangThaiMoi = dangToi ? 'sang' : 'toi';
  localStorage.setItem(KHOA_LUU, trangThaiMoi);
  apDungGiaoDien(trangThaiMoi);
});

// ============ TƯƠNG TÁC 2: SAO CHÉP EMAIL ============

const nutSaoChep = document.getElementById('nut-sao-chep-email');
const emailEl = document.getElementById('email-ca-nhan');
const thongBao = document.getElementById('thong-bao-sao-chep');

nutSaoChep.addEventListener('click', async () => {
  const email = emailEl.textContent.trim();
  try {
    await navigator.clipboard.writeText(email);
    thongBao.textContent = '✅ Đã sao chép: ' + email;
    thongBao.classList.add('thong-bao--thanh-cong');
    setTimeout(() => {
      thongBao.textContent = '';
      thongBao.classList.remove('thong-bao--thanh-cong');
    }, 2000);
  } catch (loi) {
    thongBao.textContent = '❌ Không sao chép được. Vui lòng chọn thủ công.';
    thongBao.classList.add('thong-bao--loi');
    setTimeout(() => {
      thongBao.textContent = '';
      thongBao.classList.remove('thong-bao--loi');
    }, 2500);
  }
});