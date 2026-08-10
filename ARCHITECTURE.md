# Kiến trúc hệ thống — Website "Cà phê muối anh Bếu"

> Tài liệu này mô tả kiến trúc kỹ thuật của dự án để AI agent (Claude Code, Copilot, Antigravity, v.v.) có thể đọc, hiểu và hỗ trợ phát triển/bảo trì đúng quy ước.

## 1. Tổng quan

Website quảng bá thương hiệu & hệ thống dịch vụ **cung cấp sỉ & lẻ cà phê muối Anh Bếu**.

### Mục đích dự án
- **Bán lẻ (Retail)**: Giới thiệu thương hiệu, menu thức uống (cà phê muối truyền thống, kem muối, v.v.), địa chỉ cửa hàng/chi nhánh. Khách mua lẻ được điều hướng đặt hàng qua các **kênh bán hàng đối tác** (ShopeeFood, GrabFood, GoFood, BeFood, TikTok Shop...). Hệ thống **CMS Admin cung cấp cơ chế cho phép quản trị viên tự thêm, chỉnh sửa, bật/tắt hoặc sắp xếp các kênh bán hàng** này linh hoạt mà không cần sửa code.
- **Cung cấp Sỉ (Wholesale)**: Cung cấp sỉ số lượng lớn cà phê muối đóng chai.

### Kiến trúc kỹ thuật
Dự án áp dụng kiến trúc tách rời (decoupled):

- **Frontend**: Static site dùng jsRender, host trên **GitHub Pages**.
- **Backend**: REST API + CMS Admin Panel, xây dựng bằng **PHP Slim Framework**, host tại `https://cafemuoi-anhbeu.great-site.net/`.
- Frontend gọi backend qua HTTPS (fetch/AJAX), backend trả JSON, jsRender render ra HTML ở client.

```
[Browser] <---HTML/CSS/JS (static)--- [GitHub Pages: frontend repo]
    |
    | fetch() JSON qua HTTPS (CORS)
    v
[PHP Slim Backend: cafemuoi-anhbeu.great-site.net]
    |
    | REST API /api/*  ←→  CMS Admin /admin
    v
[Database (MySQL)]
```

## 2. Repository & thư mục

Dự án được phân chia thành 2 thư mục chính ở thư mục gốc: `frontend/` và `backend/`.

```
/
├── ARCHITECTURE.md           # Tài liệu kiến trúc hệ thống
├── PROJECT_STATUS.md         # Trạng thái phát triển dự án
├── frontend/                 # Frontend Static Site (GitHub Pages)
│   ├── index.html            # Trang chủ
│   ├── story.html            # Câu chuyện thương hiệu
│   ├── menu.html             # Trang menu (render động qua jsRender)
│   ├── stores.html           # Tìm cửa hàng (Mapbox GL JS)
│   ├── reviews.html          # Đánh giá khách hàng
│   ├── blog.html             # Blog / tin tức
│   ├── contact.html          # Liên hệ
│   ├── CNAME                 # Custom domain configuration
│   └── assets/
│       ├── css/
│       │   └── style.css
│       ├── img/
│       └── js/
│           ├── config.js     # Cấu hình tập trung: API_BASE_URL, MAPBOX_TOKEN
│           ├── api.js        # Hàm gọi fetch tới backend (wrapper)
│           ├── templates/    # File .html chứa <script type="text/x-jsrender">
│           │   ├── menu.tmpl.html
│           │   ├── review.tmpl.html
│           │   └── store.tmpl.html
│           └── pages/        # Logic riêng từng trang (load data + render template)
│               ├── menu.page.js
│               ├── reviews.page.js
│               └── stores.page.js
└── backend/                  # PHP Slim REST API & CMS Admin Panel
```

Backend nằm trong thư mục `backend/` đảm nhận REST API & CMS Admin Panel.

## 3. Frontend — Chi tiết kỹ thuật

- **Công nghệ**: HTML5 + CSS3 (responsive, mobile-first) + jQuery + jsRender + **Mapbox GL JS**.
- **Không dùng build tool** (không Webpack/Vite) — vì GitHub Pages chỉ serve static file thuần. Nếu sau này cần build step, dùng GitHub Actions để build rồi deploy vào nhánh `gh-pages`.
- **Data binding**: mọi phần nội dung động (menu, review, store list) lấy dữ liệu qua `fetch(CONFIG.API_BASE_URL + endpoint)`, sau đó render bằng jsRender template (`$.templates(...).render(data)`), rồi `.html()` vào DOM.
- **Cấu hình tập trung** trong `assets/js/config.js`:

