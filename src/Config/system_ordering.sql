-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 04, 2025 at 03:34 AM
-- Server version: 8.0.30
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `system_ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `spv_id` int NOT NULL,
  `approval_status` enum('waiting','approve','reject') NOT NULL,
  `comments` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`id`, `order_id`, `spv_id`, `approval_status`, `comments`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 'approve', 'Mohon Bantuannya', '2025-11-21 05:30:07', '2025-11-21 05:30:32'),
(2, 2, 8, 'approve', '', '2025-11-21 06:14:46', '2025-11-21 06:17:21'),
(3, 3, 8, 'approve', '', '2025-11-21 06:28:50', '2025-11-21 06:29:04'),
(4, 4, 8, 'approve', '', '2025-11-21 06:49:39', '2025-11-21 06:49:48'),
(5, 5, 8, 'approve', '', '2025-11-21 07:10:16', '2025-11-21 07:12:38'),
(6, 6, 8, 'approve', '', '2025-12-02 15:31:01', '2025-12-03 00:43:07');

-- --------------------------------------------------------

--
-- Table structure for table `consum_cart`
--

CREATE TABLE `consum_cart` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_type_id` int NOT NULL,
  `product_item_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `note` text,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consum_order`
--

CREATE TABLE `consum_order` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_type_id` int NOT NULL,
  `product_item_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `note` text,
  `order_type` enum('direct','cart') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `npk` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `plant_id` int NOT NULL,
  `department_id` int NOT NULL,
  `line` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `npk`, `phone`, `plant_id`, `department_id`, `line`, `created_at`, `updated_at`) VALUES
