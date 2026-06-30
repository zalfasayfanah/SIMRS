<?php
require_once '../config/database.php';

$pageTitle = 'Tambah Pemeriksaan';
$basePath = '../';

if (isset($_POST['simpan'])) {

    $id_pendaftaran = $_POST['id_pendaftaran'];
    $id_dokter = $_POST['id_dokter'];
    $keluhan = $_POST['keluhan'];
    $diagnosa = $_POST['diagnosa'];
    $icd_10 = $_POST['icd_10'];
    $rencana = $_POST['rencana_tindakan'];
    $td = $_POST['tekanan_darah'];
    $suhu = $_POST['suhu_tubuh'];
    $bb = $_POST['berat_badan'];
    $tb = $_POST['tinggi_badan'];

    mysqli_query($conn, "
        INSERT INTO pemeriksaan(
            id_pendaftaran,
            id_dokter,
            keluhan,
            diagnosa,
            icd_10,
            rencana_tindakan,
            tgl_pemeriksaan,
            tekanan_darah,
            suhu_tubuh,
            berat_badan,
            tinggi_badan
        )
        VALUES(
            '$id_pendaftaran',
            '$id_dokter',
            '$keluhan',
            '$diagnosa',
            '$icd_10',
            '$rencana',
            NOW(),
            '$td',
            '$suhu',
            '$bb',
            '$tb'
        )
    ");

    header("Location:index.php");
    exit;
}

$pasien = mysqli_query($conn, "
SELECT pd.id_pendaftaran, ps.nama, ps.no_rm
FROM pendaftaran pd
JOIN pasien ps ON ps.id_pasien=pd.id_pasien
");

$dokter = mysqli_query($conn, "SELECT * FROM dokter");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="card">

        <div class="card-header">
            <h2>Tambah Pemeriksaan</h2>
        </div>

        <div class="card-body">

            <form method="POST">

                <label>Pasien</label>
                <select name="id_pendaftaran" required>

                    <?php while ($p = mysqli_fetch_assoc($pasien)): ?>

                        <option value="<?= $p['id_pendaftaran'] ?>">
                            <?= $p['no_rm'] ?> - <?= $p['nama'] ?>
                        </option>

                    <?php endwhile; ?>

                </select>

                <br><br>

                <label>Dokter</label>

                <select name="id_dokter">

                    <?php while ($d = mysqli_fetch_assoc($dokter)): ?>

                        <option value="<?= $d['id_dokter'] ?>">
                            <?= $d['nama_dokter'] ?>
                        </option>

                    <?php endwhile; ?>

                </select>

                <br><br>

                <label>Keluhan</label>
                <textarea name="keluhan"></textarea>

                <label>Diagnosa</label>
                <textarea name="diagnosa"></textarea>

                <label>ICD 10</label>
                <input type="text" name="icd_10">

                <label>Rencana Tindakan</label>
                <textarea name="rencana_tindakan"></textarea>

                <label>Tekanan Darah</label>
                <input type="text" name="tekanan_darah">

                <label>Suhu Tubuh</label>
                <input type="number" step="0.1" name="suhu_tubuh">

                <label>Berat Badan</label>
                <input type="number" step="0.1" name="berat_badan">

                <label>Tinggi Badan</label>
                <input type="number" step="0.1" name="tinggi_badan">

                <br><br>

                <button class="btn btn-primary" name="simpan">
                    Simpan
                </button>

            </form>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>