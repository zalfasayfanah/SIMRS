<?php
// ============================================================
// pasien/edit.php — Form Edit Pasien
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Edit Pasien';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php?status=gagal'); exit; }

$result = mysqli_query($conn, "SELECT * FROM pasien WHERE id_pasien = $id");
if (!$result || mysqli_num_rows($result) === 0) { header('Location: index.php?status=gagal'); exit; }
$pasien = mysqli_fetch_assoc($result);

$errors = [];
$input  = $pasien;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['no_rm']             = trim($_POST['no_rm'] ?? '');
    $input['nik']               = trim($_POST['nik'] ?? '');
    $input['nama']              = trim($_POST['nama'] ?? '');
    $input['tgl_lahir']         = trim($_POST['tgl_lahir'] ?? '');
    $input['jenis_kelamin']     = trim($_POST['jenis_kelamin'] ?? '');
    $input['alamat']            = trim($_POST['alamat'] ?? '');
    $input['no_hp']             = trim($_POST['no_hp'] ?? '');
    $input['agama']             = trim($_POST['agama'] ?? '');
    $input['pekerjaan']         = trim($_POST['pekerjaan'] ?? '');
    $input['pendidikan']        = trim($_POST['pendidikan'] ?? '');
    $input['status_perkawinan'] = trim($_POST['status_perkawinan'] ?? '');
    $input['nama_wali']         = trim($_POST['nama_wali'] ?? '');
    $input['hubungan_wali']     = trim($_POST['hubungan_wali'] ?? '');
    $input['no_hp_wali']        = trim($_POST['no_hp_wali'] ?? '');

    if ($input['no_rm']  === '') $errors['no_rm']  = 'No. RM wajib diisi.';
    if ($input['nik']    === '') $errors['nik']    = 'NIK wajib diisi.';
    elseif (!preg_match('/^\d{16}$/', $input['nik'])) $errors['nik'] = 'NIK harus 16 digit angka.';
    if ($input['nama']              === '') $errors['nama']              = 'Nama pasien wajib diisi.';
    if ($input['tgl_lahir']         === '') $errors['tgl_lahir']         = 'Tanggal lahir wajib diisi.';
    if ($input['jenis_kelamin']     === '') $errors['jenis_kelamin']     = 'Jenis kelamin wajib dipilih.';
    if ($input['agama']             === '') $errors['agama']             = 'Agama wajib diisi.';
    if ($input['pekerjaan']         === '') $errors['pekerjaan']         = 'Pekerjaan wajib diisi.';
    if ($input['pendidikan']        === '') $errors['pendidikan']        = 'Pendidikan wajib dipilih.';
    if ($input['status_perkawinan'] === '') $errors['status_perkawinan'] = 'Status perkawinan wajib dipilih.';

    // Cek duplikat hanya jika berubah
    if (empty($errors['no_rm']) && $input['no_rm'] !== $pasien['no_rm']) {
        $cek = mysqli_query($conn, "SELECT id_pasien FROM pasien WHERE no_rm = '" . mysqli_real_escape_string($conn, $input['no_rm']) . "'");
        if (mysqli_num_rows($cek) > 0) $errors['no_rm'] = 'No. RM sudah digunakan.';
    }
    if (empty($errors['nik']) && $input['nik'] !== $pasien['nik']) {
        $cek = mysqli_query($conn, "SELECT id_pasien FROM pasien WHERE nik = '" . mysqli_real_escape_string($conn, $input['nik']) . "'");
        if (mysqli_num_rows($cek) > 0) $errors['nik'] = 'NIK sudah terdaftar.';
    }

    if (empty($errors)) {
        $e = fn($v) => mysqli_real_escape_string($conn, $v);
        $n = fn($v) => $v !== '' ? "'" . $e($v) . "'" : 'NULL';

        $sql = "UPDATE pasien SET
                    no_rm             = '{$e($input['no_rm'])}',
                    nik               = '{$e($input['nik'])}',
                    nama              = '{$e($input['nama'])}',
                    tgl_lahir         = '{$e($input['tgl_lahir'])}',
                    jenis_kelamin     = '{$e($input['jenis_kelamin'])}',
                    alamat            = {$n($input['alamat'])},
                    no_hp             = {$n($input['no_hp'])},
                    agama             = '{$e($input['agama'])}',
                    pekerjaan         = '{$e($input['pekerjaan'])}',
                    pendidikan        = '{$e($input['pendidikan'])}',
                    status_perkawinan = '{$e($input['status_perkawinan'])}',
                    nama_wali         = {$n($input['nama_wali'])},
                    hubungan_wali     = {$n($input['hubungan_wali'])},
                    no_hp_wali        = {$n($input['no_hp_wali'])}
                WHERE id_pasien = $id";

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
            <h1>Edit Pasien</h1>
            <p>Perbarui data: <strong><?= htmlspecialchars($pasien['nama']) ?></strong> — No. RM <?= htmlspecialchars($pasien['no_rm']) ?></p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h2>Formulir edit pasien</h2></div>
        <div class="card-body">
        <form method="POST">

            <div class="form-row">
                <div class="form-group">
                    <label>No. Rekam Medis <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="no_rm" value="<?= htmlspecialchars($input['no_rm']) ?>">
                    <?php if (isset($errors['no_rm'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['no_rm'] ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label>NIK <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($input['nik']) ?>" maxlength="16">
                    <?php if (isset($errors['nik'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['nik'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span style="color:var(--danger)">*</span></label>
                <input type="text" name="nama" value="<?= htmlspecialchars($input['nama']) ?>">
                <?php if (isset($errors['nama'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['nama'] ?></div><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Lahir <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($input['tgl_lahir']) ?>">
                    <?php if (isset($errors['tgl_lahir'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['tgl_lahir'] ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <span style="color:var(--danger)">*</span></label>
                    <select name="jenis_kelamin">
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= $input['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $input['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <?php if (isset($errors['jenis_kelamin'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['jenis_kelamin'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($input['no_hp'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Agama <span style="color:var(--danger)">*</span></label>
                    <select name="agama">
                        <option value="">-- Pilih --</option>
                        <?php foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $ag): ?>
                        <option value="<?= $ag ?>" <?= $input['agama'] === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['agama'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['agama'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat"><?= htmlspecialchars($input['alamat'] ?? '') ?></textarea>
            </div>

            <div style="border-top:1px solid var(--border);margin:1.25rem 0;padding-top:1.25rem">
                <div style="font-size:13px;font-weight:500;color:var(--navy);margin-bottom:1rem">Data sosial</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Pekerjaan <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="pekerjaan" value="<?= htmlspecialchars($input['pekerjaan']) ?>">
                        <?php if (isset($errors['pekerjaan'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['pekerjaan'] ?></div><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Pendidikan Terakhir <span style="color:var(--danger)">*</span></label>
                        <select name="pendidikan">
                            <option value="">-- Pilih --</option>
                            <?php foreach (['Tidak Sekolah','SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $pend): ?>
                            <option value="<?= $pend ?>" <?= $input['pendidikan'] === $pend ? 'selected' : '' ?>><?= $pend ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['pendidikan'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['pendidikan'] ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="form-group" style="max-width:50%">
                    <label>Status Perkawinan <span style="color:var(--danger)">*</span></label>
                    <select name="status_perkawinan">
                        <option value="">-- Pilih --</option>
                        <?php foreach (['Belum Menikah','Menikah','Cerai Hidup','Cerai Mati'] as $sp): ?>
                        <option value="<?= $sp ?>" <?= $input['status_perkawinan'] === $sp ? 'selected' : '' ?>><?= $sp ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['status_perkawinan'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['status_perkawinan'] ?></div><?php endif; ?>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);margin:0 0 1.25rem;padding-top:1.25rem">
                <div style="font-size:13px;font-weight:500;color:var(--navy);margin-bottom:0.25rem">Data wali / penanggung jawab</div>
                <div style="font-size:12px;color:var(--slate);margin-bottom:1rem">Opsional</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Wali</label>
                        <input type="text" name="nama_wali" value="<?= htmlspecialchars($input['nama_wali'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Hubungan dengan Pasien</label>
                        <select name="hubungan_wali">
                            <option value="">-- Pilih --</option>
                            <?php foreach (['Orang Tua','Suami','Istri','Anak','Saudara','Lainnya'] as $hub): ?>
                            <option value="<?= $hub ?>" <?= ($input['hubungan_wali'] ?? '') === $hub ? 'selected' : '' ?>><?= $hub ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="max-width:50%">
                    <label>No. HP Wali</label>
                    <input type="text" name="no_hp_wali" value="<?= htmlspecialchars($input['no_hp_wali'] ?? '') ?>">
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