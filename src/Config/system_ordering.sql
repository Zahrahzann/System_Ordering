-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 05, 2026 at 03:34 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_item_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumable_reports`
--

CREATE TABLE `consumable_reports` (
  `id` int NOT NULL,
  `section_id` int NOT NULL,
  `product_type_id` int NOT NULL,
  `product_item_id` int NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `qty` int NOT NULL,
  `inhouse_price` decimal(12,2) DEFAULT '0.00',
  `maker_price` decimal(12,2) DEFAULT '0.00',
  `benefit` decimal(12,2) GENERATED ALWAYS AS ((`inhouse_price` - `maker_price`)) STORED,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consum_cart`
--

CREATE TABLE `consum_cart` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_type_id` int NOT NULL,
  `product_item_id` int DEFAULT NULL,
  `section_id` int NOT NULL,
  `quantity` int NOT NULL,
  `note` text,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consum_orders`
--

CREATE TABLE `consum_orders` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `product_type_id` int NOT NULL,
  `product_item_id` int NOT NULL,
  `section_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `order_code` varchar(100) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
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
(12, 'Zahrah Annisa Ramadhani', '444444', '085697612586', 2, 3, 'Manufacture Engineering', '2026-01-19 08:49:23', '2026-01-19 08:49:23');

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
(5, 'Export-Import');

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
  `estimasi_pengerjaan` varchar(100) DEFAULT NULL,
  `note` text,
  `needed_date` date NOT NULL,
  `is_emergency` tinyint(1) NOT NULL DEFAULT '0',
  `emergency_type` enum('safety','line_stop') DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `category` enum('sparepart','improvement','project','regular') DEFAULT NULL,
  `pic_mfg` varchar(100) DEFAULT NULL,
  `production_status` enum('pending','on_progress','finish','completed') NOT NULL DEFAULT 'pending',
  `actual_duration_minutes` int DEFAULT NULL,
  `material_status` enum('ordered','ready') NOT NULL,
  `material_dimension_id` int NOT NULL,
  `file_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machine_rates`
--

CREATE TABLE `machine_rates` (
  `id` int NOT NULL,
  `process_name` varchar(100) NOT NULL,
  `price_per_minute` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `machine_rates`
--

INSERT INTO `machine_rates` (`id`, `process_name`, `price_per_minute`, `created_at`) VALUES
(1, 'Study dan Admin', '0.00', '2026-01-06 06:12:24'),
(2, 'Cutting Saw', '500.00', '2026-01-06 06:12:24'),
(3, 'Cutting Plasma', '3000.00', '2026-01-06 06:12:24'),
(4, 'Cutting Wheel', '500.00', '2026-01-06 06:12:24'),
(5, 'Milling I', '905.00', '2026-01-06 06:12:24'),
(6, 'Milling II', '905.00', '2026-01-06 06:12:24'),
(7, 'Milling III', '905.00', '2026-01-06 06:12:24'),
(8, 'Milling IV', '905.00', '2026-01-06 06:12:24'),
(9, 'Milling V', '905.00', '2026-01-06 06:12:24'),
(10, 'Turning Feller', '1436.00', '2026-01-06 06:12:24'),
(11, 'Konvensional', '1436.00', '2026-01-06 06:12:24'),
(12, 'Hardening', '2703.00', '2026-01-06 06:12:24'),
(13, 'EDM', '925.00', '2026-01-06 06:12:24'),
(14, 'Welding', '500.00', '2026-01-06 06:12:24'),
(15, 'Assembling', '0.00', '2026-01-06 06:12:24'),
(16, 'Others', '0.00', '2026-01-06 06:12:24'),
(17, 'Finishing', '0.00', '2026-01-06 06:12:24');

-- --------------------------------------------------------

--
-- Table structure for table `manpower_rates`
--

CREATE TABLE `manpower_rates` (
  `id` int NOT NULL,
  `process_name` varchar(100) NOT NULL,
  `price_per_minute` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `manpower_rates`
--

INSERT INTO `manpower_rates` (`id`, `process_name`, `price_per_minute`, `created_at`) VALUES
(1, 'Study & Admin', '1819.00', '2026-01-06 06:15:30'),
(2, 'Cutting Saw', '1819.00', '2026-01-06 06:15:30'),
(3, 'Cutting Plasma', '1819.00', '2026-01-06 06:15:30'),
(4, 'Cutting Wheel', '1819.00', '2026-01-06 06:15:30'),
(5, 'Milling I', '1819.00', '2026-01-06 06:15:30'),
(6, 'Milling II', '1819.00', '2026-01-06 06:15:30'),
(7, 'Milling III', '1819.00', '2026-01-06 06:15:30'),
(8, 'Milling IV', '1819.00', '2026-01-06 06:15:30'),
(9, 'Milling V', '1819.00', '2026-01-06 06:15:30'),
(10, 'Turning Feller', '1819.00', '2026-01-06 06:15:30'),
(11, 'Konvensional', '1819.00', '2026-01-06 06:15:30'),
(12, 'Hardening', '1819.00', '2026-01-06 06:15:30'),
(13, 'EDM', '1819.00', '2026-01-06 06:15:30'),
(14, 'Welding', '1819.00', '2026-01-06 06:15:30'),
(15, 'Assembling', '1819.00', '2026-01-06 06:15:30'),
(16, 'Others', '1819.00', '2026-01-06 06:15:30'),
(17, 'Finishing', '1819.00', '2026-01-06 06:15:30');

-- --------------------------------------------------------

--
-- Table structure for table `material_dimensions`
--

CREATE TABLE `material_dimensions` (
  `id` int NOT NULL,
  `material_type_id` int NOT NULL,
  `dimension` varchar(50) NOT NULL,
  `stock` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `minimum_stock` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_stock_logs`
