-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 01, 2022 at 11:21 PM
-- Server version: 10.4.21-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wallmeter`
--

-- --------------------------------------------------------

--
-- Table structure for table `as_comments`
--

CREATE TABLE `as_comments` (
  `comment_id` int(11) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `posted_by_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `post_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `as_login_attempts`
--

CREATE TABLE `as_login_attempts` (
  `id_login_attempts` int(11) NOT NULL,
  `ip_addr` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT 1,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `as_social_logins`
--

CREATE TABLE `as_social_logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'email',
  `provider_id` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `as_users`
--

CREATE TABLE `as_users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `confirmation_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `confirmed` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_key` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password_reset_confirmed` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_timestamp` datetime DEFAULT NULL,
  `register_date` date NOT NULL,
  `user_role` int(4) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `banned` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `as_user_details`
--

CREATE TABLE `as_user_details` (
  `id_user_details` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `as_user_roles`
--

CREATE TABLE `as_user_roles` (
  `role_id` int(11) NOT NULL,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wm_domains`
--

CREATE TABLE `wm_domains` (
  `domain_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `created_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `latest_test_time` datetime DEFAULT NULL,
  `latest_test_id` int(11) DEFAULT NULL,
  `latest_score` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wm_notifications`
--

CREATE TABLE `wm_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `domain` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wm_tests`
--

CREATE TABLE `wm_tests` (
  `test_id` int(11) NOT NULL,
  `domain` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `test_result_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `as_comments`
--
ALTER TABLE `as_comments`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `as_login_attempts`
--
ALTER TABLE `as_login_attempts`
  ADD PRIMARY KEY (`id_login_attempts`);

--
-- Indexes for table `as_social_logins`
--
ALTER TABLE `as_social_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `as_users`
--
ALTER TABLE `as_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `as_user_details`
--
ALTER TABLE `as_user_details`
  ADD PRIMARY KEY (`id_user_details`);

--
-- Indexes for table `as_user_roles`
--
ALTER TABLE `as_user_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `wm_domains`
--
ALTER TABLE `wm_domains`
  ADD PRIMARY KEY (`domain_id`);

--
-- Indexes for table `wm_notifications`
--
ALTER TABLE `wm_notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `wm_tests`
--
ALTER TABLE `wm_tests`
  ADD PRIMARY KEY (`test_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `as_comments`
--
ALTER TABLE `as_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `as_login_attempts`
--
ALTER TABLE `as_login_attempts`
  MODIFY `id_login_attempts` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `as_social_logins`
--
ALTER TABLE `as_social_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `as_users`
--
ALTER TABLE `as_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `as_user_details`
--
ALTER TABLE `as_user_details`
  MODIFY `id_user_details` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `as_user_roles`
--
ALTER TABLE `as_user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wm_domains`
--
ALTER TABLE `wm_domains`
  MODIFY `domain_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wm_notifications`
--
ALTER TABLE `wm_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wm_tests`
--
ALTER TABLE `wm_tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
