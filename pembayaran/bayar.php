<?php
// ============================================================
// pembayaran/bayar.php
// Form Pembayaran Tagihan Pasien
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Pembayaran';

//==================================================
// Simpan Pembayaran
//==================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_tagihan    = (int) $_POST['id_tagihan'];
    $jumlah_bayar  = (float) $_POST['jumlah_bayar'];
    $metode        = trim($_POST['metode']);
    $no_referensi  = trim($_POST['no_referensi']);

    // Ambil total tagihan
    $q = mysqli_prepare($conn,"
        SELECT total_biaya
        FROM tagihan
        WHERE id_tagihan=?
    ");

    mysqli_stmt_bind_param($q,"i",$id_tagihan);
    mysqli_stmt_execute($q);

    $hasil = mysqli_stmt_get_result($q);
    $tagihan = mysqli_fetch_assoc($hasil);

    if(!$tagihan){
        header("Location:index.php?status=gagal");
        exit;
    }

    $total = (float)$tagihan['total_biaya'];

    // Hitung kembalian
    $kembalian = 0;

    if($jumlah_bayar > $total){
        $kembalian = $jumlah_bayar - $total;
    }

    // Status pembayaran
    if($jumlah_bayar >= $total){
        $statusTagihan = 'LUNAS';
    }elseif($jumlah_bayar > 0){
        $statusTagihan = 'CICIL';
    }else{
        $statusTagihan = 'BELUM';
    }

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
            SET status_bayar=?
            WHERE id_tagihan=?
        ");

        mysqli_stmt_bind_param(
            $up,
            "si",
            $statusTagihan,
            $id_tagihan
        );

        mysqli_stmt_execute($up);

        header("Location:index.php?status=bayar_ok");
        exit;
    }

    header("Location:index.php?status=gagal");
    exit;
}

//==================================================
// Ambil semua tagihan yang belum lunas
//==================================================

$sql = "

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
ON p.id_pendaftaran=t.id_pendaftaran

JOIN pasien ps
ON ps.id_pasien=p.id_pasien

WHERE t.status_bayar<>'LUNAS'

ORDER BY t.tgl_tagihan DESC

";

$result = mysqli_query($conn,$sql);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Pembayaran</h1>

<p>Proses pembayaran tagihan pasien.</p>

</div>

<a href="index.php" class="btn btn-secondary">

Kembali

</a>

</div>

<div class="card">

<div class="card-header">

<h2>Form Pembayaran</h2>

</div>

<div class="card-body">

<form method="POST">

<div class="form-group">

<label>Pilih Tagihan</label>

<select
name="id_tagihan"
id="tagihan"
required>

<option value="">-- Pilih Tagihan --</option>

<?php while($row=mysqli_fetch_assoc($result)): ?>

<option

value="<?= $row['id_tagihan'] ?>"

data-total="<?= $row['total_biaya'] ?>"

>

<?= htmlspecialchars($row['no_invoice']) ?>

|

<?= htmlspecialchars($row['nama']) ?>

|

<?= htmlspecialchars($row['no_rm']) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="form-row">

<div class="form-group">

<label>Total Tagihan</label>

<input
type="number"
id="total"
readonly>

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
            name="kembalian"
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

<div style="display:flex;gap:10px;margin-top:20px">

    <button
        type="submit"
        class="btn btn-primary">

        Simpan Pembayaran

    </button>

    <a
        href="index.php"
        class="btn btn-secondary">

        Batal

    </a>

</div>

</form>

</div>

</div>

</div>

<script>

const tagihan=document.getElementById('tagihan');
const total=document.getElementById('total');
const bayar=document.getElementById('bayar');
const kembali=document.getElementById('kembalian');

function hitung(){

    let totalTagihan=parseFloat(total.value)||0;

    let uang=parseFloat(bayar.value)||0;

    if(uang>=totalTagihan){

        kembali.value=uang-totalTagihan;

    }else{

        kembali.value=0;

    }

}

tagihan.addEventListener('change',function(){

    let option=this.options[this.selectedIndex];

    total.value=option.dataset.total || 0;

    hitung();

});

bayar.addEventListener('keyup',hitung);
bayar.addEventListener('change',hitung);

</script>

<?php include '../includes/footer.php'; ?>