<?php
// ============================================================
// pendaftaran/hapus.php — Proses Hapus Pendaftaran
// ============================================================

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?status=gagal');
    exit;
}

if (mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_pendaftaran = $id") && mysqli_affected_rows($conn) > 0) {
    header('Location: index.php?status=hapus_ok');
} else {
    header('Location: index.php?status=gagal');
}
exit;