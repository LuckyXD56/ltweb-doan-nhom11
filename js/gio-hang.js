/**
 * js/gio-hang.js â€” Xá»­ lÃ½ thÃªm vÃ o giá»  hÃ ng qua AJAX fetch vá»›i header X-CSRF-Token (Slide 24)
 */

document.addEventListener('DOMContentLoaded', () => {
  const metaCsrf = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = metaCsrf ? metaCsrf.getAttribute('content') : '';

  document.querySelectorAll('form[action*="them-gio.php"]').forEach(form => {
    const actionInput = form.querySelector('input[name="action"]');
    if (actionInput && actionInput.value === 'them') {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const idInput = form.querySelector('input[name="id"]');
        const slInput = form.querySelector('input[name="so_luong"]');
        const id = idInput ? idInput.value : '';
        const soLuong = slInput ? parseInt(slInput.value, 10) : 1;
        const btn = form.querySelector('button[type="submit"]');
        const origText = btn ? btn.textContent : '';

        if (btn) {
          btn.disabled = true;
          btn.textContent = 'Đang thêm...';
        }

        try {
          const res = await fetch('api/gio-hang.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
              action: 'them',
              id: id,
              so_luong: soLuong
            })
          });

          const data = await res.json();
          if (res.ok && data.status === 'success') {
            if (btn) btn.textContent = '✓ Đã thêm!';
            // Cập nhật số lượng trên menu giỏ hàng nếu có
            const menuGio = document.querySelector('a[href*="gio-hang.php"]');
            if (menuGio && data.tong_so_luong !== undefined) {
              menuGio.textContent = `Giỏ hàng (${data.tong_so_luong})`;
            }
            setTimeout(() => {
              if (btn) {
                btn.disabled = false;
                btn.textContent = origText;
              }
            }, 1500);
          } else {
            alert(data.loi || data.message || 'Không thể thêm vào giỏ hàng.');
            if (btn) {
              btn.disabled = false;
              btn.textContent = origText;
            }
          }
        } catch (err) {
          // Dự phòng lỗi: submit thông thường
          form.submit();
        }
      });
    }
  });
});
