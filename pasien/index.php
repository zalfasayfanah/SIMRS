<?php
// ============================================================
// pasien/index.php — Daftar Semua Pasien
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Data Pasien';

// Pesan status dari redirect
$pesan = '';
if (isset($_GET['status'])) {
    $pesan = match($_GET['status']) {
        'tambah_ok'    => ['tipe' => 'success', 'teks' => 'Data pasien berhasil ditambahkan.'],
        'edit_ok'      => ['tipe' => 'success', 'teks' => 'Data pasien berhasil diperbarui.'],
        'hapus_ok'     => ['tipe' => 'success', 'teks' => 'Data pasien berhasil dihapus.'],
        'gagal_relasi' => ['tipe' => 'danger',  'teks' => 'Pasien tidak dapat dihapus karena masih memiliki data pendaftaran.'],
        'gagal'        => ['tipe' => 'danger',  'teks' => 'Terjadi kesalahan, silakan coba lagi.'],
        default        => ''
    };
}

// Pencarian
$cari  = trim($_GET['cari'] ?? '');
$where = [];
if ($cari !== '') {
    $s       = mysqli_real_escape_string($conn, $cari);
    $where[] = "(nama LIKE '%$s%' OR no_rm LIKE '%$s%' OR nik LIKE '%$s%')";
}
$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$totalSemua = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pasien"))[0] ?? 0;
$result     = mysqli_query($conn, "SELECT * FROM pasien $whereClause ORDER BY nama ASC");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Data Pasien</h1>
            <p>Total <?= $totalSemua ?> pasien terdaftar di sistem</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">+ Tambah pasien</a>
    </div>

    <?php if ($pesan): ?>
    <div class="alert alert-<?= $pesan['tipe'] ?>"><?= $pesan['teks'] ?></div>
    <?php endif; ?>

    <!-- Pencarian -->
    <div class="card" style="margin-bottom:1.25rem">
        <div class="card-body" style="padding:1rem 1.5rem">
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">
                <div style="flex:1;min-width:220px">
                    <label style="font-size:12px;font-weight:500;color:var(--slate);display:block;margin-bottom:4px">Cari pasien</label>
                    <input type="text" name="cari" value="<?= htmlspecialchars($cari) ?>"
                           placeholder="Nama, No. RM, atau NIK..."
                           style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                </div>
                <button type="submit" class="btn btn-primary" style="align-self:flex-end">Cari</button>
                <?php if ($cari): ?>
                <a href="index.php" class="btn btn-secondary" style="align-self:flex-end">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card">
        <div class="card-header">
            <h2>Daftar pasien</h2>
            <span style="font-size:12px;color:var(--slate)"><?= mysqli_num_rows($result) ?> pasien ditemukan</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>No. RM</th>
                        <th>NIK</th>
                        <th>Nama Pasien</th>
                        <th>Tgl. Lahir / Umur</th>
                        <th>Jenis Kelamin</th>
                        <th>No. HP</th>
                        <th>Terdaftar</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($result) > 0):
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td style="color:var(--slate)"><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['no_rm']) ?></strong></td>
                    <td style="font-size:12.5px;color:var(--slate)"><?= htmlspecialchars($row['nik'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td>
                        <?php
                        $tgl  = $row['tgl_lahir'];
                        $umur = $tgl ? (int) date_diff(date_create($tgl), date_create('today'))->y : null;
                        echo $tgl ? date('d/m/Y', strtotime($tgl)) : '-';
                        echo $umur !== null ? '<div style="font-size:11.5px;color:var(--slate)">' . $umur . ' tahun</div>' : '';
                        ?>
                    </td>
                    <td>
                        <?php
                        $jk = $row['jenis_kelamin'] ?? '';
                        if ($jk === 'L')      echo '<span class="badge badge-info">Laki-laki</span>';
                        elseif ($jk === 'P')  echo '<span class="badge badge-warning">Perempuan</span>';
                        else                  echo '-';
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                    <td style="font-size:12px;color:var(--slate)">
                        <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="edit.php?id=<?= $row['id_pasien'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= $row['id_pasien'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Hapus pasien <?= htmlspecialchars(addslashes($row['nama'])) ?>?')">
                               Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="9" style="text-align:center;padding:2.5rem;color:var(--slate)">
                        <?= $cari ? 'Tidak ada pasien yang sesuai pencarian.' : 'Belum ada data pasien.' ?>
                        <?php if (!$cari): ?>
                        <br><a href="tambah.php" class="btn btn-primary btn-sm" style="margin-top:0.75rem">+ Tambah pasien</a>
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