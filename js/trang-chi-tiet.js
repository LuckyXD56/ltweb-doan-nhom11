import { khoiTaoYeuThich } from "./modules/yeuThich.js";
khoiTaoYeuThich();
import { dinhDangTien } from "./modules/tienIch.js";

document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const sanId = parseInt(urlParams.get('id'));

    const vungThongTin = document.getElementById('thong-tin-san');
    const tieuDeChinh = document.getElementById('chi-tiet-tieu-de');
    if (!vungThongTin) return;

    if (!sanId) {
        vungThongTin.innerHTML = '<p class="chu-canh-giua" style="color: red;">Lỗi: Không tìm thấy mã sân trên URL. Vui lòng quay lại danh sách.</p>';
        document.title = 'Lỗi không tìm thấy sân | Sân Bóng Thắng Lợi';
        tieuDeChinh.textContent = 'Không tìm thấy sân';
        return;
    }

    try {
        const response = await fetch('data/san.json');
        if (!response.ok) throw new Error('Lỗi khi tải dữ liệu json');
        const data = await response.json();
        
        const sanPham = data.find(s => s.id === sanId);

        if (!sanPham) {
            vungThongTin.innerHTML = '<p class="chu-canh-giua" style="color: red;">Không tìm thấy thông tin sân bóng có mã: ' + sanId + '</p>';
            document.title = 'Không tìm thấy | Sân Bóng Thắng Lợi';
            tieuDeChinh.textContent = 'Không tìm thấy';
            return;
        }

        document.title = 'Chi tiết: ' + sanPham.ten + ' | Sân Bóng Thắng Lợi';
        tieuDeChinh.textContent = 'Chi tiết ' + sanPham.ten;
        vungThongTin.innerHTML = '';
        
        const theImgContainer = document.createElement('figure');
        theImgContainer.className = 'khoi-anh';
        const theImg = document.createElement('img');
        theImg.src = sanPham.hinhAnh || 'images/san-a1.jpg';
        theImg.alt = sanPham.ten;
        theImg.className = 'khoi-anh__anh';
        theImgContainer.appendChild(theImg);

        const theMoTa = document.createElement('p');
        theMoTa.className = 'gioi-thieu';
        theMoTa.textContent = sanPham.moTa || 'Sân bóng chất lượng cao.';

        const theUlist = document.createElement('ul');
        theUlist.style.fontSize = '1.2rem';
        theUlist.style.lineHeight = '1.8';
        theUlist.innerHTML = 
            <li><strong>Loại sân:</strong>  người</li>
            <li><strong>Khu vực:</strong> </li>
            <li><strong>Giá thuê:</strong>  VNĐ/giờ</li>
        ;

        const datSanLink = document.createElement('p');
        const nutDat = document.createElement('a');
        nutDat.href = 'dat-san.html?san=' + sanPham.id;
        nutDat.className = 'nut nut--chinh mt-1';
        nutDat.textContent = 'Đặt sân ngay';
        datSanLink.appendChild(nutDat);

        vungThongTin.appendChild(theImgContainer);
        vungThongTin.appendChild(theMoTa);
        vungThongTin.appendChild(theUlist);
        vungThongTin.appendChild(datSanLink);

    } catch (error) {
        console.error(error);
        vungThongTin.innerHTML = '<p class="chu-canh-giua" style="color: red;">Lỗi kết nối máy chủ.</p>';
    }
});
