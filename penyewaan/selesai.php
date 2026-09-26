<?php
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id_alat, status FROM penyewaan WHERE id = :id FOR UPDATE");
        $stmt->execute([':id' => $id]);
        $trx = $stmt->fetch();

        if ($trx && $trx['status'] === 'SEWA') {
            // Ubah status jadi KEMBALI
            $update = $pdo->prepare("UPDATE penyewaan SET status = 'KEMBALI' WHERE id = :id");
            $update->execute([':id' => $id]);

            // Kembalikan 1 stok alat
            $restore = $pdo->prepare("UPDATE alat SET stok = stok + 1 WHERE id = :id");
            $restore->execute([':id' => $trx['id_alat']]);

            $pdo->commit();
        }
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

header("Location: list.php");
exit;