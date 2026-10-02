<?php
require_once __DIR__ . '/config/database.php'; // Otomatis memuat sesi

session_unset();
session_destroy();
header("Location: login.php?logged_out=1");
exit();
?>