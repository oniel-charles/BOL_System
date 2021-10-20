-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2021 at 01:41 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dennisnew`
--

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `currency_code` char(10) DEFAULT NULL,
  `currency_name` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`id`, `currency_code`, `currency_name`) VALUES
(1, 'JMD', 'Jamaica Dollar'),
(2, 'USD', 'US Dollar');

-- --------------------------------------------------------

--
-- Table structure for table `currency_rate`
--

CREATE TABLE `currency_rate` (
  `currency_id` int(11) NOT NULL,
  `effective_date` int(11) DEFAULT NULL,
  `exchange_rate` decimal(7,2) DEFAULT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currency_rate`
--

INSERT INTO `currency_rate` (`currency_id`, `effective_date`, `exchange_rate`, `id`) VALUES
(2, 20180205, '120.00', 1),
(2, 20180723, '135.00', 2),
(2, 20180807, '140.00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `menu_group`
--

CREATE TABLE `menu_group` (
  `id` bigint(20) NOT NULL,
  `title` char(100) DEFAULT NULL,
  `menu_order` int(11) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `icon` char(100) DEFAULT NULL,
  `description` char(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

CREATE TABLE `menu_item` (
  `id` int(11) NOT NULL,
  `menu_group_id` bigint(20) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `menu_order` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `icon` char(100) DEFAULT NULL,
  `description` char(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_option`
--

CREATE TABLE `user_option` (
  `menu_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `user_name` char(60) DEFAULT NULL,
  `password` char(255) DEFAULT NULL,
  `full_name` char(70) DEFAULT NULL,
  `status` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_name`, `password`, `full_name`, `status`) VALUES
(6, 'oniel', '$2y$10$f50JPC8ZpdK42mx2gr/XJO8l/BMlJPqW0iTgikV9hE3qaPJaTQndy', 'Oniel Charles', 'A'),
(7, 'david', 'david', 'david', 'A'),
(8, 'krystal', 'krystal', 'krystal', 'A'),
(9, 'cassandra', '$2y$10$XNuzA3VSnqj1Z.UlbQ8A3eEcd0tuvip7JzH2EbJcEi8KNddR9p2.G', 'cassandra', 'I'),
(10, 'fabian', 'fabian', 'fabian', 'A'),
(11, 'Marline', '$2y$10$oMTABbxpOLxrg5mf6mkbJ.r3fBMVZcNMMI/iPfc3iGUmSnMrNnIcu', 'Marline', 'I'),
(12, 'Tesha', 'Tesha', 'Tesha', 'A'),
(13, 'jason', 'jason', 'jason', 'A'),
(14, 'jodi-ann', 'jodi-ann', 'jodi-ann', 'A'),
(15, 'brenda', '$2y$10$Jd1EX5s.F1.ozIzHEU5gCeuef49lHi5WxiY7pECwycdAsVc1OSzNS', 'Brenda White', 'I'),
(16, 'Floyd', 'Floyd', 'Floyd', 'A'),
(17, 'Denesha', 'Denesha', 'Denesha', 'A'),
(18, 'Ruth-ann', 'Ruth-ann', 'Ruth-ann', 'A'),
(19, 'Andrew', 'Andrew', 'Andrew', 'A'),
(20, 'Moneek', 'Moneek', 'Moneek', 'A'),
(21, 'Shanice', '$2y$10$kTp6HuxKmqm2JL/JmgLVbeUcRyPss9UYGSUWj4RvHO44IB5kuvtRO', 'Shanice', 'A'),
(22, 'kemar', '$2y$10$KFYUfWiXI1EV44opYvuSeurmZF6TgS8ADm69IJtnS1lKIaRYz0dea', 'kemar', 'A'),
(23, 'samuel', '$2y$10$/DlE9d4pJWAwhwCFOhwz9uj6Uy/Jr/nDmUGo6k.3JE2OoRfXGJmHm', 'samuel', 'A'),
(24, 'nickisha', '$2y$10$vYSYD6G.1PsDzgcQ2XKgQ.4yu4RZAWhWX7RE8LSkPIQmGEO2SrEQi', 'nickisha', 'A'),
(25, 'Kimberly', '$2y$10$0021/oKqVAseZqUeOzckUemYAwhS4GMT2J4QssqBk3ya2wqgsi7im', 'Kimberly Goode', 'A'),
(26, 'Seana-kay', 'Seana-kay', 'Seana-kay', 'A'),
(27, 'Koleen', '$2y$10$1ui1Y3iP3CrS1xkXZkyvR.pYxWiV5DrcDy57opGYg.CGBQ8IvlIi2', 'Koleen Brown', 'A'),
(28, 'Rolanda', '$2y$10$jkescvhfsaYAXGqYa9eaauWWGEp7XSv6AAhExur3izxfeFicTJ2ba', 'Rolanda', 'A'),
(29, 'nigel', '$2y$10$2NNNrACz3CALJ.muofc6.OLlRcn4VNlh2V83ms6zLM9Z3V84i3FbC', 'Nigel Johnson', 'A'),
(30, 'Nicketa', '$2y$10$7nrc2oQOI1.nEy.2fChSkuNYF7CpD78NbIs7RfNPBfYkC6UrbI4iO', 'Nicketa Watson', 'A'),
(31, 'dennis', '$2y$10$iZTQCnsnab/lRv5XYbS5oul5dA8gBmjnUUt7gW1pFrhIusljqnm6u', 'Dennis', 'A'),
(32, 'normesha', '$2y$10$eGYH0SiKsZhYf6QTwXrame/HkVAevvee8KVZ61I0k/749m6efsHie', 'normesha maragh', 'A'),
(33, 'Melissa', '$2y$10$U6fyWwo8Mrty/ncwPkiB4e3Ywzvew1WptLtt6aIgc4vKZTTo2S2GG', 'Melissa Gooden', 'A'),
(34, 'SADIKE', '$2y$10$NJqgdic822b6LJvxE78jx.e5Z3nMZu.vdaBgqPYi1BSecVmH/E7z6', 'SADIKE SADLER', 'A'),
(35, 'MARLON', '$2y$10$7sc7M2z.K0dy7micX3f75.bE2sfR0z3MeC4dUujA62AUR6LsoAFjG', 'MARLON CHAMBERS', 'A'),
(36, 'Stephan', '$2y$10$g1xixcZm4fsaiISAdrGGPunL5fyG.ToRSCzn1BpHDXcrWAGFJ95Fe', 'Stephan Gordon', 'A'),
(37, 'NADINE HALL', '$2y$10$ybywnVMvUDp420VeVWZpruDXqyPUC6wlNJDe0/Her7x8DJCMAdc5q', 'NADINE HALL', 'A');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency_rate`
--
ALTER TABLE `currency_rate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Index_date` (`currency_id`,`effective_date`);

--
-- Indexes for table `menu_group`
--
ALTER TABLE `menu_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_item_` (`menu_group_id`);

--
-- Indexes for table `user_option`
--
ALTER TABLE `user_option`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fk_user_option_user_profile` (`user_id`,`menu_item_id`) USING BTREE,
  ADD KEY `menu_item_id` (`menu_item_id`,`user_id`) USING BTREE;

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `currency_rate`
--
ALTER TABLE `currency_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_group`
--
ALTER TABLE `menu_group`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `user_option`
--
ALTER TABLE `user_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1053;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `currency_rate`
--
ALTER TABLE `currency_rate`
  ADD CONSTRAINT `fk_currency_rate_currency` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`);

--
-- Constraints for table `user_option`
--
ALTER TABLE `user_option`
  ADD CONSTRAINT `fk_user_option_menu_item` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`id`),
  ADD CONSTRAINT `fk_user_option_user_profile` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
