<?php
require_once '../config/database.php';

$pageTitle = "Detail Resep";
$basePath = "../";
// Amankan parameter ID dari URL
$id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "
SELECT r.*,
       o.nama_obat,
       ps.nama AS nama_pasien
FROM resep r
JOIN pemeriksaan pm ON pm.id_pemeriksaan=r.id_pemeriksaan
JOIN pendaftaran pd ON pd.id_pendaftaran=pm.id_pendaftaran
JOIN pasien ps ON ps.id_pasien=pd.id_pasien
JOIN obat o ON o.id_obat=r.id_obat
WHERE r.id_resep='$id'
";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// Proteksi jika ID resep tidak ditemukan di database
if (!$data) {
    echo "<script>alert('Data resep tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="card">

        <div class="card-header">
            <h2>Detail Resep</h2>
        </div>

        <div class="card-body">

            <p><b>Pasien :</b> <?= $data['nama_pasien'] ?></p>
            <p><b>Obat :</b> <?= $data['nama_obat'] ?></p>
            <p><b>Dosis :</b> <?= $data['dosis'] ?></p>
            <p><b>Jumlah :</b> <?= $data['jumlah'] ?></p>
            <p><b>Aturan Pakai :</b> <?= $data['aturan_pakai'] ?></p>
            <p><b>Status :</b> <?= $data['status_ambil'] ?></p>

            <a href="index.php" class="btn btn-secondary">
                Kembali
            </a>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>