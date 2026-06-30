<?php
require_once '../config/database.php';

$id = $_GET['id'];

if (isset($_POST['update'])) {

    mysqli_query($conn, "
        UPDATE resep SET
        dosis='$_POST[dosis]',
        jumlah='$_POST[jumlah]',
        aturan_pakai='$_POST[aturan_pakai]',
        status_ambil='$_POST[status_ambil]'
        WHERE id_resep='$id'
    ");

    header("Location:index.php");
}

$data = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM resep WHERE id_resep='$id'"
    )
);

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

                <label>Dosis</label>
                <input type="text" name="dosis" value="<?= $data['dosis'] ?>">

                <label>Jumlah</label>
                <input type="number" name="jumlah" value="<?= $data['jumlah'] ?>">

                <label>Aturan Pakai</label>
                <textarea name="aturan_pakai"><?= $data['aturan_pakai'] ?></textarea>

                <label>Status Ambil</label>

                <select name="status_ambil">

                    <option value="BELUM" <?= $data['status_ambil'] == 'BELUM' ? 'selected' : '' ?>>
                        BELUM
                    </option>

                    <option value="SUDAH" <?= $data['status_ambil'] == 'SUDAH' ? 'selected' : '' ?>>
                        SUDAH
                    </option>

                </select>

                <br><br>

                <button class="btn btn-primary" name="update">
                    Update
                </button>

            </form>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>