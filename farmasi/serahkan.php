<?php

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if($id<=0){
    header("Location:index.php");
    exit;
}

/*
Ambil data resep
*/
$sql = "

SELECT

r.id_obat,
r.jumlah,
r.status_ambil,

pm.id_pemeriksaan,

pd.id_pendaftaran,

t.id_tagihan

FROM resep r

JOIN pemeriksaan pm
ON r.id_pemeriksaan=pm.id_pemeriksaan

JOIN pendaftaran pd
ON pm.id_pendaftaran=pd.id_pendaftaran

JOIN tagihan t
ON t.id_pendaftaran=pd.id_pendaftaran

WHERE r.id_resep=?

";

$stmt=mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$data=mysqli_stmt_get_result($stmt);

$data=mysqli_fetch_assoc($data);

if(!$data){

    header("Location:index.php");

    exit;

}

/*
Kalau belum selesai,
potong stok
*/

if($data['status_ambil']!='SELESAI'){

    mysqli_query($conn,"
        UPDATE obat
        SET stok=stok-".$data['jumlah']."
        WHERE id_obat=".$data['id_obat']."
    ");

}

/*
Update resep
*/

mysqli_query($conn,"
UPDATE resep
SET status_ambil='SELESAI'
WHERE id_resep=".$id."
");

/*
Masuk Billing
*/

header("Location:../pembayaran/bayar.php?id=".$data['id_tagihan']);

exit;