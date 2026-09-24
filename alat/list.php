<?php
require_once __DIR__ . '/../config/database.php';
$stmt = $pdo->query("SELECT * FROM alat ORDER BY id ASC");
$daftar_alat = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alat - SewaAlat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col font-sans">

    <!-- Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <a href="../index.php" class="text-xl font-bold tracking-wide text-white hover:text-blue-400 transition">SewaAlat Camping</a>
            <nav class="flex space-x-3 mt-2 sm:mt-0">
                <a href="list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-slate-800">Data Alat</a>
                <a href="../penyewa/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Penyewa</a>
                <a href="../penyewaan/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Transaksi Rental</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Alat</h1>
                <p class="text-slate-500 mt-1">Daftar unit inventaris studio.</p>
            </div>
            <a href="tambah.php" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm transition">
                + Tambah Alat
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Nama Alat</th>
                            <th class="py-4 px-6">Kategori</th>
                            <th class="py-4 px-6">Harga / Hari</th>
                            <th class="py-4 px-6">Stok</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (empty($daftar_alat)): ?>
                            <tr>
                                <td colspan="6" class="py-8 px-6 text-center text-slate-400">Belum ada unit inventaris yang terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_alat as $a): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-6 font-semibold text-slate-400">#<?= htmlspecialchars($a['id']) ?></td>
                                <td class="py-4 px-6 font-semibold text-slate-900"><?= htmlspecialchars($a['nama_alat']) ?></td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700">
                                        <?= htmlspecialchars($a['kategori']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-700">Rp <?= number_format($a['harga_sewa'], 0, ',', '.') ?></td>
                                <td class="py-4 px-6">
                                    <span class="font-bold <?= $a['stok'] > 0 ? 'text-emerald-600' : 'text-red-500' ?>">
                                        <?= htmlspecialchars($a['stok']) ?> Unit
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <a href="hapus.php?id=<?= $a['id'] ?>" onclick="return confirm('Hapus alat ini?')" class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold transition">
                                        Hapus
                                    </a>
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