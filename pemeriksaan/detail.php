<?php
require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Detail Pemeriksaan';

$id = $_GET['id'] ?? 0;

$sql = "
SELECT p.*,
       ps.nama AS nama_pasien,
       ps.no_rm,
       d.nama_dokter
FROM pemeriksaan p
JOIN pendaftaran pd ON pd.id_pendaftaran = p.id_pendaftaran
JOIN pasien ps ON ps.id_pasien = pd.id_pasien
JOIN dokter d ON d.id_dokter = p.id_dokter
WHERE p.id_pemeriksaan = '$id'
";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Detail Pemeriksaan</h1>
            <p>Informasi lengkap hasil pemeriksaan pasien</p>
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <h2>Data Pemeriksaan</h2>
        </div>

        <div class="card-body">

            <table class="detail-table">

                <tr>
                    <th>No. Rekam Medis</th>
                    <td><?= htmlspecialchars($data['no_rm']) ?></td>
                </tr>

                <tr>
                    <th>Nama Pasien</th>
                    <td><?= htmlspecialchars($data['nama_pasien']) ?></td>
                </tr>

                <tr>
                    <th>Dokter Pemeriksa</th>
                    <td><?= htmlspecialchars($data['nama_dokter']) ?></td>
                </tr>

                <tr>
                    <th>Tanggal Pemeriksaan</th>
                    <td><?= date('d M Y H:i', strtotime($data['tgl_pemeriksaan'])) ?></td>
                </tr>

                <tr>
                    <th>Keluhan</th>
                    <td><?= nl2br(htmlspecialchars($data['keluhan'])) ?></td>
                </tr>

                <tr>
                    <th>Diagnosa</th>
                    <td><?= nl2br(htmlspecialchars($data['diagnosa'])) ?></td>
                </tr>

                <tr>
                    <th>Kode ICD-10</th>
                    <td><?= htmlspecialchars($data['icd_10']) ?></td>
                </tr>

                <tr>
                    <th>Rencana Tindakan</th>
                    <td><?= nl2br(htmlspecialchars($data['rencana_tindakan'])) ?></td>
                </tr>

                <tr>
                    <th>Tekanan Darah</th>
                    <td><?= htmlspecialchars($data['tekanan_darah']) ?></td>
                </tr>

                <tr>
                    <th>Suhu Tubuh</th>
                    <td><?= htmlspecialchars($data['suhu_tubuh']) ?> °C</td>
                </tr>

                <tr>
                    <th>Berat Badan</th>
                    <td><?= htmlspecialchars($data['berat_badan']) ?> kg</td>
                </tr>

                <tr>
                    <th>Tinggi Badan</th>
                    <td><?= htmlspecialchars($data['tinggi_badan']) ?> cm</td>
                </tr>

            </table>

            <div style="margin-top: 24px; display:flex; gap:10px;">

                <a href="index.php" class="btn btn-secondary">
                    ← Kembali
                </a>

                <a href="edit.php?id=<?= $data['id_pemeriksaan'] ?>"
                   class="btn btn-primary">
                    Edit Data
                </a>

            </div>

        </div>

    </div>

</div>

<style>
.detail-table{
    width:100%;
    border-collapse:collapse;
}

.detail-table th{
    width:250px;
    padding:14px;
    text-align:left;
    background:#f8fafc;
    border-bottom:1px solid #e5e7eb;
    vertical-align:top;
}

.detail-table td{
    padding:14px;
    border-bottom:1px solid #e5e7eb;
}

.detail-table tr:last-child th,
.detail-table tr:last-child td{
    border-bottom:none;
}
</style>

<?php include '../includes/footer.php'; ?>