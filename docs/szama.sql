-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 29, 2026 at 10:05 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `szama`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Otwarcie sklepu', 'Witamy w naszym nowym sklepie!', '2026-05-28 20:23:31'),
(2, 'test', 'Super wiadomosc', '2026-05-29 18:18:37'),
(3, 'test2', 'test222', '2026-05-29 18:25:01');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL CHECK (`price` > 0),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `offer_products`
--

CREATE TABLE `offer_products` (
  `offer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL CHECK (`status` in (0,1,2,3)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `unit_price_snapshot` int(11) NOT NULL CHECK (`unit_price_snapshot` > 0),
  `discount_percent_snapshot` int(11) DEFAULT NULL CHECK (`discount_percent_snapshot` between 0 and 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_offers`
--

CREATE TABLE `order_offers` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `unit_price_snapshot` int(11) NOT NULL CHECK (`unit_price_snapshot` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`) VALUES
(1, 'manage_products'),
(2, 'manage_orders'),
(3, 'view_reports'),
(4, 'claim_offers');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `price_cents` int(11) NOT NULL CHECK (`price_cents` > 0),
  `discount_percent` int(11) DEFAULT NULL CHECK (`discount_percent` between 0 and 100),
  `stock` int(11) NOT NULL CHECK (`stock` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `picture`, `price_cents`, `discount_percent`, `stock`) VALUES
(1, 'Espresso', 'Kawa', 'Espresso', 150, 0, 100),
(2, 'Espresso Macchiato', 'Kawa', 'Espresso_Macchiato', 250, 0, 100),
(3, 'Kawa Czarna', 'Kawa', 'Kawa_Czarna', 200, 0, 100),
(4, 'Kawa Biała', 'Kawa', 'Kawa_Biała', 250, 0, 100),
(5, 'Cappuccino', 'Kawa', 'Cappuccino', 350, 0, 100),
(6, 'Latte Macchiato', 'Kawa', 'Latte_Macchiato', 350, 0, 100),
(7, 'Opłata za kubek', 'Kawa', 'Opłata_za_kubek', 50, 0, 100),
(8, 'Double Shot Espresso', 'Kawa', 'Double_Shot_Espresso', 150, 0, 100),
(9, 'Tymbark Karton 1L', 'Napoje', 'Tymbark_Karton_1L', 450, 0, 100),
(10, 'Woda Gaz/N-Gaz', 'Napoje', 'Woda_Gaz_N-Gaz', 250, 0, 100),
(11, 'Tymbark Karton 2L', 'Napoje', 'Tymbark_Karton_2L', 500, 0, 100),
(12, 'Tymbark Szkło 0,25L', 'Napoje', 'Tymbark_Szkło', 250, 0, 100),
(13, 'Tymbark Plastik 0,5L', 'Napoje', 'Tymbark_Plastik', 300, 0, 100),
(14, 'Herbata', 'Napoje', 'Herbata', 250, 0, 100),
(15, 'Bułka Gołosza', 'Bułki', 'Bułka_Gołosza', 600, 0, 100),
(16, 'Bułka Ser', 'Bułki', 'Bułka_Ser', 300, 0, 100),
(17, 'Bułka Szynka', 'Bułki', 'Bułka_Szynka', 300, 0, 100),
(18, 'Bułka Szynka Ser', 'Bułki', 'Bułka_Szynka_Ser', 400, 0, 100),
(19, 'Bułka Sos', 'Bułki', 'Bułka_Sos', 300, 0, 100),
(20, 'Bułka Masło', 'Bułki', 'Bułka_Masło', 200, 0, 100),
(21, 'Bułka Sucha', 'Bułki', 'Bułka_Sucha', 150, 0, 100),
(22, 'Bułka Gołosza Ciemna', 'Bułki', 'Bułka_Gołosza_Ciemna', 600, 0, 100),
(23, 'Bułka Ser Ciemna', 'Bułki', 'Bułka_Ser_Ciemna', 300, 0, 100),
(24, 'Bułka Szynka Ciemna', 'Bułki', 'Bułka_Szynka_Ciemna', 300, 0, 100),
(25, 'Bułka Szynka Ser Ciemna', 'Bułki', 'Bułka_Szynka_Ser_Ciemna', 400, 0, 100),
(26, 'Bułka Sos Ciemna', 'Bułki', 'Bułka_Sos_Ciemna', 300, 0, 100),
(27, 'Bułka Masło Ciemna', 'Bułki', 'Bułka_Masło_Ciemna', 200, 0, 100),
(28, 'Bułka Sucha Ciemna', 'Bułki', 'Bułka_Sucha_Ciemna', 150, 0, 100),
(29, 'Hot-Dog', 'Inne na Ciepło', 'Hot-Dog', 600, 0, 100),
(30, 'Double-Dog', 'Inne na Ciepło', 'Double-Dog', 800, 0, 100),
(31, 'Tost Ser', 'Inne na Ciepło', 'Tost_Ser', 250, 0, 100),
(32, 'Tost Szynka', 'Inne na Ciepło', 'Tost_Szynka', 250, 0, 100),
(33, 'Tost Masło', 'Inne na Ciepło', 'Tost_Masło', 150, 0, 100),
(34, 'Tost Ser Szynka', 'Inne na Ciepło', 'Tost_Ser_Szynka', 400, 0, 100);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'customer'),
(3, 'manager');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 4),
(3, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `settings`
--

CREATE TABLE `settings` (
  `user_id` int(11) NOT NULL,
  `theme` varchar(50) NOT NULL DEFAULT 'light'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`user_id`, `theme`) VALUES
(1, 'light'),
(2, 'dark');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `email`, `role_id`, `created_at`) VALUES
(1, 'Anna', 'password123', 'anna@example.com', 2, '2026-05-28 20:23:31'),
(2, 'Piotr', 'adminpass', 'piotr@example.com', 1, '2026-05-28 20:23:31'),
(3, 'Damian', '$2y$10$Z8DsbwmF9U6HScksYdOtquYCJVeCt1BFSrfhcJjvzErhrbugO6hLa', 'damian@damian.damian', 2, '2026-05-28 21:39:24'),
(4, 'Tosiek', '$2y$10$lKYNyKJ8in4fiASY3btFv.P/RhOgwzFL78jForGkKrFrCwmrx9UXu', 'antos.dziegielweski@aba.com', 2, '2026-05-29 18:39:06'),
(5, 'DiscordMod', '$2y$10$31bjoAoSQtNBgCrtlP/GVegJff0XGKcQh7E5NV.ocHagEgMOtC.HG', 'admin@gmail.com', 1, '2026-05-29 21:32:33'),
(6, 'Urzytkownik1', '$2y$10$q/mnEbU9PTKqibrHYAAD.e44cIjcrlPZDDXOXbbLWJrSacSsTMyfC', 'urzytkownik@gmail.com', 2, '2026-05-29 21:33:39');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `offer_products`
--
ALTER TABLE `offer_products`
  ADD PRIMARY KEY (`offer_id`,`product_id`),
  ADD KEY `idx_offer_products_product_id` (`product_id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user_id` (`user_id`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order_id` (`order_id`),
  ADD KEY `idx_order_items_product_id` (`product_id`);

--
-- Indeksy dla tabeli `order_offers`
--
ALTER TABLE `order_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_offers_order_id` (`order_id`),
  ADD KEY `idx_order_offers_offer_id` (`offer_id`);

--
-- Indeksy dla tabeli `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `picture` (`picture`);

--
-- Indeksy dla tabeli `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indeksy dla tabeli `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_offers`
--
ALTER TABLE `order_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `offer_products`
--
ALTER TABLE `offer_products`
  ADD CONSTRAINT `offer_products_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offer_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `order_offers`
--
ALTER TABLE `order_offers`
  ADD CONSTRAINT `order_offers_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_offers_ibfk_2` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
