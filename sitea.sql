-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Jan 2026 pada 10.55
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sitea`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tabel` varchar(50) DEFAULT NULL,
  `action` enum('INSERT','UPDATE','DELETE') DEFAULT 'INSERT',
  `data_lama` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_lama`)),
  `data_baru` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_baru`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahan_masuk`
--

CREATE TABLE `bahan_masuk` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `kebun` varchar(100) NOT NULL,
  `jenis_bahan` varchar(50) DEFAULT NULL,
  `berat_awal` decimal(10,2) NOT NULL,
  `kondisi` enum('Baik','Cukup','Kurang Baik') DEFAULT 'Baik',
  `catatan` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `petugas` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bahan_masuk`
--

INSERT INTO `bahan_masuk` (`id`, `tanggal`, `user_id`, `kebun`, `jenis_bahan`, `berat_awal`, `kondisi`, `catatan`, `status`, `created_at`, `updated_at`, `petugas`) VALUES
(1, '2026-01-18', 3, 'Kebun A', 'Daun Segar', 100.50, 'Baik', 'Kualitas premium', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54', ''),
(2, '2026-01-18', 4, 'Kebun B', 'Daun Segar', 85.25, 'Baik', 'Kondisi baik', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54', ''),
(3, '2026-01-17', 3, 'Kebun C', 'Daun Segar', 120.75, 'Cukup', 'Ada beberapa daun rusak', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54', ''),
(4, '2026-01-16', 4, 'Kebun A', 'Daun Segar', 95.50, 'Baik', 'Kuantitas standar', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54', ''),
(5, '2026-01-15', 3, 'Kebun B', 'Daun Segar', 110.00, 'Kurang Baik', 'Perlu pemilahan ulang', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `energi`
--

CREATE TABLE `energi` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `mesin_proses` varchar(50) NOT NULL,
  `jenis_energi` enum('Listrik','Gas','Air') DEFAULT 'Listrik',
  `jumlah` decimal(10,2) NOT NULL,
  `satuan` varchar(20) DEFAULT NULL,
  `biaya` decimal(15,2) DEFAULT 0.00,
  `user_id` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `energi`
--

INSERT INTO `energi` (`id`, `tanggal`, `mesin_proses`, `jenis_energi`, `jumlah`, `satuan`, `biaya`, `user_id`, `catatan`, `status`, `created_at`, `updated_at`) VALUES
(1, '2026-01-18', 'Mesin Pengering 1', 'Listrik', 150.00, 'kWh', 225000.00, 3, 'Operasional normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(2, '2026-01-18', 'Mesin Pengering 2', 'Listrik', 145.50, 'kWh', 218250.00, 3, 'Operasional normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(3, '2026-01-18', 'Boiler', 'Gas', 45.75, 'm³', 550000.00, 3, 'Tekanan normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(4, '2026-01-17', 'Mesin Pengering 1', 'Listrik', 155.25, 'kWh', 232875.00, 3, 'Operasional normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(5, '2026-01-17', 'Mesin Pengering 2', 'Listrik', 148.75, 'kWh', 223125.00, 3, 'Operasional normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(6, '2026-01-17', 'Boiler', 'Gas', 47.50, 'm³', 570000.00, 3, 'Tekanan tinggi', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_kp`
--

CREATE TABLE `laporan_kp` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_bahan_masuk` decimal(10,2) DEFAULT 0.00,
  `total_produksi` decimal(10,2) DEFAULT 0.00,
  `total_penyusutan` decimal(10,2) DEFAULT 0.00,
  `rendemen_persen` decimal(5,2) DEFAULT 0.00,
  `total_energi_listrik` decimal(10,2) DEFAULT 0.00,
  `total_energi_gas` decimal(10,2) DEFAULT 0.00,
  `biaya_total_energi` decimal(15,2) DEFAULT 0.00,
  `batch_qc_lolos` int(11) DEFAULT 0,
  `batch_qc_total` int(11) DEFAULT 0,
  `persentase_lolos` decimal(5,2) DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan_kp`
--

INSERT INTO `laporan_kp` (`id`, `tanggal`, `total_bahan_masuk`, `total_produksi`, `total_penyusutan`, `rendemen_persen`, `total_energi_listrik`, `total_energi_gas`, `biaya_total_energi`, `batch_qc_lolos`, `batch_qc_total`, `persentase_lolos`, `catatan`, `status`, `created_at`, `updated_at`) VALUES
(1, '2026-01-18', 385.50, 330.50, 55.00, 85.73, 295.50, 45.75, 993250.00, 5, 6, 83.33, 'Produksi normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(2, '2026-01-17', 362.50, 310.00, 52.50, 85.52, 304.00, 47.50, 1026000.00, 8, 10, 80.00, 'Produksi normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(3, '2026-01-16', 320.00, 270.00, 50.00, 84.38, 280.00, 43.50, 950000.00, 7, 8, 87.50, 'Produksi normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penimbangan`
--

CREATE TABLE `penimbangan` (
  `id` int(11) NOT NULL,
  `petugas` varchar(200) NOT NULL,
  `tanggal` date NOT NULL,
  `tahap` enum('Withering','Drying','Sorting','Packaging') DEFAULT 'Drying',
  `berat_awal` decimal(10,2) NOT NULL,
  `berat_akhir` decimal(10,2) NOT NULL,
  `penyusutan` decimal(10,2) GENERATED ALWAYS AS (`berat_awal` - `berat_akhir`) STORED,
  `persentase_penyusutan` decimal(5,2) GENERATED ALWAYS AS (`penyusutan` / `berat_awal` * 100) STORED,
  `user_id` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penimbangan`
--

INSERT INTO `penimbangan` (`id`, `petugas`, `tanggal`, `tahap`, `berat_awal`, `berat_akhir`, `user_id`, `catatan`, `status`, `created_at`, `updated_at`) VALUES
(1, '', '2026-01-18', 'Withering', 100.00, 75.00, 3, 'Withering tahap 1', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(2, '', '2026-01-18', 'Drying', 75.00, 60.00, 3, 'Drying tahap 2', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(3, '', '2026-01-18', 'Sorting', 60.00, 58.00, 3, 'Sorting tahap 3', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(4, '', '2026-01-18', 'Packaging', 58.00, 57.50, 3, 'Packaging tahap 4', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(5, '', '2026-01-17', 'Withering', 95.00, 72.00, 3, 'Withering normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(6, '', '2026-01-17', 'Drying', 72.00, 57.50, 3, 'Drying normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(7, '', '2026-01-17', 'Sorting', 57.50, 55.50, 3, 'Sorting normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(8, '', '2026-01-17', 'Packaging', 55.50, 55.00, 3, 'Packaging normal', 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `qc_laporan`
--

CREATE TABLE `qc_laporan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tahap_proses` varchar(50) NOT NULL,
  `parameter` varchar(100) DEFAULT NULL,
  `hasil` varchar(100) DEFAULT NULL,
  `nilai_numerik` decimal(10,2) DEFAULT NULL,
  `status_hasil` enum('Lolos','Gagal','Perlu Review') DEFAULT 'Lolos',
  `catatan` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `qc_laporan`
--

INSERT INTO `qc_laporan` (`id`, `tanggal`, `tahap_proses`, `parameter`, `hasil`, `nilai_numerik`, `status_hasil`, `catatan`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '2026-01-18', 'Withering', 'Color', 'Dark Green', 8.50, 'Lolos', 'Warna bagus', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(2, '2026-01-18', 'Withering', 'Moisture', 'Within Range', 45.00, 'Lolos', 'Kelembaban OK', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(3, '2026-01-18', 'Drying', 'Final Moisture', 'Below 5%', 4.20, 'Lolos', 'Kering sempurna', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(4, '2026-01-18', 'Sorting', 'Grade', 'Grade A', 9.00, 'Lolos', 'Kualitas premium', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(5, '2026-01-18', 'Sorting', 'Foreign Matter', 'Minimal', 0.50, 'Lolos', 'Hampir tidak ada', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(6, '2026-01-18', 'Packaging', 'Weight', 'Accurate', 100.00, 'Lolos', 'Berat sesuai', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(7, '2026-01-17', 'Withering', 'Color', 'Dark Green', 8.00, 'Lolos', 'Warna bagus', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(8, '2026-01-17', 'Drying', 'Final Moisture', 'Below 5%', 4.50, 'Lolos', 'Kering OK', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(9, '2026-01-17', 'Sorting', 'Grade', 'Grade B', 7.50, 'Perlu Review', 'Grade B perlu review', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54'),
(10, '2026-01-17', 'Packaging', 'Weight', 'Inaccurate', 99.50, 'Gagal', 'Berat tidak sesuai', 4, 'active', '2026-01-18 09:02:54', '2026-01-18 09:02:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `jabatan` enum('Admin','Kepala Pabrik','Operator Produksi','Quality Control') DEFAULT 'Operator Produksi',
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `full_name`, `jabatan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', 'admin', '$2y$10$1XNB1kFFkg4H/5vSFbKXs.3/z4oXGgy.NCzbrF0mvhv4DZ2sipd46', 'Admin User', 'Admin', 'active', '2026-01-18 09:02:54', '2026-01-18 09:10:39'),
(2, 'kepala@gmail.com', 'kepala pabrik', '$2y$10$1XNB1kFFkg4H/5vSFbKXs.3/z4oXGgy.NCzbrF0mvhv4DZ2sipd46', 'Budi Hartono', 'Kepala Pabrik', 'active', '2026-01-18 09:02:54', '2026-01-18 09:10:46'),
(3, 'operator@gmail.com', 'operator', '$2y$10$1XNB1kFFkg4H/5vSFbKXs.3/z4oXGgy.NCzbrF0mvhv4DZ2sipd46', 'Andi Wijaya', 'Operator Produksi', 'active', '2026-01-18 09:02:54', '2026-01-18 09:10:52'),
(4, 'qc@gmail.com', 'qc staff', '$2y$10$1XNB1kFFkg4H/5vSFbKXs.3/z4oXGgy.NCzbrF0mvhv4DZ2sipd46', 'Budi Santoso', 'Quality Control', 'active', '2026-01-18 09:02:54', '2026-01-18 09:10:59');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_audit` (`user_id`),
  ADD KEY `idx_tabel_audit` (`tabel`),
  ADD KEY `idx_timestamp_audit` (`timestamp`);

--
-- Indeks untuk tabel `bahan_masuk`
--
ALTER TABLE `bahan_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_tanggal_bahan` (`tanggal`),
  ADD KEY `idx_kebun` (`kebun`),
  ADD KEY `idx_kondisi` (`kondisi`);

--
-- Indeks untuk tabel `energi`
--
ALTER TABLE `energi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_tanggal_energi` (`tanggal`),
  ADD KEY `idx_mesin` (`mesin_proses`),
  ADD KEY `idx_jenis_energi` (`jenis_energi`);

--
-- Indeks untuk tabel `laporan_kp`
--
ALTER TABLE `laporan_kp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tanggal` (`tanggal`);

--
-- Indeks untuk tabel `penimbangan`
--
ALTER TABLE `penimbangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_tanggal_timbang` (`tanggal`),
  ADD KEY `idx_tahap` (`tahap`);

--
-- Indeks untuk tabel `qc_laporan`
--
ALTER TABLE `qc_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_tanggal_qc` (`tanggal`),
  ADD KEY `idx_tahap_proses` (`tahap_proses`),
  ADD KEY `idx_status_hasil` (`status_hasil`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_jabatan` (`jabatan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bahan_masuk`
--
ALTER TABLE `bahan_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `energi`
--
ALTER TABLE `energi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `laporan_kp`
--
ALTER TABLE `laporan_kp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `penimbangan`
--
ALTER TABLE `penimbangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `qc_laporan`
--
ALTER TABLE `qc_laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bahan_masuk`
--
ALTER TABLE `bahan_masuk`
  ADD CONSTRAINT `bahan_masuk_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `energi`
--
ALTER TABLE `energi`
  ADD CONSTRAINT `energi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `penimbangan`
--
ALTER TABLE `penimbangan`
  ADD CONSTRAINT `penimbangan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `qc_laporan`
--
ALTER TABLE `qc_laporan`
  ADD CONSTRAINT `qc_laporan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
