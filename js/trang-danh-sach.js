// Tệp: trang-danh-sach.js (Phụ trách: Phimmasone Khamphouvanh - 3120224192)
// Chức năng: 
// - Tải danh sách sân từ san-bong.json
// - Render danh sách ra HTML
// - Tìm kiếm theo tên (gõ không dấu vẫn tìm được)
// - Sắp xếp theo giá, tên
// - Xử lý 3 trạng thái: Đang tải, Lỗi, Rỗng.

import { taiJSON } from './api.js';

const container = document.getElementById('danh-sach-san-container');
const loadingState = document.getElementById('loading-state');
const errorState = document.getElementById('error-state');
const emptyState = document.getElementById('empty-state');
const btnRetry = document.getElementById('btn-retry');

const searchInput = document.getElementById('search-input');
const sortSelect = document.getElementById('sort-select');

let tatCaSan = [];

function boDauTiengViet(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
}

function renderSan(danhSach) {
    container.innerHTML = ''; // Clear cũ
    if (danhSach.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.style.display = 'grid'; // .luoi-san đã có display: grid trong css

    danhSach.forEach(san => {
        const the = document.createElement('article');
        the.className = 'the-san';
        
        the.innerHTML = 
            <img class="the__anh" src=" + san.hinh_anh + " alt="Hình ảnh  + san.ten + " loading="lazy">
            <div class="the__noi-dung">
                <h3 class="the__tieu-de"> + san.ten + </h3>
                <p style="margin: 0.5rem 0;"><strong>Khu vực:</strong>  + san.khu_vuc + </p>
                <p style="margin: 0.5rem 0;"><strong>Sức chứa:</strong>  + san.so_nguoi +  người</p>
                <p style="margin: 0.5rem 0;">Giá: <span> + san.gia_truoc_17h.toLocaleString('vi-VN') + đ/h</span></p>
                <a href="chi-tiet.html?id= + san.id + " class="nut nut--rong">Xem chi tiết</a>
            </div>
        ;
        container.appendChild(the);
    });
}

function xuLyBoLocVaSapXep() {
    const tuKhoa = boDauTiengViet(searchInput.value);
    const kieuSapXep = sortSelect.value;
    
    // 1. Lọc theo tên
    let dsLoc = tatCaSan.filter(san => {
        return boDauTiengViet(san.ten).includes(tuKhoa) || boDauTiengViet(san.khu_vuc).includes(tuKhoa);
    });
    
    // 2. Sắp xếp
    if (kieuSapXep === 'gia-asc') {
        dsLoc.sort((a, b) => a.gia_truoc_17h - b.gia_truoc_17h);
    } else if (kieuSapXep === 'gia-desc') {
        dsLoc.sort((a, b) => b.gia_truoc_17h - a.gia_truoc_17h);
    } else if (kieuSapXep === 'ten-asc') {
        dsLoc.sort((a, b) => a.ten.localeCompare(b.ten, 'vi'));
    }
    
    renderSan(dsLoc);
}

async function taiDuLieu() {
    loadingState.style.display = 'block';
    errorState.style.display = 'none';
    container.style.display = 'none';
    emptyState.style.display = 'none';
    
    try {
        tatCaSan = await taiJSON('data/san-bong.json');
        loadingState.style.display = 'none';
        xuLyBoLocVaSapXep(); // Render lần đầu
    } catch (error) {
        loadingState.style.display = 'none';
        errorState.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (searchInput && sortSelect && btnRetry) {
        searchInput.addEventListener('input', xuLyBoLocVaSapXep);
        sortSelect.addEventListener('change', xuLyBoLocVaSapXep);
        btnRetry.addEventListener('click', taiDuLieu);
        taiDuLieu();
    }
});

