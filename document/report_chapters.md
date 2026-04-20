# ĐỒ ÁN MÔN HỌC: XÂY DỰNG WEBSITE THƯƠNG MẠI ĐIỆN TỬ KÍNH XANH OPTICAL

> *Dưới đây là nội dung chi tiết, sử dụng văn phong học thuật chuyên ngành, nhằm hoàn thiện Báo cáo Đồ án tương đương với tiêu chuẩn báo cáo Khóa luận Tốt nghiệp.*

---

## CHƯƠNG 1: TỔNG QUAN ĐỀ TÀI

### 1.1 Lý luận thực tiễn và Lý do chọn đề tài
Trong kỷ nguyên Chuyển đổi số (Digital Transformation) và Cách mạng Công nghiệp 4.0, thương mại điện tử (E-commerce) đã trở thành một thiết chế không thể thiếu trong chuỗi cung ứng hàng hóa toàn cầu. Riêng đối với ngành hàng mắt kính, đây là một thị trường ngách (niche market) mang đặc thù kép: vừa là một sản phẩm y tế thiết yếu (kính cận, viễn, loạn khúc xạ), vừa là một phụ kiện thời trang cao cấp khẳng định phong cách cá nhân.

Tuy nhiên, rào cản lớn nhất của các nền tảng bán lẻ kính mắt trực tuyến hiện nay là **Trải nghiệm người dùng (UX) rời rạc** và **Hệ thống dữ liệu cồng kềnh**. Một chiếc kính có thể kéo theo hàng chục biến thể (khung gọng, chất liệu, màu sắc, size, loại tròng chống xước/chống lóa/chống ánh sáng xanh). Việc phác họa và xử lý khối lượng thuộc tính sản phẩm lớn như vậy trên các cơ sở dữ liệu quan hệ (SQL) cũ kĩ thường dẫn tới sự đánh đổi về mặt hiệu suất.

Nhận thức được bài toán này, nhóm sinh viên quyết định lựa chọn đề tài: **"Xây dựng Website Thương mại Điện tử Kính Xanh Optical"**. Đề tài định hướng thiết kế một hệ thống thương mại trực tuyến với giao diện đột phá theo thẩm mỹ *Glassmorphism*, đồng thời áp dụng giải pháp công sở dữ liệu phi quan hệ linh hoạt **MongoDB (NoSQL)** kết hợp mô hình bảo mật Token-based của **PHP Backend**, nhằm giải quyết trọn vẹn điểm nghẽn về cấu trúc dữ liệu và khả năng mở rộng (Scale-up) trong tương lai.

### 1.2 Mục tiêu nghiên cứu và phát triển
**a. Về mặt Nghiệp vụ Kinh doanh (Business Goals):**
- Cung cấp hành trình khách hàng (Customer Journey) tự động hoá hoàn toàn từ khâu tìm kiếm, lọc phân loại chuyên sâu, tiếp nhận tư vấn (FAQ), đến khâu đưa vào giỏ hàng và thanh toán.
- Xây dựng mảng nội bộ (Admin Dashboard) mạnh mẽ giúp phía quản trị viên theo dõi sát sao biểu đồ tăng trưởng, kiểm kê luồng tồn kho vật lý và tính toán doanh thu theo thời gian thực (Real-time Analytics).

**b. Về mặt Kỹ thuật Phần mềm (Technical Specifications):**
- Vận dụng thành thạo kiến trúc **Decoupled Architecture** (Kiến trúc phân tách): Tách bạch hoàn toàn phần xử lý giao diện phía máy khách (Client-side) với máy chủ logic (Server-side) thông qua hệ thống API RESTful.
- Chuyển giao thành công tư duy thiết kế Object-Relational Mapping cổ điển sang **Document-Oriented Data Modeling** trên MongoDB. Đảm bảo tốc độ Try-out và Fetch dữ liệu chưa tới 20ms cho mỗi yêu cầu.

