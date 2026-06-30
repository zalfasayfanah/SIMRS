<?php
require_once '../config/database.php';

$basePath = '../';

if (isset($_POST['simpan'])) {

    $id_pemeriksaan = $_POST['id_pemeriksaan'];
    $id_obat = $_POST['id_obat'];
    $dosis = $_POST['dosis'];
    $jumlah = $_POST['jumlah'];
    $aturan = $_POST['aturan_pakai'];
    $status = $_POST['status_ambil'];

    mysqli_query($conn, "
        INSERT INTO resep(
            id_pemeriksaan,
            id_obat,
            dosis,
            jumlah,
            aturan_pakai,
            status_ambil,
            tgl_resep
        )
        VALUES(
            '$id_pemeriksaan',
            '$id_obat',
            '$dosis',
            '$jumlah',
            '$aturan',
            '$status',
            NOW()
        )
    ");

    header("Location:index.php");
}

$pemeriksaan = mysqli_query($conn, "
SELECT pm.id_pemeriksaan,
       ps.nama
FROM pemeriksaan pm
JOIN pendaftaran pd
ON pd.id_pendaftaran = pm.id_pendaftaran
JOIN pasien ps
ON ps.id_pasien = pd.id_pasien
");

$obat = mysqli_query($conn, "SELECT * FROM obat");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="card">

        <div class="card-header">
            <h2>Tambah Resep</h2>
        </div>

        <div class="card-body">

            <form method="POST">

                <label>Pasien Pemeriksaan</label>

                <select name="id_pemeriksaan">

                    <?php while ($p = mysqli_fetch_assoc($pemeriksaan)): ?>

                        <option value="<?= $p['id_pemeriksaan'] ?>">
                            <?= $p['nama'] ?>
                        </option>

                    <?php endwhile; ?>

                </select>

                <br><br>

                <label>Obat</label>

                <select name="id_obat">

                    <?php while ($o = mysqli_fetch_assoc($obat)): ?>

                        <option value="<?= $o['id_obat'] ?>">
                            <?= $o['nama_obat'] ?>
                        </option>

                    <?php endwhile; ?>

                </select>

                <br><br>

                <label>Dosis</label>
                <input type="text" name="dosis">

                <label>Jumlah</label>
                <input type="number" name="jumlah">

                <label>Aturan Pakai</label>
                <textarea name="aturan_pakai"></textarea>

                <label>Status Ambil</label>

                <select name="status_ambil">
                    <option value="BELUM">BELUM</option>
                    <option value="SUDAH">SUDAH</option>
                </select>

                <br><br>

                <button class="btn btn-primary" name="simpan">
                    Simpan Resep
                </button>

            </form>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>