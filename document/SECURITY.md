# Tài Liệu Bảo Mật – Kính Xanh Optical

> Mô tả toàn bộ cơ chế bảo mật: DDoS Shield, JWT, mật khẩu, CORS và phân quyền.

---

## Mô Hình OSI 7 Tầng & Giải Thích Dễ Nhớ

Mô hình OSI là nền tảng để hiểu rõ cách thức dữ liệu đi qua mạng và là cơ sở để thiết kế các cơ chế bảo mật (như mã hóa, firewall, chống DDoS).

**Mẹo nhớ thứ tự (Từ tầng 1 đến tầng 7 - Dưới lên trên):**
> **"Vật lý – Liên kết – Mạng – Giao vận – Phiên – Trình bày – Ứng dụng"**
> *(Hoặc câu tiếng Anh vui: **P**lease **D**o **N**ot **T**hrow **S**ausage **P**izza **A**way)*

**Giải thích chức năng từng tầng (Theo kiểu đời thường dễ hiểu):**

1. **Physical (Tầng Vật lý) – "Người chuyển phát cơ bắp"**
   - **Làm gì:** Truyền dữ liệu thô (chuỗi bit 0 và 1) qua các môi trường vật lý (điện, ánh sáng, sóng vô tuyến).
   - **Ví dụ:** Cáp mạng, sóng Wi-Fi, Bluetooth, đầu nối RJ45.

2. **Data Link (Tầng Liên kết dữ liệu) – "Bác bảo vệ nội bộ"**
   - **Làm gì:** Truyền dữ liệu giữa 2 thiết bị *trong cùng một mạng cục bộ (LAN)*. Nó dùng địa chỉ **MAC** (mã định danh phần cứng) để nhận biết thiết bị.
   - **Ví dụ:** Các Switch mạng chia cổng trong phòng máy tính.

3. **Network (Tầng Mạng) – "Bưu tá dẫn đường"**
   - **Làm gì:** Tìm đường đi (định tuyến) tốt nhất để gửi dữ liệu xuyên qua nhiều mạng khác nhau trên thế giới. Nó dùng địa chỉ **IP**.
   - **Ví dụ:** Router (cục phát Wi-Fi) giúp điện thoại của bạn kết nối được với Server của Google tại Mỹ.

4. **Transport (Tầng Giao vận) – "Quản lý bưu cục"**
   - **Làm gì:** Băm nhỏ dữ liệu lớn ra để dễ gửi, và lắp ráp lại ở đích đến. Có 2 phong cách giao hàng: Giao an toàn tuyệt đối, mất là gửi lại (TCP) hoặc Giao siêu tốc, rớt đồ kệ nó (UDP).
   - **Ví dụ:** Tải file, lướt web bắt buộc dùng TCP. Gọi Video Call, Livestream game thường dùng UDP để đỡ lag.

5. **Session (Tầng Phiên) – "Lễ tân"**
   - **Làm gì:** Mở cuộc trò chuyện (thiết lập), duy trì kết nối, và ngắt kết nối (kết thúc phiên) giữa 2 hệ thống.
   - **Ví dụ:** App ngân hàng tự động văng ra ngoài, yêu cầu bạn đăng nhập lại nếu để máy không quá 5 phút (đóng Session).

6. **Presentation (Tầng Trình bày) – "Thông dịch viên kiêm Thợ khóa"**
   - **Làm gì:** Chuyển đổi định dạng dữ liệu (dịch ngôn ngữ), nén cho nhẹ, và quan trọng nhất đối với bảo mật là **Mã hóa / Giải mã**.
   - **Ví dụ:** Mã hóa mật khẩu của bạn thành chuỗi ký tự loằng ngoằng (SSL/TLS) trước khi bay trên mạng để hacker không dịch được.

