<?php
require_once '../config/database.php';

$pageTitle = 'Pemeriksaan Pasien';
$basePath = '../';

include '../includes/header.php';
include '../includes/sidebar.php';

/* ============================
   VALIDASI PARAMETER
============================ */

if (
    !isset($_GET['id_pendaftaran']) ||
    !isset($_GET['id_dokter'])
) {
    die("Parameter tidak lengkap.");
}

$id_pendaftaran = (int) $_GET['id_pendaftaran'];
$id_dokter = (int) $_GET['id_dokter'];

/* ============================
   AMBIL DATA PASIEN
============================ */

$sql = "
SELECT
    pd.id_pendaftaran,
    pd.tgl_daftar,
    pd.status_daftar,

    ps.id_pasien,
    ps.no_rm,
    ps.nik,
    ps.nama,
    ps.tgl_lahir,
    ps.jenis_kelamin,
    ps.alamat,
    ps.no_hp,

    d.nama_dokter

FROM pendaftaran pd

JOIN pasien ps
ON ps.id_pasien = pd.id_pasien

JOIN dokter d
ON d.id_dokter = pd.id_dokter

WHERE pd.id_pendaftaran = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id_pendaftaran);

mysqli_stmt_execute($stmt);

$pasien = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($pasien) == 0) {

    die("Data pasien tidak ditemukan.");

}

$pasien = mysqli_fetch_assoc($pasien);
/* ============================
   RIWAYAT PEMERIKSAAN
============================ */

$sqlRiwayat = "
SELECT
    p.id_pemeriksaan,
    p.tgl_pemeriksaan,
    p.diagnosa,
    p.icd_10,
    d.nama_dokter
FROM pemeriksaan p
JOIN pendaftaran pd
    ON pd.id_pendaftaran = p.id_pendaftaran
JOIN dokter d
    ON d.id_dokter = p.id_dokter
WHERE pd.id_pasien = ?
ORDER BY p.tgl_pemeriksaan DESC
";

$stmtRiwayat = mysqli_prepare($conn, $sqlRiwayat);

if (!$stmtRiwayat) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmtRiwayat,
    "i",
    $pasien['id_pasien']
);

mysqli_stmt_execute($stmtRiwayat);

$riwayat = mysqli_stmt_get_result($stmtRiwayat);

/* ============================
   HITUNG UMUR
============================ */

$umur = date_diff(
    date_create($pasien['tgl_lahir']),
    date_create(date('Y-m-d'))
)->y;


/* ============================
   MASTER OBAT
============================ */

