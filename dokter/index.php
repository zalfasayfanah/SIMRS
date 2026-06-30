<?php
// ============================================================
// dokter/index.php — Daftar Semua Dokter
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Data Dokter';

// Tangani pesan sukses/gagal dari redirect
$pesan = '';
if (isset($_GET['status'])) {
    $pesan = match($_GET['status']) {
        'tambah_ok' => ['tipe' => 'success', 'teks' => 'Data dokter berhasil ditambahkan.'],
        'edit_ok'   => ['tipe' => 'success', 'teks' => 'Data dokter berhasil diperbarui.'],
        'hapus_ok'  => ['tipe' => 'success', 'teks' => 'Data dokter berhasil dihapus.'],
        'gagal'     => ['tipe' => 'danger',  'teks' => 'Terjadi kesalahan, silakan coba lagi.'],
        'gagal_relasi' => ['tipe' => 'danger', 'teks' => 'Dokter tidak dapat dihapus karena masih memiliki data pendaftaran.'],
        default     => ''
    };
}

// Filter & pencarian
$cari       = trim($_GET['cari'] ?? '');
$filterPoli = trim($_GET['poli'] ?? '');

$where = [];
if ($cari !== '')       $where[] = "(nama_dokter LIKE '%" . mysqli_real_escape_string($conn, $cari) . "%' OR no_izin_praktek LIKE '%" . mysqli_real_escape_string($conn, $cari) . "%')";
if ($filterPoli !== '') $where[] = "poli = '" . mysqli_real_escape_string($conn, $filterPoli) . "'";

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql    = "SELECT * FROM dokter $whereClause ORDER BY nama_dokter ASC";
$result = mysqli_query($conn, $sql);

// Ambil list poli untuk filter dropdown
$poliList = mysqli_query($conn, "SELECT DISTINCT poli FROM dokter ORDER BY poli ASC");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Data Dokter</h1>
            <p>Kelola data dokter yang bertugas di rumah sakit</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">+ Tambah dokter</a>
    </div>

    <?php if ($pesan): ?>
    <div class="alert alert-<?= $pesan['tipe'] ?>"><?= $pesan['teks'] ?></div>
    <?php endif; ?>

    <!-- Filter & pencarian -->
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-body" style="padding:1rem 1.5rem">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:1;min-width:180px">
                    <label style="font-size:12px;font-weight:500;color:var(--slate);display:block;margin-bottom:4px">Cari dokter</label>
                    <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>"
                           placeholder="Nama atau no. SIP..." style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                </div>
                <div style="min-width:150px">
                    <label style="font-size:12px;font-weight:500;color:var(--slate);display:block;margin-bottom:4px">Filter poli</label>
                    <select name="poli" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                        <option value="">Semua poli</option>
                        <?php while ($p = mysqli_fetch_assoc($poliList)): ?>
                        <option value="<?= htmlspecialchars($p['poli']) ?>"
                            <?= $filterPoli === $p['poli'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['poli']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="align-self:flex-end">Cari</button>
                <?php if ($cari || $filterPoli): ?>
                <a href="index.php" class="btn btn-secondary" style="align-self:flex-end">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabel dokter -->
    <div class="card">
        <div class="card-header">
            <h2>Daftar dokter</h2>
            <span style="font-size:12px;color:var(--slate)"><?= mysqli_num_rows($result) ?> dokter ditemukan</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Nama Dokter</th>
                        <th>Spesialis</th>
                        <th>Poli</th>
                        <th>No. SIP</th>
                        <th>Exp. SIP</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td style="color:var(--slate)"><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['nama_dokter']) ?></strong></td>
                    <td><?= htmlspecialchars($row['spesialis']) ?></td>
                    <td><?= htmlspecialchars($row['poli']) ?></td>
                    <td style="font-size:12.5px;color:var(--slate)"><?= htmlspecialchars($row['no_izin_praktek'] ?? '-') ?></td>
                    <td>
                        <?php
                        $exp = $row['tgl_exp_sip'] ?? null;
                        if ($exp) {
                            $expired = strtotime($exp) < time();
                            $soon    = !$expired && strtotime($exp) < strtotime('+30 days');
                            $badge   = $expired ? 'badge-danger' : ($soon ? 'badge-warning' : 'badge-success');
                            echo '<span class="badge ' . $badge . '">' . date('d/m/Y', strtotime($exp)) . '</span>';
                        } else {
                            echo '<span style="color:var(--slate)">—</span>';
                        }
                        ?>
                    </td>
                    <td style="font-size:12.5px">
                        <?= htmlspecialchars($row['jadwal_hari'] ?? '—') ?><br>
                        <span style="color:var(--slate)"><?= htmlspecialchars($row['jadwal_jam'] ?? '') ?></span>
                    </td>
                    <td>
                        <?php if ($row['status_aktif']): ?>
                            <span class="badge badge-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-gray">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="edit.php?id=<?= $row['id_dokter'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= $row['id_dokter'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus dokter <?= htmlspecialchars(addslashes($row['nama_dokter'])) ?>?')">
                               Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:2.5rem;color:var(--slate)">
                        <?= $cari || $filterPoli ? 'Tidak ada dokter yang sesuai filter.' : 'Belum ada data dokter.' ?>
                        <?php if (!$cari && !$filterPoli): ?>
                        <br><a href="tambah.php" class="btn btn-primary btn-sm" style="margin-top:0.75rem">+ Tambah dokter</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>