// Tệp: canhan.js (Người phụ trách: Phimmasone Khamphouvanh - 3120224192)
// Chức năng: 
// 1. Ô tìm kiếm lọc danh sách kỹ năng (nhập ký tự để lọc các thẻ li qua CSS class is-hidden).
// 2. Nút Thu gọn/Mở rộng thời khóa biểu (Accordion) thay đổi aria-expanded và toggle CSS class.
// 3. Xử lý hộp thoại xác nhận xóa qua data-confirm (100% tuân thủ CSP, không dùng inline onclick).
// CÁCH THỬ:
// - C1: Gõ chữ vào ô tìm kiếm kỹ năng, danh sách sẽ tự lọc.
// - C2: Cuộn xuống Thời khóa biểu, bấm Thu gọn / Mở rộng.
// - C3: Nhấn nút xóa công việc, xuất hiện hộp thoại xác nhận qua event listener.

document.addEventListener('DOMContentLoaded', () => {
    // 1. Tìm kiếm kỹ năng
    const searchInput = document.getElementById('skill-search');
    const skillList = document.getElementById('skill-list');
    
    if (searchInput && skillList) {
        const skills = skillList.querySelectorAll('li');
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            skills.forEach(skill => {
                const text = skill.textContent.toLowerCase();
                if (query === '' || text.includes(query)) {
                    skill.classList.remove('is-hidden');
                } else {
                    skill.classList.add('is-hidden');
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
                scheduleContainer.classList.add('is-hidden');
                btnToggle.setAttribute('aria-expanded', 'false');
                btnToggle.textContent = 'Mở rộng';
            } else {
                scheduleContainer.classList.remove('is-hidden');
                btnToggle.setAttribute('aria-expanded', 'true');
                btnToggle.textContent = 'Thu gọn';
            }
        });
    }

    // 3. Xử lý xác nhận cho các form hành động (chống inline onclick để tuân thủ CSP script-src 'self')
    const confirmForms = document.querySelectorAll('form[data-confirm]');
    confirmForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const message = form.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn thực hiện thao tác này?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
