<?php
// ============================================================
// pembayaran/invoice.php
// Cetak Invoice Pembayaran
// ============================================================

require_once '../config/database.php';

$basePath='../';
$pageTitle='Invoice Pembayaran';

$id_tagihan = (int)($_GET['id'] ?? 0);

$sql="

SELECT

pb.*,

tg.no_invoice,
tg.total_biaya,
tg.diskon,
tg.penjamin,
tg.status_bayar,

ps.nama,
ps.no_rm,
ps.alamat,
ps.no_hp,

pd.tgl_daftar,
pd.no_antrean,

d.nama_dokter,
d.poli

FROM pembayaran pb

JOIN tagihan tg
ON pb.id_tagihan=tg.id_tagihan

JOIN pendaftaran pd
ON tg.id_pendaftaran=pd.id_pendaftaran

JOIN pasien ps
ON pd.id_pasien=ps.id_pasien

JOIN dokter d
ON pd.id_dokter=d.id_dokter

WHERE tg.id_tagihan=?

ORDER BY pb.id_pembayaran DESC

LIMIT 1

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"i",$id_tagihan);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$data=mysqli_fetch_assoc($result);

if(!$data){

    echo "<script>

    alert('Invoice tidak ditemukan.');

    window.location='index.php';

    </script>";

    exit;

}

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Invoice Pembayaran</h1>

<p>Nomor Invoice : <strong><?= htmlspecialchars($data['no_invoice']) ?></strong></p>

</div>

<div>

<button onclick="window.print()" class="btn btn-primary">

Cetak Invoice

</button>

<a href="../pembayaran/index.php" class="btn btn-secondary">

Kembali

</a>

</div>

</div>

<div class="card">

<div class="card-body">

<h2 style="margin-bottom:20px">

SIMRS FRONT OFFICE

</h2>

<hr style="margin-bottom:20px">

<div class="form-row">

<div>

<p><strong>Nama Pasien</strong></p>

<p><?= htmlspecialchars($data['nama']) ?></p>

</div>

<div>

<p><strong>No RM</strong></p>

<p><?= htmlspecialchars($data['no_rm']) ?></p>

</div>

</div>

<div class="form-row">

<div>

<p><strong>Dokter</strong></p>

<p><?= htmlspecialchars($data['nama_dokter']) ?></p>

</div>

<div>

<p><strong>Poli</strong></p>

<p><?= htmlspecialchars($data['poli']) ?></p>

</div>

</div>

<div class="form-row">

<div>

<p><strong>Tanggal Daftar</strong></p>

<p><?= date('d-m-Y H:i',strtotime($data['tgl_daftar'])) ?></p>

</div>

<div>

<p><strong>No Antrean</strong></p>

<p><?= htmlspecialchars($data['no_antrean']) ?></p>

</div>

</div>

<hr style="margin:25px 0">
<table style="width:100%;border-collapse:collapse">

    <thead>

        <tr style="background:#f1f5f9">

            <th style="padding:10px;text-align:left">Keterangan</th>

            <th style="padding:10px;text-align:right">Nominal</th>

        </tr>

    </thead>

    <tbody>

        <tr>

            <td style="padding:10px">Total Tagihan</td>

            <td style="padding:10px;text-align:right">

                Rp <?= number_format($data['total_biaya'],0,',','.') ?>

            </td>

        </tr>

        <tr>

            <td style="padding:10px">Diskon</td>

            <td style="padding:10px;text-align:right">

                Rp <?= number_format($data['diskon'] ?? 0,0,',','.') ?>

            </td>

        </tr>

        <tr>

            <td style="padding:10px">Penjamin</td>

            <td style="padding:10px;text-align:right">

                <?= htmlspecialchars($data['penjamin']) ?>

            </td>

        </tr>

        <tr>

            <td style="padding:10px">Metode Pembayaran</td>

            <td style="padding:10px;text-align:right">

                <?= htmlspecialchars($data['metode']) ?>

            </td>

        </tr>

        <tr>

            <td style="padding:10px">Jumlah Bayar</td>

            <td style="padding:10px;text-align:right">

                Rp <?= number_format($data['jumlah_bayar'] ?? 0,0,',','.') ?>

            </td>

        </tr>

        <tr>

            <td style="padding:10px">Kembalian</td>

            <td style="padding:10px;text-align:right">

                Rp <?= number_format($data['kembalian'] ?? 0,0,',','.') ?>

            </td>

        </tr>

        <tr style="background:#ecfdf5;font-weight:bold">

            <td style="padding:12px">

                Status Pembayaran

            </td>

            <td style="padding:12px;text-align:right">

                <<?= htmlspecialchars($data['status_bayar']) ?>

            </td>

        </tr>

    </tbody>

</table>

<hr style="margin:35px 0">

<div style="display:flex;justify-content:space-between">

    <div>

        <strong>Petugas Kasir</strong>

        <br><br><br><br>

        _______________________

    </div>

    <div style="text-align:right">

        <strong>Pasien</strong>

        <br><br><br><br>

        <?= htmlspecialchars($data['nama']) ?>

    </div>

</div>

</div>

</div>

</div>

<style>

@media print{

    .sidebar,
    .page-header .btn,
    .sidebar-footer{
        display:none !important;
    }

    .main-content{
        margin:0;
        padding:0;
    }

    .card{
        border:none;
        box-shadow:none;
    }

    body{
        background:#fff;
    }
}

</style>

<?php include '../includes/footer.php'; ?>