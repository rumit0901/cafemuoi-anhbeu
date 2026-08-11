<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Admin Panel — Cà Phê Muối Anh Bếu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
        header { background: #1e293b; border-bottom: 1px solid #334155; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
        .logo { font-size: 20px; font-weight: 800; color: #f59e0b; display: flex; align-items: center; gap: 10px; }
        .user-info { display: flex; align-items: center; gap: 16px; font-size: 14px; color: #94a3b8; }
        .btn-logout { background: #dc2626; border: none; color: #fff; padding: 8px 16px; border-radius: 6px; font-weight: 600; cursor: pointer; }

        .layout { display: flex; flex: 1; }
        .sidebar { width: 260px; background: #1e293b; border-right: 1px solid #334155; padding: 24px 16px; }
        .nav-item { display: block; width: 100%; padding: 12px 16px; margin-bottom: 8px; border-radius: 8px; background: transparent; border: none; color: #94a3b8; font-size: 15px; font-weight: 600; text-align: left; cursor: pointer; transition: all 0.2s; }
        .nav-item.active, .nav-item:hover { background: #0f172a; color: #f59e0b; }

        .content { flex: 1; padding: 32px; overflow-y: auto; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .panel-title { font-size: 24px; font-weight: 700; color: #f8fafc; }
        .btn-add { background: #f59e0b; border: none; color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; }

        table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 12px; overflow: hidden; border: 1px solid #334155; }
        th, td { padding: 14px 18px; text-align: left; border-bottom: 1px solid #334155; font-size: 14px; }
        th { background: #0f172a; color: #94a3b8; font-weight: 600; text-transform: uppercase; font-size: 12px; }
        tr:last-child td { border-bottom: none; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; }
        .badge-active { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .badge-inactive { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .badge-new { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
        .badge-contacted { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
        
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); align-items: center; justify-content: center; z-index: 100; }
        .modal { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 28px; width: 100%; max-width: 500px; }
        .modal h3 { font-size: 20px; color: #f59e0b; margin-bottom: 20px; }
        .modal-field { margin-bottom: 16px; }
        .modal-field label { display: block; font-size: 13px; color: #94a3b8; margin-bottom: 6px; font-weight: 600; }
        .modal-field input, .modal-field select, .modal-field textarea { width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #fff; font-size: 14px; outline: none; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
        .btn-cancel { background: #475569; border: none; color: #fff; padding: 10px 18px; border-radius: 6px; cursor: pointer; }
        .btn-save { background: #f59e0b; border: none; color: #fff; padding: 10px 18px; border-radius: 6px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <span>☕</span> Cà Phê Muối Anh Bếu — CMS Admin Panel
        </div>
        <div class="user-info">
            <span>Xin chào, <strong>Quản trị viên Admin</strong></span>
            <button class="btn-logout" onclick="logout()">Đăng Xuất</button>
        </div>
    </header>

    <div class="layout">
        <div class="sidebar">
            <button class="nav-item active" onclick="switchTab('channels')">📱 Kênh Bán Hàng</button>
            <button class="nav-item" onclick="switchTab('products')">🍹 Menu Bán Lẻ</button>
            <button class="nav-item" onclick="switchTab('wholesale')">📦 Gói Sỉ Đóng Chai</button>
            <button class="nav-item" onclick="switchTab('contacts')">📩 Đơn Yêu Cầu Sỉ</button>
        </div>

        <div class="content">
            <!-- TAB 1: KÊNH BÁN HÀNG -->
            <div id="tab-channels" class="tab-panel active">
                <div class="panel-header">
                    <h2 class="panel-title">Quản Lý Kênh Bán Hàng Bán Lẻ (ShopeeFood, GrabFood...)</h2>
                    <button class="btn-add" onclick="openChannelModal()">+ Thêm Kênh Bán Hàng</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Kênh</th>
                            <th>Đường Link Gian Hàng</th>
                            <th>Badge</th>
                            <th>Thứ Tự</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="channelsTableBody"></tbody>
                </table>
            </div>

            <!-- TAB 2: MENU BÁN LẺ -->
            <div id="tab-products" class="tab-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Quản Lý Thực Đơn Bán Lẻ</h2>
                    <button class="btn-add" onclick="openProductModal()">+ Thêm Món Mới</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Món</th>
                            <th>Giá Bán</th>
                            <th>Tag Highlight</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody"></tbody>
                </table>
            </div>

            <!-- TAB 3: GÓI BÁN SỈ -->
            <div id="tab-wholesale" class="tab-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Quản Lý Gói Sỉ Cà Phê Muối Đóng Chai</h2>
                    <button class="btn-add" onclick="openWholesaleModal()">+ Thêm Gói Sỉ</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Gói</th>
                            <th>Dung Tích</th>
                            <th>Tối Thiểu</th>
                            <th>Mốc Giá Sỉ</th>
                            <th>Badge</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody id="wholesaleTableBody"></tbody>
                </table>
            </div>

            <!-- TAB 4: ĐƠN YÊU CẦU SỈ -->
            <div id="tab-contacts" class="tab-panel">
                <div class="panel-header">
                    <h2 class="panel-title">Danh Sách Khách Đăng Ký Báo Giá Sỉ & Liên Hệ</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách Hàng</th>
                            <th>Số Điện Thoại</th>
                            <th>Email</th>
                            <th>Số Lượng Chai</th>
                            <th>Ghi Chú</th>
                            <th>Trạng Thái</th>
                            <th>Cập Nhật Status</th>
                        </tr>
                    </thead>
                    <tbody id="contactsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL OVERLAY CHUNG -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal">
            <h3 id="modalTitle" class="modal-title">Thêm Mới</h3>
            <div id="modalFormContent"></div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal()">Hủy</button>
                <button id="btnModalSave" class="btn-save">Lưu Thay Đổi</button>
            </div>
        </div>
    </div>

    <script>
        window.channelsDataCache = [];
        window.productsDataCache = [];
        window.wholesaleDataCache = [];

        function switchTab(tabName) {
            document.querySelectorAll('.nav-item').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');

            if (tabName === 'channels') loadChannels();
            if (tabName === 'products') loadProducts();
            if (tabName === 'wholesale') loadWholesale();
            if (tabName === 'contacts') loadContacts();
        }

        async function logout() {
            await fetch('/admin/api/logout', { method: 'POST' });
            window.location.href = '/admin/login';
        }

        // LOAD CHANNELS
        async function loadChannels() {
            const res = await fetch('/admin/api/channels');
            const data = await res.json();
            window.channelsDataCache = data;
            const tbody = document.getElementById('channelsTableBody');
            tbody.innerHTML = data.map(c => {
                const isActive = Number(c.active) === 1;
                return `
                <tr>
                    <td>${c.id}</td>
                    <td><strong>${c.name}</strong></td>
                    <td><a href="${c.url}" target="_blank" style="color:#60a5fa;">${c.url}</a></td>
                    <td><span class="badge badge-contacted">${c.badge || '-'}</span></td>
                    <td>${c.display_order ?? 0}</td>
                    <td>${isActive ? '<span class="badge badge-active">Bật</span>' : '<span class="badge badge-inactive">Tắt</span>'}</td>
                    <td>
                        <button onclick="openEditChannelModal(${c.id})" style="background:#3b82f6; border:none; color:#fff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-right:4px;">Sửa</button>
                        <button onclick="deleteChannel(${c.id})" style="background:#ef4444; border:none; color:#fff; padding:6px 12px; border-radius:4px; cursor:pointer;">Xóa</button>
                    </td>
                </tr>
            `;
            }).join('');
        }

        // LOAD PRODUCTS
        async function loadProducts() {
            const res = await fetch('/admin/api/products');
            const data = await res.json();
            window.productsDataCache = data;
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = data.map(p => {
                const isActive = Number(p.active) === 1;
                const priceFormatted = (Number(p.price) || 0).toLocaleString('vi-VN') + ' đ';
                const tagBadge = p.tag ? `<span class="badge badge-contacted">${p.tag}</span>` : '-';
                const imgHtml = p.image ? `<img src="${p.image}" style="width:28px; height:28px; object-fit:cover; border-radius:4px; vertical-align:middle; margin-right:6px;" alt="">` : '<span style="margin-right:6px;">🍹</span>';
                return `
                <tr>
                    <td>${p.id}</td>
                    <td>${imgHtml}<strong>${p.name}</strong></td>
                    <td>${priceFormatted}</td>
                    <td>${tagBadge}</td>
                    <td>${isActive ? '<span class="badge badge-active">Bật</span>' : '<span class="badge badge-inactive">Tắt</span>'}</td>
                    <td>
                        <button onclick="openEditProductModal(${p.id})" style="background:#3b82f6; border:none; color:#fff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-right:4px;">Sửa</button>
                        <button onclick="deleteProduct(${p.id})" style="background:#ef4444; border:none; color:#fff; padding:6px 12px; border-radius:4px; cursor:pointer;">Xóa</button>
                    </td>
                </tr>
            `;
            }).join('');
        }

        // LOAD WHOLESALE
        async function loadWholesale() {
            const res = await fetch('/admin/api/wholesale');
            const data = await res.json();
            window.wholesaleDataCache = data;
            const tbody = document.getElementById('wholesaleTableBody');
            tbody.innerHTML = data.map(w => {
                const isActive = Number(w.active) === 1;
                return `
                <tr>
                    <td>${w.id}</td>
                    <td><strong>${w.name}</strong></td>
                    <td><span style="background:#334155; color:#38bdf8; padding:3px 8px; border-radius:12px; font-weight:bold; font-size:0.85rem;">📦 ${w.bottle_size || '330ml'}</span></td>
                    <td>Từ ${w.min_quantity || 1} chai</td>
                    <td><strong style="color:#f59e0b;">${w.price_tier || '-'}</strong></td>
                    <td><span class="badge badge-contacted">${w.badge || '-'}</span></td>
                    <td>${isActive ? '<span class="badge badge-active">Bật</span>' : '<span class="badge badge-inactive">Tắt</span>'}</td>
                    <td>
                        <button onclick="openEditWholesaleModal(${w.id})" style="background:#3b82f6; border:none; color:#fff; padding:6px 12px; border-radius:4px; cursor:pointer; margin-right:4px;">Sửa</button>
                        <button onclick="deleteWholesale(${w.id})" style="background:#ef4444; border:none; color:#fff; padding:6px 12px; border-radius:4px; cursor:pointer;">Xóa</button>
                    </td>
                </tr>
            `;
            }).join('');
        }

        // LOAD CONTACTS
        async function loadContacts() {
            const res = await fetch('/admin/api/contacts');
            const data = await res.json();
            const tbody = document.getElementById('contactsTableBody');
            const statusBadgeMap = {
                'new': '<span class="badge badge-new">Mới</span>',
                'contacted': '<span class="badge badge-contacted">Đã gọi điện</span>',
                'quoted': '<span class="badge badge-contacted">Đã báo giá</span>',
                'completed': '<span class="badge badge-active">Hoàn tất</span>'
            };
            tbody.innerHTML = data.map(ct => `
                <tr>
                    <td>${ct.id}</td>
                    <td><strong>${ct.name}</strong></td>
                    <td>${ct.phone}</td>
                    <td>${ct.email || '-'}</td>
                    <td><strong style="color:#4ade80;">${ct.expected_quantity || 0} chai</strong></td>
                    <td>${ct.message || '-'}</td>
                    <td>${statusBadgeMap[ct.status] || `<span class="badge badge-new">${ct.status}</span>`}</td>
                    <td>
                        <select onchange="updateContactStatus(${ct.id}, this.value)" style="background:#0f172a; color:#fff; border:1px solid #334155; padding:4px 8px; border-radius:4px;">
                            <option value="new" ${ct.status === 'new' ? 'selected' : ''}>Mới</option>
                            <option value="contacted" ${ct.status === 'contacted' ? 'selected' : ''}>Đã gọi điện</option>
                            <option value="quoted" ${ct.status === 'quoted' ? 'selected' : ''}>Đã báo giá</option>
                            <option value="completed" ${ct.status === 'completed' ? 'selected' : ''}>Hoàn tất</option>
                        </select>
                    </td>
                </tr>
            `).join('');
        }

        async function deleteChannel(id) {
            if (confirm('Bạn có chắc muốn xóa kênh bán hàng này?')) {
                await fetch('/admin/api/channels/' + id, { method: 'DELETE' });
                loadChannels();
            }
        }

        async function deleteProduct(id) {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                await fetch('/admin/api/products/' + id, { method: 'DELETE' });
                loadProducts();
            }
        }

        async function deleteWholesale(id) {
            if (confirm('Bạn có chắc muốn xóa gói sỉ này?')) {
                await fetch('/admin/api/wholesale/' + id, { method: 'DELETE' });
                loadWholesale();
            }
        }

        async function updateContactStatus(id, status) {
            await fetch('/admin/api/contacts/' + id, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            });
            loadContacts();
        }

        function closeModal() {
            document.getElementById('modalOverlay').style.display = 'none';
        }

        function openChannelModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Kênh Bán Hàng Mới';
            document.getElementById('modalFormContent').innerHTML = `
                <div class="modal-field"><label>Tên Kênh (ShopeeFood, GrabFood...)</label><input type="text" id="m_name" required></div>
                <div class="modal-field"><label>Đường Dẫn Logo (Ví dụ: /assets/images/shopeefood.png)</label><input type="text" id="m_logo" placeholder="/assets/images/shopeefood.png"></div>
                <div class="modal-field"><label>Đường Link Gian Hàng</label><input type="text" id="m_url" required></div>
                <div class="modal-field"><label>Badge Hiển Thị (vd: Đặt Ngay, Freeship)</label><input type="text" id="m_badge" value="Đặt Ngay"></div>
                <div class="modal-field"><label>Thứ Tự Sắp Xếp</label><input type="number" id="m_order" value="1"></div>
            `;
            document.getElementById('btnModalSave').onclick = async () => {
                const name = document.getElementById('m_name').value;
                const logo = document.getElementById('m_logo').value;
                const url = document.getElementById('m_url').value;
                const badge = document.getElementById('m_badge').value;
                const display_order = parseInt(document.getElementById('m_order').value) || 1;
                await fetch('/admin/api/channels', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, logo, url, badge, display_order, active: 1 })
                });
                closeModal();
                loadChannels();
            };
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function openEditChannelModal(id) {
            const item = window.channelsDataCache.find(c => c.id == id);
            if (!item) return;

            document.getElementById('modalTitle').textContent = `Chỉnh Sửa Kênh Bán Hàng (ID #${item.id})`;
            document.getElementById('modalFormContent').innerHTML = `
                <div class="modal-field"><label>Tên Kênh (ShopeeFood, GrabFood...)</label><input type="text" id="m_name" value="${(item.name || '').replace(/"/g, '&quot;')}" required></div>
                <div class="modal-field"><label>Đường Dẫn Logo (Ví dụ: /assets/images/shopeefood.png)</label><input type="text" id="m_logo" value="${(item.logo || '').replace(/"/g, '&quot;')}"></div>
                <div class="modal-field"><label>Đường Link Gian Hàng</label><input type="text" id="m_url" value="${(item.url || '').replace(/"/g, '&quot;')}" required></div>
                <div class="modal-field"><label>Badge Hiển Thị (vd: Đặt Ngay, Freeship)</label><input type="text" id="m_badge" value="${(item.badge || '').replace(/"/g, '&quot;')}"></div>
                <div class="modal-field"><label>Thứ Tự Sắp Xếp</label><input type="number" id="m_order" value="${item.display_order ?? 1}"></div>
                <div class="modal-field"><label>Trạng Thái</label>
                    <select id="m_active" style="width:100%; padding:8px; background:#0f172a; color:#fff; border:1px solid #334155; border-radius:4px;">
                        <option value="1" ${item.active == 1 ? 'selected' : ''}>Bật (Hiển thị)</option>
                        <option value="0" ${item.active == 0 ? 'selected' : ''}>Tắt (Ẩn)</option>
                    </select>
                </div>
            `;
            document.getElementById('btnModalSave').onclick = async () => {
                const name = document.getElementById('m_name').value;
                const logo = document.getElementById('m_logo').value;
                const url = document.getElementById('m_url').value;
                const badge = document.getElementById('m_badge').value;
                const display_order = parseInt(document.getElementById('m_order').value) || 1;
                const active = parseInt(document.getElementById('m_active').value) || 0;
                await fetch('/admin/api/channels/' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, logo, url, badge, display_order, active })
                });
                closeModal();
                loadChannels();
            };
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function openProductModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Món Bán Lẻ Mới';
            document.getElementById('modalFormContent').innerHTML = `
                <div class="modal-field"><label>Tên Món</label><input type="text" id="m_pname" required></div>
                <div class="modal-field"><label>Giá Bán (VNĐ)</label><input type="number" id="m_pprice" value="35000"></div>
                <div class="modal-field"><label>Tag (Best-Seller, Must-Try...)</label><input type="text" id="m_ptag"></div>
                <div class="modal-field"><label>Mô Tả Sản Phẩm</label><textarea id="m_pdesc" rows="3"></textarea></div>
            `;
            document.getElementById('btnModalSave').onclick = async () => {
                const name = document.getElementById('m_pname').value;
                const price = parseInt(document.getElementById('m_pprice').value) || 0;
                const tag = document.getElementById('m_ptag').value;
                const description = document.getElementById('m_pdesc').value;
                await fetch('/admin/api/products', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, price, tag, description, active: 1 })
                });
                closeModal();
                loadProducts();
            };
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function openEditProductModal(id) {
            const item = window.productsDataCache.find(p => p.id == id);
            if (!item) return;

            document.getElementById('modalTitle').textContent = `Chỉnh Sửa Món Bán Lẻ (ID #${item.id})`;
            document.getElementById('modalFormContent').innerHTML = `
                <div class="modal-field"><label>Tên Món</label><input type="text" id="m_pname" value="${(item.name || '').replace(/"/g, '&quot;')}" required></div>
                <div class="modal-field"><label>Giá Bán (VNĐ)</label><input type="number" id="m_pprice" value="${item.price}"></div>
                <div class="modal-field"><label>Tag (Best-Seller, Must-Try...)</label><input type="text" id="m_ptag" value="${(item.tag || '').replace(/"/g, '&quot;')}"></div>
                <div class="modal-field"><label>Mô Tả Sản Phẩm</label><textarea id="m_pdesc" rows="3">${item.description || ''}</textarea></div>
                <div class="modal-field"><label>Trạng Thái</label>
                    <select id="m_pactive" style="width:100%; padding:8px; background:#0f172a; color:#fff; border:1px solid #334155; border-radius:4px;">
                        <option value="1" ${item.active == 1 ? 'selected' : ''}>Bật (Hiển thị)</option>
                        <option value="0" ${item.active == 0 ? 'selected' : ''}>Tắt (Ẩn)</option>
                    </select>
                </div>
            `;
            document.getElementById('btnModalSave').onclick = async () => {
                const name = document.getElementById('m_pname').value;
                const price = parseInt(document.getElementById('m_pprice').value) || 0;
                const tag = document.getElementById('m_ptag').value;
                const description = document.getElementById('m_pdesc').value;
                const active = parseInt(document.getElementById('m_pactive').value) || 0;
                await fetch('/admin/api/products/' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, price, tag, description, active })
                });
                closeModal();
                loadProducts();
            };
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function openWholesaleModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Gói Sỉ Đóng Chai Mới';
            document.getElementById('modalFormContent').innerHTML = `
                <div class="modal-field"><label>Tên Gói Sỉ</label><input type="text" id="m_wname" placeholder="vd: Gói Sỉ Chai 500ml Đại Lý" required></div>
                <div class="modal-field"><label>Dung Tích Chai</label><input type="text" id="m_wsize" value="330ml" placeholder="vd: 330ml, 500ml, 1L..." required></div>
                <div class="modal-field"><label>Số Lượng Tối Thiểu (Chai)</label><input type="number" id="m_wmin" value="10"></div>
                <div class="modal-field"><label>Mốc Giá / Chiết Khấu</label><input type="text" id="m_wtier" placeholder="vd: Chiết khấu 20%" required></div>
                <div class="modal-field"><label>Badge Hiển Thị</label><input type="text" id="m_wbadge" value="Chiết Khấu Cao"></div>
                <div class="modal-field"><label>Mô Tả Gói Sỉ</label><textarea id="m_wdesc" rows="2" placeholder="Ghi chú về chính sách sỉ hoặc hỗ trợ..."></textarea></div>
            `;
            document.getElementById('btnModalSave').onclick = async () => {
                const name = document.getElementById('m_wname').value;
                const bottle_size = document.getElementById('m_wsize').value.trim() || '330ml';
                const min_quantity = parseInt(document.getElementById('m_wmin').value) || 5;
                const price_tier = document.getElementById('m_wtier').value;
                const badge = document.getElementById('m_wbadge').value;
                const description = document.getElementById('m_wdesc').value;
                await fetch('/admin/api/wholesale', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, bottle_size, min_quantity, price_tier, badge, description, active: 1 })
                });
                closeModal();
                loadWholesale();
            };
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        function openEditWholesaleModal(id) {
            const item = window.wholesaleDataCache.find(w => w.id == id);
            if (!item) return;

            document.getElementById('modalTitle').textContent = `Chỉnh Sửa Gói Sỉ (ID #${item.id})`;
            document.getElementById('modalFormContent').innerHTML = `
                <div class="modal-field"><label>Tên Gói Sỉ</label><input type="text" id="m_wname" value="${(item.name || '').replace(/"/g, '&quot;')}" required></div>
                <div class="modal-field"><label>Dung Tích Chai</label><input type="text" id="m_wsize" value="${(item.bottle_size || '330ml').replace(/"/g, '&quot;')}" placeholder="vd: 330ml, 500ml, 1L..." required></div>
                <div class="modal-field"><label>Số Lượng Tối Thiểu (Chai)</label><input type="number" id="m_wmin" value="${item.min_quantity ?? 10}"></div>
                <div class="modal-field"><label>Mốc Giá / Chiết Khấu</label><input type="text" id="m_wtier" value="${(item.price_tier || '').replace(/"/g, '&quot;')}" required></div>
                <div class="modal-field"><label>Badge Hiển Thị</label><input type="text" id="m_wbadge" value="${(item.badge || '').replace(/"/g, '&quot;')}"></div>
                <div class="modal-field"><label>Mô Tả Gói Sỉ</label><textarea id="m_wdesc" rows="2">${item.description || ''}</textarea></div>
                <div class="modal-field"><label>Trạng Thái</label>
                    <select id="m_wactive" style="width:100%; padding:8px; background:#0f172a; color:#fff; border:1px solid #334155; border-radius:4px;">
                        <option value="1" ${item.active == 1 ? 'selected' : ''}>Bật (Hiển thị)</option>
                        <option value="0" ${item.active == 0 ? 'selected' : ''}>Tắt (Ẩn)</option>
                    </select>
                </div>
            `;
            document.getElementById('btnModalSave').onclick = async () => {
                const name = document.getElementById('m_wname').value;
                const bottle_size = document.getElementById('m_wsize').value.trim() || '330ml';
                const min_quantity = parseInt(document.getElementById('m_wmin').value) || 5;
                const price_tier = document.getElementById('m_wtier').value;
                const badge = document.getElementById('m_wbadge').value;
                const description = document.getElementById('m_wdesc').value;
                const active = parseInt(document.getElementById('m_wactive').value) || 0;
                await fetch('/admin/api/wholesale/' + id, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, bottle_size, min_quantity, price_tier, badge, description, active })
                });
                closeModal();
                loadWholesale();
            };
            document.getElementById('modalOverlay').style.display = 'flex';
        }

        // Khởi tạo tab đầu tiên
        loadChannels();
    </script>
</body>
</html>
