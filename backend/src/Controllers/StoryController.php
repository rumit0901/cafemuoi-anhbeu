<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý nội dung trang Câu chuyện thương hiệu
 */
class StoryController
{
    /**
     * Lấy nội dung câu chuyện thương hiệu
     * Endpoint: GET /api/story
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON object câu chuyện
     */
    public function getStory(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Lấy dòng mới nhất trong bảng stories
            $stmt = $pdo->prepare("SELECT title, subtitle, hero_image, paragraphs FROM stories ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            $story = $stmt->fetch();

            if (!$story) {
                // Return default fallback if empty
                $storyData = [
                    'title' => 'Hành Trình Đưa Cà Phê Muối Chuẩn Vị Đến Mọi Nhà',
                    'subtitle' => 'Từ công thức gia truyền đậm đà đến mô hình phân phối sỉ & lẻ hàng đầu',
                    'image' => 'assets/img/story-hero.jpg',
                    'paragraphs' => [
                        'Thương hiệu Cà Phê Muối Anh Bếu khởi nguồn từ niềm đam mê sâu sắc với hạt cà phê Việt Nam...',
                        'Bằng việc ứng dụng quy trình đóng chai chuẩn an toàn thực phẩm, chúng tôi tự hào cung cấp cà phê muối tươi nguyên chất...'
                    ]
                ];
            } else {
                $paragraphs = json_decode($story['paragraphs'], true);
                if (!is_array($paragraphs)) {
                    $paragraphs = [$story['paragraphs']];
                }

                $storyData = [
                    'title' => $story['title'],
                    'subtitle' => $story['subtitle'] ?? '',
                    'image' => $story['hero_image'] ?? '',
                    'paragraphs' => $paragraphs
                ];
            }

            $response->getBody()->write(json_encode($storyData, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải câu chuyện thương hiệu: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
