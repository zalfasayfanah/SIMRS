<?php
// ============================================================
// pasien/hapus.php — Proses Hapus Pasien
// ============================================================

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?status=gagal');
    exit;
}

// Cek relasi ke tabel pendaftaran sebelum hapus
$cek    = mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE id_pasien = $id");
$jumlah = mysqli_fetch_row($cek)[0] ?? 0;

if ($jumlah > 0) {
    header('Location: index.php?status=gagal_relasi');
    exit;
}

if (mysqli_query($conn, "DELETE FROM pasien WHERE id_pasien = $id") && mysqli_affected_rows($conn) > 0) {
    header('Location: index.php?status=hapus_ok');
} else {
    header('Location: index.php?status=gagal');
}
exit;