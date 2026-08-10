<?php
// Router script dành riêng cho PHP built-in CLI server (php -S localhost:8000 public/router.php)
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/index.php';
