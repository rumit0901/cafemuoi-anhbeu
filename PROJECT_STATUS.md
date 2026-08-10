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
| **Giai đoạn hiện tại** | 🟢 **Phase 2 — Phát triển Frontend (Cập nhật định hướng sỉ/lẻ)** |

---

## 🗺️ Lộ trình dự án (Roadmap)

```
Phase 1: Thiết kế & Lên cấu trúc                   ✅ HOÀN THÀNH
Phase 2: Phát triển Frontend (Static HTML/CSS/JS)   ✅ HOÀN THÀNH (Đã bổ sung cấu trúc sỉ/lẻ)
Phase 3: Phát triển Backend (PHP Slim API + CMS)   ← ĐANG Ở ĐÂY
Phase 4: Tích hợp Frontend ↔ Backend (Menu Sỉ/Lẻ & Form Đăng ký Đại lý)
Phase 5: Tối ưu SEO & Performance
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


---

## 📋 Việc cần làm tiếp theo (Next Steps)

### Phase 3 — Backend (PHP Slim API + CMS)
- [ ] Khởi tạo project PHP Slim Framework (Composer)
- [ ] Cấu hình CORS cho phép origin GitHub Pages
- [ ] Thiết kế schema Database MySQL (tables: products, sales_channels, wholesale_packages, stores, reviews, blog_posts, contacts/inquiries)
- [ ] Implement GET `/api/menu` (danh sách bán lẻ)
- [ ] Implement GET `/api/channels` (danh sách kênh bán hàng ShopeeFood, GrabFood...)
- [ ] Implement GET `/api/wholesale` (danh sách gói sỉ & chính sách đại lý)
- [ ] Implement GET `/api/stores`
- [ ] Implement GET `/api/reviews`
- [ ] Implement GET `/api/blog`
- [ ] Implement POST `/api/contact` (tiếp nhận yêu cầu liên hệ & báo giá sỉ)
- [ ] Implement CMS Admin Panel (`/admin`) với xác thực (quản lý sản phẩm sỉ/lẻ, CRUD kênh bán hàng bán lẻ & quản lý yêu cầu đại lý)

---

## 📁 Cấu trúc file hiện tại (Actual File Tree)

```
/
├── .agents/
│   ├── rules/
│   │   └── update-status.md  ✅ Quy tắc bắt buộc cập nhật PROJECT_STATUS.md
│   └── skills/
│       └── deploy-frontend-github-pages/
│           └── SKILL.md      ✅ Skill hướng dẫn từng bước deploy frontend lên GitHub Pages
├── .github/
│   └── workflows/
│       └── deploy-frontend.yml ✅ GitHub Actions Workflow tự động deploy thư mục frontend/
├── ARCHITECTURE.md           ✅ Tài liệu kiến trúc (Đã cập nhật chuẩn Sỉ/Lẻ & CMS Kênh bán hàng)
├── PROJECT_STATUS.md         ✅ File theo dõi tiến độ (Đã cập nhật chuẩn Sỉ/Lẻ & CMS Kênh bán hàng)
├── frontend/                 ✅ Mã nguồn Frontend
│   ├── index.html            ✅ Trang chủ (SEO JSON-LD, Section Bán Sỉ Chai 330ml & Kênh Bán Hàng CMS)
│   ├── menu.html             ✅ Thực đơn Sỉ & Lẻ (jsRender menu & wholesale templates)
│   ├── stores.html           ✅ Tìm cửa hàng (Mapbox GL JS)
│   ├── story.html            ✅ Câu chuyện thương hiệu
│   ├── reviews.html          ✅ Đánh giá khách hàng & Đại lý
│   ├── blog.html             ✅ Tin tức, khuyến mãi & chính sách sỉ (jsRender blogTemplate)
│   ├── contact.html          ✅ Form Đăng ký Báo giá Sỉ & Liên hệ (Select type & wholesale fields)
│   ├── CNAME                 ✅ Custom domain config
│   └── assets/
│       ├── css/
│       │   └── style.css     ✅ Design System & Styles
│       └── js/
│           ├── config.js     ✅ Global CONFIG
│           ├── api.js        ✅ API Fetch Wrappers (getMenu, getChannels, getWholesale, sendContact...)
│           ├── animations.js ✅ Engine chuyển động cuộn trang (IntersectionObserver), Header scrolled & 3D Bento tilt
│           ├── templates/
│           │   ├── menu.tmpl.html
│           │   ├── review.tmpl.html
│           │   └── store.tmpl.html
│           └── pages/
│               ├── home.page.js      ✅ Logic trang chủ (channels & wholesale highlight)
│               ├── menu.page.js      ✅ Logic thực đơn Sỉ & Lẻ
│               ├── contact.page.js   ✅ Logic form liên hệ & báo giá sỉ
│               ├── blog.page.js      ✅ Logic tin tức & chính sách đại lý
│               ├── reviews.page.js   ✅ Logic đánh giá khách hàng
│               └── stores.page.js    ✅ Logic bản đồ chi nhánh
└── backend/                  ✅ Thư mục dành riêng cho Backend (PHP Slim)
```

---

## 📝 Nhật ký thay đổi (Changelog)

| Ngày | Thay đổi | Thực hiện bởi |
|---|---|---|
| 2026-08-10 | Tạo `ARCHITECTURE.md` & `PROJECT_STATUS.md` | Antigravity |
| 2026-08-10 | Triển khai hoàn tất Phase 2 Frontend (HTML5/CSS3/jQuery/jsRender/Mapbox GL JS) | Antigravity |
| 2026-08-10 | Cập nhật `ARCHITECTURE.md` & `PROJECT_STATUS.md`: Chuẩn hóa mục đích website quảng bá dịch vụ cung cấp sỉ & lẻ cà phê muối Anh Bếu | Antigravity |
| 2026-08-10 | Bổ sung cơ chế quản lý kênh bán hàng bán lẻ (ShopeeFood, GrabFood...) từ CMS vào `ARCHITECTURE.md` & `PROJECT_STATUS.md` | Antigravity |
| 2026-08-10 | Điều chỉnh phạm vi bán sỉ: Trước mắt chỉ tập trung cung cấp sỉ cà phê muối đóng chai với giá sỉ | Antigravity |
| 2026-08-10 | Cập nhật toàn bộ Frontend theo tài liệu dự án (Tích hợp API channels/wholesale, Section Bán Sỉ Chai 330ml, Form đăng ký báo giá sỉ) | Antigravity |
| 2026-08-10 | Cập nhật giao diện Minimalism: Bỏ section Must Try trên Trang chủ, tinh giản Thanh Menu Header còn 3 mục chính (Trang Chủ, Câu Chuyện, Liên Hệ) | Antigravity |
| 2026-08-10 | Động hóa nội dung trang Câu Chuyện (`story.html`) từ CMS API `/api/story` qua `story.page.js` & template jsRender `#storyTemplate` | Antigravity |
| 2026-08-10 | Xây dựng hệ thống Animations & Micro-Interactions (`assets/js/animations.js`, CSS scroll reveal, 3D Bento tilt, Count-up stats) nâng tầm sinh động cho website | Antigravity |
| 2026-08-10 | Bổ sung Favicon SVG vector & PNG Apple Touch Icon (`assets/img/favicon.svg`) đồng bộ trên toàn bộ 7 trang HTML | Antigravity |
| 2026-08-10 | Tạo `.github/workflows/deploy-frontend.yml` (GitHub Actions CI/CD) & Lưu Skill `deploy-frontend-github-pages` trong `.agents/skills` | Antigravity |





