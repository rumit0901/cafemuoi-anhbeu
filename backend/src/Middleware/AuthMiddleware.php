<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

/**
 * Middleware xác thực quyền truy cập Admin (Authentication Guard)
 * Bảo vệ các endpoint CMS Admin trong route `/admin/api/*`
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Kiểm tra trạng thái đăng nhập từ Session hoặc Authorization Token
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Khởi tạo session nếu chưa được bật
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        // Kiểm tra xem đã lưu thông tin admin trong session chưa
        $isLoggedIn = !empty($_SESSION['admin_user']);

        // Hoặc kiểm tra qua Header Authorization Bearer token đơn giản
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$isLoggedIn && str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
            if ($token === 'anhbeu_admin_secret_token') {
                $isLoggedIn = true;
            }
        }

        // Nếu chưa đăng nhập: Trả về HTTP 401 Unauthorized kèm JSON thông báo lỗi
        if (!$isLoggedIn) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Bạn cần đăng nhập quyền Quản trị viên (Admin) để thực hiện thao tác này.'
            ], JSON_UNESCAPED_UNICODE));

            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }

        // Đã xác thực thành công: Cho phép đi tiếp
        return $handler->handle($request);
    }
}
