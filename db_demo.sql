-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 03, 2022 at 11:16 AM
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

--
-- Dumping data for table `as_users`
--

INSERT INTO `as_users` (`user_id`, `email`, `username`, `password`, `confirmation_key`, `confirmed`, `password_reset_key`, `password_reset_confirmed`, `password_reset_timestamp`, `register_date`, `user_role`, `last_login`, `banned`) VALUES
(1, 'admin@localhost:3000', 'admin', '$2a$13$MxZ2C6TPrXkAd0c54o9Pk.YySmtjgEFkUi85pxT9BtKfjiDunOBTi', '', 'Y', '', 'N', NULL, '2022-04-02', 3, '2022-04-03 05:04:14', 'N'),
(2, 'admin@o3o.ca', 'test', '$2a$13$MxZ2C6TPrXkAd0c54o9Pk.4vFaLDmODhAup09uJHMrq5S40TM5Jia', '', 'Y', '', 'N', NULL, '2022-04-02', 1, '2022-04-03 05:38:49', 'N');

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

--
-- Dumping data for table `as_user_details`
--

INSERT INTO `as_user_details` (`id_user_details`, `user_id`, `first_name`, `last_name`, `phone`, `address`) VALUES
(1, 1, '', '', '', ''),
(2, 2, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `as_user_roles`
--

CREATE TABLE `as_user_roles` (
  `role_id` int(11) NOT NULL,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `as_user_roles`
--

INSERT INTO `as_user_roles` (`role_id`, `role`) VALUES
(1, 'user'),
(2, 'editor'),
(3, 'admin');

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
  `latest_test_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latest_score` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wm_domains`
--

INSERT INTO `wm_domains` (`domain_id`, `user_id`, `domain`, `enabled`, `created_time`, `latest_test_time`, `latest_test_id`, `latest_score`) VALUES
(10, 2, 'o3o.ca', 1, '2022-04-03 17:56:08', '2022-04-03 17:56:08', 'de3977f1-342c-4b23-bd26-5c6107325257', 0);

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
  `test_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `test_result_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wm_tests`
--

INSERT INTO `wm_tests` (`test_id`, `domain`, `test_result_json`, `timestamp`) VALUES
('2b705057-03fa-4e1a-8f98-5049f0a54799', 'facebook.com', '{\n    \"domain\": \"facebook.com\",\n    \"beijing_chinatelecom\": {\n        \"blocked\": 1,\n        \"result\": \"detected TCP reset attack\",\n        \"as_detected_cn\": \"AS32934 Facebook, Inc.\",\n        \"as_detected_us\": \"AS32934 Facebook, Inc.\",\n        \"ip_detected_cn\": \"157.240.6.35\",\n        \"ip_detected_us\": \"31.13.70.36\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"shanghai_unicom\": {\n        \"blocked\": 1,\n        \"result\": \"detected DNS poisoning and TCP reset attack\",\n        \"as_detected_cn\": \"AS6423 Digital Fortress\",\n        \"as_detected_us\": \"AS32934 Facebook, Inc.\",\n        \"ip_detected_cn\": \"69.30.25.21\",\n        \"ip_detected_us\": \"31.13.70.36\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"beijing_unicom\": {\n        \"blocked\": 1,\n        \"result\": \"detected TCP reset attack\",\n        \"as_detected_cn\": \"AS32934 Facebook, Inc.\",\n        \"as_detected_us\": \"AS32934 Facebook, Inc.\",\n        \"ip_detected_cn\": \"31.13.87.19\",\n        \"ip_detected_us\": \"31.13.70.36\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"beijing_tencentcloud\": {\n        \"blocked\": 1,\n        \"result\": \"detected DNS poisoning and TCP reset attack\",\n        \"as_detected_cn\": \"AS13414 Twitter Inc.\",\n        \"as_detected_us\": \"AS32934 Facebook, Inc.\",\n        \"ip_detected_cn\": \"103.252.115.153\",\n        \"ip_detected_us\": \"31.13.70.36\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"wulanchabu_aliyun\": {\n        \"blocked\": 1,\n        \"result\": \"detected DNS poisoning and TCP reset attack\",\n        \"as_detected_cn\": \"AS13414 Twitter Inc.\",\n        \"as_detected_us\": \"AS32934 Facebook, Inc.\",\n        \"ip_detected_cn\": \"199.59.148.222\",\n        \"ip_detected_us\": \"31.13.70.36\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"percentage_blocking_score\": 1,\n    \"evaluation\": \"high possibility of blocking detected\",\n    \"code\": 200,\n    \"timestamp\": 1649009564\n}', '2022-04-03 18:12:46'),
('d3669b82-490e-493a-b483-852e00890cba', 'google.com', '{\n    \"domain\": \"google.com\",\n    \"beijing_chinatelecom\": {\n        \"blocked\": 1,\n        \"result\": \"detected TCP reset attack\",\n        \"as_detected_cn\": \"AS15169 Google LLC\",\n        \"as_detected_us\": \"AS15169 Google LLC\",\n        \"ip_detected_cn\": \"172.217.163.46\",\n        \"ip_detected_us\": \"74.125.138.113\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"shanghai_unicom\": {\n        \"blocked\": 1,\n        \"result\": \"detected TCP reset attack\",\n        \"as_detected_cn\": \"AS15169 Google LLC\",\n        \"as_detected_us\": \"AS15169 Google LLC\",\n        \"ip_detected_cn\": \"142.251.42.238\",\n        \"ip_detected_us\": \"74.125.138.113\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"beijing_unicom\": {\n        \"blocked\": 1,\n        \"result\": \"detected TCP reset attack\",\n        \"as_detected_cn\": \"AS15169 Google LLC\",\n        \"as_detected_us\": \"AS15169 Google LLC\",\n        \"ip_detected_cn\": \"172.217.163.46\",\n        \"ip_detected_us\": \"74.125.138.113\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"beijing_tencentcloud\": {\n        \"blocked\": 1,\n        \"result\": \"detected DNS poisoning and TCP reset attack\",\n        \"as_detected_cn\": \"AS12874 Fastweb SpA\",\n        \"as_detected_us\": \"AS15169 Google LLC\",\n        \"ip_detected_cn\": \"93.46.8.90\",\n        \"ip_detected_us\": \"74.125.138.113\",\n        \"web_server_status_cn\": \"down\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 0\n    },\n    \"wulanchabu_aliyun\": {\n        \"blocked\": 1,\n        \"result\": \"detected DNS poisoning\",\n        \"as_detected_cn\": \"AS3320 Deutsche Telekom AG\",\n        \"as_detected_us\": \"AS15169 Google LLC\",\n        \"ip_detected_cn\": \"46.82.174.69\",\n        \"ip_detected_us\": \"74.125.138.113\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"percentage_blocking_score\": 1,\n    \"evaluation\": \"high possibility of blocking detected\",\n    \"code\": 200\n}', '2022-04-03 18:08:39'),
('948fc571-11a1-419f-afbb-e822c585f59b', 'o3o.ca', '{\n    \"domain\": \"o3o.ca\",\n    \"beijing_chinatelecom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"shanghai_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_tencentcloud\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"wulanchabu_aliyun\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"percentage_blocking_score\": 0,\n    \"evaluation\": \"no blocking detected\",\n    \"code\": 200\n}', '2022-04-03 18:04:12'),
('12e6829c-1de5-4349-b6d1-37543772b417', 'o3o.ca', '{\n    \"domain\": \"o3o.ca\",\n    \"beijing_chinatelecom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"shanghai_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_tencentcloud\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"wulanchabu_aliyun\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"percentage_blocking_score\": 0,\n    \"evaluation\": \"no blocking detected\",\n    \"code\": 200\n}', '2022-04-03 17:40:32'),
('de3977f1-342c-4b23-bd26-5c6107325257', 'o3o.ca', '{\n    \"domain\": \"o3o.ca\",\n    \"beijing_chinatelecom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"shanghai_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_tencentcloud\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"wulanchabu_aliyun\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"104.21.5.93\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"percentage_blocking_score\": 0,\n    \"evaluation\": \"no blocking detected\",\n    \"code\": 200\n}', '2022-04-03 17:56:08'),
('a9f93d74-9719-42d8-98d9-af306a458978', 'o3o.ca', '{\n    \"domain\": \"o3o.ca\",\n    \"beijing_chinatelecom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"172.67.133.64\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"shanghai_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_unicom\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"beijing_tencentcloud\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"wulanchabu_aliyun\": {\n        \"blocked\": 0,\n        \"result\": \"no blocking detected\",\n        \"as_detected_cn\": \"AS13335 Cloudflare, Inc.\",\n        \"as_detected_us\": \"AS13335 Cloudflare, Inc.\",\n        \"ip_detected_cn\": \"104.21.5.93\",\n        \"ip_detected_us\": \"2606:4700:3031::6815:55d\",\n        \"web_server_status_cn\": \"up\",\n        \"web_server_status_us\": \"up\",\n        \"web_server_status_match\": 1\n    },\n    \"percentage_blocking_score\": 0,\n    \"evaluation\": \"no blocking detected\",\n    \"code\": 200\n}', '2022-04-03 17:33:20');

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
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_id` (`user_id`);

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
  ADD PRIMARY KEY (`notification_id`),
  ADD UNIQUE KEY `notification_id` (`notification_id`);

--
-- Indexes for table `wm_tests`
--
ALTER TABLE `wm_tests`
  ADD PRIMARY KEY (`test_id`),
  ADD UNIQUE KEY `test_id` (`test_id`);

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `as_user_details`
--
ALTER TABLE `as_user_details`
  MODIFY `id_user_details` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `as_user_roles`
--
ALTER TABLE `as_user_roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wm_domains`
--
ALTER TABLE `wm_domains`
  MODIFY `domain_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wm_notifications`
--
ALTER TABLE `wm_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
