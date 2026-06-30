<?php
// ============================================================
// pasien/cari.php — Endpoint AJAX cari pasien
// Pencarian berdasarkan No. RM atau NIK (bukan ID)
// Dipanggil dari pendaftaran/daftar.php via fetch()
// ============================================================

require_once '../config/database.php';

header('Content-Type: application/json');

$keyword = trim($_GET['keyword'] ?? '');

if ($keyword === '') {
    echo json_encode(['nama' => null]);
    exit;
}

$kw = mysqli_real_escape_string($conn, $keyword);

// Cari berdasarkan No. RM ATAU NIK (exact match lebih diutamakan)
$result = mysqli_query($conn, "
    SELECT id_pasien, nama, no_rm, nik, tgl_lahir, jenis_kelamin
    FROM pasien
    WHERE no_rm = '$kw' OR nik = '$kw'
    LIMIT 1
");

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'id_pasien' => $row['id_pasien'],
        'nama'      => $row['nama'],
        'no_rm'     => $row['no_rm'],
        'nik'       => $row['nik'],
        'tgl_lahir' => $row['tgl_lahir'],
    ]);
} else {
    echo json_encode(['nama' => null]);
}