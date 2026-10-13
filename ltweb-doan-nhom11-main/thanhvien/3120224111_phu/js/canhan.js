/**
 * canhan.js — Trang cá nhân Trương Nguyễn Ngọc Phú (MSSV 3120224111)
 *
 * Tương tác 1: Chế độ tối (đổi giao diện sáng/tối, lưu vào localStorage)
 * Tương tác 2: Sao chép email vào clipboard
 */

document.addEventListener('DOMContentLoaded', function () {

  // ============================================================
  // 1. CHẾ ĐỘ TỐI
  // ============================================================
  const nutDoiGiaoDien = document.getElementById('nut-doi-giao-dien');
  const KHOA_LUU = 'phu-giao-dien';

  function apDungGiaoDien(trangThai) {
    const laToi = trangThai === 'toi';
    document.body.classList.toggle('giao-dien-toi', laToi);
    if (nutDoiGiaoDien) {
      nutDoiGiaoDien.textContent = laToi ? '☀️ Chế độ sáng' : '🌙 Chế độ tối';
      nutDoiGiaoDien.setAttribute('aria-pressed', String(laToi));
    }
  }

  if (nutDoiGiaoDien) {
    const trangThaiBanDau = localStorage.getItem(KHOA_LUU) || 'sang';
    apDungGiaoDien(trangThaiBanDau);

    nutDoiGiaoDien.addEventListener('click', function () {
      const dangToi = document.body.classList.contains('giao-dien-toi');
      const trangThaiMoi = dangToi ? 'sang' : 'toi';
      localStorage.setItem(KHOA_LUU, trangThaiMoi);
      apDungGiaoDien(trangThaiMoi);
    });
  }

  // ============================================================
  // 2. SAO CHÉP EMAIL
  // ============================================================
  const nutSaoChep = document.getElementById('nut-sao-chep-email');
  const emailEl = document.getElementById('email-ca-nhan');
  const thongBao = document.getElementById('thong-bao-sao-chep');

  if (nutSaoChep && emailEl && thongBao) {
    nutSaoChep.addEventListener('click', async function () {
      const email = emailEl.textContent.trim();
      try {
        await navigator.clipboard.writeText(email);
        thongBao.textContent = '✅ Đã sao chép: ' + email;
        thongBao.classList.add('thong-bao--thanh-cong');
        setTimeout(function () {
          thongBao.textContent = '';
          thongBao.classList.remove('thong-bao--thanh-cong');
        }, 2000);
      } catch (loi) {
        thongBao.textContent = '❌ Không sao chép được. Vui lòng chọn thủ công.';
        thongBao.classList.add('thong-bao--loi');
        setTimeout(function () {
          thongBao.textContent = '';
          thongBao.classList.remove('thong-bao--loi');
        }, 2500);
      }
    });
  }

});