```js
// Cấu hình toàn cục — đây là nguồn duy nhất cho các hằng số môi trường
const CONFIG = {
  API_BASE_URL: "https://cafemuoi-anhbeu.great-site.net", // Domain Backend PHP Slim
  MAPBOX_TOKEN: "pk.eyJ1I..."                             // Public token Mapbox GL JS
};
```

- **Quy ước đặt tên template jsRender**: `<ten-doi-tuong>Template`, ví dụ `menuItemTemplate`, `reviewTemplate`.
- **Bản đồ (Stores)**: Sử dụng **Mapbox GL JS** hiển thị danh sách chi nhánh kèm marker tương tác. Token lấy từ `CONFIG.MAPBOX_TOKEN`.
- **Tối ưu SEO**:
  - Đầy đủ thẻ `<title>`, `<meta name="description">`, Open Graph (`og:image`, `og:title`, `og:description`) tĩnh trên tất cả các trang HTML.
  - Cung cấp sẵn nội dung tĩnh tối thiểu trong HTML (fallback) để bot tìm kiếm (Googlebot) cào dữ liệu trước khi JS render.
  - Tích hợp Schema.org Structured Data (JSON-LD) cho thương hiệu, quán cà phê (`LocalBusiness` / `CafeOrCoffeeShop`), sản phẩm menu (`MenuItem`).

## 4. Backend — Hợp đồng API & CMS Admin Panel

Backend được phát triển bằng **PHP Slim Framework**, host tại `https://cafemuoi-anhbeu.great-site.net/` (shared hosting, hỗ trợ PHP + MySQL).

Backend đảm nhận **2 vai trò chính**:
1. **REST API Provider**: Cung cấp các endpoint JSON công khai cho Frontend tiêu thụ (GET data cho Menu sỉ/lẻ, Kênh bán hàng, Chi nhánh, Đánh giá, Blog; POST cho Contact & Báo giá sỉ).
2. **CMS Admin Panel**: Giao diện quản trị viên bảo mật (route `/admin`, xác thực session/JWT) để CRUD dữ liệu (Sản phẩm bán lẻ, Kênh bán hàng đối tác ShopeeFood/GrabFood..., Sản phẩm/Gói sỉ, Bài viết, Chi nhánh, Đánh giá, Quản lý yêu cầu báo giá sỉ) trực tiếp vào Database MySQL.

### Yêu cầu bắt buộc phía backend
- Bật **CORS** cho phép origin từ GitHub Pages (hoặc custom domain của frontend).
- Bắt buộc **HTTPS** (tránh mixed content vì GitHub Pages luôn serve qua HTTPS).
- REST API trả dữ liệu dạng JSON thuần (`Content-Type: application/json`), không kèm markup.
- Route `/admin` **bắt buộc** có xác thực (không public).

### Các endpoint dự kiến

| Method | Endpoint | Mô tả | Response mẫu |
|---|---|---|---|
| GET | `/api/menu` | Danh sản phẩm bán lẻ | `[{ "id": 1, "name": "Cà phê muối truyền thống", "price": 35000, "image": "url", "tag": "best-seller" }]` |
| GET | `/api/channels` | Danh sách kênh bán hàng (ShopeeFood, GrabFood...) | `[{ "id": 1, "name": "ShopeeFood", "logo": "url", "url": "https://shopeefood.vn/...", "badge": "Đặt Ngay", "active": 1 }]` |
| GET | `/api/wholesale` | Danh sách sản phẩm cà phê muối đóng chai giá sỉ | `[{ "id": 1, "name": "Cà phê muối đóng chai (Sỉ)", "min_quantity": 5, "price_tier": "Báo giá sỉ", "bottle_size": "330ml" }]` |
| GET | `/api/story` | Nội dung câu chuyện thương hiệu CMS | `{ "title": "...", "subtitle": "...", "image": "...", "paragraphs": ["..."] }` |
| GET | `/api/stores` | Danh sách chi nhánh | `[{ "id": 1, "name": "Chi nhánh Q1", "address": "...", "lat": 10.7769, "lng": 106.7009 }]` |
| GET | `/api/reviews` | Đánh giá khách hàng & đại lý | `[{ "id": 1, "customer": "...", "rating": 5, "comment": "...", "type": "wholesale_partner" }]` |
| GET | `/api/blog` | Bài viết / tin tức / chính sách sỉ | `[{ "id": 1, "title": "...", "excerpt": "...", "slug": "..." }]` |
| POST | `/api/contact` | Gửi form liên hệ / yêu cầu báo giá sỉ | Body: `{ "name", "phone", "email", "inquiry_type": "retail"\|"wholesale", "message" }` → `{ "success": true }` |

> Frontend chỉ phụ thuộc vào **hợp đồng JSON** ở trên, không phụ thuộc vào công nghệ nội bộ của backend.

