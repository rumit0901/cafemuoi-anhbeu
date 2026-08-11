# Trạng Thái Phát Triển Dự Án — Website "Cà Phê Muối Anh Bếu"

> **Dành cho AI Agent**: Đây là file trạng thái phát triển của dự án. Đọc file này TRƯỚC KHI bắt đầu tác vụ và CẬP NHẬT file sau khi hoàn thành.

---

## 📌 Tổng Quan Nhanh (Quick Context)

| Thông tin | Giá trị |
|---|---|
| **Tên dự án** | Landing Page Giới Thiệu 2 Dịch Vụ "Cà Phê Muối Anh Bếu" |
| **Mục tiêu chính** | 1. Đặt online bán lẻ qua Be (BeFood) & ShopeeFood<br>2. Đặt sỉ cà phê muối đóng chai 330ml/500ml qua Hotline `0905 382 080` & Zalo |
| **Loại kiến trúc** | **100% Frontend Tĩnh (Static Landing Page on GitHub Pages)** — Deploy trực tiếp từ Root (`.`) |
| **Tên miền custom** | `cafemuoi-anhbeu.io.vn` |
| **Tài liệu kiến trúc** | `ARCHITECTURE.md` |
| **Cập nhật lần cuối** | 2026-08-11 |
| **Cập nhật bởi** | Antigravity AI Agent |
| **Trạng thái** | 🟢 **HOÀN THÀNH (Tối Giản Thư Mục Root — Ready for GitHub Pages)** |

---

## 🗺️ Lộ Trình Dự Án (Roadmap)

```
Phase 1: Chuyển đổi kiến trúc sang Pure FE (GitHub Pages)   ✅ HOÀN THÀNH
Phase 2: Loại bỏ Backend & Thư mục `frontend/` trùng lặp    ✅ HOÀN THÀNH
Phase 3: Thiết kế Landing Page 2 Dịch Vụ Core               ✅ HOÀN THÀNH
Phase 4: Tối ưu UI/UX, SEO, Mobile Bar & GitHub Actions      ✅ HOÀN THÀNH
```

---

## ✅ Đã Hoàn Thành (Completed)

- [x] **Loại bỏ thư mục `frontend/` trùng lặp**: Toàn bộ source code Landing Page (`index.html`, `CNAME`, `assets/`) hiện tại đặt duy nhất tại thư mục gốc Root.
- [x] **Cập nhật GitHub Actions (`deploy-frontend.yml`)**: Cấu hình upload artifact từ root `.` trực tiếp lên GitHub Pages.
- [x] **Tái cấu trúc Landing Page (`index.html`)**:
  - **Dịch vụ 1 (Đặt Online Bán Lẻ)**: Giới thiệu ứng dụng **Be (BeFood)** & **ShopeeFood** với nút click liên kết trực tiếp & ưu đãi.
  - **Dịch vụ 2 (Đặt Sỉ Chai 330ml & 500ml)**: Bảng mốc quy cách chai 330ml (pha sẵn) và chai 500ml (đậm đặc + hũ kem riêng), kèm nút gọi hotline `0905 382 080`.
  - **Quy trình đặt sỉ 3 bước**: Gọi điện/Zalo → Chốt số lượng & giá sỉ → Giao hàng tận nơi.
  - **Bento Grid**: 4 ưu điểm nổi bật (Kem muối độc quyền, Robusta nguyên chất, Đóng chai ATTP, Chiết khấu hấp dẫn).
- [x] **Tối ưu UI/UX & CSS**: Glassmorphism header, animations cuộn trang (IntersectionObserver), nghiêng 3D hero bento, đếm số (Count-up), sticky mobile call bar.

---

## 📝 Nhật Ký Thay Đổi (Changelog)

| Ngày | Thay đổi | Thực hiện bởi |
|---|---|---|
| 2026-08-11 | Loại bỏ hoàn toàn thư mục `frontend/` trùng lặp. Đưa toàn bộ mã nguồn về root (`.`) và cập nhật workflow deploy GitHub Actions. | Antigravity AI |
| 2026-08-11 | Chuyển đổi kiến trúc sang Pure Frontend Static Landing Page trên GitHub Pages giới thiệu 2 dịch vụ core (Be & ShopeeFood + Gọi sỉ chai 330ml/500ml). | Antigravity AI |
