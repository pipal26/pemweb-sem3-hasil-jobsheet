<?php
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT id_alat, status FROM penyewaan WHERE id = :id FOR UPDATE");
        $stmt->execute([':id' => $id]);
        $trx = $stmt->fetch();

        if ($trx && $trx['status'] === 'SEWA') {
            // Ubah status jadi KEMBALI
            $update = $conn->prepare("UPDATE penyewaan SET status = 'KEMBALI' WHERE id = :id");
            $update->execute([':id' => $id]);

            // Kembalikan 1 stok alat
            $restore = $conn->prepare("UPDATE alat SET stok = stok + 1 WHERE id = :id");
            $restore->execute([':id' => $trx['id_alat']]);

            $conn->commit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
    }
}

header("Location: list.php");
exit;