## 5. Luồng render dữ liệu (ví dụ: trang Menu)

1. `menu.page.js` hiển thị skeleton/loading state trong `#menu-container`.
2. Gọi `fetch(CONFIG.API_BASE_URL + "/api/menu")`.
3. Nhận JSON array sản phẩm.
4. Lấy template từ `menu.tmpl.html` (đã load sẵn trong `menu.html` qua `<script type="text/x-jsrender">`).
5. `$.templates("#menuItemTemplate").render(data)` → chuỗi HTML.
6. Chèn HTML vào `#menu-container` trong DOM, xóa skeleton.
7. Nếu fetch lỗi → ẩn skeleton, hiển thị fallback nội dung tĩnh + thông báo lỗi thân thiện.

## 6. Môi trường & triển khai (deployment)

- **Frontend**: push lên GitHub → GitHub Pages tự serve static file (không cần CI build).
- **Backend**: PHP Slim Framework host tại `https://cafemuoi-anhbeu.great-site.net/` (shared hosting hỗ trợ PHP + MySQL), HTTPS và CORS được cấu hình phía server.
- **Biến môi trường phía frontend**: tập trung trong `config.js` — đổi `API_BASE_URL` và `MAPBOX_TOKEN` theo môi trường (dev/staging/production). Vì frontend là static, không có `.env` thật — switch thủ công hoặc theo branch.
- **Lưu ý**: Không commit `MAPBOX_TOKEN` production lên public repo — dùng Mapbox Token scoping để giới hạn domain được phép dùng token.

## 7. Quy ước cho AI agent khi chỉnh sửa code

- **Luôn comment cho mỗi khối code** — bắt buộc, theo quy tắc tại Mục 9.
- Khi thêm section động mới: tạo template jsRender trong `assets/js/templates/`, tạo file logic tương ứng trong `assets/js/pages/`, và định nghĩa endpoint mới trong bảng API contract ở Mục 4.
- Không viết logic nghiệp vụ (tính giá, xác thực, database) trong frontend — mọi logic thuộc về backend.
- Không hardcode URL backend trực tiếp trong từng file — luôn tham chiếu qua `CONFIG.API_BASE_URL`.
- Không hardcode Mapbox token ngoài `config.js` — luôn tham chiếu qua `CONFIG.MAPBOX_TOKEN`.
- Giữ nguyên cấu trúc thư mục `templates/` và `pages/` tách biệt để dễ bảo trì.
- Khi backend đổi contract (thêm/sửa field), cập nhật lại bảng ở Mục 4 và bộ test liên quan (Mục 10) đồng thời.

## 8. Các quyết định kỹ thuật đã thống nhất

- [x] **Mục đích & Kinh doanh**: Quảng bá song song Bán lẻ (điều hướng qua các kênh đối tác ShopeeFood, GrabFood...) và Bán sỉ Cà phê muối đóng chai giá sỉ thương hiệu Anh Bếu.
- [x] **Kênh bán lẻ động (CMS-driven)**: Phần bán lẻ kết nối qua các ứng dụng/kênh bán hàng (ShopeeFood, GrabFood...). Quản trị viên CMS có thể tự do CRUD (thêm/sửa/xóa/ẩn/hiện) danh sách kênh bán hàng và liên kết tương ứng mà không phải can thiệp code HTML/JS.
- [x] **Backend Framework**: PHP Slim Framework
- [x] **Domain Backend**: `https://cafemuoi-anhbeu.great-site.net/`
- [x] **Tích hợp CMS**: Giao diện Quản trị (Admin Dashboard) tích hợp cùng Backend PHP Slim để CRUD dữ liệu sản phẩm sỉ/lẻ, danh mục kênh bán hàng và tiếp nhận đăng ký đại lý.
- [x] **Giải pháp Bản đồ**: Mapbox GL JS (trang Tìm cửa hàng)
- [x] **Tối ưu SEO**: Meta tags, Open Graph, Schema JSON-LD, Static Fallback Content
- [x] **Quy chuẩn Code & TDD**: Bắt buộc comment khối code + quy trình TDD (xem Mục 9 & 10)

## 9. Quy tắc Lập trình (Coding Standards)

### 9.1 Bắt buộc comment cho mỗi khối code

Tất cả các khối code — bao gồm functions, classes, methods, vòng lặp phức tạp, nhánh điều kiện quan trọng, lệnh `fetch`/AJAX, DOM rendering — **bắt buộc** phải có comment giải thích mục đích ngay phía trên.

