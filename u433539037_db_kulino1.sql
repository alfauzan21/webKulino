-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 04, 2025 at 05:29 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u433539037_db_kulino1`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_berita`
--

CREATE TABLE `tb_berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_berita`
--

INSERT INTO `tb_berita` (`id`, `judul`, `deskripsi`, `link`, `gambar`, `created_at`) VALUES
(3, 'Homestay', 'bakaskjakdaskda', NULL, '1759230649.jpg', '2025-09-30 11:10:49'),
(7, 'Kulino NaikCukup Drastis', 'Lorem....', 'https://www.instagram.com/kulinohouse', '1763018670.jpg', '2025-11-13 07:24:30'),
(8, 'JENIPAPO NEW', 'Kini dikulino hadir Tattoo Temporary', 'https://www.instagram.com/kulinohouse', '1763217594.jpg', '2025-11-15 14:39:54');

-- --------------------------------------------------------

--
-- Table structure for table `tb_games`
--

CREATE TABLE `tb_games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video_hover` varchar(255) DEFAULT NULL COMMENT 'Video untuk hover effect',
  `game_url` varchar(500) NOT NULL COMMENT 'URL atau path ke game',
  `badge` varchar(50) DEFAULT NULL COMMENT 'New, Hot, Top, Updated, Popular',
  `is_featured` tinyint(1) DEFAULT 0 COMMENT '1 = Featured Game, 0 = Regular Game',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = Active, 0 = Coming Soon',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Urutan tampilan',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_games`
--

INSERT INTO `tb_games` (`id`, `title`, `description`, `image`, `video_hover`, `game_url`, `badge`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(13, 'Fly to The Moon', 'ly to the Moon adalah game 2D bertema petualangan luar angkasa, berusaha terbang setinggi mungkin untuk mendapatkan koin KULINO', '1763463459_img.png', '', 'fly-to-the-moon', 'New', 1, 1, 2, '2025-11-18 10:57:39', '2025-11-28 12:13:16');

-- --------------------------------------------------------

--
-- Table structure for table `tb_marketplace`
--

CREATE TABLE `tb_marketplace` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL COMMENT 'Aksesoris atau Board Game',
  `subcategory` varchar(100) NOT NULL COMMENT 'Baju, Ganci, Topi, dll',
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL COMMENT 'Harga coret jika ada diskon',
  `image` varchar(255) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tb_marketplace`
--

INSERT INTO `tb_marketplace` (`id`, `product_name`, `category`, `subcategory`, `description`, `price`, `original_price`, `image`, `stock`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Jenipapo', 'Board Game', 'Monopoly', 'Sebuah Tattoo yang baguss dan tanpa takut permanent', 1500000.00, 200000.00, '1763955935_6923d4df48987.jpg', 10, 1, '2025-11-24 03:45:35', '2025-11-24 09:42:57'),
(2, 'Gelas', 'Aksesoris', 'Gelas', 'Gelas canggih yang bewarna putih', 200000.00, 10000000.00, '1763977533_6924293d057cd.png', 2, 1, '2025-11-24 09:45:33', '2025-11-24 09:45:33'),
(3, 'T-Shirt Kulino Size ( M - XXL )', 'Aksesoris', 'Baju', 'akffdaokfdsknfsodno', 100000.00, 150000.00, '1763978784_69242e20d0ada.jpg', 12, 1, '2025-11-24 10:06:24', '2025-11-24 10:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sponsor`
--

CREATE TABLE `tb_sponsor` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_sponsor`
--

INSERT INTO `tb_sponsor` (`id`, `nama`, `gambar`, `link`) VALUES
(1, 'Mobile Legends', '1759902494.jpg', 'https://www.youtube.com/watch?v=-R92GsDLipg'),
(2, 'kulino', '1759903843.PNG', 'https://www.instagram.com/kulinohouse');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id`, `username`, `password`) VALUES
(1, 'kulinoweb123', '225421cbc2a1143e0f108bf56ad4a60a');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `device` varchar(255) DEFAULT 'Unknown Device',
  `latitude` decimal(10,8) DEFAULT NULL COMMENT 'GPS Latitude',
  `longitude` decimal(11,8) DEFAULT NULL COMMENT 'GPS Longitude',
  `street_address` varchar(255) DEFAULT NULL COMMENT 'Street address (Jl. Ciliwung No. 20)',
  `city` varchar(100) DEFAULT NULL COMMENT 'City/Kabupaten',
  `region` varchar(100) DEFAULT NULL COMMENT 'Province/State',
  `country` varchar(100) DEFAULT NULL COMMENT 'Country name',
  `country_code` varchar(5) DEFAULT NULL COMMENT 'Country code (ID, US, etc)',
  `postal_code` varchar(20) DEFAULT NULL COMMENT 'Postal/ZIP code',
  `full_address` text DEFAULT NULL COMMENT 'Complete formatted address',
  `location_accuracy` decimal(10,2) DEFAULT NULL COMMENT 'GPS accuracy in meters',
  `timezone` varchar(50) DEFAULT 'Asia/Jakarta',
  `visited_at` timestamp NULL DEFAULT current_timestamp(),
  `street` varchar(255) DEFAULT NULL COMMENT 'Street/Road name',
  `house_number` varchar(50) DEFAULT NULL COMMENT 'House/Building number',
  `district` varchar(100) DEFAULT NULL COMMENT 'District/Kecamatan',
  `subdistrict` varchar(100) DEFAULT NULL COMMENT 'Subdistrict/Kelurahan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `ip_address`, `device`, `latitude`, `longitude`, `street_address`, `city`, `region`, `country`, `country_code`, `postal_code`, `full_address`, `location_accuracy`, `timezone`, `visited_at`, `street`, `house_number`, `district`, `subdistrict`) VALUES
(1, '103.47.134.122', 'Windows 10 - Chrome', -7.25750000, 112.75210000, NULL, 'Jember', 'Jawa Timur', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-04 04:29:28', 'Jalan Kapten Sarjpan', '123', 'Tamanasari', 'Kelurahan Tamanasari'),
(2, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', '', '', NULL, 'Asia/Jakarta', '2025-12-04 12:02:15', '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_berita`
--
ALTER TABLE `tb_berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_games`
--
ALTER TABLE `tb_games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_marketplace`
--
ALTER TABLE `tb_marketplace`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_sponsor`
--
ALTER TABLE `tb_sponsor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visited_date` (`visited_at`),
  ADD KEY `idx_ip_device` (`ip_address`,`device`),
  ADD KEY `idx_country` (`country`),
  ADD KEY `idx_city_country` (`city`,`country`),
  ADD KEY `idx_timezone` (`timezone`),
  ADD KEY `idx_latitude_longitude` (`latitude`,`longitude`),
  ADD KEY `idx_street_city` (`street_address`,`city`),
  ADD KEY `idx_postal_code` (`postal_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_berita`
--
ALTER TABLE `tb_berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_games`
--
ALTER TABLE `tb_games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_marketplace`
--
ALTER TABLE `tb_marketplace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_sponsor`
--
ALTER TABLE `tb_sponsor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
