<?php
require_once '../config/database.php';

$pageTitle = 'Data Pemeriksaan';
$basePath = '../';

$id_dokter_terpilih = isset($_GET['id_dokter']) ? intval($_GET['id_dokter']) : 0;

// Daftar dokter
$dokterResult = mysqli_query($conn, "
    SELECT id_dokter, nama_dokter
    FROM dokter
    ORDER BY nama_dokter
");

// Pasien menunggu
$pasienMenunggu = null;

if ($id_dokter_terpilih > 0) {

    $sql = "
    SELECT
        pd.id_pendaftaran,
        pd.tgl_daftar,
        pd.status_daftar,
        ps.no_rm,
        ps.nama AS nama_pasien
    FROM pendaftaran pd
    JOIN pasien ps
        ON ps.id_pasien = pd.id_pasien
    WHERE pd.id_dokter = ?
      AND pd.status_daftar = 'MENUNGGU'
    ORDER BY pd.tgl_daftar ASC
    ";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_dokter_terpilih);
    mysqli_stmt_execute($stmt);

    $pasienMenunggu = mysqli_stmt_get_result($stmt);
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Pemeriksaan</h1>
            <p>Daftar pasien yang menunggu pemeriksaan.</p>
        </div>
    </div>

    <div class="card">

        <div class="card-header">
            <h3>Pilih Dokter</h3>

            <form method="GET">
                <select
                    name="id_dokter"
                    class="form-control"
                    onchange="this.form.submit()">

                    <option value="">-- Pilih Dokter --</option>

                    <?php while($d = mysqli_fetch_assoc($dokterResult)): ?>

                    <option
                        value="<?= $d['id_dokter'] ?>"
                        <?= $id_dokter_terpilih == $d['id_dokter'] ? 'selected' : '' ?>>

                        <?= htmlspecialchars($d['nama_dokter']) ?>

                    </option>

                    <?php endwhile; ?>

                </select>
            </form>

        </div>

        <?php if($id_dokter_terpilih > 0): ?>

        <div class="table-wrap">

            <table>

                <thead>

                <tr>

                    <th>No RM</th>
                    <th>Nama Pasien</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

                </thead>

                <tbody>

                <?php if(mysqli_num_rows($pasienMenunggu)>0): ?>

                    <?php while($row=mysqli_fetch_assoc($pasienMenunggu)): ?>

                    <tr>

                        <td><?= $row['no_rm'] ?></td>

                        <td><?= htmlspecialchars($row['nama_pasien']) ?></td>

                        <td><?= date('d/m/Y H:i',strtotime($row['tgl_daftar'])) ?></td>

                        <td>

                            <span class="badge badge-warning">

                                <?= $row['status_daftar'] ?>

                            </span>

                        </td>

                        <td>

                            <a
                                href="pilih.php?id_pendaftaran=<?= $row['id_pendaftaran'] ?>&id_dokter=<?= $id_dokter_terpilih ?>"
                                class="btn btn-primary btn-sm">

                                Periksa

                            </a>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5" style="text-align:center">

                            Tidak ada pasien menunggu.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

        <?php endif; ?>

    </div>

</div>

<?php include '../includes/footer.php'; ?>