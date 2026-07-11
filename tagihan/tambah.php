<?php
// ============================================================
// tagihan/tambah.php
// Tambah Tagihan
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Tambah Tagihan';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_pendaftaran = (int)$_POST['id_pendaftaran'];
    $total_biaya    = (float)$_POST['total_biaya'];
    $diskon         = (float)$_POST['diskon'];
    $penjamin       = trim($_POST['penjamin']);
    $status_bayar   = trim($_POST['status_bayar']);
    $keterangan     = trim($_POST['keterangan']);

    // Generate nomor invoice
    $invoice = "INV" . date("YmdHis");

    $stmt = mysqli_prepare($conn,"
        INSERT INTO tagihan
        (
            id_pendaftaran,
            total_biaya,
            diskon,
            penjamin,
            status_bayar,
            no_invoice,
            keterangan
        )
        VALUES
        (?,?,?,?,?,?,?)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "iddssss",
        $id_pendaftaran,
        $total_biaya,
        $diskon,
        $penjamin,
        $status_bayar,
        $invoice,
        $keterangan
    );

    if(mysqli_stmt_execute($stmt)){

        header("Location:index.php?status=tambah_ok");
        exit;

    }

    header("Location:index.php?status=gagal");
    exit;

}

//=====================================
// Ambil Data Pendaftaran
//=====================================

$pendaftaran = mysqli_query($conn,"

SELECT

p.id_pendaftaran,

p.no_antrean,

ps.nama,

ps.no_rm,

p.jenis_penjamin,

d.nama_dokter

FROM pendaftaran p

JOIN pasien ps
ON p.id_pasien=ps.id_pasien

JOIN dokter d
ON p.id_dokter=d.id_dokter

ORDER BY p.tgl_daftar DESC

");

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Tambah Tagihan</h1>

<p>Membuat tagihan pasien.</p>

</div>

<a href="index.php" class="btn btn-secondary">

Kembali

</a>

</div>

<div class="card">

<div class="card-header">

<h2>Form Tagihan</h2>

</div>

<div class="card-body">

<form method="POST">

<div class="form-group">

<label>Pendaftaran</label>

<select
name="id_pendaftaran"
required>

<option value="">Pilih Pendaftaran</option>

<?php while($row=mysqli_fetch_assoc($pendaftaran)): ?>

<option
value="<?= $row['id_pendaftaran'] ?>">

<?= htmlspecialchars($row['no_antrean']) ?>

-

<?= htmlspecialchars($row['nama']) ?>

(

<?= htmlspecialchars($row['no_rm']) ?>

)

-

<?= htmlspecialchars($row['nama_dokter']) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="form-row">

<div class="form-group">

<label>Total Biaya</label>

<input
type="number"
name="total_biaya"
required
min="0">

</div>

<div class="form-group">

<label>Diskon</label>

<input
type="number"
name="diskon"
value="0"
min="0">

</div>

</div>
<div class="form-row">

    <div class="form-group">

        <label>Penjamin</label>

        <select name="penjamin" required>

            <option value="UMUM">UMUM</option>
            <option value="BPJS">BPJS</option>
            <option value="ASURANSI">ASURANSI</option>

        </select>

    </div>

    <div class="form-group">

        <label>Status Pembayaran</label>

        <select name="status_bayar" required>

            <option value="BELUM" selected>BELUM</option>
            <option value="LUNAS">LUNAS</option>
            <option value="BATAL">BATAL</option>

        </select>

    </div>

</div>

<div class="form-group">

    <label>Keterangan</label>

    <textarea
        name="keterangan"
        rows="4"
        placeholder="Masukkan keterangan jika diperlukan..."></textarea>

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

        Simpan Tagihan

    </button>

</div>

</form>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>