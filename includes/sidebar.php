<?php
$currentPage = $_SERVER['REQUEST_URI'];

function isActive(string $path): string {
    global $currentPage;
    return str_contains($currentPage, $path) ? 'active' : '';
}

// Dashboard aktif kalau tidak berada di subfolder manapun
$isDashboard = !str_contains($currentPage, '/dokter/')
            && !str_contains($currentPage, '/pasien/')
            && !str_contains($currentPage, '/pendaftaran/');
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
        <div class="nav-section-label">Menu Utama</div>

        <a href="<?= $basePath ?? '' ?>index.php"
           class="nav-item <?= $isDashboard ? 'active' : '' ?>">
            <span class="nav-icon">&#9632;</span>
            Dashboard
        </a>

        <div class="nav-section-label">Data Master</div>

        <a href="<?= $basePath ?? '' ?>pasien/index.php"
           class="nav-item <?= isActive('/pasien/') ?>">
            <span class="nav-icon">&#9632;</span>
            Pasien
        </a>

        <a href="<?= $basePath ?? '' ?>dokter/index.php"
           class="nav-item <?= isActive('/dokter/') ?>">
            <span class="nav-icon">&#9632;</span>
            Dokter
        </a>

        <div class="nav-section-label">Transaksi</div>

        <a href="<?= $basePath ?? '' ?>pendaftaran/index.php"
           class="nav-item <?= isActive('/pendaftaran/') ?>">
            <span class="nav-icon">&#9632;</span>
            Pendaftaran
        </a>
    </nav>

    <div class="sidebar-footer">
        Sistem Informasi Manajemen<br>Rumah Sakit
    </div>
</aside>