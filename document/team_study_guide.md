# TÀI LIỆU HƯỚNG DẪN BẢO VỆ ĐỒ ÁN CHO TỪNG THÀNH VIÊN
*Dự án: Website Thương mại điện tử Kính mắt Cao cấp Kính Xanh Optical*

Tài liệu này đóng vai trò như một bài "giảng nhanh" giúp mỗi thành viên trong nhóm 8 người hiểu tận gốc rễ phần việc mình phụ trách. Hãy đọc kỹ phần của mình để có thể tự tin trả lời bất kỳ câu hỏi phản biện nào từ giảng viên.

---

## TỔNG QUAN: CẤU TRÚC KỸ THUẬT VÀ CÁCH HỆ THỐNG VẬN HÀNH
*(Tất cả thành viên đều phải đọc phần này để nắm nền tảng chung trước khi bảo vệ)*

### 1. Kiến trúc phân tách (Decoupled Architecture - RESTful)
Khí được hỏi: *"Mô hình hệ thống của em là gì?"*
- **Trả lời:** Chạy theo mô hình tách bạch Client-Server. Frontend (Giao diện) chạy bằng HTML/CSS/JS thuần, Backend (Máy chủ) chạy bằng API PHP trả về đúng Data là tập hợp chữ JSON. 
- Ưu điểm: Tách rời thì nhóm có thể giao việc độc lập. Giao diện thay đổi không làm vỡ logic Backend. Dễ mở rộng sau này.

### 2. Định dạng Cơ sở dữ liệu: Tại sao MongoDB (NoSQL) lại mạnh?
- Đặc thù kính mắt: Một mã kính gọng vàng nhưng có size S, L, có tròng cận, tròng râm... Nếu xài SQL thì phải tạo quá nhiều bảng và `JOIN` lại với nhau dẫn đến web tải rất chậm.
- Nhóm dùng **MongoDB**: Gọi là Schema-less (Không cần cột cố định). Tất cả màu, size được cho thẳng vào một cái Mảng lồng (Array) bên trong dữ liệu Sản phẩm đó. Lấy ra một thao tác là xong.

### 3. Vận hành thực tế trên Windows (Automation)
- Giảng viên hỏi: *"Cài đặt rườm rà không em?"*
- Trả lời: KHÔNG! Nhóm em viết file tự động `.bat`. Click `setup_mongodb.bat` là nó tự cấu hình PHP, tự đổ cợ sở dữ liệu vào MongoDB. Click `start_server.bat` là nó tự dùng máy chủ ngầm của PHP (Port 8000) để bung Web lên chạy. Không cần vào WAMP/XAMPP gõ thủ công bất kỳ thứ gì.

---

## KHỐI FRONTEND (GIAO DIỆN)

### 1. Nguyễn Thị Như Ý (Trưởng Frontend - UI Tổng Thể & Trang Chủ)
*Dành cho bạn Ý:*
- **Hiệu ứng Kính Mờ (Glassmorphism):** Khi em trình bày giao diện ở thanh Menu (Navbar) có độ mờ xuyên thấu, em hãy giải thích nhóm đã dùng thuộc tính CSS `backdrop-filter: blur(20px)` kết hợp với nền có độ trong suốt `rgba(255,255,255,0.7)`. Đây là chuẩn ngôn ngữ thiết kế Glassmorphism trên Apple iOS.
- **Tại sao nhóm không dùng Framework React/Vue?** Cứ mạnh dạn nói: *"Nhóm muốn nắm chắc gốc rễ của Thao tác DOM (Document Object Model) trên JavaScript thuần và tự quản lý State (Trạng thái) bằng LocalStorage, từ đó tối ưu mã nguồn nhẹ nhất, không phụ thuộc bộ cài nặng nề."*
- **Responsive (Chống vỡ màn hình Điện thoại):** Tailwind CSS quản lý thiết bị theo các điểm ngắt (breakpoints). Màn hình nhỏ (<768px - md) thì Menu dài trên máy tính sẽ bị `hidden`, nhường chỗ cho khối Icon 3 gạch (Hamburger) hiện ra. 

