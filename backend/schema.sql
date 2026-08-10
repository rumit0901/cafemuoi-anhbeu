-- MySQL & SQLite Schema cho Hệ thống Backend Cà phê muối Anh Bếu

-- 1. Bảng tài khoản quản trị viên (Admin Users)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Bảng sản phẩm bán lẻ (Retail Products)
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'retail',
    price INT DEFAULT 0,
    bottle_size VARCHAR(50) NULL,
    min_quantity INT DEFAULT 1,
    price_tier VARCHAR(100) NULL,
    tag VARCHAR(50) NULL,
    image TEXT NULL,
    description TEXT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Bảng kênh bán hàng bán lẻ (Sales Channels: ShopeeFood, GrabFood...)
CREATE TABLE IF NOT EXISTS sales_channels (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    logo TEXT NULL,
    url TEXT NOT NULL,
    badge VARCHAR(50) DEFAULT 'Đặt Ngay',
    active TINYINT DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Bảng gói sỉ đóng chai (Wholesale Packages)
CREATE TABLE IF NOT EXISTS wholesale_packages (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    bottle_size VARCHAR(50) DEFAULT '330ml',
    min_quantity INT DEFAULT 5,
    price_tier VARCHAR(100) NOT NULL,
    description TEXT NULL,
    badge VARCHAR(50) DEFAULT 'Chiết khấu cao',
    active TINYINT DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. Bảng chi nhánh cửa hàng (Stores)
CREATE TABLE IF NOT EXISTS stores (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NULL,
    opening_hours VARCHAR(100) NULL,
    lat DOUBLE NOT NULL,
    lng DOUBLE NOT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 6. Bảng đánh giá khách hàng (Reviews)
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    rating INT DEFAULT 5,
    comment TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'retail',
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 7. Bảng bài viết / tin tức (Blog Posts)
CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    excerpt TEXT NULL,
    content TEXT NULL,
    image TEXT NULL,
    author VARCHAR(100) DEFAULT 'Anh Bếu',
    published_at VARCHAR(20) NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 8. Bảng câu chuyện thương hiệu (Story Content)
CREATE TABLE IF NOT EXISTS stories (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NULL,
    hero_image TEXT NULL,
    paragraphs TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. Bảng đơn liên hệ & đăng ký báo giá sỉ (Contacts / Inquiries)
CREATE TABLE IF NOT EXISTS contacts (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    inquiry_type VARCHAR(20) DEFAULT 'wholesale',
    expected_quantity INT DEFAULT 0,
    message TEXT NULL,
    status VARCHAR(20) DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- SEED DATA MẶC ĐỊNH (Sample Initial Data)
-- ==========================================

-- Mật khẩu admin: 'admin' (được hash bằng PASSWORD_BCRYPT)
INSERT INTO users (username, password_hash, full_name, role)
VALUES ('admin', '$2y$10$89v100h2j33oH.H7l8j1wO9.9yR09Z5u1234567890abcdefghijk', 'Quản trị viên Anh Bếu', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Seed dữ liệu Kênh bán hàng bán lẻ
INSERT INTO sales_channels (name, logo, url, badge, active, display_order) VALUES
('ShopeeFood', 'assets/img/shopeefood.png', 'https://shopeefood.vn/', 'Giảm 20k', 1, 1),
('GrabFood', 'assets/img/grabfood.png', 'https://food.grab.com/', 'Freeship', 1, 2),
('GoFood', 'assets/img/gofood.png', 'https://gofood.vn/', 'Đặt Ngay', 1, 3),
('BeFood', 'assets/img/befood.png', 'https://be.com.vn/', 'Ưu Đãi', 1, 4);

-- Seed dữ liệu Sản phẩm bán lẻ mẫu
INSERT INTO products (name, category, price, tag, image, description, active) VALUES
('Cà phê muối truyền thống Anh Bếu', 'retail', 35000, 'Best-Seller', 'assets/img/menu-1.jpg', 'Hương vị cà phê phin nguyên chất kết hợp lớp kem muối béo mịn độc quyền.', 1),
('Cà phê kem muối Macchiato', 'retail', 39000, 'Must-Try', 'assets/img/menu-2.jpg', 'Đậm đà hương vị cà phê hiện đại với lớp foam muối bồng bềnh.', 1),
('Cà phê muối Đen Đậm', 'retail', 32000, 'Mới', 'assets/img/menu-3.jpg', 'Dành cho tín đồ thích gu cà phê đậm đà chuẩn vị truyền thống.', 1);

-- Seed dữ liệu Gói sỉ đóng chai mẫu (Đa dung tích 330ml & 500ml)
INSERT INTO wholesale_packages (name, bottle_size, min_quantity, price_tier, description, badge, active, display_order) VALUES
('Gói Sỉ Dùng Thử 330ml (Thùng 10 chai)', '330ml', 10, 'Chiết khấu 15%', 'Thích hợp cho quán cà phê nhỏ, sự kiện hoặc văn phòng mua dùng thử.', 'Phổ biến', 1, 1),
('Gói Sỉ Chai Lớn 500ml (Thùng 20 chai)', '500ml', 20, 'Chiết khấu 20%', 'Chai dung tích lớn 500ml tiện lợi cho gia đình & sự kiện.', 'Bán Chạy', 1, 2),
('Gói Sỉ Đại Lý 330ml (Thùng 50 chai)', '330ml', 50, 'Chiết khấu 25%', 'Miễn phí giao hàng nội thành, hỗ trợ khay trưng bày & POSM thương hiệu.', 'Gợi ý', 1, 3),
('Gói Sỉ Đại Lý 500ml (Thùng 50 chai)', '500ml', 50, 'Chiết khấu 30%', 'Ưu đãi đặc biệt dành cho chuỗi đối tác phân phối chai 500ml.', 'Ưu Đãi Lớn', 1, 4),
('Gói Sỉ Nhà Phân Phối (Từ 100 chai)', '330ml', 100, 'Báo giá sỉ ưu đãi lớn', 'Chính sách độc quyền khu vực, chiết khấu tối đa & hỗ trợ marketing.', 'Chiết Khấu Cao', 1, 5);

-- Seed dữ liệu Chi nhánh mẫu
INSERT INTO stores (name, address, phone, opening_hours, lat, lng, active) VALUES
('Chi Nhánh Quận 1 - Flagship Store', '123 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh', '0901 234 567', '07:00 - 22:00', 10.7769, 106.7009, 1),
('Chi Nhánh Bình Thạnh', '456 Điện Biên Phủ, Phường 25, Quận Bình Thạnh, TP. Hồ Chí Minh', '0902 345 678', '06:30 - 21:30', 10.8012, 106.7123, 1);

-- Seed dữ liệu Đánh giá mẫu
INSERT INTO reviews (customer_name, rating, comment, type, active) VALUES
('Nguyễn Văn Minh (Quản lý Chuỗi Cà phê)', 5, 'Chất lượng cà phê muối đóng chai 330ml rất ổn định, lớp kem muối giữ được độ béo ngậy tới 5 ngày. Đã nhập sỉ hơn 300 chai.', 'wholesale_partner', 1),
('Trần Thị Hương', 5, 'Cà phê muối Anh Bếu ngon đậm đà, kem muối mặn nhẹ vừa miệng không bị ngấy. Đặt qua ShopeeFood giao hàng siêu nhanh!', 'retail', 1);

-- Seed dữ liệu Bài viết mẫu
INSERT INTO blog_posts (title, slug, excerpt, content, image, author, published_at, active) VALUES
('Chính sách tuyển đại lý & nhà phân phối Cà phê muối Anh Bếu 2026', 'chinh-sach-dai-ly-2026', 'Cơ hội hợp tác kinh doanh cà phê muối đóng chai 330ml với chiết khấu lên tới 35% và hỗ trợ truyền thông toàn diện.', 'Chi tiết chính sách sỉ cà phê muối đóng chai...', 'assets/img/blog-1.jpg', 'Anh Bếu', '10/08/2026', 1);

-- Seed dữ liệu Story mẫu
INSERT INTO stories (title, subtitle, hero_image, paragraphs) VALUES
('Hành Trình Đưa Cà Phê Muối Chuẩn Vị Đến Mọi Nhà', 'Từ công thức gia truyền đậm đà đến mô hình phân phối sỉ & lẻ hàng đầu', 'assets/img/story-hero.jpg', '["Thương hiệu Cà Phê Muối Anh Bếu khởi nguồn từ niềm đam mê sâu sắc với hạt cà phê Việt Nam và mong muốn đem đến trải nghiệm thức uống kết hợp độc đáo giữa vị đắng đậm đà và vị mặn béo ngậy của kem muối.", "Bằng việc ứng dụng quy trình đóng chai chuẩn an toàn thực phẩm, chúng tôi tự hào cung cấp cà phê muối tươi nguyên chất không chỉ cho khách lẻ thưởng thức hàng ngày mà còn là đối tác cung cấp giá sỉ uy tín cho hàng trăm quán cà phê và đại lý trên toàn quốc."]');
