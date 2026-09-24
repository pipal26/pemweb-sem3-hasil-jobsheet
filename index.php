<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Rental Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col font-sans">

    <!-- Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex flex-wrap justify-between items-center">
            <a href="index.php" class="text-xl font-bold tracking-wide text-white hover:text-blue-400 transition">SewaAlat Studio</a>
            <nav class="flex space-x-3 mt-2 sm:mt-0">
                <a href="alat/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Alat</a>
                <a href="penyewa/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Data Penyewa</a>
                <a href="penyewaan/list.php" class="px-4 py-2 text-sm font-medium rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">Transaksi Rental</a>
            </nav>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full">
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Manajemen Rental</h1>
            <p class="text-slate-500 mt-2 text-base">Kelola inventaris alat studio, informasi pelanggan, dan seluruh alur transaksi peminjaman.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card Alat -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex flex-col justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-200 border-t-4 border-t-blue-500">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl mb-4">📷</div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Manajemen Alat</h2>
                    <p class="text-slate-500 text-sm leading-relaxed">Pantau stok perlengkapan kamera, tata kelola harga per hari, dan registrasi unit baru.</p>
                </div>
                <a href="alat/list.php" class="mt-8 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800">
                    Buka Data Alat <span class="ml-2">&rarr;</span>
                </a>
            </div>

            <!-- Card Penyewa -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex flex-col justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-200 border-t-4 border-t-violet-500">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center font-bold text-xl mb-4">👥</div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Data Penyewa</h2>
                    <p class="text-slate-500 text-sm leading-relaxed">Kelola identitas pelanggan, nomor telepon aktif, dan alamat penyewa yang terdaftar.</p>
                </div>
                <a href="penyewa/list.php" class="mt-8 inline-flex items-center text-sm font-semibold text-violet-600 hover:text-violet-800">
                    Buka Data Penyewa <span class="ml-2">&rarr;</span>
                </a>
            </div>

            <!-- Card Transaksi -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 flex flex-col justify-between hover:shadow-lg hover:-translate-y-1 transition-all duration-200 border-t-4 border-t-emerald-500">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl mb-4">📋</div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Transaksi Rental</h2>
                    <p class="text-slate-500 text-sm leading-relaxed">Buat peminjaman baru, hitung total biaya otomatis, dan selesaikan pengembalian alat.</p>
                </div>
                <a href="penyewaan/list.php" class="mt-8 inline-flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-800">
                    Buka Transaksi <span class="ml-2">&rarr;</span>
                </a>
            </div>
        </div>
    </main>

</body>
</html>