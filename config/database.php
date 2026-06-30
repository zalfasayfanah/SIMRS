<?php
// ============================================================
// config/database.php
// Koneksi ke database SIMRS menggunakan MySQLi
// ============================================================

define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_USER', 'root');         // sesuaikan username MySQL kamu
define('DB_PASS', '');             // sesuaikan password MySQL kamu
define('DB_NAME', 'simrs');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if (!$conn) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#b91c1c;">
        <strong>Koneksi database gagal:</strong> ' . mysqli_connect_error() . '
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');

