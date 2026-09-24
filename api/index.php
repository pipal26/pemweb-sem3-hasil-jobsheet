<?php
// Tangkap URI request dari browser
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Tentukan path file target berdasarkan root project
$filePath = __DIR__ . '/..' . $requestUri;

// Jika membuka halaman root ("/") arahkan ke index.php utama
if ($requestUri === '/' || $requestUri === '') {
    require __DIR__ . '/../index.php';
    exit;
}

// Jika mengakses file PHP langsung (seperti /alat/list.php)
if (file_exists($filePath) && !is_dir($filePath)) {
    require $filePath;
    exit;
}

// Jika mengakses folder (seperti /alat/) cari list.php atau index.php di dalamnya
if (is_dir($filePath)) {
    if (file_exists($filePath . '/list.php')) {
        require $filePath . '/list.php';
        exit;
    }
    if (file_exists($filePath . '/index.php')) {
        require $filePath . '/index.php';
        exit;
    }
}

// Jika rute tidak ditemukan
http_response_code(404);
echo "404 - Halaman tidak ditemukan";