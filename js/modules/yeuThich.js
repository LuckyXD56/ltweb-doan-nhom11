export function khoiTaoYeuThich() {
  const hienThiDem = document.getElementById("dem-yeu-thich");
  if (!hienThiDem) return;

  function layDanhSach() {
    try {
      return JSON.parse(localStorage.getItem("danhSachYeuThich")) || [];
    } catch {
      return [];
    }
  }

  function capNhatHienThi() {
    const ds = layDanhSach();
    hienThiDem.textContent = ds.length;
  }

  // Ủy quyền sự kiện (Event Delegation) trên toàn bộ body
  document.body.addEventListener("click", (e) => {
    const nut = e.target.closest(".nut-yeu-thich");
    if (!nut) return;

    const idSan = nut.getAttribute("data-id");
    if (!idSan) return;

    const ds = layDanhSach();
    const viTri = ds.indexOf(idSan);

    if (viTri > -1) {
      ds.splice(viTri, 1);
      nut.textContent = "🤍 Yêu thích";
    } else {
      ds.push(idSan);
      nut.textContent = "❤️ Đã thích";
    }

    localStorage.setItem("danhSachYeuThich", JSON.stringify(ds));
    capNhatHienThi();
  });

  capNhatHienThi();
}

export function capNhatNutYeuThich(nut, idSan) {
  try {
    const ds = JSON.parse(localStorage.getItem("danhSachYeuThich")) || [];
    if (ds.includes(idSan.toString())) {
      nut.textContent = "❤️ Đã thích";
    } else {
      nut.textContent = "🤍 Yêu thích";
    }
  } catch {}
}
