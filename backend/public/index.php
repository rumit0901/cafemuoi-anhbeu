<?php

use App\Controllers\AdminController;
use App\Controllers\BlogController;
use App\Controllers\ChannelController;
use App\Controllers\ContactController;
use App\Controllers\MenuController;
use App\Controllers\ReviewController;
use App\Controllers\StoreController;
use App\Controllers\StoryController;
use App\Controllers\WholesaleController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

// Tự động tải file .env nếu tồn tại (Dành cho Production / Shared Hosting InfinityFree)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim(trim($val), '"\'');
            $_ENV[$key] = $val;
            putenv("{$key}={$val}");
        }
    }
}

require __DIR__ . '/../vendor/autoload.php';

// Khởi tạo ứng dụng Slim Framework 4
$app = AppFactory::create();

// Đăng ký Middlewares toàn cục (Global Middlewares)
$app->add(new CorsMiddleware());
$app->add(new JsonBodyParserMiddleware());
$app->addRoutingMiddleware();

// Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Route gốc / tự động chuyển hướng sang trang CMS Admin
$app->get('/', function ($request, $response) {
    return $response->withHeader('Location', '/admin')->withStatus(302);
});

// ==========================================
// PUBLIC REST API ENDPOINTS (Dành cho Frontend)
// ==========================================

$app->get('/api/menu', [MenuController::class, 'getMenu']);
$app->get('/api/channels', [ChannelController::class, 'getChannels']);
$app->get('/api/wholesale', [WholesaleController::class, 'getWholesale']);
$app->get('/api/stores', [StoreController::class, 'getStores']);
$app->get('/api/reviews', [ReviewController::class, 'getReviews']);
$app->get('/api/blog', [BlogController::class, 'getBlog']);
$app->get('/api/story', [StoryController::class, 'getStory']);
$app->post('/api/contact', [ContactController::class, 'sendContact']);

// Options preflight handler cho tất cả public API
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

// ==========================================
// CMS ADMIN UI & AUTHENTICATION ROUTES
// ==========================================

// Trang đăng nhập Admin HTML
$app->get('/admin/login', function ($request, $response) {
    ob_start();
    include __DIR__ . '/../templates/admin_login.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

// Trang Admin Dashboard HTML
$app->get('/admin', function ($request, $response) {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (empty($_SESSION['admin_user'])) {
        return $response->withHeader('Location', '/admin/login')->withStatus(302);
    }
    ob_start();
    include __DIR__ . '/../templates/admin_dashboard.php';
    $html = ob_get_clean();
    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html');
});

// API Đăng nhập & Đăng xuất Admin
$app->post('/admin/api/login', [AdminController::class, 'login']);
$app->post('/admin/api/logout', [AdminController::class, 'logout']);

// ==========================================
// PROTECTED CMS ADMIN API ROUTES (Bảo vệ bởi AuthMiddleware)
// ==========================================

$app->group('/admin/api', function (RouteCollectorProxy $group) {
    $group->get('/me', [AdminController::class, 'me']);

    // CRUD Sản phẩm Bán lẻ
    $group->get('/products', [AdminController::class, 'getProducts']);
    $group->post('/products', [AdminController::class, 'createProduct']);
    $group->put('/products/{id}', [AdminController::class, 'updateProduct']);
    $group->delete('/products/{id}', [AdminController::class, 'deleteProduct']);

    // CRUD Kênh Bán Hàng
    $group->get('/channels', [AdminController::class, 'getChannels']);
    $group->post('/channels', [AdminController::class, 'createChannel']);
    $group->put('/channels/{id}', [AdminController::class, 'updateChannel']);
    $group->delete('/channels/{id}', [AdminController::class, 'deleteChannel']);

    // CRUD Gói Bán Sỉ
    $group->get('/wholesale', [AdminController::class, 'getWholesale']);
    $group->post('/wholesale', [AdminController::class, 'createWholesale']);
    $group->put('/wholesale/{id}', [AdminController::class, 'updateWholesale']);
    $group->delete('/wholesale/{id}', [AdminController::class, 'deleteWholesale']);

    // Quản lý Đơn Yêu cầu Báo Giá Sỉ & Liên hệ
    $group->get('/contacts', [AdminController::class, 'getContacts']);
    $group->put('/contacts/{id}', [AdminController::class, 'updateContactStatus']);

})->add(new AuthMiddleware());

// Chạy ứng dụng Slim
$app->run();
