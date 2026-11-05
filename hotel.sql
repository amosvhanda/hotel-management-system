-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2023 at 11:59 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `category_name`, `category_slug`, `created_at`, `updated_at`) VALUES
(1, 'Travel Destinations', 'travel-destinations', NULL, NULL),
(2, 'Luxury Hotels', 'luxury-hotels', NULL, '2023-06-26 16:22:34'),
(3, 'Boutique Hotels', 'boutique-hotels', NULL, NULL),
(4, 'Travel Tips and Hacks', 'travel-tips-and-hacks', NULL, NULL),
(5, 'Hotel Reviews', 'hotel-reviews', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blogcat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_titile` varchar(255) NOT NULL,
  `post_slug` varchar(255) NOT NULL,
  `post_image` varchar(255) NOT NULL,
  `short_descp` text NOT NULL,
  `long_descp` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `blogcat_id`, `user_id`, `post_titile`, `post_slug`, `post_image`, `short_descp`, `long_descp`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Hotel Management is the', 'hotel-management-is-the', 'upload/post/1769890092497476.png', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>', '2023-06-27 14:45:24', NULL),
(2, 2, 1, 'Best Issue in the Global Market', 'best-issue-in-the-global-market', 'upload/post/1769890322212673.png', 'quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>', '2023-06-27 14:49:03', NULL),
(3, 1, 1, 'Hotel Room Reservation is a Common Fact Now', 'hotel-room-reservation-is-a-common-fact-now', 'upload/post/1769890362372886.png', 'orem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\r\n<p>&nbsp;</p>', '2023-06-27 14:49:41', NULL),
(4, 1, 1, '11We Can Easily Make a Good Hotel Management', '11we-can-easily-make-a-good-hotel-management', 'upload/post/1769892115844648.png', '11Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis', '<p>11Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>\r\n<p>Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quci velit modi tempora incidunt ut labore et dolore magnam aliquam quaerat .</p>\r\n<p>&nbsp;</p>', '2023-06-27 15:17:33', '2023-06-27 15:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rooms_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `check_in` varchar(255) DEFAULT NULL,
  `check_out` varchar(255) DEFAULT NULL,
  `persion` varchar(255) DEFAULT NULL,
  `number_of_rooms` varchar(255) DEFAULT NULL,
  `total_night` double(8,2) NOT NULL DEFAULT 0.00,
  `actual_price` double(8,2) NOT NULL DEFAULT 0.00,
  `subtotal` double(8,2) NOT NULL DEFAULT 0.00,
  `discount` int(11) NOT NULL DEFAULT 0,
  `total_price` double(8,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `transation_id` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `rooms_id`, `user_id`, `check_in`, `check_out`, `persion`, `number_of_rooms`, `total_night`, `actual_price`, `subtotal`, `discount`, `total_price`, `payment_method`, `transation_id`, `payment_status`, `name`, `email`, `phone`, `country`, `state`, `zip_code`, `address`, `code`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 2, '2023-06-16', '2023-06-18', '02', '01', 2.00, 120.00, 240.00, 24, 216.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'India', 'Gujrat', '2323', 'India', '879008861', 0, '2023-06-14 05:54:20', '2023-06-14 05:54:20'),
(2, 7, 2, '2023-06-20', '2023-06-22', '02', '01', 2.00, 250.00, 500.00, 50, 450.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '2222', 'India', '252717967', 0, '2023-06-14 05:58:28', '2023-06-14 05:58:28'),
(3, 4, 2, '2023-06-20', '2023-06-23', '02', '01', 3.00, 120.00, 360.00, 36, 324.00, 'Stripe', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '445175806', 1, '2023-06-17 15:17:23', '2023-06-22 16:12:18'),
(4, 5, 2, '2023-06-22', '2023-06-24', '02', '01', 2.00, 130.00, 260.00, 0, 260.00, 'Stripe', '', '1', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '982519279', 1, '2023-06-17 15:20:38', '2023-06-20 15:36:49'),
(5, 7, 2, '2023-06-21', '2023-06-26', '02', '2', 2.00, 250.00, 500.00, 50, 450.00, 'COD', '', '1', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '810180464', 1, '2023-06-17 15:22:35', '2023-06-22 14:53:13'),
(6, 1, 1, '2023-06-25', '2023-06-27', '2', '1', 2.00, 100.00, 200.00, 0, 200.00, 'COD', NULL, '1', 'Ariyan', 'ariyan@gmail.com', '5566', 'India', 'Dhaka', '3434', 'your address', '562567108', 1, '2023-06-21 15:17:55', '2023-06-21 15:19:59'),
(7, 4, 2, '2023-07-05', '2023-07-07', '01', '01', 2.00, 120.00, 240.00, 24, 216.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '111663583', 0, '2023-07-04 15:04:30', '2023-07-04 15:04:30'),
(8, 7, 2, '2023-07-19', '2023-07-21', '02', '01', 2.00, 250.00, 500.00, 50, 450.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '774740996', 0, '2023-07-04 15:04:53', '2023-07-04 15:04:53'),
(9, 1, 2, '2023-07-27', '2023-07-30', '01', '01', 3.00, 100.00, 300.00, 0, 300.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '343434', 'India', '414646819', 0, '2023-07-04 15:05:23', '2023-07-04 15:05:23'),
(10, 5, 3, '2023-07-19', '2023-07-21', '02', '01', 2.00, 130.00, 260.00, 0, 260.00, 'COD', '', '0', 'Khan', 'khan@gmail.com', '3434', 'India', 'India', '2343', 'India', '482435708', 0, '2023-07-04 15:06:54', '2023-07-04 15:06:54'),
(11, 5, 3, '2023-07-24', '2023-07-26', '02', '01', 2.00, 130.00, 260.00, 0, 260.00, 'COD', '', '0', 'Khan', 'khan@gmail.com', '3434', 'India', 'India', '3434', 'India', '372917640', 0, '2023-07-04 15:07:25', '2023-07-04 15:07:25'),
(12, 4, 2, '2023-07-14', '2023-07-16', '01', '01', 2.00, 120.00, 240.00, 24, 216.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '289862124', 0, '2023-07-06 16:04:59', '2023-07-06 16:04:59'),
(13, 4, 2, '2023-07-19', '2023-07-21', '02', '01', 2.00, 120.00, 240.00, 24, 216.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '756136510', 0, '2023-07-07 12:10:55', '2023-07-07 12:10:55'),
(14, 1, 2, '2023-07-17', '2023-07-18', '01', '01', 1.00, 100.00, 100.00, 0, 100.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '224493315', 0, '2023-07-07 12:14:21', '2023-07-07 12:14:21'),
(15, 5, 2, '2023-07-19', '2023-07-20', '01', '01', 1.00, 130.00, 130.00, 0, 130.00, 'COD', '', '0', 'User', 'user@gmail.com', '01711', 'Bangladesh', 'Dhaka', '3434', 'India', '515669929', 0, '2023-07-07 13:08:02', '2023-07-07 13:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `booking_room_lists`
--

CREATE TABLE `booking_room_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `room_number_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_room_lists`
--

INSERT INTO `booking_room_lists` (`id`, `booking_id`, `room_id`, `room_number_id`, `created_at`, `updated_at`) VALUES
(4, 5, 7, 17, '2023-06-20 13:44:35', '2023-06-20 13:44:35'),
(5, 5, 7, 18, '2023-06-20 13:44:42', '2023-06-20 13:44:42'),
(6, 4, 5, 14, '2023-06-20 15:36:43', '2023-06-20 15:36:43'),
(7, 6, 1, 1, '2023-06-21 15:19:48', '2023-06-21 15:19:48'),
(8, 3, 4, 11, '2023-06-22 16:12:05', '2023-06-22 16:12:05');

-- --------------------------------------------------------

--
-- Table structure for table `book_areas`
--

CREATE TABLE `book_areas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `short_title` varchar(255) DEFAULT NULL,
  `main_title` varchar(255) DEFAULT NULL,
  `short_desc` text DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `book_areas`
--

INSERT INTO `book_areas` (`id`, `image`, `short_title`, `main_title`, `short_desc`, `link_url`, `created_at`, `updated_at`) VALUES
(1, 'upload/bookarea/1768170161053573.jpg', 'MAKE A QUICK BOOKING', 'We Are the Best in All-time & So Please Get a Quick Booking', 'Atoli is one of the best resorts in the global market and that\'s why you will get a luxury life period on the global market. We always provide you a special support for all of our guests and that\'s will be the main reason to be the most popular.', 'http://127.0.0.1:8000/', NULL, '2023-06-08 15:08:29');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `message`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 4, 'Engineering requires many building blocks and tools. To produce real world results & one must mathematics and sciences to tangible problems and we are one of the best company in the world.', '1', '2023-06-28 14:34:22', '2023-07-04 14:28:15'),
(2, 2, 4, 'Ecespiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae', '1', '2023-06-28 14:34:57', '2023-07-04 14:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `created_at`, `updated_at`) VALUES
(1, 'Ariyan', 'ariyan@gmail.com', '01545', 'i need your help', 'i want contact i need some help from you.', '2023-07-05 15:36:21', NULL),
(2, 'Khan', 'khan@gmail.com', '012233', 'Need to change hosting one domain', 'Let\'s Start to Give Us a Message and Contact With Us', '2023-07-05 15:37:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rooms_id` int(11) NOT NULL,
  `facility_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `rooms_id`, `facility_name`, `created_at`, `updated_at`) VALUES
(35, 1, 'Work Desk', '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(36, 1, 'Slippers', '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(37, 1, 'Wake-up service', '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(38, 1, 'Hair dryer', '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(39, 1, NULL, '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(40, 1, NULL, '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(41, 1, 'Safety box', '2023-06-08 13:59:02', '2023-06-08 13:59:02'),
(42, 4, 'Complimentary Breakfast', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(43, 4, '32/42 inch LED TV', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(44, 4, 'Smoke alarms', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(45, 4, 'Minibar', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(46, 4, 'Laundry & Dry Cleaning', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(47, 4, 'Slippers', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(48, 4, 'Work Desk', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(49, 4, 'Safety box', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(79, 5, NULL, '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(80, 5, 'Minibar', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(81, 5, 'Smoke alarms', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(82, 5, NULL, '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(83, 5, 'Work Desk', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(84, 5, 'Free Wi-Fi', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(85, 5, 'Rain Shower', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(86, 5, 'Hair dryer', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(87, 5, 'Wake-up service', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(88, 5, 'Electronic door lock', '2023-06-11 16:16:44', '2023-06-11 16:16:44'),
(89, 7, NULL, '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(90, 7, NULL, '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(91, 7, 'Smoke alarms', '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(92, 7, NULL, '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(93, 7, 'Work Desk', '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(94, 7, 'Free Wi-Fi', '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(95, 7, 'Rain Shower', '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(96, 7, 'Laundry & Dry Cleaning', '2023-06-13 13:15:41', '2023-06-13 13:15:41'),
(97, 7, 'Electronic door lock', '2023-06-13 13:15:41', '2023-06-13 13:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `photo_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `photo_name`, `created_at`, `updated_at`) VALUES
(2, 'upload/gallery/1770612216903774.jpg', '2023-07-05 14:03:15', NULL),
(9, 'upload/gallery/1770614272093895.jpg', '2023-07-05 14:35:55', NULL),
(11, 'upload/gallery/1770614272581374.jpg', '2023-07-05 14:35:56', NULL),
(15, 'upload/gallery/1770614313721474.jpg', '2023-07-05 14:36:35', NULL),
(16, 'upload/gallery/1770614313943646.jpg', '2023-07-05 14:36:35', NULL),
(17, 'upload/gallery/1770614314158036.jpg', '2023-07-05 14:36:35', NULL),
(18, 'upload/gallery/1770614323056850.jpg', '2023-07-05 14:36:44', NULL),
(19, 'upload/gallery/1770614323277093.jpg', '2023-07-05 14:36:44', NULL),
(20, 'upload/gallery/1770614323493151.jpg', '2023-07-05 14:36:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_05_30_200905_create_teams_table', 2),
(6, '2023_05_31_202120_create_book_areas_table', 3),
(7, '2023_06_02_190521_create_room_types_table', 4),
(8, '2023_06_02_195413_create_rooms_table', 5),
(9, '2023_06_02_195445_create_facilities_table', 5),
(10, '2023_06_02_195519_create_multi_images_table', 5),
(11, '2023_06_04_203012_create_room_numbers_table', 6),
(12, '2023_06_11_185047_create_bookings_table', 7),
(13, '2023_06_11_185924_create_room_booked_dates_table', 7),
(14, '2023_06_11_213736_create_booking_room_lists_table', 8),
(15, '2023_06_22_212420_create_smtp_settings_table', 9),
(16, '2023_06_25_203821_create_testimonials_table', 10),
(17, '2023_06_26_211511_create_blog_categories_table', 11),
(18, '2023_06_27_193828_create_blog_posts_table', 12),
(19, '2023_06_28_200821_create_comments_table', 13),
(20, '2023_07_04_212639_create_site_settings_table', 14),
(21, '2023_07_05_192140_create_galleries_table', 15),
(22, '2023_07_05_212001_create_contacts_table', 16),
(23, '2023_07_07_174310_create_notifications_table', 17),
(24, '2023_07_07_200410_create_permission_tables', 18);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Table structure for table `multi_images`
--

