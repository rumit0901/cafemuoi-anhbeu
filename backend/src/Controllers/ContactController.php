<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Controller xử lý tiếp nhận Yêu cầu Báo giá Sỉ & Form Liên hệ
 */
class ContactController
{
    /**
     * Gửi yêu cầu liên hệ hoặc đăng ký đại lý sỉ
     * Endpoint: POST /api/contact
     *
     * @param Request $request
     * @param Response $response
     * @return Response JSON kết quả thành công hoặc lỗi
     */
    public function sendContact(Request $request, Response $response): Response
    {
        try {
            // Lấy dữ liệu gửi lên từ body
            $data = $request->getParsedBody() ?? [];

            // Sanitize & Validate dữ liệu đầu vào
            $name = trim($data['name'] ?? '');
            $phone = trim($data['phone'] ?? '');
            $email = trim($data['email'] ?? '');
            $inquiryType = trim($data['inquiry_type'] ?? 'wholesale');
            
            $expectedQuantity = 0;
            if (isset($data['expected_quantity']) && is_numeric($data['expected_quantity'])) {
                $expectedQuantity = (int) $data['expected_quantity'];
            } elseif (isset($data['quantity']) && is_numeric($data['quantity'])) {
                $expectedQuantity = (int) $data['quantity'];
            }

            $storeName = trim($data['store_name'] ?? '');
            $message = trim($data['message'] ?? '');
            if (!empty($storeName)) {
                $message = "[Cửa hàng/Đại lý: {$storeName}] " . $message;
            }

            // Bắt buộc nhập Họ tên và Số điện thoại
            if (empty($name) || strlen($name) < 2) {
                return $this->jsonError($response, 'Vui lòng nhập họ và tên hợp lệ.', 400);
            }

            if (empty($phone) || strlen($phone) < 8) {
                return $this->jsonError($response, 'Vui lòng nhập số điện thoại liên hệ hợp lệ (tối thiểu 8 chữ số).', 400);
            }

            // Kiểm tra format email nếu người dùng có nhập
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->jsonError($response, 'Địa chỉ email không đúng định dạng.', 400);
            }

            // Chuẩn hóa loại yêu cầu (retail hoặc wholesale)
            if (!in_array($inquiryType, ['retail', 'wholesale'], true)) {
                $inquiryType = 'wholesale';
            }

            // Lưu yêu cầu vào bảng contacts trong CSDL
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO contacts (name, phone, email, inquiry_type, expected_quantity, message, status, created_at)
                VALUES (:name, :phone, :email, :inquiry_type, :expected_quantity, :message, 'new', CURRENT_TIMESTAMP)
            ");

            $stmt->execute([
                ':name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                ':phone' => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
                ':email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
                ':inquiry_type' => $inquiryType,
                ':expected_quantity' => $expectedQuantity,
                ':message' => htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            ]);

            // Trả về JSON thông báo thành công cho Frontend
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Cảm ơn bạn đã gửi yêu cầu! Đội ngũ Cà phê muối Anh Bếu sẽ liên hệ tư vấn và gửi báo giá trong thời gian sớm nhất.'
            ], JSON_UNESCAPED_UNICODE));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Throwable $e) {
            return $this->jsonError($response, 'Lỗi server khi tiếp nhận yêu cầu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper trả về response JSON báo lỗi
     *
     * @param Response $response
     * @param string $message
     * @param int $statusCode
     * @return Response
     */
    private function jsonError(Response $response, string $message, int $statusCode = 400): Response
    {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $message
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}
