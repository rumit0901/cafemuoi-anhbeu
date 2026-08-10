<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý tin tức, bài viết & chính sách sỉ
 */
class BlogController
{
    /**
     * Lấy danh sách bài viết blog / tin tức
     * Endpoint: GET /api/blog
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON array các bài viết
     */
    public function getBlog(Request $request, Response $response): Response
    {
        try {
            $pdo = Database::getConnection();

            // Lấy danh sách bài viết active = 1
            $stmt = $pdo->prepare("SELECT id, title, slug, excerpt, content, image, author, published_at FROM blog_posts WHERE active = 1 ORDER BY id DESC");
            $stmt->execute();
            $posts = $stmt->fetchAll();

            $formatted = array_map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['excerpt'] ?? '',
                    'content' => $item['content'] ?? '',
                    'image' => $item['image'] ?? '',
                    'author' => $item['author'] ?? 'Anh Bếu',
                    'published_at' => $item['published_at'] ?? ''
                ];
            }, $posts);

            $response->getBody()->write(json_encode($formatted, JSON_UNESCAPED_UNICODE));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Lỗi hệ thống khi tải bài viết: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
