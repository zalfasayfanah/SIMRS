<?php
$currentPage = $_SERVER['REQUEST_URI'];

function isActive(string $path): string
{
    global $currentPage;
    return str_contains($currentPage, $path) ? 'active' : '';
}

// Dashboard aktif kalau tidak berada di subfolder manapun
$isDashboard = !str_contains($currentPage, '/dokter/')
    && !str_contains($currentPage, '/pasien/')
    && !str_contains($currentPage, '/pendaftaran/')
    && !str_contains($currentPage, '/obat/')
    && !str_contains($currentPage, '/tagihan/')
    && !str_contains($currentPage, '/pembayaran/')
    && !str_contains($currentPage, '/pemeriksaan/')
    && !str_contains($currentPage, '/resep/');
?>

<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">&#43;</div>
        <div>
            <div class="brand-name">SIMRS</div>
            <div class="brand-sub">Front Office</div>
        </div>
    </div>

    <nav class="sidebar-nav">

        <!-- MENU UTAMA -->
        <div class="nav-section-label">Menu Utama</div>

        <a href="<?= $basePath ?? '' ?>index.php" class="nav-item <?= $isDashboard ? 'active' : '' ?>">
            <span class="nav-icon">&#9632;</span>
            Dashboard
        </a>

        <!-- DATA MASTER -->
        <div class="nav-section-label">Data Master</div>

        <a href="<?= $basePath ?? '' ?>pasien/index.php" class="nav-item <?= isActive('/pasien/') ?>">
            <span class="nav-icon">&#9632;</span>
            Pasien
        </a>

        <a href="<?= $basePath ?? '' ?>dokter/index.php" class="nav-item <?= isActive('/dokter/') ?>">
            <span class="nav-icon">&#9632;</span>
            Dokter
        </a>

        <a href="<?= $basePath ?? '' ?>obat/index.php" class="nav-item <?= isActive('/obat/') ?>">
            <span class="nav-icon">&#9632;</span>
            Obat
        </a>



        <!-- TRANSAKSI -->
        <div class="nav-section-label">Transaksi</div>

        <a href="<?= $basePath ?? '' ?>pendaftaran/index.php" class="nav-item <?= isActive('/pendaftaran/') ?>">
            <span class="nav-icon">&#9632;</span>
            Pendaftaran
        </a>

        <a href="<?= $basePath ?? '' ?>pemeriksaan/index.php" class="nav-item <?= isActive('/pemeriksaan/') ?>">
            <span class="nav-icon">&#9632;</span>
            Pemeriksaan
        </a>

        <a href="<?= $basePath ?? '' ?>farmasi/index.php" class="nav-item <?= isActive('/farmasi/') ?>">
            <span class="nav-icon">&#9632;</span>
            Farmasi
        </a>
        
        <a href="<?= $basePath ?? '' ?>pembayaran/index.php" class="nav-item <?= isActive('/pembayaran/') ?>">
            <span class="nav-icon">&#9632;</span>
            Pembayaran
        </a>

    </nav>

    <div class="sidebar-footer">
        Sistem Informasi Manajemen<br>
        Rumah Sakit
    </div>

</aside>
<div class="overlay" id="overlay"></div>