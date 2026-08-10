<?php

namespace App\Tests;

use App\Controllers\AdminController;
use App\Controllers\BlogController;
use App\Controllers\ChannelController;
use App\Controllers\ContactController;
use App\Controllers\MenuController;
use App\Controllers\ReviewController;
use App\Controllers\StoreController;
use App\Controllers\StoryController;
use App\Controllers\WholesaleController;
use App\Database\Database;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Routing\RouteCollectorProxy;

/**
 * Suite kiểm thử TDD cho toàn bộ REST API & CMS Admin Backend
 */
class ApiTest extends TestCase
{
    private $app;

    /**
     * Khởi tạo môi trường test trước mỗi test case
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Thiết lập môi trường test dùng SQLite memory
        putenv('DB_DRIVER=sqlite');
        putenv('DB_DATABASE=:memory:');
        $_ENV['DB_DRIVER'] = 'sqlite';
        $_ENV['DB_DATABASE'] = ':memory:';

        // Reset lại instance kết nối Database PDO
        Database::setConnection(null);

        // Khởi tạo App Slim 4
        $app = AppFactory::create();
        $app->add(new CorsMiddleware());
        $app->add(new JsonBodyParserMiddleware());
        $app->addRoutingMiddleware();

        // Routes Công khai
        $app->get('/api/menu', [MenuController::class, 'getMenu']);
        $app->get('/api/channels', [ChannelController::class, 'getChannels']);
        $app->get('/api/wholesale', [WholesaleController::class, 'getWholesale']);
        $app->get('/api/stores', [StoreController::class, 'getStores']);
        $app->get('/api/reviews', [ReviewController::class, 'getReviews']);
        $app->get('/api/blog', [BlogController::class, 'getBlog']);
        $app->get('/api/story', [StoryController::class, 'getStory']);
        $app->post('/api/contact', [ContactController::class, 'sendContact']);

        // Routes Admin
        $app->post('/admin/api/login', [AdminController::class, 'login']);
        $app->group('/admin/api', function (RouteCollectorProxy $group) {
            $group->get('/products', [AdminController::class, 'getProducts']);
            $group->post('/products', [AdminController::class, 'createProduct']);
            $group->get('/wholesale', [AdminController::class, 'getWholesale']);
            $group->post('/wholesale', [AdminController::class, 'createWholesale']);
            $group->put('/wholesale/{id}', [AdminController::class, 'updateWholesale']);
            $group->delete('/wholesale/{id}', [AdminController::class, 'deleteWholesale']);
        })->add(new AuthMiddleware());

        $this->app = $app;
    }

    /**
     * Test tất cả các Public GET APIs trả về status 200 và định dạng JSON chuẩn
     */
    public function testPublicApiEndpoints(): void
    {
        $endpoints = ['/api/menu', '/api/channels', '/api/wholesale', '/api/stores', '/api/reviews', '/api/blog', '/api/story'];

        foreach ($endpoints as $path) {
            $request = (new RequestFactory())->createRequest('GET', $path);
            $response = $this->app->handle($request);

            $this->assertEquals(200, $response->getStatusCode(), "Endpoint {$path} phải trả về HTTP 200 OK");
            $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));

