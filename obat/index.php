<?php
// ============================================================
// obat/index.php
// Daftar Data Obat SIMRS
// ============================================================

require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Data Obat';

/* ===========================
   Notifikasi
=========================== */

$pesan = '';

if (isset($_GET['status'])) {

    $pesan = match ($_GET['status']) {

    'tambah_ok' => [
        'tipe'=>'success',
        'teks'=>'Data obat berhasil ditambahkan.'
    ],

    'edit_ok' => [
        'tipe'=>'success',
        'teks'=>'Data obat berhasil diperbarui.'
    ],

    'hapus_ok' => [
        'tipe'=>'success',
        'teks'=>'Data obat berhasil dihapus.'
    ],

    'gagal' => [
        'tipe'=>'danger',
        'teks'=>'Terjadi kesalahan saat menghapus data.'
    ],

    default => ''

};

}

/* ===========================
   Filter
=========================== */

$cari = trim($_GET['cari'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$where = [];

if ($cari != '') {

    $where[] =
    "(nama_obat LIKE '%".mysqli_real_escape_string($conn,$cari)."%')";

}

if ($kategori != '') {

    $where[] =
    "kategori='".mysqli_real_escape_string($conn,$kategori)."'";

}

$whereClause = '';

if(count($where)>0){

    $whereClause = "WHERE ".implode(" AND ",$where);

}

/* ===========================
   Query
=========================== */

$sql = "
SELECT *
FROM obat
$whereClause
ORDER BY nama_obat ASC
";

$result = mysqli_query($conn,$sql);

$listKategori = mysqli_query(
    $conn,
    "SELECT DISTINCT kategori
     FROM obat
     ORDER BY kategori ASC"
);

include '../includes/header.php';
include '../includes/sidebar.php';

?>

<div class="main-content">

    <div class="page-header">

        <div>

            <h1>Data Obat</h1>

            <p>Kelola data obat dan stok farmasi rumah sakit.</p>

        </div>

        <a href="tambah.php" class="btn btn-primary">

            + Tambah Obat

        </a>

    </div>

<?php if($pesan): ?>

<div class="alert alert-<?= $pesan['tipe']; ?>">

<?= $pesan['teks']; ?>

</div>

<?php endif; ?>

<div class="card" style="margin-bottom:20px;">

<div class="card-body">

<form method="GET"
style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">

<div style="flex:1;min-width:220px;">

<label>Cari Obat</label>

<input
type="text"
name="cari"
value="<?= htmlspecialchars($cari) ?>"
placeholder="Nama obat">

</div>

<div style="min-width:200px;">

<label>Kategori</label>

<select name="kategori">

<option value="">Semua Kategori</option>

<?php while($k=mysqli_fetch_assoc($listKategori)): ?>

<option
value="<?= htmlspecialchars($k['kategori']) ?>"
<?= ($kategori==$k['kategori'])?'selected':'';?>>

<?= htmlspecialchars($k['kategori']) ?>

</option>

<?php endwhile; ?>

</select>

</div>

<button
type="submit"
class="btn btn-primary">

Cari

</button>

<?php if($cari!='' || $kategori!=''): ?>

<a
href="index.php"
class="btn btn-secondary">

Reset

</a>

<?php endif; ?>

</form>

</div>

</div>

<div class="card">

<div class="card-header">

<h2>Daftar Obat</h2>

<span style="font-size:12px;color:var(--slate);">

<?= mysqli_num_rows($result) ?>

Data

</span>

</div>

<div class="table-wrap">

<table>

<thead>

<tr>

<th>No</th>

<th>Nama Obat</th>

<th>Kategori</th>

<th>Satuan</th>

<th>Harga</th>

<th>Stok</th>

<th>Minimal</th>

<th>Expired</th>

<th>Status</th>

<th>Aksi</th>

</tr>

</thead>

<tbody>
<?php

if(mysqli_num_rows($result) > 0):

$no = 1;

while($row = mysqli_fetch_assoc($result)):

?>

<tr>

    <td><?= $no++ ?></td>

    <td>

        <strong>

            <?= htmlspecialchars($row['nama_obat']) ?>

        </strong>

    </td>

    <td>

        <?= htmlspecialchars($row['kategori'] ?? '-') ?>

    </td>

    <td>

        <?= htmlspecialchars($row['satuan'] ?? '-') ?>

    </td>

    <td>

        Rp <?= number_format($row['harga'],0,',','.') ?>

    </td>

    <td>

        <?= $row['stok'] ?>

    </td>

    <td>

        <?= $row['minimal_stok'] ?>

    </td>

    <td>

        <?php

        if(!empty($row['expired_date'])){

            echo date('d/m/Y',strtotime($row['expired_date']));

        }else{

            echo '-';

        }

        ?>

    </td>

    <td>

        <?php

        $status = 'Aman';
        $badge = 'badge-success';

        if(
            !empty($row['expired_date']) &&
            strtotime($row['expired_date']) < strtotime(date('Y-m-d'))
        ){

            $status = 'Expired';
            $badge = 'badge-danger';

        }
        elseif($row['stok'] <= $row['minimal_stok']){

            $status = 'Stok Minim';
            $badge = 'badge-warning';

        }

        ?>

        <span class="badge <?= $badge ?>">

            <?= $status ?>

        </span>

    </td>

    <td>

        <div style="display:flex;gap:6px;">

            <a
            href="edit.php?id=<?= $row['id_obat'] ?>"
            class="btn btn-secondary btn-sm">

                Edit

            </a>

            <a
            href="hapus.php?id=<?= $row['id_obat'] ?>"
            class="btn btn-danger btn-sm"
            onclick="return confirm('Yakin ingin menghapus obat <?= htmlspecialchars(addslashes($row['nama_obat'])) ?> ?')">

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

<td colspan="10"
style="text-align:center;padding:35px;color:var(--slate);">

Belum ada data obat.

<br><br>

<a
href="tambah.php"
class="btn btn-primary">

+ Tambah Obat

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