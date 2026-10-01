// Tệp: canhan.js (Silaphet)
// Chức năng: 1. Lời chào theo buổi (Sáng/Chiều/Tối). 2. Hiệu ứng gõ chữ (Typewriter) cho tên.

document.addEventListener('DOMContentLoaded', () => {
    // 1. Lời chào
    const greetingEl = document.getElementById('greeting');
    if (greetingEl) {
        const hour = new Date().getHours();
        let greeting = 'Chào buổi tối,';
        if (hour < 12) greeting = 'Chào buổi sáng,';
        else if (hour < 18) greeting = 'Chào buổi chiều,';
        greetingEl.textContent = greeting;
    }

    // 2. Typewriter
    const twEl = document.getElementById('typewriter');
    if (twEl) {
        const text = 'Silaphet Thit';
        let i = 0;
        function typeWriter() {
            if (i < text.length) {
                twEl.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        }
        typeWriter();
    }
});
