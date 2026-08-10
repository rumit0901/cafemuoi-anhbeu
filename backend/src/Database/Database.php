<?php

namespace App\Database;

use PDO;
use PDOException;

/**
 * Quản lý kết nối Cơ sở dữ liệu (PDO Wrapper)
 * Hỗ trợ cả MySQL (production shared hosting) và SQLite (kiểm thử / local dev).
 */
class Database
{
    /**
     * Instance PDO duy nhất trong vòng đời ứng dụng (Singleton pattern)
     */
    private static ?PDO $instance = null;

    /**
     * Lấy đối tượng kết nối PDO tới Database.
     * Tự động khởi tạo kết nối nếu chưa có và tự chạy schema DDL nếu dùng SQLite.
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            // Lấy loại driver DB từ biến môi trường (mặc định 'sqlite' cho dev/test)
            $driver = $_ENV['DB_DRIVER'] ?? getenv('DB_DRIVER') ?: 'sqlite';

            try {
                if ($driver === 'mysql') {
                    // Cấu hình kết nối MySQL cho Production Shared Hosting
                    $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
                    $dbname = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'cafemuoi_anhbeu';
                    $user = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
                    $pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
                    $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';

                    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                    self::$instance = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                } else {
                    // Cấu hình kết nối SQLite (Memory hoặc File) cho môi trường kiểm thử & dev
                    $dbFile = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: __DIR__ . '/../../database.sqlite';
                    $dsn = "sqlite:" . $dbFile;

                    self::$instance = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);

                    // Tự động tạo bảng & seed dữ liệu nếu bảng 'products' chưa tồn tại
                    self::initSqliteTables(self::$instance);
                }
            } catch (PDOException $e) {
                // Đẩy ngoại lệ kết nối DB để controller/middleware bắt
                throw new PDOException("Lỗi kết nối CSDL: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * Đặt lại instance PDO (dùng cho unit test khi cần reset database)
     *
     * @param PDO|null $pdo
     */
    public static function setConnection(?PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    /**
     * Tự động khởi tạo bảng và seed data cho SQLite khi chạy local hoặc test
     *
     * @param PDO $pdo
     */
    private static function initSqliteTables(PDO $pdo): void
    {
        // Kiểm tra xem bảng products đã có chưa
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'")->fetch();
        if (!$result) {
            // Đọc file schema.sql và thực thi các câu lệnh DDL
            $schemaFile = __DIR__ . '/../../schema.sql';
            if (file_exists($schemaFile)) {
                $sql = file_get_contents($schemaFile);
                
                // Thay thế syntax MySQL đặc thù cho tương thích SQLite
                $sql = str_replace('AUTO_INCREMENT', 'AUTOINCREMENT', $sql);
                $sql = str_replace('ON DUPLICATE KEY UPDATE username=username', 'ON CONFLICT DO NOTHING', $sql);

                // Thực thi từng câu lệnh SQL
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($statements as $stmt) {
                    if (!empty($stmt)) {
                        try {
                            $pdo->exec($stmt);
                        } catch (PDOException $ex) {
                            // Bỏ qua lỗi câu lệnh trùng lặp
                        }
                    }
                }
            }
        }
    }
}