(1, 'Zahrah Annisa Ramadhani', '444444', '085697612586', 2, 3, 'Manufacture Engineering', '2025-10-19 04:25:45', '2025-10-19 04:25:45'),
(2, 'Regita Rizkie Fannisyah', '111111', '081574817104', 2, 1, 'K-line 3', '2025-10-19 06:37:47', '2025-10-19 06:37:47'),
(4, 'Customer Satu', '222222', '123456789011', 2, 3, 'K-Line 2', '2025-10-22 07:37:57', '2025-10-22 07:37:57'),
(6, 'Zanni', '5555555', '12345', 2, 1, 'K-Line 2', '2025-10-27 03:39:16', '2025-10-27 03:39:16');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'Produksi'),
(2, 'Quality'),
(3, 'Maintenance'),
(4, 'PCL'),
(5, 'Export-Import'),
(6, 'PE');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `item_type` enum('work_order','consumable') NOT NULL,
  `quantity` int NOT NULL,
  `note` text,
  `needed_date` date NOT NULL,
  `is_emergency` tinyint(1) NOT NULL DEFAULT '0',
  `emergency_type` enum('safety','line_stop') DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `category` enum('sparepart','improvement','project','regular') DEFAULT NULL,
  `pic_mfg` varchar(100) DEFAULT NULL,
  `production_status` enum('pending','on_progress','finish','completed') NOT NULL DEFAULT 'pending',
  `material` enum('ordered','ready') DEFAULT NULL,
  `material_type` varchar(255) NOT NULL COMMENT 'wajib diisi',
  `file_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `customer_id`, `order_id`, `item_type`, `quantity`, `note`, `needed_date`, `is_emergency`, `emergency_type`, `product_id`, `item_name`, `category`, `pic_mfg`, `production_status`, `material`, `material_type`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'work_order', 18, 'Catatan', '2025-11-30', 0, NULL, NULL, 'PART', 'regular', 'Zanni', 'completed', 'ready', 'stainless steel', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_691ff8d8b2de47.38758967.pdf\"]', '2025-11-21 05:30:00', '2025-11-21 05:32:53'),
(2, 1, 2, 'work_order', 12, 'Contoh', '2025-11-28', 1, 'line_stop', NULL, 'SLIPER', 'improvement', '', 'on_progress', 'ready', 'Bolt M6', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_6920035003ac32.01717194.pdf\"]', '2025-11-21 06:14:40', '2025-11-21 06:18:01'),
(3, 1, NULL, 'work_order', 18, 'Catatan', '2025-11-30', 1, 'safety', NULL, 'PART', 'regular', NULL, 'pending', 'ready', 'stainless steel', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_691ff8d8b2de47.38758967.pdf\"]', '2025-11-21 06:25:21', '2025-11-21 06:25:21'),
(4, 1, 6, 'work_order', 18, 'Catatan', '2025-11-30', 1, 'safety', NULL, 'PART', 'regular', NULL, 'pending', 'ready', 'stainless steel', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_691ff8d8b2de47.38758967.pdf\"]', '2025-11-21 06:25:24', '2025-12-02 15:31:01'),
(5, 1, 3, 'work_order', 18, 'Catatan', '2025-11-30', 1, 'safety', NULL, 'PART', 'regular', NULL, 'pending', 'ready', 'stainless steel', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_691ff8d8b2de47.38758967.pdf\"]', '2025-11-21 06:28:33', '2025-11-21 06:28:50'),
(6, 1, 4, 'work_order', 18, 'Catatan', '2025-11-30', 0, NULL, NULL, 'PART', 'improvement', '', 'pending', 'ready', 'stainless steel', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_691ff8d8b2de47.38758967.pdf\"]', '2025-11-21 06:49:16', '2025-11-24 03:57:56'),
(7, 1, 5, 'work_order', 2, 'Catatan', '2025-11-23', 1, 'line_stop', NULL, 'PAD 1', 'regular', 'Zanni', 'completed', 'ordered', 'Bolt M6', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_692010157b4ee7.39063466.pdf\"]', '2025-11-21 07:09:09', '2025-11-21 07:18:51'),
(8, 1, NULL, 'work_order', 1, 'dxfcgvhjk', '2025-12-12', 0, NULL, NULL, 'PAD 1', 'improvement', NULL, 'pending', 'ordered', 'stainless steel', '[\"\\/system_ordering\\/public\\/uploads\\/drawings\\/drawing_692f061cd039c9.48140176.pdf\"]', '2025-12-02 15:30:36', '2025-12-02 15:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `icon` varchar(50) DEFAULT 'fas fa-info-circle',
  `color` varchar(20) DEFAULT 'primary'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `customer_id`, `user_id`, `type`, `message`, `is_read`, `created_at`, `icon`, `color`) VALUES
(1, 1, NULL, 'work_order', 'Item \'PART\' berhasil ditambahkan ke Work Order', 0, '2025-11-21 06:28:33', 'fas fa-clipboard-list', 'success'),
(2, 1, NULL, 'work_order', 'Item \'PART\' berhasil ditambahkan ke Work Order', 0, '2025-11-21 06:49:16', 'fas fa-clipboard-list', 'success'),
(3, 1, NULL, 'work_order', 'Item \'PAD 1\' berhasil ditambahkan ke Work Order', 0, '2025-11-21 07:09:09', 'fas fa-clipboard-list', 'success'),
(4, 1, NULL, 'work_order', 'Item \'PAD 1\' berhasil ditambahkan ke Work Order', 0, '2025-12-02 15:30:36', 'fas fa-clipboard-list', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_code` varchar(30) DEFAULT NULL,
  `customer_id` int NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `plant_id` int DEFAULT NULL,
  `repeat_source_order_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `approval_status` enum('waiting','approve','reject') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `customer_id`, `department`, `plant_id`, `repeat_source_order_id`, `created_at`, `updated_at`, `approval_status`) VALUES
(1, NULL, 1, NULL, 2, NULL, '2025-11-21 05:30:07', '2025-11-21 05:30:32', 'approve'),
(2, NULL, 1, NULL, 2, NULL, '2025-11-21 06:14:46', '2025-11-21 06:17:21', 'approve'),
(3, NULL, 1, NULL, 2, NULL, '2025-11-21 06:28:50', '2025-11-21 06:29:04', 'approve'),
(4, NULL, 1, NULL, 2, NULL, '2025-11-21 06:49:39', '2025-11-21 06:49:48', 'approve'),
(5, NULL, 1, NULL, 2, NULL, '2025-11-21 07:10:16', '2025-11-21 07:12:38', 'approve'),
(6, NULL, 1, NULL, 2, NULL, '2025-12-02 15:31:01', '2025-12-03 00:43:07', 'approve');

-- --------------------------------------------------------

--
-- Table structure for table `order_histories`
--

CREATE TABLE `order_histories` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `item_id` int NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `vendor_price` decimal(15,2) NOT NULL,
  `cost_benefit` decimal(15,2) NOT NULL,
  `completed_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

CREATE TABLE `plants` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `plants`
--

