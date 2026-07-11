-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Jul 2026 pada 08.56
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simrs`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialis` varchar(50) NOT NULL,
  `poli` varchar(50) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `no_izin_praktek` varchar(30) DEFAULT NULL,
  `tgl_exp_sip` date DEFAULT NULL,
  `jadwal_hari` varchar(50) DEFAULT NULL,
  `jadwal_jam` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama_dokter`, `spesialis`, `poli`, `status_aktif`, `no_izin_praktek`, `tgl_exp_sip`, `jadwal_hari`, `jadwal_jam`, `created_at`, `updated_at`) VALUES
(1, 'dr. Budi Santoso, Sp.PD', 'Penyakit Dalam', 'Poli Umum', 1, '1234567890287', '2027-09-30', 'Selasa, Rabu, Kamis', '08:00 - 12:00', '2026-06-30 04:44:35', '2026-06-30 04:44:35'),
(2, 'dr. Ahmad Fauzi', 'Dokter Umum', 'Poli Umum', 1, '445/0123/SIP-DU/DPMPTSP/2025', NULL, 'Senin Rabu Jumat', '09.30 - 15.00', '2026-07-10 08:58:30', '2026-07-10 08:58:30');

-- --------------------------------------------------------

--
-- Struktur dari tabel `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `satuan` varchar(20) NOT NULL,
  `expired_date` date DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `minimal_stok` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `stok`, `harga`, `satuan`, `expired_date`, `kategori`, `minimal_stok`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol 500 mg', 21, 1000.00, 'Tablet', '2027-07-27', 'Analgesik & Antipiretik', 9, '2026-07-09 04:06:08', '2026-07-11 06:05:21'),
(2, 'Amoxicillin 500mg', 1, 10000.00, 'Tablet', '2029-09-09', 'Antibiotik', 10, '2026-07-11 04:42:02', '2026-07-11 06:22:34'),
(3, 'Cetirizine 10mg', 0, 25499.00, 'Tablet', '2987-08-09', 'Antihistamin', 10, '2026-07-11 04:42:54', '2026-07-11 06:30:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `no_rm` varchar(20) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `agama` varchar(20) NOT NULL,
  `pekerjaan` varchar(50) NOT NULL,
  `pendidikan` varchar(50) NOT NULL,
  `status_perkawinan` enum('Belum Menikah','Menikah','Cerai Hidup','Cerai Mati') NOT NULL,
  `nama_wali` varchar(100) DEFAULT NULL,
  `hubungan_wali` varchar(50) DEFAULT NULL,
  `no_hp_wali` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `no_rm`, `nik`, `nama`, `tgl_lahir`, `jenis_kelamin`, `alamat`, `no_hp`, `agama`, `pekerjaan`, `pendidikan`, `status_perkawinan`, `nama_wali`, `hubungan_wali`, `no_hp_wali`, `created_at`, `updated_at`) VALUES
