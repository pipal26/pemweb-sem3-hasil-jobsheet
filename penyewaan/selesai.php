<?php
require_once '../config/database.php';
$penyewa = $pdo->query("SELECT id, nama FROM penyewa ORDER BY nama ASC")->fetchAll();
$alat    = $pdo->query("SELECT id, nama_alat, harga_sewa, stok FROM alat WHERE stok > 0 ORDER BY nama_alat ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penyewa  = (int)$_POST['id_penyewa'];
    $id_alat     = (int)$_POST['id_alat'];
    $durasi_hari = (int)$_POST['durasi_hari'];

    try {
        $pdo->beginTransaction();
        $stmtAlat = $pdo->prepare("SELECT harga_sewa, stok FROM alat WHERE id = :id FOR UPDATE");
        $stmtAlat->execute([':id' => $id_alat]);
        $dataAlat = $stmtAlat->fetch();

        if (!$dataAlat || $dataAlat['stok'] <= 0) {
            throw new Exception("Stok alat habis.");
        }

        $total_biaya = $dataAlat['harga_sewa'] * $durasi_hari;

        $stmtInsert = $pdo->prepare("INSERT INTO penyewaan (id_penyewa, id_alat, durasi_hari, total_biaya, status) VALUES (:p, :a, :d, :t, 'SEWA')");
        $stmtInsert->execute([':p' => $id_penyewa, ':a' => $id_alat, ':d' => $durasi_hari, ':t' => $total_biaya]);

        $pdo->prepare("UPDATE alat SET stok = stok - 1 WHERE id = :id")->execute([':id' => $id_alat]);
        $pdo->commit();
        header("Location: list.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sewa Alat</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Form Transaksi Sewa</h2>
    <?php if (isset($error)): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Penyewa</label>
            <select name="id_penyewa" required>
                <option value="">-- Pilih Penyewa --</option>
                <?php foreach ($penyewa as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Alat</label>
            <select name="id_alat" required>
                <option value="">-- Pilih Alat --</option>
                <?php foreach ($alat as $a): ?>
                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama_alat']) ?> (Rp <?= number_format($a['harga_sewa'], 0, ',', '.') ?> | Sisa: <?= $a['stok'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group"><label>Durasi (Hari)</label><input type="number" name="durasi_hari" min="1" value="1" required></div>
        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        <a href="list.php" class="btn">Batal</a>
    </form>
</div>
</body>
</html>