### 2. Nguyễn Thị Hồng Hòa (Frontend - Danh Mục Sản Phẩm & Lọc Tìm Kiếm)
*Dành cho bạn Hòa:*
- **Cơ chế Lọc giá & Thương Hiệu:** Giao diện có một thanh trượt kéo giá (Range Slider) và các dấu Checkbox. Khi người dùng kéo chuột hoặc tick chọn, em giải thích rằng hàm JavaScript `addEventListener('change')` đang rình bắt ngay hành động đó. Lập tức JS sẽ gom từ khoá (Rayban, Dưới 2 triệu) đính lên trên URL Web (Ví dụ `?brand=Rayban`), và Backend sẽ ngửi thấy dòng chữ này chuyển Data tương ứng về. 
- **Render Sản phẩm:** Khi lấy được cục dữ liệu JSON 20 chiếc kính mắt từ Backend, em giải thích JS dùng một vòng lặp `forEach()` chạy 20 lần để sinh ra (Render) 20 cấu trúc thẻ `div` Card HTML và nhét lên trang web.

### 3. Võ Ngọc Gia Vy (Frontend - Giỏ Hàng & Checkout)
*Dành cho bạn Vy:*
- **Cơ chế lưu trữ Giỏ Hàng (Cart Localstorage):** Giảng viên hỏi: *"Khách tắt máy tính ngày mai vô lại giỏ hàng còn kính không?"*
Em trả lời: "Dạ CÒN. Giỏ hàng chưa thanh toán không bị cất vào DB, mà cất thẳng vào **LocalStorage** (Bộ nhớ cục bộ của trình duyệt) qua key `kx_cart` bằng chuỗi văn bản JSON. Trình duyệt đóng lại thì cái hộp này vẫn còn lưu cứng ở đó."
- **Quy trình Checkout Logic:** Khi nhấn thanh toán, em gom thông tin Địa Chỉ trên form HTML ghép với Giỏ Hàng dưới LocalStorage. Biến chúng thành Object và gửi lệnh `fetch(POST)` xuống đường truyền giao dịch API Backend. Nếu Bếp báo Data đúng, em lập tức `localStorage.removeItem('kx_cart')` để làm trống giỏ và hiện thông báo Đặt Thành Công.

---

## KHỐI BACKEND & CƠ SỞ DỮ LIỆU

### 4. Xuân Đạt (Nhóm trưởng / Chuyên gia Kiến trúc Backend)
*Dành cho Đạt:*
- Với quyền nhóm trưởng, em phải am hiểu bức tranh bao quát.
- **Trang Admin & Doanh thu:** Ở Dashboard, để đếm được Web kiếm được bao nhiêu tiền tháng này, em giải thích Backend đã dùng phương pháp **Aggregate Pipeline** của MongoDB. Tức là đưa Data vào 1 cái ống lọc: Lọc bỏ đơn hàng Hủy (`$match status: completed`) -> Sau đó tính tổng tiền các đơn hàng đó (`$group` và dùng phép cộng `$sum`). Xử lý ở cấp Database nhanh hơn rất nhiều so với tải tất cả xuống PHP dùng lệnh `foreach` cộng chay.

### 5. Nguyễn Vũ Duy (Quản lý Data MongoDB)
*Dành cho bạn Duy:*
- **Cấu trúc Dữ liệu Đơn hàng (`orders`):** Đây là con ác chủ bài. Khi thầy hỏi tại sao không làm SQL? Em nói: *"Kính thưa thầy, khi hóa đơn chốt xong trong SQL, nó chỉ lưu ID của món kính (VD số 5). Rủi ngày mai tiệm tăng giá cái kính số 5 từ 1 lên 2 triệu. Thì Hóa đơn cũ truy xuất ngược lại cũng sẽ ghi sai thành 2 triệu. Để chống vỡ giá, trong MongoDB nhóm em áp dụng **Embedded Documents**. Bê nguyên tên sản phẩm, màu sắc, giá ngay tại thời điểm mua 'nhúng tĩnh' vĩnh viễn vào trong cục Hóa Đơn luôn. Không phụ thuộc chéo nữa.*"

