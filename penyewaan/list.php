<?php
require_once __DIR__ . '/../config/database.php';

// 1. Cek semua tabel yang ada di database PostgreSQL
$tabelDB = $pdo->query("
    SELECT table_name 
    FROM information_schema.tables 
    WHERE table_schema = 'public'
")->fetchAll(PDO::FETCH_COLUMN);

// 2. Ambil data langsung dari tabel penyewaan
$errorQuery = null;
$rawTransaksi = [];
$kolomTabel = [];

try {
    $stmt = $pdo->query("SELECT * FROM penyewaan");
    $rawTransaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil nama-nama kolom tabel penyewaan yang ada di DB
    $stmtCol = $pdo->query("
        SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_name = 'penyewaan'
    ");
    $kolomTabel = $stmtCol->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $errorQuery = $e->getMessage();
}

// 3. Ambil data alat & penyewa untuk join manual
$penyewaMap = [];
try {
    $pRows = $pdo->query("SELECT * FROM penyewa")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($pRows as $p) {
        $pId = $p['id'] ?? reset($p);
        $penyewaMap[$pId] = $p['nama'] ?? 'Tanpa Nama';
    }
} catch (Exception $e) {}

$alatMap = [];
try {
    $aRows = $pdo->query("SELECT * FROM alat")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($aRows as $a) {
        $aId = $a['id'] ?? reset($a);
        $alatMap[$aId] = $a['nama_alat'] ?? 'Tanpa Nama';
    }
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Rental - SewaAlat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col font-sans">

    <header class="bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <a href="../index.php" class="text-xl font-bold text-white hover:text-emerald-400">🏕️ SewaAlat Camping</a>
            <nav class="flex space-x-3 mt-2 sm:mt-0">
                <a href="../alat/list.php" class="px-4 py-2 text-sm rounded-lg text-slate-300 hover:bg-slate-800">Data Alat</a>
                <a href="../penyewa/list.php" class="px-4 py-2 text-sm rounded-lg text-slate-300 hover:bg-slate-800">Data Penyewa</a>
                <a href="list.php" class="px-4 py-2 text-sm rounded-lg text-white bg-slate-800">Transaksi Rental</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full">

        <!-- KOTAK DIAGNOSTIK DATABASE (Membongkar status asli database) -->
        <div class="mb-6 p-5 bg-amber-50 border border-amber-300 rounded-2xl text-xs space-y-2">
            <div class="font-bold text-amber-900 text-sm">🔍 Status Database PostgreSQL:</div>
            <div><strong>Daftar Tabel Publik:</strong> <?= implode(', ', $tabelDB) ?></div>
            <div><strong>Struktur Kolom Tabel 'penyewaan':</strong> 
                <?php if ($kolomTabel): ?>
                    <?= implode(', ', array_map(fn($c) => $c['column_name'] . " (" . $c['data_type'] . ")", $kolomTabel)) ?>
                <?php else: ?>
                    <span class="text-red-600 font-bold">Tabel 'penyewaan' TIDAK DITEMUKAN di database!</span>
                <?php endif; ?>
            </div>
            <div><strong>Jumlah Baris Mentah di Tabel 'penyewaan':</strong> <span class="font-bold text-base text-slate-900"><?= count($rawTransaksi) ?> baris</span></div>
            <?php if ($errorQuery): ?>
                <div class="text-red-600 font-bold">Error Query: <?= htmlspecialchars($errorQuery) ?></div>
            <?php endif; ?>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Daftar Transaksi</h1>
                <p class="text-slate-500 mt-1">Riwayat pinjaman dan pengembalian unit.</p>
            </div>
            <a href="sewa.php" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-sm transition">
                + Buat Transaksi Sewa
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-6">Invoice</th>
                            <th class="py-4 px-6">Penyewa</th>
                            <th class="py-4 px-6">Alat</th>
                            <th class="py-4 px-6">Tgl Sewa</th>
                            <th class="py-4 px-6">Durasi</th>
                            <th class="py-4 px-6">Total Biaya</th>
                            <th class="py-4 px-6 text-center">Status</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (empty($rawTransaksi)): ?>
                            <tr>
                                <td colspan="8" class="py-8 px-6 text-center text-slate-400">
                                    Belum ada transaksi sewa (Data kosong di database).
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rawTransaksi as $t): ?>
                                <?php
                                    $tId = $t['id'] ?? $t['id_penyewaan'] ?? '-';
                                    $namaP = $penyewaMap[$t['id_penyewa'] ?? 0] ?? ('ID #' . ($t['id_penyewa'] ?? '?'));
                                    $namaA = $alatMap[$t['id_alat'] ?? 0] ?? ('ID #' . ($t['id_alat'] ?? '?'));
                                    $durasi = $t['durasi_hari'] ?? $t['durasi'] ?? 1;
                                    $total  = $t['total_biaya'] ?? 0;
                                    $status = strtoupper($t['status'] ?? 'SEWA');
                                    $tgl    = $t['tgl_sewa'] ?? $t['tanggal_sewa'] ?? $t['created_at'] ?? '-';
                                ?>
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-4 px-6 font-semibold text-slate-400">#<?= htmlspecialchars((string)$tId) ?></td>
                                    <td class="py-4 px-6 font-semibold text-slate-900"><?= htmlspecialchars($namaP) ?></td>
                                    <td class="py-4 px-6 text-slate-700"><?= htmlspecialchars($namaA) ?></td>
                                    <td class="py-4 px-6 text-slate-500"><?= htmlspecialchars((string)$tgl) ?></td>
                                    <td class="py-4 px-6 text-slate-700"><?= htmlspecialchars((string)$durasi) ?> Hari</td>
                                    <td class="py-4 px-6 font-bold text-slate-900">Rp <?= number_format((float)$total, 0, ',', '.') ?></td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $status === 'SEWA' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                            <?= $status === 'SEWA' ? 'Dipinjam' : 'Kembali' ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <?php if ($status === 'SEWA'): ?>
                                            <a href="selesai.php?id=<?= $tId ?>" onclick="return confirm('Konfirmasi pengembalian alat?')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                                                Kembalikan
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-xs font-medium">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>