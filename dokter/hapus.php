<?php
// ============================================================
// dokter/hapus.php — Proses Hapus Dokter
// File ini tidak menampilkan UI, langsung proses lalu redirect
// ============================================================

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?status=gagal');
    exit;
}

// Cek apakah dokter masih punya data pendaftaran
$cekPendaftaran = mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE id_dokter = $id");
$jumlah = mysqli_fetch_row($cekPendaftaran)[0] ?? 0;

if ($jumlah > 0) {
    // Tidak bisa hapus kalau masih ada relasi pendaftaran
    header('Location: index.php?status=gagal_relasi');
    exit;
}

$sql = "DELETE FROM dokter WHERE id_dokter = $id";

if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
    header('Location: index.php?status=hapus_ok');
} else {
    header('Location: index.php?status=gagal');
}
exit;