<?php
require_once __DIR__ . '/config/database.php';

// Wajib login untuk akses halaman ini
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (password_verify($old_password, $user['password'])) {
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($updateStmt->execute([$new_password, $user_id])) {
            $message = "Password berhasil diubah!";
        }
    } else {
        $message = "Password lama salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Ubah Password</title></head>
<body>
    <h2>Ubah Password</h2>
    <p style="color: blue;"><?= $message ?></p>
    <form method="POST">
        <label>Password Lama:</label><br>
        <input type="password" name="old_password" required><br>
        <label>Password Baru:</label><br>
        <input type="password" name="new_password" required><br><br>
        <button type="submit">Simpan Password Baru</button>
    </form>
    <br>
    <a href="index.php">Kembali ke Dashboard</a>
</body>
</html>