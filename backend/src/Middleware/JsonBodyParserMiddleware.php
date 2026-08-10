<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Middleware tự động parse dữ liệu JSON từ body request
 * Chuyển chuỗi JSON gửi từ client/frontend thành array trong $request->getParsedBody()
 */
class JsonBodyParserMiddleware implements MiddlewareInterface
{
    /**
     * Parse JSON body nếu header Content-Type chứa 'application/json'
     *
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');

        // Kiểm tra xem request gửi lên có dạng JSON hay không
        if (str_contains($contentType, 'application/json')) {
            $contents = (string) $request->getBody();
            if (!empty($contents)) {
                $parsed = json_decode($contents, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $request = $request->withParsedBody($parsed);
                }
            }
        }

        return $handler->handle($request);
    }
}
