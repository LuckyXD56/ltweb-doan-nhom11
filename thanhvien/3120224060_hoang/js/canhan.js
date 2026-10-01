// Tệp: canhan.js (Hoàng)
// Chức năng: 1. Chuyển giao diện sáng/tối lưu vào localStorage. 2. Nút sao chép email vào clipboard.

document.addEventListener('DOMContentLoaded', () => {
    const btnDarkMode = document.getElementById('btn-dark-mode');
    const isDark = localStorage.getItem('hoang_dark_mode') === 'true';
    if (isDark) { document.body.classList.add('dark-mode'); btnDarkMode.textContent = 'Giao diện sáng'; }

    btnDarkMode.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const isNowDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('hoang_dark_mode', isNowDark);
        btnDarkMode.textContent = isNowDark ? 'Giao diện sáng' : 'Giao diện tối';
    });

    const btnCopy = document.getElementById('btn-copy');
    const emailText = document.getElementById('email-text');
    const copyMsg = document.getElementById('copy-msg');

    if (btnCopy && emailText) {
        btnCopy.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(emailText.textContent);
                copyMsg.textContent = 'Đã sao chép!';
                setTimeout(() => { copyMsg.textContent = ''; }, 2000);
            } catch (err) {
                console.error('Lỗi khi sao chép:', err);
            }
        });
    }
});
