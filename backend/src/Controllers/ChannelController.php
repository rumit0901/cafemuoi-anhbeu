<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý danh sách Kênh Bán Hàng Bán Lẻ (ShopeeFood, GrabFood...)
 */
class ChannelController
{
    /**
     * Lấy danh sách kênh bán hàng đang hoạt động (active = 1)
     * Endpoint: GET /api/channels
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON array các kênh bán hàng
     */
    public function getChannels(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Truy vấn danh sách kênh bán hàng theo thứ tự hiển thị display_order
            $stmt = $pdo->prepare("SELECT id, name, logo, url, badge, active FROM sales_channels WHERE active = 1 ORDER BY display_order ASC, id ASC");
            $stmt->execute();
            $channels = $stmt->fetchAll();

            $formatted = array_map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'name' => $item['name'],
                    'logo' => $item['logo'] ?? '',
                    'url' => $item['url'],
                    'badge' => $item['badge'] ?? 'Đặt Ngay',
                    'active' => (int) $item['active']
                ];
            }, $channels);

            $response->getBody()->write(json_encode($formatted, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải kênh bán hàng: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
