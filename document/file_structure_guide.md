# HƯỚNG DẪN ĐỌC CODE & LUỒNG HOẠT ĐỘNG CÁC FILE THEO TỪNG THÀNH VIÊN
*(Bản đồ Hệ thống Mã nguồn Mã - Dành riêng để giải thích file code)*

Nhiều bạn nhìn vào thư mục dự án với hàng chục file sẽ bị ngợp và không biết code đoạn nào đang chạy cho cái gì. Tài liệu này sẽ **"chỉ tận tay, day tận mặt"** chính xác file nào thuộc về người nào, bên trong nó chứa cái gì và luồng chạy của nó hoạt động ra làm sao.

---

## 🟢 KHỐI FRONTEND (GIAO DIỆN)

### 1. Nguyễn Thị Như Ý (Trưởng Frontend - Giao diện Tổng)
**Các file bạn Ý phải chịu trách nhiệm thuộc lòng:**
*   **`index.html` (Trang chủ chính):** 
    *   *Nội dung:* Đây là bộ mặt website. Gồm thẻ `<header>` chứa logo và menu. Thẻ `<main>` chứa ảnh Banner quảng cáo to (Hero section), đồng hồ đếm ngược Flash Sale, và danh sách sản phẩm nổi bật.
    *   *Hoạt động:* Khi người dùng gõ `localhost:8000`, file này được nạp lên đầu tiên.
*   **`css/style.css` (File trang điểm CSS mảng hiệu ứng):**
    *   *Nội dung:* Nơi chứa toàn bộ mã CSS tự viết thêm. 
    *   *Hoạt động:* Em cần nắm các Class (lớp) như `.glass-nav` (class làm mờ thanh menu khi cuộn chuột), hay `.page-fade` (hiệu ứng mờ dần khi chuyển trang). Khi trình duyệt đọc file `<link rel="stylesheet">`, nó sẽ quét các class này để vẽ hiệu ứng.
*   **`js/tailwind-config.js` (Lõi cấu hình thư viện Tailwind):**
    *   *Hoạt động:* HTML dùng Tailwind nhưng màu mặc định không hợp. File js này sẽ định nghĩa lại biến màu `primary: '#00113a'` làm màu xanh navy đặc trưng của hệ thống.

### 2. Nguyễn Thị Hồng Hòa (Frontend - Giao diện Danh mục & Lọc Kính)
**Các file bạn Hòa phải chịu trách nhiệm thuộc lòng:**
*   **`products.html` (Trang danh sách bán hàng):**
    *   *Nội dung:* Bên trái là Layout cột `<aside>` quy định bộ lọc (Giá, Hãng, Gọng kính). Bên phải là Layout thẻ lưới `grid` để chèn các thẻ `div` chứa từng con kính mắt.
*   **`product-detail.html` (Trang thông tin chiếc Kính):**
    *   *Nội dung:* Cấu trúc gồm ảnh lớn, chùm 3 ảnh nhỏ bấm vào đổi qua lại. Phía bên phải là các nút Button "Chọn Đen", "Chọn Vàng".
*   **`js/main.js` (Phần Lọc):** 
    *   *Hoạt động:* Bên trong `main.js` có các hàm `document.querySelectorAll()` để "tóm" (Query) các nút Checkbox ở trang `products.html`. Khi khách tích ô "Rayban", hàm này sẽ thay đổi giao diện, giấu mấy thẻ `div` của Hãng khác đi.

### 3. Võ Ngọc Gia Vy (Frontend - Giỏ Hàng & Checkout)
**Các file bạn Vy phải chịu trách nhiệm thuộc lòng:**
*   **`js/add-to-cart.js` hoặc hàm giỏ hàng trong `main.js`:**
    *   *Hoạt động:* Dòng code `localStorage.setItem('kx_cart', v)` là quan trọng nhất. Mỗi khi click nút "Thêm vào giỏ", hàm này gom (Tên, hình ảnh, giá) ở HTML nén thành cục chữ lưu ẩn dưới ổ cứng trình duyệt. Hàm `localStorage.getItem` lấy ra xài.
*   **`cart.html` (Trang quản lý Giỏ hàng):**
    *   *Nội dung:* Các thẻ `<input type="number">` để ấn + và - số lượng kính.
*   **`checkout.html` (Trang chốt Đơn):**
    *   *Nội dung:* Chứa thẻ `<form>` nhập số điện thoại, tên, địa chỉ. 
    *   *Hoạt động:* Nút "Thanh toán" sẽ gom data điền từ các ô `<input>` gửi một lệnh Fetch (AJAX) đi thẳng xuống đường hầm `backend/api/orders`.


---

## 🔵 KHỐI BACKEND (MÁY CHỦ VÀ LOGIC DATABASE)

### 4. Xuân Đạt (Nhóm trưởng - Kiến trúc Cơ sở & Dashboard Admin)
**Các file bạn Đạt phải chịu trách nhiệm thuộc lòng:**
*   **`setup_mongodb.bat` & `start_server.bat` (Công tắc điện của hệ thống):**
    *   *Hoạt động:* Nó chạy các tập lệnh của Windows CMD để "hét" máy tính dò tìm thư mục XAMPP, nhạy bén móc `mongodb.dll` (cầu nối NoSQL) vào file `php.ini`. File kia thì chạy lệnh `php -S localhost:8000` để giả lập mở port liên kết Web.
