// Tệp: canhan.js (Người phụ trách: Phimmasone Khamphouvanh - 3120224192)
// Chức năng: 
// 1. Ô tìm kiếm lọc danh sách kỹ năng (nhập ký tự để lọc các thẻ li).
// 2. Nút Thu gọn/Mở rộng thời khóa biểu (Accordion) thay đổi aria-expanded.
// CÁCH THỬ:
// - C1: Gõ chữ vào ô tìm kiếm kỹ năng, danh sách sẽ tự lọc.
// - C2: Cuộn xuống Thời khóa biểu, bấm Thu gọn / Mở rộng.

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tìm kiếm kỹ năng
    const searchInput = document.getElementById('skill-search');
    const skillList = document.getElementById('skill-list');
    
    if (searchInput && skillList) {
        const skills = skillList.querySelectorAll('li');
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            skills.forEach(skill => {
                const text = skill.textContent.toLowerCase();
                if (text.includes(query)) {
                    skill.style.display = '';
                } else {
                    skill.style.display = 'none';
                }
            });
        });
    }

    // 2. Thu gọn / Mở rộng lịch học
    const btnToggle = document.getElementById('btn-toggle-schedule');
    const scheduleContainer = document.getElementById('schedule-container');

    if (btnToggle && scheduleContainer) {
        btnToggle.addEventListener('click', () => {
            const isExpanded = btnToggle.getAttribute('aria-expanded') === 'true';
            
            if (isExpanded) {
                scheduleContainer.style.display = 'none';
                btnToggle.setAttribute('aria-expanded', 'false');
                btnToggle.textContent = 'Mở rộng';
            } else {
                scheduleContainer.style.display = 'block';
                btnToggle.setAttribute('aria-expanded', 'true');
                btnToggle.textContent = 'Thu gọn';
            }
        });
    }
});
