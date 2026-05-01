# Hướng Dẫn Thuyết Trình Bảo Mật Qua GitNexus Sidebar
*(Chuyên đề: Phân tích Mô hình OSI & Cơ chế Token vs Cookie vs Session)*

## 1. Mục Đích & Sức Mạnh Của GitNexus
Thay vì chỉ trình chiếu PowerPoint khô khan, bạn sẽ dùng **GitNexus Sidebar** (khung bên trái với đồ thị File Tree & Relationships) để trình diễn trực tiếp trên Source Code. Điều này chứng minh cho hội đồng chấm thi thấy bạn:
- Hiểu cực sâu kiến trúc hệ thống và sự liên kết giữa các file (như hiển thị ở góc dưới GitNexus: *672 nodes, 1067 edges*).
- Nắm rõ luồng đi của dữ liệu từ Frontend (Client) xuống Backend (Server).
- Thực sự hiểu bản chất của Bảo mật mạng và Áp dụng lý thuyết (OSI, JWT) vào code thực tế.

---

## 2. Chuẩn Bị Thao Tác Trên GitNexus
Trước khi bắt đầu nói, hãy mở GitNexus Sidebar lên, cấu hình hiển thị dạng Cây thư mục (File Tree) sao cho các file/thư mục sau đây nằm trong tầm nhìn và dễ click nhất:
- Thư mục `document/` (chứa `SECURITY.md`)
- Thư mục `backend/lib/` (chứa `security.php`)
- Thư mục `js/` (chứa `auth-api.js` và `main.js`)

---

## 3. Kịch Bản Thuyết Trình Chi Tiết

### Phần A: Mở đầu bằng Mô hình OSI 7 Tầng trên thực tế code
*(Thao tác: Click vào file `document/SECURITY.md` để mở tài liệu làm sườn, sau đó dùng chuột lia qua các thư mục trên GitNexus Sidebar để minh họa)*

**Lời dẫn:** 
"Thưa thầy/cô, để xây dựng cơ chế bảo mật cho website Kính Xanh, nhóm em không làm mò mẫm mà áp dụng tư duy từ nền tảng **Mô hình mạng OSI 7 Tầng**. Thay vì nói lý thuyết suông, em xin phép ánh xạ trực tiếp mô hình này vào cấu trúc Source Code đang hiển thị trên GitNexus bên trái màn hình:

- **Tầng 7 - Application (Ứng dụng):** Nằm chính ở thư mục `js/` và các file giao diện `.html`. Đây là nơi tương tác trực tiếp với người dùng cuối.
- **Tầng 6 - Presentation (Trình bày):** Thể hiện ở việc code JS và Backend liên tục *Parse JSON* và xử lý mã hóa/giải mã cấu trúc dữ liệu.
- **Tầng 4 - Transport (Giao vận):** Chính là khi các file trong `js/` thực hiện gọi `fetch()` API qua giao thức HTTP/TCP (đang chạy trên cổng 8000 của hệ thống).

*(Thao tác: Click đúp hoặc trỏ chuột nhấn mạnh vào file `backend/lib/security.php`)*
"Và để bảo vệ toàn bộ cấu trúc này, nhóm em xây dựng riêng module `security.php`. Nó hoạt động như một Firewall ở Tầng 7 (Application Layer), đứng ra hứng chịu, phân tích và chặn đứng các Request độc hại, chống DDoS hay Rate Limiting trước khi luồng dữ liệu tiến sâu vào Database."

---

### Phần B: Bảo vệ phiên đăng nhập - Tại sao bỏ Cookie/Session để chọn JWT Token?
*(Thao tác: Click vào file `js/auth-api.js` trên GitNexus. Nếu GitNexus có tính năng highlight edges, hãy để nó show các đường liên kết từ auth-api.js gọi xuống backend)*

**Lời dẫn:**
"Tiếp theo, một điểm nhấn quan trọng trong hệ thống Kính Xanh là quá trình Xác thực người dùng (Authentication). Nhóm em đã quyết định **loại bỏ hoàn toàn Cookie và Session**, chuyển sang dùng **JWT Token**. Lựa chọn này giải quyết được 3 bài toán lớn:

**1. Hạn chế của Session (Sổ cái máy chủ):** 
*(Thao tác: Di chuột khoanh vùng thư mục `backend`)*
"Nếu dùng Session, mỗi lần khách đăng nhập, Backend PHP phải cấp phát RAM để lưu lại trạng thái, giống như một quán bar phải ghi tên khách vào Sổ cái. Nếu website có đợt Sale lớn, hàng ngàn khách truy cập, Server sẽ bị quá tải bộ nhớ và cực kỳ khó mở rộng (Scale)."

**2. Rủi ro của Cookie (Túi áo tự động):** 
"Nếu lưu Session ID vào Cookie, điểm yếu là trình duyệt sẽ *tự động* gửi Cookie đó lên Server ở mọi Request. Sự "tự động" tiện lợi này lại là khe hở chết người tạo ra lỗ hổng **CSRF** (Kẻ gian dụ người dùng bấm link độc và mượn Cookie để thực hiện tác vụ giả mạo)."

**3. Sức mạnh của JWT Token (Thẻ VIP Độc lập):** 
*(Thao tác: Trỏ chuột vào code file `js/auth-api.js`, làm nổi bật đoạn code xử lý Token)*
"Thay vào đó, Kính Xanh dùng Token. Khi User đăng nhập thành công, Backend tạo ra một cái 'Thẻ VIP' có chữ ký điện tử mã hóa phức tạp, giao cho Frontend tự cất vào `localStorage`. 

Mỗi lần Frontend (như trang Profile hay Cart) muốn gọi API, nó phải chủ động 'móc Thẻ VIP' ra, đính kèm vào Header (`Authorization: Bearer`). 
*(Thao tác: Lia chuột lại sang `backend/lib/security.php`)*
Lúc này, Backend chỉ cần lấy Token ra, dùng Secret Key kiểm tra chữ ký. Thấy chữ ký chuẩn là xác nhận người dùng ngay **mà không cần mất công tra cứu trong Database hay RAM** (cơ chế Stateless). 

Cách này vừa giúp Server chạy cực nhẹ, vừa chặt đứt hoàn toàn rủi ro bị tấn công CSRF vì chẳng có Cookie nào tự động bay lên Server cả!"

---

## 4. Các "Tip" Ghi Điểm Tuyệt Đối
1. **Liên kết thị giác (Visual Connection):** Giảng viên cực kỳ thích sinh viên "Nói đến đâu - Chỉ code đến đó". GitNexus sinh ra là để làm việc này. Số lượng 672 node/1067 edges thể hiện Project có độ phức tạp cao, khi bạn tự tin điều hướng qua Sidebar, bạn chứng minh mình là người viết code, không phải đi copy.
2. **Sẵn sàng trả lời phản biện:** 
   - *Hỏi:* "Thế lưu Token ở localStorage bị tấn công XSS lấy cắp thì sao?"
   - *Đáp trả:* "Dạ đúng, LocalStorage dễ bị XSS. Nhưng trong dự án này (như thầy thấy ở `security.php`), tụi em đã set Header `X-XSS-Protection: 1; mode=block` và kiểm soát chặt các input đầu vào để chống chèn script độc hại. Đánh đổi một chút rủi ro XSS lấy việc chống được CSRF và tăng hiệu năng Server là một Trade-off hoàn toàn xứng đáng cho một site E-commerce ở mức độ này ạ."
