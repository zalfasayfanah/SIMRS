<?php
// ============================================================
// pendaftaran/index.php — Daftar Pendaftaran
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Pendaftaran';

$pesan = '';
if (isset($_GET['status'])) {
    $pesan = match($_GET['status']) {
        'tambah_ok' => ['tipe' => 'success', 'teks' => 'Pendaftaran berhasil disimpan.'],
        'edit_ok'   => ['tipe' => 'success', 'teks' => 'Data pendaftaran berhasil diperbarui.'],
        'hapus_ok'  => ['tipe' => 'success', 'teks' => 'Data pendaftaran berhasil dihapus.'],
        'gagal'     => ['tipe' => 'danger',  'teks' => 'Terjadi kesalahan, silakan coba lagi.'],
        default     => ''
    };
}

// Filter tanggal — default hari ini
$filterTgl  = $_GET['tgl']    ?? date('Y-m-d');
$filterPoli = trim($_GET['poli']   ?? '');
$filterStatus = trim($_GET['status_daftar'] ?? '');

$where = ["DATE(p.tgl_daftar) = '" . mysqli_real_escape_string($conn, $filterTgl) . "'"];
if ($filterPoli   !== '') $where[] = "p.poli = '"          . mysqli_real_escape_string($conn, $filterPoli) . "'";
if ($filterStatus !== '') $where[] = "p.status_daftar = '" . mysqli_real_escape_string($conn, $filterStatus) . "'";
$whereClause = 'WHERE ' . implode(' AND ', $where);

$sql = "
    SELECT p.*, ps.nama AS nama_pasien, ps.no_rm,
           d.nama_dokter, d.spesialis
    FROM pendaftaran p
    JOIN pasien ps ON ps.id_pasien = p.id_pasien
    JOIN dokter d  ON d.id_dokter  = p.id_dokter
    $whereClause
    ORDER BY p.no_antrean ASC
";
$result = mysqli_query($conn, $sql);

// List poli untuk filter dropdown
$poliList = mysqli_query($conn, "SELECT DISTINCT poli FROM pendaftaran ORDER BY poli ASC");

