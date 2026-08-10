<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý Đánh giá khách hàng & đại lý đối tác
 */
class ReviewController
{
    /**
     * Lấy danh sách đánh giá khách hàng
     * Endpoint: GET /api/reviews
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON array các đánh giá
     */
    public function getReviews(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Lấy danh sách reviews active = 1
            $stmt = $pdo->prepare("SELECT id, customer_name, rating, comment, type FROM reviews WHERE active = 1 ORDER BY id DESC");
            $stmt->execute();
            $reviews = $stmt->fetchAll();

            $formatted = array_map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'customer' => $item['customer_name'],
                    'rating' => (int) $item['rating'],
                    'comment' => $item['comment'],
                    'type' => $item['type'] ?? 'retail'
                ];
            }, $reviews);

            $response->getBody()->write(json_encode($formatted, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải đánh giá: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
