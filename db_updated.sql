SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@CHARACTER_SET_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- This file is intended for import into the selected database in phpMyAdmin.
-- Make sure the target database exists and is selected before importing.

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
  INDEX `idx_userType` (`userType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  INDEX `idx_changed_at` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  INDEX `idx_sale_date` (`sale_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `tbl_orders`;
CREATE TABLE `tbl_orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `customerID` int(11) NULL,
  `user_id` int(11) NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) GENERATED ALWAYS AS (quantity * price) STORED,
  `status` enum('active', 'purchased', 'removed', 'cancelled', 'Logged In') DEFAULT 'active',
  `added_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  INDEX `idx_customerID` (`customerID`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_added_at` (`added_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  INDEX `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `tbl_categories` (`categoryName`, `categoryDesc`, `created_at`, `updated_at`)
VALUES
  ('Adidas Originals', 'Classic Adidas heritage sneakers and lifestyle shoes.', NOW(), NOW()),
  ('Running Shoes', 'Performance running shoes for daily training and races.', NOW(), NOW()),
  ('Trainers', 'Comfortable trainers for gym, streetwear, and casual wear.', NOW(), NOW()),
  ('Women''s Shoes', 'Women''s footwear collection with style and comfort.', NOW(), NOW());

INSERT INTO `tbl_products` (`product_name`, `price`, `quantity`, `category_id`, `image_path`, `description`, `created_at`, `updated_at`)
VALUES
  ('Blue Adidas Shoes', 2499.99, 25, 1, 'uploads/1765966906_blue adaias.jpg', 'Classic blue adidas sneaker with comfortable cushioning.', NOW(), NOW()),
  ('Adidas Campus', 2699.99, 20, 3, 'uploads/1765966919_download.JPG', 'Timeless adidas Campus design with premium styling.', NOW(), NOW()),
  ('Retro Runner', 2299.99, 22, 2, 'uploads/1771241803_s-l1600.jpg', 'Retro running shoe with modern comfort and support.', NOW(), NOW()),
  ('70s Running Shoes', 1999.99, 18, 2, 'uploads/70s running shoes.jpg', 'Vintage-inspired running shoes built for everyday wear.', NOW(), NOW()),
  ('Adidas Grand Court', 2599.99, 24, 1, 'uploads/Adidas Grand Court.jpg', 'Adidas Grand Court low-top with clean, minimalist style.', NOW(), NOW()),
  ('Adidas Campus II', 2699.99, 20, 3, 'uploads/Buy campus.jpg', 'Campus sneaker with soft suede upper and classic lines.', NOW(), NOW()),
  ('Men''s Adidas Originals', 2799.99, 30, 1, 'uploads/Men''s adidas Original Shoes.jpg', 'Premium men''s adidas Originals with iconic branding.', NOW(), NOW()),
  ('Men''s Originals Plus', 2799.99, 30, 1, 'uploads/Mens adidas Original Shoes.jpg', 'A second version of the men''s Originals sneaker.', NOW(), NOW()),
  ('Men''s Running Shoes', 2199.99, 27, 2, 'uploads/Mens Running Shoes.jpg', 'Men''s running shoe with responsive cushioning.', NOW(), NOW()),
  ('Mens Samba', 2399.99, 21, 1, 'uploads/mens samba.jpg', 'Classic Samba style for court and street wear.', NOW(), NOW()),
  ('Own The Game', 2499.99, 19, 3, 'uploads/Own the game.jpg', 'Sporty trainer with a bold, athletic design.', NOW(), NOW()),
  ('Runfalcon 5', 2199.99, 26, 2, 'uploads/Runfalcon 5.jpg', 'Modern running shoe with lightweight support.', NOW(), NOW()),
  ('Trainer Shoes', 2299.99, 23, 3, 'uploads/Trainer shoes.jpg', 'All-purpose trainer with comfortable fit and grip.', NOW(), NOW()),
  ('Women''s Tokyo', 2599.99, 20, 4, 'uploads/womens tokyo.jpg', 'Women''s Tokyo shoe with sleek, stylish design.', NOW(), NOW());

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `tbl_user` (`userName`, `email`, `userPassword`, `userType`, `created_at`, `updated_at`)
VALUES ('admin', 'admin@example.com', '$2y$10$7QJZA6ag5Pi2qLPSb8tKS.KA64CR0VRQcCvan98T19MNu4x8mhWJu', 'Admin', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
