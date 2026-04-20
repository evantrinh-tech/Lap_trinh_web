# BÁO CÁO KỸ THUẬT - PHÂN HỆ BACKEND HỆ THỐNG KÍNH XANH OPTICAL

## 1. Kiến trúc Backend
Backend của hệ thống website thương mại điện tử Kính Xanh được xây dựng theo mô hình **PHP thuần kết hợp MongoDB**, tập trung vào việc cung cấp các API xử lý nghiệp vụ cho toàn bộ hệ thống. Kiến trúc này không sử dụng framework lớn mà tổ chức mã nguồn theo hướng tách chức năng rõ ràng, giúp dễ đọc, dễ bảo trì và phù hợp với quy mô của đề tài.

**Cấu trúc thư mục cốt lõi:**
- `backend/api/`: Chứa các endpoint xử lý nghiệp vụ chính như xác thực người dùng, sản phẩm, giỏ hàng, đơn hàng, đánh giá và quản trị. Mỗi chức năng được triển khai thành các tệp PHP riêng biệt (ví dụ: `auth/login.php`, `orders/index.php`, `admin/index.php`). Cách tổ chức vi dịch vụ (Micro-level) này giúp mỗi endpoint hoạt động độc lập, luồng xử lý rõ ràng và thuận tiện khi mở rộng.
- `backend/config/`: Đảm nhiệm cấu hình kết nối. Tệp trung tâm `database.php` đóng vai trò quan trọng trong việc đọc biến môi trường từ tệp `.env`, khởi tạo kết nối đến MongoDB, chọn DataBase và cung cấp các hàm dùng chung (như lấy collection tĩnh, chuyển đổi ID, chuẩn hóa cấu trúc). Điều này tuân thủ nguyên tắc DRY (Don't Repeat Yourself).
- `backend/lib/`: Chứa các bộ thư viện cốt lõi phục vụ toàn hệ thống. Tệp `auth.php` xử lý Access Token; `response.php` chuẩn hóa đầu ra dữ liệu API định dạng JSON; `security.php` triển khai các cơ chế bảo vệ tối quan trọng như giới hạn Request (Rate Limiting), danh sách đen IP (Blacklisting), lọc Header và ngăn chặn tấn công DDoS.

**Luồng xử lý dữ liệu (Data Flow):**  
Khi nhận Request từ Client, endpoint tương ứng sẽ:
1. Kiểm tra phương thức truy cập và xử lý OPTIONS (CORS preflight).
2. Áp dụng ngay lớp bảo mật (từ chối nếu phát hiện truy cập bất thường).
3. Xác thực Token người dùng nếu endpoint mang tính cá nhân/chuyên sâu.
4. Truy vấn NoSQL (MongoDB) để giải quyết logic nghiệp vụ.
5. Định dạng dữ liệu và trả phản hồi JSON chuẩn về lại Client.

Đặc biệt, việc sử dụng **MongoDB (NoSQL)** thay vì CSDL quan hệ SQL cho phép hệ thống lưu trữ dữ liệu sản phẩm linh hoạt (nhiều biến thể, thuộc tính). Cách tiếp cận Document-based này đẩy nhanh tốc độ đọc/ghi dữ liệu và thiết kế schema hiệu quả hơn.

---

## 2. Phân hệ Quản trị (Admin System)
Phần quản trị trong backend đóng vai trò đầu não, được triển khai thông qua endpoint `backend/api/admin/index.php`. API này cung cấp dữ liệu số liệu kinh doanh định lượng: Tổng số đơn hàng, số người dùng, sản phẩm và tính toán tổng doanh thu.

**Bảo mật & Phân quyền:**
Điểm chạm quản trị không bao giờ cấu hình Public. Hệ thống sử dụng cơ chế kiểm tra Token với `requireAuth()`. Sau khi xác thực danh tính, hệ thống tiếp tục bắt buộc kiểm tra trường phân quyền `role_id == 1` ở Collection người dùng. Mọi truy cập vào khu vực này không có Data chứng minh là Quản trị sẽ lập tức nhận mã lỗi `403 Forbidden`. Bức tường phân quyền này khá vững chắc cho nghiệp vụ.

**Xử lý dữ liệu phức hợp:**
Dữ liệu trả về cho bản điều khiển không chỉ đếm chay. Với doanh thu, backend ứng dụng phương pháp ***MongoDB Aggregate Pipeline*** để trích xuất và tính tổng (`$group`, `$sum`) trường `total_price` chỉ đối với các đơn hàng mang trạng thái `completed`. Cách tính toán ngay ở lớp Database giúp Admin Dashboard phản ánh số liệu tài chính siêu chính xác tức thời và không độ trễ.

---

## 3. Quy trình Triển khai (Deployment & Configuration)
Quy trình Setup và Deploy của Kính Xanh được thiết kế ưu tiên tối đa tính ứng dụng ở môi trường demo/học tập mà không làm mất đi các tiêu chuẩn thực thế.

- **Quản lý Cấu hình:**  Môi trường được tách khỏi mã nguồn qua tệp `.env`, che giấu thông tin mật (Connection URI, JWT Secret Key).
- **Quản lý Thư viện (Dependency):** Sử dụng PHP `>=8.0` và cài đặt gói driver `mongodb/mongodb` quy chuẩn thông qua `composer.json`.
- **Tự động hoá Windows (1-Click Run):**
  - Cung cấp Script `setup_mongodb.bat` thông minh có khả năng quét đường dẫn XAMPP, tự động tải Extension .dll MongoDB cho đúng phiên bản PHP rồi gắn vào file `php.ini`.
  - Cung cấp Script `start_server.bat` kích hoạt PHP Built-in Server ở Port `8000`. Cả hai script rút gọn 10 bước cài đặt hệ thống phức tạp xuống chỉ còn "nhấp đúp chuột".
- **Dữ liệu hạt giống (Database Seeding):** File `database/seed_mongo.php` giải quyết hoàn hảo câu lệnh tạo cấu trúc và nạp dữ liệu mẫu. Tệp này tự động Drop CSDL cũ, Index bảo mật, nạp trước Admin, Users, Products cùng biến thể, và Orders mẫu. Client ngay lập tức có dữ liệu tương tác đầy đủ sau cú click chạy lần đầu.

---

## 4. Tổng hợp, Đánh giá và Các API Cốt lõi

Nhìn chung, Backend của Kính Xanh có tính phân cấp thư mục tốt, tuân thủ mô hình API REST và xử lý gọn các nhóm nghiệp vụ phức tạp của Thương Mại Điện Tử. Hệ thống đã triển khai đầy đủ kiến trúc Core: *Database Connection, Authentication/Authorization, Request Parsing, Security Rules & Rate Limiting.*

Dù đã đáp ứng trọn vẹn quy mô đồ án học thuật hiện tại, hệ thống vẫn còn dư địa mở rộng: Nếu sau này dự án scale lớn hơn, cấu trúc có thể bổ sung Centralized Router (bộ định tuyến tập trung), Logger chi tiết cho File System và tích hợp Framework xử lý nền tảng Cache (ví dụ Redis).

### Bảng tóm tắt Kiến trúc các Enpoint Tiêu biểu

| Nhóm chức năng cốt lõi | Các File / Endpoint tiêu biểu | Vai trò chính trong Hệ thống |
| :--- | :--- | :--- |
| **Lõi hệ thống (Core Engine)** | `config/database.php`<br>`lib/auth.php`<br>`lib/security.php`<br>`lib/response.php` | Khởi tạo kết nối MongoDB, cấu trúc thư viện xác minh Identity người dùng, bảo vệ API chống tấn công và định dạng dữ liệu đầu ra JSON. |
| **Xác thực Tài khoản** | `auth/login.php`<br>`auth/register.php`<br>`auth/me.php` | Phụ trách toàn bộ chu trình gia nhập hệ thống: Tạo mới người dùng, đăng nhập, bảo vệ thông tin Session và đối sánh bảo mật mật khẩu. |
| **Quản trị Máy chủ** | `admin/index.php` | Mạng lưới phân quyền cấp cao phân phối dữ liệu phân tích số liệu thống kê (Lượt mua, người dùng, tổng doanh thu thực tế). |
| **Nghiệp vụ Mua hàng** | `cart/index.php`<br>`orders/index.php` | API Quản lý hàng rào giỏ hàng điện tử; xác nhận hành vi mua và luân chuyển JSON sang tập hợp dữ liệu đơn đặt hàng hoàn chỉnh. |
| **Hậu mãi & Đánh giá** | `reviews/index.php` | Hệ thống lắng nghe Feedback của người dùng và lưu trữ đánh giá sản phẩm. |
| **Triển khai Hệ thống** | `seed_mongo.php`, `.env`, `start_server.bat` | Bộ công cụ Automate (tự động hóa) gieo thông tin ban đầu, cài đặt máy chủ giả lập, khởi chạy Website với 1 click. |
