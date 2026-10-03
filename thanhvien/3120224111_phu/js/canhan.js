(function () {
      // ==================== CHẾ ĐỘ TỐI ====================
      var nutCheDo = document.getElementById('nut-che-do-toi');
      var bieuTuong = document.getElementById('bieu-tuong-che-do');
      var chuCheDo = document.getElementById('chu-che-do');

      function capNhatNut(toi) {
        bieuTuong.textContent = toi ? '☀️' : '🌙';
        chuCheDo.textContent = toi ? 'Chế độ sáng' : 'Chế độ tối';
      }

      // Khôi phục lựa chọn đã lưu
      var daLuu = localStorage.getItem('che-do-toi') === '1';
      if (daLuu) {
        document.body.classList.add('dark-mode');
      }
      capNhatNut(daLuu);

      nutCheDo.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
        var toi = document.body.classList.contains('dark-mode');
        localStorage.setItem('che-do-toi', toi ? '1' : '0');
        capNhatNut(toi);
      });

      // ==================== COPY EMAIL ====================
      var nutCopy = document.getElementById('nut-copy-email');
      var thongBao = document.getElementById('thong-bao-copy');

      function hienThongBao(text) {
        thongBao.textContent = text;
        thongBao.classList.add('hien');
        setTimeout(function () {
          thongBao.classList.remove('hien');
        }, 2000);
      }

      nutCopy.addEventListener('click', function () {
        var email = nutCopy.getAttribute('data-email');
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(email).then(function () {
            hienThongBao('Đã copy: ' + email);
          }).catch(function () {
            copyFallback(email);
          });
        } else {
          copyFallback(email);
        }
      });

      function copyFallback(text) {
        var tam = document.createElement('textarea');
        tam.value = text;
        tam.style.position = 'fixed';
        tam.style.opacity = '0';
        document.body.appendChild(tam);
        tam.select();
        try {
          document.execCommand('copy');
          hienThongBao('Đã copy: ' + text);
        } catch (e) {
          hienThongBao('Không copy được, email là: ' + text);
        }
        document.body.removeChild(tam);
      }
    })();