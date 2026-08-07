-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 29, 2026 at 09:10 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `biqala`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(11,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `building_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `floor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apartment_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `title`, `address`, `latitude`, `longitude`, `building_number`, `floor`, `apartment_number`, `note`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 33, NULL, 'شارع بغداد بجوار الميناء', 24.55582600, 46.69064060, NULL, NULL, NULL, NULL, 0, '2026-07-28 11:28:55', '2026-07-28 11:40:36', NULL),
(2, 33, NULL, 'شارع الحرية  الثاني', 24.55087152, 46.68065115, NULL, NULL, NULL, NULL, 1, '2026-07-28 11:34:48', '2026-07-28 11:40:36', NULL),
(3, 33, NULL, 'شارع قلقلياء', 24.55582600, 46.69064060, NULL, NULL, NULL, NULL, 0, '2026-07-28 11:38:38', '2026-07-28 11:41:28', '2026-07-28 11:41:28'),
(4, 33, NULL, 'شارع قلقلياء', 24.55582600, 46.69064060, NULL, NULL, NULL, NULL, 0, '2026-07-28 11:51:33', '2026-07-28 11:51:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_variation` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `type`, `created_at`, `updated_at`, `deleted_at`, `is_variation`) VALUES
(2, 'color', '2025-12-21 11:21:15', '2026-07-26 15:31:30', NULL, 1),
(3, 'select', '2025-12-21 11:21:29', '2026-07-26 15:44:39', NULL, 1),
(4, 'select', '2025-12-21 11:21:51', '2025-12-21 11:21:51', NULL, 0),
(5, 'select', '2026-04-26 09:31:04', '2026-04-26 09:31:04', NULL, 0),
(6, 'select', '2026-04-26 09:39:03', '2026-07-26 15:42:08', NULL, 0),
(8, 'select', '2026-07-25 12:21:20', '2026-07-25 12:21:20', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_translations`
--

CREATE TABLE `attribute_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `attribute_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_translations`
--

INSERT INTO `attribute_translations` (`id`, `attribute_id`, `locale`, `name`) VALUES
(3, 2, 'ar', 'اللون'),
(4, 2, 'en', 'Color'),
(5, 3, 'ar', 'المقاس'),
(6, 3, 'en', 'Size'),
(7, 4, 'ar', 'الطول'),
(8, 4, 'en', 'height'),
(9, 5, 'ar', 'التغليف'),
(10, 5, 'en', 'Packaging'),
(11, 6, 'ar', 'التجفيف'),
(12, 6, 'en', 'drying'),
(15, 8, 'ar', 'الوزن'),
(16, 8, 'en', 'weight');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` bigint UNSIGNED NOT NULL,
  `attribute_id` bigint UNSIGNED NOT NULL,
  `color_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `attribute_id`, `color_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 3, NULL, '2025-12-21 12:23:15', '2025-12-21 12:23:15', NULL),
(3, 3, NULL, '2025-12-21 12:38:48', '2025-12-21 12:44:56', NULL),
(6, 2, '#ed1010', '2026-01-01 11:24:38', '2026-01-01 11:24:38', NULL),
(7, 3, NULL, '2026-01-02 16:39:09', '2026-01-02 16:39:09', NULL),
(8, 3, NULL, '2026-01-02 16:39:19', '2026-01-02 16:39:19', NULL),
(9, 4, NULL, '2026-01-02 16:39:49', '2026-01-02 16:39:49', NULL),
(10, 4, NULL, '2026-01-02 16:40:13', '2026-01-02 16:40:13', NULL),
(11, 4, NULL, '2026-01-02 16:40:59', '2026-01-02 16:40:59', NULL),
(12, 2, '#f5fe0f', '2026-01-02 16:41:44', '2026-01-02 16:41:44', NULL),
(13, 2, '#69c803', '2026-01-02 16:42:25', '2026-01-02 16:42:25', NULL),
(14, 2, '#ffffff', '2026-01-02 16:42:42', '2026-01-02 16:42:42', NULL),
(15, 2, '#f58904', '2026-01-02 16:43:08', '2026-01-02 16:43:08', NULL),
(16, 2, '#000000', '2026-01-06 15:53:55', '2026-01-06 15:53:55', NULL),
(17, 5, NULL, '2026-04-26 09:31:24', '2026-04-26 09:31:24', NULL),
(18, 5, NULL, '2026-04-26 09:31:39', '2026-04-26 09:31:39', NULL),
(19, 5, NULL, '2026-04-26 09:32:02', '2026-04-26 09:32:02', NULL),
(20, 6, NULL, '2026-04-26 09:39:27', '2026-04-26 09:39:27', NULL),
(21, 6, NULL, '2026-04-26 09:39:55', '2026-04-26 09:39:55', NULL),
(22, 6, NULL, '2026-04-26 09:40:16', '2026-04-26 09:40:16', NULL),
(25, 8, NULL, '2026-07-25 12:21:42', '2026-07-25 12:21:42', NULL),
(26, 8, NULL, '2026-07-25 12:22:03', '2026-07-25 12:22:03', NULL),
(27, 8, NULL, '2026-07-25 12:22:13', '2026-07-25 12:22:13', NULL),
(28, 3, NULL, '2026-07-26 16:09:09', '2026-07-26 16:09:09', NULL),
(29, 3, NULL, '2026-07-26 16:09:19', '2026-07-26 16:09:19', NULL),
(30, 3, NULL, '2026-07-26 16:09:29', '2026-07-26 16:09:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value_product`
--

CREATE TABLE `attribute_value_product` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `attribute_value_id` bigint UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(10,2) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_value_product`
--

INSERT INTO `attribute_value_product` (`id`, `product_id`, `attribute_value_id`, `price`, `quantity`, `is_default`, `sort_order`, `created_at`, `updated_at`) VALUES
(140, 44, 25, 0.00, NULL, 0, 0, '2026-07-25 12:23:25', '2026-07-25 12:23:25'),
(141, 44, 26, 0.00, NULL, 0, 0, '2026-07-25 12:23:25', '2026-07-25 12:23:25'),
(142, 44, 27, 0.00, NULL, 0, 0, '2026-07-25 12:23:25', '2026-07-25 12:23:25'),
(143, 87, 6, 50.00, 30.00, 0, 0, '2026-07-26 12:53:31', '2026-07-26 12:54:22'),
(144, 87, 12, 40.00, 20.00, 0, 0, '2026-07-26 12:53:31', '2026-07-26 12:54:22'),
(145, 87, 13, 22.00, 12.00, 0, 0, '2026-07-26 12:53:31', '2026-07-26 12:54:22'),
(146, 87, 15, 15.00, 11.00, 0, 0, '2026-07-26 12:53:31', '2026-07-26 12:54:22'),
(147, 89, 6, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(148, 89, 12, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(149, 89, 13, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(150, 89, 14, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(151, 89, 15, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(152, 89, 16, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(153, 89, 2, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(154, 89, 3, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(155, 89, 7, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(156, 89, 8, 0.00, NULL, 0, 0, '2026-07-26 15:57:26', '2026-07-26 15:57:26'),
(157, 90, 17, 20.00, NULL, 0, 0, '2026-07-26 16:03:43', '2026-07-26 16:04:07'),
(158, 90, 18, 30.00, NULL, 0, 0, '2026-07-26 16:03:43', '2026-07-26 16:04:07'),
(159, 90, 19, 40.00, NULL, 0, 0, '2026-07-26 16:03:43', '2026-07-26 16:04:07'),
(160, 91, 28, 0.00, NULL, 0, 0, '2026-07-26 16:09:52', '2026-07-26 16:09:52'),
(161, 91, 29, 0.00, NULL, 0, 0, '2026-07-26 16:09:52', '2026-07-26 16:09:52'),
(162, 91, 30, 0.00, NULL, 0, 0, '2026-07-26 16:09:52', '2026-07-26 16:09:52'),
(163, 92, 6, 0.00, NULL, 0, 0, '2026-07-26 16:13:42', '2026-07-26 16:13:42'),
(164, 92, 12, 0.00, NULL, 0, 0, '2026-07-26 16:13:42', '2026-07-26 16:13:42'),
(165, 92, 2, 0.00, NULL, 0, 0, '2026-07-26 16:13:42', '2026-07-26 16:13:42'),
(166, 92, 7, 0.00, NULL, 0, 0, '2026-07-26 16:13:42', '2026-07-26 16:13:42'),
(167, 92, 17, 25.00, NULL, 0, 0, '2026-07-26 16:13:57', '2026-07-26 16:14:51'),
(168, 92, 19, 50.00, NULL, 0, 0, '2026-07-26 16:13:57', '2026-07-26 16:14:51'),
(169, 94, 6, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(170, 94, 12, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(171, 94, 13, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(172, 94, 14, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(173, 94, 2, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(174, 94, 3, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(175, 94, 7, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20'),
(176, 94, 8, 0.00, NULL, 0, 0, '2026-07-26 16:21:20', '2026-07-26 16:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value_translations`
--

CREATE TABLE `attribute_value_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `attribute_value_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_value_translations`
--

INSERT INTO `attribute_value_translations` (`id`, `attribute_value_id`, `locale`, `value`) VALUES
(3, 2, 'ar', 'XL'),
(4, 2, 'en', 'XL'),
(5, 3, 'ar', 'L'),
(6, 3, 'en', 'L'),
(11, 6, 'ar', 'احمر'),
(12, 6, 'en', 'red'),
(13, 7, 'ar', 'M'),
(14, 7, 'en', 'M'),
(15, 8, 'ar', 'S'),
(16, 8, 'en', 'S'),
(17, 9, 'ar', 'متر'),
(18, 9, 'en', 'Meter'),
(19, 10, 'ar', 'انش'),
(20, 10, 'en', 'inches'),
(21, 11, 'ar', 'سنتيمتر'),
(22, 11, 'en', 'Centimeter'),
(23, 12, 'ar', 'اصفر'),
(24, 12, 'en', 'Yellow'),
(25, 13, 'ar', 'اخضر'),
(26, 13, 'en', 'Green'),
(27, 14, 'ar', 'ابيض'),
(28, 14, 'en', 'White'),
(29, 15, 'ar', 'برتقالي'),
(30, 15, 'en', 'Orange'),
(31, 16, 'ar', 'اسود'),
(32, 16, 'en', 'black'),
(33, 17, 'ar', 'هدية'),
(34, 17, 'en', 'gift'),
(35, 18, 'ar', 'رمضان'),
(36, 18, 'en', 'Ramadan'),
(37, 19, 'ar', 'مناسبة'),
(38, 19, 'en', 'suitable'),
(39, 20, 'ar', 'طبيعي'),
(40, 20, 'en', 'natural'),
(41, 21, 'ar', 'صحي'),
(42, 21, 'en', 'healthy'),
(43, 22, 'ar', 'مرضى السكر'),
(44, 22, 'en', 'diabetic patients'),
(49, 25, 'ar', '1 كيلو'),
(50, 25, 'en', '1 kg'),
(51, 26, 'ar', '2 كيلو'),
(52, 26, 'en', '2 kg'),
(53, 27, 'ar', '3 كيلو'),
(54, 27, 'en', '3 kg'),
(55, 28, 'ar', 'EU 42.3'),
(56, 28, 'en', 'EU 42.3'),
(57, 29, 'ar', 'EU 43'),
(58, 29, 'en', 'EU 43'),
(59, 30, 'ar', 'EU 45'),
(60, 30, 'en', 'EU 45');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint UNSIGNED NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `phone`, `lat`, `lng`, `is_main`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 1, 1, 0, '2026-07-25 10:32:40', '2026-07-25 10:32:40');

-- --------------------------------------------------------

--
-- Table structure for table `branch_product`
--

CREATE TABLE `branch_product` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `stock_quantity` int UNSIGNED DEFAULT NULL,
  `price_override` decimal(10,2) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_translations`
--

CREATE TABLE `branch_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branch_translations`
--

INSERT INTO `branch_translations` (`id`, `branch_id`, `locale`, `name`, `address`) VALUES
(1, 1, 'ar', 'الفرع الرئيسي', NULL),
(2, 1, 'en', 'Main Branch', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `image`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '3fd9e336-befb-48f2-9214-97d2faa65719.webp', 1, NULL, '2025-12-21 10:27:49', '2026-04-26 08:52:44'),
(2, 'd5dea48d-6be6-44b4-9fa3-4b78a7d5442e.webp', 1, NULL, '2025-12-21 10:30:38', '2026-04-26 08:50:58'),
(3, '0e9e9f78-fb0c-4b90-b197-fcddad0e78e4.webp', 1, NULL, '2025-12-21 10:31:26', '2026-04-26 08:49:17'),
(4, 'b0292b8e-c26c-4026-8dfc-e17e9554c765.webp', 1, NULL, '2025-12-21 10:32:21', '2026-04-26 08:49:54'),
(5, '929d3095-aea2-4aa6-8c7d-661004c4fed4.webp', 1, NULL, '2026-07-25 11:24:44', '2026-07-25 11:24:44'),
(6, '20d67299-849c-485c-b51a-c04c668729ef.webp', 1, NULL, '2026-07-25 11:25:50', '2026-07-25 11:25:50'),
(7, '0dfa77ca-8dd1-4322-b925-0eded6f92ecc.webp', 1, NULL, '2026-07-25 11:26:21', '2026-07-25 11:26:21'),
(8, '3c109e03-207a-4588-ab20-a61b622d8467.webp', 1, NULL, '2026-07-25 11:27:04', '2026-07-25 11:27:04'),
(9, 'ab95f0a6-acf5-43fe-ba79-beb1d96bfb93.webp', 1, NULL, '2026-07-25 11:27:59', '2026-07-25 11:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `brand_translations`
--

CREATE TABLE `brand_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `brand_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand_translations`
--

INSERT INTO `brand_translations` (`id`, `brand_id`, `locale`, `name`, `slug`) VALUES
(1, 1, 'ar', 'تمور الزاد', 'تمور-الزاد'),
(2, 1, 'en', 'Dates of Zad', 'dates-of-zad'),
(3, 2, 'ar', 'تمور المملكة', 'تمور-المملكة'),
(4, 2, 'en', 'Kingdom Dates', 'kingdom-dates'),
(5, 3, 'ar', 'تمور ليوا', 'تمور-ليوا'),
(6, 3, 'en', 'Liwa dates', 'liwa-dates'),
(7, 4, 'ar', 'تامرينا', 'تامرينا'),
(8, 4, 'en', 'Tamrina', 'tamrina'),
(9, 5, 'ar', 'كوكا كولا', 'كوكا-كولا'),
(10, 5, 'en', 'Coca-Cola', 'coca-cola'),
(11, 6, 'ar', 'نستلا', 'نستلا'),
(12, 6, 'en', 'Nestlé', 'nestle'),
(13, 7, 'ar', 'المراعي', 'المراعي'),
(14, 7, 'en', 'Almarai', 'almarai'),
(15, 8, 'ar', 'اوريو', 'اوريو'),
(16, 8, 'en', 'Oreo', 'oreo'),
(17, 9, 'ar', 'ديتول', 'ديتول'),
(18, 9, 'en', 'Dettol', 'dettol');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('biqala-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:7;', 1790331131),
('biqala-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1790331130;', 1790331130),
('biqala-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:3:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";}s:11:\"permissions\";a:27:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:4:\"Home\";s:1:\"c\";s:5:\"admin\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"General_Settings\";s:1:\"c\";s:5:\"admin\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"Store_Settings\";s:1:\"c\";s:5:\"admin\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:21:\"Store_policy_settings\";s:1:\"c\";s:5:\"admin\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"Social_Media\";s:1:\"c\";s:5:\"admin\";}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:19:\"Cancel_Reason_Order\";s:1:\"c\";s:5:\"admin\";}i:6;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:11:\"Onboardings\";s:1:\"c\";s:5:\"admin\";}i:7;a:3:{s:1:\"a\";i:8;s:1:\"b\";s:7:\"Clients\";s:1:\"c\";s:5:\"admin\";}i:8;a:3:{s:1:\"a\";i:9;s:1:\"b\";s:6:\"Orders\";s:1:\"c\";s:5:\"admin\";}i:9;a:3:{s:1:\"a\";i:10;s:1:\"b\";s:8:\"Products\";s:1:\"c\";s:5:\"admin\";}i:10;a:3:{s:1:\"a\";i:11;s:1:\"b\";s:10:\"Categories\";s:1:\"c\";s:5:\"admin\";}i:11;a:3:{s:1:\"a\";i:12;s:1:\"b\";s:6:\"Brands\";s:1:\"c\";s:5:\"admin\";}i:12;a:3:{s:1:\"a\";i:13;s:1:\"b\";s:16:\"Product_Features\";s:1:\"c\";s:5:\"admin\";}i:13;a:3:{s:1:\"a\";i:14;s:1:\"b\";s:20:\"Offers and discounts\";s:1:\"c\";s:5:\"admin\";}i:14;a:3:{s:1:\"a\";i:15;s:1:\"b\";s:9:\"The_Sales\";s:1:\"c\";s:5:\"admin\";}i:15;a:3:{s:1:\"a\";i:16;s:1:\"b\";s:18:\"Products_purchased\";s:1:\"c\";s:5:\"admin\";}i:16;a:3:{s:1:\"a\";i:17;s:1:\"b\";s:6:\"Alerts\";s:1:\"c\";s:5:\"admin\";}i:17;a:3:{s:1:\"a\";i:18;s:1:\"b\";s:9:\"TextPages\";s:1:\"c\";s:5:\"admin\";}i:18;a:3:{s:1:\"a\";i:19;s:1:\"b\";s:9:\"Slideshow\";s:1:\"c\";s:5:\"admin\";}i:19;a:3:{s:1:\"a\";i:20;s:1:\"b\";s:10:\"Contact_Us\";s:1:\"c\";s:5:\"admin\";}i:20;a:3:{s:1:\"a\";i:21;s:1:\"b\";s:22:\"Working_days_and_times\";s:1:\"c\";s:5:\"admin\";}i:21;a:3:{s:1:\"a\";i:22;s:1:\"b\";s:17:\"Delivery_Settings\";s:1:\"c\";s:5:\"admin\";}i:22;a:3:{s:1:\"a\";i:23;s:1:\"b\";s:7:\"Drivers\";s:1:\"c\";s:5:\"admin\";}i:23;a:3:{s:1:\"a\";i:24;s:1:\"b\";s:15:\"Payment_Methods\";s:1:\"c\";s:5:\"admin\";}i:24;a:3:{s:1:\"a\";i:25;s:1:\"b\";s:5:\"Units\";s:1:\"c\";s:5:\"admin\";}i:25;a:3:{s:1:\"a\";i:26;s:1:\"b\";s:28:\"Verification_method_settings\";s:1:\"c\";s:5:\"admin\";}i:26;a:3:{s:1:\"a\";i:27;s:1:\"b\";s:23:\"Loyalty_Points_Settings\";s:1:\"c\";s:5:\"admin\";}}s:5:\"roles\";a:0:{}}', 1785322058);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `image`, `is_active`, `parent_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(15, '38be3184-9979-4aab-948d-9164adea13c9.webp', 1, NULL, '2026-07-25 11:30:12', '2026-07-25 11:30:12', NULL),
(16, '0c3c4d43-735b-492f-b6e4-affa5418fd46.webp', 1, 15, '2026-07-25 11:31:40', '2026-07-25 11:31:40', NULL),
(17, 'c282a84c-50e0-4350-bfd3-3529b8976670.webp', 1, 15, '2026-07-25 11:33:01', '2026-07-25 11:33:01', NULL),
(18, 'c258f0f0-bc44-491b-965c-15e69f39bd2a.webp', 1, 15, '2026-07-25 11:35:12', '2026-07-25 11:35:12', NULL),
(19, 'f892d569-556f-43fb-92e8-22626d513715.webp', 1, 15, '2026-07-25 11:36:16', '2026-07-25 11:36:16', NULL),
(20, '9e851a5f-283a-44a7-b78c-c23e8c9fb1dc.webp', 1, NULL, '2026-07-25 11:37:38', '2026-07-25 11:37:38', NULL),
(21, '4363432a-82ce-4cc2-9a24-829fca80fb59.webp', 1, 20, '2026-07-25 11:39:01', '2026-07-25 11:39:01', NULL),
(22, '252b7e7d-582b-449a-8acf-372f6a4e05bc.webp', 1, 20, '2026-07-25 11:40:35', '2026-07-25 11:40:35', NULL),
(23, '23c31f9e-4173-4c5f-99d8-d34a7c495665.webp', 1, 20, '2026-07-25 11:41:55', '2026-07-25 11:41:55', NULL),
(24, 'd72b3c4d-89be-4d75-974d-cf24a68a17f7.webp', 1, NULL, '2026-07-25 11:43:40', '2026-07-25 11:43:40', NULL),
(25, '44b91e4b-288b-438c-80fb-6edb293054f6.webp', 1, 24, '2026-07-25 11:45:43', '2026-07-25 11:45:43', NULL),
(26, '5ec7f125-f7fa-4acc-bf24-3f8c06dbe9a8.webp', 1, 24, '2026-07-25 11:46:32', '2026-07-25 11:46:32', NULL),
(28, 'da83618b-ba2f-46fd-bc54-0816aa8aaedc.webp', 1, 24, '2026-07-25 11:48:31', '2026-07-25 11:48:31', NULL),
(32, '13d942db-cb06-4302-8d9b-d89f0f76638b.webp', 1, NULL, '2026-07-25 11:52:13', '2026-07-25 11:52:13', NULL),
(33, '9a8474ae-b19c-448d-b543-6ad1c2cc5c52.webp', 1, 32, '2026-07-25 11:54:01', '2026-07-25 11:54:01', NULL),
(34, '6064c713-3e33-43b7-b194-eee5f9cc87ef.webp', 1, 32, '2026-07-25 11:55:20', '2026-07-25 11:55:20', NULL),
(38, '00456fa9-7023-4c50-9e01-3ae76528c489.webp', 1, NULL, '2026-07-25 12:01:29', '2026-07-26 12:37:39', NULL),
(39, '332fe280-6169-4f5b-a05e-eebba5463b63.webp', 1, NULL, '2026-07-26 12:39:38', '2026-07-26 12:39:38', NULL),
(40, 'd66114d4-e100-4a12-8d8c-0bb4a7049f6e.webp', 1, 39, '2026-07-26 12:40:20', '2026-07-26 12:40:20', NULL),
(41, '6dab6e19-307a-4430-8893-ee345793668d.webp', 1, 39, '2026-07-26 12:41:09', '2026-07-26 12:41:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_sliders`
--

CREATE TABLE `category_sliders` (
  `id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_sliders`
--

INSERT INTO `category_sliders` (`id`, `image`, `category_id`, `created_at`, `updated_at`) VALUES
(53, '23e62bc0-4a18-4a3f-a022-c1d70666d6d7.webp', 38, '2026-07-25 12:02:34', '2026-07-26 12:38:06'),
(54, 'b4c5f1b2-fd68-4d05-bcf4-8d4bc645b564.webp', 38, '2026-07-25 12:02:41', '2026-07-26 12:38:11'),
(55, '63448c90-65bc-4b6c-8281-79f6bed8ced2.webp', 38, '2026-07-25 12:02:48', '2026-07-26 12:38:18'),
(56, 'f76f8177-ed93-4757-b24f-0170691f6d56.webp', 38, '2026-07-25 12:02:56', '2026-07-26 12:38:33'),
(60, '9a407914-84aa-4762-9f8a-f83ed61d8b5b.webp', 19, '2026-07-25 12:05:55', '2026-07-25 12:05:55'),
(61, 'a754f746-dba7-4fff-9f3b-f1d8f35c2a08.webp', 19, '2026-07-25 12:06:06', '2026-07-25 12:06:06'),
(62, '6806d932-8da0-4c96-a6a2-457d24773b37.webp', 19, '2026-07-25 12:06:15', '2026-07-25 12:06:15'),
(63, 'ce6e37a4-39d7-4303-b435-45d41c476da6.webp', 16, '2026-07-25 12:07:16', '2026-07-25 12:07:16'),
(64, '2c296f94-e29a-47a1-a7f8-7574a1537968.webp', 16, '2026-07-25 12:07:26', '2026-07-25 12:07:26'),
(65, '665020b0-bdbf-496e-9324-3cf5cf43182c.webp', 16, '2026-07-25 12:07:48', '2026-07-25 12:07:48'),
(66, 'eef72f08-674a-4929-a785-8f1c84ea78e6.webp', 23, '2026-07-25 12:08:38', '2026-07-25 12:08:38'),
(67, 'ae1c3ac7-92ca-4ebf-826f-9d133331ab6b.webp', 23, '2026-07-25 12:08:47', '2026-07-25 12:08:47'),
(68, '37703532-94ca-4241-9437-3b2c00954e6f.webp', 23, '2026-07-25 12:08:54', '2026-07-25 12:08:54'),
(69, 'b1aba970-3766-44f3-8d16-2c7ad0608938.webp', 21, '2026-07-25 12:09:50', '2026-07-25 12:09:50'),
(70, '3865f626-932b-47ad-a28e-eaa5346096d2.webp', 21, '2026-07-25 12:09:57', '2026-07-25 12:09:57'),
(71, '7d1c0d0a-2fdb-4da0-b7ec-7337f9c8d59a.webp', 21, '2026-07-25 12:10:05', '2026-07-25 12:10:05'),
(72, '4a4309a4-676f-4435-ba25-095b92e43178.webp', 21, '2026-07-25 12:10:14', '2026-07-25 12:10:14'),
(73, '90ef05f0-a7dc-48eb-8d2a-f800b07f0a3f.webp', 25, '2026-07-25 12:11:03', '2026-07-25 12:11:03'),
(74, 'b5eb3d63-e6b7-46c5-852a-cba641097fc1.webp', 25, '2026-07-25 12:11:10', '2026-07-25 12:11:10'),
(77, '9a2b1e67-fb79-4510-b080-642bd025402c.webp', 33, '2026-07-25 12:14:06', '2026-07-25 12:14:06'),
(78, '46f25255-22af-4bcd-949f-eb70e3f09eb6.webp', 33, '2026-07-25 12:14:13', '2026-07-25 12:14:13'),
(79, 'fe217d8e-5edb-492d-a6b0-81e5b2cb9a7b.webp', 33, '2026-07-25 12:14:22', '2026-07-25 12:14:22'),
(83, '3ae513d1-42fb-473c-b2cb-c83247c22c30.webp', 32, '2026-07-25 12:16:16', '2026-07-25 12:16:16'),
(84, '833be0db-a843-496f-a677-6473092e8d04.webp', 32, '2026-07-25 12:16:23', '2026-07-25 12:16:23'),
(85, '48f523e4-95bb-4558-ab84-54c37fc1f9ab.webp', 32, '2026-07-25 12:16:30', '2026-07-25 12:16:30'),
(86, '0ad14c8c-56c4-4ad5-be6b-241ca90cf8c4.webp', 39, '2026-07-26 12:41:46', '2026-07-26 12:42:05'),
(87, 'e3827057-ec23-455a-a791-40c3f3939efe.webp', 39, '2026-07-26 12:41:53', '2026-07-26 12:41:53'),
(88, 'f7708256-6af9-4c13-b0a4-e890d840fee8.webp', 39, '2026-07-26 12:42:22', '2026-07-26 12:42:22'),
(89, 'a54d1c1b-6194-48a9-b18f-90aed0398d61.webp', 39, '2026-07-26 12:42:38', '2026-07-26 12:42:38'),
(90, '7cf7faa4-0dd9-4c14-ad14-a195f6609808.webp', 39, '2026-07-26 12:43:03', '2026-07-26 12:43:03'),
(91, '3b6f260c-c368-4fa5-b88e-913b9cab6bf4.webp', 40, '2026-07-26 12:43:15', '2026-07-26 12:43:15'),
(92, '5c0b804a-df5a-4692-bd97-2353f2ddda21.webp', 40, '2026-07-26 12:43:25', '2026-07-26 12:43:25'),
(93, '2653046e-0f0d-477a-a8b3-6e1c79a41c46.webp', 40, '2026-07-26 12:43:40', '2026-07-26 12:43:40'),
(94, '252d0c10-3b36-4612-8ae1-f7a931ae03d4.webp', 40, '2026-07-26 12:43:50', '2026-07-26 12:43:50'),
(95, '48bae0aa-4ade-42ab-8737-2cb07fa30e20.webp', 40, '2026-07-26 12:44:01', '2026-07-26 12:44:01'),
(96, '0d26b2fb-2a81-43bd-a97f-9885a76bb97a.webp', 40, '2026-07-26 12:44:12', '2026-07-26 12:44:12'),
(97, 'f051f92e-3c30-4a0c-aba5-56e9e37978e1.webp', 41, '2026-07-26 12:44:22', '2026-07-26 12:44:22'),
(98, 'db7283f1-e0e8-419d-b78c-501dc3120afa.webp', 41, '2026-07-26 12:44:31', '2026-07-26 12:44:31'),
(99, '4cbb4e5d-a56d-4b85-a4cd-2a34c94c9fd7.webp', 41, '2026-07-26 12:44:41', '2026-07-26 12:44:41'),
(100, '71f5bd05-5112-4c84-a7a2-bf454a6c2abd.webp', 41, '2026-07-26 12:44:49', '2026-07-26 12:44:49'),
(101, '5aed31d4-d3ff-400e-838e-457f61789263.webp', 41, '2026-07-26 12:45:06', '2026-07-26 12:45:06');

-- --------------------------------------------------------

--
-- Table structure for table `category_translations`
--

CREATE TABLE `category_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_translations`
--

INSERT INTO `category_translations` (`id`, `category_id`, `locale`, `name`, `slug`) VALUES
(23, 15, 'ar', 'الخضار والفواكه', 'الخضار-والفواكه'),
(24, 15, 'en', 'Vegetables and fruits', 'vegetables-and-fruits'),
(25, 16, 'ar', 'خضار طازجة', 'خضار-طازجة'),
(26, 16, 'en', 'fresh vegetables', 'fresh-vegetables'),
(27, 17, 'ar', 'فواكه', 'فواكه'),
(28, 17, 'en', 'fruits', 'fruits'),
(29, 18, 'ar', 'أعشاب وخضار ورقية', 'أعشاب-وخضار-ورقية'),
(30, 18, 'en', 'Herbs and leafy vegetables', 'herbs-and-leafy-vegetables'),
(31, 19, 'ar', 'تمور', 'تمور'),
(32, 19, 'en', 'Dates', 'dates'),
(33, 20, 'ar', 'اللحوم والدواجن', 'اللحوم-والدواجن'),
(34, 20, 'en', 'Meat and poultry', 'meat-and-poultry'),
(35, 21, 'ar', 'لحوم طازجة', 'لحوم-طازجة'),
(36, 21, 'en', 'Fresh meat', 'fresh-meat'),
(37, 22, 'ar', 'دجاج', 'دجاج'),
(38, 22, 'en', 'chickens', 'chickens'),
(39, 23, 'ar', 'أسماك ومأكولات بحرية', 'أسماك-ومأكولات-بحرية'),
(40, 23, 'en', 'Fish and seafood', 'fish-and-seafood'),
(41, 24, 'ar', 'المواد الغذائية الأساسية', 'المواد-الغذائية-الأساسية'),
(42, 24, 'en', 'basic food items', 'basic-food-items'),
(43, 25, 'ar', 'أرز', 'أرز'),
(44, 25, 'en', 'rice', 'rice'),
(45, 26, 'ar', 'سكر', 'سكر'),
(46, 26, 'en', 'sugar', 'sugar'),
(49, 28, 'ar', 'معكرونة', 'معكرونة'),
(50, 28, 'en', 'pasta', 'pasta'),
(57, 32, 'ar', 'البهارات والصلصات', 'البهارات-والصلصات'),
(58, 32, 'en', 'Spices and sauces', 'spices-and-sauces'),
(59, 33, 'ar', 'خل وزيوت', 'خل-وزيوت'),
(60, 33, 'en', 'Vinegar and oils', 'vinegar-and-oils'),
(61, 34, 'ar', 'بهارات', 'بهارات'),
(62, 34, 'en', 'spices', 'spices'),
(69, 38, 'ar', 'اجهزة كهربائية', 'اجهزة-كهربائية'),
(70, 38, 'en', 'electrical appliances', 'electrical-appliances'),
(71, 39, 'ar', 'ملابس', 'ملابس'),
(72, 39, 'en', 'clothes', 'clothes'),
(73, 40, 'ar', 'رجالي', 'رجالي'),
(74, 40, 'en', 'Men\'s', 'mens'),
(75, 41, 'ar', 'حريمي', 'حريمي'),
(76, 41, 'en', 'women\'s', 'womens');

-- --------------------------------------------------------

--
-- Table structure for table `contactusmessages`
--

CREATE TABLE `contactusmessages` (
  `id` bigint UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contactusmessages`
--

INSERT INTO `contactusmessages` (`id`, `is_read`, `name`, `mobile`, `email`, `subject`, `details`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0, 'محمد حسنين', '12154412231', 'aaa@gmail.com', 'استاشرة', 'تفاصيل الرسااالة', '2025-12-18 12:12:12', NULL, NULL),
(2, 1, 'سوسن عبد الرحمن', '4444444444444444', 'asad@gmail.com', 'موضوعي', 'تفاصيييييل', '2025-12-18 12:12:17', '2025-12-18 11:12:36', NULL),
(3, 0, 'باجس الدلو', '1478963214', 'aaaa@aaa.aa', 'موضوووع', 'تفاصيييللل', '2026-01-17 13:33:02', '2026-01-17 13:33:02', NULL),
(4, 0, 'باجس الدلو', '1478963214', 'aaaa@aaa.aa', 'موضوووع', 'تفاصيييللل', '2026-07-27 10:30:52', '2026-07-27 10:30:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discount_codes`
--

CREATE TABLE `discount_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percentage` int NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `usage_type` enum('once','multiple','limited') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'multiple',
  `max_usage` int DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discount_codes`
--

INSERT INTO `discount_codes` (`id`, `code`, `discount_percentage`, `start_date`, `end_date`, `usage_type`, `max_usage`, `usage_count`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'YELRJ', 20, '2026-01-05 00:00:00', '2026-01-26 00:00:00', 'limited', 20, 4, 1, NULL, '2026-01-05 08:55:41', '2026-01-20 09:13:14');

-- --------------------------------------------------------

--
-- Table structure for table `discount_code_usages`
--

CREATE TABLE `discount_code_usages` (
  `id` bigint UNSIGNED NOT NULL,
  `discount_code_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `discount_percentage` int NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_reviews`
--

CREATE TABLE `driver_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `driver_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint NOT NULL COMMENT '1-5 Stars',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `imagesstore`
--

CREATE TABLE `imagesstore` (
  `id` bigint UNSIGNED NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` decimal(20,2) UNSIGNED NOT NULL,
  `imageable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imageable_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `imagesstore`
--

INSERT INTO `imagesstore` (`id`, `filename`, `extension`, `size`, `imageable_type`, `imageable_id`, `created_at`, `updated_at`) VALUES
(1, 'ab171561-6f4f-4343-bb3b-c1ec243516a4.webp', 'webp', 72604.00, 'App\\Models\\Product', 1, '2026-01-05 12:03:03', '2026-01-05 12:03:03'),
(3, '86c748e9-c27a-4b46-853b-028a61bf6041.webp', 'webp', 118852.00, 'App\\Models\\Product', 1, '2026-01-05 12:03:05', '2026-01-05 12:03:05'),
(4, '1766c98c-fe38-43e5-8baf-89fa3df6a048.webp', 'webp', 41904.00, 'App\\Models\\Product', 1, '2026-01-05 12:10:13', '2026-01-05 12:10:13'),
(5, '62aa76e9-3994-43ef-8289-5a6a78c47ec4.webp', 'webp', 23263.00, 'App\\Models\\Product', 3, '2026-01-06 15:39:01', '2026-01-06 15:39:01'),
(6, 'fdfc8937-1391-4b19-9e92-1efaad11b588.webp', 'webp', 27045.00, 'App\\Models\\Product', 3, '2026-01-06 15:39:02', '2026-01-06 15:39:02'),
(7, '8d8c648d-4adb-4d4d-b939-bac1afbcb35b.webp', 'webp', 13571.00, 'App\\Models\\Product', 4, '2026-01-06 15:41:56', '2026-01-06 15:41:56'),
(8, 'c4f06a34-58b0-4358-a4d9-efd3631fe21e.webp', 'webp', 16871.00, 'App\\Models\\Product', 4, '2026-01-06 15:41:56', '2026-01-06 15:41:56'),
(9, '5e77551f-025c-493c-bec1-0b484b6df6a7.webp', 'webp', 14046.00, 'App\\Models\\Product', 4, '2026-01-06 15:41:58', '2026-01-06 15:41:58'),
(10, '85b0d1e3-4e8a-429a-86ba-a8f6f5dc5196.webp', 'webp', 35712.00, 'App\\Models\\Product', 4, '2026-01-06 15:42:00', '2026-01-06 15:42:00'),
(11, '209a357d-4ea2-4a3a-80ed-2a2aba8a1166.webp', 'webp', 16015.00, 'App\\Models\\Product', 5, '2026-01-06 15:53:13', '2026-01-06 15:53:13'),
(12, '4c132faa-5fa3-40b1-bee4-217cd00ea70b.webp', 'webp', 16535.00, 'App\\Models\\Product', 5, '2026-01-06 15:53:14', '2026-01-06 15:53:14'),
(13, '9375ba21-eabc-48a7-9b95-0cbe8f2bc5d9.webp', 'webp', 22671.00, 'App\\Models\\Product', 5, '2026-01-06 15:53:16', '2026-01-06 15:53:16'),
(14, '395dc7ad-d6dc-4327-8fb4-228e1b309133.webp', 'webp', 26585.00, 'App\\Models\\Product', 6, '2026-01-06 15:58:05', '2026-01-06 15:58:05'),
(15, '3bb1eb68-4219-4c81-a5a3-c1e09360d8eb.webp', 'webp', 23862.00, 'App\\Models\\Product', 6, '2026-01-06 15:58:06', '2026-01-06 15:58:06'),
(16, 'e8e216aa-213c-412f-9047-0de7ce5b28cd.webp', 'webp', 16456.00, 'App\\Models\\Product', 6, '2026-01-06 15:58:06', '2026-01-06 15:58:06'),
(17, '9e34b226-c1d5-4189-9098-51f7ee39fd74.webp', 'webp', 43481.00, 'App\\Models\\Product', 6, '2026-01-06 15:58:07', '2026-01-06 15:58:07'),
(18, '06f8b9e9-da30-4c2b-b4ec-3611e5f7638f.webp', 'webp', 42809.00, 'App\\Models\\Product', 6, '2026-01-06 15:58:14', '2026-01-06 15:58:14'),
(19, 'e6f0618e-8f90-4fdd-b909-536fd5a666a2.webp', 'webp', 36826.00, 'App\\Models\\Product', 6, '2026-01-06 15:58:15', '2026-01-06 15:58:15'),
(20, 'e83d40e2-c859-40a4-bf0f-21727d15d079.webp', 'webp', 15900.00, 'App\\Models\\Product', 7, '2026-01-06 16:05:22', '2026-01-06 16:05:22'),
(21, 'b2dd0d34-bb3e-450d-acef-61efeb0944f5.webp', 'webp', 14582.00, 'App\\Models\\Product', 7, '2026-01-06 16:05:23', '2026-01-06 16:05:23'),
(22, '8aa38788-c981-4b61-9306-84fa90621cf8.webp', 'webp', 15751.00, 'App\\Models\\Product', 7, '2026-01-06 16:05:23', '2026-01-06 16:05:23'),
(23, '1c36be24-66fe-4edb-94a3-aab6198d0a1a.webp', 'webp', 14634.00, 'App\\Models\\Product', 7, '2026-01-06 16:05:24', '2026-01-06 16:05:24'),
(24, 'de500f32-f3fa-454b-b2a9-e4a44af6aa31.webp', 'webp', 18470.00, 'App\\Models\\Product', 7, '2026-01-06 16:05:24', '2026-01-06 16:05:24'),
(25, '9cf52580-1549-448e-937f-f842054c1ff8.webp', 'webp', 17550.00, 'App\\Models\\Product', 7, '2026-01-06 16:05:25', '2026-01-06 16:05:25'),
(26, '82ca0044-a136-4be5-8b01-f46d75376e04.webp', 'webp', 15771.00, 'App\\Models\\Product', 8, '2026-01-06 16:07:36', '2026-01-06 16:07:36'),
(27, '3f3fa2be-8be6-4066-a192-194e00ee1874.webp', 'webp', 17517.00, 'App\\Models\\Product', 8, '2026-01-06 16:07:37', '2026-01-06 16:07:37'),
(28, '533c3d28-b71c-4f3b-8d5d-787822dadcd9.webp', 'webp', 47222.00, 'App\\Models\\Product', 9, '2026-01-06 16:09:58', '2026-01-06 16:09:58'),
(29, '3a9ecf3b-60d9-45e8-9e10-558706020ef4.webp', 'webp', 88536.00, 'App\\Models\\Product', 9, '2026-01-06 16:09:58', '2026-01-06 16:09:58'),
(30, '544968c4-8b05-4a26-9fad-18a764753f94.webp', 'webp', 88426.00, 'App\\Models\\Product', 9, '2026-01-06 16:09:59', '2026-01-06 16:09:59'),
(31, '485ce4dd-20e9-4186-a20e-8568b0a90c1d.webp', 'webp', 88426.00, 'App\\Models\\Product', 9, '2026-01-06 16:10:00', '2026-01-06 16:10:00'),
(32, '92a2e7b5-3ea0-4b49-b448-0530f4123c23.webp', 'webp', 73936.00, 'App\\Models\\Product', 9, '2026-01-06 16:10:01', '2026-01-06 16:10:01'),
(33, 'c8f2a222-29bb-4217-a637-b08b9cb4836c.webp', 'webp', 94090.00, 'App\\Models\\Product', 9, '2026-01-06 16:10:03', '2026-01-06 16:10:03'),
(34, '1e71712e-e598-4193-a411-6e30d4cf69bb.webp', 'webp', 18960.00, 'App\\Models\\Product', 10, '2026-01-06 16:14:22', '2026-01-06 16:14:22'),
(35, '6b575da4-3ba4-4686-bee9-f8f65524eee5.webp', 'webp', 17024.00, 'App\\Models\\Product', 10, '2026-01-06 16:14:22', '2026-01-06 16:14:22'),
(36, 'deeb9e22-b735-495c-ae69-1b37664b66b9.webp', 'webp', 17867.00, 'App\\Models\\Product', 10, '2026-01-06 16:14:23', '2026-01-06 16:14:23'),
(37, 'a874f149-76c9-4f06-88fc-baf58f3b38d4.webp', 'webp', 16909.00, 'App\\Models\\Product', 10, '2026-01-06 16:14:24', '2026-01-06 16:14:24'),
(38, '1633a0b0-18dc-4727-b01b-536fb1c76563.webp', 'webp', 41813.00, 'App\\Models\\Product', 10, '2026-01-06 16:14:25', '2026-01-06 16:14:25'),
(39, '8a1b36f1-d1ef-4c2f-b869-e1aa54c75df4.webp', 'webp', 34706.00, 'App\\Models\\Product', 11, '2026-01-06 16:16:55', '2026-01-06 16:16:55'),
(40, '074ef86f-0281-4325-b2e0-b14df8626afa.webp', 'webp', 26842.00, 'App\\Models\\Product', 11, '2026-01-06 16:16:56', '2026-01-06 16:16:56'),
(41, 'ff474cf8-c47f-4af3-b875-d29edd6a7209.webp', 'webp', 28393.00, 'App\\Models\\Product', 11, '2026-01-06 16:16:57', '2026-01-06 16:16:57'),
(42, 'fb9a0138-b466-4fdf-9298-cc300166e2ab.webp', 'webp', 48272.00, 'App\\Models\\Product', 12, '2026-01-06 16:19:40', '2026-01-06 16:19:40'),
(43, 'a0683546-c464-4678-b85b-c71b81cf3436.webp', 'webp', 49457.00, 'App\\Models\\Product', 12, '2026-01-06 16:19:41', '2026-01-06 16:19:41'),
(44, '1e39d740-7229-4d11-bdd2-1a7cc473992e.webp', 'webp', 67346.00, 'App\\Models\\Product', 12, '2026-01-06 16:19:42', '2026-01-06 16:19:42'),
(45, '5b232291-3e39-4c5e-8fd2-77ee5189672a.webp', 'webp', 51473.00, 'App\\Models\\Product', 12, '2026-01-06 16:19:43', '2026-01-06 16:19:43'),
(46, 'e53afcf6-f54e-4d27-aa91-ed8b68a1446e.webp', 'webp', 43295.00, 'App\\Models\\Product', 12, '2026-01-06 16:19:44', '2026-01-06 16:19:44'),
(47, '00229238-ceed-4cef-87b4-5d14e8424e69.webp', 'webp', 28507.00, 'App\\Models\\Product', 13, '2026-01-06 16:22:27', '2026-01-06 16:22:27'),
(48, '1e59f124-a36c-45f9-824d-66fee4a0fdfb.webp', 'webp', 38814.00, 'App\\Models\\Product', 13, '2026-01-06 16:22:27', '2026-01-06 16:22:27'),
(49, '2925b0b5-257c-4957-8328-44c0cf7a1b60.webp', 'webp', 45538.00, 'App\\Models\\Product', 13, '2026-01-06 16:22:28', '2026-01-06 16:22:28'),
(50, '34b737a1-95cb-4e9e-a845-0575ac433b0b.webp', 'webp', 65433.00, 'App\\Models\\Product', 14, '2026-01-06 16:23:57', '2026-01-06 16:23:57'),
(51, '84829c0e-bd9c-44fa-92d4-ab6895ae0a61.webp', 'webp', 73271.00, 'App\\Models\\Product', 15, '2026-01-06 16:26:52', '2026-01-06 16:26:52'),
(52, '145ed1c6-483e-453e-98e1-f60e89861226.webp', 'webp', 26805.00, 'App\\Models\\Product', 15, '2026-01-06 16:26:53', '2026-01-06 16:26:53'),
(53, '3705a516-c0a6-47c7-ad4e-52cf5a570ff7.webp', 'webp', 39569.00, 'App\\Models\\Product', 15, '2026-01-06 16:26:53', '2026-01-06 16:26:53'),
(54, '7fa4100e-187b-441f-bbc1-7022f3d13512.webp', 'webp', 34331.00, 'App\\Models\\Product', 15, '2026-01-06 16:26:54', '2026-01-06 16:26:54'),
(55, '76f045a4-a00b-4e1f-8cc2-3c098dfdd4ff.webp', 'webp', 38030.00, 'App\\Models\\Product', 15, '2026-01-06 16:26:55', '2026-01-06 16:26:55'),
(56, '56dabdd1-5cc9-4d16-aa2c-0a886d62a27e.webp', 'webp', 45809.00, 'App\\Models\\Product', 15, '2026-01-06 16:26:56', '2026-01-06 16:26:56'),
(57, 'b530cbdb-3278-437b-abb3-cb4ae7c57024.webp', 'webp', 21589.00, 'App\\Models\\Product', 16, '2026-01-06 16:30:06', '2026-01-06 16:30:06'),
(58, '53201e12-29ee-48c2-8d14-cbd2356a1cc9.webp', 'webp', 61788.00, 'App\\Models\\Product', 16, '2026-01-06 16:30:07', '2026-01-06 16:30:07'),
(59, '321efdb8-7b7a-4494-8aad-c435bb6f77fc.webp', 'webp', 27823.00, 'App\\Models\\Product', 16, '2026-01-06 16:30:08', '2026-01-06 16:30:08'),
(60, '22ffc1a6-893f-4fa1-a49c-09f9773a02cb.webp', 'webp', 26131.00, 'App\\Models\\Product', 16, '2026-01-06 16:30:09', '2026-01-06 16:30:09'),
(61, 'cb7ca41e-f1c4-47eb-b0a6-410bdd3a7b62.webp', 'webp', 25601.00, 'App\\Models\\Product', 16, '2026-01-06 16:30:09', '2026-01-06 16:30:09'),
(62, '81a2d8ad-f080-4805-9edc-c8c9cb47b777.webp', 'webp', 185762.00, 'App\\Models\\Product', 17, '2026-01-06 16:32:34', '2026-01-06 16:32:34'),
(63, 'c9d2f6dc-46d4-42f8-9519-884814f4ec67.webp', 'webp', 145623.00, 'App\\Models\\Product', 17, '2026-01-06 16:32:35', '2026-01-06 16:32:35'),
(64, '869ecc4b-f32a-4d8f-8c19-db252b5771fb.webp', 'webp', 90739.00, 'App\\Models\\Product', 17, '2026-01-06 16:32:36', '2026-01-06 16:32:36'),
(65, 'e32b634b-5e56-492e-9923-9c4b63172aaa.webp', 'webp', 195318.00, 'App\\Models\\Product', 17, '2026-01-06 16:32:38', '2026-01-06 16:32:38'),
(66, 'defd8fce-0c62-45d6-a8e6-d432017f417c.webp', 'webp', 82192.00, 'App\\Models\\Product', 18, '2026-01-06 16:35:22', '2026-01-06 16:35:22'),
(67, '2d0854d4-3ab0-498e-bad2-21b81b24b681.webp', 'webp', 64493.00, 'App\\Models\\Product', 18, '2026-01-06 16:35:22', '2026-01-06 16:35:22'),
(68, 'c4cd4a8e-2038-4ccf-bd2d-93c8c3850729.webp', 'webp', 55756.00, 'App\\Models\\Product', 18, '2026-01-06 16:35:23', '2026-01-06 16:35:23'),
(69, '09287377-26be-4f4d-b6d6-a85e12994ae7.webp', 'webp', 64909.00, 'App\\Models\\Product', 18, '2026-01-06 16:35:24', '2026-01-06 16:35:24'),
(70, '17413a67-f853-4643-be0f-c7fe5ac44067.webp', 'webp', 95325.00, 'App\\Models\\Product', 18, '2026-01-06 16:35:25', '2026-01-06 16:35:25'),
(71, '557b2b62-9a48-4fce-a0e2-e90c7612808f.webp', 'webp', 49528.00, 'App\\Models\\Product', 18, '2026-01-06 16:35:26', '2026-01-06 16:35:26'),
(72, 'ae0588f8-f280-4c1a-8b49-eff3c08a319b.webp', 'webp', 21086.00, 'App\\Models\\Product', 19, '2026-01-06 16:41:48', '2026-01-06 16:41:48'),
(73, '5f38fb9c-6d6f-446e-be71-951fc763cb89.webp', 'webp', 41136.00, 'App\\Models\\Product', 19, '2026-01-06 16:41:49', '2026-01-06 16:41:49'),
(74, '5a17de45-89bd-4eea-a62f-859b4aaa56eb.webp', 'webp', 25677.00, 'App\\Models\\Product', 19, '2026-01-06 16:41:49', '2026-01-06 16:41:49'),
(75, '07a75229-5333-4b1e-ac94-49db7a023de0.webp', 'webp', 24343.00, 'App\\Models\\Product', 19, '2026-01-06 16:41:50', '2026-01-06 16:41:50'),
(76, '2d9883fe-e762-46e6-a402-140db6022ee3.webp', 'webp', 33040.00, 'App\\Models\\Product', 20, '2026-01-06 16:43:35', '2026-01-06 16:43:35'),
(77, '142ac0cf-71da-44a4-954b-725b008c5427.webp', 'webp', 42146.00, 'App\\Models\\Product', 20, '2026-01-06 16:43:36', '2026-01-06 16:43:36'),
(78, '4cee2221-33f3-4e37-b3bd-1deb6ec82ed1.webp', 'webp', 77963.00, 'App\\Models\\Product', 20, '2026-01-06 16:43:36', '2026-01-06 16:43:36'),
(79, 'e6f06863-9a09-4cfe-8e21-f04622f30b6c.webp', 'webp', 39353.00, 'App\\Models\\Product', 21, '2026-01-06 16:45:10', '2026-01-06 16:45:10'),
(80, 'eec811b7-d77f-4020-8747-a5e8f5f9827e.webp', 'webp', 36153.00, 'App\\Models\\Product', 22, '2026-01-06 16:47:06', '2026-01-06 16:47:06'),
(81, 'c38eb941-48cb-40ae-abba-8ba9425d8020.webp', 'webp', 43430.00, 'App\\Models\\Product', 22, '2026-01-06 16:47:07', '2026-01-06 16:47:07'),
(82, '631ead5d-04b1-450d-b6d0-13daf071cbc6.webp', 'webp', 43514.00, 'App\\Models\\Product', 22, '2026-01-06 16:47:08', '2026-01-06 16:47:08'),
(83, 'c20f40a9-7bc6-4c34-8f49-e2fc7044014f.webp', 'webp', 62073.00, 'App\\Models\\Product', 22, '2026-01-06 16:47:09', '2026-01-06 16:47:09'),
(84, 'd65625d9-8540-4e81-a306-69aa377a080c.webp', 'webp', 131878.00, 'App\\Models\\Product', 22, '2026-01-18 12:11:57', '2026-01-18 12:11:57'),
(85, '8816944c-6824-4271-a8cc-6f42c411852b.webp', 'webp', 720770.00, 'App\\Models\\Product', 23, '2026-04-26 09:35:29', '2026-04-26 09:35:29'),
(86, 'e8dde44b-f1e5-4ea8-90c2-ee54c1520d5e.webp', 'webp', 740586.00, 'App\\Models\\Product', 23, '2026-04-26 09:35:30', '2026-04-26 09:35:30'),
(87, '50e5732d-5924-4787-a25b-14946d76ebac.webp', 'webp', 100478.00, 'App\\Models\\Product', 24, '2026-04-26 09:37:49', '2026-04-26 09:37:49'),
(88, 'fc60f6ba-409e-4472-9cb0-2b64d6288286.webp', 'webp', 62878.00, 'App\\Models\\Product', 25, '2026-04-26 09:42:24', '2026-04-26 09:42:24'),
(89, '58bd752a-591e-4f49-bb40-cef5fc7cbdd9.webp', 'webp', 339594.00, 'App\\Models\\Product', 26, '2026-04-26 09:44:31', '2026-04-26 09:44:31'),
(90, '9647990a-e48e-4277-ac14-50f463e9a73b.webp', 'webp', 73268.00, 'App\\Models\\Product', 26, '2026-04-26 09:44:32', '2026-04-26 09:44:32'),
(91, '65782b82-dda3-4e4a-83a6-1b533edc1952.webp', 'webp', 339594.00, 'App\\Models\\Product', 26, '2026-04-26 09:44:34', '2026-04-26 09:44:34'),
(92, 'c62cadcd-97ac-45aa-a171-60577881e97a.webp', 'webp', 547214.00, 'App\\Models\\Product', 26, '2026-04-26 09:44:36', '2026-04-26 09:44:36'),
(93, 'e0cc3bcf-3f30-4483-a529-fb5a74c25c65.webp', 'webp', 1040324.00, 'App\\Models\\Product', 26, '2026-04-26 09:44:37', '2026-04-26 09:44:37'),
(94, '40b7e14d-8211-4f9f-ab0d-59ab66e2237b.webp', 'webp', 72190.00, 'App\\Models\\Product', 27, '2026-04-26 09:46:24', '2026-04-26 09:46:24'),
(95, '8a75121f-c0b8-43ee-b8e4-728989a972c5.webp', 'webp', 152106.00, 'App\\Models\\Product', 28, '2026-04-26 09:48:08', '2026-04-26 09:48:08'),
(96, 'af9f75a0-f456-4c8e-8826-df50cb348830.webp', 'webp', 116950.00, 'App\\Models\\Product', 29, '2026-04-26 09:49:52', '2026-04-26 09:49:52'),
(97, '439c9d28-ad45-492f-9a3a-5c1d92b4f53a.webp', 'webp', 65598.00, 'App\\Models\\Product', 30, '2026-04-26 09:51:46', '2026-04-26 09:51:46'),
(98, 'e2f37838-0612-4bc9-ae0c-a24441a36577.webp', 'webp', 556432.00, 'App\\Models\\Product', 30, '2026-04-26 09:51:47', '2026-04-26 09:51:47'),
(99, '0404048a-2c62-4937-8b59-f9a67b5ed125.webp', 'webp', 591778.00, 'App\\Models\\Product', 30, '2026-04-26 09:51:48', '2026-04-26 09:51:48'),
(100, 'a1dc1d5f-e45f-4c2f-942d-70d4a355beff.webp', 'webp', 57258.00, 'App\\Models\\Product', 31, '2026-04-26 09:55:00', '2026-04-26 09:55:00'),
(101, '25204e6c-c4a8-4c79-9080-f999e5515aca.webp', 'webp', 38894.00, 'App\\Models\\Product', 31, '2026-04-26 09:55:01', '2026-04-26 09:55:01'),
(102, 'a5af4bbb-3b5d-4efd-a89e-034480b9afc5.webp', 'webp', 69154.00, 'App\\Models\\Product', 32, '2026-04-26 09:57:06', '2026-04-26 09:57:06'),
(103, 'b977f570-7f13-474e-90a4-a50555debfe9.webp', 'webp', 72822.00, 'App\\Models\\Product', 32, '2026-04-26 09:57:08', '2026-04-26 09:57:08'),
(104, '48b3c0ea-1800-440d-a932-18ceb6741e50.webp', 'webp', 96506.00, 'App\\Models\\Product', 33, '2026-04-26 09:59:55', '2026-04-26 09:59:55'),
(105, '54bde6da-1b61-46df-a43e-3d81d3d51515.webp', 'webp', 109682.00, 'App\\Models\\Product', 33, '2026-04-26 09:59:56', '2026-04-26 09:59:56'),
(106, 'f981f237-ade3-419d-915f-f29fdb7cf835.webp', 'webp', 1083820.00, 'App\\Models\\Product', 33, '2026-04-26 09:59:57', '2026-04-26 09:59:57'),
(107, '53c88923-4ecc-48e7-acec-233f0f44cb5f.webp', 'webp', 26962.00, 'App\\Models\\Product', 34, '2026-04-26 10:02:03', '2026-04-26 10:02:03'),
(108, '5711d5f4-6830-4025-a45a-4babe7390795.webp', 'webp', 21304.00, 'App\\Models\\Product', 34, '2026-04-26 10:02:04', '2026-04-26 10:02:04'),
(109, '478238c4-f704-4f53-aaed-c9f04966cad1.webp', 'webp', 87734.00, 'App\\Models\\Product', 35, '2026-04-26 10:23:53', '2026-04-26 10:23:53'),
(110, '7ca2316a-11a7-4124-b15d-d2c65c1a2b29.webp', 'webp', 88216.00, 'App\\Models\\Product', 35, '2026-04-26 10:23:54', '2026-04-26 10:23:54'),
(111, 'b675c4a5-648c-41a4-8454-4e6e22194e34.webp', 'webp', 151057.00, 'App\\Models\\Product', 35, '2026-04-26 10:23:55', '2026-04-26 10:23:55'),
(112, '556d3ebd-4fb3-4cce-8811-4bc7aba1494b.webp', 'webp', 94921.00, 'App\\Models\\Product', 36, '2026-04-26 10:25:57', '2026-04-26 10:25:57'),
(113, '3574aeb7-4fbf-4675-9ec3-9585f4882e85.webp', 'webp', 127892.00, 'App\\Models\\Product', 37, '2026-04-26 10:27:53', '2026-04-26 10:27:53'),
(114, 'ff41b187-a2ef-441f-8ee5-7bea66a69407.webp', 'webp', 74894.00, 'App\\Models\\Product', 38, '2026-04-26 10:32:42', '2026-04-26 10:32:42'),
(115, '67c92581-be38-4e7f-894a-3fe8f5b74afe.webp', 'webp', 38976.00, 'App\\Models\\Product', 39, '2026-04-26 10:34:01', '2026-04-26 10:34:01'),
(116, '3f07ac69-5574-4dbb-8be3-b5b5e6d0306a.webp', 'webp', 124439.00, 'App\\Models\\Product', 40, '2026-04-26 10:36:52', '2026-04-26 10:36:52'),
(117, '6ad5dc68-ed3a-40b7-bc2e-e568c85ca8a4.webp', 'webp', 73124.00, 'App\\Models\\Product', 40, '2026-04-26 10:36:53', '2026-04-26 10:36:53'),
(118, 'dfe54e37-530b-4b21-b476-0ef996394c19.webp', 'webp', 152106.00, 'App\\Models\\Product', 41, '2026-04-26 10:38:48', '2026-04-26 10:38:48'),
(119, 'c1c1bb26-e465-41ae-b42e-526b52a63052.webp', 'webp', 60028.00, 'App\\Models\\Product', 42, '2026-04-26 10:42:02', '2026-04-26 10:42:02'),
(120, 'c83b74a1-ae27-40d1-a943-8e7052c1cab9.webp', 'webp', 75928.00, 'App\\Models\\Product', 45, '2026-07-25 12:27:59', '2026-07-25 12:27:59'),
(121, '321f93fb-685e-490b-af2a-5f6b8a77d0b6.webp', 'webp', 36652.00, 'App\\Models\\Product', 45, '2026-07-25 12:28:00', '2026-07-25 12:28:00'),
(122, '16ecca16-85b6-4d48-a04d-98eef1cecced.webp', 'webp', 28680.00, 'App\\Models\\Product', 45, '2026-07-25 12:28:02', '2026-07-25 12:28:02'),
(123, '93f77c3d-caeb-4c46-b1a9-6285114a7a9b.webp', 'webp', 75928.00, 'App\\Models\\Product', 45, '2026-07-25 12:28:05', '2026-07-25 12:28:05'),
(124, '53895391-b8de-445a-b97b-898a3905fc06.webp', 'webp', 28883.00, 'App\\Models\\Product', 47, '2026-07-25 12:33:05', '2026-07-25 12:33:05'),
(125, '0d768ca5-78fd-4371-8621-93b8aac2eb18.webp', 'webp', 35311.00, 'App\\Models\\Product', 47, '2026-07-25 12:33:06', '2026-07-25 12:33:06'),
(126, '4ee259f4-23a2-4300-8f84-ce2d20a4bba1.webp', 'webp', 25962.00, 'App\\Models\\Product', 47, '2026-07-25 12:33:08', '2026-07-25 12:33:08'),
(127, '0f7e2810-1fb8-4064-8812-03e196f1527c.webp', 'webp', 43567.00, 'App\\Models\\Product', 48, '2026-07-25 12:35:13', '2026-07-25 12:35:13'),
(128, '704af451-2fc9-4087-a0ca-8cd495c86a35.webp', 'webp', 25190.00, 'App\\Models\\Product', 48, '2026-07-25 12:35:14', '2026-07-25 12:35:14'),
(129, '038a118c-cdea-4ca7-8a12-0fabe7c9891f.webp', 'webp', 17470.00, 'App\\Models\\Product', 48, '2026-07-25 12:35:14', '2026-07-25 12:35:14'),
(130, '40531178-422a-4d30-8038-ff1556befc4a.webp', 'webp', 45162.00, 'App\\Models\\Product', 48, '2026-07-25 12:35:16', '2026-07-25 12:35:16'),
(131, 'a0bc75bb-a3ef-4547-b4f4-a5917119f49f.webp', 'webp', 94960.00, 'App\\Models\\Product', 49, '2026-07-26 11:19:39', '2026-07-26 11:19:39'),
(132, '0b3d5671-fb51-4438-9af8-868c4f1d1fa1.webp', 'webp', 23545.00, 'App\\Models\\Product', 49, '2026-07-26 11:19:40', '2026-07-26 11:19:40'),
(133, '79ab1784-486a-4011-bb20-88183c004355.webp', 'webp', 20550.00, 'App\\Models\\Product', 49, '2026-07-26 11:19:41', '2026-07-26 11:19:41'),
(134, '1a493431-11a6-4307-a717-5e08e7a9d412.webp', 'webp', 124736.00, 'App\\Models\\Product', 50, '2026-07-26 11:22:18', '2026-07-26 11:22:18'),
(135, '1e22f897-e465-430a-b36c-d7e75e675105.webp', 'webp', 16521.00, 'App\\Models\\Product', 50, '2026-07-26 11:22:18', '2026-07-26 11:22:18'),
(136, '4fec19bf-8b7a-44d8-b692-47ca8c082fec.webp', 'webp', 78500.00, 'App\\Models\\Product', 50, '2026-07-26 11:22:20', '2026-07-26 11:22:20'),
(137, '917bfdb8-4ab2-4cbd-8013-6f6d946b7a6c.webp', 'webp', 49648.00, 'App\\Models\\Product', 52, '2026-07-26 11:25:04', '2026-07-26 11:25:04'),
(138, '3a68186c-85e7-4ca3-a84e-c39993a0aaff.webp', 'webp', 213468.00, 'App\\Models\\Product', 52, '2026-07-26 11:25:06', '2026-07-26 11:25:06'),
(139, '78265ae0-ab86-41c3-b56c-629087f6bf8d.webp', 'webp', 111668.00, 'App\\Models\\Product', 53, '2026-07-26 11:28:06', '2026-07-26 11:28:06'),
(140, 'd6441ec9-5505-4d48-a50f-e62162cfd53e.webp', 'webp', 494375.00, 'App\\Models\\Product', 53, '2026-07-26 11:28:08', '2026-07-26 11:28:08'),
(141, '7fb12a8b-3239-470d-a983-816d3b24ea8d.webp', 'webp', 30302.00, 'App\\Models\\Product', 55, '2026-07-26 11:31:32', '2026-07-26 11:31:32'),
(142, 'c85b37c4-9524-4a30-a413-884df0975995.webp', 'webp', 267928.00, 'App\\Models\\Product', 55, '2026-07-26 11:31:34', '2026-07-26 11:31:34'),
(143, '4c3c8c3b-d052-4f6f-b22e-df17b3b14a0e.webp', 'webp', 52782.00, 'App\\Models\\Product', 55, '2026-07-26 11:31:35', '2026-07-26 11:31:35'),
(144, 'fdcef1e5-bc98-4f90-a2a2-79cbb2a1b5fb.webp', 'webp', 17976.00, 'App\\Models\\Product', 56, '2026-07-26 11:33:10', '2026-07-26 11:33:10'),
(145, 'd566a743-b47b-4d97-bb3d-285b098856ac.webp', 'webp', 9801.00, 'App\\Models\\Product', 56, '2026-07-26 11:33:11', '2026-07-26 11:33:11'),
(146, 'd94fc709-ecb1-4e92-ba66-61874e91224f.webp', 'webp', 43864.00, 'App\\Models\\Product', 58, '2026-07-26 11:36:17', '2026-07-26 11:36:17'),
(147, 'af0637d4-0b3e-4f45-af01-8ed247e93f06.webp', 'webp', 46038.00, 'App\\Models\\Product', 58, '2026-07-26 11:36:18', '2026-07-26 11:36:18'),
(148, 'e0db3df1-19ce-407f-81b0-4d23a719f874.webp', 'webp', 18933.00, 'App\\Models\\Product', 58, '2026-07-26 11:36:19', '2026-07-26 11:36:19'),
(149, 'd0f3b059-6ad5-40c3-8001-2c6d36bb4b22.webp', 'webp', 12454.00, 'App\\Models\\Product', 58, '2026-07-26 11:36:21', '2026-07-26 11:36:21'),
(150, '67b6a330-3932-44a1-8f26-d3fc11139fd3.webp', 'webp', 102784.00, 'App\\Models\\Product', 59, '2026-07-26 11:38:13', '2026-07-26 11:38:13'),
(151, '21a148e4-8ae4-48c5-a69a-153e98ce74b3.webp', 'webp', 13906.00, 'App\\Models\\Product', 59, '2026-07-26 11:38:14', '2026-07-26 11:38:14'),
(152, 'df7e6597-e0ee-4fbb-8c8b-6b21a088a362.webp', 'webp', 10614.00, 'App\\Models\\Product', 59, '2026-07-26 11:38:15', '2026-07-26 11:38:15'),
(153, '2dc73ce7-d82b-4c27-887f-99aa3f8fccc8.webp', 'webp', 3915.00, 'App\\Models\\Product', 61, '2026-07-26 11:41:21', '2026-07-26 11:41:21'),
(154, '694dd66a-4062-4160-9afd-0138540bdfdb.webp', 'webp', 72729.00, 'App\\Models\\Product', 61, '2026-07-26 11:41:23', '2026-07-26 11:41:23'),
(155, '4fb350a0-f185-4a65-81a1-3718781eaf32.webp', 'webp', 156844.00, 'App\\Models\\Product', 62, '2026-07-26 11:43:03', '2026-07-26 11:43:03'),
(156, '64fe02b4-54c8-4f29-8a9f-7edce1e75dac.webp', 'webp', 36606.00, 'App\\Models\\Product', 62, '2026-07-26 11:43:05', '2026-07-26 11:43:05'),
(157, 'bd89a8f4-d29b-475d-87b2-f5da318537d8.webp', 'webp', 22848.00, 'App\\Models\\Product', 62, '2026-07-26 11:43:07', '2026-07-26 11:43:07'),
(158, '06e24280-610f-4c95-9a34-16b7787ae255.webp', 'webp', 44960.00, 'App\\Models\\Product', 62, '2026-07-26 11:43:11', '2026-07-26 11:43:11'),
(159, 'a0616c10-307e-4abe-ba7e-b1a928e132f0.webp', 'webp', 106209.00, 'App\\Models\\Product', 62, '2026-07-26 11:43:16', '2026-07-26 11:43:16'),
(160, 'bc92a52c-dfe3-437c-98a1-1bf4f5f27d49.webp', 'webp', 78786.00, 'App\\Models\\Product', 63, '2026-07-26 11:44:40', '2026-07-26 11:44:40'),
(161, 'bcda7f13-4e25-4240-8b39-de299d9b757a.webp', 'webp', 24568.00, 'App\\Models\\Product', 63, '2026-07-26 11:44:41', '2026-07-26 11:44:41'),
(162, 'd376a573-7703-495b-9a36-1c143e7fd14c.webp', 'webp', 26317.00, 'App\\Models\\Product', 65, '2026-07-26 11:48:09', '2026-07-26 11:48:09'),
(163, 'ff92242a-293c-4754-bb52-c6bec1e3e997.webp', 'webp', 57777.00, 'App\\Models\\Product', 65, '2026-07-26 11:48:10', '2026-07-26 11:48:10'),
(164, '61b6c09e-194c-4dd4-99a0-a59c23acc810.webp', 'webp', 66138.00, 'App\\Models\\Product', 66, '2026-07-26 11:49:47', '2026-07-26 11:49:47'),
(165, '123b760e-6dc8-451b-ade5-c05cb99f7295.webp', 'webp', 11718.00, 'App\\Models\\Product', 66, '2026-07-26 11:49:48', '2026-07-26 11:49:48'),
(166, 'd6b81594-aaf3-470d-8e34-9784569c67f7.webp', 'webp', 103144.00, 'App\\Models\\Product', 67, '2026-07-26 11:52:15', '2026-07-26 11:52:15'),
(167, 'bdba39de-3016-4704-831d-e4944ef7a56e.webp', 'webp', 9771.00, 'App\\Models\\Product', 67, '2026-07-26 11:52:16', '2026-07-26 11:52:16'),
(168, '6c13e4ab-aae3-4f9d-a2f3-2e2318b216b0.webp', 'webp', 55294.00, 'App\\Models\\Product', 67, '2026-07-26 11:52:17', '2026-07-26 11:52:17'),
(169, '3d473169-0bc3-4fd6-b283-9490c89ea4a9.webp', 'webp', 32096.00, 'App\\Models\\Product', 67, '2026-07-26 11:52:17', '2026-07-26 11:52:17'),
(170, 'e748e208-4a8e-4e24-86a8-080d91cc57eb.webp', 'webp', 53828.00, 'App\\Models\\Product', 67, '2026-07-26 11:52:20', '2026-07-26 11:52:20'),
(171, '5c2881bc-9713-4df2-bebb-61626fb9688c.webp', 'webp', 89470.00, 'App\\Models\\Product', 73, '2026-07-26 12:06:37', '2026-07-26 12:06:37'),
(172, 'd6322dab-2f78-4495-9522-f1f1fdedb79b.webp', 'webp', 35380.00, 'App\\Models\\Product', 73, '2026-07-26 12:06:38', '2026-07-26 12:06:38'),
(173, 'ed2d0046-139b-43eb-a966-ac7cf9fbb8e8.webp', 'webp', 47258.00, 'App\\Models\\Product', 73, '2026-07-26 12:06:39', '2026-07-26 12:06:39'),
(174, 'eb124452-9dfa-48f5-9eae-5c7c19a2a300.webp', 'webp', 106724.00, 'App\\Models\\Product', 73, '2026-07-26 12:06:42', '2026-07-26 12:06:42'),
(175, '88bfd420-a331-4bbe-b7d2-6cff6ceed40b.webp', 'webp', 153114.00, 'App\\Models\\Product', 73, '2026-07-26 12:06:43', '2026-07-26 12:06:43'),
(176, '70fee5e4-30de-4819-954f-ab6166d97e21.webp', 'webp', 50552.00, 'App\\Models\\Product', 74, '2026-07-26 12:09:27', '2026-07-26 12:09:27'),
(177, 'd59d26b2-41b1-4c9f-bec0-fdf1075674e5.webp', 'webp', 48358.00, 'App\\Models\\Product', 74, '2026-07-26 12:09:28', '2026-07-26 12:09:28'),
(178, 'fd7d2898-0cc3-44df-910a-84f75d911029.webp', 'webp', 53582.00, 'App\\Models\\Product', 74, '2026-07-26 12:09:29', '2026-07-26 12:09:29'),
(179, '79285708-b138-4fac-a467-c1dd90f35cf3.webp', 'webp', 63708.00, 'App\\Models\\Product', 74, '2026-07-26 12:09:31', '2026-07-26 12:09:31'),
(180, '46daf8ef-1870-4568-b5f2-6a693e71692d.webp', 'webp', 36611.00, 'App\\Models\\Product', 77, '2026-07-26 12:16:25', '2026-07-26 12:16:25'),
(181, '46e84c33-4bec-4de9-8cd1-b3c45815fe29.webp', 'webp', 10193.00, 'App\\Models\\Product', 77, '2026-07-26 12:16:26', '2026-07-26 12:16:26'),
(182, '52c76021-ab4c-4539-af1d-f145174bf8ba.webp', 'webp', 63786.00, 'App\\Models\\Product', 81, '2026-07-26 12:22:54', '2026-07-26 12:22:54'),
(183, '6e86e4d7-6969-478f-b772-c863f45fea61.webp', 'webp', 21081.00, 'App\\Models\\Product', 81, '2026-07-26 12:22:55', '2026-07-26 12:22:55'),
(184, '8e0c8478-b618-4699-91af-7a88e4b7c879.webp', 'webp', 69324.00, 'App\\Models\\Product', 81, '2026-07-26 12:22:57', '2026-07-26 12:22:57'),
(185, '51ae4427-dfe8-444d-8c3b-3bc97012050f.webp', 'webp', 342406.00, 'App\\Models\\Product', 83, '2026-07-26 12:26:35', '2026-07-26 12:26:35'),
(186, '575732bb-996a-44e4-8304-3250fd8b7f15.webp', 'webp', 106711.00, 'App\\Models\\Product', 86, '2026-07-26 12:49:40', '2026-07-26 12:49:40'),
(187, '7c1f3c57-81f8-4512-a71d-875c195a2b9a.webp', 'webp', 111075.00, 'App\\Models\\Product', 86, '2026-07-26 12:49:41', '2026-07-26 12:49:41'),
(188, '6d7c2962-1f7c-4f35-982e-27fc542a59bb.webp', 'webp', 18420.00, 'App\\Models\\Product', 86, '2026-07-26 12:49:55', '2026-07-26 12:49:55'),
(189, 'a41f2634-3249-493c-869a-4ff7ccb32ec1.webp', 'webp', 49666.00, 'App\\Models\\Product', 86, '2026-07-26 12:49:56', '2026-07-26 12:49:56'),
(190, '368e0e64-a3c1-446e-8b68-4f8adc11097e.webp', 'webp', 76895.00, 'App\\Models\\Product', 86, '2026-07-26 12:49:57', '2026-07-26 12:49:57'),
(191, '9212fd99-e4a0-4460-8774-1ce4127331d8.webp', 'webp', 53201.00, 'App\\Models\\Product', 86, '2026-07-26 12:49:59', '2026-07-26 12:49:59'),
(192, 'e9a1eafd-0814-457a-b0c5-6d10a71678f3.webp', 'webp', 29093.00, 'App\\Models\\Product', 87, '2026-07-26 12:53:10', '2026-07-26 12:53:10'),
(193, '382a1836-99e3-40e8-915b-8fba731896b3.webp', 'webp', 34814.00, 'App\\Models\\Product', 87, '2026-07-26 12:53:11', '2026-07-26 12:53:11'),
(194, 'b8537a24-f720-4983-94fd-841deabccfe4.webp', 'webp', 46074.00, 'App\\Models\\Product', 87, '2026-07-26 12:53:12', '2026-07-26 12:53:12'),
(195, '34a1fac8-4bc6-4925-b3fe-a1ce3fd6fa01.webp', 'webp', 18389.00, 'App\\Models\\Product', 87, '2026-07-26 12:53:13', '2026-07-26 12:53:13'),
(196, 'f8230a7e-9147-4f3a-82fc-7e9be7b3e9c2.webp', 'webp', 60770.00, 'App\\Models\\Product', 87, '2026-07-26 12:53:16', '2026-07-26 12:53:16'),
(197, 'f839ca2a-0fb3-4766-b4eb-76a980ec7432.webp', 'webp', 41966.00, 'App\\Models\\Product', 87, '2026-07-26 12:53:17', '2026-07-26 12:53:17'),
(198, '75624882-1be7-476e-a4da-779594aaa594.webp', 'webp', 93836.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:44', '2026-07-26 12:58:44'),
(199, '58588abc-4f6e-4edf-856a-46dc6a0395ae.webp', 'webp', 127621.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:46', '2026-07-26 12:58:46'),
(200, 'd30acfe4-f19c-4584-b955-ae9f81b3a5bc.webp', 'webp', 188322.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:48', '2026-07-26 12:58:48'),
(201, 'f2c9e7ba-08f7-43f9-a5c8-ca3db1c1892b.webp', 'webp', 237336.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:49', '2026-07-26 12:58:49'),
(202, '3de4ee8b-8a9e-4876-be56-831ec3339529.webp', 'webp', 279182.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:51', '2026-07-26 12:58:51'),
(203, 'd079b854-4144-48b1-a0dc-b0ca86c36db2.webp', 'webp', 175990.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:54', '2026-07-26 12:58:54'),
(204, '3410d3e6-2d9e-450d-86da-346da29751d8.webp', 'webp', 304211.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:57', '2026-07-26 12:58:57'),
(205, '32bd81d1-37d8-41cd-9cab-9e0cbaa9688b.webp', 'webp', 179310.00, 'App\\Models\\Product', 88, '2026-07-26 12:58:58', '2026-07-26 12:58:58'),
(206, 'a9052132-6189-4c36-929f-1049b12837be.webp', 'webp', 510557.00, 'App\\Models\\Product', 88, '2026-07-26 12:59:00', '2026-07-26 12:59:00'),
(207, '51fb1aa4-a28e-49bc-8009-f5e28ee94c7c.webp', 'webp', 20317.00, 'App\\Models\\Product', 89, '2026-07-26 15:56:30', '2026-07-26 15:56:30'),
(208, 'd275d27c-ae5d-47f1-b189-ab79d9970b91.webp', 'webp', 19619.00, 'App\\Models\\Product', 89, '2026-07-26 15:56:32', '2026-07-26 15:56:32'),
(209, 'fd77b023-8536-4901-99de-9a450fa1a5f8.webp', 'webp', 23193.00, 'App\\Models\\Product', 89, '2026-07-26 15:56:34', '2026-07-26 15:56:34'),
(210, 'dfd920d4-8334-4ffd-abb8-415233fce34f.webp', 'webp', 25442.00, 'App\\Models\\Product', 89, '2026-07-26 15:56:36', '2026-07-26 15:56:36'),
(211, 'c47774ce-252a-4883-a3b9-2f6a04f2783e.webp', 'webp', 49413.00, 'App\\Models\\Product', 89, '2026-07-26 15:56:39', '2026-07-26 15:56:39'),
(212, 'fffaa8e8-ed5f-4da4-8829-823aeeb4ba9b.webp', 'webp', 64861.00, 'App\\Models\\Product', 89, '2026-07-26 15:56:45', '2026-07-26 15:56:45'),
(213, 'eb618dfb-8499-46f7-be29-841985001a5d.webp', 'webp', 46632.00, 'App\\Models\\Product', 90, '2026-07-26 16:03:09', '2026-07-26 16:03:09'),
(214, '26674044-349c-4d2d-8320-f4a72cdb3efd.webp', 'webp', 54549.00, 'App\\Models\\Product', 90, '2026-07-26 16:03:11', '2026-07-26 16:03:11'),
(215, '5ab9a97b-d83f-47e2-ad32-fd566811eb34.webp', 'webp', 50465.00, 'App\\Models\\Product', 90, '2026-07-26 16:03:12', '2026-07-26 16:03:12'),
(216, '306f4769-2135-49a6-a25c-d7d60f0bc769.webp', 'webp', 49696.00, 'App\\Models\\Product', 90, '2026-07-26 16:03:17', '2026-07-26 16:03:17'),
(217, '8f485eed-0ac6-4695-bd02-d628c94fb147.webp', 'webp', 111555.00, 'App\\Models\\Product', 90, '2026-07-26 16:03:20', '2026-07-26 16:03:20'),
(218, '1bade7ab-90b6-483e-9ba5-c021c8371f09.webp', 'webp', 83373.00, 'App\\Models\\Product', 90, '2026-07-26 16:03:27', '2026-07-26 16:03:27'),
(219, 'f6cbf304-57f5-477d-8adc-7099de1904f2.webp', 'webp', 41259.00, 'App\\Models\\Product', 91, '2026-07-26 16:07:02', '2026-07-26 16:07:02'),
(220, '50285439-cf20-4e84-b61a-85a8c91d1f4e.webp', 'webp', 57673.00, 'App\\Models\\Product', 91, '2026-07-26 16:07:04', '2026-07-26 16:07:04'),
(221, '6d126d7f-a20e-450d-b903-fc6a66c52cec.webp', 'webp', 107517.00, 'App\\Models\\Product', 91, '2026-07-26 16:07:07', '2026-07-26 16:07:07'),
(222, '08957b3b-4a17-4f4f-8707-3d799446567f.webp', 'webp', 69955.00, 'App\\Models\\Product', 91, '2026-07-26 16:07:09', '2026-07-26 16:07:09'),
(223, '452f86c3-731d-4210-8f29-a34379ef8036.webp', 'webp', 52276.00, 'App\\Models\\Product', 91, '2026-07-26 16:07:11', '2026-07-26 16:07:11'),
(224, '31fdca6f-05e0-4023-be46-cf515a257f90.webp', 'webp', 50143.00, 'App\\Models\\Product', 91, '2026-07-26 16:07:16', '2026-07-26 16:07:16'),
(225, 'e23065ad-c2bb-4097-a170-c37dac1156b0.webp', 'webp', 31955.00, 'App\\Models\\Product', 92, '2026-07-26 16:13:09', '2026-07-26 16:13:09'),
(226, 'b42951ea-4e9b-48aa-a143-310371b7acc3.webp', 'webp', 38367.00, 'App\\Models\\Product', 92, '2026-07-26 16:13:11', '2026-07-26 16:13:11'),
(227, '908c11bb-1754-4cc9-942b-022d82f6726e.webp', 'webp', 38402.00, 'App\\Models\\Product', 92, '2026-07-26 16:13:12', '2026-07-26 16:13:12'),
(228, '10f1525e-e84d-45f7-bf6c-67048470b40e.webp', 'webp', 39410.00, 'App\\Models\\Product', 92, '2026-07-26 16:13:15', '2026-07-26 16:13:15'),
(229, 'a22ae89f-1216-476b-9be5-b3028d8c67f2.webp', 'webp', 38146.00, 'App\\Models\\Product', 92, '2026-07-26 16:13:18', '2026-07-26 16:13:18'),
(230, 'a1df2eef-8735-4189-b1b5-a3f892f287a1.webp', 'webp', 15438.00, 'App\\Models\\Product', 93, '2026-07-26 16:17:13', '2026-07-26 16:17:13'),
(231, 'dcef5685-236d-4c9f-81a9-78328b3e2cb0.webp', 'webp', 21154.00, 'App\\Models\\Product', 93, '2026-07-26 16:17:15', '2026-07-26 16:17:15'),
(232, 'b9ba1897-cc9d-4fb2-9ead-b2f64f36ee3d.webp', 'webp', 23413.00, 'App\\Models\\Product', 93, '2026-07-26 16:17:18', '2026-07-26 16:17:18'),
(233, '2b874229-6120-4f0b-a5a9-17550a089144.webp', 'webp', 18541.00, 'App\\Models\\Product', 93, '2026-07-26 16:17:20', '2026-07-26 16:17:20'),
(234, '9c3bd4ad-8946-4d43-aaa4-c2f01265651f.webp', 'webp', 24327.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:28', '2026-07-26 16:20:28'),
(235, '107f5f0b-7bd3-41c5-ac81-ec424f35a3ee.webp', 'webp', 37719.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:30', '2026-07-26 16:20:30'),
(236, '6b8a20d7-ec2d-4ef9-b257-6226d60a96ca.webp', 'webp', 41098.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:31', '2026-07-26 16:20:31'),
(237, 'd6e3d1b1-44b5-4b0d-8549-2606f5f644eb.webp', 'webp', 43554.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:33', '2026-07-26 16:20:33'),
(238, '12b14a8e-a707-467e-a984-d74cf081c1fd.webp', 'webp', 34561.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:36', '2026-07-26 16:20:36'),
(239, '3e464467-a11f-4184-8e5c-b2c4d996cf35.webp', 'webp', 60143.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:39', '2026-07-26 16:20:39'),
(240, 'f0b52d9f-bbef-4e19-9db3-ceebf30a3db8.webp', 'webp', 33880.00, 'App\\Models\\Product', 94, '2026-07-26 16:20:42', '2026-07-26 16:20:42');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_point_transactions`
--

CREATE TABLE `loyalty_point_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('earn','redeem','refund','expire','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int NOT NULL,
  `balance_after` int UNSIGNED NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_15_151439_create_permission_tables', 2),
(5, '2025_12_15_153032_add_columns_to_users_table', 3),
(6, '2025_12_17_125219_create_settings_table', 4),
(7, '2025_12_17_125222_create_setting_translations_table', 4),
(8, '2025_12_18_104950_create_socialmedialinks_table', 5),
(9, '2025_12_18_111621_create_onboardings_table', 6),
(10, '2025_12_18_111625_create_onboarding_translations_table', 6),
(12, '2025_12_18_125335_create_textpages_table', 7),
(13, '2025_12_18_125337_create_textpage_translations_table', 7),
(14, '2025_12_18_130612_create_contactusmessages_table', 8),
(15, '2025_12_18_135129_create_slideshows_table', 9),
(16, '2025_12_21_120711_create_brands_table', 10),
(17, '2025_12_21_120713_create_brand_translations_table', 10),
(22, '2025_12_21_124103_create_attributes_table', 11),
(23, '2025_12_21_124502_create_attribute_values_table', 11),
(24, '2025_12_21_124504_create_attribute_value_translations_table', 11),
(25, '2025_12_21_124848_create_attribute_translations_table', 11),
(26, '2026_01_02_185808_create_categories_table', 12),
(27, '2026_01_02_185819_create_category_translations_table', 12),
(28, '2026_01_02_211042_create_category_sliders_table', 13),
(29, '2026_01_05_104708_create_discount_codes_table', 14),
(30, '2026_01_05_104929_create_discount_orders_table', 14),
(31, '2026_01_05_104938_create_discount_code_usages_table', 14),
(32, '2026_01_05_105814_create_products_table', 15),
(33, '2026_01_05_105815_create_product_translations_table', 15),
(34, '2026_01_05_135131_create_images_store_table', 16),
(35, '2026_01_05_142941_create_attribute_value_product_table', 17),
(36, '2026_01_13_121102_create_notifications_table', 18),
(37, '2026_01_13_122058_create_orderstatuses_table', 19),
(38, '2026_01_13_122100_create_orderstatus_translations_table', 19),
(43, '2026_01_13_123539_create_order_cancel_reasons_table', 20),
(44, '2026_01_13_123541_create_order_cancel_reason_translations_table', 20),
(45, '2026_01_13_124234_create_orders_table', 20),
(46, '2026_01_13_125530_create_order_items_table', 20),
(47, '2026_01_13_125850_create_order_item_attribute_values_table', 20),
(48, '2026_01_13_142845_create_order_reviews_table', 21),
(49, '2026_01_13_150820_create_wallets_table', 22),
(50, '2026_01_13_150821_create_wallet_transactions_table', 22),
(51, '2026_01_17_134402_add_delivery_at_to_orders_table', 23),
(52, '2026_01_17_135528_create_wish_lists_table', 24),
(57, '2026_01_17_142734_create_personal_access_tokens_table', 26),
(61, '2026_07_20_132442_create_otp_codes_table', 27),
(62, '2026_07_21_152609_add_is_variation_to_attributes_table', 28),
(64, '2026_07_21_152835_create_product_variants_table', 29),
(65, '2026_07_21_152842_create_product_variant_attribute_value_table', 29),
(66, '2026_07_22_120000_add_pricing_columns_to_attribute_value_product_table', 30),
(67, '2026_07_22_120001_add_combination_key_to_product_variants_table', 30),
(68, '2026_07_22_120002_add_variant_to_order_items_table', 30),
(69, '2026_07_22_120003_add_price_to_order_item_attribute_values_table', 30),
(70, '2026_07_22_180534_add_delivery_columns_to_orders_table', 31),
(71, '2026_07_23_090000_create_addresses_table', 32),
(72, '2026_07_23_101647_add_status_timestamps_to_orders_table', 33),
(73, '2026_07_23_142953_add_loyalty_points_balance_to_users_table', 34),
(74, '2026_07_23_143255_create_loyalty_point_transactions', 35),
(75, '2026_07_23_143546_add_loyalty_points_columns_to_orders', 36),
(76, '2026_07_23_200000_add_driver_delivery_fields_to_orders_table', 37),
(77, '2026_07_25_080204_create_driver_reviews_table', 38),
(78, '2026_07_25_090000_create_payment_methods_table', 39),
(79, '2026_07_25_100000_create_branches_table', 40),
(80, '2026_07_25_100050_create_branch_translations_table', 41),
(81, '2026_07_25_100100_add_branch_id_to_orders_table', 42),
(83, '2026_07_25_100300_create_branch_product_table', 44),
(84, '2026_07_25_100200_add_branch_id_to_staff_tables', 45),
(85, '2026_07_25_153955_create_units_table', 46),
(86, '2026_07_25_153957_create_unit_translations_table', 47),
(87, '2026_07_26_111644_add_unit_id_products_table', 48),
(88, '2026_07_26_121602_change_quantity_to_decimal_table', 49),
(89, '2026_07_26_122151_add_track_quantity_to_products', 50),
(90, '2026_07_27_200849_fix_discount_codes_is_active', 51),
(91, '2026_07_29_111546_create_admin_notifications_table', 52);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(2, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 16),
(5, 'App\\Models\\User', 16),
(6, 'App\\Models\\User', 16),
(19, 'App\\Models\\User', 16),
(20, 'App\\Models\\User', 16),
(2, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 21),
(20, 'App\\Models\\User', 21),
(21, 'App\\Models\\User', 21),
(22, 'App\\Models\\User', 21);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 16),
(2, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 26),
(3, 'App\\Models\\User', 27),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29),
(3, 'App\\Models\\User', 30),
(3, 'App\\Models\\User', 31),
(7, 'App\\Models\\User', 32),
(3, 'App\\Models\\User', 33);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `name`, `details`, `user_id`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(10, 'السلام عليكم', 'صلي على رسول الله', 33, 1, '2026-07-28 12:41:49', '2026-07-28 12:41:18', '2026-07-28 12:41:49');

-- --------------------------------------------------------

--
-- Table structure for table `onboardings`
--

CREATE TABLE `onboardings` (
  `id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `onboardings`
--

INSERT INTO `onboardings` (`id`, `image`, `created_at`, `updated_at`) VALUES
(2, 'b47069ba-02bf-456b-8082-b4b46b87df1a.webp', '2025-12-18 10:28:46', '2025-12-18 10:28:46'),
(3, '56ab3d7a-e408-43cc-981c-58e0721bf074.webp', '2025-12-18 10:29:32', '2025-12-18 10:29:32'),
(4, '8d1431f0-5d9b-4cf6-bf24-f9c09a0a756a.webp', '2025-12-18 10:30:16', '2025-12-18 10:30:16');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding_translations`
--

CREATE TABLE `onboarding_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `onboarding_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `onboarding_translations`
--

INSERT INTO `onboarding_translations` (`id`, `onboarding_id`, `locale`, `name`, `slug`, `details`, `created_at`, `updated_at`) VALUES
(3, 2, 'ar', 'اكتشف الموضة', 'اكتشف-الموضة', 'تصفح آلاف المنتجات العصرية واعثر على أسلوبك المثالي', NULL, NULL),
(4, 2, 'en', 'Discover Fashion', 'discover-fashion', 'Browse thousands of trendy products and find your perfect style', NULL, NULL),
(5, 3, 'ar', 'تسوق بسهولة', 'تسوق-بسهولة', 'أضف المنتجات إلى السلة وأدر المفضلات والدفع بسهولة', NULL, NULL),
(6, 3, 'en', 'Easy Shopping', 'easy-shopping', 'Add items to cart, manage favorites, and checkout with ease', NULL, NULL),
(7, 4, 'ar', 'توصيل سريع', 'توصيل-سريع', 'احصل على طلباتك بسرعة وتتبعها في الوقت الفعلي', NULL, NULL),
(8, 4, 'en', 'Fast Delivery', 'fast-delivery', 'Get your orders delivered quickly and track them in real-time', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `address_id` bigint UNSIGNED DEFAULT NULL,
  `total_items` int UNSIGNED NOT NULL,
  `total_price` decimal(18,4) UNSIGNED NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `final_price` decimal(18,4) UNSIGNED NOT NULL,
  `status_id` bigint UNSIGNED NOT NULL,
  `prepared_at` timestamp NULL DEFAULT NULL,
  `out_for_delivery_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `earned_points` int UNSIGNED NOT NULL DEFAULT '0',
  `redeemed_points` int UNSIGNED NOT NULL DEFAULT '0',
  `redeemed_points_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `points_awarded_at` timestamp NULL DEFAULT NULL,
  `coupon_id` bigint UNSIGNED DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SAR',
  `note` text COLLATE utf8mb4_unicode_ci,
  `delivery_type` enum('delivery','pickup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `delivery_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `distance` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('cash','online','wallet') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `added_tax` decimal(5,2) DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancel_reason_id` bigint UNSIGNED DEFAULT NULL,
  `cancel_other_reason` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` time DEFAULT NULL,
  `driver_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_pin` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_collected` tinyint(1) NOT NULL DEFAULT '0',
  `driver_note` text COLLATE utf8mb4_unicode_ci,
  `coupon_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderstatuses`
--

CREATE TABLE `orderstatuses` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orderstatuses`
--

INSERT INTO `orderstatuses` (`id`, `created_at`, `updated_at`) VALUES
(1, '2026-01-13 10:27:27', '2026-01-13 10:27:27'),
(2, '2026-01-13 10:27:41', '2026-01-13 10:27:41'),
(3, '2026-01-13 10:28:03', '2026-01-13 10:28:03'),
(4, '2026-01-13 10:28:18', '2026-01-13 10:28:18'),
(5, '2026-01-13 10:28:30', '2026-01-13 10:28:30'),
(6, '2026-07-23 10:15:43', '2026-07-23 10:15:43'),
(7, '2026-07-28 16:08:44', '2026-07-28 16:08:44');

-- --------------------------------------------------------

--
-- Table structure for table `orderstatus_translations`
--

CREATE TABLE `orderstatus_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `orderstatus_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orderstatus_translations`
--

INSERT INTO `orderstatus_translations` (`id`, `orderstatus_id`, `locale`, `name`) VALUES
(1, 1, 'ar', 'طلب جديد'),
(2, 1, 'en', 'New order'),
(3, 2, 'ar', 'قيد التجهيز'),
(4, 2, 'en', 'Processing'),
(5, 3, 'ar', 'تم التجهيز'),
(6, 3, 'en', 'Processed'),
(7, 4, 'ar', 'قيد التوصيل'),
(8, 4, 'en', 'Out for Delivery'),
(9, 5, 'ar', 'تم التسليم'),
(10, 5, 'en', 'delivered'),
(11, 6, 'ar', 'ملغي'),
(12, 6, 'en', 'cancelled'),
(13, 7, 'ar', 'بانتظار الدفع'),
(14, 7, 'en', 'Payment pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_cancel_reasons`
--

CREATE TABLE `order_cancel_reasons` (
  `id` bigint UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_cancel_reasons`
--

INSERT INTO `order_cancel_reasons` (`id`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-01-13 11:17:24', '2026-01-13 11:17:24', NULL),
(2, 1, '2026-01-13 11:17:44', '2026-01-13 11:17:44', NULL),
(3, 1, '2026-01-13 11:18:03', '2026-01-13 11:18:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_cancel_reason_translations`
--

CREATE TABLE `order_cancel_reason_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `order_cancel_reason_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_cancel_reason_translations`
--

INSERT INTO `order_cancel_reason_translations` (`id`, `order_cancel_reason_id`, `locale`, `name`) VALUES
(1, 1, 'ar', 'نسيت استخدام كوبون الخصم'),
(2, 1, 'en', 'I forgot to use the discount coupon'),
(3, 2, 'ar', 'حدث ظرف طارئ ولم استطيع استلام الطلب'),
(4, 2, 'en', 'An emergency occurred and I was unable to receive ...'),
(5, 3, 'ar', 'سبب آخر'),
(6, 3, 'en', 'Another reason');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED DEFAULT NULL,
  `variant_label_snapshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_attribute_values`
--

CREATE TABLE `order_item_attribute_values` (
  `id` bigint UNSIGNED NOT NULL,
  `order_item_id` bigint UNSIGNED NOT NULL,
  `attribute_id` bigint UNSIGNED NOT NULL,
  `attribute_value_id` bigint UNSIGNED NOT NULL,
  `attribute_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `color_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_reviews`
--

CREATE TABLE `order_reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint NOT NULL COMMENT '1-5 Stars',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `verifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verifiable_id` bigint UNSIGNED NOT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `expires_at` timestamp NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `verifiable_type`, `verifiable_id`, `purpose`, `method`, `contact`, `code`, `is_used`, `expires_at`, `attempts`, `created_at`, `updated_at`) VALUES
(7, 'App\\Models\\User', 33, 'registration', 'sms', '112233445566', '1111', 1, '2026-07-27 10:22:26', 0, '2026-07-27 10:12:26', '2026-07-27 10:12:46'),
(9, 'App\\Models\\User', 32, 'login', 'sms', '2588522580', '1111', 1, '2026-07-27 10:33:31', 0, '2026-07-27 10:23:31', '2026-07-27 10:24:46'),
(10, 'App\\Models\\User', 33, 'login', 'sms', '112233445566', '1111', 1, '2026-07-28 11:34:41', 0, '2026-07-28 11:24:41', '2026-07-28 11:25:25');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `mode` enum('test','live') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'test',
  `config` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `code`, `is_enabled`, `mode`, `config`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'hyperpay', 1, 'test', 'eyJpdiI6InJxU3liL0tUSWpxaEJPVWhWOFZpTkE9PSIsInZhbHVlIjoiZ2pRZEJnTWNiVWtieUQ1SHJKQUFPbFVZTFk1Z2V3NnV0NlExcUZab1pSeG1PTG5EbXVWNUdMbjBBcGVTNVl1SEhOc0NLWHN1T2NpUm91WUhEM2FhOHM3Y0pRUjNYN2pqYkRXbFQ0ZjZtb3ltNWo1M2xNbWowaXF1SEZHMTJ3bTQxdzVGSjNhWGhLblc1NFVrKy9hS2xWTjFwZDd5Znc0SDdLRlFTY1FDWFgwV01EWTlxekdwR2JnbDEzU1VCYmt2L0MrUmlLQmV5YWgraWhxc0RheUIrUldxQU1NbGN4eE1WY2FpMXBlakZ1WT0iLCJtYWMiOiI1NWIxYmE5Mzk4YmZlMjQwMDU4NjM5MGFjZDAxZmI2MWQ2MTI0ODdkZTE5MjJhOGQxOTQ1MDYzMTY2MzQ5N2RjIiwidGFnIjoiIn0=', 0, '2026-07-25 09:20:35', '2026-07-28 15:51:48'),
(2, 'paytabs', 1, 'test', 'eyJpdiI6IlVDVXdrTjZJRXpaZUFEWEp5UlZsaFE9PSIsInZhbHVlIjoieUNpWEJRd0pubEl3d29BY1kvL2RVMldtRDdubHc4L251eDNVSHppQ2FnckVzeFg4Q05LUUtwVTJDM0ZsQy9mbVVicmlrTzJub2JmSVhUclJxUWxURWlacmFPL2xCeGdWRUwzczBXTk1nbEZPSkcvbGh0cDdQNElPZXlhblBYZEhQYkpsaUZOdzFPdmVCb3d1WGpZb3dmT1VaVjQ4TVUrM0dsN2x4S01WMlNrPSIsIm1hYyI6ImI2ZmVlNmFhMDM5ZGRlNjBkODliMTIzMWU0ZWUxMWIzMGZiOGQxZjcyNmJmNTcxYWI1OTY1MGQxNzg2YWFjODYiLCJ0YWciOiIifQ==', 1, '2026-07-25 09:20:35', '2026-07-28 15:50:11'),
(3, 'moyasar', 0, 'test', NULL, 2, '2026-07-25 09:20:35', '2026-07-25 09:20:35'),
(4, 'myfatoorah', 1, 'test', 'eyJpdiI6Ii9yV2djK2NhZXdjNGtZVjVvbjkvT3c9PSIsInZhbHVlIjoiQlg0NHVmaitIdm1wYXFkK2Y3U1BtM2tWVHhQZ0lBbG56cUtYQmpQdFNDbXRlNVFtdit1dndRVUJxT1p0ZDU0SGNKS0dnbHRmSFN5WUc5Y2JhVStjVnl4TlBDenNXYXdnMUlNNTYxWEhzd1pHYWlwU1NPYzkwNlZyTnByMGNDUS8iLCJtYWMiOiIwMDNmNTRmNGUwNjJhZWEwMGZiMWZlNzgwYzFkNTdlNjU2ZDIzODYyZjUzYmE0ZDk3ODg5ZThlYjljYzJhZGI0IiwidGFnIjoiIn0=', 3, '2026-07-25 09:20:35', '2026-07-28 12:49:09'),
(5, 'stripe', 0, 'test', NULL, 4, '2026-07-25 09:20:35', '2026-07-25 09:20:35'),
(6, 'paypal', 0, 'test', NULL, 5, '2026-07-25 09:20:35', '2026-07-25 09:20:35'),
(7, 'tabby', 0, 'test', NULL, 6, '2026-07-25 09:20:36', '2026-07-25 09:20:36'),
(8, 'tamara', 0, 'test', NULL, 7, '2026-07-25 09:20:36', '2026-07-25 09:20:36');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Home', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(2, 'General_Settings', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(3, 'Store_Settings', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(4, 'Store_policy_settings', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(5, 'Social_Media', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(6, 'Cancel_Reason_Order', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(7, 'Onboardings', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(8, 'Clients', 'admin', '2026-01-14 12:25:09', '2026-01-14 12:25:09'),
(9, 'Orders', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(10, 'Products', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(11, 'Categories', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(12, 'Brands', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(13, 'Product_Features', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(14, 'Offers and discounts', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(15, 'The_Sales', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(16, 'Products_purchased', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(17, 'Alerts', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(18, 'TextPages', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(19, 'Slideshow', 'admin', '2026-01-14 12:25:10', '2026-01-14 12:25:10'),
(20, 'Contact_Us', 'admin', '2026-01-14 12:25:11', '2026-01-14 12:25:11'),
(21, 'Working_days_and_times', 'admin', '2026-01-17 10:34:24', '2026-01-14 12:25:11'),
(22, 'Delivery_Settings', 'admin', '2026-01-17 10:34:24', '2026-01-14 12:25:11'),
(23, 'Drivers', 'admin', '2026-07-22 15:13:55', '2026-07-22 15:13:55'),
(24, 'Payment_Methods', 'admin', '2026-07-25 09:18:13', '2026-07-25 09:18:13'),
(25, 'Units', 'admin', '2026-07-25 16:26:43', '2026-07-25 16:26:43'),
(26, 'Verification_method_settings', 'admin', '2026-07-26 08:36:46', '2026-07-26 08:36:46'),
(27, 'Loyalty_Points_Settings', 'admin', '2026-07-26 08:36:46', '2026-07-26 08:36:46');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(12, 'App\\Models\\User', 31, 'ايمن محفوظAuth-Token', '73633c4848441bb4a19f4f66a6f72fbfc91cc2ac922dab1ce5e703d629c75654', '[\"*\"]', NULL, NULL, '2026-07-20 18:57:34', '2026-07-20 18:57:34'),
(14, 'App\\Models\\User', 32, 'حسام ابو حصيرةAuth-Token', '5b129a4eb15f3416ff4328e4e7c3c3c28977219f2729ef9e8bc4f2b54151c906', '[\"*\"]', '2026-07-27 10:27:06', NULL, '2026-07-27 10:24:46', '2026-07-27 10:27:06'),
(15, 'App\\Models\\User', 33, 'امير محمد حسنينAuth-Token', '3204b48d2235ec44184430e6d07f3512b960ab0568a4776826168547fc68c114', '[\"*\"]', '2026-07-28 12:50:33', NULL, '2026-07-28 11:25:26', '2026-07-28 12:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `netPrice` decimal(18,4) UNSIGNED NOT NULL,
  `salePrice` decimal(18,4) UNSIGNED DEFAULT NULL,
  `salePrice_start` date DEFAULT NULL,
  `salePrice_end` date DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `track_quantity` tinyint(1) NOT NULL DEFAULT '1',
  `unit_id` bigint UNSIGNED DEFAULT NULL,
  `calories` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `new_arrivals` tinyint(1) NOT NULL DEFAULT '0',
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `image`, `netPrice`, `salePrice`, `salePrice_start`, `salePrice_end`, `sku`, `quantity`, `track_quantity`, `unit_id`, `calories`, `is_active`, `new_arrivals`, `brand_id`, `category_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(43, '145b57dc-9bd6-4b43-832f-357846a72789.webp', 100.0000, 20.0000, '2026-07-25', '2026-07-31', '302', 300.00, 1, NULL, '15444', 1, 1, 6, 21, NULL, '2026-07-25 12:19:09', '2026-07-25 12:19:09'),
(44, 'f42ad36e-f891-4649-b565-71169e7ff120.webp', 150.0000, NULL, NULL, NULL, NULL, 300.00, 1, NULL, NULL, 1, 1, NULL, 21, NULL, '2026-07-25 12:23:13', '2026-07-25 12:23:13'),
(45, '8b225e52-35a0-40ae-8651-5e09f62a5ad5.webp', 3000.0000, NULL, NULL, NULL, NULL, 300.00, 1, NULL, '3500', 1, 0, NULL, 21, NULL, '2026-07-25 12:26:21', '2026-07-25 12:26:21'),
(46, '8f9899d6-7995-481e-b91b-6de49c7abb86.webp', 20.0000, NULL, NULL, NULL, NULL, 500.00, 1, NULL, NULL, 1, 1, 2, 16, NULL, '2026-07-25 12:30:42', '2026-07-25 12:30:42'),
(47, 'c98a1d78-14f6-494b-8e12-a65083e83751.webp', 685.0000, NULL, NULL, NULL, NULL, 500.00, 1, NULL, NULL, 1, 1, NULL, 16, NULL, '2026-07-25 12:31:56', '2026-07-25 12:31:56'),
(48, '09416211-09ed-44ff-9691-f7d0532e47a6.webp', 2363.0000, 8.0000, '2026-07-29', '2026-08-31', NULL, NULL, 0, NULL, NULL, 1, 1, 6, 16, NULL, '2026-07-25 12:34:27', '2026-07-26 10:14:06'),
(49, 'dae4b44a-5593-4f2f-9532-6cf8e60dbc0d.webp', 30.0000, NULL, NULL, NULL, NULL, NULL, 0, 2, '333', 1, 1, NULL, 16, NULL, '2026-07-26 11:18:24', '2026-07-26 11:18:24'),
(50, '77d4fcf8-a150-4354-ab91-bc8c1e8bf978.webp', 40.0000, 5.0000, '2026-07-31', '2026-09-27', '333', 400.00, 1, 2, NULL, 1, 1, 4, 16, NULL, '2026-07-26 11:21:48', '2026-07-26 11:21:48'),
(51, 'a11be0e3-0553-4cfa-9aad-364e98614522.webp', 22.0000, NULL, NULL, NULL, NULL, 400.00, 1, 1, NULL, 1, 0, NULL, 16, NULL, '2026-07-26 11:23:24', '2026-07-26 11:23:24'),
(52, '7d9bfd6b-6882-4cf9-9f4f-79b119988c69.webp', 35.0000, NULL, NULL, NULL, NULL, 400.00, 1, 2, '10', 1, 1, 8, 16, NULL, '2026-07-26 11:24:20', '2026-07-26 11:24:20'),
(53, '8daa8e36-6055-4b17-8938-ecdd27ea8844.webp', 69.0000, NULL, NULL, NULL, NULL, 20.00, 1, 6, NULL, 1, 1, NULL, 17, NULL, '2026-07-26 11:27:54', '2026-07-26 11:27:54'),
(54, '0695d350-b271-4f70-ad64-814d375b478f.webp', 80.0000, NULL, NULL, NULL, NULL, NULL, 0, 6, NULL, 1, 1, 6, 17, NULL, '2026-07-26 11:29:11', '2026-07-26 11:29:11'),
(55, 'ca51db71-7999-4aee-baa7-65d7540e825d.webp', 25.0000, 10.0000, '2026-08-01', '2026-11-29', NULL, 400.00, 1, 2, NULL, 1, 1, NULL, 17, NULL, '2026-07-26 11:31:18', '2026-07-26 11:31:18'),
(56, '4c7f6912-5806-48ad-8871-6d2385d325c9.webp', 188.0000, NULL, NULL, NULL, NULL, 40.00, 1, 1, NULL, 1, 0, 7, 17, NULL, '2026-07-26 11:32:58', '2026-07-26 11:32:58'),
(57, 'b79c5a36-2d24-4861-99ef-6b3ed0c37eec.webp', 333.0000, NULL, NULL, NULL, NULL, NULL, 0, 3, '879', 1, 1, NULL, 17, NULL, '2026-07-26 11:34:36', '2026-07-26 11:34:36'),
(58, '3481ecf4-7700-43a6-ab05-427f1234b259.webp', 434.0000, NULL, NULL, NULL, NULL, 400.00, 1, 3, NULL, 1, 1, 6, 17, NULL, '2026-07-26 11:36:04', '2026-07-26 11:36:04'),
(59, 'f15afbc3-1789-4106-bde8-9ac14f751614.webp', 250.0000, NULL, NULL, NULL, '2222', 40.00, 1, 1, NULL, 1, 1, 3, 17, NULL, '2026-07-26 11:37:55', '2026-07-26 11:37:55'),
(60, 'd1b134fd-5707-4276-a1a6-17b6f1472eaa.webp', 32.0000, NULL, NULL, NULL, '222', 33.00, 1, 5, NULL, 1, 1, NULL, 18, NULL, '2026-07-26 11:39:42', '2026-07-26 11:39:42'),
(61, '51b8faeb-b031-4c50-b541-23b4ccd07d58.webp', 30.0000, NULL, NULL, NULL, '33', 40.00, 1, 1, NULL, 1, 0, 7, 18, NULL, '2026-07-26 11:40:42', '2026-07-26 11:40:42'),
(62, '0496c751-05ff-4fb4-aff3-1f68ea6b1aa2.webp', 12.0000, NULL, NULL, NULL, NULL, NULL, 0, 6, NULL, 1, 1, NULL, 18, NULL, '2026-07-26 11:42:09', '2026-07-26 11:42:09'),
(63, '1a93f007-ca9a-4802-9a62-f4263907518b.webp', 432.0000, NULL, NULL, NULL, '333', 234.00, 1, 2, '433', 1, 1, 2, 18, NULL, '2026-07-26 11:44:26', '2026-07-26 11:44:26'),
(64, '26f8878b-fa1f-4180-8cc6-b2125631053a.webp', 15.0000, NULL, NULL, NULL, NULL, 20.00, 1, 6, NULL, 1, 1, NULL, 18, NULL, '2026-07-26 11:45:43', '2026-07-26 11:45:43'),
(65, '0b9c4d54-1813-4892-bea7-db00c5da79ba.webp', 200.0000, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, 19, NULL, '2026-07-26 11:47:57', '2026-07-26 11:47:57'),
(66, '9fdd9e89-ddd3-4bd2-8945-1b4039b5ffe5.webp', 88.0000, NULL, NULL, NULL, NULL, 1000.00, 1, 2, NULL, 1, 1, 3, 19, NULL, '2026-07-26 11:49:35', '2026-07-26 11:49:35'),
(67, '3710d3ad-d16c-4cc5-b12a-7644b5fe908e.webp', 365.0000, NULL, NULL, NULL, NULL, 400.00, 1, 2, '18700', 1, 1, 1, 19, NULL, '2026-07-26 11:52:00', '2026-07-26 11:52:00'),
(68, '9c44df1b-e1d3-4f15-8559-f656c4fe8f3f.webp', 1874.0000, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, 21, NULL, '2026-07-26 11:54:50', '2026-07-26 11:54:50'),
(69, '1ac5a3e8-5b27-44e6-afa1-d355964e0ce5.webp', 50.0000, NULL, NULL, NULL, '22', 800.00, 1, 2, NULL, 1, 0, 7, 21, NULL, '2026-07-26 11:55:59', '2026-07-26 11:55:59'),
(70, '2618f2b7-5818-47c1-8cda-69ee6e236356.webp', 17.5000, NULL, NULL, NULL, NULL, 30.00, 1, 3, NULL, 1, 1, 6, 22, NULL, '2026-07-26 12:00:50', '2026-07-26 12:00:50'),
(71, 'cec3e146-c940-42da-ae3b-9e7656ce2065.webp', 25.0000, NULL, NULL, NULL, NULL, 400.00, 1, NULL, NULL, 1, 1, NULL, 22, NULL, '2026-07-26 12:01:43', '2026-07-26 12:01:43'),
(72, '6043f913-cd96-4f53-9978-991d0372353f.webp', 157.0000, NULL, NULL, NULL, '33', 50.00, 1, 2, NULL, 1, 1, 8, 23, NULL, '2026-07-26 12:03:39', '2026-07-26 12:03:39'),
(73, '7e14af96-85e8-4d56-b076-dc3b7bb6f925.webp', 147.0000, NULL, NULL, NULL, NULL, NULL, 0, 2, NULL, 1, 1, NULL, 23, NULL, '2026-07-26 12:05:19', '2026-07-26 12:05:19'),
(74, '01875dc8-d502-4488-b1bf-1d3e07216019.webp', 855.0000, 12.0000, '2026-08-01', '2026-11-29', NULL, NULL, 0, NULL, '1588', 1, 1, NULL, 23, NULL, '2026-07-26 12:08:31', '2026-07-26 12:08:31'),
(75, 'f1e4a06d-1a57-4607-96e9-c1f751fdadbb.webp', 187.0000, NULL, NULL, NULL, '222', 1000.00, 1, 2, NULL, 1, 1, 6, 25, NULL, '2026-07-26 12:12:36', '2026-07-26 12:12:36'),
(76, '6a22d66a-e16e-43f8-8980-2e0fb9ff2e69.webp', 147.0000, NULL, NULL, NULL, NULL, 500.00, 1, 3, NULL, 1, 0, 8, 25, NULL, '2026-07-26 12:14:06', '2026-07-26 12:14:06'),
(77, 'ce1cf93c-5f6f-4154-bc2a-9ae0f3653085.webp', 10.0000, NULL, NULL, NULL, '333', 3212.00, 1, 2, NULL, 1, 1, 7, 26, NULL, '2026-07-26 12:15:59', '2026-07-26 12:15:59'),
(78, '0a7284b1-4a2e-4ff3-90e9-b83719af9e3e.webp', 25.0000, NULL, NULL, NULL, NULL, 1500.00, 1, 3, NULL, 1, 1, 7, 26, NULL, '2026-07-26 12:17:11', '2026-07-26 12:17:11'),
(79, '17579f5c-2d1d-4bd3-8ae9-50780151f021.webp', 777.0000, 5.0000, '2026-08-01', '2026-08-30', NULL, NULL, 0, NULL, '987', 1, 0, NULL, 26, NULL, '2026-07-26 12:18:15', '2026-07-26 12:18:15'),
(80, '00f9b67d-558f-4dff-9c0c-a9fe1264b441.webp', 15.0000, NULL, NULL, NULL, NULL, 1500.00, 1, 3, NULL, 1, 1, 6, 28, NULL, '2026-07-26 12:21:33', '2026-07-26 12:21:33'),
(81, '40c66f6a-ac6d-4028-b5ae-77ba1e010239.webp', 43.0000, NULL, NULL, NULL, '333', 4342.00, 1, 3, '345234', 1, 1, 7, 28, NULL, '2026-07-26 12:22:26', '2026-07-26 12:22:26'),
(82, 'ec6a4ef1-59c3-400d-adae-c00134396b51.webp', 30.0000, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 1, 7, 33, NULL, '2026-07-26 12:25:20', '2026-07-26 12:25:20'),
(83, 'f33d5640-84c8-47fe-8d69-4d12abda775b.webp', 20.0000, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, 33, NULL, '2026-07-26 12:26:18', '2026-07-26 12:26:18'),
(84, '89ece381-0d19-427c-bba4-504c984a8c85.webp', 28.0000, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, 34, NULL, '2026-07-26 12:27:53', '2026-07-26 12:27:53'),
(85, 'a89759bc-621b-4bdd-b7f8-53f2590d994a.webp', 20.0000, NULL, NULL, NULL, NULL, 450.00, 1, 3, NULL, 1, 1, 7, 34, NULL, '2026-07-26 12:29:20', '2026-07-26 12:29:20'),
(86, '50be997a-3fa1-41bf-85e1-271e4bcad911.webp', 888.0000, 12.0000, '2026-08-09', '2026-10-25', '333', 1000.00, 1, 5, NULL, 1, 1, 8, 38, NULL, '2026-07-26 12:48:13', '2026-07-26 12:48:13'),
(87, '014f87e7-97f1-49f7-a59f-a94e577d6080.webp', 433.0000, NULL, NULL, NULL, '444', 4433.00, 1, 5, NULL, 1, 1, 7, 38, NULL, '2026-07-26 12:52:08', '2026-07-26 12:52:08'),
(88, 'a5a98e28-3961-4344-8eea-08263fe0eecb.webp', 4342.0000, NULL, NULL, NULL, '333', 3333.00, 1, 5, NULL, 1, 1, 8, 38, NULL, '2026-07-26 12:57:29', '2026-07-26 12:57:29'),
(89, '2e7b3704-488f-4139-81e5-4d822f1b9815.webp', 100.0000, 20.0000, '2026-08-31', '2026-10-25', 'AAd', 100.00, 1, 5, NULL, 1, 1, 8, 40, NULL, '2026-07-26 15:54:16', '2026-07-26 15:54:16'),
(90, 'db7bbcfb-c1ca-4168-95a3-75dd72e7ad5a.webp', 220.0000, NULL, NULL, NULL, NULL, 40.00, 1, 5, NULL, 1, 1, NULL, 40, NULL, '2026-07-26 16:01:43', '2026-07-26 16:01:43'),
(91, '356db740-0b24-49c7-925b-eddce9df987e.webp', 100.0000, NULL, NULL, NULL, 'aaa', 200.00, 1, 5, NULL, 1, 1, 7, 40, NULL, '2026-07-26 16:06:07', '2026-07-26 16:06:07'),
(92, '983bff23-82b3-46b8-b900-febc32cf4a28.webp', 120.0000, 22.0000, '2026-07-31', '2026-12-27', NULL, 100.00, 1, 5, NULL, 1, 1, 2, 41, NULL, '2026-07-26 16:12:09', '2026-07-27 13:05:28'),
(93, '188ec329-0a41-4793-b1ff-f647ee0d5ece.webp', 555.0000, NULL, NULL, NULL, NULL, NULL, 0, 5, NULL, 1, 0, NULL, 41, NULL, '2026-07-26 16:16:28', '2026-07-26 16:16:28'),
(94, 'a0d0e146-db25-4e79-974e-3ac39088e70b.webp', 66.0000, NULL, NULL, NULL, NULL, 100.00, 1, 5, NULL, 1, 1, NULL, 41, NULL, '2026-07-26 16:19:04', '2026-07-26 16:19:04');

-- --------------------------------------------------------

--
-- Table structure for table `product_translations`
--

CREATE TABLE `product_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `details` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_translations`
--

INSERT INTO `product_translations` (`id`, `product_id`, `locale`, `name`, `slug`, `description`, `details`, `created_at`, `updated_at`) VALUES
(85, 43, 'ar', 'كبدة دجاج 1ك', 'كبدة-دجاج-1ك', NULL, NULL, NULL, NULL),
(86, 43, 'en', 'Chicken liver 1 kg', 'chicken-liver-1-kg', NULL, NULL, NULL, NULL),
(87, 44, 'ar', 'أجنحة دجاج', 'أجنحة-دجاج', NULL, NULL, NULL, NULL),
(88, 44, 'en', 'Chicken wings', 'chicken-wings', NULL, NULL, NULL, NULL),
(89, 45, 'ar', 'فيليه عجل 1ك طازج', 'فيليه-عجل-1ك-طازج', 'فيليه عجل طازج عالي الجودة، يتم اختياره بعناية ليمنحك طراوة ومذاقًا غنيًا. مناسب للشوي والتحمير وتحضير أشهى الوجبات العائلية. يتم توصيله طازجًا للحفاظ على أفضل نكهة وجودة.', '<p>فيليه عجل طازج عالي الجودة، يتم اختياره بعناية ليمنحك طراوة ومذاقًا غنيًا. مناسب للشوي والتحمير وتحضير أشهى الوجبات العائلية. يتم توصيله طازجًا للحفاظ على أفضل نكهة وجودة.</p>', NULL, NULL),
(90, 45, 'en', 'Fresh veal fillet 1kg', 'fresh-veal-fillet-1kg', 'Premium quality fresh veal fillet, carefully selected for its tenderness and rich flavor. Perfect for grilling, roasting, or preparing delicious family meals. Delivered fresh to maintain the best taste and quality.', '<p>Premium quality fresh veal fillet, carefully selected for its tenderness and rich flavor. Perfect for grilling, roasting, or preparing delicious family meals. Delivered fresh to maintain the best taste and quality.</p>', NULL, NULL),
(91, 46, 'ar', 'بطاطا حلوة', 'بطاطا-حلوة', NULL, NULL, NULL, NULL),
(92, 46, 'en', 'Sweet Potato', 'sweet-potato', NULL, NULL, NULL, NULL),
(93, 47, 'ar', 'كوسة  مقشرة', 'كوسة-مقشرة', NULL, NULL, NULL, NULL),
(94, 47, 'en', 'Markazi Cored Zucchini', 'markazi-cored-zucchini', NULL, NULL, NULL, NULL),
(95, 48, 'ar', 'الليمون الأفريقي', 'الليمون-الأفريقي', NULL, NULL, NULL, NULL),
(96, 48, 'en', 'African Lemon', 'african-lemon', NULL, NULL, NULL, NULL),
(97, 49, 'ar', 'افوكادو اخضر', 'افوكادو-اخضر', NULL, NULL, NULL, NULL),
(98, 49, 'en', 'Green avocado 0.5 kg', 'green-avocado-05-kg', NULL, NULL, NULL, NULL),
(99, 50, 'ar', 'باذنجان', 'باذنجان', NULL, NULL, NULL, NULL),
(100, 50, 'en', 'eggplant', 'eggplant', NULL, NULL, NULL, NULL),
(101, 51, 'ar', 'بروكلي', 'بروكلي', NULL, NULL, NULL, NULL),
(102, 51, 'en', 'broccoli', 'broccoli', NULL, NULL, NULL, NULL),
(103, 52, 'ar', 'بصل', 'بصل', NULL, NULL, NULL, NULL),
(104, 52, 'en', 'onion', 'onion', NULL, NULL, NULL, NULL),
(105, 53, 'ar', 'شمام', 'شمام', NULL, NULL, NULL, NULL),
(106, 53, 'en', 'melon', 'melon', NULL, NULL, NULL, NULL),
(107, 54, 'ar', 'اناناس منجاوي', 'اناناس-منجاوي', NULL, NULL, NULL, NULL),
(108, 54, 'en', 'Mango pineapple', 'mango-pineapple', NULL, NULL, NULL, NULL),
(109, 55, 'ar', 'بطيخ حبة كبير', 'بطيخ-حبة-كبير', NULL, NULL, NULL, NULL),
(110, 55, 'en', 'large watermelon', 'large-watermelon', NULL, NULL, NULL, NULL),
(111, 56, 'ar', 'برتقال ابو صرة اسباني', 'برتقال-ابو-صرة-اسباني', NULL, NULL, NULL, NULL),
(112, 56, 'en', 'Spanish navel oranges', 'spanish-navel-oranges', NULL, NULL, NULL, NULL),
(113, 57, 'ar', 'مشمش علبة 500', 'مشمش-علبة-500', NULL, NULL, NULL, NULL),
(114, 57, 'en', 'Apricots, 500g box', 'apricots-500g-box', NULL, NULL, NULL, NULL),
(115, 58, 'ar', 'بخاري احمر 500جرام', 'بخاري-احمر-500جرام', NULL, NULL, NULL, NULL),
(116, 58, 'en', 'Bukhari red 500 grams', 'bukhari-red-500-grams', NULL, NULL, NULL, NULL),
(117, 59, 'ar', 'موز سكري كرتون 3 كيلو', 'موز-سكري-كرتون-3-كيلو', NULL, NULL, NULL, NULL),
(118, 59, 'en', 'Sweet bananas, 3 kg carton', 'sweet-bananas-3-kg-carton', NULL, NULL, NULL, NULL),
(119, 60, 'ar', 'سبانخ بيبي ايطالي-صحن', 'سبانخ-بيبي-ايطالي-صحن', NULL, NULL, NULL, NULL),
(120, 60, 'en', 'Italian Baby Spinach - Dish', 'italian-baby-spinach-dish', NULL, NULL, NULL, NULL),
(121, 61, 'ar', 'جرجير بيبي ايطالي-صحن', 'جرجير-بيبي-ايطالي-صحن', NULL, NULL, NULL, NULL),
(122, 61, 'en', 'Italian baby rocket - a plate', 'italian-baby-rocket-a-plate', NULL, NULL, NULL, NULL),
(123, 62, 'ar', 'خس لولا بيوندا هولندي-1حبة', 'خس-لولا-بيوندا-هولندي-1حبة', NULL, NULL, NULL, NULL),
(124, 62, 'en', 'Lola Bionda Dutch Lettuce - 1 head', 'lola-bionda-dutch-lettuce-1-head', NULL, NULL, NULL, NULL),
(125, 63, 'ar', 'ملوخــــــــيه', 'ملوخــــــــيه', NULL, NULL, NULL, NULL),
(126, 63, 'en', 'Molokhia', 'molokhia', NULL, NULL, NULL, NULL),
(127, 64, 'ar', 'بصل أخــــضر', 'بصل-أخــــضر', NULL, NULL, NULL, NULL),
(128, 64, 'en', 'green onions', 'green-onions', NULL, NULL, NULL, NULL),
(129, 65, 'ar', 'سكري مكنوز فاخر', 'سكري-مكنوز-فاخر', NULL, NULL, NULL, NULL),
(130, 65, 'en', 'Luxurious, high-quality sugar', 'luxurious-high-quality-sugar', NULL, NULL, NULL, NULL),
(131, 66, 'ar', 'عجوة المدينة فاخر', 'عجوة-المدينة-فاخر', NULL, NULL, NULL, NULL),
(132, 66, 'en', 'Premium Ajwa dates from Madinah', 'premium-ajwa-dates-from-madinah', NULL, NULL, NULL, NULL),
(133, 67, 'ar', 'سكري مفتل فاخر', 'سكري-مفتل-فاخر', NULL, NULL, NULL, NULL),
(134, 67, 'en', 'Sukkari Maftouh Luxury', 'sukkari-maftouh-luxury', NULL, NULL, NULL, NULL),
(135, 68, 'ar', 'نص كيلو نعيمي بلدي', 'نص-كيلو-نعيمي-بلدي', NULL, NULL, NULL, NULL),
(136, 68, 'en', 'Half a kilo of local Naeemi sheep', 'half-a-kilo-of-local-naeemi-sheep', NULL, NULL, NULL, NULL),
(137, 69, 'ar', 'كبدة', 'كبدة', NULL, NULL, NULL, NULL),
(138, 69, 'en', 'liver', 'liver', NULL, NULL, NULL, NULL),
(139, 70, 'ar', 'دجاج طازج 800ج', 'دجاج-طازج-800ج', NULL, NULL, NULL, NULL),
(140, 70, 'en', 'Fresh chicken 800g', 'fresh-chicken-800g', NULL, NULL, NULL, NULL),
(141, 71, 'ar', 'فيليه صدور دجاج طازجة 900 غم', 'فيليه-صدور-دجاج-طازجة-900-غم', NULL, NULL, NULL, NULL),
(142, 71, 'en', 'Fresh chicken breast fillets 900g', 'fresh-chicken-breast-fillets-900g', NULL, NULL, NULL, NULL),
(143, 72, 'ar', 'سمك البلطي سعر الكيلو', 'سمك-البلطي-سعر-الكيلو', NULL, NULL, NULL, NULL),
(144, 72, 'en', 'Tilapia fish price per kilo', 'tilapia-fish-price-per-kilo', NULL, NULL, NULL, NULL),
(145, 73, 'ar', 'فيليه سمك السلمون المرقط - 1 قيراط', 'فيليه-سمك-السلمون-المرقط-1-قيراط', 'سمك السلمون المرقط ذو قوام طري ونكهة خفيفة تشبه المكسرات، وليست نكهة سمكية قوية. إذا كنت من محبي سمك السلمون، فستعشق سمك السلمون المرقط! يتميز هذا السمك بتعدد استخداماته، حيث يتناسب مع طرق تحضير متنوعة مثل الشواء، والتقليب، والخبز، والقلي العميق. يمكن استخدام سمك السلمون المرقط في أي وصفة تتطلب سمك النهاش أو السلمون.', '<p>سمك السلمون المرقط ذو قوام طري ونكهة خفيفة تشبه المكسرات، وليست نكهة سمكية قوية. إذا كنت من محبي سمك السلمون، فستعشق سمك السلمون المرقط! يتميز هذا السمك بتعدد استخداماته، حيث يتناسب مع طرق تحضير متنوعة مثل الشواء، والتقليب، والخبز، والقلي العميق. يمكن استخدام سمك السلمون المرقط في أي وصفة تتطلب سمك النهاش أو السلمون.</p>\r\n\r\n<p>يُربى في مزارع برية دون أي تأثير على البيئة البحرية.</p>\r\n\r\n<p>سمك السلمون المرقط متوفر لدينا على مدار العام.</p>\r\n\r\n<p>خالٍ من المضادات الحيوية.</p>\r\n\r\n<p>سمك السلمون المرقط ممتاز للتدخين.</p>\r\n\r\n<p>ينتمي إلى نفس فصيلة سمك السلمون المرقط الصلب الرأس، لكن سمك السلمون المرقط يقضي معظم أو كل وقته في المياه العذبة. أما سمك السلمون المرقط الصلب الرأس فيولد في المياه العذبة، ويقضي حياته في البحر، ثم يعود إلى الأنهار للتكاثر.</p>', NULL, NULL),
(146, 73, 'en', 'Rainbow Trout Fillet - 1ct', 'rainbow-trout-fillet-1ct', 'Rainbow trout are a soft flaky fish with a delicate almost nutty not overly “fishy” flavor. If you like salmon you will love rainbow trout! This versatile fish works well with a variety of preparation methods like grilling, sautéing, baking, and deep-frying. Rainbow trout can be used in any recipe that calls for snapper or salmon..', '<p><br />\r\nRainbow trout are a soft flaky fish with a delicate almost nutty not overly &ldquo;fishy&rdquo; flavor. If you like salmon you will love rainbow trout! This versatile fish works well with a variety of preparation methods like grilling, saut&eacute;ing, baking, and deep-frying. Rainbow trout can be used in any recipe that calls for snapper or salmon.</p>\r\n\r\n<p>Raised on land-based farms with no impact on the sea environment.<br />\r\nOur rainbow trout is available year-round.<br />\r\nAntibiotic free.<br />\r\nRainbow trout is an excellent fish to smoke.<br />\r\nIt&rsquo;s the same species as steelhead trout, but rainbow trout spend most or all of their time in freshwater. Steelhead trout are born in freshwater, spend their lives in the sea and then go back to rivers to spawn.</p>', NULL, NULL),
(147, 74, 'ar', 'روبيان أزرق ضخم بري مقشر ومنزوع العروق - 1 رطل', 'روبيان-أزرق-ضخم-بري-مقشر-ومنزوع-العروق-1-رطل', 'يُقشّر ويُنظّف روبياننا الأزرق العملاق يدويًا، ليُقدّم لكم روبيانًا حلوًا وشهيًا جاهزًا للطهي بكل سهولة. يُصطاد هذا الروبيان في مياه المحيط الصافية قبالة سواحل خليج كاليفورنيا، احتفظوا بكيس منه في المُجمّد لوجبات عشاء سريعة وسهلة في أي وقت.', '<p>يُقشّر ويُنظّف روبياننا الأزرق العملاق يدويًا، ليُقدّم لكم روبيانًا حلوًا وشهيًا جاهزًا للطهي بكل سهولة. يُصطاد هذا الروبيان في مياه المحيط الصافية قبالة سواحل خليج كاليفورنيا، احتفظوا بكيس منه في المُجمّد لوجبات عشاء سريعة وسهلة في أي وقت.</p>\r\n\r\n<p>نظرًا لعدم استخدام أي محاليل ملحية، يكون هذا الروبيان عادةً أكبر حجمًا من أنواع الروبيان الأخرى من نفس الحجم بعد الطهي.</p>\r\n\r\n<p>مصيد بري، مستدام، وخالٍ من المواد الكيميائية.</p>\r\n\r\n<p>الروبيان الوحيد في العالم الحاصل على شهادة التجارة العادلة.<br />\r\nيُجمّد الروبيان فورًا بعد المعالجة لضمان نضارته وجودته العالية.</p>', NULL, NULL),
(148, 74, 'en', 'Wild Colossal Peeled & Deveined Blue Shrimp - 1lb', 'wild-colossal-peeled-deveined-blue-shrimp-1lb', 'Our colossal blue shrimp are peeled and deveined by hand, leaving you with a sweet and succulent shrimp that is ready to cook without any fuss. Wild-caught in the clear open ocean off the coast of the Gulf of California, keep a bag of shrimp in the freezer for quick and easy dinners anytime.', '<p>Our colossal blue shrimp are peeled and deveined by hand, leaving you with a sweet and succulent shrimp that is ready to cook without any fuss. Wild-caught in the clear open ocean off the coast of the Gulf of California, keep a bag of shrimp in the freezer for quick and easy dinners anytime.</p>\r\n\r\n<ul>\r\n	<li>Since no brines are used, these shrimp are typically larger than other shrimp of the same size after cooking.</li>\r\n	<li>Wild-caught, sustainable, and chemical-free.</li>\r\n	<li>The only Fair Trade certified shrimp in the world.</li>\r\n	<li>Shrimp is immediately frozen after processing to ensure freshness and peak quality.</li>\r\n</ul>', NULL, NULL),
(149, 75, 'ar', 'ارز شقحة 10ك', 'ارز-شقحة-10ك', NULL, NULL, NULL, NULL),
(150, 75, 'en', 'Shaqha rice 10kg', 'shaqha-rice-10kg', NULL, NULL, NULL, NULL),
(151, 76, 'ar', 'ارز صن وايت شقحة 25غم', 'ارز-صن-وايت-شقحة-25غم', NULL, NULL, NULL, NULL),
(152, 76, 'en', 'Sun White Rice, 25g', 'sun-white-rice-25g', NULL, NULL, NULL, NULL),
(153, 77, 'ar', 'سكر 1ك', 'سكر-1ك', NULL, NULL, NULL, NULL),
(154, 77, 'en', 'Sugar 1 kg', 'sugar-1-kg', NULL, NULL, NULL, NULL),
(155, 78, 'ar', 'بديل السكر بودرة 200g', 'بديل-السكر-بودرة-200g', NULL, NULL, NULL, NULL),
(156, 78, 'en', 'Sugar substitute powder 200g', 'sugar-substitute-powder-200g', NULL, NULL, NULL, NULL),
(157, 79, 'ar', 'حبيبات بديل السكر 80 غم', 'حبيبات-بديل-السكر-80-غم', NULL, NULL, NULL, NULL),
(158, 79, 'en', 'Sugar substitute granules 80g', 'sugar-substitute-granules-80g', NULL, NULL, NULL, NULL),
(159, 80, 'ar', 'معكرونة ريجيا حلزوني 500 غم', 'معكرونة-ريجيا-حلزوني-500-غم', NULL, NULL, NULL, NULL),
(160, 80, 'en', 'Regia spiral pasta 500g', 'regia-spiral-pasta-500g', NULL, NULL, NULL, NULL),
(161, 81, 'ar', 'باريلا معكرونة ايطالية 500 غم', 'باريلا-معكرونة-ايطالية-500-غم', NULL, NULL, NULL, NULL),
(162, 81, 'en', 'Barilla Italian Pasta 500g', 'barilla-italian-pasta-500g', NULL, NULL, NULL, NULL),
(163, 82, 'ar', 'خل تفاح طبيعي فرشلي', 'خل-تفاح-طبيعي-فرشلي', NULL, NULL, NULL, NULL),
(164, 82, 'en', 'Freshly Natural Apple Cider Vinegar', 'freshly-natural-apple-cider-vinegar', NULL, NULL, NULL, NULL),
(165, 83, 'ar', 'خل تفاح الزهراء 650 مل', 'خل-تفاح-الزهراء-650-مل', NULL, NULL, NULL, NULL),
(166, 83, 'en', 'Al Zahraa Apple Cider Vinegar 650ml', 'al-zahraa-apple-cider-vinegar-650ml', NULL, NULL, NULL, NULL),
(167, 84, 'ar', 'خردل بالبهارات الحارة 340 غم', 'خردل-بالبهارات-الحارة-340-غم', NULL, NULL, NULL, NULL),
(168, 84, 'en', 'Spicy Mustard 340g', 'spicy-mustard-340g', NULL, NULL, NULL, NULL),
(169, 85, 'ar', 'فلفل اسود 125 غم', 'فلفل-اسود-125-غم', NULL, NULL, NULL, NULL),
(170, 85, 'en', 'Black pepper 125g', 'black-pepper-125g', NULL, NULL, NULL, NULL),
(171, 86, 'ar', 'غسالة ملابس سوبر جنرال ذات التحميل الأمامي سعة 6 كجم', 'غسالة-ملابس-سوبر-جنرال-ذات-التحميل-الأمامي-سعة-6-كجم', 'سعة 6 كجم لغسيل فعال: تُعد هذه الغسالة ذات التحميل الأمامي بسعة 6 كجم مثالية للأسر الصغيرة والمتوسطة الحجم، حيث توفر توازنًا مثاليًا بين الحجم والأداء. سواء كنت تغسل كميات يومية أو تتعامل مع قطع كبيرة مثل أغطية الأسرة، فإنها تلبي احتياجات الغسيل المتنوعة دون أن تشغل مساحة كبيرة. تصميمها المدمج يجعلها مناسبة تمامًا للمنازل في المدن والشقق أو المساحات ذات مساحة الغسيل المحدودة.', '<p>سعة 6 كجم لغسيل فعال: تُعد هذه الغسالة ذات التحميل الأمامي بسعة 6 كجم مثالية للعائلات الصغيرة والمتوسطة، حيث توفر توازنًا مثاليًا بين الحجم والأداء. سواء كنت تغسل حمولات يومية أو تتعامل مع قطع كبيرة مثل أغطية الأسرة، فإنها تلبي احتياجات الغسيل المتنوعة دون أن تشغل مساحة كبيرة. تصميمها المدمج يجعلها مناسبة تمامًا للمنازل في المدن والشقق أو المساحات ذات مساحة الغسيل المحدودة.</p>\r\n\r\n<p>غسيل سريع وتشغيل هادئ لمزيد من الراحة: تتيح لك وظيفة الغسيل السريع تنظيف الملابس بسرعة، وهي مثالية للملابس قليلة الاتساخ أو الأحمال الصغيرة عندما يكون الوقت جوهريًا. سواء كنت تستعد للعمل أو تحتاج إلى انتعاش سريع، فإن الغسيل السريع يلبي احتياجاتك. بالإضافة إلى ذلك، يضمن التشغيل الهادئ بيئة منزلية هادئة أثناء الاستخدام، مما يجعلها مثالية لغسيل الملابس في الصباح الباكر أو في وقت متأخر من الليل دون إزعاج الآخرين.</p>\r\n\r\n<p>كفاءة عالية في استهلاك الطاقة لفواتير أقل: وفر في فواتير الطاقة مع آلية هذه الغسالة عالية الكفاءة في استهلاك الطاقة، المصممة لتقديم تنظيف قوي مع الحفاظ على الكهرباء. تُلبي هذه الغسالة معايير كفاءة الطاقة، مما يجعلها صديقة للبيئة واقتصادية في آنٍ واحد. مع كل غسلة، تُقلل من انبعاثات الكربون مع الحفاظ على نظافة الملابس وانتعاشها، كل ذلك مع التحكم في تكاليف الطاقة على المدى الطويل.</p>\r\n\r\n<p>حوض متين من الفولاذ المقاوم للصدأ لعناية تدوم طويلاً: صُمم حوض الغسالة من الفولاذ المقاوم للصدأ ليدوم طويلاً، فهو مقاوم للصدأ ويتمتع بالمتانة حتى بعد سنوات من الاستخدام. يوفر عناية لطيفة وفعالة لملابسك، مما يضمن غسلها جيدًا دون تعريضها للتلف. يساعد سطح الحوض الأملس على منع التمزق، مما يجعله مثاليًا للأقمشة الرقيقة ويضمن بقاء ملابسك في أفضل حالاتها بعد كل غسلة.</p>\r\n\r\n<p>تقنية توفير المياه لغسيل مستدام: صُممت هذه الغسالة لترشيد استهلاك المياه، مما يضمن غسيلًا فعالًا بأقل استخدام ممكن للمياه، ما يجعلها صديقة للبيئة وموفرة للتكاليف. فهي تُنظم مستويات المياه بدقة لتتناسب مع حجم الحمولة، مما يضمن الاستفادة القصوى من كل قطرة دون المساس بقوة التنظيف. هذا مثالي للأسر المهتمة بالبيئة والتي تسعى إلى تقليل استهلاكها للمياه مع الحفاظ على نتائج غسيل ممتازة.</p>', NULL, NULL),
(172, 86, 'en', 'Super General 6kg Front Loading Washing Machine, 1200rpm', 'super-general-6kg-front-loading-washing-machine-1200rpm', '6kg Capacity for Efficient Laundry: Ideal for small to medium-sized households, this 6kg front loading washing machine offers the perfect balance of size and performance. Whether you\'re washing daily loads or tackling larger items like bedding, it accommodates a variety of laundry needs without taking up too much space. Its compact design makes it a great fit for urban homes, apartments, or spaces where laundry area is limited.', '<ul>\r\n	<li>6kg Capacity for Efficient Laundry: Ideal for small to medium-sized households, this 6kg front loading washing machine offers the perfect balance of size and performance. Whether you&#39;re washing daily loads or tackling larger items like bedding, it accommodates a variety of laundry needs without taking up too much space. Its compact design makes it a great fit for urban homes, apartments, or spaces where laundry area is limited.</li>\r\n	<li>Quick Wash &amp; Silent Operation for Added Convenience: The quick wash function allows you to clean clothes in a hurry, perfect for lightly soiled garments or small loads when time is of the essence. Whether you&rsquo;re getting ready for work or need a quick refresh, the quick wash has you covered. Additionally, the silent operation ensures a peaceful home environment during use, making it perfect for early mornings or late-night laundry without disturbing others.</li>\r\n	<li>High Energy Efficiency for Lower Bills: Save on energy bills with this washing machine&rsquo;s high energy-efficient mechanism, designed to deliver powerful cleaning while conserving electricity. The machine meets energy efficiency standards, making it both eco-friendly and economical. With every wash, you&rsquo;re reducing your carbon footprint while still getting clean, fresh clothes &ndash; all while keeping long-term utility costs in check.</li>\r\n	<li>Durable Stainless-Steel Drum for Long-Lasting Care: The stainless-steel drum is designed for long-lasting performance, offering rust-resistance and durability even after years of use. It provides gentle yet effective care for your garments, ensuring that your clothes are washed thoroughly without being subjected to harsh wear and tear. The drum&rsquo;s smooth surface helps prevent snagging, making it ideal for delicate fabrics and ensuring your clothes remain in top condition after each wash.</li>\r\n	<li>Water-Saving Technology for Sustainable Washing: Optimized for water consumption, this washing machine ensures effective washing with minimal water usage, making it both eco-friendly and cost-effective. It carefully regulates water levels to match the size of the load, ensuring that every drop counts without sacrificing cleaning power. This is perfect for eco-conscious households looking to reduce their water usage while maintaining top-notch washing results.</li>\r\n</ul>', NULL, NULL),
(173, 87, 'ar', 'ثلاجة آرو بسعة 534 لترًا (18.9 قدم مكعب)، بابين، مانعة للتجمد، مزودة بلوحة تحكم رقمية موفرة للطاقة، لون فضي، موديل RO-830SNS-WD', 'ثلاجة-آرو-بسعة-534-لترًا-(18.9-قدم-مكعب)،-بابين،-مانعة-للتجمد،-مزودة-بلوحة-تحكم-رقمية-موفرة-للطاقة،-لون-فضي،-موديل-ro-830sns-wd', 'أداء موثوق به للاستخدام اليومي\r\nمُفعّل ومُنتج طوال اليوم.\r\n\r\nاحصل على منظور جديد\r\nأرفف من الزجاج المقوى\r\nالتحكم في درجة الحرارة\r\nنضارة تدوم طويلاً', '<p>أداء موثوق به للاستخدام اليومي<br />\r\nمُفعّل ومُنتج طوال اليوم.</p>\r\n\r\n<p>احصل على منظور جديد<br />\r\nأرفف من الزجاج المقوى<br />\r\nالتحكم في درجة الحرارة<br />\r\nنضارة تدوم طويلاً</p>', NULL, NULL),
(174, 87, 'en', 'ARROW 534-Liter 18.9 Cubic Feet, Frost Free Double Door Refrigerator With Power Saving Digital Control Panel Silver Colour RO-830SNS-WD', 'arrow-534-liter-189-cubic-feet-frost-free-double-door-refrigerator-with-power-saving-digital-control-panel-silver-colour-ro-830sns-wd', 'Reliable performance for every day\r\nPowered up and productive. All day long.\r\nGet a fresh perspective\r\nTempered glass shelves\r\nTemperature control\r\nLong-lasting freshness', '<p>Reliable performance for every day<br />\r\nPowered up and productive. All day long.<br />\r\nGet a fresh perspective<br />\r\nTempered glass shelves<br />\r\nTemperature control<br />\r\nLong-lasting freshness</p>', NULL, NULL),
(175, 88, 'ar', 'مكنسة كهربائية إمبكس بسعة 21 لترًا | قوة عالية 2000 واط', 'مكنسة-كهربائية-إمبكس-بسعة-21-لترًا-|-قوة-عالية-2000-واط', 'سعة كبيرة للغبار ٢١ لترًا لتنظيف سهل ومريح - بفضل سعتها الكبيرة التي تبلغ ٢١ لترًا، يمكنك التنظيف لفترة أطول دون الحاجة إلى تفريغها بشكل متكرر، مما يجعلها مثالية للمنازل الكبيرة والمساحات التجارية ومهام التنظيف المستمرة.', '<p>سعة كبيرة للغبار ٢١ لترًا لتنظيف سهل ومريح - بفضل سعتها الكبيرة التي تبلغ ٢١ لترًا، يمكنك التنظيف لفترة أطول دون الحاجة إلى تفريغها بشكل متكرر، مما يجعلها مثالية للمنازل الكبيرة والمساحات التجارية ومهام التنظيف المستمرة.</p>\r\n\r\n<p>مستوى ضوضاء منخفض لتنظيف مريح - استمتع بتجربة تنظيف هادئة بفضل تقنية خفض الضوضاء، مما يقلل من الإزعاج مع ضمان قوة شفط عالية وأداء تنظيف فعال.</p>\r\n\r\n<p>محرك قوي ٢٠٠٠ واط لتنظيف عالي الكفاءة - تمتع بقوة شفط فائقة مع مكنسة إمبكس VC 4704 الكهربائية، المصممة بمحرك ٢٠٠٠ واط لتوفير أداء تنظيف عميق للغبار والأوساخ والشوائب على جميع الأسطح.</p>\r\n\r\n<p>هيكل معدني متين وقوي مع أنبوب تلسكوبي - بفضل هيكلها المعدني المتين وأنبوبها المعدني التلسكوبي القابل للتعديل، تضمن هذه المكنسة متانة طويلة الأمد ومدى تنظيف مرن، مما يجعلها مثالية للاستخدام المنزلي والمكتبي.</p>\r\n\r\n<p>وظيفة مزدوجة مع وضع النفخ - ليست مجرد مكنسة كهربائية! تساعدك وظيفة النفخ على إزالة الغبار من الزوايا التي يصعب الوصول إليها، والأثاث، والمساحات الخارجية، مما يوفر لك تنوعًا كاملاً في التنظيف 2 في 1.</p>\r\n\r\n<p>مؤشر ذكي لامتلاء خزان الغبار ومقبض حمل مريح - ينبهك مؤشر امتلاء الخزان عندما يحين وقت تفريغه، بينما يضمن مقبض الحمل المريح سهولة الحركة في منزلك أو مكان عملك.</p>\r\n\r\n<p>الخيار الأمثل لاحتياجات التنظيف المنزلية والتجارية - سواءً كان الأمر يتعلق بالسجاد، أو الأرضيات، أو الأثاث، أو الغبار الخارجي، فإن مكنسة إمبكس VC 4704 عالية الطاقة توفر تنظيفًا موثوقًا وعالي الأداء لتطبيقات متعددة.</p>\r\n\r\n<p>موثوقة ومتينة مع ضمان لمدة عامين - مدعومة بضمان من الشركة المصنعة لمدة عامين، مما يضمن جودة عالية وموثوقية وراحة بال للاستخدام طويل الأمد.</p>', NULL, NULL),
(176, 88, 'en', 'Impex 21L Large Capacity Vacuum Cleaner | 2000W High Power', 'impex-21l-large-capacity-vacuum-cleaner-2000w-high-power', 'Large 21L Dust Capacity for Hassle-Free Cleaning – With a generous 21-litre capacity, you can clean longer without frequent emptying, making it ideal for large households, commercial spaces, and continuous cleaning tasks.', '<p>Large 21L Dust Capacity for Hassle-Free Cleaning &ndash; With a generous 21-litre capacity, you can clean longer without frequent emptying, making it ideal for large households, commercial spaces, and continuous cleaning tasks.<br />\r\nLow Noise Level for Comfortable Cleaning &ndash; Enjoy a peaceful cleaning experience with the low-noise technology, reducing disturbance while ensuring powerful suction and efficient cleaning performance.<br />\r\nPowerful 2000W Motor for High-Efficiency Cleaning &ndash; Experience superior suction power with the Impex VC 4704 Vacuum Cleaner, designed with a 2000W motor to deliver deep cleaning performance for dust, dirt, and debris on all surfaces.<br />\r\nDurable Strong Metal Body with Telescopic Tube &ndash; Built with a sturdy metal body and adjustable telescopic metal tube, this vacuum ensures long-lasting durability and flexible cleaning reach, making it perfect for both home and office use.<br />\r\nDual Function with Blower Mode &ndash; Not just a vacuum! The blower function helps you blow away dust from hard-to-reach corners, furniture, and outdoor spaces, offering complete 2-in-1 cleaning versatility.<br />\r\nSmart Dust Full Indicator &amp; Convenient Carrying Handle &ndash; The dust-full indicator alerts you when it&rsquo;s time to empty, while the convenient carrying handle ensures effortless movement around your home or workspace.<br />\r\nPerfect Choice for Home &amp; Commercial Cleaning Needs &ndash; Whether it&rsquo;s carpets, floors, furniture, or outdoor dust, the Impex VC 4704 High-Power Vacuum Cleaner delivers reliable, high-performance cleaning for versatile applications.<br />\r\nReliable &amp; Durable with 2-Year Warranty &ndash; Backed by a 2-year manufacturer warranty, ensuring trusted quality, durability, and peace of mind for long-term use.</p>', NULL, NULL),
(177, 89, 'ar', 'تيشيرت رياضي', 'تيشيرت-رياضي', 'حرير سريع الجفاف وطارد للرطوبة وتيشيرت رياضي بأكمام قصيرة للجري والتدريب للرجال، 1 قطعة', NULL, NULL, NULL),
(178, 89, 'en', 'Men\'s Quick-Drying', 'mens-quick-drying', 'Quick-drying, moisture-wicking silk short-sleeved athletic t-shirt for men, ideal for running and training, 1 piece', NULL, NULL, NULL),
(179, 90, 'ar', 'شورت رياضي', 'شورت-رياضي', 'قماش بوليستر: الشورتات الرياضية للرجال مصنوعة من مادة البوليستر الناعمة والمتينة، وهي خفيفة الوزن ولطيفة على البشرة وتدوم طويلاً؛ تصميم قابل للغسل في الغسالة، سهل الصيانة، قادر على الحفاظ على الشكل واللون حتى بعد الارتداء المتعدد.', '<p>قماش بوليستر: الشورتات الرياضية للرجال مصنوعة من مادة البوليستر الناعمة والمتينة، وهي خفيفة الوزن ولطيفة على البشرة وتدوم طويلاً؛ تصميم قابل للغسل في الغسالة، سهل الصيانة، قادر على الحفاظ على الشكل واللون حتى بعد الارتداء المتعدد.<br />\r\nجيد التهوية وسريع الجفاف، منعش وغير محشو: مقترنة بتصميم فتحة حصرية جيدة التهوية ونسيج ماص للرطوبة، يمكنها طرد العرق بسرعة أثناء التمرين وتحقيق تأثير تجفيف سريع ممتاز. يحافظ على جفافك وبرودة جسمك أثناء ممارسة الأنشطة الرياضية عالية الشدة، مثل اللياقة البدنية، والجري، وكرة السلة، وغيرها.<br />\r\nحزام خصر عالي المرونة، مقاس مريح: خصر مرن عالي المرونة، يناسب بدون ضيق، مناسب لأشكال الجسم المختلفة؛ ليس من السهل أن ينزلق أثناء التمرين، مما يوفر تجربة ارتداء مريحة للملابس الرياضية والأنشطة الخارجية.<br />\r\nجيوب متعددة بسحاب + تصميم شريط عاكس: يحتوي الشورت على جيبين جانبيين وجيب خلفي واحد، وجميعهم مجهزة بسحابات أمان لتخزين الأشياء الصغيرة بأمان مثل الهواتف والمفاتيح؛ بالإضافة إلى تصميم الشريط العاكس، فهو أكثر جاذبية وأمانًا للتمرين عند الغسق والليل.<br />\r\nمتوفرة بألوان متعددة، مجموعة من قطعتين أكثر عملية: توفر الشورتات 6 خيارات للألوان، عبوة من مجموعتين، مريحة للغسيل اليومي، ومناسبة للرجال الذين يمارسون التمارين الرياضية بانتظام للارتداء اليومي والملابس الرياضية.</p>', NULL, NULL),
(180, 90, 'en', 'Sports shorts', 'sports-shorts', 'Polyester Fabric: These men\'s athletic shorts are made of soft and durable polyester, making them lightweight, skin-friendly, and long-lasting. The machine-washable design is easy to care for and maintains its shape and color even after multiple wears.', '<p>Polyester Fabric: These men&#39;s athletic shorts are made of soft and durable polyester, making them lightweight, skin-friendly, and long-lasting. The machine-washable design is easy to care for and maintains its shape and color even after multiple wears.</p>\r\n\r\n<p>Breathable and Quick-Drying: The breathable, moisture-wicking fabric, combined with an exclusive ventilation design, quickly draws out sweat during exercise, providing excellent quick-drying performance. This keeps you dry and cool during high-intensity activities such as fitness, running, basketball, and more.</p>\r\n\r\n<p>High-Elastic Waistband, Comfortable Fit: The highly elastic waistband provides a comfortable, non-restrictive fit suitable for various body types. It stays in place during exercise, ensuring a comfortable fit for sportswear and outdoor activities.</p>\r\n\r\n<p>Multiple Zippered Pockets + Reflective Strip Design: The shorts feature two side pockets and one back pocket, all equipped with secure zippers for storing small items like phones and keys. The reflective strip design enhances visibility and safety during low-light and nighttime workouts. Available in multiple colors, a more practical two-piece set: The shorts offer 6 color options, a two-pack, are comfortable for everyday washing, and are suitable for men who exercise regularly for everyday wear and sportswear.</p>', NULL, NULL),
(181, 91, 'ar', 'حذاء جري نورث بلس للرجال من كامبوس', 'حذاء-جري-نورث-بلس-للرجال-من-كامبوس', 'إغلاق برباط مع نوع ربط بالأشرطة يتيح ملاءمة قابلة للتعديل وارتداء آمن أثناء الأنشطة\r\nنوع كعب مسطح مع مادة نعل داخلي من الفوم الذكي يوفر التوسيد ويتكيف مع شكل القدم\r\nنوع رياضة الجري مصمم للرياضة والاستخدام على الأسطح الطرقية مع تصميم شبكي قابل للتهوية\r\nاسم الموديل كامبوس بلو نورث بلس مع رقم الموديل 11G-677 باللون AF.BLU\r\nارتداء لجميع المواسم مع نوع نمط سادة ووصف بطانة شبكية للتهوية', '<p>إغلاق برباط مع نوع ربط بالأشرطة يتيح ملاءمة قابلة للتعديل وارتداء آمن أثناء الأنشطة<br />\r\nنوع كعب مسطح مع مادة نعل داخلي من الفوم الذكي يوفر التوسيد ويتكيف مع شكل القدم<br />\r\nنوع رياضة الجري مصمم للرياضة والاستخدام على الأسطح الطرقية مع تصميم شبكي قابل للتهوية<br />\r\nاسم الموديل كامبوس بلو نورث بلس مع رقم الموديل 11G-677 باللون AF.BLU<br />\r\nارتداء لجميع المواسم مع نوع نمط سادة ووصف بطانة شبكية للتهوية</p>', NULL, NULL),
(182, 91, 'en', 'Campus North Plus Men\'s Running Shoes', 'campus-north-plus-mens-running-shoes', 'Lace-up closure with a strap-on design allows for an adjustable fit and secure wear during activities.\r\nFlat heel with a memory foam insole provides cushioning and adapts to the shape of the foot.\r\nRunning type: Designed for sports and road surfaces with a breathable mesh upper.\r\nModel name: Campus Blue North Plus, model number: 11G-677, color: AF.BLU.\r\nAll-season wear with a solid pattern and breathable mesh lining.', '<p>Lace-up closure with a strap-on design allows for an adjustable fit and secure wear during activities.<br />\r\nFlat heel with a memory foam insole provides cushioning and adapts to the shape of the foot.<br />\r\nRunning type: Designed for sports and road surfaces with a breathable mesh upper.<br />\r\nModel name: Campus Blue North Plus, model number: 11G-677, color: AF.BLU.<br />\r\nAll-season wear with a solid pattern and breathable mesh lining.</p>', NULL, NULL),
(183, 92, 'ar', 'فيسوو تي شيرت رياضي خفيف الوزن برقبة دائرية ضيقة واكمام قصيرة للنساء', 'فيسوو-تي-شيرت-رياضي-خفيف-الوزن-برقبة-دائرية-ضيقة-واكمام-قصيرة-للنساء', 'قميص نسائي للبالغات بمقاس M بقصة جسم عادية ومناسب لطول عادي\r\nتي شيرت بأكمام قصيرة وياقة دائرية بنمط أسود سادة مع تصميم حافة متموجة تغطي الوركين\r\nمصنوع من مزيج 45% بوليستر و45% قطن و10% إيلاستين لتجربة ارتداء مريحة وقابلة للتهوية\r\nمناسب لليوغا والجري واللياقة البدنية والصالة الرياضية والبيلاتس وأنشطة التمارين أو الارتداء اليومي\r\nيُغسل في الغسالة أو باليد بالماء البارد، بدون تعرض لأشعة الشمس، بدون تبييض، بدون تجفيف ساخن', '<p>قميص نسائي للبالغات بمقاس M بقصة جسم عادية ومناسب لطول عادي<br />\r\nتي شيرت بأكمام قصيرة وياقة دائرية بنمط أسود سادة مع تصميم حافة متموجة تغطي الوركين<br />\r\nمصنوع من مزيج 45% بوليستر و45% قطن و10% إيلاستين لتجربة ارتداء مريحة وقابلة للتهوية<br />\r\nمناسب لليوغا والجري واللياقة البدنية والصالة الرياضية والبيلاتس وأنشطة التمارين أو الارتداء اليومي<br />\r\nيُغسل في الغسالة أو باليد بالماء البارد، بدون تعرض لأشعة الشمس، بدون تبييض، بدون تجفيف ساخن</p>', NULL, NULL),
(184, 92, 'en', 'Viso Women\'s Lightweight Crew Neck Short Sleeve Sports T-Shirt', 'viso-womens-lightweight-crew-neck-short-sleeve-sports-t-shirt', 'Women\'s adult size M T-shirt with a regular fit and average length.\r\nShort-sleeved crew neck T-shirt in a solid black pattern with a wavy hem that covers the hips\r\nMade from a blend of 45% polyester, 45% cotton, and 10% elastane for a comfortable and breathable fit.\r\nSuitable for yoga, running, fitness, gym workouts, Pilates, exercise, or everyday wear.\r\nMachine or hand wash in cold water, do not expose to sunlight, do not bleach, do not tumble dry.', '<p>Women&#39;s adult size M T-shirt with a regular fit and average length.</p>\r\n\r\n<p>Short-sleeved crew neck T-shirt in a solid black pattern with a wavy hem that covers the hips.<br />\r\nMade from a blend of 45% polyester, 45% cotton, and 10% elastane for a comfortable and breathable fit.<br />\r\nSuitable for yoga, running, fitness, gym workouts, Pilates, exercise, or everyday wear.<br />\r\nMachine or hand wash in cold water, do not expose to sunlight, do not bleach, do not tumble dry.</p>', NULL, NULL),
(185, 93, 'ar', 'طقم جلباب وعباية', 'طقم-جلباب-وعباية', NULL, '<p>صميم أنيق ومحتشم: طقم صلاة إسلامي مكون من قطعتين، يشتمل على جلباب بأكمام كاملة مع تنورة متطابقة، ليوفر لكِ تغطية كاملة وراحة تامة.<br />\r\nسهل الارتداء وقابل للتعديل: يتميز بخصر مطاطي مريح وتصميم فضفاض، مما يجعله مناسبًا لمختلف الأجسام والمقاسات.<br />\r\nأنيق وعملي: مزيج يجمع بين الأناقة التقليدية واللمسة العصرية، مما يجعله خيارًا رائعًا للمنزل، والمساجد، وأثناء السفر.</p>', NULL, NULL),
(186, 93, 'en', 'Jilbab and Abaya Set', 'jilbab-and-abaya-set', NULL, '<p>Elegant and modest design: This two-piece Islamic prayer set includes a full-sleeved abaya and matching skirt, providing complete coverage and comfort.</p>\r\n\r\n<p>Easy to wear and adjustable: Featuring a comfortable elastic waistband and a relaxed fit, it accommodates various body types and sizes.</p>\r\n\r\n<p>Stylish and practical: Combining traditional elegance with a modern touch, this set is a great choice for home, mosques, and travel.</p>', NULL, NULL),
(187, 94, 'ar', 'طقم سويتر', 'طقم-سويتر', 'هذه الأطقم النسائية المكونة من قطعتين مصنوعة من قماش ناعم وخفيف الوزن وعالي الجودة يجعلك تتمتعين بتجربة ارتداء أفضل.', '<p>أطقم كاجوال عصرية ومريحة للنساء<br />\r\nهذه الأطقم النسائية المكونة من قطعتين مصنوعة من قماش ناعم وخفيف الوزن وعالي الجودة يجعلك تتمتعين بتجربة ارتداء أفضل.<br />\r\nبلوزات بلوفر: أكمام قصيرة، قبة دائرية، كتف متدلي، وحاشية مقسمة جانبية لبلوزات سويتر كاجوال للنساء<br />\r\nالسراويل: خصر مرتفع للنساء، حزام خصر مطاطي، لون سادة لسراويل رسمية فضفاضة، قصة مريحة، سروال مريح بأرجل واسعة للنساء<br />\r\nالمناسبة: ملابس نسائية كاجوال من قطعتين ليست فقط سويتر ولكن أيضًا ملابس مريحة للاسترخاء ومجموعات بيجاما وملابس يومية وبدلات نزهة والتسوق والعطلات وما إلى ذلك. يمكن للأحذية الرياضية العادية أن تكمل مظهرًا أنيقًا في الحياة اليومية، أو ستبدو أنيقًا مع جاكيت أو معطف أو كارديجان في الأيام الباردة</p>', NULL, NULL),
(188, 94, 'en', 'Sweater set', 'sweater-set', 'These two-piece women\'s outfits are made from soft, lightweight, and high-quality fabric for a comfortable wearing experience.', '<p>Women&#39;s Casual and Comfortable Outfits</p>\r\n\r\n<p>These two-piece women&#39;s outfits are made from soft, lightweight, and high-quality fabric for a comfortable wearing experience.</p>\r\n\r\n<p>Pull-out Top: Short sleeves, round neck, dropped shoulders, and a side slit hem for a casual women&#39;s pullover top.</p>\r\n\r\n<p>Pants: High-waisted women&#39;s pants with an elastic waistband, solid color, loose fit, and a relaxed, wide-leg silhouette.</p>\r\n\r\n<p>Occasion: This two-piece women&#39;s casual outfit isn&#39;t just a sweater; it&#39;s also perfect for lounging, pajama sets, everyday wear, picnics, shopping, vacations, and more. Pair it with casual sneakers for a stylish everyday look, or layer it with a jacket, coat, or cardigan for a more sophisticated look on cooler days.</p>', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `combination_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `track_quantity` tinyint(1) NOT NULL DEFAULT '1',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `combination_key`, `sku`, `price`, `quantity`, `track_quantity`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 44, '25', NULL, 150.00, NULL, 1, NULL, 1, '2026-07-25 12:23:45', '2026-07-25 12:23:45', NULL),
(8, 44, '26', NULL, 250.00, NULL, 1, NULL, 1, '2026-07-25 12:23:46', '2026-07-25 12:23:46', NULL),
(9, 44, '27', NULL, 400.00, NULL, 1, NULL, 1, '2026-07-25 12:23:46', '2026-07-25 12:23:46', NULL),
(10, 87, '6', NULL, 533.00, 25.00, 1, NULL, 1, '2026-07-26 15:32:34', '2026-07-26 15:32:34', NULL),
(11, 87, '12', NULL, 633.00, 30.00, 1, NULL, 1, '2026-07-26 15:32:34', '2026-07-26 15:32:34', NULL),
(12, 87, '13', NULL, 733.00, 40.00, 1, NULL, 1, '2026-07-26 15:32:34', '2026-07-26 15:32:34', NULL),
(13, 87, '15', NULL, 833.00, 50.00, 1, NULL, 1, '2026-07-26 15:32:34', '2026-07-26 15:32:34', NULL),
(14, 89, '2_6', NULL, 200.00, 10.00, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(15, 89, '2_12', NULL, 250.00, 30.00, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(16, 89, '2_13', NULL, 300.00, 40.00, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(17, 89, '2_14', NULL, 310.00, NULL, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(18, 89, '2_15', NULL, 320.00, NULL, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(19, 89, '2_16', NULL, 330.00, NULL, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(20, 89, '3_6', NULL, 340.00, NULL, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(21, 89, '3_12', NULL, 350.00, NULL, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(22, 89, '3_13', NULL, 360.00, NULL, 1, NULL, 1, '2026-07-26 15:59:40', '2026-07-26 15:59:40', NULL),
(23, 89, '3_14', NULL, 370.00, NULL, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(24, 89, '3_15', NULL, 380.00, NULL, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(25, 89, '3_16', NULL, 390.00, NULL, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(26, 89, '6_7', NULL, 400.00, NULL, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(27, 89, '7_12', NULL, 410.00, 60.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(28, 89, '7_13', NULL, 420.00, NULL, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(29, 89, '7_14', NULL, 430.00, 6.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(30, 89, '7_15', NULL, 440.00, 5.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(31, 89, '7_16', NULL, 450.00, 4.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(32, 89, '6_8', NULL, 460.00, 3.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(33, 89, '8_12', NULL, 460.00, 10.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(34, 89, '8_13', NULL, 470.00, 5.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(35, 89, '8_14', NULL, 480.00, 12.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(36, 89, '8_15', NULL, 490.00, 22.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(37, 89, '8_16', NULL, 500.00, 20.00, 1, NULL, 1, '2026-07-26 15:59:41', '2026-07-26 15:59:41', NULL),
(38, 92, '2_6', NULL, 130.00, 20.00, 1, NULL, 1, '2026-07-26 16:14:31', '2026-07-28 11:01:03', NULL),
(39, 92, '2_12', NULL, 150.00, 30.00, 1, NULL, 1, '2026-07-26 16:14:31', '2026-07-26 16:14:31', NULL),
(40, 92, '6_7', NULL, 200.00, 10.00, 1, NULL, 1, '2026-07-26 16:14:31', '2026-07-26 16:14:31', NULL),
(41, 92, '7_12', NULL, 220.00, 20.00, 1, NULL, 1, '2026-07-26 16:14:31', '2026-07-26 16:14:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_attribute_value`
--

CREATE TABLE `product_variant_attribute_value` (
  `id` bigint UNSIGNED NOT NULL,
  `product_variant_id` bigint UNSIGNED NOT NULL,
  `attribute_value_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variant_attribute_value`
--

INSERT INTO `product_variant_attribute_value` (`id`, `product_variant_id`, `attribute_value_id`) VALUES
(13, 7, 25),
(14, 8, 26),
(15, 9, 27),
(16, 10, 6),
(17, 11, 12),
(18, 12, 13),
(19, 13, 15),
(20, 14, 2),
(21, 14, 6),
(22, 15, 2),
(23, 15, 12),
(24, 16, 2),
(25, 16, 13),
(26, 17, 2),
(27, 17, 14),
(28, 18, 2),
(29, 18, 15),
(30, 19, 2),
(31, 19, 16),
(32, 20, 3),
(33, 20, 6),
(34, 21, 3),
(35, 21, 12),
(36, 22, 3),
(37, 22, 13),
(38, 23, 3),
(39, 23, 14),
(40, 24, 3),
(41, 24, 15),
(42, 25, 3),
(43, 25, 16),
(45, 26, 6),
(44, 26, 7),
(46, 27, 7),
(47, 27, 12),
(48, 28, 7),
(49, 28, 13),
(50, 29, 7),
(51, 29, 14),
(52, 30, 7),
(53, 30, 15),
(54, 31, 7),
(55, 31, 16),
(57, 32, 6),
(56, 32, 8),
(58, 33, 8),
(59, 33, 12),
(60, 34, 8),
(61, 34, 13),
(62, 35, 8),
(63, 35, 14),
(64, 36, 8),
(65, 36, 15),
(66, 37, 8),
(67, 37, 16),
(68, 38, 2),
(69, 38, 6),
(70, 39, 2),
(71, 39, 12),
(73, 40, 6),
(72, 40, 7),
(74, 41, 7),
(75, 41, 12);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '2025-12-15 13:23:38', '2025-12-15 13:23:38'),
(2, 'employee', 'admin', '2025-12-15 13:23:38', '2025-12-15 13:23:38'),
(3, 'client', 'web', '2025-12-15 13:23:38', '2025-12-15 13:23:38'),
(7, 'driver', 'web', '2026-07-22 15:42:38', '2026-07-22 15:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Go7W2cyfMWTxFly8Izj7wba14pLmnVOr37ZWkY7l', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidnhxRTlIdjlEaVZOYnNzb241dlhrMlJQVFZnQnVQYmlabVMzU09wUCI7czoyMjoiUEhQREVCVUdCQVJfU1RBQ0tfREFUQSI7YTowOnt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1785251414),
('oTdrDmk3jfIFcjh5YoWUPpOlfJ1LcNjvlYueeplW', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiUjFCaER6QVNXQXlKS2dtUktwTkxlS3lEbXJDT1JwS0pBekpHcFFiRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hci9hZG1pbiI7czo1OiJyb3V0ZSI7czoxNToiYWRtaW4uZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2xvZ2luIjt9czo2OiJsb2NhbGUiO3M6MjoiYXIiO3M6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjIyOiJQSFBERUJVR0JBUl9TVEFDS19EQVRBIjthOjA6e319', 1785314445),
('ugOdUAjSTDkRjFARTpHGR5Lp6LYwxTeBhyuEPKmc', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiaUo4WWFxVFpjMUp0UUh1WFdKSXlORjRnb3NBcDNCcWg0c0xWRVo3aiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoxMDU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hci9hZG1pbi90ZXh0cGFnZXMvZWRpdHRleHQvJUQ4JUI5JUQ5JTg2LSVEOCVBNyVEOSU4NCVEOCVBQSVEOCVCNyVEOCVBOCVEOSU4QSVEOSU4MiI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXIvYWRtaW4vcGF5bWVudC1tZXRob2RzIjtzOjU6InJvdXRlIjtzOjE0OiJwYXltZW50bWV0aG9kcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NjoibG9jYWxlIjtzOjI6ImFyIjtzOjUyOiJsb2dpbl9hZG1pbl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoyMjoiUEhQREVCVUdCQVJfU1RBQ0tfREFUQSI7YTowOnt9fQ==', 1785243452),
('v4arnL47BQUtDRZVj2rvuyerp0C2qQ1eAqWXrlsd', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoib3lkcEZDS2lyM09hbm8zQlg4U0J6elFXU0p2V1RwM3VXVGlvWE1PUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hci9hZG1pbi9vcmRlcnMiO3M6NToicm91dGUiO3M6Njoib3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2xvZ2luIjt9czo2OiJsb2NhbGUiO3M6MjoiYXIiO3M6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjIyOiJQSFBERUJVR0JBUl9TVEFDS19EQVRBIjthOjA6e319', 1785255706);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_translatable` tinyint(1) NOT NULL DEFAULT '0',
  `plain_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `is_translatable`, `plain_value`, `created_at`, `updated_at`) VALUES
(1, 'store_logo', 0, 'ac628a48-3bbd-4443-bbc4-09373e6009bd.webp', '2025-12-17 11:18:36', '2026-07-19 08:25:07'),
(2, 'store_email', 0, 'dev.magic.noon@gmail.com', '2025-12-17 11:18:36', '2025-12-17 12:08:03'),
(3, 'store_mobile', 0, '972599832753', '2025-12-17 11:18:36', '2025-12-17 12:08:03'),
(4, 'store_whatsapp', 0, '972599832753', '2025-12-17 11:18:36', '2025-12-17 12:08:04'),
(5, 'added_tax', 0, '10', '2025-12-17 11:18:36', '2025-12-17 11:18:36'),
(6, 'appClosed', 0, '0', '2025-12-17 11:18:36', '2026-07-27 10:33:20'),
(7, 'appClosedText', 1, NULL, '2025-12-17 11:18:36', '2025-12-17 11:18:36'),
(8, 'showFullScreenAdvert', 0, '1', '2025-12-17 11:18:36', '2026-07-27 10:37:47'),
(9, 'currentVersionNumber', 0, '1.0.0', '2025-12-17 11:18:36', '2025-12-17 11:18:36'),
(10, 'appStorePageLink', 0, 'https://apps.apple.com/us/app/%D8%A7%D8%B3%D9%87%D9%84-%D8%AA%D8%B3%D8%B9%D9%8A%D8%B1%D8%A9/id6748383709', '2025-12-17 11:18:36', '2025-12-17 13:27:44'),
(11, 'googlePlayPageLink', 0, 'https://play.google.com/store/apps/details?id=com.mhsneen.tasera&hl=ar&pli=1', '2025-12-17 11:18:37', '2025-12-17 13:27:44'),
(12, 'delivery_price', 0, '3', '2025-12-17 11:18:37', '2026-07-19 09:58:50'),
(13, 'bannerLink', 0, '9ce1806c-ff3f-4590-b2b4-a4e0d21875e1.webp', '2025-12-17 11:18:37', '2025-12-17 13:27:05'),
(14, 'store_name', 1, NULL, '2025-12-17 11:18:37', '2025-12-17 11:18:37'),
(15, 'store_address', 1, NULL, '2025-12-17 11:18:37', '2025-12-17 11:18:37'),
(16, 'free_shipping_sentence', 1, NULL, '2025-12-17 11:18:37', '2025-12-17 11:18:37'),
(17, 'free_shipping_details', 1, NULL, '2025-12-17 11:18:37', '2025-12-17 11:18:37'),
(18, 'return_policy_sentence', 1, NULL, '2025-12-17 11:18:37', '2025-12-17 11:18:37'),
(19, 'return_policy_details', 1, NULL, '2025-12-17 11:18:37', '2025-12-17 11:18:37'),
(20, 'secure_payment_sentence', 1, NULL, '2025-12-17 11:18:38', '2025-12-17 11:18:38'),
(21, 'secure_payment_details', 1, NULL, '2025-12-17 11:18:38', '2025-12-17 11:18:38'),
(22, 'expired_quantities', 0, '10', '2026-01-14 10:58:10', '2026-01-14 10:00:51'),
(23, 'minimum_price_order', 0, '400', '2026-01-17 09:34:55', '2026-01-17 10:34:34'),
(24, 'start_work', 0, '{\"saturday\":\"08:00\",\"sunday\":null,\"monday\":null,\"tuesday\":\"08:00\",\"wednesday\":null,\"thursday\":null}', '2026-01-17 11:29:55', '2026-07-28 09:54:26'),
(25, 'end_work', 0, '{\"saturday\":\"12:00\",\"sunday\":null,\"monday\":null,\"tuesday\":\"23:00\",\"wednesday\":null,\"thursday\":null}', NULL, '2026-07-28 09:55:08'),
(26, 'days_work', 0, '[\"saturday\",\"sunday\",\"monday\",\"tuesday\",\"wednesday\",\"thursday\"]', NULL, '2026-07-28 09:54:26'),
(27, 'longitude', 0, '46.6906406', NULL, '2026-01-17 11:24:24'),
(28, 'Latitude', 0, '24.555826', NULL, '2026-01-17 11:24:24'),
(29, 'delivery_max_distance', 0, '25', NULL, '2026-01-17 11:24:24'),
(31, 'open_work', 0, '1', NULL, '2026-07-28 09:38:06'),
(32, 'out_of_working_hours_message', 1, NULL, '2026-07-19 09:00:33', '2026-07-19 09:00:33'),
(33, 'free_delivery_enabled', 0, '0', '2026-07-19 11:35:45', '2026-07-19 09:58:50'),
(34, 'free_delivery_distance', 0, '0', '2026-07-19 09:35:45', '2026-07-19 09:58:50'),
(35, 'delivery_price_type', 0, '2', '2026-07-19 09:36:58', '2026-07-28 11:53:46'),
(36, 'fixed_delivery_price', 0, '5', '2026-07-19 09:36:58', '2026-07-19 09:58:50'),
(37, 'verification_method', 0, 'sms', '2026-07-19 10:10:41', '2026-07-20 18:17:07'),
(38, 'sms_provider', 0, '1', '2026-07-19 10:28:22', '2026-07-19 11:05:50'),
(39, 'sms_api_key', 0, '2', '2026-07-19 10:28:22', '2026-07-19 11:05:50'),
(40, 'sms_sender_id', 0, '3', '2026-07-19 10:28:57', '2026-07-19 11:05:50'),
(41, 'sms_username', 0, '4', '2026-07-19 10:28:57', '2026-07-19 11:05:50'),
(42, 'sms_password', 0, '5', '2026-07-19 10:29:24', '2026-07-19 11:05:50'),
(43, 'sms_base_url', 0, '6', '2026-07-19 10:29:24', '2026-07-19 11:05:50'),
(44, 'whatsapp_provider', 0, '7', '2026-07-19 10:55:06', '2026-07-19 11:07:51'),
(45, 'whatsapp_api_url', 0, '8', '2026-07-19 10:55:06', '2026-07-19 11:07:51'),
(46, 'whatsapp_token', 0, '9', '2026-07-19 10:55:30', '2026-07-19 11:07:51'),
(47, 'whatsapp_instance_id', 0, '10', '2026-07-19 10:55:30', '2026-07-19 11:07:51'),
(48, 'whatsapp_phone_number', 0, '11', '2026-07-19 10:55:52', '2026-07-19 11:07:51'),
(49, 'whatsapp_phone_number_id', 0, '12', '2026-07-19 10:55:52', '2026-07-19 11:07:51'),
(50, 'whatsapp_business_account_id', 0, '13', '2026-07-19 10:56:13', '2026-07-19 11:07:51'),
(51, 'mail_mailer', 0, '14', '2026-07-19 11:29:55', '2026-07-19 11:38:37'),
(52, 'mail_host', 0, '15', '2026-07-19 11:29:55', '2026-07-19 11:38:37'),
(53, 'mail_port', 0, '16', '2026-07-19 11:30:35', '2026-07-19 11:38:37'),
(54, 'mail_username', 0, '17', '2026-07-19 11:30:35', '2026-07-19 11:38:37'),
(55, 'mail_password', 0, '18', '2026-07-19 11:30:55', '2026-07-19 11:38:37'),
(56, 'mail_encryption', 0, '19', '2026-07-19 11:30:55', '2026-07-19 11:38:37'),
(57, 'mail_from_address', 0, '20', '2026-07-19 11:31:17', '2026-07-19 11:38:37'),
(58, 'mail_from_name', 0, '21', '2026-07-19 11:31:17', '2026-07-19 11:38:37'),
(59, 'registration_activation_mode', 0, 'verification', '2026-07-20 09:35:45', '2026-07-20 18:47:38'),
(60, 'test_mode', 0, '1', '2026-07-20 10:54:27', '2026-07-20 10:54:27'),
(61, 'dynamic_delivery_pricing', 0, '[{\"from\":\"1\",\"to\":\"5\",\"price\":\"10\"},{\"from\":\"5\",\"to\":\"10\",\"price\":\"20\"}]', '2026-07-22 15:37:00', '2026-07-22 15:37:00'),
(62, 'loyalty_points_enabled', 0, '1', '2026-07-23 10:28:28', '2026-07-23 11:22:28'),
(63, 'loyalty_points_earn_rate', 0, '50', '2026-07-23 10:28:28', '2026-07-23 11:22:28'),
(64, 'loyalty_points_redeem_value', 0, '5.5', '2026-07-23 10:29:02', '2026-07-23 11:22:35'),
(65, 'loyalty_points_min_redeem', 0, '200', '2026-07-23 10:29:02', '2026-07-23 11:22:28'),
(66, 'open_pickup', 0, '1', '2026-07-28 07:30:34', '2026-07-28 07:37:39');

-- --------------------------------------------------------

--
-- Table structure for table `setting_translations`
--

CREATE TABLE `setting_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `setting_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_translations`
--

INSERT INTO `setting_translations` (`id`, `setting_id`, `locale`, `value`) VALUES
(1, 14, 'ar', 'متجر عام'),
(2, 15, 'ar', 'الرياض الشارع الاتحاد'),
(3, 16, 'ar', 'جملة : الشحن المجاني ( AR )'),
(4, 17, 'ar', '<p>نص : الشحن المجاني ( AR )&nbsp;</p>'),
(5, 18, 'ar', 'جملة : سياسة الاسترجاع ( AR )'),
(6, 19, 'ar', '<p>نص : سياسة الاسترجاع ( AR )&nbsp;</p>'),
(7, 20, 'ar', 'جملة : الدفع الامن ( AR ) :'),
(8, 21, 'ar', '<p>نص : الدفع الامن ( AR ) :</p>'),
(9, 14, 'en', 'General store'),
(10, 15, 'en', 'Riyadh, Al-Ittihad Street'),
(11, 7, 'ar', '<p>صيانة وصف عربي</p>'),
(12, 7, 'en', '<p>mentance eglish&nbsp;</p>'),
(13, 16, 'en', 'جملة : الشحن المجاني ( EN )'),
(14, 17, 'en', '<p>نص : الشحن المجاني ( EN ) :</p>'),
(15, 18, 'en', 'جملة : سياسة الاسترجاع ( EN )'),
(16, 19, 'en', '<p>نص : سياسة الاسترجاع ( EN ) :</p>'),
(17, 20, 'en', 'جملة : الدفع الامن ( EN ) :'),
(18, 21, 'en', '<p>نص : الدفع الامن ( EN ) :</p>'),
(19, 32, 'ar', '<p>عذرًا، المتجر مغلق حاليًا. ساعات العمل من 09:00 صباحًا حتى 11:00 مساءً<img alt=\"\" src=\"http://localhost:8000/userfiles/images/271708316_3109389046000476_8226129466311026694_n.jpg\" style=\"height:200px; width:200px\" /></p>'),
(20, 32, 'en', '<p>Sorry, the store is currently closed. Opening hours are from 9:00 AM to 11:00 PM.</p>');

-- --------------------------------------------------------

--
-- Table structure for table `slideshows`
--

CREATE TABLE `slideshows` (
  `id` bigint UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slideshows`
--

INSERT INTO `slideshows` (`id`, `is_active`, `image`, `created_at`, `updated_at`) VALUES
(12, 1, '1ed6a35c-73e8-457c-8a20-fe0dd589b1ed.webp', '2026-07-25 11:03:22', '2026-07-25 11:03:22'),
(13, 1, '40bc913c-9032-4836-9887-ad97d3748457.webp', '2026-07-25 11:03:48', '2026-07-25 11:03:48'),
(14, 1, 'a1bde6fa-2309-45a3-bbea-60ff641713e5.webp', '2026-07-25 11:04:04', '2026-07-25 11:04:04'),
(15, 1, '29946f3c-c7d1-489a-8d91-a15c94f7883f.webp', '2026-07-25 11:11:48', '2026-07-25 11:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `socialmedialinks`
--

CREATE TABLE `socialmedialinks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `socialmedialinks`
--

INSERT INTO `socialmedialinks` (`id`, `name`, `link`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'facebook', 'https://www.facebook.com/', 'a0345ca0-f8e4-42d7-a1d3-c642b681aff8.webp', '2025-12-18 09:06:37', '2025-12-18 09:11:47'),
(2, 'instagram', 'https://www.instagram.com/', '15adb525-0a3b-4fbe-97c8-ca3aef43c0ac.webp', '2025-12-18 09:12:53', '2025-12-18 09:12:53'),
(3, 'twitter', 'https://x.com/', 'a203552b-fb63-4d59-9000-6dccee332837.webp', '2025-12-18 09:13:40', '2025-12-18 09:13:40');

-- --------------------------------------------------------

--
-- Table structure for table `textpages`
--

CREATE TABLE `textpages` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `textpages`
--

INSERT INTO `textpages` (`id`, `created_at`, `updated_at`) VALUES
(1, '2025-12-18 11:02:33', '2025-12-18 11:02:33'),
(2, '2025-12-18 11:03:13', '2025-12-18 11:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `textpage_translations`
--

CREATE TABLE `textpage_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `textpage_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `textpage_translations`
--

INSERT INTO `textpage_translations` (`id`, `textpage_id`, `locale`, `name`, `slug`, `details`) VALUES
(1, 1, 'ar', 'عن التطبيق', 'عن-التطبيق', '<p>عن التطيق عربي<img alt=\"\" src=\"http://localhost:8000/userfiles/images/271708316_3109389046000476_8226129466311026694_n.jpg\" style=\"height:50px; width:50px\" /></p>'),
(2, 1, 'en', 'about app', 'about-app', '<p>about us english</p>'),
(3, 2, 'ar', 'الشروط و الحكام', 'الشروط-و-الحكام', '<p><img alt=\"\" src=\"http://localhost:8000/userfiles/images/271708316_3109389046000476_8226129466311026694_n.jpg\" style=\"height:200px; width:200px\" />الشروط و الاحكام عربي</p>'),
(4, 2, 'en', 'Terms and Conditions', 'terms-and-conditions', '<p>Terms and Conditions english</p>');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2026-07-25 13:08:30', '2026-07-25 13:08:30', NULL),
(2, '2026-07-25 15:33:32', '2026-07-25 15:33:32', NULL),
(3, '2026-07-25 15:33:59', '2026-07-25 15:33:59', NULL),
(4, '2026-07-25 15:34:37', '2026-07-25 15:34:37', NULL),
(5, '2026-07-25 15:35:12', '2026-07-25 15:35:12', NULL),
(6, '2026-07-26 11:26:25', '2026-07-26 11:26:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `unit_translations`
--

CREATE TABLE `unit_translations` (
  `id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `unit_translations`
--

INSERT INTO `unit_translations` (`id`, `unit_id`, `locale`, `name`) VALUES
(1, 1, 'ar', 'عبوة'),
(2, 1, 'en', 'refill'),
(3, 2, 'ar', 'كيلو'),
(4, 2, 'en', 'kilo'),
(5, 3, 'ar', 'جرام'),
(6, 3, 'en', 'gram'),
(7, 4, 'ar', 'لتر'),
(8, 4, 'en', 'liter'),
(9, 5, 'ar', 'قطعة'),
(10, 5, 'en', 'piece'),
(11, 6, 'ar', 'حبة'),
(12, 6, 'en', 'grain');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `branch_id` bigint UNSIGNED DEFAULT NULL,
  `loyalty_points_balance` int UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `device_token` longtext COLLATE utf8mb4_unicode_ci,
  `desktop_token` longtext COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `branch_id`, `loyalty_points_balance`, `name`, `mobile`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `image`, `is_active`, `device_token`, `desktop_token`, `deleted_at`) VALUES
(2, NULL, 0, 'مدير التطبيق', '123456789', 'admin@admin.com', '2025-12-15 13:37:52', '$2y$12$IycpA2GwF12oQwXMZo0OUOW3SuvVHfZoHuHClsr2qTLnlST/gVnH2', NULL, '2025-12-15 13:37:52', '2026-01-13 09:26:04', '3c6a19a1-2a69-46bc-b1f9-1a52f5aeb887.webp', 1, NULL, NULL, NULL),
(32, NULL, 0, 'حسام ابو حصيرة', '2588522580', NULL, NULL, '$2y$12$xtvTCGXpegMnvG8o88o8oeQcDj4BKenGm3bq0EHtFGzE9pl1wDSK2', NULL, '2026-07-22 15:51:17', '2026-07-27 10:24:46', '578a4199-9974-46a9-aae2-64cc7bbfb776.webp', 1, '2222', NULL, NULL),
(33, NULL, 0, 'امير محمد حسنين', '112233445566', NULL, NULL, NULL, NULL, '2026-07-27 10:10:27', '2026-07-28 11:25:25', 'ee90fdf7-1040-4975-8bb3-314aa1e61ac5.webp', 1, '2222', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(19, 32, 0.00, '2026-07-22 15:51:17', '2026-07-22 15:51:17', NULL),
(21, 33, 0.00, '2026-07-27 10:10:27', '2026-07-27 10:10:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `wallet_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('credit','debit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wish_lists`
--

CREATE TABLE `wish_lists` (
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wish_lists`
--

INSERT INTO `wish_lists` (`user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(33, 73, '2026-07-28 11:56:43', '2026-07-28 11:56:43'),
(33, 92, '2026-07-28 11:55:26', '2026-07-28 11:55:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_is_default_index` (`user_id`,`is_default`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_notifications_order_id_foreign` (`order_id`);

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attribute_translations`
--
ALTER TABLE `attribute_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attribute_translations_attribute_id_locale_unique` (`attribute_id`,`locale`);

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_values_attribute_id_foreign` (`attribute_id`);

--
-- Indexes for table `attribute_value_product`
--
ALTER TABLE `attribute_value_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attribute_value_product_product_id_attribute_value_id_unique` (`product_id`,`attribute_value_id`),
  ADD KEY `attribute_value_product_attribute_value_id_foreign` (`attribute_value_id`);

--
-- Indexes for table `attribute_value_translations`
--
ALTER TABLE `attribute_value_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attribute_value_translations_attribute_value_id_locale_unique` (`attribute_value_id`,`locale`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch_product`
--
ALTER TABLE `branch_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_product_branch_id_product_id_unique` (`branch_id`,`product_id`),
  ADD KEY `branch_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `branch_translations`
--
ALTER TABLE `branch_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_translations_branch_id_locale_unique` (`branch_id`,`locale`),
  ADD KEY `branch_translations_locale_index` (`locale`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand_translations`
--
ALTER TABLE `brand_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brand_translations_brand_id_locale_unique` (`brand_id`,`locale`),
  ADD UNIQUE KEY `brand_translations_slug_unique` (`slug`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `category_sliders`
--
ALTER TABLE `category_sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_sliders_category_id_foreign` (`category_id`);

--
-- Indexes for table `category_translations`
--
ALTER TABLE `category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_translations_category_id_locale_unique` (`category_id`,`locale`),
  ADD UNIQUE KEY `category_translations_slug_unique` (`slug`);

--
-- Indexes for table `contactusmessages`
--
ALTER TABLE `contactusmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_codes`
--
ALTER TABLE `discount_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `discount_codes_code_unique` (`code`);

--
-- Indexes for table `discount_code_usages`
--
ALTER TABLE `discount_code_usages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_code_usages_discount_code_id_foreign` (`discount_code_id`),
  ADD KEY `discount_code_usages_order_id_foreign` (`order_id`),
  ADD KEY `discount_code_usages_user_id_foreign` (`user_id`);

--
-- Indexes for table `driver_reviews`
--
ALTER TABLE `driver_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_reviews_order_id_client_id_unique` (`order_id`,`client_id`),
  ADD KEY `driver_reviews_client_id_foreign` (`client_id`),
  ADD KEY `driver_reviews_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `imagesstore`
--
ALTER TABLE `imagesstore`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imagesstore_imageable_type_imageable_id_index` (`imageable_type`,`imageable_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loyalty_point_transactions`
--
ALTER TABLE `loyalty_point_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loyalty_point_transactions_user_id_foreign` (`user_id`),
  ADD KEY `loyalty_point_transactions_order_id_foreign` (`order_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `onboardings`
--
ALTER TABLE `onboardings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `onboarding_translations`
--
ALTER TABLE `onboarding_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `onboarding_translations_onboarding_id_locale_unique` (`onboarding_id`,`locale`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_status_id_foreign` (`status_id`),
  ADD KEY `orders_coupon_id_foreign` (`coupon_id`),
  ADD KEY `orders_cancel_reason_id_foreign` (`cancel_reason_id`),
  ADD KEY `orders_user_id_status_id_index` (`user_id`,`status_id`),
  ADD KEY `orders_driver_id_foreign` (`driver_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`),
  ADD KEY `orders_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `orderstatuses`
--
ALTER TABLE `orderstatuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orderstatus_translations`
--
ALTER TABLE `orderstatus_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orderstatus_translations_orderstatus_id_locale_unique` (`orderstatus_id`,`locale`);

--
-- Indexes for table `order_cancel_reasons`
--
ALTER TABLE `order_cancel_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_cancel_reason_translations`
--
ALTER TABLE `order_cancel_reason_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ocr_reason_locale_unique` (`order_cancel_reason_id`,`locale`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `order_item_attribute_values`
--
ALTER TABLE `order_item_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_attribute_values_order_item_id_foreign` (`order_item_id`),
  ADD KEY `order_item_attribute_values_attribute_id_foreign` (`attribute_id`),
  ADD KEY `order_item_attribute_values_attribute_value_id_foreign` (`attribute_value_id`);

--
-- Indexes for table `order_reviews`
--
ALTER TABLE `order_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_reviews_client_id_foreign` (`client_id`),
  ADD KEY `order_reviews_order_id_foreign` (`order_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_codes_verifiable_type_verifiable_id_index` (`verifiable_type`,`verifiable_id`),
  ADD KEY `otp_codes_lookup_index` (`verifiable_id`,`verifiable_type`,`purpose`,`is_used`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_methods_code_unique` (`code`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_index` (`category_id`),
  ADD KEY `products_brand_id_index` (`brand_id`),
  ADD KEY `products_is_active_index` (`is_active`),
  ADD KEY `products_new_arrivals_index` (`new_arrivals`),
  ADD KEY `products_sku_index` (`sku`),
  ADD KEY `products_unit_id_foreign` (`unit_id`);

--
-- Indexes for table `product_translations`
--
ALTER TABLE `product_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_translations_product_id_locale_unique` (`product_id`,`locale`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_variants_sku_unique` (`sku`),
  ADD UNIQUE KEY `pv_product_combo_unique` (`product_id`,`combination_key`);

--
-- Indexes for table `product_variant_attribute_value`
--
ALTER TABLE `product_variant_attribute_value`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pv_av_unique` (`product_variant_id`,`attribute_value_id`),
  ADD KEY `product_variant_attribute_value_attribute_value_id_foreign` (`attribute_value_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_translations_setting_id_locale_unique` (`setting_id`,`locale`);

--
-- Indexes for table `slideshows`
--
ALTER TABLE `slideshows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socialmedialinks`
--
ALTER TABLE `socialmedialinks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `textpages`
--
ALTER TABLE `textpages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `textpage_translations`
--
ALTER TABLE `textpage_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `textpage_translations_textpage_id_locale_unique` (`textpage_id`,`locale`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unit_translations`
--
ALTER TABLE `unit_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unit_translations_unit_id_locale_unique` (`unit_id`,`locale`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mobile` (`mobile`),
  ADD KEY `deleted_at` (`deleted_at`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `users_branch_id_foreign` (`branch_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_user_id_foreign` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_transactions_wallet_id_foreign` (`wallet_id`),
  ADD KEY `wallet_transactions_order_id_foreign` (`order_id`);

--
-- Indexes for table `wish_lists`
--
ALTER TABLE `wish_lists`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `wish_lists_product_id_foreign` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attribute_translations`
--
ALTER TABLE `attribute_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `attribute_value_product`
--
ALTER TABLE `attribute_value_product`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `attribute_value_translations`
--
ALTER TABLE `attribute_value_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branch_product`
--
ALTER TABLE `branch_product`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_translations`
--
ALTER TABLE `branch_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `brand_translations`
--
ALTER TABLE `brand_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `category_sliders`
--
ALTER TABLE `category_sliders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `category_translations`
--
ALTER TABLE `category_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `contactusmessages`
--
ALTER TABLE `contactusmessages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `discount_codes`
--
ALTER TABLE `discount_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discount_code_usages`
--
ALTER TABLE `discount_code_usages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `driver_reviews`
--
ALTER TABLE `driver_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imagesstore`
--
ALTER TABLE `imagesstore`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_point_transactions`
--
ALTER TABLE `loyalty_point_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `onboardings`
--
ALTER TABLE `onboardings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `onboarding_translations`
--
ALTER TABLE `onboarding_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orderstatuses`
--
ALTER TABLE `orderstatuses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orderstatus_translations`
--
ALTER TABLE `orderstatus_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_cancel_reasons`
--
ALTER TABLE `order_cancel_reasons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_cancel_reason_translations`
--
ALTER TABLE `order_cancel_reason_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `order_item_attribute_values`
--
ALTER TABLE `order_item_attribute_values`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_reviews`
--
ALTER TABLE `order_reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `product_translations`
--
ALTER TABLE `product_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `product_variant_attribute_value`
--
ALTER TABLE `product_variant_attribute_value`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `setting_translations`
--
ALTER TABLE `setting_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `slideshows`
--
ALTER TABLE `slideshows`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `socialmedialinks`
--
ALTER TABLE `socialmedialinks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `textpages`
--
ALTER TABLE `textpages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `textpage_translations`
--
ALTER TABLE `textpage_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `unit_translations`
--
ALTER TABLE `unit_translations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `admin_notifications_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attribute_translations`
--
ALTER TABLE `attribute_translations`
  ADD CONSTRAINT `attribute_translations_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD CONSTRAINT `attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_value_product`
--
ALTER TABLE `attribute_value_product`
  ADD CONSTRAINT `attribute_value_product_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attribute_value_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_value_translations`
--
ALTER TABLE `attribute_value_translations`
  ADD CONSTRAINT `attribute_value_translations_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_product`
--
ALTER TABLE `branch_product`
  ADD CONSTRAINT `branch_product_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `branch_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branch_translations`
--
ALTER TABLE `branch_translations`
  ADD CONSTRAINT `branch_translations_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `brand_translations`
--
ALTER TABLE `brand_translations`
  ADD CONSTRAINT `brand_translations_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_sliders`
--
ALTER TABLE `category_sliders`
  ADD CONSTRAINT `category_sliders_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_translations`
--
ALTER TABLE `category_translations`
  ADD CONSTRAINT `category_translations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_code_usages`
--
ALTER TABLE `discount_code_usages`
  ADD CONSTRAINT `discount_code_usages_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discount_code_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `discount_code_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_reviews`
--
ALTER TABLE `driver_reviews`
  ADD CONSTRAINT `driver_reviews_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_reviews_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_point_transactions`
--
ALTER TABLE `loyalty_point_transactions`
  ADD CONSTRAINT `loyalty_point_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loyalty_point_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onboarding_translations`
--
ALTER TABLE `onboarding_translations`
  ADD CONSTRAINT `onboarding_translations_onboarding_id_foreign` FOREIGN KEY (`onboarding_id`) REFERENCES `onboardings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_cancel_reason_id_foreign` FOREIGN KEY (`cancel_reason_id`) REFERENCES `order_cancel_reasons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `discount_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `orderstatuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orderstatus_translations`
--
ALTER TABLE `orderstatus_translations`
  ADD CONSTRAINT `orderstatus_translations_orderstatus_id_foreign` FOREIGN KEY (`orderstatus_id`) REFERENCES `orderstatuses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_cancel_reason_translations`
--
ALTER TABLE `order_cancel_reason_translations`
  ADD CONSTRAINT `order_cancel_reason_translations_order_cancel_reason_id_foreign` FOREIGN KEY (`order_cancel_reason_id`) REFERENCES `order_cancel_reasons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_item_attribute_values`
--
ALTER TABLE `order_item_attribute_values`
  ADD CONSTRAINT `order_item_attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_attribute_values_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_attribute_values_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_reviews`
--
ALTER TABLE `order_reviews`
  ADD CONSTRAINT `order_reviews_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_translations`
--
ALTER TABLE `product_translations`
  ADD CONSTRAINT `product_translations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variant_attribute_value`
--
ALTER TABLE `product_variant_attribute_value`
  ADD CONSTRAINT `product_variant_attribute_value_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variant_attribute_value_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_translations`
--
ALTER TABLE `setting_translations`
  ADD CONSTRAINT `setting_translations_setting_id_foreign` FOREIGN KEY (`setting_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `textpage_translations`
--
ALTER TABLE `textpage_translations`
  ADD CONSTRAINT `textpage_translations_textpage_id_foreign` FOREIGN KEY (`textpage_id`) REFERENCES `textpages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `unit_translations`
--
ALTER TABLE `unit_translations`
  ADD CONSTRAINT `unit_translations_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `wallet_transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`);

--
-- Constraints for table `wish_lists`
--
ALTER TABLE `wish_lists`
  ADD CONSTRAINT `wish_lists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wish_lists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
