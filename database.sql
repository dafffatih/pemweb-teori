-- Database: readwatch

CREATE DATABASE IF NOT EXISTS readwatch;
USE readwatch;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
`
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('Film','Anime','Komik','Novel') NOT NULL,
  `status` enum('Sedang Berjalan','Sudah Tamat') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user
INSERT INTO `users` (`full_name`, `username`, `email`, `password`, `role`) VALUES
('Administrator', 'admin', 'admin@readwatch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample user
INSERT INTO `users` (`full_name`, `username`, `email`, `password`, `role`) VALUES
('User Demo', 'user1', 'user@readwatch.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert sample items for demo user (user_id = 2)
INSERT INTO `items` (`user_id`, `title`, `category`, `status`, `notes`) VALUES
(2, 'One Piece', 'Anime', 'Sedang Berjalan', 'Anime terpanjang yang pernah ada'),
(2, 'Naruto', 'Komik', 'Sudah Tamat', 'Cerita ninja yang inspiratif'),
(2, 'Avengers Endgame', 'Film', 'Sudah Tamat', 'Film superhero terbaik'),
(2, 'Attack on Titan', 'Anime', 'Sudah Tamat', 'Anime dengan plot twist terbaik'),
(2, 'Harry Potter', 'Novel', 'Sudah Tamat', 'Novel fantasy klasik'),
(2, 'Demon Slayer', 'Komik', 'Sudah Tamat', 'Komik dengan art terbaik'),
(2, 'Your Name', 'Film', 'Sudah Tamat', 'Film anime yang mengharukan'),
(2, 'Death Note', 'Komik', 'Sudah Tamat', 'Thriller psikologis terbaik');