--

CREATE TABLE `material_stock_logs` (
  `id` int NOT NULL,
  `material_dimension_id` int NOT NULL,
  `change_type` enum('IN','OUT') NOT NULL,
  `quantity` float NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_types`
--

CREATE TABLE `material_types` (
  `id` int NOT NULL,
  `material_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `material_types`
--

INSERT INTO `material_types` (`id`, `material_number`, `name`, `created_at`, `updated_at`) VALUES
(7, '900045321', 'STEEL', '2026-01-15 04:06:42', '2026-01-15 04:06:42'),
(8, '900012265', 'URETHANE					 					', '2026-01-15 04:07:09', '2026-01-15 04:07:09'),
(9, '900076545', 'POM', '2026-01-15 04:07:47', '2026-01-15 04:07:47'),
(10, '900087788', 'NC BLUE					 					', '2026-01-15 04:08:09', '2026-01-26 02:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `order_id` int DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `icon` varchar(50) DEFAULT 'fas fa-info-circle',
  `color` varchar(20) DEFAULT 'primary',
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `stock` int DEFAULT '0',
  `section_id` int DEFAULT NULL,
  `maker_price` decimal(12,2) DEFAULT NULL
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

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `review` text,
  `status` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `workorder_costs`
--

CREATE TABLE `workorder_costs` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `qty` int NOT NULL,
  `cost_material` decimal(15,2) DEFAULT '0.00',
  `cost_machine_tool_electric` decimal(15,2) DEFAULT '0.00',
  `cost_manpower` decimal(15,2) DEFAULT '0.00',
  `overhead` decimal(15,2) DEFAULT '0.00',
  `cost_per_pcs` decimal(15,2) DEFAULT '0.00',
  `cost_inhouse_total` decimal(15,2) DEFAULT '0.00',
  `vendor_price_per_pcs` decimal(15,2) DEFAULT '0.00',
  `vendor_price_total` decimal(15,2) DEFAULT '0.00',
  `benefit` decimal(15,2) DEFAULT '0.00',
  `status` varchar(50) DEFAULT 'on_progress',
  `report_year` int DEFAULT NULL,
  `department_id` varchar(100) NOT NULL,
  `customer_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workorder_processes`
--

CREATE TABLE `workorder_processes` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `machine_process` varchar(255) DEFAULT NULL,
  `machine_time` int DEFAULT NULL,
  `machine_cost` decimal(15,2) DEFAULT NULL,
  `manpower_process` varchar(255) DEFAULT NULL,
  `manpower_time` int DEFAULT NULL,
  `manpower_cost` decimal(15,2) DEFAULT NULL,
  `material_cost` decimal(15,2) DEFAULT NULL,
  `vendor_price_per_pcs` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consumable_reports`
--
ALTER TABLE `consumable_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `consum_cart`
--
ALTER TABLE `consum_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart` (`customer_id`,`product_item_id`),
  ADD UNIQUE KEY `uniq_customer_item` (`customer_id`,`product_item_id`),
  ADD KEY `product_type_id` (`product_type_id`),
  ADD KEY `consum_cart_ibfk_2` (`product_item_id`);

--
-- Indexes for table `consum_orders`
--
ALTER TABLE `consum_orders`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_items_dimension` (`material_dimension_id`);

--
-- Indexes for table `machine_rates`
--
ALTER TABLE `machine_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manpower_rates`
--
ALTER TABLE `manpower_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_dimensions`
--
ALTER TABLE `material_dimensions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_dimensions_ibfk_1` (`material_type_id`);

--
-- Indexes for table `material_stock_logs`
--
ALTER TABLE `material_stock_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_dimension_id` (`material_dimension_id`);

--
-- Indexes for table `material_types`
--
ALTER TABLE `material_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_number` (`material_number`);

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`,`customer_id`);

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
-- Indexes for table `workorder_costs`
--
ALTER TABLE `workorder_costs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workorder_processes`
--
ALTER TABLE `workorder_processes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumable_reports`
--
ALTER TABLE `consumable_reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consum_cart`
--
ALTER TABLE `consum_cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consum_orders`
--
ALTER TABLE `consum_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `machine_rates`
--
ALTER TABLE `machine_rates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `manpower_rates`
--
ALTER TABLE `manpower_rates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `material_dimensions`
--
ALTER TABLE `material_dimensions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_stock_logs`
--
ALTER TABLE `material_stock_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_types`
--
ALTER TABLE `material_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workorder_costs`
--
ALTER TABLE `workorder_costs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workorder_processes`
--
ALTER TABLE `workorder_processes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `consum_cart_ibfk_2` FOREIGN KEY (`product_item_id`) REFERENCES `product_items` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_items_dimension` FOREIGN KEY (`material_dimension_id`) REFERENCES `material_dimensions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `items_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `material_dimensions`
--
ALTER TABLE `material_dimensions`
  ADD CONSTRAINT `material_dimensions_ibfk_1` FOREIGN KEY (`material_type_id`) REFERENCES `material_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `material_stock_logs`
--
ALTER TABLE `material_stock_logs`
  ADD CONSTRAINT `material_stock_logs_ibfk_1` FOREIGN KEY (`material_dimension_id`) REFERENCES `material_dimensions` (`id`);

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
