-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 11 Ara 2023, 15:05:17
-- Sunucu sürümü: 10.4.27-MariaDB
-- PHP Sürümü: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `eticaret-api`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `discount_rate` int(11) NOT NULL,
  `coupon_code` varchar(255) DEFAULT NULL,
  `gift` varchar(255) DEFAULT NULL,
  `last_total` decimal(10,2) NOT NULL,
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`products`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `total_amount`, `shipping_fee`, `discount_amount`, `discount_rate`, `coupon_code`, `gift`, `last_total`, `products`, `created_at`, `updated_at`, `status`, `user_id`) VALUES
(1, 4339292, '114.96', '54.99', '0.00', 0, NULL, NULL, '169.95', '[{\"product_id\":1,\"quantity\":1},{\"product_id\":2,\"quantity\":3}]', '2023-12-10 16:38:31', '2023-12-11 10:32:56', 1, 1),
(2, 7884396, '114.96', '54.99', '0.00', 0, NULL, NULL, '169.95', '[{\"product_id\":1,\"quantity\":1},{\"product_id\":2,\"quantity\":3}]', '2023-12-10 16:43:21', '2023-12-11 13:59:12', 1, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `product_id` bigint(20) NOT NULL,
  `title` varchar(1024) DEFAULT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `category_title` varchar(1024) DEFAULT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `stock_quantity` bigint(20) DEFAULT NULL,
  `origin` varchar(1024) DEFAULT NULL,
  `roast_level` varchar(1024) DEFAULT NULL,
  `flavor_notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`flavor_notes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`product_id`, `title`, `category_id`, `category_title`, `description`, `price`, `stock_quantity`, `origin`, `roast_level`, `flavor_notes`) VALUES
(1, 'Harika Kahve', 2, 'Kahve', 'Özel karışım, harika lezzet!', 24.99, 50, 'Brezilya', 'Orta', '[\"Çikolata\",\"Fındık\",\"Vanilya\"]'),
(2, 'Yoğun Lezzet', 2, 'Kahve', 'Güçlü ve yoğun aromalar.', 29.99, 30, 'Kolombiya', 'Koyu', '[\"Karamel\",\"Kara Kiraz\",\"Baharatlı\"]'),
(3, 'Hafif Sipariş', 2, 'Kahve', 'Hafif ve ferahlatıcı bir deneyim.', 19.99, 40, 'Etiyopya', 'Hafif', '[\"Meyve\",\"Çiçek\",\"Nane\"]'),
(4, 'Espresso Gücü', 2, 'Kahve', 'Espresso severler için güçlü bir tercih.', 27.99, 25, 'İtalya', 'Orta-Koyu', '[\"Çikolata\",\"Fındık\",\"Kavrulmuş Ekmek\"]'),
(5, 'Özel Karışım', 2, 'Kahve', 'Uzmanlar tarafından özel olarak hazırlanan karışım.', 34.99, 20, 'Karışık', 'Orta', '[\"Karışık Notlar\"]'),
(6, 'Doğal Yollarla Yetiştirilmiş', 2, 'Kahve', 'Kimyasal gübre veya ilaç içermez.', 39.99, 15, 'Peru', 'Hafif', '[\"Meyve\",\"Çiçek\",\"Ahududu\"]'),
(7, 'Geleneksel Türk Kahvesi', 2, 'Kahve', 'Türk kahvesi keyfi evinizde!', 22.99, 35, 'Türkiye', 'Orta', NULL),
(8, 'Vanilla Dream', 2, 'Kahve', 'Vanilya sevenler için rüya gibi bir kahve.', 31.99, 28, 'Madagaskar', 'Orta', '[\"Vanilya\",\"Karamel\",\"Hafif Baharatlı\"]'),
(9, 'Organik Karadeniz', 2, 'Kahve', 'Doğal ve organik, Karadeniz\'in en iyisi.', 26.99, 22, 'Türkiye', 'Orta-Koyu', '[\"Çikolata\",\"Fındık\",\"Hafif Baharatlı\"]'),
(10, 'Özel Filtrasyon', 2, 'Kahve', 'Özel filtre yöntemiyle hazırlanmış.', 29.99, 18, 'Kenya', 'Orta', '[\"Meyve\",\"Çiçek\",\"Şeker Kamışı\"]'),
(11, 'Iced Coffee', 2, 'Kahve', 'Soğuk kahve keyfi!', 17.99, 45, 'Kolombiya', 'Hafif', '[\"Çikolata\",\"Kara Kiraz\",\"Buzlu\"]'),
(12, 'Kahve Çikolata Karışımı', 2, 'Kahve', 'İki lezzet bir arada.', 32.99, 23, 'Karışık', 'Orta-Koyu', '[\"Çikolata\",\"Vanilya\",\"Fındık\"]'),
(13, 'Bergamot Burst', 2, 'Kahve', 'Bergamot aromasıyla canlanın!', 28.99, 27, 'Kosta Rika', 'Orta', '[\"Bergamot\",\"Çiçek\",\"Meyve\"]'),
(14, 'Dark Delight', 2, 'Kahve', 'Koyu kavrulmuş bir zevk.', 36.99, 16, 'Brexit Coffee Co.', 'Koyu', '[\"Koyu Çikolata\",\"Fındık\",\"Kavrulmuş Ekmek\"]'),
(15, 'Sürpriz Karışım', 2, 'Kahve', 'Her fincanda farklı bir lezzet sürprizi!', 38.99, 14, 'Dünya Geneli', 'Orta-Koyu', '[\"Karışık Notlar\"]'),
(16, 'Şeker Kamışı Rüyası', 2, 'Kahve', 'Doğal şeker kamışı notalarıyla tatlı bir deneyim.', 30.99, 31, 'Brasil', 'Orta', '[\"Şeker Kamışı\",\"Vanilya\",\"Çikolata\"]'),
(17, 'Honey Hike', 2, 'Kahve', 'Ballı bir yolculuk!', 25.99, 29, 'Etiyopya', 'Hafif', '[\"Bal\",\"Meyve\",\"Çiçek\"]'),
(18, 'Mocha Magic', 2, 'Kahve', 'Mocha severler için sihirli bir lezzet.', 33.99, 21, 'Gana', 'Orta-Koyu', '[\"Çikolata\",\"Kahve\",\"Karamel\"]'),
(19, 'Exotic Espresso', 2, 'Kahve', 'Espresso sevenler için egzotik bir seçenek.', 35.99, 19, 'Endonezya', 'Koyu', '[\"Çikolata\",\"Baharatlı\",\"Vanilya\"]'),
(20, 'Tropikal Rüya', 2, 'Kahve', 'Tropikal meyve notalarıyla dolu bir rüya.', 37.99, 17, 'Kosta Rika', 'Orta', '[\"Mango\",\"Ananas\",\"Kokos\"]');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `password` varchar(400) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `password`) VALUES
(1, 'Buğra Şıkel', 'bugraskl@gmail.com', '5372150185', 'Örnek adres bilgisi', '25d55ad283aa400af464c76d713c07ad');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_code` (`order_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `product_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
