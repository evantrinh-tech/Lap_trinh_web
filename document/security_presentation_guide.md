# Hướng dẫn Thuyết trình: Mô hình OSI & Cơ chế Xác thực (Session/Cookie/Token)

Tài liệu này cung cấp kịch bản chi tiết và cách sử dụng các công cụ IDE (đặc biệt là mục Sidebar của GitNexus) để thuyết trình phần Bảo mật trong dự án Kính Xanh một cách trực quan và thuyết phục nhất.

---

## 1. Bí kíp sử dụng Sidebar của GitNexus để ăn điểm

Giảng viên thường đánh giá rất cao sinh viên biết kết hợp giữa **lý thuyết** và **thực tế mã nguồn (codebase)**. Thay vì chỉ đọc slide, bạn hãy sử dụng Sidebar của GitNexus để minh họa:

- **Sử dụng tính năng GitNexus Context / Trò chuyện:** Trong lúc thuyết trình, bạn có thể gõ vào sidebar GitNexus câu lệnh/yêu cầu như: *"Hiển thị luồng thực thi của hàm giải mã JWT"* hoặc *"Tìm các vị trí gắn header Authorization"*. GitNexus sẽ ngay lập tức highlight các đoạn code trong backend và frontend, chứng minh cho thầy cô thấy hệ thống thực sự được cài đặt theo lý thuyết bạn đang nói.
- **Chia đôi màn hình (Split View):** Mở file `SECURITY.md` (đã render Markdown) ở nửa màn hình trái để làm "Slide" dàn ý. Ở nửa màn hình phải, hãy mở file code thực tế như `backend/lib/security.php` hoặc `frontend/js/auth-api.js`.
- **Chỉ điểm (Point and Talk):** Khi nhắc đến "Thẻ VIP" hay "Quán Bar", con trỏ chuột của bạn hãy bôi đen đoạn mã sinh ra Token (`jwt_encode`) ở nửa màn hình bên phải. Điều này tạo ra sức thuyết phục tuyệt đối.

---

## 2. Kịch bản Thuyết trình Chi tiết (Khoảng 5 - 7 phút)

### Phần A: Dẫn nhập bằng Mô hình OSI (2 phút)
*(Thao tác: Cuộn file `SECURITY.md` đến phần Mô hình OSI 7 tầng)*

**"Kính thưa thầy/cô, để bảo vệ một hệ thống thương mại điện tử thực tế, nhóm em không chỉ áp dụng code máy móc mà dựa trên nền tảng kiến trúc mạng chuẩn - đó là Mô hình OSI 7 tầng.**

Để dễ hình dung thay vì lý thuyết mạng khô khan, chúng em ví von luồng dữ liệu đi qua 7 tầng này như một hệ thống bưu cục chuyển phát. 
Từ tầng thấp nhất là **Vật lý** (giống như người vác hàng cơ bắp), lên đến tầng **Network** (Bưu tá dò đường bằng IP), và cao nhất là tầng **Ứng dụng - Application** (Giao diện hòm thư của người dùng).

Việc tư duy theo tầng giúp nhóm em biết phải chặn đứng các đợt tấn công ở đâu. Ví dụ, hệ thống chặn IP vi phạm bằng Blacklist ở tầng Network, và chặn tấn công chèn mã độc (Payload Injection) ở tầng Ứng dụng."

### Phần B: Phân tích sâu "Quán Bar" - Session vs Cookie vs Token (3 phút)
*(Thao tác: Kéo xuống phần so sánh Session/Cookie/Token, vừa nói vừa nhấn giọng ở các từ khóa "Sổ cái", "Túi áo", "Thẻ VIP")*

**"Điểm sáng thứ hai ở tầng Ứng dụng là việc quản lý trạng thái người dùng. Làm sao hệ thống biết ai đang thao tác để cho phép họ mua hàng?**
Em xin phép ví von Server Backend của dự án như một **Quán Bar** và người dùng là **Khách hàng**. Có 3 cách tiếp cận:

1. **Cách 1 - Session (Sổ cái của quán):** Quán cấp cho bạn một vé nhựa số 123 và ghi tên bạn vào sổ cái. Cách này an toàn, nhưng khi quán đông khách (nhiều truy cập), việc lật sổ cái liên tục khiến Server quá tải, cạn kiệt RAM. Rất khó mở rộng chi nhánh.
2. **Cách 2 - Cookie (Túi áo tự động):** Để tiện hơn, quán thiết kế cho khách một cái túi áo. Khách nhét vé vào đó, bảo vệ tự động lấy ra kiểm tra. Lập trình viên rất nhàn vì trình duyệt tự động gửi. Tuy nhiên, rủi ro là hacker có thể lợi dụng túi áo này để gửi request độc hại thay bạn (lỗ hổng CSRF).
3. **Cách 3 - Token / JWT (Thẻ VIP có chữ ký):** Đây là giải pháp đột phá nhất! Quán bỏ luôn sổ cái. Quán cấp hẳn cho khách một tấm Thẻ VIP có chữ ký số hóa của Giám đốc. Lần sau tới, bảo vệ chỉ nhìn chữ ký là cho qua, không tốn công tra sổ."

### Phần C: Khẳng định kiến trúc của dự án Kính Xanh (2 phút)
*(Thao tác: Mở file `frontend/js/auth-api.js` chỗ lưu `localStorage` hoặc code gắn Header `Authorization` lên để minh họa)*

**"Áp dụng vào thực tế dự án Kính Xanh, chúng em đã triển khai Cách số 3: Sử dụng JWT Token và lưu trữ hoàn toàn ở localStorage.**

Quyết định này mang lại 2 ưu điểm mang tính sống còn cho hệ thống:
- **Thứ nhất (Không dùng Session):** Server hoàn toàn Stateless (không trạng thái), không tốn RAM nhớ user. Sau này nếu Kính Xanh mở rộng từ 1 Server lên 10 Server thì thẻ VIP (Token) mang qua server nào cũng quét được, đảm bảo hiệu năng tối đa.
- **Thứ hai (Không dùng Cookie):** Chúng em bắt buộc Client (Frontend) phải tự lấy Token gắn vào Header Authorization mỗi khi gọi API. Điều này chặn đứng 100% rủi ro bị tấn công giả mạo CSRF qua Cookie.

Hệ thống vừa nhẹ, vừa siêu an toàn!"

---

> **💡 Lời khuyên cuối:** Bạn hãy nói phần "Quán Bar" với tông giọng tự tin, mỉm cười nhẹ. Cách ví von này thường làm không khí buổi bảo vệ đồ án bớt căng thẳng và khiến giảng viên đánh giá cực cao tư duy trừu tượng hóa vấn đề của bạn!
