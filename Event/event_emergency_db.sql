-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 06:40 AM
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
-- Database: `event_emergency_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `emergencies`
--

CREATE TABLE `emergencies` (
  `id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergencies`
--

INSERT INTO `emergencies` (`id`, `location`, `description`, `event_id`, `media`, `status`, `created_at`) VALUES
(1, 'Manila', 'Incident report', 1, NULL, 'pending', '2025-05-04 06:56:17'),
(2, 'Latitude: 14.59653138746465, Longitude: 120.99043025358087', 'incident report', 1, 'uploads/testing.jpg', 'pending', '2025-05-04 07:00:38'),
(3, 'Latitude: 14.596517128858359, Longitude: 120.99051171268069', 'incident report', 1, 'uploads/testing.jpg', 'pending', '2025-05-04 07:04:32'),
(4, 'San Juan', 'Fire out Phase 2', NULL, NULL, 'resolved', '2025-05-04 07:17:31'),
(5, 'Manila', 'Place testing', 3, 'uploads/bleh.jpg', 'pending', '2025-05-04 09:56:15'),
(7, 'Manila', 'Fire east side', NULL, 'side', '', '2025-05-04 18:10:21'),
(8, 'Manila', 'Fire out in Manila', 8, 'uploads/orange.jpg', 'pending', '2025-05-04 18:11:29'),
(9, 'Manila', 'Panot', NULL, 'uploads/sleepy.jpg', '', '2025-05-04 18:29:13'),
(10, 'Manila', 'Hello Testing', NULL, '', '', '2025-05-04 18:35:41'),
(11, 'Manila', 'Simple test', NULL, 'uploads/1746383924_orange.jpg', '', '2025-05-04 18:38:44'),
(12, 'Manila', 'Test', NULL, 'uploads/1746384109_bleh.jpg', '', '2025-05-04 18:41:49'),
(14, 'Manila', 'Approved Test', NULL, 'uploads/1746384870_testing.jpg', '', '2025-05-04 18:54:30'),
(15, 'Manila', 'Testing Emergency', NULL, 'uploads/1746419681_bleh.jpg', '', '2025-05-05 04:34:41'),
(16, 'Manila', 'Testing Emergency', NULL, 'uploads/1746419689_bleh.jpg', '', '2025-05-05 04:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `attendees` int(11) NOT NULL,
  `status` enum('PENDING','APPROVED','DENIED') DEFAULT 'PENDING',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `location`, `date`, `attendees`, `status`, `created_at`) VALUES
(1, 'House Party 2k25', 'Manila', '2025-08-05 13:30:00', 15, 'PENDING', '2025-05-04 06:43:48'),
(3, 'Zumba Dance', 'Pasig', '2025-10-10 08:30:00', 20, 'DENIED', '2025-05-04 07:54:08'),
(6, 'QA Network', 'San Juan', '2025-05-05 00:00:00', 10, 'APPROVED', '2025-05-04 13:34:39'),
(7, 'Dorm Party', 'Pasig', '2025-05-06 00:00:00', 20, 'APPROVED', '2025-05-04 13:45:17'),
(8, 'Mayvel Suprise Party', 'La Union', '2025-05-08 11:30:00', 50, 'APPROVED', '2025-05-04 15:24:37'),
(9, 'Bembi\\\'s Birthday', 'Mandaluyong', '2025-05-05 00:00:00', 2, 'PENDING', '2025-05-04 15:38:16'),
(10, 'Kick-off Party', 'Zambales', '2025-05-30 00:00:00', 50, 'PENDING', '2025-05-04 18:03:54'),
(11, 'Camp Rock', 'Tanay', '2025-05-25 00:00:00', 20, 'DENIED', '2025-05-05 01:08:45'),
(12, 'Add Event Party', 'BGC Taguig', '2025-05-15 00:00:00', 45, 'DENIED', '2025-05-05 01:53:54');

-- --------------------------------------------------------

--
-- Table structure for table `holidays_list`
--

CREATE TABLE `holidays_list` (
  `id` int(10) NOT NULL,
  `date` varchar(20) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `bdate` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `holidays_list`
--

INSERT INTO `holidays_list` (`id`, `date`, `reason`, `bdate`) VALUES
(1, '2025-12-25', 'Christmas', ''),
(2, '2025-12-25', 'Christmas', ''),
(3, '2025-12-25', 'Christmas', ''),
(4, '', '', ''),
(5, '2025-06-12', 'Araw ng Kalayaan', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Gek', 'gek@gmail.com', 'gek', '$2y$10$I7WTRfDFe1lkhdUWSsyYx.RAOiz1PpfWV47WUtmE/CO2H3Boxyv4.', '2025-05-05 02:22:56', '2025-05-05 02:22:56'),
(2, 'Hannah Acosta', 'hannah.acosta@ymail.com', 'hannah', '$2y$10$2vVr3XgtbDV9Xp0brexcY.qMII10VV6MlPKv5rv4MChS5VUSexeZK', '2025-05-05 02:28:24', '2025-05-05 02:28:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emergencies`
--
ALTER TABLE `emergencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `holidays_list`
--
ALTER TABLE `holidays_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `emergencies`
--
ALTER TABLE `emergencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `holidays_list`
--
ALTER TABLE `holidays_list`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emergencies`
--
ALTER TABLE `emergencies`
  ADD CONSTRAINT `emergencies_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
