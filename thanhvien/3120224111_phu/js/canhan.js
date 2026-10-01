// Tệp: canhan.js (Phú)
// Chức năng: 1. Nút cuộn lên đầu trang. 2. Lọc kỹ năng theo danh mục bằng nút bấm.

document.addEventListener('DOMContentLoaded', () => {
    // 1. Back to top
    const btnTop = document.getElementById('btn-back-top');
    if (btnTop) {
        window.addEventListener('scroll', () => {
            btnTop.style.display = window.scrollY > 200 ? 'block' : 'none';
        });
        btnTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // 2. Filter skills
    const filterBtns = document.querySelectorAll('.filter-btn');
    const skills = document.querySelectorAll('#skills-list li');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.getAttribute('data-filter');
            skills.forEach(skill => {
                if (filter === 'all' || skill.getAttribute('data-category') === filter) {
                    skill.style.display = '';
                } else {
                    skill.style.display = 'none';
                }
            });
        });
    });
});
