-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2021 at 01:47 PM
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
-- Database: `jacksondb2`
--

-- --------------------------------------------------------

--
-- Table structure for table `billoflading_discount`
--

CREATE TABLE `billoflading_discount` (
  `id` int(11) NOT NULL,
  `charge_item_id` int(11) NOT NULL,
  `basis` varchar(15) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `billoflading_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `consignee_name` char(100) DEFAULT NULL,
  `consignee_address` char(255) DEFAULT NULL,
  `consignee_phone_num` char(30) DEFAULT NULL,
  `consignee_id` int(11) DEFAULT NULL,
  `shipper_name` char(100) DEFAULT NULL,
  `shipper_address` char(255) DEFAULT NULL,
  `shipper_phone_num` char(30) DEFAULT NULL,
  `notify_name` char(40) DEFAULT NULL,
  `notify_address` char(255) DEFAULT NULL,
  `notify_date` int(11) DEFAULT NULL,
  `notify_phone_num` char(15) DEFAULT NULL,
  `master_bol_id` int(11) DEFAULT NULL,
  `voyage_id` int(11) NOT NULL,
  `order_processed` smallint(6) DEFAULT NULL,
  `receipt_processed` smallint(6) DEFAULT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `edit_type` char(25) DEFAULT NULL,
  `value_of_goods` decimal(12,2) DEFAULT NULL,
  `value_currency` int(11) DEFAULT NULL,
  `customer_type` char(15) DEFAULT NULL,
  `status` char(10) DEFAULT NULL
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

--
-- Dumping data for table `charge_item`
--

INSERT INTO `charge_item` (`id`, `item_code`, `description`, `basis`, `currency_id`, `item_rate`, `commodity_id`, `package_id`, `print_seperate`, `gct`, `system_def`) VALUES
(-2, 'SUR', 'SUR Charge', '', 0, '0.00', NULL, NULL, 0, 0, NULL),
(-1, 'GCT', 'GCT', '', 0, '0.00', NULL, NULL, 0, 0, NULL),
(24, 'OFFICE', 'Office Charge', 'fixed', 1, '100.00', 26, 33, 0, 0, NULL),
(25, 'KWL', 'Kingston Wharves', 'fixed', 1, '790.00', 26, 33, 0, 0, NULL),
(26, 'FRT', 'Freight', 'fixed', 2, '40.00', 26, 33, 0, 0, NULL),
(27, 'ADMINFEE', 'Admin Fee ( Warfage, Stripping, BL Fee)', 'fixed', 1, '0.00', 26, 33, 0, 0, NULL),
(28, 'TRANSP', 'Transportation', 'fixed', 1, '0.00', 26, 33, 0, 0, NULL),
(29, 'P-SERVICE', 'PRE-CLEARANCE SERVICE', 'fixed', 1, '0.00', 26, 33, 0, 0, NULL),
(30, 'DUTY', 'CUSTOMS DUTY', 'fixed', 1, '0.00', 26, 33, 0, 0, NULL),
(31, 'FTC', 'FREIGHT COLLECT', 'fixed', 2, '0.00', 26, 33, 0, 0, NULL),
(32, 'AMEND', 'Amendment', 'fixed', 1, '1000.00', 26, 33, 0, 0, NULL);

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

--
-- Dumping data for table `charge_item_rate`
--

INSERT INTO `charge_item_rate` (`id`, `effective_date`, `rate`, `charge_item_id`) VALUES
(3, 20210601, '15.00', -1);

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
-- Table structure for table `client_discount`
--

CREATE TABLE `client_discount` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `charge_item_id` int(11) NOT NULL,
  `basis` varchar(15) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL
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

--
-- Dumping data for table `commodity`
--

INSERT INTO `commodity` (`id`, `commodity_code`, `description`) VALUES
(1, 'KW', 'Kitchen Ware'),
(2, 'SP', 'SPICES1'),
(3, 'PNT', 'PAINT'),
(4, 'PL', 'PLY'),
(5, 'FC', 'Food & Clothing'),
(6, 'F', 'Food'),
(7, 'LAD', 'Ladder'),
(8, 'C', 'Clothing'),
(9, 'CP', 'Computer Parts'),
(10, 'G', 'Generator'),
(11, 'COU', 'Couch'),
(12, 'RE', 'Refridgerator'),
(13, 'STR', 'Stroller'),
(14, 'BABE', 'Baby Items'),
(15, 'W', 'Washer'),
(16, 'BK', 'Book'),
(17, 'PC', 'Computer'),
(18, 'PW', 'Power Wash'),
(19, 'PR', 'Printer'),
(20, 'M', 'Mattress'),
(21, 'D', 'Diapers'),
(22, 'ST', 'Stove'),
(24, 'P', 'PAMPERS'),
(25, 'T', 'Television'),
(26, 'APP', 'Appliances'),
(27, 'CH', 'Chairs'),
(28, 'MB', 'Motor Bike'),
(29, 'BIC', 'Bicycle'),
(30, 'FN', 'FURNITURE'),
(31, 'KID TBL', 'Kid Table & Chairs Set'),
(32, 'WHL', 'Wheel Chair'),
(33, 'MIRR', 'Mirror'),
(34, 'USC', 'USED CLOTHING'),
(35, 'PLC', 'Plastic Container'),
(36, 'TY', 'Tyres'),
(37, 'FI', 'Food & Linen'),
(38, 'TL', 'Toiletries'),
(39, 'HSP', 'Hospital Items'),
(40, 'TYS', 'TOYS'),
(41, 'AP', 'Auto Parts'),
(42, 'MSC', 'MUSICAL INSTRUMENTS'),
(43, 'WD', 'Weed Wacker'),
(44, 'SH', 'Shoes'),
(45, 'SCH', 'School supplies'),
(46, 'TLS', 'Tiles'),
(47, 'FUC', 'Food & Used Clothing'),
(48, 'FT', 'Food & Toiletries'),
(49, 'FA', 'FRAMES'),
(50, 'PF', 'PICTURE FRAMES'),
(51, 'MI', 'MICROWAVE'),
(52, 'CLT', 'Clothing & Toiletries'),
(53, 'MA', 'Machine'),
(54, 'EQ', 'equipment'),
(55, 'IG', 'Igloo'),
(56, 'HH', 'Household items'),
(57, 'GH', 'Groceries & household items');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `company_name` char(50) DEFAULT NULL,
  `company_address` char(100) DEFAULT NULL,
  `phone` char(20) DEFAULT NULL,
  `email` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `company_name`, `company_address`, `phone`, `email`) VALUES
(1, 'Jackson\'s Q1 International Shipping Network Ltd', 'Unit #2 195 Second Street, Newport West Kingston 13, Jamaica W.I.', '876-7648758', 'okland@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `container_size_type`
--

CREATE TABLE `container_size_type` (
  `id` int(11) NOT NULL,
  `size_type_code` char(10) DEFAULT NULL,
  `description` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `container_size_type`
--

INSERT INTO `container_size_type` (`id`, `size_type_code`, `description`) VALUES
(1, '20FT', '20 Foot Container'),
(2, '40HQ', '40 ft High Cube');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `country_code` char(10) DEFAULT NULL,
  `country_name` char(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `country_code`, `country_name`) VALUES
(2, 'AD', 'Andorra'),
(3, 'AE', 'United Arab Emirates'),
(4, 'AF', 'Afghanistan'),
(5, 'AG', 'Antigua'),
(7, 'AI', 'Anguilla'),
(8, 'AL', 'Albania'),
(9, 'AM', 'Armenia'),
(10, 'AN', 'Bonaire'),
(16, 'AO', 'Angola'),
(17, 'AR', 'Argentina'),
(18, 'AS', 'American Samoa'),
(19, 'AT', 'Austria'),
(20, 'AU', 'Australia'),
(21, 'AW', 'Aruba'),
(22, 'AZ', 'Azerbaijan'),
(23, 'BA', 'Bosnia-Herzegovina'),
(24, 'BB', 'Barbados'),
(25, 'BD', 'Bangladesh'),
(26, 'BE', 'Belgium'),
(27, 'BF', 'Burkina Faso'),
(28, 'BG', 'Bulgaria'),
(29, 'BH', 'Bahrain'),
(30, 'BI', 'Burundi'),
(31, 'BJ', 'Benin'),
(32, 'BM', 'Bermuda'),
(33, 'BN', 'Brunei'),
(34, 'BO', 'Bolivia'),
(35, 'BR', 'Brazil'),
(36, 'BS', 'Bahamas'),
(37, 'BT', 'Bhutan'),
(38, 'BW', 'Botswana'),
(39, 'BY', 'Belarus'),
(40, 'BZ', 'Belize'),
(41, 'CA', 'Canada'),
(42, 'CD', 'Congo, Dem. Rep. of'),
(43, 'CG', 'Congo'),
(44, 'CH', 'Switzerland'),
(45, 'CI', 'Ivory Coast'),
(46, 'CK', 'Cook Islands'),
(47, 'CL', 'Chile'),
(48, 'CM', 'Cameroon'),
(49, 'CN', 'China,'),
(50, 'CO', 'Colombia'),
(51, 'CR', 'Costa Rica'),
(52, 'CS', 'Serbia and Montenegro'),
(53, 'CV', 'Cape Verde'),
(54, 'CY', 'Cyprus'),
(55, 'CZ', 'Czech Republic'),
(56, 'DE', 'Germany'),
(57, 'DJ', 'Djibouti'),
(58, 'DK', 'Denmark'),
(59, 'DM', 'Dominica'),
(60, 'DO', 'Dominican Republic'),
(61, 'DZ', 'Algeria'),
(62, 'EC', 'Ecuador'),
(63, 'EE', 'Estonia'),
(64, 'EG', 'Egypt'),
(65, 'ER', 'Eritrea'),
(66, 'ES', 'Spain'),
(67, 'ET', 'Ethiopia'),
(68, 'FI', 'Finland'),
(69, 'FJ', 'Fiji'),
(70, 'FM', 'Micronesia'),
(71, 'FO', 'Faeroe Islands'),
(72, 'FR', 'France'),
(73, 'GA', 'Gabon'),
(74, 'GB', 'Great Britain'),
(75, 'GD', 'Grenada'),
(76, 'GE', 'Georgia, Republic of'),
(77, 'GF', 'French Guiana'),
(78, 'GH', 'Ghana'),
(79, 'GI', 'Gibraltar'),
(80, 'GL', 'Greenland'),
(81, 'GM', 'Gambia'),
(82, 'GN', 'Guinea'),
(83, 'GP', 'Guadeloupe'),
(85, 'GR', 'Greece'),
(86, 'GT', 'Guatemala'),
(87, 'GU', 'Guam'),
(88, 'GY', 'Guyana'),
(89, 'HK', 'Hong Kong'),
(90, 'HN', 'Honduras'),
(91, 'HR', 'Croatia'),
(92, 'HT', 'Haiti'),
(93, 'HU', 'Hungary'),
(94, 'ID', 'Indonesia'),
(95, 'IE', 'Ireland, Republic of'),
(96, 'IL', 'Israel'),
(97, 'IN', 'India'),
(98, 'IQ', 'Iraq'),
(99, 'IS', 'Iceland'),
(100, 'IT', 'Italy'),
(103, 'JM', 'Jamaica'),
(104, 'JO', 'Jordan'),
(105, 'JP', 'Japan'),
(106, 'KE', 'Kenya'),
(107, 'KG', 'Kyrgyzstan'),
(108, 'KH', 'Cambodia'),
(109, 'KN', 'St. Kitts and Nevis'),
(110, 'KR', 'Korea, South'),
(111, 'KW', 'Kuwait'),
(112, 'KY', 'Cayman Islands'),
(113, 'KZ', 'Kazakhstan'),
(114, 'LA', 'Laos'),
(115, 'LB', 'Lebanon'),
(116, 'LC', 'St. Lucia'),
(117, 'LI', 'Liechtenstein'),
(118, 'LK', 'Sri Lanka'),
(119, 'LR', 'Liberia'),
(120, 'LS', 'Lesotho'),
(121, 'LT', 'Lithuania'),
(122, 'LU', 'Luxembourg'),
(123, 'LV', 'Latvia'),
(124, 'LY', 'Libya'),
(125, 'MA', 'Morocco'),
(126, 'MC', 'Monaco'),
(127, 'MD', 'Moldova'),
(128, 'MG', 'Madagascar'),
(129, 'MH', 'Marshall Islands'),
(130, 'MK', 'Macedonia'),
(131, 'ML', 'Mali'),
(132, 'MN', 'Mongolia'),
(133, 'MO', 'Macau'),
(134, 'MP', 'Saipan'),
(135, 'MQ', 'Martinique'),
(136, 'MR', 'Mauritania'),
(137, 'MS', 'Montserrat'),
(138, 'MT', 'Malta'),
(139, 'MU', 'Mauritius'),
(140, 'MV', 'Maldives, Republic of'),
(141, 'MW', 'Malawi'),
(142, 'MX', 'Mexico'),
(143, 'MY', 'Malaysia'),
(144, 'MZ', 'Mozambique'),
(145, 'NA', 'Namibia'),
(146, 'NC', 'New Caledonia'),
(147, 'NE', 'Niger'),
(148, 'NG', 'Nigeria'),
(149, 'NI', 'Nicaragua'),
(150, 'NL', 'Netherlands'),
(151, 'NO', 'Norway'),
(152, 'NP', 'Nepal'),
(153, 'NZ', 'New Zealand'),
(154, 'OM', 'Oman'),
(155, 'PA', 'Panama'),
(156, 'PE', 'Peru'),
(157, 'PF', 'French Polynesia'),
(158, 'PG', 'Papua New Guinea'),
(159, 'PH', 'Philippines'),
(160, 'PK', 'Pakistan'),
(161, 'PL', 'Poland'),
(162, 'PS', 'Palestine'),
(163, 'PT', 'Portugal'),
(164, 'PW', 'Palau'),
(165, 'PY', 'Paraguay'),
(166, 'QA', 'Qatar'),
(167, 'RE', 'Reunion'),
(168, 'RO', 'Romania'),
(169, 'RU', 'Russia'),
(170, 'RW', 'Rwanda'),
(171, 'SA', 'Saudi Arabia'),
(172, 'SC', 'Seychelles'),
(173, 'SE', 'Sweden'),
(174, 'SG', 'Singapore'),
(175, 'SI', 'Slovenia'),
(176, 'SK', 'Slovak Republic'),
(177, 'SN', 'Senegal'),
(178, 'SR', 'Suriname'),
(179, 'SV', 'El Salvador'),
(180, 'SY', 'Syria'),
(181, 'SZ', 'Swaziland'),
(182, 'TC', 'Turks and Caicos Islands'),
(183, 'TD', 'Chad'),
(184, 'TG', 'Togo'),
(185, 'TH', 'Thailand'),
(186, 'TL', 'East Timor'),
(187, 'TN', 'Tunisia'),
(188, 'TO', 'Tonga'),
(189, 'TR', 'Turkey'),
(190, 'TT', 'Trinidad and Tobago'),
(191, 'TW', 'Taiwan,'),
(192, 'TZ', 'Tanzania'),
(193, 'UA', 'Ukraine'),
(194, 'UG', 'Uganda'),
(195, 'US', 'Puerto Rico'),
(197, 'UY', 'Uruguay'),
(198, 'UZ', 'Uzbekistan'),
(199, 'VC', 'St. Vincent'),
(200, 'VE', 'Venezuela'),
(201, 'VG', 'British Virgin Islands'),
(202, 'VI', 'U.S. Virgin Islands'),
(203, 'VN', 'Vietnam'),
(204, 'VU', 'Vanuatu'),
(205, 'WF', 'Wallis & Futuna Islands'),
(206, 'WS', 'Samoa'),
(207, 'YE', 'Yemen, The Republic of'),
(208, 'ZA', 'South African Republic'),
(209, 'ZM', 'Zambia'),
(210, 'ZW', 'Zimbabwe'),
(211, 'USA', 'UNITED STATES');

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
(2, 20210720, '160.00', 4);

-- --------------------------------------------------------

--
-- Table structure for table `custom_office`
--

CREATE TABLE `custom_office` (
  `id` int(11) NOT NULL,
  `code` char(20) DEFAULT NULL,
  `description` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `custom_office`
--

INSERT INTO `custom_office` (`id`, `code`, `description`) VALUES
(2, 'JMCHQ', 'Customs House Kingston'),
(3, 'JMKWL', 'Kingston Wharves Limited & Car P'),
(4, 'JMKCT', 'Kingston Conatiner Terminal (KCT'),
(5, 'JMALB', 'Adolph Levy - 228'),
(6, 'JMOSC', 'One Stop Customs Clearance Centr'),
(7, 'JMKQW', 'Queen\'s Warehouse, Spanish Town'),
(8, 'JMKLC', 'Kingston Logistic Centre Ltd'),
(9, 'JMKFZ', 'Kingston Free Zone'),
(10, 'JMGFZ', 'Garmex Free Zone'),
(11, 'JMBFZ', 'Bashco Single Entity Free Zone'),
(12, 'JMCSO', 'Parcels Post Kingston'),
(13, 'JMKIN', 'Norman Manley Int\'l Airport Pass'),
(14, 'JMNQW', 'Queen\'s Warehouse NMIA'),
(15, 'JMAJO', 'AJAS1 (NMIA)'),
(16, 'JMAJT', 'AJAS2 (NMIA)'),
(17, 'JMKAJ', 'Amerijet (NMIA)'),
(18, 'JMKCA', 'Caribbean Airlines (NMIA)'),
(19, 'JMKDH', 'DHL (NMIA)'),
(20, 'JMKFE', 'FEDEX (NMIA)'),
(21, 'JMMHO', 'Customs House Montego Bay'),
(22, 'JMMBJ', 'Sangster International Airport P'),
(23, 'JMMAC', 'Air Cargo (SIA/MBJ)'),
(24, 'JMMQW', 'Queen\'s Warehouse (MBJ)'),
(25, 'JMMSU', 'Site Inspection Unit (MBJ)'),
(26, 'JMMPH', 'Port Handlers (Wharf) (MBJ)'),
(27, 'JMMSB', 'Seaboard Warehouse (MBJ)'),
(28, 'JMMFZ', 'Montego Bay Free Zone'),
(29, 'JMSBO', 'St Ann\'s Bay Customs Office'),
(30, 'JMOCJ', 'Ian Fleming International Airpor'),
(31, 'JMOCP', 'Ocho Rios Pier'),
(32, 'JMPRH', 'Port Rhoades'),
(33, 'JMHFZ', 'Hayes Free Zone'),
(34, 'JMPEV', 'Port Esquivel'),
(35, 'JMROP', 'Rocky Point'),
(36, 'JMSRI', 'Salt River'),
(37, 'JMPOT', 'Port Antonio (Marina)'),
(38, 'JMPCO', 'Port Antonio Customs Office'),
(39, 'JMMCO', 'Mandeville Customs Office'),
(40, 'JMBLR', 'Black River'),
(41, 'JMPKS', 'Port Kaiser'),
(42, 'JMRIB', 'Port Rio Bueno'),
(43, 'JMFMH', 'Falmouth Pier'),
(44, 'JMLUC', 'Port Lucea'),
(45, 'JMSLM', 'Savanna-La-Mar'),
(46, 'JMPMO', 'Port Morant'),
(47, 'JMPRO', 'Port Royal'),
(48, 'JMKPJ', 'Kingston Petrojam Pier'),
(49, 'JMKMW', 'Myers Wharf'),
(50, 'JMKSP', 'Shell Pier'),
(51, 'JMKCC', 'Carib Cement Pier'),
(52, 'JMKAB', 'Agean Bunkering Pier'),
(53, 'JMKGP', 'Gypsum Pier'),
(54, 'JMKWW', 'Wherry Wharf'),
(55, 'JMKAE', 'Agricultural Export Complex -NMI'),
(56, 'JMKCP', 'Carib Cement Coal Pier -Kingston');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_order`
--

CREATE TABLE `delivery_order` (
  `id` int(11) NOT NULL,
  `voyage_id` int(11) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `cancelled` smallint(6) DEFAULT NULL,
  `order_date` int(11) DEFAULT NULL,
  `cancel_by` int(11) DEFAULT NULL,
  `cancel_date` int(11) DEFAULT NULL,
  `cancel_time` smallint(11) DEFAULT NULL
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

--
-- Dumping data for table `edi_translation`
--

INSERT INTO `edi_translation` (`id`, `type`, `internal_code`, `external_code`, `code_id`, `translation_source_id`) VALUES
(1, 'commodity', NULL, 'CLT', 34, 8),
(2, 'package', NULL, 'BA', 1, 8),
(3, 'commodity', NULL, 'PE', 13, 8),
(4, 'commodity', NULL, 'CLT', 24, 8),
(5, 'package', NULL, 'PK', 17, 8),
(6, 'commodity', NULL, 'PE', 39, 8),
(7, 'commodity', NULL, 'CL', 47, 8),
(8, 'package', NULL, 'BX', 13, 8),
(9, 'package', NULL, 'CR', 4, 8),
(10, 'commodity', NULL, 'CL', 8, 8),
(11, 'commodity', NULL, 'BI', 29, 8),
(12, 'package', NULL, 'SI', 10, 8),
(13, 'commodity', NULL, 'PE', 32, 8),
(14, 'commodity', NULL, 'FD', 6, 8),
(15, 'commodity', NULL, 'PE', 38, 8),
(16, 'port', NULL, 'USNYC', 6, 8),
(17, 'port', NULL, 'JMKIN', 15, 8),
(18, 'port', NULL, 'JMKIN', 1, 8),
(19, 'package', NULL, 'BI', 36, 8),
(20, 'package', NULL, 'PK', 11, 8),
(21, 'package', NULL, 'BX', 34, 8),
(22, 'package', NULL, 'BG', 33, 8),
(23, 'package', NULL, 'BI', 28, 8),
(24, 'package', NULL, 'PK', 40, 8),
(25, 'package', NULL, 'PX', 5, 8),
(26, 'port', NULL, 'USMIA', 16, 8),
(27, 'package', NULL, 'BX', 43, 8),
(28, 'package', NULL, 'PA', 0, 8),
(29, 'package', NULL, 'PA', 41, 8),
(30, 'package', NULL, 'PK', 44, 8),
(32, 'package', NULL, 'PK', 42, 8),
(33, 'package', NULL, 'PK', 37, 8),
(34, 'package', NULL, 'PK', 38, 8);

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
(41, 21, 'Exchange Rate', 'Maintain_Exchange.html', 4, NULL, 2, NULL, NULL),
(42, 6, 'Archived BLs', 'billOfLading_History_Enquiry.html', 6, NULL, 2, NULL, NULL),
(43, 6, 'Close Accounts', 'billOfLading_Admin_Processed.html', 10, NULL, 2, NULL, NULL),
(44, 29, 'Maintain EDI Codes', 'maintain_edi_codes.html', 7, NULL, 2, NULL, NULL),
(45, 2, 'Customers', 'maintain_client.html', 8, NULL, 2, NULL, NULL),
(46, 6, 'Bill of Laing Discount', 'billoflading_discount.html', 7, NULL, 2, NULL, NULL),
(47, 9, 'Cashier Receipts', 'rpt_cashier_listing.html', 7, 0, 2, NULL, NULL),
(48, 21, 'Rate Table', 'rate_table.html', 2, 0, 2, NULL, NULL),
(49, 18, 'Pre-Clearance', 'preclearance.html', 3, NULL, 2, NULL, NULL),
(50, 6, 'Admin Enquiry ', 'billOfLading_Enquiry_admin.html', 5, 0, 2, NULL, NULL),
(51, 9, 'Outstanding Bills', 'rpt_outstanding_bills.html', 4, NULL, 2, NULL, NULL),
(52, 9, 'Daily Balance', 'rpt_daily_balance.html', 8, NULL, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification_history`
--

CREATE TABLE `notification_history` (
  `id` int(11) NOT NULL,
  `notification_date` int(11) DEFAULT NULL,
  `notification_detail` char(100) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `id` int(11) NOT NULL,
  `package_code` char(10) DEFAULT NULL,
  `description` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`id`, `package_code`, `description`) VALUES
(1, 'BA', 'Barrel'),
(4, 'CR', 'Crate'),
(5, 'PX', 'Pallet'),
(10, 'SI', 'Skid'),
(11, 'PP', 'Piece'),
(13, 'BX', 'BOX'),
(17, 'PK', 'Package'),
(28, 'PLC', 'Plastic Container'),
(33, 'BG', 'Bag'),
(34, 'CN', 'D Container'),
(36, 'BI', 'BIN'),
(37, 'BIN B', 'Binbox'),
(38, 'SC', 'Suite Case'),
(39, 'CT', 'E CONTAINER'),
(40, 'TNT', 'TENT'),
(41, 'LG', 'LIGHTS'),
(42, 'TO', 'TONERS'),
(43, 'TV', 'Television'),
(44, 'IG', 'Igloo');

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

--
-- Dumping data for table `port`
--

INSERT INTO `port` (`id`, `port_code`, `port_name`, `country_id`) VALUES
(1, 'JMKIN', 'Kingston', 103),
(2, 'B7', 'Berth 7', NULL),
(3, 'MIA', 'MIAMI', NULL),
(4, 'KW', 'Kingston Wharves', NULL),
(5, 'NJ', 'New Jersey', NULL),
(6, 'USNYC', 'NEW YORK', NULL),
(9, 'BR6', 'BERTH 6', NULL),
(10, 'B5', 'BERTH 5', NULL),
(11, 'ADLPH L', 'ADOLPH LEVY', NULL),
(14, 'USPEF', 'PORT EVERGLADES', NULL),
(15, 'KWTLF', 'TOTAL LOGISTICS FACILITY', NULL),
(16, 'FL', 'FLORIDA', 211),
(17, 'BRT1', 'Bert 1', 103);

-- --------------------------------------------------------

--
-- Table structure for table `preclearance`
--

CREATE TABLE `preclearance` (
  `id` int(11) NOT NULL,
  `preclearance_date` int(11) DEFAULT NULL,
  `preclearance_time` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `payee` char(40) DEFAULT NULL,
  `preclearance_total` decimal(12,2) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `local_total` decimal(12,2) DEFAULT NULL,
  `exchange_rate` decimal(8,2) DEFAULT NULL,
  `printed` smallint(6) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `billoflading_id` int(11) DEFAULT NULL,
  `cancel_by` int(11) DEFAULT NULL,
  `cancel_date` int(11) DEFAULT NULL,
  `cancel_time` smallint(6) DEFAULT NULL,
  `cancelled` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `preclearance_detail`
--

CREATE TABLE `preclearance_detail` (
  `id` int(11) NOT NULL,
  `preclearance_id` int(11) DEFAULT NULL,
  `bol_id` int(11) DEFAULT NULL,
  `charge_item_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rate_table`
--

CREATE TABLE `rate_table` (
  `id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `basis` char(20) DEFAULT NULL,
  `unit` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `charge_item_id` int(11) DEFAULT NULL,
  `purpose` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rate_table`
--

INSERT INTO `rate_table` (`id`, `package_id`, `basis`, `unit`, `rate`, `charge_item_id`, `purpose`) VALUES
(1, 1, 'quantity', '1.00', '4000.00', 27, 'charge'),
(2, 4, 'measure', '1.00', '260.00', 27, 'charge'),
(3, 1, 'quantity', '1.00', '40.00', 26, 'value'),
(4, 13, 'measure', '1.00', '260.00', 27, 'charge'),
(5, 13, 'measure', '1.00', '1.50', 26, 'value'),
(6, 17, 'measure', '1.00', '1.50', 26, 'value'),
(7, 10, 'measure', '1.00', '1.50', 26, 'value'),
(8, 5, 'measure', '1.00', '1.50', 26, 'value'),
(9, 11, 'measure', '1.00', '1.50', 26, 'value'),
(10, 4, 'measure', '1.00', '1.50', 26, 'value'),
(11, 36, 'quantity', '0.00', '3500.00', 27, 'charge'),
(12, 34, 'quantity', '0.00', '14400.00', 27, 'charge'),
(13, 39, 'quantity', '0.00', '6000.00', 27, 'charge'),
(14, 43, 'quantity', '0.00', '8000.00', 27, 'charge'),
(15, 17, 'measure', '1.00', '260.00', 27, 'charge');

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `id` int(11) NOT NULL,
  `receipt_date` int(11) DEFAULT NULL,
  `receipt_time` int(11) DEFAULT NULL,
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
  `customer_identification` char(20) NOT NULL,
  `payment_type` char(20) DEFAULT NULL,
  `payment_type_no` char(15) DEFAULT NULL
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
-- Table structure for table `receipt_payment`
--

CREATE TABLE `receipt_payment` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) DEFAULT NULL,
  `payment_type` char(15) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `currency_amount` decimal(12,2) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `payment_type_no` char(25) DEFAULT NULL
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
(1, 'ANSIX12 export sequence', 'int', '133', 'ANSIX12'),
(2, 'Freight ID', 'dec', '31', 'freight_id'),
(8, 'ASYCUDA SCAC Code', 'char', '0016905070000', 'ansix12Xp'),
(9, 'ASYCUDA SCAC Code', 'char', '0016905070000', 'asycudaXp'),
(10, 'Print Bill of Lading', 'char', 'billoflading_f1', 'forms'),
(11, 'Pre-Clearance', 'char', 'preclearance', 'forms'),
(12, 'Arrival Notice', 'char', 'arrival_notice', 'forms'),
(13, 'Force Print Order', 'char', 'N', 'printorder'),
(14, '0020127150000', 'char', 'agent_code', 'PCS'),
(15, 'SMLU', 'char', 'carrier_code', 'PCS'),
(16, 'Transportation Charge ID', 'int', '28', 'TRNSPRTID'),
(17, 'Pre-Clearance Income ID', 'int', '29', 'PRECLRID');

-- --------------------------------------------------------

--
-- Table structure for table `translation_source`
--

CREATE TABLE `translation_source` (
  `id` int(11) NOT NULL,
  `code` char(15) DEFAULT NULL,
  `description` char(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `translation_source`
--

INSERT INTO `translation_source` (`id`, `code`, `description`) VALUES
(5, 'ansix12', 'ANSIX12 Export'),
(6, 'asycuda', 'ASYCUDA Export'),
(7, 'bl-import', 'Manifest Import'),
(8, 'PCS', 'PCS Export');

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

--
-- Dumping data for table `vessel`
--

INSERT INTO `vessel` (`id`, `vessel_name`, `vessel_code`, `lloyd_number`, `country_id`) VALUES
(143, 'Erato', 'ERA', '9472103', 119),
(144, 'As Petra', 'ASPETRA', '12444', 50),
(145, 'BOMAR CAEN', 'BCA', '9301433', 163),
(146, 'AS SAVANNA', 'AS SAVANNA', '754282', 103),
(147, 'As Samanta', 'AS SAM', '9410260', 211),
(148, 'Seaboard Sun', 'SEASUN', '1123122', 211),
(149, 'AS SABRINA', 'AS SAB', '9410260', 211),
(150, 'AS PAULINE', 'ASP', '9410260', 211),
(151, 'HELLE RITSCHER', 'HR', '9410260', 211);

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
  `mby_vessel_id` int(11) DEFAULT NULL,
  `transportation_mode` char(1) DEFAULT NULL,
  `manifest_number` char(15) DEFAULT NULL,
  `registration_number` char(10) DEFAULT NULL
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
-- Indexes for table `billoflading_discount`
--
ALTER TABLE `billoflading_discount`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bol_charge_item` (`charge_item_id`,`billoflading_id`),
  ADD KEY `fk_charge_item_idx` (`charge_item_id`),
  ADD KEY `fk_billoflading_idx` (`billoflading_id`);

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
  ADD KEY `fk_bol_port_origin` (`port_of_origin`),
  ADD KEY `by_Receipt_processed` (`receipt_processed`),
  ADD KEY `by_order_processed` (`order_processed`),
  ADD KEY `by_parent` (`parent_bol`),
  ADD KEY `fk_edit_user_idx` (`edited_by`);

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
-- Indexes for table `client_discount`
--
ALTER TABLE `client_discount`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `by_client_item` (`client_id`,`charge_item_id`),
  ADD KEY `fk_charge_item_idx` (`charge_item_id`);

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
-- Indexes for table `delivery_order`
--
ALTER TABLE `delivery_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_bol` (`billoflading_id`),
  ADD KEY `fk_order_created_by` (`created_by`),
  ADD KEY `fk_order_voyage` (`voyage_id`);

--
-- Indexes for table `edi_translation`
--
ALTER TABLE `edi_translation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_edi_translation_translation_source1_idx` (`translation_source_id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_menu_item_` (`menu_group_id`);

--
-- Indexes for table `notification_history`
--
ALTER TABLE `notification_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_billoflading_notification` (`billoflading_id`);

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
-- Indexes for table `preclearance`
--
ALTER TABLE `preclearance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Unique_preclearance_by_bol` (`preclearance_date`,`billoflading_id`,`preclearance_time`,`preclearance_total`),
  ADD KEY `fk_preclearance_currency` (`currency_id`),
  ADD KEY `fk_preclearancet_bol` (`billoflading_id`),
  ADD KEY `fk_preclearance_client` (`client_id`),
  ADD KEY `fk_preclearance_created_by` (`created_by`),
  ADD KEY `fk_preclearance_deleted_by` (`cancel_by`);

--
-- Indexes for table `preclearance_detail`
--
ALTER TABLE `preclearance_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_preclearance_detail_charge_item` (`charge_item_id`),
  ADD KEY `fk_preclearance_idx` (`preclearance_id`);

--
-- Indexes for table `rate_table`
--
ALTER TABLE `rate_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rate_package_idx` (`package_id`),
  ADD KEY `fk_rate_charge_item_idx` (`charge_item_id`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Unique_Receipt_by_bol` (`receipt_date`,`billoflading_id`,`receipt_time`,`receipt_total`),
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
-- Indexes for table `receipt_payment`
--
ALTER TABLE `receipt_payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `by_receipt` (`receipt_id`,`payment_type`),
  ADD KEY `fk_receipt_payment_receipt_idx` (`receipt_id`);

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
-- AUTO_INCREMENT for table `billoflading_discount`
--
ALTER TABLE `billoflading_discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `bill_of_lading`
--
ALTER TABLE `bill_of_lading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2542;

--
-- AUTO_INCREMENT for table `bill_of_lading_container`
--
ALTER TABLE `bill_of_lading_container`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `bill_of_lading_detail`
--
ALTER TABLE `bill_of_lading_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2080;

--
-- AUTO_INCREMENT for table `bill_of_lading_other_charge`
--
ALTER TABLE `bill_of_lading_other_charge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3558;

--
-- AUTO_INCREMENT for table `charge_item`
--
ALTER TABLE `charge_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `charge_item_rate`
--
ALTER TABLE `charge_item_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_discount`
--
ALTER TABLE `client_discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commodity`
--
ALTER TABLE `commodity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `container_size_type`
--
ALTER TABLE `container_size_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `currency_rate`
--
ALTER TABLE `currency_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `custom_office`
--
ALTER TABLE `custom_office`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `delivery_order`
--
ALTER TABLE `delivery_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edi_translation`
--
ALTER TABLE `edi_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `notification_history`
--
ALTER TABLE `notification_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=961;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `port`
--
ALTER TABLE `port`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `preclearance`
--
ALTER TABLE `preclearance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `preclearance_detail`
--
ALTER TABLE `preclearance_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=342;

--
-- AUTO_INCREMENT for table `rate_table`
--
ALTER TABLE `rate_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=858;

--
-- AUTO_INCREMENT for table `receipt_detail`
--
ALTER TABLE `receipt_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1230;

--
-- AUTO_INCREMENT for table `receipt_payment`
--
ALTER TABLE `receipt_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1065;

--
-- AUTO_INCREMENT for table `shipment_order`
--
ALTER TABLE `shipment_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_values`
--
ALTER TABLE `system_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `translation_source`
--
ALTER TABLE `translation_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_option`
--
ALTER TABLE `user_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1625;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vessel`
--
ALTER TABLE `vessel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `voyage`
--
ALTER TABLE `voyage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `voyage_container`
--
ALTER TABLE `voyage_container`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billoflading_discount`
--
ALTER TABLE `billoflading_discount`
  ADD CONSTRAINT `fk_billoflading` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_charge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `charge_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `bill_of_lading`
--
ALTER TABLE `bill_of_lading`
  ADD CONSTRAINT `fk_bol_port_delivery` FOREIGN KEY (`port_of_delivery`) REFERENCES `port` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bol_port_discharge` FOREIGN KEY (`port_of_discharge`) REFERENCES `port` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_bol_port_loading` FOREIGN KEY (`port_of_loading`) REFERENCES `port` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_bol_port_origin` FOREIGN KEY (`port_of_origin`) REFERENCES `port` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `fk_edit_user` FOREIGN KEY (`edited_by`) REFERENCES `user_profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
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
-- Constraints for table `delivery_order`
--
ALTER TABLE `delivery_order`
  ADD CONSTRAINT `fk_order_bol0` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`),
  ADD CONSTRAINT `fk_order_created_by0` FOREIGN KEY (`created_by`) REFERENCES `user_profile` (`id`),
  ADD CONSTRAINT `fk_order_voyage0` FOREIGN KEY (`voyage_id`) REFERENCES `voyage` (`id`);

--
-- Constraints for table `edi_translation`
--
ALTER TABLE `edi_translation`
  ADD CONSTRAINT `fk_edi_translation_translation_source1` FOREIGN KEY (`translation_source_id`) REFERENCES `translation_source` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `notification_history`
--
ALTER TABLE `notification_history`
  ADD CONSTRAINT `fk_billoflading_notification` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `port`
--
ALTER TABLE `port`
  ADD CONSTRAINT `fk_port_country` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Constraints for table `preclearance`
--
ALTER TABLE `preclearance`
  ADD CONSTRAINT `fk_preclearance_client0` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_preclearance_created_by0` FOREIGN KEY (`created_by`) REFERENCES `user_profile` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_preclearance_currency0` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  ADD CONSTRAINT `fk_preclearance_deleted_by0` FOREIGN KEY (`cancel_by`) REFERENCES `user_profile` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `preclearance_detail`
--
ALTER TABLE `preclearance_detail`
  ADD CONSTRAINT `fk_preclearance0` FOREIGN KEY (`preclearance_id`) REFERENCES `preclearance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_preclearance_detail_charge_item0` FOREIGN KEY (`charge_item_id`) REFERENCES `charge_item` (`id`);

--
-- Constraints for table `rate_table`
--
ALTER TABLE `rate_table`
  ADD CONSTRAINT `fk_rate_charge_item` FOREIGN KEY (`charge_item_id`) REFERENCES `charge_item` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rate_package` FOREIGN KEY (`package_id`) REFERENCES `package` (`id`) ON DELETE NO ACTION;

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
-- Constraints for table `receipt_payment`
--
ALTER TABLE `receipt_payment`
  ADD CONSTRAINT `fk_receipt_payment_receipt` FOREIGN KEY (`receipt_id`) REFERENCES `receipt` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `shipment_order`
--
ALTER TABLE `shipment_order`
  ADD CONSTRAINT `fk_order_bol` FOREIGN KEY (`billoflading_id`) REFERENCES `bill_of_lading` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_created_by` FOREIGN KEY (`created_by`) REFERENCES `user_profile` (`id`),
  ADD CONSTRAINT `fk_order_voyage` FOREIGN KEY (`voyage_id`) REFERENCES `voyage` (`id`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_voyage_container_voyage` FOREIGN KEY (`voyage_id`) REFERENCES `voyage` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
