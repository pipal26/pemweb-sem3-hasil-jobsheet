<?php
require_once __DIR__ . '/config/database.php';

$keyword = trim($_GET['q'] ?? '');
$hasilAlat = [];
$hasilPenyewa = [];
$hasilSewa = [];

if ($keyword !== '') {
    $searchTerm = "%$keyword%";
    
    // 1. Cari Alat (Case-Insensitive di PostgreSQL menggunakan ILIKE)
    $qAlat = $pdo->prepare("SELECT * FROM alat WHERE nama_alat ILIKE :q OR kategori ILIKE :q ORDER BY id DESC");
    $qAlat->execute([':q' => $searchTerm]);
    $hasilAlat = $qAlat->fetchAll();

    // 2. Cari Penyewa (Kolom yang benar: nama, no_telp, alamat)
    $qPenyewa = $pdo->prepare("SELECT * FROM penyewa WHERE nama ILIKE :q OR no_telp ILIKE :q OR alamat ILIKE :q ORDER BY id DESC");
    $qPenyewa->execute([':q' => $searchTerm]);
    $hasilPenyewa = $qPenyewa->fetchAll();

    // 3. Cari Transaksi Rental
    $qSewa = $pdo->prepare("
        SELECT py.*, p.nama AS nama_penyewa, a.nama_alat 
        FROM penyewaan py
        JOIN penyewa p ON py.id_penyewa = p.id
        JOIN alat a ON py.id_alat = a.id
        WHERE p.nama ILIKE :q OR a.nama_alat ILIKE :q OR py.status ILIKE :q
        ORDER BY py.id DESC
    ");
    $qSewa->execute([':q' => $searchTerm]);
    $hasilSewa = $qSewa->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rental Outdoor</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-slide {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover; background-position: center;
            opacity: 0; transition: opacity 1.5s ease-in-out;
            z-index: -2;
        }
        .bg-slide.active { opacity: 1; }
        .bg-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(2px);
            z-index: -1;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 flex flex-col font-sans relative">

    <!-- Slider Gambar Pemandangan -->
    <div class="bg-slide active" style="background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80');"></div>
    <div class="bg-slide" style="background-image: url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=80');"></div>
    <div class="bg-slide" style="background-image: url('https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=1920&q=80');"></div>
    <div class="bg-overlay"></div>

    <!-- Navbar -->
    <header class="bg-slate-900/90 backdrop-blur border-b border-slate-800 sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <a href="index.php" class="text-xl font-bold tracking-wide text-white hover:text-emerald-400 transition">🏕️ SewaAlat Camping</a>
            <nav class="flex space-x-3 mt-2 sm:mt-0">
                <a href="alat/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Alat</a>
                <a href="penyewa/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Penyewa</a>
                <a href="penyewaan/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Transaksi Rental</a>
            </nav>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="max-w-6xl mx-auto px-6 py-12 flex-1 w-full">
        <!-- Hero & Search -->
        <div class="text-center text-white mb-10">
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight drop-shadow-md">Kelola Petualangan & Persewaan Anda</h1>
            <p class="text-slate-300 mt-3 text-base sm:text-lg max-w-2xl mx-auto">Pencarian terpadu untuk data inventaris alat, data penyewa, serta transaksi peminjaman aktif.</p>

            <form action="index.php" method="GET" class="mt-8 flex justify-center max-w-xl mx-auto gap-2">
                <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Ketik nama alat, pelanggan, dsb..." class="w-full px-5 py-3.5 rounded-xl text-slate-900 outline-none shadow-lg focus:ring-2 focus:ring-emerald-400" required>
                <button type="submit" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-lg transition">Cari</button>
                <?php if ($keyword !== ''): ?>
                    <a href="index.php" class="px-4 py-3.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl shadow-lg flex items-center justify-center">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Bagian Hasil Pencarian (Muncul jika ada pencarian) -->
        <?php if ($keyword !== ''): ?>
        <div class="mb-12 space-y-6">
            <h2 class="text-xl font-bold text-white mb-4">Hasil Pencarian untuk: "<span class="text-emerald-400"><?= htmlspecialchars($keyword) ?></span>"</h2>

            <!-- Hasil Alat -->
            <div class="bg-white/95 backdrop-blur rounded-2xl shadow-xl p-6 border border-slate-100">
                <h3 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">📦 Alat Ditemukan (<?= count($hasilAlat) ?>)</h3>
                <?php if (count($hasilAlat) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                                <tr><th class="p-3">Nama Alat</th><th class="p-3">Kategori</th><th class="p-3">Harga/Hari</th><th class="p-3">Stok</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($hasilAlat as $row): ?>
                                    <tr>
                                        <td class="p-3 font-semibold text-slate-900"><?= htmlspecialchars($row['nama_alat']) ?></td>
                                        <td class="p-3 text-slate-600"><?= htmlspecialchars($row['kategori']) ?></td>
                                        <td class="p-3">Rp <?= number_format($row['harga_sewa'], 0, ',', '.') ?></td>
                                        <td class="p-3 font-bold <?= $row['stok'] > 0 ? 'text-emerald-600' : 'text-red-500' ?>"><?= $row['stok'] ?> Unit</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-slate-500 text-sm">Tidak ada data alat yang cocok.</p>
                <?php endif; ?>
            </div>

            <!-- Hasil Penyewa -->
            <div class="bg-white/95 backdrop-blur rounded-2xl shadow-xl p-6 border border-slate-100">
                <h3 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">👤 Penyewa Ditemukan (<?= count($hasilPenyewa) ?>)</h3>
                <?php if (count($hasilPenyewa) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                                <tr><th class="p-3">Nama Penyewa</th><th class="p-3">Nomor Telepon</th><th class="p-3">Alamat</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($hasilPenyewa as $row): ?>
                                    <tr>
                                        <td class="p-3 font-semibold text-slate-900"><?= htmlspecialchars($row['nama']) ?></td>
                                        <td class="p-3 font-mono text-slate-600"><?= htmlspecialchars($row['no_telp']) ?></td>
                                        <td class="p-3 text-slate-600"><?= htmlspecialchars($row['alamat']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-slate-500 text-sm">Tidak ada data penyewa yang cocok.</p>
                <?php endif; ?>
            </div>

            <!-- Hasil Transaksi -->
            <div class="bg-white/95 backdrop-blur rounded-2xl shadow-xl p-6 border border-slate-100">
                <h3 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">📄 Transaksi Ditemukan (<?= count($hasilSewa) ?>)</h3>
                <?php if (count($hasilSewa) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                                <tr><th class="p-3">Invoice</th><th class="p-3">Penyewa</th><th class="p-3">Alat</th><th class="p-3">Durasi</th><th class="p-3">Total Biaya</th><th class="p-3 text-center">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($hasilSewa as $row): ?>
                                    <tr>
                                        <td class="p-3 font-semibold text-slate-400">#<?= $row['id'] ?></td>
                                        <td class="p-3 font-semibold text-slate-900"><?= htmlspecialchars($row['nama_penyewa']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($row['nama_alat']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($row['durasi_hari']) ?> Hari</td>
                                        <td class="p-3 font-bold">Rp <?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
                                        <td class="p-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $row['status'] === 'SEWA' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                                <?= $row['status'] === 'SEWA' ? 'Dipinjam' : 'Kembali' ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-slate-500 text-sm">Tidak ada transaksi yang cocok.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Akses Cepat Tombol Menu -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="alat/tambah.php" class="bg-white/95 backdrop-blur rounded-2xl p-6 shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-200 border-t-4 border-blue-500">
                <h4 class="text-lg font-bold text-slate-900 mb-1">+ Tambah Alat</h4>
                <p class="text-slate-500 text-sm">Input data tenda, kompor, ransel, dan perlengkapan lainnya.</p>
            </a>
            <a href="penyewa/tambah.php" class="bg-white/95 backdrop-blur rounded-2xl p-6 shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-200 border-t-4 border-violet-500">
                <h4 class="text-lg font-bold text-slate-900 mb-1">+ Tambah Penyewa</h4>
                <p class="text-slate-500 text-sm">Daftarkan identitas kontak pelanggan baru.</p>
            </a>
            <a href="penyewaan/sewa.php" class="bg-white/95 backdrop-blur rounded-2xl p-6 shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-200 border-t-4 border-emerald-500">
                <h4 class="text-lg font-bold text-slate-900 mb-1">+ Buat Sewa Baru</h4>
                <p class="text-slate-500 text-sm">Mulai transaksi peminjaman alat dengan kalkulasi otomatis.</p>
            </a>
        </div>
    </main>

    <!-- Footer Lengkap & Berisi -->
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">
            <div>
                <h3 class="text-white font-bold text-base mb-3">🏕️ SewaAlat Camping</h3>
                <p class="leading-relaxed">Platform manajemen persewaan alat pendakian dan perlengkapan kegiatan outdoor terintegrasi.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-3">Tautan Cepat</h4>
                <ul class="space-y-2">
                    <li><a href="alat/list.php" class="hover:text-emerald-400 transition">Katalog Inventaris Alat</a></li>
                    <li><a href="penyewa/list.php" class="hover:text-emerald-400 transition">Daftar Pelanggan</a></li>
                    <li><a href="penyewaan/list.php" class="hover:text-emerald-400 transition">Daftar Transaksi</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-3">Informasi Sistem</h4>
                <p>⏱️ Sesi Aktif: <span class="text-emerald-400 font-semibold">3 Jam (10.800s)</span></p>
                <p class="mt-1">📍 Basis Data: <span class="text-emerald-400 font-semibold">PostgreSQL (Neon)</span></p>
            </div>
        </div>
        <div class="border-t border-slate-800 text-center py-4 text-xs text-slate-500">
            &copy; <?= date('Y') ?> SewaAlat Camping. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    <!-- Background Slider Script -->
    <script>
        const slides = document.querySelectorAll('.bg-slide');
        let current = 0;
        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 5000);
    </script>
</body>
</html>