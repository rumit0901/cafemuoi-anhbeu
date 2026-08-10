<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý dữ liệu Sản phẩm Bán lẻ (Menu API)
 */
class MenuController
{
    /**
     * Lấy danh sách sản phẩm bán lẻ đang hoạt động (active = 1)
     * Endpoint: GET /api/menu
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON array danh sách sản phẩm
     */
    public function getMenu(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Truy vấn các sản phẩm thuộc phân loại 'retail' đang active
            $stmt = $pdo->prepare("SELECT id, name, price, tag, image, description FROM products WHERE active = 1 ORDER BY id ASC");
            $stmt->execute();
            $items = $stmt->fetchAll();

            // Ép kiểu dữ liệu đúng định dạng JSON
            $formattedItems = array_map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'name' => $item['name'],
                    'price' => (int) $item['price'],
                    'tag' => $item['tag'] ?? '',
                    'image' => $item['image'] ?? '',
                    'description' => $item['description'] ?? ''
                ];
            }, $items);

            $response->getBody()->write(json_encode($formattedItems, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải thực đơn bán lẻ: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
