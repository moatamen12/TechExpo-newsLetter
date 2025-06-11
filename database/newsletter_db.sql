-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2025 at 06:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newsletter_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `summary` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','scheduled','sent') NOT NULL DEFAULT 'draft',
  `recipient_type` enum('all_followers','selected_followers','test') NOT NULL DEFAULT 'all_followers',
  `selected_subscribers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_subscribers`)),
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `total_sent` int(11) DEFAULT 0,
  `total_failed` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `author_id`, `title`, `content`, `summary`, `category_id`, `featured_image`, `status`, `recipient_type`, `selected_subscribers`, `scheduled_at`, `sent_at`, `total_sent`, `total_failed`, `created_at`, `updated_at`) VALUES
(12, 3, 'test if it works', '<p>test if it works test if it works</p>', 'test if it works test if it works', 5, 'featured_images/17496098816_Screenshot 2025-06-10 232433.png', 'draft', '', NULL, NULL, NULL, 0, 0, '2025-06-11 01:44:42', '2025-06-11 01:44:42'),
(13, 3, 'test if it works', '<p>test if it works test if it works</p>', 'test if it works test if it works', 5, 'featured_images/17496098896_Screenshot 2025-06-10 232433.png', 'draft', '', NULL, NULL, NULL, 0, 0, '2025-06-11 01:44:49', '2025-06-11 01:44:49'),
(14, 3, 'test if it works', '<p>test if it works test if it works</p>', 'test if it works test if it works', 5, 'featured_images/17496099026_Screenshot 2025-06-10 232433.png', 'draft', '', NULL, NULL, NULL, 0, 0, '2025-06-11 01:45:02', '2025-06-11 01:45:02'),
(15, 3, 'test if it works', '<p>test if it works test if it works</p>', 'test if it works test if it works', 5, 'featured_images/17496103206_Screenshot 2025-06-10 232433.png', 'draft', '', NULL, NULL, NULL, 0, 0, '2025-06-11 01:52:00', '2025-06-11 01:52:00'),
(16, 3, 'test if it works', '<p>test if it works test if it works</p>', 'test if it works test if it works', 5, 'featured_images/17496103246_Screenshot 2025-06-10 232433.png', 'draft', '', NULL, NULL, NULL, 0, 0, '2025-06-11 01:52:04', '2025-06-11 01:52:04'),
(17, 3, 'test if it works', '<p>test if it works test if it works</p>', 'test if it works test if it works', 5, 'featured_images/17496103386_Screenshot 2025-06-10 232433.png', 'sent', 'all_followers', NULL, NULL, '2025-06-11 02:09:30', 0, 0, '2025-06-11 01:52:18', '2025-06-11 02:09:30'),
(18, 3, 'test again', '<p>test again test again test again test again&nbsp;</p>', 'test again  test again test again  test again', 2, NULL, 'draft', 'all_followers', NULL, NULL, NULL, 0, 0, '2025-06-11 02:11:28', '2025-06-11 02:11:28'),
(19, 3, 'test Schedule Send', '<p>test Schedule Sendtest Schedule Sendtest Schedule Send</p>', 'test Schedule Send test Schedule Sendtest Schedule Send', 2, NULL, 'scheduled', 'all_followers', NULL, '2025-06-11 03:29:00', NULL, 0, 0, '2025-06-11 02:29:24', '2025-06-11 02:29:24'),
(20, 3, 'test Schedule Send', '<p>test Schedule Sendtest Schedule Send test Schedule Send</p>', 'test Schedule Send test Schedule Send', 2, NULL, 'sent', 'all_followers', NULL, NULL, '2025-06-11 02:51:36', 0, 0, '2025-06-11 02:32:59', '2025-06-11 02:51:36'),
(22, 3, 'test for specefec users', '<p>test for specefec users test for specefec users &nbsp;test for specefec users&nbsp;</p>', 'test for specefec users  test for specefec users', 2, NULL, 'draft', 'all_followers', NULL, NULL, NULL, 0, 0, '2025-06-11 03:19:35', '2025-06-11 03:19:35'),
(23, 3, 'test foe sum subscribers', '<p>test foe sum subscribers test foe sum subscribers &nbsp;test foe sum subscribers&nbsp;</p>', 'test foe sum subscribers test foe sum subscribers', 5, NULL, 'sent', 'selected_followers', '\"[\\\"45\\\"]\"', NULL, '2025-06-11 03:27:00', 0, 0, '2025-06-11 03:26:35', '2025-06-11 03:27:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `newsletter_author_id_foreign` (`author_id`),
  ADD KEY `newsletter_category_id_foreign` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD CONSTRAINT `newsletter_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `user_profiles` (`profile_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `newsletter_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
