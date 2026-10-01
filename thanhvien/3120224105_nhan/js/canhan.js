// Tệp: canhan.js (Nhân)
// Chức năng: 1. Đếm ngược đến ngày thi. 2. Ẩn/hiện danh sách kỹ năng.

document.addEventListener('DOMContentLoaded', () => {
    // 1. Đếm ngược
    const countdownEl = document.getElementById('countdown');
    const examDate = new Date('2026-12-25T00:00:00').getTime();
    
    if (countdownEl) {
        setInterval(() => {
            const now = new Date().getTime();
            const distance = examDate - now;
            if (distance < 0) { countdownEl.textContent = 'Đã thi xong!'; return; }
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            countdownEl.textContent = Còn  + days +  ngày  + hours +  giờ tới ngày thi;
        }, 1000);
    }

    // 2. Ẩn hiện kỹ năng
    const btnToggle = document.getElementById('btn-toggle-skills');
    const skillsContainer = document.getElementById('skills-container');
    if (btnToggle && skillsContainer) {
        btnToggle.addEventListener('click', () => {
            const isExpanded = btnToggle.getAttribute('aria-expanded') === 'true';
            skillsContainer.style.display = isExpanded ? 'none' : 'flex';
            btnToggle.setAttribute('aria-expanded', !isExpanded);
        });
    }
});
