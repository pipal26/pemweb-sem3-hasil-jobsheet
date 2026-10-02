<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/..' . $uri;

// 1. Jika request adalah aset statis (CSS, JS, gambar, dll), kirim langsung filenya
if (file_exists($file) && !is_dir($file) && !str_ends_with($file, '.php')) {
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $mimes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon'
    ];
    
    if (isset($mimes[$extension])) {
        header("Content-Type: " . $mimes[$extension]);
    } else {
        header("Content-Type: " . (mime_content_type($file) ?: 'text/plain'));
    }
    
    readfile($file);
    exit;
}

// 2. Jalankan Routing PHP
if ($uri === '/' || $uri === '') {
    require __DIR__ . '/../index.php';
    exit;
}

// Jika URL menyertakan ekstensi .php (contoh: /login.php)
if (str_ends_with($uri, '.php') && file_exists($file) && is_file($file)) {
    require $file;
    exit;
}

// Jika URL tanpa ekstensi .php (contoh: /login -> /login.php)
if (file_exists($file . '.php') && is_file($file . '.php')) {
    require $file . '.php';
    exit;
}

// Halaman 404 jika file tidak ditemukan
http_response_code(404);
echo "404 Not Found";