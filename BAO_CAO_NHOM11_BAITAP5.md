# TRƯỜNG ĐẠI HỌC SƯ PHẠM – ĐẠI HỌC ĐÀ NẴNG
## KHOA TOÁN – TIN
### HỌC PHẦN: THIẾT KẾ VÀ LẬP TRÌNH WEB (HỌC KỲ 1, NĂM HỌC 2026 – 2027)

---

# BÁO CÁO BÀI TẬP THỰC HÀNH NHÓM SỐ 5
## CHƯƠNG 5: LẬP TRÌNH PHP PHÍA MÁY CHỦ
**Đề tài: Chuyển website Đồ án Đặt sân bóng đá Thắng Lợi thành website động bằng PHP 8**

- **Nhóm thực hiện:** Nhóm 11
- **Kho GitHub:** [https://github.com/LuckyXD56/ltweb-doan-nhom11](https://github.com/LuckyXD56/ltweb-doan-nhom11)
- **Mã commit cuối:** `a8a40b2`
- **Môi trường chạy thử:** PHP 8.2+, Composer 2.x, Windows 11 / PowerShell

### Danh sách thành viên nhóm:
| STT | Họ và tên | Mã sinh viên | Vai trò | Tỷ lệ đóng góp |
|:---:|---|:---:|:---:|:---:|
| 1 | **Phimmasone Khamphouvanh** | **3120224192** | **Trưởng nhóm** | **100%** |
| 2 | Nguyễn Quý Minh Hoàng | 3120224060 | Thành viên | 100% |
| 3 | Lê Nguyễn Gia Nhân | 3120224105 | Thành viên | 100% |
| 4 | Silaphet Thit | 3120224188 | Thành viên | 100% |
| 5 | Trương Nguyễn Ngọc Phú | 3120224111 | Thành viên | 100% |

---

# PHẦN A – WEBSITE ĐỘNG BẰNG PHP CHO ĐỒ ÁN (5,0 ĐIỂM)

## 1. Bảng 1. Sáu chức năng bắt buộc của website nhóm

| TT | Trang | Chức năng | Kỹ thuật bắt buộc | Tệp PHP | Người phụ trách |
|:--:|---|---|---|---|---|
| 1 | Mọi trang | Khung trang dùng chung: header (menu đánh dấu trang đang xem qua `aria-current="page"`, số món trong giỏ, trạng thái đăng nhập), footer; trang 404, 500 thân thiện kèm đúng mã HTTP status | `inc/config.php` nạp đầu tiên; `require` + `__DIR__`; biến `$tieuDe`, `$trang`, `$goc`; `404.php`, `500.php` | `inc/header.php`<br>`inc/footer.php`<br>`inc/config.php`<br>`404.php`, `500.php` | Trương Nguyễn Ngọc Phú |
| 2 | `danh-sach.php` | Danh sách sân bóng do PHP sinh từ `data/san.json`; biểu mẫu GET: tìm theo tên/khu vực (không dấu), lọc theo loại sân, sắp xếp theo giá tăng/giảm; giữ lại lựa chọn trên input; thông báo khi không có kết quả | Danh mục, kiểu sắp xếp thuộc whitelist cho phép; `array_filter`, `usort`, `mb_stripos`; URL chia sẻ được: `?q=san&loai=7&sx=gia-tang` | `danh-sach.php`<br>`src/Data/KhoSanPham.php` | Trương Nguyễn Ngọc Phú & Lê Nguyễn Gia Nhân |
| 3 | `chi-tiet.php`<br>`index.php` | Chi tiết theo `?id=`, id sai hoặc thiếu thì trả về 404; nút "Đặt sân / Thêm vào giỏ"; cookie nhớ 4 sân xem gần nhất; trang chủ hiện khối "Đã xem gần đây" | `filter_var` kiểm tra id là số nguyên; gọi `KhoSanPham`; `setcookie()` gọi trước mọi output, `httponly`, `samesite=Lax`; đọc cookie kiểm tra lại | `chi-tiet.php`<br>`index.php`<br>`src/Data/KhoSanPham.php` | Lê Nguyễn Gia Nhân |
| 4 | `lien-he.php` | Biểu mẫu liên hệ: kiểm tra mọi ô ở máy chủ, báo lỗi dưới từng ô, điền lại dữ liệu cũ; đính kèm ảnh ≤ 2 MB; lưu vào `storage/lien-he.jsonl`; PRG kèm flash message | Mảng `$du`, `$loi`; `filter_var`, `preg_match`, `mb_strlen`; kiểm tra MIME type bằng `finfo`, đặt tên ảnh ngẫu nhiên lưu `uploads/`; Post/Redirect/Get | `lien-he.php`<br>`inc/xu-ly-lien-he.php`<br>`src/Data/KhoLienHe.php` | Silaphet Thit |
| 5 | `gio-hang.php`<br>`them-gio.php` | Giỏ hàng đặt sân trong session: thêm sân, đổi số lượng khung giờ, xóa 1 sân, xóa hết giỏ hàng; tính thành tiền từng sân và tổng tiền cả giỏ | Lớp `GioHang` bọc `$_SESSION['gio'] = [id => sl]`; kiểm tra số lượng min-max; id phải tồn tại trong kho; giá tiền lấy từ dữ liệu gốc; PRG | `gio-hang.php`<br>`them-gio.php`<br>`src/Services/GioHang.php` | Trương Nguyễn Ngọc Phú & Phimmasone Khamphouvanh |
| 6 | `dang-nhap.php`<br>`dang-xuat.php`<br>`quan-tri.php` | Đăng nhập tài khoản quản trị đơn giản; trang quản trị bảo vệ hiển thị danh sách liên hệ đã nhận kèm ảnh đính kèm (mới nhất trước); đăng xuất an toàn | `password_verify` với mật khẩu băm trong `inc/tai-khoan.php`; `session_regenerate_id(true)`; `inc/bao-ve.php`; ghi log khi đăng nhập sai | `dang-nhap.php`<br>`dang-xuat.php`<br>`quan-tri.php`<br>`inc/bao-ve.php`<br>`inc/tai-khoan.php` | Nguyễn Quý Minh Hoàng |

---

## 2. Bảng 2. Các lớp PHP của nhóm (Hướng đối tượng & PSR-4 Autoloading)

Nhóm cấu hình PSR-4 trong `composer.json` ánh xạ namespace `App\` vào thư mục `src/`, tuân thủ chuẩn OOP PHP 8:

| TT | Lớp (tên đầy đủ) và tệp | Thuộc tính, phương thức chính | Dùng ở trang |
|:--:|---|---|---|
| 1 | `App\Models\SanPham`<br>`src/Models/SanPham.php` | - Thuộc tính `readonly`: `$id`, `$ten`, `$loaiSan`, `$giaThuong`, `$giaCaoDiem`, `$hinhAnh`, `$moTa`, `$khuVuc`<br>- `public static function tuMang(array $d): self` – Khởi tạo đối tượng `SanPham` từ mảng trích xuất JSON | `index.php`, `danh-sach.php`, `chi-tiet.php`, `gio-hang.php` |
| 2 | `App\Data\KhoSanPham`<br>`src/Data/KhoSanPham.php` | - `private ?array $ds = null`<br>- `public function tatCa(): array`<br>- `public function timTheoId(string $id): ?SanPham`<br>- `public function timKiem(string $tuKhoa, int $loaiSan): array`<br>- `public function sapXep(array $ds, string $tieuChi): array` | `index.php`, `danh-sach.php`, `chi-tiet.php`, `gio-hang.php` |
| 3 | `App\Services\GioHang`<br>`src/Services/GioHang.php` | - Quản lý `$_SESSION['gio']`<br>- `public function them(string $idSan, int $sl = 1): bool`<br>- `public function doiSoLuong(string $idSan, int $sl): void`<br>- `public function xoaMot(string $idSan): void`<br>- `public function xoaHet(): void`<br>- `public function soMon(): int`<br>- `public function chiTiet(KhoSanPham $kho): array`<br>- `public function tongTien(KhoSanPham $kho): int` | `gio-hang.php`, `them-gio.php`, `inc/header.php` |
| 4 | `App\Data\KhoLienHe`<br>`src/Data/KhoLienHe.php` | - `public function __construct(private string $tepJsonl)`<br>- `public function them(array $lh): void` (ghi tệp dạng JSON Lines có `LOCK_EX`)<br>- `public function tatCa(): array` (đọc tệp trả về mảng đảo ngược theo thời gian) | `lien-he.php`, `quan-tri.php` |

---

## 3. Mô tả cách hoạt động của từng chức năng chính

### 3.1. Khung trang dùng chung (Header, Footer, Config, 404, 500)
- Tệp `inc/config.php` được gọi đầu tiên ở mọi trang bằng `require_once __DIR__ . '/inc/config.php'`. Tệp này nạp Composer Autoload (`vendor/autoload.php`), hàm tiện ích `inc/ham.php`, khởi động phiên làm việc `session_start()`, cấu hình `error_reporting(E_ALL)` và thiết lập `set_exception_handler()` để bắt mọi lỗi ngoại lệ chưa xử lý ghi vào `logs/php-error.log`.
- `inc/header.php` và `inc/footer.php` quản lý khung giao diện chung. `header.php` tự động đối chiếu biến `$trang` để gắn `aria-current="page"` vào mục menu tương ứng, gọi đối tượng `GioHang` để hiển thị số lượng mục trên thanh điều hướng, và kiểm tra `$_SESSION['admin_logged']` để hiển thị liên kết "Đăng xuất" hoặc "Đăng nhập quản trị".
- Biến `$goc` giúp các trang nằm trong thư mục con (`thanhvien/*/gioithieu.php`) trỏ chính xác về thư mục gốc (`$goc = '../../'`).
- Trang `404.php` và `500.php` thiết lập đúng HTTP Status Code qua `http_response_code(404)` và `http_response_code(500)`.

*(Hình minh họa 1: Giao diện Khung trang dùng chung Header/Footer và trang 404 thân thiện)*

### 3.2. Danh sách sân bóng động (Tìm kiếm, Lọc, Sắp xếp)
- Trang `danh-sach.php` nhận các tham số qua phương thức GET: `q` (từ khóa tìm kiếm), `loai` (loại sân: 5, 7, 11 người), và `sx` (tiêu chí sắp xếp: `gia-tang`, `gia-giam`, `ten-az`).
- Mọi tham số đều được kiểm tra tính hợp lệ bằng Whitelist: nếu `loai` hoặc `sx` chứa giá trị nằm ngoài danh mục quy định, hệ thống tự động gán về mặc định an toàn.
- Dữ liệu sân bóng được đọc duy nhất thông qua lớp `KhoSanPham`. Hàm lọc sử dụng `mb_stripos` kết hợp hàm chuẩn hóa bỏ dấu tiếng Việt giúp người dùng tìm kiếm chính xác kể cả khi không gõ dấu.
- Khi không có sân nào khớp tiêu chí, trang hiển thị thông báo thân thiện "Không tìm thấy sân bóng nào phù hợp" kèm liên kết Đặt lại bộ lọc.

*(Hình minh họa 2: Giao diện Trang danh sách sân bóng kèm bộ lọc GET và giữ lại giá trị đã nhập)*

### 3.3. Trang chi tiết và Cookie "Đã xem gần đây"
- `chi-tiet.php` đọc tham số `id` từ URL qua `$_GET['id']`. Hệ thống kiểm tra ID bằng `KhoSanPham::timTheoId()`. Nếu không tìm thấy hoặc người dùng nhập ID giả mạo (`chi-tiet.php?id=abc`), hệ thống gửi mã `http_response_code(404)` và hiển thị giao diện `404.php`.
- Trước khi xuất ra bất kỳ thẻ HTML nào, trang thiết lập cookie `da_xem` lưu trữ danh sách tối đa 4 ID sân xem gần nhất bằng lệnh `setcookie()` với các cờ bảo mật: `expires = 30 ngày`, `path = '/'`, `httponly = true`, `samesite = 'Lax'`.
- Ở `index.php`, hệ thống giải mã cookie `da_xem`, kiểm tra từng ID qua `KhoSanPham` (phòng trường hợp người dùng sửa cookie thủ công) và render khối "Sân bóng bạn đã xem gần đây".

*(Hình minh họa 3: Giao diện Trang chi tiết sân bóng và khối "Đã xem gần đây" ở trang chủ)*

### 3.4. Biểu mẫu liên hệ an toàn, Upload ảnh và PRG Pattern
- Biểu mẫu `lien-he.php` gửi dữ liệu bằng phương thức POST. Phía máy chủ kiểm tra toàn bộ các trường: họ tên, email (qua `FILTER_VALIDATE_EMAIL`), số điện thoại (qua biểu thức chính quy `preg_match('/^[0-9]{10,11}$/')`), và nội dung.
- **Xử lý Upload ảnh an toàn:** Kiểm tra kích thước không vượt quá 2 MB (`$_FILES['dinhkem']['size'] <= 2 * 1024 * 1024`), kiểm tra MIME type thật thông qua `finfo_open(FILEINFO_MIME_TYPE)` để đảm bảo tệp thực sự là `image/jpeg`, `image/png`, hoặc `image/webp`. Đổi tên tệp thành chuỗi ngẫu nhiên `bin2hex(random_bytes(8))` để ngăn chặn ghi đè tệp và tấn công tải mã độc.
- Áp dụng mô hình **Post/Redirect/Get (PRG)**: Sau khi lưu thông tin vào `storage/lien-he.jsonl` (sử dụng cờ `FILE_APPEND | LOCK_EX`), máy chủ lưu thông báo vào `$_SESSION['flash']` và chuyển hướng trình duyệt về `lien-he.php` bằng `header('Location: lien-he.php')`. Điều này ngăn chặn triệt để tình trạng người dùng bấm F5 gây gửi lặp biểu mẫu.

*(Hình minh họa 4: Giao diện Biểu mẫu liên hệ báo lỗi từng ô và Giao diện gửi thành công với Flash Message)*

### 3.5. Giỏ hàng / Đặt sân trong Session
- Lớp `App\Services\GioHang` đóng gói toàn bộ logic lưu trữ giỏ hàng bên trong `$_SESSION['gio']`.
- Tệp `them-gio.php` nhận yêu cầu thêm sân hoặc cập nhật số lượng qua POST, kiểm tra số lượng phải nằm trong khoảng hợp lệ từ 1 đến 10, ID sân phải tồn tại trong `KhoSanPham`. Giá tiền của sân được lấy trực tiếp từ `KhoSanPham`, tuyệt đối không lấy từ dữ liệu do trình duyệt gửi lên form.
- Trang `gio-hang.php` cho phép thay đổi số lượng, xóa từng mục hoặc xóa toàn bộ giỏ hàng, đồng thời tự động tính tổng tiền chính xác.

*(Hình minh họa 5: Giao diện Giỏ hàng / Đặt khung giờ sân bóng trong Session)*

### 3.6. Đăng nhập quản trị, Bảo vệ phiên và Xem liên hệ
- Mật khẩu quản trị được băm an toàn bằng `password_hash()` với thuật toán `PASSWORD_DEFAULT` lưu trong `inc/tai-khoan.php`.
- Khi người dùng đăng nhập tại `dang-nhap.php`, mật khẩu được kiểm tra bằng `password_verify()`. Khi xác thực thành công, máy chủ gọi `session_regenerate_id(true)` để cấp mã phiên mới nhằm chống tấn công Session Fixation.
- Nếu đăng nhập thất bại, hệ thống ghi log thời gian, địa chỉ IP và tài khoản vào tệp `storage/dang-nhap-sai.jsonl` để theo dõi rà soát an ninh.
- Trang `quan-tri.php` được bảo vệ bằng tệp `inc/bao-ve.php`: nếu chưa đăng nhập, tự động chuyển hướng về `dang-nhap.php`. Trang quản trị liệt kê danh sách tất cả các liên hệ từ `KhoLienHe` kèm hình ảnh đính kèm, sắp xếp theo thứ tự mới nhất hiển thị trước.

*(Hình minh họa 6: Giao diện Đăng nhập quản trị và Trang Quản trị xem danh sách liên hệ)*

---

## 4. Bảng 3. Kiểm thử phía máy chủ (Server-side Testing)

| TT | Ca kiểm thử (cách làm) | Kết quả mong đợi | Kết quả thực tế | Đạt? |
|:--:|---|---|---|:---:|
| 1 | **Liên hệ:** tắt JavaScript (thêm `novalidate` vào thẻ `<form>`), để trống mọi ô rồi gửi | Hiện lại biểu mẫu, báo lỗi dưới từng ô bắt buộc; không lưu gì vào tệp | Biểu mẫu hiện lại ngay lập tức, hiển thị thông báo lỗi đỏ dưới từng ô Họ tên, Email, Nội dung. Tệp `storage/lien-he.jsonl` không ghi thêm dòng nào. | **Đạt** |
| 2 | **Liên hệ:** nhập email `"lan@ued"`, số điện thoại `"abc"`, các ô khác hợp lệ | Báo lỗi đúng hai ô đó; các ô khác giữ nguyên giá trị đã nhập | Báo lỗi "Email không đúng định dạng" và "Số điện thoại chỉ được chứa 10-11 chữ số"; ô Họ tên và Nội dung vẫn giữ nguyên giá trị cũ. | **Đạt** |
| 3 | **Liên hệ:** đính kèm tệp `gia-mao.jpg` (tệp văn bản chứa mã `<?php phpinfo(); ?>` được đổi đuôi `.jpg`) | Từ chối tệp, báo lỗi chỉ nhận ảnh JPG, PNG, WebP; `uploads/` không có tệp mới | Máy chủ dùng `finfo` kiểm tra MIME type trả về `text/plain`, từ chối lưu tệp và báo lỗi "Tệp tải lên không phải là ảnh hợp lệ". Thư mục `uploads/` không sinh tệp mới. | **Đạt** |
| 4 | **Liên hệ:** đính kèm một ảnh có dung lượng lớn hơn 2 MB | Báo lỗi kích thước tệp vượt quá 2 MB, không có Warning; các ô khác giữ nguyên | Hiển thị thông báo "Dung lượng ảnh không được vượt quá 2 MB", không phát sinh bất kỳ Warning nào của PHP; các ô dữ liệu khác được điền lại đầy đủ. | **Đạt** |
| 5 | Gửi một liên hệ hợp lệ, rồi bấm F5 ở trang hiện ra sau đó | `storage/lien-he.jsonl` chỉ thêm đúng một dòng; thông báo "đã gửi" hiện đúng một lần | Máy chủ thực hiện đúng Post/Redirect/Get (mã HTTP 302 chuyển hướng về `lien-he.php`). Bấm F5 trình duyệt tải lại trang GET bình thường, thông báo Flash Message biến mất sau khi F5, không bị trùng dữ liệu. | **Đạt** |
| 6 | Nhập chuỗi `"><script>alert(1)</script>` vào ô tìm kiếm và ô nội dung liên hệ; xem `danh-sach.php` và `quan-tri.php` | Chuỗi hiển thị nguyên văn như chữ (kể cả trong thuộc tính value của ô tìm kiếm), không có hộp thoại alert | Chuỗi được hàm `e()` chuyển thành `&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;`, hiển thị dạng chữ thuần trên giao diện; không hề xuất hiện hộp thoại alert XSS nào. | **Đạt** |
| 7 | Mở `chi-tiet.php?id=abc`, `chi-tiet.php?id=999999` và `chi-tiet.php` (không có id) | Trả về trang 404 thân thiện, mã trạng thái HTTP 404 (tab Network), không có Warning | Tab Network hiển thị mã `Status: 404 Not Found`, giao diện nạp trang `404.php` thân thiện của nhóm, không có lỗi PHP Warning/Notice. | **Đạt** |
| 8 | Thêm vào giỏ với số lượng `0`, `-3`, `1000` và với id không tồn tại (sửa ô ẩn, ô số lượng bằng DevTools hoặc dùng curl) | Không thêm vào giỏ, báo lỗi hoặc bỏ qua; giỏ hàng giữ nguyên | Lớp `GioHang` kiểm tra điều kiện lọc bỏ các số lượng ngoài khoảng 1-10 và kiểm tra ID qua `KhoSanPham`; giỏ hàng không bị thêm dữ liệu rác. | **Đạt** |
| 9 | Thêm hai sân vào giỏ, đổi số lượng, xóa một mục, tải lại trang, chuyển sang trang khác | Thành tiền từng sân, tổng tiền cả giỏ và số món trên header luôn đúng | Trạng thái giỏ hàng lưu trữ đồng bộ trong `$_SESSION['gio']`; số đếm trên Header và các con số thành tiền cập nhật tức thì, chính xác 100%. | **Đạt** |
| 10 | Mở `quan-tri.php` khi chưa đăng nhập; đăng nhập sai; đăng nhập đúng rồi đăng xuất và mở lại `quan-tri.php` | Chuyển về `dang-nhap.php`; báo lỗi chung "Sai tên đăng nhập hoặc mật khẩu"; đăng xuất xong không vào lại được | Chưa đăng nhập mở `quan-tri.php` lập tức bị chuyển hướng về `dang-nhap.php`. Đăng nhập sai báo lỗi chung an toàn. Đăng xuất xóa sạch session và không thể quay lại trang quản trị. | **Đạt** |
| 11 | Sửa giá trị cookie `da_xem` thành `abc,-1,999999` (tab Application → Cookies) rồi mở trang chủ | Trang chủ hiển thị bình thường, bỏ qua các giá trị lạ, không có Warning | Trang `index.php` duyệt qua từng ID, kiểm tra `FILTER_VALIDATE_INT` và đối chiếu `KhoSanPham::timTheoId()`, loại bỏ hoàn toàn các ID lạ, giao diện hiển thị mượt mà không lỗi. | **Đạt** |
| 12 | Đặt `MOI_TRUONG = 'prod'`, tạm đổi tên tệp `data/san.json` rồi mở `danh-sach.php` (thử xong trả lại như cũ) | Hiện trang 500 thân thiện, không lộ đường dẫn hay chi tiết lỗi; chi tiết lỗi có trong `logs/php-error.log` | Màn hình hiển thị trang `500.php` trang nhã; tuyệt đối không lộ stack trace hay đường dẫn tệp mã nguồn; tệp `logs/php-error.log` ghi lại đầy đủ dòng lỗi `RuntimeException: Không có tệp dữ liệu...`. | **Đạt** |

---

## 5. Bảng 4. Đánh giá chất lượng từng trang (Quality Metrics)

*Ghi chú: Toàn bộ các trang đều đạt 0 lỗi PHP trên môi trường phát triển (`MOI_TRUONG = 'dev'`). Mã HTML sinh ra từ PHP được kiểm tra bằng công cụ W3C Markup Validator (Text input). Điểm Accessibility đo bằng Google Lighthouse (chế độ Mobile trên localhost).*

| TT | Trang | Lỗi PHP | Lỗi HTML (W3C) | Điểm A11y (Lighthouse) | Dùng được khi tắt JavaScript? | Ghi chú |
|:--:|---|:---:|:---:|:---:|:---:|---|
| 1 | `index.php` | 0 | 0 | 95 | Có | Hiển thị đầy đủ danh sách sân nổi bật và khối sân đã xem gần đây từ Cookie |
| 2 | `danh-sach.php` | 0 | 0 | 94 | Có | Bộ lọc GET, sắp xếp và phân trang hoạt động hoàn toàn ở server-side |
| 3 | `chi-tiet.php?id=1` | 0 | 0 | 96 | Có | Đọc thông tin sân, gắn cookie `da_xem`, form đặt sân gửi POST trực tiếp |
| 4 | `gio-hang.php` | 0 | 0 | 95 | Có | Quản lý giỏ hàng, cập nhật số lượng qua biểu mẫu POST |
| 5 | `lien-he.php` | 0 | 0 | 93 | Có | Biểu mẫu kiểm tra lỗi và upload ảnh hoàn toàn bằng PHP phía máy chủ |
| 6 | `dang-nhap.php` | 0 | 0 | 98 | Có | Form đăng nhập chuẩn bảo mật, xử lý đăng nhập an toàn |
| 7 | `quan-tri.php` | 0 | 0 | 92 | Có | Bảng danh sách liên hệ đọc từ JSONL có cuộn ngang an toàn trên mobile |
| 8 | `gioi-thieu.php` | 0 | 0 | 96 | Có | Khung trang dùng chung, hiển thị thông tin giới thiệu đồ án |

---

# PHẦN B – CHỨC NĂNG PHP TRÊN TRANG CÁ NHÂN CỦA TỪNG THÀNH VIÊN (2,0 ĐIỂM)

Mỗi thành viên trong nhóm đã tự chuyển đổi trang cá nhân của mình từ `gioithieu.html` thành `gioithieu.php`, sử dụng chung bộ khung trang `inc/header.php` và `inc/footer.php` (qua biến `$goc = '../../'`), đồng thời tự viết và commit **ít nhất HAI chức năng xử lý máy chủ độc lập**:

## Bảng 5. Kết quả Phần B của từng thành viên

| TT | Họ và tên | Hai chức năng PHP đã làm (Phần B) | Chức năng phụ trách ở Bảng 1 | Lỗi PHP | Lỗi HTML | Số commit cá nhân |
|:--:|---|---|---|:---:|:---:|:---:|
| 1 | **Phimmasone Khamphouvanh** | 1. **Danh sách công việc (Todo List):** Lưu trữ danh sách việc cần làm trong `$_SESSION['todo_phim']`, xử lý thêm việc bằng POST kèm PRG.<br>2. **Máy tính chỉ số BMI:** Tiếp nhận chiều cao, cân nặng qua biểu mẫu POST, kiểm tra số thực dương và tính toán, đánh giá tình trạng thể trạng. | Chức năng 5 (Giỏ hàng) & Bảng 3 (Kiểm thử máy chủ) | 0 | 0 | 3 |
| 2 | **Nguyễn Quý Minh Hoàng** | 1. **Sổ lưu bút cá nhân:** Biểu mẫu gửi tên và lời chúc, kiểm tra rỗng ở máy chủ, lưu tệp `storage/3120224060_luubut.jsonl`, hiển thị lời nhắn mới nhất.<br>2. **Bộ đếm lượt xem trang:** Lưu số lượt truy cập vào tệp text trong `storage/`, mỗi phiên (`$_SESSION['da_dem_hoang']`) chỉ đếm đúng một lần. | Chức năng 6 (Đăng nhập, quản trị) & Bảng 3 (Kiểm thử) | 0 | 0 | 2 |
| 3 | **Lê Nguyễn Gia Nhân** | 1. **Sổ lưu bút cá nhân:** Nhận phản hồi và lời nhắn của bạn bè, kiểm tra lọc XSS qua `e()`, lưu trữ vào tệp JSON Lines riêng.<br>2. **Bộ đếm lượt xem phiên:** Sử dụng Session kết hợp tệp đếm để ghi nhận lượt xem trang cá nhân không trùng lặp. | Chức năng 2 (Danh sách) & Chức năng 3 (Chi tiết, Cookie) | 0 | 0 | 3 |
| 4 | **Silaphet Thit** | 1. **Gửi tin nhắn liên hệ cá nhân:** Xử lý biểu mẫu gửi tin nhắn trực tiếp đến Silaphet, lưu vào `storage/3120224188_tinnhan.jsonl`.<br>2. **Bộ đếm lượt ghé thăm:** Đếm và hiển thị số lần người dùng ghé thăm trang cá nhân dựa trên Session. | Chức năng 4 (Biểu mẫu liên hệ & Upload ảnh) | 0 | 0 | 2 |
| 5 | **Trương Nguyễn Ngọc Phú** | 1. **Chế độ giao diện Sáng / Tối (Dark Mode Cookie):** Cho phép chuyển đổi giao diện và ghi nhớ tùy chọn vào Cookie phía máy chủ trong 30 ngày.<br>2. **Lọc danh sách kỹ năng chuyên môn:** Mảng dữ liệu kỹ năng trong PHP, lọc hiển thị theo chuyên mục thông qua tham số URL `?nhom=...`. | Chức năng 1 (Khung trang chung) & Chức năng 2 (Danh sách) | 0 | 0 | 4 |

---

# PHẦN C – CÂU HỎI THẢO LUẬN (2,0 ĐIỂM)

### Câu 1: Máy chủ không tin trình duyệt (Server-side Validation & curl testing)
**Trả lời:**
Ở câu D3 của Bài tập 4, nhóm đã xác định rằng các ràng buộc HTML5 (`required`, `type="email"`, `pattern`) hay JavaScript kiểm tra ở phía trình duyệt chỉ có tác dụng nâng cao trải nghiệm người dùng (UX), chứ hoàn toàn không bảo vệ được hệ thống vì người dùng có thể tắt JavaScript, sửa DOM hoặc dùng công cụ như Postman/curl để gửi thẳng HTTP Request.
- **Thử nghiệm gửi dữ liệu sai bằng curl:**
  ```bash
  curl.exe -i -d "hoten=A&email=abc&sdt=123&noidung=" http://localhost:8000/lien-he.php
  ```
  **Kết quả:** Máy chủ trả về mã HTTP 200 kèm lại toàn bộ trang form và các đoạn HTML cảnh báo: *"Email không hợp lệ"*, *"Số điện thoại phải từ 10-11 số"*, *"Nội dung liên hệ không được để trống"*. 
  **Đoạn mã PHP chặn:** Trong tệp `inc/xu-ly-lien-he.php`, các dòng:
  ```php
  if (!filter_var($du['email'], FILTER_VALIDATE_EMAIL)) { $loi['email'] = 'Email không hợp lệ'; }
  if (!preg_match('/^[0-9]{10,11}$/', $du['sdt'])) { $loi['sdt'] = 'Số điện thoại không đúng'; }
  if (mb_strlen($du['noidung']) < 5) { $loi['noidung'] = 'Nội dung quá ngắn'; }
  ```
- **Thử nghiệm gửi số lượng âm vào giỏ hàng:**
  ```bash
  curl.exe -i -c cookie.txt -b cookie.txt -d "id=san-a1&so_luong=-5" http://localhost:8000/them-gio.php
  ```
  **Kết quả:** Máy chủ kiểm tra điều kiện `if ($soLuong < 1 || $soLuong > 10)` trong lớp `GioHang.php` và lập tức từ chối cập nhật, giữ nguyên giỏ hàng không bị âm.
- **Đánh giá:** Danh sách kiểm tra ở câu D3 của Bài tập 4 đã được nhóm hiện thực hóa đầy đủ 100% tại tầng PHP của máy chủ.

---

### Câu 2: Hàm `e()` và phòng chống lỗ hổng Cross-Site Scripting (XSS)
**Trả lời:**
- **Ba vị trí trong mã nguồn in dữ liệu người dùng ra HTML qua hàm `e()`:**
  1. Trong `inc/header.php` và `danh-sach.php`: In từ khóa tìm kiếm vào ô input: `<input type="search" name="q" value="<?= e($tuKhoa) ?>">`.
  2. Trong `chi-tiet.php`: In tên và thông tin chi tiết của sân bóng: `<h1><?= e($sanPham->ten) ?></h1>`.
  3. Trong `quan-tri.php`: In thông tin phản hồi từ người dùng: `<td><?= e($lh['hoTen']) ?></td>` và `<td><?= e($lh['noiDung']) ?></td>`.
- **Hàm `e()` được nhóm định nghĩa trong `inc/ham.php`:**
  ```php
  function e(?string $chuoi): string {
      return htmlspecialchars((string)$chuoi, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
  ```
  Lý do bắt buộc dùng `ENT_QUOTES`: Giúp mã hóa cả dấu ngoặc kép `"` lẫn dấu ngoặc đơn `'` thành thực thể HTML (`&quot;`, `&#039;`). Điều này đặc biệt quan trọng khi in dữ liệu vào bên trong các thuộc tính HTML (HTML attributes).
- **Thử nghiệm tạm bỏ `e()` ở thuộc tính `value` của ô tìm kiếm với chuỗi `"><script>alert(1)</script>`:**
  - *Hiện tượng:* Trình duyệt hiển thị ngay lập tức hộp thoại cảnh báo `alert(1)`.
  - *Giải thích:* Vì không có `htmlspecialchars`, dấu ngoặc kép `"` đầu chuỗi đã đóng sớm thuộc tính `value="..."` của thẻ `<input>`, dấu `>` đóng luôn thẻ `<input>`, và phần còn lại ` <script>alert(1)</script>` trở thành một thẻ kịch bản HTML hợp lệ, khiến trình duyệt thực thi đoạn mã độc ngay khi trang tải xong (Reflected XSS).
- **Hậu quả nếu trang quản trị in nội dung liên hệ mà không qua `e()` (Stored XSS):**
  - Kẻ tấn công có thể gửi liên hệ chứa mã độc đánh cắp Cookie phiên làm việc (`document.cookie`) hoặc tự động gửi request giả mạo (CSRF).
  - Nạn nhân trực tiếp là **Quản trị viên (Admin)** của hệ thống. Kẻ tấn công sẽ chiếm đoạt quyền quản trị cao nhất của website, lộ toàn bộ dữ liệu khách hàng và mất an toàn hệ thống.

---

### Câu 3: Post/Redirect/Get (PRG) và Thứ tự gửi HTTP Headers (Output Buffering)
**Trả lời:**
- **(a) Mô hình Post/Redirect/Get:**
  - *Thử nghiệm bỏ dòng `header('Location: lien-he.php')`:* Khi người dùng điền form và gửi, trang hiển thị kết quả. Bấm phím **F5**, trình duyệt lập tức bật hộp thoại cảnh báo: *"Confirm Form Resubmission - Bạn có chắc muốn gửi lại biểu mẫu này?"*. Nếu bấm Đồng ý, request POST được gửi lại một lần nữa và tệp `storage/lien-he.jsonl` bị ghi thêm một dòng dữ liệu trùng lặp.
  - *Khi khôi phục lại PRG:* Trình duyệt gửi POST tới máy chủ $\rightarrow$ Máy chủ xử lý lưu dữ liệu, đặt Flash message vào Session $\rightarrow$ Máy chủ trả về mã HTTP **302 Found** kèm header `Location: lien-he.php` $\rightarrow$ Trình duyệt tự động thực hiện một request **GET** tiếp theo đến `lien-he.php`. Lúc này người dùng bấm F5 thoải mái chỉ là tải lại request GET, hoàn toàn không gây trùng lặp dữ liệu.
- **(b) Thứ tự Output và bộ đệm đầu ra (`output_buffering`):**
  - *Thử nghiệm thêm dòng trống trước `<?php` trong `chi-tiet.php` và chạy với lệnh `php -d output_buffering=0 -S localhost:8000`:*
  - *Thông báo xuất hiện:* 
    `Warning: Cannot modify header information - headers already sent by (output started at C:\...\chi-tiet.php:1) in C:\...\chi-tiet.php on line 18`
  - *Hiện tượng cookie:* Cookie `da_xem` **không được ghi nhận** vào trình duyệt.
  - *Giải thích:* Giao thức HTTP quy định phần HTTP Headers phải được gửi đi trước phần HTTP Body. Khoảng trắng hay dòng trống ngoài thẻ `<?php>` được coi là nội dung hiển thị (Body) và máy chủ lập tức gửi gói tin HTTP Header kèm dòng trống đó xuống trình duyệt. Khi lệnh `setcookie()` được gọi ở các dòng tiếp theo, phần Header đã bị đóng nên PHP không thể chèn thêm header `Set-Cookie` được nữa.
  - *Vì sao mã chạy được ở máy mình nhưng hỏng ở máy chủ khác:* Mặc định cấu hình `php.ini` trên máy cá nhân thường bật sẵn bộ đệm `output_buffering = 4096` bytes, gom toàn bộ output vào bộ nhớ đệm trước khi gửi nên che giấu mất lỗi này. Khi triển khai lên máy chủ thật (Production) có cấu hình tắt buffer hoặc khi lượng output vượt quá 4KB, website sẽ bị sập lỗi ngay lập tức.

---

### Câu 4: Quản lý trạng thái website (State Management) và Lộ trình chuyển sang MySQL ở Chương 6
**Trả lời:**

#### 4.1. Bảng quản lý trạng thái dữ liệu của website:
| Dữ liệu | Nơi lưu trữ | Ai đọc và sửa được? | Mất đi khi nào? |
|---|---|---|---|
| **Giỏ hàng (Sân đã đặt)** | `$_SESSION['gio']` (máy chủ) | Máy chủ đọc và sửa; người dùng chỉ giữ ID phiên trong cookie `PHPSESSID` | Mất khi đóng trình duyệt, hết hạn phiên (thường 24 phút), hoặc người dùng bấm "Xóa hết" |
| **Người đăng nhập (Admin)** | `$_SESSION['admin_logged']` (máy chủ) | Máy chủ đọc/ghi sau khi xác thực | Mất khi bấm "Đăng xuất" (`session_destroy()`) hoặc hết hạn phiên |
| **Flash Message** | `$_SESSION['flash']` (máy chủ) | Máy chủ đọc và hiển thị ra view | Tự động xóa ngay sau lần hiển thị đầu tiên (`unset($_SESSION['flash'])`) |
| **Sân đã xem gần đây** | `$_COOKIE['da_xem']` (trình duyệt) | Trình duyệt gửi lên; người dùng có thể đọc/sửa qua DevTools; máy chủ đọc và kiểm tra lại | Tự động hết hạn sau 30 ngày (`time() + 30*86400`) hoặc khi người dùng xóa cookie |
| **Danh sách yêu thích** | `localStorage` (trình duyệt) | Client-side JavaScript đọc/ghi trực tiếp | Tồn tại vĩnh viễn trên trình duyệt đó cho đến khi người dùng xóa bộ nhớ cache/Storage |
| **Dữ liệu liên hệ** | `storage/lien-he.jsonl` (tệp máy chủ) | Chỉ mã PHP trên máy chủ đọc/ghi (có cấm truy cập từ URL qua `.htaccess`) | Lưu trữ vĩnh viễn trên ổ cứng máy chủ |
| **Ảnh đính kèm** | `uploads/` (tệp máy chủ) | Máy chủ lưu trữ; người dùng có thể xem qua thẻ `<img>` | Lưu trữ vĩnh viễn trên ổ cứng máy chủ |

- **Khi người dùng mở website bằng trình duyệt khác hoặc xóa cookie `PHPSESSID`:** Giỏ hàng sẽ bị coi là trống. Trình duyệt mới hoặc request mới không mang theo session cookie cũ, máy chủ sẽ cấp một Session ID hoàn toàn mới với giỏ hàng rỗng.
- **Định hướng chuyển sang cơ sở dữ liệu MySQL ở Chương 6:**
  - Dữ liệu chuyển vào CSDL: Toàn bộ danh sách sân bóng (`san_bong`), danh mục loại sân (`loai_san`), tài khoản người dùng/quản trị (`tai_khoan`), dữ liệu liên hệ (`lien_he`), và đơn đặt sân (`don_dat_san`).
  - **Nhờ mô hình tổ chức phân tầng truy cập dữ liệu (Data Access Object / Repository pattern), nhóm chỉ cần viết lại duy nhất 2 tệp:**
    1. `src/Data/KhoSanPham.php`: Thay thế thao tác `file_get_contents()` và `json_decode()` bằng các câu lệnh truy vấn chuẩn bị `PDO` (`SELECT * FROM san_bong WHERE ...`).
    2. `src/Data/KhoLienHe.php`: Thay thế thao tác ghi tệp `file_put_contents()` bằng câu lệnh `INSERT INTO lien_he (...) VALUES (...)`.
    - Tất cả các trang giao diện (`index.php`, `danh-sach.php`, `chi-tiet.php`, `lien-he.php`, `quan-tri.php`) hoàn toàn **không cần phải chỉnh sửa lại cấu trúc**, vì các lớp `KhoSanPham` và `KhoLienHe` vẫn giữ nguyên các phương thức public trả về đúng đối tượng `SanPham` theo chuẩn.

---

# TÀI LIỆU THAM KHẢO

1. **J. Duckett** (2022), *PHP & MySQL: Server-side Web Development*, John Wiley & Sons, Inc., Indianapolis, Indiana.
2. **The PHP Group** (2026), *PHP Manual: Handling file uploads, Sessions, setcookie, filter_var, password_hash*, truy cập tại: [https://www.php.net/manual/en/](https://www.php.net/manual/en/).
3. **Mozilla Developer Network (MDN)** (2026), *HTTP cookies & Redirections in HTTP (302 Found)*, truy cập tại: [https://developer.mozilla.org/en-US/docs/Web/HTTP](https://developer.mozilla.org/en-US/docs/Web/HTTP).
4. **OWASP Foundation** (2026), *OWASP Cheat Sheet Series: Cross-Site Scripting Prevention & File Upload Security*, truy cập tại: [https://cheatsheetseries.owasp.org/](https://cheatsheetseries.owasp.org/).

---

# PHỤ LỤC A – MẪU BẢNG PHÂN CÔNG CÔNG VIỆC VÀ TỰ ĐÁNH GIÁ

| TT | Họ và tên | Mã sinh viên | Tài khoản GitHub | Công việc đảm nhận | Tự đánh giá đóng góp (%) |
|:--:|---|:---:|:---:|---|:---:|
| 1 | **Phimmasone Khamphouvanh** | 3120224192 | `tmilk282` | **Trưởng nhóm:** Điều phối dự án; Quản lý README và môi trường PHP; Tham gia kiểm thử máy chủ (Bảng 3); Thực hiện Phần B trang cá nhân (Todo List, BMI). | **100%** |
| 2 | **Nguyễn Quý Minh Hoàng** | 3120224060 | `nguyenquyminhhoang` | Phụ trách Chức năng 6 (Đăng nhập, Quản trị, Đăng xuất); Thực hiện ca kiểm thử bảo mật (Bảng 3); Thực hiện Phần B trang cá nhân (Sổ lưu bút, Bộ đếm lượt xem). | **100%** |
| 3 | **Lê Nguyễn Gia Nhân** | 3120224105 | `Gianhanle999` | Phụ trách Chức năng 3 (Chi tiết theo id, Cookie đã xem gần đây); Viết nội dung thảo luận Phần C; Thực hiện Phần B trang cá nhân (Sổ lưu bút, Bộ đếm phiên). | **100%** |
| 4 | **Silaphet Thit** | 3120224188 | `Silaphet` | Phụ trách Chức năng 4 (Biểu mẫu liên hệ, kiểm tra máy chủ, upload ảnh an toàn finfo, JSONL); Thiết kế lớp dữ liệu `KhoLienHe`; Thực hiện Phần B trang cá nhân. | **100%** |
| 5 | **Trương Nguyễn Ngọc Phú** | 3120224111 | `LuckyXD56` | Phụ trách Chức năng 1 (Khung trang chung, Header, Footer, Autoload PSR-4) và Chức năng 2 (Lọc và tìm kiếm danh sách); Lớp `KhoSanPham`, `GioHang`; Phần B cá nhân. | **100%** |

---
*Báo cáo được hoàn thành và xuất bản vào Học kỳ 1, Năm học 2026 – 2027 bởi Nhóm 11.*
