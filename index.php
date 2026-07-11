<?php
// ============================================================
// index.php — Dashboard Utama SIMRS Front Office
// ============================================================

require_once 'config/database.php';

$basePath = '';
$pageTitle = 'Dashboard';

// Ambil statistik ringkas
$totalPasien = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pasien"))[0] ?? 0;
$totalDokter = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM dokter WHERE status_aktif = 1"))[0] ?? 0;
$daftarHariIni = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE DATE(tgl_daftar) = CURDATE()"))[0] ?? 0;
$menunggu = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE DATE(tgl_daftar) = CURDATE() AND status_daftar = 'MENUNGGU'"))[0] ?? 0;

//PEMERIKSAAN
$totalPemeriksaan = mysqli_fetch_row(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) FROM pemeriksaan
         WHERE DATE(tgl_pemeriksaan)=CURDATE()"
    )
)[0] ?? 0;

// Ambil daftar pendaftaran hari ini (10 terbaru)
$sqlHariIni = "
    SELECT p.no_antrean, ps.nama AS nama_pasien, ps.no_rm,
           d.nama_dokter, p.poli, p.status_daftar, p.tgl_daftar,
           p.jenis_penjamin
    FROM pendaftaran p
    JOIN pasien ps ON ps.id_pasien = p.id_pasien
    JOIN dokter d  ON d.id_dokter  = p.id_dokter
    WHERE DATE(p.tgl_daftar) = CURDATE()
    ORDER BY p.tgl_daftar DESC
    LIMIT 10
";
$hasilHariIni = mysqli_query($conn, $sqlHariIni);

include 'includes/header.php';
include 'includes/sidebar.php';



$sqlPemeriksaan = "
SELECT p.id_pemeriksaan,
       ps.nama AS nama_pasien,
       d.nama_dokter,
       p.diagnosa,
       p.tgl_pemeriksaan
FROM pemeriksaan p
JOIN pendaftaran pd
ON pd.id_pendaftaran = p.id_pendaftaran
JOIN pasien ps
ON ps.id_pasien = pd.id_pasien
JOIN dokter d
ON d.id_dokter = p.id_dokter
ORDER BY p.tgl_pemeriksaan DESC
LIMIT 5
";

$dataPemeriksaan = mysqli_query($conn, $sqlPemeriksaan);


?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Selamat datang di SIMRS Front Office &mdash; <?= date('l, d F Y') ?></p>
        </div>
    </div>

    <!-- Stat cards -->
    <div class="stat-grid">
        <div class="stat-card accent">
            <div class="stat-label">Pendaftaran hari ini</div>
            <div class="stat-value"><?= $daftarHariIni ?></div>
            <div class="stat-sub"><?= $menunggu ?> masih menunggu</div>
        </div>
        <div class="stat-card info">
            <div class="stat-label">Total pasien terdaftar</div>
            <div class="stat-value"><?= $totalPasien ?></div>
            <div class="stat-sub">seluruh data rekam medis</div>
        </div>
        <div class="stat-card warn">
            <div class="stat-label">Dokter aktif</div>
            <div class="stat-value"><?= $totalDokter ?></div>
            <div class="stat-sub">siap melayani hari ini</div>
        </div>
        <div class="stat-card success">
            <div class="stat-label">Pemeriksaan hari ini</div>
            <div class="stat-value"><?= $totalPemeriksaan ?></div>
            <div class="stat-sub">pasien telah diperiksa</div>
        </div>
    </div>

    <!-- Tabel pendaftaran hari ini -->
    <div class="card">
        <div class="card-header">
            <h2>Pendaftaran hari ini</h2>
            <a href="pages/pendaftaran/daftar.php" class="btn btn-primary btn-sm">+ Pendaftaran baru</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Antrean</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Dokter / Poli</th>
                        <th>Penjamin</th>
                        <th>Jam Daftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($hasilHariIni && mysqli_num_rows($hasilHariIni) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($hasilHariIni)): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['no_antrean']) ?></strong></td>
                                <td><?= htmlspecialchars($row['no_rm']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['nama_dokter']) ?>
                                    <div style="font-size:11.5px;color:var(--slate)"><?= htmlspecialchars($row['poli']) ?></div>
                                </td>
                                <td>
                                    <?php
                                    $penjamin = $row['jenis_penjamin'] ?? 'Umum';
                                    $badgeClass = match ($penjamin) {
                                        'BPJS' => 'badge-info',
                                        'Asuransi' => 'badge-warning',
                                        default => 'badge-gray',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($penjamin) ?></span>
                                </td>
                                <td><?= date('H:i', strtotime($row['tgl_daftar'])) ?></td>
                                <td>
                                    <?php
                                    $status = $row['status_daftar'];
                                    $statusClass = match ($status) {
                                        'MENUNGGU' => 'badge-warning',
                                        'DIPANGGIL' => 'badge-info',
                                        'SELESAI' => 'badge-success',
                                        'BATAL' => 'badge-danger',
                                        default => 'badge-gray',
                                    };
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst(strtolower($status)) ?></span>
                                </td>
                                <td>
                                    <a href="pages/pendaftaran/index.php" class="btn btn-secondary btn-sm">Lihat</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:2rem;color:var(--slate)">
                                Belum ada pendaftaran hari ini.<br>
                                <a href="pages/pendaftaran/daftar.php" class="btn btn-primary btn-sm"
                                    style="margin-top:0.75rem">
                                    + Tambah pendaftaran
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Pemeriksaan Terbaru</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Diagnosa</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($row = mysqli_fetch_assoc($dataPemeriksaan)): ?>

                        <tr>
                            <td><?= $row['nama_pasien']; ?></td>
                            <td><?= $row['nama_dokter']; ?></td>
                            <td><?= $row['diagnosa']; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tgl_pemeriksaan'])); ?></td>
                        </tr>

                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Link cepat -->
    <div class="card">
        <div class="card-header">
            <h2>Akses cepat</h2>
        </div>
        <div class="card-body" style="display:flex;gap:0.75rem;flex-wrap:wrap">
            <a href="pasien/tambah.php" class="btn btn-secondary">+ Pasien baru</a>
            <a href="dokter/index.php" class="btn btn-secondary">Daftar dokter</a>
            <a href="pendaftaran/index.php" class="btn btn-secondary">Semua pendaftaran</a>
            <a href="pages/pemeriksaan/index.php" class="btn btn-secondary">Pemeriksaan Pasien</a>
            <a href="pages/resep/index.php" class="btn btn-secondary">Kelola Resep</a>
        </div>
    </div>

</div><!-- /.main-content -->


<?php include 'includes/footer.php'; ?>
