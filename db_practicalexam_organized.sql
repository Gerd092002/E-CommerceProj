
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================================
-- Database: `db_ecommerce`
-- ============================================================================

DROP DATABASE IF EXISTS `db_ecommerce`;
CREATE DATABASE `db_ecommerce` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_ecommerce`;

-- ============================================================================
-- TABLE 1: tbl_user - User Authentication & Profile Management
-- ============================================================================

DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(255) NOT NULL UNIQUE,
  `email` varchar(250) NOT NULL UNIQUE,
  `userPassword` varchar(250) NOT NULL,
  `userType` enum('Admin', 'Staff', 'Customer') NOT NULL DEFAULT 'Customer',
  `profile_picture` varchar(255) NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_userType` (`userType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Insert Default Admin Account
-- ============================================================================
-- Username: admin | Password: admin123 (hashed with bcrypt)
INSERT INTO `tbl_user` (`user_id`, `userName`, `email`, `userPassword`, `userType`) VALUES
(1, 'admin', 'admin@adidadidadas.com', '$2y$10$N9qo8uLOickgx2ZMRZoMye4zIW9PdyPqHYW9xr6.eHr8u2y.3XKU6', 'Admin');

-- ============================================================================
-- TABLE 2: tbl_categories - Product Categories
-- ============================================================================

DROP TABLE IF EXISTS `tbl_categories`;
CREATE TABLE `tbl_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(250) NOT NULL,
  `categoryDesc` text NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `unique_category_name` (`categoryName`),
  INDEX `idx_categoryName` (`categoryName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Insert Sample Categories
-- ============================================================================

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

-- ============================================================================
-- TABLE 3: tbl_products - Product Inventory
-- ============================================================================

DROP TABLE IF EXISTS `tbl_products`;
CREATE TABLE `tbl_products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(250) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL,
  `image_path` varchar(255) NULL,
  `description` text NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`),
  INDEX `idx_category_id` (`category_id`),
  INDEX `idx_product_name` (`product_name`),
  INDEX `idx_price` (`price`),
  FOREIGN KEY (`category_id`) REFERENCES `tbl_categories` (`category_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Insert Sample Products
-- ============================================================================

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

-- ============================================================================
-- TABLE 4: tbl_customers - Customer Information
-- ============================================================================

DROP TABLE IF EXISTS `tbl_customers`;
CREATE TABLE `tbl_customers` (
  `customerID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fullname` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `phone` varchar(20) NULL,
  `address` varchar(250) NULL,
  `bday` date NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customerID`),
  UNIQUE KEY `unique_user_id` (`user_id`),
  INDEX `idx_fullname` (`fullname`),
  INDEX `idx_email` (`email`),
  FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Insert Sample Customer
-- ============================================================================

INSERT INTO `tbl_customers` (`customerID`, `user_id`, `fullname`, `email`, `phone`, `address`, `bday`) VALUES
(1, 8, 'Rejallejon', 'RMRUFIN@gmail.com', '09123123123', '', '2025-12-24');

-- ============================================================================
-- TABLE 5: tbl_orders - Shopping Cart & Orders
-- ============================================================================

DROP TABLE IF EXISTS `tbl_orders`;
CREATE TABLE `tbl_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customerID` int(11) NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (quantity * price) STORED,
  `status` enum('active', 'purchased', 'removed', 'cancelled') DEFAULT 'active',
  `added_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  INDEX `idx_customerID` (`customerID`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_added_at` (`added_at`),
  FOREIGN KEY (`customerID`) REFERENCES `tbl_customers` (`customerID`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Insert Sample Orders
-- ============================================================================

INSERT INTO `tbl_orders` (`order_id`, `customerID`, `product_id`, `quantity`, `price`, `status`) VALUES
(6, 1, 19, 1, 5000.00, 'active'),
(7, 1, 18, 1, 5000.00, 'removed'),
(8, 1, 13, 1, 10000.00, 'active');

-- ============================================================================
-- TABLE 6: tbl_sales - Completed Transactions (NEW - For Sales Tracking)
-- ============================================================================

DROP TABLE IF EXISTS `tbl_sales`;
CREATE TABLE `tbl_sales` (
  `sale_id` int(11) NOT NULL AUTO_INCREMENT,
  `customerID` int(11) NULL,
  `order_id` int(11) NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (quantity * price) STORED,
  `payment_method` varchar(50) NULL DEFAULT 'Not Specified',
  `sale_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `notes` text NULL,
  PRIMARY KEY (`sale_id`),
  INDEX `idx_customerID` (`customerID`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_sale_date` (`sale_date`),
  INDEX `idx_month_year` (DATE_FORMAT(`sale_date`, '%Y-%m')),
  FOREIGN KEY (`customerID`) REFERENCES `tbl_customers` (`customerID`) ON DELETE SET NULL,
  FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABLE 7: tbl_user_logs - User Activity & Login Tracking
-- ============================================================================

DROP TABLE IF EXISTS `tbl_user_logs`;
CREATE TABLE `tbl_user_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `userType` varchar(50) NOT NULL,
  `action` varchar(100) DEFAULT 'Login',
  `remarks` text NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_time` timestamp NULL,
  `status` varchar(50) DEFAULT 'Logged In',
  PRIMARY KEY (`log_id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_login_time` (`login_time`),
  INDEX `idx_userType` (`userType`),
  FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Insert Sample User Logs
-- ============================================================================

INSERT INTO `tbl_user_logs` (`log_id`, `user_id`, `username`, `userType`, `remarks`, `login_time`, `logout_time`, `status`) VALUES
(1, 1, 'admin', 'Admin', 'Initial login', '2026-06-17 10:00:00', NULL, 'Logged In');

-- ============================================================================
-- TABLE 8: tbl_inventory_logs - Product Stock Changes (NEW - For Inventory Tracking)
-- ============================================================================

DROP TABLE IF EXISTS `tbl_inventory_logs`;
CREATE TABLE `tbl_inventory_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `change_reason` varchar(100) NOT NULL,
  `changed_by` int(11) NULL,
  `changed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_changed_at` (`changed_at`),
  FOREIGN KEY (`product_id`) REFERENCES `tbl_products` (`product_id`) ON DELETE CASCADE,
  FOREIGN KEY (`changed_by`) REFERENCES `tbl_user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- Create Views for Admin Dashboard Reports
-- ============================================================================

-- ============================================================================
-- View: vw_sales_summary - Daily Sales Summary
-- ============================================================================

DROP VIEW IF EXISTS `vw_sales_summary`;
CREATE VIEW `vw_sales_summary` AS
SELECT 
    DATE(`s`.`sale_date`) as sale_date,
    COUNT(`s`.`sale_id`) as total_orders,
    SUM(`s`.`quantity`) as total_items,
    SUM(`s`.`total`) as total_sales,
    COUNT(DISTINCT `s`.`customerID`) as unique_customers
FROM `tbl_sales` `s`
GROUP BY DATE(`s`.`sale_date`)
ORDER BY `s`.`sale_date` DESC;

-- ============================================================================
-- View: vw_product_sales_stats - Product Performance Stats
-- ============================================================================

DROP VIEW IF EXISTS `vw_product_sales_stats`;
CREATE VIEW `vw_product_sales_stats` AS
SELECT 
    `p`.`product_id`,
    `p`.`product_name`,
    `p`.`price`,
    `p`.`quantity` as current_stock,
    COUNT(`s`.`sale_id`) as times_sold,
    SUM(`s`.`quantity`) as total_quantity_sold,
    SUM(`s`.`total`) as total_revenue,
    AVG(`s`.`price`) as avg_selling_price
FROM `tbl_products` `p`
LEFT JOIN `tbl_sales` `s` ON `p`.`product_id` = `s`.`product_id`
GROUP BY `p`.`product_id`, `p`.`product_name`, `p`.`price`, `p`.`quantity`
ORDER BY total_revenue DESC;

-- ============================================================================
-- View: vw_customer_purchases - Customer Purchase History
-- ============================================================================

DROP VIEW IF EXISTS `vw_customer_purchases`;
CREATE VIEW `vw_customer_purchases` AS
SELECT 
    `c`.`customerID`,
    `c`.`fullname`,
    `c`.`email`,
    COUNT(`s`.`sale_id`) as total_purchases,
    SUM(`s`.`total`) as total_spent,
    MAX(`s`.`sale_date`) as last_purchase_date
FROM `tbl_customers` `c`
LEFT JOIN `tbl_sales` `s` ON `c`.`customerID` = `s`.`customerID`
GROUP BY `c`.`customerID`, `c`.`fullname`, `c`.`email`
ORDER BY total_spent DESC;

-- ============================================================================
-- Create Stored Procedures for Common Operations
-- ============================================================================

-- ============================================================================
-- Procedure: Complete Purchase Order
-- ============================================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_complete_purchase`$$

CREATE PROCEDURE `sp_complete_purchase` (
    IN p_order_id INT
)
BEGIN
    DECLARE v_customerID INT;
    DECLARE v_product_id INT;
    DECLARE v_quantity INT;
    DECLARE v_price DECIMAL(10,2);
    
    -- Get order details
    SELECT customerID, product_id, quantity, price INTO v_customerID, v_product_id, v_quantity, v_price
    FROM tbl_orders
    WHERE order_id = p_order_id AND status = 'active';
    
    -- Insert into sales table
    INSERT INTO tbl_sales (customerID, order_id, product_id, quantity, price)
    VALUES (v_customerID, p_order_id, v_product_id, v_quantity, v_price);
    
    -- Update order status to purchased
    UPDATE tbl_orders SET status = 'purchased' WHERE order_id = p_order_id;
    
    -- Reduce product quantity
    UPDATE tbl_products SET quantity = quantity - v_quantity WHERE product_id = v_product_id;
    
    -- Log inventory change
    INSERT INTO tbl_inventory_logs (product_id, quantity_before, quantity_after, change_reason, changed_by)
    SELECT v_product_id, quantity + v_quantity, quantity, 'Sale Completed', NULL;
END$$

DELIMITER ;

-- ============================================================================
-- Set Foreign Key Constraints
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- ============================================================================
-- END OF DATABASE SCHEMA
-- ============================================================================
-- 
-- ADMIN ACCOUNT CREDENTIALS:
-- Username: admin
-- Password: admin123
-- Email: admin@adidadidadas.com
-- Type: Admin
--
-- NEW FEATURES ADDED:
-- 1. tbl_sales - Complete transaction tracking
-- 2. tbl_inventory_logs - Stock movement tracking
-- 3. Views for dashboard reporting and analytics
-- 4. Stored procedure for order completion
-- 5. Better indexing for performance
-- 6. Profile picture support in tbl_user
--
-- ============================================================================
