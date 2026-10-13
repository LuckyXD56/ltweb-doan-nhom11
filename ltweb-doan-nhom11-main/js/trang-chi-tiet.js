/**
 * trang-chi-tiet.js — Trang chi tiết sân bóng
 *
 * Đọc ?id= trên URL (dạng string, ví dụ "A1"), tìm sân tương ứng trong
 * data/san.json và render thông tin. Đổi document.title theo tên sân.
 *
 * Cấu trúc san.json: { id, ten, loaiSan, khuVuc, giaThuong, giaVang,
 *                       danhGia, dangHoatDong, moTa, hinhAnh }
 */

import { khoiTaoYeuThich } from "./modules/yeuThich.js";
import { dinhDangTien } from "./modules/tienIch.js";

khoiTaoYeuThich();

document.addEventListener("DOMContentLoaded", async () => {

  // ---------- 1. Đọc ?id= trên URL ----------
  const urlParams = new URLSearchParams(window.location.search);
  const sanId = urlParams.get("id");   // ← giữ string, KHÔNG parseInt

  const vungThongTin = document.getElementById("thong-tin-san");
  const tieuDeChinh = document.getElementById("chi-tiet-tieu-de");

  if (!vungThongTin) return;

  // ---------- 2. Không có ?id= trên URL ----------
  if (!sanId) {
    vungThongTin.innerHTML =
      '<p class="chu-canh-giua" style="color: red;">' +
      '❌ Lỗi: Không tìm thấy mã sân trên URL. Vui lòng quay lại danh sách.' +
      '</p>';
    document.title = "Lỗi: Không tìm thấy sân | Sân Bóng Thắng Lợi";
    if (tieuDeChinh) tieuDeChinh.textContent = "Không tìm thấy sân";
    return;
  }

  // ---------- 3. Tải dữ liệu ----------
  try {
    const response = await fetch("data/san.json");
    if (!response.ok) throw new Error("Lỗi khi tải dữ liệu json");

    const data = await response.json();
    const sanPham = data.find(s => s.id === sanId);

    // ---------- 4. Không tìm thấy sân ----------
    if (!sanPham) {
      vungThongTin.innerHTML =
        '<p class="chu-canh-giua" style="color: red;">' +
        '❌ Không tìm thấy thông tin sân bóng có mã: <strong>' + sanId + '</strong>' +
        '</p>';
      document.title = "Không tìm thấy | Sân Bóng Thắng Lợi";
      if (tieuDeChinh) tieuDeChinh.textContent = "Không tìm thấy";
      return;
    }

    // ---------- 5. Cập nhật tiêu đề ----------
    document.title = "Chi tiết " + sanPham.ten + " | Sân Bóng Thắng Lợi";
    if (tieuDeChinh) tieuDeChinh.textContent = "Chi tiết " + sanPham.ten;

    // ---------- 6. Render HTML bằng DOM API ----------
    vungThongTin.innerHTML = "";

    // 6.1. Ảnh
    const theImgContainer = document.createElement("figure");
    theImgContainer.className = "khoi-anh";

    const theImg = document.createElement("img");
    theImg.src = sanPham.hinhAnh || "images/san-a1.jpg";
    theImg.alt = sanPham.ten;
    theImg.className = "khoi-anh__anh";
    theImg.width = 800;
    theImg.height = 400;
    theImg.loading = "lazy";

    theImgContainer.appendChild(theImg);

    // 6.2. Mô tả
    const theMoTa = document.createElement("p");
    theMoTa.className = "gioi-thieu";
    theMoTa.textContent = sanPham.moTa || "Sân bóng chất lượng cao.";

    // 6.3. Danh sách thông số
    const theUlist = document.createElement("ul");
    theUlist.style.fontSize = "1.2rem";
    theUlist.style.lineHeight = "1.8";
    theUlist.innerHTML =
      "<li><strong>Mã sân:</strong> " + sanPham.id + "</li>" +
      "<li><strong>Loại sân:</strong> " + sanPham.loaiSan + " người</li>" +
      "<li><strong>Khu vực:</strong> " + (sanPham.khuVuc || "—") + "</li>" +
      "<li><strong>Giá giờ thường:</strong> " + dinhDangTien(sanPham.giaThuong) + " VNĐ/giờ</li>" +
      "<li><strong>Giá giờ vàng:</strong> " + dinhDangTien(sanPham.giaVang) + " VNĐ/giờ</li>" +
      "<li><strong>Đánh giá:</strong> " + (sanPham.danhGia ? sanPham.danhGia + "/5" : "—") + "</li>" +
      "<li><strong>Trạng thái:</strong> " + (sanPham.dangHoatDong ? "Đang hoạt động" : "Tạm đóng") + "</li>";

    // 6.4. Nút đặt sân
    const datSanLink = document.createElement("p");
    datSanLink.className = "mt-1";

    const nutDat = document.createElement("a");
    nutDat.href = "dat-san.html?san=" + sanPham.id;
    nutDat.className = "nut nut--chinh";
    nutDat.textContent = "Đặt sân này";

    const nutQuayLai = document.createElement("a");
    nutQuayLai.href = "danh-sach.html";
    nutQuayLai.className = "nut nut--phu";
    nutQuayLai.textContent = "← Về danh sách sân";
    nutQuayLai.style.marginLeft = "0.5rem";

    datSanLink.appendChild(nutDat);
    datSanLink.appendChild(nutQuayLai);

    // 6.5. Gắn vào vùng thông tin
    vungThongTin.appendChild(theImgContainer);
    vungThongTin.appendChild(theMoTa);
    vungThongTin.appendChild(theUlist);
    vungThongTin.appendChild(datSanLink);

  } catch (error) {
    console.error(error);
    vungThongTin.innerHTML =
      '<p class="chu-canh-giua" style="color: red;">' +
      '❌ Lỗi kết nối máy chủ.' +
      '</p>';
    document.title = "Lỗi | Sân Bóng Thắng Lợi";
    if (tieuDeChinh) tieuDeChinh.textContent = "Lỗi tải dữ liệu";
  }

});