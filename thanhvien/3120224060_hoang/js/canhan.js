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
