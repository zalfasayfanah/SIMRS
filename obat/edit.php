<?php
// ============================================================
// obat/edit.php
// Edit Data Obat
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Edit Obat';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM obat WHERE id_obat=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location:index.php");
    exit;
}

$data = mysqli_fetch_assoc($result);

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

        $update = mysqli_prepare(
            $conn,
            "UPDATE obat SET
                nama_obat=?,
                stok=?,
                harga=?,
                satuan=?,
                expired_date=?,
                kategori=?,
                minimal_stok=?
            WHERE id_obat=?"
        );

        mysqli_stmt_bind_param(
            $update,
            "sidsssii",
            $nama_obat,
            $stok,
            $harga,
            $satuan,
            $expired,
            $kategori,
            $minimal,
            $id
        );

        if (mysqli_stmt_execute($update)) {

            header("Location:index.php?status=edit_ok");
            exit;

        } else {

            $error = "Gagal mengubah data.";

        }

    }

    $data = [
        'nama_obat'    => $nama_obat,
        'kategori'     => $kategori,
        'satuan'       => $satuan,
        'harga'        => $harga,
        'stok'         => $stok,
        'minimal_stok' => $minimal,
        'expired_date' => $expired
    ];
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Edit Obat</h1>

<p>Perbarui data obat.</p>

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

<h2>Form Edit Obat</h2>

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
value="<?= htmlspecialchars($data['nama_obat']) ?>">

</div>

<div class="form-group">

<label>Kategori</label>

<input
type="text"
name="kategori"
value="<?= htmlspecialchars($data['kategori']) ?>">

</div>

</div>

<div class="form-row">

<div class="form-group">

<label>Satuan</label>

<input
type="text"
name="satuan"
required
value="<?= htmlspecialchars($data['satuan']) ?>">

</div>

<div class="form-group">

<label>Harga</label>

<input
type="number"
name="harga"
min="0"
required
value="<?= htmlspecialchars($data['harga']) ?>">

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
            value="<?= htmlspecialchars($data['stok']) ?>">

    </div>

    <div class="form-group">

        <label>Minimal Stok</label>

        <input
            type="number"
            name="minimal_stok"
            min="0"
            required
            value="<?= htmlspecialchars($data['minimal_stok']) ?>">

    </div>

</div>

<div class="form-row">

    <div class="form-group">

        <label>Tanggal Expired</label>

        <input
            type="date"
            name="expired_date"
            value="<?= htmlspecialchars($data['expired_date']) ?>">

    </div>

    <div class="form-group">

    </div>

</div>

<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">

    <a
        href="index.php"
        class="btn btn-secondary">

        Batal

    </a>

    <button
        type="submit"
        class="btn btn-primary">

        Simpan Perubahan

    </button>

</div>

</form>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>