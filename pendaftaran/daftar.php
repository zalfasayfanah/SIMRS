<?php
// ============================================================
// pendaftaran/daftar.php — Form Pendaftaran Baru
// ============================================================

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Pendaftaran Baru';

$errors = [];
$input  = [
    'jenis_kunjungan'  => 'Baru',
    'jenis_penjamin'   => 'Umum',
    'status_daftar'    => 'MENUNGGU',
];

// Ambil data dokter aktif untuk dropdown
$dokterList = mysqli_query($conn, "SELECT id_dokter, nama_dokter, spesialis, poli, jadwal_hari, jadwal_jam FROM dokter WHERE status_aktif = 1 ORDER BY poli, nama_dokter ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['id_pasien']       = intval($_POST['id_pasien'] ?? 0);
    $input['id_dokter']       = intval($_POST['id_dokter'] ?? 0);
    $input['poli']            = trim($_POST['poli'] ?? '');
    $input['jenis_kunjungan'] = trim($_POST['jenis_kunjungan'] ?? 'Baru');
    $input['jenis_penjamin']  = trim($_POST['jenis_penjamin'] ?? 'Umum');
    $input['no_kartu_bpjs']   = trim($_POST['no_kartu_bpjs'] ?? '');
    $input['tgl_daftar']      = trim($_POST['tgl_daftar'] ?? date('Y-m-d'));

    // Validasi
    if ($input['id_pasien'] <= 0) $errors['id_pasien'] = 'Pasien wajib dipilih.';
    if ($input['id_dokter'] <= 0) $errors['id_dokter'] = 'Dokter wajib dipilih.';
    if ($input['poli']      === '') $errors['poli']    = 'Poli wajib diisi.';
    if ($input['jenis_penjamin'] === 'BPJS' && $input['no_kartu_bpjs'] === '')
        $errors['no_kartu_bpjs'] = 'No. kartu BPJS wajib diisi jika penjamin BPJS.';

    if (empty($errors)) {
        // Generate no antrean otomatis: format A001, A002, dst per poli per hari
        $tgl    = mysqli_real_escape_string($conn, $input['tgl_daftar']);
        $poli   = mysqli_real_escape_string($conn, $input['poli']);
        $cekUrut = mysqli_fetch_row(mysqli_query($conn,
            "SELECT COUNT(*) FROM pendaftaran
             WHERE DATE(tgl_daftar) = '$tgl' AND poli = '$poli'"))[0] ?? 0;
        $noUrut     = str_pad($cekUrut + 1, 3, '0', STR_PAD_LEFT);
        $hurufPoli  = strtoupper(substr($input['poli'], 0, 1)); // ambil huruf pertama poli
        $noAntrean  = $hurufPoli . $noUrut; // contoh: U001, A001, dll

        $e = fn($v) => mysqli_real_escape_string($conn, $v);
        $n = fn($v) => $v !== '' ? "'" . $e($v) . "'" : 'NULL';

        $sql = "INSERT INTO pendaftaran
                    (id_pasien, id_dokter, tgl_daftar, poli,
                     jenis_kunjungan, jenis_penjamin, no_kartu_bpjs,
                     no_antrean, status_daftar)
                VALUES
                    ({$input['id_pasien']}, {$input['id_dokter']},
                     '{$e($input['tgl_daftar'])} " . date('H:i:s') . "',
                     '{$e($input['poli'])}',
                     '{$e($input['jenis_kunjungan'])}',
                     '{$e($input['jenis_penjamin'])}',
                     {$n($input['no_kartu_bpjs'])},
                     '{$e($noAntrean)}', 'MENUNGGU')";

        if (mysqli_query($conn, $sql)) {
            header('Location: index.php?status=tambah_ok');
            exit;
        } else {
            $errors['db'] = 'Gagal menyimpan: ' . mysqli_error($conn);
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1>Pendaftaran Baru</h1>
            <p>Daftarkan pasien untuk kunjungan rawat jalan</p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <?php if (isset($errors['db'])): ?>
    <div class="alert alert-danger"><?= $errors['db'] ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><h2>Formulir pendaftaran</h2></div>
        <div class="card-body">
        <form method="POST">

            <!-- Cari & pilih pasien -->
            <div class="form-group">
                <label>Pasien <span style="color:var(--danger)">*</span></label>
                <div style="display:flex;gap:0.5rem">
                    <input type="text" id="keyword_pasien"
                           placeholder="No. RM atau NIK pasien"
                           style="width:220px;padding:0.55rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px">
                    <input type="hidden" name="id_pasien" id="id_pasien" value="<?= intval($input['id_pasien'] ?? 0) ?: '' ?>">
                    <input type="text" id="info_pasien" readonly placeholder="Nama pasien akan muncul di sini..."
                           style="flex:1;padding:0.55rem 0.75rem;border:1px solid var(--border);border-radius:7px;font-size:13.5px;background:#f8fafc;color:var(--slate)">
                    <button type="button" onclick="cariPasien()" class="btn btn-secondary">Cari</button>
                </div>
                <div class="form-hint">Masukkan No. RM atau NIK pasien lalu klik Cari. Belum punya No. RM? Gunakan <a href="../pasien/index.php" target="_blank">halaman pasien</a> untuk mencari, atau <a href="../pasien/tambah.php" target="_blank">daftarkan pasien baru</a></div>
                <?php if (isset($errors['id_pasien'])): ?>
                <div class="form-hint" style="color:var(--danger)"><?= $errors['id_pasien'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Pilih dokter -->
            <div class="form-group">
                <label>Dokter <span style="color:var(--danger)">*</span></label>
                <select name="id_dokter" id="id_dokter" onchange="isiPoli()">
                    <option value="">-- Pilih dokter --</option>
                    <?php
                    // Reset pointer result
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
                            data-jadwal="<?= htmlspecialchars($dok['jadwal_hari'] . ($dok['jadwal_jam'] ? ' | ' . $dok['jadwal_jam'] : '')) ?>"
                            <?= ($input['id_dokter'] ?? 0) == $dok['id_dokter'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dok['nama_dokter']) ?> — <?= htmlspecialchars($dok['spesialis']) ?>
                    </option>
                    <?php endwhile; if ($currentPoli) echo '</optgroup>'; ?>
                </select>
                <div id="info_jadwal" class="form-hint" style="color:var(--teal)"></div>
                <?php if (isset($errors['id_dokter'])): ?>
                <div class="form-hint" style="color:var(--danger)"><?= $errors['id_dokter'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Poli (auto-fill dari dokter) -->
            <div class="form-row">
                <div class="form-group">
                    <label>Poli <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="poli" id="poli"
                           value="<?= htmlspecialchars($input['poli'] ?? '') ?>"
                           placeholder="Terisi otomatis saat memilih dokter">
                    <?php if (isset($errors['poli'])): ?>
                    <div class="form-hint" style="color:var(--danger)"><?= $errors['poli'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Tanggal Daftar</label>
                    <input type="date" name="tgl_daftar" value="<?= htmlspecialchars($input['tgl_daftar'] ?? date('Y-m-d')) ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jenis Kunjungan</label>
                    <select name="jenis_kunjungan">
                        <option value="Baru"  <?= ($input['jenis_kunjungan'] ?? '') === 'Baru'  ? 'selected' : '' ?>>Baru (belum pernah ke poli ini)</option>
                        <option value="Lama"  <?= ($input['jenis_kunjungan'] ?? '') === 'Lama'  ? 'selected' : '' ?>>Lama (kontrol / follow-up)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Penjamin</label>
                    <select name="jenis_penjamin" id="jenis_penjamin" onchange="toggleBpjs()">
                        <option value="Umum"     <?= ($input['jenis_penjamin'] ?? '') === 'Umum'     ? 'selected' : '' ?>>Umum</option>
                        <option value="BPJS"     <?= ($input['jenis_penjamin'] ?? '') === 'BPJS'     ? 'selected' : '' ?>>BPJS</option>
                        <option value="Asuransi" <?= ($input['jenis_penjamin'] ?? '') === 'Asuransi' ? 'selected' : '' ?>>Asuransi</option>
                    </select>
                </div>
            </div>

            <!-- No BPJS — muncul hanya jika BPJS -->
            <div class="form-group" id="field_bpjs" style="display:<?= ($input['jenis_penjamin'] ?? '') === 'BPJS' ? 'block' : 'none' ?>;max-width:50%">
                <label>No. Kartu BPJS <span style="color:var(--danger)">*</span></label>
                <input type="text" name="no_kartu_bpjs"
                       value="<?= htmlspecialchars($input['no_kartu_bpjs'] ?? '') ?>"
                       placeholder="Nomor kartu BPJS pasien">
                <?php if (isset($errors['no_kartu_bpjs'])): ?>
                <div class="form-hint" style="color:var(--danger)"><?= $errors['no_kartu_bpjs'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display:flex;gap:0.75rem;margin-top:1rem">
                <button type="submit" class="btn btn-primary">Simpan pendaftaran</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>

        </form>
        </div>
    </div>

</div>

<script>
// Auto-fill poli dari pilihan dokter
function isiPoli() {
    const sel  = document.getElementById('id_dokter');
    const opt  = sel.options[sel.selectedIndex];
    document.getElementById('poli').value = opt.dataset.poli || '';
    const jadwal = opt.dataset.jadwal || '';
    document.getElementById('info_jadwal').textContent = jadwal ? 'Jadwal: ' + jadwal : '';
}

// Show/hide field BPJS
function toggleBpjs() {
    const val   = document.getElementById('jenis_penjamin').value;
    document.getElementById('field_bpjs').style.display = val === 'BPJS' ? 'block' : 'none';
}

// Cari pasien berdasarkan No. RM atau NIK (AJAX)
function cariPasien() {
    const keyword = document.getElementById('keyword_pasien').value.trim();
    if (!keyword) { alert('Masukkan No. RM atau NIK terlebih dahulu.'); return; }
    fetch('../pasien/cari.php?keyword=' + encodeURIComponent(keyword))
        .then(r => r.json())
        .then(data => {
            if (data.nama) {
                document.getElementById('id_pasien').value = data.id_pasien;
                document.getElementById('info_pasien').value = data.nama + ' — ' + data.no_rm + ' (NIK: ' + data.nik + ')';
                document.getElementById('info_pasien').style.color = 'var(--navy)';
            } else {
                document.getElementById('id_pasien').value = '';
                document.getElementById('info_pasien').value = 'Pasien tidak ditemukan — periksa No. RM/NIK atau daftarkan pasien baru';
                document.getElementById('info_pasien').style.color = 'var(--danger)';
            }
        })
        .catch(() => { document.getElementById('info_pasien').value = 'Gagal mencari pasien'; });
}

// Jalankan saat halaman load jika ada nilai (misal setelah error validasi)
window.onload = function() {
    isiPoli();
    toggleBpjs();
}
</script>

<?php include '../includes/footer.php'; ?>