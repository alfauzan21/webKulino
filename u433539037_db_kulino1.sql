-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 04, 2025 at 03:38 AM
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
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `country_code` varchar(5) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL COMMENT 'Postal/ZIP code',
  `full_address` text DEFAULT NULL COMMENT 'Complete formatted address',
  `location_accuracy` decimal(10,2) DEFAULT NULL COMMENT 'GPS accuracy in meters',
  `timezone` varchar(50) DEFAULT 'Asia/Jakarta',
  `visited_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `ip_address`, `device`, `latitude`, `longitude`, `street_address`, `city`, `region`, `country`, `country_code`, `postal_code`, `full_address`, `location_accuracy`, `timezone`, `visited_at`) VALUES
(1, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'Asia/Jakarta', '2025-11-28 07:07:32'),
(2, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'Asia/Jakarta', '2025-11-28 07:10:17'),
(3, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'Asia/Jakarta', '2025-11-28 07:14:55'),
(4, '103.47.134.122', 'iOS - Safari', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'Asia/Jakarta', '2025-11-28 07:15:37'),
(5, '103.47.134.122', 'Android - Chrome', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'Asia/Jakarta', '2025-11-28 07:17:48'),
(6, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 08:35:03'),
(7, '103.47.134.122', 'iOS - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 08:38:07'),
(8, '103.47.134.122', 'MacOS - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 09:29:48'),
(9, '103.47.134.122', 'MacOS - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 09:30:42'),
(10, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 10:51:59'),
(11, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 10:52:23'),
(12, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 11:09:44'),
(13, '103.156.70.177', 'iOS - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 11:26:32'),
(14, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:03:14'),
(15, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:06:27'),
(16, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:11:58'),
(17, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:12:26'),
(18, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:12:34'),
(19, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:13:25'),
(20, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 12:18:05'),
(21, '202.65.229.190', 'iOS - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 13:02:44'),
(22, '202.65.229.190', 'iOS - Browser', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 13:02:54'),
(23, '202.65.229.190', 'iOS - Browser', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 13:06:02'),
(24, '202.65.229.190', 'MacOS - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 13:06:47'),
(25, '202.65.229.190', 'iOS - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 13:06:49'),
(26, '202.65.229.190', 'iOS - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 14:06:31'),
(27, '202.65.229.190', 'iOS - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 14:11:03'),
(28, '202.65.229.190', 'iOS - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 14:11:07'),
(29, '103.156.70.177', 'Windows PC - Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-28 17:01:15'),
(30, '103.47.134.122', 'Android - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 02:31:44'),
(31, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 04:04:53'),
(32, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 04:08:26'),
(33, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 04:11:02'),
(34, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:36:25'),
(35, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:37:48'),
(36, '103.47.134.122', 'iOS - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:43:21'),
(37, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:43:55'),
(38, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:46:21'),
(39, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:47:00'),
(40, '103.47.134.122', 'Windows PC - Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:48:06'),
(41, '103.47.134.122', 'iOS - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 06:51:16'),
(42, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:34:28'),
(43, '103.47.134.122', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:36:18'),
(44, '103.47.134.122', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:39:18'),
(45, '202.65.229.190', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:40:37'),
(46, '202.65.229.190', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:40:40'),
(47, '202.65.229.190', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:44:40'),
(48, '202.65.229.190', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:46:42'),
(49, '103.47.134.122', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:48:34'),
(50, '202.65.229.190', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:48:53'),
(51, '202.65.229.190', 'iPhone - Unknown Browser', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:49:03'),
(52, '103.47.134.122', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:51:08'),
(53, '103.47.134.122', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:53:28'),
(54, '114.5.105.19', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, 'Surabaya, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:54:10'),
(55, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 15:55:28'),
(56, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 16:56:29'),
(57, '66.249.72.2', 'Android - Google Chrome', NULL, NULL, NULL, 'Oskaloosa', 'IA', 'United States', 'US', NULL, 'Oskaloosa, IA, United States', NULL, 'America/Chicago', '2025-11-29 04:37:57'),
(58, '66.249.72.2', 'Android - Google Chrome', NULL, NULL, NULL, 'Oskaloosa', 'IA', 'United States', 'US', NULL, 'Oskaloosa, IA, United States', NULL, 'America/Chicago', '2025-11-29 04:38:14'),
(59, '66.249.72.2', 'Unknown Device - Google Chrome', NULL, NULL, NULL, 'Oskaloosa', 'IA', 'United States', 'US', NULL, 'Oskaloosa, IA, United States', NULL, 'America/Chicago', '2025-11-29 04:38:16'),
(60, '2001:448a:5130:a3fa:f412:77d4:5423:4b7c', 'iPhone - Safari', NULL, NULL, NULL, 'Jember', 'JI', 'Indonesia', 'ID', NULL, 'Jember, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 21:35:02'),
(61, '103.47.134.102', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, 'Jakarta, JK, Indonesia', NULL, 'Asia/Jakarta', '2025-11-29 21:46:19'),
(62, '103.156.70.177', 'iPhone - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-30 11:41:58'),
(63, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-30 20:38:42'),
(64, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-30 20:55:41'),
(65, '103.156.70.181', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, 'Sobo Krajan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-11-30 21:04:01'),
(66, '103.156.70.181', 'Android - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, 'Sobo Krajan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 02:55:06'),
(67, '103.156.70.181', 'Android - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, 'Sobo Krajan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 02:55:30'),
(68, '103.156.70.181', 'Android - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, 'Sobo Krajan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 02:57:58'),
(69, '103.156.70.181', 'Android - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, 'Sobo Krajan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 02:58:45'),
(70, '103.156.70.181', 'Android - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, 'Sobo Krajan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 03:09:57'),
(71, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 03:23:14'),
(72, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 03:29:25'),
(73, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 03:31:26'),
(74, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 03:33:03'),
(75, '103.156.70.177', 'Windows 10 - Opera', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, 'Tukangkayu Krajan Selatan, JI, Indonesia', NULL, 'Asia/Jakarta', '2025-12-01 03:34:01'),
(76, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 04:26:13'),
(77, '103.156.70.177', 'iPhone - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 04:26:29'),
(78, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 04:27:57'),
(79, '103.156.70.177', 'iPhone - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 07:17:03'),
(80, '103.156.70.177', 'iPhone - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 07:17:07'),
(81, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 07:50:07'),
(82, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 07:55:27'),
(83, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 08:01:14'),
(84, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 08:04:45'),
(85, '103.165.210.82', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tangerang', 'BT', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 08:22:38'),
(86, '103.165.210.82', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tangerang', 'BT', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 09:25:44'),
(87, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 09:26:09'),
(88, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 14:31:54'),
(89, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 14:42:58'),
(90, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 15:45:23'),
(91, '103.47.134.122', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 20:10:29'),
(92, '103.156.70.181', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 22:40:51'),
(93, '103.156.70.181', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 22:58:50'),
(94, '103.156.70.181', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-01 23:08:32'),
(95, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:16'),
(96, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:17'),
(97, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:17'),
(98, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:18'),
(99, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:18'),
(100, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:20'),
(101, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:22'),
(102, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:23'),
(103, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:25'),
(104, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:26'),
(105, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:26'),
(106, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:26'),
(107, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:28'),
(108, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:28'),
(109, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:28'),
(110, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:28'),
(111, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:29'),
(112, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:29'),
(113, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:29'),
(114, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:30'),
(115, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:32'),
(116, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:32'),
(117, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:31:32'),
(118, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:37'),
(119, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:41'),
(120, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:41'),
(121, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:57'),
(122, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:57'),
(123, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:57'),
(124, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:31:58'),
(125, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:31:59'),
(126, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:32:00'),
(127, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:32:00'),
(128, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:32:00'),
(129, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:32:00'),
(130, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:32:03'),
(131, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:32:03'),
(132, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:32:03'),
(133, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:32:04'),
(134, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:06'),
(135, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:08'),
(136, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:08'),
(137, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:10'),
(138, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:13'),
(139, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:13'),
(140, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:39'),
(141, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:42'),
(142, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:32:42'),
(143, '176.113.177.232', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Bucharest', 'B', 'Romania', 'RO', NULL, NULL, NULL, 'Europe/Bucharest', '2025-12-02 06:33:07'),
(144, '103.196.9.200', 'MacOS - Google Chrome', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:33:10'),
(145, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:33:28'),
(146, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:33:51'),
(147, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:33:52'),
(148, '154.49.199.176', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:33:52'),
(149, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:33:53'),
(150, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:33:54'),
(151, '154.49.199.177', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Vélizy-Villacoublay', 'IDF', 'France', 'FR', NULL, NULL, NULL, 'Europe/Paris', '2025-12-02 05:33:54'),
(152, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:33:57'),
(153, '103.196.9.250', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:33:58'),
(154, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:34:07'),
(155, '103.4.250.29', 'MacOS - Google Chrome', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:34:09'),
(156, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:34:10'),
(157, '104.6.210.243', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Mesquite', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:34:11'),
(158, '103.4.251.88', 'iPhone - Safari', NULL, NULL, NULL, 'New York', 'NY', 'United States', 'US', NULL, NULL, NULL, 'America/New_York', '2025-12-01 23:34:20'),
(159, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:34:36'),
(160, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:34:39'),
(161, '66.169.202.182', 'MacOS - Google Chrome', NULL, NULL, NULL, 'Waxahachie', 'TX', 'United States', 'US', NULL, NULL, NULL, 'America/Chicago', '2025-12-01 22:34:39'),
(162, '103.156.70.181', 'Android - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 18:16:13'),
(163, '114.125.126.22', 'Android - Google Chrome', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 18:19:01'),
(164, '125.166.116.236', 'iPhone - Safari', NULL, NULL, NULL, 'Jember', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 22:14:52'),
(165, '125.166.116.236', 'Android - Google Chrome', NULL, NULL, NULL, 'Jember', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 22:15:58'),
(166, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 22:51:01'),
(167, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 22:51:14'),
(168, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 23:20:28'),
(169, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 23:26:37'),
(170, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-02 23:55:37'),
(171, '103.156.70.177', 'Android - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 05:38:21'),
(172, '103.156.70.177', 'Android - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 05:38:52'),
(173, '103.156.70.177', 'Android - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 05:40:32'),
(174, '103.156.70.181', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Sobo Krajan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 06:52:41'),
(175, '103.156.70.177', 'iPhone - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 08:02:10'),
(176, '114.5.223.223', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 08:02:59'),
(177, '103.156.70.177', 'iPhone - Safari', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 08:03:44'),
(178, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:23:41'),
(179, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:24:31'),
(180, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:26:05'),
(181, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:30:35'),
(182, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:32:02'),
(183, '103.47.134.122', 'Xiaomi Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:32:22'),
(184, '103.172.197.69', 'Android - Google Chrome', NULL, NULL, NULL, 'Banyuwangi', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:35:25'),
(185, '103.172.197.69', 'Android - Google Chrome', NULL, NULL, NULL, 'Banyuwangi', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 09:36:08'),
(186, '114.5.223.223', 'iPhone - Safari', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 13:18:41'),
(187, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:19:20'),
(188, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:19:42'),
(189, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:19:43'),
(190, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:20:00'),
(191, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:20:44'),
(192, '103.47.134.122', 'Android - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:21:47'),
(193, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 14:24:39'),
(194, '202.65.234.76', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Surabaya', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 17:07:30'),
(195, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 21:52:32'),
(196, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 21:55:21'),
(197, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:12:33'),
(198, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:13:32'),
(199, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:14:33'),
(200, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:15:46'),
(201, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:16:05'),
(202, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:16:13'),
(203, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:16:20'),
(204, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 22:23:20'),
(205, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 23:36:10'),
(206, '103.156.70.177', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Tukangkayu Krajan Selatan', 'JI', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-03 23:40:34'),
(207, '103.47.134.122', 'iPhone - Safari', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-04 09:47:34'),
(208, '103.47.134.122', 'Windows 10 - Google Chrome', NULL, NULL, NULL, 'Jakarta', 'JK', 'Indonesia', 'ID', NULL, NULL, NULL, 'Asia/Jakarta', '2025-12-04 10:34:58');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
