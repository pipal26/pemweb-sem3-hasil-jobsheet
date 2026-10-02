<?php
// Forward static assets (CSS, JS, gambar, dll)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/..' . $uri;

if (file_exists($file) && !is_dir($file)) {
    $mime = mime_content_type($file);
    if (str_ends_with($file, '.css')) {
        $mime = 'text/css';
    } elseif (str_ends_with($file, '.js')) {
        $mime = 'application/javascript';
    }
    header("Content-Type: $mime");
    readfile($file);
    exit;
}

// Router untuk file PHP
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/../index.php';
    exit;
}

// Jika request menyertakan .php atau file ada secara spesifik
if (file_exists($file) && is_file($file)) {
    require $file;
    exit;
}

// Jika request tanpa ekstensi .php (contoh: /login -> /login.php)
if (file_exists($file . '.php') && is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

// Jika halaman tidak ditemukan
http_response_code(404);
echo "404 Not Found";