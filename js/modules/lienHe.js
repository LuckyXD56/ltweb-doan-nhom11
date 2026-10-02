// lienHe.js
// Chức năng 5 (Bảng 1): Biểu mẫu liên hệ (gửi + nâng cấp dần).
// Người phụ trách: ______ (điền tên thành viên)
// Khi JS tắt: form vẫn gửi theo kiểu thông thường nhờ action="#" method="post".
// Khi JS bật: chặn submit mặc định, kiểm tra hợp lệ, hiển thị kết quả gửi
// trong vùng aria-live="polite" để trình đọc màn hình đọc được thông báo mới.

const BIEU_THUC_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function khoiTaoLienHe({ form, vungThongBao }) {
  if (!form) return;

  form.addEventListener("submit", (suKien) => {
    suKien.preventDefault();
    vungThongBao.textContent = "";
    delete vungThongBao.dataset.trangThai;

    const hoTen = form.elements["ho_ten"].value.trim();
    const email = form.elements["email"].value.trim();
    const noiDung = form.elements["noi_dung"].value.trim();

    if (hoTen.length < 2) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = "Vui lòng nhập họ tên (ít nhất 2 ký tự).";
      return;
    }
    if (!BIEU_THUC_EMAIL.test(email)) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = "Vui lòng nhập email đúng định dạng.";
      return;
    }
    if (noiDung.length < 10) {
      vungThongBao.dataset.trangThai = "loi";
      vungThongBao.textContent = "Nội dung liên hệ cần ít nhất 10 ký tự.";
      return;
    }

    vungThongBao.dataset.trangThai = "thanh-cong";
    vungThongBao.textContent = `Cảm ơn ${hoTen}, chúng tôi đã nhận được nội dung liên hệ và sẽ phản hồi qua ${email} sớm nhất.`;
    form.reset();
  });
}
