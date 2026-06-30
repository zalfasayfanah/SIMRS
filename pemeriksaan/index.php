<?php
require_once '../config/database.php';

$pageTitle = 'Data Pemeriksaan';
$basePath = '../';

$sql = "
SELECT p.*,
       ps.nama AS nama_pasien,
       ps.no_rm,
       d.nama_dokter
FROM pemeriksaan p
JOIN pendaftaran pd ON pd.id_pendaftaran = p.id_pendaftaran
JOIN pasien ps ON ps.id_pasien = pd.id_pasien
JOIN dokter d ON d.id_dokter = p.id_dokter
ORDER BY p.tgl_pemeriksaan DESC
";

$result = mysqli_query($conn, $sql);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Pemeriksaan</h1>
            <p>Kelola data pemeriksaan pasien</p>
        </div>

        <a href="tambah.php" class="btn btn-primary">
            + Tambah Pemeriksaan
        </a>
    </div>

    <div class="card">
        <div class="table-wrap">

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No RM</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Diagnosa</th>
                        <th>ICD-10</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if(mysqli_num_rows($result) > 0): ?>

                    <?php while($row = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>
                            <?= date('d/m/Y H:i', strtotime($row['tgl_pemeriksaan'])) ?>
                        </td>

                        <td><?= htmlspecialchars($row['no_rm']) ?></td>

                        <td><?= htmlspecialchars($row['nama_pasien']) ?></td>

                        <td><?= htmlspecialchars($row['nama_dokter']) ?></td>

                        <td><?= htmlspecialchars($row['diagnosa']) ?></td>

                        <td><?= htmlspecialchars($row['icd_10']) ?></td>

                        <td>
                            <a href="detail.php?id=<?= $row['id_pemeriksaan'] ?>" class="btn btn-secondary btn-sm">Detail</a>

                            <a href="edit.php?id=<?= $row['id_pemeriksaan'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" style="text-align:center">
                            Belum ada data pemeriksaan
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>