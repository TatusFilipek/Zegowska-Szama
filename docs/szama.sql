-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 29, 2026 at 04:03 PM
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
(1, 'Otwarcie sklepu', 'Witamy w naszym nowym sklepie!', '2026-05-28 20:23:31');

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

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `name`, `price`, `created_at`) VALUES
(1, 'Zestaw śniadaniowy', 1200, '2026-05-28 20:23:31'),
(2, 'Promocja masła', 700, '2026-05-28 20:23:31'),
(3, 'Zestaw owoce i napoje', 1200, '2026-05-29 12:17:38');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `offer_products`
--

CREATE TABLE `offer_products` (
  `offer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offer_products`
--

INSERT INTO `offer_products` (`offer_id`, `product_id`, `quantity`) VALUES
(1, 1, 1),
(1, 3, 1),
(2, 3, 1),
(3, 4, 1),
(3, 8, 1);

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `created_at`) VALUES
(1, 1, 0, '2026-05-28 20:23:31'),
(2, 2, 1, '2026-05-28 20:23:31');

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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price_snapshot`, `discount_percent_snapshot`) VALUES
(1, 1, 2, 3, 150, 10),
(2, 1, 1, 1, 500, 0),
(3, 2, 3, 2, 800, 5);

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

--
-- Dumping data for table `order_offers`
--

INSERT INTO `order_offers` (`id`, `order_id`, `offer_id`, `quantity`, `unit_price_snapshot`) VALUES
(1, 2, 2, 1, 700);

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
(1, 'Chleb razowy', 'pieczywo', 'photo1', 500, 0, 100),
(2, 'Bułka maślana', 'pieczywo', 'photo2', 150, 10, 200),
(3, 'Masło 200g', 'nabiał', 'photo3', 800, 5, 50),
(4, 'Jabłka czerwone 1kg', 'owoce', 'jablka_czerwone.jpg', 400, 0, 150),
(5, 'Banany 1kg', 'owoce', 'banany.jpg', 350, 5, 120),
(6, 'Marchew 1kg', 'warzywa', 'marchew.jpg', 250, 0, 80),
(7, 'Ziemniaki 2kg', 'warzywa', 'ziemniaki_2kg.jpg', 300, 0, 200),
(8, 'Sok pomarańczowy 1L', 'napoje', 'sok_pomaranczowy_1l.jpg', 600, 10, 60),
(9, 'Woda mineralna 1.5L', 'napoje', 'woda_1_5l.jpg', 200, 0, 300),
(10, 'Czekolada mleczna 100g', 'słodycze', 'czekolada_mleczna_100g.jpg', 450, 15, 90),
(11, 'Ciasteczka owsiane 200g', 'słodycze', 'ciasteczka_owsiane_200g.jpg', 500, 0, 70),
(12, 'Ser żółty 250g', 'nabiał', 'ser_zolty_250g.jpg', 1100, 20, 40),
(13, 'Kurczak świeży 1kg', 'mięso', 'kurczak_1kg.jpg', 1500, 0, 30),
(14, 'Łosoś wędzony 200g', 'ryby', 'losos_wedzony_200g.jpg', 2200, 10, 25),
(15, 'Bagietka francuska', 'pieczywo', 'bagietka_francuska.jpg', 220, 0, 120),
(16, 'Chleb pszenny', 'pieczywo', 'chleb_pszeny.jpg', 480, 0, 80),
(17, 'Bułka kajzerka', 'pieczywo', 'bulka_kajzerka.jpg', 120, 0, 250),
(18, 'Pumpernikiel 500g', 'pieczywo', 'pumpernikiel_500g.jpg', 650, 5, 60),
(19, 'Chleb orkiszowy', 'pieczywo', 'chleb_orkiszowy.jpg', 700, 0, 40),
(20, 'Bułka z sezamem', 'pieczywo', 'bulka_sezam.jpg', 140, 0, 180),
(21, 'Chleb na zakwasie 500g', 'pieczywo', 'chleb_zakwas_500g.jpg', 900, 10, 50),
(22, 'Bułka z makiem', 'pieczywo', 'bulka_mak.jpg', 130, 0, 140),
(23, 'Jogurt naturalny 400g', 'nabiał', 'jogurt_naturalny_400g.jpg', 300, 0, 120),
(24, 'Mleko 1L', 'nabiał', 'mleko_1l.jpg', 280, 0, 200),
(25, 'Śmietana 18% 200g', 'nabiał', 'smietana_18_200g.jpg', 220, 0, 80),
(26, 'Serek wiejski 200g', 'nabiał', 'serek_wiejski_200g.jpg', 450, 0, 70),
(27, 'Masło ekstra 250g', 'nabiał', 'maslo_ekstra_250g.jpg', 1200, 15, 30),
(28, 'Twaróg półtusty 250g', 'nabiał', 'twarog_pol_250g.jpg', 600, 0, 60),
(29, 'Kefir 500ml', 'nabiał', 'kefir_500ml.jpg', 240, 0, 100),
(30, 'Mleko roślinne 1L', 'nabiał', 'mleko_rozs_1l.jpg', 700, 0, 90),
(31, 'Gruszki 1kg', 'owoce', 'gruszki_1kg.jpg', 450, 0, 100),
(32, 'Winogrona 500g', 'owoce', 'winogrona_500g.jpg', 550, 5, 80),
(33, 'Truskawki 250g', 'owoce', 'truskawki_250g.jpg', 700, 0, 60),
(34, 'Cytryny 1kg', 'owoce', 'cytryny_1kg.jpg', 650, 0, 90),
(35, 'Mandarynki 1kg', 'owoce', 'mandarynki_1kg.jpg', 480, 0, 110),
(36, 'Ananas świeży', 'owoce', 'ananas_swiezy.jpg', 1200, 10, 30),
(37, 'Kiwi 1kg', 'owoce', 'kiwi_1kg.jpg', 900, 0, 50),
(38, 'Śliwki 1kg', 'owoce', 'sliwki_1kg.jpg', 550, 0, 40),
(39, 'Papryka czerwona 1kg', 'warzywa', 'papryka_czerwona_1kg.jpg', 900, 0, 60),
(40, 'Ogórki 1kg', 'warzywa', 'ogorki_1kg.jpg', 300, 0, 120),
(41, 'Cebula 1kg', 'warzywa', 'cebula_1kg.jpg', 200, 0, 180),
(42, 'Pomidory 1kg', 'warzywa', 'pomidory_1kg.jpg', 850, 0, 90),
(43, 'Sałata świeża', 'warzywa', 'salata_swieza.jpg', 300, 0, 70),
(44, 'Czosnek 100g', 'warzywa', 'czosnek_100g.jpg', 180, 0, 200),
(45, 'Buraki 1kg', 'warzywa', 'buraki_1kg.jpg', 220, 0, 80),
(46, 'Rzodkiewka pęczek', 'warzywa', 'rzodkiewka_peczek.jpg', 150, 0, 140),
(47, 'Herbata czarna 50 torebek', 'napoje', 'herbata_czarna_50.jpg', 500, 0, 150),
(48, 'Kawa mielona 250g', 'napoje', 'kawa_mielona_250g.jpg', 1400, 5, 80),
(49, 'Sok jabłkowy 1L', 'napoje', 'sok_jablkowy_1l.jpg', 550, 0, 90),
(50, 'Napój gazowany 330ml', 'napoje', 'napoj_gazowany_330ml.jpg', 250, 0, 400),
(51, 'Kakao instant 200g', 'napoje', 'kakao_instant_200g.jpg', 800, 0, 60),
(52, 'Sok pomidorowy 1L', 'napoje', 'sok_pomidorowy_1l.jpg', 420, 0, 70),
(53, 'Izotonik 500ml', 'napoje', 'izotonik_500ml.jpg', 450, 0, 120),
(54, 'Kompocik 1L', 'napoje', 'kompocik_1l.jpg', 350, 0, 60),
(55, 'Guma do żucia 30szt', 'słodycze', 'guma_30szt.jpg', 200, 0, 300),
(56, 'Lizaki 10szt', 'słodycze', 'lizaki_10szt.jpg', 180, 0, 200),
(57, 'Baton kokosowy 50g', 'słodycze', 'baton_kokosowy_50g.jpg', 350, 5, 120),
(58, 'Cukierki miętowe 200g', 'słodycze', 'cukierki_mietowe_200g.jpg', 400, 0, 90),
(59, 'Batony zbożowe 6szt', 'słodycze', 'batony_zbozowe_6szt.jpg', 900, 0, 80),
(60, 'Czekoladki praliny 200g', 'słodycze', 'czekoladki_praliny_200g.jpg', 1400, 10, 50),
(61, 'Żelki mix 250g', 'słodycze', 'zelki_mix_250g.jpg', 600, 0, 110),
(62, 'Herbatniki maślane 200g', 'słodycze', 'herbatniki_maslane_200g.jpg', 480, 0, 130),
(63, 'Wołowina mielona 500g', 'mięso', 'wolowina_mielona_500g.jpg', 1800, 0, 40),
(64, 'Karkówka 1kg', 'mięso', 'karkowka_1kg.jpg', 1600, 0, 35),
(65, 'Szynka gotowana 200g', 'mięso', 'szynka_gotowana_200g.jpg', 900, 5, 60),
(66, 'Kiełbasa wiejska 500g', 'mięso', 'kielbasa_wiejska_500g.jpg', 1200, 0, 45),
(67, 'Polędwica 300g', 'mięso', 'poledwica_300g.jpg', 2200, 10, 20),
(68, 'Boczek wędzony 250g', 'mięso', 'boczek_wedzony_250g.jpg', 950, 0, 50),
(69, 'Udka z kurczaka 1kg', 'mięso', 'udka_kurczaka_1kg.jpg', 1000, 0, 70),
(70, 'Kaczka świeża 1kg', 'mięso', 'kaczka_1kg.jpg', 2000, 0, 15),
(71, 'Polędwiczki wieprzowe 500g', 'mięso', 'poledwiczki_wieprzowe_500g.jpg', 2100, 0, 25),
(72, 'Tilapia świeża 1kg', 'ryby', 'tilapia_1kg.jpg', 1400, 0, 20),
(73, 'Pstrąg świeży 1kg', 'ryby', 'pstrag_1kg.jpg', 1600, 0, 18),
(74, 'Śledź solony 200g', 'ryby', 'sledz_solony_200g.jpg', 300, 0, 100),
(75, 'Krewetki 300g', 'ryby', 'krewetki_300g.jpg', 1800, 10, 30),
(76, 'Świeży dorsz 1kg', 'ryby', 'dorsz_1kg.jpg', 2000, 0, 15),
(77, 'Małże świeże 500g', 'ryby', 'malze_500g.jpg', 1200, 0, 25),
(78, 'Tuńczyk w kawałkach 200g', 'ryby', 'tunczyk_200g.jpg', 900, 0, 60),
(79, 'Filet z pstrąga 200g', 'ryby', 'filet_pstrag_200g.jpg', 700, 0, 40),
(80, 'Marynowany śledź 300g', 'ryby', 'marynowany_sledz_300g.jpg', 450, 0, 50);

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
(3, 'Damian', '$2y$10$Z8DsbwmF9U6HScksYdOtquYCJVeCt1BFSrfhcJjvzErhrbugO6hLa', 'damian@damian.damian', 2, '2026-05-28 21:39:24');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_claimed_offers`
--

CREATE TABLE `user_claimed_offers` (
  `user_id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `claimed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_claimed_offers`
--

INSERT INTO `user_claimed_offers` (`user_id`, `offer_id`, `claimed_at`) VALUES
(1, 1, '2026-05-28 20:23:31');

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
-- Indeksy dla tabeli `user_claimed_offers`
--
ALTER TABLE `user_claimed_offers`
  ADD PRIMARY KEY (`user_id`,`offer_id`),
  ADD KEY `idx_user_claimed_offers_offer_id` (`offer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_offers`
--
ALTER TABLE `order_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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

--
-- Constraints for table `user_claimed_offers`
--
ALTER TABLE `user_claimed_offers`
  ADD CONSTRAINT `user_claimed_offers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_claimed_offers_ibfk_2` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
