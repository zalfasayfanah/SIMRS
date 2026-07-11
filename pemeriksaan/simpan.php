<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location:index.php");
    exit;
}

mysqli_begin_transaction($conn);

try {

    $id_pendaftaran = $_POST['id_pendaftaran'];
    $id_dokter      = $_POST['id_dokter'];

    $keluhan            = $_POST['keluhan'];
    $diagnosa           = $_POST['diagnosa'];
    $icd                = $_POST['icd_10'];
    $rencana            = $_POST['rencana_tindakan'];

    $td                 = $_POST['tekanan_darah'];
    $suhu               = $_POST['suhu_tubuh'];
    $bb                 = $_POST['berat_badan'];
    $tb                 = $_POST['tinggi_badan'];

    /* ===========================================
       SIMPAN PEMERIKSAAN
    ============================================ */

    $stmt = mysqli_prepare($conn,"
        INSERT INTO pemeriksaan
        (
            id_pendaftaran,
            id_dokter,
            keluhan,
            diagnosa,
            icd_10,
            rencana_tindakan,
            tekanan_darah,
            suhu_tubuh,
            berat_badan,
            tinggi_badan
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?,?,?
        )
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "iisssssddd",
        $id_pendaftaran,
        $id_dokter,
        $keluhan,
        $diagnosa,
        $icd,
        $rencana,
        $td,
        $suhu,
        $bb,
        $tb
    );

    mysqli_stmt_execute($stmt);

    $id_pemeriksaan = mysqli_insert_id($conn);



   /* ===========================================
       SIMPAN RESEP
    ============================================ */

    $obat   = $_POST['id_obat'];
    $dosis_array = $_POST['dosis']; // 1. TANGKAP ARRAY DOSIS DARI FORM HTML
    $jumlah = $_POST['jumlah'];
    $aturan = $_POST['aturan_pakai'];

    foreach($obat as $i => $id_obat){

        $jml          = $jumlah[$i];
        $aturan_pakai = $aturan[$i];
        $dosis        = mysqli_real_escape_string($conn, $dosis_array[$i]); // 2. AMBIL DOSIS SESUAI INDEKS LOOPING

        if(empty($id_obat))
            continue;

        //cek stok
        $cek = mysqli_query($conn,"
            SELECT stok
            FROM obat
            WHERE id_obat='$id_obat'
        ");

        $stok = mysqli_fetch_assoc($cek);

        if($stok['stok'] < $jml){
            throw new Exception("Stok obat tidak mencukupi.");
        }

        //insert resep
        $stmt = mysqli_prepare($conn,"
            INSERT INTO resep
            (
                id_pemeriksaan,
                id_obat,
                dosis,
                jumlah,
                aturan_pakai
            )
            VALUES
            (
                ?, ?, ?, ?, ?
            )
        ");

        // 3. PASIKAN URUTAN TIPE DATA PADA BIND PARAM BENAR:
        // i = id_pemeriksaan (int)
        // s = id_obat (string/varchar) -> sesuaikan jika id_obat di DB kamu int, ubah jadi 'i'
        // s = dosis (string)
        // i = jumlah (int)
        // s = aturan_pakai (string)
        mysqli_stmt_bind_param(
            $stmt,
            "issis", 
            $id_pemeriksaan,
            $id_obat,
            $dosis, // Sekarang variabel ini sudah dinamis menangkap input dokter
            $jml,
            $aturan_pakai
        );

        mysqli_stmt_execute($stmt);

        //kurangi stok
        mysqli_query($conn,"
            UPDATE obat
            SET stok = stok - $jml
            WHERE id_obat='$id_obat'
        ");
    }

    /* ===========================================
       UPDATE STATUS PENDAFTARAN
    ============================================ */
    mysqli_query($conn,"
        UPDATE pendaftaran
        SET status_daftar='DIPERIKSA'
        WHERE id_pendaftaran='$id_pendaftaran'
    ");

    mysqli_commit($conn);
    header("Location:index.php?id_dokter=".$id_dokter."&sukses=1");

}
catch(Exception $e){
    mysqli_rollback($conn);
    die($e->getMessage());

}