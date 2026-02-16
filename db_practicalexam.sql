-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 09:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_practicalexam`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_categories`
--

CREATE TABLE `tbl_categories` (
  `category_id` int(11) NOT NULL,
  `categoryName` varchar(250) DEFAULT NULL,
  `categoryDesc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_categories`
--

INSERT INTO `tbl_categories` (`category_id`, `categoryName`, `categoryDesc`) VALUES
(9, 'Men Casual Shoes', 'Comfortable casual shoes for everyday wear'),
(11, 'Men Everyday Shoes', 'The quick brown fox jump over the lazy dog.'),
(12, 'Men Fashion Shoes', 'Stylish men fashion shoes - Amethyst Collection'),
(13, 'Men Dress Shoes', 'Formal dress shoes for professional occasions'),
(14, 'Men Trainer Shoes', 'Trainer Shoes Usa siya ka sapatos'),
(15, 'Men Court Shoes', 'Pang Court siya nga sapatos.'),
(16, 'Men Sports Court Shoes', 'Pang Court siya nga sapatos.'),
(17, 'Women Shoes', 'Pang Girl nga Sapatos'),
(18, 'Men Basketball Shoes', 'Pang Court siya nga sapatos.'),
(19, 'Men Senior Shoes', 'Pang Tiguwang siya nga sapatos.'),
(20, 'Men Lifestyle Shoes', 'Pang Choy2 siya nga sapatos.'),
(21, 'Men Running Shoes', 'Pang Dagan siya nga sapatos nig gukdon kas iro.'),
(22, 'Men Athletic Shoes', 'Pang Dagan siya nga sapatos nig gukdon kas iro.'),
(23, 'Men Work Shoes', 'Kini nga sapatos dali ra magka hugaw'),
(29, 'Men\'s Shirt', 'Black Adidadis Shirt Small'),
(31, 'Women\'s Cap', 'Aron dili mainitan or mabugnawan ang bagol².'),
(32, 'Women\'s Shirt', 'Sinina');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customers`
--

CREATE TABLE `tbl_customers` (
  `customerID` int(11) NOT NULL,
  `fullname` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(250) NOT NULL,
  `bday` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customers`
--

INSERT INTO `tbl_customers` (`customerID`, `fullname`, `email`, `phone`, `address`, `bday`, `user_id`, `created_at`) VALUES
(1, 'Rejallejon', 'RMRUFIN@gmail.com', '09123123123', '', '2025-12-24', 8, '2025-12-14 11:23:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_orders`
--

CREATE TABLE `tbl_orders` (
  `order_id` int(11) NOT NULL,
  `customerID` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `added_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('active','purchased','removed') DEFAULT 'active',
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_orders`
--

INSERT INTO `tbl_orders` (`order_id`, `customerID`, `product_id`, `quantity`, `price`, `added_at`, `status`, `updated_at`) VALUES
(6, 1, 19, 1, 5000.00, '2025-12-14 11:59:32', 'active', '2025-12-14 11:59:32'),
(7, 1, 18, 1, 5000.00, '2025-12-14 12:00:58', 'removed', '2025-12-14 12:00:58'),
(8, 1, 13, 1, 10000.00, '2025-12-14 12:33:16', 'active', '2025-12-14 12:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE `tbl_products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(250) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`product_id`, `product_name`, `price`, `quantity`, `category_id`, `image_path`) VALUES
(9, 'Edon Edon', 5000.00, 123, 11, 'uploads/Buy campus.jpg'),
(12, 'Trainer shoes', 5000.00, 123, 14, 'uploads/Trainer shoes.jpg'),
(13, 'GSM Blue Shoes', 10000.00, 100, 15, 'uploads/Adidas Grand Court.jpg'),
(14, 'Run Falcon 5', 5000.00, 100, 16, 'uploads/Runfalcon 5.jpg'),
(17, '70s Running Shoes', 1000.00, 20, 19, 'uploads/70s running shoes.jpg'),
(18, 'Divisoria Shoes', 5000.00, 201, 20, 'uploads/mens samba.jpg'),
(19, 'Mens Running Shoes', 5000.00, 201, 21, 'uploads/Mens Running Shoes.jpg'),
(21, 'Grand Court', 4300.00, 100, 23, 'uploads/Adidas Grand Court.jpg'),
(27, 'Adidadis Shirt', 399.00, 26, 29, 'uploads/1765891545_download.JPG'),
(29, 'Adidadis Shirt', 500.00, 25, 32, 'uploads/1765943948_blue adaias.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `email` varchar(250) NOT NULL,
  `userPassword` varchar(250) DEFAULT NULL,
  `userType` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `userName`, `email`, `userPassword`, `userType`) VALUES
(2, 'eds', 'eds@gmail.com', '$2y$10$iCv.kAL8.a90OlXIE7Fitu89wVMgegiy0cDUliKCla/kr.SbAfaj2', 'Admin'),
(3, 'hello', 'hello@gmail.com', '$2y$10$LWGR2Jpk.G6KUWMGfbduCeZiKOLrkA7kqt1X0fBY8Jc8KQxNnIV5C', 'Staff'),
(4, 'PinakaAdmin', 'admin@gmail.com', '$2y$10$Ukp.4hvr.3iL.2XH5gLaqey20Hd2ByxzjOLkiLBGL53KY43Q1oTrC', 'Admin'),
(5, 'uwuStaff', 'student@gmail.com', '$2y$10$UjpTKp4WALQf8Afs0LqcV.hyoGFfyqd9wJ43YfdvRDOWj3yg.2U/q', 'Staff'),
(7, 'uwu', 'uwu@gmail.com', '$2y$10$NnXXgWD4Tjug71Cq11PimeEsVSN2EGItXvZ.vX1YhtxAA4ng3UHpC', 'Customer'),
(8, 'RMRUFIN', 'RMRUFIN@gmail.com', '$2y$10$f2r60DmS9pawHxQyDcRkTOKYzIza0EGD3/TJBvO5fKWm2KgYyla9.', 'Customer'),
(9, 'ai123', 'MAN@gmail.com', '$2y$10$CbuV/KsGAGlQcfsBEhebXeu5ioXvfzH4CANG/nyWmwn/UN0LjMQsi', 'Admin'),
(13, 'aila', 'mantong@gmail.com', '$2y$10$ATWMPIR11Y6WeAhRkqSiNOGuwmYQn6f.HeZ0yRQk.JfRA1hByGLeq', 'Staff'),
(16, 'ai12345', 'MAN@gmail.com', '$2y$10$Dgv16j8K5Ug0SZvJAGsNxubiW1X3F3RWQbgf/yCW6J5S7Zpv8nADu', 'Customer');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_logs`
--

CREATE TABLE `tbl_user_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `userType` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NULL DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Logged In'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_logs`
--

INSERT INTO `tbl_user_logs` (`log_id`, `user_id`, `username`, `userType`, `remarks`, `login_time`, `logout_time`, `status`) VALUES
(4, 9, 'ai123', 'ADMIN', 'brownout', '2025-12-16 11:08:13', '2025-12-16 11:08:39', 'Logged In'),
(7, 9, 'ai123', 'STAFF', '', '2025-12-16 11:55:50', NULL, 'Logged Out'),
(8, 9, 'ai123', 'ADMIN', '', '2025-12-17 00:12:02', '2025-12-17 02:36:23', 'Logged Out'),
(9, 9, 'ai123', 'Admin', NULL, '2025-12-17 02:43:59', '2025-12-17 02:51:39', 'Logged Out'),
(10, 9, 'ai123', 'Admin', NULL, '2025-12-17 03:03:46', '2025-12-17 03:08:38', 'Logged Out'),
(11, 9, 'ai123', 'Admin', NULL, '2025-12-17 03:09:10', NULL, 'Logged In'),
(12, 9, 'ai123', 'Admin', NULL, '2025-12-17 05:06:31', NULL, 'Logged In'),
(13, 9, 'ai123', 'Admin', NULL, '2025-12-17 06:55:10', '2025-12-17 08:20:03', 'Logged Out'),
(14, 9, 'ai123', 'Admin', NULL, '2025-12-17 08:21:02', '2025-12-17 08:22:23', 'Logged Out'),
(15, 9, 'ai123', 'Admin', NULL, '2025-12-17 08:25:13', '2025-12-17 08:25:31', 'Logged Out'),
(16, 13, 'aila', 'Staff', NULL, '2025-12-17 08:25:51', '2025-12-17 08:26:15', 'Logged Out'),
(17, 16, 'ai12345', 'Customer', NULL, '2025-12-17 08:34:07', NULL, 'Logged In');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_categories`
--
ALTER TABLE `tbl_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD PRIMARY KEY (`customerID`),
  ADD KEY `fk_users` (`user_id`);

--
-- Indexes for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_customer` (`customerID`),
  ADD KEY `fk_product` (`product_id`);

--
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tbl_user_logs`
--
ALTER TABLE `tbl_user_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_categories`
--
ALTER TABLE `tbl_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_user_logs`
--
ALTER TABLE `tbl_user_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD CONSTRAINT `fk_users` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_orders`
--
ALTER TABLE `tbl_orders`
  ADD CONSTRAINT `fk_customer` FOREIGN KEY (`customerID`) REFERENCES `tbl_customers` (`customerID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `tbl_categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
