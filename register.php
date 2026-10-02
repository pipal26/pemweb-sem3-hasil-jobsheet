<?php
require_once __DIR__ . '/config/database.php';
$message = '';
$registrationSuccess = false;

// Jika sudah login, lempar ke index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    // Hash password sebelum disimpan
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt->execute([$username, $password])) {
            $registrationSuccess = true;
            $message = 'Registrasi berhasil. Silakan masuk menggunakan akun baru Anda.';
        }
    } catch (PDOException $e) {
        $message = 'Registrasi gagal. Username mungkin sudah digunakan.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | SewaAlat Camping</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative isolate min-h-screen overflow-x-hidden bg-slate-950 text-slate-800">
    <div class="fixed inset-0 -z-10 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="fixed inset-0 -z-10 bg-slate-950/75 backdrop-blur-[2px]"></div>

    <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
        <a href="login.php" class="text-lg font-bold text-white transition hover:text-emerald-300">🏕️ SewaAlat Camping</a>
        <a href="login.php" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:border-emerald-300 hover:text-emerald-300">Masuk</a>
    </header>

    <main class="mx-auto grid min-h-[calc(100vh-88px)] max-w-7xl items-center gap-10 px-6 pb-12 pt-4 lg:grid-cols-[1fr_440px]">
        <section class="hidden text-white lg:block">
            <p class="mb-4 text-sm font-bold uppercase tracking-[0.18em] text-emerald-300">Mulai dari sini</p>
            <h1 class="max-w-2xl text-5xl font-extrabold leading-tight">Kelola rental outdoor dengan lebih praktis.</h1>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-200">Buat akun untuk mengatur perlengkapan, penyewa, dan transaksi persewaan Anda.</p>
        </section>

        <section class="w-full rounded-2xl border border-white/70 bg-white/95 p-7 shadow-2xl backdrop-blur sm:p-9">
            <div class="mb-7">
                <p class="text-sm font-bold uppercase tracking-wider text-emerald-700">Akun baru</p>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Daftar akun</h2>
                <p class="mt-2 text-sm text-slate-500">Isi data berikut untuk membuat akun SewaAlat Camping.</p>
            </div>

            <?php if ($message !== ''): ?>
                <p class="mb-5 rounded-lg border px-4 py-3 text-sm font-medium <?= $registrationSuccess ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-700' ?>" role="<?= $registrationSuccess ? 'status' : 'alert' ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                    <input id="username" type="text" name="username" autocomplete="username" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                </div>
                <button type="submit" class="w-full rounded-lg bg-emerald-700 px-5 py-3 font-bold text-white shadow-md transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Buat akun</button>
            </form>

            <?php if ($registrationSuccess): ?>
                <p class="mt-5 text-center text-sm"><a href="login.php" class="font-bold text-emerald-700 hover:text-emerald-900">Lanjut ke halaman masuk</a></p>
            <?php else: ?>
                <p class="mt-6 text-center text-sm text-slate-600">Sudah punya akun? <a href="login.php" class="font-bold text-emerald-700 hover:text-emerald-900">Masuk di sini</a></p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>