### 6. Văn Ngọc Phương (Backend - Xác thực & Bảo mật)
*Dành cho bạn Phương:*
- **Rate Limit & Anti-DDoS:** Bức tường lửa được đặt tại hàm chặn Request. Nếu Hacker cố gõ sai mật khẩu 5 lần trong 1 phút để phá băng Đăng nhập, biến đếm của Backend sẽ lưu lại IP mạng đó. Qua lần thứ 6, Server sẽ không chọc xuống MongoDB để tìm kiếm nữa mà Lập tức chặn kết nối báo lỗi mã HTTP Status Code `429 (Too Many Requests)` nhằm bảo vệ Database chống kiệt sức.
- **Mã hoá Mật Khẩu:** Mật khẩu khi lưu xuống Bảng Users trong DB không hiện ra dạng chữ như `123`, mà bị băm nát thành chuỗi loằng ngoằng nhờ cơ chế thuật toán Hashing (`password_hash` của PHP). Dù admin có nhìn trộm Database cũng không lấy được Pass của khách.

### 7. Tài Vũ (Backend - Xử lý API Sản phẩm, Tìm Kiếm)
*Dành cho Tài:*
- **Logic Filter Backend:** Khi Frontend gửi query URL như `?max_price=2000000`, PHP của em sẽ gắp cái chuỗi này, chuyển thành ngôn ngữ Filter cho MongoDB. Nó sử dụng điều kiện `$lte` (Less than or equal - Nhỏ hơn hoặc bằng số tiền) để móc ra đống Kính Mắt thoả mãn.
- Còn nếu điền tên kính ở thanh Tìm Kiếm (VD "Rayban"), MongoDB không search chay dấu = , mà nhóm dùng toán tử `$regex` có trang bị chữ `i` (Insensitive case - Không phân biệt hoa/thường) để cứ hễ có xuất hiện chữ "ray" là nhả ra kết quả.

### 8. Nguyễn Trọng Tín (Backend - Giao dịch điện tử & Orders)
*Dành cho Tín:*
- **Luồng Giao Dịch Chốt Đơn:** Khi tiếp quản cục JSON Giỏ Hàng từ Frontend gửi nạp xuống API. Tín không vội lưu liền mà Backend sẽ chạy ngược xuống CSDL dò lại đúng ID từng cái Mắt kính đó trên kho xem giá thật và số lượng Tồn Kho có còn hay không (Ngăn chặn trường hợp FE tự ý sửa giá web ảo xuống 0đ mua trộm hàng).
- Chốt xong, Data mới được INSERT một cục bự vào Collection `orders`. Đồng thời, Backend kích hoạt Lệnh Update, trừ đi (Minus) giá trị Kho Hàng trong bảng Biến Thể Sản Phẩm (`Stock Deduction`) -> Tránh hiện tượng khách khác vô coi báo còn hàng mà lúc chọn mua lại hết.

---
## PHỤ LỤC: Giải thích hệ thống theo ví dụ "Nhà Hàng" 
*(Dùng để trả lời nếu Giảng Viên hỏi: "Hãy tóm tắt nguyên lý hoạt động của Web ngắn gọn nhất?")*

Hãy tưởng tượng website Kính Xanh giống hệt như một nhà hàng:
1. **Frontend (Giao dịch viên):** Chính là bạn bồi bàn và Menu (HTML/CSS/JS). Bạn bồi bàn chỉ ghi Order của thầy vô giấy (Gọi API Request) chứ chưa hề biết bếp còn nấu được không.
2. **Backend (Trưởng Bếp PHP):** Bồi bàn đem giấy chạy vô khuất mắt khách (Khu vực bảo mật). Ông Bếp (PHP API) nhận, lật quy định nội quy xem khách lừa đảo gõ sai pass nãy giờ không. Nếu an toàn mới mở cửa ngó vô Tủ Lạnh.
3. **MondoDB (Kho Tủ Lạnh NoSQL):** Thay vì tủ đông chia ô vuông cứng nhắc bằng nhau gọi là SQL, tủ lạnh Mongo là bao ni lông (Túi Zip Document). Một mã tính có hàng chục cái gọng, kính màu các loại nhét lộn xộn trong 1 túi zip rút ra cho nhanh luôn làm Tốc độ cực mượt.
4. Ông Bếp rút hàng, trừ kho, cất hoá đơn thu tiền. Cười tươi ghi tờ giấy nhỏ (Dữ liệu JSON) bảo êm xui rồi ném cho Bồi Bàn chạy ra khoe giao diện báo màu Xanh (Thành Công) cho khách. Hết.
