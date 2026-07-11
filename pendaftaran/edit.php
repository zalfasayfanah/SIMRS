<?php
// ============================================================
// pendaftaran/edit.php — Edit Status & Data Pendaftaran
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Edit Pendaftaran';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php?status=gagal'); exit; }

$result = mysqli_query($conn, "
    SELECT p.*, ps.nama AS nama_pasien, ps.no_rm,
           d.nama_dokter, d.poli AS poli_dokter
    FROM pendaftaran p
    JOIN pasien ps ON ps.id_pasien = p.id_pasien
    JOIN dokter d  ON d.id_dokter  = p.id_dokter
    WHERE p.id_pendaftaran = $id
");
if (!$result || mysqli_num_rows($result) === 0) { header('Location: index.php?status=gagal'); exit; }
$data  = mysqli_fetch_assoc($result);
$input = $data;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['id_dokter']       = intval($_POST['id_dokter'] ?? $data['id_dokter']);
    $input['poli']            = trim($_POST['poli'] ?? '');
    $input['jenis_kunjungan'] = trim($_POST['jenis_kunjungan'] ?? '');
    $input['jenis_penjamin']  = trim($_POST['jenis_penjamin'] ?? '');
    $input['no_kartu_bpjs']   = trim($_POST['no_kartu_bpjs'] ?? '');
    $input['status_daftar']   = trim($_POST['status_daftar'] ?? '');

    if ($input['poli']           === '') $errors['poli']           = 'Poli wajib diisi.';
    if ($input['status_daftar']  === '') $errors['status_daftar']  = 'Status wajib dipilih.';
    if ($input['jenis_penjamin'] === 'BPJS' && $input['no_kartu_bpjs'] === '')
        $errors['no_kartu_bpjs'] = 'No. kartu BPJS wajib diisi jika penjamin BPJS.';

    if (empty($errors)) {
        $e = fn($v) => mysqli_real_escape_string($conn, $v);
        $n = fn($v) => $v !== '' ? "'" . $e($v) . "'" : 'NULL';

        $sql = "UPDATE pendaftaran SET
                    id_dokter       = {$input['id_dokter']},
                    poli            = '{$e($input['poli'])}',
                    jenis_kunjungan = '{$e($input['jenis_kunjungan'])}',
                    jenis_penjamin  = '{$e($input['jenis_penjamin'])}',
                    no_kartu_bpjs   = {$n($input['no_kartu_bpjs'])},
                    status_daftar   = '{$e($input['status_daftar'])}'
                WHERE id_pendaftaran = $id";

        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?status=edit_ok');
            exit;
        } else {
            $errors['db'] = 'Gagal memperbarui: ' . mysqli_error($conn);
        }
    }
}

// Ambil list dokter aktif
$dokterList = mysqli_query($conn, "SELECT id_dokter, nama_dokter, spesialis, poli FROM dokter WHERE status_aktif = 1 ORDER BY poli, nama_dokter ASC");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Edit Pendaftaran</h1>
            <p>No. antrean <strong><?= htmlspecialchars($data['no_antrean']) ?></strong>
               — <?= htmlspecialchars($data['nama_pasien']) ?> (<?= htmlspecialchars($data['no_rm']) ?>)</p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h2>Formulir edit pendaftaran</h2></div>
        <div class="card-body">
        <form method="POST">

            <!-- Info pasien (readonly) -->
            <div class="form-group">
                <label>Pasien</label>
                <input type="text" readonly value="<?= htmlspecialchars($data['nama_pasien']) ?> — No. RM: <?= htmlspecialchars($data['no_rm']) ?>"
                       style="background:#f8fafc;color:var(--slate)">
                <div class="form-hint">Data pasien tidak dapat diubah di sini</div>
            </div>

            <!-- Pilih dokter -->
            <div class="form-group">
                <label>Dokter</label>
                <select name="id_dokter" id="id_dokter" onchange="isiPoli()">
                    <?php
                    mysqli_data_seek($dokterList, 0);
                    $currentPoli = '';
                    while ($dok = mysqli_fetch_assoc($dokterList)):
                        if ($dok['poli'] !== $currentPoli) {
                            if ($currentPoli !== '') echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($dok['poli']) . '">';
                            $currentPoli = $dok['poli'];
                        }
                    ?>
                    <option value="<?= $dok['id_dokter'] ?>"
                            data-poli="<?= htmlspecialchars($dok['poli']) ?>"
                            <?= $input['id_dokter'] == $dok['id_dokter'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dok['nama_dokter']) ?> — <?= htmlspecialchars($dok['spesialis']) ?>
                    </option>
                    <?php endwhile; if ($currentPoli) echo '</optgroup>'; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Poli <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="poli" id="poli" value="<?= htmlspecialchars($input['poli']) ?>">
                    <?php if (isset($errors['poli'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['poli'] ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Status Antrian <span style="color:var(--danger)">*</span></label>
                    <select name="status_daftar">
                        <?php foreach (['MENUNGGU','DIPERIKSA','SELESAI','BATAL'] as $st): ?>
                        <option value="<?= $st ?>" <?= $input['status_daftar'] === $st ? 'selected' : '' ?>>
                            <?= ucfirst(strtolower($st)) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['status_daftar'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['status_daftar'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jenis Kunjungan</label>
                    <select name="jenis_kunjungan">
                        <option value="Baru" <?= $input['jenis_kunjungan'] === 'Baru' ? 'selected' : '' ?>>Baru</option>
                        <option value="Lama" <?= $input['jenis_kunjungan'] === 'Lama' ? 'selected' : '' ?>>Lama</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Penjamin</label>
                    <select name="jenis_penjamin" id="jenis_penjamin" onchange="toggleBpjs()">
                        <?php foreach (['Umum','BPJS','Asuransi'] as $pj): ?>
                        <option value="<?= $pj ?>" <?= $input['jenis_penjamin'] === $pj ? 'selected' : '' ?>><?= $pj ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group" id="field_bpjs"
                 style="display:<?= $input['jenis_penjamin'] === 'BPJS' ? 'block' : 'none' ?>;max-width:50%">
                <label>No. Kartu BPJS <span style="color:var(--danger)">*</span></label>
                <input type="text" name="no_kartu_bpjs"
                       value="<?= htmlspecialchars($input['no_kartu_bpjs'] ?? '') ?>">
                <?php if (isset($errors['no_kartu_bpjs'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['no_kartu_bpjs'] ?></div><?php endif; ?>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
        </div>
    </div>

</div>

<script>
function isiPoli() {
    const sel = document.getElementById('id_dokter');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('poli').value = opt.dataset.poli || '';
}
function toggleBpjs() {
    const val = document.getElementById('jenis_penjamin').value;
    document.getElementById('field_bpjs').style.display = val === 'BPJS' ? 'block' : 'none';
}
</script>

<?php include '../includes/footer.php'; ?>