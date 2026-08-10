<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý danh sách Sản phẩm & Gói sỉ Cà phê muối đóng chai
 */
class WholesaleController
{
    /**
     * Lấy danh sách gói bán sỉ đang hoạt động
     * Endpoint: GET /api/wholesale
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON array danh sách gói sỉ
     */
    public function getWholesale(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Truy vấn các gói bán sỉ active = 1
            $stmt = $pdo->prepare("SELECT id, name, bottle_size, min_quantity, price_tier, description, badge FROM wholesale_packages WHERE active = 1 ORDER BY display_order ASC, id ASC");
            $stmt->execute();
            $packages = $stmt->fetchAll();

            $formatted = array_map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'name' => $item['name'],
                    'bottle_size' => $item['bottle_size'] ?? '330ml',
                    'min_quantity' => (int) $item['min_quantity'],
                    'price_tier' => $item['price_tier'],
                    'description' => $item['description'] ?? '',
                    'badge' => $item['badge'] ?? ''
                ];
            }, $packages);

            $response->getBody()->write(json_encode($formatted, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải gói bán sỉ: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
