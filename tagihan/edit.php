<?php
// ============================================================
// tagihan/edit.php
// Edit Tagihan
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Edit Tagihan';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location:index.php");
    exit;
}

$id = (int)$_GET['id'];

//==================================================
// Update Data
//==================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $total_biaya  = (float)$_POST['total_biaya'];
    $diskon       = (float)$_POST['diskon'];
    $penjamin     = trim($_POST['penjamin']);
    $status_bayar = trim($_POST['status_bayar']);
    $keterangan   = trim($_POST['keterangan']);

    $stmt = mysqli_prepare($conn,"
        UPDATE tagihan
        SET
            total_biaya=?,
            diskon=?,
            penjamin=?,
            status_bayar=?,
            keterangan=?
        WHERE id_tagihan=?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "ddsssi",
        $total_biaya,
        $diskon,
        $penjamin,
        $status_bayar,
        $keterangan,
        $id
    );

    if(mysqli_stmt_execute($stmt)){

        header("Location:index.php?status=edit_ok");
        exit;

    }

    header("Location:index.php?status=gagal");
    exit;

}

//==================================================
// Ambil Data Tagihan
//==================================================

$stmt = mysqli_prepare($conn,"

SELECT

t.*,

ps.nama,

ps.no_rm,

p.no_antrean

FROM tagihan t

JOIN pendaftaran p
ON t.id_pendaftaran=p.id_pendaftaran

JOIN pasien ps
ON p.id_pasien=ps.id_pasien

WHERE t.id_tagihan=?

");

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    header("Location:index.php");
    exit;

}

$data=mysqli_fetch_assoc($result);

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Edit Tagihan</h1>

<p>Perbarui data tagihan pasien.</p>

</div>

<a href="index.php" class="btn btn-secondary">

Kembali

</a>

</div>

<div class="card">

<div class="card-header">

<h2>Form Edit Tagihan</h2>

</div>

<div class="card-body">

<form method="POST">

<div class="form-group">

<label>Nomor Invoice</label>

<input
type="text"
value="<?= htmlspecialchars($data['no_invoice']) ?>"
readonly>

</div>

<div class="form-group">

<label>Pasien</label>

<input
type="text"
value="<?= htmlspecialchars($data['nama']) ?> (<?= htmlspecialchars($data['no_rm']) ?>) - <?= htmlspecialchars($data['no_antrean']) ?>"
readonly>

</div>

<div class="form-row">

<div class="form-group">

<label>Total Biaya</label>

<input
type="number"
name="total_biaya"
required
min="0"
value="<?= $data['total_biaya'] ?>">

</div>

<div class="form-group">

<label>Diskon</label>

<input
type="number"
name="diskon"
min="0"
value="<?= $data['diskon'] ?>">

</div>

</div>
<div class="form-row">

    <div class="form-group">

        <label>Penjamin</label>

        <select name="penjamin" required>

            <option value="UMUM"
                <?= strtoupper($data['penjamin'])=='UMUM' ? 'selected' : '' ?>>
                UMUM
            </option>

            <option value="BPJS"
                <?= strtoupper($data['penjamin'])=='BPJS' ? 'selected' : '' ?>>
                BPJS
            </option>

            <option value="ASURANSI"
                <?= strtoupper($data['penjamin'])=='ASURANSI' ? 'selected' : '' ?>>
                ASURANSI
            </option>

        </select>

    </div>

    <div class="form-group">

        <label>Status Pembayaran</label>

        <select name="status_bayar" required>

            <option value="BELUM"
                <?= $data['status_bayar']=='BELUM' ? 'selected' : '' ?>>
                BELUM
            </option>

            <option value="LUNAS"
                <?= $data['status_bayar']=='LUNAS' ? 'selected' : '' ?>>
                LUNAS
            </option>

            <option value="CICIL"
                <?= $data['status_bayar']=='CICIL' ? 'selected' : '' ?>>
                CICIL
            </option>

            <option value="BATAL"
                <?= $data['status_bayar']=='BATAL' ? 'selected' : '' ?>>
                BATAL
            </option>

        </select>

    </div>

</div>

<div class="form-group">

    <label>Keterangan</label>

    <textarea
        name="keterangan"
        rows="4"><?= htmlspecialchars($data['keterangan']) ?></textarea>

</div>

<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">

    <a href="index.php" class="btn btn-secondary">

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