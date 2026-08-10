<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý danh sách Chi nhánh cửa hàng (Stores / Map locations)
 */
class StoreController
{
    /**
     * Lấy danh sách chi nhánh cửa hàng
     * Endpoint: GET /api/stores
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON array các cửa hàng
     */
    public function getStores(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Lấy danh sách stores active = 1
            $stmt = $pdo->prepare("SELECT id, name, address, phone, opening_hours, lat, lng FROM stores WHERE active = 1 ORDER BY id ASC");
            $stmt->execute();
            $stores = $stmt->fetchAll();

            $formatted = array_map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'name' => $item['name'],
                    'address' => $item['address'],
                    'phone' => $item['phone'] ?? '',
                    'opening_hours' => $item['opening_hours'] ?? '',
                    'lat' => (float) $item['lat'],
                    'lng' => (float) $item['lng']
                ];
            }, $stores);

            $response->getBody()->write(json_encode($formatted, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải danh sách chi nhánh: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
