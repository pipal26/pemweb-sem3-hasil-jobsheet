<?php
// Aktifkan laporan error penuh
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';

// Ambil list penyewa & alat
$penyewa = $pdo->query("SELECT id, nama FROM penyewa ORDER BY nama ASC")->fetchAll();
$alat    = $pdo->query("SELECT id, nama_alat, harga_sewa, stok FROM alat ORDER BY nama_alat ASC")->fetchAll();

$pesan_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_penyewa  = (int)($_POST['id_penyewa'] ?? 0);
    $id_alat     = (int)($_POST['id_alat'] ?? 0);
    $durasi_hari = (int)($_POST['durasi_hari'] ?? 1);

    if ($id_penyewa > 0 && $id_alat > 0 && $durasi_hari > 0) {
        try {
            // 1. Ambil harga sewa dan stok alat secara sederhana (tanpa FOR UPDATE agar tidak di-lock pooler Neon)
            $stmtAlat = $pdo->prepare("SELECT harga_sewa, stok FROM alat WHERE id = ?");
            $stmtAlat->execute([$id_alat]);
            $dataAlat = $stmtAlat->fetch();

            if (!$dataAlat) {
                throw new Exception("Data alat dengan ID $id_alat tidak ditemukan.");
            }

            $total_biaya = (float)$dataAlat['harga_sewa'] * $durasi_hari;
            $tgl_sewa    = date('Y-m-d');
            $tgl_kembali = date('Y-m-d', strtotime("+$durasi_hari days"));

            // 2. Insert ke tabel penyewaan
            // Gunakan RETURNING id untuk memastikan PostgreSQL benar-benar mengembalikan ID baris baru
            $sqlInsert = "INSERT INTO penyewaan (id_penyewa, id_alat, tgl_sewa, tgl_kembali, durasi_hari, total_biaya, status) 
                          VALUES (?, ?, ?, ?, ?, ?, 'SEWA') RETURNING id";
            
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                $id_penyewa,
                $id_alat,
                $tgl_sewa,
                $tgl_kembali,
                $durasi_hari,
                $total_biaya
            ]);
            
            $newId = $stmtInsert->fetchColumn();

            // 3. Update stok alat jika ada stok
            if ($dataAlat['stok'] > 0) {
                $stmtUpdate = $pdo->prepare("UPDATE alat SET stok = stok - 1 WHERE id = ?");
                $stmtUpdate->execute([$id_alat]);
            }

            // Jika sukses, lempar langsung ke list.php bersama notifikasi
            header("Location: list.php?sukses=" . $newId);
            exit;

        } catch (PDOException $e) {
            // Tampilkan error database langsung dan hentikan redirect
            $pesan_error = "Error Database: " . $e->getMessage() . " (Kode: " . $e->getCode() . ")";
        } catch (Exception $e) {
            $pesan_error = "Error: " . $e->getMessage();
        }
    } else {
        $pesan_error = "Semua form wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi Sewa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col font-sans">
    <header class="bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="../index.php" class="text-xl font-bold tracking-wide text-white hover:text-emerald-400">🏕️ SewaAlat Camping</a>
            <a href="list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800">&larr; Kembali ke Daftar</a>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-6 py-12 flex-1 w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">Form Transaksi Sewa</h1>

            <?php if (!empty($pesan_error)): ?>
                <div class="mb-5 p-4 rounded-xl bg-red-100 border border-red-400 text-red-900 text-sm font-semibold">
                    ⚠️ TERJADI KESALAHAN:<br>
                    <?= htmlspecialchars($pesan_error) ?>
                </div>
            <?php endif; ?>

            <form action="sewa.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Penyewa</label>
                    <select name="id_penyewa" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 outline-none text-sm">
                        <option value="">-- Pilih Penyewa --</option>
                        <?php foreach ($penyewa as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Alat</label>
                    <select name="id_alat" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 outline-none text-sm">
                        <option value="">-- Pilih Alat --</option>
                        <?php foreach ($alat as $a): ?>
                            <option value="<?= $a['id'] ?>">
                                <?= htmlspecialchars($a['nama_alat']) ?> (Rp <?= number_format($a['harga_sewa'], 0, ',', '.') ?> | Stok: <?= $a['stok'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Durasi Sewa (Hari)</label>
                    <input type="number" name="durasi_hari" min="1" value="1" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 outline-none text-sm">
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow transition">Simpan Transaksi</button>
                    <a href="list.php" class="py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl text-center transition">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>