7. **Application (Tầng Ứng dụng) – "Giao diện cửa hàng"**
   - **Làm gì:** Gần với bạn nhất, là nơi các phần mềm (Trình duyệt, Email app, Game) sử dụng mạng để phục vụ bạn.
   - **Ví dụ:** Giao thức lướt Web (HTTP/HTTPS), gửi Email (SMTP/IMAP), tải file (FTP).

---

## Hiểu Rõ Cookie, Session & Token (Bản Chất & Phân Biệt)

Trong lập trình Web, để duy trì trạng thái đăng nhập (ai là ai), chúng ta có 3 khái niệm cốt lõi. Hãy tưởng tượng server là **một quán bar** và client (người dùng) là **khách hàng**.

### 1. Session (Phiên làm việc) – "Sổ ghi nợ của quán bar"
- **Cách hoạt động:** Khi bạn đăng nhập thành công, Server tạo ra một Session (lưu trên RAM hoặc Database của server) đánh dấu bạn đã login, và trả về cho bạn một cái mã gọi là `Session ID`. Mỗi lần bạn thao tác, bạn gửi kèm `Session ID` này lên. Server lật "sổ" ra dò xem ID này của ai.
- **Ví dụ quán bar:** Lần đầu bạn tới, quán bar ghi tên bạn vào **sổ cái** (Session) và phát cho bạn **vé nhựa số 123** (Session ID). Những lần sau, bạn chỉ cần chìa vé nhựa ra, quán sẽ tự động biết bạn là ai nhờ tra sổ cái.
- **Ưu điểm:** An toàn tuyệt đối (vì dữ liệu nhạy cảm nằm hết trên server).
- **Nhược điểm:** Server phải nhớ (tốn RAM), nếu quán bar (server) đông quá sẽ bị quá tải. Khó mở rộng hệ thống (nếu có 2 server A và B, bạn có vé ở server A sang server B sẽ không được nhận ra nếu 2 sổ cái không liên thông).

### 2. Cookie – "Cái túi áo của khách hàng"
- **Cách hoạt động:** Cookie thực chất là **nơi lưu trữ** (một file text nhỏ) nằm ở trình duyệt của người dùng. Cookie hay được dùng để đựng `Session ID` (hoặc `Token`). Điểm đặc biệt: Trình duyệt *tự động* gửi kèm Cookie trong mọi Request lên Server ở cùng tên miền mà không cần dev phải tự viết code gửi.
- **Ví dụ quán bar:** Thay vì bắt bạn cầm khư khư cái vé nhựa bằng tay, quán bar thiết kế riêng một cái **túi áo** (Cookie) để bỏ vé nhựa (Session ID) vào đó. Mỗi lần bạn bước qua cửa, bảo vệ tự động thò tay vào túi áo lấy vé ra kiểm tra, bạn không cần tự móc ra.
- **Ưu điểm:** Tự động hóa việc gửi/nhận, rất tiện lợi cho lập trình viên.
- **Nhược điểm:** Dễ bị tấn công CSRF (Giả mạo yêu cầu) nếu bị kẻ xấu lợi dụng "túi áo" gửi request độc hại thay bạn. Có giới hạn dung lượng nhỏ (khoảng 4KB).

### 3. Token (Ví dụ: JWT - JSON Web Token) – "Thẻ Căn cước công dân"
- **Cách hoạt động:** Khi bạn đăng nhập, Server xác thực xong sẽ đóng dấu ký tên vào một cái thẻ (`Token`) có ghi sẵn tên, quyền hạn, thời gian hết hạn... rồi giao hẳn cho bạn giữ. Lần sau bạn đến, Server chỉ cần nhìn con dấu chữ ký (signature) trên Token là biết thẻ thật hay giả, **không cần phải tra sổ cái nữa**.
- **Ví dụ quán bar:** Quán bar làm cho bạn một cái **thẻ VIP** (Token) có chữ ký xịn của giám đốc, trên thẻ ghi rõ "Nguyễn Văn A - 20 tuổi". Lần sau bạn đến, bảo vệ chỉ cần xem thẻ, thấy chữ ký chuẩn và chưa hết hạn là cho vào. Quán **không cần tốn công tra sổ cái nữa**.
- **Ưu điểm:** Không tốn bộ nhớ server (Stateless). Cực kỳ dễ mở rộng (Scalable) - có mở 100 server thì bảo vệ cứ thấy chữ ký là cho vào, không cần chung một sổ cái. Tránh được lỗi CSRF nếu không lưu trong Cookie.
- **Nhược điểm:** Khó thu hồi (revoke) thẻ ngay lập tức một khi đã phát ra (trừ khi thẻ hết hạn hoặc server phải làm cơ chế "Blacklist" rườm rà). Nếu lộ Token là bị đoạt quyền, nên cần gửi qua HTTPS chặt chẽ.

