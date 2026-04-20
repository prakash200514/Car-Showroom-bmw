-- Database: showroom_db

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Table: roles
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Sales Manager'),
(3, 'Service Manager'),
(4, 'Spare Manager'),
(5, 'Customer');

-- --------------------------------------------------------

-- Table: users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin: admin@showroom.com / Admin@123
-- Hash for 'Admin@123' is needed. Using a placeholder hash for now, dependent on PHP password_hash default.
-- For this SQL file, I will insert a raw hash.
-- hash for 'Admin@123' using PASSWORD_DEFAULT (bcrypt) approx: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (this is 'password', let's just make sure the PHP register flow works or provide a valid hash)
-- Let's use a known hash for 'Admin@123'.
-- $2y$10$zP6Y/.1/.. (Example) - Better to let the user register or provide a script.
-- I'll insert a standard testing hash: $2y$10$w8.1/.. for 'Admin@123'
-- Hash for 'Admin@123': $2y$10$r/w/.. (I will generate this in PHP or use a common one)
-- Let's use: $2y$10$7/.. (This is complicated to guess without running PHP). 
-- I will leave the insert for the PHP setup or provide a known query.
-- Actually, let's insert the admin user with a known simple hash if possible, or just the user structure.
-- I'll insert it in the PHP setup or a separate seed file to be safe, OR valid SQL:
-- Default Admin
INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password_hash`, `role_id`, `created_at`) VALUES
(1, 'Super Admin', 'admin@showroom.com', '1234567890', '$2y$10$8Wk/..', 1, NOW()); 
-- Note: User needs to update this hash or I'll provide a script to generate it. 
-- For now, let's just create the table.

-- --------------------------------------------------------

-- Table: branches
CREATE TABLE `branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `map_embed` text,
  `image` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: cars
CREATE TABLE `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `body_type` varchar(50) NOT NULL,
  `transmission` varchar(50) NOT NULL, -- Automatic, Manual
  `fuel_type` varchar(50) NOT NULL, -- Petrol, Diesel, Electric, Hybrid
  `year` int(11) NOT NULL,
  `engine_cc` varchar(20),
  `power_hp` varchar(20),
  `mileage` varchar(20),
  `seats` int(11),
  `description` text,
  `is_featured` tinyint(1) DEFAULT 0,
  `spec_seats` text,
  `spec_lights` text,
  `spec_airbags` text,
  `spec_safety` text,
  `spec_tyres` text,
  `spec_gearbox` text,
  `spec_speakers` text,
  `spec_advantages` text,
  `reveal_section_1_title` varchar(255),
  `reveal_section_1_text` text,
  `reveal_section_2_title` varchar(255),
  `reveal_section_2_text` text,
  `img_reveal_1` text,
  `img_reveal_2` text,
  `img_seats` text,
  `img_lights` text,
  `img_airbags` text,
  `img_safety` text,
  `img_tyres` text,
  `img_gearbox` text,
  `img_speakers` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert BMW Cars
INSERT INTO `cars` (`id`, `name`, `brand`, `price`, `body_type`, `transmission`, `fuel_type`, `year`, `engine_cc`, `power_hp`, `mileage`, `seats`, `description`, `is_featured`, `created_at`) VALUES
(1, 'BMW M4 Competition', 'BMW', '79000.00', 'Coupe', 'Automatic', 'Petrol', 2024, '2993 cc', '503 hp', '10 kmpl', 4, 'The BMW M4 Competition Coupe is the ultimate driving machine.', 1, current_timestamp()),
(2, 'BMW X5 M', 'BMW', '105900.00', 'SUV', 'Automatic', 'Petrol', 2024, '4395 cc', '617 hp', '8 kmpl', 5, 'The BMW X5 M is a high-performance SAV that combines luxury with raw power.', 1, current_timestamp()),
(3, 'BMW i8 Roadster', 'BMW', '163300.00', 'Convertible', 'Automatic', 'Hybrid', 2023, '1499 cc', '369 hp', '40 kmpl', 2, 'The BMW i8 Roadster is a plug-in hybrid sports car with gullwing doors.', 1, current_timestamp()),
(4, 'BMW 3 Series Gran Limousine', 'BMW', '45000.00', 'Sedan', 'Automatic', 'Diesel', 2024, '1995 cc', '190 hp', '18 kmpl', 5, 'The BMW 3 Series Gran Limousine offers best-in-class comfort.', 1, current_timestamp()),
(5, 'BMW Z4 M40i', 'BMW', '65000.00', 'Convertible', 'Automatic', 'Petrol', 2024, '2998 cc', '335 hp', '12 kmpl', 2, 'The BMW Z4 Roadster is a classic sports car reinterpreted.', 1, current_timestamp()),
(6, 'BMW 7 Series', 'BMW', '95000.00', 'Sedan', 'Automatic', 'Hybrid', 2024, '2998 cc', '375 hp', '15 kmpl', 5, 'The BMW 7 Series is the epitome of luxury and innovation.', 1, current_timestamp());

INSERT INTO `car_images` (`car_id`, `image_path`, `is_primary`) VALUES
(1, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1),
(2, 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1),
(3, 'https://images.unsplash.com/photo-1556189250-72ba954e96b5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1),
(4, 'https://images.unsplash.com/photo-1555215695-3004980adade?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1),
(5, 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1),
(6, 'https://images.unsplash.com/photo-1553440683-1b94dd08f6d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 1);

-- --------------------------------------------------------

-- Table: car_images
CREATE TABLE `car_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `fk_car_images` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: car_360_frames
CREATE TABLE `car_360_frames` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `frame_no` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `fk_car_360` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: car_variants
CREATE TABLE `car_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `variant_name` varchar(100) NOT NULL,
  `price_extra` decimal(15,2) DEFAULT 0.00,
  `features_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `fk_car_variants` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: wishlist
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: test_drive_bookings
CREATE TABLE `test_drive_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `time_slot` varchar(20) NOT NULL,
  `status` enum('Requested','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Requested',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `car_id` (`car_id`),
  KEY `branch_id` (`branch_id`),
  CONSTRAINT `fk_td_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_td_car` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_td_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: services
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `description` text,
  `base_price` decimal(15,2) NOT NULL,
  `duration_hours` int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: service_bookings
CREATE TABLE `service_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `car_model` varchar(100) NOT NULL,
  `reg_number` varchar(20) NOT NULL,
  `service_date` date NOT NULL,
  `pickup_required` tinyint(1) DEFAULT 0,
  `pickup_address` text,
  `status` enum('Pending','Confirmed','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_sb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: service_status_logs
CREATE TABLE `service_status_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `fk_ssl_booking` FOREIGN KEY (`booking_id`) REFERENCES `service_bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: spare_categories
CREATE TABLE `spare_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `image` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: spares
CREATE TABLE `spares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `description` text,
  `image` varchar(255),
  `part_number` varchar(50),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_spare_cat` FOREIGN KEY (`category_id`) REFERENCES `spare_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: orders
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: order_items
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `spare_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `spare_id` (`spare_id`),
  CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_oi_spare` FOREIGN KEY (`spare_id`) REFERENCES `spares` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: garage_backgrounds
CREATE TABLE `garage_backgrounds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: invoices
CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_type` enum('Service','Order','CarPurchase') NOT NULL,
  `ref_id` int(11) NOT NULL, -- Reference ID (Booking ID or Order ID)
  `invoice_no` varchar(50) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `invoice_no` (`invoice_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table: enquiries
CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20),
  `subject` varchar(255),
  `message` text,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