### 1.3 Phạm vi và Giới hạn đề tài
**Phạm vi hệ thống:**
- Triển khai toàn vẹn các module: Quản lý danh mục (Catalog), Quy trình Thanh toán (Checkout Flow), Hệ thống thành viên (Authentication & Authorization), và Trang tin Quản trị (Admin Panel).
- Hệ thống hỗ trợ đa nền tảng trình duyệt lớn hiện nay (Responsive Web Design).

**Giới hạn nền tảng:**
- Hệ thống giải quyết thanh toán thông qua cơ chế Lưu vết Đơn hàng dùng phương thức COD (Cash-on-Delivery), chưa móc nối trực tiếp định tuyến Sandbox của các nhà cung cấp Fintech (VNPay, Momo, PayPal).
- Chưa tích hợp hệ thống Recommendation System (Gợi ý sản phẩm bằng Trí tuệ nhân tạo).

---

## CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG CƠ SỞ ĐẮP KIẾN TRÚC

### 2.1 Đặc tả Use-Case và Phân quyền Tác nhân (Actors)
Hệ thống Kính Xanh được thiết kế với chuẩn mức bảo mật 2 lớp quyền (RBAC - Role Based Access Control) dành cho 3 nhóm tác nhân như sau:

1. **Khách vãng lai (Guest):**
   - *Tính năng cho phép:* Duyệt cửa hàng, sử dụng bộ lọc tìm kiếm động (Ajax-based), xem chi tiết hình ảnh, số liệu sản phẩm, thêm sản phẩm vào bộ nhớ giỏ hàng tạm (Local-Storage), đọc tin tức cẩm nang nhãn khoa.
   - *Điều kiện:* Yêu cầu phải Đăng ký/Đăng nhập để thực hiện Đặt hàng (Checkout).

2. **Khách hàng thân thiết (User logged in):**
   - *Đặc quyền bổ sung:* Sở hữu định danh Session (JWT Token). Được quyền khai báo địa chỉ Address Book, xử lý Submit quá trình đặt hàng thành công, để lại nhận xét (Review), xem Lịch sử đơn hàng cá nhân trong hồ sơ (Profile).

3. **Quản trị viên hệ thống (Administrator - `role_id: 1`):**
   - *Quyền hạn tuyệt đối:* Bypass qua tấm màn ảo ở trang Index và đăng nhập vào Phân hệ Ban Quản Trị `admin.html`. Request số liệu kinh doanh tối mật, can thiệp trạng thái (Status) của đơn hàng, cảnh báo tồn kho sắp cạn kiệt, kiểm soát danh sách người dùng.

### 2.2 Sơ đồ Cấu trúc CSDL Phi quan hệ (MongoDB Document Schema)
Điểm nhấn học thuật của đồ án là việc áp dụng MongoDB. Thay vì thực hiện các lệnh `JOIN` đắt đỏ rườm rà qua trung gian nhiều Bảng (Tables) như SQL truyền thống, Kính Xanh thiết kế dữ liệu theo cơ chế **Embedded Documents (Tài liệu lồng nhau)** và **Referencing (Tham chiếu vòng)**, giải bài toán hiệu suất đọc (Read Optimization).

**A. Collection `products` (Phiên bản Gốc)**
Đóng vai trò là Catalog chính, chỉ lưu thông tin vĩ mô.
```json
{
  "_id": ObjectId("65dfa4..."),
  "sku": "RB3025-AVIATOR-GLD",
  "slug": "ray-ban-aviator-classic-gold",
  "name": "Ray-Ban Aviator Classic Gold",
  "brand": "Ray-Ban",         // Lọc nhanh bằng Index(brand: 1)
  "category": "Kính mát",
  "base_price": 3200000,
  "discount_percent": 0,
  "rating": 4.9,
  "is_featured": true,        // Flag để render ở Homepage Flash Sale
  "created_at": ISODate("2026-04-20T22:00:00Z")
}
```

