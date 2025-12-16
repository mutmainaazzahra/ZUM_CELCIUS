-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 14, 2025 at 10:44 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zum_celcius`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Outdoor','Indoor') NOT NULL,
  `notes` text,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `time` time DEFAULT '00:00:00',
  `reminder_sent` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `name`, `type`, `notes`, `date`, `created_at`, `time`, `reminder_sent`) VALUES
(4, 8, 'nugas', 'Indoor', '', '2025-12-12', '2025-12-12 13:39:15', '21:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','failed') DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `status`, `created_at`, `is_read`) VALUES
(1, 1, 'Push Notif: Jakarta (Target: 2, Sukses: 0)', 'failed', '2025-12-04 10:06:42', 1),
(2, 1, 'Push Notif: Jakarta (Target: 3, Sukses: 0)', 'failed', '2025-12-04 10:11:52', 1),
(3, 1, 'Push Notif: Jakarta (Valid: 3, Sukses: 0)', 'failed', '2025-12-04 10:22:07', 1),
(4, 1, 'Push Notif: Jakarta (Valid: 6, Sukses: 2)', 'sent', '2025-12-04 12:29:45', 1),
(5, 1, 'Push Notif: Jakarta (Valid: 5, Sukses: 1)', 'sent', '2025-12-05 08:19:20', 1),
(6, 1, 'Push Notif: Jakarta (Valid: 5, Sukses: 1)', 'sent', '2025-12-05 08:31:01', 1),
(7, 1, 'Push Notif: Jakarta (Valid: 7, Sukses: 1)', 'sent', '2025-12-06 15:03:12', 1),
(8, 1, 'Broadcast: Peringatan Admin (Broadcast)', 'sent', '2025-12-07 13:24:26', 1),
(10, 1, 'Web Push: Peringatan cuaca dikirim ke 1 perangkat.', 'sent', '2025-12-08 01:29:02', 1),
(11, 1, 'Broadcast: Peringatan Admin (Broadcast)', 'sent', '2025-12-08 01:29:11', 1),
(13, 1, 'Broadcast: Peringatan Admin (Broadcast)', 'sent', '2025-12-09 10:43:47', 1),
(15, 1, 'Web Push: Peringatan cuaca dikirim ke 1 perangkat.', 'sent', '2025-12-09 10:44:02', 1),
(16, 1, 'Broadcast: Peringatan Admin (Broadcast)', 'sent', '2025-12-09 13:48:57', 1),
(18, 1, 'Web Push: Peringatan cuaca dikirim ke 1 perangkat.', 'sent', '2025-12-09 13:49:06', 1),
(19, 1, 'Web Push: Peringatan cuaca dikirim ke 1 perangkat.', 'sent', '2025-12-09 13:58:54', 1),
(22, 8, '⏰ Pengingat: nugas akan dimulai pukul 21:00', 'sent', '2025-12-12 13:56:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `endpoint` varchar(512) NOT NULL,
  `p256dh` varchar(255) NOT NULL,
  `auth` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `push_subscriptions`
--

INSERT INTO `push_subscriptions` (`id`, `user_id`, `endpoint`, `p256dh`, `auth`, `created_at`) VALUES
(1, 1, 'https://fcm.googleapis.com/fcm/send/cZJS2UNK4OU:APA91bEBO6PokJwlFl9dyqyPervjbL_noMicXJlPbJGquOtJRdfJDWI96fERDH5SfYU1TU-0Brn3AdnnrH_uIq93ylap_zsdHRroh-tHBEqE6C8bxGCJ3MauWLdcy4ihzuZDXy9XVCkA', 'BHSizCwnhhZ71XboHIlZUaH2-4Lv3yojhG1NgW8EM-TNia6vpB_Z2-RaXD4QOqoZY_5zDHnbishcZ6tJVUKUOug', 'FD-jHjT4bGZVwnk-Vuh5Zg', '2025-12-04 10:06:10'),
(2, 1, 'https://fcm.googleapis.com/fcm/send/f7vUgyl68Fs:APA91bHvz3Oyh5Qcmee4wSJnNubrVtJImSS6np58N4WJqw2ky7BJdfKm6AtWh834iqyTOmVvbjWQzwWKDlu91vtuFXbrzWyTnATel-pyyNrIJq0vrq2AwABPFgQ-92PvahiJvFQmGFw8', 'BPcCFDOQ9EDRdPMNLoqxPCsdDyQsM1bkjFOc9OqtWpGnGKnAt3Bn5WHUF8SV6ivHUn34zdkbpd2XqOlI3txkhwc', 'YLnkcctreMsP9OXD_PvbBw', '2025-12-04 10:06:36'),
(4, 1, 'https://fcm.googleapis.com/fcm/send/df9MPiDYxbw:APA91bHD_1ojsDbQixA9BuYPIRCTCYSwOXHeLz0EDTr3y7JDWCo65fZYA2X-33PEH6nMl1HxY44W9fnSlISj7WPJYXyBVY_snMGREhmylhLY60FXcguyA8K9ft6Et0fzjMb6LS_3y0fe', 'BAIG11Aeust5B2de7m1HiMr1g3tCW3Th0Y1u4CtgRcn0a1OdChXSoiHnWnGhDCxbCxY57KwMeJNZyupkCFrYqvM', 'ef2p5TjVnpL7NLZ-npAYsg', '2025-12-04 10:22:04'),
(5, 1, 'https://fcm.googleapis.com/fcm/send/dMnyXifGVjg:APA91bF8uQhNlpNHdkPenUfPUGXNdyrE2gxclMF5vnpXnfJlasK0JzCqqf6T1FmgPqifYawczWYZvmKmN1Tcyy6uc5KtVh6FHI7z0thjofAQFguaql5Z7A_rbifEJ8ES_GV8JNJ3aXbM', 'BFMWWiJ_I6iqk-d5XkGwxrsSGI1sX9eTm450NKPlrPIkZ2Du7yMtLoTPIaLnj8WbsBJTPQLooT54GEcB2bwbuGg', 'TtB-fMMTxYAc1o2lmjXrNQ', '2025-12-04 10:25:15'),
(6, 1, 'https://fcm.googleapis.com/fcm/send/fCQiwg8f0Os:APA91bF9GCObaWfNxdHHOW9nFw6kN1G2e22TplH3BPD5HCFDSh_BUPZ9mLPeYHtVEAf6SDlLu9-a5CC5Um294B5PMxB_j1cU870ZUsrMvfQrn1dcFFEWyFugTd4sVAIKnikq0wfb6wk0', 'BNUQPVoclOkut3bjZ7h2qyAnidnaXiXUy5p6fmAD8ZHqit1y-sOLpFLQfYL-CgywZQUXH_p3BAeDybfmDK2WBUc', 'iZHxDVo7SXhZRJnRN6ohxA', '2025-12-04 10:32:16'),
(8, 1, 'https://fcm.googleapis.com/fcm/send/djJnBTcLevg:APA91bE8inS5xN395anpkGnNJC8Co6BTrf0unmRs2JRQfNoxTvrx9r5_KN7Awsm3MPd7agEK6AswhSMI5JqtDrA7sHW4Xy2ITC9Rg0RddNY4e0AsOiMhNQ2vfybG4LM5R6Vdoi_FXepr', 'BKpvaRykPF8J-nrAMs3RrPHwshuVyPQzyKhpIy-y1YIbD_z_LmoeUVQlAeS5MakepHjh7WaEXkNoJkkhSUbtWxw', '3Woc--crZAkvo2XG2pqrug', '2025-12-05 08:49:53'),
(9, 1, 'https://fcm.googleapis.com/fcm/send/cn_TKYQyzJI:APA91bFECa1srpKiU_4oLTGsMUuwi9WXnqrCJr6M2Rcf393LrUadDS_WOMFNPZ7yaD2Dik3b3qmA0i_RXZjs3gaTWRFhBtbrfccx9HF67dXysqrCptYZuHHnfmrgsQmRPHnO8Y93PKVl', 'BHESLWlGHx1yb9RB787ma2c5tE3FXNyEBTlkP0RaurWPnczViV63FlRxxuEvohs5MiL3om-Vn5Bo1UOjBPdeLZI', 'mhiXiBpSXrSXL31fsKuRiQ', '2025-12-06 11:28:38'),
(10, 8, 'https://fcm.googleapis.com/fcm/send/cy1udbfzgZI:APA91bH_aGXPjZZY9NoInsZTlaKWuf3jb31VectbUBdzlwchROom_azRwpFBpFR8XVPqq3nPS3j9kIhlrB_9PMF6lF4Yw4f2N8S6yY0PzRHiMwdmg2XGK1H091DKIi8iUvhQZ5Y69P8H', 'BKfaYPeyP4odBRBXaygDzv79VHlyl9l7VIW222jC0T1HLo3I54h0A7FAlZaUAAHQxTvCK2-iCT98QyXCeja5DU4', 'IiwQUqMt6lqvDA3eaeksMQ', '2025-12-10 10:04:38'),
(11, 1, 'https://fcm.googleapis.com/fcm/send/dmyUoclZaW0:APA91bHAw_Hnj7jXkL_TmdW2l7IvDoCcZtDr2DzF5lKMlmwcWPL1Z-ciLAlinWbOOg9IapdVwqY-ftgsOnOIN97WvhMw7gmviIDZY7HU33i6csRYc2eeRZh1Ou9pJ_mXFdk0c5V6uYTM', 'BJF0VRyTahX7NIFgRJCMaCSkI_aavqe1Kk_Wc2mhVS23GiH9B_JsbxAzEk9Zfg1w2gNOwbFSXsYsMoqeg9aSjA4', 'klSkj46McJhXlxriUHkJmA', '2025-12-12 13:39:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('guest user','administrator') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'guest user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_photo` varchar(255) DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lon` decimal(11,8) DEFAULT NULL,
  `last_location_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `profile_photo`, `lat`, `lon`, `last_location_name`) VALUES
(1, 'admin', 'admin@zumcelcius.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrator', '2025-12-03 08:54:06', '1764765117_pink-lilies-bouquet-elegant-flowers-spring-blossom.png', '-6.37009920', '107.40039680', 'Cengkong, 41377, Purwasari, Kota Karawang, Jawa Barat, Indonesia'),
(8, 'guest', 'zumcelcius@gmail.com', '$2y$10$ionEOzpYOQ5jLton1JbhMOMRJ2lXZr51IZ3CBFCm1JmDtfoPbecpK', 'guest user', '2025-12-12 13:37:47', NULL, '-6.37009920', '107.40039680', 'Cengkong, 41377, Purwasari, Kota Karawang, Jawa Barat, Indonesia');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `endpoint` (`endpoint`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD CONSTRAINT `push_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