$obat = mysqli_query($conn, "
SELECT
    id_obat,
    nama_obat,
    stok,
    satuan
FROM obat
WHERE stok > 0
ORDER BY nama_obat ASC
");

/* ============================
   PROSES SIMPAN PEMERIKSAAN & RESEP
============================ */
if (isset($_POST['simpan_pemeriksaan'])) { // Sesuaikan dengan nama atribut 'name' pada tombol submit kamu
    $diagnosa = mysqli_real_escape_string($conn, $_POST['diagnosa']);
    $icd_10 = mysqli_real_escape_string($conn, $_POST['icd_10']);
    $rencana_tindakan = mysqli_real_escape_string($conn, $_POST['rencana_tindakan']);
    $tgl_pemeriksaan = date('Y-m-d H:i:s');

    // 1. Simpan ke tabel pemeriksaan terlebih dahulu
    $sql_periksa = "INSERT INTO pemeriksaan (id_pendaftaran, id_dokter, tgl_pemeriksaan, diagnosa, icd_10, rencana_tindakan) 
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_periksa = mysqli_prepare($conn, $sql_periksa);
    mysqli_stmt_bind_param($stmt_periksa, "iissss", $id_pendaftaran, $id_dokter, $tgl_pemeriksaan, $diagnosa, $icd_10, $rencana_tindakan);
    
    if (mysqli_stmt_execute($stmt_periksa)) {
        // Ambil ID Pemeriksaan yang baru saja terbuat
        $id_pemeriksaan = mysqli_insert_id($conn);

        // Update status pendaftaran menjadi SELESAI / DIPERIKSA
        mysqli_query($conn, "UPDATE pendaftaran SET status_daftar = 'SELESAI' WHERE id_pendaftaran = $id_pendaftaran");

        // 2. Proses Simpan Obat Resep (Array)
        if (isset($_POST['id_obat']) && is_array($_POST['id_obat'])) {
            $id_obat_array = $_POST['id_obat'];
            $dosis_array = $_POST['dosis']; // Menangkap input DOSIS baru
            $jumlah_array = $_POST['jumlah'];
            $aturan_pakai_array = $_POST['aturan_pakai'];

            for ($i = 0; $i < count($id_obat_array); $i++) {
                // Lewati jika obat tidak dipilih
                if (empty($id_obat_array[$i])) continue;

                $id_obat = (int)$id_obat_array[$i];
                $dosis = mysqli_real_escape_string($conn, $dosis_array[$i]); // Di-escape agar aman
                $jumlah = (int)$jumlah_array[$i];
                $aturan_pakai = mysqli_real_escape_string($conn, $aturan_pakai_array[$i]);

                // Query Insert ke tabel resep lengkap dengan kolom DOSIS
                $sql_resep = "INSERT INTO resep (id_pemeriksaan, id_obat, dosis, jumlah, aturan_pakai, status_ambil) 
                              VALUES ('$id_pemeriksaan', '$id_obat', '$dosis', '$jumlah', '$aturan_pakai', 'MENUNGGU')";
                mysqli_query($conn, $sql_resep);
            }
        }

        // Redirect setelah sukses simpan
        echo "<script>alert('Data pemeriksaan dan resep berhasil disimpan!'); window.location='index.php';</script>";
        exit();
    } else {
        die("Gagal menyimpan data pemeriksaan: " . mysqli_error($conn));
    }
}
?>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>Pemeriksaan Pasien</h1>
            <p>
                Isi hasil pemeriksaan dan resep obat pasien.
            </p>
        </div>
    </div>
    <a href="index.php?id_dokter=<?= $id_dokter ?>" class="btn btn-secondary">
        ← Kembali
    </a>
    <br><br>

    <!-- INFORMASI PASIEN -->
    <div class="card">
        <div class="card-header">
            <h3>Informasi Pasien</h3>
        </div>
        <div class="card-body">
            <table class="table-detail">
                <tr>
                    <td width="220"><strong>No Rekam Medis</strong></td>
                    <td><?= htmlspecialchars($pasien['no_rm']) ?></td>
                </tr>
                <tr>
                    <td><strong>NIK</strong></td>
                    <td><?= htmlspecialchars($pasien['nik']) ?></td>
                </tr>
                <tr>
                    <td><strong>Nama Pasien</strong></td>
                    <td><?= htmlspecialchars($pasien['nama']) ?></td>
                </tr>
                <tr>
                    <td><strong>Jenis Kelamin</strong></td>
                    <td>
                        <?= $pasien['jenis_kelamin'] == "L"
                            ? "Laki-laki"
                            : "Perempuan"; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Umur</strong></td>
                    <td><?= $umur ?> Tahun</td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td><?= htmlspecialchars($pasien['alamat']) ?></td>
                </tr>
                <tr>
                    <td><strong>No HP</strong></td>
                    <td><?= htmlspecialchars($pasien['no_hp']) ?></td>
                </tr>
                <tr>
                    <td><strong>Dokter Pemeriksa</strong></td>
                    <td><?= htmlspecialchars($pasien['nama_dokter']) ?></td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pemeriksaan</strong></td>
                    <td><?= date('d-m-Y H:i') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan -->
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Pemeriksaan</h3>
        </div>
        <div class="card-body">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Diagnosa</th>
                            <th>ICD-10</th>
                            <th>Dokter</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$riwayat) {
                            die("Riwayat gagal diambil : " . mysqli_error($conn));
                        }
                        ?>
                        <?php if (mysqli_num_rows($riwayat) > 0): ?>
                            <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['tgl_pemeriksaan'])) ?></td>
                                    <td><?= htmlspecialchars($r['diagnosa']) ?></td>
                                    <td><?= htmlspecialchars($r['icd_10']) ?></td>
                                    <td><?= htmlspecialchars($r['nama_dokter']) ?></td>
                                    <td>
                                        <a href="detail.php?id=<?= $r['id_pemeriksaan'] ?>" class="btn btn-secondary btn-sm">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center">
                                    Belum pernah melakukan pemeriksaan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FORM PEMERIKSAAN -->
   
    <form action="simpan.php" method="POST">
        <input type="hidden" name="id_pendaftaran" value="<?= $id_pendaftaran ?>">
        <input type="hidden" name="id_dokter" value="<?= $id_dokter ?>">

        <!-- Bagian Pemeriksaan -->
        <div class="card">
            <div class="card-header">
                <h3>Pemeriksaan Dokter</h3>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <!-- Keluhan -->
                    <div class="form-group full-width">
                        <label>Keluhan Pasien <span style="color:red">*</span></label>
                        <textarea name="keluhan" class="form-control" rows="4"
                            placeholder="Masukkan keluhan utama pasien..." required></textarea>
                    </div>

                    <!-- Tekanan Darah -->
                    <div class="form-group">
                        <label>Tekanan Darah</label>
                        <input type="text" name="tekanan_darah" class="form-control" placeholder="120/80 mmHg">
                    </div>

                    <!-- Suhu -->
                    <div class="form-group">
                        <label>Suhu Tubuh (°C)</label>
                        <input type="number" step="0.1" min="30" max="45" name="suhu_tubuh" class="form-control"
                            placeholder="36.5">
                    </div>

                    <!-- Berat -->
                    <div class="form-group">
                        <label>Berat Badan (kg)</label>
                        <input type="number" step="0.1" min="1" name="berat_badan" class="form-control"
                            placeholder="65">
                    </div>

                    <!-- Tinggi -->
                    <div class="form-group">
                        <label>Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" min="30" name="tinggi_badan" class="form-control"
                            placeholder="170">
                    </div>

                    <!-- Diagnosa -->
                    <div class="form-group full-width">
                        <label>Diagnosa <span style="color:red">*</span></label>
                        <textarea name="diagnosa" class="form-control" rows="4"
                            placeholder="Masukkan diagnosa dokter..." required></textarea>
                    </div>

                    <!-- ICD -->
                    <div class="form-group">
                        <label>Kode ICD-10</label>
                        <input type="text" name="icd_10" class="form-control" maxlength="10"
                            placeholder="Contoh : J06.9">
                    </div>

                    <!-- Tanggal -->
                    <div class="form-group">
                        <label>Tanggal Pemeriksaan</label>
                        <input type="datetime-local" name="tgl_pemeriksaan" class="form-control"
                            value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>

                    <!-- Rencana -->
                    <div class="form-group full-width">
                        <label>Rencana Tindakan</label>
                        <textarea name="rencana_tindakan" class="form-control" rows="4" placeholder="Contoh :
                            - Rawat Jalan
                            - Kontrol 7 Hari
                            - Pemeriksaan Laboratorium
                            - Rujukan"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Resep -->
        <div class="table-wrap" style="margin-top: 20px; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid var(--border);">
            <table class="table">
                <thead>
                    <tr>
                        <th width="25%">Obat</th>
                        <th width="10%">Stok</th>
                        <th width="10%">Satuan</th>
                        <th width="15%">Dosis</th>
                        <th width="10%">Jumlah</th>
                        <th width="20%">Aturan Pakai</th>
                        <th width="8%" style="text-align: center;">Aksi</th>
                    </tr>
                </thead>

                <tbody id="resepBody">
                    <tr>
                        <td>
                            <select name="id_obat[]" class="form-control obat-select" required>
                                <option value="">-- Pilih Obat --</option>
                                <?php
                                mysqli_data_seek($obat, 0);
                                while ($o = mysqli_fetch_assoc($obat)):
                                    ?>
                                    <option value="<?= $o['id_obat'] ?>" data-stok="<?= $o['stok'] ?>"
                                        data-satuan="<?= htmlspecialchars($o['satuan']) ?>">
                                        <?= htmlspecialchars($o['nama_obat']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </td>

                        <td>
                            <input type="text" class="form-control stok" readonly>
                        </td>

                        <td>
                            <input type="text" class="form-control satuan" readonly>
                        </td>

                        <td>
                             <input type="text" name="dosis[]" class="form-control" placeholder="Contoh: 500 mg" required>
                        </td>

                        <td>
                            <input type="number" name="jumlah[]" class="form-control" min="1" required>
                        </td>

                        <td>
                            <input type="text" name="aturan_pakai[]" class="form-control"
                                placeholder="Contoh : 3x1 sesudah makan" required>
                        </td>

                        <td style="text-align: center;">
                            <button type="button" class="btn btn-danger btn-sm" onclick="hapusBaris(this)" style="padding: 0.45rem 0.85rem;">
                                 Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>

        <button type="button" class="btn btn-success" onclick="tambahObat()" style="background: #10B981; color: white;">
            + Tambah Obat
        </button>
        <hr>
        <div style="text-align:right">
            <button type="submit" class="btn btn-primary btn-lg">
                💾 Simpan Pemeriksaan
            </button>
        </div>
    </form>
</div>

<script>
    function tambahObat() {
        let tbody = document.getElementById("resepBody");
        // Menyalin baris pertama (otomatis menyertakan kolom dosis baru)
        let row = tbody.rows[0].cloneNode(true); 
        
        // Mengosongkan semua elemen input (termasuk jumlah, aturan pakai, dan dosis)
        row.querySelectorAll("input").forEach(function (input) {
            input.value = "";
            // Reset attribute max jika ada
            if(input.type === "number") {
                input.removeAttribute("max");
            }
        });

        // Reset pilihan select obat kembali ke "-- Pilih Obat --"
        row.querySelector("select").selectedIndex = 0;
        tbody.appendChild(row);
    }

    function hapusBaris(btn) {
        let tbody = document.getElementById("resepBody");
        if (tbody.rows.length == 1) {
            alert("Minimal harus ada satu obat.");
            return;
        }
        btn.closest("tr").remove();
    }

    // Event Listener saat dokter memilih obat
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("obat-select")) {
            let option = e.target.options[e.target.selectedIndex];
            let row = e.target.closest("tr");
            let stok = option.dataset.stok;
            let satuan = option.dataset.satuan;

            // Mengisi input stok dan satuan secara otomatis
            row.querySelector(".stok").value = stok ? stok : "";
            row.querySelector(".satuan").value = satuan ? satuan : "";

            // BONUS VALIDASI: Set batasan max input jumlah sesuai stok obat yang ada
            let inputJumlah = row.querySelector("input[type='number']");
            if (stok) {
                inputJumlah.setAttribute("max", stok);
            } else {
                inputJumlah.removeAttribute("max");
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>