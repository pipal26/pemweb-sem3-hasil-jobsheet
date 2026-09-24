<?php
require_once '../config/database.php';
$stmt = $pdo->query("SELECT * FROM penyewa ORDER BY id ASC");
$daftar_penyewa = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penyewa - SewaAlat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col font-sans">

    <!-- Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <a href="../index.php" class="text-xl font-bold tracking-wide text-white hover:text-blue-400 transition">SewaAlat Studio</a>
            <nav class="flex space-x-3 mt-2 sm:mt-0">
                <a href="../alat/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Alat</a>
                <a href="list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-slate-800">Data Penyewa</a>
                <a href="../penyewaan/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Transaksi Rental</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Data Penyewa</h1>
                <p class="text-slate-500 mt-1">Daftar kontak dan alamat pelanggan terdaftar.</p>
            </div>
            <a href="tambah.php" class="inline-flex items-center justify-center px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-medium rounded-xl shadow-sm transition">
                + Tambah Penyewa
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Nama Lengkap</th>
                            <th class="py-4 px-6">Nomor Telepon</th>
                            <th class="py-4 px-6">Alamat</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php if (empty($daftar_penyewa)): ?>
                            <tr>
                                <td colspan="5" class="py-8 px-6 text-center text-slate-400">Belum ada pelanggan terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_penyewa as $p): ?>
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-4 px-6 font-semibold text-slate-400">#<?= htmlspecialchars($p['id']) ?></td>
                                <td class="py-4 px-6 font-semibold text-slate-900"><?= htmlspecialchars($p['nama']) ?></td>
                                <td class="py-4 px-6 font-mono text-slate-600"><?= htmlspecialchars($p['no_telp']) ?></td>
                                <td class="py-4 px-6 text-slate-600"><?= htmlspecialchars($p['alamat']) ?></td>
                                <td class="py-4 px-6 text-center">
                                    <a href="hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Hapus penyewa ini?')" class="inline-flex items-center px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold transition">
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