**Tóm lại (Luồng đi ở dự án Kính Xanh):**
Dự án Kính Xanh của chúng ta sử dụng **JWT Token** và lưu trữ ở `localStorage` của trình duyệt. 
Tức là ta cấp "Thẻ VIP" cho khách tự giữ trong ví, mỗi lần gọi API khách phải tự tay lấy thẻ ra (qua HTTP header `Authorization: Bearer <token>`) trình cho Server. 
- **Không dùng Session:** Để giảm tải cho Server và dễ dàng mở rộng.
- **Không dùng Cookie:** Để tránh hoàn toàn rủi ro bị tấn công CSRF!

---

## 1. Tổng Quan

Hệ thống bảo mật được xây dựng tùy chỉnh trong `backend/lib/security.php`, không phụ thuộc thư viện bên ngoài. Gồm **6 lớp bảo vệ** kích hoạt qua hàm `applyDDoSProtection()`.

```
Request đến
    │
    ▼
[Lớp 1] IP Blacklist ────── Bị ban? → 403 Forbidden
    │
    ▼
[Lớp 2] Rate Limiting ───── Vượt giới hạn? → 429 Too Many Requests
    │
    ▼
[Lớp 3] HTTP Headers ────── User-Agent trống? → 400 Bad Request
    │
    ▼
[Lớp 4] Payload Check ───── Body > 512KB / Injection? → 400 / 413
    │
    ▼
[Lớp 5] Ghi Vi Phạm ─────── Tự động ban IP khi vượt ngưỡng
    │
    ▼
[Lớp 6] Garbage Collection ─ Dọn dữ liệu cũ (xác suất 1%)
    │
    ▼
Xử lý request bình thường
```

---

## 2. Cách Kích Hoạt

Gọi ở đầu mỗi endpoint cần bảo vệ:

```php
applyDDoSProtection(
    rateLimit:    60,       // Số request tối đa
    rateMinutes:  1,        // Trong khoảng thời gian (phút)
    endpoint:     'cart',   // Tên endpoint (dùng để phân biệt rate limit)
    checkPayload: true      // Có kiểm tra body không
);
```

**Các endpoint đã áp dụng DDoS protection:**

| Endpoint | Rate Limit |
|---|---|
| `auth/login.php` | 5 req/phút |
| `auth/register.php` | 3 req/phút |
| `auth/me.php` | 30 req/phút |
| `products.php` | 120 req/phút |
| `cart/index.php` | 60 req/phút |
| `orders/index.php` | 10 req/phút |
| `admin/index.php` | 30 req/phút |

**Các endpoint chưa áp dụng DDoS protection (cần bổ sung khi production):**
- `product-detail.php`
- `product-variants.php`
- `reviews/index.php`

---

## 3. Chi Tiết 6 Lớp Bảo Vệ

### Lớp 1 – IP Blacklist

Kiểm tra IP trong collection `ip_blacklist` trước khi xử lý bất kỳ thứ gì.

- Block tạm thời: **10 phút** (`DDOS_BLOCK_DURATION = 600`)
- Block vĩnh viễn: khi vi phạm ≥ **5 lần** (`DDOS_BAN_THRESHOLD = 5`)
- Hỗ trợ phát hiện IP thực qua proxy/CDN:

