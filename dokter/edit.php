<?php
// ============================================================
// dokter/edit.php — Form Edit Dokter
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Edit Dokter';

// Ambil ID dari URL
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?status=gagal');
    exit;
}

// Ambil data dokter yang akan diedit
$result = mysqli_query($conn, "SELECT * FROM dokter WHERE id_dokter = $id");
if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: index.php?status=gagal');
    exit;
}
$dokter = mysqli_fetch_assoc($result);

$errors = [];
$input  = $dokter; // pre-fill form dengan data existing

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['nama_dokter']     = trim($_POST['nama_dokter'] ?? '');
    $input['spesialis']       = trim($_POST['spesialis'] ?? '');
    $input['poli']            = trim($_POST['poli'] ?? '');
    $input['no_izin_praktek'] = trim($_POST['no_izin_praktek'] ?? '');
    $input['tgl_exp_sip']     = trim($_POST['tgl_exp_sip'] ?? '');
    $input['jadwal_hari']     = trim($_POST['jadwal_hari'] ?? '');
    $input['jadwal_jam']      = trim($_POST['jadwal_jam'] ?? '');
    $input['status_aktif']    = isset($_POST['status_aktif']) ? 1 : 0;

    if ($input['nama_dokter'] === '') $errors['nama_dokter'] = 'Nama dokter wajib diisi.';
    if ($input['spesialis']   === '') $errors['spesialis']   = 'Spesialis wajib diisi.';
    if ($input['poli']        === '') $errors['poli']        = 'Poli wajib diisi.';

    if (empty($errors)) {
        $nama  = mysqli_real_escape_string($conn, $input['nama_dokter']);
        $spes  = mysqli_real_escape_string($conn, $input['spesialis']);
        $poli  = mysqli_real_escape_string($conn, $input['poli']);
        $sip   = mysqli_real_escape_string($conn, $input['no_izin_praktek']);
        $exp   = $input['tgl_exp_sip'] !== '' ? "'" . mysqli_real_escape_string($conn, $input['tgl_exp_sip']) . "'" : 'NULL';
        $hari  = mysqli_real_escape_string($conn, $input['jadwal_hari']);
        $jam   = mysqli_real_escape_string($conn, $input['jadwal_jam']);
        $aktif = $input['status_aktif'];

        $sql = "UPDATE dokter SET
                    nama_dokter     = '$nama',
                    spesialis       = '$spes',
                    poli            = '$poli',
                    no_izin_praktek = '$sip',
                    tgl_exp_sip     = $exp,
                    jadwal_hari     = '$hari',
                    jadwal_jam      = '$jam',
                    status_aktif    = $aktif
                WHERE id_dokter = $id";

        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?status=edit_ok');
            exit;
        } else {
            $errors['db'] = 'Gagal memperbarui data: ' . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Edit Dokter</h1>
            <p>Perbarui data dokter: <strong><?= htmlspecialchars($dokter['nama_dokter']) ?></strong></p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h2>Formulir edit dokter</h2></div>
        <div class="card-body">
            <form method="POST">

                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Dokter <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_dokter"
                               value="<?= htmlspecialchars($input['nama_dokter']) ?>">
                        <?php if (isset($errors['nama_dokter'])): ?>
                        <div class="form-hint" style="color:var(--danger)"><?= $errors['nama_dokter'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Spesialis <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="spesialis"
                               value="<?= htmlspecialchars($input['spesialis']) ?>">
                        <?php if (isset($errors['spesialis'])): ?>
                        <div class="form-hint" style="color:var(--danger)"><?= $errors['spesialis'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Poli <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="poli"
                               value="<?= htmlspecialchars($input['poli']) ?>">
                        <?php if (isset($errors['poli'])): ?>
                        <div class="form-hint" style="color:var(--danger)"><?= $errors['poli'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>No. Izin Praktek (SIP)</label>
                        <input type="text" name="no_izin_praktek"
                               value="<?= htmlspecialchars($input['no_izin_praktek'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Exp. SIP</label>
                        <input type="date" name="tgl_exp_sip"
                               value="<?= htmlspecialchars($input['tgl_exp_sip'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:8px">
                            <input type="checkbox" name="status_aktif" id="status_aktif"
                                   <?= $input['status_aktif'] ? 'checked' : '' ?>
                                   style="width:16px;height:16px;accent-color:var(--teal)">
                            <label for="status_aktif" style="font-weight:400;margin-bottom:0">Dokter aktif</label>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jadwal Hari Praktek</label>
                        <input type="text" name="jadwal_hari"
                               value="<?= htmlspecialchars($input['jadwal_hari'] ?? '') ?>"
                               placeholder="Contoh: Senin, Rabu, Jumat">
                    </div>
                    <div class="form-group">
                        <label>Jam Praktek</label>
                        <input type="text" name="jadwal_jam"
                               value="<?= htmlspecialchars($input['jadwal_jam'] ?? '') ?>"
                               placeholder="Contoh: 08:00 - 12:00">
                    </div>
                </div>

                <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>

            </form>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>