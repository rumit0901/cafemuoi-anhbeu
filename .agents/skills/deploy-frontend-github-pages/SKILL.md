---
name: deploy-frontend-github-pages
description: Hướng dẫn chi tiết từng bước deploy thư mục frontend (static HTML/CSS/JS hoặc SPA) lên GitHub Pages sử dụng GitHub Actions hoặc git subtree.
---

# Kỹ năng Deploy Thư mục Frontend lên GitHub Pages

Sử dụng kỹ năng này khi cần xuất bản hoặc cập nhật phần frontend nằm trong thư mục con (ví dụ: `frontend/`) của repository lên GitHub Pages.

## Phương pháp 1: GitHub Actions Workflow (Khuyên dùng - Tự động hóa)

### Bước 1: Tạo file workflow GitHub Actions
Tạo file `.github/workflows/deploy-frontend.yml` tại gốc dự án:

```yaml
name: Deploy Frontend to GitHub Pages

on:
  push:
    branches:
      - main
  workflow_dispatch:

permissions:
  contents: read
  pages: write
  id-token: write

concurrency:
  group: 'pages'
  cancel-in-progress: true

jobs:
  deploy:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: ubuntu-latest
    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Setup Pages
        uses: actions/configure-pages@v5

      - name: Upload artifact (Thư mục frontend)
        uses: actions/upload-pages-artifact@v3
        with:
          path: './frontend'

      - name: Deploy to GitHub Pages
        id: deployment
        uses: actions/deploy-pages@v4
```

### Bước 2: Cấu hình trên GitHub Repository Settings
1. Mở Repository trên GitHub.
2. Vào **Settings** -> **Pages**.
3. Tại mục **Build and deployment** -> **Source**, chọn **GitHub Actions**.

### Bước 3: Commit & Push
```bash
git add .github/workflows/deploy-frontend.yml
git commit -m "ci: setup GitHub Pages auto-deploy workflow"
git push origin main
```

---

## Phương pháp 2: Git Subtree (Thủ công / Đơn giản)

Nếu không dùng GitHub Actions, có thể đẩy trực tiếp thư mục `frontend` vào nhánh `gh-pages`:

```bash
git subtree push --prefix frontend origin gh-pages
```

Sau đó tại **Settings** -> **Pages**:
- Source: **Deploy from a branch**
- Branch: **`gh-pages`** / Folder: **`/ (root)`** -> **Save**.

---

## 🛠 Lưu ý quan trọng & Custom Domain (CNAME)
1. **File CNAME**: Nếu frontend có Custom Domain (ví dụ `cafemuoi-anhbeu.vn`), đảm bảo file `CNAME` nằm ngay trong thư mục `frontend/CNAME`.
2. **Cấu hình DNS**: Trỏ bản ghi A record về các IP của GitHub Pages (`185.199.108.153`, `185.199.109.153`, `185.199.110.153`, `185.199.111.153`) hoặc CNAME record trỏ về `<username>.github.io`.
