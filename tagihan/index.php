<?php
// ============================================================
// tagihan/index.php
// Daftar Tagihan SIMRS
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Tagihan';

//==============================
// Pesan
//==============================
$pesan = '';

if (isset($_GET['status'])) {

    $pesan = match ($_GET['status']) {

        'tambah_ok' => [
            'tipe'=>'success',
            'teks'=>'Tagihan berhasil ditambahkan.'
        ],

        'edit_ok' => [
            'tipe'=>'success',
            'teks'=>'Tagihan berhasil diperbarui.'
        ],

        'hapus_ok' => [
            'tipe'=>'success',
            'teks'=>'Tagihan berhasil dihapus.'
        ],

        'gagal' => [
            'tipe'=>'danger',
            'teks'=>'Terjadi kesalahan.'
        ],

        default => ''

    };

}

//==============================
// Filter
//==============================

$cari = trim($_GET['cari'] ?? '');

$status = trim($_GET['status_bayar'] ?? '');

$where = [];

if ($cari != '') {

    $cariEsc = mysqli_real_escape_string($conn,$cari);

    $where[] = "(
        t.no_invoice LIKE '%$cariEsc%'
        OR ps.nama LIKE '%$cariEsc%'
        OR ps.no_rm LIKE '%$cariEsc%'
    )";

}

if ($status != '') {

    $statusEsc = mysqli_real_escape_string($conn,$status);

    $where[] = "t.status_bayar='$statusEsc'";

}

$whereClause = '';

if(count($where)>0){

    $whereClause='WHERE '.implode(' AND ',$where);

}
$sql = "

SELECT

t.*,

ps.nama,

ps.no_rm,

p.no_antrean,

p.jenis_penjamin

FROM tagihan t

JOIN pendaftaran p
ON t.id_pendaftaran=p.id_pendaftaran

JOIN pasien ps
ON p.id_pasien=ps.id_pasien

$whereClause

ORDER BY t.tgl_tagihan DESC

";

$result = mysqli_query($conn,$sql);

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">

<div class="page-header">

<div>

<h1>Data Tagihan</h1>

<p>Kelola seluruh tagihan pasien.</p>

</div>

<a href="tambah.php" class="btn btn-primary">

+ Tambah Tagihan

</a>

</div>

<?php if($pesan): ?>

<div class="alert alert-<?= $pesan['tipe'] ?>">

<?= $pesan['teks'] ?>

</div>

<?php endif; ?>

<div class="card" style="margin-bottom:20px;">

<div class="card-body">

<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">

<input

type="text"

name="cari"

placeholder="Cari invoice / pasien / no RM"

value="<?= htmlspecialchars($cari) ?>"

style="flex:1">

<select name="status_bayar">

<option value="">Semua Status</option>

<option value="BELUM" <?= $status=='BELUM'?'selected':'' ?>>BELUM</option>

<option value="LUNAS" <?= $status=='LUNAS'?'selected':'' ?>>LUNAS</option>

<option value="BATAL" <?= $status=='BATAL'?'selected':'' ?>>BATAL</option>

</select>

<button class="btn btn-primary">

Cari

</button>

<?php if($cari || $status): ?>

<a href="index.php" class="btn btn-secondary">

Reset

</a>

<?php endif; ?>

</form>

</div>

</div>

<div class="card">

<div class="card-header">

<h2>Daftar Tagihan</h2>

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

<th>Antrean</th>

<th>Total</th>

<th>Penjamin</th>

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

    case 'BATAL':
        $badge='badge-danger';
        break;

    default:
        $badge='badge-gray';

}

?>

<tr>

<td><?= $no++ ?></td>

<td>

<strong><?= htmlspecialchars($row['no_invoice']) ?></strong>

</td>

<td>

<?= htmlspecialchars($row['nama']) ?>

</td>

<td>

<?= htmlspecialchars($row['no_rm']) ?>

</td>

<td>

<?= htmlspecialchars($row['no_antrean']) ?>

</td>

<td>

Rp <?= number_format($row['total_biaya'],0,',','.') ?>

</td>

<td>

<?= htmlspecialchars($row['penjamin']) ?>

</td>

<td>

<div style="display:flex;gap:6px;flex-wrap:wrap">

<?php if($row['status_bayar'] == 'BELUM'): ?>

<a
href="../pembayaran/bayar.php?id=<?= $row['id_tagihan']; ?>"
class="btn btn-success btn-sm">

Bayar

</a>

<?php endif; ?>

<a
href="edit.php?id=<?= $row['id_tagihan']; ?>"
class="btn btn-secondary btn-sm">

Edit

</a>

<a
href="hapus.php?id=<?= $row['id_tagihan']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Hapus tagihan ini?')">

Hapus

</a>

</div>

</td>

</tr>

<?php

endwhile;

else:

?>

<tr>

<td colspan="9" style="text-align:center;padding:35px;color:#64748B;">

Belum ada data tagihan.

<br><br>

<a href="tambah.php" class="btn btn-primary">

+ Tambah Tagihan

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