**Ví dụ JavaScript (JSDoc + inline comment):**
```js
/**
 * Lấy danh sách sản phẩm từ API và render vào #menu-container.
 * Hiển thị skeleton trong khi chờ, fallback tĩnh nếu fetch thất bại.
 */
async function loadMenu() {
  // Hiển thị skeleton loading trước khi fetch
  $('#menu-container').html('<div class="skeleton">...</div>');

  try {
    // Gọi API lấy danh sách sản phẩm
    const response = await fetch(CONFIG.API_BASE_URL + '/api/menu');
    const data = await response.json();

    // Render dữ liệu vào template jsRender
    const html = $.templates('#menuItemTemplate').render(data);
    $('#menu-container').html(html);
  } catch (error) {
    // Khi fetch thất bại: hiển thị fallback tĩnh và log lỗi
    console.error('[loadMenu] Fetch thất bại:', error);
    $('#menu-container').html('<p class="error-msg">Không thể tải menu. Vui lòng thử lại.</p>');
  }
}
```

**Ví dụ PHP (PHPDoc + inline comment):**
```php
/**
 * Lấy danh sách sản phẩm menu từ Database và trả về JSON.
 *
 * @param Request  $request  Slim request object
 * @param Response $response Slim response object
 * @return Response JSON array sản phẩm
 */
public function getMenu(Request $request, Response $response): Response {
  // Truy vấn toàn bộ sản phẩm đang active từ bảng products
  $items = $this->db->query("SELECT * FROM products WHERE active = 1")->fetchAll();

  // Trả về JSON với header Content-Type chuẩn
  $response->getBody()->write(json_encode($items));
  return $response->withHeader('Content-Type', 'application/json');
}
```

### 9.2 Quy tắc đặt tên (Naming Conventions)

- **JavaScript**: `camelCase` cho biến và hàm (`fetchMenuList`, `renderStoreList`); `PascalCase` cho Class và tên template jsRender (`menuItemTemplate`).
- **PHP (Slim)**: `camelCase` cho variable/method (`getMenuList`), `PascalCase` cho Class/Controller/Middleware (`MenuController`), `UPPER_SNAKE_CASE` cho hằng số (`DB_HOST`).
- **HTML/CSS**: `kebab-case` cho class và id (`#menu-container`, `.btn-primary`).

### 9.3 Xử lý lỗi (Error Handling)

- Tất cả các thao tác I/O, API fetch, xử lý DB phải bọc trong `try...catch`.
- Không nuốt lỗi (silent catch) — luôn `console.error()` / log phía server và hiển thị thông báo thân thiện cho người dùng.
- API backend trả về HTTP status code đúng ngữ nghĩa (`200 OK`, `400 Bad Request`, `404 Not Found`, `500 Internal Server Error`).

## 10. Quy trình Kiểm thử & TDD (Testing & TDD Workflow)

Hệ thống áp dụng phương pháp **TDD (Test-Driven Development)** để đảm bảo độ tin cậy của code.

### 10.1 Quy trình TDD 3 bước (Red → Green → Refactor)

1. **🔴 RED — Viết Test trước**: Viết test case cho tính năng/endpoint trước khi viết bất kỳ code xử lý nào. Chạy test và đảm bảo **Fails** (đúng kỳ vọng).
2. **🟢 GREEN — Viết Code tối thiểu**: Viết code logic đơn giản vừa đủ để test **Passes**.
3. **🔵 REFACTOR — Tối ưu & Comment**: Tái cấu trúc, làm sạch code, **bổ sung comment đầy đủ** cho các khối code, đảm bảo toàn bộ test vẫn **Passes**.

### 10.2 Định hướng Test theo từng tầng

- **Backend (PHP Slim — PHPUnit)**:
  - **Unit Test**: Kiểm thử logic nghiệp vụ, format dữ liệu trong các Service / Helper class.
  - **API Integration Test**: Kiểm thử endpoint REST API — HTTP status code (200, 400, 404, 500), định dạng JSON payload, dữ liệu trả về đúng API contract.

- **Frontend (JavaScript — Jest)**:
  - **Unit Test**: Kiểm thử các hàm tiện ích trong `api.js` (format tiền tệ, xử lý lỗi, parse dữ liệu).
  - **Mock API Test**: Giả lập (mock) dữ liệu JSON trả về để kiểm thử quá trình render jsRender template và chèn DOM đúng cấu trúc.

### 10.3 Nguyên tắc bảo trì Test

- Tuyệt đối không xóa hoặc comment-out test case đang fail để cố tình thông qua build.
- Khi API Contract thay đổi, bắt buộc cập nhật đồng thời: bảng API Contract (Mục 4) + bộ test tương ứng.
- Mỗi endpoint mới thêm vào bảng Mục 4 **phải** đi kèm ít nhất 1 integration test.
