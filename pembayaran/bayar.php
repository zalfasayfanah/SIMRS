<?php
// ============================================================
// pembayaran/bayar.php
// Form Pembayaran Tagihan Pasien
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Pembayaran';

// ============================================================
// Ambil ID Tagihan (jika dari halaman Tagihan)
// ============================================================

$id_tagihan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ============================================================
// Simpan Pembayaran
// ============================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_tagihan   = (int)$_POST['id_tagihan'];
    $jumlah_bayar = (float)$_POST['jumlah_bayar'];
    $metode       = trim($_POST['metode']);
    $no_referensi = trim($_POST['no_referensi']);

    // Ambil total tagihan
    $cek = mysqli_prepare($conn,"
        SELECT total_biaya
        FROM tagihan
        WHERE id_tagihan=?
    ");

    mysqli_stmt_bind_param($cek,"i",$id_tagihan);
    mysqli_stmt_execute($cek);

    $hasil = mysqli_stmt_get_result($cek);
    $tagihan = mysqli_fetch_assoc($hasil);

    if(!$tagihan){

        header("Location:index.php?status=gagal");
        exit;

    }

    $total = (float)$tagihan['total_biaya'];

    // Validasi pembayaran harus lunas

    if($jumlah_bayar < $total){

        echo "<script>

            alert('Jumlah pembayaran tidak boleh kurang dari total tagihan.');

            history.back();

        </script>";

        exit;

    }

    $kembalian = $jumlah_bayar - $total;

    // Simpan pembayaran

    $stmt = mysqli_prepare($conn,"

        INSERT INTO pembayaran
        (
            id_tagihan,
            metode,
            jumlah_bayar,
            kembalian,
            no_referensi
        )

        VALUES

        (?,?,?,?,?)

    ");

    mysqli_stmt_bind_param(

        $stmt,

        "isdds",

        $id_tagihan,
        $metode,
        $jumlah_bayar,
        $kembalian,
        $no_referensi

    );

    if(mysqli_stmt_execute($stmt)){

        // Update status tagihan

        $up = mysqli_prepare($conn,"

            UPDATE tagihan

            SET status_bayar='LUNAS'

            WHERE id_tagihan=?

        ");

        mysqli_stmt_bind_param($up,"i",$id_tagihan);

        mysqli_stmt_execute($up);

        header("Location:../tagihan/index.php?status=bayar_ok");

        exit;

    }

    header("Location:index.php?status=gagal");

    exit;

}

// ============================================================
// Ambil Data Tagihan
// ============================================================

if($id_tagihan > 0){

    // Jika dari halaman Tagihan

    $sql="

    SELECT

    t.id_tagihan,
    t.no_invoice,
    t.total_biaya,
    t.penjamin,

    ps.nama,
    ps.no_rm,

    p.tgl_daftar

    FROM tagihan t

    JOIN pendaftaran p
    ON t.id_pendaftaran=p.id_pendaftaran

    JOIN pasien ps
    ON p.id_pasien=ps.id_pasien

    WHERE t.id_tagihan=$id_tagihan

    ";

}else{

    // Jika dibuka dari menu Pembayaran

    $sql="

    SELECT

    t.id_tagihan,
    t.no_invoice,
    t.total_biaya,
    t.penjamin,

    ps.nama,
    ps.no_rm,

    p.tgl_daftar

    FROM tagihan t

    JOIN pendaftaran p
    ON t.id_pendaftaran=p.id_pendaftaran

    JOIN pasien ps
    ON p.id_pasien=ps.id_pasien

    WHERE t.status_bayar='BELUM'

    ORDER BY t.tgl_tagihan DESC

    ";

}

$result = mysqli_query($conn,$sql);

$dataTagihan = null;

if($id_tagihan > 0){

    $dataTagihan = mysqli_fetch_assoc($result);

}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Pembayaran</h1>

<p>Proses pembayaran tagihan pasien.</p>

</div>

<a href="../tagihan/index.php" class="btn btn-secondary">

Kembali

</a>

</div>

<div class="card">

<div class="card-header">

<h2>Form Pembayaran</h2>

</div>

<div class="card-body">

<form method="POST">

<?php if($dataTagihan): ?>

<input
type="hidden"
name="id_tagihan"
value="<?= $dataTagihan['id_tagihan']; ?>">

<div class="form-group">

<label>No Invoice</label>

<input
type="text"
value="<?= htmlspecialchars($dataTagihan['no_invoice']); ?>"
readonly>

</div>

<div class="form-group">

<label>Nama Pasien</label>

<input
type="text"
value="<?= htmlspecialchars($dataTagihan['nama']); ?>"
readonly>

</div>

<div class="form-group">

<label>No Rekam Medis</label>

<input
type="text"
value="<?= htmlspecialchars($dataTagihan['no_rm']); ?>"
readonly>

</div>

<div class="form-group">

<label>Penjamin</label>

<input
type="text"
value="<?= htmlspecialchars($dataTagihan['penjamin']); ?>"
readonly>

</div>

<?php else: ?>

<div class="form-group">

<label>Pilih Tagihan</label>

<select
name="id_tagihan"
id="tagihan"
required>

<option value="">-- Pilih Tagihan --</option>

<?php while($row=mysqli_fetch_assoc($result)): ?>

<option
value="<?= $row['id_tagihan']; ?>"
data-total="<?= $row['total_biaya']; ?>">

<?= htmlspecialchars($row['no_invoice']); ?>

|

<?= htmlspecialchars($row['nama']); ?>

|

<?= htmlspecialchars($row['no_rm']); ?>

</option>

<?php endwhile; ?>

</select>

</div>

<?php endif; ?>
<div class="form-row">

    <div class="form-group">

        <label>Total Tagihan</label>

        <input
            type="number"
            id="total"
            readonly
            value="<?= $dataTagihan ? $dataTagihan['total_biaya'] : '' ?>">

    </div>

    <div class="form-group">

        <label>Jumlah Bayar</label>

        <input
            type="number"
            name="jumlah_bayar"
            id="bayar"
            min="0"
            required>

    </div>

</div>

<div class="form-row">

    <div class="form-group">

        <label>Kembalian</label>

        <input
            type="number"
            id="kembalian"
            value="0"
            readonly>

    </div>

    <div class="form-group">

        <label>Metode Pembayaran</label>

        <select name="metode" required>

            <option value="TUNAI">Tunai</option>

            <option value="TRANSFER">Transfer</option>

            <option value="QRIS">QRIS</option>

            <option value="EDC">EDC</option>

            <option value="ASURANSI">Asuransi</option>

        </select>

    </div>

</div>

<div class="form-group">

    <label>No Referensi</label>

    <input
        type="text"
        name="no_referensi"
        placeholder="Kosongkan jika pembayaran tunai">

</div>

<div style="display:flex;gap:10px;margin-top:20px;">

    <button
        type="submit"
        class="btn btn-primary">

        Simpan Pembayaran

    </button>

    <a
        href="../tagihan/index.php"
        class="btn btn-secondary">

        Batal

    </a>

</div>

</form>

</div>

</div>

</div>

<script>

const tagihan = document.getElementById('tagihan');
const total = document.getElementById('total');
const bayar = document.getElementById('bayar');
const kembalian = document.getElementById('kembalian');

function hitungKembalian(){

    let totalTagihan = parseFloat(total.value) || 0;
    let uang = parseFloat(bayar.value) || 0;

    if(uang >= totalTagihan){

        kembalian.value = uang - totalTagihan;

    }else{

        kembalian.value = 0;

    }

}

if(tagihan){

    tagihan.addEventListener('change',function(){

        let option = this.options[this.selectedIndex];

        total.value = option.dataset.total || 0;

        hitungKembalian();

    });

}

bayar.addEventListener('keyup', hitungKembalian);
bayar.addEventListener('change', hitungKembalian);
bayar.addEventListener('input', hitungKembalian);

</script>

<?php include '../includes/footer.php'; ?>