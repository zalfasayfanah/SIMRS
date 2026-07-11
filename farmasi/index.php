<?php
require_once '../config/database.php';

$basePath = '../';
$pageTitle = 'Data Resep';

$sql = "
SELECT r.*,
       ps.nama AS nama_pasien,
       ps.no_rm,
       o.nama_obat,
       p.diagnosa
FROM resep r
JOIN pemeriksaan pm ON pm.id_pemeriksaan = r.id_pemeriksaan
JOIN pendaftaran pd ON pd.id_pendaftaran = pm.id_pendaftaran
JOIN pasien ps ON ps.id_pasien = pd.id_pasien
JOIN obat o ON o.id_obat = r.id_obat
JOIN pemeriksaan p ON p.id_pemeriksaan = r.id_pemeriksaan
ORDER BY r.tgl_resep DESC
";

$result = mysqli_query($conn, $sql);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Data Resep</h1>
            <p>Kelola resep pasien rawat jalan</p>
        </div>

        <!-- <a href="tambah.php" class="btn btn-primary">
            + Tambah Resep
        </a> -->
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No RM</th>
                        <th>Pasien</th>
                        <th>Obat</th>
                        <th>Dosis</th>
                        <th>Jumlah</th>
                        <th>Status Ambil</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if(mysqli_num_rows($result) > 0): ?>

                <?php while($row = mysqli_fetch_assoc($result)): ?>

                <tr>

                    <td>
                        <?= date('d/m/Y', strtotime($row['tgl_resep'])) ?>
                    </td>

                    <td><?= $row['no_rm'] ?></td>

                    <td><?= $row['nama_pasien'] ?></td>

                    <td><?= $row['nama_obat'] ?></td>

                    <td><?= $row['dosis'] ?></td>

                    <td><?= $row['jumlah'] ?></td>

                    <td>

                    <?php
                    $badge = $row['status_ambil'] == 'SUDAH'
                            ? 'badge-success'
                            : 'badge-warning';
                    ?>

                    <span class="badge <?= $badge ?>">
                        <?= $row['status_ambil'] ?>
                    </span>

                    </td>

                    <td>

                        <a href="detail.php?id=<?= $row['id_resep'] ?>"
                           class="btn btn-secondary btn-sm">
                            Detail
                        </a>

                        <a href="edit.php?id=<?= $row['id_resep'] ?>"
                           class="btn btn-primary btn-sm">
                            Edit
                        </a>

                    </td>

                </tr>

                <?php endwhile; ?>

                <?php else: ?>

                <tr>
                    <td colspan="8" style="text-align:center">
                        Belum ada data resep
                    </td>
                </tr>

                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>