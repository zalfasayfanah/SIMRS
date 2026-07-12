<?php
// ============================================================
// pembayaran/index.php
// Billing / Pembayaran
// ============================================================

require_once '../config/database.php';

$basePath='../';
$pageTitle='Billing';

$pesan='';

if(isset($_GET['status'])){

    $pesan=match($_GET['status']){

        'tambah_ok'=>[
            'tipe'=>'success',
            'teks'=>'Pembayaran berhasil disimpan.'
        ],

        'edit_ok'=>[
            'tipe'=>'success',
            'teks'=>'Pembayaran berhasil diperbarui.'
        ],

        'hapus_ok'=>[
            'tipe'=>'success',
            'teks'=>'Pembayaran berhasil dihapus.'
        ],

        'gagal'=>[
            'tipe'=>'danger',
            'teks'=>'Terjadi kesalahan.'
        ],

        default=>''

    };

}

$cari=trim($_GET['cari'] ?? '');

$where='';

if($cari!=''){

    $cari=mysqli_real_escape_string($conn,$cari);

    $where="WHERE
        t.no_invoice LIKE '%$cari%'
        OR ps.nama LIKE '%$cari%'
        OR ps.no_rm LIKE '%$cari%'";

}

$sql="

SELECT

t.id_tagihan,
t.no_invoice,
t.total_biaya,
t.status_bayar,
t.penjamin,

ps.nama,
ps.no_rm

FROM tagihan t

JOIN pendaftaran p
ON t.id_pendaftaran=p.id_pendaftaran

JOIN pasien ps
ON p.id_pasien=ps.id_pasien

$where

ORDER BY t.tgl_tagihan DESC

";

$result=mysqli_query($conn,$sql);

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Billing / Pembayaran</h1>

<p>Kelola pembayaran pasien.</p>

</div>

<a href="bayar.php" class="btn btn-primary">

+ Pembayaran Baru

</a>

</div>

<?php if($pesan): ?>

<div class="alert alert-<?= $pesan['tipe']?>">

<?= $pesan['teks'] ?>

</div>

<?php endif; ?>

<div class="card">

<div class="card-body">

<form method="GET" style="display:flex;gap:10px">

<input

type="text"

name="cari"

value="<?= htmlspecialchars($cari) ?>"

placeholder="Cari Invoice / Nama / RM"

style="flex:1">

<button class="btn btn-primary">

Cari

</button>

<?php if($cari): ?>

<a href="index.php" class="btn btn-secondary">

Reset

</a>

<?php endif; ?>

</form>

</div>

</div>

<div class="card">

<div class="card-header">

<h2>Daftar Pembayaran</h2>

<span><?= mysqli_num_rows($result) ?> data</span>

</div>

<div class="table-wrap">

<table>

<thead>

<tr>

<th>No</th>

<th>Invoice</th>

<th>Pasien</th>

<th>No RM</th>

<th>Total</th>

<th>Status</th>

<th>Aksi</th>

</tr>

</thead>

<tbody>
    <?php

if(mysqli_num_rows($result)>0):

$no=1;

while($row=mysqli_fetch_assoc($result)):

switch($row['status_bayar']){

    case 'LUNAS':
        $badge='badge-success';
        break;

    case 'BELUM':
        $badge='badge-warning';
        break;

    default:
        $badge='badge-gray';
}

?>

<tr>

<td><?= $no++ ?></td>

<td>

<strong>

<?= htmlspecialchars($row['no_invoice']) ?>

</strong>

</td>

<td>

<?= htmlspecialchars($row['nama']) ?>

</td>

<td>

<?= htmlspecialchars($row['no_rm']) ?>

</td>

<td>

Rp <?= number_format($row['total_biaya'],0,',','.') ?>

</td>

<td>

<span class="badge <?= $badge ?>">

<?= htmlspecialchars($row['status_bayar']) ?>

</span>

</td>

<td>

<div style="display:flex;gap:6px">

<?php if($row['status_bayar']=='BELUM'): ?>

<a
href="bayar.php?id=<?= $row['id_tagihan'] ?>"
class="btn btn-success btn-sm">

Bayar

</a>

<?php else: ?>

<a
href="invoice.php?id=<?= $row['id_tagihan'] ?>"
class="btn btn-primary btn-sm">

Invoice

</a>

<?php endif; ?>

</div>

</td>

</tr>

<?php

endwhile;

else:

?>

<tr>

<td colspan="9" style="text-align:center;padding:35px;color:#64748B;">

Belum ada data pembayaran.

<br><br>

<a href="bayar.php" class="btn btn-primary">

+ Tambah Pembayaran

</a>

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include '../includes/footer.php'; ?>
<a
href="../pembayaran/bayar.php?id=<?= $row['id_tagihan'] ?>"
class="btn btn-success btn-sm">

Bayar

</a>