<?php
// ============================================================
// tagihan/hapus.php
// Hapus Tagihan
// ============================================================

require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int) $_GET['id'];

// Cek apakah tagihan ada
$cek = mysqli_prepare(
    $conn,
    "SELECT id_tagihan FROM tagihan WHERE id_tagihan=?"
);

mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);

$result = mysqli_stmt_get_result($cek);

if (mysqli_num_rows($result) == 0) {

    header("Location:index.php");
    exit;

}

// Hapus tagihan
$hapus = mysqli_prepare(
    $conn,
    "DELETE FROM tagihan WHERE id_tagihan=?"
);

mysqli_stmt_bind_param($hapus, "i", $id);

if (mysqli_stmt_execute($hapus)) {

    header("Location:index.php?status=hapus_ok");

} else {

    header("Location:index.php?status=gagal");

}

exit;