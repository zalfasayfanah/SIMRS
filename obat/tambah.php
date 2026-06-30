<?php
// ============================================================
// obat/tambah.php
// Tambah Data Obat
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Tambah Obat';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_obat   = trim($_POST['nama_obat']);
    $kategori    = trim($_POST['kategori']);
    $satuan      = trim($_POST['satuan']);
    $harga       = trim($_POST['harga']);
    $stok        = trim($_POST['stok']);
    $minimal     = trim($_POST['minimal_stok']);
    $expired     = trim($_POST['expired_date']);

    if ($nama_obat == '') {

        $error = 'Nama obat wajib diisi.';

    } elseif (!is_numeric($harga) || $harga < 0) {

        $error = 'Harga tidak valid.';

    } elseif (!is_numeric($stok) || $stok < 0) {

        $error = 'Stok tidak valid.';

    } elseif (!is_numeric($minimal) || $minimal < 0) {

        $error = 'Minimal stok tidak valid.';

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO obat
            (
                nama_obat,
                stok,
                harga,
                satuan,
                expired_date,
                kategori,
                minimal_stok
            )
            VALUES
            (
                ?,?,?,?,?,?,?
            )"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sidsssi",
            $nama_obat,
            $stok,
            $harga,
            $satuan,
            $expired,
            $kategori,
            $minimal
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location:index.php?status=tambah_ok");
            exit;

        } else {

            $error = "Gagal menyimpan data.";

        }

    }

}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Tambah Obat</h1>

<p>Masukkan data obat baru.</p>

</div>

<a href="index.php" class="btn btn-secondary">

Kembali

</a>

</div>

<?php if($error!=''): ?>

<div class="alert alert-danger">

<?= $error ?>

</div>

<?php endif; ?>

<div class="card">

<div class="card-header">

<h2>Form Tambah Obat</h2>

</div>

<div class="card-body">

<form method="POST">

<div class="form-row">

<div class="form-group">

<label>Nama Obat</label>

<input
type="text"
name="nama_obat"
required
value="<?= htmlspecialchars($_POST['nama_obat'] ?? '') ?>">

</div>

<div class="form-group">

<label>Kategori</label>

<input
type="text"
name="kategori"
value="<?= htmlspecialchars($_POST['kategori'] ?? '') ?>">

</div>

</div>

<div class="form-row">

<div class="form-group">

<label>Satuan</label>

<input
type="text"
name="satuan"
required
value="<?= htmlspecialchars($_POST['satuan'] ?? '') ?>">

</div>

<div class="form-group">

<label>Harga</label>

<input
type="number"
name="harga"
min="0"
required
value="<?= htmlspecialchars($_POST['harga'] ?? '0') ?>">

</div>

</div>
<div class="form-row">

    <div class="form-group">

        <label>Stok</label>

        <input
            type="number"
            name="stok"
            min="0"
            required
            value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>">

    </div>

    <div class="form-group">

        <label>Minimal Stok</label>

        <input
            type="number"
            name="minimal_stok"
            min="0"
            required
            value="<?= htmlspecialchars($_POST['minimal_stok'] ?? '10') ?>">

    </div>

</div>

<div class="form-row">

    <div class="form-group">

        <label>Tanggal Expired</label>

        <input
            type="date"
            name="expired_date"
            value="<?= htmlspecialchars($_POST['expired_date'] ?? '') ?>">

    </div>

    <div class="form-group">

    </div>

</div>

<div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">

    <a href="index.php" class="btn btn-secondary">

        Batal

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        Simpan Data

    </button>

</div>

</form>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>