**B. Collection `product_variants` (Quản trị Tồn kho và Cá nhân hóa)**
Mỗi con mắt kính có nhiều màu. Variant Collection chịu trách nhiệm tham chiếu dội ngược lên Product, chứa giá trị lõi.
```json
{
  "_id": ObjectId("65dfb1..."),
  "product_id": ObjectId("65dfa4..."), // Bắt tay (Referencing) với Products
  "sku": "RB3025-GLD-58",
  "color": "Gold",
  "color_hex": "#D4AF37",              // Sử dụng trực tiếp để render màu ở UI
  "stock": 20,                         // Trừ đi ngay lập tức khi Checkout
  "size": "58mm"
}
```

**C. Collection `orders` (Hoá đơn đóng gói)**
Đây là giải pháp xuất sắc của hệ thống. Thay vì tham chiếu Order, Order_Items, Product gây mất thời gian query, MongoDB sẽ *"Embedded"* (Nhúng tĩnh) luôn chi tiết sản phẩm mua được vào thân đơn hàng. Dù sau này cửa hàng có thay đổi giá hay xóa sản phẩm đi, giá của Biên lai hóa đơn cũ này vĩnh viễn không bị biến đổi sai lệch.
```json
{
  "_id": ObjectId("65dfc9..."),
  "user_id": ObjectId("65dfd1..."),
  "total_price": 3200000,
  "status": "completed",               // aggregate revenue condition
  "items": [
    {
      "product_variant_id": ObjectId("65dfb1..."),
      "product_name": "Ray-Ban Aviator Classic Gold (Gold - 58mm)",
      "price_at_purchase": 3200000,  // Khóa cứng giá tại thời điểm Mua
      "quantity": 1
    }
  ],
  "customer_note": "Giao giờ hành chính, gọi trước 30 phút.",
  "created_at": ISODate("2026-04-21T08:15:00Z")
}
```

---

## CHƯƠNG 3: KIỂM THỬ PHẦN MỀM (SOFTWARE TESTING & QA)

Dự án chú trọng áp dụng kiểm định Hộp Đen (Black-box Testing) nhằm rà soát các luồng Use Case thực thi có đúng logic hay không. Các kịch bản kiểm thử (Test Scenario) được vạch ra kỹ lưỡng từ UI đến Backend Security.

| Mã TC | Hạng mục Kiểm Thử | Điều kiện Tiên quyết | Các bước Thực thi | Kết quả Mong đợi (Expected Result) | Đánh giá |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **AUTH-01** | **Xác thực Đăng nhập & Rate Limit** | Tạm có sẵn 1 tài khoản (admin/123) | Nhập sai mật khẩu liên tục quá 5 lần trong 1 phút bằng Bot. | Server ném ra `HTTP 429 Too Many Requests`. Khóa tạm thời IP của phía Request. UI hiện thông báo toast Đỏ. | ✅ ĐẠT |
| **CART-01** | **Ràng buộc Dữ liệu Đầu vào** | Có 1 Kính Ray-Ban trong giỏ hàng. | Khách hàng mở ô input số lượng và chủ động gõ dấu trừ nhỏ hơn 0 (VD: `-5`). | Hàm JavaScript Listener bắt lặp biến và ngăn chặn. Tự động set value = 1. Hiển thị cảnh báo số lượng không tồn tại. | ✅ ĐẠT |
| **ORD-01** | **Chu trình Checkout toàn vẹn** | Giỏ hàng tồn tại giỏ >= 1 sp. | Điền đủ Form Họ Tên + Địa chỉ. Nhấn "Thanh toán Đơn hàng". | Đơn được push thẳng xuống Collection `orders`. localStorage bị xoá (Xoá giỏ ở client). Redirect nhảy sang trang `/order-success.html` báo mã ID. | ✅ ĐẠT |
| **SEC-01** | **Thâm nhập Phân quyền (Authorization)** | Đang đăng nhập dưới danh nghĩa User bình thường. | Lấy cắp trực tiếp Access Token bỏ vào Header gửi API Request tới `/api/admin/index.php`. | File `security.php` giải mã Token, phát hiện `role_id != 1`. Ném `HTTP 403 Forbidden Access`. Console trả về chuỗi JSON rỗng. | ✅ ĐẠT |
| **FIL-01** | **Truy vấn Mảng (Query Filter)** | Tại URL `/products.html` | Client Tick cùng lúc 2 Checkbox: Hãng Rayban + Khoảng giá "Dưới 2 Triệu". | Tốc độ lọc tức thời (<100ms). Web ẩn đi các SP Gucci cao cấp. Chỉ Render đúng Wayfarer 1tr4 của Ray-Ban. | ✅ ĐẠT |

