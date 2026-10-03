/*
 * Tệp JavaScript tạo các tương tác cho trang cá nhân của Hoàng.
 * Chức năng 1: Bộ lọc ảnh (Gallery Filter) theo danh mục.
 * Chức năng 2: Nút Like đếm số lần bấm và lưu vào sessionStorage.
 */

document.addEventListener('DOMContentLoaded', () => {
    // =========================
    // 1. Bộ lọc ảnh (Gallery)
    // =========================
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Đổi màu nút đang chọn
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.classList.add('nut--phu');
            });
            btn.classList.add('active');
            btn.classList.remove('nut--phu');

            // Lọc ảnh theo data-category
            const filterValue = btn.getAttribute('data-filter');
            galleryItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // =========================
    // 2. Nút Like đếm số
    // =========================
    const btnLike = document.getElementById('btn-like');
    const likeCountSpan = document.getElementById('like-count');

    if (btnLike && likeCountSpan) {
        // Lấy số like cũ từ sessionStorage (chỉ lưu tạm thời)
        let luotLike = parseInt(sessionStorage.getItem('hoang_likes')) || 0;
        likeCountSpan.textContent = luotLike;

        btnLike.addEventListener('click', () => {
            luotLike++;
            likeCountSpan.textContent = luotLike;
            sessionStorage.setItem('hoang_likes', luotLike);
            
            // Hiệu ứng phóng to nhẹ khi bấm
            btnLike.style.transform = 'scale(1.1)';
            setTimeout(() => {
                btnLike.style.transform = 'scale(1)';
            }, 200);
        });
    }
});



/* ============================================================
   BỔ SUNG — Nút "Chế độ tối" và "Sao chép email"
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

  // ---------- 1. CHẾ ĐỘ TỐI ----------
  const nutDoiGiaoDien = document.getElementById('nut-doi-giao-dien');
  const KHOA_LUU = 'hoang-giao-dien';

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

  // ---------- 2. SAO CHÉP EMAIL ----------
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