```
HTTP_CF_CONNECTING_IP  → Cloudflare
HTTP_X_REAL_IP         → Nginx proxy
HTTP_X_FORWARDED_FOR   → Load balancer
REMOTE_ADDR            → Fallback
```

**Response khi bị chặn:**
```json
{
  "success": false,
  "code": "IP_BLOCKED",
  "message": "IP [x.x.x.x] bị chặn. Thử lại sau HH:MM DD/MM/YYYY."
}
```
HTTP Status: `403 Forbidden`

---

### Lớp 2 – Rate Limiting (Sliding Window)

Theo dõi số request theo cặp `(IP, endpoint)` trong cửa sổ thời gian.

**Cơ chế:**
- Lần đầu: tạo record trong `rate_limits` với `requests = 1`
- Mỗi request tiếp theo: tăng `requests` lên 1
- Khi cửa sổ thời gian hết: reset về 1
- Khi vượt giới hạn: ghi vi phạm + trả 429

**Response headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
Retry-After: 38   (chỉ khi bị chặn)
```

**Response khi vượt giới hạn:**
```json
{
  "success": false,
  "code": "RATE_LIMIT_EXCEEDED",
  "message": "Quá nhiều yêu cầu từ IP [x.x.x.x]. Vui lòng thử lại sau 38 giây.",
  "retry_after": 38
}
```
HTTP Status: `429 Too Many Requests`

---

### Lớp 3 – HTTP Header Validation

**3.1 User-Agent bắt buộc** (`DDOS_UA_REQUIRED = true`):
- Request không có User-Agent → ghi vi phạm → 400

**3.2 Host Header** (chống Host Header Attack):
- Chỉ chấp nhận: `localhost`, `127.0.0.1`, giá trị `APP_DOMAIN` trong `.env`

---

### Lớp 4 – Payload Inspection

**4.1 Giới hạn kích thước body:**
- Tối đa **512 KB** (`DDOS_MAX_BODY_SIZE = 1024 * 512`)
- Vượt quá → ghi vi phạm → 413

**4.2 Phát hiện injection trong query string:**

| Pattern | Loại tấn công |
|---|---|
| `UNION SELECT`, `DROP TABLE`, `INSERT INTO` | SQL Injection |
| `OR 1=1`, `--`, `/* */` | SQL Injection |
| `<script>`, `javascript:` | XSS |
| `onload=`, `onclick=` | XSS Event Handler |
| `../`, `..\` | Path Traversal |
| `\|`, `;`, `&&`, `$(`, `` ` `` | Command Injection |

---

### Lớp 5 – Ghi Vi Phạm & Tự Động Ban

```
Vi phạm lần 1-4: Block tạm 10 phút (expires_at = now + 600s)
Vi phạm lần 5+:  Block vĩnh viễn (expires_at = null)
```

Mỗi lần vi phạm cập nhật record trong `ip_blacklist`:
```json
{
  "ip": "x.x.x.x",
  "reason": "Rate limit exceeded on cart",
  "violations": 3,
  "blocked_at": "...",
  "expires_at": "..."
}
```

---

### Lớp 6 – Garbage Collection

Chạy ngẫu nhiên với xác suất **1%** mỗi request để giảm overhead:

```php
if (random_int(1, 100) !== 1) return; // 99% bỏ qua
```

Dọn dẹp:
- `rate_limits`: xóa records cũ hơn 10 phút
- `ip_blacklist`: xóa ban đã hết hạn (giữ lại ban vĩnh viễn)
- `security_logs`: xóa logs cũ hơn 7 ngày

---

## 4. Security Headers Tự Động

`applyDDoSProtection()` tự động thêm vào mọi response:

```
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

---

## 5. JWT Authentication

### Cấu Trúc Token

```
Token = base64(payload) + "." + HMAC-SHA256(base64(payload), SECRET_KEY)

Payload = {
  "user_id": "mongodb_object_id_string",
  "exp":     unix_timestamp + 86400
}
```

### Quy Trình Xác Thực

```
1. Đọc header: Authorization: Bearer <token>
2. Tách token thành [encoded, signature]
3. Tính lại HMAC-SHA256(encoded, SECRET_KEY)
4. So sánh bằng hash_equals() → chống timing attack
5. Decode payload, kiểm tra exp chưa hết hạn
6. Trả về user_id
```

### Bảo Mật Token

- Thời hạn: **24 giờ**
- Lưu phía client: `localStorage['kx_auth_token']`
- Không lưu trong cookie (tránh CSRF)
- Dùng `hash_equals()` thay vì `===` để chống timing attack

---

## 6. Bảo Mật Mật Khẩu

```php
// Hash khi đăng ký / đổi mật khẩu
$hash = password_hash($password, PASSWORD_DEFAULT); // bcrypt

// Xác thực khi đăng nhập
$ok = password_verify($plaintext, $hash);
```

- Thuật toán: **bcrypt** (PHP `PASSWORD_DEFAULT`)
- Không bao giờ lưu hoặc so sánh plaintext
- Đổi mật khẩu: yêu cầu nhập mật khẩu hiện tại để xác nhận

---

## 7. CORS

Tất cả response đều có:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
```

> **Lưu ý production:** Thay `*` bằng domain cụ thể để tăng bảo mật.

---

## 8. Phân Quyền

### Phân Loại User

| `role_id` | Loại | Quyền |
|---|---|---|
| `null` | User thường | Xem sản phẩm, giỏ hàng, đơn hàng của mình |
| `1` | Admin | Tất cả + xem thống kê tổng quan |

### Kiểm Tra Quyền Admin

```php
$user = $users->findOne(['_id' => $userOid]);
if (!$user || ($user['role_id'] ?? null) != 1) {
    jsonResponse(['message' => 'Bạn không có quyền truy cập.'], 403);
}
```

Frontend `admin-api.js` tự redirect về trang chủ khi nhận 403.

---

## 9. Soft Delete

User bị xóa không bị xóa khỏi DB mà được đánh dấu `deleted_at`:

```php
// Khi đăng nhập, bỏ qua user đã bị soft-delete
$user = $users->findOne([
    'email'      => $email,
    'deleted_at' => ['$exists' => false],
]);
```

---

## 10. Cấu Hình Bảo Mật

```php
// security.php
define('DDOS_BLOCK_DURATION', 600);    // Block tạm: 10 phút
define('DDOS_BAN_THRESHOLD',  5);      // Vi phạm → ban vĩnh viễn
define('DDOS_MAX_BODY_SIZE',  524288); // Max body: 512 KB
define('DDOS_UA_REQUIRED',    true);   // Bắt buộc User-Agent
```

```env
# .env
SECRET_KEY=KinhXanhSecretKey2026  # PHẢI đổi khi deploy production
```

---

## 11. Checklist Trước Khi Deploy Production

- [ ] Đổi `SECRET_KEY` trong `.env` thành chuỗi ngẫu nhiên mạnh (≥ 32 ký tự)
- [ ] Xóa file `backend/api/debug.php`
- [ ] Thay `Access-Control-Allow-Origin: *` bằng domain thực
- [ ] Bật HTTPS (SSL/TLS)
- [ ] Đổi `MONGO_URI` sang MongoDB Atlas hoặc server riêng
- [ ] Bật logging thực sự (bỏ comment trong `logSecurityEvent`)
- [ ] Cân nhắc dùng Redis thay MongoDB cho rate limiting (hiệu năng cao hơn)
- [ ] Đặt `DDOS_UA_REQUIRED = true` và bật lại danh sách bot độc hại
- [ ] Thêm `applyDDoSProtection()` vào `product-detail.php`, `product-variants.php`, `reviews/index.php`

---

*© 2026 Kính Xanh Optical – Tài liệu Bảo Mật*
