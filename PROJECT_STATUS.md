# Trạng thái Phát triển Dự án — Website "Cà phê muối anh Bếu"

> **Dành cho AI Agent**: Đây là file trạng thái phát triển của dự án. Hãy đọc file này TRƯỚC KHI bắt đầu bất kỳ tác vụ nào. Sau khi hoàn thành bất kỳ thay đổi nào, bắt buộc cập nhật file này theo đúng cấu trúc bên dưới.

---

## 📌 Tổng quan nhanh (Quick Context)

| Thông tin | Giá trị |
|---|---|
| **Tên dự án** | Website quảng bá dịch vụ cung cấp sỉ & lẻ "Cà phê muối anh Bếu" |
| **Mục tiêu chính** | Bán lẻ (điều hướng qua ShopeeFood, GrabFood...) & Cung cấp sỉ số lượng lớn cà phê muối đóng chai |
| **Loại dự án** | Static Frontend (GitHub Pages) + REST API & CMS Backend (PHP Slim) |
| **Frontend URL** | _(chờ push repo GitHub Pages)_ |
| **Backend URL** | `https://cafemuoi-anhbeu.great-site.net/` |
| **Tài liệu kiến trúc** | `ARCHITECTURE.md` |
| **Cập nhật lần cuối** | 2026-08-10 |
| **Cập nhật bởi** | Antigravity AI Agent |
| **Giai đoạn hiện tại** | 🟢 **Phase 5 — Tối ưu SEO & Performance** |

---

## 🗺️ Lộ trình dự án (Roadmap)

```
Phase 1: Thiết kế & Lên cấu trúc                   ✅ HOÀN THÀNH
Phase 2: Phát triển Frontend (Static HTML/CSS/JS)   ✅ HOÀN THÀNH (Đã bổ sung cấu trúc sỉ/lẻ)
Phase 3: Phát triển Backend (PHP Slim API + CMS)   ✅ HOÀN THÀNH (API REST + CMS Admin + TDD PHPUnit Pass 100%)
Phase 4: Tích hợp Frontend ↔ Backend               ✅ HOÀN THÀNH (Đồng bộ hợp đồng API, auto dev URL & TDD Pass 100%)
Phase 5: Tối ưu SEO & Performance                  ← ĐANG Ở ĐÂY
Phase 6: Kiểm thử (TDD / PHPUnit / Jest)
Phase 7: Deploy & Go Live
```

---

## ✅ Đã hoàn thành (Completed)

### Phase 1 — Thiết kế & Lên cấu trúc
- [x] Xác định kiến trúc hệ thống tách rời (Decoupled: Static Frontend + PHP Slim Backend)
- [x] Xây dựng tài liệu kiến trúc `ARCHITECTURE.md` (bao gồm Coding Standards & TDD Workflow)
- [x] Tạo file theo dõi trạng thái dự án `PROJECT_STATUS.md`
- [x] Định hình mục đích website: Quảng bá song song dịch vụ cung cấp Sỉ & Lẻ cà phê muối Anh Bếu

### Phase 2 — Phát triển Frontend
- [x] Tạo `index.html` (Trang chủ) với nội dung tĩnh + SEO meta tags + Schema JSON-LD
- [x] Tạo `assets/css/style.css` — design system, color palette, typography, responsive grid
- [x] Tạo `assets/js/config.js` — cấu hình `API_BASE_URL` và `MAPBOX_TOKEN`
- [x] Tạo `assets/js/api.js` — wrapper fetch functions với error handling
- [x] Tạo `menu.html` + `assets/js/templates/menu.tmpl.html` + `assets/js/pages/menu.page.js`
- [x] Tạo `stores.html` + tích hợp Mapbox GL JS + `assets/js/templates/store.tmpl.html` + `assets/js/pages/stores.page.js`
- [x] Tạo `reviews.html` + `assets/js/templates/review.tmpl.html` + `assets/js/pages/reviews.page.js`
- [x] Tạo `story.html`, `blog.html`, `contact.html`
- [x] Tạo `CNAME` file cho custom domain

