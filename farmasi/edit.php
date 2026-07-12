<?php
require_once '../config/database.php';

$pageTitle = "Edit Resep";
$basePath = "../";

$id = mysqli_real_escape_string($conn, $_GET['id']);

if (isset($_POST['update'])) {
    $dosis = mysqli_real_escape_string($conn, $_POST['dosis']);
    $jumlah = (int)$_POST['jumlah'];
    $aturan_pakai = mysqli_real_escape_string($conn, $_POST['aturan_pakai']);
    $status_ambil = mysqli_real_escape_string($conn, $_POST['status_ambil']);

    // PERBAIKAN DI SINI: Tanda petik string sudah diperbaiki agar tidak tabrakan
    $cekOld = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_obat, jumlah, status_ambil FROM resep WHERE id_resep='$id'"));

    mysqli_query($conn, "
        UPDATE resep SET
        dosis= '$dosis',
        jumlah='$jumlah',
        aturan_pakai='$aturan_pakai',
        status_ambil='$status_ambil'
        WHERE id_resep='$id'
    ");

    // 3. LOGIKA POTONG STOK: Jika status berubah menjadi SELESAI dan sebelumnya BUKAN SELESAI
    if ($status_ambil == 'SELESAI' && $cekOld['status_ambil'] != 'SELESAI') {
        // Potong stok obat berdasarkan id_obat di resep ini
        mysqli_query($conn, "UPDATE obat SET stok = stok - $jumlah WHERE id_obat = '{$cekOld['id_obat']}'");
    }
    
    // LOGIKA PENGEMBALIAN STOK: Jika status dibatalkan/diubah dari SELESAI ke status lain (Batal/Menunggu)
    if ($status_ambil != 'SELESAI' && $cekOld['status_ambil'] == 'SELESAI') {
        // Kembalikan stok obat yang sempat dipotong sebelumnya
        mysqli_query($conn, "UPDATE obat SET stok = stok + {$cekOld['jumlah']} WHERE id_obat = '{$cekOld['id_obat']}'");
    }

    if($status_ambil=='SELESAI'){

    header("Location:../pembayaran/bayar.php?id=".$getTagihan['id_tagihan']);

    }else{

        header("Location:index.php");

    }

    exit();
}

    $getTagihan = mysqli_fetch_assoc(mysqli_query($conn, "

    SELECT t.id_tagihan

    FROM tagihan t

    JOIN pendaftaran pd
    ON t.id_pendaftaran=pd.id_pendaftaran

    JOIN pemeriksaan pm
    ON pm.id_pendaftaran=pd.id_pendaftaran

    JOIN resep r
    ON r.id_pemeriksaan=pm.id_pemeriksaan

    WHERE r.id_resep='$id'

    "));

include '../includes/header.php';
include '../includes/sidebar.php';
?>
<div class="main-content">

    <div class="card">

        <div class="card-header">
            <h2>Edit Resep</h2>
        </div>

        <div class="card-body">

          <form method="POST">

    <div class="form-group">
        <label>Dosis</label>
        <input
            type="text"
            name="dosis"
            class="form-control"
            value="<?= htmlspecialchars($data['dosis']) ?>">
    </div>

    <div class="form-group">
        <label>Jumlah</label>
        <input
            type="number"
            name="jumlah"
            class="form-control"
            value="<?= $data['jumlah'] ?>">
    </div>

    <div class="form-group">
        <label>Aturan Pakai</label>
        <textarea
            name="aturan_pakai"
            class="form-control"
            rows="4"><?= htmlspecialchars($data['aturan_pakai']) ?></textarea>
    </div>

    <div class="form-group">
        <label>Status Ambil</label>

        <select
            name="status_ambil"
            class="form-control">

            <option value="MENUNGGU"
                <?= $data['status_ambil']=='MENUNGGU' ? 'selected' : '' ?>>
                MENUNGGU
            </option>

            <option value="DIPROSES"
                <?= $data['status_ambil']=='DIPROSES' ? 'selected' : '' ?>>
                DIPROSES
            </option>

            <option value="SELESAI"
                <?= $data['status_ambil']=='SELESAI' ? 'selected' : '' ?>>
                SELESAI
            </option>

            <option value="BATAL"
                <?= $data['status_ambil']=='BATAL' ? 'selected' : '' ?>>
                BATAL
            </option>

        </select>
    </div>

    <div style="margin-top:20px; display:flex; gap:10px;">

        <a href="index.php" class="btn btn-secondary">
            Kembali
        </a>

        <button
            type="submit"
            name="update"
            class="btn btn-primary">

            Simpan Perubahan

        </button>

    </div>

</form>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>