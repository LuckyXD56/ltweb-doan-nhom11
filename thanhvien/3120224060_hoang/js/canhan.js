document.addEventListener('DOMContentLoaded', () => {

  // 1. CHẾ ĐỘ TỐI / SÁNG
  const nutDoiGiaoDien = document.getElementById('nut-doi-giao-dien');
  const KHOA_LUU_THEME = 'hoang_theme';

  function apDungGiaoDien(trangThai) {
    const laToi = trangThai === 'toi';
    document.body.classList.toggle('giao-dien-toi', laToi);
    if (nutDoiGiaoDien) {
      nutDoiGiaoDien.textContent = laToi ? '☀️ Chế độ sáng' : '🌙 Chế độ tối';
      nutDoiGiaoDien.setAttribute('aria-pressed', String(laToi));
    }
  }

  if (nutDoiGiaoDien) {
    const trangThaiLuu = localStorage.getItem(KHOA_LUU_THEME) || 'sang';
    apDungGiaoDien(trangThaiLuu);

    nutDoiGiaoDien.addEventListener('click', () => {
      const dangToi = document.body.classList.contains('giao-dien-toi');
      const trangThaiMoi = dangToi ? 'sang' : 'toi';
      localStorage.setItem(KHOA_LUU_THEME, trangThaiMoi);
      apDungGiaoDien(trangThaiMoi);
    });
  }

  // 2. SAO CHÉP EMAIL
  const nutSaoChep = document.getElementById('nut-sao-chep-email');
  const emailEl = document.getElementById('email-ca-nhan');
  const thongBao = document.getElementById('thong-bao-sao-chep');

  if (nutSaoChep && emailEl && thongBao) {
    nutSaoChep.addEventListener('click', async () => {
      const email = emailEl.textContent.trim();
      try {
        await navigator.clipboard.writeText(email);
        thongBao.textContent = '✅ Đã sao chép!';
        setTimeout(() => { thongBao.textContent = ''; }, 2000);
      } catch (err) {
        thongBao.textContent = '❌ Lỗi sao chép!';
        setTimeout(() => { thongBao.textContent = ''; }, 2000);
      }
    });
  }

  // 3. NÚT LIKE (SESSION STORAGE)
  const btnLike = document.getElementById('btn-like');
  const likeCountSpan = document.getElementById('like-count');

  if (btnLike && likeCountSpan) {
    let luotLike = parseInt(sessionStorage.getItem('hoang_likes')) || 0;
    likeCountSpan.textContent = luotLike;

    btnLike.addEventListener('click', () => {
      luotLike++;
      likeCountSpan.textContent = luotLike;
      sessionStorage.setItem('hoang_likes', luotLike);

      btnLike.style.transform = 'scale(1.1)';
      setTimeout(() => {
        btnLike.style.transform = 'scale(1)';
      }, 150);
    });
  }

});