<?php
require_once __DIR__ . '/../config/database.php';
$sql = "SELECT py.*, p.nama AS nama_penyewa, a.nama_alat 
        FROM penyewaan py
        JOIN penyewa p ON py.id_penyewa = p.id
        JOIN alat a ON py.id_alat = a.id
        ORDER BY py.id DESC";
$transaksi = $pdo->query($sql)->fetchAll();
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

    <!-- Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <a href="../index.php" class="text-xl font-bold tracking-wide text-white hover:text-blue-400 transition">SewaAlat Studio</a>
            <nav class="flex space-x-3 mt-2 sm:mt-0">
                <a href="../alat/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Alat</a>
                <a href="../penyewa/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Penyewa</a>
                <a href="list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-slate-800">Transaksi Rental</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Transaksi</h1>
                <p class="text-slate-500 mt-1">Riwayat pinjaman, perhitungan tagihan sewa, dan pengembalian unit.</p>
            </div>
            <a href="sewa.php" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-sm transition">
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
                        <?php if (empty($transaksi)): ?>
                            <tr>
                                <td colspan="8" class="py-8 px-6 text-center text-slate-400">Belum ada transaksi sewa.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transaksi as $t): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-6 font-semibold text-slate-400">#<?= htmlspecialchars($t['id']) ?></td>
                                <td class="py-4 px-6 font-semibold text-slate-900"><?= htmlspecialchars($t['nama_penyewa']) ?></td>
                                <td class="py-4 px-6 text-slate-700"><?= htmlspecialchars($t['nama_alat']) ?></td>
                                <td class="py-4 px-6 text-slate-500"><?= htmlspecialchars($t['tgl_sewa']) ?></td>
                                <td class="py-4 px-6 text-slate-700"><?= htmlspecialchars($t['durasi_hari']) ?> Hari</td>
                                <td class="py-4 px-6 font-bold text-slate-900">Rp <?= number_format($t['total_biaya'], 0, ',', '.') ?></td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($t['status'] === 'SEWA'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            Dipinjam
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Kembali
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <?php if ($t['status'] === 'SEWA'): ?>
                                        <a href="selesai.php?id=<?= $t['id'] ?>" onclick="return confirm('Konfirmasi pengembalian alat?')" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition">
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