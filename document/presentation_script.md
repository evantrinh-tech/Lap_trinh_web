# Kịch Bản Thuyết Trình Demo Hệ Thống Kính Xanh Optical

> **Mục tiêu:** Trình bày rõ ràng kiến trúc, khối lượng công nghệ và điểm sáng của dự án trước giảng viên. Bài thuyết trình được chia thành 4 phần chính.

---

## Phần 1: Mở đầu & Khởi động hệ thống
*(Thao tác: Vừa nói vừa click đúp vào file `start_server.bat`)*

**"Chào thầy/cô và các bạn. Hôm nay nhóm em xin phép demo dự án Website Thương mại điện tử Kính mắt Cao cấp - Kính Xanh.**

Thay vì phải gõ các dòng lệnh phức tạp để khởi động backend và frontend riêng biệt hoặc cài đặt rườm rà từng công cụ, nhóm em đã đóng gói toàn bộ quy trình khởi chạy vào một automation script là file `start_server.bat` duy nhất. 

*(Chỉ vào màn hình console đen nhảy lên)*

Khi chạy file này, hệ thống sẽ tự động thực hiện 3 tác vụ ngầm:
1. Nhận diện môi trường XAMPP PHP trên hệ điều hành.
2. Tự động thiết lập một Local Web Server ở cổng `8000`.
3. Kiểm tra, tải extension nếu thiếu và kết nối trực tiếp với cơ sở dữ liệu **MongoDB**.

Chỉ với 1 click, server nội bộ đã được bật, sẵn sàng tiếp nhận cả giao diện Frontend và các API từ Backend."

---

## Phần 2: Giới thiệu Kiến trúc 
*(Thao tác: Mở trình duyệt, truy cập hệ thống `localhost:8000` và cho thấy giao diện trang chủ đẹp mắt)*

**"Về mặt kiến trúc, hệ thống được thiết kế theo mô hình tách bạch (Decoupled Architecture) nhưng có thể chạy chung trên một máy chủ để dễ triển khai:**

- **Frontend:** Xây dựng bằng trình duyệt thuần tuý là HTML5, Vanilla JavaScript và Tailwind CSS. Nhóm hạn chế sử dụng Framework để hiểu rõ cơ chế Core JS. Bọn em chú trọng vào UX/UI, sử dụng ngôn ngữ thiết kế Glassmorphism (hiệu ứng kính mờ), đảm bảo trang web có tính thẩm mỹ cao cấp đúng với ngành hàng thời trang kính mắt.
- **Backend API:** Viết bằng **PHP thuần** kết hợp Composer để quản trị các thư viện phụ thuộc. RESTful APIs được thiết kế rõ ràng theo từng phân hệ.
- **Database:** Quyết định sử dụng **MongoDB (NoSQL)** thay vì MySQL truyền thống. Lý do là vì sản phẩm kính mắt thường có rất nhiều biến thể thay đổi (ví dụ: gọng chuẩn, màu sắc đa dạng, size kính, thông số tròng) và các metadata đánh giá. Kiến trúc Document của NoSQL giúp mở rộng cấu trúc dữ liệu mô tả sản phẩm mà không bị gò bó bởi các bảng (Table) cố định cứng nhắc."

---

## Phần 3: Demo Luồng tính năng cốt lõi
*(Thao tác: Thực hiện trực tiếp trên Web)*

### 1. Luồng Xác thực (Đăng nhập):
"Đầu tiên em xin demo luồng đăng nhập. Khi nhập tài khoản Admin (`admin@kinhxanh.vn`) và mật khẩu, Client sẽ gửi dữ liệu dạng JSON qua API POST xuống backend. PHP sẽ truy vấn vào Collection `users` trong MongoDB, đối chiếu mật khẩu đã được mã hóa Hash an toàn. Nếu khớp, Access Token (hoặc Session) sẽ được xác nhận để đăng nhập."

### 2. Luồng Mua sắm & Bộ Lọc Sản Phẩm:
"Ở danh mục sản phẩm, khi click vào các bộ lọc (như Khoảng giá, Thương hiệu, Chất liệu), JavaScript ở Frontend sẽ linh hoạt cập nhật danh sách lập tức.
Điểm đặc biệt ở trang *chi tiết sản phẩm* là cấu trúc dữ liệu linh hoạt: Một sản phẩm "Cha" sẽ liên kết trực tiếp với các 'Sub-Variant' (biến thể con) trong Database. Khi đổi màu kính (Đen sang Vàng), giá bán, hình ảnh và cả số lượng tồn kho tự động update theo thời gian thực."

### 3. Giỏ hàng & Thanh toán:
"Cuối cùng là giỏ hàng. Quá trình thêm sản phẩm được xử lý mượt mà qua AJAX. Khi xác nhận thanh toán, Đơn hàng (Order) sẽ gom toàn bộ sản phẩm và thông tin vận chuyển thành một BSON Document nguyên gốc và lưu thẳng vào Collection `orders` trong MongoDB. Cách này rất tiện lợi và truy xuất siêu nhanh so với việc phải Join gộp bảng dữ liệu Order, OrderItems, Product như SQL thường làm."

---

## Phần 4: Tổng kết & Điểm sáng kỹ thuật
*(Thao tác: Quay lại màn hình mã nguồn hoặc Terminal, tổng kết thuyết phục)*

"Để hoàn thành dự án Kính Xanh, điểm tự hào nhất của nhóm em nằm ở tư duy **tự động hóa Deployment và sự chặt chẽ của Kiến Trúc Dữ Liệu.** 

Từ việc viết script `.bat` tự động hoá (hạ cấp Driver xuống phiên bản tương thích với PHP cũ qua Platform Check), tự động gieo cơ sở dữ liệu mẫu (`Database Seeding`) cực nhanh vào MongoDB, cho đến việc thiết kế giao diện tinh xảo bằng Tailwind CSS. 

Hệ thống mang lại trải nghiệm xuất sắc cho người dùng và cực kỳ dễ dàng cho người chấm hoặc lập trình viên Maintain sau này: **Không cài đặt rườm rà, tải source code về click chuột một cái là Server chạy ngay lập tức.**

Em xin kết thúc phần demo sản phẩm Kính Xanh. Cảm ơn thầy/cô đã lắng nghe!"

---

> **💡 Mẹo:** Khi bạn nhắc đến việc _"Sản phẩm có nhiều biến thể rất hợp với MongoDB"_, hãy mở file `database/seed_mongo.php` ra chia nửa màn hình để giảng viên tận mắt nhìn thấy quy trình đẻ ra các mảng dữ liệu cấp dưới. Giảng viên công nghệ đánh giá rất cao sinh viên show được Code/Data Structure!