// Stat hari ini
$statMenunggu  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE DATE(tgl_daftar) = '$filterTgl' AND status_daftar = 'MENUNGGU'"))[0]  ?? 0;
$statDiperiksa = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE DATE(tgl_daftar) = '$filterTgl' AND status_daftar = 'Diperiksa'"))[0] ?? 0;
$statSelesai   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE DATE(tgl_daftar) = '$filterTgl' AND status_daftar = 'SELESAI'"))[0]   ?? 0;
$statTotal     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pendaftaran WHERE DATE(tgl_daftar) = '$filterTgl'"))[0] ?? 0;

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Pendaftaran Pasien</h1>
            <p>Manajemen antrian dan pendaftaran rawat jalan</p>
        </div>
        <div style="display:flex;gap:0.75rem">
            <a href="../pasien/tambah.php" class="btn btn-secondary">+ Pasien Baru</a>
            <a href="daftar.php" class="btn btn-primary">+ Pasien Lama </a>
        </div>
    </div>

    <?php if ($pesan): ?>
    <div class="alert alert-<?= $pesan['tipe'] ?>"><?= $pesan['teks'] ?></div>
    <?php endif; ?>

    <!-- Stat mini -->
    <div class="stat-grid" style="margin-bottom:1.25rem">
        <div class="stat-card">
            <div class="stat-label">Total hari ini</div>
            <div class="stat-value"><?= $statTotal ?></div>
        </div>
        <div class="stat-card warn">
            <div class="stat-label">Menunggu</div>
            <div class="stat-value"><?= $statMenunggu ?></div>
        </div>
        <div class="stat-card info">
            <div class="stat-label">Diperiksa</div>
            <div class="stat-value"><?= $statDiperiksa ?></div>
        </div>
        <div class="stat-card accent">
            <div class="stat-label">Selesai</div>
            <div class="stat-value"><?= $statSelesai ?></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-body" style="padding:1rem 1.5rem">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">
                <div>
                    <label style="font-size:12px;font-weight:500;color:var(--slate);display:block;margin-bottom:4px">Tanggal</label>
                    <input type="date" name="tgl" value="<?= htmlspecialchars($filterTgl) ?>"
                           style="padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                </div>
                <div style="min-width:140px">
                    <label style="font-size:12px;font-weight:500;color:var(--slate);display:block;margin-bottom:4px">Poli</label>
                    <select name="poli" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                        <option value="">Semua poli</option>
                        <?php while ($p = mysqli_fetch_assoc($poliList)): ?>
                        <option value="<?= htmlspecialchars($p['poli']) ?>" <?= $filterPoli === $p['poli'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['poli']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="min-width:140px">
                    <label style="font-size:12px;font-weight:500;color:var(--slate);display:block;margin-bottom:4px">Status</label>
                    <select name="status_daftar" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                        <option value="">Semua status</option>
                        <?php foreach (['MENUNGGU','Diperiksa','SELESAI','BATAL'] as $st): ?>
                        <option value="<?= $st ?>" <?= $filterStatus === $st ? 'selected' : '' ?>><?= ucfirst(strtolower($st)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="align-self:flex-end">Tampilkan</button>
                <a href="index.php" class="btn btn-secondary" style="align-self:flex-end">Hari ini</a>
            </form>
        </div>
    </div>

    <!-- Tabel antrian -->
    <div class="card">
        <div class="card-header">
            <h2>Daftar antrian — <?= date('d F Y', strtotime($filterTgl)) ?></h2>
            <span style="font-size:12px;color:var(--slate)"><?= mysqli_num_rows($result) ?> pendaftaran</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:80px">Antrean</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Dokter / Poli</th>
                        <th>Jenis Kunjungan</th>
                        <th>Penjamin</th>
                        <th>Jam Daftar</th>
                        <th>Status</th>
                        <th style="width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td style="text-align:center">
                        <strong style="font-size:16px"><?= htmlspecialchars($row['no_antrean']) ?></strong>
                    </td>
                    <td><?= htmlspecialchars($row['no_rm']) ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_pasien']) ?></strong></td>
                    <td>
                        <?= htmlspecialchars($row['nama_dokter']) ?>
                        <div style="font-size:11.5px;color:var(--slate)"><?= htmlspecialchars($row['poli']) ?></div>
                    </td>
                    <td>
                        <?php $jk = $row['jenis_kunjungan'] ?? 'Baru'; ?>
                        <span class="badge <?= $jk === 'Baru' ? 'badge-info' : 'badge-gray' ?>"><?= $jk ?></span>
                    </td>
                    <td>
                        <?php
                        $penjamin = $row['jenis_penjamin'] ?? 'Umum';
                        $pc = match($penjamin) { 'BPJS' => 'badge-info', 'Asuransi' => 'badge-warning', default => 'badge-gray' };
                        echo '<span class="badge ' . $pc . '">' . htmlspecialchars($penjamin) . '</span>';
                        if ($penjamin === 'BPJS' && !empty($row['no_kartu_bpjs'])):
                        ?>
                        <div style="font-size:11px;color:var(--slate)"><?= htmlspecialchars($row['no_kartu_bpjs']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= date('H:i', strtotime($row['tgl_daftar'])) ?></td>
                    <td>
                        <?php
                        $st = $row['status_daftar'];
                        $sc = match($st) {
                            'MENUNGGU'  => 'badge-warning',
                            'Diperiksa' => 'badge-info',
                            'SELESAI'   => 'badge-success',
                            'BATAL'     => 'badge-danger',
                            default     => 'badge-gray'
                        };
                        ?>
                        <span class="badge <?= $sc ?>"><?= ucfirst(strtolower($st)) ?></span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <a href="edit.php?id=<?= $row['id_pendaftaran'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= $row['id_pendaftaran'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus pendaftaran ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:2.5rem;color:var(--slate)">
                        Tidak ada pendaftaran pada tanggal ini.<br>
                        <a href="daftar.php" class="btn btn-primary btn-sm" style="margin-top:0.75rem">+ Tambah pendaftaran</a>
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>