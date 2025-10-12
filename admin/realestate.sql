-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2025 at 02:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `realestate`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(200) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `photo`, `created_at`) VALUES
(2, 'admin', 'admin@mail.com', '$2y$10$QOrbctdkynoxujMaGL9Hc.KSYjeLX6V/oQYQ24hoAavmyee8sB76G', '1758718449_business-8788630_1920.jpg', '2025-08-25 08:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `name`, `email`, `phone`, `photo`, `trade_license`, `document`, `password`, `created_at`) VALUES
(1, 'Jolshiri', 'jolshiri@mail.com', '01834028094', '1758628719_business-8788630_1920.jpg', NULL, NULL, '', '2025-08-21 12:41:14'),
(3, 'PWAD', 'iam@mail.com', '5405654', '1758628456_pexels-mikhail-nilov-7736041.jpg', 'BD123456789', NULL, '$2y$10$nSyeXnGprxq7ubfUdX8Ec.5AkdOtmlU0HRUZaGsuYkK7M25xRdbCa', '2025-08-28 11:52:10'),
(5, 'Ghost Rayan', 'agent@mail.com', '01341007502', 'uploads/agents/1759061843_architecture-real-estate-building-concept.jpg', 'BD1234567899', NULL, '$2y$10$B9ZdQzS0zkNbtoJXqDGaUOeYDa3lfe5tMuzJager4p/KeUic436dy', '2025-09-01 09:45:42'),
(7, 'Concord', 'admin@admin.com', '+1 (165) 222-1657', '1758628684_business-8788621_1920.jpg', 'BD12345678', NULL, '$2y$10$jJR9ESdp.i4EQ4HVOXmR3.xHhOgvrhuj4kX8Iqp.uwMX2kuNudcIu', '2025-09-01 09:57:57'),
(8, 'Fiona French', 'vyhetekupo@mailinator.com', '+1 (798) 702-9762', '1758628438_pexels-mikhail-nilov-7736041.jpg', 'uploads/docs/1757325307_webaliser-_TPTXZd9mOo-unsplash.jpg', NULL, '$2y$10$qRa3gNvp0ZL1qhGKBXvtD.laMx0ims8xnbCzwu9kX8AJ7UT4sPuvW', '2025-09-08 09:55:07'),
(9, 'Lev Stafford', 'teqezev@mailinator.com', '+1 (798) 702-9762', '1758628400_pexels-rdne-7821683.jpg', 'uploads/docs/1757325542_anime-style-character-space.jpg', NULL, '$2y$10$XSJpdVfTxmH9PNhlDBzXZ.mep0BueeCqZRymNOVzKsOJxJ3vlnD7C', '2025-09-08 09:59:02'),
(10, 'Nasia Natila', 'afda@mail.com', '+1 (694) 422-7766', '1758628384_pexels-olly-864994.jpg', 'uploads/docs/1758098819_-1-mobile.jpg', NULL, '$2y$10$V3J0ywQjKrFitaiMSkhpIetpiPlhKYhSyOn68dP1VT9VG0vWXbOlG', '2025-09-17 08:46:59'),
(11, 'Chaim Burris', 'cysibof@mailinator.com', '+1 (798) 702-9762', '1758628247_pexels-mart-production-7415040.jpg', 'uploads/docs/1758109381_images1.jfif', NULL, '$2y$10$qWPVWJ8lRf5O.5.jSc.4EeJNUjJL5I6pGdhQVQYf1AB0/UpI25Tfm', '2025-09-17 11:43:01'),
(12, 'Idola Hendrix', 'kuhukyj@mailinator.com', '01834028094', '1758628232_pexels-kampus-8815915.jpg', 'uploads/docs/1758109570_breno-assis-r3WAWU5Fi5Q-unsplash.jpg', NULL, '$2y$10$0OeorIfCkCniSaZZderS7uhP9W1CZSh0WVBkJ2OpxiFa8l0x.0wU.', '2025-09-17 11:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `content`, `photo`, `created_at`) VALUES
(1, 'introducing-ai-seo-specialist', 'While businesses struggle to keep pace with constantly evolving search algorithms and the emergence of AI-powered search engines, a revolutionary solution has emerged that transforms SEO from a manual, time-intensive process into an intelligent, self-managing system.\r\n\r\nEnter the AI SEO Specialist, part of Luxury Presenceâ€™s transformative lineup of AI Marketing Specialists, bringing expert-level search engine optimization with minimal oversight. In just the past 30 days, this powerful tool has executed over 4,000 individual optimizations across our platform, driving measurable results while freeing you to focus on growing your business.\r\n\r\nWeâ€™ll walk you through how it works, what it looks like in action, how we measure success, and, most importantly, how it simplifies your life and drives your business growth.', '9fb320980e5c372e_1759060969.jpg', '2025-09-24 12:02:15'),
(2, 'introducing-ai-seo-specialist', 'While businesses struggle to keep pace with constantly evolving search algorithms and the emergence of AI-powered search engines, a revolutionary solution has emerged that transforms SEO from a manual, time-intensive process into an intelligent, self-managing system.\r\n\r\nEnter the AI SEO Specialist, part of Luxury Presenceâ€™s transformative lineup of AI Marketing Specialists, bringing expert-level search engine optimization with minimal oversight. In just the past 30 days, this powerful tool has executed over 4,000 individual optimizations across our platform, driving measurable results while freeing you to focus on growing your business.\r\n\r\nWeâ€™ll walk you through how it works, what it looks like in action, how we measure success, and, most importantly, how it simplifies your life and drives your business growth.', '6469b0c1d1f1b8d2_1759061165.webp', '2025-09-24 12:03:10'),
(3, 'introducing-ai-seo-specialist', 'While businesses struggle to keep pace with constantly evolving search algorithms and the emergence of AI-powered search engines, a revolutionary solution has emerged that transforms SEO from a manual, time-intensive process into an intelligent, self-managing system.\r\n\r\nEnter the AI SEO Specialist, part of Luxury Presenceâ€™s transformative lineup of AI Marketing Specialists, bringing expert-level search engine optimization with minimal oversight. In just the past 30 days, this powerful tool has executed over 4,000 individual optimizations across our platform, driving measurable results while freeing you to focus on growing your business.\r\n\r\nWeâ€™ll walk you through how it works, what it looks like in action, how we measure success, and, most importantly, how it simplifies your life and drives your business growth.', '82aee856848eca7f_1759060947.jpg', '2025-09-24 12:04:33'),
(4, 'introducing-ai-seo-specialist', 'While businesses struggle to keep pace with constantly evolving search algorithms and the emergence of AI-powered search engines, a revolutionary solution has emerged that transforms SEO from a manual, time-intensive process into an intelligent, self-managing system.\r\n\r\nEnter the AI SEO Specialist, part of Luxury Presenceâ€™s transformative lineup of AI Marketing Specialists, bringing expert-level search engine optimization with minimal oversight. In just the past 30 days, this powerful tool has executed over 4,000 individual optimizations across our platform, driving measurable results while freeing you to focus on growing your business.\r\n\r\nWeâ€™ll walk you through how it works, what it looks like in action, how we measure success, and, most importantly, how it simplifies your life and drives your business growth.', 'b80b83801c5463bc_1759060707.jpg', '2025-09-24 12:05:25'),
(5, 'Do you have a handful of blogs that you follow purely for fun?', 'Do you have a handful of blogs that you follow purely for fun?\r\n\r\nOver the past few years, Iâ€™ve moved away from following primarily recipe blogs to spending my time enjoying other types of lifestyle blogs, and it has been awesome. I have blog categories on Feedly for photographers I admire, cool people working for social justice, interesting perspectives on faith, DIY stuff I actually want to try, you name it! Mostly theyâ€™re just blogs that have nothing to do with recipes, and I find that I look forward to reading them more than ever. But that said, the blogs in my reader that get the most clicks by a landslide are in my favorite category that I have oh-so-creatively named â€” get ready for it â€” â€œfunâ€.\r\n\r\nAnd they are just that â€” blogs I find fun! Some I have followed for years and years, some are pretty new to me. Some deal with 100% light and fluffy stuff, some dig a little deeper into the meat of life. Some of them share the most beautiful prose, some are random and chatty and full of incomplete sentences. But I love them all, and can genuinely say that I look forward to reading them most mornings (after I read The Skimm, of course, which Iâ€™m still obsessed with), and also genuinely admire each of the bloggers behind them. â™¥\r\n\r\nSo if any of you are on the prowl for some new non-recipe blogs, I thought Iâ€™d share 7 of my favorites with you today. Iâ€™m guessing youâ€™ll be familiar with a few, but Iâ€™m hoping there might be a few new gems in there for you to discover. And hey â€” if you have some favorites of your own, pretty please share them in the comments below, because I totally want to check â€™em out.', '94ef0d8788777a14_1758969371.jpg', '2025-09-27 10:19:19'),
(6, 'Do you have a handful of blogs that you follow purely for fun?', 'Do you have a handful of blogs that you follow purely for fun?Do you have a handful of blogs that you follow purely for fun?Do you have a handful of blogs that you follow purely for fun?Do you have a handful of blogs that you follow purely for fun?Do you have a handful of blogs that you follow purely for fun?Do you have a handful of blogs that you follow purely for fun?Do you have a handful of blogs that you follow purely for fun?', '47431468398eb3b0_1758969261.jpg', '2025-09-27 10:24:55');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `agent_id`, `name`, `email`, `photo`, `phone`, `created_at`, `password`) VALUES
(1, 5, 'Scarlet Herrera', 'bivirefan@mailinator.com', NULL, '+1 (595) 875-1756', '2025-09-17 09:35:21', ''),
(4, 1, 'Britanney Adams', 'qohikepezo@mailinator.com', NULL, NULL, '2025-09-17 09:38:24', '$2y$10$q8Lvabm7vnn3oHvyVOhRNONnUoNoiswSDdLeVE606ow.8A1jBPLn2'),
(5, 1, 'Jenna Espinoza', 'nuxuxel@mailinator.com', NULL, NULL, '2025-09-17 09:38:59', '$2y$10$EJr5ggbU1Xp6zX2u.pEJQOVT6RWwv/p9lBDo4fZ/Eb2gSM7jfCZie'),
(6, 1, 'Stuart Randall', 'figobeko@mailinator.com', NULL, NULL, '2025-09-17 09:41:19', '$2y$10$fNS/FX9b1FT4l1V5enaUXegKvE3oja812ST1505NnQJXO4p0R8cJ.'),
(7, 1, 'Heidi Fitzgerald', 'rotaf@mailinator.com', NULL, NULL, '2025-09-17 09:47:25', '$2y$10$Lfgjt1iAXRecvISMaVZ.kOdb2lEKvbGrQXNLnVeGYfybmnxoHOOR.'),
(8, 1, 'Sayed Mishra', 'client@mail.com', 'uploads/clients/1758198031_-1-mobile.jpg', NULL, '2025-09-17 09:48:50', '$2y$10$bW.AvCr/L576aba29vzmJevQPjvhv37Wu7yguTzFaDM4/cGVjQvQe'),
(9, 1, 'Kylee Dorsey', 'vazofet@mailinator.com', NULL, NULL, '2025-09-17 09:50:40', '$2y$10$KxjjT6qcJRWl7Zc81fKSMuVZbUNtOiyjoeJl.MEbF/3zKX0drOBUG'),
(10, 1, 'Graham Cooley', 'motofij@mailinator.com', NULL, NULL, '2025-09-17 09:51:14', '$2y$10$I6TX5N4tJAMXT8PKKLJg6uDEl47Lnupsf.Dm7fUAS5QN0XCPWAV8G'),
(11, 1, 'fdfeqfr', 'ereqqfd@kfdkfjdl.com', NULL, NULL, '2025-09-17 09:54:11', '$2y$10$hBj1H7oKqKNBiNXEv7bMguWAMnjnE5WUcggwQFjiioM4eQWSIee22'),
(12, 1, 'Parsons', 'robertrhart@jourrapide.com', NULL, NULL, '2025-09-17 10:43:14', '$2y$10$M9xvdESDegwH/a6p2BPH9.6C1DJUnCgte2zN55IARPk9Q62ty0hzS'),
(13, 1, 'booo', 'robertrart@jourrapide.com', NULL, NULL, '2025-09-17 10:44:26', '$2y$10$IupDfzsSK6B4lqy9Y5S8SepeoCIoJu4ALo1JBNe.lkpPU8uy6128i'),
(14, 1, 'joigjiowrjif', 'adfdafqe@mail.com', NULL, NULL, '2025-09-17 10:54:10', '$2y$10$rTOTxtqxTewwv4rKKkyUaOCbjx7b7oHxvBDJUIzjjyKBqGMATf2mW'),
(15, 1, 'Zane Sharpe', 'zywig@mailinator.com', NULL, NULL, '2025-09-17 11:03:18', '$2y$10$14JbgRlzXrO2FDFP9jrvR.mGiZHSvtqmWW0kiU6jUYQbwfm1pfySe'),
(16, 1, 'Saqline', 'saqline@mail.com', NULL, NULL, '2025-09-17 11:47:36', '$2y$10$THgEaFVuxaGNABJrZKDORedhTsZ.gkqXEE037vp4w0A73ZJ/IDbZm'),
(17, 1, 'Carolyn Hensley', 'dokibiqalu@mailinator.com', NULL, NULL, '2025-09-17 12:54:35', '$2y$10$0ccqPGFl3UAZltk5Pg007.rtbGVYYCxT0NrTgY31E5vpBcpRWrhia'),
(18, 1, 'Gloria Haley', 'woqejoxux@mailinator.com', NULL, NULL, '2025-09-17 12:54:45', '$2y$10$d/sf9Ue0OvwpaOGkz3OoN.FsHVZbpu364E4.k1dIyc.7HdNoE.At6'),
(20, 5, 'Saqline Hasan', 'saaqline@mail.com', NULL, '123545', '2025-09-28 12:16:08', '');

-- --------------------------------------------------------

--
-- Table structure for table `concerns`
--

CREATE TABLE `concerns` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `url` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `target_blank` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `concerns`
--

INSERT INTO `concerns` (`id`, `name`, `url`, `logo`, `description`, `sort_order`, `target_blank`, `is_active`, `created_at`) VALUES
(1, 'Home Interior', 'https://bdinterior.net', 'd4215b07716aa1b8_1758977020.jpg', 'A strong description for an interior design company emphasizes its ability to transform spaces into functional, beautiful, and personalized environments by combining creativity, client-focused service, and a deep understanding of design principles. It should clearly state the company\'s unique selling proposition, whether it\'s a full-service approach, specialized expertise in a certain style, or a focus on a particular type of client or project. Including details about its process, the services offered (from consultation to project management), and what makes it stand out from competitors helps clients envision the outcome and understand the value provided', 1, 1, 1, '2025-09-27 12:39:39'),
(2, 'Car Dealer', 'https://cardeal.ge/en', '0b5751d684d11a7f_1758977785.webp', 'A good car selling description includes the vehicle\'s make, model, year, and mileage at the top, followed by details like fuel type, color, and owner count. Be honest about its condition, mentioning any significant damage and also highlight any positive aspects like a full service history (FSH) or desirable optional extras. For a quick sale, include your price, contact information, and as many high-quality photos as possible.', 0, 1, 1, '2025-09-27 12:56:25');

-- --------------------------------------------------------

--
-- Table structure for table `concern_groups`
--

CREATE TABLE `concern_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `concern_groups`
--

INSERT INTO `concern_groups` (`id`, `name`, `slug`, `description`, `color`, `is_active`, `created_at`) VALUES
(1, 'Tour', 'tour', 'hula hula', '#0d6efd', 1, '2025-09-27 11:56:03');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`id`, `client_id`, `property_id`, `description`, `status`, `created_at`) VALUES
(1, 8, 11, 'Water supply issue', 'pending', '2025-09-25 10:40:47');

-- --------------------------------------------------------

--
-- Table structure for table `mortgage_calculations`
--

CREATE TABLE `mortgage_calculations` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `property_id` int(11) DEFAULT NULL,
  `principal` decimal(15,2) DEFAULT NULL,
  `interest_rate` decimal(5,2) DEFAULT NULL,
  `years` int(11) DEFAULT NULL,
  `monthly_payment` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` varchar(100) NOT NULL,
  `contact` varchar(12) NOT NULL,
  `manufacture_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('Available','Sold','Rented','Ongoing') DEFAULT 'Available',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `category_id`, `name`, `location`, `agent_id`, `client_id`, `price`, `photo`, `status`, `description`, `created_at`) VALUES
(2, 3, 'Concord', 'Gulshan', 1, NULL, 800000.00, '1755943756_new-buildings-with-green-areas.jpg', 'Rented', '', '2025-08-23 10:09:16'),
(3, 2, 'Charulota', 'Gulshan-2', 1, NULL, 2185000.00, NULL, 'Available', 'Charulata is ideally situated only five minutesâ€™ drive away from Baridhara and Gulshan 2, offering tranquility together with the suburban romance adjacent to the bustling metropolis.', '2025-08-26 10:03:18'),
(4, 3, 'Ocen City', 'Banani', 1, NULL, 25900000.00, NULL, 'Available', 'Each of the floors designed in a unique way which gives you the peace you find in nature while enjoying the contemporary look of the houses.\r\n\r\n\r\nUnit Size\r\nTYPE A - 2230 sft, 2254 sft & 2649 sft\r\nTYPE B - 2199-2206 sft', '2025-08-26 10:09:32'),
(5, 1, 'Purbachal New Town', 'Purbachal', 1, NULL, 1360000.00, NULL, 'Ongoing', 'Purbachal Project is located at Rupganj Upazila of Narayangonj district and the rest of it is in Kaliganj Upazila of Gazipur district, with the river Balu to the west and the river Sitalakhya to the east. It is 16 KM away from the zero point of Dhaka. The total area is 6227 acres.', '2025-08-26 10:13:37'),
(6, 4, 'Rupayon City', 'Kanchpur', 1, NULL, 1200000.00, NULL, 'Available', 'Redeem Rupanyan City Ltd gives you the opportunity of buying 5 Katha plots in Rupayan town. They come in 3 different types- General Plots, South Facing Plots, and Road Corner Plots, and these lands are available for sale in installments as well.\r\n\r\nOrientation of the plots and their position gives you further freedom to choose from a lot of many options.', '2025-08-26 10:18:56'),
(7, 2, 'Kashful Villa', 'Dhanmondi', 5, NULL, 120000.00, NULL, 'Available', '', '2025-09-01 12:48:00'),
(8, 5, 'The HillTop', 'Uttara Central', 5, NULL, 1200.99, NULL, 'Available', '', '2025-09-04 09:56:25'),
(9, 2, 'Paloma Mack', 'Tenetur beatae venia', 5, NULL, 424.00, 'uploads/properties/1756981367_point3d-commercial-imaging-ltd-HOjvz4GY1K8-unsplash.jpg', 'Available', '', '2025-09-04 10:22:47'),
(10, 3, 'Bree Burke', 'Eaque et nulla quaer', 5, NULL, 50.00, 'uploads/properties/1756981383_aerial-view-factory-trucks-parked-near-warehouse-daytime.jpg', 'Ongoing', '', '2025-09-04 10:23:03'),
(11, 3, 'Hotel Amania', 'Cox\'s Bazar', 5, NULL, 150000.00, NULL, 'Available', '', '2025-09-04 10:56:47'),
(12, 2, 'Joldhar Bhaban', 'Gulshan', 5, NULL, 120000.00, 'uploads/properties/1758362397_breno-assis-r3WAWU5Fi5Q-unsplash.jpg', 'Available', '', '2025-09-20 09:59:57'),
(13, 2, 'Blue Sky Realty', 'Uttara Central', 5, NULL, 1500000.00, 'uploads/properties/1759061593_pexels-pixabay-53610.jpg', 'Available', NULL, '2025-09-28 12:13:13'),
(14, 5, 'DreamHaven Homes', 'Purbachal', 5, NULL, 13000000.00, 'uploads/properties/1759061691_industrial-rustic-living-room-in-earthy-tones.jpg', 'Available', NULL, '2025-09-28 12:14:51');

--
-- Triggers `properties`
--
DELIMITER $$
CREATE TRIGGER `sold_property_end_rentals` AFTER UPDATE ON `properties` FOR EACH ROW BEGIN
  -- If status changed to Sold
  IF NEW.status = 'Sold' AND OLD.status <> 'Sold' THEN
    UPDATE rented
    SET rented_end_date = CURDATE()
    WHERE property_id = NEW.id
      AND (rented_end_date IS NULL OR rented_end_date >= CURDATE());
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `property_categories`
--

CREATE TABLE `property_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `property_categories`
--

INSERT INTO `property_categories` (`id`, `name`, `description`, `photo`, `created_at`) VALUES
(1, 'Land Area Space', 'Realize your financial goals through smart real estate investments.', 'cat_68ac4cec94d6c0.20282711.jpg', '2025-08-24 11:59:00'),
(2, 'Residential Area', 'Your listings, our passion for strategic real estate marketing', 'cat_68ac4f8f5a95e5.05379893.jpg', '2025-08-24 12:00:14'),
(3, 'Commercial Area', 'Creating the foundation for your business through commercial real estate', 'cat_68ac4cd90dfec9.67207135.jpg', '2025-08-24 12:01:08'),
(4, 'Industrial Area', 'Focus on a Benefit, not a Feature', 'cat_68ac4f7e101250.36139628.jpg', '2025-08-24 12:02:30'),
(5, 'Rental Area', 'All modern amenities, safety and healthy living', 'cat_68ac4c1a0daa35.20364239.jpg', '2025-08-25 11:42:02'),
(6, 'Ongoing', '', 'cat_68c80528cb6f66.01463247.jpg', '2025-09-15 12:23:04');

-- --------------------------------------------------------

--
-- Table structure for table `property_photos`
--

CREATE TABLE `property_photos` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `property_photos`
--

INSERT INTO `property_photos` (`id`, `property_id`, `photo`, `created_at`) VALUES
(1, 2, '1756198669_imgi_12_United-City.jpg', '2025-08-26 08:57:49'),
(2, 2, '1756198669_imgi_36_IMG_0026_Excutive-Building-1920x1280-1-1024x683.jpg', '2025-08-26 08:57:49'),
(3, 2, '1756198669_imgi_49_Land-Project.jpg', '2025-08-26 08:57:49'),
(4, 2, '1756198669_imgi_53_Land-Project-1536x640.jpg', '2025-08-26 08:57:49'),
(5, 3, 'prop_68ad86662dde34.43385438.jpg', '2025-08-26 10:03:18'),
(6, 3, 'prop_68ad8666520e49.14552384.jpg', '2025-08-26 10:03:18'),
(7, 3, 'prop_68ad86666a5f11.19213726.jpg', '2025-08-26 10:03:18'),
(8, 4, 'prop_68ad87dc53c7b4.03710966.jpg', '2025-08-26 10:09:32'),
(9, 5, 'prop_68ad88d17483e3.38134226.jpg', '2025-08-26 10:13:37'),
(10, 6, 'prop_68ad8a1081e995.60319988.jpg', '2025-08-26 10:18:56'),
(11, 11, 'prop_68be94104c8930.73269523.jpg', '2025-09-08 08:30:08'),
(12, 10, 'prop_68be9419689620.09178478.jpg', '2025-09-08 08:30:17'),
(13, 9, 'prop_68be9425cc2015.84214538.jpg', '2025-09-08 08:30:29'),
(14, 8, 'prop_68be94311ae816.58894863.jpg', '2025-09-08 08:30:41'),
(15, 7, 'prop_68bec9246d2098.85422190.jpg', '2025-09-08 12:16:36'),
(16, 12, 'prop_68ce80cfe44857.07469024.jpg', '2025-09-20 10:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `rented`
--

CREATE TABLE `rented` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `rented_start_date` date NOT NULL DEFAULT curdate(),
  `rented_end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rented`
--

INSERT INTO `rented` (`id`, `property_id`, `agent_id`, `client_id`, `category`, `rented_start_date`, `rented_end_date`, `created_at`) VALUES
(1, 7, 5, 7, 'House A', '2025-09-04', '2025-09-04', '2025-09-04 09:51:22'),
(2, 8, 5, 2, 'Apartment A', '2025-09-04', '2025-09-27', '2025-09-04 09:58:17'),
(3, 3, 5, 14, 'House A', '2025-09-04', '2025-10-08', '2025-09-04 10:23:58'),
(4, 3, 5, 14, 'House A', '2025-09-04', '2025-10-08', '2025-09-04 10:24:12'),
(5, 10, 5, 11, 'Apartment A', '2025-09-04', '2025-09-26', '2025-09-04 10:46:37'),
(6, 11, 5, 8, 'Commercial A', '2025-09-30', '2025-12-31', '2025-09-15 09:29:53'),
(7, 2, 5, 8, 'Apartment B', '2025-09-28', '2025-10-01', '2025-09-28 12:18:29');

--
-- Triggers `rented`
--
DELIMITER $$
CREATE TRIGGER `delete_rental_reset` AFTER DELETE ON `rented` FOR EACH ROW BEGIN
  -- Only update if property is not Sold
  UPDATE properties 
  SET status = 'Available'
  WHERE id = OLD.property_id
    AND status <> 'Sold';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `reset_property_available` AFTER UPDATE ON `rented` FOR EACH ROW BEGIN
  IF NEW.rented_end_date IS NOT NULL 
     AND NEW.rented_end_date < CURDATE() THEN
    -- Only update if property is not Sold
    UPDATE properties 
    SET status = 'Available'
    WHERE id = NEW.property_id
      AND status <> 'Sold';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `set_property_rented` AFTER INSERT ON `rented` FOR EACH ROW BEGIN
  -- Only update if property is not Sold
  UPDATE properties 
  SET status = 'Rented'
  WHERE id = NEW.property_id
    AND status <> 'Sold';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rented_photos`
--

CREATE TABLE `rented_photos` (
  `id` int(11) NOT NULL,
  `rented_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rented_photos`
--

INSERT INTO `rented_photos` (`id`, `rented_id`, `photo_path`, `uploaded_at`) VALUES
(1, 7, 'uploads/rented/1759061909_architecture-real-estate-building-concept.jpg', '2025-09-28 12:18:29');

-- --------------------------------------------------------

--
-- Table structure for table `saved_properties`
--

CREATE TABLE `saved_properties` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saved_properties`
--

INSERT INTO `saved_properties` (`id`, `client_id`, `property_id`, `created_at`) VALUES
(2, 8, 12, '2025-09-27 09:16:52'),
(3, 8, 11, '2025-09-27 09:16:54'),
(4, 8, 10, '2025-09-27 11:11:25');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `method` varchar(50) DEFAULT NULL,
  `payment_amount` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `property_id`, `client_id`, `agent_id`, `amount`, `date`, `status`, `method`, `payment_amount`) VALUES
(2, 3, 13, 3, 10321046.00, '2025-09-03 18:00:00', 'Completed', NULL, 0.00),
(3, 9, 8, 5, 21000.00, '2025-09-24 18:00:00', 'Completed', 'mastercard', 0.00),
(4, 3, 8, 5, 12200.00, '2025-09-21 18:00:00', 'Completed', 'bank', 0.00),
(5, 10, 8, 7, 50.00, '2025-09-26 18:00:00', 'Completed', 'paypal', 20.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `concerns`
--
ALTER TABLE `concerns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `concern_groups`
--
ALTER TABLE `concern_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `uq_concern_groups_name` (`name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `mortgage_calculations`
--
ALTER TABLE `mortgage_calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_id` (`category_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `idx_client_id` (`client_id`);

--
-- Indexes for table `property_categories`
--
ALTER TABLE `property_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `rented`
--
ALTER TABLE `rented`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rented_property` (`property_id`),
  ADD KEY `fk_rented_agent` (`agent_id`),
  ADD KEY `fk_rented_client` (`client_id`);

--
-- Indexes for table `rented_photos`
--
ALTER TABLE `rented_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rented_photos_rented` (`rented_id`);

--
-- Indexes for table `saved_properties`
--
ALTER TABLE `saved_properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`),
  ADD KEY `client_id` (`client_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `concerns`
--
ALTER TABLE `concerns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `concern_groups`
--
ALTER TABLE `concern_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mortgage_calculations`
--
ALTER TABLE `mortgage_calculations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `property_categories`
--
ALTER TABLE `property_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `property_photos`
--
ALTER TABLE `property_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `rented`
--
ALTER TABLE `rented`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rented_photos`
--
ALTER TABLE `rented_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saved_properties`
--
ALTER TABLE `saved_properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `maintenance_requests_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `mortgage_calculations`
--
ALTER TABLE `mortgage_calculations`
  ADD CONSTRAINT `mortgage_calculations_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `mortgage_calculations_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `property_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`);

--
-- Constraints for table `property_photos`
--
ALTER TABLE `property_photos`
  ADD CONSTRAINT `property_photos_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rented`
--
ALTER TABLE `rented`
  ADD CONSTRAINT `fk_rented_agent` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rented_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rented_property` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rented_photos`
--
ALTER TABLE `rented_photos`
  ADD CONSTRAINT `fk_rented_photos_rented` FOREIGN KEY (`rented_id`) REFERENCES `rented` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_properties`
--
ALTER TABLE `saved_properties`
  ADD CONSTRAINT `saved_properties_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_properties_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