*   **`admin.html` (Giao diện bảng vẽ thống kê).**
*   **`backend/api/admin/index.php` (Bộ cỗ máy đếm tiền & dữ liệu):**
    *   *Hoạt động:* Khi file này chạy, nó kết nối MongoDB để ĐẾM `countDocuments()` số đơn, số user. Khúc đếm tiền thì nó gọi `$aggregate` (ống xả của Mongo) để tính cộng dồn tổng tiền các đơn hàng. Cuối cùng nhả lại JSON về `admin.html`.

### 5. Nguyễn Vũ Duy (Backend - Quản lý Kho Dữ liệu NoSQL)
**Các file bạn Duy phải chịu trách nhiệm thuộc lòng:**
*   **Thư mục Database Ảo (Tự nhớ vào đầu):** Thay vì file `.sql` có thể sờ, MongoDB tàng hình chạy trong RAM máy. Khai báo schema dựa trên cấu trúc các Json trả ra. Sẽ có 3 khay chứa (`Collections`) tên là: `users`, `products`, `orders`.
*   **`database/seed_mongo.php` (Máy xúc Data giả):**
    *   *Hoạt động:* Nhóm không nhập bằng tay từng cái kính cho mệt. Chạy thử file này (`php seed_mongo.php`), nó sẽ dùng lệnh `$collection->insertOne(...)` nhét cả ngàn dòng dữ liệu gồm kính chữ Gucci, Rayban... đâm thẳng vào DataBase để web có Mồi chạy ra ngay. Phải giải thích được quá trình Insert này. 

### 6. Văn Ngọc Phương (Backend - Xác thực Đăng nhập & Security)
**Các file bạn Phương phải chịu trách nhiệm thuộc lòng:**
*   **`backend/api/auth/login.php` (Đường hầm gác cổng Đăng nhập):**
    *   *Hoạt động:* Nó dùng lệnh lấy Email, Pass mà người dùng gõ. Mở lệnh chọc xuống DataBase MongoDB bảng `users`. PHP lôi Pass (dưới dạng loằng ngoằng) lên và đối chiếu bằng hàm giải mã `password_verify()`. Nếu khớp -> gật đầu -> cấp 1 cái chứng minh thư ảo JSON đem về cho Client.
*   **`backend/lib/security.php` (Lá chắn tên lửa):**
    *   *Hoạt động:* Trong file gác cổng bảo mật này chứa hàm Kiểm tra `IP Limit` (Rate limit). Nó lưu số đếm vô Mongo, dùng cấu trúc rẽ nhánh `if ($count > 5)` chặn và die() in ra màn hình `Lỗi chặn 429`.

### 7. Tài Vũ (Backend - API Trả sản phẩm)
**Các file bạn Vũ phải chịu trách nhiệm thuộc lòng:**
*   **`backend/config/database.php` (Bản đồ kết nối SQL):**
    *   *Hoạt động:* File chứa lệnh gõ cửa `new \MongoDB\Client()`. Giải thích cách PHP lấy đường link cấu hình URL kết nối tới Database.
*   **`backend/api/products/...` (Hoặc các file rút sản phẩm ra):**
    *   *Hoạt động:* Ở Frontend bạn Hoà gõ tìm kiếm, thì thông số chạy thẳng vào file này nấp dưới biến mảng `$_GET['brand']` của ngôn ngữ PHP. File này lấy cái biến đó gán vô lệnh Mongo `['brand' => 'Rayban']`, móc ra mảng Object Kính mắt, dũa cho đẹp và đẩy trả cho hàm `Response` nôn ra chữ JSON ném về lại cái giao diện.

### 8. Nguyễn Trọng Tín (Backend - Order API & Trừ Kho)
**Các file bạn Tín phải chịu trách nhiệm thuộc lòng:**
*   **`backend/api/orders/index.php` (Máy làm Bill hoá đơn):**
    *   *Hoạt động:* Dữ liệu của Vy gửi xuống bằng Method `POST`, Tín sẽ dùng lệnh lấy chữ `file_get_contents('php://input')` để hốt xác cục JSON đó.
    *   Sau đó dùng vòng lập `foreach` kiểm đếm lại Tương ứng mỗi một cái mã mắt kính, Tín phải viết lệnh `updateOne()` xuống thằng Kho Hàng (`product_variants`) trừ đi (Minus) giá trị Kho Hàng trong bảng Biến Thể Sản Phẩm (`Stock Deduction -1`). Cuối cùng ném hóa đơn tổng vào bảng `orders`.
*   **`backend/api/reviews/...` (Máy lưu lời khen):**
    *   Nhận rate 1-5 sao và chuỗi Text. Liên kết `user_id` và `product_id` lưu thẳng một cục vào Mảng. Đơn giản dễ xơi. 

---
**💡 Chốt hạ:** Đừng sợ hãi khi nhìn vào hàng trăm dòng code. File `.html/.css` là phần "Vẽ tranh". Thư mục `backend/...` là đống công cụ tính quy luật ẩn giấu. Ai phụ trách file nào trên đây thì bật đúng file đó lên xem bằng Code Editor là sẽ hiểu tường tận!
