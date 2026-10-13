/**
 * trangChu.js — Logic trang chủ
 * - Khối "Sân nổi bật": fetch data/san.json, sắp xếp theo giá hoặc đánh giá
 * - Khối "Thời tiết": gọi Open-Meteo API công khai
 *
 * Người phụ trách: (điền tên thành viên)
 */

import { layDuLieuJSON, hienDangTai, hienLoi, hienRong, xoaThongBao, dinhDangTien } from "./tienIch.js";

const DUONG_DAN_DU_LIEU = "data/san.json";
let danhSachSan = [];

function taoTheSan(san) {
  const the = document.createElement("li");
  the.className = "the";

  const tieuDe = document.createElement("strong");
  tieuDe.textContent = san.ten + " - sân " + san.loaiSan + " người";
  the.appendChild(tieuDe);

  const anh = document.createElement("img");
  anh.src = san.hinhAnh;
  anh.alt = "Hình ảnh " + san.ten;
  anh.width = 280;
  anh.height = 158;
  anh.loading = "lazy";
  the.appendChild(anh);

  const gia = document.createElement("p");
  gia.textContent = dinhDangTien(san.giaThuong) + "/giờ · " + san.khuVuc + " · " + san.danhGia.toFixed(1) + "★";
  the.appendChild(gia);

  return the;
}

/**
 * Khởi tạo khối "Sân nổi bật" trên trang chủ.
 */
export async function khoiTaoTrangChu({ danhSachNoiBat, vungThongBao, oSapXep }) {
  if (!danhSachNoiBat) return;

  function sapXep(tieuChi) {
    const banSaoChep = [...danhSachSan].filter((san) => san.dangHoatDong);
    if (tieuChi === "danh-gia") {
      banSaoChep.sort((a, b) => b.danhGia - a.danhGia);
    } else {
      banSaoChep.sort((a, b) => a.giaThuong - b.giaThuong);
    }
    return banSaoChep.slice(0, 3);
  }

  function ve() {
    const tieuChi = oSapXep ? oSapXep.value : "gia";
    const noiBat = sapXep(tieuChi);
    danhSachNoiBat.replaceChildren();

    if (noiBat.length === 0) {
      hienRong(vungThongBao, "Hiện chưa có sân nào để gợi ý.");
      return;
    }

    xoaThongBao(vungThongBao);
    noiBat.forEach((san) => danhSachNoiBat.appendChild(taoTheSan(san)));
  }

  async function taiDuLieu() {
    hienDangTai(vungThongBao, "Đang tải sân nổi bật…");
    try {
      danhSachSan = await layDuLieuJSON(DUONG_DAN_DU_LIEU);
      ve();
    } catch (loi) {
      hienLoi(vungThongBao, "Không tải được sân nổi bật: " + loi.message, taiDuLieu);
    }
  }

  if (oSapXep) oSapXep.addEventListener("change", ve);
  await taiDuLieu();
}

/**
 * Khởi tạo khối "Thời tiết Đà Nẵng" — dùng createElement để tránh lỗi ký tự ẩn.
 */
export async function khoiTaoThoiTiet() {
  const widget = document.getElementById("thoi-tiet-widget");
  if (!widget) return;

  try {
    const url = "https://api.open-meteo.com/v1/forecast?latitude=16.05&longitude=108.2&current=temperature_2m,relative_humidity_2m";
    const res = await fetch(url);
    if (!res.ok) throw new Error("API Error");

    const data = await res.json();
    const nhietDo = data.current.temperature_2m;
    const doAm = data.current.relative_humidity_2m;

    widget.replaceChildren();

    const p = document.createElement("p");
    p.style.fontSize = "1.2rem";
    p.textContent = "🌡️ Nhiệt độ: " + nhietDo + "°C  |  💧 Độ ẩm: " + doAm + "%";
    widget.appendChild(p);

  } catch (error) {
    widget.replaceChildren();

    const p = document.createElement("p");
    p.style.color = "red";
    p.textContent = "Không thể tải thời tiết lúc này.";
    widget.appendChild(p);
  }
}