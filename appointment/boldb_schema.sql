-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2018 at 10:42 AM
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
-- Database: `boldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill_of_lading`
--

CREATE TABLE `bill_of_lading` (
  `id` int(11) NOT NULL,
  `parent_bol` smallint(6) DEFAULT NULL,
  `bol_total` decimal(10,2) DEFAULT NULL,
  `bill_of_lading_number` char(25) DEFAULT NULL,
  `port_of_origin` int(11) DEFAULT NULL,
  `port_of_loading` int(11) DEFAULT NULL,
  `port_of_discharge` int(11) DEFAULT NULL,
  `port_of_delivery` int(11) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `consignee_name` char(40) DEFAULT NULL,
  `consignee_address` char(200) DEFAULT NULL,
  `consignee_phone_num` char(15) DEFAULT NULL,
  `consignee_id` int(11) DEFAULT NULL,
  `shipper_name` char(40) DEFAULT NULL,
  `shipper_address` char(200) DEFAULT NULL,
  `shipper_phone_num` char(15) DEFAULT NULL,
  `notify_name` char(40) DEFAULT NULL,
  `notify_address` char(200) DEFAULT NULL,
  `notify_date` int(11) DEFAULT NULL,
  `notify_phone_num` char(15) DEFAULT NULL,
  `master_bol_id` int(11) DEFAULT NULL,
  `voyage_id` int(11) NOT NULL,
  `order_processed` smallint(6) DEFAULT NULL,
  `receipt_processed` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bill_of_lading_container`
--

CREATE TABLE `bill_of_lading_container` (
  `id` int(11) NOT NULL,
  `container_number` char(15) DEFAULT NULL,
  `container_size_type_id` int(11) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bill_of_lading_detail`
--

CREATE TABLE `bill_of_lading_detail` (
  `id` int(11) NOT NULL,
  `billoflading_id` int(11) DEFAULT NULL,
  `package_type_id` int(11) DEFAULT NULL,
  `commodity_id` int(11) DEFAULT NULL,
  `Description_of_goods` varchar(500) DEFAULT NULL,
  `number_of_items` int(11) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `measure` decimal(10,2) DEFAULT NULL,
  `weight_unit` char(10) DEFAULT NULL,
  `measure_unit` char(10) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `depth` decimal(10,2) DEFAULT NULL,
  `breath` decimal(10,2) DEFAULT NULL,
  `volume` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bill_of_lading_other_charge`
--

CREATE TABLE `bill_of_lading_other_charge` (
  `id` int(11) NOT NULL,
  `charge_item_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `prepaid_flag` char(10) DEFAULT NULL,
  `attract_gct` smallint(6) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `charge_item`
--

CREATE TABLE `charge_item` (
  `id` int(11) NOT NULL,
  `item_code` char(20) DEFAULT NULL,
  `description` char(50) DEFAULT NULL,
  `basis` char(20) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `item_rate` decimal(10,2) DEFAULT NULL,
  `commodity_id` int(11) DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `print_seperate` smallint(6) DEFAULT NULL,
  `gct` smallint(6) DEFAULT NULL,
  `system_def` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `charge_item_rate`
--

CREATE TABLE `charge_item_rate` (
  `id` int(11) NOT NULL,
  `effective_date` int(11) DEFAULT NULL,
  `rate` decimal(5,2) DEFAULT NULL,
  `charge_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `client_code` char(10) DEFAULT NULL,
  `client_name` char(40) DEFAULT NULL,
  `client_address` char(200) DEFAULT NULL,
  `phone_number` char(25) DEFAULT NULL,
  `email_address` char(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `commodity`
--

CREATE TABLE `commodity` (
  `id` int(11) NOT NULL,
  `commodity_code` char(10) DEFAULT NULL,
  `description` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `company_name` char(40) DEFAULT NULL,
  `company_address` char(100) DEFAULT NULL,
  `phone` char(20) DEFAULT NULL,
  `email` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `container_size_type`
--

CREATE TABLE `container_size_type` (
  `id` int(11) NOT NULL,
  `size_type_code` char(10) DEFAULT NULL,
  `description` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `country_code` char(10) DEFAULT NULL,
  `country_name` char(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `id` int(11) NOT NULL,
  `currency_code` char(10) DEFAULT NULL,
  `currency_name` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `custom_office`
--

CREATE TABLE `custom_office` (
  `id` int(11) NOT NULL,
  `code` char(20) DEFAULT NULL,
  `description` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `edi_translation`
--

CREATE TABLE `edi_translation` (
  `id` int(11) NOT NULL,
  `type` char(15) DEFAULT NULL,
  `internal_code` char(15) DEFAULT NULL,
  `external_code` char(255) DEFAULT NULL,
  `code_id` int(11) DEFAULT NULL,
  `translation_source_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`id`, `menu_group_id`, `title`, `url`, `menu_order`, `status`, `level`, `icon`, `description`) VALUES
(2, 0, 'Maintenance', '', 3, NULL, 1, 'fa fa-cogs', NULL),
(3, 2, 'Vessel', 'maintain_vessel.html', 1, NULL, 2, NULL, NULL),
(4, 2, 'Port', 'maintain_port.html', 2, NULL, 2, NULL, NULL),
(5, 2, 'Country', 'maintain_country.html', 3, NULL, 2, NULL, NULL),
(6, 0, 'Manifest Processing', NULL, 1, NULL, 1, 'fa fa-anchor', NULL),
(7, 0, 'Calendar', 'calendar.html', 3, NULL, 1, 'fa fa-calendar', NULL),
(8, 6, 'Voyage', 'maintain_voyage.html', 1, NULL, 2, NULL, NULL),
(9, 0, 'Reports', NULL, 7, NULL, 1, 'fa fa-bar-chart', NULL),
(10, 9, 'Item Charge Report', 'rpt_collections_by_item.html', 1, NULL, 2, NULL, NULL),
(11, 9, 'Print Daily Receipt', 'rpt_receipt_listing.html', 2, NULL, 2, NULL, NULL),
(12, 9, 'Print Daily Order', 'rpt_daily_orders.html', 3, NULL, 2, NULL, NULL),
(13, 9, 'Print Cancel Receipt', 'rpt_cancel_receipts.html', 3, 0, 2, NULL, NULL),
(14, 9, 'Print Cancel Order', 'rpt_cancel_orders.html', 5, NULL, 2, NULL, NULL),
(16, 6, 'Bill of Lading', 'maintain_bill_of_lading.html', 2, NULL, 2, NULL, NULL),
(17, 6, 'Bill of Lading Inquiry', 'BillOfLading_Enquiry.html', 3, NULL, 2, NULL, NULL),
(18, 0, 'Billing', NULL, 2, NULL, 1, 'fa fa-dollar', NULL),
(19, 18, 'Receipt', 'manage_receipts.html', 1, NULL, 2, NULL, NULL),
(20, 18, 'Order', 'manage_order.html', 2, NULL, 2, NULL, NULL),
(21, 0, 'Charges', NULL, 4, NULL, 1, 'fa fa-list-alt', NULL),
(22, 21, 'Charge Item', 'Maintain_Charges.html', 1, NULL, 2, NULL, NULL),
(24, 21, 'GCT Rate', 'maintain_gct_rates.html', 3, NULL, 2, NULL, NULL),
(25, 2, 'Currency', 'Maintain_Currency.html', 4, NULL, 2, NULL, NULL),
(26, 2, 'Package', 'Maintain_Package.html', 5, NULL, 2, NULL, NULL),
(27, 2, 'Commodity', 'maintain_commodity.html', 6, NULL, 2, NULL, NULL),
(28, 2, 'Container Size Type', 'maintain_container_size_type.html', 7, NULL, 2, NULL, NULL),
(29, NULL, 'EDI', NULL, 6, NULL, 1, 'fa fa-exchange', NULL),
(30, 29, 'Export to Customs', 'exportAnsix12.html', 1, NULL, 2, NULL, NULL),
(31, 29, 'Export ASYCUDA', 'exportAsycuda.html', 3, NULL, 2, NULL, NULL),
(32, 29, 'Export PCS', 'exportPCS.html', 4, NULL, 2, NULL, NULL),
(33, 29, 'Manifest Import ', 'importManifest.html', 1, NULL, 2, NULL, NULL),
(34, NULL, 'Home', NULL, 0, NULL, 1, 'fa fa-home', NULL),
(35, 34, 'Dashboard', 'index.html', 1, NULL, 2, '', NULL),
(36, NULL, 'Administration', NULL, 5, NULL, 1, 'fa fa-group', NULL),
(37, 36, 'Maintain Users', 'maintain_user.html', 1, NULL, 2, '', NULL),
(38, 36, 'User Options', 'user_options.html', 2, NULL, 2, NULL, NULL),
(39, 9, 'User Queries', 'rpt_excel.html', 6, NULL, 2, NULL, NULL),
(40, 6, 'Batch Orders', 'batch_order.html', 4, NULL, 2, NULL, NULL),
(41, 21, 'Exchange Rate', 'maintain_exchange.html', 5, NULL, 2, NULL, NULL),
(42, 6, 'Archived BLs', 'billOfLading_History_Enquiry.html', 6, NULL, 2, NULL, NULL),
(43, 29, 'Maintain EDI Codes', 'maintain_edi_codes.html', 7, NULL, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `id` int(11) NOT NULL,
  `package_code` char(10) DEFAULT NULL,
  `description` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `port`
--

CREATE TABLE `port` (
  `id` int(11) NOT NULL,
  `port_code` char(10) DEFAULT NULL,
  `port_name` char(60) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `receipt_date` int(11) DEFAULT NULL,
  `receipt_time` smallint(6) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `payee` char(40) DEFAULT NULL,
  `receipt_total` decimal(12,2) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `local_total` decimal(12,2) DEFAULT NULL,
  `exchange_rate` decimal(8,2) DEFAULT NULL,
  `printed` smallint(6) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL,
  `cancel_by` int(11) DEFAULT NULL,
  `cancel_date` int(11) DEFAULT NULL,
  `cancel_time` smallint(6) DEFAULT NULL,
  `cancelled` smallint(6) DEFAULT NULL,
  `customer_identification` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receipt_detail`
--

CREATE TABLE `receipt_detail` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `bol_id` int(11) DEFAULT NULL,
  `charge_item_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `comment` char(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shipment_order`
--

CREATE TABLE `shipment_order` (
  `id` int(11) NOT NULL,
  `voyage_id` int(11) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `locked` int(11) DEFAULT NULL,
  `printed` smallint(6) DEFAULT NULL,
  `cancelled` smallint(6) DEFAULT NULL,
  `order_date` int(11) DEFAULT NULL,
  `cancel_by` int(11) DEFAULT NULL,
  `cancel_date` int(11) DEFAULT NULL,
  `cancel_time` smallint(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `system_values`
--

CREATE TABLE `system_values` (
  `id` int(11) NOT NULL,
  `description` char(30) DEFAULT NULL,
  `data_type` char(10) DEFAULT NULL,
  `data_value` char(30) DEFAULT NULL,
  `code` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `system_values`
--

INSERT INTO `system_values` (`id`, `description`, `data_type`, `data_value`, `code`) VALUES
(1, 'ANSIX12 export sequence', 'int', '34', 'ANSIX12'),
(2, 'freight_id', 'dec', '3', 'freight_id');

-- --------------------------------------------------------

--
-- Table structure for table `translation_source`
--

CREATE TABLE `translation_source` (
  `id` int(11) NOT NULL,
  `code` char(15) DEFAULT NULL,
  `description` char(30) DEFAULT NULL
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
(6, 'oniel', '$2y$10$LscWjqNnwAy/NTLk4DSYwehjrI2HFK2MjrMQzQMYgxY7/47a99RvS', 'Oniel Charles', 'A'),
(9, 'admin', '$2y$10$UPXAEPjUuUyrR7Sb3RdsmOSfCek9zFXeMx7Wo7qwZE8/aTN9Lc8OK', 'ADMIN', 'A'),
(12, 'donna', '$2y$10$VKzXbHsucqWkZ4OeUBE7neCmtu8fQG8rywDL1.fBOTFmJX5uCF7Ri', 'Donna Anderson', 'A'),
(13, 'howard', '$2y$10$vT.e9lbDHan11tzYZtzHGeboJ6jCRz0ycjyEPj6PxdIdTkr2Qd4F2', 'Howard James', 'A'),
(14, 'kemarg', '$2y$10$iASkPmWW/PGslNJIAbZmKuQWXIHGYi3lW/oomqKanbqs0eTxVBDJ6', 'Kemar Goodhall', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `vessel`
--

CREATE TABLE `vessel` (
  `id` int(11) NOT NULL,
  `vessel_name` char(50) DEFAULT NULL,
  `vessel_code` char(10) DEFAULT NULL,
  `lloyd_number` char(20) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `voyage`
--

CREATE TABLE `voyage` (
  `id` int(11) NOT NULL,
  `vessel_id` int(11) DEFAULT NULL,
  `voyage_number` char(20) DEFAULT NULL,
  `departure_date` int(11) DEFAULT NULL,
  `arrival_date` int(11) DEFAULT NULL,
  `stripped` smallint(6) DEFAULT NULL,
  `stripped_date` int(11) DEFAULT NULL,
  `mby_arrival_date` int(11) DEFAULT NULL,
  `mby_vessel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `voyage_container`
--

CREATE TABLE `voyage_container` (
  `id` int(11) NOT NULL,
  `voyage_id` int(11) DEFAULT NULL,
  `container_number` char(15) DEFAULT NULL,
  `port_origin` int(11) DEFAULT NULL,
  `seal` char(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill_of_lading`
--
ALTER TABLE `bill_of_lading`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bl` (`voyage_id`,`bill_of_lading_number`),
  ADD KEY `fk_voyage_idx` (`voyage_id`),
  ADD KEY `fk_bol_port_delivery` (`port_of_delivery`),
  ADD KEY `fk_bol_port_discharge` (`port_of_discharge`),
  ADD KEY `fk_bol_port_loading` (`port_of_loading`),
  ADD KEY `fk_bol_port_origin` (`port_of_origin`);

--
-- Indexes for table `bill_of_lading_container`
--
ALTER TABLE `bill_of_lading_container`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bol_container_size_type` (`container_size_type_id`),
  ADD KEY `fk_bill_of_lading_container_` (`billoflading_id`);

--
-- Indexes for table `bill_of_lading_detail`
--
ALTER TABLE `bill_of_lading_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bol_detail_bill_of_lading` (`billoflading_id`);

--
-- Indexes for table `bill_of_lading_other_charge`
--
ALTER TABLE `bill_of_lading_other_charge`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bycharges` (`billoflading_id`,`charge_item_id`),
  ADD KEY `fk_bol_other_charge_currency` (`currency_id`),
  ADD KEY `fk_bol_other_charge_item` (`charge_item_id`);

--
-- Indexes for table `charge_item`
--
ALTER TABLE `charge_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_charge_item_commodity` (`commodity_id`),
  ADD KEY `fk_charge_item_package` (`package_id`);

--
-- Indexes for table `charge_item_rate`
--
ALTER TABLE `charge_item_rate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charge_FK_idx` (`charge_item_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commodity`
--
ALTER TABLE `commodity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `container_size_type`
--
ALTER TABLE `container_size_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `custom_office`
--
ALTER TABLE `custom_office`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edi_translation`
--
ALTER TABLE `edi_translation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_edi_translation_translation_source1_idx` (`translation_source_id`);

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
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `port`
--
ALTER TABLE `port`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_receipt_currency` (`currency_id`),
  ADD KEY `fk_receipt_bol` (`billoflading_id`),
  ADD KEY `fk_receipt_client` (`client_id`),
  ADD KEY `fk_receipt_created_by` (`created_by`),
  ADD KEY `fk_receipt_deleted_by` (`cancel_by`);

--
-- Indexes for table `receipt_detail`
--
ALTER TABLE `receipt_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_receipt_detail_charge_item` (`charge_item_id`),
  ADD KEY `fk_Receipt` (`receipt_id`);

--
-- Indexes for table `shipment_order`
--
ALTER TABLE `shipment_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_bol` (`billoflading_id`),
  ADD KEY `fk_order_created_by` (`created_by`),
  ADD KEY `fk_order_voyage` (`voyage_id`);

--
-- Indexes for table `system_values`
--
ALTER TABLE `system_values`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translation_source`
--
ALTER TABLE `translation_source`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codeIndex` (`code`);

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
-- Indexes for table `vessel`
--
ALTER TABLE `vessel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vessel_country` (`country_id`);

--
-- Indexes for table `voyage`
--
ALTER TABLE `voyage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voyage_container`
--
ALTER TABLE `voyage_container`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_voyage_container_voyage` (`voyage_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bill_of_lading`
--
ALTER TABLE `bill_of_lading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2191;

--
-- AUTO_INCREMENT for table `bill_of_lading_container`
--
ALTER TABLE `bill_of_lading_container`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bill_of_lading_detail`
--
ALTER TABLE `bill_of_lading_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=775;

--
-- AUTO_INCREMENT for table `bill_of_lading_other_charge`
--
ALTER TABLE `bill_of_lading_other_charge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `charge_item`
--
ALTER TABLE `charge_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `charge_item_rate`
--
ALTER TABLE `charge_item_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commodity`
--
ALTER TABLE `commodity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `container_size_type`
--
ALTER TABLE `container_size_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `currency_rate`
--
ALTER TABLE `currency_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `custom_office`
--
ALTER TABLE `custom_office`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `edi_translation`
--
ALTER TABLE `edi_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `menu_group`
--
ALTER TABLE `menu_group`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `port`
--
ALTER TABLE `port`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `receipt_detail`
--
ALTER TABLE `receipt_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `shipment_order`
--
ALTER TABLE `shipment_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `system_values`
--
ALTER TABLE `system_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `translation_source`
--
ALTER TABLE `translation_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_option`
--
ALTER TABLE `user_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vessel`
--
ALTER TABLE `vessel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `voyage`
--
ALTER TABLE `voyage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `voyage_container`
--
ALTER TABLE `voyage_container`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill_of_lading`
--
ALTER TABLE `bill_of_lading`
  ADD CONSTRAINT `fk_bol_port_delivery` FOREIGN KEY (`port_of_delivery`) REFERENCES `port` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bol_port_discharge` FOREIGN KEY (`port_of_discharge`) REFERENCES `port` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_bol_port_loading` FOREIGN KEY (`port_of_loading`) REFERENCES `port` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_bol_port_origin` FOREIGN KEY (`port_of_origin`) REFERENCES `port` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_voyage` FOREIGN KEY (`voyage_id`) REFERENCES `voyage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bill_of_lading_container`
--
ALTER TABLE `bill_of_lading_container`
  ADD CONSTRAINT `fk_bill_of_lading_container_` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bol_container_size_type` FOREIGN KEY (`container_size_type_id`) REFERENCES `container_size_type` (`id`);

--
-- Constraints for table `bill_of_lading_detail`
--
ALTER TABLE `bill_of_lading_detail`
  ADD CONSTRAINT `fk_bol_detail_bill_of_lading` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bill_of_lading_other_charge`
--
ALTER TABLE `bill_of_lading_other_charge`
  ADD CONSTRAINT `fk_bol_other_charge_bol` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bol_other_charge_commodity` FOREIGN KEY (`currency_id`) REFERENCES `commodity` (`id`),
  ADD CONSTRAINT `fk_bol_other_charge_currency` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  ADD CONSTRAINT `fk_bol_other_charge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `charge_item` (`id`);

--
-- Constraints for table `charge_item`
--
ALTER TABLE `charge_item`
  ADD CONSTRAINT `fk_charge_item_commodity` FOREIGN KEY (`commodity_id`) REFERENCES `commodity` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_charge_item_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `charge_item_rate`
--
ALTER TABLE `charge_item_rate`
  ADD CONSTRAINT `charge_FK` FOREIGN KEY (`charge_item_id`) REFERENCES `charge_item` (`id`);

--
-- Constraints for table `currency_rate`
--
ALTER TABLE `currency_rate`
  ADD CONSTRAINT `fk_currency_rate_currency` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`);

--
-- Constraints for table `edi_translation`
--
ALTER TABLE `edi_translation`
  ADD CONSTRAINT `fk_edi_translation_translation_source1` FOREIGN KEY (`translation_source_id`) REFERENCES `translation_source` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `port`
--
ALTER TABLE `port`
  ADD CONSTRAINT `fk_port_country` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `fk_receipt_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_receipt_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_profile` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_receipt_currency` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  ADD CONSTRAINT `fk_receipt_deleted_by` FOREIGN KEY (`cancel_by`) REFERENCES `user_profile` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `receipt_detail`
--
ALTER TABLE `receipt_detail`
  ADD CONSTRAINT `fk_Receipt` FOREIGN KEY (`receipt_id`) REFERENCES `receipt` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_receipt_detail_charge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `charge_item` (`id`);

--
-- Constraints for table `shipment_order`
--
ALTER TABLE `shipment_order`
  ADD CONSTRAINT `fk_order_bol` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`),
  ADD CONSTRAINT `fk_order_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_profile` (`id`),
  ADD CONSTRAINT `fk_order_voyage` FOREIGN KEY (`voyage_id`) REFERENCES `voyage` (`id`);

--
-- Constraints for table `user_option`
--
ALTER TABLE `user_option`
  ADD CONSTRAINT `fk_user_option_menu_item` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`id`),
  ADD CONSTRAINT `fk_user_option_user_profile` FOREIGN KEY (`user_id`) REFERENCES `user_profile` (`id`);

--
-- Constraints for table `vessel`
--
ALTER TABLE `vessel`
  ADD CONSTRAINT `fk_vessel_country` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Constraints for table `voyage_container`
--
ALTER TABLE `voyage_container`
  ADD CONSTRAINT `fk_voyage_container_voyage` FOREIGN KEY (`voyage_id`) REFERENCES `voyage` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