### Phase 3 — Phát triển Backend (PHP Slim API + CMS)
- [x] Khởi tạo PHP Slim Framework 4 & Composer dependencies (`slim/slim`, `slim/psr7`, `php-di/php-di`) trong thư mục [`backend/`](file:///c:/Users/buian/Documents/App/cafemuoi.anhbeu/backend)
- [x] Xây dựng schema DDL & seed data [`schema.sql`](file:///c:/Users/buian/Documents/App/cafemuoi.anhbeu/backend/schema.sql) hỗ trợ đồng thời MySQL (production) và SQLite (kiểm thử/dev)
- [x] Cấu hình CorsMiddleware (HTTPS CORS), AuthMiddleware (Guard Admin), JsonBodyParserMiddleware
- [x] Xây dựng các Public REST API endpoints:
  - `GET /api/menu`: Danh sách sản phẩm bán lẻ
  - `GET /api/channels`: Danh sách kênh bán hàng đối tác (ShopeeFood, GrabFood...)
  - `GET /api/wholesale`: Danh sách sản phẩm & mốc giá sỉ chai đa dung tích (330ml, 500ml và dung tích tùy chỉnh)
  - `GET /api/stores`: Danh sách cửa hàng & tọa độ bản đồ
  - `GET /api/reviews`: Đánh giá khách hàng & đại lý
  - `GET /api/blog`: Bài viết tin tức & chính sách sỉ
  - `GET /api/story`: Nội dung câu chuyện thương hiệu
  - `POST /api/contact`: Tiếp nhận form liên hệ & báo giá sỉ (có validation dữ liệu)
- [x] Xây dựng CMS Admin Panel (`/admin` & `/admin/login`) với tài khoản mặc định `admin`/`admin`
- [x] Triển khai bộ API CRUD tại `/admin/api/*` cho quản trị viên quản lý kênh bán hàng, thực đơn, gói sỉ và đơn đăng ký
- [x] Viết bộ kiểm thử TDD tự động [`tests/ApiTest.php`](file:///c:/Users/buian/Documents/App/cafemuoi.anhbeu/backend/tests/ApiTest.php) với PHPUnit 9.6 — **Đạt PASS 100% (3 test cases, 29 assertions)**

### Phase 4 — Tích hợp Frontend ↔ Backend
- [x] Chuẩn hóa hợp đồng dữ liệu REST API (Response field mapping cho `stores`, `reviews`, `contact`)
- [x] Cấu hình tự động switch `API_BASE_URL` trong `config.js` theo môi trường localhost (`http://127.0.0.1:8000`) và production
- [x] Xử lý linh hoạt form gửi yêu cầu báo giá sỉ & cửa hàng trong `ContactController` và `contact.page.js`
- [x] Mở rộng bộ kiểm thử TDD tự động [`tests/ApiTest.php`](file:///c:/Users/buian/Documents/App/cafemuoi.anhbeu/backend/tests/ApiTest.php) — **Đạt PASS 100% (5 test cases, 47 assertions)**

---

## 📋 Việc cần làm tiếp theo (Next Steps)

### Phase 5 — Tối ưu SEO & Performance
- [ ] Tối ưu hóa SEO On-page, OpenGraph, Schema JSON-LD và Sitemap XML
- [ ] Chạy kiểm thử E2E tích hợp nâng cao
- [ ] Deploy backend PHP Slim lên server `cafemuoi-anhbeu.great-site.net` & import MySQL schema

---

## 📝 Nhật ký thay đổi (Changelog)

| Ngày | Thay đổi | Thực hiện bởi |
|---|---|---|
| 2026-08-10 | Tạo `ARCHITECTURE.md` & `PROJECT_STATUS.md` | Antigravity |
| 2026-08-10 | Triển khai hoàn tất Phase 2 Frontend (HTML5/CSS3/jQuery/jsRender/Mapbox GL JS) | Antigravity |
| 2026-08-10 | Hoàn tất Phase 3 Backend: PHP Slim Framework 4 REST API, CMS Admin Panel, Auth guard & bộ kiểm thử TDD PHPUnit đạt 100% Pass | Antigravity |
| 2026-08-11 | Hoàn tất Phase 4 Tích hợp Frontend ↔ Backend API, chuẩn hóa Data Contract & bổ sung Test TDD đạt 100% Pass (5 tests, 47 assertions) | Antigravity |