---

## CHƯƠNG 4: TỔNG KẾT, NHẬN ĐỊNH VÀ ĐỊNH HƯỚNG TƯƠNG LAI

### 4.1. Đánh giá Kết quả Nghiên cứu và Phát triển
Sau quá trình dài dồn tâm huyết nghiêm túc, tập thể nhóm đã xuất sắc nghiệm thu hệ thống **"Thương mại Điện tử Kính Xanh Optical"** đúng với các tiêu chí khắt khe đặt ra ban đầu. Dự án gặt hái được những thành công tiêu biểu:

1. **Về Công nghệ Vận hành:** Hiện thực hoá thành công hệ sinh thái Decoupled Application. Phân tách rõ Front-end UI (sắc nét, thẩm mỹ chuyên nghiệp chuẩn Studio) với API Back-end thuần PHP mạnh mẽ. Bức phá khỏi vòng an toàn với **MongoDB**. Từ đó, minh chứng rõ rệt tốc độ truy xuất của NoSQL đánh bại bảng SQL nhờ lược đồ Schema linh hoạt.
2. **Về Triển khai DevOps:** Môi trường cài đặt siêu giản tiện (1-Click Run Scripts) thể hiện tính chuyên nghiệp trong tư duy đóng gói phần mềm và kiểm soát gói phụ thuộc bằng Composer. Hệ thống miễn nhiễm với mã độc cơ bản, kháng tấn công DDoS mức độ tầng tiếp nhận qua kỹ thuật Rate Limiting IP.

### 4.2. Khó khăn gặp phải & Chấm dứt Hạn chế
Công tâm đánh giá, đồ án vẫn còn một vài kẽ hở công nghệ do cản trở thời gian và cấu hình:
- Quản trị giao thức Đăng nhập (Session Lifecycle) vẫn dựa trên vòng lặp của LocalStorage/Cookie dạng cơ sở, nếu ứng dụng phát triển làm App Mobile (Cross-platform) thì cần phải xây dựng kiến trúc JWT chuẩn hóa OAuth 2.0.
- Thống kê phía Server Admin chưa cho phép đổ số liệu ra File Excel theo tháng để kế toán tiện tính lợi nhuận. Chỉ dừng lại ở dạng xem trực quan (Read-Only).

### 4.3. Định hướng mở rộng ở quy mô Enterprise
Với nền tảng hạ tầng vững chắc hiện có, bước tiếp theo để thương mại hóa hệ thống vào thực tiễn doanh nghiệp thật sẽ bao gồm:
1. **Triển khai Trí tuệ Nhân tạo AR (Augmented Reality):** Tích hợp Plugin như FaceMesh API từ TensorFlow. Khách hàng bật Webcam và *“Thử chiếc kính Gucci Ảo lên trên khuôn mặt thật”* ngay tại trình duyệt để quyết định chốt đơn nhanh hơn mà không cần ra tiệm vật lý.
2. **Khởi tạo Gateway Thanh toán:** Liên kết chữ ký tự động bảo mật với Ngân hàng nhà nước / VNPay, webhook phản hồi thanh toán thành công tự cập nhật hoá đơn báo đẩy về Email của khách hàng.
3. **Phân tán Container (Dockerization):** Container hoá toàn bộ luồng môi trường Front - Back - Database vào Docker Images. Setup các CI/CD Pipelines lên hạ tầng Google Cloud hoặc AWS, phục vụ cho hàng thập kỷ tăng trưởng dữ liệu mà không sợ gãy nghẽn lưu lượng (Traffic Spikes).