CREATE TABLE `multi_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rooms_id` int(11) NOT NULL,
  `multi_img` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `multi_images`
--

INSERT INTO `multi_images` (`id`, `rooms_id`, `multi_img`, `created_at`, `updated_at`) VALUES
(13, 1, '202306081957PREMIER-SINGLE-Room-Photo-Singel.jpg', '2023-06-08 13:57:48', '2023-06-08 13:57:48'),
(14, 1, '202306081957PREMIER-SINGLE-Room-Photo-Singel2.jpg', '2023-06-08 13:57:48', '2023-06-08 13:57:48'),
(15, 1, '202306081957PREMIER-SINGLE-Room-Photo-Singel4.jpg', '2023-06-08 13:57:48', '2023-06-08 13:57:48'),
(16, 4, '202306082001super-premier-deluxe-main-Photo1.jpg', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(17, 4, '202306082001super-premier-deluxe-main-Photo2.jpg', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(18, 4, '202306082001super-premier-deluxe-main-Photo3.jpg', '2023-06-08 14:01:52', '2023-06-08 14:01:52'),
(19, 5, '202306082004Premier-Deluxe-Twin-main-Photo1.jpg', '2023-06-08 14:04:15', '2023-06-08 14:04:15'),
(20, 5, '202306082004Premier-Deluxe-Twin-main-Photo2.jpg', '2023-06-08 14:04:15', '2023-06-08 14:04:15'),
(21, 5, '202306082004Premier-Deluxe-Twin-main-Photo3.jpg', '2023-06-08 14:04:15', '2023-06-08 14:04:15'),
(22, 7, '202306082006Executive-Suite-Photo1.jpg', '2023-06-08 14:06:57', '2023-06-08 14:06:57'),
(23, 7, '202306082006Executive-Suite-Photo2.jpg', '2023-06-08 14:06:57', '2023-06-08 14:06:57'),
(24, 7, '202306082006Executive-Suite-Photo3.jpg', '2023-06-08 14:06:57', '2023-06-08 14:06:57');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('19d1a583-5367-444b-b519-ac762e39d7ca', 'App\\Notifications\\BookingComplete', 'App\\Models\\User', 1, '{\"message\":\"New Booking Added in Hotel\"}', '2023-07-07 13:07:07', '2023-07-07 12:14:21', '2023-07-07 13:07:07'),
('66e46839-026e-4927-9b06-fe4e206fc0cc', 'App\\Notifications\\BookingComplete', 'App\\Models\\User', 1, '{\"message\":\"New Booking Added in Hotel\"}', '2023-07-07 13:08:30', '2023-07-07 13:08:03', '2023-07-07 13:08:30'),
('cd09e3c0-4d6a-41ce-bc61-665f0fd1fe19', 'App\\Notifications\\BookingComplete', 'App\\Models\\User', 1, '{\"message\":\"New Booking Added in Hotel\"}', '2023-07-07 13:07:17', '2023-07-07 12:10:55', '2023-07-07 13:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `group_name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'team.menu', 'Team', 'web', '2023-07-07 15:18:50', '2023-07-07 15:18:50'),
(3, 'team.all', 'Team', 'web', '2023-07-08 12:38:33', '2023-07-08 12:38:33'),
(4, 'team.add', 'Team', 'web', '2023-07-08 12:38:41', '2023-07-08 12:38:41'),
(5, 'team.edit', 'Team', 'web', '2023-07-08 13:13:28', '2023-07-08 13:13:28'),
(6, 'team.delete ', 'Team', 'web', '2023-07-08 13:13:28', '2023-07-08 13:13:28'),
(7, 'bookarea.menu', 'Book Area', 'web', '2023-07-08 13:18:04', '2023-07-08 13:18:04'),
(8, 'update.bookarea', 'Book Area', 'web', '2023-07-08 13:18:11', '2023-07-08 13:18:11'),
(9, 'room.type.menu', 'Manage Room', 'web', '2023-07-08 13:18:23', '2023-07-08 13:18:23'),
(10, 'room.type', 'Manage Room', 'web', '2023-07-08 13:18:31', '2023-07-08 13:18:31'),
(11, 'booking.menu', 'Booking', 'web', '2023-07-08 13:18:39', '2023-07-08 13:18:39'),
(12, 'booking.list', 'Booking', 'web', '2023-07-08 13:18:46', '2023-07-08 13:18:46'),
(13, 'booking.add', 'Booking', 'web', '2023-07-08 13:18:52', '2023-07-08 13:18:52'),
(14, 'room.list.menu', 'RoomList', 'web', '2023-07-08 13:19:01', '2023-07-08 13:19:01'),
(15, 'room.list', 'RoomList', 'web', '2023-07-08 13:19:07', '2023-07-08 13:19:07'),
(16, 'setting.menu', 'Setting', 'web', '2023-07-08 13:19:16', '2023-07-08 13:19:16'),
(17, 'tesimonial.menu', 'Tesimonial', 'web', '2023-07-08 13:19:24', '2023-07-08 13:19:24'),
(18, 'tesimonial.all', 'Tesimonial', 'web', '2023-07-08 13:19:34', '2023-07-08 13:19:34'),
(19, 'tesimonial.add', 'Tesimonial', 'web', '2023-07-08 13:19:42', '2023-07-08 13:19:42'),
(20, 'tesimonial.edit', 'Tesimonial', 'web', '2023-07-08 13:19:49', '2023-07-08 13:19:49'),
(21, 'tesimonial.delete', 'Tesimonial', 'web', '2023-07-08 13:19:56', '2023-07-08 13:19:56'),
(22, 'blog.menu', 'Blog', 'web', '2023-07-08 13:20:02', '2023-07-08 13:20:02'),
(23, 'blog.category', 'Blog', 'web', '2023-07-08 13:20:09', '2023-07-08 13:20:09'),
(24, 'blog.post.list', 'Blog', 'web', '2023-07-08 13:20:16', '2023-07-08 13:20:16'),
(25, 'comment.menu', 'Manage Comment', 'web', '2023-07-08 13:20:28', '2023-07-08 13:20:28'),
(26, 'booking.report.menu', 'Booking Report', 'web', '2023-07-08 13:20:38', '2023-07-08 13:20:38'),
(27, 'gallery.menu', 'Hotel Gallery', 'web', '2023-07-08 13:20:47', '2023-07-08 13:20:47'),
(28, 'contact.message.menu', 'Contact Message', 'web', '2023-07-08 13:20:53', '2023-07-08 13:21:08'),
(29, 'role.permission.menu', 'Role and Permission', 'web', '2023-07-08 13:21:17', '2023-07-08 13:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2023-07-08 13:33:26', '2023-07-08 13:33:26'),
(2, 'Admin', 'web', '2023-07-08 13:33:36', '2023-07-08 13:33:36'),
(3, 'Receptionist', 'web', '2023-07-08 13:33:54', '2023-07-08 13:33:54'),
(4, 'Manager', 'web', '2023-07-08 13:34:02', '2023-07-08 13:34:02');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 3),
(1, 4),
(3, 1),
(3, 4),
(4, 1),
(4, 3),
(4, 4),
(5, 1),
(5, 4),
(6, 1),
(6, 4),
(7, 1),
(7, 3),
(8, 1),
(8, 3),
(9, 1),
(10, 1),
(11, 1),
(11, 3),
(12, 1),
(12, 3),
(13, 1),
(13, 3),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(17, 4),
(18, 1),
(18, 4),
(19, 1),
(19, 4),
(20, 1),
(20, 4),
(21, 1),
(21, 4),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(26, 2),
(26, 3),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(28, 3),
(29, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `roomtype_id` int(11) NOT NULL,
  `total_adult` varchar(255) DEFAULT NULL,
  `total_child` varchar(255) DEFAULT NULL,
  `room_capacity` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `view` varchar(255) DEFAULT NULL,
  `bed_style` varchar(255) DEFAULT NULL,
  `discount` int(11) NOT NULL DEFAULT 0,
  `short_desc` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `roomtype_id`, `total_adult`, `total_child`, `room_capacity`, `image`, `price`, `size`, `view`, `bed_style`, `discount`, `short_desc`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, '1', '0', '4', '1768165755633554.jpg', '100', '120', 'Sea View', 'Queen Bed', 0, 'Our Premier Single Rooms are designed with today’s business travelers in mind.', '<p>Our Premier Single Rooms are designed with today&rsquo;s business travelers in mind. The delightful interior, softened by yellow accents, create an ultra-contemporary space. The smart location of our hotel, one plot away from the main road, means you will have silent and peaceful nights, found rarely at Dhaka.</p>\n<p>Balancing productivity and relaxation in equal measure, Premier Single Rooms include a softly-lit desk, ideal for reading the daily newspaper or using your notebook on. Meanwhile complimentary wireless High Speed Internet Access offers a home-like setting to do your chores.</p>\n<table style=\"border-collapse: collapse; width: 99.963%;\" border=\"1\"><colgroup><col style=\"width: 50%;\"><col style=\"width: 50%;\"></colgroup>\n<tbody>\n<tr>\n<td>\n<ul class=\"elementor-icon-list-items\">\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">1 Twin Bed</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Personal Amenities</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Free Wifi</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Complimentary Breakfast</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Cable TV</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Tea &amp; Coffee Making</span></li>\n</ul>\n</td>\n<td>\n<ul class=\"elementor-icon-list-items\">\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Work Desk</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Minibar</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Safety Box</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Hair Dryer</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Rain Shower</span></li>\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">24/7 Room Service</span></li>\n</ul>\n</td>\n</tr>\n</tbody>\n</table>', 1, NULL, '2023-06-08 13:59:02'),
(4, 6, '2', '1', '3', '1768166011445491.jpg', '120', '210', 'Sea View', 'Twin Bed', 10, 'Our spacious Super Premier Deluxe Rooms are designed with today’s business travelers in mind', '<p>Our spacious Super Premier Deluxe Rooms are designed with today&rsquo;s business travelers in mind. The delightful interior, softened by yellow accents, create an ultra-contemporary space. The smart location of our hotel, one plot away from the main road, means you will have silent and peaceful nights, found rarely at Dhaka.</p>\r\n<p>Balancing productivity and relaxation in equal measure, Premier Deluxe Rooms include a softly-lit desk, ideal for reading the daily newspaper or using your notebook on. Meanwhile complimentary wireless High Speed Internet Access offers a home-like setting to do your chores.</p>\r\n<table style=\"border-collapse: collapse; width: 99.963%;\" border=\"1\"><colgroup><col style=\"width: 50%;\"><col style=\"width: 50%;\"></colgroup>\r\n<tbody>\r\n<tr>\r\n<td>\r\n<ul class=\"elementor-icon-list-items\">\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">1 Queen Bed</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Personal Amenities</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Free Wifi</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Complimentary Breakfast</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Cable TV</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Tea &amp; Coffee Making</span></li>\r\n</ul>\r\n</td>\r\n<td>\r\n<ul class=\"elementor-icon-list-items\">\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Work Desk</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Minibar</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Safety Box</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Hair Dryer</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Rain Shower</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">24/7 Room Service</span></li>\r\n</ul>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, NULL, '2023-06-08 14:01:52'),
(5, 7, '2', '1', '3', '1768166161393593.jpg', '130', '200', 'Sea View', 'Twin Bed', 0, 'Our Premier Deluxe Twin Rooms are designed with today’s business travelers in mind.', '<p>Our Premier Deluxe Twin Rooms are designed with today&rsquo;s business travelers in mind. The delightful interior, softened by yellow accents, create an ultra-contemporary space. The smart location of our hotel, one plot away from the main road, means you will have silent and peaceful nights, found rarely at Dhaka.</p>\r\n<p>Balancing productivity and relaxation in equal measure, Premier Deluxe Twin Rooms include a softly-lit desk, ideal for reading the daily newspaper or using your notebook on. Meanwhile complimentary wireless High Speed Internet Access offers a home-like setting to do your chores.</p>\r\n<table style=\"border-collapse: collapse; width: 99.963%;\" border=\"1\"><colgroup><col style=\"width: 50%;\"><col style=\"width: 50%;\"></colgroup>\r\n<tbody>\r\n<tr>\r\n<td>\r\n<ul class=\"elementor-icon-list-items\">\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">2 Twin Beds</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Personal Amenities</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Free Wifi</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Complimentary Breakfast</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Cable TV</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Tea &amp; Coffee Making</span></li>\r\n</ul>\r\n</td>\r\n<td>\r\n<ul class=\"elementor-icon-list-items\">\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Work Desk</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Minibar</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Safety Box</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Hair Dryer</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Rain Shower</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">24/7 Room Service</span></li>\r\n</ul>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, NULL, '2023-06-11 16:16:44'),
(7, 9, '3', '0', '4', '1768166331718043.jpg', '250', '300', 'Sea View', 'Twin Bed', 10, 'As cozy as your own home, the modern décor of our Executive Suite offers the ultimate in business travel.', '<p>As cozy as your own home, the modern d&eacute;cor of our Executive Suite offers the ultimate in business travel. The multi-functional living space provides a king-size bed in the bedroom, and a separate living area for small group discussions or meetings. Enveloped by calming colors, let your mind drift off as you melt into one of our plush sofas.</p>\r\n<p>The smart location of our hotel, one plot away from the main road, means you will have silent and peaceful nights, found rarely at Dhaka. Balancing productivity and relaxation in equal measure, Executive Suite Rooms include a softly-lit desk, ideal for reading the daily newspaper or using your notebook on.</p>\r\n<table style=\"border-collapse: collapse; width: 99.963%;\" border=\"1\"><colgroup><col style=\"width: 50%;\"><col style=\"width: 50%;\"></colgroup>\r\n<tbody>\r\n<tr>\r\n<td>\r\n<ul class=\"elementor-icon-list-items\">\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">1 King Bed</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Large Room - 37 SQM</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Free Wifi</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Complimentary Breakfast</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Cable TV</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Tea &amp; Coffee Making</span></li>\r\n</ul>\r\n</td>\r\n<td>\r\n<ul class=\"elementor-icon-list-items\">\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Work Desk</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Minibar</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Safety Box</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Hair Dryer</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Bath Tub + Rain Shower</span></li>\r\n<li class=\"elementor-icon-list-item\"><span class=\"elementor-icon-list-text\">Guest Washroom</span></li>\r\n</ul>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', 1, NULL, '2023-06-13 13:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `room_booked_dates`
--

CREATE TABLE `room_booked_dates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `book_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_booked_dates`
--

INSERT INTO `room_booked_dates` (`id`, `booking_id`, `room_id`, `book_date`, `created_at`, `updated_at`) VALUES
(1, 1, 4, '2023-06-16', '2023-06-14 05:54:20', '2023-06-14 05:54:20'),
(2, 1, 4, '2023-06-17', '2023-06-14 05:54:20', '2023-06-14 05:54:20'),
(3, 2, 7, '2023-06-20', '2023-06-14 05:58:28', '2023-06-14 05:58:28'),
(4, 2, 7, '2023-06-21', '2023-06-14 05:58:28', '2023-06-14 05:58:28'),
(5, 3, 4, '2023-06-20', '2023-06-17 15:17:23', '2023-06-17 15:17:23'),
(6, 3, 4, '2023-06-21', '2023-06-17 15:17:23', '2023-06-17 15:17:23'),
(7, 3, 4, '2023-06-22', '2023-06-17 15:17:23', '2023-06-17 15:17:23'),
(8, 4, 5, '2023-06-22', '2023-06-17 15:20:38', '2023-06-17 15:20:38'),
(9, 4, 5, '2023-06-23', '2023-06-17 15:20:38', '2023-06-17 15:20:38'),
(17, 5, 7, '2023-06-21', '2023-06-19 14:42:06', '2023-06-19 14:42:06'),
(18, 5, 7, '2023-06-22', '2023-06-19 14:42:06', '2023-06-19 14:42:06'),
(19, 5, 7, '2023-06-23', '2023-06-19 14:42:06', '2023-06-19 14:42:06'),
(20, 5, 7, '2023-06-24', '2023-06-19 14:42:06', '2023-06-19 14:42:06'),
(21, 5, 7, '2023-06-25', '2023-06-19 14:42:06', '2023-06-19 14:42:06'),
(22, 6, 1, '2023-06-25', '2023-06-21 15:17:55', '2023-06-21 15:17:55'),
(23, 6, 1, '2023-06-26', '2023-06-21 15:17:55', '2023-06-21 15:17:55'),
(24, 7, 4, '2023-07-05', '2023-07-04 15:04:30', '2023-07-04 15:04:30'),
(25, 7, 4, '2023-07-06', '2023-07-04 15:04:30', '2023-07-04 15:04:30'),
(26, 8, 7, '2023-07-19', '2023-07-04 15:04:53', '2023-07-04 15:04:53'),
(27, 8, 7, '2023-07-20', '2023-07-04 15:04:53', '2023-07-04 15:04:53'),
(28, 9, 1, '2023-07-27', '2023-07-04 15:05:23', '2023-07-04 15:05:23'),
(29, 9, 1, '2023-07-28', '2023-07-04 15:05:23', '2023-07-04 15:05:23'),
(30, 9, 1, '2023-07-29', '2023-07-04 15:05:23', '2023-07-04 15:05:23'),
(31, 10, 5, '2023-07-19', '2023-07-04 15:06:54', '2023-07-04 15:06:54'),
(32, 10, 5, '2023-07-20', '2023-07-04 15:06:54', '2023-07-04 15:06:54'),
(33, 11, 5, '2023-07-24', '2023-07-04 15:07:25', '2023-07-04 15:07:25'),
(34, 11, 5, '2023-07-25', '2023-07-04 15:07:25', '2023-07-04 15:07:25'),
(35, 12, 4, '2023-07-14', '2023-07-06 16:04:59', '2023-07-06 16:04:59'),
(36, 12, 4, '2023-07-15', '2023-07-06 16:04:59', '2023-07-06 16:04:59'),
(37, 13, 4, '2023-07-19', '2023-07-07 12:10:55', '2023-07-07 12:10:55'),
(38, 13, 4, '2023-07-20', '2023-07-07 12:10:55', '2023-07-07 12:10:55'),
(39, 14, 1, '2023-07-17', '2023-07-07 12:14:21', '2023-07-07 12:14:21'),
(40, 15, 5, '2023-07-19', '2023-07-07 13:08:02', '2023-07-07 13:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `room_numbers`
--

CREATE TABLE `room_numbers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rooms_id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_no` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_numbers`
--

INSERT INTO `room_numbers` (`id`, `rooms_id`, `room_type_id`, `room_no`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '201', 'Active', '2023-06-04 15:49:36', '2023-06-04 15:49:36'),
(3, 1, 3, '203', 'Active', '2023-06-04 15:50:48', '2023-06-04 15:50:48'),
(4, 1, 3, '204', 'Active', '2023-06-04 15:57:40', '2023-06-04 15:57:40'),
(10, 1, 3, '202', 'Active', '2023-06-08 13:59:29', '2023-06-08 13:59:29'),
(11, 4, 6, '301', 'Active', '2023-06-08 14:02:06', '2023-06-08 14:02:06'),
(12, 4, 6, '302', 'Active', '2023-06-08 14:02:15', '2023-06-08 14:02:15'),
(13, 4, 6, '303', 'Active', '2023-06-08 14:02:22', '2023-06-08 14:02:22'),
(14, 5, 7, '401', 'Active', '2023-06-08 14:04:30', '2023-06-08 14:04:30'),
(15, 5, 7, '402', 'Active', '2023-06-08 14:04:37', '2023-06-08 14:04:37'),
(16, 5, 7, '403', 'Active', '2023-06-08 14:04:43', '2023-06-08 14:04:43'),
(17, 7, 9, '501', 'Active', '2023-06-08 14:07:14', '2023-06-08 14:07:14'),
(18, 7, 9, '502', 'Active', '2023-06-08 14:07:20', '2023-06-08 14:07:20'),
(19, 7, 9, '503', 'Active', '2023-06-08 14:07:26', '2023-06-08 14:07:26'),
(20, 7, 9, '504', 'Active', '2023-06-08 14:07:33', '2023-06-08 14:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(3, 'PREMIER SINGLE', '2023-06-02 14:34:37', NULL),
(6, 'SUPER PREMIER DELUXE ROOM', '2023-06-08 14:00:00', NULL),
(7, 'PREMIER DELUXE TWIN ROOM', '2023-06-08 14:02:50', NULL),
(9, 'EXECUTIVE SUITE ROOM', '2023-06-08 14:05:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `logo`, `phone`, `address`, `email`, `facebook`, `twitter`, `copyright`, `created_at`, `updated_at`) VALUES
(1, 'upload/site/1770528607651701.png', '+1609-453-1654', '2504 Ivins Avenue, Egg Harbor Township, NJ 08234', 'support@easylearningbd.com', 'https://www.facebook.com/test222', 'https://twitter.com/hotel', 'Copyright © 2023 Easy Hotel All Rights Reserved.', NULL, '2023-07-04 15:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mailer` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `port` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `encryption` varchar(255) DEFAULT NULL,
  `from_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smtp_settings`
--

INSERT INTO `smtp_settings` (`id`, `mailer`, `host`, `port`, `username`, `password`, `encryption`, `from_address`, `created_at`, `updated_at`) VALUES
(1, 'smtp', 'sandbox.smtp.mailtrap.io', '2525', '007307d810463b', '46dad6d8da8939', 'tls', 'support@easylearningbd.com', NULL, '2023-06-22 15:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `postion` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `image`, `name`, `postion`, `facebook`, `created_at`, `updated_at`) VALUES
(1, 'upload/team/1768167899070419.png', 'Ariyan', 'CEO', 'https://www.facebook.com/ariyan', '2023-06-08 14:31:52', '2023-06-08 14:31:52'),
(4, 'upload/team/1768167927328351.png', 'Jony Rai', 'Manager', 'https://www.facebook.com/jonyrai', '2023-06-08 14:47:20', '2023-06-08 14:47:20'),
(5, 'upload/team/1768167966839305.png', 'Sultana', 'CTO', 'https://www.facebook.com/sultana', '2023-06-08 14:32:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `city`, `image`, `message`, `created_at`, `updated_at`) VALUES
(1, 'Ariyan', 'India', 'upload/testimonial/1769798520205768.jpg', 'This is one of the best & quality full hotels in the world that will help you to make a good market. This is one of the best & quality full hotels in the world that will help you to make an excellent study market.', '2023-06-26 14:49:27', '2023-06-26 14:49:27'),
(2, '2Khan', '2Usa', 'upload/testimonial/1769798508526937.jpg', 'This is one of the best & quality full hotels in the world that will help you to make a good market. This is one of the best & quality full hotels in the world that will help you to make an excellent study market.', '2023-06-26 14:49:31', '2023-06-26 14:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `photo`, `phone`, `address`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$10$5exAQbdDMgXRYLxc1Ep2eO4WzIgRHa4gmAgG/KCO2LR7Kw8dWq1oW', '202305262209avatar-2.png', '5566', 'Usa', 'admin', 'active', NULL, NULL, '2023-05-27 13:50:27'),
(2, 'User', 'user@gmail.com', NULL, '$2y$10$gmnjhJgPvs8ioqNYEppjVOVk0Z2DtChL8lR64rZ1xQABb7y3agkIO', '202305292149avatar-5.png', '01711', 'India', 'user', 'active', 'N34cQl5KlidtdNuiLt6T4kXGAYgAgA7WTy2PPABKrqE0eFUSJjzcwXhLSODS', NULL, '2023-05-29 16:25:14'),
(3, 'Khan', 'khan@gmail.com', NULL, '$2y$10$6yCVFR.Os6r9AGdTe7Gc/uEMt9o8YW8vKKw8Tt/sD3tCaCyr2jqCS', '202307042106avatar-6.png', '3434', 'India', 'user', 'active', NULL, '2023-05-27 15:38:22', '2023-07-04 15:06:23'),
(5, 'udemy', 'udemy@gmail.com', NULL, '$2y$10$5exAQbdDMgXRYLxc1Ep2eO4WzIgRHa4gmAgG/KCO2LR7Kw8dWq1oW', '202307092043avatar-4.png', '23231', 'USA1', 'admin', 'active', NULL, '2023-07-09 13:51:19', '2023-07-09 14:43:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_room_lists`
--
ALTER TABLE `booking_room_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_areas`
--
ALTER TABLE `book_areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `multi_images`
--
ALTER TABLE `multi_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

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
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

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
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_booked_dates`
--
ALTER TABLE `room_booked_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_room_lists`
--
ALTER TABLE `booking_room_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `book_areas`
--
ALTER TABLE `book_areas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `multi_images`
--
ALTER TABLE `multi_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `room_booked_dates`
--
ALTER TABLE `room_booked_dates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smtp_settings`
--
ALTER TABLE `smtp_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
