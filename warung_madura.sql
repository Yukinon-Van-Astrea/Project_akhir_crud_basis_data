-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 01:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warung_madura`
--

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` varchar(10) NOT NULL,
  `nama_pelanggan` varchar(150) NOT NULL,
  `no_hp` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_hp`) VALUES
('PLG001', 'Alexander the Great', '08123456701'),
('PLG002', 'Artoria Pendragon', '08123456702'),
('PLG003', 'Emiya Shirou', '08123456703'),
('PLG004', 'Jeanne d Arc', '08123456704'),
('PLG005', 'Merlin', '08123456705'),
('PLG006', 'Gilgamesh', '08123456706'),
('PLG007', 'Karna', '08123456707'),
('PLG008', 'Medusa', '08123456708'),
('PLG009', 'Uther Pendragon', '08123456709'),
('PLG010', 'Cu Chulainn', '08123456710'),
('PLG011', 'ibrahim', '0867457534');

--
-- Triggers `pelanggan`
--
DELIMITER $$
CREATE TRIGGER `trg_pelanggan_before_insert` BEFORE INSERT ON `pelanggan` FOR EACH ROW BEGIN
    IF NEW.id_pelanggan IS NULL OR NEW.id_pelanggan = '' THEN
        SET NEW.id_pelanggan = CONCAT(
            'PLG',
            LPAD(
                (SELECT IFNULL(MAX(CAST(SUBSTRING(id_pelanggan,4) AS UNSIGNED)),0) + 1 FROM pelanggan),
                3,
                '0'
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id_penjualan` varchar(12) NOT NULL,
  `id_produk` varchar(10) NOT NULL,
  `id_pelanggan` varchar(10) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `jumlah` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id_penjualan`, `id_produk`, `id_pelanggan`, `tanggal`, `jumlah`, `total_harga`) VALUES
('TRX001', 'PRD008', 'PLG001', '2025-11-28 13:54:45', 1, 4000.00),
('TRX002', 'PRD005', 'PLG004', '2025-11-28 13:55:21', 3, 75000.00),
('TRX003', 'PRD002', 'PLG003', '2025-11-28 13:55:36', 3, 66000.00),
('TRX004', 'PRD006', 'PLG009', '2025-11-28 13:55:50', 2, 16000.00),
('TRX005', 'PRD010', 'PLG002', '2025-11-28 13:58:07', 10, 60000.00),
('TRX006', 'PRD009', 'PLG006', '2025-11-28 13:58:23', 5, 35000.00),
('TRX007', 'PRD004', 'PLG010', '2025-11-28 13:59:16', 12, 180000.00),
('TRX008', 'PRD009', 'PLG007', '2025-11-28 13:59:28', 7, 49000.00),
('TRX009', 'PRD001', 'PLG008', '2025-11-28 13:59:48', 5, 90000.00),
('TRX010', 'PRD007', 'PLG005', '2025-11-28 14:00:03', 15, 75000.00);

--
-- Triggers `penjualan`
--
DELIMITER $$
CREATE TRIGGER `trg_penjualan_before_insert` BEFORE INSERT ON `penjualan` FOR EACH ROW BEGIN
    IF NEW.id_penjualan IS NULL OR NEW.id_penjualan = '' THEN
        SET NEW.id_penjualan = CONCAT(
            'TRX',
            LPAD(
                (SELECT IFNULL(MAX(CAST(SUBSTRING(id_penjualan,4) AS UNSIGNED)),0) + 1 FROM penjualan),
                3,
                '0'
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` varchar(10) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `stok`) VALUES
('PRD001', 'Es Kopi Susu', 18000.00, 40),
('PRD002', 'Coklat Panas', 22000.00, 17),
('PRD003', 'Kopi Hitam', 12000.00, 50),
('PRD004', 'Roti Bakar', 15000.00, 18),
('PRD005', 'Nasi Goreng', 25000.00, 22),
('PRD006', 'Indomie Goreng', 8000.00, 98),
('PRD007', 'Keripik Pedas', 5000.00, 20),
('PRD008', 'Air Mineral', 4000.00, 79),
('PRD009', 'Susu Kotak', 7000.00, 28),
('PRD010', 'Teh Manis', 6000.00, 50);

--
-- Triggers `produk`
--
DELIMITER $$
CREATE TRIGGER `trg_produk_before_insert` BEFORE INSERT ON `produk` FOR EACH ROW BEGIN
    IF NEW.id_produk IS NULL OR NEW.id_produk = '' THEN
        SET NEW.id_produk = CONCAT(
            'PRD',
            LPAD(
                (SELECT IFNULL(MAX(CAST(SUBSTRING(id_produk,4) AS UNSIGNED)),0) + 1 FROM produk),
                3,
                '0'
            )
        );
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id_penjualan`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
