-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql300.infinityfree.com
-- Generation Time: May 29, 2026 at 04:15 AM
-- Server version: 11.4.11-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42011841_kinema_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `custom_playlists`
--

CREATE TABLE `custom_playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_casts`
--

CREATE TABLE `favorite_casts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cast_id` int(11) NOT NULL,
  `cast_name` varchar(255) NOT NULL,
  `cast_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorite_casts`
--

INSERT INTO `favorite_casts` (`id`, `user_id`, `cast_id`, `cast_name`, `cast_image`, `created_at`) VALUES
(2, 6, 17697, 'John Krasinski', 'https://image.tmdb.org/t/p/w200/pmVGDb6Yl6OyFcHVGbu1EYNfyFK.jpg', '2026-05-29 02:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'like', 'Ulasan Anda disukai', 'Pengguna <strong>Fathur</strong> menyukai ulasan Anda pada <em>Obsession</em>.', 1, '2026-05-29 01:06:51'),
(2, 3, 'like', 'Ulasan Anda disukai', 'Pengguna <strong>Fathur</strong> menyukai ulasan Anda pada <em>Obsession</em>.', 1, '2026-05-29 01:06:56'),
(3, 3, 'comment', 'Balasan Baru di Ulasan Anda', 'Pengguna <strong>Fathur</strong> mengomentari ulasan Anda pada <em>Obsession</em>.', 0, '2026-05-29 03:03:29'),
(4, 3, 'comment', 'Balasan Baru di Ulasan Anda', 'Pengguna <strong>Rilo</strong> mengomentari ulasan Anda pada <em>Obsession</em>.', 0, '2026-05-29 03:07:21'),
(5, 3, 'follow', 'Anda mendapat pengikut baru', 'Pengguna <strong>Fathur</strong> mulai mengikuti Anda.', 0, '2026-05-29 03:49:16'),
(6, 2, 'follow', 'Anda mendapat pengikut baru', 'Pengguna <strong>Rilo</strong> mulai mengikuti Anda.', 0, '2026-05-29 04:06:13'),
(7, 3, 'comment', 'Balasan Baru di Ulasan Anda', 'Pengguna <strong>habib</strong> mengomentari ulasan Anda pada <em>Backrooms</em>.', 0, '2026-05-29 04:19:16'),
(8, 7, 'like', 'Ulasan Anda disukai', 'Pengguna <strong>Rilo</strong> menyukai ulasan Anda pada <em>Backrooms</em>.', 0, '2026-05-29 07:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `playlist_items`
--

CREATE TABLE `playlist_items` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `media_type` varchar(20) NOT NULL,
  `media_title` varchar(255) DEFAULT NULL,
  `media_poster` varchar(255) DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `media_type` enum('movie','tv') NOT NULL DEFAULT 'movie',
  `media_title` varchar(255) DEFAULT NULL,
  `media_poster` varchar(255) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `media_id`, `media_type`, `media_title`, `media_poster`, `rating`, `review_text`, `created_at`) VALUES
(4, 2, 1439930, 'movie', NULL, NULL, 5, 'gsahdbasbh', '2026-05-21 15:06:19'),
(5, 1, 1399, 'tv', NULL, NULL, 5, 'kereeennn', '2026-05-22 01:15:58'),
(6, 3, 1083381, 'movie', 'Backrooms', 'https://image.tmdb.org/t/p/w500/vpkNMkbisv5cTaIfCzUduYzXnjb.jpg', 5, 'best', '2026-05-28 08:28:06'),
(7, 3, 1339713, 'movie', 'Obsession', 'https://image.tmdb.org/t/p/w500/6X4qFYBsG3bpWDG2XIKqr04kFJa.jpg', 5, 'best', '2026-05-28 09:07:32'),
(8, 7, 1083381, 'movie', 'Backrooms', 'https://image.tmdb.org/t/p/w500/vpkNMkbisv5cTaIfCzUduYzXnjb.jpg', 5, 'nice movie', '2026-05-29 04:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `review_likes`
--

CREATE TABLE `review_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_likes`
--

INSERT INTO `review_likes` (`id`, `user_id`, `review_id`, `created_at`) VALUES
(5, 6, 7, '2026-05-29 01:06:56'),
(7, 5, 8, '2026-05-29 07:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `review_replies`
--

CREATE TABLE `review_replies` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `review_replies`
--

INSERT INTO `review_replies` (`id`, `review_id`, `user_id`, `reply_text`, `created_at`) VALUES
(1, 7, 6, 'same same', '2026-05-29 03:03:29'),
(2, 7, 5, 'apalah dia guys', '2026-05-29 03:07:21'),
(3, 6, 7, 'yes', '2026-05-29 04:19:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `avatar`, `role`) VALUES
(1, 'Vanmok', 'jomokers@gmail.com', '$2y$10$k93y2sXey0ND7ljJkWu2sOlJtQ60FEchD0d7RdtOt52MLDMyV47eG', '2026-05-21 14:07:49', NULL, 'user'),
(2, 'rinoria', 'rinoria@gmail.com', '$2y$10$4dNrTe18ia7dQLEp69x/GeHRG6IjQ/W8bF3EkVh/xRmUd6OpozC/a', '2026-05-21 14:38:48', NULL, 'user'),
(3, 'ehehhe', 'selby@gmail.com', '$2y$10$/dmNdqkusemHWdJ495ivGu176cAFCBYW/NxdSWavWBW5WY0HcrLSq', '2026-05-25 02:35:43', NULL, 'user'),
(4, 'abcd', 'abcd@abcd.com', '$2y$10$ItPi9sU6Ef.j7kgW9X9sCuWc2Bnv1XppNrhM9smSZKeJipyirU0OW', '2026-05-25 03:47:59', NULL, 'user'),
(5, 'Rilo', 'Rilo@gmail.com', '$2y$10$QeR96gdQ5vLSJaLetQhZsOYg4g.RQ46XINRlz3ywPFFYAuOP/7D5a', '2026-05-28 04:33:34', NULL, 'user'),
(6, 'Fathur', 'fathur@gmail.com', '$2y$10$p6b5FB9lD7f1sp1CDZJ1cOztAE.ZA20qMvGjovjwCbyno5tU4OL7i', '2026-05-28 09:08:33', 'assets/uploads/avatars/avatar_6_1780028038.jpeg', 'user'),
(7, 'habib', 'habib@gmail.com', '$2y$10$JZ2/Ruo4zthqc30mleW1AOFcpqY0Jug/OpgQx/TKjEhyWjSwWGCNm', '2026-05-29 04:18:26', NULL, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_follows`
--

CREATE TABLE `user_follows` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_follows`
--

INSERT INTO `user_follows` (`id`, `follower_id`, `following_id`, `created_at`) VALUES
(1, 6, 3, '2026-05-29 03:49:16'),
(2, 5, 2, '2026-05-29 04:06:13');

-- --------------------------------------------------------

--
-- Table structure for table `watchlist`
--

CREATE TABLE `watchlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `media_type` enum('movie','tv') NOT NULL DEFAULT 'movie',
  `title` varchar(255) NOT NULL,
  `poster_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `watchlist`
--

INSERT INTO `watchlist` (`id`, `user_id`, `media_id`, `media_type`, `title`, `poster_path`, `created_at`) VALUES
(2, 1, 1228710, 'movie', 'Star Wars: The Mandalorian and Grogu', 'https://image.tmdb.org/t/p/w500/5Vi8dSauVwH1HOsiZceDMbRr1Ca.jpg', '2026-05-21 14:14:02'),
(3, 2, 1304313, 'movie', 'Lee Cronin\'s The Mummy', 'https://image.tmdb.org/t/p/w500/1q308iixueCU4pFtSYugNOevtNx.jpg', '2026-05-21 15:06:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `custom_playlists`
--
ALTER TABLE `custom_playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorite_casts`
--
ALTER TABLE `favorite_casts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_cast` (`user_id`,`cast_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `playlist_items`
--
ALTER TABLE `playlist_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_id` (`playlist_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_user_media` (`user_id`,`media_id`,`media_type`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_media_type` (`media_id`,`media_type`);

--
-- Indexes for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_review_like` (`user_id`,`review_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `review_replies`
--
ALTER TABLE `review_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_email` (`email`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `user_follows`
--
ALTER TABLE `user_follows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`following_id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `watchlist`
--
ALTER TABLE `watchlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_media` (`user_id`,`media_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `custom_playlists`
--
ALTER TABLE `custom_playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorite_casts`
--
ALTER TABLE `favorite_casts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `playlist_items`
--
ALTER TABLE `playlist_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `review_likes`
--
ALTER TABLE `review_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `review_replies`
--
ALTER TABLE `review_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_follows`
--
ALTER TABLE `user_follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `watchlist`
--
ALTER TABLE `watchlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_likes`
--
ALTER TABLE `review_likes`
  ADD CONSTRAINT `fk_like_review` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_like_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