(1, 'RM-2026-0001', '1234567812345678', 'Bunga Citra', '2023-10-30', 'P', 'Jl Ki Hajar Dewantara II', '081235467892', 'Islam', 'Pelajar', 'SD', 'Belum Menikah', 'Puji Astuti', 'Orang Tua', '081235467892', '2026-06-30 04:46:31', '2026-06-30 04:46:31'),
(2, 'RM-2026-0002', '3273055505980002', 'SANTI NUN', '2001-09-27', 'P', 'Jl. Merpati No. 45, RT 003/RW 002, Kelurahan Sekayu, Kecamatan Semarang Tengah, Kota Semarang, Jawa Tengah, 50132', '088298883', 'Hindu', 'Mahasiswa', 'S1', 'Belum Menikah', 'Wali Nun', 'Orang Tua', '089967446', '2026-07-10 08:35:37', '2026-07-10 08:35:37'),
(3, 'RM-2026-0003', '3564658708098097', 'HUHU', '7890-06-05', 'L', 'YKJHOUOK[K,IUUUYTDS', '8653217997', 'Konghucu', 'PEGAU', 'S1', 'Menikah', 'KOH', 'Orang Tua', '09996764656', '2026-07-11 04:46:27', '2026-07-11 04:46:27'),
(4, 'RM-2026-0004', '7283686489992003', 'NENENG', '2009-03-12', 'P', 'LFJOIAHFOSAHF', NULL, 'Katolik', 'Pelajar', 'SD', 'Belum Menikah', 'KJIIJ', 'Orang Tua', '90893716726376', '2026-07-11 06:07:08', '2026-07-11 06:07:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `tgl_bayar` datetime DEFAULT current_timestamp(),
  `metode` enum('TUNAI','TRANSFER','QRIS','EDC','ASURANSI') NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `kembalian` decimal(15,2) DEFAULT 0.00,
  `no_referensi` varchar(50) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_konfirmasi` enum('MENUNGGU','KONFIRMASI','BATAL') DEFAULT 'KONFIRMASI',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id_pemeriksaan` int(11) NOT NULL,
  `id_pendaftaran` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `keluhan` text DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `icd_10` varchar(10) DEFAULT NULL,
  `rencana_tindakan` text DEFAULT NULL,
  `tgl_pemeriksaan` datetime DEFAULT current_timestamp(),
  `tekanan_darah` varchar(20) DEFAULT NULL,
  `suhu_tubuh` decimal(4,1) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id_pemeriksaan`, `id_pendaftaran`, `id_dokter`, `keluhan`, `diagnosa`, `icd_10`, `rencana_tindakan`, `tgl_pemeriksaan`, `tekanan_darah`, `suhu_tubuh`, `berat_badan`, `tinggi_badan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Sering HALUSINASI', 'GILA', ' F20.0', 'minum kopi dan liburan', '2026-06-30 23:48:07', '120/80', 28.0, 56.00, 190.00, '2026-06-30 16:48:07', '2026-06-30 16:48:07'),
(2, 2, 1, 'batuk, pilek, kepala sakit, demam', 'Influenza (Flu)', 'J11', 'Konsumsi obat', '2026-07-10 15:48:44', '120/80', 39.0, 50.00, 166.00, '2026-07-10 08:48:44', '2026-07-10 08:48:44'),
(3, 2, 1, 'jhufsdfjsiyef', 'kjhiuysfja', 'J11', 'kdyiuayfoanf', '2026-07-10 15:55:10', '120/80', 36.0, 78.00, 180.00, '2026-07-10 08:55:10', '2026-07-10 08:55:10'),
(4, 3, 2, 'JUJUU', 'DJHFIA', ' F20.0', 'HUOAUIO', '2026-07-11 11:44:50', '120/80', 30.0, 38.00, 178.00, '2026-07-11 04:44:50', '2026-07-11 04:44:50'),
(5, 5, 2, 'FIUFKNSKJHF', 'KJAHFI', ' F20.0', 'ALJHFAFJBAKJNOJFLNF', '2026-07-11 13:09:11', '120/80', 30.0, 45.00, 129.90, '2026-07-11 06:09:11', '2026-07-11 06:09:11'),
(6, 4, 1, 'ohfoeo', 'fkajhoihfoe', 'J11', 'jhfoihfoq', '2026-07-11 13:22:34', '120/80', 35.9, 65.00, 188.90, '2026-07-11 06:22:34', '2026-07-11 06:22:34'),
(7, 6, 2, 'KHOIHFOKJJ', 'AJHFOAHF', '', 'AHFOUAH', '2026-07-11 13:28:17', '', 0.0, 0.00, 0.00, '2026-07-11 06:28:17', '2026-07-11 06:28:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `tgl_daftar` datetime DEFAULT current_timestamp(),
  `poli` varchar(50) NOT NULL,
  `jenis_kunjungan` enum('Baru','Lama') NOT NULL DEFAULT 'Baru',
  `jenis_penjamin` enum('Umum','BPJS','Asuransi') NOT NULL DEFAULT 'Umum',
  `no_kartu_bpjs` varchar(20) DEFAULT NULL,
  `no_antrean` varchar(20) NOT NULL,
  `status_daftar` enum('MENUNGGU','DIPERIKSA','SELESAI') DEFAULT 'MENUNGGU',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `id_pasien`, `id_dokter`, `tgl_daftar`, `poli`, `jenis_kunjungan`, `jenis_penjamin`, `no_kartu_bpjs`, `no_antrean`, `status_daftar`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-06-30 06:47:56', 'Poli Umum', 'Baru', 'BPJS', '2345609876', 'P001', '', '2026-06-30 04:47:56', '2026-06-30 04:50:30'),
(2, 2, 1, '2026-07-10 10:38:08', 'Poli Umum', 'Baru', 'Umum', NULL, 'P001', 'DIPERIKSA', '2026-07-10 08:38:08', '2026-07-10 08:55:10'),
(3, 1, 2, '2026-07-09 11:23:09', 'Poli Umum', 'Lama', 'Umum', NULL, 'P001', 'DIPERIKSA', '2026-07-10 09:23:09', '2026-07-11 04:44:50'),
(4, 3, 1, '2026-07-11 06:47:31', 'Poli Umum', 'Baru', 'Umum', NULL, 'P001', 'DIPERIKSA', '2026-07-11 04:47:31', '2026-07-11 06:22:34'),
(5, 4, 2, '2026-07-11 08:08:05', 'Poli Umum', 'Baru', 'Umum', NULL, 'P002', 'DIPERIKSA', '2026-07-11 06:08:05', '2026-07-11 06:09:11'),
(6, 3, 2, '2026-07-11 08:27:34', 'Poli Umum', 'Baru', 'Umum', NULL, 'P003', 'DIPERIKSA', '2026-07-11 06:27:34', '2026-07-11 06:28:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `resep`
--

CREATE TABLE `resep` (
  `id_resep` int(11) NOT NULL,
  `id_pemeriksaan` int(11) NOT NULL,
  `id_obat` int(11) NOT NULL,
  `dosis` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `aturan_pakai` text DEFAULT NULL,
  `status_ambil` enum('MENUNGGU','DIPROSES','SELESAI','BATAL') DEFAULT 'MENUNGGU',
  `tgl_resep` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `resep`
--

INSERT INTO `resep` (`id_resep`, `id_pemeriksaan`, `id_obat`, `dosis`, `jumlah`, `aturan_pakai`, `status_ambil`, `tgl_resep`, `created_at`, `updated_at`) VALUES
(2, 1, 1, '2', 1, 'Setelah kopi', '', '2026-07-09 11:07:06', '2026-07-09 04:07:06', '2026-07-09 04:07:06'),
(3, 2, 1, '0', 1, '3x1 Sesudah makan', 'SELESAI', '2026-07-10 15:48:44', '2026-07-10 08:48:44', '2026-07-11 06:04:41'),
(4, 3, 1, '0', 1, '3x1 Sesudah makan', 'SELESAI', '2026-07-10 15:55:10', '2026-07-10 08:55:10', '2026-07-11 06:05:21'),
(5, 4, 2, '0', 1, '1X1 SESUDAH MAKAN', 'MENUNGGU', '2026-07-11 11:44:50', '2026-07-11 04:44:50', '2026-07-11 04:44:50'),
(6, 5, 2, '0', 1, '1X1 SESUDAH MAKAN', 'MENUNGGU', '2026-07-11 13:09:11', '2026-07-11 06:09:11', '2026-07-11 06:09:11'),
(7, 6, 2, '0', 1, '1X1 SESUDAH MAKAN', 'MENUNGGU', '2026-07-11 13:22:34', '2026-07-11 06:22:34', '2026-07-11 06:22:34'),
(8, 7, 3, '500mg', 1, '1X1 SESUDAH MAKAN', 'SELESAI', '2026-07-11 13:28:17', '2026-07-11 06:28:17', '2026-07-11 06:30:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tagihan`
--

CREATE TABLE `tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `id_pendaftaran` int(11) NOT NULL,
  `total_biaya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `diskon` decimal(15,2) DEFAULT 0.00,
  `penjamin` varchar(50) DEFAULT 'UMUM',
  `status_bayar` enum('BELUM','LUNAS','CICIL','BATAL') DEFAULT 'BELUM',
  `tgl_tagihan` datetime DEFAULT current_timestamp(),
  `no_invoice` varchar(20) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`),
  ADD UNIQUE KEY `no_izin_praktek` (`no_izin_praktek`),
  ADD KEY `idx_poli` (`poli`),
  ADD KEY `idx_spesialis` (`spesialis`);

--
-- Indeks untuk tabel `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`),
  ADD KEY `idx_nama_obat` (`nama_obat`),
  ADD KEY `idx_stok` (`stok`),
  ADD KEY `idx_expired` (`expired_date`),
  ADD KEY `idx_kategori` (`kategori`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD UNIQUE KEY `no_rm` (`no_rm`),
  ADD UNIQUE KEY `uq_nik` (`nik`),
  ADD KEY `idx_no_rm` (`no_rm`),
  ADD KEY `idx_nama` (`nama`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `idx_tagihan` (`id_tagihan`),
  ADD KEY `idx_metode` (`metode`),
  ADD KEY `idx_no_referensi` (`no_referensi`),
  ADD KEY `idx_tgl_bayar` (`tgl_bayar`),
  ADD KEY `idx_status_konfirmasi` (`status_konfirmasi`);

--
-- Indeks untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD KEY `idx_pendaftaran` (`id_pendaftaran`),
  ADD KEY `idx_dokter` (`id_dokter`),
  ADD KEY `idx_icd10` (`icd_10`),
  ADD KEY `idx_tgl_pemeriksaan` (`tgl_pemeriksaan`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `idx_pasien` (`id_pasien`),
  ADD KEY `idx_tgl_daftar` (`tgl_daftar`),
  ADD KEY `idx_no_antrean` (`no_antrean`),
  ADD KEY `idx_poli` (`poli`),
  ADD KEY `idx_status` (`status_daftar`),
  ADD KEY `fk_pendaftaran_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `idx_pemeriksaan` (`id_pemeriksaan`),
  ADD KEY `idx_obat` (`id_obat`),
  ADD KEY `idx_status` (`status_ambil`),
  ADD KEY `idx_tgl_resep` (`tgl_resep`);

--
-- Indeks untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD UNIQUE KEY `no_invoice` (`no_invoice`),
  ADD KEY `idx_pendaftaran` (`id_pendaftaran`),
  ADD KEY `idx_status_bayar` (`status_bayar`),
  ADD KEY `idx_no_invoice` (`no_invoice`),
  ADD KEY `idx_penjamin` (`penjamin`),
  ADD KEY `idx_tgl_tagihan` (`tgl_tagihan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id_pemeriksaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `resep`
--
ALTER TABLE `resep`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `fk_pendaftaran_dokter` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `resep`
--
ALTER TABLE `resep`
  ADD CONSTRAINT `resep_ibfk_1` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `pemeriksaan` (`id_pemeriksaan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `resep_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id_obat`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
