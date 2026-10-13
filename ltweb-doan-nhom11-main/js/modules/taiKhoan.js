// taiKhoan.js
// Chức năng 4 (Bảng 1): Đăng nhập / Đăng ký — kiểm tra hợp lệ phía client.
// Người phụ trách: ______ (điền tên thành viên)
// Không có API đăng nhập thật nên chỉ mô phỏng phản hồi; trọng tâm là kiểm tra
// hợp lệ và thông báo trạng thái trong vùng aria-live="polite".
// Khi JS tắt: hai form vẫn gửi theo kiểu thông thường (action="#" method="post").

const BIEU_THUC_SDT = /^0[0-9]{9}$/;

function thongBao(vungThongBao, trangThai, noiDung) {
  if (!vungThongBao) return;
  vungThongBao.dataset.trangThai = trangThai;
  vungThongBao.textContent = noiDung;
}

/**
 * Khởi tạo kiểm tra hợp lệ cho form đăng nhập.
 */
export function khoiTaoDangNhap({ form, vungThongBao }) {
  if (!form) return;
  form.addEventListener("submit", (suKien) => {
    suKien.preventDefault();
    const taiKhoan = form.elements["tai_khoan"].value.trim();
    const matKhau = form.elements["mat_khau"].value;

    if (taiKhoan === "" || matKhau === "") {
      thongBao(vungThongBao, "loi", "Vui lòng nhập đầy đủ tài khoản và mật khẩu.");
      return;
    }
    if (matKhau.length < 6) {
      thongBao(vungThongBao, "loi", "Mật khẩu phải có ít nhất 6 ký tự.");
      return;
    }
    thongBao(vungThongBao, "thanh-cong", `Thông tin hợp lệ. Đang chuyển hướng cho tài khoản "${taiKhoan}"…`);
  });
}

/**
 * Khởi tạo kiểm tra hợp lệ cho form đăng ký.
 */
export function khoiTaoDangKy({ form, vungThongBao }) {
  if (!form) return;
  form.addEventListener("submit", (suKien) => {
    suKien.preventDefault();
    const hoTen = form.elements["ho_ten"].value.trim();
    const email = form.elements["email"].value.trim();
    const soDienThoai = form.elements["so_dien_thoai"].value.trim();
    const matKhau = form.elements["mat_khau"].value;
    const xacNhanMatKhau = form.elements["xac_nhan_mat_khau"].value;
    const dongYDieuKhoan = form.elements["dong_y_dieu_khoan"].checked;

    if (hoTen.length < 2) {
      thongBao(vungThongBao, "loi", "Họ và tên phải có ít nhất 2 ký tự.");
      return;
    }
    if (!BIEU_THUC_SDT.test(soDienThoai)) {
      thongBao(vungThongBao, "loi", "Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.");
      return;
    }
    if (matKhau.length < 6) {
      thongBao(vungThongBao, "loi", "Mật khẩu phải có ít nhất 6 ký tự.");
      return;
    }
    if (matKhau !== xacNhanMatKhau) {
      thongBao(vungThongBao, "loi", "Xác nhận mật khẩu không khớp.");
      return;
    }
    if (!dongYDieuKhoan) {
      thongBao(vungThongBao, "loi", "Vui lòng đồng ý với điều khoản sử dụng dịch vụ.");
      return;
    }

    thongBao(vungThongBao, "thanh-cong", `Tạo tài khoản cho "${hoTen}" (${email}) thành công. Vui lòng đăng nhập để tiếp tục.`);
    form.reset();
  });
}