            $body = json_decode((string) $response->getBody(), true);
            $this->assertNotNull($body, "Response của {$path} phải là JSON hợp lệ");
        }
    }

    /**
     * Test API gửi form liên hệ / báo giá sỉ với validation
     */
    public function testContactFormSubmissionValidationAndSuccess(): void
    {
        // 1. Kiểm thử khi thiếu Tên và SĐT -> Phải fail HTTP 400
        $requestFail = (new RequestFactory())
            ->createRequest('POST', '/api/contact')
            ->withHeader('Content-Type', 'application/json');

        $stream = (new StreamFactory())->createStream(json_encode([
            'name' => '',
            'phone' => ''
        ]));
        $requestFail = $requestFail->withBody($stream);
        $responseFail = $this->app->handle($requestFail);

        $this->assertEquals(400, $responseFail->getStatusCode());
        $bodyFail = json_decode((string) $responseFail->getBody(), true);
        $this->assertFalse($bodyFail['success']);

        // 2. Kiểm thử khi nhập đúng thông tin -> Phải pass HTTP 200
        $requestPass = (new RequestFactory())
            ->createRequest('POST', '/api/contact')
            ->withHeader('Content-Type', 'application/json');

        $streamPass = (new StreamFactory())->createStream(json_encode([
            'name' => 'Nguyễn Văn A',
            'phone' => '0987654321',
            'email' => 'nguyenvana@gmail.com',
            'inquiry_type' => 'wholesale',
            'expected_quantity' => 100,
            'message' => 'Tôi muốn nhập sỉ 100 chai 330ml'
        ]));
        $requestPass = $requestPass->withBody($streamPass);
        $responsePass = $this->app->handle($requestPass);

        $this->assertEquals(200, $responsePass->getStatusCode());
        $bodyPass = json_decode((string) $responsePass->getBody(), true);
        $this->assertTrue($bodyPass['success']);
    }

    /**
     * Test đăng nhập Admin & Bảo vệ các Admin Route bằng AuthMiddleware
     */
    public function testAdminAuthenticationAndProtectedRoutes(): void
    {
        // 1. Truy cập route Admin khi chưa đăng nhập -> Phải trả về HTTP 401
        $reqUnauth = (new RequestFactory())->createRequest('GET', '/admin/api/products');
        $resUnauth = $this->app->handle($reqUnauth);
        $this->assertEquals(401, $resUnauth->getStatusCode());

        // 2. Thực hiện Đăng nhập Admin với admin / admin -> HTTP 200
        $reqLogin = (new RequestFactory())
            ->createRequest('POST', '/admin/api/login')
            ->withHeader('Content-Type', 'application/json');
        
        $streamLogin = (new StreamFactory())->createStream(json_encode([
            'username' => 'admin',
            'password' => 'admin'
        ]));
        $reqLogin = $reqLogin->withBody($streamLogin);
        $resLogin = $this->app->handle($reqLogin);

        $this->assertEquals(200, $resLogin->getStatusCode());
        $bodyLogin = json_decode((string) $resLogin->getBody(), true);
        $this->assertTrue($bodyLogin['success']);

        // 3. Truy cập route Admin kèm Header Authorization Bearer Token -> HTTP 200
        $reqAuth = (new RequestFactory())
            ->createRequest('GET', '/admin/api/products')
            ->withHeader('Authorization', 'Bearer ' . $bodyLogin['token']);
        $resAuth = $this->app->handle($reqAuth);

        $this->assertEquals(200, $resAuth->getStatusCode());
    }

    /**
     * Test quy trình CRUD tạo, sửa, lấy danh sách và xóa Gói Sỉ Đóng Chai đa dung tích (330ml, 500ml)
     */
    public function testWholesalePackagesMultiCapacityCrud(): void
    {
        // 1. Đăng nhập lấy Token Admin
        $reqLogin = (new RequestFactory())
            ->createRequest('POST', '/admin/api/login')
            ->withHeader('Content-Type', 'application/json');
        $streamLogin = (new StreamFactory())->createStream(json_encode(['username' => 'admin', 'password' => 'admin']));
        $resLogin = $this->app->handle($reqLogin->withBody($streamLogin));
        $token = json_decode((string) $resLogin->getBody(), true)['token'];

        // 2. Tạo mới gói sỉ dung tích 500ml qua POST /admin/api/wholesale
        $reqCreate500 = (new RequestFactory())
            ->createRequest('POST', '/admin/api/wholesale')
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Content-Type', 'application/json');

        $streamCreate500 = (new StreamFactory())->createStream(json_encode([
            'name' => 'Gói Sỉ Chai Lớn 500ml Hợp Tác',
            'bottle_size' => '500ml',
            'min_quantity' => 20,
            'price_tier' => 'Chiết khấu 22%',
            'badge' => 'Chai 500ml',
            'description' => 'Gói sỉ chai 500ml tiện lợi',
            'active' => 1
        ]));
        $resCreate500 = $this->app->handle($reqCreate500->withBody($streamCreate500));
        $this->assertEquals(200, $resCreate500->getStatusCode());

        $createdData = json_decode((string) $resCreate500->getBody(), true);
        $this->assertTrue($createdData['success']);
        $newId = $createdData['id'];

        // 3. Kiểm tra API công khai GET /api/wholesale có hiển thị đúng dung tích 500ml
        $reqGetPublic = (new RequestFactory())->createRequest('GET', '/api/wholesale');
        $resGetPublic = $this->app->handle($reqGetPublic);
        $this->assertEquals(200, $resGetPublic->getStatusCode());
        $wholesaleList = json_decode((string) $resGetPublic->getBody(), true);

        $found500 = false;
        foreach ($wholesaleList as $item) {
            if ($item['id'] === $newId) {
                $this->assertEquals('500ml', $item['bottle_size']);
                $this->assertEquals('Gói Sỉ Chai Lớn 500ml Hợp Tác', $item['name']);
                $found500 = true;
                break;
            }
        }
        $this->assertTrue($found500, "Gói sỉ 500ml mới tạo phải có mặt trong API public");

        // 4. Chỉnh sửa (PUT) gói sỉ sang dung tích 1000ml (1L)
        $reqUpdate = (new RequestFactory())
            ->createRequest('PUT', '/admin/api/wholesale/' . $newId)
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Content-Type', 'application/json');

        $streamUpdate = (new StreamFactory())->createStream(json_encode([
            'name' => 'Gói Sỉ Chai 1L Đại Lý',
            'bottle_size' => '1000ml',
            'min_quantity' => 10,
            'price_tier' => 'Chiết khấu 30%',
            'badge' => 'Chai 1L',
            'description' => 'Dung tích siêu tiết kiệm',
            'active' => 1
        ]));
        $resUpdate = $this->app->handle($reqUpdate->withBody($streamUpdate));
        $this->assertEquals(200, $resUpdate->getStatusCode());

        // 5. Xóa (DELETE) gói sỉ vừa tạo
        $reqDelete = (new RequestFactory())
            ->createRequest('DELETE', '/admin/api/wholesale/' . $newId)
            ->withHeader('Authorization', 'Bearer ' . $token);
        $resDelete = $this->app->handle($reqDelete);
        $this->assertEquals(200, $resDelete->getStatusCode());
    }
}
