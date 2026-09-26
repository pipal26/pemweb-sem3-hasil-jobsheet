<?php
// === TAMBAHAN FITUR SESI 3 JAM (10800 DETIK) ===
ini_set('session.gc_maxlifetime', 10800);
session_set_cookie_params(10800);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 10800)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();
// ===============================================
$dbUrl = getenv('DATABASE_URL') ?: 'postgresql://neondb_owner:npg_4QGtPJ1jKaWs@ep-ancient-frost-b3adensu-pooler.c-4.ap-southeast-1.aws.neon.tech/neondb?sslmode=require&channel_binding=require';

if ($dbUrl) {
    // Parsing string koneksi dari Neon
    $dbParts = parse_url($dbUrl);
    
    $host     = $dbParts['host'];
    $port     = $dbParts['port'] ?? '5432';
    $user     = $dbParts['user'];
    $password = $dbParts['pass'] ?? '';
    $dbname   = ltrim($dbParts['path'], '/');
    
    // Ambil parameter query jika ada (seperti sslmode dan channel_binding)
    parse_str($dbParts['query'] ?? '', $queryParams);
    $sslmode        = $queryParams['sslmode'] ?? 'require';
    $channelBinding = $queryParams['channel_binding'] ?? 'require';
} else {
    // Pengaturan fallback lokal
    $host           = getenv('PGHOST') ?: 'localhost';
    $port           = getenv('PGPORT') ?: '5432';
    $dbname         = getenv('PGDATABASE') ?: 'sewa_db';
    $user           = getenv('PGUSER') ?: 'postgres';
    $password       = getenv('PGPASSWORD') ?: 'postgres';
    $sslmode        = getenv('PGSSLMODE') ?: 'prefer';
    $channelBinding = null;
}

// Susun DSN dengan sslmode dan channel_binding
$dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";
if ($channelBinding) {
    $dsn .= ";channel_binding={$channelBinding}";
}

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);
} catch (PDOException $e) {
    die("Koneksi basis data gagal: " . $e->getMessage());
}