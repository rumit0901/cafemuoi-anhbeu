# Kiến Trúc Hệ Thống — Website Landing Page "Cà Phê Muối Anh Bếu"

> Tài liệu mô tả kiến trúc kỹ thuật mới của dự án. Hệ thống được tối giản thành **100% Frontend Tĩnh (Static Landing Page)** hosted trên **GitHub Pages**.

---

## 1. Tổng Quan

Website Landing Page chính thức của thương hiệu **Cà Phê Muối Anh Bếu** tại TP. Hồ Chí Minh.

### Mục Đích Dự Án
Giới thiệu & thúc đẩy chuyển đổi cho **2 dịch vụ cốt lõi**:
1. **Dịch Vụ 1: Đặt Hàng Online Bán Lẻ (Giao Tận Nơi 15 Phút)**
   - Khách mua lẻ được điều hướng đặt món trực tiếp qua 2 ứng dụng đối tác chính: **Be (BeFood)** & **ShopeeFood**.
   - Cung cấp trải nghiệm giao hàng tận nơi nhanh chóng với đầy đủ voucher & freeship.
2. **Dịch Vụ 2: Đặt Sỉ Cà Phê Muối Đóng Chai 330ml & 500ml**
   - **Chai 330ml**: Pha sẵn nguyên chai tiện lợi uống ngay.
   - **Chai 500ml**: Cốt cà phê Robusta đậm đặc + Hũ kem muối béo ngậy riêng biệt (phục vụ 4-5 ly).
   - Đặt sỉ trực tiếp bằng cách **Gọi điện thoại Hotline (`0905 382 080`)** hoặc nhắn **Zalo**.

---

## 2. Kiến Trúc Kỹ Thuật (Architecture Overview)

Dự án áp dụng kiến trúc **Static Single Landing Page** tối ưu hiệu năng và chi phí vận hành:

- **Hosting**: GitHub Pages (`https://cafemuoi-anhbeu.github.io/` & Custom Domain `cafemuoi-anhbeu.io.vn`).
- **Deployment**: Tự động hóa qua GitHub Actions Workflow (`.github/workflows/deploy-frontend.yml`).
- **Backend Dependency**: **Không sử dụng Backend server** (No Backend API). Toàn bộ dữ liệu & hiệu ứng được xử lý 100% Client-side.

```
[Browser Client]
       │
       ▼ (HTTPS Static Serving)
[GitHub Pages CDN (cafemuoi-anhbeu.io.vn)]
       │
       ├── index.html       (SEO Schema + 2 Dịch Vụ Core + Call CTAs)
       ├── assets/css/      (Glassmorphism Design System + Animations)
       └── assets/js/       (Native Observer + Tilt 3D + Counter Animation)
```

---

## 3. Cấu Trúc Repository & Thư Mục

```
/
├── .github/workflows/
│   └── deploy-frontend.yml   # GitHub Actions Workflow deploy sang GitHub Pages
├── CNAME                     # Custom domain configuration (cafemuoi-anhbeu.io.vn)
├── ARCHITECTURE.md           # Tài liệu kiến trúc hệ thống
├── PROJECT_STATUS.md         # Trạng thái dự án
├── index.html                # Single Landing Page chính
├── assets/
│   ├── css/
│   │   └── style.css         # Styling system, responsive layout & micro-interactions
│   ├── img/
│   │   ├── logo.png          # Logo Cà phê muối Anh Bếu
│   │   └── favicon.svg       # Biểu tượng Favicon
│   └── js/
│       ├── animations.js     # Animation engine (Reveal observer, bento tilt, count-up)
│       └── main.js           # Smooth scroll, mobile navbar & event handlers
└── frontend/                 # Thư mục mirror dành cho GitHub Actions deployment
    ├── CNAME
    ├── index.html
    └── assets/
```

---

## 4. Công Nghệ & Tối Ưu

- **Frontend Tech Stack**: HTML5 Semantic, CSS3 Vanilla (Custom Properties, Flexbox, CSS Grid), jQuery, Native JavaScript (IntersectionObserver).
- **Thiết Kế Aesthetic**: Tone màu Espresso & Hổ Phách cao cấp (`#1A0F0A`, `#D35400`, `#FAF3E0`), hiệu ứng Glassmorphism, card nghiêng 3D, nút bấm phát sáng & floating badge.
- **Tối Ưu SEO**:
  - Thẻ Meta chuẩn SEO: Title, Description, Open Graph social share icons.
  - JSON-LD Structured Data `LocalBusiness` / `CafeOrCoffeeShop` cho Google Search Index.
- **Hỗ Trợ Thiết Bị Di Động**: Thanh **Mobile Sticky Call Bar** cố định góc dưới giúp tăng tối đa tỷ lệ chuyển đổi gọi đặt sỉ.

---

## 5. Quy Trình Triển Khai (Deployment)

1. Mỗi lần push code lên nhánh `main`, GitHub Actions tự động kích hoạt workflow `.github/workflows/deploy-frontend.yml`.
2. Workflow đóng gói thư mục `frontend/` thành artifact và phát hành trực tiếp lên GitHub Pages với tên miền `cafemuoi-anhbeu.io.vn`.
