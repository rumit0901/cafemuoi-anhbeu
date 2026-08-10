<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập CMS Admin — Cà Phê Muối Anh Bếu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 40px; width: 100%; max-width: 420px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        .logo-area { text-align: center; margin-bottom: 28px; }
        .logo-area h1 { font-size: 24px; font-weight: 800; color: #f59e0b; margin-top: 10px; }
        .logo-area p { color: #94a3b8; font-size: 14px; margin-top: 4px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px 16px; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 15px; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: #f59e0b; }
        .btn-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #f59e0b, #d97706); border: none; border-radius: 8px; color: #fff; font-weight: 700; font-size: 16px; cursor: pointer; transition: opacity 0.2s; }
        .btn-submit:hover { opacity: 0.9; }
        .alert-box { padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; display: none; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #86efac; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-area">
            <span style="font-size: 40px;">☕</span>
            <h1>Cà Phê Muối Anh Bếu</h1>
            <p>Hệ thống CMS Quản trị Kênh Bán Hàng & Báo Giá Sỉ</p>
        </div>

        <div id="alertBox" class="alert-box alert-error"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="username">Tên đăng nhập Admin</label>
                <input type="text" id="username" placeholder="Nhập username (mặc định: admin)" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" placeholder="Nhập mật khẩu (mặc định: admin)" required>
            </div>
            <button type="submit" class="btn-submit" id="btnSubmit">Đăng Nhập CMS Admin</button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const alertBox = document.getElementById('alertBox');
            const btnSubmit = document.getElementById('btnSubmit');
            alertBox.style.display = 'none';
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Đang xử lý đăng nhập...';

            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            try {
                const response = await fetch('/admin/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    alertBox.className = 'alert-box alert-success';
                    alertBox.textContent = 'Đăng nhập thành công! Đang chuyển hướng...';
                    alertBox.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = '/admin';
                    }, 800);
                } else {
                    alertBox.className = 'alert-box alert-error';
                    alertBox.textContent = data.message || 'Đăng nhập thất bại.';
                    alertBox.style.display = 'block';
                }
            } catch (err) {
                alertBox.className = 'alert-box alert-error';
                alertBox.textContent = 'Lỗi kết nối tới Server Backend.';
                alertBox.style.display = 'block';
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Đăng Nhập CMS Admin';
            }
        });
    </script>
</body>
</html>
