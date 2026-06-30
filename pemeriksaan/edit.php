<?php
require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Edit Pemeriksaan';

$id = $_GET['id'] ?? 0;

if (isset($_POST['update'])) {

    $keluhan             = mysqli_real_escape_string($conn, $_POST['keluhan']);
    $diagnosa            = mysqli_real_escape_string($conn, $_POST['diagnosa']);
    $icd_10              = mysqli_real_escape_string($conn, $_POST['icd_10']);
    $rencana_tindakan    = mysqli_real_escape_string($conn, $_POST['rencana_tindakan']);
    $tekanan_darah       = mysqli_real_escape_string($conn, $_POST['tekanan_darah']);
    $suhu_tubuh          = mysqli_real_escape_string($conn, $_POST['suhu_tubuh']);
    $berat_badan         = mysqli_real_escape_string($conn, $_POST['berat_badan']);
    $tinggi_badan        = mysqli_real_escape_string($conn, $_POST['tinggi_badan']);

    mysqli_query($conn, "
        UPDATE pemeriksaan SET
            keluhan            = '$keluhan',
            diagnosa           = '$diagnosa',
            icd_10             = '$icd_10',
            rencana_tindakan   = '$rencana_tindakan',
            tekanan_darah      = '$tekanan_darah',
            suhu_tubuh         = '$suhu_tubuh',
            berat_badan        = '$berat_badan',
            tinggi_badan       = '$tinggi_badan'
        WHERE id_pemeriksaan = '$id'
    ");

    header("Location: index.php");
    exit;
}

$data = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM pemeriksaan WHERE id_pemeriksaan = '$id'"
    )
);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Edit Pemeriksaan</h1>
            <p>Perbarui data hasil pemeriksaan pasien</p>
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <h2>Form Edit Pemeriksaan</h2>
        </div>

        <div class="card-body">

            <form method="POST" class="form-grid">

                <div class="form-group">
                    <label>Keluhan</label>
                    <textarea name="keluhan" rows="4" required><?= htmlspecialchars($data['keluhan']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Diagnosa</label>
                    <textarea name="diagnosa" rows="4" required><?= htmlspecialchars($data['diagnosa']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Kode ICD-10</label>
                    <input type="text"
                           name="icd_10"
                           value="<?= htmlspecialchars($data['icd_10']) ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Rencana Tindakan</label>
                    <textarea name="rencana_tindakan" rows="3"><?= htmlspecialchars($data['rencana_tindakan']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Tekanan Darah</label>
                    <input type="text"
                           name="tekanan_darah"
                           placeholder="Contoh: 120/80 mmHg"
                           value="<?= htmlspecialchars($data['tekanan_darah']) ?>">
                </div>

                <div class="form-group">
                    <label>Suhu Tubuh (°C)</label>
                    <input type="number"
                           step="0.1"
                           name="suhu_tubuh"
                           value="<?= htmlspecialchars($data['suhu_tubuh']) ?>">
                </div>

                <div class="form-group">
                    <label>Berat Badan (kg)</label>
                    <input type="number"
                           step="0.1"
                           name="berat_badan"
                           value="<?= htmlspecialchars($data['berat_badan']) ?>">
                </div>

                <div class="form-group">
                    <label>Tinggi Badan (cm)</label>
                    <input type="number"
                           step="0.1"
                           name="tinggi_badan"
                           value="<?= htmlspecialchars($data['tinggi_badan']) ?>">
                </div>

                <div class="form-action">
                    <a href="index.php" class="btn btn-secondary">
                        ← Kembali
                    </a>

                    <button type="submit"
                            name="update"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

<style>
.form-grid{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group label{
    margin-bottom:8px;
    font-weight:600;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    font-size:14px;
}

.form-group textarea{
    resize:vertical;
}

.form-action{
    margin-top:20px;
    display:flex;
    gap:10px;
}
</style>

<?php include '../includes/footer.php'; ?>