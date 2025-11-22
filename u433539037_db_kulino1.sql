-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 22, 2025 at 09:19 AM
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
(13, 'Fly to The Moon', 'ly to the Moon adalah game 2D bertema petualangan luar angkasa, berusaha terbang setinggi mungkin untuk mendapatkan koin KULINO', '1763463459_img.png', '', 'block', 'New', 1, 1, 2, '2025-11-18 10:57:39', '2025-11-21 07:34:15');

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
  `visited_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

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
  ADD KEY `idx_ip_device` (`ip_address`,`device`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
