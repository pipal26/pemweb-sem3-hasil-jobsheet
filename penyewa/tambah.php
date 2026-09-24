<?php
require_once '../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO penyewa (nama, no_telp, alamat) VALUES (:n, :t, :a)");
    $stmt->execute([
        ':n' => trim($_POST['nama']),
        ':t' => trim($_POST['no_telp']),
        ':a' => trim($_POST['alamat'])
    ]);
    header("Location: list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penyewa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Tambah Penyewa Baru</h2>
    <form method="POST">
        <div class="form-group"><label>Nama</label><input type="text" name="nama" required></div>
        <div class="form-group"><label>No. Telp</label><input type="text" name="no_telp" required></div>
        <div class="form-group"><label>Alamat</label><textarea name="alamat"></textarea></div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="list.php" class="btn">Batal</a>
    </form>
</div>
</body>
</html>