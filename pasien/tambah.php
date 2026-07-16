<?php
// ============================================================
// pasien/tambah.php — Form Tambah Pasien
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Tambah Pasien';

$errors = [];
$input  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Validasi wajib
    if ($input['nik']    === '') $errors['nik']    = 'NIK wajib diisi.';
    elseif (!preg_match('/^\d{16}$/', $input['nik'])) $errors['nik'] = 'NIK harus 16 digit angka.';
    if ($input['nama']              === '') $errors['nama']              = 'Nama pasien wajib diisi.';
    if ($input['tgl_lahir']         === '') $errors['tgl_lahir']         = 'Tanggal lahir wajib diisi.';
    if ($input['jenis_kelamin']     === '') $errors['jenis_kelamin']     = 'Jenis kelamin wajib dipilih.';
    if ($input['agama']             === '') $errors['agama']             = 'Agama wajib diisi.';
    if ($input['alamat']            === '') $errors['alamat']            = 'Alamat wajib diisi.';
    if ($input['pekerjaan']         === '') $errors['pekerjaan']         = 'Pekerjaan wajib diisi.';
    if ($input['pendidikan']        === '') $errors['pendidikan']        = 'Pendidikan wajib dipilih.';
    if ($input['status_perkawinan'] === '') $errors['status_perkawinan'] = 'Status perkawinan wajib dipilih.';
    if ($input['nama_wali']         === '') $errors['nama_wali']         = 'Nama wali wajib diisi.';
    if ($input['hubungan_wali']     === '') $errors['hubungan_wali']     = 'Hubungan wali wajib dipilih.';
    if ($input['no_hp_wali']        === '') $errors['no_hp_wali']        = 'No. HP wali wajib diisi.';

    // Cek duplikat NIK
    if (empty($errors['nik'])) {
        $cekNik = mysqli_query($conn, "SELECT id_pasien FROM pasien WHERE nik = '" . mysqli_real_escape_string($conn, $input['nik']) . "'");
        if (mysqli_num_rows($cekNik) > 0) $errors['nik'] = 'NIK sudah terdaftar.';
    }

    if (empty($errors)) {
        // Generate No. RM otomatis format: RM-YYYY-XXXX
        $tahunIni = date('Y');
        $cekUrut  = mysqli_query($conn,
            "SELECT no_rm FROM pasien WHERE no_rm LIKE 'RM-$tahunIni-%' ORDER BY no_rm DESC LIMIT 1");
        if ($cekUrut && mysqli_num_rows($cekUrut) > 0) {
            $lastRm  = mysqli_fetch_assoc($cekUrut)['no_rm'];
            $lastNum = (int) substr($lastRm, -4);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }
        $noRmBaru = 'RM-' . $tahunIni . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $e = fn($v) => mysqli_real_escape_string($conn, $v);
        $n = fn($v) => $v !== '' ? "'" . $e($v) . "'" : 'NULL';

        $sql = "INSERT INTO pasien
                    (no_rm, nik, nama, tgl_lahir, jenis_kelamin, alamat, no_hp,
                     agama, pekerjaan, pendidikan, status_perkawinan,
                     nama_wali, hubungan_wali, no_hp_wali)
                VALUES
                    ('{$e($noRmBaru)}', '{$e($input['nik'])}', '{$e($input['nama'])}',
                     '{$e($input['tgl_lahir'])}', '{$e($input['jenis_kelamin'])}',
                     {$n($input['alamat'])}, {$n($input['no_hp'])},
                     '{$e($input['agama'])}', '{$e($input['pekerjaan'])}',
                     '{$e($input['pendidikan'])}', '{$e($input['status_perkawinan'])}',
                     {$n($input['nama_wali'])}, {$n($input['hubungan_wali'])}, {$n($input['no_hp_wali'])})";

        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?status=tambah_ok');
            exit;
        } else {
            $errors['db'] = 'Gagal menyimpan data: ' . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Tambah Pasien</h1>
            <p>Daftarkan pasien baru ke dalam sistem rekam medis</p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Data identitas minimal</h2>
            <span style="font-size:11.5px;color:var(--slate)">Sesuai Permenkes No. 24/2022 Pasal 14</span>
        </div>
        <div class="card-body">
        <form method="POST">

            <div class="form-row">
                <div class="form-group">
                    <label>No. Rekam Medis</label>
                    <input type="text" value="Akan di-generate otomatis setelah disimpan" readonly
                           style="background:#f8fafc;color:var(--slate);font-style:italic">
                </div>
                <div class="form-group">
                    <label>NIK <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($input['nik'] ?? '') ?>" placeholder="16 digit NIK KTP" maxlength="16">
                    <?php if (isset($errors['nik'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['nik'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Nama Lengkap <span style="color:var(--danger)">*</span></label>
                <input type="text" name="nama" value="<?= htmlspecialchars($input['nama'] ?? '') ?>" placeholder="Nama sesuai KTP">
                <?php if (isset($errors['nama'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['nama'] ?></div><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tanggal Lahir <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="tgl_lahir" value="<?= htmlspecialchars($input['tgl_lahir'] ?? '') ?>">
                    <?php if (isset($errors['tgl_lahir'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['tgl_lahir'] ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin <span style="color:var(--danger)">*</span></label>
                    <select name="jenis_kelamin">
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= ($input['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($input['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <?php if (isset($errors['jenis_kelamin'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['jenis_kelamin'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" value="<?= htmlspecialchars($input['no_hp'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Agama <span style="color:var(--danger)">*</span></label>
                    <select name="agama">
                        <option value="">-- Pilih --</option>
                        <?php foreach (['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $ag): ?>
                        <option value="<?= $ag ?>" <?= ($input['agama'] ?? '') === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['agama'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['agama'] ?></div><?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Alamat <span style="color:var(--danger)">*</span></label>
                <textarea name="alamat" placeholder="Alamat lengkap sesuai KTP"><?= htmlspecialchars($input['alamat'] ?? '') ?></textarea>
                <?php if (isset($errors['alamat'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['alamat'] ?></div><?php endif; ?>
            </div>

            <!-- Data sosial minimal -->
            <div style="border-top:1px solid var(--border);margin:1.25rem 0;padding-top:1.25rem">
                <div style="font-size:13px;font-weight:500;color:var(--navy);margin-bottom:1rem">
                    Data sosial <span style="font-size:11.5px;font-weight:400;color:var(--slate)">(Permenkes 24/2022 Pasal 14)</span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Pekerjaan <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="pekerjaan" value="<?= htmlspecialchars($input['pekerjaan'] ?? '') ?>" placeholder="Contoh: Pegawai Swasta">
                        <?php if (isset($errors['pekerjaan'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['pekerjaan'] ?></div><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Pendidikan Terakhir <span style="color:var(--danger)">*</span></label>
                        <select name="pendidikan">
                            <option value="">-- Pilih --</option>
                            <?php foreach (['Tidak Sekolah','SD','SMP','SMA/SMK','D3','S1','S2','S3'] as $pend): ?>
                            <option value="<?= $pend ?>" <?= ($input['pendidikan'] ?? '') === $pend ? 'selected' : '' ?>><?= $pend ?></option>
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
                        <option value="<?= $sp ?>" <?= ($input['status_perkawinan'] ?? '') === $sp ? 'selected' : '' ?>><?= $sp ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['status_perkawinan'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['status_perkawinan'] ?></div><?php endif; ?>
                </div>
            </div>

            <!-- Data wali -->
            <div style="border-top:1px solid var(--border);margin:0 0 1.25rem;padding-top:1.25rem">
                <div style="font-size:13px;font-weight:500;color:var(--navy);margin-bottom:1rem">Data wali / penanggung jawab</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Wali <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="nama_wali" value="<?= htmlspecialchars($input['nama_wali'] ?? '') ?>" placeholder="Nama lengkap wali">
                        <?php if (isset($errors['nama_wali'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['nama_wali'] ?></div><?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>Hubungan dengan Pasien <span style="color:var(--danger)">*</span></label>
                        <select name="hubungan_wali">
                            <option value="">-- Pilih --</option>
                            <?php foreach (['Orang Tua','Suami','Istri','Anak','Saudara','Lainnya'] as $hub): ?>
                            <option value="<?= $hub ?>" <?= ($input['hubungan_wali'] ?? '') === $hub ? 'selected' : '' ?>><?= $hub ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['hubungan_wali'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['hubungan_wali'] ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="form-group" style="max-width:50%">
                    <label>No. HP Wali <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="no_hp_wali" value="<?= htmlspecialchars($input['no_hp_wali'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                    <?php if (isset($errors['no_hp_wali'])): ?><div class="form-hint" style="color:var(--danger)"><?= $errors['no_hp_wali'] ?></div><?php endif; ?>
                </div>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:0.5rem">
                <button type="submit" class="btn btn-primary">Simpan data pasien</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>