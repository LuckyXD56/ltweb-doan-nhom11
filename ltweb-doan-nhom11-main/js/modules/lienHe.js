
const BIEU_THUC_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function khoiTaoLienHe({ form, vungThongBao }) {
  if (!form) return;

  const hoTen = form.elements["ho_ten"];
  const email = form.elements["email"];
  const noiDung = form.elements["noi_dung"];
  const nutGui = form.querySelector('button[type="submit"]');

  const hienThiLoi = (input, spanId, message) => {
    const span = document.getElementById(spanId);
    if (span) span.textContent = message;
    input.setCustomValidity(message);
  };

  const xoaLoi = (input, spanId) => {
    const span = document.getElementById(spanId);
    if (span) span.textContent = "";
    input.setCustomValidity("");
  };

  const kiemTraTruong = (input, spanId, checkFn, msg) => {
    input.addEventListener("blur", () => {
      if (!checkFn(input.value.trim())) {
        hienThiLoi(input, spanId, msg);
      } else {
        xoaLoi(input, spanId);
      }
    });
    input.addEventListener("input", () => {
      xoaLoi(input, spanId);
    });
  };

  kiemTraTruong(hoTen, "loi-ho-ten", val => val.length >= 2, "Vui lòng nhập họ tên (ít nhất 2 ký tự).");
  kiemTraTruong(email, "loi-email", val => BIEU_THUC_EMAIL.test(val), "Vui lòng nhập email đúng định dạng.");
  kiemTraTruong(noiDung, "loi-noi-dung", val => val.length >= 10, "Nội dung liên hệ cần ít nhất 10 ký tự.");

  form.addEventListener("submit", async (suKien) => {
    suKien.preventDefault();
    vungThongBao.textContent = "";
    delete vungThongBao.dataset.trangThai;

    // Trigger blur manually to check all
    hoTen.focus(); hoTen.blur();
    email.focus(); email.blur();
    noiDung.focus(); noiDung.blur();

    if (!form.checkValidity()) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = "Vui lòng sửa các lỗi trên biểu mẫu trước khi gửi.";
      return;
    }

    nutGui.disabled = true;
    nutGui.textContent = "Đang gửi...";

    try {
      const response = await fetch('https://jsonplaceholder.typicode.com/posts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          hoTen: hoTen.value.trim(),
          email: email.value.trim(),
          noiDung: noiDung.value.trim()
        })
      });

      if (!response.ok) throw new Error("Lỗi mạng");

      vungThongBao.dataset.trangThai = "thanh-cong";
      vungThongBao.textContent = Cảm ơn , chúng tôi đã nhận được liên hệ và sẽ phản hồi qua  sớm nhất.;
      form.reset();
    } catch (error) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = "Có lỗi xảy ra khi gửi. Vui lòng thử lại sau.";
    } finally {
      nutGui.disabled = false;
      nutGui.textContent = "Gửi liên hệ";
    }
  });
}
