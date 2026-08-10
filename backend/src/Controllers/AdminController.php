<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý toàn bộ CMS Admin Panel APIs (Xác thực & CRUD dữ liệu)
 */
class AdminController
{
    /**
     * Đăng nhập Admin
     * Endpoint: POST /admin/api/login
     */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');

        if (empty($username) || empty($password)) {
            return $this->jsonResponse($response, ['success' => false, 'message' => 'Vui lòng nhập tên đăng nhập và mật khẩu.'], 400);
        }

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            // Kiểm tra mật khẩu (hỗ trợ password_verify hoặc tài khoản admin/admin ban đầu)
            $isPasswordValid = false;
            if ($user) {
                if (password_verify($password, $user['password_hash'])) {
                    $isPasswordValid = true;
                } elseif ($username === 'admin' && $password === 'admin') {
                    $isPasswordValid = true;
                }
            }

            if (!$user || !$isPasswordValid) {
                return $this->jsonResponse($response, ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'], 401);
            }

            // Lưu session admin
            if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
                @session_start();
            }
            $_SESSION['admin_user'] = [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ];

            return $this->jsonResponse($response, [
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'token' => 'anhbeu_admin_secret_token',
                'user' => $_SESSION['admin_user']
            ]);
        } catch (\Throwable $e) {
            return $this->jsonResponse($response, ['success' => false, 'message' => 'Lỗi server khi đăng nhập: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Đăng xuất Admin
     * Endpoint: POST /admin/api/logout
     */
    public function logout(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        unset($_SESSION['admin_user']);
        @session_destroy();

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Đã đăng xuất thành công.']);
    }

    /**
     * Lấy thông tin tài khoản admin hiện tại
     * Endpoint: GET /admin/api/me
     */
    public function me(Request $request, Response $response): Response
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        $user = $_SESSION['admin_user'] ?? ['username' => 'admin', 'full_name' => 'Quản trị viên Anh Bếu', 'role' => 'admin'];
        return $this->jsonResponse($response, ['success' => true, 'user' => $user]);
    }

    // ==========================================
    // CRUD SẢN PHẨM BÁN LẺ (PRODUCTS)
    // ==========================================

    public function getProducts(Request $request, Response $response): Response
    {
        $pdo = Database::getConnection();
        $items = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
        return $this->jsonResponse($response, $items);
    }

    public function createProduct(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, tag, image, description, active) VALUES (:name, :category, :price, :tag, :image, :description, :active)");
        $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':category' => $data['category'] ?? 'retail',
            ':price' => (int) ($data['price'] ?? 0),
            ':tag' => $data['tag'] ?? '',
            ':image' => $data['image'] ?? '',
            ':description' => $data['description'] ?? '',
            ':active' => (int) ($data['active'] ?? 1)
        ]);

        return $this->jsonResponse($response, ['success' => true, 'id' => (int) $pdo->lastInsertId(), 'message' => 'Thêm sản phẩm thành công!']);
    }

    public function updateProduct(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, tag = :tag, image = :image, description = :description, active = :active WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'] ?? '',
            ':price' => (int) ($data['price'] ?? 0),
            ':tag' => $data['tag'] ?? '',
            ':image' => $data['image'] ?? '',
            ':description' => $data['description'] ?? '',
            ':active' => (int) ($data['active'] ?? 1)
        ]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Cập nhật sản phẩm thành công!']);
    }

    public function deleteProduct(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Xóa sản phẩm thành công!']);
    }

    // ==========================================
    // CRUD KÊNH BÁN HÀNG (SALES CHANNELS)
    // ==========================================

    public function getChannels(Request $request, Response $response): Response
    {
        $pdo = Database::getConnection();
        $items = $pdo->query("SELECT * FROM sales_channels ORDER BY display_order ASC, id ASC")->fetchAll();
        return $this->jsonResponse($response, $items);
    }

    public function createChannel(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO sales_channels (name, logo, url, badge, active, display_order) VALUES (:name, :logo, :url, :badge, :active, :display_order)");
        $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':logo' => $data['logo'] ?? '',
            ':url' => $data['url'] ?? '',
            ':badge' => $data['badge'] ?? 'Đặt Ngay',
            ':active' => (int) ($data['active'] ?? 1),
            ':display_order' => (int) ($data['display_order'] ?? 0)
        ]);

        return $this->jsonResponse($response, ['success' => true, 'id' => (int) $pdo->lastInsertId(), 'message' => 'Thêm kênh bán hàng thành công!']);
    }

    public function updateChannel(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE sales_channels SET name = :name, logo = :logo, url = :url, badge = :badge, active = :active, display_order = :display_order WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'] ?? '',
            ':logo' => $data['logo'] ?? '',
            ':url' => $data['url'] ?? '',
            ':badge' => $data['badge'] ?? 'Đặt Ngay',
            ':active' => (int) ($data['active'] ?? 1),
            ':display_order' => (int) ($data['display_order'] ?? 0)
        ]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Cập nhật kênh bán hàng thành công!']);
    }

    public function deleteChannel(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM sales_channels WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Xóa kênh bán hàng thành công!']);
    }

    // ==========================================
    // CRUD GÓI SỈ (WHOLESALE PACKAGES)
    // ==========================================

    public function getWholesale(Request $request, Response $response): Response
    {
        $pdo = Database::getConnection();
        $items = $pdo->query("SELECT * FROM wholesale_packages ORDER BY display_order ASC, id ASC")->fetchAll();
        return $this->jsonResponse($response, $items);
    }

    public function createWholesale(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO wholesale_packages (name, bottle_size, min_quantity, price_tier, description, badge, active, display_order) VALUES (:name, :bottle_size, :min_quantity, :price_tier, :description, :badge, :active, :display_order)");
        $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':bottle_size' => $data['bottle_size'] ?? '330ml',
            ':min_quantity' => (int) ($data['min_quantity'] ?? 5),
            ':price_tier' => $data['price_tier'] ?? '',
            ':description' => $data['description'] ?? '',
            ':badge' => $data['badge'] ?? '',
            ':active' => (int) ($data['active'] ?? 1),
            ':display_order' => (int) ($data['display_order'] ?? 0)
        ]);

        return $this->jsonResponse($response, ['success' => true, 'id' => (int) $pdo->lastInsertId(), 'message' => 'Thêm gói bán sỉ thành công!']);
    }

    public function updateWholesale(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE wholesale_packages SET name = :name, bottle_size = :bottle_size, min_quantity = :min_quantity, price_tier = :price_tier, description = :description, badge = :badge, active = :active, display_order = :display_order WHERE id = :id");
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'] ?? '',
            ':bottle_size' => $data['bottle_size'] ?? '330ml',
            ':min_quantity' => (int) ($data['min_quantity'] ?? 5),
            ':price_tier' => $data['price_tier'] ?? '',
            ':description' => $data['description'] ?? '',
            ':badge' => $data['badge'] ?? '',
            ':active' => (int) ($data['active'] ?? 1),
            ':display_order' => (int) ($data['display_order'] ?? 0)
        ]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Cập nhật gói bán sỉ thành công!']);
    }

    public function deleteWholesale(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM wholesale_packages WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Xóa gói bán sỉ thành công!']);
    }

    // ==========================================
    // QUẢN LÝ ĐƠN LIÊN HỆ & ĐĂNG KÝ BÁO GIÁ SỈ
    // ==========================================

    public function getContacts(Request $request, Response $response): Response
    {
        $pdo = Database::getConnection();
        $items = $pdo->query("SELECT * FROM contacts ORDER BY id DESC")->fetchAll();
        return $this->jsonResponse($response, $items);
    }

    public function updateContactStatus(Request $request, Response $response, array $args): Response
    {
        $id = (int) ($args['id'] ?? 0);
        $data = $request->getParsedBody() ?? [];
        $status = $data['status'] ?? 'contacted';

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE contacts SET status = :status WHERE id = :id");
        $stmt->execute([':id' => $id, ':status' => $status]);

        return $this->jsonResponse($response, ['success' => true, 'message' => 'Cập nhật trạng thái đơn thành công!']);
    }

    /**
     * Helper trả về response JSON
     */
    private function jsonResponse(Response $response, mixed $data, int $statusCode = 200): Response
    {
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withStatus($statusCode)->withHeader('Content-Type', 'application/json');
    }
}