INSERT INTO `plants` (`id`, `name`) VALUES
(1, 'Plant 1'),
(2, 'Plant 2'),
(3, 'Plant 3'),
(4, 'Plant 4'),
(5, 'Plant 5');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `product_code` varchar(255) DEFAULT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `price` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_items`
--

CREATE TABLE `product_items` (
  `id` int NOT NULL,
  `product_type_id` int NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stock` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_types`
--

CREATE TABLE `product_types` (
  `id` int NOT NULL,
  `section_id` int NOT NULL,
  `product_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_types`
--

INSERT INTO `product_types` (`id`, `section_id`, `product_code`, `name`, `price`, `description`, `image_path`, `file_path`, `created_at`, `updated_at`) VALUES
(7, 1, 'PRESU-3512-CYH', 'CYH', '0.00', 'xdtcfgvhbjn', '/uploads/consum-product-type/gear.jpeg', '/uploads/consum-product-type/Sistem_Microprocessor.jpg', '2025-12-02 04:08:21', '2025-12-02 04:08:21'),
(9, 1, 'PRESU-1774-CYB', 'CYB', NULL, 'cfgvbhjnk', '/uploads/consum-product-type/Sistem_Microprocessor.jpg', '/uploads/consum-product-type/gear.jpeg', '2025-12-02 06:03:36', '2025-12-02 06:03:36'),
(10, 1, 'PRESU-9699-CRSH', 'CRSH', '100000.00', 'SRXDTCFYVGUBHINJ', '/uploads/consum-product-type/gear.jpeg', '/uploads/consum-product-type/Sistem_Microprocessor.jpg', '2025-12-02 07:37:23', '2025-12-02 07:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Presub', 'Klik untuk melihat semua produk dalam section ini.', '2025-11-17 10:23:54', '2025-12-01 11:29:32'),
(2, 'K-Line 3', 'Klik untuk melihat semua produk dalam section ini.', '2025-11-17 10:23:54', '2025-12-01 11:29:23'),
(3, 'K-LINE 4 SPS', 'Klik untuk melihat semua produk dalam section ini.', '2025-11-17 10:23:54', '2025-12-01 11:29:15'),
(4, 'K-Line 5', 'Klik untuk melihat semua produk dalam section ini.\r\n', '2025-11-17 10:23:54', '2025-12-01 11:29:01'),
(23, 'Delivery', NULL, '2025-12-02 08:34:58', '2025-12-02 08:35:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `npk` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','spv') NOT NULL,
  `department_id` int DEFAULT NULL,
  `plant_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `npk`, `phone`, `email`, `password`, `role`, `department_id`, `plant_id`, `created_at`, `updated_at`) VALUES
(7, 'AdminME', '111111', '08123456789', 'adminsatu@email.com', '$2y$10$s3vbpPoce6Iz0qvHvcQtfeLmyIEdXPIa/Vp1rpclXA.xqr.SM7stS', 'admin', NULL, NULL, '2025-11-21 01:49:55', '2025-11-21 01:49:55'),
(8, 'SupervisorMaintenance', '999999', '08987654321', 'supervisormtn@email.com', '$2y$10$VAsQFv.DmSdo3dcwEr0uNuHExplW2zhKKqit593irw.pw4BiyR.ru', 'spv', 3, 2, '2025-11-21 01:51:51', '2025-11-21 01:51:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `spv_id` (`spv_id`);

--
-- Indexes for table `consum_cart`
--
ALTER TABLE `consum_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_type_id` (`product_type_id`),
  ADD KEY `product_item_id` (`product_item_id`);

--
-- Indexes for table `consum_order`
--
ALTER TABLE `consum_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_type_id` (`product_type_id`),
  ADD KEY `product_item_id` (`product_item_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npk` (`npk`),
  ADD KEY `plant_id` (`plant_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `repeat_source_order_id` (`repeat_source_order_id`);

--
-- Indexes for table `order_histories`
--
ALTER TABLE `order_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `plants`
--
ALTER TABLE `plants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD UNIQUE KEY `product_code_2` (`product_code`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `product_items`
--
ALTER TABLE `product_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_code` (`item_code`),
  ADD KEY `product_type_id` (`product_type_id`);

--
-- Indexes for table `product_types`
--
ALTER TABLE `product_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npk` (`npk`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `plant_id` (`plant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `consum_cart`
--
ALTER TABLE `consum_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consum_order`
--
ALTER TABLE `consum_order`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_histories`
--
ALTER TABLE `order_histories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plants`
--
ALTER TABLE `plants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_items`
--
ALTER TABLE `product_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`spv_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consum_cart`
--
ALTER TABLE `consum_cart`
  ADD CONSTRAINT `consum_cart_ibfk_1` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`),
  ADD CONSTRAINT `consum_cart_ibfk_2` FOREIGN KEY (`product_item_id`) REFERENCES `product_items` (`id`);

--
-- Constraints for table `consum_order`
--
ALTER TABLE `consum_order`
  ADD CONSTRAINT `consum_order_ibfk_1` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`),
  ADD CONSTRAINT `consum_order_ibfk_2` FOREIGN KEY (`product_item_id`) REFERENCES `product_items` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`repeat_source_order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_histories`
--
ALTER TABLE `order_histories`
  ADD CONSTRAINT `order_histories_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_histories_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_items`
--
ALTER TABLE `product_items`
  ADD CONSTRAINT `product_items_ibfk_1` FOREIGN KEY (`product_type_id`) REFERENCES `product_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_types`
--
ALTER TABLE `product_types`
  ADD CONSTRAINT `product_types_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
