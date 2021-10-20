-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2018 at 06:35 PM
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

--
-- Dumping data for table `container_size_type`
--
/*INSERT INTO `user_profile` (`id`, `user_name`, `password`, `full_name`, `status`) VALUES
(6, 'oniel', '$2y$10$LscWjqNnwAy/NTLk4DSYwehjrI2HFK2MjrMQzQMYgxY7/47a99RvS', 'Oniel Charles', 'A');
*/

INSERT INTO `container_size_type` (`id`, `size_type_code`, `description`) VALUES
(1, '20FT', '20 Foot'),
(2, '40FT', '40 Feet'),
(3, '20G0', '20FT Dry Container');

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`id`, `currency_code`, `currency_name`) VALUES
(1, 'JMD', 'Jamaican Dollar'),
(2, 'USD', 'US Dollar');

--
-- Dumping data for table `currency_rate`
--

INSERT INTO `currency_rate` (`currency_id`, `effective_date`, `exchange_rate`, `id`) VALUES
(2, 20170101, '120.00', 1),
(2, 20180615, '155.02', 2);

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
(43, 29, 'Maintain EDI Codes', 'maintain_edi_codes.html', 7, NULL, 2, NULL, NULL),
(44, 2, 'Customers', 'maintain_client.html', 8, NULL, 2, NULL, NULL),
(45, 6, 'Bill of Laing Discount', 'billoflading_discount.html', 7, NULL, 2, NULL, NULL),
(46, 6, 'Close Accounts', 'billOfLading_Admin_Processed.html', 8, NULL, 2, NULL, NULL);
COMMIT;

INSERT INTO `user_option` (`menu_item_id`, `user_id`, `id`) VALUES
(38, 6, 11);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
