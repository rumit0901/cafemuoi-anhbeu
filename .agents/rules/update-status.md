# Rule: Luôn cập nhật PROJECT_STATUS.md sau mỗi thay đổi

## Áp dụng cho
Tất cả AI agent làm việc trong dự án **"Cà phê muối anh Bếu"** (repo này).

---

## Quy tắc bắt buộc

Sau khi hoàn thành **bất kỳ thay đổi nào** trong dự án (tạo file, sửa code, thêm tính năng, thay đổi cấu hình, cập nhật tài liệu...), AI agent **bắt buộc phải**:

### 1. Cập nhật `PROJECT_STATUS.md` ngay lập tức với các thay đổi:

- **Mục "Giai đoạn hiện tại"**: Cập nhật phase đang ở nếu có thay đổi.
- **Mục "Cập nhật lần cuối"**: Cập nhật ngày hiện tại (định dạng YYYY-MM-DD).
- **Mục "Cập nhật bởi"**: Ghi tên AI agent hoặc "User + [tên AI]".
- **Mục "✅ Đã hoàn thành"**: Đánh dấu `[x]` cho các task vừa hoàn thành.
- **Mục "🔄 Đang thực hiện"**: Cập nhật task đang làm dở (nếu có).
- **Mục "📋 Việc cần làm tiếp theo"**: Cập nhật task mới nếu phát sinh.
- **Mục "📁 Cấu trúc file hiện tại"**: Thêm/xóa file đúng với thực tế sau thay đổi.
- **Mục "📝 Nhật ký thay đổi (Changelog)"**: Thêm 1 dòng mới ghi lại thay đổi vừa thực hiện.

### 2. Nếu có quyết định kỹ thuật mới:
- Thêm vào bảng **"Quyết định kỹ thuật đã thống nhất"** trong `PROJECT_STATUS.md`.
- Cập nhật **`ARCHITECTURE.md`** phần liên quan (API Contract, cấu trúc thư mục, v.v.) nếu cần.

---

## Khi bắt đầu một phiên làm việc mới

Trước khi thực hiện bất kỳ tác vụ nào, AI agent **phải đọc**:
1. `PROJECT_STATUS.md` — để nắm trạng thái hiện tại và context của dự án.
2. `ARCHITECTURE.md` — để hiểu kiến trúc, quy ước code, coding standards và TDD workflow.

---

## Ví dụ Changelog entry đúng chuẩn

```markdown
| 2026-08-10 | Tạo `menu.html` + `menu.page.js` + `menu.tmpl.html` — hoàn chỉnh trang Menu | Antigravity |
| 2026-08-10 | Implement GET `/api/menu` trong PHP Slim, trả về JSON đúng API contract | Antigravity |
```

---

## Lý do

File `PROJECT_STATUS.md` là **bộ nhớ liên tục (persistent memory)** của dự án.
Nó đảm bảo bất kỳ AI agent mới nào (hoặc AI trong phiên mới) đều có thể:
- Hiểu ngay dự án đang ở đâu mà không cần đọc toàn bộ codebase.
- Không làm lại việc đã làm.
- Tiếp tục đúng từ điểm dừng của AI trước.
