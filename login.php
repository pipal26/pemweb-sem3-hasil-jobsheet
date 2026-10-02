<?php
require_once __DIR__ . '/config/database.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$loggedOut = isset($_GET['logged_out']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Verifikasi hash password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SewaAlat Camping</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative isolate min-h-screen overflow-x-hidden bg-slate-950 text-slate-800">
    <div class="fixed inset-0 -z-10 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="fixed inset-0 -z-10 bg-slate-950/75 backdrop-blur-[2px]"></div>

    <header class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
        <a href="login.php" class="text-lg font-bold text-white transition hover:text-emerald-300">🏕️ SewaAlat Camping</a>
        <a href="register.php" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:border-emerald-300 hover:text-emerald-300">Buat akun</a>
    </header>

    <main class="mx-auto grid min-h-[calc(100vh-88px)] max-w-7xl items-center gap-10 px-6 pb-12 pt-4 lg:grid-cols-[1fr_440px]">
        <section class="hidden text-white lg:block">
            <p class="mb-4 text-sm font-bold uppercase tracking-[0.18em] text-emerald-300">Siap untuk petualangan berikutnya?</p>
            <h1 class="max-w-2xl text-5xl font-extrabold leading-tight">Perlengkapan outdoor, lebih mudah dikelola.</h1>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-200">Masuk untuk mengelola inventaris, data penyewa, dan transaksi rental dalam satu tempat.</p>
        </section>

        <section class="w-full rounded-2xl border border-white/70 bg-white/95 p-7 shadow-2xl backdrop-blur sm:p-9">
            <div class="mb-7">
                <p class="text-sm font-bold uppercase tracking-wider text-emerald-700">Selamat datang kembali</p>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900">Masuk ke akun</h2>
                <p class="mt-2 text-sm text-slate-500">Gunakan username dan password Anda untuk melanjutkan.</p>
            </div>

            <?php if ($loggedOut): ?>
                <p class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">Anda berhasil keluar dari akun.</p>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <p class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700" role="alert"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                    <input id="username" type="text" name="username" autocomplete="username" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                </div>
                <button type="submit" class="w-full rounded-lg bg-emerald-700 px-5 py-3 font-bold text-white shadow-md transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Masuk</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">Belum punya akun? <a href="register.php" class="font-bold text-emerald-700 hover:text-emerald-900">Daftar sekarang</a></p>
        </section>
    </main>
</body>
</html>