<?php
// ============================================================
// obat/hapus.php
// Hapus Data Obat
// ============================================================

require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int) $_GET['id'];

// Cek apakah data ada
$cek = mysqli_prepare($conn, "SELECT id_obat FROM obat WHERE id_obat=?");
mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
$result = mysqli_stmt_get_result($cek);

if (mysqli_num_rows($result) == 0) {
    header("Location:index.php");
    exit;
}

// Hapus data
$hapus = mysqli_prepare($conn, "DELETE FROM obat WHERE id_obat=?");
mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {

    header("Location:index.php?status=hapus_ok");

} else {

    header("Location:index.php?status=gagal");

}

exit;