<?php
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO alat (nama_alat, kategori, harga_sewa, stok) VALUES (:n, :k, :h, :s)");
    $stmt->execute([
        ':n' => trim($_POST['nama_alat']),
        ':k' => trim($_POST['kategori']),
        ':h' => (float)$_POST['harga_sewa'],
        ':s' => (int)$_POST['stok']
    ]);
    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Alat</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Tambah Alat Baru</h2>
    <form method="POST">
        <div class="form-group"><label>Nama Alat</label><input type="text" name="nama_alat" required></div>
        <div class="form-group"><label>Kategori</label><input type="text" name="kategori" required></div>
        <div class="form-group"><label>Harga / Hari</label><input type="number" name="harga_sewa" required></div>
        <div class="form-group"><label>Stok</label><input type="number" name="stok" required></div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="list.php" class="btn">Batal</a>
    </form>
</div>
</body>
</html>