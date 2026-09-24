<?php
require_once '../config/database.php';
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM alat WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
}
header("Location: list.php");
exit;