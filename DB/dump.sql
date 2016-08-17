-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 12, 2016 at 08:41 AM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.5.32-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bindu`
--

-- --------------------------------------------------------

--
-- Table structure for table `Academic`
--

CREATE TABLE IF NOT EXISTS `Academic` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `degree` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `course` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `passingYear` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `result` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `institute` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remark` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `AcademicMeta`
--

CREATE TABLE IF NOT EXISTS `AcademicMeta` (
  `id` int(11) NOT NULL,
  `tutor_id` int(11) DEFAULT NULL,
  `metaKey` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metaValue` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Admission`
--

CREATE TABLE IF NOT EXISTS `Admission` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8_unicode_ci,
  `content` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coursePeriod` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `classDuration` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tuitionFee` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shifting` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `isOnlineRegistration` tinyint(1) NOT NULL,
  `isPayment` tinyint(1) NOT NULL,
  `isPromotion` tinyint(1) NOT NULL,
  `promotionStartDate` datetime DEFAULT NULL,
  `promotionEndDate` datetime DEFAULT NULL,
  `position` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paymentStatus` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `courseLevel_id` int(11) DEFAULT NULL,
  `createUser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `AdmissionComment`
--

CREATE TABLE IF NOT EXISTS `AdmissionComment` (
  `id` int(11) NOT NULL,
  `admission_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admission_branch`
--

CREATE TABLE IF NOT EXISTS `admission_branch` (
  `admission_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `AdsTool`
--

CREATE TABLE IF NOT EXISTS `AdsTool` (
  `id` int(11) NOT NULL,
  `googleServiceID` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slotID` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keyword` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `metaDescription` longtext COLLATE utf8_unicode_ci,
  `googleVerificationCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `redirectLang` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `redirectCode` longtext COLLATE utf8_unicode_ci,
  `statCounterID` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statCounterSecurityCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `statCounterVisible` tinyint(1) DEFAULT NULL,
  `redirectTablet` tinyint(1) DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `AdsTool`
--

INSERT INTO `AdsTool` (`id`, `googleServiceID`, `slotID`, `siteName`, `keyword`, `metaDescription`, `googleVerificationCode`, `redirectLang`, `redirectCode`, `statCounterID`, `statCounterSecurityCode`, `statCounterVisible`, `redirectTablet`, `globalOption_id`) VALUES
  (1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
  (2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8),
  (3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9),
  (4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10),
  (5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 11);

-- --------------------------------------------------------

--
-- Table structure for table `Advertisment`
--

CREATE TABLE IF NOT EXISTS `Advertisment` (
  `id` int(11) NOT NULL,
  `position` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `expiredDate` int(11) NOT NULL,
  `targetUrl` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Apartment`
--

CREATE TABLE IF NOT EXISTS `Apartment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `AppModule`
--

CREATE TABLE IF NOT EXISTS `AppModule` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `moduleClass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `AppModule`
--

INSERT INTO `AppModule` (`id`, `name`, `moduleClass`, `created`, `slug`, `content`, `status`) VALUES
  (1, 'Dish Billing', 'DishBilling', '2015-12-01 08:25:37', 'dish-billing', 'Dish Billing', 1),
  (2, 'Inventory', 'Inventory', '2015-12-01 08:30:34', 'inventory', 'Inventory', 1),
  (3, 'E-commerce', 'Ecommerce', '2015-12-14 13:01:10', 'e-commerce', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Bank`
--

CREATE TABLE IF NOT EXISTS `Bank` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Bank`
--

INSERT INTO `Bank` (`id`, `name`) VALUES
  (1, 'AB Bank Limited\r\n'),
  (2, 'Agrani Bank Limited\r\n'),
  (3, 'Al-Arafah Islami Bank Limited\r\n'),
  (4, 'Bangladesh Commerce Bank Limited\r\n'),
  (5, 'Bangladesh Development Bank Limited\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `Blackout`
--

CREATE TABLE IF NOT EXISTS `Blackout` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blackoutDate` longtext COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Blog`
--

CREATE TABLE IF NOT EXISTS `Blog` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoGallery_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `BlogComment`
--

CREATE TABLE IF NOT EXISTS `BlogComment` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Branch`
--

CREATE TABLE IF NOT EXISTS `Branch` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Branding`
--

CREATE TABLE IF NOT EXISTS `Branding` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sponsor` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `customUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Branding`
--

INSERT INTO `Branding` (`id`, `name`, `slug`, `type`, `sponsor`, `status`, `customUrl`, `path`) VALUES
  (2, 'Canon', 'canon', 'national', 1, 1, 'http://kintethako.dev/', '1167351_720901744651851_844466413731183831_o.jpg'),
  (3, 'Espirit', 'espirit', 'international', 1, 1, 'http://kintethako.dev/', 'esprit.jpg'),
  (4, 'Gap', 'gap', 'international', 0, 1, NULL, 'gap.jpg'),
  (5, 'Next', 'next', 'international', 1, 1, NULL, 'next.jpg'),
  (6, 'Puma', 'puma', 'international', 0, 1, NULL, 'puma.jpg'),
  (7, 'Zara', 'zara', 'international', 1, 1, NULL, 'zara.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `branding_category`
--

CREATE TABLE IF NOT EXISTS `branding_category` (
  `branding_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE IF NOT EXISTS `cart_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` int(11) NOT NULL,
  `adjustments_total` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `is_immutable` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sorting` smallint(6) NOT NULL,
  `feature` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `imagePath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=669 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent`, `name`, `slug`, `level`, `path`, `sorting`, `feature`, `status`, `imagePath`, `inventoryConfig_id`, `permission`) VALUES
  (1, NULL, 'Wears', 'wears', 1, '1/', 2, 0, 1, NULL, NULL, ''),
  (2, 1, 'Men''s Wears', 'wears-men-s-wears', 2, '1/2/', 1, 0, 1, NULL, NULL, ''),
  (3, 1, 'Women''s Wears', 'wears-women-s-wears', 2, '1/3/', 0, 0, 1, NULL, NULL, ''),
  (4, 1, 'Baby & Child Wear''s', 'wears-baby-child-wear-s', 2, '1/4/', 0, 0, 1, NULL, NULL, ''),
  (5, 6, 'Boutique & Batik', 'jute-handicrafts-boutique-batik', 2, '6/5/', 0, 0, 2, NULL, NULL, ''),
  (6, NULL, 'Jute & Handicrafts', 'jute-handicrafts', 1, '6/', 13, 0, 2, NULL, NULL, ''),
  (7, NULL, 'Leather & Bags', 'leather-bags', 1, '7/', 14, 0, 1, 'woman.jpg', NULL, ''),
  (8, 6, 'Jute Products', 'jute-handicrafts-jute-products', 2, '6/8/', 0, 0, 1, NULL, NULL, ''),
  (9, 6, 'Handicrafts', 'jute-handicrafts-handicrafts', 2, '6/9/', 0, 0, 1, NULL, NULL, ''),
  (10, NULL, 'Foot wears', 'foot-wears', 1, '10/', 8, 1, 1, NULL, NULL, ''),
  (11, 7, 'Bag & Travelling', 'leather-bags-bag-travelling', 2, '7/11/', 0, 0, 1, NULL, NULL, ''),
  (12, 7, 'Leather Goods', 'leather-bags-leather-goods', 2, '7/12/', 0, 0, 1, NULL, NULL, ''),
  (13, 7, 'Belts', 'leather-bags-belts', 2, '7/13/', 0, 0, 1, NULL, NULL, ''),
  (14, NULL, 'Jewelry & Watches', 'jewelry-watches', 1, '14/', 12, 0, 1, NULL, NULL, ''),
  (15, 14, 'Jewellery', 'jewelry-watches-jewellery', 2, '14/15/', 0, 0, 1, NULL, NULL, ''),
  (16, 14, 'Watch & Sunglass', 'jewelry-watches-watch-sunglass', 2, '14/16/', 0, 0, 1, NULL, NULL, ''),
  (17, 14, 'Optics', 'jewelry-watches-optics', 2, '14/17/', 0, 0, 1, NULL, NULL, ''),
  (18, NULL, 'Beauty & Fragrances', 'beauty-fragrances', 1, '18/', 1, 1, 1, 'man.jpg', NULL, ''),
  (19, 18, 'Consumer', 'beauty-fragrances-consumer', 2, '18/19/', 0, 0, 1, NULL, NULL, ''),
  (20, 18, 'Cosmetics', 'beauty-fragrances-cosmetics', 2, '18/20/', 0, 0, 1, NULL, NULL, ''),
  (21, 18, 'Hair & Skin care', 'beauty-fragrances-hair-skin-care', 2, '18/21/', 0, 0, 1, NULL, NULL, ''),
  (22, 18, 'Bath & Body', 'beauty-fragrances-bath-body', 2, '18/22/', 0, 0, 1, NULL, NULL, ''),
  (23, 18, 'Dental Care', 'beauty-fragrances-dental-care', 2, '18/23/', 0, 0, 1, NULL, NULL, ''),
  (24, NULL, 'Foods', 'foods', 1, '24/', 7, 1, 1, '10756558_3695716_1000.jpg', NULL, ''),
  (25, 24, 'Fast Food', 'foods-fast-food', 2, '24/25/', 0, 0, 1, NULL, NULL, ''),
  (26, 24, 'Chinuse', 'foods-chinuse', 2, '24/26/', 0, 0, 1, NULL, NULL, ''),
  (27, 24, 'Resturants', 'foods-resturants', 2, '24/27/', 0, 0, 1, NULL, NULL, ''),
  (28, 24, 'Coffie House', 'foods-coffie-house', 2, '24/28/', 0, 0, 1, NULL, NULL, ''),
  (29, 24, 'Sweets & Confectionary', 'foods-sweets-confectionary', 2, '24/29/', 0, 0, 1, NULL, NULL, ''),
  (30, 24, 'Birani & Tehari', 'foods-birani-tehari', 2, '24/30/', 0, 0, 1, NULL, NULL, ''),
  (31, NULL, 'Stationery & Gifts', 'stationery-gifts', 1, '31/', 19, 1, 1, NULL, NULL, ''),
  (32, 31, 'Gift Cards', 'stationery-gifts-gift-cards', 2, '31/32/', 0, 0, 1, NULL, NULL, ''),
  (33, 31, 'Cards & Stationery', 'stationery-gifts-cards-stationery', 2, '31/33/', 0, 0, 1, NULL, NULL, ''),
  (34, 31, 'Toys & Games', 'stationery-gifts-toys-games', 2, '31/34/', 0, 0, 1, NULL, NULL, ''),
  (35, 31, 'Flowers & Gifts', 'stationery-gifts-flowers-gifts', 2, '31/35/', 0, 0, 1, NULL, NULL, ''),
  (36, 31, 'Wedding', 'stationery-gifts-wedding', 2, '31/36/', 0, 0, 1, NULL, NULL, ''),
  (37, 31, 'Sports', 'stationery-gifts-sports-1', 2, '31/37/', 0, 0, 1, NULL, NULL, ''),
  (38, 31, 'Garden', 'stationery-gifts-garden', 2, '31/38/', 0, 0, 1, NULL, NULL, ''),
  (39, NULL, 'Computer & Electronics', 'computer-electronics', 1, '39/', 3, 0, 1, NULL, NULL, ''),
  (40, 39, 'Consumer Electronics', 'computer-electronics-consumer-electronics', 2, '39/40/', 0, 0, 1, NULL, NULL, ''),
  (41, 39, 'Mobile Phone & Accessories', 'computer-electronics-mobile-phone-accessories', 2, '39/41/', 0, 0, 1, NULL, NULL, ''),
  (42, 39, 'Computing & Accessories', 'computer-electronics-computing-accessories', 2, '39/42/', 0, 0, 1, NULL, NULL, ''),
  (43, NULL, 'Home Appliances', 'home-appliances', 1, '43/', 11, 1, 1, NULL, NULL, ''),
  (44, NULL, 'Electrical & Lighting', 'electrical-lighting', 1, '44/', 5, 1, 1, NULL, NULL, ''),
  (45, NULL, 'Furnitures', 'furnitures', 1, '45/', 9, 1, 1, NULL, NULL, ''),
  (46, 45, 'Wood Furniture', 'furnitures-wood-furniture', 2, '45/46/', 0, 0, 1, NULL, NULL, ''),
  (47, 43, 'Kitchen & Gourmet', 'home-appliances-kitchen-gourmet', 2, '43/47/', 0, 0, 1, NULL, NULL, ''),
  (48, 43, 'Cookware & Dining', 'home-appliances-cookware-dining', 2, '43/48/', 0, 0, 1, NULL, NULL, ''),
  (49, 43, 'Bathroom Fitness', 'home-appliances-bathroom-fitness', 2, '43/49/', 0, 0, 1, NULL, NULL, ''),
  (50, 43, 'Decorative Accessories', 'home-appliances-decorative-accessories', 2, '43/50/', 0, 0, 1, NULL, NULL, ''),
  (51, 43, 'Bedding', 'home-appliances-bedding', 2, '43/51/', 0, 0, 1, NULL, NULL, ''),
  (52, NULL, 'Transportation', 'transportation', 1, '52/', 20, 1, 1, NULL, NULL, ''),
  (53, 52, 'Cars', 'transportation-cars', 2, '52/53/', 0, 0, 1, NULL, NULL, ''),
  (54, 52, 'Elevator & Funicular Car', 'transportation-elevator-funicular-car', 2, '52/54/', 0, 0, 1, NULL, NULL, ''),
  (55, 52, 'Bus & Trucs Accessories', 'transportation-bus-trucs-accessories', 2, '52/55/', 0, 0, 1, NULL, NULL, ''),
  (56, 52, 'Car Accessories', 'transportation-car-accessories', 2, '52/56/', 0, 0, 1, NULL, NULL, ''),
  (57, 52, 'Bike &  Accessories', 'transportation-bike-accessories', 2, '52/57/', 0, 0, 1, NULL, NULL, ''),
  (58, 52, 'Cycle', 'transportation-cycle', 2, '52/58/', 0, 0, 1, NULL, NULL, ''),
  (59, NULL, 'Health & Medicine', 'health-medicine', 1, '59/', 10, 1, 1, NULL, NULL, ''),
  (60, 59, 'Beauty Equipment', 'health-medicine-beauty-equipment', 2, '59/60/', 0, 0, 1, NULL, NULL, ''),
  (61, 59, 'Dental Apparatus', 'health-medicine-dental-apparatus', 2, '59/61/', 0, 0, 1, NULL, NULL, ''),
  (62, 59, 'Diagnosis Equipment', 'health-medicine-diagnosis-equipment', 2, '59/62/', 0, 0, 1, NULL, NULL, ''),
  (63, 59, 'Disposable Medical Supplies', 'health-medicine-disposable-medical-supplies', 2, '59/63/', 0, 0, 1, NULL, NULL, ''),
  (64, 59, 'Health Care Appliance', 'health-medicine-health-care-appliance', 2, '59/64/', 0, 0, 1, NULL, NULL, ''),
  (65, 59, 'Surgical Equipment', 'health-medicine-surgical-equipment', 2, '59/65/', 0, 0, 1, NULL, NULL, ''),
  (66, 59, 'Fitness Equipment', 'health-medicine-fitness-equipment', 2, '59/66/', 0, 0, 1, NULL, NULL, ''),
  (67, NULL, 'Construction & Decoration', 'construction-decoration', 1, '67/', 4, 1, 1, NULL, NULL, ''),
  (68, 67, 'Tiles & Ceramics', 'construction-decoration-tiles-ceramics', 2, '67/68/', 0, 0, 1, NULL, NULL, ''),
  (69, 67, 'Pipe Fittings', 'construction-decoration-pipe-fittings', 2, '67/69/', 0, 0, 1, NULL, NULL, ''),
  (70, 67, 'Hardware', 'construction-decoration-hardware', 2, '67/70/', 0, 0, 1, NULL, NULL, ''),
  (71, 67, 'Timber & Plywood', 'construction-decoration-timber-plywood', 2, '67/71/', 0, 0, 1, NULL, NULL, ''),
  (72, 67, 'Bathroom Furniture', 'construction-decoration-bathroom-furniture', 2, '67/72/', 0, 0, 1, NULL, NULL, ''),
  (73, 67, 'Sanatary & Bathroom Fitnes', 'construction-decoration-sanatary-bathroom-fitnes', 2, '67/73/', 0, 0, 1, NULL, NULL, ''),
  (74, NULL, 'Services', 'services', 1, '74/', 17, 1, 1, NULL, NULL, ''),
  (75, 74, 'Hospital & Diagonistic Center', 'services-hospital-diagonistic-center', 2, '74/75/', 0, 0, 1, NULL, NULL, ''),
  (76, 74, 'Doctor Chember', 'services-doctor-chember', 2, '74/76/', 0, 0, 1, NULL, NULL, ''),
  (77, 74, 'Dental Chember', 'services-dental-chember', 2, '74/77/', 0, 0, 1, NULL, NULL, ''),
  (78, 74, 'Gents & Women Parler', 'services-gents-women-parler', 2, '74/78/', 0, 0, 1, NULL, NULL, ''),
  (79, 74, 'Dry Cleanners', 'services-dry-cleanners', 2, '74/79/', 0, 0, 1, NULL, NULL, ''),
  (80, 74, 'Advertising', 'services-advertising', 2, '74/80/', 0, 0, 1, NULL, NULL, ''),
  (81, 74, 'Express Delivery', 'services-express-delivery', 2, '74/81/', 0, 0, 1, NULL, NULL, ''),
  (82, 74, 'Package & Printing', 'services-package-printing', 2, '74/82/', 0, 0, 1, NULL, NULL, ''),
  (83, 74, 'Shipment & Storage', 'services-shipment-storage', 2, '74/83/', 0, 0, 1, NULL, NULL, ''),
  (84, 74, 'Comunity Center', 'services-comunity-center', 2, '74/84/', 0, 0, 1, NULL, NULL, ''),
  (85, 74, 'Conference Center', 'services-conference-center', 2, '74/85/', 0, 0, 1, NULL, NULL, ''),
  (86, 74, 'Body Fitness', 'services-body-fitness', 2, '74/86/', 0, 0, 1, NULL, NULL, ''),
  (87, 74, 'Event Management', 'services-event-management', 2, '74/87/', 0, 0, 1, NULL, NULL, ''),
  (88, NULL, 'Reservations', 'reservations', 1, '88/', 16, 0, 1, NULL, NULL, ''),
  (89, 88, 'Transport', 'reservations-transport', 2, '88/89/', 0, 0, 1, NULL, NULL, ''),
  (90, 88, 'Hotel', 'reservations-hotel', 2, '88/90/', 0, 0, 1, NULL, NULL, ''),
  (91, 88, 'Tour & Travel', 'reservations-tour-travel', 2, '88/91/', 0, 0, 1, NULL, NULL, ''),
  (92, 88, 'Steamer', 'reservations-steamer', 2, '88/92/', 0, 0, 1, NULL, NULL, ''),
  (93, NULL, 'Books & Magazines', 'books-magazines', 1, '93/', 6, 1, 1, NULL, NULL, ''),
  (94, 93, 'Regular Books', 'books-magazines-regular-books', 2, '93/94/', 0, 0, 1, NULL, NULL, ''),
  (95, 93, 'Kids', 'books-magazines-kids', 2, '93/95/', 0, 0, 1, NULL, NULL, ''),
  (96, 93, 'Magazines', 'books-magazines-magazines', 2, '93/96/', 0, 0, 1, NULL, NULL, ''),
  (97, NULL, 'Sports', 'sports', 1, '97/', 18, 1, 1, NULL, NULL, ''),
  (98, 97, 'Indoor Sports', 'sports-indoor-sports', 2, '97/98/', 0, 0, 1, NULL, NULL, ''),
  (99, 2, 'Casual Shirts', 'wears-men-s-wears-casual-shirts', 3, '1/2/99/', 0, 0, 1, NULL, NULL, ''),
  (100, 2, 'Casual Trousers', 'wears-men-s-wears-casual-trousers', 3, '1/2/100/', 0, 0, 1, NULL, NULL, ''),
  (101, 2, 'Cardigans & Jumpers', 'wears-men-s-wears-cardigans-jumpers', 3, '1/2/101/', 0, 0, 1, NULL, NULL, ''),
  (102, 2, 'Formal Shirts', 'wears-men-s-wears-formal-shirts', 3, '1/2/102/', 0, 0, 1, NULL, NULL, ''),
  (103, 2, 'Formal Pant', 'wears-men-s-wears-formal-pant', 3, '1/2/103/', 0, 0, 1, NULL, NULL, ''),
  (104, 2, 'Formal Trousers', 'wears-men-s-wears-formal-trousers', 3, '1/2/104/', 0, 0, 1, NULL, NULL, ''),
  (105, 2, 'Jeans', 'wears-men-s-wears-jeans', 3, '1/2/105/', 0, 0, 1, NULL, NULL, ''),
  (106, 2, 'Knitwear', 'wears-men-s-wears-knitwear', 3, '1/2/106/', 0, 0, 1, NULL, NULL, ''),
  (107, 2, 'Nightwear', 'wears-men-s-wears-nightwear', 3, '1/2/107/', 0, 0, 1, NULL, NULL, ''),
  (108, 2, 'Panjabi', 'wears-men-s-wears-panjabi', 3, '1/2/108/', 0, 0, 1, NULL, NULL, ''),
  (109, 2, 'Short Panjabi', 'wears-men-s-wears-short-panjabi', 3, '1/2/109/', 0, 0, 1, NULL, NULL, ''),
  (110, 2, 'Smart Shirts', 'wears-men-s-wears-smart-shirts', 3, '1/2/110/', 0, 0, 1, NULL, NULL, ''),
  (111, 2, 'Shorts & Swimwear', 'wears-men-s-wears-shorts-swimwear', 3, '1/2/111/', 0, 0, 1, NULL, NULL, ''),
  (112, 2, 'Socks', 'wears-men-s-wears-socks', 3, '1/2/112/', 0, 0, 1, NULL, NULL, ''),
  (113, 2, 'Sportswear', 'wears-men-s-wears-sportswear', 3, '1/2/113/', 0, 0, 1, NULL, NULL, ''),
  (114, 2, 'Suits', 'wears-men-s-wears-suits', 3, '1/2/114/', 0, 0, 1, NULL, NULL, ''),
  (115, 2, 'T-Shirts', 'wears-men-s-wears-t-shirts', 3, '1/2/115/', 0, 0, 1, NULL, NULL, ''),
  (116, 2, 'Ties', 'wears-men-s-wears-ties', 3, '1/2/116/', 0, 0, 1, NULL, NULL, ''),
  (117, 2, 'Underwear', 'wears-men-s-wears-underwear', 3, '1/2/117/', 0, 0, 1, NULL, NULL, ''),
  (118, 2, 'Waistcoats', 'wears-men-s-wears-waistcoats', 3, '1/2/118/', 0, 0, 1, NULL, NULL, ''),
  (119, 2, 'Accessories', 'wears-men-s-wears-accessories', 3, '1/2/119/', 0, 0, 1, NULL, NULL, ''),
  (120, 1, 'Sharee', 'wears-sharee', 2, '1/120/', 0, 0, 1, NULL, NULL, ''),
  (121, 3, 'Coats & Jackets', 'wears-women-s-wears-coats-jackets', 3, '1/3/121/', 0, 0, 1, NULL, NULL, ''),
  (122, 3, 'Dresses', 'wears-women-s-wears-dresses', 3, '1/3/122/', 0, 0, 1, NULL, NULL, ''),
  (123, 3, 'Fatua', 'wears-women-s-wears-fatua', 3, '1/3/123/', 0, 0, 1, NULL, NULL, ''),
  (124, 3, 'Jeans Laydies', 'wears-women-s-wears-jeans-laydies', 3, '1/3/124/', 0, 0, 1, NULL, NULL, ''),
  (125, 3, 'Hijab Niqab Burqa', 'wears-women-s-wears-hijab-niqab-burqa', 3, '1/3/125/', 0, 0, 1, NULL, NULL, ''),
  (126, 3, 'Indian Dresses', 'wears-women-s-wears-indian-dresses', 3, '1/3/126/', 0, 0, 1, NULL, NULL, ''),
  (127, 3, 'Knitwear', 'wears-women-s-wears-knitwear', 3, '1/3/127/', 0, 0, 1, NULL, NULL, ''),
  (128, 3, 'Lehenga', 'wears-women-s-wears-lehenga', 3, '1/3/128/', 0, 0, 1, NULL, NULL, ''),
  (129, 3, 'Long Scart', 'wears-women-s-wears-long-scart', 3, '1/3/129/', 0, 0, 1, NULL, NULL, ''),
  (130, 3, 'Leggings', 'wears-women-s-wears-leggings', 3, '1/3/130/', 0, 0, 1, NULL, NULL, ''),
  (131, 3, 'Ladies Panjabi', 'wears-women-s-wears-ladies-panjabi', 3, '1/3/131/', 0, 0, 1, NULL, NULL, ''),
  (132, 3, 'Ladeies Short Panjabi', 'wears-women-s-wears-ladeies-short-panjabi', 3, '1/3/132/', 0, 0, 1, NULL, NULL, ''),
  (133, 3, 'Lingerie & Underwear', 'wears-women-s-wears-lingerie-underwear', 3, '1/3/133/', 0, 0, 1, NULL, NULL, ''),
  (134, 3, 'Mini Scart', 'wears-women-s-wears-mini-scart', 3, '1/3/134/', 0, 0, 1, NULL, NULL, ''),
  (135, 3, 'Maternity', 'wears-women-s-wears-maternity', 3, '1/3/135/', 0, 0, 1, NULL, NULL, ''),
  (136, 3, 'Nightwear', 'wears-women-s-wears-nightwear', 3, '1/3/136/', 0, 0, 1, NULL, NULL, ''),
  (137, 3, 'Petite', 'wears-women-s-wears-petite', 3, '1/3/137/', 0, 0, 1, NULL, NULL, ''),
  (138, 3, 'Short Shirt', 'wears-women-s-wears-short-shirt', 3, '1/3/138/', 0, 0, 1, NULL, NULL, ''),
  (139, 3, 'Socks & Tights', 'wears-women-s-wears-socks-tights', 3, '1/3/139/', 0, 0, 1, NULL, NULL, ''),
  (140, 3, 'Sportswear', 'wears-women-s-wears-sportswear', 3, '1/3/140/', 0, 0, 1, NULL, NULL, ''),
  (141, 3, 'Suits & Tailoring', 'wears-women-s-wears-suits-tailoring', 3, '1/3/141/', 0, 0, 1, NULL, NULL, ''),
  (142, 3, 'Swimwear & Beachwear', 'wears-women-s-wears-swimwear-beachwear', 3, '1/3/142/', 0, 0, 1, NULL, NULL, ''),
  (143, 3, 'Two piec', 'wears-women-s-wears-two-piec', 3, '1/3/143/', 0, 0, 1, NULL, NULL, ''),
  (144, 3, 'Three pice', 'wears-women-s-wears-three-pice', 3, '1/3/144/', 0, 0, 1, NULL, NULL, ''),
  (145, 3, 'Three Quater', 'wears-women-s-wears-three-quater', 3, '1/3/145/', 0, 0, 1, NULL, NULL, ''),
  (146, 3, 'Tops', 'wears-women-s-wears-tops', 3, '1/3/146/', 0, 0, 1, NULL, NULL, ''),
  (147, 3, 'Trousers & Shorts', 'wears-women-s-wears-trousers-shorts', 3, '1/3/147/', 0, 0, 1, NULL, NULL, ''),
  (148, 3, 'Accessories', 'wears-women-s-wears-accessories', 3, '1/3/148/', 0, 0, 1, NULL, NULL, ''),
  (149, 4, 'Baby & Toddlerwear', 'wears-baby-child-wear-s-baby-toddlerwear', 3, '1/4/149/', 0, 0, 1, NULL, NULL, ''),
  (150, 4, 'Boyswear', 'wears-baby-child-wear-s-boyswear', 3, '1/4/150/', 0, 0, 1, NULL, NULL, ''),
  (151, 4, 'Fancy Dress', 'wears-baby-child-wear-s-fancy-dress', 3, '1/4/151/', 0, 0, 1, NULL, NULL, ''),
  (152, 4, 'Girlswear', 'wears-baby-child-wear-s-girlswear', 3, '1/4/152/', 0, 0, 1, NULL, NULL, ''),
  (153, 4, 'Mums & Babies', 'wears-baby-child-wear-s-mums-babies', 3, '1/4/153/', 0, 0, 1, NULL, NULL, ''),
  (154, 4, 'Nightwear', 'wears-baby-child-wear-s-nightwear', 3, '1/4/154/', 0, 0, 1, NULL, NULL, ''),
  (155, 4, 'School Uniform', 'wears-baby-child-wear-s-school-uniform', 3, '1/4/155/', 0, 0, 1, NULL, NULL, ''),
  (156, 4, 'Socks & Underwear', 'wears-baby-child-wear-s-socks-underwear', 3, '1/4/156/', 0, 0, 1, NULL, NULL, ''),
  (157, 4, 'Accessories', 'wears-baby-child-wear-s-accessories', 3, '1/4/157/', 0, 0, 1, NULL, NULL, ''),
  (158, 120, 'Cotton Sharee', 'wears-sharee-cotton-sharee', 3, '1/120/158/', 0, 0, 1, NULL, NULL, ''),
  (159, 120, 'Benarosi', 'wears-sharee-benarosi', 3, '1/120/159/', 0, 0, 1, NULL, NULL, ''),
  (160, 120, 'Bridal Sharee', 'wears-sharee-bridal-sharee', 3, '1/120/160/', 0, 0, 1, NULL, NULL, ''),
  (161, 120, 'Jamdani', 'wears-sharee-jamdani', 3, '1/120/161/', 0, 0, 1, NULL, NULL, ''),
  (162, 120, 'Jorzet', 'wears-sharee-jorzet', 3, '1/120/162/', 0, 0, 1, NULL, NULL, ''),
  (163, 120, 'Katan', 'wears-sharee-katan', 3, '1/120/163/', 0, 0, 1, NULL, NULL, ''),
  (164, 120, 'Rajshahi Silk', 'wears-sharee-rajshahi-silk', 3, '1/120/164/', 0, 0, 1, NULL, NULL, ''),
  (165, 120, 'Taat', 'wears-sharee-taat', 3, '1/120/165/', 0, 0, 1, NULL, NULL, ''),
  (166, 120, 'Tangail', 'wears-sharee-tangail', 3, '1/120/166/', 0, 0, 1, NULL, NULL, ''),
  (167, 120, 'Accessories', 'wears-sharee-accessories', 3, '1/120/167/', 0, 0, 1, NULL, NULL, ''),
  (168, 10, 'Boy''s  Shoes', 'foot-wears-boy-s-shoes', 2, '10/168/', 0, 0, 1, NULL, NULL, ''),
  (169, 168, 'Boys'' Athletic Shoes', 'foot-wears-boy-s-shoes-boys-athletic-shoes', 3, '10/168/169/', 0, 0, 1, NULL, NULL, ''),
  (170, 168, 'Boys'' Skate Shoes', 'foot-wears-boy-s-shoes-boys-skate-shoes', 3, '10/168/170/', 0, 0, 1, NULL, NULL, ''),
  (171, 168, 'Boys'' Sandals', 'foot-wears-boy-s-shoes-boys-sandals', 3, '10/168/171/', 0, 0, 1, NULL, NULL, ''),
  (172, 168, 'Boys'' Dress Shoes', 'foot-wears-boy-s-shoes-boys-dress-shoes', 3, '10/168/172/', 0, 0, 1, NULL, NULL, ''),
  (173, 168, 'Boys'' Grade-School Shoes', 'foot-wears-boy-s-shoes-boys-grade-school-shoes', 3, '10/168/173/', 0, 0, 1, NULL, NULL, ''),
  (174, 275, 'Bridal Shoes', 'foot-wears-other-s-shoes-bridal-shoes', 3, '10/275/174/', 0, 0, 1, NULL, NULL, ''),
  (175, 275, 'Converse', 'foot-wears-other-s-shoes-converse', 3, '10/275/175/', 0, 0, 1, NULL, NULL, ''),
  (176, 271, 'Men''s Sandals', 'foot-wears-men-s-shoes-men-s-sandals', 3, '10/271/176/', 0, 0, 1, NULL, NULL, ''),
  (177, 271, 'Men''s Dress Shoes', 'foot-wears-men-s-shoes-men-s-dress-shoes', 3, '10/271/177/', 0, 0, 1, NULL, NULL, ''),
  (178, 271, 'Men''s Casual Shoes', 'foot-wears-men-s-shoes-men-s-casual-shoes', 3, '10/271/178/', 0, 0, 1, NULL, NULL, ''),
  (179, 271, 'Men''s Work Boots', 'foot-wears-men-s-shoes-men-s-work-boots', 3, '10/271/179/', 0, 0, 1, NULL, NULL, ''),
  (180, 271, 'Men''s Athletic Shoes', 'foot-wears-men-s-shoes-men-s-athletic-shoes', 3, '10/271/180/', 0, 0, 1, NULL, NULL, ''),
  (182, 274, 'Women''s Dress Shoes', 'foot-wears-women-s-shoes-women-s-dress-shoes', 3, '10/274/182/', 0, 0, 1, NULL, NULL, ''),
  (183, 274, 'Women''s Casual Shoes', 'foot-wears-women-s-shoes-women-s-casual-shoes', 3, '10/274/183/', 0, 0, 1, NULL, NULL, ''),
  (184, 274, 'Women''s Boots', 'foot-wears-women-s-shoes-women-s-boots', 3, '10/274/184/', 0, 0, 1, NULL, NULL, ''),
  (185, 274, 'Women''s Athletic Shoes', 'foot-wears-women-s-shoes-women-s-athletic-shoes', 3, '10/274/185/', 0, 0, 1, NULL, NULL, ''),
  (186, 273, 'Girls'' Athletic Shoes', 'foot-wears-girl-s-shoes-girls-athletic-shoes', 3, '10/273/186/', 0, 0, 1, NULL, NULL, ''),
  (187, 273, 'Girls'' Sandals', 'foot-wears-girl-s-shoes-girls-sandals', 3, '10/273/187/', 0, 0, 1, NULL, NULL, ''),
  (188, 273, 'Girls'' Dress Shoes', 'foot-wears-girl-s-shoes-girls-dress-shoes', 3, '10/273/188/', 0, 0, 1, NULL, NULL, ''),
  (189, 273, 'Girls'' Grade-School Shoes', 'foot-wears-girl-s-shoes-girls-grade-school-shoes', 3, '10/273/189/', 0, 0, 1, NULL, NULL, ''),
  (190, 8, 'Jute Promotional Bags', 'jute-handicrafts-jute-products-jute-promotional-bags', 3, '6/8/190/', 0, 0, 1, NULL, NULL, ''),
  (191, 8, 'Jute Shopping Bags', 'jute-handicrafts-jute-products-jute-shopping-bags', 3, '6/8/191/', 0, 0, 1, NULL, NULL, ''),
  (192, 8, 'Jute Gift Bags', 'jute-handicrafts-jute-products-jute-gift-bags', 3, '6/8/192/', 0, 0, 1, NULL, NULL, ''),
  (193, 8, 'Jute Beach Bags', 'jute-handicrafts-jute-products-jute-beach-bags', 3, '6/8/193/', 0, 0, 1, NULL, NULL, ''),
  (194, 8, 'Jute Embroidery Bags', 'jute-handicrafts-jute-products-jute-embroidery-bags', 3, '6/8/194/', 0, 0, 1, NULL, NULL, ''),
  (195, 8, 'Jute Designer Bags', 'jute-handicrafts-jute-products-jute-designer-bags', 3, '6/8/195/', 0, 0, 1, NULL, NULL, ''),
  (196, 8, 'Jute Fancy Bags', 'jute-handicrafts-jute-products-jute-fancy-bags', 3, '6/8/196/', 0, 0, 1, NULL, NULL, ''),
  (197, 8, 'Jute Bottole Bags', 'jute-handicrafts-jute-products-jute-bottole-bags', 3, '6/8/197/', 0, 0, 1, NULL, NULL, ''),
  (198, 8, 'Jute Colour Shed', 'jute-handicrafts-jute-products-jute-colour-shed', 3, '6/8/198/', 0, 0, 1, NULL, NULL, ''),
  (199, 8, 'Cotton Bag', 'jute-handicrafts-jute-products-cotton-bag', 3, '6/8/199/', 0, 0, 1, NULL, NULL, ''),
  (200, 8, 'Cotton Promotional Bags', 'jute-handicrafts-jute-products-cotton-promotional-bags', 3, '6/8/200/', 0, 0, 1, NULL, NULL, ''),
  (201, 8, 'Canvas Bags', 'jute-handicrafts-jute-products-canvas-bags', 3, '6/8/201/', 0, 0, 1, NULL, NULL, ''),
  (202, 8, 'Canvas Promotional Bags', 'jute-handicrafts-jute-products-canvas-promotional-bags', 3, '6/8/202/', 0, 0, 1, NULL, NULL, ''),
  (203, 8, 'Non Woven Bags', 'jute-handicrafts-jute-products-non-woven-bags', 3, '6/8/203/', 0, 0, 1, NULL, NULL, ''),
  (204, 8, 'Handles', 'jute-handicrafts-jute-products-handles', 3, '6/8/204/', 0, 0, 1, NULL, NULL, ''),
  (205, 9, 'Wood', 'jute-handicrafts-handicrafts-wood', 3, '6/9/205/', 0, 0, 1, NULL, NULL, ''),
  (206, 9, 'Stone', 'jute-handicrafts-handicrafts-stone-1', 3, '6/9/206/', 0, 0, 1, NULL, NULL, ''),
  (207, 9, 'Stone', 'jute-handicrafts-handicrafts-stone', 3, '6/9/207/', 0, 0, 1, NULL, NULL, ''),
  (208, 9, 'Glass', 'jute-handicrafts-handicrafts-glass', 3, '6/9/208/', 0, 0, 1, NULL, NULL, ''),
  (209, 9, 'Cane And Bamboo', 'jute-handicrafts-handicrafts-cane-and-bamboo', 3, '6/9/209/', 0, 0, 1, NULL, NULL, ''),
  (210, 9, 'Pottery', 'jute-handicrafts-handicrafts-pottery', 3, '6/9/210/', 0, 0, 1, NULL, NULL, ''),
  (211, 9, 'Home Decorations', 'jute-handicrafts-handicrafts-home-decorations', 3, '6/9/211/', 0, 0, 1, NULL, NULL, ''),
  (212, 9, 'Textile Designer', 'jute-handicrafts-handicrafts-textile-designer', 3, '6/9/212/', 0, 0, 1, NULL, NULL, ''),
  (213, 9, 'Home Furnishing', 'jute-handicrafts-handicrafts-home-furnishing', 3, '6/9/213/', 0, 0, 1, NULL, NULL, ''),
  (214, 9, 'Tribal', 'jute-handicrafts-handicrafts-tribal', 3, '6/9/214/', 0, 0, 1, NULL, NULL, ''),
  (215, 9, 'Beaded', 'jute-handicrafts-handicrafts-beaded', 3, '6/9/215/', 0, 0, 1, NULL, NULL, ''),
  (216, 9, 'Metal', 'jute-handicrafts-handicrafts-metal', 3, '6/9/216/', 0, 0, 1, NULL, NULL, ''),
  (217, 9, 'Silver', 'jute-handicrafts-handicrafts-silver', 3, '6/9/217/', 0, 0, 1, NULL, NULL, ''),
  (218, 9, 'Lacquer', 'jute-handicrafts-handicrafts-lacquer', 3, '6/9/218/', 0, 0, 1, NULL, NULL, ''),
  (219, 9, 'Jewelry Boxes', 'jute-handicrafts-handicrafts-jewelry-boxes', 3, '6/9/219/', 0, 0, 1, NULL, NULL, ''),
  (220, 9, 'Fashion Jewelry', 'jute-handicrafts-handicrafts-fashion-jewelry', 3, '6/9/220/', 0, 0, 1, NULL, NULL, ''),
  (221, 9, 'Apparels & Accessories', 'jute-handicrafts-handicrafts-apparels-accessories', 3, '6/9/221/', 0, 0, 1, NULL, NULL, ''),
  (222, 9, 'Gifts For Her', 'jute-handicrafts-handicrafts-gifts-for-her', 3, '6/9/222/', 0, 0, 1, NULL, NULL, ''),
  (223, 9, 'Fashion Accessory', 'jute-handicrafts-handicrafts-fashion-accessory', 3, '6/9/223/', 0, 0, 1, NULL, NULL, ''),
  (224, 9, 'Folk Art', 'jute-handicrafts-handicrafts-folk-art', 3, '6/9/224/', 0, 0, 1, NULL, NULL, ''),
  (225, 9, 'Miniature Painting', 'jute-handicrafts-handicrafts-miniature-painting', 3, '6/9/225/', 0, 0, 1, NULL, NULL, ''),
  (226, 9, 'Contemporary Artist', 'jute-handicrafts-handicrafts-contemporary-artist', 3, '6/9/226/', 0, 0, 1, NULL, NULL, ''),
  (227, 9, 'Birth Day Gifts', 'jute-handicrafts-handicrafts-birth-day-gifts', 3, '6/9/227/', 0, 0, 1, NULL, NULL, ''),
  (228, 9, 'Marrige Day Gift', 'jute-handicrafts-handicrafts-marrige-day-gift', 3, '6/9/228/', 0, 0, 1, NULL, NULL, ''),
  (229, 9, 'Valentine Day Gifts', 'jute-handicrafts-handicrafts-valentine-day-gifts-1', 3, '6/9/229/', 0, 0, 1, NULL, NULL, ''),
  (230, 9, 'Mothers Day Gifts', 'jute-handicrafts-handicrafts-mothers-day-gifts', 3, '6/9/230/', 0, 0, 1, NULL, NULL, ''),
  (231, 9, 'Fathers Day Gifts', 'jute-handicrafts-handicrafts-fathers-day-gifts', 3, '6/9/231/', 0, 0, 1, NULL, NULL, ''),
  (232, 9, 'Valentine Day Gifts', 'jute-handicrafts-handicrafts-valentine-day-gifts', 3, '6/9/232/', 0, 0, 1, NULL, NULL, ''),
  (233, 9, 'Christmas Gift', 'jute-handicrafts-handicrafts-christmas-gift', 3, '6/9/233/', 0, 0, 1, NULL, NULL, ''),
  (234, 9, 'Candles', 'jute-handicrafts-handicrafts-candles', 3, '6/9/234/', 0, 0, 1, NULL, NULL, ''),
  (235, 9, 'CraftsCenter', 'jute-handicrafts-handicrafts-craftscenter', 3, '6/9/235/', 0, 0, 1, NULL, NULL, ''),
  (236, 9, 'Affiliates', 'jute-handicrafts-handicrafts-affiliates', 3, '6/9/236/', 0, 0, 1, NULL, NULL, ''),
  (237, 9, 'Picture Gallery', 'jute-handicrafts-handicrafts-picture-gallery', 3, '6/9/237/', 0, 0, 1, NULL, NULL, ''),
  (238, 9, 'Marble Write up', 'jute-handicrafts-handicrafts-marble-write-up', 3, '6/9/238/', 0, 0, 1, NULL, NULL, ''),
  (239, 9, 'Stone Carving', 'jute-handicrafts-handicrafts-stone-carving', 3, '6/9/239/', 0, 0, 1, NULL, NULL, ''),
  (240, 9, 'Stone Write up', 'jute-handicrafts-handicrafts-stone-write-up', 3, '6/9/240/', 0, 0, 1, NULL, NULL, ''),
  (241, 9, 'Marble Sculptures', 'jute-handicrafts-handicrafts-marble-sculptures', 3, '6/9/241/', 0, 0, 1, NULL, NULL, ''),
  (242, 9, 'Kids Crafts', 'jute-handicrafts-handicrafts-kids-crafts', 3, '6/9/242/', 0, 0, 1, NULL, NULL, ''),
  (243, 11, 'Shoulder Bugs', 'leather-bags-bag-travelling-shoulder-bugs', 3, '7/11/243/', 0, 0, 1, NULL, NULL, ''),
  (244, 11, 'Across Body', 'leather-bags-bag-travelling-across-body', 3, '7/11/244/', 0, 0, 1, NULL, NULL, ''),
  (245, 11, 'Evening & Clutch Bugs', 'leather-bags-bag-travelling-evening-clutch-bugs', 3, '7/11/245/', 0, 0, 1, NULL, NULL, ''),
  (246, 11, 'Tote Bags', 'leather-bags-bag-travelling-tote-bags', 3, '7/11/246/', 0, 0, 1, NULL, NULL, ''),
  (247, 11, 'School bags', 'leather-bags-bag-travelling-school-bags', 3, '7/11/247/', 0, 0, 1, NULL, NULL, ''),
  (248, 11, 'Bowling', 'leather-bags-bag-travelling-bowling', 3, '7/11/248/', 0, 0, 1, NULL, NULL, ''),
  (249, 11, 'Backpacks', 'leather-bags-bag-travelling-backpacks', 3, '7/11/249/', 0, 0, 1, NULL, NULL, ''),
  (250, 11, 'Workbags', 'leather-bags-bag-travelling-workbags', 3, '7/11/250/', 0, 0, 1, NULL, NULL, ''),
  (251, 11, 'Leather Bags', 'leather-bags-bag-travelling-leather-bags-1', 3, '7/11/251/', 0, 0, 1, NULL, NULL, ''),
  (252, 11, 'Ladies Hand Bags', 'leather-bags-bag-travelling-ladies-hand-bags', 3, '7/11/252/', 0, 0, 1, NULL, NULL, ''),
  (253, 11, 'Fancy Hand Bags', 'leather-bags-bag-travelling-fancy-hand-bags', 3, '7/11/253/', 0, 0, 1, NULL, NULL, ''),
  (254, 11, 'Purses & Wallets', 'leather-bags-bag-travelling-purses-wallets', 3, '7/11/254/', 0, 0, 1, NULL, NULL, ''),
  (255, 11, 'Across Body Bag', 'leather-bags-bag-travelling-across-body-bag', 3, '7/11/255/', 0, 0, 1, NULL, NULL, ''),
  (256, 11, 'Shopper Bags', 'leather-bags-bag-travelling-shopper-bags', 3, '7/11/256/', 0, 0, 1, NULL, NULL, ''),
  (257, 11, 'Occasion Handbags', 'leather-bags-bag-travelling-occasion-handbags', 3, '7/11/257/', 0, 0, 1, NULL, NULL, ''),
  (258, 11, 'Luggage & travel bags', 'leather-bags-bag-travelling-luggage-travel-bags', 3, '7/11/258/', 0, 0, 1, NULL, NULL, ''),
  (259, 11, 'Carry-on luggage', 'leather-bags-bag-travelling-carry-on-luggage', 3, '7/11/259/', 0, 0, 1, NULL, NULL, ''),
  (260, 11, 'Trolley Cases', 'leather-bags-bag-travelling-trolley-cases', 3, '7/11/260/', 0, 0, 1, NULL, NULL, ''),
  (261, 11, 'Wheeled Duffles', 'leather-bags-bag-travelling-wheeled-duffles', 3, '7/11/261/', 0, 0, 1, NULL, NULL, ''),
  (262, 11, 'Garment Bags', 'leather-bags-bag-travelling-garment-bags', 3, '7/11/262/', 0, 0, 1, NULL, NULL, ''),
  (265, 14, 'Diamonds', 'jewelry-watches-diamonds', 2, '14/265/', 0, 0, 1, NULL, NULL, ''),
  (266, 45, 'Steel Furniture', 'furnitures-steel-furniture', 2, '45/266/', 0, 0, 1, NULL, NULL, ''),
  (267, 45, 'Bamboo Furniture', 'furnitures-bamboo-furniture', 2, '45/267/', 0, 0, 1, NULL, NULL, ''),
  (268, 45, 'Cane Furniture', 'furnitures-cane-furniture', 2, '45/268/', 0, 0, 1, NULL, NULL, ''),
  (269, 45, 'Palywood Furniture', 'furnitures-palywood-furniture', 2, '45/269/', 0, 0, 1, NULL, NULL, ''),
  (271, 10, 'Men''s Shoes', 'foot-wears-men-s-shoes', 2, '10/271/', 0, 0, 1, NULL, NULL, ''),
  (273, 10, 'Girl''s Shoes', 'foot-wears-girl-s-shoes', 2, '10/273/', 0, 0, 1, NULL, NULL, ''),
  (274, 10, 'Women''s Shoes', 'foot-wears-women-s-shoes', 2, '10/274/', 0, 0, 1, NULL, NULL, ''),
  (275, 10, 'Other''s Shoes', 'foot-wears-other-s-shoes', 2, '10/275/', 0, 0, 1, NULL, NULL, ''),
  (276, 24, 'Cake & Pastry', 'foods-cake-pastry', 2, '24/276/', 0, 0, 1, NULL, NULL, ''),
  (277, 39, 'Sound & Vision', 'computer-electronics-sound-vision', 2, '39/277/', 0, 0, 1, NULL, NULL, ''),
  (278, 277, 'Televisions', 'computer-electronics-sound-vision-televisions', 3, '39/277/278/', 0, 0, 1, NULL, NULL, ''),
  (279, 277, 'Blu-ray, DVD & Home Cinema', 'computer-electronics-sound-vision-blu-ray-dvd-home-cinema', 3, '39/277/279/', 0, 0, 1, NULL, NULL, ''),
  (280, 277, 'Photography & Camcorders', 'computer-electronics-sound-vision-photography-camcorders', 3, '39/277/280/', 0, 0, 1, NULL, NULL, ''),
  (281, 277, 'Audio', 'computer-electronics-sound-vision-audio', 3, '39/277/281/', 0, 0, 1, NULL, NULL, ''),
  (282, 277, 'iPods & MP3 Players', 'computer-electronics-sound-vision-ipods-mp3-players', 3, '39/277/282/', 0, 0, 1, NULL, NULL, ''),
  (283, 277, 'Headphones', 'computer-electronics-sound-vision-headphones', 3, '39/277/283/', 0, 0, 1, NULL, NULL, ''),
  (284, 277, 'Gaming', 'computer-electronics-sound-vision-gaming', 3, '39/277/284/', 0, 0, 1, NULL, NULL, ''),
  (285, 277, 'Kindle & eReaders', 'computer-electronics-sound-vision-kindle-ereaders', 3, '39/277/285/', 0, 0, 1, NULL, NULL, ''),
  (286, 277, 'Stands & Accessories', 'computer-electronics-sound-vision-stands-accessories', 3, '39/277/286/', 0, 0, 1, NULL, NULL, ''),
  (287, 277, 'Technology Offers', 'computer-electronics-sound-vision-technology-offers', 3, '39/277/287/', 0, 0, 1, NULL, NULL, ''),
  (288, 42, 'iPad & Tablet PCs', 'computer-electronics-computing-accessories-ipad-tablet-pcs', 3, '39/42/288/', 0, 0, 1, NULL, NULL, ''),
  (289, 42, 'Laptops & Netbooks', 'computer-electronics-computing-accessories-laptops-netbooks', 3, '39/42/289/', 0, 0, 1, NULL, NULL, ''),
  (290, 42, 'Desktop PCs & Servers', 'computer-electronics-computing-accessories-desktop-pcs-servers', 3, '39/42/290/', 0, 0, 1, NULL, NULL, ''),
  (291, 42, 'Software', 'computer-electronics-computing-accessories-software', 3, '39/42/291/', 0, 0, 1, NULL, NULL, ''),
  (292, 42, 'Printing', 'computer-electronics-computing-accessories-printing', 3, '39/42/292/', 0, 0, 1, NULL, NULL, ''),
  (293, 42, 'Computer Accessories', 'computer-electronics-computing-accessories-computer-accessories', 3, '39/42/293/', 0, 0, 1, NULL, NULL, ''),
  (294, 42, 'Telephones', 'computer-electronics-computing-accessories-telephones', 3, '39/42/294/', 0, 0, 1, NULL, NULL, ''),
  (295, 40, 'Express Delivery', 'computer-electronics-consumer-electronics-express-delivery', 3, '39/40/295/', 0, 0, 1, NULL, NULL, ''),
  (296, 40, 'Cookers & Ovens', 'computer-electronics-consumer-electronics-cookers-ovens', 3, '39/40/296/', 0, 0, 1, NULL, NULL, ''),
  (297, 40, 'Dishwashers', 'computer-electronics-consumer-electronics-dishwashers', 3, '39/40/297/', 0, 0, 1, NULL, NULL, ''),
  (298, 40, 'Fridges & Freezers', 'computer-electronics-consumer-electronics-fridges-freezers', 3, '39/40/298/', 0, 0, 1, NULL, NULL, ''),
  (299, 40, 'Washing Machines', 'computer-electronics-consumer-electronics-washing-machines', 3, '39/40/299/', 0, 0, 1, NULL, NULL, ''),
  (300, 40, 'Tumble Dryers', 'computer-electronics-consumer-electronics-tumble-dryers', 3, '39/40/300/', 0, 0, 1, NULL, NULL, ''),
  (301, 40, 'Washer Dryers', 'computer-electronics-consumer-electronics-washer-dryers', 3, '39/40/301/', 0, 0, 1, NULL, NULL, ''),
  (302, 40, 'Vacuum Cleaners', 'computer-electronics-consumer-electronics-vacuum-cleaners', 3, '39/40/302/', 0, 0, 1, NULL, NULL, ''),
  (303, 40, 'Fires', 'computer-electronics-consumer-electronics-fires', 3, '39/40/303/', 0, 0, 1, NULL, NULL, ''),
  (304, 40, 'Hostess Trolleys', 'computer-electronics-consumer-electronics-hostess-trolleys', 3, '39/40/304/', 0, 0, 1, NULL, NULL, ''),
  (305, 40, 'Maytag Laundry Solutions', 'computer-electronics-consumer-electronics-maytag-laundry-solutions', 3, '39/40/305/', 0, 0, 1, NULL, NULL, ''),
  (306, 40, 'Cooking Appliances', 'computer-electronics-consumer-electronics-cooking-appliances', 3, '39/40/306/', 0, 0, 1, NULL, NULL, ''),
  (307, 40, 'Food & Drink Preparation', 'computer-electronics-consumer-electronics-food-drink-preparation', 3, '39/40/307/', 0, 0, 1, NULL, NULL, ''),
  (308, 40, 'Tea & Coffee', 'computer-electronics-consumer-electronics-tea-coffee', 3, '39/40/308/', 0, 0, 1, NULL, NULL, ''),
  (309, 40, 'Kettles', 'computer-electronics-consumer-electronics-kettles', 3, '39/40/309/', 0, 0, 1, NULL, NULL, ''),
  (310, 40, 'Toasters', 'computer-electronics-consumer-electronics-toasters', 3, '39/40/310/', 0, 0, 1, NULL, NULL, ''),
  (311, 40, 'Kettle & Toaster Sets', 'computer-electronics-consumer-electronics-kettle-toaster-sets', 3, '39/40/311/', 0, 0, 1, NULL, NULL, ''),
  (312, 40, 'Ironing', 'computer-electronics-consumer-electronics-ironing', 3, '39/40/312/', 0, 0, 1, NULL, NULL, ''),
  (313, 40, 'Security & Monitoring', 'computer-electronics-consumer-electronics-security-monitoring', 3, '39/40/313/', 0, 0, 1, NULL, NULL, ''),
  (314, 40, 'Heating & Cooling', 'computer-electronics-consumer-electronics-heating-cooling', 3, '39/40/314/', 0, 0, 1, NULL, NULL, ''),
  (315, 40, 'Sewing', 'computer-electronics-consumer-electronics-sewing', 3, '39/40/315/', 0, 0, 1, NULL, NULL, ''),
  (316, 41, 'Blackberry Cases', 'computer-electronics-mobile-phone-accessories-blackberry-cases', 3, '39/41/316/', 0, 0, 1, NULL, NULL, ''),
  (317, 41, 'Handsfree Kits', 'computer-electronics-mobile-phone-accessories-handsfree-kits', 3, '39/41/317/', 0, 0, 1, NULL, NULL, ''),
  (318, 41, 'iPhone Cases', 'computer-electronics-mobile-phone-accessories-iphone-cases', 3, '39/41/318/', 0, 0, 1, NULL, NULL, ''),
  (319, 41, 'Chargers', 'computer-electronics-mobile-phone-accessories-chargers', 3, '39/41/319/', 0, 0, 1, NULL, NULL, ''),
  (320, 39, 'Solar System', 'computer-electronics-solar-system', 2, '39/320/', 0, 0, 1, NULL, NULL, ''),
  (321, 320, 'Solar Street Light', 'computer-electronics-solar-system-solar-street-light', 3, '39/320/321/', 0, 0, 1, NULL, NULL, ''),
  (322, 320, 'Solar Water Heater', 'computer-electronics-solar-system-solar-water-heater', 3, '39/320/322/', 0, 0, 1, NULL, NULL, ''),
  (323, 320, 'Solar Water Pump', 'computer-electronics-solar-system-solar-water-pump', 3, '39/320/323/', 0, 0, 1, NULL, NULL, ''),
  (324, 320, 'Solar Garden Light', 'computer-electronics-solar-system-solar-garden-light', 3, '39/320/324/', 0, 0, 1, NULL, NULL, ''),
  (325, 320, 'Solar Power Packs', 'computer-electronics-solar-system-solar-power-packs', 3, '39/320/325/', 0, 0, 1, NULL, NULL, ''),
  (326, 320, 'Solar Cooking System', 'computer-electronics-solar-system-solar-cooking-system', 3, '39/320/326/', 0, 0, 1, NULL, NULL, ''),
  (327, 320, 'Solar Laptop Charger', 'computer-electronics-solar-system-solar-laptop-charger', 3, '39/320/327/', 0, 0, 1, NULL, NULL, ''),
  (328, 320, 'Solar Mobile Phone Charger', 'computer-electronics-solar-system-solar-mobile-phone-charger', 3, '39/320/328/', 0, 0, 1, NULL, NULL, ''),
  (329, 44, 'Interior Lighting', 'electrical-lighting-interior-lighting', 2, '44/329/', 0, 0, 1, NULL, NULL, ''),
  (330, 44, 'Exterior Lighting', 'electrical-lighting-exterior-lighting', 2, '44/330/', 0, 0, 1, NULL, NULL, ''),
  (331, 44, 'Emergency Lighting', 'electrical-lighting-emergency-lighting', 2, '44/331/', 0, 0, 1, NULL, NULL, ''),
  (332, 44, 'Lamps & Tubes', 'electrical-lighting-lamps-tubes', 2, '44/332/', 0, 0, 1, NULL, NULL, ''),
  (334, 44, 'Transformers & Drivers', 'electrical-lighting-transformers-drivers', 2, '44/334/', 0, 0, 1, NULL, NULL, ''),
  (335, 44, 'Wiring Accessories', 'electrical-lighting-wiring-accessories', 2, '44/335/', 0, 0, 1, NULL, NULL, ''),
  (336, 44, 'Protection Accessories', 'electrical-lighting-protection-accessories', 2, '44/336/', 0, 0, 1, NULL, NULL, ''),
  (337, 44, 'Control Accessories', 'electrical-lighting-control-accessories', 2, '44/337/', 0, 0, 1, NULL, NULL, ''),
  (338, 94, 'Art, Architecture & Photography', 'books-magazines-regular-books-art-architecture-photography', 3, '93/94/338/', 0, 0, 1, NULL, NULL, ''),
  (339, 94, 'Audiobooks', 'books-magazines-regular-books-audiobooks', 3, '93/94/339/', 0, 0, 1, NULL, NULL, ''),
  (340, 94, 'Biography', 'books-magazines-regular-books-biography', 3, '93/94/340/', 0, 0, 1, NULL, NULL, ''),
  (341, 94, 'Books For Study', 'books-magazines-regular-books-books-for-study', 3, '93/94/341/', 0, 0, 1, NULL, NULL, ''),
  (342, 94, 'Business, Finance & Law', 'books-magazines-regular-books-business-finance-law', 3, '93/94/342/', 0, 0, 1, NULL, NULL, ''),
  (343, 94, 'Calendars, Diaries, Annuals & More', 'books-magazines-regular-books-calendars-diaries-annuals-more', 3, '93/94/343/', 0, 0, 1, NULL, NULL, ''),
  (344, 94, 'Children''s Books', 'books-magazines-regular-books-children-s-books', 3, '93/94/344/', 0, 0, 1, NULL, NULL, ''),
  (345, 94, 'Comics & Graphic Novels', 'books-magazines-regular-books-comics-graphic-novels', 3, '93/94/345/', 0, 0, 1, NULL, NULL, ''),
  (346, 94, 'Computing & Internet', 'books-magazines-regular-books-computing-internet', 3, '93/94/346/', 0, 0, 1, NULL, NULL, ''),
  (347, 94, 'Crime, Thrillers & Mystery', 'books-magazines-regular-books-crime-thrillers-mystery', 3, '93/94/347/', 0, 0, 1, NULL, NULL, ''),
  (348, 94, 'Fiction', 'books-magazines-regular-books-fiction', 3, '93/94/348/', 0, 0, 1, NULL, NULL, ''),
  (349, 94, 'Food & Drink', 'books-magazines-regular-books-food-drink', 3, '93/94/349/', 0, 0, 1, NULL, NULL, ''),
  (350, 94, 'Health, Family & Lifestyle', 'books-magazines-regular-books-health-family-lifestyle', 3, '93/94/350/', 0, 0, 1, NULL, NULL, ''),
  (351, 94, 'History', 'books-magazines-regular-books-history', 3, '93/94/351/', 0, 0, 1, NULL, NULL, ''),
  (352, 94, 'Home & Garden', 'books-magazines-regular-books-home-garden', 3, '93/94/352/', 0, 0, 1, NULL, NULL, ''),
  (353, 94, 'Horror', 'books-magazines-regular-books-horror', 3, '93/94/353/', 0, 0, 1, NULL, NULL, ''),
  (354, 94, 'Humour', 'books-magazines-regular-books-humour', 3, '93/94/354/', 0, 0, 1, NULL, NULL, ''),
  (355, 94, 'Languages', 'books-magazines-regular-books-languages', 3, '93/94/355/', 0, 0, 1, NULL, NULL, ''),
  (356, 94, 'Mind, Body & Spirit', 'books-magazines-regular-books-mind-body-spirit', 3, '93/94/356/', 0, 0, 1, NULL, NULL, ''),
  (357, 94, 'Music, Stage & Screen', 'books-magazines-regular-books-music-stage-screen', 3, '93/94/357/', 0, 0, 1, NULL, NULL, ''),
  (358, 94, 'Poetry, Drama & Criticism', 'books-magazines-regular-books-poetry-drama-criticism', 3, '93/94/358/', 0, 0, 1, NULL, NULL, ''),
  (359, 94, 'Reference', 'books-magazines-regular-books-reference', 3, '93/94/359/', 0, 0, 1, NULL, NULL, ''),
  (360, 94, 'Religion & Spirituality', 'books-magazines-regular-books-religion-spirituality', 3, '93/94/360/', 0, 0, 1, NULL, NULL, ''),
  (361, 94, 'Romance', 'books-magazines-regular-books-romance', 3, '93/94/361/', 0, 0, 1, NULL, NULL, ''),
  (362, 94, 'Science & Nature', 'books-magazines-regular-books-science-nature', 3, '93/94/362/', 0, 0, 1, NULL, NULL, ''),
  (363, 94, 'Science Fiction & Fantasy', 'books-magazines-regular-books-science-fiction-fantasy', 3, '93/94/363/', 0, 0, 1, NULL, NULL, ''),
  (364, 94, 'Scientific, Technical & Medical', 'books-magazines-regular-books-scientific-technical-medical', 3, '93/94/364/', 0, 0, 1, NULL, NULL, ''),
  (365, 94, 'Society, Politics & Philosophy', 'books-magazines-regular-books-society-politics-philosophy', 3, '93/94/365/', 0, 0, 1, NULL, NULL, ''),
  (366, 94, 'Sports, Hobbies & Games', 'books-magazines-regular-books-sports-hobbies-games', 3, '93/94/366/', 0, 0, 1, NULL, NULL, ''),
  (367, 94, 'Textbooks For University', 'books-magazines-regular-books-textbooks-for-university', 3, '93/94/367/', 0, 0, 1, NULL, NULL, ''),
  (368, 94, 'Travel & Holiday', 'books-magazines-regular-books-travel-holiday', 3, '93/94/368/', 0, 0, 1, NULL, NULL, ''),
  (369, 60, 'Haircare', 'health-medicine-beauty-equipment-haircare', 3, '59/60/369/', 0, 0, 1, NULL, NULL, ''),
  (370, 60, 'Shaving & Hair Removal', 'health-medicine-beauty-equipment-shaving-hair-removal', 3, '59/60/370/', 0, 0, 1, NULL, NULL, ''),
  (371, 60, 'Health Monitors', 'health-medicine-beauty-equipment-health-monitors', 3, '59/60/371/', 0, 0, 1, NULL, NULL, ''),
  (372, 60, 'Personal Care', 'health-medicine-beauty-equipment-personal-care', 3, '59/60/372/', 0, 0, 1, NULL, NULL, ''),
  (373, 60, 'Breast Care Products', 'health-medicine-beauty-equipment-breast-care-products', 3, '59/60/373/', 0, 0, 1, NULL, NULL, ''),
  (374, 60, 'Massager', 'health-medicine-beauty-equipment-massager', 3, '59/60/374/', 0, 0, 1, NULL, NULL, ''),
  (375, 60, 'Fitness Equipment', 'health-medicine-beauty-equipment-fitness-equipment', 3, '59/60/375/', 0, 0, 1, NULL, NULL, ''),
  (376, 60, 'Treadmill', 'health-medicine-beauty-equipment-treadmill', 3, '59/60/376/', 0, 0, 1, NULL, NULL, ''),
  (377, 60, 'Breast Pump', 'health-medicine-beauty-equipment-breast-pump', 3, '59/60/377/', 0, 0, 1, NULL, NULL, ''),
  (378, 60, 'Other Beauty Equipment', 'health-medicine-beauty-equipment-other-beauty-equipment', 3, '59/60/378/', 0, 0, 1, NULL, NULL, ''),
  (379, 60, 'Hair Salon Equipment', 'health-medicine-beauty-equipment-hair-salon-equipment', 3, '59/60/379/', 0, 0, 1, NULL, NULL, ''),
  (380, 18, 'Make Up', 'beauty-fragrances-make-up', 2, '18/380/', 0, 0, 1, NULL, NULL, ''),
  (381, 18, 'Nails', 'beauty-fragrances-nails', 2, '18/381/', 0, 0, 1, NULL, NULL, ''),
  (382, 18, 'Slimming', 'beauty-fragrances-slimming', 2, '18/382/', 0, 0, 1, NULL, NULL, ''),
  (383, 18, 'Fragrance', 'beauty-fragrances-fragrance', 2, '18/383/', 0, 0, 1, NULL, NULL, ''),
  (384, 18, 'Organic & Natural', 'beauty-fragrances-organic-natural', 2, '18/384/', 0, 0, 1, NULL, NULL, ''),
  (385, 18, 'Men''s Shaving', 'beauty-fragrances-men-s-shaving', 2, '18/385/', 0, 0, 1, NULL, NULL, ''),
  (386, 18, 'Men''s Skincare', 'beauty-fragrances-men-s-skincare', 2, '18/386/', 0, 0, 1, NULL, NULL, ''),
  (387, 18, 'Men''s Bodycare', 'beauty-fragrances-men-s-bodycare', 2, '18/387/', 0, 0, 1, NULL, NULL, ''),
  (388, 18, 'Men''s Hair', 'beauty-fragrances-men-s-hair', 2, '18/388/', 0, 0, 1, NULL, NULL, ''),
  (389, 18, 'Men''s Fragrance', 'beauty-fragrances-men-s-fragrance', 2, '18/389/', 0, 0, 1, NULL, NULL, ''),
  (390, 18, 'Gift''s For Him', 'beauty-fragrances-gift-s-for-him', 2, '18/390/', 0, 0, 1, NULL, NULL, ''),
  (391, 18, 'Luxury Gifts', 'beauty-fragrances-luxury-gifts', 2, '18/391/', 0, 0, 1, NULL, NULL, ''),
  (392, 18, 'Men''s Toiletry & Wash Bags', 'beauty-fragrances-men-s-toiletry-wash-bags', 2, '18/392/', 0, 0, 1, NULL, NULL, ''),
  (393, 18, 'Men''s Tanning & Make-Up', 'beauty-fragrances-men-s-tanning-make-up', 2, '18/393/', 0, 0, 1, NULL, NULL, ''),
  (394, 18, 'Men''s Shavers', 'beauty-fragrances-men-s-shavers', 2, '18/394/', 0, 0, 1, NULL, NULL, ''),
  (395, 18, 'Men''s Hair Trimmers', 'beauty-fragrances-men-s-hair-trimmers', 2, '18/395/', 0, 0, 1, NULL, NULL, ''),
  (396, 14, 'Gold Jewellery', 'jewelry-watches-gold-jewellery', 2, '14/396/', 0, 0, 1, NULL, NULL, ''),
  (397, 14, 'Silver Jewellery', 'jewelry-watches-silver-jewellery', 2, '14/397/', 0, 0, 1, NULL, NULL, ''),
  (398, 14, 'Imitation Jewellery', 'jewelry-watches-imitation-jewellery', 2, '14/398/', 0, 0, 1, NULL, NULL, ''),
  (399, 396, 'Gold Ring', 'jewelry-watches-gold-jewellery-gold-ring', 3, '14/396/399/', 0, 0, 1, NULL, NULL, ''),
  (400, 396, 'Gold Pendant', 'jewelry-watches-gold-jewellery-gold-pendant', 3, '14/396/400/', 0, 0, 1, NULL, NULL, ''),
  (401, 396, 'Gold Bracelet', 'jewelry-watches-gold-jewellery-gold-bracelet', 3, '14/396/401/', 0, 0, 1, NULL, NULL, ''),
  (402, 396, 'Gold Earrings', 'jewelry-watches-gold-jewellery-gold-earrings', 3, '14/396/402/', 0, 0, 1, NULL, NULL, ''),
  (403, 396, 'Gold Necklace', 'jewelry-watches-gold-jewellery-gold-necklace', 3, '14/396/403/', 0, 0, 1, NULL, NULL, ''),
  (404, 396, 'Gold Bangles', 'jewelry-watches-gold-jewellery-gold-bangles', 3, '14/396/404/', 0, 0, 1, NULL, NULL, ''),
  (405, 397, 'Silver Earrings', 'jewelry-watches-silver-jewellery-silver-earrings', 3, '14/397/405/', 0, 0, 1, NULL, NULL, ''),
  (406, 397, 'Silver Bracelet', 'jewelry-watches-silver-jewellery-silver-bracelet', 3, '14/397/406/', 0, 0, 1, NULL, NULL, ''),
  (407, 397, 'Silver Necklace', 'jewelry-watches-silver-jewellery-silver-necklace', 3, '14/397/407/', 0, 0, 1, NULL, NULL, ''),
  (408, 397, 'Silver Pendant', 'jewelry-watches-silver-jewellery-silver-pendant', 3, '14/397/408/', 0, 0, 1, NULL, NULL, ''),
  (409, 265, 'Diamond Ring', 'jewelry-watches-diamonds-diamond-ring', 3, '14/265/409/', 0, 0, 1, NULL, NULL, ''),
  (410, 265, 'Diamond Pendant', 'jewelry-watches-diamonds-diamond-pendant', 3, '14/265/410/', 0, 0, 1, NULL, NULL, ''),
  (411, 265, 'Diamond Bracelets', 'jewelry-watches-diamonds-diamond-bracelets', 3, '14/265/411/', 0, 0, 1, NULL, NULL, ''),
  (412, 265, 'Diamond Earrings', 'jewelry-watches-diamonds-diamond-earrings', 3, '14/265/412/', 0, 0, 1, NULL, NULL, ''),
  (413, 265, 'Diamond Necklace', 'jewelry-watches-diamonds-diamond-necklace', 3, '14/265/413/', 0, 0, 1, NULL, NULL, ''),
  (414, 265, 'Diamond Bangles', 'jewelry-watches-diamonds-diamond-bangles', 3, '14/265/414/', 0, 0, 1, NULL, NULL, ''),
  (415, 398, 'Imitation Earrings', 'jewelry-watches-imitation-jewellery-imitation-earrings', 3, '14/398/415/', 0, 0, 1, NULL, NULL, ''),
  (416, 398, 'Imitation Necklace', 'jewelry-watches-imitation-jewellery-imitation-necklace', 3, '14/398/416/', 0, 0, 1, NULL, NULL, ''),
  (417, 398, 'Imitation Bracelet', 'jewelry-watches-imitation-jewellery-imitation-bracelet', 3, '14/398/417/', 0, 0, 1, NULL, NULL, ''),
  (418, 398, 'Imitation Pendant', 'jewelry-watches-imitation-jewellery-imitation-pendant', 3, '14/398/418/', 0, 0, 1, NULL, NULL, ''),
  (419, 265, 'Jewellery sets', 'jewelry-watches-diamonds-jewellery-sets', 3, '14/265/419/', 0, 0, 1, NULL, NULL, ''),
  (420, 396, 'Jewellery sets', 'jewelry-watches-gold-jewellery-jewellery-sets', 3, '14/396/420/', 0, 0, 1, NULL, NULL, ''),
  (421, 14, 'Stainless Jewellery', 'jewelry-watches-stainless-jewellery', 2, '14/421/', 0, 0, 1, NULL, NULL, ''),
  (422, 421, 'Bangles Jewellery', 'jewelry-watches-stainless-jewellery-bangles-jewellery', 3, '14/421/422/', 0, 0, 1, NULL, NULL, ''),
  (423, 421, 'Charms Jewellery', 'jewelry-watches-stainless-jewellery-charms-jewellery', 3, '14/421/423/', 0, 0, 1, NULL, NULL, ''),
  (424, 421, 'Necklaces', 'jewelry-watches-stainless-jewellery-necklaces', 3, '14/421/424/', 0, 0, 1, NULL, NULL, ''),
  (425, 421, 'Bracelets', 'jewelry-watches-stainless-jewellery-bracelets', 3, '14/421/425/', 0, 0, 1, NULL, NULL, ''),
  (426, 421, 'Earrings', 'jewelry-watches-stainless-jewellery-earrings', 3, '14/421/426/', 0, 0, 1, NULL, NULL, ''),
  (427, 421, 'Rings', 'jewelry-watches-stainless-jewellery-rings', 3, '14/421/427/', 0, 0, 1, NULL, NULL, ''),
  (428, 14, 'Men''s Jewellery', 'jewelry-watches-men-s-jewellery', 2, '14/428/', 0, 0, 1, NULL, NULL, ''),
  (429, 428, 'Men''s Rings', 'jewelry-watches-men-s-jewellery-men-s-rings', 3, '14/428/429/', 0, 0, 1, NULL, NULL, ''),
  (430, 428, 'Men''s Bracelets', 'jewelry-watches-men-s-jewellery-men-s-bracelets', 3, '14/428/430/', 0, 0, 1, NULL, NULL, ''),
  (431, 396, 'Gold-Tikkas', 'jewelry-watches-gold-jewellery-gold-tikkas', 3, '14/396/431/', 0, 0, 1, NULL, NULL, ''),
  (432, 396, 'Gold-Brooches', 'jewelry-watches-gold-jewellery-gold-brooches', 3, '14/396/432/', 0, 0, 1, NULL, NULL, ''),
  (433, 428, 'Diamond Watch', 'jewelry-watches-men-s-jewellery-diamond-watch', 3, '14/428/433/', 0, 0, 1, NULL, NULL, ''),
  (434, 428, 'Pearl Watches', 'jewelry-watches-men-s-jewellery-pearl-watches', 3, '14/428/434/', 0, 0, 1, NULL, NULL, ''),
  (435, 428, 'Cufflinks', 'jewelry-watches-men-s-jewellery-cufflinks', 3, '14/428/435/', 0, 0, 1, NULL, NULL, ''),
  (436, 428, 'Tiepin', 'jewelry-watches-men-s-jewellery-tiepin', 3, '14/428/436/', 0, 0, 1, NULL, NULL, ''),
  (437, 428, 'Solitaire Rings', 'jewelry-watches-men-s-jewellery-solitaire-rings', 3, '14/428/437/', 0, 0, 1, NULL, NULL, ''),
  (438, 428, 'Special White Gold', 'jewelry-watches-men-s-jewellery-special-white-gold', 3, '14/428/438/', 0, 0, 1, NULL, NULL, ''),
  (439, 14, 'Baby Jewellery', 'jewelry-watches-baby-jewellery', 2, '14/439/', 0, 0, 1, NULL, NULL, ''),
  (440, 439, 'Baby Bangles', 'jewelry-watches-baby-jewellery-baby-bangles', 3, '14/439/440/', 0, 0, 1, NULL, NULL, ''),
  (441, 396, 'Gold Vaddanams', 'jewelry-watches-gold-jewellery-gold-vaddanams', 3, '14/396/441/', 0, 0, 1, NULL, NULL, ''),
  (442, 16, 'Men''s', 'jewelry-watches-watch-sunglass-men-s', 3, '14/16/442/', 0, 0, 1, NULL, NULL, ''),
  (443, 16, 'Women''s Watches', 'jewelry-watches-watch-sunglass-women-s-watches', 3, '14/16/443/', 0, 0, 1, NULL, NULL, ''),
  (444, 16, 'Kid''s Watches', 'jewelry-watches-watch-sunglass-kid-s-watches', 3, '14/16/444/', 0, 0, 1, NULL, NULL, ''),
  (445, 16, 'Pair Watches', 'jewelry-watches-watch-sunglass-pair-watches', 3, '14/16/445/', 0, 0, 1, NULL, NULL, ''),
  (446, 14, 'Sunglass', 'jewelry-watches-sunglass', 2, '14/446/', 0, 0, 1, NULL, NULL, ''),
  (447, 446, 'Mne''s Sunglass', 'jewelry-watches-sunglass-mne-s-sunglass', 3, '14/446/447/', 0, 0, 1, NULL, NULL, ''),
  (448, 446, 'Women''s Sunglass', 'jewelry-watches-sunglass-women-s-sunglass', 3, '14/446/448/', 0, 0, 1, NULL, NULL, ''),
  (449, 446, 'Kids Sunglass', 'jewelry-watches-sunglass-kids-sunglass', 3, '14/446/449/', 0, 0, 1, NULL, NULL, ''),
  (450, 446, 'Contact Lence', 'jewelry-watches-sunglass-contact-lence', 3, '14/446/450/', 0, 0, 1, NULL, NULL, ''),
  (451, 446, 'Accessories', 'jewelry-watches-sunglass-accessories', 3, '14/446/451/', 0, 0, 1, NULL, NULL, ''),
  (452, 446, 'Optical Lens', 'jewelry-watches-sunglass-optical-lens', 3, '14/446/452/', 0, 0, 1, NULL, NULL, ''),
  (453, 446, 'Intraocular Lenses', 'jewelry-watches-sunglass-intraocular-lenses', 3, '14/446/453/', 0, 0, 1, NULL, NULL, ''),
  (454, 46, 'Bathroom Furniture', 'furnitures-wood-furniture-bathroom-furniture', 3, '45/46/454/', 0, 0, 1, NULL, NULL, ''),
  (455, 46, 'Bedroom Furniture', 'furnitures-wood-furniture-bedroom-furniture', 3, '45/46/455/', 0, 0, 1, NULL, NULL, ''),
  (456, 46, 'Dining Room Furniture', 'furnitures-wood-furniture-dining-room-furniture', 3, '45/46/456/', 0, 0, 1, NULL, NULL, ''),
  (457, 46, 'Home Office Furniture', 'furnitures-wood-furniture-home-office-furniture', 3, '45/46/457/', 0, 0, 1, NULL, NULL, ''),
  (458, 46, 'Kids Bedroom Furniture', 'furnitures-wood-furniture-kids-bedroom-furniture', 3, '45/46/458/', 0, 0, 1, NULL, NULL, ''),
  (459, 46, 'Kitchen Furniture', 'furnitures-wood-furniture-kitchen-furniture', 3, '45/46/459/', 0, 0, 1, NULL, NULL, ''),
  (460, 46, 'Living Room Furniture', 'furnitures-wood-furniture-living-room-furniture', 3, '45/46/460/', 0, 0, 1, NULL, NULL, ''),
  (461, 455, 'Beds', 'furnitures-wood-furniture-bedroom-furniture-beds', 4, '45/46/455/461/', 0, 0, 1, NULL, NULL, ''),
  (462, 45, 'Contemporary Furniture', 'furnitures-contemporary-furniture', 2, '45/462/', 0, 0, 1, NULL, NULL, ''),
  (463, 462, 'Side and dining chairs', 'furnitures-contemporary-furniture-side-and-dining-chairs', 3, '45/462/463/', 0, 0, 1, NULL, NULL, '');
INSERT INTO `categories` (`id`, `parent`, `name`, `slug`, `level`, `path`, `sorting`, `feature`, `status`, `imagePath`, `inventoryConfig_id`, `permission`) VALUES
  (464, 462, 'Armchairs&lounge chairs', 'furnitures-contemporary-furniture-armchairs-lounge-chairs', 3, '45/462/464/', 0, 0, 1, NULL, NULL, ''),
  (465, 462, 'bar&counter stools', 'furnitures-contemporary-furniture-bar-counter-stools', 3, '45/462/465/', 0, 0, 1, NULL, NULL, ''),
  (466, 462, 'low stools', 'furnitures-contemporary-furniture-low-stools', 3, '45/462/466/', 0, 0, 1, NULL, NULL, ''),
  (467, 462, 'sofas', 'furnitures-contemporary-furniture-sofas', 3, '45/462/467/', 0, 0, 1, NULL, NULL, ''),
  (468, 462, 'benches', 'furnitures-contemporary-furniture-benches', 3, '45/462/468/', 0, 0, 1, NULL, NULL, ''),
  (469, 462, 'stacking chairs', 'furnitures-contemporary-furniture-stacking-chairs', 3, '45/462/469/', 0, 0, 1, NULL, NULL, ''),
  (470, 462, 'task&office chairs', 'furnitures-contemporary-furniture-task-office-chairs', 3, '45/462/470/', 0, 0, 1, NULL, NULL, ''),
  (471, 462, 'outdoor&patio furniture', 'furnitures-contemporary-furniture-outdoor-patio-furniture', 3, '45/462/471/', 0, 0, 1, NULL, NULL, ''),
  (472, 462, 'dining tables', 'furnitures-contemporary-furniture-dining-tables', 3, '45/462/472/', 0, 0, 1, NULL, NULL, ''),
  (473, 462, 'coffee&cocktail tables', 'furnitures-contemporary-furniture-coffee-cocktail-tables', 3, '45/462/473/', 0, 0, 1, NULL, NULL, ''),
  (474, 462, 'side&end tables', 'furnitures-contemporary-furniture-side-end-tables', 3, '45/462/474/', 0, 0, 1, NULL, NULL, ''),
  (475, 462, 'work&office tables', 'furnitures-contemporary-furniture-work-office-tables', 3, '45/462/475/', 0, 0, 1, NULL, NULL, ''),
  (476, 462, 'wall mounted shelving', 'furnitures-contemporary-furniture-wall-mounted-shelving', 3, '45/462/476/', 0, 0, 1, NULL, NULL, ''),
  (477, 462, 'free standing shelving', 'furnitures-contemporary-furniture-free-standing-shelving', 3, '45/462/477/', 0, 0, 1, NULL, NULL, ''),
  (478, 462, 'storage', 'furnitures-contemporary-furniture-storage', 3, '45/462/478/', 0, 0, 1, NULL, NULL, ''),
  (479, 462, 'beds', 'furnitures-contemporary-furniture-beds', 3, '45/462/479/', 0, 0, 1, NULL, NULL, ''),
  (480, 462, 'children''s furniture', 'furnitures-contemporary-furniture-children-s-furniture', 3, '45/462/480/', 0, 0, 1, NULL, NULL, ''),
  (481, 46, 'Office Furniture', 'furnitures-wood-furniture-office-furniture', 3, '45/46/481/', 0, 0, 1, NULL, NULL, ''),
  (482, 46, 'Outdoor Furniture', 'furnitures-wood-furniture-outdoor-furniture', 3, '45/46/482/', 0, 0, 1, NULL, NULL, ''),
  (483, 455, 'Bedside Cabinets', 'furnitures-wood-furniture-bedroom-furniture-bedside-cabinets', 4, '45/46/455/483/', 0, 0, 1, NULL, NULL, ''),
  (484, 455, 'Blanket Boxes', 'furnitures-wood-furniture-bedroom-furniture-blanket-boxes', 4, '45/46/455/484/', 0, 0, 1, NULL, NULL, ''),
  (485, 455, 'Chest of Drawers', 'furnitures-wood-furniture-bedroom-furniture-chest-of-drawers', 4, '45/46/455/485/', 0, 0, 1, NULL, NULL, ''),
  (486, 455, 'Dressing Tables', 'furnitures-wood-furniture-bedroom-furniture-dressing-tables', 4, '45/46/455/486/', 0, 0, 1, NULL, NULL, ''),
  (487, 455, 'Mattresses', 'furnitures-wood-furniture-bedroom-furniture-mattresses', 4, '45/46/455/487/', 0, 0, 1, NULL, NULL, ''),
  (488, 455, 'Mirrors', 'furnitures-wood-furniture-bedroom-furniture-mirrors', 4, '45/46/455/488/', 0, 0, 1, NULL, NULL, ''),
  (489, 455, 'Wardrobes', 'furnitures-wood-furniture-bedroom-furniture-wardrobes', 4, '45/46/455/489/', 0, 0, 1, NULL, NULL, ''),
  (490, 456, 'Bookcases & Cabinets', 'furnitures-wood-furniture-dining-room-furniture-bookcases-cabinets', 4, '45/46/456/490/', 0, 0, 1, NULL, NULL, ''),
  (491, 456, 'Console Tables & Hall Tables', 'furnitures-wood-furniture-dining-room-furniture-console-tables-hall-tables', 4, '45/46/456/491/', 0, 0, 1, NULL, NULL, ''),
  (492, 456, 'Dining Chairs', 'furnitures-wood-furniture-dining-room-furniture-dining-chairs', 4, '45/46/456/492/', 0, 0, 1, NULL, NULL, ''),
  (493, 456, 'Dining Tables', 'furnitures-wood-furniture-dining-room-furniture-dining-tables', 4, '45/46/456/493/', 0, 0, 1, NULL, NULL, ''),
  (494, 456, 'Dining Tables & Chair Sets', 'furnitures-wood-furniture-dining-room-furniture-dining-tables-chair-sets', 4, '45/46/456/494/', 0, 0, 1, NULL, NULL, ''),
  (495, 456, 'Dressers', 'furnitures-wood-furniture-dining-room-furniture-dressers', 4, '45/46/456/495/', 0, 0, 1, NULL, NULL, ''),
  (496, 456, 'Sideboards', 'furnitures-wood-furniture-dining-room-furniture-sideboards', 4, '45/46/456/496/', 0, 0, 1, NULL, NULL, ''),
  (497, 456, 'Benches', 'furnitures-wood-furniture-dining-room-furniture-benches', 4, '45/46/456/497/', 0, 0, 1, NULL, NULL, ''),
  (498, 456, 'Stools', 'furnitures-wood-furniture-dining-room-furniture-stools', 4, '45/46/456/498/', 0, 0, 1, NULL, NULL, ''),
  (499, 456, 'Display Cabinets', 'furnitures-wood-furniture-dining-room-furniture-display-cabinets', 4, '45/46/456/499/', 0, 0, 1, NULL, NULL, ''),
  (500, 456, 'Mirrors', 'furnitures-wood-furniture-dining-room-furniture-mirrors', 4, '45/46/456/500/', 0, 0, 1, NULL, NULL, ''),
  (501, 456, 'Shelving Units', 'furnitures-wood-furniture-dining-room-furniture-shelving-units', 4, '45/46/456/501/', 0, 0, 1, NULL, NULL, ''),
  (502, 456, 'Shelves', 'furnitures-wood-furniture-dining-room-furniture-shelves', 4, '45/46/456/502/', 0, 0, 1, NULL, NULL, ''),
  (503, 456, 'Dining Furniture Sets', 'furnitures-wood-furniture-dining-room-furniture-dining-furniture-sets', 4, '45/46/456/503/', 0, 0, 1, NULL, NULL, ''),
  (504, 460, 'Small Tables', 'furnitures-wood-furniture-living-room-furniture-small-tables', 4, '45/46/460/504/', 0, 0, 1, NULL, NULL, ''),
  (505, 460, 'Cabinets', 'furnitures-wood-furniture-living-room-furniture-cabinets', 4, '45/46/460/505/', 0, 0, 1, NULL, NULL, ''),
  (506, 460, 'Sideboards', 'furnitures-wood-furniture-living-room-furniture-sideboards', 4, '45/46/460/506/', 0, 0, 1, NULL, NULL, ''),
  (507, 460, 'Display Cabinets', 'furnitures-wood-furniture-living-room-furniture-display-cabinets', 4, '45/46/460/507/', 0, 0, 1, NULL, NULL, ''),
  (508, 460, 'Drink Cabinets', 'furnitures-wood-furniture-living-room-furniture-drink-cabinets', 4, '45/46/460/508/', 0, 0, 1, NULL, NULL, ''),
  (509, 460, 'Bookcases', 'furnitures-wood-furniture-living-room-furniture-bookcases', 4, '45/46/460/509/', 0, 0, 1, NULL, NULL, ''),
  (510, 460, 'CD & DVD Unit Storage', 'furnitures-wood-furniture-living-room-furniture-cd-dvd-unit-storage', 4, '45/46/460/510/', 0, 0, 1, NULL, NULL, ''),
  (511, 460, 'Television Stands', 'furnitures-wood-furniture-living-room-furniture-television-stands', 4, '45/46/460/511/', 0, 0, 1, NULL, NULL, ''),
  (512, 460, 'Hallway Furniture', 'furnitures-wood-furniture-living-room-furniture-hallway-furniture', 4, '45/46/460/512/', 0, 0, 1, NULL, NULL, ''),
  (513, 460, 'Shelving Units', 'furnitures-wood-furniture-living-room-furniture-shelving-units', 4, '45/46/460/513/', 0, 0, 1, NULL, NULL, ''),
  (514, 460, 'Shelves', 'furnitures-wood-furniture-living-room-furniture-shelves', 4, '45/46/460/514/', 0, 0, 1, NULL, NULL, ''),
  (515, 460, 'Storage Trunks', 'furnitures-wood-furniture-living-room-furniture-storage-trunks', 4, '45/46/460/515/', 0, 0, 1, NULL, NULL, ''),
  (516, 460, 'Mirrors', 'furnitures-wood-furniture-living-room-furniture-mirrors', 4, '45/46/460/516/', 0, 0, 1, NULL, NULL, ''),
  (517, 460, 'Footstools', 'furnitures-wood-furniture-living-room-furniture-footstools', 4, '45/46/460/517/', 0, 0, 1, NULL, NULL, ''),
  (518, 460, 'Sofa', 'furnitures-wood-furniture-living-room-furniture-sofa', 4, '45/46/460/518/', 0, 0, 1, NULL, NULL, ''),
  (519, 460, 'Armchairs', 'furnitures-wood-furniture-living-room-furniture-armchairs', 4, '45/46/460/519/', 0, 0, 1, NULL, NULL, ''),
  (520, 460, 'Snugglers', 'furnitures-wood-furniture-living-room-furniture-snugglers', 4, '45/46/460/520/', 0, 0, 1, NULL, NULL, ''),
  (521, 460, 'Chair''s', 'furnitures-wood-furniture-living-room-furniture-chair-s', 4, '45/46/460/521/', 0, 0, 1, NULL, NULL, ''),
  (522, 460, 'Chaise Longues', 'furnitures-wood-furniture-living-room-furniture-chaise-longues', 4, '45/46/460/522/', 0, 0, 1, NULL, NULL, ''),
  (523, 459, 'Kitchen Trolleys', 'furnitures-wood-furniture-kitchen-furniture-kitchen-trolleys', 4, '45/46/459/523/', 0, 0, 1, NULL, NULL, ''),
  (524, 459, 'Kitchen Racks & Stands', 'furnitures-wood-furniture-kitchen-furniture-kitchen-racks-stands', 4, '45/46/459/524/', 0, 0, 1, NULL, NULL, ''),
  (525, 459, 'Tabels', 'furnitures-wood-furniture-kitchen-furniture-tabels', 4, '45/46/459/525/', 0, 0, 1, NULL, NULL, ''),
  (526, 459, 'Counter Stools', 'furnitures-wood-furniture-kitchen-furniture-counter-stools', 4, '45/46/459/526/', 0, 0, 1, NULL, NULL, ''),
  (527, 458, 'Childrens Wardrobes', 'furnitures-wood-furniture-kids-bedroom-furniture-childrens-wardrobes', 4, '45/46/458/527/', 0, 0, 1, NULL, NULL, ''),
  (528, 458, 'Kids Beds', 'furnitures-wood-furniture-kids-bedroom-furniture-kids-beds', 4, '45/46/458/528/', 0, 0, 1, NULL, NULL, ''),
  (529, 458, 'Toy/Storage Boxes', 'furnitures-wood-furniture-kids-bedroom-furniture-toy-storage-boxes', 4, '45/46/458/529/', 0, 0, 1, NULL, NULL, ''),
  (530, 458, 'Chairs', 'furnitures-wood-furniture-kids-bedroom-furniture-chairs', 4, '45/46/458/530/', 0, 0, 1, NULL, NULL, ''),
  (531, 458, 'Desks', 'furnitures-wood-furniture-kids-bedroom-furniture-desks', 4, '45/46/458/531/', 0, 0, 1, NULL, NULL, ''),
  (532, 458, 'Kids Dressers', 'furnitures-wood-furniture-kids-bedroom-furniture-kids-dressers', 4, '45/46/458/532/', 0, 0, 1, NULL, NULL, ''),
  (533, 458, 'Kids Storage', 'furnitures-wood-furniture-kids-bedroom-furniture-kids-storage', 4, '45/46/458/533/', 0, 0, 1, NULL, NULL, ''),
  (534, 458, 'Rocking Chairs', 'furnitures-wood-furniture-kids-bedroom-furniture-rocking-chairs', 4, '45/46/458/534/', 0, 0, 1, NULL, NULL, ''),
  (535, 458, 'Bunk Beds', 'furnitures-wood-furniture-kids-bedroom-furniture-bunk-beds', 4, '45/46/458/535/', 0, 0, 1, NULL, NULL, ''),
  (536, 481, 'Computer Workstations', 'furnitures-wood-furniture-office-furniture-computer-workstations', 4, '45/46/481/536/', 0, 0, 1, NULL, NULL, ''),
  (537, 481, 'Conference Tables', 'furnitures-wood-furniture-office-furniture-conference-tables', 4, '45/46/481/537/', 0, 0, 1, NULL, NULL, ''),
  (538, 481, 'Ergonomic Chairs', 'furnitures-wood-furniture-office-furniture-ergonomic-chairs', 4, '45/46/481/538/', 0, 0, 1, NULL, NULL, ''),
  (539, 481, 'Executive Desks', 'furnitures-wood-furniture-office-furniture-executive-desks', 4, '45/46/481/539/', 0, 0, 1, NULL, NULL, ''),
  (540, 481, 'Home Office Furniture', 'furnitures-wood-furniture-office-furniture-home-office-furniture', 4, '45/46/481/540/', 0, 0, 1, NULL, NULL, ''),
  (541, 481, 'Office Chairs', 'furnitures-wood-furniture-office-furniture-office-chairs', 4, '45/46/481/541/', 0, 0, 1, NULL, NULL, ''),
  (542, 481, 'Office Desks', 'furnitures-wood-furniture-office-furniture-office-desks', 4, '45/46/481/542/', 0, 0, 1, NULL, NULL, ''),
  (543, 481, 'Office Reception Seating', 'furnitures-wood-furniture-office-furniture-office-reception-seating', 4, '45/46/481/543/', 0, 0, 1, NULL, NULL, ''),
  (544, 481, 'Stacking Chairs', 'furnitures-wood-furniture-office-furniture-stacking-chairs', 4, '45/46/481/544/', 0, 0, 1, NULL, NULL, ''),
  (545, 481, 'Storage File Cabinets', 'furnitures-wood-furniture-office-furniture-storage-file-cabinets-1', 4, '45/46/481/545/', 0, 0, 1, NULL, NULL, ''),
  (546, 481, 'Storage File Cabinets', 'furnitures-wood-furniture-office-furniture-storage-file-cabinets', 4, '45/46/481/546/', 0, 0, 1, NULL, NULL, ''),
  (547, 481, 'Task Chairs', 'furnitures-wood-furniture-office-furniture-task-chairs', 4, '45/46/481/547/', 0, 0, 1, NULL, NULL, ''),
  (548, 46, 'Hallway', 'furnitures-wood-furniture-hallway', 3, '45/46/548/', 0, 0, 1, NULL, NULL, ''),
  (549, 26, 'Fruits & Vegetables', 'foods-chinuse-fruits-vegetables', 3, '24/26/549/', 0, 0, 1, NULL, NULL, ''),
  (550, 26, 'Meat', 'foods-chinuse-meat', 3, '24/26/550/', 0, 0, 1, NULL, NULL, ''),
  (551, 26, 'Sea Food', 'foods-chinuse-sea-food', 3, '24/26/551/', 0, 0, 1, NULL, NULL, ''),
  (552, 26, 'Entrees', 'foods-chinuse-entrees', 3, '24/26/552/', 0, 0, 1, NULL, NULL, ''),
  (553, 25, 'Pizza & Pasta', 'foods-fast-food-pizza-pasta', 3, '24/25/553/', 0, 0, 1, NULL, NULL, ''),
  (554, 26, 'Side Dishes', 'foods-chinuse-side-dishes', 3, '24/26/554/', 0, 0, 1, NULL, NULL, ''),
  (555, 25, 'Snacks', 'foods-fast-food-snacks', 3, '24/25/555/', 0, 0, 1, NULL, NULL, ''),
  (556, 26, 'Desserts', 'foods-chinuse-desserts', 3, '24/26/556/', 0, 0, 1, NULL, NULL, ''),
  (557, 24, 'Pizza & Pasta', 'foods-pizza-pasta', 2, '24/557/', 0, 0, 1, NULL, NULL, ''),
  (561, 557, 'Appetisers', 'foods-pizza-pasta-appetisers', 3, '24/557/561/', 0, 0, 1, NULL, NULL, ''),
  (562, 557, 'Salad', 'foods-pizza-pasta-salad', 3, '24/557/562/', 0, 0, 1, NULL, NULL, ''),
  (563, 557, 'Pasta', 'foods-pizza-pasta-pasta', 3, '24/557/563/', 0, 0, 1, NULL, NULL, ''),
  (564, 557, 'Toppings', 'foods-pizza-pasta-toppings', 3, '24/557/564/', 0, 0, 1, NULL, NULL, ''),
  (565, 25, 'Burger', 'foods-fast-food-burger', 3, '24/25/565/', 0, 0, 1, NULL, NULL, ''),
  (566, 25, 'Bevarage', 'foods-fast-food-bevarage', 3, '24/25/566/', 0, 0, 1, NULL, NULL, ''),
  (567, 25, 'Buns Item', 'foods-fast-food-buns-item', 3, '24/25/567/', 0, 0, 1, NULL, NULL, ''),
  (568, 25, 'Bread Item', 'foods-fast-food-bread-item', 3, '24/25/568/', 0, 0, 1, NULL, NULL, ''),
  (569, 25, 'Pizza Item', 'foods-fast-food-pizza-item', 3, '24/25/569/', 0, 0, 1, NULL, NULL, ''),
  (570, 25, 'Roll Item', 'foods-fast-food-roll-item', 3, '24/25/570/', 0, 0, 1, NULL, NULL, ''),
  (571, 25, 'Puff Item', 'foods-fast-food-puff-item', 3, '24/25/571/', 0, 0, 1, NULL, NULL, ''),
  (572, 25, 'Danish Item', 'foods-fast-food-danish-item', 3, '24/25/572/', 0, 0, 1, NULL, NULL, ''),
  (573, 25, 'Dount Item', 'foods-fast-food-dount-item', 3, '24/25/573/', 0, 0, 1, NULL, NULL, ''),
  (574, 25, 'Sandwich Item', 'foods-fast-food-sandwich-item', 3, '24/25/574/', 0, 0, 1, NULL, NULL, ''),
  (575, 25, 'Pie Item', 'foods-fast-food-pie-item', 3, '24/25/575/', 0, 0, 1, NULL, NULL, ''),
  (576, 25, 'Toast Item', 'foods-fast-food-toast-item', 3, '24/25/576/', 0, 0, 1, NULL, NULL, ''),
  (577, 25, 'Cake Item', 'foods-fast-food-cake-item', 3, '24/25/577/', 0, 0, 1, NULL, NULL, ''),
  (578, 25, 'Patisseries Item', 'foods-fast-food-patisseries-item', 3, '24/25/578/', 0, 0, 1, NULL, NULL, ''),
  (579, 25, 'Cookies Item', 'foods-fast-food-cookies-item', 3, '24/25/579/', 0, 0, 1, NULL, NULL, ''),
  (580, 25, 'Fried Chicken', 'foods-fast-food-fried-chicken', 3, '24/25/580/', 0, 0, 1, NULL, NULL, ''),
  (581, 557, 'Chicken Curry', 'foods-pizza-pasta-chicken-curry', 3, '24/557/581/', 0, 0, 1, NULL, NULL, ''),
  (582, 557, 'Fried Chicken', 'foods-pizza-pasta-fried-chicken', 3, '24/557/582/', 0, 0, 1, NULL, NULL, ''),
  (605, NULL, 'Mobile Phone', 'mobile-phone', 1, '605/', 15, 0, 1, NULL, NULL, ''),
  (606, 605, 'Apple', 'mobile-phone-apple', 2, '605/606/', 0, 0, 1, NULL, NULL, ''),
  (607, 605, 'Nokia', 'mobile-phone-nokia', 2, '605/607/', 0, 0, 1, NULL, NULL, ''),
  (608, 605, 'Sumsung', 'mobile-phone-sumsung', 2, '605/608/', 0, 0, 1, NULL, NULL, ''),
  (609, 605, 'Symphoney', 'mobile-phone-symphoney', 2, '605/609/', 0, 0, 1, NULL, NULL, ''),
  (610, 605, 'Sony', 'mobile-phone-sony', 2, '605/610/', 0, 0, 1, NULL, NULL, ''),
  (611, NULL, 'Automibiles', 'automibiles', 1, '611/', 2, 1, 1, '10723377_3579755_1000.jpg', NULL, ''),
  (612, 611, 'Car', 'automibiles-car', 2, '611/612/', 0, 0, 1, NULL, NULL, ''),
  (613, 611, 'Motorcycle', 'automibiles-motorcycle', 2, '611/613/', 0, 0, 1, NULL, NULL, ''),
  (614, 611, 'Bycycle', 'automibiles-bycycle', 2, '611/614/', 0, 0, 1, NULL, NULL, ''),
  (615, 611, 'Bus', 'automibiles-bus', 2, '611/615/', 0, 0, 1, NULL, NULL, ''),
  (616, 611, 'Truck', 'automibiles-truck', 2, '611/616/', 0, 0, 1, NULL, NULL, ''),
  (617, 611, 'Pickup', 'automibiles-pickup', 2, '611/617/', 0, 0, 1, NULL, NULL, ''),
  (618, 613, 'Bajaj', 'automibiles-motorcycle-bajaj', 3, '611/613/618/', 0, 0, 1, NULL, NULL, ''),
  (619, 613, 'Hero Honda', 'automibiles-motorcycle-hero-honda', 3, '611/613/619/', 0, 0, 1, NULL, NULL, ''),
  (620, 613, 'Yamaha', 'automibiles-motorcycle-yamaha', 3, '611/613/620/', 0, 0, 1, NULL, NULL, ''),
  (621, 613, 'Honda', 'automibiles-motorcycle-honda', 3, '611/613/621/', 0, 0, 1, NULL, NULL, ''),
  (622, 613, 'TVS', 'automibiles-motorcycle-tvs', 3, '611/613/622/', 0, 0, 1, NULL, NULL, ''),
  (623, 613, 'Walton', 'automibiles-motorcycle-walton', 3, '611/613/623/', 0, 0, 1, NULL, NULL, ''),
  (624, 613, 'Runner', 'automibiles-motorcycle-runner', 3, '611/613/624/', 0, 0, 1, NULL, NULL, ''),
  (625, 88, 'Air Craft & Helicopter', 'reservations-air-craft-helicopter', 2, '88/625/', 0, 0, 1, NULL, NULL, ''),
  (626, 2, 'Swater', 'wears-men-s-wears-swater', 3, '1/2/626/', 0, 0, 1, NULL, NULL, ''),
  (627, 3, 'Lehanga', 'wears-women-s-wears-lehanga', 3, '1/3/627/', 0, 0, 1, 'Screenshot from 2015-12-02 15:17:01.png', 2, 'private'),
  (628, 2, 'Men''s Shirt', 'wears-men-s-wears-men-s-shirt', 3, '1/2/628/', 0, 0, 1, NULL, NULL, 'public'),
  (629, 2, 'Men''s Full Tshirt', 'wears-men-s-wears-men-s-full-tshirt', 3, '1/2/629/', 0, 0, 1, NULL, NULL, 'public'),
  (631, 2, 'Men''s Panjabi', 'wears-men-s-wears-men-s-panjabi', 3, '1/2/631/', 0, 0, 1, NULL, NULL, 'public'),
  (632, 2, 'Mens Casual Shirt', 'wears-men-s-wears-mens-casual-shirt', 3, '1/2/632/', 0, 0, 1, NULL, NULL, 'public'),
  (633, 2, 'Gents Polo Tshirt', 'wears-men-s-wears-gents-polo-tshirt', 3, '1/2/633/', 0, 0, 1, NULL, NULL, 'public'),
  (634, 389, 'Gents Body spray', 'beauty-fragrances-men-s-fragrance-gents-body-spray', 3, '18/389/634/', 0, 0, 1, NULL, NULL, 'public'),
  (635, 13, 'Gents Belt', 'leather-bags-belts-gents-belt', 3, '7/13/635/', 0, 0, 1, NULL, NULL, 'public'),
  (636, 12, 'Gents Wallet', 'leather-bags-leather-goods-gents-wallet', 3, '7/12/636/', 0, 0, 1, NULL, NULL, 'public'),
  (637, NULL, 'Men''s Fashion', 'men-s-fashion', 1, '637/', 0, 0, 1, NULL, NULL, 'public'),
  (638, 387, 'Gents Tai', 'beauty-fragrances-men-s-bodycare-gents-tai', 3, '18/387/638/', 0, 0, 1, NULL, NULL, 'public'),
  (639, 387, 'Gents Tai Pin', 'beauty-fragrances-men-s-bodycare-gents-tai-pin', 3, '18/387/639/', 0, 0, 1, NULL, NULL, 'public'),
  (640, 387, 'Gents Culflen', 'beauty-fragrances-men-s-bodycare-gents-culflen', 3, '18/387/640/', 0, 0, 1, NULL, NULL, 'public'),
  (641, 2, 'Gents Tshirt', 'wears-men-s-wears-gents-tshirt', 3, '1/2/641/', 0, 0, 1, NULL, NULL, 'public'),
  (642, 2, 'Gents Short Pant', 'wears-men-s-wears-gents-short-pant', 3, '1/2/642/', 0, 0, 1, NULL, NULL, 'public'),
  (643, 2, 'Gents Blezar', 'wears-men-s-wears-gents-blezar', 3, '1/2/643/', 0, 0, 1, NULL, NULL, 'public'),
  (644, 2, 'Gents Coti', 'wears-men-s-wears-gents-coti', 3, '1/2/644/', 0, 0, 1, NULL, NULL, 'public'),
  (645, 2, 'Gents Half Polo Tshirt', 'wears-men-s-wears-gents-half-polo-tshirt', 3, '1/2/645/', 0, 0, 1, NULL, NULL, 'public'),
  (646, 2, 'Gents Jeans Pant', 'wears-men-s-wears-gents-jeans-pant', 3, '1/2/646/', 0, 0, 1, NULL, NULL, 'public'),
  (647, 2, 'Gents Gavadin Pant', 'wears-men-s-wears-gents-gavadin-pant', 3, '1/2/647/', 0, 0, 1, NULL, NULL, 'public'),
  (648, 2, 'Gents Formal Pant', 'wears-men-s-wears-gents-formal-pant', 3, '1/2/648/', 0, 0, 1, NULL, NULL, 'public'),
  (649, 2, 'Gents Under Wear', 'wears-men-s-wears-gents-under-wear', 3, '1/2/649/', 0, 0, 1, NULL, NULL, 'public'),
  (650, 2, 'Gents Sando Genji', 'wears-men-s-wears-gents-sando-genji', 3, '1/2/650/', 0, 0, 1, NULL, NULL, 'public'),
  (651, 3, 'Three Pics', 'wears-women-s-wears-three-pics', 3, '1/3/651/', 0, 0, 1, NULL, NULL, 'public'),
  (652, 3, 'Ladies Shirt', 'wears-women-s-wears-ladies-shirt', 3, '1/3/652/', 0, 0, 1, NULL, NULL, 'public'),
  (653, 3, 'Ladies Tshirt', 'wears-women-s-wears-ladies-tshirt', 3, '1/3/653/', 0, 0, 1, NULL, NULL, 'public'),
  (654, 3, 'Ladies Tops', 'wears-women-s-wears-ladies-tops', 3, '1/3/654/', 0, 0, 1, NULL, NULL, 'public'),
  (655, 3, 'Gents Gavadin Pants', 'wears-women-s-wears-gents-gavadin-pants', 3, '1/3/655/', 0, 0, 1, NULL, NULL, 'public'),
  (656, 2, 'Gents Jeans Pants', 'wears-men-s-wears-gents-jeans-pants', 3, '1/2/656/', 0, 0, 1, NULL, NULL, 'public'),
  (657, 3, 'Ladies Jeans Pants', 'wears-women-s-wears-ladies-jeans-pants', 3, '1/3/657/', 0, 0, 1, NULL, NULL, 'public'),
  (658, 3, 'Ladies Jagins', 'wears-women-s-wears-ladies-jagins', 3, '1/3/658/', 0, 0, 1, NULL, NULL, 'public'),
  (659, 3, 'Ladies Three Quater', 'wears-women-s-wears-ladies-three-quater', 3, '1/3/659/', 0, 0, 1, NULL, NULL, 'public'),
  (660, 3, 'Ladies Ties', 'wears-women-s-wears-ladies-ties', 3, '1/3/660/', 0, 0, 1, NULL, NULL, 'public'),
  (661, 4, 'Babies T-shirt', 'wears-baby-child-wear-s-babies-t-shirt', 3, '1/4/661/', 0, 0, 1, NULL, NULL, 'public'),
  (662, 2, 'Gents Panjabi', 'wears-men-s-wears-gents-panjabi', 3, '1/2/662/', 0, 0, 1, NULL, NULL, 'public'),
  (663, 3, 'Ladies Coti', 'wears-women-s-wears-ladies-coti', 3, '1/3/663/', 0, 0, 1, NULL, NULL, 'public'),
  (664, 2, 'Gents Huddy Half Slip', 'wears-men-s-wears-gents-huddy-half-slip', 3, '1/2/664/', 0, 0, 1, NULL, NULL, 'public'),
  (665, 2, 'Gents Huddy Full Slip', 'wears-men-s-wears-gents-huddy-full-slip', 3, '1/2/665/', 0, 0, 1, NULL, NULL, 'public'),
  (666, 2, 'Gents Sweater Full Slip', 'wears-men-s-wears-gents-sweater-full-slip', 3, '1/2/666/', 0, 0, 1, NULL, NULL, 'public'),
  (667, 2, 'Gents Sweater Half Slip', 'wears-men-s-wears-gents-sweater-half-slip', 3, '1/2/667/', 0, 0, 1, NULL, NULL, 'public'),
  (668, 2, 'Gents Jacket', 'wears-men-s-wears-gents-jacket', 3, '1/2/668/', 0, 0, 1, NULL, NULL, 'public');

-- --------------------------------------------------------

--
-- Table structure for table `CategoryGrouping`
--

CREATE TABLE IF NOT EXISTS `CategoryGrouping` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorygrouping_category`
--

CREATE TABLE IF NOT EXISTS `categorygrouping_category` (
  `categorygrouping_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ClassRoutine`
--

CREATE TABLE IF NOT EXISTS `ClassRoutine` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Collection`
--

CREATE TABLE IF NOT EXISTS `Collection` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `discountPercentage` decimal(10,0) DEFAULT NULL,
  `isFeature` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Collection`
--

INSERT INTO `Collection` (`id`, `parent`, `name`, `slug`, `content`, `discountPercentage`, `isFeature`, `status`, `created`, `path`) VALUES
  (1, NULL, 'Season', 'season-1', NULL, '10', 1, 1, '2015-03-06 23:09:12', 'large.jpg'),
  (2, 1, 'Summer Collection', 'summer-collection-1', 'Summer Collection<br>', '5', 1, 1, '2015-03-06 23:29:19', '0_4200_0_2800_one_La_Villette_Woodson_050.jpg'),
  (3, NULL, 'Festaval', 'festaval', 'Festaval<br>', NULL, 0, 1, '2015-03-06 23:34:04', '0_4200_0_2800_one_La_Villette_Woodson_008.jpg'),
  (4, 3, 'Eidul Fetar', 'eidul-fetar', 'Eidul Fetar<br>', NULL, 0, 1, '2015-03-06 23:35:31', '0_4200_0_2800_one_La_Villette_Woodson_035.jpg'),
  (5, NULL, 'Discount', 'discount', 'Discount<br><br>', NULL, 0, 1, '2015-03-13 20:51:36', NULL),
  (6, 5, 'Discount 10 %', 'discount-10', 'cdcdczx', '10', 0, 1, '2015-03-13 20:52:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ColorSize`
--

CREATE TABLE IF NOT EXISTS `ColorSize` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ColorSize`
--

INSERT INTO `ColorSize` (`id`, `name`, `type`, `slug`, `code`, `status`, `inventoryConfig_id`) VALUES
  (1, 'White', 'color', 'white', '01', 1, 2),
  (2, 'Blue', 'color', 'blue', '02', 1, 2),
  (3, 'Small', 'size', 'small', '01', 1, 2),
  (4, 'Medium', 'size', 'medium', '02', 1, 2),
  (5, 'Large', 'size', 'large', '03', 1, 2),
  (52, 'Purple', 'color', 'purple', '003', 1, 2),
  (53, 'Pink', 'color', 'pink', '004', 1, 2),
  (54, 'Silver', 'color', 'silver', '005', 1, 2),
  (55, 'Red', 'color', 'red', '006', 1, 2),
  (56, 'Mixed', 'color', 'mixed', '007', 1, 2),
  (57, 'Black', 'color', 'black', '008', 1, 2),
  (58, 'White', 'color', 'white-1', '009', 1, 2),
  (59, 'Blue', 'color', 'blue-1', '010', 1, 2),
  (60, 'Yellow', 'color', 'yellow', '011', 1, 2),
  (61, 'Green', 'color', 'green', '012', 1, 2),
  (62, 'Coffy', 'color', 'coffy', '013', 1, 2),
  (63, 'Sky Blue', 'color', 'sky-blue', '014', 1, 2),
  (64, 'Brown', 'color', 'brown', '015', 1, 2),
  (65, 'Orange', 'color', 'orange', '016', 1, 2),
  (66, 'Botton Green', 'color', 'botton-green', '017', 1, 2),
  (67, 'Golden', 'color', 'golden', '018', 1, 2),
  (68, '', 'color', '', '019', 1, 2),
  (69, 'Merun', 'color', 'merun', '020', 1, 2),
  (70, 'Ligtt Pink', 'color', 'ligtt-pink', '021', 1, 2),
  (71, 'Biskit', 'color', 'biskit', '022', 1, 2),
  (72, 'Light purple', 'color', 'light-purple', '023', 1, 2),
  (73, 'Ofwhite', 'color', 'ofwhite', '024', 1, 2),
  (74, 'Pest', 'color', 'pest', '025', 1, 2),
  (75, 'Olive', 'color', 'olive', '026', 1, 2),
  (76, 'Light Orange', 'color', 'light-orange', '027', 1, 2),
  (77, 'Misti', 'color', 'misti', '028', 1, 2),
  (78, 'Tiya', 'color', 'tiya', '029', 1, 2),
  (79, 'Malti', 'color', 'malti', '030', 1, 2),
  (80, '16.5', 'size', '16-5', '004', 1, 2),
  (81, '15.5', 'size', '15-5', '005', 1, 2),
  (82, '16', 'size', '16', '006', 1, 2),
  (83, '15', 'size', '15', '007', 1, 2),
  (84, 'M', 'size', 'm', '008', 1, 2),
  (85, 'E', 'size', 'e', '009', 1, 2),
  (86, 'O', 'size', 'o', '010', 1, 2),
  (87, 'XL', 'size', 'xl', '011', 1, 2),
  (88, 'L', 'size', 'l', '012', 1, 2),
  (89, 'F', 'size', 'f', '013', 1, 2),
  (90, 'S', 'size', 's', '014', 1, 2),
  (91, 'S|M|L|XL|XXL|XXXL', 'size', 's-m-l-xl-xxl-xxxl', '015', 1, 2),
  (92, '44', 'size', '44', '016', 1, 2),
  (93, '45', 'size', '45', '017', 1, 2),
  (94, '42', 'size', '42', '018', 1, 2),
  (95, '40', 'size', '40', '019', 1, 2),
  (96, 'XXL', 'size', 'xxl', '020', 1, 2),
  (97, '38', 'size', '38', '021', 1, 2),
  (98, '50', 'size', '50', '022', 1, 2),
  (99, '', 'size', '-1', '023', 1, 2),
  (100, '46', 'size', '46', '024', 1, 2),
  (101, '52', 'size', '52', '025', 1, 2),
  (102, '54', 'size', '54', '026', 1, 2),
  (103, '30', 'size', '30', '027', 1, 2),
  (104, '32', 'size', '32', '028', 1, 2),
  (105, '36', 'size', '36', '029', 1, 2),
  (106, '34', 'size', '34', '030', 1, 2),
  (107, '28', 'size', '28', '031', 1, 2),
  (108, '31', 'size', '31', '032', 1, 2),
  (109, '33', 'size', '33', '033', 1, 2),
  (110, '29', 'size', '29', '034', 1, 2),
  (111, '43', 'size', '43', '035', 1, 2),
  (112, '35', 'size', '35', '036', 1, 2),
  (113, '37', 'size', '37', '037', 1, 2),
  (114, 'XXXL', 'size', 'xxxl', '038', 1, 2),
  (115, '27', 'size', '27', '039', 1, 2),
  (116, '25', 'size', '25', '040', 1, 2),
  (117, '26', 'size', '26', '041', 1, 2),
  (118, 'Pics', 'unit', 'pics', '001', 1, 2),
  (119, 'Pair', 'unit', 'pair', '002', 1, 2),
  (120, 'Three pics', 'unit', 'three-pics', '003', 1, 2),
  (121, 'Two Pics', 'unit', 'two-pics', '004', 1, 2),
  (122, 'Set', 'unit', 'set', '005', 1, 2),
  (123, 'Pis', 'unit', 'pis', '006', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `id` int(11) NOT NULL,
  `thread_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `ancestors` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `depth` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `state` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ContactMessage`
--

CREATE TABLE IF NOT EXISTS `ContactMessage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `reply` longtext COLLATE utf8_unicode_ci,
  `archive` tinyint(1) NOT NULL,
  `created` datetime DEFAULT NULL,
  `replyDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ContactPage`
--

CREATE TABLE IF NOT EXISTS `ContactPage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `thana_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `address1` longtext COLLATE utf8_unicode_ci,
  `address2` longtext COLLATE utf8_unicode_ci,
  `additionalPhone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `additionalEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weeklyOffDay` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isContactForm` tinyint(1) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `askForSms` tinyint(1) DEFAULT NULL,
  `askForEmail` tinyint(1) DEFAULT NULL,
  `displyPhone` tinyint(1) DEFAULT NULL,
  `displayEmail` tinyint(1) DEFAULT NULL,
  `isBranch` tinyint(1) DEFAULT NULL,
  `isMap` tinyint(1) DEFAULT NULL,
  `isBaseInformation` tinyint(1) NOT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ContactPage`
--

INSERT INTO `ContactPage` (`id`, `user_id`, `thana_id`, `district_id`, `name`, `content`, `address1`, `address2`, `additionalPhone`, `additionalEmail`, `postalCode`, `fax`, `contactPerson`, `designation`, `startHour`, `endHour`, `weeklyOffDay`, `isContactForm`, `email`, `askForSms`, `askForEmail`, `displyPhone`, `displayEmail`, `isBranch`, `isMap`, `isBaseInformation`, `globalOption_id`) VALUES
  (3, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 0, NULL, 1, 8),
  (4, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 0, NULL, 1, 9),
  (5, 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 0, 0, 0, NULL, 1, 10),
  (6, 17, 202, 8, NULL, NULL, 'Opu Collection, 2nd Floor, Tokyo Square, Ring Road, Mohammadpur ', NULL, '013412421,123123,213123', 'opu@gmail.com', '1207', '123123', 'Opu', 'Propiter', '10:00 AM', '8:00 PM', 'Thursday', 0, NULL, 1, 1, 0, 0, 0, NULL, 1, 11);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(25) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `name`, `code`) VALUES
  (1, 'Afghanistan', 'AF'),
  (2, 'Aland Islands', 'AX'),
  (3, 'Albania', 'AL'),
  (4, 'Algeria', 'DZ'),
  (5, 'American Samoa', 'AS'),
  (6, 'Andorra', 'AD'),
  (7, 'Angola', 'AO'),
  (8, 'Anguilla', 'AI'),
  (9, 'Antarctica', 'AQ'),
  (10, 'Antigua and Barbuda', 'AG'),
  (11, 'Argentina', 'AR'),
  (12, 'Armenia', 'AM'),
  (13, 'Aruba', 'AW'),
  (14, 'Australia', 'AU'),
  (15, 'Austria', 'AT'),
  (16, 'Azerbaijan', 'AZ'),
  (17, 'Bahamas', 'BS'),
  (18, 'Bahrain', 'BH'),
  (19, 'Bangladesh', 'BD'),
  (20, 'Barbados', 'BB'),
  (21, 'Belarus', 'BY'),
  (22, 'Belgium', 'BE'),
  (23, 'Belize', 'BZ'),
  (24, 'Benin', 'BJ'),
  (25, 'Bermuda', 'BM'),
  (26, 'Bhutan', 'BT'),
  (27, 'Bolivia, Plurinational State of', 'BO'),
  (28, 'Bonaire, Sint Eustatius and Saba', 'BQ'),
  (29, 'Bosnia and Herzegovina', 'BA'),
  (30, 'Botswana', 'BW'),
  (31, 'Bouvet Island', 'BV'),
  (32, 'Brazil', 'BR'),
  (33, 'British Indian Ocean Territory', 'IO'),
  (34, 'Brunei Darussalam', 'BN'),
  (35, 'Bulgaria', 'BG'),
  (36, 'Burkina Faso', 'BF'),
  (37, 'Burundi', 'BI'),
  (38, 'Cambodia', 'KH'),
  (39, 'Cameroon', 'CM'),
  (40, 'Canada', 'CA'),
  (41, 'Cape Verde', 'CV'),
  (42, 'Cayman Islands', 'KY'),
  (43, 'Central African Republic', 'CF'),
  (44, 'Chad', 'TD'),
  (45, 'Chile', 'CL'),
  (46, 'China', 'CN'),
  (47, 'Christmas Island', 'CX'),
  (48, 'Cocos (Keeling) Islands', 'CC'),
  (49, 'Colombia', 'CO'),
  (50, 'Comoros', 'KM'),
  (51, 'Congo', 'CG'),
  (52, 'Congo, The Democratic Republic of the', 'CD'),
  (53, 'Cook Islands', 'CK'),
  (54, 'Costa Rica', 'CR'),
  (55, 'Cote d''Ivoire', 'CI'),
  (56, 'Croatia', 'HR'),
  (57, 'Cuba', 'CU'),
  (58, 'Curacao', 'CW'),
  (59, 'Cyprus', 'CY'),
  (60, 'Czech Republic', 'CZ'),
  (61, 'Denmark', 'DK'),
  (62, 'Djibouti', 'DJ'),
  (63, 'Dominica', 'DM'),
  (64, 'Dominican Republic', 'DO'),
  (65, 'Ecuador', 'EC'),
  (66, 'Egypt', 'EG'),
  (67, 'El Salvador', 'SV'),
  (68, 'Equatorial Guinea', 'GQ'),
  (69, 'Eritrea', 'ER'),
  (70, 'Estonia', 'EE'),
  (71, 'Ethiopia', 'ET'),
  (72, 'Falkland Islands (Malvinas)', 'FK'),
  (73, 'Faroe Islands', 'FO'),
  (74, 'Fiji', 'FJ'),
  (75, 'Finland', 'FI'),
  (76, 'France', 'FR'),
  (77, 'French Guiana', 'GF'),
  (78, 'French Polynesia', 'PF'),
  (79, 'French Southern Territories', 'TF'),
  (80, 'Gabon', 'GA'),
  (81, 'Gambia', 'GM'),
  (82, 'Georgia', 'GE'),
  (83, 'Germany', 'DE'),
  (84, 'Ghana', 'GH'),
  (85, 'Gibraltar', 'GI'),
  (86, 'Greece', 'GR'),
  (87, 'Greenland', 'GL'),
  (88, 'Grenada', 'GD'),
  (89, 'Guadeloupe', 'GP'),
  (90, 'Guam', 'GU'),
  (91, 'Guatemala', 'GT'),
  (92, 'Guernsey', 'GG'),
  (93, 'Guinea', 'GN'),
  (94, 'Guinea-Bissau', 'GW'),
  (95, 'Guyana', 'GY'),
  (96, 'Haiti', 'HT'),
  (97, 'Heard Island and McDonald Islands', 'HM'),
  (98, 'Holy See (Vatican City State)', 'VA'),
  (99, 'Honduras', 'HN'),
  (100, 'Hong Kong', 'HK'),
  (101, 'Hungary', 'HU'),
  (102, 'Iceland', 'IS'),
  (103, 'India', 'IN'),
  (104, 'Indonesia', 'ID'),
  (105, 'Iran, Islamic Republic of', 'IR'),
  (106, 'Iraq', 'IQ'),
  (107, 'Ireland', 'IE'),
  (108, 'Isle of Man', 'IM'),
  (109, 'Israel', 'IL'),
  (110, 'Italy', 'IT'),
  (111, 'Jamaica', 'JM'),
  (112, 'Japan', 'JP'),
  (113, 'Jersey', 'JE'),
  (114, 'Jordan', 'JO'),
  (115, 'Kazakhstan', 'KZ'),
  (116, 'Kenya', 'KE'),
  (117, 'Kiribati', 'KI'),
  (118, 'Korea, Democratic People''s Republic of', 'KP'),
  (119, 'Korea, Republic of', 'KR'),
  (120, 'Kuwait', 'KW'),
  (121, 'Kyrgyzstan', 'KG'),
  (122, 'Lao People''s Democratic Republic', 'LA'),
  (123, 'Latvia', 'LV'),
  (124, 'Lebanon', 'LB'),
  (125, 'Lesotho', 'LS'),
  (126, 'Liberia', 'LR'),
  (127, 'Libyan Arab Jamahiriya', 'LY'),
  (128, 'Liechtenstein', 'LI'),
  (129, 'Lithuania', 'LT'),
  (130, 'Luxembourg', 'LU'),
  (131, 'Macao', 'MO'),
  (132, 'Macedonia, The Former Yugoslav Republic of', 'MK'),
  (133, 'Madagascar', 'MG'),
  (134, 'Malawi', 'MW'),
  (135, 'Malaysia', 'MY'),
  (136, 'Maldives', 'MV'),
  (137, 'Mali', 'ML'),
  (138, 'Malta', 'MT'),
  (139, 'Marshall Islands', 'MH'),
  (140, 'Martinique', 'MQ'),
  (141, 'Mauritania', 'MR'),
  (142, 'Mauritius', 'MU'),
  (143, 'Mayotte', 'YT'),
  (144, 'Mexico', 'MX'),
  (145, 'Micronesia, Federated States of', 'FM'),
  (146, 'Moldova, Republic of', 'MD'),
  (147, 'Monaco', 'MC'),
  (148, 'Mongolia', 'MN'),
  (149, 'Montenegro', 'ME'),
  (150, 'Montserrat', 'MS'),
  (151, 'Morocco', 'MA'),
  (152, 'Mozambique', 'MZ'),
  (153, 'Myanmar', 'MM'),
  (154, 'Namibia', 'NA'),
  (155, 'Nauru', 'NR'),
  (156, 'Nepal', 'NP'),
  (157, 'Netherlands', 'NL'),
  (158, 'New Caledonia', 'NC'),
  (159, 'New Zealand', 'NZ'),
  (160, 'Nicaragua', 'NI'),
  (161, 'Niger', 'NE'),
  (162, 'Nigeria', 'NG'),
  (163, 'Niue', 'NU'),
  (164, 'Norfolk Island', 'NF'),
  (165, 'Northern Mariana Islands', 'MP'),
  (166, 'Norway', 'NO'),
  (167, 'Occupied Palestinian Territory', 'PS'),
  (168, 'Oman', 'OM'),
  (169, 'Pakistan', 'PK'),
  (170, 'Palau', 'PW'),
  (171, 'Panama', 'PA'),
  (172, 'Papua New Guinea', 'PG'),
  (173, 'Paraguay', 'PY'),
  (174, 'Peru', 'PE'),
  (175, 'Philippines', 'PH'),
  (176, 'Pitcairn', 'PN'),
  (177, 'Poland', 'PL'),
  (178, 'Portugal', 'PT'),
  (179, 'Puerto Rico', 'PR'),
  (180, 'Qatar', 'QA'),
  (181, 'Reunion', 'RE'),
  (182, 'Romania', 'RO'),
  (183, 'Russian Federation', 'RU'),
  (184, 'Rwanda', 'RW'),
  (185, 'Saint Barthelemy', 'BL'),
  (186, 'Saint Helena, Ascension and Tristan da Cunha', 'SH'),
  (187, 'Saint Kitts and Nevis', 'KN'),
  (188, 'Saint Lucia', 'LC'),
  (189, 'Saint Martin (French part)', 'MF'),
  (190, 'Saint Pierre and Miquelon', 'PM'),
  (191, 'Saint Vincent and The Grenadines', 'VC'),
  (192, 'Samoa', 'WS'),
  (193, 'San Marino', 'SM'),
  (194, 'Sao Tome and Principe', 'ST'),
  (195, 'Saudi Arabia', 'SA'),
  (196, 'Senegal', 'SN'),
  (197, 'Serbia', 'RS'),
  (198, 'Seychelles', 'SC'),
  (199, 'Sierra Leone', 'SL'),
  (200, 'Singapore', 'SG'),
  (201, 'Sint Maarten (Dutch part)', 'SX'),
  (202, 'Slovakia', 'SK'),
  (203, 'Slovenia', 'SI'),
  (204, 'Solomon Islands', 'SB'),
  (205, 'Somalia', 'SO'),
  (206, 'South Africa', 'ZA'),
  (207, 'South Georgia and the South Sandwich Islands', 'GS'),
  (208, 'South Sudan', 'SS'),
  (209, 'Spain', 'ES'),
  (210, 'Sri Lanka', 'LK'),
  (211, 'Sudan', 'SD'),
  (212, 'Suriname', 'SR'),
  (213, 'Svalbard and Jan Mayen', 'SJ'),
  (214, 'Swaziland', 'SZ'),
  (215, 'Sweden', 'SE'),
  (216, 'Switzerland', 'CH'),
  (217, 'Syrian Arab Republic', 'SY'),
  (218, 'Taiwan, Province of China', 'TW'),
  (219, 'Tajikistan', 'TJ'),
  (220, 'Tanzania, United Republic of', 'TZ'),
  (221, 'Thailand', 'TH'),
  (222, 'Timor-Leste', 'TL'),
  (223, 'Togo', 'TG'),
  (224, 'Tokelau', 'TK'),
  (225, 'Tonga', 'TO'),
  (226, 'Trinidad and Tobago', 'TT'),
  (227, 'Tunisia', 'TN'),
  (228, 'Turkey', 'TR'),
  (229, 'Turkmenistan', 'TM'),
  (230, 'Turks and Caicos Islands', 'TC'),
  (231, 'Tuvalu', 'TV'),
  (232, 'Uganda', 'UG'),
  (233, 'Ukraine', 'UA'),
  (234, 'United Arab Emirates', 'AE'),
  (235, 'United Kingdom', 'GB'),
  (236, 'United States', 'US'),
  (237, 'United States Minor Outlying Islands', 'UM'),
  (238, 'Uruguay', 'UY'),
  (239, 'Uzbekistan', 'UZ'),
  (240, 'Vanuatu', 'VU'),
  (241, 'Venezuela, Bolivarian Republic of', 'VE'),
  (242, 'Viet Nam', 'VN'),
  (243, 'Virgin Islands, British', 'VG'),
  (244, 'Virgin Islands, U.S.', 'VI'),
  (245, 'Wallis and Futuna', 'WF'),
  (246, 'Western Sahara', 'EH'),
  (247, 'Yemen', 'YE'),
  (248, 'Zambia', 'ZM'),
  (249, 'Zimbabwe', 'ZW');

-- --------------------------------------------------------

--
-- Table structure for table `Course`
--

CREATE TABLE IF NOT EXISTS `Course` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Course`
--

INSERT INTO `Course` (`id`, `name`, `slug`, `status`) VALUES
  (1, 'Computer Science & IT', 'computer-science-it', 1),
  (2, 'Business, Marketing & Management', 'business-marketing-management', 1),
  (3, 'Engineering & Architecture', 'engineering-architecture', 1),
  (4, 'Medical & Health Science', 'medical-health-science', 1),
  (5, 'Textile, Garment & Leather Technology', 'textile-garment-leather-technology', 1),
  (6, 'Media, Journalism & Communication', 'media-journalism-communication', 1),
  (7, 'Language Studies', 'language-studies', 1);

-- --------------------------------------------------------

--
-- Table structure for table `CourseLevel`
--

CREATE TABLE IF NOT EXISTS `CourseLevel` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `CourseLevel`
--

INSERT INTO `CourseLevel` (`id`, `name`, `slug`, `status`) VALUES
  (1, 'Postgraduate Degree', 'postgraduate-degree', 1),
  (2, 'Postgraduate Diploma', 'postgraduate-diploma', 1),
  (3, 'Undergraduate Degree', 'undergraduate-degree', 1),
  (4, 'HSC/A-Level', 'hsc-a-level', 1),
  (5, 'SSC/O-Level', 'ssc-o-level', 1),
  (6, 'Diploma Course', 'diploma-course', 1),
  (7, 'Certification Course', 'certification-course', 1),
  (8, 'Professional Course', 'professional-course', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_courselevel`
--

CREATE TABLE IF NOT EXISTS `course_courselevel` (
  `course_id` int(11) NOT NULL,
  `courselevel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Customer`
--

CREATE TABLE IF NOT EXISTS `Customer` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `address` longtext COLLATE utf8_unicode_ci,
  `thana_id` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bloodGroup` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` datetime DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Customer`
--

INSERT INTO `Customer` (`id`, `name`, `mobile`, `district_id`, `address`, `thana_id`, `email`, `bloodGroup`, `dob`, `globalOption_id`) VALUES
  (1, 'ami', '', NULL, '', NULL, '', '', '0000-00-00 00:00:00', 10),
  (2, 'tumi', '', NULL, '', NULL, '', '', '0000-00-00 00:00:00', 10),
  (3, '34242342', '34242342', NULL, NULL, NULL, NULL, NULL, NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `DomainUser`
--

CREATE TABLE IF NOT EXISTS `DomainUser` (
  `id` int(11) NOT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `globalOption_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `DomainUser`
--

INSERT INTO `DomainUser` (`id`, `role`, `created`, `status`, `globalOption_id`, `user_id`) VALUES
  (1, 'SUPER_ADMIN', '0000-00-00 00:00:00', 1, 10, 16);

-- --------------------------------------------------------

--
-- Table structure for table `Education`
--

CREATE TABLE IF NOT EXISTS `Education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `establishment` date DEFAULT NULL,
  `instituteCheif` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `instituteCheifDesignation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8_unicode_ci,
  `hotline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registrationNo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skypeId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weeklyOffDay` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `overview` longtext COLLATE utf8_unicode_ci,
  `contactPersonDesignation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education_courselevel`
--

CREATE TABLE IF NOT EXISTS `education_courselevel` (
  `education_id` int(11) NOT NULL,
  `courselevel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education_institutelevel`
--

CREATE TABLE IF NOT EXISTS `education_institutelevel` (
  `education_id` int(11) NOT NULL,
  `institutelevel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EmailBox`
--

CREATE TABLE IF NOT EXISTS `EmailBox` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Event`
--

CREATE TABLE IF NOT EXISTS `Event` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `additionalPhone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `startHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8_unicode_ci,
  `content` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoGallery_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `EventCalender`
--

CREATE TABLE IF NOT EXISTS `EventCalender` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Faq`
--

CREATE TABLE IF NOT EXISTS `Faq` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `FooterSetting`
--

CREATE TABLE IF NOT EXISTS `FooterSetting` (
  `id` int(11) NOT NULL,
  `copyRight` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `displayWebsite` tinyint(1) NOT NULL,
  `turnOffBranding` tinyint(1) NOT NULL,
  `addressHomePage` tinyint(1) NOT NULL,
  `addressSubPage` tinyint(1) NOT NULL,
  `addressIconPage` tinyint(1) NOT NULL,
  `phoneHomePage` tinyint(1) NOT NULL,
  `phoneSubPage` tinyint(1) NOT NULL,
  `phoneDisplay` tinyint(1) NOT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `FooterSetting`
--

INSERT INTO `FooterSetting` (`id`, `copyRight`, `displayWebsite`, `turnOffBranding`, `addressHomePage`, `addressSubPage`, `addressIconPage`, `phoneHomePage`, `phoneSubPage`, `phoneDisplay`, `globalOption_id`) VALUES
  (2, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 8),
  (3, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 9),
  (4, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 10),
  (5, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 11);

-- --------------------------------------------------------

--
-- Table structure for table `fos_user`
--

CREATE TABLE IF NOT EXISTS `fos_user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `fos_user`
--

INSERT INTO `fos_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `avatar`) VALUES
  (1, 'admin', 'admin', 'admin@bindu.com', 'admin@bindu.com', 1, 'shftvv5sn808g8ow44444cwcc44kc4o', '1FIperpxrkIq7aAfphnamyU0UTcKdzo4SeCZ9nOgiWxhHp2ZVIazqip1IsUVYlM15jrD3lV2Emxfl6vHuS5Qtw==', '2016-01-21 22:40:47', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}', 0, NULL, NULL),
  (2, '01828148148', '01824148148', '018281488948@gmail.com', '018281488948@gmail.com', 1, '30ei8xmfl7i8wgwwco808cg0ok8ok0w', 'nrIpvRyW5XZgNCXaZQlujfv6pcYuSOFNfqLE1dBkeIjTvCKhaYmmBrfLCjAcMT0GUCA3dxFFcYb4laIGyR3Huw==', '2016-01-01 19:50:06', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (4, '01827164133', '01817164133', '0182716419933@gmail.com', '0182716419933@gmail.com', 1, 'kjbw935ns408wkcww00k0wc00gkkws0', 'atTYcE28mOELHj+4HmmmIR936VgY232eMIMp0bQbuJeEUwwF3OUylxcvZIXLJTCyoC/I80aQHfb0j5GNefhSew==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (6, '01827164144', '01827164144', '01827164144@gmail.com', '01827164144@gmail.com', 1, 'mqkyylb6h5w4c0sw8s8wos4kc4oss00', 'VmSw3qog+ngMtiFmvygeG18FlLmPyKe007THu7UtzQqStoL975ddjElfoe2vsSklMJ302ngotuNP1kWZmeHQgg==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (8, '01827164145', '01827164145', '01827164145@gmail.com', '01827164145@gmail.com', 1, 'tclnjaqasnk8ggoos4ckk8c8swkgss8', '1PNtFA9Jc38Fkov7EFVTPmGqqhbzdKdv17AdNenolyjL0z4uP3zcxfOTza8f8V8YyqwCVRRbw+HBptGcc9VpmA==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (9, '01827164146', '01827164146', '01827164146@gmail.com', '01827164146@gmail.com', 1, '9w5pissqlqg4oc4c0c0k0448gc8soo', 'Kc+eXSlOPPl/7NWTgszWTd9zu0BBuLucFiHyPPawL4w2fXPHWxPlCreRtqrnW2TV5RqnbS9YZaFegwncIISA4Q==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (10, '01827164101', '01827164101', '01827164101@gmail.com', '01827164101@gmail.com', 1, '8htqiib9hk00s8woccccocc4w00040k', 'EgenpYJ/ZkzLYd+qLLZNmCClqO5Jp3FdowJlV5S1YeCbeDvDyaPAL8i8mddQVJ5KZRmqNBX0kD7XEQ4R9p5OKA==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (11, '01827164102', '01827164102', '01827164102@gmail.com', '01827164102@gmail.com', 1, 'pxqg83i4n6okwok4sggssso4c0s440o', 'Ks3eRTujmqPCstegDASse4CNmRnhbocARy12DM04sQA5alzlSpTimJ6+JjXdqsZ7n7YtkrxCE19SkNfLBou8Iw==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (13, '01828148183', '018281481843', '01828148184@gmail.com', '01828148184@gmail.com', 1, '2d8ok0mdnjr4ok4k8084w4k4s4s00o', 'T9utB8H0gFGCcoY7I8jdrBVIaA9PRg1pxS/dmjiV+zIOIz6yCrQXX853PPThnqevXJO/aBTv3CYc6VGPdRNVxw==', '2016-01-05 00:48:58', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (16, 'opu', 'opu', '01828148148@gmail.com', '01828148148@gmail.com', 1, 'j66id93437wocokwkwo4o8coso88840', 'ZeHdTU95fn2zqbsVHVP3OZbG9rhsrouciV7/URuQXmufUYMOe/mbLGFWb6Cj8BUEgzpEH2kLuonmf221fVkmRA==', '2016-02-11 23:30:23', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL),
  (17, '01552496139', '01552496139', '01552496139@gmail.com', '01552496139@gmail.com', 1, 'jmjwug1rx9kows8o4o0o8wcogokw0kw', 'jlA9BPNYp9GCkgrcxky4py4uXCFsIft/5xKLcHLnu0V1hBS4JM1WnfOwqeVjvvOAcK0PKNWTYP7Cqd6d2qgO0w==', '2016-01-02 17:04:09', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:11:"ROLE_VENDOR";}', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `GalleryImage`
--

CREATE TABLE IF NOT EXISTS `GalleryImage` (
  `id` int(11) NOT NULL,
  `caption` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` tinytext COLLATE utf8_unicode_ci,
  `path` tinytext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL,
  `sorting` smallint(6) NOT NULL,
  `photoGallery_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `GalleryImage`
--

INSERT INTO `GalleryImage` (`id`, `caption`, `description`, `path`, `status`, `sorting`, `photoGallery_id`) VALUES
  (1, NULL, NULL, 'p1a8123uuu24bck31ngro9c1mms4.JPG', 1, 1, 1),
  (2, NULL, NULL, 'p1a8123uuu1fp1b3j13h0jrfl1p5.JPG', 1, 4, 1),
  (3, NULL, NULL, 'p1a8123uuv126rg67npp1fm91tj16.JPG', 1, 7, 1),
  (4, NULL, NULL, 'p1a8123uuv9mc15hkun6p9cqlc7.JPG', 1, 10, 1),
  (5, NULL, NULL, 'p1a8123uuv1tq21hk5130m1e6d5ko8.JPG', 1, 13, 1),
  (6, NULL, NULL, 'p1a8123uuv1smo18aq1kte17no1s8f9.JPG', 1, 16, 1);

-- --------------------------------------------------------

--
-- Table structure for table `GlobalOption`
--

CREATE TABLE IF NOT EXISTS `GlobalOption` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `syndicate_id` int(11) DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subDomain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isMobile` tinyint(1) DEFAULT NULL,
  `customizeDesign` tinyint(1) DEFAULT NULL,
  `facebookAds` tinyint(1) DEFAULT NULL,
  `facebookApps` tinyint(1) DEFAULT NULL,
  `facebookPageUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitterUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `googlePlus` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `promotion` tinyint(1) DEFAULT NULL,
  `googleAds` tinyint(1) DEFAULT NULL,
  `smsIntegration` tinyint(1) DEFAULT NULL,
  `emailIntegration` tinyint(1) DEFAULT NULL,
  `isIntro` tinyint(1) DEFAULT NULL,
  `callBackEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `callBackContent` longtext COLLATE utf8_unicode_ci,
  `callBackNotify` tinyint(1) DEFAULT NULL,
  `primaryNumber` tinyint(1) DEFAULT NULL,
  `leaveEmail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `leaveContent` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `GlobalOption`
--

INSERT INTO `GlobalOption` (`id`, `user_id`, `location_id`, `syndicate_id`, `mobile`, `email`, `name`, `slug`, `domain`, `subDomain`, `isMobile`, `customizeDesign`, `facebookAds`, `facebookApps`, `facebookPageUrl`, `twitterUrl`, `googlePlus`, `promotion`, `googleAds`, `smsIntegration`, `emailIntegration`, `isIntro`, `callBackEmail`, `callBackContent`, `callBackNotify`, `primaryNumber`, `leaveEmail`, `leaveContent`, `status`) VALUES
  (1, 1, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (3, 4, 63, 152, NULL, NULL, 'Shoshi Collection', 'shoshi-collection', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0),
  (4, 6, 63, 152, NULL, NULL, 'Shoshi Collection', 'shoshi-collection-1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1),
  (5, 8, 63, 152, NULL, NULL, 'Shoshi Collection', 'shoshi-collection-2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1),
  (6, 9, 63, 152, NULL, NULL, 'Shoshi Collection', 'shoshi-collection-3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1),
  (7, 10, 63, 152, NULL, NULL, 'Shoshi Collection', 'shoshi-collection-4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1),
  (8, 11, 63, 152, '01827164102', NULL, 'Shoshi Collection', 'shoshi-collection-5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 0),
  (9, 13, 63, 152, '01828148184', NULL, 'Shoshi Collection', 'shoshi-collection-6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 1),
  (10, 16, 62, 152, '01828148148', NULL, 'Nahar Fashion', 'nahar-fashion', 'dhaka.com', 'dhaka', NULL, 0, 0, 0, '', '', '', 0, 0, 0, 0, NULL, NULL, NULL, 0, 1, NULL, NULL, 1),
  (11, 17, 14, 152, '01552496139', NULL, 'Opu Collection', 'opu-collection', 'opucollection.com', 'opu', NULL, 0, 0, 0, NULL, NULL, NULL, 0, 0, 0, 0, 1, NULL, NULL, 0, 1, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `HomeBlock`
--

CREATE TABLE IF NOT EXISTS `HomeBlock` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `homePage_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `HomePage`
--

CREATE TABLE IF NOT EXISTS `HomePage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `showingListing` int(11) DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL,
  `photoGallery_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `HomePage`
--

INSERT INTO `HomePage` (`id`, `user_id`, `name`, `content`, `showingListing`, `path`, `globalOption_id`, `photoGallery_id`) VALUES
  (2, 11, 'Home', NULL, NULL, NULL, 8, NULL),
  (3, 13, 'Home', NULL, NULL, NULL, 9, NULL),
  (4, 16, 'Home', NULL, NULL, NULL, 10, NULL),
  (5, 17, 'Home', 'Enter a description for your business. This text will appear above/below the logo on the home page of your mobile site only.\r\n        ', NULL, NULL, 11, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `homepage_module`
--

CREATE TABLE IF NOT EXISTS `homepage_module` (
  `module_id` int(11) NOT NULL,
  `homePage_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_syndicate`
--

CREATE TABLE IF NOT EXISTS `homepage_syndicate` (
  `syndicate_id` int(11) NOT NULL,
  `homePage_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_syndicatemodule`
--

CREATE TABLE IF NOT EXISTS `homepage_syndicatemodule` (
  `syndicate_module_id` int(11) NOT NULL,
  `homePage_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `HomeSlider`
--

CREATE TABLE IF NOT EXISTS `HomeSlider` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `sorting` smallint(6) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instituteLevel`
--

CREATE TABLE IF NOT EXISTS `instituteLevel` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `InventoryConfig`
--

CREATE TABLE IF NOT EXISTS `InventoryConfig` (
  `id` int(11) NOT NULL,
  `globalOption_id` int(11) DEFAULT NULL,
  `itemPath` longtext COLLATE utf8_unicode_ci,
  `itemGalleryPath` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `InventoryConfig`
--

INSERT INTO `InventoryConfig` (`id`, `globalOption_id`, `itemPath`, `itemGalleryPath`) VALUES
  (1, 4, NULL, NULL),
  (2, 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Item`
--

CREATE TABLE IF NOT EXISTS `Item` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `itemUnit` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `minQnt` int(11) DEFAULT NULL,
  `maxQnt` int(11) DEFAULT NULL,
  `remainingQnt` int(11) DEFAULT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchasePrice` double DEFAULT NULL,
  `purchaseAvgPrice` double DEFAULT NULL,
  `salesPrice` double DEFAULT NULL,
  `webPrice` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `vendor_id` int(11) DEFAULT NULL,
  `updated` datetime NOT NULL,
  `itemCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `masterItem_id` int(11) DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skuSlug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skuWebSlug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `webName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchaseQuantity` double DEFAULT NULL,
  `purchaseQuantityReturn` double DEFAULT NULL,
  `salesQuantity` double DEFAULT NULL,
  `salesQuantityReturn` double DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Item`
--

INSERT INTO `Item` (`id`, `name`, `slug`, `color_id`, `created`, `status`, `itemUnit`, `minQnt`, `maxQnt`, `remainingQnt`, `inventoryConfig_id`, `barcode`, `purchasePrice`, `purchaseAvgPrice`, `salesPrice`, `webPrice`, `quantity`, `country_id`, `brand_id`, `path`, `content`, `vendor_id`, `updated`, `itemCode`, `size_id`, `masterItem_id`, `sku`, `skuSlug`, `skuWebSlug`, `webName`, `purchaseQuantity`, `purchaseQuantityReturn`, `salesQuantity`, `salesQuantityReturn`) VALUES
  (1, 'test', 'formal-shirt-black-grey-medium-nahar-fashion', 1, '2016-02-08 21:22:15', 1, NULL, NULL, NULL, NULL, 2, NULL, 100, NULL, 150, 0, 10, NULL, NULL, NULL, NULL, 6, '2016-02-11 15:23:54', '0001', 1, 1, '0001-01-01-006', 'formal_shirt-black-grey-medium-gb', 'formal_shirt-black-grey-medium-nahar-fashion', 'Test web', 20, 281, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ItemColor`
--

CREATE TABLE IF NOT EXISTS `ItemColor` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ItemColor`
--

INSERT INTO `ItemColor` (`id`, `name`, `slug`, `code`, `status`, `inventoryConfig_id`) VALUES
  (1, 'Black Grey', 'black-grey', '01', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ItemGallery`
--

CREATE TABLE IF NOT EXISTS `ItemGallery` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `path` tinytext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ItemInventory`
--

CREATE TABLE IF NOT EXISTS `ItemInventory` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `itemProcess` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ItemSize`
--

CREATE TABLE IF NOT EXISTS `ItemSize` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ItemSize`
--

INSERT INTO `ItemSize` (`id`, `name`, `slug`, `code`, `status`, `inventoryConfig_id`) VALUES
  (1, 'Medium', 'medium', '01', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ItemTypeGrouping`
--

CREATE TABLE IF NOT EXISTS `ItemTypeGrouping` (
  `id` int(11) NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ItemTypeGrouping`
--

INSERT INTO `ItemTypeGrouping` (`id`, `inventoryConfig_id`) VALUES
  (1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `itemtypegrouping_category`
--

CREATE TABLE IF NOT EXISTS `itemtypegrouping_category` (
  `itemtypegrouping_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `itemtypegrouping_category`
--

INSERT INTO `itemtypegrouping_category` (`itemtypegrouping_id`, `category_id`) VALUES
  (1, 1),
  (1, 2),
  (1, 3),
  (1, 4),
  (1, 7),
  (1, 11),
  (1, 12),
  (1, 13),
  (1, 18),
  (1, 19),
  (1, 20),
  (1, 21),
  (1, 22),
  (1, 23),
  (1, 99),
  (1, 100),
  (1, 101),
  (1, 102),
  (1, 103),
  (1, 104),
  (1, 105),
  (1, 106),
  (1, 107),
  (1, 108),
  (1, 109),
  (1, 110),
  (1, 111),
  (1, 112),
  (1, 113),
  (1, 114),
  (1, 115),
  (1, 116),
  (1, 117),
  (1, 118),
  (1, 119),
  (1, 120),
  (1, 121),
  (1, 122),
  (1, 123),
  (1, 124),
  (1, 125),
  (1, 126),
  (1, 127),
  (1, 128),
  (1, 129),
  (1, 130),
  (1, 131),
  (1, 132),
  (1, 133),
  (1, 134),
  (1, 135),
  (1, 136),
  (1, 137),
  (1, 138),
  (1, 139),
  (1, 140),
  (1, 141),
  (1, 142),
  (1, 143),
  (1, 144),
  (1, 145),
  (1, 146),
  (1, 147),
  (1, 148),
  (1, 149),
  (1, 150),
  (1, 151),
  (1, 152),
  (1, 153),
  (1, 154),
  (1, 155),
  (1, 156),
  (1, 157),
  (1, 158),
  (1, 159),
  (1, 160),
  (1, 161),
  (1, 162),
  (1, 163),
  (1, 164),
  (1, 165),
  (1, 166),
  (1, 167),
  (1, 243),
  (1, 244),
  (1, 245),
  (1, 246),
  (1, 247),
  (1, 248),
  (1, 249),
  (1, 250),
  (1, 251),
  (1, 252),
  (1, 253),
  (1, 254),
  (1, 255),
  (1, 256),
  (1, 257),
  (1, 258),
  (1, 259),
  (1, 260),
  (1, 261),
  (1, 262),
  (1, 380),
  (1, 381),
  (1, 382),
  (1, 383),
  (1, 384),
  (1, 385),
  (1, 386),
  (1, 387),
  (1, 388),
  (1, 389),
  (1, 390),
  (1, 391),
  (1, 392),
  (1, 393),
  (1, 394),
  (1, 395);

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE IF NOT EXISTS `item_category` (
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_master`
--

CREATE TABLE IF NOT EXISTS `item_master` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=348 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item_master`
--

INSERT INTO `item_master` (`id`, `category_id`, `slug`, `created`, `updated`, `name`, `code`, `unit`, `status`, `inventoryConfig_id`) VALUES
  (1, 628, 'formal_shirt', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Formal Shirt', '0001', 'Pics', 1, 2),
  (2, 628, 'casual_shirt', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Casual Shirt', '0002', 'Pics', 1, 2),
  (3, 629, 'full_tshirt', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Full Tshirt', '0003', 'Pics', 1, 2),
  (4, 629, 'full_polo_tshirt', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Full Polo Tshirt', '0004', 'Pics', 1, 2),
  (5, 631, 'panjabi', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Panjabi', '0005', 'Pics', 1, 2),
  (307, 631, 'panjabi_1', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Panjabi', '0006', 'Pair', 1, 2),
  (308, 632, 'casual_shirt_1', '2016-01-22 10:51:12', '2016-01-22 10:51:12', 'Casual Shirt', '0007', 'Pics', 1, 2),
  (309, 633, 'polo_tshirt', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Polo Tshirt', '0008', 'Pics', 1, 2),
  (310, 634, 'body_spray', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Body Spray', '0009', 'Pics', 1, 2),
  (311, 635, 'body_spray_1', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Body Spray', '0010', 'Pics', 1, 2),
  (312, 635, 'belt', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Belt', '0011', 'Pics', 1, 2),
  (313, 636, 'wallet', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Wallet', '0012', 'Pics', 1, 2),
  (314, 638, 'tai', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Tai', '0013', 'Pics', 1, 2),
  (315, 639, 'tai_pin', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Tai Pin', '0014', 'Pics', 1, 2),
  (316, 640, 'culflen', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Culflen', '0015', 'Pics', 1, 2),
  (317, 641, 'tshirt', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Tshirt', '0016', 'Pics', 1, 2),
  (318, 642, 'short_pant', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Short Pant', '0017', 'Pics', 1, 2),
  (319, 643, 'blezar', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Blezar', '0018', 'Pics', 1, 2),
  (320, 644, 'coti', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Coti', '0019', 'Pics', 1, 2),
  (321, 645, 'half_polo_tshirt', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Half Polo Tshirt', '0020', 'Pics', 1, 2),
  (322, 646, 'jeans_pant', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Jeans Pant', '0021', 'Pics', 1, 2),
  (323, 647, 'gavadin_pant', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Gavadin Pant', '0022', 'Pics', 1, 2),
  (324, 648, 'formal_pant', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Formal Pant', '0023', 'Pics', 1, 2),
  (325, 649, 'under_wear', '2016-01-22 10:51:13', '2016-01-22 10:51:13', 'Under Wear', '0024', 'Pics', 1, 2),
  (326, 650, 'sando_genji', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Sando Genji', '0025', 'Pics', 1, 2),
  (327, NULL, 'three_pics', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Three Pics', '0026', 'Three pics', 1, 2),
  (328, 652, 'ladies_shirt', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Shirt', '0027', 'Pics', 1, 2),
  (329, 653, 'ladies_tshirt', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Tshirt', '0028', 'Two Pics', 1, 2),
  (330, 654, 'ladies_tops', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Tops', '0029', 'Pics', 1, 2),
  (331, NULL, 'ladies_palazzo', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Palazzo', '0030', 'Pics', 1, 2),
  (332, 655, 'gents_gavadin_pants', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Gents Gavadin Pants', '0031', 'Pics', 1, 2),
  (333, 656, 'gents_jeans_pants', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Gents Jeans Pants', '0032', 'Pics', 1, 2),
  (334, 657, 'ladies_jeans_pants', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Jeans Pants', '0033', 'Pics', 1, 2),
  (335, 658, 'ladies_jagins', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Jagins', '0034', 'Pics', 1, 2),
  (336, 659, 'ladies_three_quater', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Three Quater', '0035', 'Pics', 1, 2),
  (337, 660, 'ladies_ties', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Ties', '0036', 'Pics', 1, 2),
  (338, NULL, 'babies_tshirt', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Babies Tshirt', '0037', 'Pics', 1, 2),
  (339, NULL, 'babies_tshirt_1', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Babies Tshirt', '0038', 'Set', 1, 2),
  (340, NULL, 'babies_tshirt_2', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Babies Tshirt', '0039', 'Pis', 1, 2),
  (341, 662, 'gents_panjabi', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Gents Panjabi', '0040', 'Pics', 1, 2),
  (342, 663, 'ladies_coti', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Ladies Coti', '0041', 'Pics', 1, 2),
  (343, 664, 'gents_huddy_half_slip', '2016-01-22 10:51:14', '2016-01-22 10:51:14', 'Gents Huddy Half Slip', '0042', 'Pics', 1, 2),
  (344, 665, 'gents_huddy_full_slip', '2016-01-22 10:51:15', '2016-01-22 10:51:15', 'Gents Huddy Full Slip', '0043', 'Pics', 1, 2),
  (345, 666, 'sweater', '2016-01-22 10:51:15', '2016-01-22 10:51:15', 'Sweater', '0044', 'Pics', 1, 2),
  (346, 667, 'sweater_1', '2016-01-22 10:51:15', '2016-01-22 10:51:15', 'Sweater', '0045', 'Pics', 1, 2),
  (347, 668, 'jacket', '2016-01-22 10:51:15', '2016-01-22 10:51:15', 'Jacket', '0046', 'Pics', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1468 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `parent`, `name`, `level`, `path`) VALUES
  (1, NULL, 'Dhaka', 1, '/1'),
  (2, NULL, 'Chittagong', 1, '/2'),
  (3, NULL, 'Rajshahi', 1, '/3'),
  (4, NULL, 'Rangpur', 1, '/4'),
  (5, NULL, 'KhulnA', 1, '/5'),
  (6, NULL, 'Barisal', 1, '/6'),
  (7, NULL, 'Sylhet', 1, '/7'),
  (8, 1, 'Dhaka', 2, '/1/8'),
  (9, 1, 'Faridpur', 2, '/1/9'),
  (10, 1, 'Gazipur', 2, '/1/10'),
  (11, 1, 'Gopalganj', 2, '/1/11'),
  (12, 1, 'Jamalpur', 2, '/1/12'),
  (13, 1, 'Kishorgonj', 2, '/1/13'),
  (14, 1, 'Madaripur', 2, '/1/14'),
  (15, 1, 'Manikganj', 2, '/1/15'),
  (16, 1, 'Munshiganj', 2, '/1/16'),
  (17, 1, 'Mymensingh', 2, '/1/17'),
  (18, 1, 'Narayanganj', 2, '/1/18'),
  (19, 1, 'Narsingdi', 2, '/1/19'),
  (20, 1, 'Netrakona', 2, '/1/20'),
  (21, 1, 'Rajbari', 2, '/1/21'),
  (22, 1, 'Shariatpur', 2, '/1/22'),
  (23, 1, 'Sherpur', 2, '/1/23'),
  (24, 1, 'Tangail', 2, '/1/24'),
  (25, 2, 'Bandarban', 2, '/2/25'),
  (26, 2, 'Brahmanbaria', 2, '/2/26'),
  (27, 2, 'Chandpur', 2, '/2/27'),
  (28, 2, 'Chittagong', 2, '/2/28'),
  (29, 2, 'Comilla', 2, '/2/29'),
  (30, 2, 'Cox''s Bazar', 2, '/2/30'),
  (31, 2, 'Feni', 2, '/2/31'),
  (32, 2, 'Khagrachhari', 2, '/2/32'),
  (33, 2, 'Lakshmipur', 2, '/2/33'),
  (34, 2, 'Noakhali', 2, '/2/34'),
  (35, 2, 'Rangamati', 2, '/2/35'),
  (36, 3, 'Bogra', 2, '/3/36'),
  (37, 3, 'Chapai Nawabganj', 2, '/3/37'),
  (38, 3, 'Joypurhat', 2, '/3/38'),
  (39, 3, 'Naogaon', 2, '/3/39'),
  (40, 3, 'Natore', 2, '/3/40'),
  (41, 3, 'Pabna', 2, '/3/41'),
  (42, 3, 'Rajshahi', 2, '/3/42'),
  (43, 3, 'Sirajganj', 2, '/3/43'),
  (44, 4, 'Dinajpur', 2, '/4/44'),
  (45, 4, 'Gaibandha', 2, '/4/45'),
  (46, 4, 'Kurigram', 2, '/4/46'),
  (47, 4, 'Lalmonirhat', 2, '/4/47'),
  (48, 4, 'Nilphamari', 2, '/4/48'),
  (49, 4, 'Panchagarh', 2, '/4/49'),
  (50, 4, 'Rangpur', 2, '/4/50'),
  (51, 4, 'Thakurgaon', 2, '/4/51'),
  (52, 5, 'Bagerhat', 2, '/5/52'),
  (53, 5, 'Chuadanga', 2, '/5/53'),
  (54, 5, 'Jessore', 2, '/5/54'),
  (55, 5, 'Jhenaidah', 2, '/5/55'),
  (56, 5, 'Khulna', 2, '/5/56'),
  (57, 5, 'Kushtia', 2, '/5/57'),
  (58, 5, 'Magura', 2, '/5/58'),
  (59, 5, 'Meherpur', 2, '/5/59'),
  (60, 5, 'Narail', 2, '/5/60'),
  (61, 5, 'Satkhira', 2, '/5/61'),
  (62, 6, 'Barguna', 2, '/6/62'),
  (63, 6, 'Barisal', 2, '/6/63'),
  (64, 6, 'Bhola', 2, '/6/64'),
  (65, 6, 'Jhalokati', 2, '/6/65'),
  (66, 6, 'Patuakhali', 2, '/6/66'),
  (67, 6, 'Pirojpur', 2, '/6/67'),
  (68, 7, 'Habiganj', 2, '/7/68'),
  (69, 7, 'Maulvibazar', 2, '/7/69'),
  (70, 7, 'Sunamganj', 2, '/7/70'),
  (71, 7, 'Sylhet', 2, '/7/71'),
  (72, 25, 'ALIKADAM', 3, '/2/25/72'),
  (73, 25, 'BANDARBAN SADAR', 3, '/2/25/73'),
  (74, 25, 'LAMA', 3, '/2/25/74'),
  (75, 25, 'NAIKHONGCHHARI', 3, '/2/25/75'),
  (76, 25, 'ROWANGCHHARI', 3, '/2/25/76'),
  (77, 25, 'RUMA', 3, '/2/25/77'),
  (78, 25, 'THANCHI', 3, '/2/25/78'),
  (79, 26, 'AKHAURA', 3, '/2/26/79'),
  (80, 26, 'BANCHHARAMPUR', 3, '/2/26/80'),
  (81, 26, 'BIJOYNAGAR', 3, '/2/26/81'),
  (82, 26, 'BRAHMANBARIA SADAR', 3, '/2/26/82'),
  (83, 26, 'ASHUGANJ', 3, '/2/26/83'),
  (84, 26, 'KASBA', 3, '/2/26/84'),
  (85, 26, 'NABINAGAR', 3, '/2/26/85'),
  (86, 26, 'NASIRNAGAR', 3, '/2/26/86'),
  (87, 26, 'SARAIL', 3, '/2/26/87'),
  (88, 27, 'CHANDPUR SADAR', 3, '/2/27/88'),
  (89, 27, 'FARIDGANJ', 3, '/2/27/89'),
  (90, 27, 'HAIM CHAR', 3, '/2/27/90'),
  (91, 27, 'HAJIGANJ', 3, '/2/27/91'),
  (92, 27, 'KACHUA', 3, '/2/27/92'),
  (93, 27, 'MATLAB DAKSHIN', 3, '/2/27/93'),
  (94, 27, 'MATLAB UTTAR', 3, '/2/27/94'),
  (95, 27, 'SHAHRASTI', 3, '/2/27/95'),
  (96, 28, 'ANOWARA', 3, '/2/28/96'),
  (97, 28, 'BAYEJID BOSTAMI', 3, '/2/28/97'),
  (98, 28, 'BANSHKHALI', 3, '/2/28/98'),
  (99, 28, 'BAKALIA', 3, '/2/28/99'),
  (100, 28, 'BOALKHALI', 3, '/2/28/100'),
  (101, 28, 'CHANDANAISH', 3, '/2/28/101'),
  (102, 28, 'CHANDGAON', 3, '/2/28/102'),
  (103, 28, 'CHITTAGONG PORT', 3, '/2/28/103'),
  (104, 28, 'DOUBLE MOORING', 3, '/2/28/104'),
  (105, 28, 'FATIKCHHARI', 3, '/2/28/105'),
  (106, 28, 'HALISHAHAR', 3, '/2/28/106'),
  (107, 28, 'HATHAZARI', 3, '/2/28/107'),
  (108, 28, 'KOTWALI', 3, '/2/28/108'),
  (109, 28, 'KHULSHI', 3, '/2/28/109'),
  (110, 28, 'LOHAGARA', 3, '/2/28/110'),
  (111, 28, 'MIRSHARAI', 3, '/2/28/111'),
  (112, 28, 'PAHARTALI', 3, '/2/28/112'),
  (113, 28, 'PANCHLAISH', 3, '/2/28/113'),
  (114, 28, 'PATIYA', 3, '/2/28/114'),
  (115, 28, 'PATENGA', 3, '/2/28/115'),
  (116, 28, 'RANGUNIA', 3, '/2/28/116'),
  (117, 28, 'RAOZAN', 3, '/2/28/117'),
  (118, 28, 'SANDWIP', 3, '/2/28/118'),
  (119, 28, 'SATKANIA', 3, '/2/28/119'),
  (120, 28, 'SITAKUNDA', 3, '/2/28/120'),
  (121, 29, 'BARURA', 3, '/2/29/121'),
  (122, 29, 'BRAHMAN PARA', 3, '/2/29/122'),
  (123, 29, 'BURICHANG', 3, '/2/29/123'),
  (124, 29, 'CHANDINA', 3, '/2/29/124'),
  (125, 29, 'CHAUDDAGRAM', 3, '/2/29/125'),
  (126, 29, 'COMILLA SADAR DAKSHIN', 3, '/2/29/126'),
  (127, 29, 'DAUDKANDI', 3, '/2/29/127'),
  (128, 29, 'DEBIDWAR', 3, '/2/29/128'),
  (129, 29, 'HOMNA', 3, '/2/29/129'),
  (130, 29, 'COMILLA ADARSHA SADAR', 3, '/2/29/130'),
  (131, 29, 'LAKSAM', 3, '/2/29/131'),
  (132, 29, 'MANOHARGANJ', 3, '/2/29/132'),
  (133, 29, 'MEGHNA', 3, '/2/29/133'),
  (134, 29, 'MURADNAGAR', 3, '/2/29/134'),
  (135, 29, 'NANGALKOT', 3, '/2/29/135'),
  (136, 29, 'TITAS', 3, '/2/29/136'),
  (137, 30, 'CHAKARIA', 3, '/2/30/137'),
  (138, 30, 'COX''S BAZAR SADAR', 3, '/2/30/138'),
  (139, 30, 'KUTUBDIA', 3, '/2/30/139'),
  (140, 30, 'MAHESHKHALI', 3, '/2/30/140'),
  (141, 30, 'PEKUA', 3, '/2/30/141'),
  (142, 30, 'RAMU', 3, '/2/30/142'),
  (143, 30, 'TEKNAF', 3, '/2/30/143'),
  (144, 30, 'UKHIA', 3, '/2/30/144'),
  (145, 31, 'CHHAGALNAIYA', 3, '/2/31/145'),
  (146, 31, 'DAGANBHUIYAN', 3, '/2/31/146'),
  (147, 31, 'FENI SADAR', 3, '/2/31/147'),
  (148, 31, 'FULGAZI', 3, '/2/31/148'),
  (149, 31, 'PARSHURAM', 3, '/2/31/149'),
  (150, 31, 'SONAGAZI', 3, '/2/31/150'),
  (151, 32, 'DIGHINALA', 3, '/2/32/151'),
  (152, 32, 'KHAGRACHHARI SADAR', 3, '/2/32/152'),
  (153, 32, 'LAKSHMICHHARI', 3, '/2/32/153'),
  (154, 32, 'MAHALCHHARI', 3, '/2/32/154'),
  (155, 32, 'MANIKCHHARI', 3, '/2/32/155'),
  (156, 32, 'MATIRANGA', 3, '/2/32/156'),
  (157, 32, 'PANCHHARI', 3, '/2/32/157'),
  (158, 32, 'RAMGARH', 3, '/2/32/158'),
  (159, 33, 'KAMALNAGAR', 3, '/2/33/159'),
  (160, 62, 'AMTALI', 3, '/6/62/160'),
  (161, 62, 'BAMNA', 3, '/6/62/161'),
  (162, 62, 'BARGUNA SADAR', 3, '/6/62/162'),
  (163, 62, 'BETAGI', 3, '/6/62/163'),
  (164, 62, 'PATHARGHATA', 3, '/6/62/164'),
  (165, 62, 'TALTALI', 3, '/6/62/165'),
  (166, 63, 'AGAILJHARA', 3, '/6/63/166'),
  (167, 63, 'BABUGANJ', 3, '/6/63/167'),
  (168, 63, 'BAKERGANJ', 3, '/6/63/168'),
  (169, 63, 'BANARI PARA', 3, '/6/63/169'),
  (170, 63, 'GAURNADI', 3, '/6/63/170'),
  (171, 63, 'HIZLA', 3, '/6/63/171'),
  (172, 63, 'BARISAL SADAR (KOTWALI)', 3, '/6/63/172'),
  (173, 63, 'MHENDIGANJ', 3, '/6/63/173'),
  (174, 63, 'MULADI', 3, '/6/63/174'),
  (175, 63, 'WAZIRPUR', 3, '/6/63/175'),
  (176, 64, 'BHOLA SADAR', 3, '/6/64/176'),
  (177, 64, 'BURHANUDDIN', 3, '/6/64/177'),
  (178, 64, 'CHAR FASSON', 3, '/6/64/178'),
  (179, 64, 'DAULAT KHAN', 3, '/6/64/179'),
  (180, 64, 'LALMOHAN', 3, '/6/64/180'),
  (181, 64, 'MANPURA', 3, '/6/64/181'),
  (182, 64, 'TAZUMUDDIN', 3, '/6/64/182'),
  (183, 65, 'JHALOKATI SADAR', 3, '/6/65/183'),
  (184, 65, 'KANTHALIA', 3, '/6/65/184'),
  (185, 65, 'NALCHITY', 3, '/6/65/185'),
  (186, 65, 'RAJAPUR', 3, '/6/65/186'),
  (187, 66, 'BAUPHAL', 3, '/6/66/187'),
  (188, 66, 'DASHMINA', 3, '/6/66/188'),
  (189, 66, 'DUMKI', 3, '/6/66/189'),
  (190, 66, 'GALACHIPA', 3, '/6/66/190'),
  (191, 66, 'KALAPARA', 3, '/6/66/191'),
  (192, 66, 'MIRZAGANJ', 3, '/6/66/192'),
  (193, 66, 'PATUAKHALI SADAR', 3, '/6/66/193'),
  (194, 66, 'RANGABALI', 3, '/6/66/194'),
  (195, 67, 'BHANDARIA', 3, '/6/67/195'),
  (196, 67, 'KAWKHALI', 3, '/6/67/196'),
  (197, 67, 'MATHBARIA', 3, '/6/67/197'),
  (198, 67, 'NAZIRPUR', 3, '/6/67/198'),
  (199, 67, 'PIROJPUR SADAR', 3, '/6/67/199'),
  (200, 67, 'NESARABAD (SWARUPKATI)', 3, '/6/67/200'),
  (201, 67, 'ZIANAGAR', 3, '/6/67/201'),
  (202, 8, 'ADABOR', 3, '/1/8/202'),
  (203, 8, 'BADDA', 3, '/1/8/203'),
  (204, 8, 'BANGSHAL', 3, '/1/8/204'),
  (205, 8, 'BIMAN BANDAR', 3, '/1/8/205'),
  (206, 8, 'BANANI', 3, '/1/8/206'),
  (207, 8, 'CANTONMENT', 3, '/1/8/207'),
  (208, 8, 'CHAK BAZAR', 3, '/1/8/208'),
  (209, 8, 'DAKSHINKHAN', 3, '/1/8/209'),
  (210, 8, 'DARUS SALAM', 3, '/1/8/210'),
  (211, 8, 'DEMRA', 3, '/1/8/211'),
  (212, 8, 'DHAMRAI', 3, '/1/8/212'),
  (213, 8, 'DOHAR', 3, '/1/8/213'),
  (214, 8, 'BHASAN TEK', 3, '/1/8/214'),
  (215, 8, 'BHATARA', 3, '/1/8/215'),
  (216, 8, 'GENDARIA', 3, '/1/8/216'),
  (217, 8, 'GULSHAN', 3, '/1/8/217'),
  (218, 8, 'JATRABARI', 3, '/1/8/218'),
  (219, 8, 'KAFRUL', 3, '/1/8/219'),
  (220, 8, 'KADAMTALI', 3, '/1/8/220'),
  (221, 8, 'KALABAGAN', 3, '/1/8/221'),
  (222, 8, 'KAMRANGIR CHAR', 3, '/1/8/222'),
  (223, 8, 'KHILGAON', 3, '/1/8/223'),
  (224, 8, 'KHILKHET', 3, '/1/8/224'),
  (225, 8, 'KERANIGANJ', 3, '/1/8/225'),
  (226, 8, 'KOTWALI', 3, '/1/8/226'),
  (227, 8, 'LALBAGH', 3, '/1/8/227'),
  (228, 8, 'MIRPUR', 3, '/1/8/228'),
  (229, 8, 'MOTIJHEEL', 3, '/1/8/229'),
  (230, 8, 'MUGDA PARA', 3, '/1/8/230'),
  (231, 8, 'NAWABGANJ', 3, '/1/8/231'),
  (232, 8, 'NEW MARKET', 3, '/1/8/232'),
  (233, 8, 'PALLABI', 3, '/1/8/233'),
  (234, 8, 'PALTAN', 3, '/1/8/234'),
  (235, 8, 'RAMPURA', 3, '/1/8/235'),
  (236, 8, 'SABUJBAGH', 3, '/1/8/236'),
  (237, 8, 'RUPNAGAR', 3, '/1/8/237'),
  (238, 8, 'SAVAR', 3, '/1/8/238'),
  (239, 8, 'SHAHJAHANPUR', 3, '/1/8/239'),
  (240, 8, 'SHAH ALI', 3, '/1/8/240'),
  (241, 8, 'SHAHBAGH', 3, '/1/8/241'),
  (242, 8, 'SHYAMPUR', 3, '/1/8/242'),
  (243, 8, 'SHER-E-BANGLA NAGAR', 3, '/1/8/243'),
  (244, 8, 'SUTRAPUR', 3, '/1/8/244'),
  (245, 8, 'TEJGAON', 3, '/1/8/245'),
  (246, 8, 'TEJGAON IND. AREA', 3, '/1/8/246'),
  (247, 8, 'TURAG', 3, '/1/8/247'),
  (248, 8, 'UTTARA  PASCHIM', 3, '/1/8/248'),
  (249, 8, 'UTTARA  PURBA', 3, '/1/8/249'),
  (250, 8, 'UTTAR KHAN', 3, '/1/8/250'),
  (251, 8, 'WARI', 3, '/1/8/251'),
  (252, 9, 'ALFADANGA', 3, '/1/9/252'),
  (253, 9, 'BHANGA', 3, '/1/9/253'),
  (254, 9, 'BOALMARI', 3, '/1/9/254'),
  (255, 9, 'CHAR BHADRASAN', 3, '/1/9/255'),
  (256, 9, 'FARIDPUR SADAR', 3, '/1/9/256'),
  (257, 9, 'MADHUKHALI', 3, '/1/9/257'),
  (258, 9, 'NAGARKANDA', 3, '/1/9/258'),
  (259, 9, 'SADARPUR', 3, '/1/9/259'),
  (260, 9, 'SALTHA', 3, '/1/9/260'),
  (261, 10, 'GAZIPUR SADAR', 3, '/1/10/261'),
  (262, 10, 'KALIAKAIR', 3, '/1/10/262'),
  (263, 10, 'KALIGANJ', 3, '/1/10/263'),
  (264, 10, 'KAPASIA', 3, '/1/10/264'),
  (265, 10, 'SREEPUR', 3, '/1/10/265'),
  (266, 11, 'GOPALGANJ SADAR', 3, '/1/11/266'),
  (267, 11, 'KASHIANI', 3, '/1/11/267'),
  (268, 11, 'KOTALIPARA', 3, '/1/11/268'),
  (269, 11, 'MUKSUDPUR', 3, '/1/11/269'),
  (270, 11, 'TUNGIPARA', 3, '/1/11/270'),
  (271, 12, 'BAKSHIGANJ', 3, '/1/12/271'),
  (272, 12, 'DEWANGANJ', 3, '/1/12/272'),
  (273, 12, 'ISLAMPUR', 3, '/1/12/273'),
  (274, 12, 'JAMALPUR SADAR', 3, '/1/12/274'),
  (275, 12, 'MADARGANJ', 3, '/1/12/275'),
  (276, 12, 'MELANDAHA', 3, '/1/12/276'),
  (277, 12, 'SARISHABARI UPAZILA', 3, '/1/12/277'),
  (278, 13, 'AUSTAGRAM', 3, '/1/13/278'),
  (279, 13, 'BAJITPUR', 3, '/1/13/279'),
  (280, 13, 'BHAIRAB', 3, '/1/13/280'),
  (281, 13, 'HOSSAINPUR', 3, '/1/13/281'),
  (282, 13, 'ITNA', 3, '/1/13/282'),
  (283, 13, 'KARIMGANJ', 3, '/1/13/283'),
  (284, 13, 'KATIADI', 3, '/1/13/284'),
  (285, 13, 'KISHOREGANJ SADAR', 3, '/1/13/285'),
  (286, 13, 'KULIAR CHAR', 3, '/1/13/286'),
  (287, 13, 'MITHAMAIN', 3, '/1/13/287'),
  (288, 13, 'NIKLI', 3, '/1/13/288'),
  (289, 13, 'PAKUNDIA', 3, '/1/13/289'),
  (290, 13, 'TARAIL', 3, '/1/13/290'),
  (291, 14, 'KALKINI', 3, '/1/14/291'),
  (292, 14, 'MADARIPUR SADAR', 3, '/1/14/292'),
  (293, 14, 'RAJOIR', 3, '/1/14/293'),
  (294, 14, 'SHIBCHAR', 3, '/1/14/294'),
  (295, 15, 'DAULATPUR', 3, '/1/15/295'),
  (296, 15, 'GHIOR', 3, '/1/15/296'),
  (297, 15, 'HARIRAMPUR', 3, '/1/15/297'),
  (298, 15, 'MANIKGANJ SADAR', 3, '/1/15/298'),
  (299, 15, 'SATURIA', 3, '/1/15/299'),
  (300, 15, 'SHIBALAYA', 3, '/1/15/300'),
  (301, 15, 'SINGAIR', 3, '/1/15/301'),
  (302, 16, 'GAZARIA', 3, '/1/16/302'),
  (303, 16, 'LOHAJANG', 3, '/1/16/303'),
  (304, 16, 'MUNSHIGANJ SADAR', 3, '/1/16/304'),
  (305, 16, 'SERAJDIKHAN', 3, '/1/16/305'),
  (306, 16, 'SREENAGAR', 3, '/1/16/306'),
  (307, 16, 'TONGIBARI', 3, '/1/16/307'),
  (308, 17, 'BHALUKA', 3, '/1/17/308'),
  (309, 33, 'LAKSHMIPUR SADAR', 3, '/2/33/309'),
  (310, 33, 'ROYPUR', 3, '/2/33/310'),
  (311, 33, 'RAMGANJ', 3, '/2/33/311'),
  (312, 33, 'RAMGATI', 3, '/2/33/312'),
  (313, 34, 'BEGUMGANJ', 3, '/2/34/313'),
  (314, 34, 'CHATKHIL', 3, '/2/34/314'),
  (315, 34, 'COMPANIGANJ', 3, '/2/34/315'),
  (316, 34, 'HATIYA', 3, '/2/34/316'),
  (317, 34, 'KABIRHAT', 3, '/2/34/317'),
  (318, 34, 'SENBAGH', 3, '/2/34/318'),
  (319, 34, 'SONAIMURI', 3, '/2/34/319'),
  (320, 34, 'SUBARNACHAR', 3, '/2/34/320'),
  (321, 34, 'NOAKHALI SADAR', 3, '/2/34/321'),
  (322, 35, 'BAGHAICHHARI', 3, '/2/35/322'),
  (323, 35, 'BARKAL UPAZILA', 3, '/2/35/323'),
  (324, 35, 'KAWKHALI (BETBUNIA)', 3, '/2/35/324'),
  (325, 35, 'BELAI CHHARI  UPAZI', 3, '/2/35/325'),
  (326, 35, 'KAPTAI  UPAZILA', 3, '/2/35/326'),
  (327, 35, 'JURAI CHHARI UPAZIL', 3, '/2/35/327'),
  (328, 35, 'LANGADU  UPAZILA', 3, '/2/35/328'),
  (329, 35, 'NANIARCHAR  UPAZILA', 3, '/2/35/329'),
  (330, 35, 'RAJASTHALI  UPAZILA', 3, '/2/35/330'),
  (331, 35, 'RANGAMATI SADAR  UP', 3, '/2/35/331'),
  (332, 17, 'DHOBAURA', 3, '/1/17/332'),
  (333, 17, 'FULBARIA', 3, '/1/17/333'),
  (334, 17, 'GAFFARGAON', 3, '/1/17/334'),
  (335, 17, 'GAURIPUR', 3, '/1/17/335'),
  (336, 17, 'HALUAGHAT', 3, '/1/17/336'),
  (337, 17, 'ISHWARGANJ', 3, '/1/17/337'),
  (338, 17, 'MYMENSINGH SADAR', 3, '/1/17/338'),
  (339, 17, 'MUKTAGACHHA', 3, '/1/17/339'),
  (340, 17, 'NANDAIL', 3, '/1/17/340'),
  (341, 17, 'PHULPUR', 3, '/1/17/341'),
  (342, 17, 'TARAKANDA', 3, '/1/17/342'),
  (343, 17, 'TRISHAL', 3, '/1/17/343'),
  (344, 18, 'ARAIHAZAR', 3, '/1/18/344'),
  (345, 18, 'SONARGAON', 3, '/1/18/345'),
  (346, 18, 'BANDAR', 3, '/1/18/346'),
  (347, 18, 'NARAYANGANJ SADAR', 3, '/1/18/347'),
  (348, 18, 'RUPGANJ', 3, '/1/18/348'),
  (349, 19, 'BELABO', 3, '/1/19/349'),
  (350, 19, 'MANOHARDI', 3, '/1/19/350'),
  (351, 19, 'NARSINGDI SADAR', 3, '/1/19/351'),
  (352, 19, 'PALASH', 3, '/1/19/352'),
  (353, 19, 'ROYPURA', 3, '/1/19/353'),
  (354, 19, 'SHIBPUR', 3, '/1/19/354'),
  (355, 20, 'ATPARA', 3, '/1/20/355'),
  (356, 20, 'BARHATTA', 3, '/1/20/356'),
  (357, 20, 'DURGAPUR', 3, '/1/20/357'),
  (358, 20, 'KHALIAJURI', 3, '/1/20/358'),
  (359, 20, 'KALMAKANDA', 3, '/1/20/359'),
  (360, 20, 'KENDUA', 3, '/1/20/360'),
  (361, 20, 'MADAN', 3, '/1/20/361'),
  (362, 20, 'MOHANGANJ', 3, '/1/20/362'),
  (363, 20, 'NETROKONA SADAR', 3, '/1/20/363'),
  (364, 20, 'PURBADHALA', 3, '/1/20/364'),
  (365, 21, 'BALIAKANDI', 3, '/1/21/365'),
  (366, 21, 'GOALANDA', 3, '/1/21/366'),
  (367, 21, 'KALUKHALI', 3, '/1/21/367'),
  (368, 21, 'PANGSHA', 3, '/1/21/368'),
  (369, 21, 'RAJBARI SADAR', 3, '/1/21/369'),
  (370, 22, 'BHEDARGANJ', 3, '/1/22/370'),
  (371, 22, 'DAMUDYA', 3, '/1/22/371'),
  (372, 22, 'GOSAIRHAT', 3, '/1/22/372'),
  (373, 22, 'NARIA', 3, '/1/22/373'),
  (374, 22, 'SHARIATPUR SADAR', 3, '/1/22/374'),
  (375, 22, 'ZANJIRA', 3, '/1/22/375'),
  (376, 23, 'JHENAIGATI', 3, '/1/23/376'),
  (377, 23, 'NAKLA', 3, '/1/23/377'),
  (378, 23, 'NALITABARI', 3, '/1/23/378'),
  (379, 23, 'SHERPUR SADAR', 3, '/1/23/379'),
  (380, 23, 'SREEBARDI', 3, '/1/23/380'),
  (381, 24, 'BASAIL', 3, '/1/24/381'),
  (382, 24, 'BHUAPUR', 3, '/1/24/382'),
  (383, 24, 'DELDUAR', 3, '/1/24/383'),
  (384, 24, 'DHANBARI', 3, '/1/24/384'),
  (385, 24, 'GHATAIL', 3, '/1/24/385'),
  (386, 24, 'GOPALPUR', 3, '/1/24/386'),
  (387, 24, 'KALIHATI', 3, '/1/24/387'),
  (388, 24, 'MADHUPUR', 3, '/1/24/388'),
  (389, 24, 'MIRZAPUR', 3, '/1/24/389'),
  (390, 24, 'NAGARPUR', 3, '/1/24/390'),
  (391, 24, 'SAKHIPUR', 3, '/1/24/391'),
  (392, 24, 'TANGAIL SADAR', 3, '/1/24/392'),
  (393, 36, 'ADAMDIGHI', 3, '/3/36/393'),
  (394, 36, 'BOGRA SADAR', 3, '/3/36/394'),
  (395, 36, 'DHUNAT', 3, '/3/36/395'),
  (396, 36, 'DHUPCHANCHIA', 3, '/3/36/396'),
  (397, 36, 'GABTALI', 3, '/3/36/397'),
  (398, 52, 'BAGERHAT SADAR', 3, '/5/52/398'),
  (399, 52, 'CHITALMARI', 3, '/5/52/399'),
  (400, 52, 'FAKIRHAT', 3, '/5/52/400'),
  (401, 52, 'KACHUA', 3, '/5/52/401'),
  (402, 52, 'MOLLAHAT', 3, '/5/52/402'),
  (403, 52, 'MONGLA', 3, '/5/52/403'),
  (404, 52, 'MORRELGANJ', 3, '/5/52/404'),
  (405, 52, 'RAMPAL', 3, '/5/52/405'),
  (406, 52, 'SARANKHOLA', 3, '/5/52/406'),
  (407, 53, 'ALAMDANGA', 3, '/5/53/407'),
  (408, 53, 'CHUADANGA SADAR', 3, '/5/53/408'),
  (409, 53, 'DAMURHUDA', 3, '/5/53/409'),
  (410, 53, 'JIBAN NAGAR', 3, '/5/53/410'),
  (411, 54, 'ABHAYNAGAR', 3, '/5/54/411'),
  (412, 54, 'BAGHER PARA', 3, '/5/54/412'),
  (413, 54, 'CHAUGACHHA', 3, '/5/54/413'),
  (414, 54, 'JHIKARGACHHA', 3, '/5/54/414'),
  (415, 54, 'KESHABPUR', 3, '/5/54/415'),
  (416, 54, 'JESSORE SADAR', 3, '/5/54/416'),
  (417, 54, 'MANIRAMPUR', 3, '/5/54/417'),
  (418, 54, 'SHARSHA', 3, '/5/54/418'),
  (419, 55, 'HARINAKUNDA', 3, '/5/55/419'),
  (420, 55, 'JHENAIDAH SADAR', 3, '/5/55/420'),
  (421, 55, 'KALIGANJ', 3, '/5/55/421'),
  (422, 55, 'KOTCHANDPUR', 3, '/5/55/422'),
  (423, 55, 'MAHESHPUR', 3, '/5/55/423'),
  (424, 55, 'SHAILKUPA', 3, '/5/55/424'),
  (425, 56, 'BATIAGHATA', 3, '/5/56/425'),
  (426, 56, 'DACOPE', 3, '/5/56/426'),
  (427, 56, 'DAULATPUR', 3, '/5/56/427'),
  (428, 56, 'DUMURIA', 3, '/5/56/428'),
  (429, 56, 'DIGHALIA', 3, '/5/56/429'),
  (430, 56, 'KHALISHPUR', 3, '/5/56/430'),
  (431, 56, 'KHAN JAHAN ALI', 3, '/5/56/431'),
  (432, 56, 'KHULNA SADAR', 3, '/5/56/432'),
  (433, 56, 'KOYRA', 3, '/5/56/433'),
  (434, 56, 'PAIKGACHHA', 3, '/5/56/434'),
  (435, 56, 'PHULTALA', 3, '/5/56/435'),
  (436, 56, 'RUPSA', 3, '/5/56/436'),
  (437, 56, 'SONADANGA', 3, '/5/56/437'),
  (438, 56, 'TEROKHADA', 3, '/5/56/438'),
  (439, 57, 'BHERAMARA', 3, '/5/57/439'),
  (440, 57, 'DAULATPUR', 3, '/5/57/440'),
  (441, 57, 'KHOKSA', 3, '/5/57/441'),
  (442, 57, 'KUMARKHALI', 3, '/5/57/442'),
  (443, 57, 'KUSHTIA SADAR', 3, '/5/57/443'),
  (444, 57, 'MIRPUR', 3, '/5/57/444'),
  (445, 58, 'MAGURA SADAR', 3, '/5/58/445'),
  (446, 58, 'MOHAMMADPUR', 3, '/5/58/446'),
  (447, 58, 'SHALIKHA', 3, '/5/58/447'),
  (448, 58, 'SREEPUR', 3, '/5/58/448'),
  (449, 59, 'GANGNI', 3, '/5/59/449'),
  (450, 59, 'MUJIB NAGAR', 3, '/5/59/450'),
  (451, 59, 'MEHERPUR SADAR', 3, '/5/59/451'),
  (452, 60, 'KALIA', 3, '/5/60/452'),
  (453, 60, 'LOHAGARA', 3, '/5/60/453'),
  (454, 60, 'NARAIL SADAR', 3, '/5/60/454'),
  (455, 61, 'ASSASUNI', 3, '/5/61/455'),
  (456, 61, 'DEBHATA', 3, '/5/61/456'),
  (457, 61, 'KALAROA', 3, '/5/61/457'),
  (458, 61, 'KALIGANJ', 3, '/5/61/458'),
  (459, 61, 'SATKHIRA SADAR', 3, '/5/61/459'),
  (460, 61, 'SHYAMNAGAR', 3, '/5/61/460'),
  (461, 61, 'TALA', 3, '/5/61/461'),
  (462, 36, 'KAHALOO', 3, '/3/36/462'),
  (463, 36, 'NANDIGRAM', 3, '/3/36/463'),
  (464, 36, 'SARIAKANDI', 3, '/3/36/464'),
  (465, 36, 'SHAJAHANPUR', 3, '/3/36/465'),
  (466, 36, 'SHERPUR', 3, '/3/36/466'),
  (467, 36, 'SHIBGANJ', 3, '/3/36/467'),
  (468, 36, 'SONATOLA', 3, '/3/36/468'),
  (469, 37, 'BHOLAHAT', 3, '/3/37/469'),
  (470, 37, 'GOMASTAPUR', 3, '/3/37/470'),
  (471, 37, 'NACHOLE', 3, '/3/37/471'),
  (472, 37, 'CHAPAI NABABGANJ SADAR', 3, '/3/37/472'),
  (473, 37, 'SHIBGANJ', 3, '/3/37/473'),
  (474, 44, 'BIRAMPUR', 3, '/4/44/474'),
  (475, 44, 'BIRGANJ', 3, '/4/44/475'),
  (476, 44, 'BIRAL', 3, '/4/44/476'),
  (477, 44, 'BOCHAGANJ', 3, '/4/44/477'),
  (478, 44, 'CHIRIRBANDAR', 3, '/4/44/478'),
  (479, 44, 'FULBARI', 3, '/4/44/479'),
  (480, 44, 'GHORAGHAT', 3, '/4/44/480'),
  (481, 44, 'HAKIMPUR', 3, '/4/44/481'),
  (482, 44, 'KAHAROLE', 3, '/4/44/482'),
  (483, 44, 'KHANSAMA', 3, '/4/44/483'),
  (484, 44, 'DINAJPUR SADAR', 3, '/4/44/484'),
  (485, 44, 'NAWABGANJ', 3, '/4/44/485'),
  (486, 44, 'PARBATIPUR', 3, '/4/44/486'),
  (487, 45, 'FULCHHARI', 3, '/4/45/487'),
  (488, 45, 'GAIBANDHA SADAR', 3, '/4/45/488'),
  (489, 45, 'GOBINDAGANJ', 3, '/4/45/489'),
  (490, 45, 'PALASHBARI', 3, '/4/45/490'),
  (491, 45, 'SADULLAPUR', 3, '/4/45/491'),
  (492, 45, 'SAGHATA', 3, '/4/45/492'),
  (493, 45, 'SUNDARGANJ', 3, '/4/45/493'),
  (494, 38, 'AKKELPUR', 3, '/3/38/494'),
  (495, 38, 'JOYPURHAT SADAR', 3, '/3/38/495'),
  (496, 38, 'KALAI', 3, '/3/38/496'),
  (497, 38, 'KHETLAL', 3, '/3/38/497'),
  (498, 38, 'PANCHBIBI', 3, '/3/38/498'),
  (499, 46, 'BHURUNGAMARI', 3, '/4/46/499'),
  (500, 46, 'CHAR RAJIBPUR', 3, '/4/46/500'),
  (501, 46, 'CHILMARI', 3, '/4/46/501'),
  (502, 46, 'PHULBARI', 3, '/4/46/502'),
  (503, 46, 'KURIGRAM SADAR', 3, '/4/46/503'),
  (504, 46, 'NAGESHWARI', 3, '/4/46/504'),
  (505, 46, 'RAJARHAT', 3, '/4/46/505'),
  (506, 46, 'RAUMARI', 3, '/4/46/506'),
  (507, 46, 'ULIPUR', 3, '/4/46/507'),
  (508, 47, 'ADITMARI', 3, '/4/47/508'),
  (509, 47, 'HATIBANDHA', 3, '/4/47/509'),
  (510, 47, 'KALIGANJ', 3, '/4/47/510'),
  (511, 47, 'LALMONIRHAT SADAR', 3, '/4/47/511'),
  (512, 47, 'PATGRAM', 3, '/4/47/512'),
  (513, 39, 'ATRAI', 3, '/3/39/513'),
  (514, 39, 'BADALGACHHI', 3, '/3/39/514'),
  (515, 39, 'DHAMOIRHAT', 3, '/3/39/515'),
  (516, 39, 'MANDA', 3, '/3/39/516'),
  (517, 39, 'MAHADEBPUR', 3, '/3/39/517'),
  (518, 39, 'NAOGAON SADAR', 3, '/3/39/518'),
  (519, 39, 'NIAMATPUR', 3, '/3/39/519'),
  (520, 39, 'PATNITALA', 3, '/3/39/520'),
  (521, 39, 'PORSHA', 3, '/3/39/521'),
  (522, 39, 'RANINAGAR', 3, '/3/39/522'),
  (523, 39, 'SAPAHAR', 3, '/3/39/523'),
  (524, 40, 'BAGATIPARA', 3, '/3/40/524'),
  (525, 40, 'BARAIGRAM', 3, '/3/40/525'),
  (526, 40, 'GURUDASPUR', 3, '/3/40/526'),
  (527, 40, 'LALPUR', 3, '/3/40/527'),
  (528, 40, 'NALDANGA', 3, '/3/40/528'),
  (529, 40, 'NATORE SADAR', 3, '/3/40/529'),
  (530, 40, 'SINGRA', 3, '/3/40/530'),
  (531, 48, 'DIMLA', 3, '/4/48/531'),
  (532, 48, 'DOMAR UPAZILA', 3, '/4/48/532'),
  (533, 48, 'JALDHAKA', 3, '/4/48/533'),
  (534, 48, 'KISHOREGANJ', 3, '/4/48/534'),
  (535, 48, 'NILPHAMARI SADAR', 3, '/4/48/535'),
  (536, 48, 'SAIDPUR UPAZILA', 3, '/4/48/536'),
  (537, 41, 'ATGHARIA', 3, '/3/41/537'),
  (538, 41, 'BERA', 3, '/3/41/538'),
  (539, 41, 'BHANGURA', 3, '/3/41/539'),
  (540, 41, 'CHATMOHAR', 3, '/3/41/540'),
  (541, 41, 'FARIDPUR', 3, '/3/41/541'),
  (542, 41, 'ISHWARDI', 3, '/3/41/542'),
  (543, 41, 'PABNA SADAR', 3, '/3/41/543'),
  (544, 41, 'SANTHIA', 3, '/3/41/544'),
  (545, 41, 'SUJANAGAR', 3, '/3/41/545'),
  (546, 49, 'ATWARI', 3, '/4/49/546'),
  (547, 49, 'BODA', 3, '/4/49/547'),
  (548, 49, 'DEBIGANJ', 3, '/4/49/548'),
  (549, 49, 'PANCHAGARH SADAR', 3, '/4/49/549'),
  (550, 49, 'TENTULIA', 3, '/4/49/550'),
  (551, 42, 'BAGHA', 3, '/3/42/551'),
  (552, 42, 'BAGHMARA', 3, '/3/42/552'),
  (553, 42, 'BOALIA', 3, '/3/42/553'),
  (554, 42, 'CHARGHAT', 3, '/3/42/554'),
  (555, 42, 'DURGAPUR', 3, '/3/42/555'),
  (556, 42, 'GODAGARI', 3, '/3/42/556'),
  (557, 42, 'MATIHAR', 3, '/3/42/557'),
  (558, 42, 'MOHANPUR', 3, '/3/42/558'),
  (559, 42, 'PABA', 3, '/3/42/559'),
  (560, 42, 'PUTHIA', 3, '/3/42/560'),
  (561, 42, 'RAJPARA', 3, '/3/42/561'),
  (562, 42, 'SHAH MAKHDUM', 3, '/3/42/562'),
  (563, 42, 'TANORE', 3, '/3/42/563'),
  (564, 50, 'BADARGANJ', 3, '/4/50/564'),
  (565, 50, 'GANGACHARA', 3, '/4/50/565'),
  (566, 50, 'KAUNIA', 3, '/4/50/566'),
  (567, 50, 'RANGPUR SADAR', 3, '/4/50/567'),
  (568, 50, 'MITHA PUKUR', 3, '/4/50/568'),
  (569, 50, 'PIRGACHHA', 3, '/4/50/569'),
  (570, 50, 'PIRGANJ', 3, '/4/50/570'),
  (571, 50, 'TARAGANJ', 3, '/4/50/571'),
  (572, 43, 'BELKUCHI', 3, '/3/43/572'),
  (573, 43, 'CHAUHALI', 3, '/3/43/573'),
  (574, 43, 'KAMARKHANDA', 3, '/3/43/574'),
  (575, 43, 'KAZIPUR', 3, '/3/43/575'),
  (576, 43, 'ROYGANJ', 3, '/3/43/576'),
  (577, 43, 'SHAHJADPUR', 3, '/3/43/577'),
  (578, 43, 'SIRAJGANJ SADAR', 3, '/3/43/578'),
  (579, 43, 'TARASH', 3, '/3/43/579'),
  (580, 43, 'ULLAH PARA', 3, '/3/43/580'),
  (581, 51, 'BALIADANGI', 3, '/4/51/581'),
  (582, 51, 'HARIPUR', 3, '/4/51/582'),
  (583, 51, 'PIRGANJ', 3, '/4/51/583'),
  (584, 51, 'RANISANKAIL', 3, '/4/51/584'),
  (585, 51, 'THAKURGAON SADAR', 3, '/4/51/585'),
  (586, 68, 'AJMIRIGANJ', 3, '/7/68/586'),
  (587, 68, 'BAHUBAL', 3, '/7/68/587'),
  (588, 68, 'BANIACHONG', 3, '/7/68/588'),
  (589, 68, 'CHUNARUGHAT', 3, '/7/68/589'),
  (590, 68, 'HABIGANJ SADAR', 3, '/7/68/590'),
  (591, 68, 'LAKHAI', 3, '/7/68/591'),
  (592, 68, 'MADHABPUR', 3, '/7/68/592'),
  (593, 68, 'NABIGANJ', 3, '/7/68/593'),
  (594, 69, 'BARLEKHA', 3, '/7/69/594'),
  (595, 69, 'JURI', 3, '/7/69/595'),
  (596, 69, 'KAMALGANJ', 3, '/7/69/596'),
  (597, 69, 'KULAURA', 3, '/7/69/597'),
  (598, 69, 'MAULVIBAZAR SADAR', 3, '/7/69/598'),
  (599, 69, 'RAJNAGAR', 3, '/7/69/599'),
  (600, 69, 'SREEMANGAL', 3, '/7/69/600'),
  (601, 70, 'BISHWAMBARPUR', 3, '/7/70/601'),
  (602, 70, 'CHHATAK', 3, '/7/70/602'),
  (603, 70, 'DAKSHIN SUNAMGANJ', 3, '/7/70/603'),
  (604, 70, 'DERAI', 3, '/7/70/604'),
  (605, 70, 'DHARAMPASHA', 3, '/7/70/605'),
  (606, 70, 'DOWARABAZAR', 3, '/7/70/606'),
  (607, 70, 'JAGANNATHPUR', 3, '/7/70/607'),
  (608, 70, 'JAMALGANJ', 3, '/7/70/608'),
  (609, 70, 'SULLA', 3, '/7/70/609'),
  (610, 70, 'SUNAMGANJ SADAR', 3, '/7/70/610'),
  (611, 70, 'TAHIRPUR', 3, '/7/70/611'),
  (612, 71, 'BALAGANJ', 3, '/7/71/612'),
  (613, 71, 'BEANI BAZAR', 3, '/7/71/613'),
  (614, 71, 'BISHWANATH', 3, '/7/71/614'),
  (615, 71, 'COMPANIGANJ', 3, '/7/71/615'),
  (616, 71, 'DAKSHIN SURMA', 3, '/7/71/616'),
  (617, 71, 'FENCHUGANJ', 3, '/7/71/617'),
  (618, 71, 'GOLAPGANJ', 3, '/7/71/618'),
  (619, 71, 'GOWAINGHAT', 3, '/7/71/619'),
  (620, 71, 'JAINTIAPUR', 3, '/7/71/620'),
  (621, 71, 'KANAIGHAT', 3, '/7/71/621'),
  (622, 71, 'SYLHET SADAR', 3, '/7/71/622'),
  (623, 71, 'ZAKIGANJ', 3, '/7/71/623'),
  (624, 160, 'AMTALI PAURASAVA', 4, '/6/62/160/624'),
  (625, 162, 'BARGUNA PAURASAVA', 4, '/6/62/162/625'),
  (626, 163, 'BETAGI PAURASAVA', 4, '/6/62/163/626'),
  (627, 164, 'PATHARGHATA PAURASAVA', 4, '/6/62/164/627'),
  (628, 168, 'BAKERGANJ PAURASAVA', 4, '/6/63/168/628'),
  (629, 169, 'BANARI PARA PAURASAVA', 4, '/6/63/169/629'),
  (630, 170, 'GAURNADI PAURASAVA', 4, '/6/63/170/630'),
  (631, 172, 'BARISAL CITY CORP.', 4, '/6/63/172/631'),
  (632, 173, 'MHENDIGANJ PAURASAVA', 4, '/6/63/173/632'),
  (633, 174, 'MULADI PAURASHAVA', 4, '/6/63/174/633'),
  (634, 176, 'BHOLA PAURASAVA', 4, '/6/64/176/634'),
  (635, 177, 'BURHANUDDIN PAURASAVA', 4, '/6/64/177/635'),
  (636, 178, 'CHAR FASSON PAURASAVA', 4, '/6/64/178/636'),
  (637, 179, 'DAULATKHAN PAURASAVA', 4, '/6/64/179/637'),
  (638, 180, 'LALMOHAN PAURASAVA', 4, '/6/64/180/638'),
  (639, 183, 'JHALOKATI PAURASAVA', 4, '/6/65/183/639'),
  (640, 185, 'NALCHITY PAURASAVA', 4, '/6/65/185/640'),
  (641, 187, 'BAUPHAL PAURASHAVA', 4, '/6/66/187/641'),
  (642, 190, 'GALACHIPA PAURASAVA', 4, '/6/66/190/642'),
  (643, 191, 'KALAPARA PAURASAVA', 4, '/6/66/191/643'),
  (644, 191, 'KUAKATA PAURASAVA', 4, '/6/66/191/644'),
  (645, 193, 'PATUAKHALI PAURASHAVA', 4, '/6/66/193/645'),
  (646, 197, 'MATHBARIA PAURASAVA', 4, '/6/67/197/646'),
  (647, 199, 'PIROJPUR PAURASAVA', 4, '/6/67/199/647'),
  (648, 200, 'SWARUPKATI PAURASAVA', 4, '/6/67/200/648'),
  (649, 73, 'BANDARBAN PAURASHAVA', 4, '/2/25/73/649'),
  (650, 74, 'LAMA PAURASHAVA', 4, '/2/25/74/650'),
  (651, 79, 'AKHAURA PAURASAVA', 4, '/2/26/79/651'),
  (652, 82, 'BRAHMANBARIA PAURASAVA', 4, '/2/26/82/652'),
  (653, 84, 'KASBA PAURASAVA', 4, '/2/26/84/653'),
  (654, 85, 'NABINAGAR PAURASAVA', 4, '/2/26/85/654'),
  (655, 88, 'CHANDPUR PAURASAVA', 4, '/2/27/88/655'),
  (656, 89, 'FARIDGANJ PAURASHAVA', 4, '/2/27/89/656'),
  (657, 91, 'HAJIGANJ PAURASAVA', 4, '/2/27/91/657'),
  (658, 92, 'KACHUA PAURASAVA', 4, '/2/27/92/658'),
  (659, 93, 'MATLAB PAURASAVA', 4, '/2/27/93/659'),
  (660, 94, 'SENGARCHAR PAURASAVA', 4, '/2/27/94/660'),
  (661, 95, 'SHAHRASTI PAURASAVA', 4, '/2/27/95/661'),
  (662, 97, 'CHITTAGONG CITY CORP.', 4, '/2/28/97/662'),
  (663, 98, 'BANSHKHALI PAURASHAVA', 4, '/2/28/98/663'),
  (664, 99, 'CHITTAGONG CITY CORP.', 4, '/2/28/99/664'),
  (665, 100, 'BOALKHALI PAURASHAVA', 4, '/2/28/100/665'),
  (666, 101, 'CHANDANAISH PAURASHAVA', 4, '/2/28/101/666'),
  (667, 102, 'CHITTAGONG CITY CORP.', 4, '/2/28/102/667'),
  (668, 103, 'CHITTAGONG CITY CORP.', 4, '/2/28/103/668'),
  (669, 104, 'CHITTAGONG CITY CORP.', 4, '/2/28/104/669'),
  (670, 105, 'FATIKCHHARI PAURASAVA', 4, '/2/28/105/670'),
  (671, 106, 'CHITTAGONG CITY CORP.', 4, '/2/28/106/671'),
  (672, 108, 'CHITTAGONG CITY CORP.', 4, '/2/28/108/672'),
  (673, 109, 'CHITTAGONG CITY CORP.', 4, '/2/28/109/673'),
  (674, 111, 'BAROIAR HAT PAURASHAVA', 4, '/2/28/111/674'),
  (675, 111, 'MIRSHARAI PAURASHAVA', 4, '/2/28/111/675'),
  (676, 112, 'CHITTAGONG CITY CORP.', 4, '/2/28/112/676'),
  (677, 113, 'CHITTAGONG CITY CORP.', 4, '/2/28/113/677'),
  (678, 114, 'PATIYA PAURASHAVA', 4, '/2/28/114/678'),
  (679, 115, 'CHITTAGONG CITY CORP.', 4, '/2/28/115/679'),
  (680, 116, 'RANGUNIA PAURASHAVA', 4, '/2/28/116/680'),
  (681, 117, 'RAOZAN PAURASHAVA', 4, '/2/28/117/681'),
  (682, 118, 'SANDWIP PAURASHAVA', 4, '/2/28/118/682'),
  (683, 119, 'SATKANIA PAURASHAVA', 4, '/2/28/119/683'),
  (684, 120, 'SITAKUNDA PAURASHAVA', 4, '/2/28/120/684'),
  (685, 121, 'BARURA PAURASAVA', 4, '/2/29/121/685'),
  (686, 121, 'COMILLA CITY CORP.', 4, '/2/29/121/686'),
  (687, 124, 'CHANDINA PAURASAVA', 4, '/2/29/124/687'),
  (688, 125, 'CHAUDDAGRAM PAURASHAVA', 4, '/2/29/125/688'),
  (689, 127, 'DAUDKANDI PAURASAVA', 4, '/2/29/127/689'),
  (690, 128, 'DEBIDWAR PAURASHAVA', 4, '/2/29/128/690'),
  (691, 129, 'HOMNA PAURASHAVA', 4, '/2/29/129/691'),
  (692, 131, 'LAKSAM PAURASAVA', 4, '/2/29/131/692'),
  (693, 135, 'NANGALKOT PAURASHAVA', 4, '/2/29/135/693'),
  (694, 137, 'CHAKARIA PAURASHAVA', 4, '/2/30/137/694'),
  (695, 138, 'COX''S BAZAR PAURASHAVA', 4, '/2/30/138/695'),
  (696, 140, 'MAHESHKHALI PAURASHAVA', 4, '/2/30/140/696'),
  (697, 143, 'TEKNAF PAURASHAVA', 4, '/2/30/143/697'),
  (698, 145, 'CHHAGALNAIYA PAURASHAVA', 4, '/2/31/145/698'),
  (699, 146, 'DAGANBHUIYAN PAURASAVA', 4, '/2/31/146/699'),
  (700, 147, 'FENI PAURASAVA', 4, '/2/31/147/700'),
  (701, 149, 'PARSHURAM PAURASHAVA', 4, '/2/31/149/701'),
  (702, 150, 'SONAGAZI PAURASHAVA', 4, '/2/31/150/702'),
  (703, 152, 'KHAGRACHHARI PAURASAVA', 4, '/2/32/152/703'),
  (704, 156, 'MATIRANGA PAURASHAVA', 4, '/2/32/156/704'),
  (705, 158, 'RAMGARH PAURASHAVA', 4, '/2/32/158/705'),
  (706, 309, 'LAKSHMIPUR PAURASAVA', 4, '/2/33/309/706'),
  (707, 310, 'ROYPUR PAURASAVA', 4, '/2/33/310/707'),
  (708, 311, 'RAMGANJ PAURASAVA', 4, '/2/33/311/708'),
  (709, 312, 'RAMGATI PAURASHAVA', 4, '/2/33/312/709'),
  (710, 313, 'CHAUMOHANI PAURASAVA', 4, '/2/34/313/710'),
  (711, 314, 'CHATKHIL PAURASAVA', 4, '/2/34/314/711'),
  (712, 315, 'BASURHAT PAURASAVA', 4, '/2/34/315/712'),
  (713, 316, 'HATIYA PAURASHAVA', 4, '/2/34/316/713'),
  (714, 317, 'KABIRHAT PAURASAVA', 4, '/2/34/317/714'),
  (715, 318, 'SENBAGH PAURASHAVA', 4, '/2/34/318/715'),
  (716, 319, 'SONAIMURI PAURASAVA', 4, '/2/34/319/716'),
  (717, 321, 'NOAKHALI PAURASAVA', 4, '/2/34/321/717'),
  (718, 322, 'BAGHAICHHARI PAURASHAVA', 4, '/2/35/322/718'),
  (719, 331, 'RANGAMATI PAURASHAVA', 4, '/2/35/331/719'),
  (720, 202, 'DHAKA DAKSHIN CITY CORP.', 4, '/1/8/202/720'),
  (721, 202, 'DHAKA UTTAR CITY CORP.', 4, '/1/8/202/721'),
  (722, 212, 'DHAMRAI PAURASHAVA', 4, '/1/8/212/722'),
  (723, 213, 'DOHAR PAURASHAVA', 4, '/1/8/213/723'),
  (724, 238, 'SAVAR PAURASHAVA', 4, '/1/8/238/724'),
  (725, 253, 'BHANGA PAURASHAVA', 4, '/1/9/253/725'),
  (726, 254, 'BOALMARI PAURASHAVA', 4, '/1/9/254/726'),
  (727, 256, 'FARIDPUR PAURASHAVA', 4, '/1/9/256/727'),
  (728, 257, 'MADHUKHALI PAURASAVA', 4, '/1/9/257/728'),
  (729, 258, 'NAGARKANDA PAURASHAVA', 4, '/1/9/258/729'),
  (730, 261, 'GAZIPUR CITY CORPORATION', 4, '/1/10/261/730'),
  (731, 262, 'KALIAKAIR PAURASHAVA', 4, '/1/10/262/731'),
  (732, 263, 'KALIGANJ PAURASHAVA', 4, '/1/10/263/732'),
  (733, 265, 'SREEPUR PAURASHAVA', 4, '/1/10/265/733'),
  (734, 266, 'GOPALGANJ PAURASHAVA', 4, '/1/11/266/734'),
  (735, 268, 'KOTALIPARA PAURASHAVA', 4, '/1/11/268/735'),
  (736, 269, 'MUKSUDPUR PAURASHAVA', 4, '/1/11/269/736'),
  (737, 270, 'TUNGIPARA PAURASHAVA', 4, '/1/11/270/737'),
  (738, 271, 'BAKSHIGANJ PAURASHAVA', 4, '/1/12/271/738'),
  (739, 272, 'DEWANGANJ PAURASHAVA', 4, '/1/12/272/739'),
  (740, 273, 'ISLAMPUR PAURASHAVA', 4, '/1/12/273/740'),
  (741, 274, 'JAMALPUR PAURASHAVA', 4, '/1/12/274/741'),
  (742, 275, 'MADARGANJ PAURASHAVA', 4, '/1/12/275/742'),
  (743, 276, 'MELANDAHA PAURASHAVA', 4, '/1/12/276/743'),
  (744, 277, 'SARISHABARI PAURASHAVA', 4, '/1/12/277/744'),
  (745, 279, 'BAJITPUR PAURASHAVA', 4, '/1/13/279/745'),
  (746, 280, 'BHAIRAB PAURASHAVA', 4, '/1/13/280/746'),
  (747, 281, 'HOSSAINPUR PAURASHAVA', 4, '/1/13/281/747'),
  (748, 283, 'KARIMGANJ PAURASHAVA', 4, '/1/13/283/748'),
  (749, 284, 'KATIADI PAURASHAVA', 4, '/1/13/284/749'),
  (750, 285, 'KISHOREGANJ PAURASHAVA', 4, '/1/13/285/750'),
  (751, 286, 'KULIAR CHAR PAURASHAVA', 4, '/1/13/286/751'),
  (752, 289, 'PAKUNDIA PAURASHAVA', 4, '/1/13/289/752'),
  (753, 291, 'KALKINI PAURASHAVA', 4, '/1/14/291/753'),
  (754, 292, 'MADARIPUR PAURASHAVA', 4, '/1/14/292/754'),
  (755, 293, 'RAJOIR PAURASHAVA', 4, '/1/14/293/755'),
  (756, 294, 'SHIBCHAR PAURASHAVA', 4, '/1/14/294/756'),
  (757, 298, 'MANIKGANJ PAURASHAVA', 4, '/1/15/298/757'),
  (758, 301, 'SINGAIR PAURASHAVA', 4, '/1/15/301/758'),
  (759, 304, 'MIRKADIM PAURASHAVA', 4, '/1/16/304/759'),
  (760, 304, 'MUNSHIGANJ PAURASHAVA', 4, '/1/16/304/760'),
  (761, 308, 'BHALUKA PAURASHAVA', 4, '/1/17/308/761'),
  (762, 333, 'FULBARIA PAURASHAVA', 4, '/1/17/333/762'),
  (763, 334, 'GAFFARGAON PAURASHAVA', 4, '/1/17/334/763'),
  (764, 335, 'GAURIPUR PAURASHAVA', 4, '/1/17/335/764'),
  (765, 337, 'ISHWARGANJ PAURASHAVA', 4, '/1/17/337/765'),
  (766, 338, 'MYMENSINGH PAURASHAVA', 4, '/1/17/338/766'),
  (767, 339, 'MUKTAGACHHA PAURASHAVA', 4, '/1/17/339/767'),
  (768, 340, 'NANDAIL PAURASHAVA', 4, '/1/17/340/768'),
  (769, 341, 'PHULPUR PAURASHAVA', 4, '/1/17/341/769'),
  (770, 343, 'TRISHAL PAURASHAVA', 4, '/1/17/343/770'),
  (771, 344, 'ARAIHAZAR PAURASHAVA', 4, '/1/18/344/771'),
  (772, 344, 'GOPALDI PAURASHAVA', 4, '/1/18/344/772'),
  (773, 345, 'SONARGAON PAURASHAVA', 4, '/1/18/345/773'),
  (774, 347, 'NARAYANGANJ CITY CORP.', 4, '/1/18/347/774'),
  (775, 348, 'KANCHAN PAURASHAVA', 4, '/1/18/348/775'),
  (776, 348, 'TARABO PAURASHAVA', 4, '/1/18/348/776'),
  (777, 350, 'MANOHARDI PAURASHAVA', 4, '/1/19/350/777'),
  (778, 351, 'MADHABDI PAURASHAVA', 4, '/1/19/351/778'),
  (779, 351, 'NARSINGDI PAURASHAVA', 4, '/1/19/351/779'),
  (780, 352, 'GHORASHAL PAURASHAVA', 4, '/1/19/352/780'),
  (781, 353, 'ROYPURA PAURASHAVA', 4, '/1/19/353/781'),
  (782, 354, 'SHIBPUR PAURASHAVA', 4, '/1/19/354/782'),
  (783, 357, 'DURGAPUR PAURASAVA', 4, '/1/20/357/783'),
  (784, 360, 'KENDUA PAURASAVA', 4, '/1/20/360/784'),
  (785, 361, 'MADAN PAURASAVA', 4, '/1/20/361/785'),
  (786, 362, 'MOHANGANJ PAURASAVA', 4, '/1/20/362/786'),
  (787, 363, 'NETROKONA PAURASAVA', 4, '/1/20/363/787'),
  (788, 366, 'GOALANDAGHAT PAURASHAVA', 4, '/1/21/366/788'),
  (789, 368, 'PANGSHA PAURASHAVA', 4, '/1/21/368/789'),
  (790, 369, 'RAJBARI PAURASHAVA', 4, '/1/21/369/790'),
  (791, 370, 'BHEDARGANJ PAURASHAVA', 4, '/1/22/370/791'),
  (792, 371, 'DAMUDYA PAURASHAVA', 4, '/1/22/371/792'),
  (793, 373, 'NARIA PAURASHAVA', 4, '/1/22/373/793'),
  (794, 374, 'SHARIATPUR PAURASHAVA', 4, '/1/22/374/794'),
  (795, 375, 'ZANJIRA PAURASHAVA', 4, '/1/22/375/795'),
  (796, 377, 'NAKLA PAURASHAVA', 4, '/1/23/377/796'),
  (797, 378, 'NALITABARI PAURASHAVA', 4, '/1/23/378/797'),
  (798, 379, 'SHERPUR PAURASHAVA', 4, '/1/23/379/798'),
  (799, 380, 'SREEBARDI PAURASHAVA', 4, '/1/23/380/799'),
  (800, 381, 'BASAIL PAURASHAVA', 4, '/1/24/381/800'),
  (801, 382, 'BHUAPUR PAURASHAVA', 4, '/1/24/382/801'),
  (802, 384, 'DHANBARI PAURASAVA', 4, '/1/24/384/802'),
  (803, 385, 'GHATAIL PAURASHAVA', 4, '/1/24/385/803'),
  (804, 386, 'GOPALPUR PAURASHAVA', 4, '/1/24/386/804'),
  (805, 387, 'ELENGA PAURASHAVA', 4, '/1/24/387/805'),
  (806, 387, 'KALIHATI PAURASHAVA', 4, '/1/24/387/806'),
  (807, 388, 'MADHUPUR PAURASHAVA', 4, '/1/24/388/807'),
  (808, 389, 'MIRZAPUR PAURASHAVA', 4, '/1/24/389/808'),
  (809, 391, 'SAKHIPUR PAURASHAVA', 4, '/1/24/391/809'),
  (810, 392, 'TANGAIL PAURASHAVA', 4, '/1/24/392/810'),
  (811, 398, 'BAGERHAT PAURASAVA', 4, '/5/52/398/811'),
  (812, 403, 'MONGLA PORT PAURASAVA', 4, '/5/52/403/812'),
  (813, 404, 'MORRELGANJ PAURASAVA', 4, '/5/52/404/813'),
  (814, 407, 'ALAMDANGA PAURASAVA', 4, '/5/53/407/814'),
  (815, 408, 'CHUADANGA PAURASAVA', 4, '/5/53/408/815'),
  (816, 409, 'DARSHANA PAURASAVA', 4, '/5/53/409/816'),
  (817, 410, 'JIBAN NAGAR PAURASAVA', 4, '/5/53/410/817'),
  (818, 411, 'NOAPARA PAURASAVA', 4, '/5/54/411/818'),
  (819, 412, 'BAGHER PARA PAURASAVA', 4, '/5/54/412/819'),
  (820, 413, 'CHAUGACHHA PAURASAVA', 4, '/5/54/413/820'),
  (821, 414, 'JHIKARGACHHA PAURASAVA', 4, '/5/54/414/821'),
  (822, 415, 'KESHABPUR PAURASHAVA', 4, '/5/54/415/822'),
  (823, 416, 'JESSORE PAURASAVA', 4, '/5/54/416/823'),
  (824, 417, 'MANIRAMPUR PAURASAVA', 4, '/5/54/417/824'),
  (825, 418, 'BENAPOLE PAURASHAVA', 4, '/5/54/418/825'),
  (826, 419, 'HARINAKUNDA PAURASHAVA', 4, '/5/55/419/826'),
  (827, 420, 'JHENAIDAH PAURASAVA', 4, '/5/55/420/827'),
  (828, 263, 'KALIGANJ PAURASAVA', 4, '/1/10/263/828'),
  (829, 422, 'KOTCHANDPUR PAURASAVA', 4, '/5/55/422/829'),
  (830, 423, 'MAHESHPUR PAURASAVA', 4, '/5/55/423/830'),
  (831, 424, 'SHAILKUPA PAURASAVA', 4, '/5/55/424/831'),
  (832, 426, 'CHALNA PAURASAVA', 4, '/5/56/426/832'),
  (833, 295, 'KHULNA CITY CORP.', 4, '/1/15/295/833'),
  (834, 430, 'KHULNA CITY CORP.', 4, '/5/56/430/834'),
  (835, 431, 'KHULNA CITY CORP.', 4, '/5/56/431/835'),
  (836, 432, 'KHULNA CITY CORP.', 4, '/5/56/432/836'),
  (837, 434, 'PAIKGACHHA PAURASHAVA', 4, '/5/56/434/837'),
  (838, 437, 'KHULNA CITY CORP.', 4, '/5/56/437/838'),
  (839, 439, 'BHERAMARA PAURASHAVA', 4, '/5/57/439/839'),
  (840, 441, 'KHOKSA PAURASHAVA', 4, '/5/57/441/840'),
  (841, 442, 'KUMARKHALI PAURASHAVA', 4, '/5/57/442/841'),
  (842, 443, 'KUSHTIA PAURASHAVA', 4, '/5/57/443/842'),
  (843, 228, 'MIRPUR PAURASHAVA', 4, '/1/8/228/843'),
  (844, 445, 'MAGURA PAURASAVA', 4, '/5/58/445/844'),
  (845, 449, 'GANGNI PAURASHAVA', 4, '/5/59/449/845'),
  (846, 451, 'MEHERPUR PAURASHAVA', 4, '/5/59/451/846'),
  (847, 452, 'KALIA PAURASAVA', 4, '/5/60/452/847'),
  (848, 110, 'LOHAGARA PAURASAVA', 4, '/2/28/110/848'),
  (849, 454, 'NARAIL PAURASAVA', 4, '/5/60/454/849'),
  (850, 457, 'KALAROA PAURASAVA', 4, '/5/61/457/850'),
  (851, 459, 'SATKHIRA PAURASAVA', 4, '/5/61/459/851'),
  (852, 393, 'SANTAHAR PAURASAVA', 4, '/3/36/393/852'),
  (853, 394, 'BOGRA PAURASAVA', 4, '/3/36/394/853'),
  (854, 395, 'DHUNAT PAURASHAVA', 4, '/3/36/395/854'),
  (855, 396, 'DHUPCHANCHIA PAURASAVA', 4, '/3/36/396/855'),
  (856, 396, 'TALORA  PAURASHAVA', 4, '/3/36/396/856'),
  (857, 397, 'GABTALI PAURASHAVA', 4, '/3/36/397/857'),
  (858, 462, 'KAHALOO PAURASHAVA', 4, '/3/36/462/858'),
  (859, 463, 'NANDIGRAM PAURASHAVA', 4, '/3/36/463/859'),
  (860, 464, 'SARIAKANDI PAURASAVA', 4, '/3/36/464/860'),
  (861, 466, 'SHERPUR PAURASAVA', 4, '/3/36/466/861'),
  (862, 467, 'SHIBGANJ PAURASHAVA', 4, '/3/36/467/862'),
  (863, 468, 'SONATOLA PAURASHAVA', 4, '/3/36/468/863'),
  (864, 494, 'AKKELPUR PAURASAVA', 4, '/3/38/494/864'),
  (865, 495, 'JOYPURHAT PAURASAVA', 4, '/3/38/495/865'),
  (866, 496, 'KALAI PAURASAVA', 4, '/3/38/496/866'),
  (867, 497, 'KHETLAL PAURASAVA', 4, '/3/38/497/867'),
  (868, 498, 'PANCHBIBI PAURASAVA', 4, '/3/38/498/868'),
  (869, 515, 'DHAMOIRHAT PAURASAVA', 4, '/3/39/515/869'),
  (870, 518, 'NAOGAON PAURASAVA', 4, '/3/39/518/870'),
  (871, 520, 'NOZIPUR PAURASAVA', 4, '/3/39/520/871'),
  (872, 524, 'BAGATIPARA PAURASAVA', 4, '/3/40/524/872'),
  (873, 525, 'BANPARA PAURASAVA', 4, '/3/40/525/873'),
  (874, 525, 'BARAIGRAM PAURASAVA', 4, '/3/40/525/874'),
  (875, 526, 'GURUDASPUR PAURASAVA', 4, '/3/40/526/875'),
  (876, 527, 'GOPALPUR (LALPUR) PAURASAVA', 4, '/3/40/527/876'),
  (877, 528, 'NALDANGA PAURASAVA', 4, '/3/40/528/877'),
  (878, 529, 'NATORE PAURASAVA', 4, '/3/40/529/878'),
  (879, 530, 'SINGRA PAURASAVA', 4, '/3/40/530/879'),
  (880, 470, 'RAHANPUR PAURASHAVA', 4, '/3/37/470/880'),
  (881, 471, 'NACHOLE PAURASHAVA', 4, '/3/37/471/881'),
  (882, 472, 'CHAPAI NABABGANJ PAURASHAVA', 4, '/3/37/472/882'),
  (883, 467, 'SHIBGANJ PAURASHAVA', 4, '/3/36/467/883'),
  (884, 160, 'Unions of AMTALI Upazila', 4, '/6/62/160/884'),
  (885, 161, 'Unions of BAMNA Upazila', 4, '/6/62/161/885'),
  (886, 162, 'Unions of BARGUNA SADAR Upazila', 4, '/6/62/162/886'),
  (887, 163, 'Unions of BETAGI Upazila', 4, '/6/62/163/887'),
  (888, 164, 'Unions of PATHARGHATA Upazila', 4, '/6/62/164/888'),
  (889, 165, 'Unions of TALTALI Upazila', 4, '/6/62/165/889'),
  (890, 166, 'Unions of AGAILJHARA Upazila', 4, '/6/63/166/890'),
  (891, 167, 'Unions of BABUGANJ Upazila', 4, '/6/63/167/891'),
  (892, 168, 'Unions of BAKERGANJ Upazila', 4, '/6/63/168/892'),
  (893, 169, 'Unions of BANARI PARA Upazila', 4, '/6/63/169/893'),
  (894, 170, 'Unions of GAURNADI Upazila', 4, '/6/63/170/894'),
  (895, 171, 'Unions of HIZLA Upazila', 4, '/6/63/171/895'),
  (896, 172, 'Unions of BARISAL SADAR (KOTWALI) Upazila', 4, '/6/63/172/896'),
  (897, 173, 'Unions of MHENDIGANJ Upazila', 4, '/6/63/173/897'),
  (898, 174, 'Unions of MULADI Upazila', 4, '/6/63/174/898'),
  (899, 175, 'Unions of WAZIRPUR Upazila', 4, '/6/63/175/899'),
  (900, 176, 'Unions of BHOLA SADAR Upazila', 4, '/6/64/176/900'),
  (901, 177, 'Unions of BURHANUDDIN Upazila', 4, '/6/64/177/901'),
  (902, 178, 'Unions of CHAR FASSON Upazila', 4, '/6/64/178/902'),
  (903, 179, 'Unions of DAULAT KHAN Upazila', 4, '/6/64/179/903'),
  (904, 180, 'Unions of LALMOHAN Upazila', 4, '/6/64/180/904'),
  (905, 181, 'Unions of MANPURA Upazila', 4, '/6/64/181/905'),
  (906, 182, 'Unions of TAZUMUDDIN Upazila', 4, '/6/64/182/906'),
  (907, 183, 'Unions of JHALOKATI SADAR Upazila', 4, '/6/65/183/907'),
  (908, 184, 'Unions of KANTHALIA Upazila', 4, '/6/65/184/908'),
  (909, 185, 'Unions of NALCHITY Upazila', 4, '/6/65/185/909'),
  (910, 186, 'Unions of RAJAPUR Upazila', 4, '/6/65/186/910'),
  (911, 187, 'Unions of BAUPHAL Upazila', 4, '/6/66/187/911'),
  (912, 188, 'Unions of DASHMINA Upazila', 4, '/6/66/188/912'),
  (913, 189, 'Unions of DUMKI Upazila', 4, '/6/66/189/913'),
  (914, 190, 'Unions of GALACHIPA Upazila', 4, '/6/66/190/914'),
  (915, 191, 'Unions of KALAPARA Upazila', 4, '/6/66/191/915'),
  (916, 192, 'Unions of MIRZAGANJ Upazila', 4, '/6/66/192/916'),
  (917, 193, 'Unions of PATUAKHALI SADAR Upazila', 4, '/6/66/193/917'),
  (918, 194, 'Unions of RANGABALI Upazila', 4, '/6/66/194/918'),
  (919, 195, 'Unions of BHANDARIA Upazila', 4, '/6/67/195/919'),
  (920, 196, 'Unions of KAWKHALI Upazila', 4, '/6/67/196/920'),
  (921, 197, 'Unions of MATHBARIA Upazila', 4, '/6/67/197/921'),
  (922, 198, 'Unions of NAZIRPUR Upazila', 4, '/6/67/198/922'),
  (923, 199, 'Unions of PIROJPUR SADAR Upazila', 4, '/6/67/199/923'),
  (924, 200, 'Unions of NESARABAD (SWARUPKATI) Upazila', 4, '/6/67/200/924'),
  (925, 201, 'Unions of ZIANAGAR Upazila', 4, '/6/67/201/925'),
  (926, 72, 'Unions of ALIKADAM Upazila', 4, '/2/25/72/926'),
  (927, 73, 'Unions of BANDARBAN SADAR Upazila', 4, '/2/25/73/927'),
  (928, 74, 'Unions of LAMA Upazila', 4, '/2/25/74/928'),
  (929, 75, 'Unions of NAIKHONGCHHARI Upazila', 4, '/2/25/75/929'),
  (930, 76, 'Unions of ROWANGCHHARI Upazila', 4, '/2/25/76/930'),
  (931, 77, 'Unions of RUMA Upazila', 4, '/2/25/77/931'),
  (932, 537, 'ATGHARIA PAURASAVA', 4, '/3/41/537/932'),
  (933, 538, 'BERA PAURASAVA', 4, '/3/41/538/933'),
  (934, 539, 'BHANGURA PAURASAVA', 4, '/3/41/539/934'),
  (935, 540, 'CHATMOHAR PAURASAVA', 4, '/3/41/540/935'),
  (936, 541, 'FARIDPUR PAURASAVA', 4, '/3/41/541/936'),
  (937, 542, 'ISHWARDI PAURASAVA', 4, '/3/41/542/937'),
  (938, 543, 'PABNA PAURASAVA', 4, '/3/41/543/938'),
  (939, 544, 'SANTHIA PAURASAVA', 4, '/3/41/544/939'),
  (940, 545, 'SUJANAGAR PAURASAVA', 4, '/3/41/545/940'),
  (941, 551, 'ARANI PAURASAVA', 4, '/3/42/551/941'),
  (942, 551, 'BAGHA PAURASAVA', 4, '/3/42/551/942'),
  (943, 552, 'BHABANIGONJ PAURASAVA', 4, '/3/42/552/943'),
  (944, 552, 'TAHIRPUR PAURASAVA', 4, '/3/42/552/944'),
  (945, 553, 'RAJSHAHI CITY CORP.', 4, '/3/42/553/945'),
  (946, 554, 'CHARGHAT PAURASAVA', 4, '/3/42/554/946'),
  (947, 357, 'DURGAPUR PAURASAVA', 4, '/1/20/357/947'),
  (948, 556, 'GODAGARI PAURASAVA', 4, '/3/42/556/948'),
  (949, 556, 'KAKANHAT PAURASAVA', 4, '/3/42/556/949'),
  (950, 557, 'RAJSHAHI CITY CORP.', 4, '/3/42/557/950'),
  (951, 558, 'KESHARHAT PAURASAVA', 4, '/3/42/558/951'),
  (952, 559, 'KATAKHALI PAURASAVA', 4, '/3/42/559/952'),
  (953, 559, 'NOAHATA PAURASAVA', 4, '/3/42/559/953'),
  (954, 560, 'PUTHIA PAURASAVA', 4, '/3/42/560/954'),
  (955, 561, 'RAJSHAHI CITY CORP.', 4, '/3/42/561/955'),
  (956, 562, 'RAJSHAHI CITY CORP.', 4, '/3/42/562/956'),
  (957, 563, 'MUNDUMALA PAURASAVA', 4, '/3/42/563/957'),
  (958, 563, 'TANORE PAURASAVA', 4, '/3/42/563/958'),
  (959, 572, 'BELKUCHI PAURASHAVA', 4, '/3/43/572/959'),
  (960, 575, 'KAZIPUR PAURASAVA', 4, '/3/43/575/960'),
  (961, 576, 'ROYGANJ PAURASHAVA', 4, '/3/43/576/961'),
  (962, 577, 'SHAHJADPUR PAURASAVA', 4, '/3/43/577/962'),
  (963, 578, 'SIRAJGANJ PAURASAVA', 4, '/3/43/578/963'),
  (964, 580, 'ULLAH PARA PAURASAVA', 4, '/3/43/580/964'),
  (965, 474, 'BIRAMPUR PAURASAVA', 4, '/4/44/474/965'),
  (966, 475, 'BIRGANJ PAURASHAVA', 4, '/4/44/475/966'),
  (967, 477, 'SETABGANJ PAURASAVA', 4, '/4/44/477/967'),
  (968, 479, 'FULBARI PAURASAVA', 4, '/4/44/479/968'),
  (969, 480, 'GHORAGHAT PAURASHAVA', 4, '/4/44/480/969'),
  (970, 481, 'HAKIMPUR PAURASAVA', 4, '/4/44/481/970'),
  (971, 484, 'DINAJPUR PAURASAVA', 4, '/4/44/484/971'),
  (972, 486, 'PARBATIPUR PAURASAVA', 4, '/4/44/486/972'),
  (973, 488, 'GAIBANDHA PAURASAVA', 4, '/4/45/488/973'),
  (974, 489, 'GOBINDAGANJ PAURASAVA', 4, '/4/45/489/974'),
  (975, 490, 'PALASHBARI PAURASHAV', 4, '/4/45/490/975'),
  (976, 493, 'SUNDARGANJ PAURASHAVA', 4, '/4/45/493/976'),
  (977, 503, 'KURIGRAM PAURASAVA', 4, '/4/46/503/977'),
  (978, 504, 'NAGESHWARI PAURASHAVA', 4, '/4/46/504/978'),
  (979, 507, 'ULIPUR PAURASAVA', 4, '/4/46/507/979'),
  (980, 511, 'LALMONIRHAT PAURASAVA', 4, '/4/47/511/980'),
  (981, 512, 'PATGRAM PAURASAVA', 4, '/4/47/512/981'),
  (982, 532, 'DOMAR PAURASAVA', 4, '/4/48/532/982'),
  (983, 533, 'JALDHAKA PAURASHAVA', 4, '/4/48/533/983'),
  (984, 535, 'NILPHAMARI PAURASAVA', 4, '/4/48/535/984'),
  (985, 536, 'SAIDPUR PAURASAVA', 4, '/4/48/536/985'),
  (986, 547, 'BODA PAURASHAVA', 4, '/4/49/547/986'),
  (987, 549, 'PANCHAGARH PAURASAVA', 4, '/4/49/549/987'),
  (988, 564, 'BADARGANJ PAURASAVA', 4, '/4/50/564/988'),
  (989, 566, 'HARAGACHH (KAUNIA) PAURASAVA', 4, '/4/50/566/989'),
  (990, 567, 'RANGPUR CITY CORPORATION', 4, '/4/50/567/990'),
  (991, 570, 'PIRGANJ PAURASAVA', 4, '/4/50/570/991'),
  (992, 584, 'RANISANKAIL PAURASHAVA', 4, '/4/51/584/992'),
  (993, 585, 'THAKURGAON PAURASAVA', 4, '/4/51/585/993'),
  (994, 586, 'AJMIRIGANJ PAURASHAVA', 4, '/7/68/586/994'),
  (995, 589, 'CHUNARUGHAT PAURASHAVA', 4, '/7/68/589/995'),
  (996, 590, 'HABIGANJ PAURASHAVA', 4, '/7/68/590/996'),
  (997, 590, 'SHAYESTAGANG PAURASHAVA', 4, '/7/68/590/997'),
  (998, 592, 'MADHABPUR PAURASHAVA', 4, '/7/68/592/998'),
  (999, 593, 'NABIGANJ PAURASHAVA', 4, '/7/68/593/999'),
  (1000, 594, 'BARLEKHA PAURASHAVA', 4, '/7/69/594/1000'),
  (1001, 596, 'KAMALGANJ PAURASHAVA', 4, '/7/69/596/1001'),
  (1002, 597, 'KULAURA PAURASHAVA', 4, '/7/69/597/1002'),
  (1003, 598, 'MAULVIBAZAR PAURASHAVA', 4, '/7/69/598/1003'),
  (1004, 600, 'SREEMANGAL PAURASHAVA', 4, '/7/69/600/1004'),
  (1005, 602, 'CHHATAK PAURASHAVA', 4, '/7/70/602/1005'),
  (1006, 604, 'DERAI PAURASHAVA', 4, '/7/70/604/1006'),
  (1007, 607, 'JAGANNATHPUR PAURASHAVA', 4, '/7/70/607/1007'),
  (1008, 610, 'SUNAMGANJ PAURASHAVA', 4, '/7/70/610/1008'),
  (1009, 613, 'BEANI BAZAR PAURASHAVA', 4, '/7/71/613/1009'),
  (1010, 618, 'GOLAPGANJ PAURASHAVA', 4, '/7/71/618/1010'),
  (1011, 621, 'KANAIGHAT PAURASHAVA', 4, '/7/71/621/1011'),
  (1012, 622, 'SYLHET CITY CORP.', 4, '/7/71/622/1012'),
  (1013, 623, 'ZAKIGANJ PAURASHAVA', 4, '/7/71/623/1013'),
  (1014, 78, 'Unions of THANCHI Upazila', 4, '/2/25/78/1014'),
  (1015, 79, 'Unions of AKHAURA Upazila', 4, '/2/26/79/1015'),
  (1016, 80, 'Unions of BANCHHARAMPUR Upazila', 4, '/2/26/80/1016'),
  (1017, 81, 'Unions of BIJOYNAGAR Upazila', 4, '/2/26/81/1017'),
  (1018, 82, 'Unions of BRAHMANBARIA SADAR Upazila', 4, '/2/26/82/1018'),
  (1019, 83, 'Unions of ASHUGANJ Upazila', 4, '/2/26/83/1019'),
  (1020, 84, 'Unions of KASBA Upazila', 4, '/2/26/84/1020'),
  (1021, 85, 'Unions of NABINAGAR Upazila', 4, '/2/26/85/1021'),
  (1022, 86, 'Unions of NASIRNAGAR Upazila', 4, '/2/26/86/1022'),
  (1023, 87, 'Unions of SARAIL Upazila', 4, '/2/26/87/1023'),
  (1024, 88, 'Unions of CHANDPUR SADAR Upazila', 4, '/2/27/88/1024'),
  (1025, 89, 'Unions of FARIDGANJ Upazila', 4, '/2/27/89/1025'),
  (1026, 90, 'Unions of HAIM CHAR Upazila', 4, '/2/27/90/1026'),
  (1027, 91, 'Unions of HAJIGANJ Upazila', 4, '/2/27/91/1027'),
  (1028, 92, 'Unions of KACHUA Upazila', 4, '/2/27/92/1028'),
  (1029, 93, 'Unions of MATLAB DAKSHIN Upazila', 4, '/2/27/93/1029'),
  (1030, 94, 'Unions of MATLAB UTTAR Upazila', 4, '/2/27/94/1030'),
  (1031, 95, 'Unions of SHAHRASTI Upazila', 4, '/2/27/95/1031'),
  (1032, 96, 'Unions of ANOWARA Upazila', 4, '/2/28/96/1032'),
  (1033, 98, 'Unions of BANSHKHALI Upazila', 4, '/2/28/98/1033'),
  (1034, 100, 'Unions of BOALKHALI Upazila', 4, '/2/28/100/1034'),
  (1035, 101, 'Unions of CHANDANAISH Upazila', 4, '/2/28/101/1035'),
  (1036, 105, 'Unions of FATIKCHHARI Upazila', 4, '/2/28/105/1036'),
  (1037, 107, 'Unions of HATHAZARI Upazila', 4, '/2/28/107/1037'),
  (1038, 110, 'Unions of LOHAGARA Upazila', 4, '/2/28/110/1038'),
  (1039, 111, 'Unions of MIRSHARAI Upazila', 4, '/2/28/111/1039'),
  (1040, 114, 'Unions of PATIYA Upazila', 4, '/2/28/114/1040'),
  (1041, 116, 'Unions of RANGUNIA Upazila', 4, '/2/28/116/1041'),
  (1042, 117, 'Unions of RAOZAN Upazila', 4, '/2/28/117/1042'),
  (1043, 118, 'Unions of SANDWIP Upazila', 4, '/2/28/118/1043'),
  (1044, 119, 'Unions of SATKANIA Upazila', 4, '/2/28/119/1044'),
  (1045, 120, 'Unions of SITAKUNDA Upazila', 4, '/2/28/120/1045'),
  (1046, 121, 'Unions of BARURA Upazila', 4, '/2/29/121/1046'),
  (1047, 122, 'Unions of BRAHMAN PARA Upazila', 4, '/2/29/122/1047'),
  (1048, 123, 'Unions of BURICHANG Upazila', 4, '/2/29/123/1048'),
  (1049, 124, 'Unions of CHANDINA Upazila', 4, '/2/29/124/1049'),
  (1050, 125, 'Unions of CHAUDDAGRAM Upazila', 4, '/2/29/125/1050'),
  (1051, 126, 'Unions of COMILLA SADAR DAKSHIN Upazila', 4, '/2/29/126/1051'),
  (1052, 127, 'Unions of DAUDKANDI Upazila', 4, '/2/29/127/1052'),
  (1053, 128, 'Unions of DEBIDWAR Upazila', 4, '/2/29/128/1053'),
  (1054, 129, 'Unions of HOMNA Upazila', 4, '/2/29/129/1054'),
  (1055, 130, 'Unions of COMILLA ADARSHA SADAR Upazila', 4, '/2/29/130/1055'),
  (1056, 131, 'Unions of LAKSAM Upazila', 4, '/2/29/131/1056'),
  (1057, 132, 'Unions of MANOHARGANJ Upazila', 4, '/2/29/132/1057'),
  (1058, 133, 'Unions of MEGHNA Upazila', 4, '/2/29/133/1058'),
  (1059, 134, 'Unions of MURADNAGAR Upazila', 4, '/2/29/134/1059'),
  (1060, 135, 'Unions of NANGALKOT Upazila', 4, '/2/29/135/1060'),
  (1061, 136, 'Unions of TITAS Upazila', 4, '/2/29/136/1061'),
  (1062, 137, 'Unions of CHAKARIA Upazila', 4, '/2/30/137/1062'),
  (1063, 138, 'Unions of COX''S BAZAR SADAR Upazila', 4, '/2/30/138/1063'),
  (1064, 139, 'Unions of KUTUBDIA Upazila', 4, '/2/30/139/1064'),
  (1065, 140, 'Unions of MAHESHKHALI Upazila', 4, '/2/30/140/1065'),
  (1066, 141, 'Unions of PEKUA Upazila', 4, '/2/30/141/1066'),
  (1067, 142, 'Unions of RAMU Upazila', 4, '/2/30/142/1067'),
  (1068, 143, 'Unions of TEKNAF Upazila', 4, '/2/30/143/1068'),
  (1069, 144, 'Unions of UKHIA Upazila', 4, '/2/30/144/1069'),
  (1070, 145, 'Unions of CHHAGALNAIYA Upazila', 4, '/2/31/145/1070'),
  (1071, 146, 'Unions of DAGANBHUIYAN Upazila', 4, '/2/31/146/1071'),
  (1072, 147, 'Unions of FENI SADAR Upazila', 4, '/2/31/147/1072'),
  (1073, 148, 'Unions of FULGAZI Upazila', 4, '/2/31/148/1073'),
  (1074, 149, 'Unions of PARSHURAM Upazila', 4, '/2/31/149/1074'),
  (1075, 150, 'Unions of SONAGAZI Upazila', 4, '/2/31/150/1075'),
  (1076, 151, 'Unions of DIGHINALA Upazila', 4, '/2/32/151/1076'),
  (1077, 152, 'Unions of KHAGRACHHARI SADAR Upazila', 4, '/2/32/152/1077'),
  (1078, 153, 'Unions of LAKSHMICHHARI Upazila', 4, '/2/32/153/1078'),
  (1079, 154, 'Unions of MAHALCHHARI Upazila', 4, '/2/32/154/1079'),
  (1080, 155, 'Unions of MANIKCHHARI Upazila', 4, '/2/32/155/1080'),
  (1081, 156, 'Unions of MATIRANGA Upazila', 4, '/2/32/156/1081'),
  (1082, 157, 'Unions of PANCHHARI Upazila', 4, '/2/32/157/1082'),
  (1083, 158, 'Unions of RAMGARH Upazila', 4, '/2/32/158/1083'),
  (1084, 159, 'Unions of KAMALNAGAR Upazila', 4, '/2/33/159/1084'),
  (1085, 309, 'Unions of LAKSHMIPUR SADAR Upazila', 4, '/2/33/309/1085'),
  (1086, 310, 'Unions of ROYPUR Upazila', 4, '/2/33/310/1086'),
  (1087, 311, 'Unions of RAMGANJ Upazila', 4, '/2/33/311/1087'),
  (1088, 312, 'Unions of RAMGATI Upazila', 4, '/2/33/312/1088'),
  (1089, 313, 'Unions of BEGUMGANJ Upazila', 4, '/2/34/313/1089'),
  (1090, 314, 'Unions of CHATKHIL Upazila', 4, '/2/34/314/1090'),
  (1091, 315, 'Unions of COMPANIGANJ Upazila', 4, '/2/34/315/1091'),
  (1092, 316, 'Unions of HATIYA Upazila', 4, '/2/34/316/1092'),
  (1093, 317, 'Unions of KABIRHAT Upazila', 4, '/2/34/317/1093'),
  (1094, 318, 'Unions of SENBAGH Upazila', 4, '/2/34/318/1094'),
  (1095, 319, 'Unions of SONAIMURI Upazila', 4, '/2/34/319/1095'),
  (1096, 320, 'Unions of SUBARNACHAR Upazila', 4, '/2/34/320/1096'),
  (1097, 321, 'Unions of NOAKHALI SADAR Upazila', 4, '/2/34/321/1097'),
  (1098, 322, 'Unions of BAGHAICHHARI Upazila', 4, '/2/35/322/1098'),
  (1099, 323, 'Unions of BARKAL UPAZILA Upazila', 4, '/2/35/323/1099'),
  (1100, 324, 'Unions of KAWKHALI (BETBUNIA) Upazila', 4, '/2/35/324/1100'),
  (1101, 325, 'Unions of BELAI CHHARI  UPAZI Upazila', 4, '/2/35/325/1101'),
  (1102, 326, 'Unions of KAPTAI  UPAZILA Upazila', 4, '/2/35/326/1102');
INSERT INTO `locations` (`id`, `parent`, `name`, `level`, `path`) VALUES
  (1103, 327, 'Unions of JURAI CHHARI UPAZIL Upazila', 4, '/2/35/327/1103'),
  (1104, 328, 'Unions of LANGADU  UPAZILA Upazila', 4, '/2/35/328/1104'),
  (1105, 329, 'Unions of NANIARCHAR  UPAZILA Upazila', 4, '/2/35/329/1105'),
  (1106, 330, 'Unions of RAJASTHALI  UPAZILA Upazila', 4, '/2/35/330/1106'),
  (1107, 331, 'Unions of RANGAMATI SADAR  UP Upazila', 4, '/2/35/331/1107'),
  (1108, 203, 'Unions of BADDA Upazila', 4, '/1/8/203/1108'),
  (1109, 209, 'Unions of DAKSHINKHAN Upazila', 4, '/1/8/209/1109'),
  (1110, 211, 'Unions of DEMRA Upazila', 4, '/1/8/211/1110'),
  (1111, 212, 'Unions of DHAMRAI Upazila', 4, '/1/8/212/1111'),
  (1112, 213, 'Unions of DOHAR Upazila', 4, '/1/8/213/1112'),
  (1113, 215, 'Unions of BHATARA Upazila', 4, '/1/8/215/1113'),
  (1114, 218, 'Unions of JATRABARI Upazila', 4, '/1/8/218/1114'),
  (1115, 220, 'Unions of KADAMTALI Upazila', 4, '/1/8/220/1115'),
  (1116, 222, 'Unions of KAMRANGIR CHAR Upazila', 4, '/1/8/222/1116'),
  (1117, 223, 'Unions of KHILGAON Upazila', 4, '/1/8/223/1117'),
  (1118, 224, 'Unions of KHILKHET Upazila', 4, '/1/8/224/1118'),
  (1119, 225, 'Unions of KERANIGANJ Upazila', 4, '/1/8/225/1119'),
  (1120, 230, 'Unions of MUGDA PARA Upazila', 4, '/1/8/230/1120'),
  (1121, 231, 'Unions of NAWABGANJ Upazila', 4, '/1/8/231/1121'),
  (1122, 236, 'Unions of SABUJBAGH Upazila', 4, '/1/8/236/1122'),
  (1123, 238, 'Unions of SAVAR Upazila', 4, '/1/8/238/1123'),
  (1124, 247, 'Unions of TURAG Upazila', 4, '/1/8/247/1124'),
  (1125, 250, 'Unions of UTTAR KHAN Upazila', 4, '/1/8/250/1125'),
  (1126, 252, 'Unions of ALFADANGA Upazila', 4, '/1/9/252/1126'),
  (1127, 253, 'Unions of BHANGA Upazila', 4, '/1/9/253/1127'),
  (1128, 254, 'Unions of BOALMARI Upazila', 4, '/1/9/254/1128'),
  (1129, 255, 'Unions of CHAR BHADRASAN Upazila', 4, '/1/9/255/1129'),
  (1130, 256, 'Unions of FARIDPUR SADAR Upazila', 4, '/1/9/256/1130'),
  (1131, 257, 'Unions of MADHUKHALI Upazila', 4, '/1/9/257/1131'),
  (1132, 258, 'Unions of NAGARKANDA Upazila', 4, '/1/9/258/1132'),
  (1133, 259, 'Unions of SADARPUR Upazila', 4, '/1/9/259/1133'),
  (1134, 260, 'Unions of SALTHA Upazila', 4, '/1/9/260/1134'),
  (1135, 261, 'Unions of GAZIPUR SADAR Upazila', 4, '/1/10/261/1135'),
  (1136, 262, 'Unions of KALIAKAIR Upazila', 4, '/1/10/262/1136'),
  (1137, 263, 'Unions of KALIGANJ Upazila', 4, '/1/10/263/1137'),
  (1138, 264, 'Unions of KAPASIA Upazila', 4, '/1/10/264/1138'),
  (1139, 265, 'Unions of SREEPUR Upazila', 4, '/1/10/265/1139'),
  (1140, 266, 'Unions of GOPALGANJ SADAR Upazila', 4, '/1/11/266/1140'),
  (1141, 267, 'Unions of KASHIANI Upazila', 4, '/1/11/267/1141'),
  (1142, 268, 'Unions of KOTALIPARA Upazila', 4, '/1/11/268/1142'),
  (1143, 269, 'Unions of MUKSUDPUR Upazila', 4, '/1/11/269/1143'),
  (1144, 270, 'Unions of TUNGIPARA Upazila', 4, '/1/11/270/1144'),
  (1145, 271, 'Unions of BAKSHIGANJ Upazila', 4, '/1/12/271/1145'),
  (1146, 272, 'Unions of DEWANGANJ Upazila', 4, '/1/12/272/1146'),
  (1147, 273, 'Unions of ISLAMPUR Upazila', 4, '/1/12/273/1147'),
  (1148, 274, 'Unions of JAMALPUR SADAR Upazila', 4, '/1/12/274/1148'),
  (1149, 275, 'Unions of MADARGANJ Upazila', 4, '/1/12/275/1149'),
  (1150, 276, 'Unions of MELANDAHA Upazila', 4, '/1/12/276/1150'),
  (1151, 277, 'Unions of SARISHABARI UPAZILA Upazila', 4, '/1/12/277/1151'),
  (1152, 278, 'Unions of AUSTAGRAM Upazila', 4, '/1/13/278/1152'),
  (1153, 279, 'Unions of BAJITPUR Upazila', 4, '/1/13/279/1153'),
  (1154, 280, 'Unions of BHAIRAB Upazila', 4, '/1/13/280/1154'),
  (1155, 281, 'Unions of HOSSAINPUR Upazila', 4, '/1/13/281/1155'),
  (1156, 282, 'Unions of ITNA Upazila', 4, '/1/13/282/1156'),
  (1157, 283, 'Unions of KARIMGANJ Upazila', 4, '/1/13/283/1157'),
  (1158, 284, 'Unions of KATIADI Upazila', 4, '/1/13/284/1158'),
  (1159, 285, 'Unions of KISHOREGANJ SADAR Upazila', 4, '/1/13/285/1159'),
  (1160, 286, 'Unions of KULIAR CHAR Upazila', 4, '/1/13/286/1160'),
  (1161, 287, 'Unions of MITHAMAIN Upazila', 4, '/1/13/287/1161'),
  (1162, 288, 'Unions of NIKLI Upazila', 4, '/1/13/288/1162'),
  (1163, 289, 'Unions of PAKUNDIA Upazila', 4, '/1/13/289/1163'),
  (1164, 290, 'Unions of TARAIL Upazila', 4, '/1/13/290/1164'),
  (1165, 291, 'Unions of KALKINI Upazila', 4, '/1/14/291/1165'),
  (1166, 292, 'Unions of MADARIPUR SADAR Upazila', 4, '/1/14/292/1166'),
  (1167, 293, 'Unions of RAJOIR Upazila', 4, '/1/14/293/1167'),
  (1168, 294, 'Unions of SHIBCHAR Upazila', 4, '/1/14/294/1168'),
  (1169, 295, 'Unions of DAULATPUR Upazila', 4, '/1/15/295/1169'),
  (1170, 296, 'Unions of GHIOR Upazila', 4, '/1/15/296/1170'),
  (1171, 297, 'Unions of HARIRAMPUR Upazila', 4, '/1/15/297/1171'),
  (1172, 298, 'Unions of MANIKGANJ SADAR Upazila', 4, '/1/15/298/1172'),
  (1173, 299, 'Unions of SATURIA Upazila', 4, '/1/15/299/1173'),
  (1174, 300, 'Unions of SHIBALAYA Upazila', 4, '/1/15/300/1174'),
  (1175, 301, 'Unions of SINGAIR Upazila', 4, '/1/15/301/1175'),
  (1176, 302, 'Unions of GAZARIA Upazila', 4, '/1/16/302/1176'),
  (1177, 303, 'Unions of LOHAJANG Upazila', 4, '/1/16/303/1177'),
  (1178, 304, 'Unions of MUNSHIGANJ SADAR Upazila', 4, '/1/16/304/1178'),
  (1179, 305, 'Unions of SERAJDIKHAN Upazila', 4, '/1/16/305/1179'),
  (1180, 306, 'Unions of SREENAGAR Upazila', 4, '/1/16/306/1180'),
  (1181, 307, 'Unions of TONGIBARI Upazila', 4, '/1/16/307/1181'),
  (1182, 308, 'Unions of BHALUKA Upazila', 4, '/1/17/308/1182'),
  (1183, 332, 'Unions of DHOBAURA Upazila', 4, '/1/17/332/1183'),
  (1184, 333, 'Unions of FULBARIA Upazila', 4, '/1/17/333/1184'),
  (1185, 334, 'Unions of GAFFARGAON Upazila', 4, '/1/17/334/1185'),
  (1186, 335, 'Unions of GAURIPUR Upazila', 4, '/1/17/335/1186'),
  (1187, 336, 'Unions of HALUAGHAT Upazila', 4, '/1/17/336/1187'),
  (1188, 337, 'Unions of ISHWARGANJ Upazila', 4, '/1/17/337/1188'),
  (1189, 338, 'Unions of MYMENSINGH SADAR Upazila', 4, '/1/17/338/1189'),
  (1190, 339, 'Unions of MUKTAGACHHA Upazila', 4, '/1/17/339/1190'),
  (1191, 340, 'Unions of NANDAIL Upazila', 4, '/1/17/340/1191'),
  (1192, 341, 'Unions of PHULPUR Upazila', 4, '/1/17/341/1192'),
  (1193, 342, 'Unions of TARAKANDA Upazila', 4, '/1/17/342/1193'),
  (1194, 343, 'Unions of TRISHAL Upazila', 4, '/1/17/343/1194'),
  (1195, 344, 'Unions of ARAIHAZAR Upazila', 4, '/1/18/344/1195'),
  (1196, 345, 'Unions of SONARGAON Upazila', 4, '/1/18/345/1196'),
  (1197, 346, 'Unions of BANDAR Upazila', 4, '/1/18/346/1197'),
  (1198, 347, 'Unions of NARAYANGANJ SADAR Upazila', 4, '/1/18/347/1198'),
  (1199, 348, 'Unions of RUPGANJ Upazila', 4, '/1/18/348/1199'),
  (1200, 349, 'Unions of BELABO Upazila', 4, '/1/19/349/1200'),
  (1201, 350, 'Unions of MANOHARDI Upazila', 4, '/1/19/350/1201'),
  (1202, 351, 'Unions of NARSINGDI SADAR Upazila', 4, '/1/19/351/1202'),
  (1203, 352, 'Unions of PALASH Upazila', 4, '/1/19/352/1203'),
  (1204, 353, 'Unions of ROYPURA Upazila', 4, '/1/19/353/1204'),
  (1205, 354, 'Unions of SHIBPUR Upazila', 4, '/1/19/354/1205'),
  (1206, 355, 'Unions of ATPARA Upazila', 4, '/1/20/355/1206'),
  (1207, 356, 'Unions of BARHATTA Upazila', 4, '/1/20/356/1207'),
  (1208, 357, 'Unions of DURGAPUR Upazila', 4, '/1/20/357/1208'),
  (1209, 358, 'Unions of KHALIAJURI Upazila', 4, '/1/20/358/1209'),
  (1210, 359, 'Unions of KALMAKANDA Upazila', 4, '/1/20/359/1210'),
  (1211, 360, 'Unions of KENDUA Upazila', 4, '/1/20/360/1211'),
  (1212, 361, 'Unions of MADAN Upazila', 4, '/1/20/361/1212'),
  (1213, 362, 'Unions of MOHANGANJ Upazila', 4, '/1/20/362/1213'),
  (1214, 363, 'Unions of NETROKONA SADAR Upazila', 4, '/1/20/363/1214'),
  (1215, 364, 'Unions of PURBADHALA Upazila', 4, '/1/20/364/1215'),
  (1216, 365, 'Unions of BALIAKANDI Upazila', 4, '/1/21/365/1216'),
  (1217, 366, 'Unions of GOALANDA Upazila', 4, '/1/21/366/1217'),
  (1218, 367, 'Unions of KALUKHALI Upazila', 4, '/1/21/367/1218'),
  (1219, 368, 'Unions of PANGSHA Upazila', 4, '/1/21/368/1219'),
  (1220, 369, 'Unions of RAJBARI SADAR Upazila', 4, '/1/21/369/1220'),
  (1221, 370, 'Unions of BHEDARGANJ Upazila', 4, '/1/22/370/1221'),
  (1222, 371, 'Unions of DAMUDYA Upazila', 4, '/1/22/371/1222'),
  (1223, 372, 'Unions of GOSAIRHAT Upazila', 4, '/1/22/372/1223'),
  (1224, 373, 'Unions of NARIA Upazila', 4, '/1/22/373/1224'),
  (1225, 374, 'Unions of SHARIATPUR SADAR Upazila', 4, '/1/22/374/1225'),
  (1226, 375, 'Unions of ZANJIRA Upazila', 4, '/1/22/375/1226'),
  (1227, 376, 'Unions of JHENAIGATI Upazila', 4, '/1/23/376/1227'),
  (1228, 377, 'Unions of NAKLA Upazila', 4, '/1/23/377/1228'),
  (1229, 378, 'Unions of NALITABARI Upazila', 4, '/1/23/378/1229'),
  (1230, 379, 'Unions of SHERPUR SADAR Upazila', 4, '/1/23/379/1230'),
  (1231, 380, 'Unions of SREEBARDI Upazila', 4, '/1/23/380/1231'),
  (1232, 381, 'Unions of BASAIL Upazila', 4, '/1/24/381/1232'),
  (1233, 382, 'Unions of BHUAPUR Upazila', 4, '/1/24/382/1233'),
  (1234, 383, 'Unions of DELDUAR Upazila', 4, '/1/24/383/1234'),
  (1235, 384, 'Unions of DHANBARI Upazila', 4, '/1/24/384/1235'),
  (1236, 385, 'Unions of GHATAIL Upazila', 4, '/1/24/385/1236'),
  (1237, 386, 'Unions of GOPALPUR Upazila', 4, '/1/24/386/1237'),
  (1238, 387, 'Unions of KALIHATI Upazila', 4, '/1/24/387/1238'),
  (1239, 388, 'Unions of MADHUPUR Upazila', 4, '/1/24/388/1239'),
  (1240, 389, 'Unions of MIRZAPUR Upazila', 4, '/1/24/389/1240'),
  (1241, 390, 'Unions of NAGARPUR Upazila', 4, '/1/24/390/1241'),
  (1242, 391, 'Unions of SAKHIPUR Upazila', 4, '/1/24/391/1242'),
  (1243, 392, 'Unions of TANGAIL SADAR Upazila', 4, '/1/24/392/1243'),
  (1244, 398, 'Unions of BAGERHAT SADAR Upazila', 4, '/5/52/398/1244'),
  (1245, 399, 'Unions of CHITALMARI Upazila', 4, '/5/52/399/1245'),
  (1246, 400, 'Unions of FAKIRHAT Upazila', 4, '/5/52/400/1246'),
  (1247, 92, 'Unions of KACHUA Upazila', 4, '/2/27/92/1247'),
  (1248, 402, 'Unions of MOLLAHAT Upazila', 4, '/5/52/402/1248'),
  (1249, 403, 'Unions of MONGLA Upazila', 4, '/5/52/403/1249'),
  (1250, 404, 'Unions of MORRELGANJ Upazila', 4, '/5/52/404/1250'),
  (1251, 405, 'Unions of RAMPAL Upazila', 4, '/5/52/405/1251'),
  (1252, 406, 'Unions of SARANKHOLA Upazila', 4, '/5/52/406/1252'),
  (1253, 407, 'Unions of ALAMDANGA Upazila', 4, '/5/53/407/1253'),
  (1254, 408, 'Unions of CHUADANGA SADAR Upazila', 4, '/5/53/408/1254'),
  (1255, 409, 'Unions of DAMURHUDA Upazila', 4, '/5/53/409/1255'),
  (1256, 410, 'Unions of JIBAN NAGAR Upazila', 4, '/5/53/410/1256'),
  (1257, 411, 'Unions of ABHAYNAGAR Upazila', 4, '/5/54/411/1257'),
  (1258, 412, 'Unions of BAGHER PARA Upazila', 4, '/5/54/412/1258'),
  (1259, 413, 'Unions of CHAUGACHHA Upazila', 4, '/5/54/413/1259'),
  (1260, 414, 'Unions of JHIKARGACHHA Upazila', 4, '/5/54/414/1260'),
  (1261, 415, 'Unions of KESHABPUR Upazila', 4, '/5/54/415/1261'),
  (1262, 416, 'Unions of JESSORE SADAR Upazila', 4, '/5/54/416/1262'),
  (1263, 417, 'Unions of MANIRAMPUR Upazila', 4, '/5/54/417/1263'),
  (1264, 418, 'Unions of SHARSHA Upazila', 4, '/5/54/418/1264'),
  (1265, 419, 'Unions of HARINAKUNDA Upazila', 4, '/5/55/419/1265'),
  (1266, 420, 'Unions of JHENAIDAH SADAR Upazila', 4, '/5/55/420/1266'),
  (1267, 263, 'Unions of KALIGANJ Upazila', 4, '/1/10/263/1267'),
  (1268, 422, 'Unions of KOTCHANDPUR Upazila', 4, '/5/55/422/1268'),
  (1269, 423, 'Unions of MAHESHPUR Upazila', 4, '/5/55/423/1269'),
  (1270, 424, 'Unions of SHAILKUPA Upazila', 4, '/5/55/424/1270'),
  (1271, 425, 'Unions of BATIAGHATA Upazila', 4, '/5/56/425/1271'),
  (1272, 426, 'Unions of DACOPE Upazila', 4, '/5/56/426/1272'),
  (1273, 295, 'Unions of DAULATPUR Upazila', 4, '/1/15/295/1273'),
  (1274, 428, 'Unions of DUMURIA Upazila', 4, '/5/56/428/1274'),
  (1275, 429, 'Unions of DIGHALIA Upazila', 4, '/5/56/429/1275'),
  (1276, 431, 'Unions of KHAN JAHAN ALI Upazila', 4, '/5/56/431/1276'),
  (1277, 433, 'Unions of KOYRA Upazila', 4, '/5/56/433/1277'),
  (1278, 434, 'Unions of PAIKGACHHA Upazila', 4, '/5/56/434/1278'),
  (1279, 435, 'Unions of PHULTALA Upazila', 4, '/5/56/435/1279'),
  (1280, 436, 'Unions of RUPSA Upazila', 4, '/5/56/436/1280'),
  (1281, 438, 'Unions of TEROKHADA Upazila', 4, '/5/56/438/1281'),
  (1282, 439, 'Unions of BHERAMARA Upazila', 4, '/5/57/439/1282'),
  (1283, 295, 'Unions of DAULATPUR Upazila', 4, '/1/15/295/1283'),
  (1284, 441, 'Unions of KHOKSA Upazila', 4, '/5/57/441/1284'),
  (1285, 442, 'Unions of KUMARKHALI Upazila', 4, '/5/57/442/1285'),
  (1286, 443, 'Unions of KUSHTIA SADAR Upazila', 4, '/5/57/443/1286'),
  (1287, 228, 'Unions of MIRPUR Upazila', 4, '/1/8/228/1287'),
  (1288, 445, 'Unions of MAGURA SADAR Upazila', 4, '/5/58/445/1288'),
  (1289, 446, 'Unions of MOHAMMADPUR Upazila', 4, '/5/58/446/1289'),
  (1290, 447, 'Unions of SHALIKHA Upazila', 4, '/5/58/447/1290'),
  (1291, 265, 'Unions of SREEPUR Upazila', 4, '/1/10/265/1291'),
  (1292, 449, 'Unions of GANGNI Upazila', 4, '/5/59/449/1292'),
  (1293, 450, 'Unions of MUJIB NAGAR Upazila', 4, '/5/59/450/1293'),
  (1294, 451, 'Unions of MEHERPUR SADAR Upazila', 4, '/5/59/451/1294'),
  (1295, 452, 'Unions of KALIA Upazila', 4, '/5/60/452/1295'),
  (1296, 110, 'Unions of LOHAGARA Upazila', 4, '/2/28/110/1296'),
  (1297, 454, 'Unions of NARAIL SADAR Upazila', 4, '/5/60/454/1297'),
  (1298, 455, 'Unions of ASSASUNI Upazila', 4, '/5/61/455/1298'),
  (1299, 456, 'Unions of DEBHATA Upazila', 4, '/5/61/456/1299'),
  (1300, 457, 'Unions of KALAROA Upazila', 4, '/5/61/457/1300'),
  (1301, 263, 'Unions of KALIGANJ Upazila', 4, '/1/10/263/1301'),
  (1302, 459, 'Unions of SATKHIRA SADAR Upazila', 4, '/5/61/459/1302'),
  (1303, 460, 'Unions of SHYAMNAGAR Upazila', 4, '/5/61/460/1303'),
  (1304, 461, 'Unions of TALA Upazila', 4, '/5/61/461/1304'),
  (1305, 393, 'Unions of ADAMDIGHI Upazila', 4, '/3/36/393/1305'),
  (1306, 394, 'Unions of BOGRA SADAR Upazila', 4, '/3/36/394/1306'),
  (1307, 395, 'Unions of DHUNAT Upazila', 4, '/3/36/395/1307'),
  (1308, 396, 'Unions of DHUPCHANCHIA Upazila', 4, '/3/36/396/1308'),
  (1309, 397, 'Unions of GABTALI Upazila', 4, '/3/36/397/1309'),
  (1310, 462, 'Unions of KAHALOO Upazila', 4, '/3/36/462/1310'),
  (1311, 463, 'Unions of NANDIGRAM Upazila', 4, '/3/36/463/1311'),
  (1312, 464, 'Unions of SARIAKANDI Upazila', 4, '/3/36/464/1312'),
  (1313, 465, 'Unions of SHAJAHANPUR Upazila', 4, '/3/36/465/1313'),
  (1314, 466, 'Unions of SHERPUR Upazila', 4, '/3/36/466/1314'),
  (1315, 467, 'Unions of SHIBGANJ Upazila', 4, '/3/36/467/1315'),
  (1316, 468, 'Unions of SONATOLA Upazila', 4, '/3/36/468/1316'),
  (1317, 494, 'Unions of AKKELPUR Upazila', 4, '/3/38/494/1317'),
  (1318, 495, 'Unions of JOYPURHAT SADAR Upazila', 4, '/3/38/495/1318'),
  (1319, 496, 'Unions of KALAI Upazila', 4, '/3/38/496/1319'),
  (1320, 497, 'Unions of KHETLAL Upazila', 4, '/3/38/497/1320'),
  (1321, 498, 'Unions of PANCHBIBI Upazila', 4, '/3/38/498/1321'),
  (1322, 513, 'Unions of ATRAI Upazila', 4, '/3/39/513/1322'),
  (1323, 514, 'Unions of BADALGACHHI Upazila', 4, '/3/39/514/1323'),
  (1324, 515, 'Unions of DHAMOIRHAT Upazila', 4, '/3/39/515/1324'),
  (1325, 516, 'Unions of MANDA Upazila', 4, '/3/39/516/1325'),
  (1326, 517, 'Unions of MAHADEBPUR Upazila', 4, '/3/39/517/1326'),
  (1327, 518, 'Unions of NAOGAON SADAR Upazila', 4, '/3/39/518/1327'),
  (1328, 519, 'Unions of NIAMATPUR Upazila', 4, '/3/39/519/1328'),
  (1329, 520, 'Unions of PATNITALA Upazila', 4, '/3/39/520/1329'),
  (1330, 521, 'Unions of PORSHA Upazila', 4, '/3/39/521/1330'),
  (1331, 522, 'Unions of RANINAGAR Upazila', 4, '/3/39/522/1331'),
  (1332, 523, 'Unions of SAPAHAR Upazila', 4, '/3/39/523/1332'),
  (1333, 524, 'Unions of BAGATIPARA Upazila', 4, '/3/40/524/1333'),
  (1334, 525, 'Unions of BARAIGRAM Upazila', 4, '/3/40/525/1334'),
  (1335, 526, 'Unions of GURUDASPUR Upazila', 4, '/3/40/526/1335'),
  (1336, 527, 'Unions of LALPUR Upazila', 4, '/3/40/527/1336'),
  (1337, 528, 'Unions of NALDANGA Upazila', 4, '/3/40/528/1337'),
  (1338, 529, 'Unions of NATORE SADAR Upazila', 4, '/3/40/529/1338'),
  (1339, 530, 'Unions of SINGRA Upazila', 4, '/3/40/530/1339'),
  (1340, 469, 'Unions of BHOLAHAT Upazila', 4, '/3/37/469/1340'),
  (1341, 470, 'Unions of GOMASTAPUR Upazila', 4, '/3/37/470/1341'),
  (1342, 471, 'Unions of NACHOLE Upazila', 4, '/3/37/471/1342'),
  (1343, 472, 'Unions of CHAPAI NABABGANJ SADAR Upazila', 4, '/3/37/472/1343'),
  (1344, 467, 'Unions of SHIBGANJ Upazila', 4, '/3/36/467/1344'),
  (1345, 537, 'Unions of ATGHARIA Upazila', 4, '/3/41/537/1345'),
  (1346, 538, 'Unions of BERA Upazila', 4, '/3/41/538/1346'),
  (1347, 539, 'Unions of BHANGURA Upazila', 4, '/3/41/539/1347'),
  (1348, 540, 'Unions of CHATMOHAR Upazila', 4, '/3/41/540/1348'),
  (1349, 541, 'Unions of FARIDPUR Upazila', 4, '/3/41/541/1349'),
  (1350, 542, 'Unions of ISHWARDI Upazila', 4, '/3/41/542/1350'),
  (1351, 543, 'Unions of PABNA SADAR Upazila', 4, '/3/41/543/1351'),
  (1352, 544, 'Unions of SANTHIA Upazila', 4, '/3/41/544/1352'),
  (1353, 545, 'Unions of SUJANAGAR Upazila', 4, '/3/41/545/1353'),
  (1354, 551, 'Unions of BAGHA Upazila', 4, '/3/42/551/1354'),
  (1355, 552, 'Unions of BAGHMARA Upazila', 4, '/3/42/552/1355'),
  (1356, 554, 'Unions of CHARGHAT Upazila', 4, '/3/42/554/1356'),
  (1357, 357, 'Unions of DURGAPUR Upazila', 4, '/1/20/357/1357'),
  (1358, 556, 'Unions of GODAGARI Upazila', 4, '/3/42/556/1358'),
  (1359, 558, 'Unions of MOHANPUR Upazila', 4, '/3/42/558/1359'),
  (1360, 559, 'Unions of PABA Upazila', 4, '/3/42/559/1360'),
  (1361, 560, 'Unions of PUTHIA Upazila', 4, '/3/42/560/1361'),
  (1362, 563, 'Unions of TANORE Upazila', 4, '/3/42/563/1362'),
  (1363, 572, 'Unions of BELKUCHI Upazila', 4, '/3/43/572/1363'),
  (1364, 573, 'Unions of CHAUHALI Upazila', 4, '/3/43/573/1364'),
  (1365, 574, 'Unions of KAMARKHANDA Upazila', 4, '/3/43/574/1365'),
  (1366, 575, 'Unions of KAZIPUR Upazila', 4, '/3/43/575/1366'),
  (1367, 576, 'Unions of ROYGANJ Upazila', 4, '/3/43/576/1367'),
  (1368, 577, 'Unions of SHAHJADPUR Upazila', 4, '/3/43/577/1368'),
  (1369, 578, 'Unions of SIRAJGANJ SADAR Upazila', 4, '/3/43/578/1369'),
  (1370, 579, 'Unions of TARASH Upazila', 4, '/3/43/579/1370'),
  (1371, 580, 'Unions of ULLAH PARA Upazila', 4, '/3/43/580/1371'),
  (1372, 474, 'Unions of BIRAMPUR Upazila', 4, '/4/44/474/1372'),
  (1373, 475, 'Unions of BIRGANJ Upazila', 4, '/4/44/475/1373'),
  (1374, 476, 'Unions of BIRAL Upazila', 4, '/4/44/476/1374'),
  (1375, 477, 'Unions of BOCHAGANJ Upazila', 4, '/4/44/477/1375'),
  (1376, 478, 'Unions of CHIRIRBANDAR Upazila', 4, '/4/44/478/1376'),
  (1377, 479, 'Unions of FULBARI Upazila', 4, '/4/44/479/1377'),
  (1378, 480, 'Unions of GHORAGHAT Upazila', 4, '/4/44/480/1378'),
  (1379, 481, 'Unions of HAKIMPUR Upazila', 4, '/4/44/481/1379'),
  (1380, 482, 'Unions of KAHAROLE Upazila', 4, '/4/44/482/1380'),
  (1381, 483, 'Unions of KHANSAMA Upazila', 4, '/4/44/483/1381'),
  (1382, 484, 'Unions of DINAJPUR SADAR Upazila', 4, '/4/44/484/1382'),
  (1383, 231, 'Unions of NAWABGANJ Upazila', 4, '/1/8/231/1383'),
  (1384, 486, 'Unions of PARBATIPUR Upazila', 4, '/4/44/486/1384'),
  (1385, 487, 'Unions of FULCHHARI Upazila', 4, '/4/45/487/1385'),
  (1386, 488, 'Unions of GAIBANDHA SADAR Upazila', 4, '/4/45/488/1386'),
  (1387, 489, 'Unions of GOBINDAGANJ Upazila', 4, '/4/45/489/1387'),
  (1388, 490, 'Unions of PALASHBARI Upazila', 4, '/4/45/490/1388'),
  (1389, 491, 'Unions of SADULLAPUR Upazila', 4, '/4/45/491/1389'),
  (1390, 492, 'Unions of SAGHATA Upazila', 4, '/4/45/492/1390'),
  (1391, 493, 'Unions of SUNDARGANJ Upazila', 4, '/4/45/493/1391'),
  (1392, 499, 'Unions of BHURUNGAMARI Upazila', 4, '/4/46/499/1392'),
  (1393, 500, 'Unions of CHAR RAJIBPUR Upazila', 4, '/4/46/500/1393'),
  (1394, 501, 'Unions of CHILMARI Upazila', 4, '/4/46/501/1394'),
  (1395, 502, 'Unions of PHULBARI Upazila', 4, '/4/46/502/1395'),
  (1396, 503, 'Unions of KURIGRAM SADAR Upazila', 4, '/4/46/503/1396'),
  (1397, 504, 'Unions of NAGESHWARI Upazila', 4, '/4/46/504/1397'),
  (1398, 505, 'Unions of RAJARHAT Upazila', 4, '/4/46/505/1398'),
  (1399, 506, 'Unions of RAUMARI Upazila', 4, '/4/46/506/1399'),
  (1400, 507, 'Unions of ULIPUR Upazila', 4, '/4/46/507/1400'),
  (1401, 508, 'Unions of ADITMARI Upazila', 4, '/4/47/508/1401'),
  (1402, 509, 'Unions of HATIBANDHA Upazila', 4, '/4/47/509/1402'),
  (1403, 263, 'Unions of KALIGANJ Upazila', 4, '/1/10/263/1403'),
  (1404, 511, 'Unions of LALMONIRHAT SADAR Upazila', 4, '/4/47/511/1404'),
  (1405, 512, 'Unions of PATGRAM Upazila', 4, '/4/47/512/1405'),
  (1406, 531, 'Unions of DIMLA Upazila', 4, '/4/48/531/1406'),
  (1407, 532, 'Unions of DOMAR UPAZILA Upazila', 4, '/4/48/532/1407'),
  (1408, 533, 'Unions of JALDHAKA Upazila', 4, '/4/48/533/1408'),
  (1409, 534, 'Unions of KISHOREGANJ Upazila', 4, '/4/48/534/1409'),
  (1410, 535, 'Unions of NILPHAMARI SADAR Upazila', 4, '/4/48/535/1410'),
  (1411, 536, 'Unions of SAIDPUR UPAZILA Upazila', 4, '/4/48/536/1411'),
  (1412, 546, 'Unions of ATWARI Upazila', 4, '/4/49/546/1412'),
  (1413, 547, 'Unions of BODA Upazila', 4, '/4/49/547/1413'),
  (1414, 548, 'Unions of DEBIGANJ Upazila', 4, '/4/49/548/1414'),
  (1415, 549, 'Unions of PANCHAGARH SADAR Upazila', 4, '/4/49/549/1415'),
  (1416, 550, 'Unions of TENTULIA Upazila', 4, '/4/49/550/1416'),
  (1417, 564, 'Unions of BADARGANJ Upazila', 4, '/4/50/564/1417'),
  (1418, 565, 'Unions of GANGACHARA Upazila', 4, '/4/50/565/1418'),
  (1419, 566, 'Unions of KAUNIA Upazila', 4, '/4/50/566/1419'),
  (1420, 567, 'Unions of RANGPUR SADAR Upazila', 4, '/4/50/567/1420'),
  (1421, 568, 'Unions of MITHA PUKUR Upazila', 4, '/4/50/568/1421'),
  (1422, 569, 'Unions of PIRGACHHA Upazila', 4, '/4/50/569/1422'),
  (1423, 570, 'Unions of PIRGANJ Upazila', 4, '/4/50/570/1423'),
  (1424, 571, 'Unions of TARAGANJ Upazila', 4, '/4/50/571/1424'),
  (1425, 581, 'Unions of BALIADANGI Upazila', 4, '/4/51/581/1425'),
  (1426, 582, 'Unions of HARIPUR Upazila', 4, '/4/51/582/1426'),
  (1427, 570, 'Unions of PIRGANJ Upazila', 4, '/4/50/570/1427'),
  (1428, 584, 'Unions of RANISANKAIL Upazila', 4, '/4/51/584/1428'),
  (1429, 585, 'Unions of THAKURGAON SADAR Upazila', 4, '/4/51/585/1429'),
  (1430, 586, 'Unions of AJMIRIGANJ Upazila', 4, '/7/68/586/1430'),
  (1431, 587, 'Unions of BAHUBAL Upazila', 4, '/7/68/587/1431'),
  (1432, 588, 'Unions of BANIACHONG Upazila', 4, '/7/68/588/1432'),
  (1433, 589, 'Unions of CHUNARUGHAT Upazila', 4, '/7/68/589/1433'),
  (1434, 590, 'Unions of HABIGANJ SADAR Upazila', 4, '/7/68/590/1434'),
  (1435, 591, 'Unions of LAKHAI Upazila', 4, '/7/68/591/1435'),
  (1436, 592, 'Unions of MADHABPUR Upazila', 4, '/7/68/592/1436'),
  (1437, 593, 'Unions of NABIGANJ Upazila', 4, '/7/68/593/1437'),
  (1438, 594, 'Unions of BARLEKHA Upazila', 4, '/7/69/594/1438'),
  (1439, 595, 'Unions of JURI Upazila', 4, '/7/69/595/1439'),
  (1440, 596, 'Unions of KAMALGANJ Upazila', 4, '/7/69/596/1440'),
  (1441, 597, 'Unions of KULAURA Upazila', 4, '/7/69/597/1441'),
  (1442, 598, 'Unions of MAULVIBAZAR SADAR Upazila', 4, '/7/69/598/1442'),
  (1443, 599, 'Unions of RAJNAGAR Upazila', 4, '/7/69/599/1443'),
  (1444, 600, 'Unions of SREEMANGAL Upazila', 4, '/7/69/600/1444'),
  (1445, 601, 'Unions of BISHWAMBARPUR Upazila', 4, '/7/70/601/1445'),
  (1446, 602, 'Unions of CHHATAK Upazila', 4, '/7/70/602/1446'),
  (1447, 603, 'Unions of DAKSHIN SUNAMGANJ Upazila', 4, '/7/70/603/1447'),
  (1448, 604, 'Unions of DERAI Upazila', 4, '/7/70/604/1448'),
  (1449, 605, 'Unions of DHARAMPASHA Upazila', 4, '/7/70/605/1449'),
  (1450, 606, 'Unions of DOWARABAZAR Upazila', 4, '/7/70/606/1450'),
  (1451, 607, 'Unions of JAGANNATHPUR Upazila', 4, '/7/70/607/1451'),
  (1452, 608, 'Unions of JAMALGANJ Upazila', 4, '/7/70/608/1452'),
  (1453, 609, 'Unions of SULLA Upazila', 4, '/7/70/609/1453'),
  (1454, 610, 'Unions of SUNAMGANJ SADAR Upazila', 4, '/7/70/610/1454'),
  (1455, 611, 'Unions of TAHIRPUR Upazila', 4, '/7/70/611/1455'),
  (1456, 612, 'Unions of BALAGANJ Upazila', 4, '/7/71/612/1456'),
  (1457, 613, 'Unions of BEANI BAZAR Upazila', 4, '/7/71/613/1457'),
  (1458, 614, 'Unions of BISHWANATH Upazila', 4, '/7/71/614/1458'),
  (1459, 315, 'Unions of COMPANIGANJ Upazila', 4, '/2/34/315/1459'),
  (1460, 616, 'Unions of DAKSHIN SURMA Upazila', 4, '/7/71/616/1460'),
  (1461, 617, 'Unions of FENCHUGANJ Upazila', 4, '/7/71/617/1461'),
  (1462, 618, 'Unions of GOLAPGANJ Upazila', 4, '/7/71/618/1462'),
  (1463, 619, 'Unions of GOWAINGHAT Upazila', 4, '/7/71/619/1463'),
  (1464, 620, 'Unions of JAINTIAPUR Upazila', 4, '/7/71/620/1464'),
  (1465, 621, 'Unions of KANAIGHAT Upazila', 4, '/7/71/621/1465'),
  (1466, 622, 'Unions of SYLHET SADAR Upazila', 4, '/7/71/622/1466'),
  (1467, 623, 'Unions of ZAKIGANJ Upazila', 4, '/7/71/623/1467');

-- --------------------------------------------------------

--
-- Table structure for table `MegaMenu`
--

CREATE TABLE IF NOT EXISTS `MegaMenu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `sorting` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_advertisment`
--

CREATE TABLE IF NOT EXISTS `megamenu_advertisment` (
  `megamenu_id` int(11) NOT NULL,
  `advertisment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_branding`
--

CREATE TABLE IF NOT EXISTS `megamenu_branding` (
  `megamenu_id` int(11) NOT NULL,
  `branding_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_category`
--

CREATE TABLE IF NOT EXISTS `megamenu_category` (
  `megamenu_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_collection`
--

CREATE TABLE IF NOT EXISTS `megamenu_collection` (
  `megamenu_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_globaloption`
--

CREATE TABLE IF NOT EXISTS `megamenu_globaloption` (
  `megamenu_id` int(11) NOT NULL,
  `globaloption_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `megamenu_syndicate`
--

CREATE TABLE IF NOT EXISTS `megamenu_syndicate` (
  `megamenu_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Menu`
--

CREATE TABLE IF NOT EXISTS `Menu` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `syndicate_id` int(11) DEFAULT NULL,
  `menu` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menuSlug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uniqueCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `defaultMenu` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `syndicateModule_id` int(11) DEFAULT NULL,
  `siteSetting_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Menu`
--

INSERT INTO `Menu` (`id`, `user_id`, `module_id`, `page_id`, `syndicate_id`, `menu`, `menuSlug`, `uniqueCode`, `defaultMenu`, `status`, `syndicateModule_id`, `siteSetting_id`) VALUES
  (1, 17, NULL, 6, NULL, 'About us', 'about-us', NULL, 0, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `MenuGroup`
--

CREATE TABLE IF NOT EXISTS `MenuGroup` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `groupType` longtext COLLATE utf8_unicode_ci COMMENT '(DC2Type:array)',
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `MenuGroup`
--

INSERT INTO `MenuGroup` (`id`, `name`, `groupType`, `status`) VALUES
  (1, 'Header Menu', NULL, 1),
  (2, 'Left Menu', NULL, 1),
  (3, 'Footer Menu', NULL, 1),
  (4, 'Right', NULL, 1),
  (5, 'Dashboard', 'a:2:{i:0;i:1;i:1;i:2;}', 1),
  (6, 'Mobile Menu', 'a:1:{i:0;i:2;}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `MenuGrouping`
--

CREATE TABLE IF NOT EXISTS `MenuGrouping` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `sorting` smallint(6) DEFAULT NULL,
  `menuGroup_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `MenuGrouping`
--

INSERT INTO `MenuGrouping` (`id`, `parent`, `user_id`, `menu_id`, `sorting`, `menuGroup_id`) VALUES
  (1, NULL, 17, 1, 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `MobileIcon`
--

CREATE TABLE IF NOT EXISTS `MobileIcon` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `MobileIcon`
--

INSERT INTO `MobileIcon` (`id`, `name`, `globalOption_id`) VALUES
  (2, NULL, 8),
  (3, NULL, 9),
  (4, NULL, 10),
  (5, NULL, 11);

-- --------------------------------------------------------

--
-- Table structure for table `MobileTheme`
--

CREATE TABLE IF NOT EXISTS `MobileTheme` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folderName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `MobileTheme`
--

INSERT INTO `MobileTheme` (`id`, `name`, `folderName`, `status`, `path`) VALUES
  (1, 'Default', 'Default', 1, 'theme5.png'),
  (2, 'Education', 'Education', 1, 'theme1.png'),
  (3, 'Mobile Theme Three', 'Theme3', 1, 'theme4.png'),
  (4, 'Dynamic Sensor Theme', 'Dynamic', 1, 'theme6.png'),
  (5, 'Simple for Education', 'Education', 1, 'theme7.png'),
  (6, 'Slasher Theme Forest', 'Slasher', 1, 'theme10.png');

-- --------------------------------------------------------

--
-- Table structure for table `mobiletheme_syndicate`
--

CREATE TABLE IF NOT EXISTS `mobiletheme_syndicate` (
  `mobiletheme_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Module`
--

CREATE TABLE IF NOT EXISTS `Module` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menuSlug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `moduleClass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isHome` tinyint(1) NOT NULL,
  `isSingle` tinyint(1) NOT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Module`
--

INSERT INTO `Module` (`id`, `name`, `menu`, `menuSlug`, `status`, `description`, `moduleClass`, `isHome`, `isSingle`, `slug`) VALUES
  (1, 'News', 'News', 'news', 0, 'News Feature add This module1', 'News', 1, 0, 'news'),
  (2, 'Blog', 'Blog', 'blog', 1, 'Blog...', 'Blog', 1, 0, 'blog'),
  (3, 'Testimonials', 'Testimonial', 'testimonial', 1, NULL, 'Testimonial', 1, 0, 'testimonial'),
  (4, 'Timeline', 'Timeline', 'timeline', 1, NULL, 'Timeline', 0, 1, 'timeline'),
  (5, 'Calender Blackout', 'Calender Blackout', 'calender-blackout', 1, 'Calender Blackout', 'Blackout', 0, 1, 'calender-blackout'),
  (6, 'Branch', 'Branches', 'branches', 1, 'Branch', 'Branch', 0, 1, 'branches'),
  (7, 'Event', 'Event', 'event', 1, 'Event', 'Event', 1, 0, 'event'),
  (8, 'NoticeBoard', 'NoticeBoard', 'noticeboard', 1, 'NoticeBoard', 'NoticeBoard', 1, 0, 'noticeboard'),
  (9, 'Contact-us', 'Contact-us', 'contact', 0, NULL, 'ContactPage', 0, 0, 'contact'),
  (10, 'HomeSlider', 'HomeSlider', 'homeslider', 1, 'HomeSlider', 'HomeSlider', 0, 1, 'homeslider'),
  (11, 'Faq', 'Faq', 'faq', 1, 'dfsdfsdfsf', 'Faq', 0, 0, 'faq'),
  (12, 'Page', 'Infromation', 'page', 1, NULL, 'Page', 0, 0, 'page');

-- --------------------------------------------------------

--
-- Table structure for table `News`
--

CREATE TABLE IF NOT EXISTS `News` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `feature` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `NoticeBoard`
--

CREATE TABLE IF NOT EXISTS `NoticeBoard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menuSlug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoGallery_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`id`, `parent`, `user_id`, `name`, `menu`, `menuSlug`, `slug`, `content`, `status`, `created`, `updated_at`, `path`, `photoGallery_id`) VALUES
  (2, NULL, 4, 'About us', 'About-us', 'About-us', 'About-us', 'About us', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL),
  (3, NULL, 11, 'About us', 'About us', 'shoshi-collection-5-about-us', 'shoshi-collection-5-about-us', NULL, 1, '2016-01-01 20:23:02', '2016-01-01 20:23:02', NULL, NULL),
  (4, NULL, 13, 'About us', 'About us', 'shoshi-collection-6-about-us', 'shoshi-collection-6-about-us', NULL, 1, '2016-01-01 20:28:48', '2016-01-01 20:28:48', NULL, NULL),
  (5, NULL, 16, 'About us', 'About us', 'tipu-collection-about-us', 'tipu-collection-about-us', NULL, 1, '2016-01-01 20:33:22', '2016-01-01 20:33:22', NULL, NULL),
  (6, NULL, 17, 'About us', 'About us', 'about-us', 'opu-collection-about-us', 'Bringing quality to life through the communities we touch, whether it''s \r\nthe one just outside our shops, or one of our suppliers on another \r\ncontinent.', 1, '2016-01-02 17:03:44', '2016-01-02 17:34:20', 'DSC_2197.JPG', 1);

-- --------------------------------------------------------

--
-- Table structure for table `PaymentCard`
--

CREATE TABLE IF NOT EXISTS `PaymentCard` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PaymentCard`
--

INSERT INTO `PaymentCard` (`id`, `name`, `status`) VALUES
  (1, 'Visa Card', 0),
  (2, 'Credit Card', 0);

-- --------------------------------------------------------

--
-- Table structure for table `photo_gallery`
--

CREATE TABLE IF NOT EXISTS `photo_gallery` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `photo_gallery`
--

INSERT INTO `photo_gallery` (`id`, `user_id`, `name`, `status`, `description`, `created`, `path`) VALUES
  (1, 17, 'About us', 1, NULL, '2016-01-02 17:38:06', 'DSC_1995.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `Product`
--

CREATE TABLE IF NOT EXISTS `Product` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double DEFAULT NULL,
  `salesPrice` double DEFAULT NULL,
  `vendorPrice` double DEFAULT NULL,
  `shipping` double DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `productCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `size` longtext COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `color` longtext COLLATE utf8_unicode_ci,
  `weight` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dimension` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `availability` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `madeIn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isFeature` tinyint(1) NOT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `dealType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` tinytext COLLATE utf8_unicode_ci,
  `parentCategory_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ProductCustomAttribute`
--

CREATE TABLE IF NOT EXISTS `ProductCustomAttribute` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ProductGallery`
--

CREATE TABLE IF NOT EXISTS `ProductGallery` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `path` tinytext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ProductReview`
--

CREATE TABLE IF NOT EXISTS `ProductReview` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rating` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE IF NOT EXISTS `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_import`
--

CREATE TABLE IF NOT EXISTS `product_import` (
  `name` varchar(255) NOT NULL,
  `web` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `vendor_code` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `purchase_price` varchar(255) NOT NULL,
  `sales_price` varchar(255) NOT NULL,
  `memo_no` varchar(255) NOT NULL,
  `rack` varchar(255) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `dimension` varchar(255) NOT NULL,
  `extra_1` varchar(255) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1630 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product_import`
--

INSERT INTO `product_import` (`name`, `web`, `code`, `vendor_code`, `category`, `quantity`, `purchase_price`, `sales_price`, `memo_no`, `rack`, `unit`, `color`, `dimension`, `extra_1`, `id`) VALUES
  ('Formal Shirt', 'Formal Shirt', 'GB-0023', 'GB', 'Men''s Shirt', '', '850', '1250', '589', '', 'Pics', 'Purple', '16.5', '', 1),
  ('Formal Shirt', 'Formal Shirt', 'GB-0021', 'GB', 'Men''s Shirt', '', '850', '1250', '589', '', 'Pics', 'Purple', '15.5', '', 2),
  ('Formal Shirt', 'Formal Shirt', 'GB-008', 'GB', 'Men''s Shirt', '', '650', '1050', '589', '', 'Pics', 'Pink', '16', '', 3),
  ('Formal Shirt', 'Formal Shirt', 'GB-005', 'GB', 'Men''s Shirt', '', '650', '1050', '589', '', 'Pics', 'Silver', '15.5', '', 4),
  ('Formal Shirt', 'Formal Shirt', 'GB-0040', 'GB', 'Men''s Shirt', '', '850', '1550', '626', '', 'Pics', 'Red', '15.5', '', 5),
  ('Formal Shirt', 'Formal Shirt', 'GB-0035', 'GB', 'Men''s Shirt', '', '850', '1550', '626', '', 'Pics', 'Red', '16', '', 6),
  ('Formal Shirt', 'Formal Shirt', 'GB-0041', 'GB', 'Men''s Shirt', '', '850', '1550', '626', '', 'Pics', 'Red', '16', '', 7),
  ('Formal Shirt', 'Formal Shirt', 'GB-0037', 'GB', 'Men''s Shirt', '', '850', '1550', '626', '', 'Pics', 'Red', '15', '', 8),
  ('Formal Shirt', 'Formal Shirt', 'GB-0039', 'GB', 'Men''s Shirt', '', '850', '1550', '626', '', 'Pics', 'Purple', '15.5', '', 9),
  ('Formal Shirt', 'Formal Shirt', 'GB-0042', 'GB', 'Men''s Shirt', '', '850', '1550', '626', '', 'Pics', 'Purple', '15', '', 10),
  ('Formal Shirt', 'Formal Shirt', 'GB-0021', 'GB', 'Men''s Shirt', '', '850', '1550', '589', '', 'Pics', 'Purple', '15', '', 11),
  ('Formal Shirt', 'Formal Shirt', 'CM-0045', 'CM', 'Men''s Shirt', '', '850', '1250', '332', '', 'Pics', 'Red', '15.5', '', 12),
  ('Formal Shirt', 'Formal Shirt', 'CM-0043', 'CM', 'Men''s Shirt', '', '850', '1250', '332', '', 'Pics', 'Red', '16', '', 13),
  ('Formal Shirt', 'Formal Shirt', 'CM-0044', 'CM', 'Men''s Shirt', '', '850', '1250', '332', '', 'Pics', 'Red', '15', '', 14),
  ('Formal Shirt', 'Formal Shirt', 'CM-0060', 'CM', 'Men''s Shirt', '', '750', '1250', '332', '', 'Pics', 'Purple', '16', '', 15),
  ('Formal Shirt', 'Formal Shirt', 'CM-0059', 'CM', 'Men''s Shirt', '', '750', '1250', '332', '', 'Pics', 'Purple', '15', '', 16),
  ('Formal Shirt', 'Formal Shirt', 'CM-0061', 'CM', 'Men''s Shirt', '', '750', '1250', '332', '', 'Pics', 'Purple', '15.5', '', 17),
  ('Formal Shirt', 'Formal Shirt', 'CM-0064', 'CM', 'Men''s Shirt', '', '750', '1550', '332', '', 'Pics', 'Purple', '16', '', 18),
  ('Formal Shirt', 'Formal Shirt', 'CM-0062', 'CM', 'Men''s Shirt', '', '750', '1550', '332', '', 'Pics', 'Purple', '16', '', 19),
  ('Formal Shirt', 'Formal Shirt', 'GB-0028', 'GB', 'Men''s Shirt', '', '680', '1150', '589', '', 'Pics', 'Mixed', '16', '', 20),
  ('Formal Shirt', 'Formal Shirt', 'CM-0054', 'CM', 'Men''s Shirt', '', '950', '1550', '332', '', 'Pics', 'Mixed', '15.5', '', 21),
  ('Formal Shirt', 'Formal Shirt', 'CM-0090', 'CM', 'Men''s Shirt', '', '850', '1280', '356', '', 'Pics', 'Black', '15.5', '', 22),
  ('Formal Shirt', 'Formal Shirt', 'CM-0088', 'CM', 'Men''s Shirt', '', '850', '1280', '356', '', 'Pics', 'Black', '16', '', 23),
  ('Formal Shirt', 'Formal Shirt', 'CM-0057', 'CM', 'Men''s Shirt', '', '550', '1050', '332', '', 'Pics', 'White', '15.5', '', 24),
  ('Formal Shirt', 'Formal Shirt', 'CM-0056', 'CM', 'Men''s Shirt', '', '550', '1050', '332', '', 'Pics', 'White', '15', '', 25),
  ('Formal Shirt', 'Formal Shirt', 'GB-0002', 'GB', 'Men''s Shirt', '', '600', '1050', '598', '', 'Pics', 'Black', '16', '', 26),
  ('Formal Shirt', 'Formal Shirt', 'GB-0001', 'GB', 'Men''s Shirt', '', '600', '1050', '598', '', 'Pics', 'Black', '15.5', '', 27),
  ('Formal Shirt', 'Formal Shirt', 'GB-0030', 'GB', 'Men''s Shirt', '', '680', '1150', '598', '', 'Pics', 'Mixed', '15.5', '', 28),
  ('Formal Shirt', 'Formal Shirt', 'CL-0045', 'CL', 'Men''s Shirt', '', '650', '975', '1958', '', 'Pics', 'Blue', '15.5', '', 29),
  ('Formal Shirt', 'Formal Shirt', 'CL-0007', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'White', '16.5', '', 30),
  ('Formal Shirt', 'Formal Shirt', 'CL-0027', 'CL', 'Men''s Shirt', '', '580', '1050', '1958', '', 'Pics', 'Blue', '16', '', 31),
  ('Formal Shirt', 'Formal Shirt', 'CL-0013', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '15.5', '', 32),
  ('Formal Shirt', 'Formal Shirt', 'CL-0011', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'White', '15.5', '', 33),
  ('Formal Shirt', 'Formal Shirt', 'CL-0040', 'CL', 'Men''s Shirt', '', '650', '975', '1958', '', 'Pics', 'Black', '16', '', 34),
  ('Formal Shirt', 'Formal Shirt', 'CL-0019', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Yellow', '15.5', '', 35),
  ('Formal Shirt', 'Formal Shirt', 'CL-0017', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Yellow', '15.5', '', 36),
  ('Formal Shirt', 'Formal Shirt', 'CL-0035', 'CL', 'Men''s Shirt', '', '580', '1050', '1958', '', 'Pics', 'Blue', '16', '', 37),
  ('Formal Shirt', 'Formal Shirt', 'CL-0039', 'CL', 'Men''s Shirt', '', '650', '1050', '1958', '', 'Pics', 'Green', '16', '', 38),
  ('Formal Shirt', 'Formal Shirt', 'CL-0002', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '15.5', '', 39),
  ('Formal Shirt', 'Formal Shirt', 'CL-0025', 'CL', 'Men''s Shirt', '', '580', '1050', '1958', '', 'Pics', 'Blue', '16', '', 40),
  ('Formal Shirt', 'Formal Shirt', 'CL-0047', 'CL', 'Men''s Shirt', '', '650', '1250', '1958', '', 'Pics', 'Coffy', '16', '', 41),
  ('Formal Shirt', 'Formal Shirt', 'CL-0008', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'White', '15.5', '', 42),
  ('Formal Shirt', 'Formal Shirt', 'CL-0043', 'CL', 'Men''s Shirt', '', '650', '1250', '1958', '', 'Pics', 'Purple', '15.5', '', 43),
  ('Formal Shirt', 'Formal Shirt', 'CL-0016', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '15.5', '', 44),
  ('Formal Shirt', 'Formal Shirt', 'CL-0018', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '16.5', '', 45),
  ('Formal Shirt', 'Formal Shirt', 'CL-0021', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '16', '', 46),
  ('Formal Shirt', 'Formal Shirt', 'CL-0036', 'CL', 'Men''s Shirt', '', '650', '1250', '1958', '', 'Pics', 'Mixed', '15.5', '', 47),
  ('Formal Shirt', 'Formal Shirt', 'CL-0034', 'CL', 'Men''s Shirt', '', '580', '1050', '1958', '', 'Pics', 'Mixed', '16.5', '', 48),
  ('Formal Shirt', 'Formal Shirt', 'CL-0033', 'CL', 'Men''s Shirt', '', '580', '1050', '1958', '', 'Pics', 'Mixed', '16', '', 49),
  ('Formal Shirt', 'Formal Shirt', 'CL-0020', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '16', '', 50),
  ('Formal Shirt', 'Formal Shirt', 'CL-0014', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '16', '', 51),
  ('Formal Shirt', 'Formal Shirt', 'CL-0015', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Mixed', '15.5', '', 52),
  ('Formal Shirt', 'Formal Shirt', 'CL-0003', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Blue', '16', '', 53),
  ('Formal Shirt', 'Formal Shirt', 'CL-0005', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Yellow', '15.5', '', 54),
  ('Formal Shirt', 'Formal Shirt', 'CL-0001', 'CL', 'Men''s Shirt', '', '850', '1270', '1958', '', 'Pics', 'Yellow', '16', '', 55),
  ('Formal Shirt', 'Formal Shirt', 'CL-0067', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Sky Blue', 'M', '', 56),
  ('Formal Shirt', 'Formal Shirt', 'CL-0066', 'CL', 'Men''s Shirt', '', '570', '1050', '977', '', 'Pics', 'Sky Blue', 'E', '', 57),
  ('Formal Shirt', 'Formal Shirt', 'CL-0059', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Pink', 'O', '', 58),
  ('Formal Shirt', 'Formal Shirt', 'CL-0060', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Pink', 'M', '', 59),
  ('Formal Shirt', 'Formal Shirt', 'CL-0061', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Pink', 'E', '', 60),
  ('Formal Shirt', 'Formal Shirt', 'CL-0068', 'CL', 'Men''s Shirt', '', '570', '1050', '977', '', 'Pics', 'Sky Blue', 'E', '', 61),
  ('Formal Shirt', 'Formal Shirt', 'CL-0051', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Coffy', 'E', '', 62),
  ('Formal Shirt', 'Formal Shirt', 'CL-0050', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Coffy', 'O', '', 63),
  ('Formal Shirt', 'Formal Shirt', 'CL-0052', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Coffy', 'E', '', 64),
  ('Formal Shirt', 'Formal Shirt', 'CL-0069', 'CL', 'Men''s Shirt', '', '570', '1050', '977', '', 'Pics', 'Pink', '16', '', 65),
  ('Formal Shirt', 'Formal Shirt', 'CL-0071', 'CL', 'Men''s Shirt', '', '570', '1050', '977', '', 'Pics', 'Pink', '15.5', '', 66),
  ('Formal Shirt', 'Formal Shirt', 'CL-0070', 'CL', 'Men''s Shirt', '', '570', '1050', '977', '', 'Pics', 'Pink', '16.5', '', 67),
  ('Formal Shirt', 'Formal Shirt', 'CL-0057', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Brown', 'E', '', 68),
  ('Formal Shirt', 'Formal Shirt', 'CL-0058', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Brown', 'M', '', 69),
  ('Formal Shirt', 'Formal Shirt', 'CL-0056', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Brown', 'O', '', 70),
  ('Formal Shirt', 'Formal Shirt', 'CL-0072', 'CL', 'Men''s Shirt', '', '650', '1150', '977', '', 'Pics', 'White', 'M', '', 71),
  ('Formal Shirt', 'Formal Shirt', 'CL-0074', 'CL', 'Men''s Shirt', '', '650', '1150', '977', '', 'Pics', 'White', 'E', '', 72),
  ('Formal Shirt', 'Formal Shirt', 'CL-0052', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Brown', 'E', '', 73),
  ('Formal Shirt', 'Formal Shirt', 'CL-0053', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Orange', 'M', '', 74),
  ('Formal Shirt', 'Formal Shirt', 'CL-0073', 'CL', 'Men''s Shirt', '', '650', '1150', '977', '', 'Pics', 'White', 'O', '', 75),
  ('Formal Shirt', 'Formal Shirt', 'CL-0054', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Orange', 'E', '', 76),
  ('Formal Shirt', 'Formal Shirt', 'CL-0055', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Orange', 'O', '', 77),
  ('Formal Shirt', 'Formal Shirt', 'CL-0062', 'CL', 'Men''s Shirt', '', '480', '1050', '977', '', 'Pics', 'Botton Green', 'M', '', 78),
  ('Formal Shirt', 'Formal Shirt', 'IS-0014', 'IS', 'Men''s Shirt', '', '710', '1450', '1511022', '', 'Pics', 'Silver', '16', '', 79),
  ('Formal Shirt', 'Formal Shirt', 'IS-0009', 'IS', 'Men''s Shirt', '', '750', '1450', '1511022', '', 'Pics', 'Golden', '16.5', '', 80),
  ('Formal Shirt', 'Formal Shirt', 'IS-0013', 'IS', 'Men''s Shirt', '', '750', '1450', '1511022', '', 'Pics', 'Golden', '15.5', '', 81),
  ('Formal Shirt', 'Formal Shirt', 'IS-0007', 'IS', 'Men''s Shirt', '', '725', '1550', '1511022', '', 'Pics', 'Coffy', '16', '', 82),
  ('Formal Shirt', 'Formal Shirt', 'IS-0008', 'IS', 'Men''s Shirt', '', '725', '1550', '1511022', '', 'Pics', 'Coffy', '16.5', '', 83),
  ('Formal Shirt', 'Formal Shirt', 'IS-0011', 'IS', 'Men''s Shirt', '', '710', '1450', '1511022', '', 'Pics', 'Pink', '16', '', 84),
  ('Formal Shirt', 'Formal Shirt', 'IS-0005', 'IS', 'Men''s Shirt', '', '725', '1550', '1511022', '', 'Pics', 'Purple', '16', '', 85),
  ('Formal Shirt', 'Formal Shirt', 'IS-0017', 'IS', 'Men''s Shirt', '', '790', '1550', '1511022', '', 'Pics', 'Mixed', '15', '', 86),
  ('Formal Shirt', 'Formal Shirt', 'IS-0018', 'IS', 'Men''s Shirt', '', '790', '1550', '1511022', '', 'Pics', '', '16', '', 87),
  ('Casual Shirt', 'Casual Shirt', 'RM-0011', 'RM', 'Men''s Shirt', '', '850', '1275', 'NAI', '', 'Pics', 'Pink', 'XL', '', 88),
  ('Casual Shirt', 'Casual Shirt', 'RM-0006', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Blue', 'L', '', 89),
  ('Casual Shirt', 'Casual Shirt', 'RM-0005', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Coffy', 'XL', '', 90),
  ('Casual Shirt', 'Casual Shirt', 'RM-0013', 'RM', 'Men''s Shirt', '', '780', '1170', '', '', 'Pics', 'Silver', 'L', '', 91),
  ('Casual Shirt', 'Casual Shirt', 'RM-0009', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Silver', 'L', '', 92),
  ('Casual Shirt', 'Casual Shirt', 'RM-0001', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Silver', 'XL', '', 93),
  ('Casual Shirt', 'Casual Shirt', 'RM-0004', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Silver', 'M', '', 94),
  ('Casual Shirt', 'Casual Shirt', 'RM-0002', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Mixed', 'L', '', 95),
  ('Casual Shirt', 'Casual Shirt', 'RM-0012', 'RM', 'Men''s Shirt', '', '780', '1170', '', '', 'Pics', 'Silver', 'M', '', 96),
  ('Casual Shirt', 'Casual Shirt', 'RM-0016', 'RM', 'Men''s Shirt', '', '750', '1125', '', '', 'Pics', 'Red', 'XL', '', 97),
  ('Casual Shirt', 'Casual Shirt', 'RM-0010              RM', '', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Blue', 'L', '', 98),
  ('Casual Shirt', 'Casual Shirt', 'RM-0018', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Mixed', '16.5', '', 99),
  ('Casual Shirt', 'Casual Shirt', 'RM-0019', 'RM', 'Men''s Shirt', '', '850', '1275', '', '', 'Pics', 'Mixed', '15.5', '', 100),
  ('Casual Shirt', 'Casual Shirt', 'CM-0033', 'CM', 'Men''s Shirt', '', '650', '975', '278', '', 'Pics', 'Silver', 'M', '', 101),
  ('Casual Shirt', 'Casual Shirt', 'CM-0036', 'CM', 'Men''s Shirt', '', '650', '975', '278', '', 'Pics', 'Blue', 'L', '', 102),
  ('Casual Shirt', 'Casual Shirt', 'CM-0035', 'CM', 'Men''s Shirt', '', '650', '975', '278', '', 'Pics', 'Blue', 'L', '', 103),
  ('Casual Shirt', 'Casual Shirt', 'CM-0083', 'CM', 'Men''s Shirt', '', '800', '1280', '356', '', 'Pics', 'Blue', 'XL', '', 104),
  ('Casual Shirt', 'Casual Shirt', 'CM-0082', 'CM', 'Men''s Shirt', '', '800', '1280', '356', '', 'Pics', 'Blue', 'M', '', 105),
  ('Casual Shirt', 'Casual Shirt', 'CM-0084', 'CM', 'Men''s Shirt', '', '800', '1280', '356', '', 'Pics', 'Blue', 'L', '', 106),
  ('Casual Shirt', 'Casual Shirt', 'CM-0074', 'CM', 'Men''s Shirt', '', '700', '1150', '356', '', 'Pics', 'Blue', 'L', '', 107),
  ('Casual Shirt', 'Casual Shirt', 'CM-0075', 'CM', 'Men''s Shirt', '', '700', '1150', '356', '', 'Pics', 'Blue', 'XL', '', 108),
  ('Casual Shirt', 'Casual Shirt', 'CM-0073', 'CM', 'Men''s Shirt', '', '700', '1150', '356', '', 'Pics', 'Mixed', 'M', '', 109),
  ('Casual Shirt', 'Casual Shirt', 'CM-0071', 'CM', 'Men''s Shirt', '', '700', '1150', '356', '', 'Pics', 'Red', 'L', '', 110),
  ('Casual Shirt', 'Casual Shirt', 'CM-0072', 'CM', 'Men''s Shirt', '', '700', '1150', '356', '', 'Pics', 'Red', 'M', '', 111),
  ('Casual Shirt', 'Casual Shirt', 'CM-0070', 'CM', 'Men''s Shirt', '', '700', '1150', '356', '', 'Pics', 'Mixed', 'XL', '', 112),
  ('Casual Shirt', 'Casual Shirt', 'CM-0079', 'CM', 'Men''s Shirt', '', '550', '950', '356', '', 'Pics', 'Mixed', 'M', '', 113),
  ('Casual Shirt', 'Casual Shirt', 'CM-0080', 'CM', 'Men''s Shirt', '', '550', '950', '356', '', 'Pics', 'Mixed', 'XL', '', 114),
  ('Casual Shirt', 'Casual Shirt', 'CM-0081', 'CM', 'Men''s Shirt', '', '550', '950', '356', '', 'Pics', 'Mixed', 'L', '', 115),
  ('Casual Shirt', 'Casual Shirt', 'CM-0049', 'CM', 'Men''s Shirt', '', '650', '1050', '332', '', 'Pics', 'Mixed', '16', '', 116),
  ('Casual Shirt', 'Casual Shirt', 'CM-0050', 'CM', 'Men''s Shirt', '', '650', '1050', '332', '', 'Pics', 'Black', '15', '', 117),
  ('Casual Shirt', 'Casual Shirt', 'CM-0069', 'CM', 'Men''s Shirt', '', '750', '1150', '332', '', 'Pics', 'Blue', 'M', '', 118),
  ('Casual Shirt', 'Casual Shirt', 'GB-0015', 'GB', 'Men''s Shirt', '', '750', '1050', '598', '', 'Pics', 'Mixed', '15.5', '', 119),
  ('Casual Shirt', 'Casual Shirt', 'GB-0017', 'GB', 'Men''s Shirt', '', '750', '1050', '598', '', 'Pics', 'Mixed', '16.5', '', 120),
  ('Casual Shirt', 'Casual Shirt', 'GB-0033', 'GB', 'Men''s Shirt', '', '750', '1050', '598', '', 'Pics', 'White', '15.5', '', 121),
  ('Casual Shirt', 'Casual Shirt', 'CM-0076', 'CM', 'Men''s Shirt', '', '850', '1250', '356', '', 'Pics', 'Sky Blue', '16', '', 122),
  ('Casual Shirt', 'Casual Shirt', 'CM-0046', 'CM', 'Men''s Shirt', '', '850', '1350', '332', '', 'Pics', 'White', 'XL', '', 123),
  ('Full Tshirt', 'Full Tshirt', 'EX-0112', 'EX', 'Men''s Full Tshirt', '', '320', '650', '1195', '', 'Pics', 'Pink', 'F', '', 124),
  ('Full Tshirt', 'Full Tshirt', 'EX-0110', 'EX', 'Men''s Full Tshirt', '', '260', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 125),
  ('Full Tshirt', 'Full Tshirt', 'EX-0113', 'EX', 'Men''s Full Tshirt', '', '260', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 126),
  ('Full Tshirt', 'Full Tshirt', 'HT-0024', 'HT', 'Men''s Full Tshirt', '', '250', '650', '143', '', 'Pics', 'Sky Blue', 'XL', '', 127),
  ('Full Tshirt', 'Full Tshirt', 'HT-0031', 'HT', 'Men''s Full Tshirt', '', '240', '640', '143', '', 'Pics', 'Yellow', 'F', '', 128),
  ('Full Tshirt', 'Full Tshirt', 'AB-0016', 'AB', 'Men''s Full Tshirt', '', '120', '350', '300', '', 'Pics', 'Green', 'M', '', 129),
  ('Full Tshirt', 'Full Tshirt', 'HT-0017', 'HT', 'Men''s Full Tshirt', '', '280', '650', '143', '', 'Pics', 'Black', 'XL', '', 130),
  ('Full Tshirt', 'Full Tshirt', 'EX-0115', 'EX', 'Men''s Full Tshirt', '', '260', '650', '1195', '', 'Pics', 'Mixed', 'F', '', 131),
  ('Full Tshirt', 'Full Tshirt', 'EX-0117', 'EX', 'Men''s Full Tshirt', '', '320', '650', '1195', '', 'Pics', 'Mixed', 'F', '', 132),
  ('Full Tshirt', 'Full Tshirt', 'EX-0113', 'EX', 'Men''s Full Tshirt', '', '320', '650', '1195', '', 'Pics', 'Mixed', 'F', '', 133),
  ('Full Tshirt', 'Full Tshirt', 'EX-0114', 'EX', 'Men''s Full Tshirt', '', '320', '650', '1195', '', 'Pics', 'Mixed', 'F', '', 134),
  ('Full Tshirt', 'Full Tshirt', 'EX-0111', 'EX', 'Men''s Full Tshirt', '', '260', '650', '1195', '', 'Pics', 'Mixed', 'F', '', 135),
  ('Full Tshirt', 'Full Tshirt', 'EX-0107', 'EX', 'Men''s Full Tshirt', '', '320', '650', '1195', '', 'Pics', 'Mixed', 'F', '', 136),
  ('Full Tshirt', 'Full Tshirt', 'EX-0109', 'EX', 'Men''s Full Tshirt', '', '350', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 137),
  ('Full Tshirt', 'Full Tshirt', 'SF-0008', 'SF', 'Men''s Full Tshirt', '', '150', '380', '', '', 'Pics', 'Silver', 'F', '', 138),
  ('Full Tshirt', 'Full Tshirt', 'HT-0022', 'HT', 'Men''s Full Tshirt', '', '280', '650', '143', '', 'Pics', 'Mixed', 'XL', '', 139),
  ('Full Tshirt', 'Full Tshirt', 'HT-0030', 'HT', 'Men''s Full Tshirt', '', '250', '640', '143', '', 'Pics', 'Mixed', 'XL', '', 140),
  ('Full Tshirt', 'Full Tshirt', 'HT-0032', 'HT', 'Men''s Full Tshirt', '', '240', '640', '143', '', 'Pics', 'Green', 'F', '', 141),
  ('Full Tshirt', 'Full Tshirt', 'HT-0033', 'HT', 'Men''s Full Tshirt', '', '240', '640', '143', '', 'Pics', 'Black', 'F', '', 142),
  ('Full Tshirt', 'Full Tshirt', 'HT-0029', 'HT', 'Men''s Full Tshirt', '', '250', '640', '143', '', 'Pics', 'Red', 'F', '', 143),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0105', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Mixed', 'L', '', 144),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0086', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Brown', 'M', '', 145),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0087', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Blue', 'M', '', 146),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0098', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Yellow', 'M', '', 147),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0103', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Black', 'M', '', 148),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-00102', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Black', 'M', '', 149),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0096', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Brown', 'S', '', 150),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0092', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Brown', 'M', '', 151),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0100', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Black', 'S', '', 152),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'FC-0099', 'FC', 'Men''s Full Tshirt', '', '330', '750', '70', '', 'Pics', 'Yellow', 'S', '', 153),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0007', 'HT', 'Men''s Full Tshirt', '', '300', '850', '143', '', 'Pics', 'Mixed', 'XL', '', 154),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0009', 'HT', 'Men''s Full Tshirt', '', '300', '850', '143', '', 'Pics', 'Mixed', 'XL', '', 155),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0001', 'HT', 'Men''s Full Tshirt', '', '250', '750', '143', '', 'Pics', 'Mixed', 'F', '', 156),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0002', 'HT', 'Men''s Full Tshirt', '', '250', '750', '143', '', 'Pics', 'Mixed', 'F', '', 157),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0005', 'HT', 'Men''s Full Tshirt', '', '250', '750', '143', '', 'Pics', 'Mixed', 'F', '', 158),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0004', 'HT', 'Men''s Full Tshirt', '', '250', '750', '143', '', 'Pics', 'Mixed', 'F', '', 159),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0003', 'HT', 'Men''s Full Tshirt', '', '250', '750', '143', '', 'Pics', 'Mixed', 'F', '', 160),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0010', 'HT', 'Men''s Full Tshirt', '', '300', '650', '143', '', 'Pics', 'White', 'F', '', 161),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'HT-0014', 'HT', 'Men''s Full Tshirt', '', '280', '650', '143', '', 'Pics', 'Black', 'F', '', 162),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT-0000', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'F', '', 163),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0001', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'F', '', 164),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0002', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Black', 'S|M|L|XL|XXL|XXXL', '', 165),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0003', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 166),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0004', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 167),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0005', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 168),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0006', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 169),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0007', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 170),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0008', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 171),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0009', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 172),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0010', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 173),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0011', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 174),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0012', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 175),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0013', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 176),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0014', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 177),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0015', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 178),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0016', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 179),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0017', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 180),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0018', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 181),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0019', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 182),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0020', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 183),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0021', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 184),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0022', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 185),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0023', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 186),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0024', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 187),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0025', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 188),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0026', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 189),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0027', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 190),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0028', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 191),
  ('Full Polo Tshirt', 'Full Polo Tshirt', 'IND-MT0029', 'IND-MT', 'Men''s Full Tshirt', '', '730', '1150', '1', '', 'Pics', 'Mixed', 'S|M|L|XL|XXL|XXXL', '', 192),
  ('Full Tshirt', 'Full Tshirt', 'HT-0023', 'HT', 'Men''s Full Tshirt', '', '250', '650', '143', '', 'Pics', 'Mixed', 'XL', '', 193),
  ('Full Tshirt', 'Full Tshirt', 'AB-0015', 'AB', 'Men''s Full Tshirt', '', '120', '350', '300', '', 'Pics', 'Mixed', 'M', '', 194),
  ('Panjabi', 'Panjabi', 'IND-NK-0044', 'NK', 'Men''s Panjabi', '', '1150', '2350', '2', '', 'Pics', 'Merun', '44', '', 195),
  ('Panjabi', 'Panjabi', 'IND-NK-0045', 'NK', 'Men''s Panjabi', '', '1150', '2350', '2', '', 'Pics', 'Merun', '45', '', 196),
  ('Panjabi', 'Panjabi', 'IND-NK-0049', 'NK', 'Men''s Panjabi', '', '880', '2050', '2', '', 'Pair', 'White', '44', '', 197),
  ('Panjabi', 'Panjabi', 'IND-NK-0051', 'NK', 'Men''s Panjabi', '', '880', '2050', '2', '', 'Pair', 'White', '42', '', 198),
  ('Panjabi', 'Panjabi', 'IND-NK-0050', 'NK', 'Men''s Panjabi', '', '880', '2050', '2', '', 'Pair', 'White', '40', '', 199),
  ('Panjabi', 'Panjabi', 'IND-NK-0046', 'NK', 'Men''s Panjabi', '', '1150', '2350', '2', '', 'Pics', 'Merun', '42', '', 200),
  ('Panjabi', 'Panjabi', 'IND-NK-0035', 'NK', 'Men''s Panjabi', '', '1500', '4400', '2', '', 'Pair', 'Ligtt Pink', '44', '', 201),
  ('Panjabi', 'Panjabi', 'IND-NK-0033', 'NK', 'Men''s Panjabi', '', '1500', '4400', '2', '', 'Pair', 'Biskit', 'F', '', 202),
  ('Panjabi', 'Panjabi', 'IND-NK-0048', 'NK', 'Men''s Panjabi', '', '1150', '2350', '2', '', 'Pics', 'Green', '44', '', 203),
  ('Panjabi', 'Panjabi', 'IND-NK-0031', 'NK', 'Men''s Panjabi', '', '1450', '2750', '2', '', 'Pics', 'Silver', '44', '', 204),
  ('Panjabi', 'Panjabi', 'IND-NK-0037', 'NK', 'Men''s Panjabi', '', '900', '2050', '2', '', 'Pics', 'Light purple', '42', '', 205),
  ('Panjabi', 'Panjabi', 'IND-NK-0022', 'NK', 'Men''s Panjabi', '', '890', '1700', '2', '', 'Pics', 'White', '42', '', 206),
  ('Panjabi', 'Panjabi', 'IND-NK-0017', 'NK', 'Men''s Panjabi', '', '890', '1700', '2', '', 'Pics', 'Sky Blue', '42', '', 207),
  ('Panjabi', 'Panjabi', 'IND-NK-0024', 'NK', 'Men''s Panjabi', '', '890', '1700', '2', '', 'Pics', 'White', '42', '', 208),
  ('Panjabi', 'Panjabi', 'IND-NK-0006', 'NK', 'Men''s Panjabi', '', '820', '1480', '2', '', 'Pics', 'Ofwhite', '44', '', 209),
  ('Panjabi', 'Panjabi', 'IND-NK-0010', 'NK', 'Men''s Panjabi', '', '820', '1450', '2', '', 'Pics', 'Pest', '44', '', 210),
  ('Panjabi', 'Panjabi', 'IND-NK-0011', 'NK', 'Men''s Panjabi', '', '820', '1450', '2', '', 'Pics', 'Pest', '42', '', 211),
  ('Panjabi', 'Panjabi', 'IND-NK-0032', 'NK', 'Men''s Panjabi', '', '1450', '2750', '2', '', 'Pics', 'Silver', '42', '', 212),
  ('Panjabi', 'Panjabi', 'IND-NK-0040', 'NK', 'Men''s Panjabi', '', '1200', '2550', '2', '', 'Pics', 'Merun', '44', '', 213),
  ('Panjabi', 'Panjabi', 'IND-NK-0034', 'NK', 'Men''s Panjabi', '', '1500', '4400', '2', '', 'Pair', 'Sky Blue', 'F', '', 214),
  ('Panjabi', 'Panjabi', 'IND-NK-0036', 'NK', 'Men''s Panjabi', '', '900', '2450', '2', '', 'Pair', 'Mixed', 'L', '', 215),
  ('Panjabi', 'Panjabi', 'IND-NK-0038', 'NK', 'Men''s Panjabi', '', '900', '2050', '2', '', 'Pics', 'Purple', '44', '', 216),
  ('Panjabi', 'Panjabi', 'IND-NK-0043', 'NK', 'Men''s Panjabi', '', '1150', '2350', '2', '', 'Pics', 'Olive', '40', '', 217),
  ('Panjabi', 'Panjabi', 'IND-NK-0020', 'NK', 'Men''s Panjabi', '', '890', '1800', '2', '', 'Pics', 'Sky Blue', '40', '', 218),
  ('Panjabi', 'Panjabi', 'IND-NK-0012', 'NK', 'Men''s Panjabi', '', '820', '1450', '2', '', 'Pics', 'Pest', '40', '', 219),
  ('Panjabi', 'Panjabi', 'IND-NK-0004', 'NK', 'Men''s Panjabi', '', '820', '1480', '2', '', 'Pics', 'Silver', '42', '', 220),
  ('Panjabi', 'Panjabi', 'IND-NK-0002', 'NK', 'Men''s Panjabi', '', '820', '1480', '2', '', 'Pics', 'Ofwhite', '40', '', 221),
  ('Panjabi', 'Panjabi', 'IND-NK-0026', 'NK', 'Men''s Panjabi', '', '820', '1450', '2', '', 'Pics', 'Sky Blue', '44', '', 222),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0020', 'IND-SS', 'Mens Casual Shirt', '', '1050', '1680', '819', '', 'Pics', 'White', 'L', '', 223),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0023', 'IND-SS', 'Mens Casual Shirt', '', '1050', '1680', '819', '', 'Pics', 'Golden', 'M', '', 224),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0051', 'IND-SS', 'Mens Casual Shirt', '', '1240', '2050', '819', '', 'Pics', 'Blue', 'L', '', 225),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0053', 'IND-SS', 'Mens Casual Shirt', '', '1240', '2050', '819', '', 'Pics', 'Blue', 'XL', '', 226),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0032', 'IND-SS', 'Mens Casual Shirt', '', '1175', '1760', '819', '', 'Pics', 'Light Orange', 'L', '', 227),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0031', 'IND-SS', 'Mens Casual Shirt', '', '1175', '1760', '819', '', 'Pics', 'Yellow', 'XL', '', 228),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0014', 'IND-SS', 'Mens Casual Shirt', '', '850', '1550', '819', '', 'Pics', 'Sky Blue', 'M', '', 229),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0012', 'IND-SS', 'Mens Casual Shirt', '', '850', '1550', '819', '', 'Pics', 'Sky Blue', 'XL', '', 230),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0017', 'IND-SS', 'Mens Casual Shirt', '', '1050', '1680', '819', '', 'Pics', 'Mixed', 'XL', '', 231),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0027', 'IND-SS', 'Mens Casual Shirt', '', '1120', '1760', '819', '', 'Pics', 'Mixed', 'M', '', 232),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0039', 'IND-SS', 'Mens Casual Shirt', '', '1175', '1760', '819', '', 'Pics', 'Merun', 'L', '', 233),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0044', 'IND-SS', 'Mens Casual Shirt', '', '1175', '1760', '819', '', 'Pics', 'Pink', 'L', '', 234),
  ('Casual Shirt', 'Casual Shirt', 'IND-SS-0035', 'IND-SS', 'Mens Casual Shirt', '', '1175', '1760', '819', '', 'Pics', 'Light Orange', 'L', '', 235),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0074', 'IND-DIV', 'Mens Casual Shirt', '', '820', '1550', '941', '', 'Pics', 'White', 'M', '', 236),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0073', 'IND-DIV', 'Mens Casual Shirt', '', '820', '1550', '941', '', 'Pics', 'White', 'L', '', 237),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0072', 'IND-DIV', 'Mens Casual Shirt', '', '820', '1550', '941', '', 'Pics', 'White', 'S', '', 238),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0042', 'IND-DIV', 'Mens Casual Shirt', '', '950', '1650', '941', '', 'Pics', 'Yellow', 'M', '', 239),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0041', 'IND-DIV', 'Mens Casual Shirt', '', '950', '1650', '941', '', 'Pics', 'Yellow', 'XL', '', 240),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0047', 'IND-DIV', 'Mens Casual Shirt', '', '1090', '1850', '941', '', 'Pics', 'White', 'L', '', 241),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0048', 'IND-DIV', 'Mens Casual Shirt', '', '1090', '1850', '941', '', 'Pics', 'White', 'XL', '', 242),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0046', 'IND-DIV', 'Mens Casual Shirt', '', '1090', '1850', '941', '', 'Pics', 'White', 'M', '', 243),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0050', 'IND-DIV', 'Mens Casual Shirt', '', '1020', '1850', '941', '', 'Pics', 'Black', 'XL', '', 244),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0051', 'IND-DIV', 'Mens Casual Shirt', '', '1020', '1850', '941', '', 'Pics', 'Black', 'XXL', '', 245),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0052', 'IND-DIV', 'Mens Casual Shirt', '', '750', '1700', '941', '', 'Pics', 'Yellow', 'L', '', 246),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0064', 'IND-DIV', 'Mens Casual Shirt', '', '1090', '1850', '941', '', 'Pics', 'Mixed', 'XL', '', 247),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0079', 'IND-DIV', 'Mens Casual Shirt', '', '1075', '1600', '939', '', 'Pics', 'Orange', 'XL', '', 248),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0081', 'IND-DIV', 'Mens Casual Shirt', '', '1075', '1600', '939', '', 'Pics', 'Orange', 'M', '', 249),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0080', 'IND-DIV', 'Mens Casual Shirt', '', '1075', '1600', '939', '', 'Pics', 'Orange', 'XXL', '', 250),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0067', 'IND-DIV', 'Mens Casual Shirt', '', '1090', '1850', '939', '', 'Pics', 'Sky Blue', 'L', '', 251),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0065', 'IND-DIV', 'Mens Casual Shirt', '', '1090', '1850', '941', '', 'Pics', 'Black', 'L', '', 252),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0083', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '393', '', 'Pics', 'Mixed', 'XXL', '', 253),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0086', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '393', '', 'Pics', 'Mixed', 'XL', '', 254),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0077', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1600', '393', '', 'Pics', 'Mixed', 'XXL', '', 255),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0075', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1600', '393', '', 'Pics', 'Mixed', 'XL', '', 256),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0088', 'IND-DIV', 'Mens Casual Shirt', '', '1110', '1950', '393', '', 'Pics', 'Mixed', 'M', '', 257),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0087', 'IND-DIV', 'Mens Casual Shirt', '', '1110', '1950', '393', '', 'Pics', 'Mixed', 'L', '', 258),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0130', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '3', '', 'Pics', 'Pest', 'XXL', '', 259),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0131', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '3', '', 'Pics', 'Pest', 'L', '', 260),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0032', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '3', '', 'Pics', 'Pest', 'M', '', 261),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0133', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '3', '', 'Pics', 'Mixed', 'M', '', 262),
  ('Casual Shirt', 'Casual Shirt', 'IND-DIV-0134', 'IND-DIV', 'Mens Casual Shirt', '', '885', '1650', '3', '', 'Pics', 'Mixed', 'XL', '', 263),
  ('Panjabi', 'Panjabi', 'NP-0036', 'NP', 'Men''s Panjabi', '', '1000', '1400', '201', '', 'Pics', 'White', '42', '', 264),
  ('Panjabi', 'Panjabi', 'NP-0027', 'NP', 'Men''s Panjabi', '', '890', '1300', '201', '', 'Pics', 'Silver', 'XL', '', 265),
  ('Panjabi', 'Panjabi', 'NP-0034', 'NP', 'Men''s Panjabi', '', '1000', '1400', '201', '', 'Pics', 'White', '44', '', 266),
  ('Panjabi', 'Panjabi', 'NP-0019', 'NP', 'Men''s Panjabi', '', '890', '1300', '201', '', 'Pics', 'Mixed', '38', '', 267),
  ('Panjabi', 'Panjabi', 'NP-0017', 'NP', 'Men''s Panjabi', '', '890', '1300', '201', '', 'Pics', 'Olive', '40', '', 268),
  ('Panjabi', 'Panjabi', 'NP-0026', 'NP', 'Men''s Panjabi', '', '890', '1300', '201', '', 'Pics', 'Purple', 'XXL', '', 269),
  ('Panjabi', 'Panjabi', 'NP-0025', 'NP', 'Men''s Panjabi', '', '890', '1300', '201', '', 'Pics', 'Olive', 'XXL', '', 270),
  ('Panjabi', 'Panjabi', 'NP-0044', 'NP', 'Men''s Panjabi', '', '835', '1170', '201', '', 'Pics', 'White', '42', '', 271),
  ('Panjabi', 'Panjabi', 'NP-0045', 'NP', 'Men''s Panjabi', '', '355', '1170', '201', '', 'Pics', 'White', '40', '', 272),
  ('Panjabi', 'Panjabi', 'NP-0018', 'NP', 'Men''s Panjabi', '', '890', '1300', '201', '', 'Pics', 'Silver', 'XXL', '', 273),
  ('Panjabi', 'Panjabi', 'CM-0067', 'CM', 'Men''s Panjabi', '', '750', '1250', '356', '', 'Pics', 'Mixed', 'M', '', 274),
  ('Panjabi', 'Panjabi', 'CM-0069', 'CM', 'Men''s Panjabi', '', '750', '1250', '356', '', 'Pics', 'Mixed', 'L', '', 275),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0073', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'Ofwhite', 'M', '', 276),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0080', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'Green', 'M', '', 277),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0065', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'Pest', 'M', '', 278),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0098', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3250', '', 'Pics', 'Black', 'M', '', 279),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0078', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3248', '', 'Pics', 'Silver', 'M', '', 280),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0084', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3246', '', 'Pics', 'Blue', 'M', '', 281),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0109', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Coffy', 'L', '', 282),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0118', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Pink', 'XL', '', 283),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0090', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3250', '', 'Pics', 'Yellow', 'XL', '', 284),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0108', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Pink', 'XL', '', 285),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0105', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Coffy', 'L', '', 286),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0106', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Coffy', 'XL', '', 287),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0114', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Pink', 'XL', '', 288),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0122', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Pink', 'L', '', 289),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0064', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'White', 'M', '', 290),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0079', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'White', 'M', '', 291),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0115', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Ofwhite', 'XL', '', 292),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0071', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'Ofwhite', 'M', '', 293),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0009', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3240', '', 'Pics', 'Ofwhite', 'XL', '', 294),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0100', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Pest', 'XL', '', 295),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0002', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3240', '', 'Pics', 'Pest', 'L', '', 296),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0012', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3240', '', 'Pics', 'Pest', 'XL', '', 297),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0112', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Pest', 'L', '', 298),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0097', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Black', 'XL', '', 299),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0099', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Black', 'XL', '', 300),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0005', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3240', '', 'Pics', 'Sky Blue', 'XL', '', 301),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0076', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3246', '', 'Pics', 'Sky Blue', 'XL', '', 302),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0113', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Light Orange', 'XL', '', 303),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0101', 'JK', 'Gents Polo Tshirt', '', '550', '870', '3258', '', 'Pics', 'Botton Green', 'XL', '', 304),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0001', 'JK', 'Gents Polo Tshirt', '', '580', '870', '3240', '', 'Pics', 'Blue', 'XL', '', 305),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0018', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'L', '', 306),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0019', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'M', '', 307),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0020', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'L', '', 308),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0021', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Silver', 'M', '', 309),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0022', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'L', '', 310),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0023', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'L', '', 311),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0024', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'M', '', 312),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0025', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'White', 'XL', '', 313),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0026', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'White', 'M', '', 314),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0027', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Mixed', 'L', '', 315),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0028', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'White', 'XL', '', 316),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0029', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Black', 'M', '', 317),
  ('Polo Tshirt', 'Polo Tshirt', 'JK-0030', 'JK', 'Gents Polo Tshirt', '', '750', '1150', '3240', '', 'Pics', 'Green', '50', '', 318),
  ('Casual Shirt', 'Casual Shirt', 'FC-0109', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Olive', 'M', '', 319),
  ('Casual Shirt', 'Casual Shirt', 'FC-0112', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Sky Blue', 'L', '', 320),
  ('Casual Shirt', 'Casual Shirt', 'FC-0116', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Sky Blue', 'XL', '', 321),
  ('Casual Shirt', 'Casual Shirt', 'FC-0110', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Sky Blue', 'M', '', 322),
  ('Casual Shirt', 'Casual Shirt', 'FC-0107', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Orange', 'M', '', 323),
  ('Casual Shirt', 'Casual Shirt', 'FC-0106', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Orange', 'L', '', 324),
  ('Casual Shirt', 'Casual Shirt', 'FC-0108', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Orange', 'XL', '', 325),
  ('Casual Shirt', 'Casual Shirt', 'FC-0114', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Yellow', 'M', '', 326),
  ('Casual Shirt', 'Casual Shirt', 'FC-0111', 'FC', 'Mens Casual Shirt', '', '400', '1050', '70', '', 'Pics', 'Yellow', 'L', '', 327),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0044', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Green', 'M', '', 328),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0110', 'FC', 'Gents Polo Tshirt', '', '290', '580', '35', '', 'Pics', 'Green', 'L', '', 329),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0045', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Coffy', 'M', '', 330),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0046', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Orange', 'XL', '', 331),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0047', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Orange', 'XL', '', 332),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0048', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Orange', 'L', '', 333),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0049', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Silver', 'XL', '', 334),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0050', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Merun', 'XL', '', 335),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0051', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Merun', 'L', '', 336),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0052', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Merun', 'M', '', 337),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0053', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Merun', 'XL', '', 338),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0054', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Olive', 'XL', '', 339),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0055', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Sky Blue', 'L', '', 340),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0056', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Orange', 'M', '', 341),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0057', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Silver', 'L', '', 342),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0058', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Pink', 'M', '', 343),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0059', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Silver', 'XL', '', 344),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0060', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Sky Blue', 'M', '', 345),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0061', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Merun', 'L', '', 346),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0062', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Silver', 'L', '', 347),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0062', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Silver', 'M', '', 348),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0063', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Pink', 'M', '', 349),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0064', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Yellow', 'L', '', 350),
  ('Polo Tshirt', 'Polo Tshirt', 'FC-0065', 'FC', 'Gents Polo Tshirt', '', '290', '580', '43', '', 'Pics', 'Orange', 'L', '', 351),
  ('Body Spray', 'Body Spray', 'SB-0045', 'SB', 'Gents Body spray', '', '190', '320', '5310', '', 'Pics', '', '', '', 352),
  ('Body Spray', 'Body Spray', 'SB-0031', 'SB', 'Gents Body spray', '', '235', '390', '5310', '', 'Pics', '', '', '', 353),
  ('Body Spray', 'Body Spray', 'SB-0043', 'SB', 'Gents Body spray', '', '190', '320', '5310', '', 'Pics', '', '', '', 354),
  ('Body Spray', 'Body Spray', 'SB-0040', 'SB', 'Gents Body spray', '', '155', '280', '5310', '', 'Pics', '', '', '', 355),
  ('Body Spray', 'Body Spray', 'SB-0041', 'SB', 'Gents Body spray', '', '155', '280', '5310', '', 'Pics', '', '', '', 356),
  ('Body Spray', 'Body Spray', 'SB-0037', 'SB', 'Gents Body spray', '', '170', '360', '5310', '', 'Pics', '', '', '', 357),
  ('Body Spray', 'Body Spray', 'SB-0036', 'SB', 'Gents Body spray', '', '170', '360', '5310', '', 'Pics', '', '', '', 358),
  ('Body Spray', 'Body Spray', 'SB-0044', 'SB', 'Gents Body spray', '', '190', '320', '5310', '', 'Pics', '', '', '', 359),
  ('Body Spray', 'Body Spray', 'SB-0053', 'SB', 'Gents Body spray', '', '190', '320', '5310', '', 'Pics', '', '', '', 360),
  ('Body Spray', 'Body Spray', 'SB-0047', 'SB', 'Gents Body spray', '', '270', '510', '5310', '', 'Pics', '', '', '', 361),
  ('Body Spray', 'Body Spray', 'SB-0046', 'SB', 'Gents Body spray', '', '270', '510', '5310', '', 'Pics', '', '', '', 362),
  ('Body Spray', 'Body Spray', 'SB-0025', 'SB', 'Gents Body spray', '', '190', '320', '5310', '', 'Pics', '', '', '', 363),
  ('Body Spray', 'Body Spray', 'SB-0024', 'SB', 'Gents Body spray', '', '190', '320', '5310', '', 'Pics', '', '', '', 364),
  ('Body Spray', 'Body Spray', 'SB-0048', 'SB', 'Gents Body spray', '', '270', '510', '5310', '', 'Pics', '', '', '', 365),
  ('Body Spray', 'Body Spray', 'SB-0038', 'SB', 'Gents Body spray', '', '170', '360', '5310', '', 'Pics', '', '', '', 366),
  ('Body Spray', 'Body Spray', 'SB-0039', 'SB', 'Gents Body spray', '', '170', '360', '5310', '', 'Pics', '', '', '', 367),
  ('Body Spray', 'Body Spray', 'SB-0035', 'SB', 'Gents Body spray', '', '170', '360', '5310', '', 'Pics', '', '', '', 368),
  ('Body Spray', 'Body Spray', 'SB-0049', 'SB', 'Gents Body spray', '', '185', '295', '5310', '', 'Pics', '', '', '', 369),
  ('Body Spray', 'Body Spray', 'SB-0029', 'SB', 'Gents Body spray', '', '235', '390', '5310', '', 'Pics', '', '', '', 370),
  ('Body Spray', 'Body Spray', 'SB-0030', 'SB', 'Gents Body spray', '', '235', '390', '5310', '', 'Pics', '', '', '', 371),
  ('Body Spray', 'Body Spray', 'SB-0032', 'SB', 'Gents Body spray', '', '235', '390', '5310', '', 'Pics', '', '', '', 372),
  ('Body Spray', 'Body Spray', 'SB-0028', 'SB', 'Gents Body spray', '', '235', '390', '5310', '', 'Pics', '', '', '', 373),
  ('Body Spray', 'Body Spray', 'SB-0017', 'SB', 'Gents Body spray', '', '215', '340', '5310', '', 'Pics', '', '', '', 374),
  ('Body Spray', 'Body Spray', 'SB-0018', 'SB', 'Gents Body spray', '', '215', '340', '5310', '', 'Pics', '', '', '', 375),
  ('Body Spray', 'Body Spray', 'SB-0021', 'SB', 'Gents Belt', '', '155', '280', '5310', '', 'Pics', '', '', '', 376),
  ('Belt', 'Belt', 'CLC-0076', 'CLC', 'Gents Belt', '', '370', '740', '1387', '', 'Pics', '', '', '', 377),
  ('Belt', 'Belt', 'CLC-0072', 'CLC', 'Gents Belt', '', '420', '840', '1387', '', 'Pics', '', '', '', 378),
  ('Belt', 'Belt', 'CLC-0075', 'CLC', 'Gents Belt', '', '370', '740', '1387', '', 'Pics', '', '', '', 379);
INSERT INTO `product_import` (`name`, `web`, `code`, `vendor_code`, `category`, `quantity`, `purchase_price`, `sales_price`, `memo_no`, `rack`, `unit`, `color`, `dimension`, `extra_1`, `id`) VALUES
  ('Belt', 'Belt', 'CLC-0079', 'CLC', 'Gents Belt', '', '360', '750', '1387', '', 'Pics', '', '', '', 380),
  ('Belt', 'Belt', 'CLC-0069', 'CLC', 'Gents Belt', '', '500', '950', '1387', '', 'Pics', '', '', '', 381),
  ('Belt', 'Belt', 'CLC-0070', 'CLC', 'Gents Belt', '', '390', '780', '1387', '', 'Pics', '', '', '', 382),
  ('Belt', 'Belt', 'CLC-0071', 'CLC', 'Gents Belt', '', '450', '880', '1387', '', 'Pics', '', '', '', 383),
  ('Belt', 'Belt', 'CLC-0067', 'CLC', 'Gents Belt', '', '340', '680', '1387', '', 'Pics', '', '', '', 384),
  ('Belt', 'Belt', 'CLC-0062', 'CLC', 'Gents Belt', '', '340', '680', '1387', '', 'Pics', '', '', '', 385),
  ('Belt', 'Belt', 'CLC-0068', 'CLC', 'Gents Belt', '', '340', '680', '1387', '', 'Pics', '', '', '', 386),
  ('Belt', 'Belt', 'CLC-0046', 'CLC', 'Gents Belt', '', '430', '850', '664', '', 'Pics', '', '', '', 387),
  ('Belt', 'Belt', 'CLC-0066', 'CLC', 'Gents Belt', '', '340', '680', '664', '', 'Pics', '', '', '', 388),
  ('Belt', 'Belt', 'CLC-0073', 'CLC', 'Gents Belt', '', '340', '680', '664', '', 'Pics', '', '', '', 389),
  ('Belt', 'Belt', 'CLC-0074', 'CLC', 'Gents Belt', '', '370', '740', '664', '', 'Pics', '', '', '', 390),
  ('Belt', 'Belt', 'CLC-0065', 'CLC', 'Gents Belt', '', '340', '680', '664', '', 'Pics', '', '', '', 391),
  ('Belt', 'Belt', 'CLC-0077', 'CLC', 'Gents Belt', '', '360', '720', '664', '', 'Pics', '', '', '', 392),
  ('Wallet', 'Wallet', 'CLC-0012', 'CLC', 'Gents Wallet', '', '550', '825', '664', '', 'Pics', '', '', '', 393),
  ('Wallet', 'Wallet', 'CLC-0014', 'CLC', 'Gents Wallet', '', '450', '775', '664', '', 'Pics', '', '', '', 394),
  ('Wallet', 'Wallet', 'CLC-0057', 'CLC', 'Gents Wallet', '', '520', '940', '664', '', 'Pics', '', '', '', 395),
  ('Wallet', 'Wallet', 'CLC-0087', 'CLC', 'Gents Wallet', '', '320', '770', '1386', '', 'Pics', '', '', '', 396),
  ('Wallet', 'Wallet', 'CLC-0086', 'CLC', 'Gents Wallet', '', '320', '770', '1386', '', 'Pics', '', '', '', 397),
  ('Wallet', 'Wallet', 'CLC-0090', 'CLC', 'Gents Wallet', '', '370', '840', '1386', '', 'Pics', '', '', '', 398),
  ('Wallet', 'Wallet', 'CLC-0084', 'CLC', 'Gents Wallet', '', '320', '730', '1386', '', 'Pics', '', '', '', 399),
  ('Wallet', 'Wallet', 'CLC-0089', 'CLC', 'Gents Wallet', '', '370', '840', '1386', '', 'Pics', '', '', '', 400),
  ('Wallet', 'Wallet', 'CLC-0085', 'CLC', 'Gents Wallet', '', '320', '730', '1386', '', 'Pics', '', '', '', 401),
  ('Wallet', 'Wallet', 'CLC-0094', 'CLC', 'Gents Wallet', '', '460', '950', '1386', '', 'Pics', '', '', '', 402),
  ('Wallet', 'Wallet', 'CLC-0091', 'CLC', 'Gents Wallet', '', '450', '1050', '1386', '', 'Pics', '', '', '', 403),
  ('Wallet', 'Wallet', 'CLC-0096', 'CLC', 'Gents Wallet', '', '150', '350', '1386', '', 'Pics', '', '', '', 404),
  ('Tai', 'Tai', 'BB-0052', 'BB', 'Gents Tai', '', '350', '525', '10', '', 'Pics', 'Red', '', '', 405),
  ('Tai', 'Tai', 'BB-0043', 'BB', 'Gents Tai', '', '250', '420', '10', '', 'Pics', 'Pink', '', '', 406),
  ('Tai', 'Tai', 'BB-0048', 'BB', 'Gents Tai', '', '350', '525', '10', '', 'Pics', 'Black', '', '', 407),
  ('Tai', 'Tai', 'BB-0049', 'BB', 'Gents Tai', '', '350', '525', '10', '', 'Pics', 'Merun', '', '', 408),
  ('Tai', 'Tai', 'BB-0050', 'BB', 'Gents Tai', '', '350', '525', '10', '', 'Pics', 'Silver', '', '', 409),
  ('Tai Pin', 'Tai Pin', 'BB-0067', 'BB', 'Gents Tai Pin', '', '180', '277', '6829', '', 'Pics', 'Golden', '', '', 410),
  ('Tai Pin', 'Tai Pin', 'BB-0069', 'BB', 'Gents Tai Pin', '', '180', '277', '6829', '', 'Pics', 'Golden', '', '', 411),
  ('Tai Pin', 'Tai Pin', 'BB-0071', 'BB', 'Gents Tai Pin', '', '180', '277', '6829', '', 'Pics', 'Golden', '', '', 412),
  ('Tai Pin', 'Tai Pin', 'BB-0066', 'BB', 'Gents Tai Pin', '', '180', '277', '6829', '', 'Pics', 'Golden', '', '', 413),
  ('Tai Pin', 'Tai Pin', 'BB-0068', 'BB', 'Gents Tai Pin', '', '180', '277', '6829', '', 'Pics', 'Silver', '', '', 414),
  ('Culflen', 'Culflen', 'BB-0063', 'BB', 'Gents Culflen', '', '450', '650', '6829', '', 'Pics', 'Black', '', '', 415),
  ('Culflen', 'Culflen', 'BB-0054', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Black', '', '', 416),
  ('Culflen', 'Culflen', 'BB-0060', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Golden', '', '', 417),
  ('Culflen', 'Culflen', 'BB-0056', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Golden', '', '', 418),
  ('Culflen', 'Culflen', 'BB-0057', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Golden', '', '', 419),
  ('Culflen', 'Culflen', 'BB-0059', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Black', '', '', 420),
  ('Culflen', 'Culflen', 'BB-0055', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Black', '', '', 421),
  ('Culflen', 'Culflen', 'BB-0061', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Black', '', '', 422),
  ('Culflen', 'Culflen', 'BB-0058', 'BB', 'Gents Culflen', '', '480', '650', '10', '', 'Pics', 'Golden', '', '', 423),
  ('Culflen', 'Culflen', 'BB-0064', 'BB', 'Gents Culflen', '', '450', '650', '6829', '', 'Pics', 'Silver', '', '', 424),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0090', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1550', '941', '', 'Pics', 'Mixed', 'F', '', 425),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0096', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 426),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0104', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'Mixed', 'F', '', 427),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0114', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'Mixed', 'F', '', 428),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0100', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1750', '941', '', 'Pics', 'Blue', 'F', '', 429),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0106', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'White', 'F', '', 430),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0118', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'Mixed', 'F', '', 431),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0119', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'Mixed', 'F', '', 432),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0091', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1550', '941', '', 'Pics', 'Mixed', 'F', '', 433),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0089', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1550', '941', '', 'Pics', 'Mixed', 'F', '', 434),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0129', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1550', '941', '', 'Pics', 'Mixed', 'F', '', 435),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0101', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 436),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0092', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1550', '941', '', 'Pics', 'Mixed', 'F', '', 437),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0098', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 438),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0105', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'Mixed', 'F', '', 439),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-DIV-0120', 'IND-DIV', 'Gents Polo Tshirt', '', '950', '1650', '941', '', 'Pics', 'Mixed', 'F', '', 440),
  ('Tshirt', 'Tshirt', 'IND-DIV-0127', 'IND-DIV', 'Gents Tshirt', '', '750', '1350', '941', '', 'Pics', 'White', 'XXL', '', 441),
  ('Tshirt', 'Tshirt', 'IND-DIV-0126', 'IND-DIV', 'Gents Tshirt', '', '750', '1350', '941', '', 'Pics', 'White', 'XXL', '', 442),
  ('Tshirt', 'Tshirt', 'IND-DIV-0122', 'IND-DIV', 'Gents Tshirt', '', '750', '1350', '941', '', 'Pics', 'Biskit', 'XXL', '', 443),
  ('Tshirt', 'Tshirt', 'IND-DIV-0128', 'IND-DIV', 'Gents Tshirt', '', '750', '1350', '941', '', 'Pics', 'Black', 'XXL', '', 444),
  ('Tshirt', 'Tshirt', 'IND-DIV-0124', 'IND-DIV', 'Gents Tshirt', '', '750', '1350', '941', '', 'Pics', 'Biskit', 'XXL', '', 445),
  ('Tshirt', 'Tshirt', 'IND-DIV-0111', 'IND-DIV', 'Gents Tshirt', '', '750', '1350', '941', '', 'Pics', 'Blue', 'XXL', '', 446),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-ISS-0002', 'IND-ISS', 'Gents Polo Tshirt', '', '750', '1450', '941', '', 'Pics', 'Yellow', 'L', '', 447),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-ISS-0001', 'IND-ISS', 'Gents Polo Tshirt', '', '750', '1450', '941', '', 'Pics', 'Yellow', 'M', '', 448),
  ('Polo Tshirt', 'Polo Tshirt', 'IND-ISS-0004', 'IND-ISS', 'Gents Polo Tshirt', '', '750', '1450', '941', '', 'Pics', 'Yellow', 'XL', '', 449),
  ('Tshirt', 'Tshirt', 'BB-0079', 'BB', 'Gents Tshirt', '', '650', '950', '6829', '', 'Pics', 'Silver', '46', '', 450),
  ('Tshirt', 'Tshirt', 'BB-0073', 'BB', 'Gents Tshirt', '', '650', '950', '6829', '', 'Pics', 'Black', '52', '', 451),
  ('Tshirt', 'Tshirt', 'BB-0077', 'BB', 'Gents Tshirt', '', '650', '950', '6829', '', 'Pics', 'Silver', '52', '', 452),
  ('Tshirt', 'Tshirt', 'BB-0074', 'BB', 'Gents Tshirt', '', '650', '950', '6829', '', 'Pics', 'Silver', '54', '', 453),
  ('Tshirt', 'Tshirt', 'BB-0076', 'BB', 'Gents Tshirt', '', '650', '950', '6829', '', 'Pics', 'Silver', '54', '', 454),
  ('Tshirt', 'Tshirt', 'JK-0063', 'JK', 'Gents Tshirt', '', '550', '825', '3243', '', 'Pics', 'Mixed', 'XXL', '', 455),
  ('Short Pant', 'Short Pant', 'XM-0008', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Coffy', '30', '', 456),
  ('Short Pant', 'Short Pant', 'XM-0012', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Black', '30', '', 457),
  ('Short Pant', 'Short Pant', 'XM-0009', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Red', '30', '', 458),
  ('Short Pant', 'Short Pant', 'XM-0018', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Green', '30', '', 459),
  ('Short Pant', 'Short Pant', 'XM-0013', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Green', '32', '', 460),
  ('Short Pant', 'Short Pant', 'XM-0003', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Black', '32', '', 461),
  ('Short Pant', 'Short Pant', 'XM-0017', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Black', '32', '', 462),
  ('Short Pant', 'Short Pant', 'XM-0015', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Black', '30', '', 463),
  ('Short Pant', 'Short Pant', 'XM-0006', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Green', '30', '', 464),
  ('Short Pant', 'Short Pant', 'XM-0002', 'XM', 'Gents Short Pant', '', '310', '650', '53', '', 'Pics', 'Green', '30', '', 465),
  ('Blezar', 'Blezar', 'IND-IBL-0006', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Purple', '38', '', 466),
  ('Blezar', 'Blezar', 'IND-IBL-0002', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Merun', '40', '', 467),
  ('Blezar', 'Blezar', 'IND-IBL-0003', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Merun', '36', '', 468),
  ('Blezar', 'Blezar', 'IND-IBL-0004', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Merun', '34', '', 469),
  ('Blezar', 'Blezar', 'IND-IBL-0001', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Merun', '38', '', 470),
  ('Blezar', 'Blezar', 'IND-IBL-0008', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Purple', '42', '', 471),
  ('Blezar', 'Blezar', 'IND-IBL-0007', 'IND-IBL', 'Gents Blezar', '', '2175', '3650', '4', '', 'Pics', 'Purple', '40', '', 472),
  ('Blezar', 'Blezar', 'IND-IBL-0011', 'IND-IBL', 'Gents Blezar', '', '1950', '3450', '4', '', 'Pics', 'Silver', '42', '', 473),
  ('Blezar', 'Blezar', 'IND-IBL-0012', 'IND-IBL', 'Gents Blezar', '', '1950', '3850', '4', '', 'Pics', 'Purple', '40', '', 474),
  ('Blezar', 'Blezar', 'IND-IBL-0016', 'IND-IBL', 'Gents Blezar', '', '1950', '3450', '4', '', 'Pics', 'Silver', '42', '', 475),
  ('Blezar', 'Blezar', 'IND-IBL-0018', 'IND-IBL', 'Gents Blezar', '', '1950', '3450', '4', '', 'Pics', 'Silver', '36', '', 476),
  ('Coti', 'Coti', 'IND-DIV-0013', 'IND-DIV', 'Gents Coti', '', '950', '1850', 'Not provided', '', 'Pics', 'Mixed', '34', '', 477),
  ('Coti', 'Coti', 'IND-DIV-0006', 'IND-DIV', 'Gents Coti', '', '950', '1850', '', '', 'Pics', 'Mixed', '38', '', 478),
  ('Coti', 'Coti', 'IND-DIV-0013', 'IND-DIV', 'Gents Coti', '', '950', '1850', '', '', 'Pics', 'Mixed', '34', '', 479),
  ('Coti', 'Coti', 'IND-DIV-0007', 'IND-DIV', 'Gents Coti', '', '950', '1850', '', '', 'Pics', 'Mixed', '36', '', 480),
  ('Coti', 'Coti', 'IND-DIV-0008', 'IND-DIV', 'Gents Coti', '', '950', '1850', '', '', 'Pics', 'Mixed', '42', '', 481),
  ('Coti', 'Coti', 'IND-DIV-0026', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Black', '40', '', 482),
  ('Coti', 'Coti', 'IND-DIV-0021', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Black', '42', '', 483),
  ('Coti', 'Coti', 'IND-DIV-0027', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Black', '42', '', 484),
  ('Coti', 'Coti', 'IND-DIV-0028', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Black', '40', '', 485),
  ('Coti', 'Coti', 'IND-DIV-0030', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Black', '38', '', 486),
  ('Coti', 'Coti', 'IND-DIV-0010', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Purple', '40', '', 487),
  ('Coti', 'Coti', 'IND-DIV-0035', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'White', '42', '', 488),
  ('Coti', 'Coti', 'IND-DIV-0034', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'White', '40', '', 489),
  ('Coti', 'Coti', 'IND-DIV-0024', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Golden', '42', '', 490),
  ('Coti', 'Coti', 'IND-DIV-0025', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Golden', '40', '', 491),
  ('Coti', 'Coti', 'IND-DIV-0023', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Golden', '36', '', 492),
  ('Coti', 'Coti', 'IND-DIV-0011', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Purple', '36', '', 493),
  ('Coti', 'Coti', 'IND-DIV-0016', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Golden', '38', '', 494),
  ('Coti', 'Coti', 'IND-DIV-0029', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Blue', '36', '', 495),
  ('Coti', 'Coti', 'IND-DIV-0014', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'Black', '38', '', 496),
  ('Coti', 'Coti', 'IND-DIV-0015', 'IND-DIV', 'Gents Coti', '', '900', '1850', '', '', 'Pics', 'White', '38', '', 497),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0037', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 498),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0059', 'EX', 'Gents Half Polo Tshirt', '', '290', '430', '801', '', 'Pics', 'Mixed', 'F', '', 499),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0068', 'EX', 'Gents Half Polo Tshirt', '', '290', '650', '801', '', 'Pics', 'Mixed', 'F', '', 500),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0036', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Black', 'F', '', 501),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0027', 'EX', 'Gents Half Polo Tshirt', '', '290', '550', '801', '', 'Pics', 'Green', 'F', '', 502),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0067', 'EX', 'Gents Half Polo Tshirt', '', '290', '650', '801', '', 'Pics', 'Mixed', 'F', '', 503),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0028', 'EX', 'Gents Half Polo Tshirt', '', '250', '550', '801', '', 'Pics', 'Mixed', 'F', '', 504),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0060', 'EX', 'Gents Half Polo Tshirt', '', '290', '430', '801', '', 'Pics', 'Green', 'F', '', 505),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0065', 'EX', 'Gents Half Polo Tshirt', '', '290', '650', '801', '', 'Pics', 'Red', 'F', '', 506),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0020', 'EX', 'Gents Half Polo Tshirt', '', '240', '480', '801', '', 'Pics', 'Mixed', 'F', '', 507),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0004', 'EX', 'Gents Half Polo Tshirt', '', '260', '520', '801', '', 'Pics', 'Merun', 'F', '', 508),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0002', 'EX', 'Gents Half Polo Tshirt', '', '260', '520', '801', '', 'Pics', 'White', 'F', '', 509),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0070', 'EX', 'Gents Half Polo Tshirt', '', '290', '650', '801', '', 'Pics', 'Mixed', 'F', '', 510),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0053', 'EX', 'Gents Half Polo Tshirt', '', '290', '420', '801', '', 'Pics', 'Mixed', 'F', '', 511),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0056', 'EX', 'Gents Half Polo Tshirt', '', '290', '430', '801', '', 'Pics', 'Mixed', 'F', '', 512),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0066', 'EX', 'Gents Half Polo Tshirt', '', '290', '650', '801', '', 'Pics', 'Mixed', 'F', '', 513),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0017', 'EX', 'Gents Half Polo Tshirt', '', '240', '480', '801', '', 'Pics', 'Mixed', 'F', '', 514),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0057', 'EX', 'Gents Half Polo Tshirt', '', '290', '430', '801', '', 'Pics', 'Red', 'F', '', 515),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0006', 'EX', 'Gents Half Polo Tshirt', '', '260', '520', '801', '', 'Pics', 'Mixed', 'F', '', 516),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0032', 'EX', 'Gents Half Polo Tshirt', '', '290', '580', '801', '', 'Pics', 'Orange', 'F', '', 517),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0055', 'EX', 'Gents Half Polo Tshirt', '', '240', '430', '801', '', 'Pics', 'Purple', 'F', '', 518),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0034', 'EX', 'Gents Half Polo Tshirt', '', '290', '580', '801', '', 'Pics', 'Blue', 'F', '', 519),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0031', 'EX', 'Gents Half Polo Tshirt', '', '290', '580', '801', '', 'Pics', 'Mixed', 'F', '', 520),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0043', 'EX', 'Gents Half Polo Tshirt', '', '240', '430', '801', '', 'Pics', 'Mixed', 'F', '', 521),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0016', 'EX', 'Gents Half Polo Tshirt', '', '240', '480', '801', '', 'Pics', 'Mixed', 'F', '', 522),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0010', 'EX', 'Gents Half Polo Tshirt', '', '260', '520', '801', '', 'Pics', 'Mixed', 'F', '', 523),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0040', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 524),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0018', 'EX', 'Gents Half Polo Tshirt', '', '240', '480', '801', '', 'Pics', 'Mixed', 'F', '', 525),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0048', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 526),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0047', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 527),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0035', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 528),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0038', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 529),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0024', 'EX', 'Gents Half Polo Tshirt', '', '250', '550', '801', '', 'Pics', 'Mixed', 'F', '', 530),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0051', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 531),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0052', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 532),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0045', 'EX', 'Gents Half Polo Tshirt', '', '290', '450', '801', '', 'Pics', 'Mixed', 'F', '', 533),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0001', 'EX', 'Gents Half Polo Tshirt', '', '260', '520', '801', '', 'Pics', 'Mixed', 'F', '', 534),
  ('Half Polo Tshirt', 'Half Polo Tshirt', 'EX-0064', 'EX', 'Gents Half Polo Tshirt', '', '290', '430', '801', '', 'Pics', 'Mixed', 'F', '', 535),
  ('Tshirt', 'Tshirt', 'FC-0075', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Orange', 'M', '', 536),
  ('Tshirt', 'Tshirt', 'FC-0076', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Pink', 'M', '', 537),
  ('Tshirt', 'Tshirt', 'FC-0077', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Sky Blue', 'M', '', 538),
  ('Tshirt', 'Tshirt', 'FC-0078', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Pink', 'L', '', 539),
  ('Tshirt', 'Tshirt', 'FC-0079', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Orange', 'L', '', 540),
  ('Tshirt', 'Tshirt', 'FC-0080', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Orange', 'XL', '', 541),
  ('Tshirt', 'Tshirt', 'FC-0081', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Sky Blue', 'L', '', 542),
  ('Tshirt', 'Tshirt', 'FC-0082', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Pink', 'XL', '', 543),
  ('Tshirt', 'Tshirt', 'FC-0083', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Orange', 'L', '', 544),
  ('Tshirt', 'Tshirt', 'FC-0084', 'FC', 'Gents Tshirt', '', '195', '350', '70', '', 'Pics', 'Pink', 'XL', '', 545),
  ('Jeans Pant', 'Jeans Pant', 'VF-0043', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '30', '', 546),
  ('Jeans Pant', 'Jeans Pant', 'VF-0053', 'VF', 'Gents Jeans Pant', '', '460', '1070', '124', '', 'Pics', 'Blue', '34', '', 547),
  ('Jeans Pant', 'Jeans Pant', 'VF-0091', 'VF', 'Gents Jeans Pant', '', '450', '1050', '124', '', 'Pics', 'Blue', '36', '', 548),
  ('Jeans Pant', 'Jeans Pant', 'VF-0068', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Blue', '36', '', 549),
  ('Jeans Pant', 'Jeans Pant', 'VF-0074', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '36', '', 550),
  ('Jeans Pant', 'Jeans Pant', 'VF-0060', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '32', '', 551),
  ('Jeans Pant', 'Jeans Pant', 'VF-0087', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '32', '', 552),
  ('Jeans Pant', 'Jeans Pant', 'VF-0090', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '36', '', 553),
  ('Jeans Pant', 'Jeans Pant', 'VF-0059', 'VF', 'Gents Jeans Pant', '', '560', '1550', '124', '', 'Pics', 'Black', '30', '', 554),
  ('Jeans Pant', 'Jeans Pant', 'VF-0086', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '32', '', 555),
  ('Jeans Pant', 'Jeans Pant', 'VF-0089', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '30', '', 556),
  ('Jeans Pant', 'Jeans Pant', 'VF-0085', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '34', '', 557),
  ('Jeans Pant', 'Jeans Pant', 'VF-0088', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '28', '', 558),
  ('Jeans Pant', 'Jeans Pant', 'VF-0061', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '30', '', 559),
  ('Jeans Pant', 'Jeans Pant', 'VF-0066', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '34', '', 560),
  ('Jeans Pant', 'Jeans Pant', 'VF-0064', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '28', '', 561),
  ('Jeans Pant', 'Jeans Pant', 'VF-0063', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '30', '', 562),
  ('Jeans Pant', 'Jeans Pant', 'VF-0067', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '36', '', 563),
  ('Jeans Pant', 'Jeans Pant', 'VF-0065', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '32', '', 564),
  ('Jeans Pant', 'Jeans Pant', 'VF-0062', 'VF', 'Gents Jeans Pant', '', '560', '1570', '124', '', 'Pics', 'Blue', '34', '', 565),
  ('Jeans Pant', 'Jeans Pant', 'VF-0052', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '34', '', 566),
  ('Jeans Pant', 'Jeans Pant', 'VF-0046', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '34', '', 567),
  ('Jeans Pant', 'Jeans Pant', 'VF-0044', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '32', '', 568),
  ('Jeans Pant', 'Jeans Pant', 'VF-0051', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '34', '', 569),
  ('Jeans Pant', 'Jeans Pant', 'VF-0048', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '32', '', 570),
  ('Jeans Pant', 'Jeans Pant', 'VF-0045', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Black', '28', '', 571),
  ('Jeans Pant', 'Jeans Pant', 'VF-0083', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Blue', '32', '', 572),
  ('Jeans Pant', 'Jeans Pant', 'VF-0081', 'VF', 'Gents Jeans Pant', '', '550', '1070', '124', '', 'Pics', 'Blue', '30', '', 573),
  ('Jeans Pant', 'Jeans Pant', 'VF-0079', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Blue', '30', '', 574),
  ('Jeans Pant', 'Jeans Pant', 'VF-0080', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Blue', '34', '', 575),
  ('Jeans Pant', 'Jeans Pant', 'VF-0070', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Blue', '36', '', 576),
  ('Jeans Pant', 'Jeans Pant', 'VF-0078', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Blue', '32', '', 577),
  ('Jeans Pant', 'Jeans Pant', 'VF-0073', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '30', '', 578),
  ('Jeans Pant', 'Jeans Pant', 'VF-0077', 'VF', 'Gents Jeans Pant', '', '550', '1550', '124', '', 'Pics', 'Black', '30', '', 579),
  ('Jeans Pant', 'Jeans Pant', 'VF-0072', 'VF', 'Gents Jeans Pant', '', '550', '1550', '413', '', 'Pics', 'Black', '36', '', 580),
  ('Jeans Pant', 'Jeans Pant', 'VF-0098', 'VF', 'Gents Jeans Pant', '', '430', '1050', '413', '', 'Pics', 'Blue', '36', '', 581),
  ('Jeans Pant', 'Jeans Pant', 'VF-0099', 'VF', 'Gents Jeans Pant', '', '430', '1050', '413', '', 'Pics', 'Blue', '36', '', 582),
  ('Jeans Pant', 'Jeans Pant', 'VF-0018', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Blue', '34', '', 583),
  ('Jeans Pant', 'Jeans Pant', 'VF-0009', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Black', '30', '', 584),
  ('Jeans Pant', 'Jeans Pant', 'VF-0017', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Blue', '34', '', 585),
  ('Jeans Pant', 'Jeans Pant', 'VF-0011', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Blue', '30', '', 586),
  ('Jeans Pant', 'Jeans Pant', 'VF-0013', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Blue', '34', '', 587),
  ('Jeans Pant', 'Jeans Pant', 'VF-0012', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Blue', '36', '', 588),
  ('Jeans Pant', 'Jeans Pant', 'VF-0010', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Blue', '30', '', 589),
  ('Jeans Pant', 'Jeans Pant', 'VF-0019', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Silver', '34', '', 590),
  ('Jeans Pant', 'Jeans Pant', 'VF-0014', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Silver', '34', '', 591),
  ('Jeans Pant', 'Jeans Pant', 'VF-0007', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '30', '', 592),
  ('Jeans Pant', 'Jeans Pant', 'VF-0002', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '34', '', 593),
  ('Jeans Pant', 'Jeans Pant', 'VF-0005', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '32', '', 594),
  ('Jeans Pant', 'Jeans Pant', 'VF-0001', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '32', '', 595),
  ('Jeans Pant', 'Jeans Pant', 'VF-0003', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '34', '', 596),
  ('Jeans Pant', 'Jeans Pant', 'VF-0006', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '32', '', 597),
  ('Jeans Pant', 'Jeans Pant', 'VF-0008', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Golden', '36', '', 598),
  ('Jeans Pant', 'Jeans Pant', 'VF-0016', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Silver', '32', '', 599),
  ('Jeans Pant', 'Jeans Pant', 'VF-0015', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Silver', '32', '', 600),
  ('Jeans Pant', 'Jeans Pant', 'VF-0020', 'VF', 'Gents Jeans Pant', '', '350', '1070', '413', '', 'Pics', 'Silver', '30', '', 601),
  ('Jeans Pant', 'Jeans Pant', 'VF-0094', 'VF', 'Gents Jeans Pant', '', '450', '1050', '413', '', 'Pics', 'Blue', '32', '', 602),
  ('Jeans Pant', 'Jeans Pant', 'VF-0092', 'VF', 'Gents Jeans Pant', '', '450', '1150', '413', '', 'Pics', 'Blue', '34', '', 603),
  ('Jeans Pant', 'Jeans Pant', 'VF-0101', 'VF', 'Gents Jeans Pant', '', '450', '1150', '413', '', 'Pics', 'Blue', '36', '', 604),
  ('Jeans Pant', 'Jeans Pant', 'VF-0102', 'VF', 'Gents Jeans Pant', '', '450', '1150', '413', '', 'Pics', 'Blue', '30', '', 605),
  ('Jeans Pant', 'Jeans Pant', 'VF-0057', 'VF', 'Gents Jeans Pant', '', '460', '1070', '413', '', 'Pics', 'Blue', '42', '', 606),
  ('Jeans Pant', 'Jeans Pant', 'VF-0056', 'VF', 'Gents Jeans Pant', '', '460', '1070', '413', '', 'Pics', 'Blue', '30', '', 607),
  ('Jeans Pant', 'Jeans Pant', 'VF-0054', 'VF', 'Gents Jeans Pant', '', '460', '1070', '413', '', 'Pics', 'Blue', '30', '', 608),
  ('Jeans Pant', 'Jeans Pant', 'VF-0162', 'VF', 'Gents Jeans Pant', '', '450', '1050', '413', '', 'Pics', 'Blue', '34', '', 609),
  ('Jeans Pant', 'Jeans Pant', 'VF-0163', 'VF', 'Gents Jeans Pant', '', '450', '1050', '413', '', 'Pics', 'Blue', '32', '', 610),
  ('Jeans Pant', 'Jeans Pant', 'VF-0125', 'VF', 'Gents Jeans Pant', '', '550', '1550', '413', '', 'Pics', 'Black', '34', '', 611),
  ('Jeans Pant', 'Jeans Pant', 'CT-0050', 'CT', 'Gents Jeans Pant', '', '525', '1150', '413', '', 'Pics', 'Blue', '34', '', 612),
  ('Jeans Pant', 'Jeans Pant', 'CT-0051', 'CT', 'Gents Jeans Pant', '', '525', '1150', '413', '', 'Pics', 'Blue', '32', '', 613),
  ('Jeans Pant', 'Jeans Pant', 'LS-0012', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '31', '', 614),
  ('Jeans Pant', 'Jeans Pant', 'LS-0023', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '33', '', 615),
  ('Jeans Pant', 'Jeans Pant', 'LS-0035', 'LS', 'Gents Jeans Pant', '', '1350', '2050', '854', '', 'Pics', 'Blue', '31', '', 616),
  ('Jeans Pant', 'Jeans Pant', 'LS-0032', 'LS', 'Gents Jeans Pant', '', '1350', '2050', '854', '', 'Pics', 'Blue', '31', '', 617),
  ('Jeans Pant', 'Jeans Pant', 'LS-0021', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '33', '', 618),
  ('Jeans Pant', 'Jeans Pant', 'LS-0013', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '29', '', 619),
  ('Jeans Pant', 'Jeans Pant', 'LS-0028', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '28', '', 620),
  ('Jeans Pant', 'Jeans Pant', 'LS-0027', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '34', '', 621),
  ('Jeans Pant', 'Jeans Pant', 'LS-0033', 'LS', 'Gents Jeans Pant', '', '1350', '2050', '854', '', 'Pics', 'Blue', '34', '', 622),
  ('Jeans Pant', 'Jeans Pant', 'LS-0034', 'LS', 'Gents Jeans Pant', '', '1350', '2050', '854', '', 'Pics', 'Blue', '32', '', 623),
  ('Jeans Pant', 'Jeans Pant', 'LS-0014', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '31', '', 624),
  ('Jeans Pant', 'Jeans Pant', 'LS-0031', 'LS', 'Gents Jeans Pant', '', '1350', '2050', '854', '', 'Pics', 'Blue', '36', '', 625),
  ('Jeans Pant', 'Jeans Pant', 'LS-0029', 'LS', 'Gents Jeans Pant', '', '1350', '2050', '854', '', 'Pics', 'Blue', '33', '', 626),
  ('Jeans Pant', 'Jeans Pant', 'LS-0015', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Blue', '30', '', 627),
  ('Jeans Pant', 'Jeans Pant', 'LS-0025', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Black', '33', '', 628),
  ('Jeans Pant', 'Jeans Pant', 'LS-0004', 'LS', 'Gents Jeans Pant', '', '1800', '2650', '854', '', 'Pics', 'Black', '33', '', 629),
  ('Jeans Pant', 'Jeans Pant', 'LS-0016', 'LS', 'Gents Jeans Pant', '', '1600', '2350', '854', '', 'Pics', 'Black', '29', '', 630),
  ('Jeans Pant', 'Jeans Pant', 'LS-0005', 'LS', 'Gents Jeans Pant', '', '1800', '2650', '854', '', 'Pics', 'Black', '43', '', 631),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0016', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', 'NAI', '', 'Pics', 'Black', '33', '', 632),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0026', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Blue', '36', '', 633),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0020', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Golden', '34', '', 634),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0001', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Brown', '33', '', 635),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0002', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Brown', '32', '', 636),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0024', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Brown', '34', '', 637),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0023', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Brown', '34', '', 638),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0012', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Golden', '31', '', 639),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0021', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Golden', '33', '', 640),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0014', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Black', '36', '', 641),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0017', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Black', '32', '', 642),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0019', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Black', '30', '', 643),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0018', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Black', '32', '', 644),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0009', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Blue', '29', '', 645),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0007', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Blue', '31', '', 646),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0028', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Blue', '32', '', 647),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0008', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Blue', '33', '', 648),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0027', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '', '', 'Pics', 'Blue', '30', '', 649),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0114', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Sky Blue', '34', '', 650),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0117', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Silver', '33', '', 651),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0021', 'VF', 'Gents Gavadin Pant', '', '350', '1050', '413', '', 'Pics', 'Silver', '30', '', 652),
  ('Gavadin Pant', 'Gavadin Pant', 'LS-0007', 'LS', 'Gents Gavadin Pant', '', '1800', '2650', '854', '', 'Pics', 'Brown', '34', '', 653),
  ('Gavadin Pant', 'Gavadin Pant', 'LS-0009', 'LS', 'Gents Gavadin Pant', '', '1800', '2650', '854', '', 'Pics', 'Blue', '32', '', 654),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0103', 'VF', 'Gents Gavadin Pant', '', '470', '1050', '413', '', 'Pics', 'Silver', '34', '', 655),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0107', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Blue', '30', '', 656),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0112', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Silver', '28', '', 657),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0113', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Silver', '34', '', 658),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0109', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Blue', '34', '', 659),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0105', 'VF', 'Gents Gavadin Pant', '', '470', '1050', '413', '', 'Pics', 'Silver', '32', '', 660),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0118', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Blue', '32', '', 661),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0116', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Blue', '28', '', 662),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0110', 'VF', 'Gents Gavadin Pant', '', '400', '1150', '413', '', 'Pics', 'Silver', '32', '', 663),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0022', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '413', '', 'Pics', 'Golden', '32', '', 664),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0010', 'VF', 'Gents Gavadin Pant', '', '1200', '1850', '413', '', 'Pics', 'Golden', '32', '', 665),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0164', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Golden', '34', '', 666),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0165', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Coffy', '32', '', 667),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0166', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Golden', '28', '', 668),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0167', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Coffy', '28', '', 669),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0168', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Golden', '32', '', 670),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0069', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Coffy', '30', '', 671),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0070', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Golden', '30', '', 672),
  ('Gavadin Pant', 'Gavadin Pant', 'VF-0071', 'VF', 'Gents Gavadin Pant', '', '400', '1050', '413', '', 'Pics', 'Coffy', '34', '', 673),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0135', 'JK', 'Gents Gavadin Pant', '', '1550', '2050', '3275', '', 'Pics', 'Black', '32', '', 674),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0132', 'JK', 'Gents Gavadin Pant', '', '1550', '2050', '3275', '', 'Pics', 'Brown', '32', '', 675),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0131', 'JK', 'Gents Gavadin Pant', '', '1250', '2050', '3275', '', 'Pics', 'Black', '34', '', 676),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0133', 'JK', 'Gents Gavadin Pant', '', '1550', '2050', '3275', '', 'Pics', 'Black', '38', '', 677),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0130', 'JK', 'Gents Gavadin Pant', '', '1250', '2050', '3275', '', 'Pics', 'Black', '34', '', 678),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0129', 'JK', 'Gents Gavadin Pant', '', '1250', '2050', '3275', '', 'Pics', 'Brown', '34', '', 679),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0126', 'JK', 'Gents Gavadin Pant', '', '1250', '2050', '3275', '', 'Pics', 'Brown', '34', '', 680),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0127', 'JK', 'Gents Gavadin Pant', '', '1250', '2050', '3275', '', 'Pics', 'Brown', '31', '', 681),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0128', 'JK', 'Gents Gavadin Pant', '', '1250', '2050', '3275', '', 'Pics', 'Brown', '38', '', 682),
  ('Gavadin Pant', 'Gavadin Pant', 'RB-0028', 'RB', 'Gents Gavadin Pant', '', '1800', '2750', '265', '', 'Pics', 'Olive', '33', '', 683),
  ('Gavadin Pant', 'Gavadin Pant', 'RB-0029', 'RB', 'Gents Gavadin Pant', '', '1800', '2750', '265', '', 'Pics', 'Silver', '30', '', 684),
  ('Gavadin Pant', 'Gavadin Pant', 'RB-0026', 'RB', 'Gents Gavadin Pant', '', '1800', '2750', '265', '', 'Pics', 'Olive', '36', '', 685),
  ('Gavadin Pant', 'Gavadin Pant', 'RB-0025', 'RB', 'Gents Gavadin Pant', '', '1800', '2750', '265', '', 'Pics', 'Olive', '32', '', 686),
  ('Gavadin Pant', 'Gavadin Pant', 'BB-0025', 'BB', 'Gents Gavadin Pant', '', '1200', '1850', '265', '', 'Pics', 'Golden', '38', '', 687),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0037', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '35', '', 688),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0041', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '35', '', 689),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0043', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '34', '', 690),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0044', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '36', '', 691),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0036', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '36', '', 692),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0039', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '31', '', 693),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0045', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '34', '', 694),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0042', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '34', '', 695),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0038', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '32', '', 696),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0047', 'JK', 'Gents Gavadin Pant', '', '1300', '1950', '3240', '', 'Pics', 'Silver', '35', '', 697),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0132', 'JK', 'Gents Gavadin Pant', '', '1550', '2550', '3240', '', 'Pics', 'Black', '38', '', 698),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0133', 'JK', 'Gents Gavadin Pant', '', '1550', '2550', '3240', '', 'Pics', 'Black', '31', '', 699),
  ('Gavadin Pant', 'Gavadin Pant', 'JK-0136', 'JK', 'Gents Gavadin Pant', '', '1550', '2550', '3240', '', 'Pics', 'Black', '33', '', 700),
  ('Formal Pant', 'Formal Pant', 'RB-0017', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '34', '', 701),
  ('Formal Pant', 'Formal Pant', 'RB-0020', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Coffy', '34', '', 702),
  ('Formal Pant', 'Formal Pant', 'RB-0015', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '33', '', 703),
  ('Formal Pant', 'Formal Pant', 'RB-0019', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Coffy', '32', '', 704),
  ('Formal Pant', 'Formal Pant', 'RB-0022', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Coffy', '36', '', 705),
  ('Formal Pant', 'Formal Pant', 'RB-0006', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Coffy', '38', '', 706),
  ('Formal Pant', 'Formal Pant', 'RB-0014', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '36', '', 707),
  ('Formal Pant', 'Formal Pant', 'RB-0023', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '37', '', 708),
  ('Formal Pant', 'Formal Pant', 'RB-0021', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '33', '', 709),
  ('Formal Pant', 'Formal Pant', 'RB-0007', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '36', '', 710),
  ('Formal Pant', 'Formal Pant', 'RB-0024', 'RB', 'Gents Formal Pant', '', '800', '1180', '262', '', 'Pics', 'Silver', '38', '', 711),
  ('Under Wear', 'Under Wear', 'BT-0110', 'BT', 'Gents Under Wear', '', '250', '400', '1781', '', 'Pics', 'Black', 'L', '', 712),
  ('Under Wear', 'Under Wear', 'BT-0109', 'BT', 'Gents Under Wear', '', '190', '400', '1781', '', 'Pics', 'Silver', 'L', '', 713),
  ('Under Wear', 'Under Wear', 'BT-0102', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'L', '', 714),
  ('Under Wear', 'Under Wear', 'BT-0101', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Silver', 'L', '', 715),
  ('Under Wear', 'Under Wear', 'BT-0103', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'XL', '', 716),
  ('Under Wear', 'Under Wear', 'BT-0104', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Purple', 'L', '', 717),
  ('Under Wear', 'Under Wear', 'BT-0107', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Silver', 'M', '', 718),
  ('Under Wear', 'Under Wear', 'BT-0108', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Blue', 'M', '', 719),
  ('Under Wear', 'Under Wear', 'BT-0105', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'XXL', '', 720),
  ('Under Wear', 'Under Wear', 'BT-0106', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'XXL', '', 721),
  ('Under Wear', 'Under Wear', 'BT-0111', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'XL', '', 722),
  ('Under Wear', 'Under Wear', 'BT-0112', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Blue', 'XL', '', 723),
  ('Under Wear', 'Under Wear', 'BT-0113', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Purple', 'M', '', 724),
  ('Under Wear', 'Under Wear', 'BT-0114', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'M', '', 725),
  ('Under Wear', 'Under Wear', 'BT-0116', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Silver', 'XXL', '', 726),
  ('Under Wear', 'Under Wear', 'BT-0115', 'BT', 'Gents Under Wear', '', '200', '400', '1781', '', 'Pics', 'Black', 'XXL', '', 727),
  ('Under Wear', 'Under Wear', 'BT-0119', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'XXL', '', 728),
  ('Under Wear', 'Under Wear', 'BT-0118', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'XXL', '', 729),
  ('Under Wear', 'Under Wear', 'BT-0117', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Olive', 'XXL', '', 730),
  ('Under Wear', 'Under Wear', 'BT-0125', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'M', '', 731),
  ('Under Wear', 'Under Wear', 'BT-0124', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'M', '', 732),
  ('Under Wear', 'Under Wear', 'BT-0123', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'M', '', 733),
  ('Under Wear', 'Under Wear', 'BT-0121', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Olive', 'XL', '', 734),
  ('Under Wear', 'Under Wear', 'BT-0120', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'XL', '', 735),
  ('Under Wear', 'Under Wear', 'BT-0122', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'XL', '', 736),
  ('Under Wear', 'Under Wear', 'BT-0128', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'L', '', 737),
  ('Under Wear', 'Under Wear', 'BT-0127', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'L', '', 738),
  ('Under Wear', 'Under Wear', 'BT-0126', 'BT', 'Gents Under Wear', '', '133', '300', '1781', '', 'Pics', 'Silver', 'L', '', 739),
  ('Under Wear', 'Under Wear', 'BT-0066', 'BT', 'Gents Under Wear', '', '220', '480', '1781', '', 'Pics', 'Blue', 'XXXL', '', 740),
  ('Under Wear', 'Under Wear', 'BT-0065', 'BT', 'Gents Under Wear', '', '220', '480', '1781', '', 'Pics', 'Silver', 'XXXL', '', 741),
  ('Under Wear', 'Under Wear', 'BT-0068', 'BT', 'Gents Under Wear', '', '220', '480', '1781', '', 'Pics', 'Silver', 'XXXL', '', 742),
  ('Under Wear', 'Under Wear', 'BT-0069', 'BT', 'Gents Under Wear', '', '220', '480', '1781', '', 'Pics', 'Purple', 'XL', '', 743),
  ('Under Wear', 'Under Wear', 'BT-0070', 'BT', 'Gents Under Wear', '', '220', '480', '1781', '', 'Pics', 'Purple', 'XXL', '', 744),
  ('Under Wear', 'Under Wear', 'BT-0067', 'BT', 'Gents Under Wear', '', '220', '480', '1781', '', 'Pics', 'Black', 'XXL', '', 745),
  ('Under Wear', 'Under Wear', 'BT-0114', 'BT', 'Gents Under Wear', '', '190', '285', '1781', '', 'Pics', 'Black', 'XXXL', '', 746),
  ('Under Wear', 'Under Wear', 'BT-0115', 'BT', 'Gents Under Wear', '', '190', '285', '1781', '', 'Pics', 'Coffy', 'XXXL', '', 747),
  ('Under Wear', 'Under Wear', 'BT-0081', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Coffy', 'XL', '', 748),
  ('Under Wear', 'Under Wear', 'BT-0080', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Blue', 'XL', '', 749),
  ('Under Wear', 'Under Wear', 'BT-0077', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Coffy', 'XXXL', '', 750),
  ('Under Wear', 'Under Wear', 'BT-0078', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Blue', 'XXXL', '', 751),
  ('Under Wear', 'Under Wear', 'BT-0082', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Coffy', 'XL', '', 752),
  ('Under Wear', 'Under Wear', 'BT-0079', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Coffy', 'XL', '', 753),
  ('Under Wear', 'Under Wear', 'BT-0083', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 754),
  ('Under Wear', 'Under Wear', 'BT-0085', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 755),
  ('Under Wear', 'Under Wear', 'BT-0088', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXL', '', 756),
  ('Under Wear', 'Under Wear', 'BT-0084', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXL', '', 757),
  ('Under Wear', 'Under Wear', 'BT-0087', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXXL', '', 758),
  ('Under Wear', 'Under Wear', 'BT-0086', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXXL', '', 759),
  ('Under Wear', 'Under Wear', 'BT-0061', 'BT', 'Gents Under Wear', '', '260', '550', '1781', '', 'Pics', 'Mixed', 'XXL', '', 760),
  ('Under Wear', 'Under Wear', 'BT-0060', 'BT', 'Gents Under Wear', '', '260', '550', '1781', '', 'Pics', 'Mixed', 'XXL', '', 761),
  ('Under Wear', 'Under Wear', 'BT-0063', 'BT', 'Gents Under Wear', '', '260', '550', '1781', '', 'Pics', 'Mixed', 'XXXL', '', 762),
  ('Under Wear', 'Under Wear', 'BT-0064', 'BT', 'Gents Under Wear', '', '260', '550', '1781', '', 'Pics', 'Mixed', 'XXXL', '', 763),
  ('Under Wear', 'Under Wear', 'BT-0062', 'BT', 'Gents Under Wear', '', '260', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 764),
  ('Under Wear', 'Under Wear', 'BT-0059', 'BT', 'Gents Under Wear', '', '260', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 765),
  ('Under Wear', 'Under Wear', 'BT-0058', 'BT', 'Gents Under Wear', '', '235', '550', '1781', '', 'Pics', 'Silver', 'XXL', '', 766),
  ('Under Wear', 'Under Wear', 'BT-0053', 'BT', 'Gents Under Wear', '', '235', '550', '1781', '', 'Pics', 'Purple', 'XXL', '', 767),
  ('Under Wear', 'Under Wear', 'BT-0054', 'BT', 'Gents Under Wear', '', '235', '550', '1781', '', 'Pics', 'Silver', 'XL', '', 768),
  ('Under Wear', 'Under Wear', 'BT-0055', 'BT', 'Gents Under Wear', '', '235', '550', '1781', '', 'Pics', 'Black', 'XL', '', 769),
  ('Under Wear', 'Under Wear', 'BT-0056', 'BT', 'Gents Under Wear', '', '235', '550', '1781', '', 'Pics', 'Silver', 'XXXL', '', 770);
INSERT INTO `product_import` (`name`, `web`, `code`, `vendor_code`, `category`, `quantity`, `purchase_price`, `sales_price`, `memo_no`, `rack`, `unit`, `color`, `dimension`, `extra_1`, `id`) VALUES
  ('Under Wear', 'Under Wear', 'BT-0057', 'BT', 'Gents Under Wear', '', '235', '550', '1781', '', 'Pics', 'Blue', 'XXXL', '', 771),
  ('Under Wear', 'Under Wear', 'BT-0072', 'BT', 'Gents Under Wear', '', '230', '550', '1781', '', 'Pics', 'Purple', 'XXL', '', 772),
  ('Under Wear', 'Under Wear', 'BT-0071', 'BT', 'Gents Under Wear', '', '230', '550', '1781', '', 'Pics', 'Silver', 'XXL', '', 773),
  ('Under Wear', 'Under Wear', 'BT-0076', 'BT', 'Gents Under Wear', '', '230', '550', '1781', '', 'Pics', 'Black', 'XXXL', '', 774),
  ('Under Wear', 'Under Wear', 'BT-0075', 'BT', 'Gents Under Wear', '', '230', '550', '1781', '', 'Pics', 'Silver', 'XXXL', '', 775),
  ('Under Wear', 'Under Wear', 'BT-0090', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Green', 'L', '', 776),
  ('Under Wear', 'Under Wear', 'BT-0089', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'L', '', 777),
  ('Under Wear', 'Under Wear', 'BT-0094', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Merun', 'XXL', '', 778),
  ('Under Wear', 'Under Wear', 'BT-0096', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXL', '', 779),
  ('Under Wear', 'Under Wear', 'BT-0093', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXL', '', 780),
  ('Under Wear', 'Under Wear', 'BT-0099', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 781),
  ('Under Wear', 'Under Wear', 'BT-0097', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 782),
  ('Under Wear', 'Under Wear', 'BT-0091', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 783),
  ('Under Wear', 'Under Wear', 'BT-0092', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 784),
  ('Under Wear', 'Under Wear', 'BT-0073', 'BT', 'Gents Under Wear', '', '230', '550', '1781', '', 'Pics', 'Mixed', 'XL', '', 785),
  ('Under Wear', 'Under Wear', 'BT-0098', 'BT', 'Gents Under Wear', '', '250', '550', '1781', '', 'Pics', 'Mixed', 'XXXL', '', 786),
  ('Under Wear', 'Under Wear', 'BT-0044', 'BT', 'Gents Under Wear', '', '290', '400', '1781', '', 'Pics', 'Mixed', 'XXL', '', 787),
  ('Under Wear', 'Under Wear', 'BT-0045', 'BT', 'Gents Under Wear', '', '290', '400', '1781', '', 'Pics', 'Mixed', 'XXL', '', 788),
  ('Under Wear', 'Under Wear', 'BT-0047', 'BT', 'Gents Under Wear', '', '290', '400', '1781', '', 'Pics', 'Mixed', 'XL', '', 789),
  ('Under Wear', 'Under Wear', 'BT-0025', 'BT', 'Gents Under Wear', '', '340', '480', '1781', '', 'Pics', 'Mixed', 'L', '', 790),
  ('Sando Genji', 'Sando Genji', 'CT-0044', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'L', '', 791),
  ('Sando Genji', 'Sando Genji', 'CT-0046', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'L', '', 792),
  ('Sando Genji', 'Sando Genji', 'CT-0048', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'M', '', 793),
  ('Sando Genji', 'Sando Genji', 'CT-0049', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'S', '', 794),
  ('Sando Genji', 'Sando Genji', 'CT-0047', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'S', '', 795),
  ('Sando Genji', 'Sando Genji', 'CT-0052', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'XL', '', 796),
  ('Sando Genji', 'Sando Genji', 'CT-0053', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'XL', '', 797),
  ('Sando Genji', 'Sando Genji', 'CT-0054', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'XL', '', 798),
  ('Sando Genji', 'Sando Genji', 'CT-0055', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'XL', '', 799),
  ('Sando Genji', 'Sando Genji', 'CT-0056', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'XL', '', 800),
  ('Sando Genji', 'Sando Genji', 'CT-0057', 'CT', 'Gents Sando Genji', '', '85', '170', '4539', '', 'Pics', 'White', 'XL', '', 801),
  ('Under Wear', 'Under Wear', 'CT-0058', 'CT', 'Gents Under Wear', '', '85', '160', 'NAI', '', 'Pics', 'Merun', 'XL', '', 802),
  ('Under Wear', 'Under Wear', 'CT-0059', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Merun', 'XL', '', 803),
  ('Under Wear', 'Under Wear', 'CT-0060', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Blue', 'L', '', 804),
  ('Under Wear', 'Under Wear', 'CT-0061', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Black', 'M', '', 805),
  ('Under Wear', 'Under Wear', 'CT-0062', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Silver', 'M', '', 806),
  ('Under Wear', 'Under Wear', 'CT-0063', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Black', 'L', '', 807),
  ('Under Wear', 'Under Wear', 'CT-0064', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Merun', 'L', '', 808),
  ('Under Wear', 'Under Wear', 'CT-0066', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Merun', 'L', '', 809),
  ('Under Wear', 'Under Wear', 'CT-0067', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Merun', 'M', '', 810),
  ('Under Wear', 'Under Wear', 'CT-0068', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Blue', 'M', '', 811),
  ('Under Wear', 'Under Wear', 'CT-0069', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Black', 'M', '', 812),
  ('Under Wear', 'Under Wear', 'CT-0070', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Merun', 'XL', '', 813),
  ('Under Wear', 'Under Wear', 'CT-0071', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Blue', 'S', '', 814),
  ('Under Wear', 'Under Wear', 'CT-0072', 'CT', 'Gents Under Wear', '', '85', '160', '', '', 'Pics', 'Blue', 'S', '', 815),
  ('Three Pics', 'Three Pics', 'FO-0304', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1411', '', 'Three pics', 'Misti', '38', '', 816),
  ('Three Pics', 'Three Pics', 'FO-0318', 'FO', 'Ladies Three Pics', '', '1400', '1980', '1411', '', 'Three pics', 'Red', '40', '', 817),
  ('Three Pics', 'Three Pics', 'FO-0297', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Mixed', '40', '', 818),
  ('Three Pics', 'Three Pics', 'FO-0301', 'FO', 'Ladies Three Pics', '', '1200', '1680', '1411', '', 'Three pics', 'Blue', '40', '', 819),
  ('Three Pics', 'Three Pics', 'FO-0228', 'FO', 'Ladies Three Pics', '', '1900', '2660', '1226', '', 'Three pics', 'White', '38', '', 820),
  ('Three Pics', 'Three Pics', 'FO-0291', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Pest', '38', '', 821),
  ('Three Pics', 'Three Pics', 'FO-0266', 'FO', 'Ladies Three Pics', '', '1200', '1950', '1287', '', 'Three pics', 'Black', '40', '', 822),
  ('Three Pics', 'Three Pics', 'FO-0279', 'FO', 'Ladies Three Pics', '', '1150', '1610', '1411', '', 'Three pics', 'Mixed', '38', '', 823),
  ('Three Pics', 'Three Pics', 'FO-0093', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1411', '', 'Three pics', 'Pink', '38', '', 824),
  ('Three Pics', 'Three Pics', 'FO-0254', 'FO', 'Ladies Three Pics', '', '2400', '3250', '1226', '', 'Three pics', 'Mixed', '36', '', 825),
  ('Three Pics', 'Three Pics', 'FO-0233', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Blue', '44', '', 826),
  ('Three Pics', 'Three Pics', 'FO-0121', 'FO', 'Ladies Three Pics', '', '1100', '2450', '1226', '', 'Three pics', 'Mixed', '40', '', 827),
  ('Three Pics', 'Three Pics', 'FO-0310', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1411', '', 'Three pics', 'Pink', 'F', '', 828),
  ('Three Pics', 'Three Pics', 'FO-0313', 'FO', 'Ladies Three Pics', '', '650', '980', '1411', '', 'Three pics', 'Blue', 'F', '', 829),
  ('Three Pics', 'Three Pics', 'FO-0312', 'FO', 'Ladies Three Pics', '', '650', '980', '1411', '', 'Three pics', 'Mixed', 'F', '', 830),
  ('Three Pics', 'Three Pics', 'FO-0314', 'FO', 'Ladies Three Pics', '', '650', '980', '1411', '', 'Three pics', 'Purple', 'F', '', 831),
  ('Three Pics', 'Three Pics', 'FO-0248', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Mixed', '40', '', 832),
  ('Three Pics', 'Three Pics', 'FO-0149', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Mixed', '38', '', 833),
  ('Three Pics', 'Three Pics', 'FO-0174', 'FO', 'Ladies Three Pics', '', '1100', '1650', '1226', '', 'Three pics', 'Yellow', '40', '', 834),
  ('Three Pics', 'Three Pics', 'FO-0272', 'FO', 'Ladies Three Pics', '', '1100', '2100', '1226', '', 'Three pics', 'Pest', '40', '', 835),
  ('Three Pics', 'Three Pics', 'FO-0080', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Blue', '38', '', 836),
  ('Three Pics', 'Three Pics', 'FO-0044', 'FO', 'Ladies Three Pics', '', '1100', '1650', '1226', '', 'Three pics', 'Mixed', '38', '', 837),
  ('Three Pics', 'Three Pics', 'FO-0042', 'FO', 'Ladies Three Pics', '', '1100', '1650', '1226', '', 'Three pics', 'Mixed', '42', '', 838),
  ('Three Pics', 'Three Pics', 'FO-0346', 'FO', 'Ladies Three Pics', '', '1400', '2100', '1226', '', 'Three pics', 'Mixed', '38', '', 839),
  ('Three Pics', 'Three Pics', 'FO-0041', 'FO', 'Ladies Three Pics', '', '1100', '1650', '1226', '', 'Three pics', 'Mixed', '40', '', 840),
  ('Three Pics', 'Three Pics', 'FO-0212', 'FO', 'Ladies Three Pics', '', '1100', '2250', '1226', '', 'Three pics', 'Mixed', '40', '', 841),
  ('Three Pics', 'Three Pics', 'FO-0271', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Merun', '38', '', 842),
  ('Three Pics', 'Three Pics', 'FO-0274', 'FO', 'Ladies Three Pics', '', '1000', '2100', '1226', '', 'Three pics', 'Merun', '40', '', 843),
  ('Three Pics', 'Three Pics', 'FO-0232', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Blue', '40', '', 844),
  ('Three Pics', 'Three Pics', 'FO-0056', 'FO', 'Ladies Three Pics', '', '1000', '1450', '1226', '', 'Three pics', 'Red', '36', '', 845),
  ('Three Pics', 'Three Pics', 'FO-0277', 'FO', 'Ladies Three Pics', '', '1050', '1650', '1226', '', 'Three pics', 'Mixed', '35', '', 846),
  ('Three Pics', 'Three Pics', 'FO-0059', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Red', '44', '', 847),
  ('Three Pics', 'Three Pics', 'FO-0060', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Mixed', '42', '', 848),
  ('Three Pics', 'Three Pics', 'FO-0078', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Blue', '42', '', 849),
  ('Three Pics', 'Three Pics', 'FO-0132', 'FO', 'Ladies Three Pics', '', '1100', '2350', '1226', '', 'Three pics', 'Blue', '40', '', 850),
  ('Three Pics', 'Three Pics', 'FO-0252', 'FO', 'Ladies Three Pics', '', '2400', '3250', '1226', '', 'Three pics', 'Red', '36', '', 851),
  ('Three Pics', 'Three Pics', 'FO-0256', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'Mixed', '40', '', 852),
  ('Three Pics', 'Three Pics', 'FO-0147', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1262', '', 'Three pics', 'Mixed', '36', '', 853),
  ('Three Pics', 'Three Pics', 'FO-0243', 'FO', 'Ladies Three Pics', '', '1550', '2170', '1262', '', 'Three pics', 'Red', '36', '', 854),
  ('Three Pics', 'Three Pics', 'FO-0077', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1262', '', 'Three pics', 'Red', '38', '', 855),
  ('Three Pics', 'Three Pics', 'FO-0264', 'FO', 'Ladies Three Pics', '', '1400', '1950', '1287', '', 'Three pics', 'Black', '38', '', 856),
  ('Three Pics', 'Three Pics', 'FO-0051', 'FO', 'Ladies Three Pics', '', '1100', '1450', '1287', '', 'Three pics', 'Red', '38', '', 857),
  ('Three Pics', 'Three Pics', 'FO-0145', 'FO', 'Ladies Three Pics', '', '1500', '2450', '1287', '', 'Three pics', 'Red', '40', '', 858),
  ('Three Pics', 'Three Pics', 'FO-0265', 'FO', 'Ladies Three Pics', '', '1400', '1950', '1287', '', 'Three pics', 'Black', '36', '', 859),
  ('Three Pics', 'Three Pics', 'FO-0096', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1287', '', 'Three pics', 'Silver', '36', '', 860),
  ('Three Pics', 'Three Pics', 'FO-0095', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1226', '', 'Three pics', 'Silver', '38', '', 861),
  ('Three Pics', 'Three Pics', 'FO-0094', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1226', '', 'Three pics', 'Silver', '40', '', 862),
  ('Three Pics', 'Three Pics', 'FO-0090', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1226', '', 'Three pics', 'Misti', '36', '', 863),
  ('Three Pics', 'Three Pics', 'FO-0091', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1226', '', 'Three pics', 'Misti', '42', '', 864),
  ('Three Pics', 'Three Pics', 'FO-0089', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1226', '', 'Three pics', 'Silver', '42', '', 865),
  ('Three Pics', 'Three Pics', 'FO-0082', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Blue', '40', '', 866),
  ('Three Pics', 'Three Pics', 'FO-0115', 'FO', 'Ladies Three Pics', '', '1600', '2350', '1226', '', 'Three pics', 'Orange', '42', '', 867),
  ('Three Pics', 'Three Pics', 'FO-0092', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1226', '', 'Three pics', 'Pink', '40', '', 868),
  ('Three Pics', 'Three Pics', 'FO-0111', 'FO', 'Ladies Three Pics', '', '1600', '2350', '1226', '', 'Three pics', 'Orange', '40', '', 869),
  ('Three Pics', 'Three Pics', 'FO-0135', 'FO', 'Ladies Three Pics', '', '1100', '2350', '1226', '', 'Three pics', 'Mixed', '38', '', 870),
  ('Three Pics', 'Three Pics', 'FO-0129', 'FO', 'Ladies Three Pics', '', '1100', '2350', '1226', '', 'Three pics', 'Blue', '36', '', 871),
  ('Three Pics', 'Three Pics', 'FO-0273', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Sky Blue', '38', '', 872),
  ('Three Pics', 'Three Pics', 'FO-0250', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Mixed', '36', '', 873),
  ('Three Pics', 'Three Pics', 'FO-0249', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1226', '', 'Three pics', 'Blue', '42', '', 874),
  ('Three Pics', 'Three Pics', 'FO-0276', 'FO', 'Ladies Three Pics', '', '1050', '1550', '1226', '', 'Three pics', 'Biskit', '38', '', 875),
  ('Three Pics', 'Three Pics', 'FO-0263', 'FO', 'Ladies Three Pics', '', '1400', '1950', '1226', '', 'Three pics', 'Black', '34', '', 876),
  ('Three Pics', 'Three Pics', 'FO-0253', 'FO', 'Ladies Three Pics', '', '2400', '3250', '1226', '', 'Three pics', 'Silver', '38', '', 877),
  ('Three Pics', 'Three Pics', 'FO-0148', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1226', '', 'Three pics', 'Red', '36', '', 878),
  ('Three Pics', 'Three Pics', 'FO-0231', 'FO', 'Ladies Three Pics', '', '1900', '2650', '1226', '', 'Three pics', 'White', '34', '', 879),
  ('Three Pics', 'Three Pics', 'FO-0112', 'FO', 'Ladies Three Pics', '', '1600', '2350', '1226', '', 'Three pics', 'Orange', '36', '', 880),
  ('Three Pics', 'Three Pics', 'FO-0151', 'FO', 'Ladies Three Pics', '', '1100', '2250', '1226', '', 'Three pics', 'Red', '36', '', 881),
  ('Three Pics', 'Three Pics', 'FO-0242', 'FO', 'Ladies Three Pics', '', '1550', '2170', '1226', '', 'Three pics', 'Tiya', '36', '', 882),
  ('Three Pics', 'Three Pics', 'FO-0275', 'FO', 'Ladies Three Pics', '', '1050', '1550', '1226', '', 'Three pics', 'Biskit', '38', '', 883),
  ('Three Pics', 'Three Pics', 'FO-0257', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'Misti', '38', '', 884),
  ('Three Pics', 'Three Pics', 'FO-0260', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'Misti', '40', '', 885),
  ('Three Pics', 'Three Pics', 'FO-0259', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'Misti', '42', '', 886),
  ('Three Pics', 'Three Pics', 'FO-0261', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'Misti', '44', '', 887),
  ('Three Pics', 'Three Pics', 'FO-0239', 'FO', 'Ladies Three Pics', '', '1100', '1540', '1262', '', 'Three pics', 'Sky Blue', '42', '', 888),
  ('Three Pics', 'Three Pics', 'FO-0238', 'FO', 'Ladies Three Pics', '', '1100', '1540', '1262', '', 'Three pics', 'Sky Blue', '40', '', 889),
  ('Three Pics', 'Three Pics', 'FO-0240', 'FO', 'Ladies Three Pics', '', '1100', '1540', '1262', '', 'Three pics', 'Sky Blue', '38', '', 890),
  ('Three Pics', 'Three Pics', 'FO-0262', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'White', '42', '', 891),
  ('Three Pics', 'Three Pics', 'FO-0255', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'White', '44', '', 892),
  ('Three Pics', 'Three Pics', 'FO-0258', 'FO', 'Ladies Three Pics', '', '1100', '1550', '1262', '', 'Three pics', 'White', '38', '', 893),
  ('Three Pics', 'Three Pics', 'FO-0244', 'FO', 'Ladies Three Pics', '', '1550', '2170', '1226', '', 'Three pics', 'Red', '44', '', 894),
  ('Three Pics', 'Three Pics', 'FO-0307', 'FO', 'Ladies Three Pics', '', '1500', '2150', '1262', '', 'Three pics', 'Misti', '42', '', 895),
  ('Three Pics', 'Three Pics', 'FO-0300', 'FO', 'Ladies Three Pics', '', '1200', '1680', '1411', '', 'Three pics', 'Blue', '42', '', 896),
  ('Three Pics', 'Three Pics', 'FO-0299', 'FO', 'Ladies Three Pics', '', '1200', '1680', '1411', '', 'Three pics', 'Blue', '44', '', 897),
  ('Three Pics', 'Three Pics', 'FO-0302', 'FO', 'Ladies Three Pics', '', '1200', '1680', '1411', '', 'Three pics', 'Blue', '36', '', 898),
  ('Three Pics', 'Three Pics', 'FO-0319', 'FO', 'Ladies Three Pics', '', '1400', '1980', '1411', '', 'Three pics', 'Biskit', '36', '', 899),
  ('Three Pics', 'Three Pics', 'FO-0316', 'FO', 'Ladies Three Pics', '', '1400', '1980', '1411', '', 'Three pics', 'Biskit', '42', '', 900),
  ('Three Pics', 'Three Pics', 'FO-0315', 'FO', 'Ladies Three Pics', '', '1400', '1980', '1411', '', 'Three pics', 'Biskit', '44', '', 901),
  ('Three Pics', 'Three Pics', 'FO-0280', 'FO', 'Ladies Three Pics', '', '1150', '1610', '1411', '', 'Three pics', 'Mixed', '40', '', 902),
  ('Three Pics', 'Three Pics', 'FO-0306', 'FO', 'Ladies Three Pics', '', '1500', '2100', '1411', '', 'Three pics', 'Misti', '44', '', 903),
  ('Three Pics', 'Three Pics', 'FO-0308', 'FO', 'Ladies Three Pics', '', '1500', '2150', '1411', '', 'Three pics', 'Misti', '40', '', 904),
  ('Three Pics', 'Three Pics', 'FO-0281', 'FO', 'Ladies Three Pics', '', '1150', '1610', '1411', '', 'Three pics', 'Mixed', '36', '', 905),
  ('Three Pics', 'Three Pics', 'FO-0283', 'FO', 'Ladies Three Pics', '', '1150', '1610', '1411', '', 'Three pics', 'Mixed', '40', '', 906),
  ('Three Pics', 'Three Pics', 'FO-0290', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Blue', '36', '', 907),
  ('Three Pics', 'Three Pics', 'FO-0293', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Blue', '40', '', 908),
  ('Three Pics', 'Three Pics', 'FO-0289', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Blue', '40', '', 909),
  ('Three Pics', 'Three Pics', 'FO-0292', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Blue', '44', '', 910),
  ('Three Pics', 'Three Pics', 'FO-0285', 'FO', 'Ladies Three Pics', '', '1000', '1400', '1411', '', 'Three pics', 'Pink', '40', '', 911),
  ('Three Pics', 'Three Pics', 'FO-0286', 'FO', 'Ladies Three Pics', '', '1000', '1400', '1411', '', 'Three pics', 'Pink', '40', '', 912),
  ('Three Pics', 'Three Pics', 'FO-0284', 'FO', 'Ladies Three Pics', '', '1000', '1400', '1411', '', 'Three pics', 'Pink', '42', '', 913),
  ('Three Pics', 'Three Pics', 'FO-0298', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Mixed', '36', '', 914),
  ('Three Pics', 'Three Pics', 'FO-0295', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Mixed', '38', '', 915),
  ('Three Pics', 'Three Pics', 'FO-0294', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Mixed', '44', '', 916),
  ('Three Pics', 'Three Pics', 'FO-0296', 'FO', 'Ladies Three Pics', '', '1050', '1470', '1411', '', 'Three pics', 'Mixed', '42', '', 917),
  ('Three Pics', 'Three Pics', 'FO-0223', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1411', '', 'Three pics', 'Green', '38', '', 918),
  ('Three Pics', 'Three Pics', 'FO-0224', 'FO', 'Ladies Three Pics', '', '1500', '2250', '1411', '', 'Three pics', 'Green', '36', '', 919),
  ('Three Pics', 'Three Pics', 'FO-0317', 'FO', 'Ladies Three Pics', '', '1400', '1980', '1411', '', 'Three pics', 'Mixed', '36', '', 920),
  ('Three Pics', 'Three Pics', 'FO-0320', 'FO', 'Ladies Three Pics', '', '1550', '2600', '1411', '', 'Three pics', 'Green', '38', '', 921),
  ('Three Pics', 'Three Pics', 'JF-0042', 'JF', 'Ladies Three Pics', '', '1300', '1950', '460', '', 'Three pics', 'Black', '42', '', 922),
  ('Three Pics', 'Three Pics', 'JF-0043', 'JF', 'Ladies Three Pics', '', '1300', '1950', '460', '', 'Three pics', 'Black', '44', '', 923),
  ('Three Pics', 'Three Pics', 'JF-0058', 'JF', 'Ladies Three Pics', '', '1050', '1575', '460', '', 'Three pics', 'Red', '42', '', 924),
  ('Three Pics', 'Three Pics', 'JF-0056', 'JF', 'Ladies Three Pics', '', '1050', '1575', '460', '', 'Three pics', 'Red', '44', '', 925),
  ('Three Pics', 'Three Pics', 'JF-0039', 'JF', 'Ladies Three Pics', '', '1200', '1800', '460', '', 'Three pics', 'Blue', '44', '', 926),
  ('Three Pics', 'Three Pics', 'JF-0040', 'JF', 'Ladies Three Pics', '', '1200', '1800', '460', '', 'Three pics', 'Blue', '36', '', 927),
  ('Three Pics', 'Three Pics', 'JF-0038', 'JF', 'Ladies Three Pics', '', '1200', '1800', '460', '', 'Three pics', 'Blue', '40', '', 928),
  ('Three Pics', 'Three Pics', 'JF-0041', 'JF', 'Ladies Three Pics', '', '1200', '1800', '460', '', 'Three pics', 'Blue', '38', '', 929),
  ('Three Pics', 'Three Pics', 'JF-0059', 'JF', 'Ladies Three Pics', '', '1050', '1575', '460', '', 'Three pics', 'Red', '38', '', 930),
  ('Three Pics', 'Three Pics', 'JF-0003', 'JF', 'Ladies Three Pics', '', '950', '1450', '279', '', 'Three pics', 'Merun', '38', '', 931),
  ('Three Pics', 'Three Pics', 'JF-0034', 'JF', 'Ladies Three Pics', '', '1400', '1950', '279', '', 'Three pics', 'Blue', '42', '', 932),
  ('Three Pics', 'Three Pics', 'JF-0035', 'JF', 'Ladies Three Pics', '', '1400', '1950', '279', '', 'Three pics', 'Blue', '40', '', 933),
  ('Three Pics', 'Three Pics', 'JF-0032', 'JF', 'Ladies Three Pics', '', '1400', '1950', '279', '', 'Three pics', 'Blue', '36', '', 934),
  ('Three Pics', 'Three Pics', 'JF-0033', 'JF', 'Ladies Three Pics', '', '1400', '1950', '279', '', 'Three pics', 'Blue', '44', '', 935),
  ('Three Pics', 'Three Pics', 'JF-0054', 'JF', 'Ladies Three Pics', '', '1250', '1875', '279', '', 'Three pics', 'Misti', '44', '', 936),
  ('Three Pics', 'Three Pics', 'JF-0050', 'JF', 'Ladies Three Pics', '', '1250', '1875', '279', '', 'Three pics', 'Misti', '42', '', 937),
  ('Three Pics', 'Three Pics', 'JF-0051', 'JF', 'Ladies Three Pics', '', '1250', '1875', '279', '', 'Three pics', 'Merun', '38', '', 938),
  ('Three Pics', 'Three Pics', 'JF-0005', 'JF', 'Ladies Three Pics', '', '950', '1450', '279', '', 'Three pics', 'Merun', '34', '', 939),
  ('Three Pics', 'Three Pics', 'JF-0001', 'JF', 'Ladies Three Pics', '', '950', '1450', '279', '', 'Three pics', 'Black', '38', '', 940),
  ('Three Pics', 'Three Pics', 'JF-0046', 'JF', 'Ladies Three Pics', '', '1150', '1750', '460', '', 'Three pics', 'Blue', '38', '', 941),
  ('Three Pics', 'Three Pics', 'JF-0037', 'JF', 'Ladies Three Pics', '', '1200', '1800', '460', '', 'Three pics', 'Blue', '36', '', 942),
  ('Three Pics', 'Three Pics', 'JF-0047', 'JF', 'Ladies Three Pics', '', '1150', '1750', '460', '', 'Three pics', 'Black', '44', '', 943),
  ('Three Pics', 'Three Pics', 'JF-0024', 'JF', 'Ladies Three Pics', '', '950', '1650', '460', '', 'Three pics', 'Misti', '44', '', 944),
  ('Three Pics', 'Three Pics', 'JF-0025', 'JF', 'Ladies Three Pics', '', '950', '1650', '460', '', 'Three pics', 'Merun', '36', '', 945),
  ('Three Pics', 'Three Pics', 'JF-0022', 'JF', 'Ladies Three Pics', '', '950', '1650', '460', '', 'Three pics', 'Merun', '38', '', 946),
  ('Three Pics', 'Three Pics', 'JE-0036', 'JE', 'Ladies Three Pics', '', '1500', '2100', '97', '', 'Three pics', 'Mixed', '36', '', 947),
  ('Three Pics', 'Three Pics', 'JE-0037', 'JE', 'Ladies Three Pics', '', '1500', '2100', '97', '', 'Three pics', 'Mixed', '42', '', 948),
  ('Three Pics', 'Three Pics', 'JE-0038', 'JE', 'Ladies Three Pics', '', '1500', '2100', '97', '', 'Three pics', 'Blue', '38', '', 949),
  ('Three Pics', 'Three Pics', 'JE-0022', 'JE', 'Ladies Three Pics', '', '1500', '1980', '97', '', 'Three pics', 'Blue', '42', '', 950),
  ('Three Pics', 'Three Pics', 'JE-0023', 'JE', 'Ladies Three Pics', '', '1500', '1980', '97', '', 'Three pics', 'Blue', '42', '', 951),
  ('Three Pics', 'Three Pics', 'JE-0021', 'JE', 'Ladies Three Pics', '', '1500', '1980', '97', '', 'Three pics', 'Blue', '36', '', 952),
  ('Three Pics', 'Three Pics', 'JE-0014', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Yellow', '40', '', 953),
  ('Three Pics', 'Three Pics', 'JE-0011', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Yellow', '36', '', 954),
  ('Three Pics', 'Three Pics', 'JE-0012', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Yellow', '42', '', 955),
  ('Three Pics', 'Three Pics', 'JE-0032', 'JE', 'Ladies Three Pics', '', '1200', '1650', '97', '', 'Three pics', 'Biskit', '40', '', 956),
  ('Three Pics', 'Three Pics', 'JE-0031', 'JE', 'Ladies Three Pics', '', '1200', '1650', '97', '', 'Three pics', 'Biskit', '36', '', 957),
  ('Three Pics', 'Three Pics', 'JE-0034', 'JE', 'Ladies Three Pics', '', '1200', '1650', '97', '', 'Three pics', 'Biskit', '42', '', 958),
  ('Three Pics', 'Three Pics', 'JE-0033', 'JE', 'Ladies Three Pics', '', '1200', '1650', '97', '', 'Three pics', 'Biskit', '44', '', 959),
  ('Three Pics', 'Three Pics', 'JE-0027', 'JE', 'Ladies Three Pics', '', '1600', '2150', '97', '', 'Three pics', 'Malti', '42', '', 960),
  ('Three Pics', 'Three Pics', 'JE-0017', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Biskit', '42', '', 961),
  ('Three Pics', 'Three Pics', 'JE-0018', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Biskit', '36', '', 962),
  ('Three Pics', 'Three Pics', 'JE-0016', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Biskit', '38', '', 963),
  ('Three Pics', 'Three Pics', 'JE-0019', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Biskit', '34', '', 964),
  ('Three Pics', 'Three Pics', 'JE-0013', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Yellow', '44', '', 965),
  ('Three Pics', 'Three Pics', 'JE-0029', 'JE', 'Ladies Three Pics', '', '1600', '2150', '97', '', 'Three pics', 'Mixed', '38', '', 966),
  ('Three Pics', 'Three Pics', 'JE-0028', 'JE', 'Ladies Three Pics', '', '1600', '2150', '97', '', 'Three pics', 'Misti', '44', '', 967),
  ('Three Pics', 'Three Pics', 'JE-0010', 'JE', 'Ladies Three Pics', '', '1500', '2150', '97', '', 'Three pics', 'Misti', '42', '', 968),
  ('Three Pics', 'Three Pics', 'JE-0008', 'JE', 'Ladies Three Pics', '', '1500', '2150', '97', '', 'Three pics', 'Malti', '40', '', 969),
  ('Three Pics', 'Three Pics', 'JE-0007', 'JE', 'Ladies Three Pics', '', '1500', '2150', '97', '', 'Three pics', 'Malti', '44', '', 970),
  ('Three Pics', 'Three Pics', 'JE-0009', 'JE', 'Ladies Three Pics', '', '1500', '2150', '97', '', 'Three pics', 'Malti', '36', '', 971),
  ('Three Pics', 'Three Pics', 'JE-0003', 'JE', 'Ladies Three Pics', '', '1300', '1850', '97', '', 'Three pics', 'Mixed', '42', '', 972),
  ('Three Pics', 'Three Pics', 'JE-0002', 'JE', 'Ladies Three Pics', '', '1300', '1850', '97', '', 'Three pics', 'Mixed', '40', '', 973),
  ('Three Pics', 'Three Pics', 'JE-0005', 'JE', 'Ladies Three Pics', '', '1500', '1850', '97', '', 'Three pics', 'Mixed', '36', '', 974),
  ('Three Pics', 'Three Pics', 'JE-0004', 'JE', 'Ladies Three Pics', '', '1300', '1850', '97', '', 'Three pics', 'Mixed', '44', '', 975),
  ('Three Pics', 'Three Pics', 'JE-0006', 'JE', 'Ladies Three Pics', '', '1500', '2150', '97', '', 'Three pics', 'Misti', '38', '', 976),
  ('Three Pics', 'Three Pics', 'JE-0045', 'JE', 'Ladies Three Pics', '', '1400', '1850', '97', '', 'Three pics', 'Pink', '42', '', 977),
  ('Three Pics', 'Three Pics', 'JE-0042', 'JE', 'Ladies Three Pics', '', '1400', '1850', '97', '', 'Three pics', 'Pink', '40', '', 978),
  ('Three Pics', 'Three Pics', 'JE-0041', 'JE', 'Ladies Three Pics', '', '1400', '1850', '97', '', 'Three pics', 'Pink', '40', '', 979),
  ('Three Pics', 'Three Pics', 'JE-0043', 'JE', 'Ladies Three Pics', '', '1400', '1850', '97', '', 'Three pics', 'Pink', '44', '', 980),
  ('Three Pics', 'Three Pics', 'JE-0044', 'JE', 'Ladies Three Pics', '', '1400', '1850', '97', '', 'Three pics', 'Pink', '36', '', 981),
  ('Three Pics', 'Three Pics', 'JE-0026', 'JE', 'Ladies Three Pics', '', '1600', '2150', '97', '', 'Three pics', 'Mixed', '36', '', 982),
  ('Three Pics', 'Three Pics', 'JE-0049', 'JE', 'Ladies Three Pics', '', '1300', '1750', '97', '', 'Three pics', 'Black', '38', '', 983),
  ('Three Pics', 'Three Pics', 'JE-0048', 'JE', 'Ladies Three Pics', '', '1300', '1750', '97', '', 'Three pics', 'Black', '38', '', 984),
  ('Three Pics', 'Three Pics', 'JE-0047', 'JE', 'Ladies Three Pics', '', '1300', '1750', '97', '', 'Three pics', 'Black', '44', '', 985),
  ('Three Pics', 'Three Pics', 'JE-0046', 'JE', 'Ladies Three Pics', '', '1300', '1750', '97', '', 'Three pics', 'Black', '40', '', 986),
  ('Three Pics', 'Three Pics', 'JE-0040', 'JE', 'Ladies Three Pics', '', '1500', '2100', '97', '', 'Three pics', 'Black', '44', '', 987),
  ('Three Pics', 'Three Pics', 'JE-0020', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Biskit', '40', '', 988),
  ('Three Pics', 'Three Pics', 'JE-0030', 'JE', 'Ladies Three Pics', '', '1600', '2150', '97', '', 'Three pics', 'Malti', '40', '', 989),
  ('Three Pics', 'Three Pics', 'JE-0025', 'JE', 'Ladies Three Pics', '', '1500', '1980', '97', '', 'Three pics', 'Blue', '40', '', 990),
  ('Three Pics', 'Three Pics', 'JE-0035', 'JE', 'Ladies Three Pics', '', '1200', '1650', '97', '', 'Three pics', 'Biskit', '38', '', 991),
  ('Three Pics', 'Three Pics', 'JE-0015', 'JE', 'Ladies Three Pics', '', '1400', '1980', '97', '', 'Three pics', 'Yellow', '38', '', 992),
  ('Three Pics', 'Three Pics', 'JE-0039', 'JE', 'Ladies Three Pics', '', '1500', '2100', '97', '', 'Three pics', 'Mixed', '40', '', 993),
  ('Three Pics', 'Three Pics', 'AF-0074', 'AF', 'Ladies Three Pics', '', '1200', '1750', '569', '', 'Three pics', 'Blue', '38', '', 994),
  ('Three Pics', 'Three Pics', 'AF-0072', 'AF', 'Ladies Three Pics', '', '1200', '1750', '569', '', 'Three pics', 'Blue', '40', '', 995),
  ('Three Pics', 'Three Pics', 'AF-0073', 'AF', 'Ladies Three Pics', '', '1200', '1750', '569', '', 'Three pics', 'Blue', '40', '', 996),
  ('Three Pics', 'Three Pics', 'AF-0075', 'AF', 'Ladies Three Pics', '', '1200', '1750', '569', '', 'Three pics', 'Blue', '44', '', 997),
  ('Three Pics', 'Three Pics', 'AF-0037', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Pest', '38', '', 998),
  ('Three Pics', 'Three Pics', 'AF-0036', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Pest', '40', '', 999),
  ('Three Pics', 'Three Pics', 'AF-0039', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Pest', '44', '', 1000),
  ('Three Pics', 'Three Pics', 'AF-0038', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Pest', '36', '', 1001),
  ('Three Pics', 'Three Pics', 'AF-0040', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Pest', '42', '', 1002),
  ('Three Pics', 'Three Pics', 'AF-0014', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Pest', '38', '', 1003),
  ('Three Pics', 'Three Pics', 'AF-0013', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Pest', '40', '', 1004),
  ('Three Pics', 'Three Pics', 'AF-0012', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Pest', '42', '', 1005),
  ('Three Pics', 'Three Pics', 'AF-0015', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Pest', '44', '', 1006),
  ('Three Pics', 'Three Pics', 'AF-0011', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Pest', '36', '', 1007),
  ('Three Pics', 'Three Pics', 'AF-0026', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Black', '40', '', 1008),
  ('Three Pics', 'Three Pics', 'AF-0027', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Black', '44', '', 1009),
  ('Three Pics', 'Three Pics', 'AF-0028', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Black', '42', '', 1010),
  ('Three Pics', 'Three Pics', 'AF-0029', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Black', '32', '', 1011),
  ('Three Pics', 'Three Pics', 'AF-0030', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Black', '30', '', 1012),
  ('Three Pics', 'Three Pics', 'AF-0057', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '38', '', 1013),
  ('Three Pics', 'Three Pics', 'AF-0060', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '36', '', 1014),
  ('Three Pics', 'Three Pics', 'AF-0058', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '44', '', 1015),
  ('Three Pics', 'Three Pics', 'AF-0057', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '42', '', 1016),
  ('Three Pics', 'Three Pics', 'AF-0006', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Merun', '42', '', 1017),
  ('Three Pics', 'Three Pics', 'AF-0009', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Merun', '34', '', 1018),
  ('Three Pics', 'Three Pics', 'AF-0007', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Merun', '36', '', 1019),
  ('Three Pics', 'Three Pics', 'AF-0048', 'AF', 'Ladies Three Pics', '', '1200', '1750', '569', '', 'Three pics', 'Golden', '40', '', 1020),
  ('Three Pics', 'Three Pics', 'AF-0049', 'AF', 'Ladies Three Pics', '', '1200', '1750', '569', '', 'Three pics', 'Golden', '38', '', 1021),
  ('Three Pics', 'Three Pics', 'AF-0064', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '38', '', 1022),
  ('Three Pics', 'Three Pics', 'AF-0065', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '44', '', 1023),
  ('Three Pics', 'Three Pics', 'AF-0063', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '42', '', 1024),
  ('Three Pics', 'Three Pics', 'AF-0062', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '40', '', 1025),
  ('Three Pics', 'Three Pics', 'AF-0018', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Coffy', '42', '', 1026),
  ('Three Pics', 'Three Pics', 'AF-0017', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Coffy', '40', '', 1027),
  ('Three Pics', 'Three Pics', 'AF-0019', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Coffy', '38', '', 1028),
  ('Three Pics', 'Three Pics', 'AF-0016', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Coffy', '44', '', 1029),
  ('Three Pics', 'Three Pics', 'AF-0020', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Coffy', '36', '', 1030),
  ('Three Pics', 'Three Pics', 'AF-0035', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '38', '', 1031),
  ('Three Pics', 'Three Pics', 'AF-0031', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '44', '', 1032),
  ('Three Pics', 'Three Pics', 'AF-0032', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '32', '', 1033),
  ('Three Pics', 'Three Pics', 'AF-0034', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '36', '', 1034),
  ('Three Pics', 'Three Pics', 'AF-0052', 'AF', 'Ladies Three Pics', '', '1600', '2250', '569', '', 'Three pics', 'Yellow', '44', '', 1035),
  ('Three Pics', 'Three Pics', 'AF-0068', 'AF', 'Ladies Three Pics', '', '1100', '1650', '569', '', 'Three pics', 'Silver', '44', '', 1036),
  ('Three Pics', 'Three Pics', 'AF-0001', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Blue', '42', '', 1037),
  ('Three Pics', 'Three Pics', 'AF-0002', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Blue', '44', '', 1038),
  ('Three Pics', 'Three Pics', 'AF-0003', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Blue', '36', '', 1039),
  ('Three Pics', 'Three Pics', 'AF-0004', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Blue', '38', '', 1040),
  ('Three Pics', 'Three Pics', 'AF-0005', 'AF', 'Ladies Three Pics', '', '1400', '2100', '569', '', 'Three pics', 'Blue', '42', '', 1041),
  ('Three Pics', 'Three Pics', 'AF-0043', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Pest', '42', '', 1042),
  ('Three Pics', 'Three Pics', 'AF-0045', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Pest', '40', '', 1043),
  ('Three Pics', 'Three Pics', 'AF-0041', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '36', '', 1044),
  ('Three Pics', 'Three Pics', 'AF-0044', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '38', '', 1045),
  ('Three Pics', 'Three Pics', 'AF-0042', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Coffy', '44', '', 1046),
  ('Three Pics', 'Three Pics', 'AF-0010', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Red', '40', '', 1047),
  ('Three Pics', 'Three Pics', 'AF-0008', 'AF', 'Ladies Three Pics', '', '1300', '1950', '569', '', 'Three pics', 'Red', '38', '', 1048),
  ('Three Pics', 'Three Pics', 'AF-0024', 'AF', 'Ladies Three Pics', '', '900', '1450', '569', '', 'Three pics', 'Biskit', '38', '', 1049),
  ('Three Pics', 'Three Pics', 'AF-0021', 'AF', 'Ladies Three Pics', '', '900', '1450', '569', '', 'Three pics', 'Biskit', '40', '', 1050),
  ('Three Pics', 'Three Pics', 'AF-0066', 'AF', 'Ladies Three Pics', '', '1100', '1650', '569', '', 'Three pics', 'Silver', '38', '', 1051),
  ('Three Pics', 'Three Pics', 'AF-0070', 'AF', 'Ladies Three Pics', '', '1100', '1650', '569', '', 'Three pics', 'Silver', '42', '', 1052),
  ('Three Pics', 'Three Pics', 'AF-0071', 'AF', 'Ladies Three Pics', '', '1600', '2250', '569', '', 'Three pics', 'Yellow', '42', '', 1053),
  ('Three Pics', 'Three Pics', 'AF-0054', 'AF', 'Ladies Three Pics', '', '1600', '2250', '569', '', 'Three pics', 'Yellow', '36', '', 1054),
  ('Three Pics', 'Three Pics', 'AF-0059', 'AF', 'Ladies Three Pics', '', '1000', '1650', '569', '', 'Three pics', 'Biskit', '40', '', 1055),
  ('Three Pics', 'Three Pics', 'AF-0033', 'AF', 'Ladies Three Pics', '', '1000', '1550', '569', '', 'Three pics', 'Biskit', '34', '', 1056),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0129', 'SS', 'Ladies Shirt', '', '190', '550', 'NAI', '', 'Pics', 'Mixed', 'F', '', 1057),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0029', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Purple', 'F', '', 1058),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0023', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Sky Blue', 'F', '', 1059),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0077', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1060),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0096', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Mixed', 'F', '', 1061),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0095', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Mixed', 'F', '', 1062),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0087', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Mixed', 'F', '', 1063),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0128', 'SS', 'Ladies Shirt', '', '190', '550', '', '', 'Pics', 'Mixed', 'F', '', 1064),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0098', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Mixed', 'F', '', 1065),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0106', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Mixed', 'F', '', 1066),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0123', 'SS', 'Ladies Shirt', '', '190', '550', '', '', 'Pics', 'Mixed', 'F', '', 1067),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0047', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Mixed', 'F', '', 1068),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0100', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Mixed', 'F', '', 1069),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0018', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Mixed', 'F', '', 1070),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0040', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1071),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0045', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1072),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0012', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Red', 'F', '', 1073),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0013', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Red', 'F', '', 1074),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0015', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Black', 'F', '', 1075),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0025', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1076),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0034', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1077),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0035', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Black', 'F', '', 1078),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0069', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1079),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0060', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1080),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0094', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1081),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0105', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1082),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0111', 'SS', 'Ladies Shirt', '', '190', '550', '', '', 'Pics', 'Blue', 'F', '', 1083),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0073', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1084),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0031', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1085),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0075', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1086),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0071', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1087),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0057', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1088),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0058', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1089),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0089', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1090),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0043', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1091),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0055', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1092),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0019', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1093),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0090', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1094),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0044', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'White', 'F', '', 1095),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0042', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1096),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0063', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1097),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0076', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1098),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0086', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1099),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0078', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1100),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0122', 'SS', 'Ladies Shirt', '', '190', '550', '', '', 'Pics', 'Malti', 'F', '', 1101),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0054', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1102),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0041', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1103),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0059', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1104),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0011', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Malti', 'F', '', 1105),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0037', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Black', 'F', '', 1106),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0051', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1107),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0033', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1108),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0038', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1109),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0021', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1110),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0066', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Silver', 'F', '', 1111),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0022', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Orange', 'F', '', 1112),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0027', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Pink', 'F', '', 1113),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0083', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1114),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0039', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Malti', 'F', '', 1115),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0088', 'SS', 'Ladies Shirt', '', '190', '700', '', '', 'Pics', 'Malti', 'F', '', 1116),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0067', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1117),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0048', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1118),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0052', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1119),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0064', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1120),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0085', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1121),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0070', 'SS', 'Ladies Shirt', '', '190', '750', '', '', 'Pics', 'Malti', 'F', '', 1122),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0102', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1123),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0099', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1124),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0032', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1125),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0024', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1126),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0026', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1127),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0107', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Malti', 'F', '', 1128),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0030', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'White', 'F', '', 1129),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0095', 'SS', 'Ladies Shirt', '', '190', '680', '', '', 'Pics', 'Malti', 'F', '', 1130),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0036', 'SS', 'Ladies Shirt', '', '190', '850', '', '', 'Pics', 'Pink', 'F', '', 1131),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0125', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1132),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0120', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1133),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0116', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1134),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0112', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1135),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0114', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1136),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0130', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1137),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0126', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1138),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0118', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1139),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0119', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1140),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0117', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1141),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0113', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1142),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0115', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1143),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0124', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1144),
  ('Ladies Shirt', 'Ladies Shirt', 'SS-0127', 'SS', 'Ladies Shirt', '', '190', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1145),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0130', 'SS', 'Ladies Tshirt', '', '250', '750', 'NAI', '', 'Two Pics', 'Purple', '38', '', 1146),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0131', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Blue', '44', '', 1147),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0132', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1148),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0133', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1149);
INSERT INTO `product_import` (`name`, `web`, `code`, `vendor_code`, `category`, `quantity`, `purchase_price`, `sales_price`, `memo_no`, `rack`, `unit`, `color`, `dimension`, `extra_1`, `id`) VALUES
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0134', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1150),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0135', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1151),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0136', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '44', '', 1152),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0137', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1153),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0138', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1154),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0139', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1155),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0140', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1156),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0141', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '42', '', 1157),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0142', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1158),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0143', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '42', '', 1159),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0144', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '44', '', 1160),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0145', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '42', '', 1161),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0146', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '40', '', 1162),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0147', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '38', '', 1163),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0148', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '40', '', 1164),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0149', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '40', '', 1165),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0150', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '36', '', 1166),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0151', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '44', '', 1167),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0152', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '44', '', 1168),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0153', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '42', '', 1169),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0154', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1170),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0155', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1171),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0156', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1172),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0157', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1173),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0158', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '42', '', 1174),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0159', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '38', '', 1175),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0160', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1176),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0161', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '42', '', 1177),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0162', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '42', '', 1178),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0163', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'White', '42', '', 1179),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0164', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Blue', '40', '', 1180),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0165', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Blue', '40', '', 1181),
  ('Ladies Tshirt', 'Ladies Tshirt', 'SS-0166', 'SS', 'Ladies Tshirt', '', '250', '750', '', '', 'Two Pics', 'Purple', '40', '', 1182),
  ('Ladies Tops', 'Ladies Tops', 'MK-0031', 'MK', 'Ladies Tops', '', '1050', '1750', '504', '', 'Pics', 'Black', 'L', '', 1183),
  ('Ladies Tops', 'Ladies Tops', 'MK-0061', 'MK', 'Ladies Tops', '', '1150', '1650', '504', '', 'Pics', 'Ofwhite', 'M', '', 1184),
  ('Ladies Tops', 'Ladies Tops', 'MK-0204', 'MK', 'Ladies Tops', '', '850', '1450', '504', '', 'Pics', 'Golden', 'M', '', 1185),
  ('Ladies Tops', 'Ladies Tops', 'MK-0137', 'MK', 'Ladies Tops', '', '750', '1150', '504', '', 'Pics', 'Black', 'M', '', 1186),
  ('Ladies Tops', 'Ladies Tops', 'MK-0104', 'MK', 'Ladies Tops', '', '1150', '1650', '504', '', 'Pics', 'Black', 'S', '', 1187),
  ('Ladies Tops', 'Ladies Tops', 'MK-0295', 'MK', 'Ladies Tops', '', '1000', '1650', '770', '', 'Pics', 'Blue', 'S', '', 1188),
  ('Ladies Tops', 'Ladies Tops', 'MK-0343', 'MK', 'Ladies Tops', '', '850', '1650', '832', '', 'Pics', 'Black', 'S', '', 1189),
  ('Ladies Tops', 'Ladies Tops', 'MK-0319', 'MK', 'Ladies Tops', '', '1100', '1850', '785', '', 'Pics', 'Mixed', 'S', '', 1190),
  ('Ladies Tops', 'Ladies Tops', 'MK-0247', 'MK', 'Ladies Tops', '', '1150', '2550', '785', '', 'Pics', 'Pink', 'L', '', 1191),
  ('Ladies Tops', 'Ladies Tops', 'MK-0153', 'MK', 'Ladies Tops', '', '1000', '1550', '785', '', 'Pics', 'White', 'M', '', 1192),
  ('Ladies Tops', 'Ladies Tops', 'MK-0152', 'MK', 'Ladies Tops', '', '1000', '1550', '785', '', 'Pics', 'White', 'S', '', 1193),
  ('Ladies Tops', 'Ladies Tops', 'MK-0314', 'MK', 'Ladies Tops', '', '1050', '1850', '785', '', 'Pics', 'Black', 'M', '', 1194),
  ('Ladies Tops', 'Ladies Tops', 'MK-0105', 'MK', 'Ladies Tops', '', '1150', '1650', '785', '', 'Pics', 'Blue', 'S', '', 1195),
  ('Ladies Tops', 'Ladies Tops', 'MK-0296', 'MK', 'Ladies Tops', '', '1000', '1650', '785', '', 'Pics', 'Black', 'S', '', 1196),
  ('Ladies Tops', 'Ladies Tops', 'MK-0317', 'MK', 'Ladies Tops', '', '1050', '1850', '785', '', 'Pics', 'Black', 'S', '', 1197),
  ('Ladies Tops', 'Ladies Tops', 'MK-0315', 'MK', 'Ladies Tops', '', '1050', '1850', '785', '', 'Pics', 'Black', 'M', '', 1198),
  ('Ladies Tops', 'Ladies Tops', 'MK-0102', 'MK', 'Ladies Tops', '', '1150', '1650', '785', '', 'Pics', 'Black', 'M', '', 1199),
  ('Ladies Tops', 'Ladies Tops', 'MK-0064', 'MK', 'Ladies Tops', '', '1150', '1650', '785', '', 'Pics', 'Red', 'M', '', 1200),
  ('Ladies Tops', 'Ladies Tops', 'MK-0145', 'MK', 'Ladies Tops', '', '1050', '1550', '785', '', 'Pics', 'Blue', 'M', '', 1201),
  ('Ladies Tops', 'Ladies Tops', 'MK-0146', 'MK', 'Ladies Tops', '', '1050', '1550', '785', '', 'Pics', 'Blue', 'S', '', 1202),
  ('Ladies Tops', 'Ladies Tops', 'MK-0144', 'MK', 'Ladies Tops', '', '1050', '1550', '785', '', 'Pics', 'Pink', 'S', '', 1203),
  ('Ladies Tops', 'Ladies Tops', 'MK-0149', 'MK', 'Ladies Tops', '', '1050', '1550', '785', '', 'Pics', 'Pink', 'M', '', 1204),
  ('Ladies Tops', 'Ladies Tops', 'MK-0134', 'MK', 'Ladies Tops', '', '750', '1150', '785', '', 'Pics', 'Red', 'L', '', 1205),
  ('Ladies Tops', 'Ladies Tops', 'MK-0147', 'MK', 'Ladies Tops', '', '1050', '1550', '785', '', 'Pics', 'Blue', 'L', '', 1206),
  ('Ladies Tops', 'Ladies Tops', 'MK-0136', 'MK', 'Ladies Tops', '', '750', '1150', '785', '', 'Pics', 'Black', 'L', '', 1207),
  ('Ladies Tops', 'Ladies Tops', 'MK-0312', 'MK', 'Ladies Tops', '', '1050', '1850', '785', '', 'Pics', 'Black', 'L', '', 1208),
  ('Ladies Tops', 'Ladies Tops', 'MK-0119', 'MK', 'Ladies Tops', '', '1150', '2050', '785', '', 'Pics', 'Biskit', 'L', '', 1209),
  ('Ladies Tops', 'Ladies Tops', 'MK-0366', 'MK', 'Ladies Tops', '', '1200', '1950', '881', '', 'Pics', 'White', 'S', '', 1210),
  ('Ladies Tops', 'Ladies Tops', 'MK-0368', 'MK', 'Ladies Tops', '', '1100', '1800', '881', '', 'Pics', 'Malti', 'M', '', 1211),
  ('Ladies Tops', 'Ladies Tops', 'MK-0369', 'MK', 'Ladies Tops', '', '1100', '1800', '881', '', 'Pics', 'Malti', 'L', '', 1212),
  ('Ladies Tops', 'Ladies Tops', 'MK-0370', 'MK', 'Ladies Tops', '', '1100', '1800', '881', '', 'Pics', 'Malti', 'S', '', 1213),
  ('Ladies Tops', 'Ladies Tops', 'MK-0371', 'MK', 'Ladies Tops', '', '1100', '1800', '881', '', 'Pics', 'Malti', 'L', '', 1214),
  ('Ladies Tops', 'Ladies Tops', 'MK-0372', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Biskit', 'M', '', 1215),
  ('Ladies Tops', 'Ladies Tops', 'MK-0373', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Malti', 'L', '', 1216),
  ('Ladies Tops', 'Ladies Tops', 'MK-0374', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Misti', 'M', '', 1217),
  ('Ladies Tops', 'Ladies Tops', 'MK-0375', 'MK', 'Ladies Tops', '', '600', '1050', '881', '', 'Pics', 'Malti', 'M', '', 1218),
  ('Ladies Tops', 'Ladies Tops', 'MK-0376', 'MK', 'Ladies Tops', '', '600', '1050', '881', '', 'Pics', 'Orange', 'M', '', 1219),
  ('Ladies Tops', 'Ladies Tops', 'MK-0377', 'MK', 'Ladies Tops', '', '600', '1050', '881', '', 'Pics', 'Orange', 'S', '', 1220),
  ('Ladies Tops', 'Ladies Tops', 'MK-0378', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Malti', 'S', '', 1221),
  ('Ladies Tops', 'Ladies Tops', 'MK-0379', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Malti', 'M', '', 1222),
  ('Ladies Tops', 'Ladies Tops', 'MK-0380', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Malti', 'L', '', 1223),
  ('Ladies Tops', 'Ladies Tops', 'MK-0381', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Malti', 'S', '', 1224),
  ('Ladies Tops', 'Ladies Tops', 'MK-0382', 'MK', 'Ladies Tops', '', '550', '1050', '881', '', 'Pics', 'Malti', 'L', '', 1225),
  ('Ladies Tops', 'Ladies Tops', 'MK-0383', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Blue', 'L', '', 1226),
  ('Ladies Tops', 'Ladies Tops', 'MK-0384', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Blue', 'M', '', 1227),
  ('Ladies Tops', 'Ladies Tops', 'MK-0385', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Green', 'S', '', 1228),
  ('Ladies Tops', 'Ladies Tops', 'MK-0386', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Malti', 'S', '', 1229),
  ('Ladies Tops', 'Ladies Tops', 'MK-0387', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Malti', 'L', '', 1230),
  ('Ladies Tops', 'Ladies Tops', 'MK-0388', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Malti', 'M', '', 1231),
  ('Ladies Tops', 'Ladies Tops', 'MK-0389', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Silver', 'M', '', 1232),
  ('Ladies Tops', 'Ladies Tops', 'MK-0390', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Silver', 'S', '', 1233),
  ('Ladies Tops', 'Ladies Tops', 'MK-0391', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Merun', 'M', '', 1234),
  ('Ladies Tops', 'Ladies Tops', 'MK-0392', 'MK', 'Ladies Tops', '', '1300', '1950', '881', '', 'Pics', 'Merun', 'L', '', 1235),
  ('Ladies Tops', 'Ladies Tops', 'MK-0393', 'MK', 'Ladies Tops', '', '1250', '1850', '881', '', 'Pics', 'Mixed', 'M', '', 1236),
  ('Ladies Tops', 'Ladies Tops', 'MK-0394', 'MK', 'Ladies Tops', '', '1250', '1850', '881', '', 'Pics', 'Biskit', 'L', '', 1237),
  ('Ladies Tops', 'Ladies Tops', 'MK-0395', 'MK', 'Ladies Tops', '', '1250', '1850', '881', '', 'Pics', 'Biskit', 'M', '', 1238),
  ('Ladies Tops', 'Ladies Tops', 'MK-0396', 'MK', 'Ladies Tops', '', '1100', '1850', '881', '', 'Pics', 'Golden', 'M', '', 1239),
  ('Ladies Tops', 'Ladies Tops', 'MK-0397', 'MK', 'Ladies Tops', '', '800', '1650', '881', '', 'Pics', 'Merun', 'M', '', 1240),
  ('Ladies Tops', 'Ladies Tops', 'MK-0344', 'MK', 'Ladies Tops', '', '1000', '1650', '881', '', 'Pics', 'Black', 'M', '', 1241),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0040', 'IND-IBY', 'Ladies Tops', '', '512', '1150', 'NAI', '', 'Pics', 'Orange', 'F', '', 1242),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0042', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Pink', 'F', '', 1243),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0039', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Orange', 'F', '', 1244),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0037', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Blue', 'F', '', 1245),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0038', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Blue', 'F', '', 1246),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0060', 'IND-IBY', 'Ladies Tops', '', '793', '1550', '', '', 'Pics', 'Blue', 'F', '', 1247),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0061', 'IND-IBY', 'Ladies Tops', '', '793', '1550', '', '', 'Pics', 'Blue', 'F', '', 1248),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0059', 'IND-IBY', 'Ladies Tops', '', '793', '1550', '', '', 'Pics', 'Misti', 'F', '', 1249),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0062', 'IND-IBY', 'Ladies Tops', '', '793', '1550', '', '', 'Pics', 'Red', 'F', '', 1250),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0118', 'IND-IBY', 'Ladies Tops', '', '575', '1250', '', '', 'Pics', 'White', 'F', '', 1251),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0005', 'IND-IBY', 'Ladies Tops', '', '638', '1250', '', '', 'Pics', 'White', 'F', '', 1252),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0129', 'IND-IBY', 'Ladies Tops', '', '575', '1250', '', '', 'Pics', 'White', 'F', '', 1253),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0007', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'White', 'F', '', 1254),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0122', 'IND-IBY', 'Ladies Tops', '', '575', '1250', '', '', 'Pics', 'White', 'F', '', 1255),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0036', 'IND-IBY', 'Ladies Tops', '', '481', '1090', '', '', 'Pics', 'Black', 'F', '', 1256),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0031', 'IND-IBY', 'Ladies Tops', '', '481', '1090', '', '', 'Pics', 'Black', 'F', '', 1257),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0032', 'IND-IBY', 'Ladies Tops', '', '481', '1090', '', '', 'Pics', 'Black', 'F', '', 1258),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0033', 'IND-IBY', 'Ladies Tops', '', '481', '1090', '', '', 'Pics', 'Black', 'F', '', 1259),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0035', 'IND-IBY', 'Ladies Tops', '', '481', '1090', '', '', 'Pics', 'Black', 'F', '', 1260),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0006', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'White', 'F', '', 1261),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0010', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Purple', 'F', '', 1262),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0019', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Blue', 'F', '', 1263),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0012', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Blue', 'F', '', 1264),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0017', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Black', 'F', '', 1265),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0029', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Black', 'F', '', 1266),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0024', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Black', 'F', '', 1267),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0030', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1268),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0022', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1269),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0023', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1270),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0027', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Black', 'F', '', 1271),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0026', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1272),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0157', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Mixed', 'F', '', 1273),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0153', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Red', 'F', '', 1274),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0156', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Blue', 'F', '', 1275),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0151', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Red', 'F', '', 1276),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0160', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Red', 'F', '', 1277),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0165', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Black', 'F', '', 1278),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0162', 'IND-IBY', 'Ladies Tops', '', '606', '1250', '', '', 'Pics', 'Black', 'F', '', 1279),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0211', 'IND-IBY', 'Ladies Tops', '', '562', '1250', '', '', 'Pics', 'White', 'F', '', 1280),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0204', 'IND-IBY', 'Ladies Tops', '', '562', '1250', '', '', 'Pics', 'White', 'F', '', 1281),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0021', 'IND-IBY', 'Ladies Tops', '', '837', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1282),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0008', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Purple', 'F', '', 1283),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0034', 'IND-IBY', 'Ladies Tops', '', '481', '1090', '', '', 'Pics', 'Black', 'F', '', 1284),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0009', 'IND-IBY', 'Ladies Tops', '', '512', '1150', '', '', 'Pics', 'Golden', 'F', '', 1285),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0213', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Blue', 'F', '', 1286),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0214', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Pink', 'F', '', 1287),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0215', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Black', 'F', '', 1288),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0211', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Green', 'F', '', 1289),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0216', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Green', 'F', '', 1290),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0217', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Pink', 'F', '', 1291),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0127', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'White', 'F', '', 1292),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0210', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'White', 'F', '', 1293),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0214', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'White', 'F', '', 1294),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0041', 'IND-IBY', 'Ladies Tops', '', '600', '1150', '', '', 'Pics', 'Orange', 'F', '', 1295),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0158', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'Black', 'F', '', 1296),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0015', 'IND-IBY', 'Ladies Tops', '', '600', '1250', '', '', 'Pics', 'White', 'F', '', 1297),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0094', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'Blue', 'F', '', 1298),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0092', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'White', 'F', '', 1299),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0087', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'Blue', 'F', '', 1300),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0084', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'Pest', 'F', '', 1301),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0088', 'IND-IBY', 'Ladies Tops', '', '880', '2000', '', '', 'Pics', 'Blue', 'F', '', 1302),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0092', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'White', 'F', '', 1303),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0091', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'White', 'F', '', 1304),
  ('Ladies Tops', 'Ladies Tops', 'IND-BY-0090', 'IND-IBY', 'Ladies Tops', '', '890', '2000', '', '', 'Pics', 'White', 'F', '', 1305),
  ('Ladies Tops', 'Ladies Tops', 'MK-0342', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'XXL', '', 1306),
  ('Ladies Tops', 'Ladies Tops', 'MK-0346', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Black', 'S', '', 1307),
  ('Ladies Tops', 'Ladies Tops', 'MK-0347', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'XXL', '', 1308),
  ('Ladies Tops', 'Ladies Tops', 'MK-0345', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'S', '', 1309),
  ('Ladies Tops', 'Ladies Tops', 'MK-0353', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'XXL', '', 1310),
  ('Ladies Tops', 'Ladies Tops', 'MK-0350', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'XXL', '', 1311),
  ('Ladies Tops', 'Ladies Tops', 'MK-0344', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'L', '', 1312),
  ('Ladies Tops', 'Ladies Tops', 'MK-0345', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'M', '', 1313),
  ('Ladies Tops', 'Ladies Tops', 'MK-0346', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Black', 'L', '', 1314),
  ('Ladies Tops', 'Ladies Tops', 'MK-0347', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Black', 'M', '', 1315),
  ('Ladies Tops', 'Ladies Tops', 'MK-0348', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'M', '', 1316),
  ('Ladies Tops', 'Ladies Tops', 'MK-0349', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'L', '', 1317),
  ('Ladies Tops', 'Ladies Tops', 'MK-0350', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'S', '', 1318),
  ('Ladies Tops', 'Ladies Tops', 'MK-0351', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'S', '', 1319),
  ('Ladies Tops', 'Ladies Tops', 'MK-0352', 'MK', 'Ladies Tops', '', '800', '1650', '', '', 'Pics', 'Silver', 'L', '', 1320),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0137', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Blue', 'F', '', 1321),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0136', 'IND-IBY', 'Ladies Palazzo', '', '570', '57', '', '', 'Pics', 'Green', 'F', '', 1322),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0200', 'IND-IBY', 'Ladies Palazzo', '', '570', '1150', '', '', 'Pics', 'Green', 'F', '', 1323),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0140', 'IND-IBY', 'Ladies Palazzo', '', '570', '1150', '', '', 'Pics', 'Merun', 'F', '', 1324),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0190', 'IND-IBY', 'Ladies Palazzo', '', '570', '1150', '', '', 'Pics', 'Blue', 'F', '', 1325),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0174', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1326),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0171', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1327),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0131', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Mixed', 'F', '', 1328),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0139', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Mixed', 'F', '', 1329),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0133', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Mixed', 'F', '', 1330),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0147', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Mixed', 'F', '', 1331),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0138', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Blue', 'F', '', 1332),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0188', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Red', 'F', '', 1333),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0176', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Mixed', 'F', '', 1334),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0175', 'IND-IBY', 'Ladies Palazzo', '', '570', '1100', '', '', 'Pics', 'Mixed', 'F', '', 1335),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0167', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1336),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0168', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1337),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0166', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1338),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0178', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1339),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0169', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1340),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0180', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1341),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0172', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1342),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0217', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Black', 'F', '', 1343),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0218', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Black', 'F', '', 1344),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0219', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Black', 'F', '', 1345),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0220', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1346),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0221', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1347),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0222', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1348),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0223', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Black', 'F', '', 1349),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0224', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1350),
  ('Ladies Palazzo', 'Ladies Palazzo', 'IND-BY-0225', 'IND-IBY', 'Ladies Palazzo', '', '570', '1050', '', '', 'Pics', 'Mixed', 'F', '', 1351),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0008', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Biskit', '36', '', 1352),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0009', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Silver', '30', '', 1353),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0010', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Biskit', '34', '', 1354),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0011', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Biskit', '30', '', 1355),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0012', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Biskit', '32', '', 1356),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0013', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Silver', '36', '', 1357),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0014', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Silver', '34', '', 1358),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0015', 'IND-FU', 'Gents Gavadin Pants', '', '1815', '3050', '', '', 'Pics', 'Silver', '32', '', 1359),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0016', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Silver', '34', '', 1360),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0017', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '34', '', 1361),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0018', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Biskit', '38', '', 1362),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0019', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Biskit', '32', '', 1363),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0020', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Biskit', '34', '', 1364),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0021', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '36', '', 1365),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0022', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '38', '', 1366),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0023', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '32', '', 1367),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0024', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Biskit', '36', '', 1368),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0025', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '36', '', 1369),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0026', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '38', '', 1370),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0027', 'IND-FU', 'Gents Gavadin Pants', '', '1560', '2850', '', '', 'Pics', 'Coffy', '32', '', 1371),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0028', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Silver', '34', '', 1372),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0029', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Silver', '30', '', 1373),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0030', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '30', '', 1374),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0031', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '34', '', 1375),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0032', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '32', '', 1376),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0033', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '42', '', 1377),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0034', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '32', '', 1378),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0035', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '36', '', 1379),
  ('Gents Gavadin Pants', 'Gents Gavadin Pants', 'IND-FU-0036', 'IND-FU', 'Gents Gavadin Pants', '', '1585', '2880', '', '', 'Pics', 'Biskit', '36', '', 1380),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0001', 'IND-FU', 'Gents Jeans Pants', '', '1815', '3150', '', '', 'Pics', 'Blue', '30', '', 1381),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0002', 'IND-FU', 'Gents Jeans Pants', '', '1815', '3150', '', '', 'Pics', 'Blue', '32', '', 1382),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0003', 'IND-FU', 'Gents Jeans Pants', '', '1815', '3150', '', '', 'Pics', 'Blue', '34', '', 1383),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0004', 'IND-FU', 'Gents Jeans Pants', '', '1815', '3150', '', '', 'Pics', 'Black', '38', '', 1384),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0005', 'IND-FU', 'Gents Jeans Pants', '', '1815', '3150', '', '', 'Pics', 'Black', '35', '', 1385),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0006', 'IND-FU', 'Gents Jeans Pants', '', '1815', '3150', '', '', 'Pics', 'Black', '34', '', 1386),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0037', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '36', '', 1387),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0038', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '36', '', 1388),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0039', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '30', '', 1389),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0040', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '36', '', 1390),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0041', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '34', '', 1391),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0042', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '32', '', 1392),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0043', 'IND-FU', 'Gents Jeans Pants', '', '1890', '3250', '', '', 'Pics', 'Blue', '32', '', 1393),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0044', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '32', '', 1394),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0045', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '34', '', 1395),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0046', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '28', '', 1396),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0047', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '30', '', 1397),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0048', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '32', '', 1398),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0049', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Black', '28', '', 1399),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0050', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '28', '', 1400),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0051', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Black', '34', '', 1401),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0052', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '32', '', 1402),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0053', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '28', '', 1403),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0054', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '34', '', 1404),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0055', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Black', '32', '', 1405),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0056', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Black', '36', '', 1406),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0057', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '30', '', 1407),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0058', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '34', '', 1408),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0059', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Black', '30', '', 1409),
  ('Gents Jeans Pants', 'Gents Jeans Pants', 'IND-FU-0060', 'IND-FU', 'Gents Jeans Pants', '', '1850', '3150', '', '', 'Pics', 'Blue', '28', '', 1410),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0001', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Blue', 'F', '', 1411),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0091', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Olive', 'F', '', 1412),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0083', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Green', 'F', '', 1413),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0121', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Blue', 'F', '', 1414),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0119', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Yellow', 'F', '', 1415),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0065', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Green', 'F', '', 1416),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0209', 'FN', 'Ladies Palazzo', '', '140', '350', '', '', 'Pics', 'Pink', 'F', '', 1417),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0187', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Pest', 'F', '', 1418),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0191', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Blue', 'F', '', 1419),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0164', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Blue', 'F', '', 1420),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0163', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Malti', 'F', '', 1421),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0157', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Malti', 'F', '', 1422),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0169', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Malti', 'F', '', 1423),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0155', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Malti', 'F', '', 1424),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0074', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Green', 'F', '', 1425),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0072', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Pest', 'F', '', 1426),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0125', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Yellow', 'F', '', 1427),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0082', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Pink', 'F', '', 1428),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0103', 'FN', 'Ladies Palazzo', '', '140', '210', '', '', 'Pics', 'Pest', 'F', '', 1429),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0201', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Pink', 'F', '', 1430),
  ('Ladies Palazzo', 'Ladies Palazzo', 'FN-0214', 'FN', 'Ladies Palazzo', '', '190', '350', '', '', 'Pics', 'Blue', 'F', '', 1431),
  ('Ladies Tops', 'Ladies Tops', 'FG-0042', 'FG', 'Ladies Tops', '', '1450', '2050', '3218', '', 'Pics', 'Mixed', 'L', '', 1432),
  ('Ladies Tops', 'Ladies Tops', 'FG-0041', 'FG', 'Ladies Tops', '', '1450', '2050', '3218', '', 'Pics', 'Mixed', 'XXL', '', 1433),
  ('Ladies Tops', 'Ladies Tops', 'FG-0044', 'FG', 'Ladies Tops', '', '1450', '2050', '3218', '', 'Pics', 'Mixed', 'XXL', '', 1434),
  ('Ladies Tops', 'Ladies Tops', 'FG-0047', 'FG', 'Ladies Tops', '', '1520', '2200', '3218', '', 'Pics', 'Red', 'XXL', '', 1435),
  ('Ladies Tops', 'Ladies Tops', 'FG-0051', 'FG', 'Ladies Tops', '', '1550', '2250', '3218', '', 'Pics', 'Black', 'XXL', '', 1436),
  ('Ladies Tops', 'Ladies Tops', 'FG-0040', 'FG', 'Ladies Tops', '', '1650', '2050', '3218', '', 'Pics', 'Malti', 'XXL', '', 1437),
  ('Ladies Tops', 'Ladies Tops', 'FG-0055', 'FG', 'Ladies Tops', '', '1630', '2300', '3218', '', 'Pics', 'Malti', 'XXL', '', 1438),
  ('Ladies Tops', 'Ladies Tops', 'FG-0008', 'FG', 'Ladies Tops', '', '1000', '1450', '3218', '', 'Pics', 'Blue', 'F', '', 1439),
  ('Ladies Tops', 'Ladies Tops', 'FG-0027', 'FG', 'Ladies Tops', '', '1500', '2250', '3218', '', 'Pics', 'White', 'F', '', 1440),
  ('Ladies Tops', 'Ladies Tops', 'FG-0025', 'FG', 'Ladies Tops', '', '1500', '2250', '3218', '', 'Pics', 'White', 'F', '', 1441),
  ('Ladies Tops', 'Ladies Tops', 'FG-0001', 'FG', 'Ladies Tops', '', '1000', '1450', '3218', '', 'Pics', 'Yellow', 'F', '', 1442),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'FN-0219', 'FN', 'Ladies Jeans Pants', '', '250', '550', '3218', '', 'Pics', 'Black', '38', '', 1443),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'FN-0220', 'FN', 'Ladies Jeans Pants', '', '250', '550', '3218', '', 'Pics', 'Blue', '30', '', 1444),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'FN-0221', 'FN', 'Ladies Jeans Pants', '', '250', '550', '3218', '', 'Pics', 'Blue', '30', '', 1445),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'FN-0222', 'FN', 'Ladies Jeans Pants', '', '250', '550', '3218', '', 'Pics', 'Blue', '40', '', 1446),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'FN-0223', 'FN', 'Ladies Jeans Pants', '', '250', '550', '3218', '', 'Pics', 'Blue', '30', '', 1447),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'FN-0224', 'FN', 'Ladies Jeans Pants', '', '250', '550', '3218', '', 'Pics', 'Black', '38', '', 1448),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0167', 'SS', 'Ladies Jeans Pants', '', '160', '550', 'NAI', '', 'Pics', 'Black', '28', '', 1449),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0168', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '27', '', 1450),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0169', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '32', '', 1451),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0170', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '27', '', 1452),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0171', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '25', '', 1453),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0172', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '25', '', 1454),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0173', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '32', '', 1455),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0174', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '26', '', 1456),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0175', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '25', '', 1457),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0176', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '25', '', 1458),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0177', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '29', '', 1459),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0178', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '30', '', 1460),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0179', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '28', '', 1461),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0180', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Black', '28', '', 1462),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0182', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Golden', '44', '', 1463),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0181', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '36', '', 1464),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0183', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '36', '', 1465),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0184', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '44', '', 1466),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0185', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '36', '', 1467),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0186', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '44', '', 1468),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0187', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Golden', '44', '', 1469),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0188', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '44', '', 1470),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0189', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '42', '', 1471),
  ('Ladies Jeans Pants', 'Ladies Jeans Pants', 'SS-0190', 'SS', 'Ladies Jeans Pants', '', '160', '550', '', '', 'Pics', 'Silver', '46', '', 1472),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0062', 'FG', 'Ladies Jagins', '', '400', '900', 'NAI', '', 'Pics', 'Blue', 'F', '', 1473),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0059', 'FG', 'Ladies Jagins', '', '400', '900', '', '', 'Pics', 'Biskit', 'F', '', 1474),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0071', 'FG', 'Ladies Jagins', '', '400', '850', '', '', 'Pics', 'Pink', 'F', '', 1475),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0074', 'FG', 'Ladies Jagins', '', '400', '850', '', '', 'Pics', 'Purple', 'F', '', 1476),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0072', 'FG', 'Ladies Jagins', '', '400', '850', '', '', 'Pics', 'Orange', 'F', '', 1477),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0066', 'FG', 'Ladies Jagins', '', '400', '900', '', '', 'Pics', 'Silver', 'F', '', 1478),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0058', 'FG', 'Ladies Jagins', '', '400', '900', '', '', 'Pics', 'Brown', 'F', '', 1479),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0056', 'FG', 'Ladies Jagins', '', '400', '900', '', '', 'Pics', 'Biskit', 'F', '', 1480),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0060', 'FG', 'Ladies Jagins', '', '400', '900', '', '', 'Pics', 'Brown', 'F', '', 1481),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0073', 'FG', 'Ladies Jagins', '', '400', '850', '', '', 'Pics', 'Red', 'F', '', 1482),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0075', 'FG', 'Ladies Jagins', '', '400', '850', '', '', 'Pics', 'Pest', 'F', '', 1483),
  ('Ladies Jagins', 'Ladies Jagins', 'FG-0076', 'FG', 'Ladies Jagins', '', '400', '850', '', '', 'Pics', 'Red', 'F', '', 1484),
  ('Ladies Three Quater', 'Ladies Three Quater', 'SS-0191', 'SS', 'Ladies Three Quater', '', '160', '550', '', '', 'Pics', 'Silver', '36', '', 1485),
  ('Ladies Three Quater', 'Ladies Three Quater', 'SS-0192', 'SS', 'Ladies Three Quater', '', '160', '550', '', '', 'Pics', 'Black', '38', '', 1486),
  ('Ladies Three Quater', 'Ladies Three Quater', 'SS-0193', 'SS', 'Ladies Three Quater', '', '160', '550', '', '', 'Pics', 'Black', '36', '', 1487),
  ('Ladies Three Quater', 'Ladies Three Quater', 'SS-0194', 'SS', 'Ladies Three Quater', '', '160', '550', '', '', 'Pics', 'Black', '36', '', 1488),
  ('Ladies Ties', 'Ladies Ties', 'SS-0195', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Brown', 'F', '', 1489),
  ('Ladies Ties', 'Ladies Ties', 'SS-0196', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Blue', 'F', '', 1490),
  ('Ladies Ties', 'Ladies Ties', 'SS-0197', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1491),
  ('Ladies Ties', 'Ladies Ties', 'SS-0198', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1492),
  ('Ladies Ties', 'Ladies Ties', 'SS-0199', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Coffy', 'F', '', 1493),
  ('Ladies Ties', 'Ladies Ties', 'SS-0200', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1494),
  ('Ladies Ties', 'Ladies Ties', 'SS-0201', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Pest', 'F', '', 1495),
  ('Ladies Ties', 'Ladies Ties', 'SS-0202', 'SS', 'Ladies Ties', '', '130', '450', '', '', 'Pics', 'Silver', 'F', '', 1496),
  ('Ladies Ties', 'Ladies Ties', 'SS-0203', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Blue', 'F', '', 1497),
  ('Ladies Ties', 'Ladies Ties', 'SS-0204', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1498),
  ('Ladies Ties', 'Ladies Ties', 'SS-0205', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Silver', 'F', '', 1499),
  ('Ladies Ties', 'Ladies Ties', 'SS-0206', 'SS', 'Ladies Ties', '', '130', '450', '', '', 'Pics', 'Silver', 'F', '', 1500),
  ('Ladies Ties', 'Ladies Ties', 'SS-0207', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Silver', 'F', '', 1501),
  ('Ladies Ties', 'Ladies Ties', 'SS-0208', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Blue', 'F', '', 1502),
  ('Ladies Ties', 'Ladies Ties', 'SS-0209', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1503),
  ('Ladies Ties', 'Ladies Ties', 'SS-0210', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Pest', 'F', '', 1504),
  ('Ladies Ties', 'Ladies Ties', 'SS-0211', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Blue', 'F', '', 1505),
  ('Ladies Ties', 'Ladies Ties', 'SS-0212', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Pest', 'F', '', 1506),
  ('Ladies Ties', 'Ladies Ties', 'SS-0113', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1507),
  ('Ladies Ties', 'Ladies Ties', 'SS-0214', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1508),
  ('Ladies Ties', 'Ladies Ties', 'SS-0215', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Silver', 'F', '', 1509),
  ('Ladies Ties', 'Ladies Ties', 'SS-0216', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1510),
  ('Ladies Ties', 'Ladies Ties', 'SS-0217', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Silver', 'F', '', 1511),
  ('Ladies Ties', 'Ladies Ties', 'SS-0218', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'White', 'F', '', 1512),
  ('Ladies Ties', 'Ladies Ties', 'SS-0219', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1513),
  ('Ladies Ties', 'Ladies Ties', 'SS-0220', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Silver', 'F', '', 1514),
  ('Ladies Ties', 'Ladies Ties', 'SS-0221', 'SS', 'Ladies Ties', '', '130', '450', '', '', 'Pics', 'Silver', 'F', '', 1515),
  ('Ladies Ties', 'Ladies Ties', 'SS-0222', 'SS', 'Ladies Ties', '', '130', '450', '', '', 'Pics', 'Silver', 'F', '', 1516),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0063', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Biskit', 'F', '', 1517),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0064', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Black', 'F', '', 1518),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0065', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'White', 'F', '', 1519),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0066', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'White', 'F', '', 1520),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0067', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Red', 'F', '', 1521),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0068', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Pink', 'F', '', 1522),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0069', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'White', 'F', '', 1523);
INSERT INTO `product_import` (`name`, `web`, `code`, `vendor_code`, `category`, `quantity`, `purchase_price`, `sales_price`, `memo_no`, `rack`, `unit`, `color`, `dimension`, `extra_1`, `id`) VALUES
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0070', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Red', 'F', '', 1524),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0071', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Purple', 'F', '', 1525),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0072', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Red', 'F', '', 1526),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0073', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Purple', 'F', '', 1527),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0074', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Blue', 'F', '', 1528),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0075', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Black', 'F', '', 1529),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0076', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'White', 'F', '', 1530),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0077', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Black', 'F', '', 1531),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0078', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Pest', 'F', '', 1532),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0079', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Blue', 'F', '', 1533),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0080', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Purple', 'F', '', 1534),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0081', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Silver', 'F', '', 1535),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0082', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Pest', 'F', '', 1536),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0083', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Black', 'F', '', 1537),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0084', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Pest', 'F', '', 1538),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0085', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Purple', 'F', '', 1539),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0086', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'White', 'F', '', 1540),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0087', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Red', 'F', '', 1541),
  ('Ladies Ties', 'Ladies Ties', 'EPZ-0088', 'EPZ', 'Ladies Ties', '', '120', '280', '', '', 'Pics', 'Pink', 'F', '', 1542),
  ('Ladies Ties', 'Ladies Ties', 'SS-0223', 'SS', 'Ladies Ties', '', '130', '350', '', '', 'Pics', 'Black', 'F', '', 1543),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0224', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Pics', 'Pink', 'F', '', 1544),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0225', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Pics', 'Purple', 'F', '', 1545),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0226', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Set', 'Silver', 'F', '', 1546),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0227', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Pics', 'Pink', 'F', '', 1547),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0228', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Pics', 'White', 'F', '', 1548),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0229', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Pics', 'White', 'F', '', 1549),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0230', 'SS', 'Babies Tshirt', '', '220', '450', '', '', 'Pics', 'Silver', 'F', '', 1550),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0231', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Set', 'White', 'F', '', 1551),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0232', 'SS', 'Babies Tshirt', '', '220', '450', '', '', 'Pics', 'Silver', 'F', '', 1552),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0233', 'SS', 'Babies Tshirt', '', '220', '450', '', '', 'Pis', 'Silver', 'F', '', 1553),
  ('Babies Tshirt', 'Babies Tshirt', 'SS-0234', 'SS', 'Babies Tshirt', '', '220', '550', '', '', 'Pics', 'Silver', 'XL', '', 1554),
  ('Ladies Ties', 'Ladies Ties', 'CT-0005', 'CT', 'Ladies Ties', '', '173', '375', '', '', 'Pics', 'Misti', 'F', '', 1555),
  ('Ladies Ties', 'Ladies Ties', 'CT-0001', 'CT', 'Ladies Ties', '', '173', '375', '', '', 'Pics', 'Blue', 'F', '', 1556),
  ('Ladies Ties', 'Ladies Ties', 'CT-0004', 'CT', 'Ladies Ties', '', '173', '375', '', '', 'Pics', 'Blue', 'F', '', 1557),
  ('Ladies Ties', 'Ladies Ties', 'CT-0006', 'CT', 'Ladies Ties', '', '173', '375', '', '', 'Pics', 'Silver', 'F', '', 1558),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0001', 'IND-IN', 'Gents Panjabi', '', '2043', '3250', '', '', 'Pics', 'Merun', 'M', '', 1559),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0002', 'IND-IN', 'Gents Panjabi', '', '2045', '3250', '', '', 'Pics', 'Merun', 'XL', '', 1560),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0003', 'IND-IN', 'Gents Panjabi', '', '2045', '3250', '', '', 'Pics', 'Merun', 'L', '', 1561),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0004', 'IND-IN', 'Gents Panjabi', '', '1950', '3850', '', '', 'Pics', 'Golden', 'XL', '', 1562),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0005', 'IND-IN', 'Gents Panjabi', '', '1950', '3850', '', '', 'Pics', 'Golden', 'L', '', 1563),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0006', 'IND-IN', 'Gents Panjabi', '', '1220', '2450', '', '', 'Pics', 'Golden', 'M', '', 1564),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0007', 'IND-IN', 'Gents Panjabi', '', '1050', '2050', '', '', 'Pics', 'Yellow', 'M', '', 1565),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0008', 'IND-IN', 'Gents Panjabi', '', '1050', '2050', '', '', 'Pics', 'Yellow', 'L', '', 1566),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0009', 'IND-IN', 'Gents Panjabi', '', '1050', '2050', '', '', 'Pics', 'Yellow', 'XL', '', 1567),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0010', 'IND-IN', 'Gents Panjabi', '', '1040', '1950', '', '', 'Pics', 'Coffy', 'XL', '', 1568),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0011', 'IND-IN', 'Gents Panjabi', '', '1040', '1950', '', '', 'Pics', 'Blue', 'M', '', 1569),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0012', 'IND-IN', 'Gents Panjabi', '', '1040', '1950', '', '', 'Pics', 'Blue', 'XL', '', 1570),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0013', 'IND-IN', 'Gents Panjabi', '', '1040', '1950', '', '', 'Pics', 'Coffy', 'L', '', 1571),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0014', 'IND-IN', 'Gents Panjabi', '', '1040', '1950', '', '', 'Pics', 'Coffy', 'M', '', 1572),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0015', 'IND-IN', 'Gents Panjabi', '', '1125', '2100', '', '', 'Pics', 'Red', 'M', '', 1573),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0016', 'IND-IN', 'Gents Panjabi', '', '1125', '2100', '', '', 'Pics', 'Red', 'L', '', 1574),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0017', 'IND-IN', 'Gents Panjabi', '', '1125', '2100', '', '', 'Pics', 'Red', 'XL', '', 1575),
  ('Gents Panjabi', 'Gents Panjabi', 'IND-IN-0018', 'IND-IN', 'Gents Panjabi', '', '1800', '3200', '', '', 'Pics', 'White', 'M', '', 1576),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0001', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1577),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0002', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1578),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0003', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1579),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0004', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Black', 'F', '', 1580),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0005', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Purple', 'F', '', 1581),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0006', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Purple', 'F', '', 1582),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0007', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Yellow', 'F', '', 1583),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0008', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1584),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0009', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1585),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0010', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'White', 'F', '', 1586),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0011', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'White', 'F', '', 1587),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0012', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'White', 'F', '', 1588),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0013', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1589),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0014', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1590),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0015', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1591),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0016', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Mixed', 'F', '', 1592),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0017', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Purple', 'F', '', 1593),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0018', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Silver', 'F', '', 1594),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0019', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'Yellow', 'F', '', 1595),
  ('Ladies Coti', 'Ladies Coti', 'IND-AW-0020', 'IND-AW', 'Ladies Coti', '', '700', '1350', '', '', 'Pics', 'White', 'F', '', 1596),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0080', 'EX', 'Gents Huddy Half Slip', '', '360', '850', '1156', '', 'Pics', 'Blue', 'F', '', 1597),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0083', 'EX', 'Gents Huddy Half Slip', '', '360', '850', '1156', '', 'Pics', 'Silver', 'F', '', 1598),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0079', 'EX', 'Gents Huddy Half Slip', '', '360', '850', '1156', '', 'Pics', 'Coffy', 'F', '', 1599),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0102', 'EX', 'Gents Huddy Half Slip', '', '320', '850', '1195', '', 'Pics', 'Mixed', 'F', '', 1600),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0103', 'EX', 'Gents Huddy Half Slip', '', '320', '850', '1195', '', 'Pics', 'Merun', 'F', '', 1601),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0104', 'EX', 'Gents Huddy Half Slip', '', '350', '850', '1195', '', 'Pics', 'Blue', 'F', '', 1602),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0100', 'EX', 'Gents Huddy Half Slip', '', '320', '850', '1195', '', 'Pics', 'Merun', 'F', '', 1603),
  ('Gents Huddy Half Slip', 'Gents Huddy Half Slip', 'EX-0101', 'EX', 'Gents Huddy Half Slip', '', '320', '850', '1195', '', 'Pics', 'Merun', 'F', '', 1604),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0092', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Red', 'F', '', 1605),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0105', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Black', 'F', '', 1606),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0085', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Black', 'F', '', 1607),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0087', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Pest', 'F', '', 1608),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0088', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Black', 'F', '', 1609),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0094', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Yellow', 'F', '', 1610),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'EX-0091', 'EX', 'Gents Huddy Full Slip', '', '380', '950', '1195', '', 'Pics', 'Blue', 'F', '', 1611),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'AK-0002', 'AK', 'Gents Huddy Full Slip', '', '300', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 1612),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'AK-0003', 'AK', 'Gents Huddy Full Slip', '', '300', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 1613),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'AK-0007', 'AK', 'Gents Huddy Full Slip', '', '300', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 1614),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'AK-0004', 'AK', 'Gents Huddy Full Slip', '', '300', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 1615),
  ('Gents Huddy Full Slip', 'Gents Huddy Full Slip', 'AK-0008', 'AK', 'Gents Huddy Full Slip', '', '300', '750', '1195', '', 'Pics', 'Mixed', 'F', '', 1616),
  ('Sweater', 'Sweater', 'IND-DIV-0135', 'IND-DIV', 'Gents Sweater Full Slip', '', '990', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 1617),
  ('Sweater', 'Sweater', 'IND-DIV-0136', 'IND-DIV', 'Gents Sweater Full Slip', '', '990', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 1618),
  ('Sweater', 'Sweater', 'IND-DIV-0137', 'IND-DIV', 'Gents Sweater Full Slip', '', '990', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 1619),
  ('Sweater', 'Sweater', 'IND-DIV-0138', 'IND-DIV', 'Gents Sweater Full Slip', '', '990', '1750', '941', '', 'Pics', 'Mixed', 'F', '', 1620),
  ('Sweater', 'Sweater', 'IND-MT-0030', 'IND-MT', 'Gents Sweater Full Slip', '', '1200', '2050', '5', '', 'Pics', 'Mixed', '40', '', 1621),
  ('Sweater', 'Sweater', 'IND-MT-0031', 'IND-MT', 'Gents Sweater Full Slip', '', '1200', '2050', '5', '', 'Pics', 'Mixed', '31', '', 1622),
  ('Sweater', 'Sweater', 'IND-MT-0032', 'IND-MT', 'Gents Sweater Full Slip', '', '1200', '2050', '5', '', 'Pics', 'Mixed', '42', '', 1623),
  ('Sweater', 'Sweater', 'IND-MT-0033', 'IND-MT', 'Gents Sweater Half Slip', '', '1200', '1650', '5', '', 'Pics', 'Mixed', '38', '', 1624),
  ('Sweater', 'Sweater', 'IND-MT-0034', 'IND-MT', 'Gents Sweater Half Slip', '', '1200', '1650', '5', '', 'Pics', 'Mixed', '38', '', 1625),
  ('Jacket', 'Jacket', 'IND-DIV-0139', 'IND-DIV', 'Gents Jacket', '', '2000', '3250', '941', '', 'Pics', 'Mixed', 'F', '', 1626),
  ('Jacket', 'Jacket', 'IND-DIV-0140', 'IND-DIV', 'Gents Jacket', '', '2000', '3250', '941', '', 'Pics', 'Mixed', 'F', '', 1627),
  ('Jacket', 'Jacket', 'IND-DIV-0141', 'IND-DIV', 'Gents Jacket', '', '2000', '3450', '941', '', 'Pics', 'Mixed', 'F', '', 1628),
  ('Jacket', 'Jacket', 'IND-DIV-0142', 'IND-DIV', 'Gents Jacket', '', '2000', '3350', '941', '', 'Pics', 'Mixed', 'F', '', 1629);

-- --------------------------------------------------------

--
-- Table structure for table `Purchase`
--

CREATE TABLE IF NOT EXISTS `Purchase` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chalan` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paymentType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receiveDate` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `totalAmount` double DEFAULT NULL,
  `paymentAmount` double DEFAULT NULL,
  `dueAmount` double DEFAULT NULL,
  `advanceAmount` double DEFAULT NULL,
  `vatAmount` double DEFAULT NULL,
  `taxAmount` double DEFAULT NULL,
  `commissionAmount` double DEFAULT NULL,
  `totalQnt` int(11) DEFAULT NULL,
  `totalItem` int(11) DEFAULT NULL,
  `paymentMethod` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `createdBy_id` int(11) DEFAULT NULL,
  `approvedBy_id` int(11) DEFAULT NULL,
  `process` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Purchase`
--

INSERT INTO `Purchase` (`id`, `vendor_id`, `invoice`, `chalan`, `memo`, `paymentType`, `receiveDate`, `created`, `totalAmount`, `paymentAmount`, `dueAmount`, `advanceAmount`, `vatAmount`, `taxAmount`, `commissionAmount`, `totalQnt`, `totalItem`, `paymentMethod`, `status`, `path`, `inventoryConfig_id`, `createdBy_id`, `approvedBy_id`, `process`, `grn`) VALUES
  (1, 6, NULL, NULL, '1', NULL, '2011-01-01 00:00:00', '2016-02-09 08:49:45', 1000, 1000, NULL, NULL, NULL, NULL, NULL, 10, 1, NULL, 1, NULL, 2, NULL, NULL, 'approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `PurchaseItem`
--

CREATE TABLE IF NOT EXISTS `PurchaseItem` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `purchasePrice` double NOT NULL,
  `itemCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salesPrice` double DEFAULT NULL,
  `purchaseSubTotal` double DEFAULT NULL,
  `salesSubTotal` double DEFAULT NULL,
  `webPrice` double DEFAULT NULL,
  `webSubTotal` double DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `purchaseVendorItem_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PurchaseItem`
--

INSERT INTO `PurchaseItem` (`id`, `item_id`, `name`, `quantity`, `purchasePrice`, `itemCode`, `salesPrice`, `purchaseSubTotal`, `salesSubTotal`, `webPrice`, `webSubTotal`, `purchase_id`, `barcode`, `purchaseVendorItem_id`) VALUES
  (1, 1, NULL, 100, 100, NULL, 150, NULL, NULL, 0, NULL, 1, '8940001240956', 1);

-- --------------------------------------------------------

--
-- Table structure for table `PurchaseOrder`
--

CREATE TABLE IF NOT EXISTS `PurchaseOrder` (
  `id` int(11) NOT NULL,
  `invoice` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chalan` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paymentType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receiveDate` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `paymentAmount` double DEFAULT NULL,
  `dueAmount` double DEFAULT NULL,
  `advanceAmount` double DEFAULT NULL,
  `vatAmount` double DEFAULT NULL,
  `taxAmount` double DEFAULT NULL,
  `totalQnt` int(11) DEFAULT NULL,
  `totalItem` int(11) DEFAULT NULL,
  `paymentMethod` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `totalAmount` double DEFAULT NULL,
  `commissionAmount` double DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PurchaseOrder`
--

INSERT INTO `PurchaseOrder` (`id`, `invoice`, `chalan`, `memo`, `paymentType`, `receiveDate`, `created`, `paymentAmount`, `dueAmount`, `advanceAmount`, `vatAmount`, `taxAmount`, `totalQnt`, `totalItem`, `paymentMethod`, `totalAmount`, `commissionAmount`, `path`, `status`) VALUES
  (1, '22', NULL, '22', NULL, '2011-01-01 00:00:00', '2016-01-03 22:53:13', 22, 2, NULL, NULL, NULL, 2, 2, NULL, 22, 22, 'ACFrOgBbW7Oy39oQzF9muTBNOIqkCJIzFkjBCusQMJ088H6vWP6MVd11VrhSN50Z-vxB3Cp486DMoQ3lC2H-1DDDf53I3bpKTqsK3SfgO_35pVcMdK8-d3XJ_sqTfqg=_print=true&nonce=mjq1f6j7gkj3c&user=14662235397777372877&hash=g0demsu3ij7489au7d5tct2prbko0uuh.pdf', 0);

-- --------------------------------------------------------

--
-- Table structure for table `PurchaseReturn`
--

CREATE TABLE IF NOT EXISTS `PurchaseReturn` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `totalQuantity` int(11) DEFAULT NULL,
  `totalItem` int(11) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createdBy_id` int(11) DEFAULT NULL,
  `process` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PurchaseReturn`
--

INSERT INTO `PurchaseReturn` (`id`, `vendor_id`, `inventoryConfig_id`, `created`, `updated`, `totalQuantity`, `totalItem`, `total`, `code`, `createdBy_id`, `process`) VALUES
  (2, 7, 2, '2016-02-11 15:23:54', '2016-02-11 19:36:14', 0, 1, 0, '0002000001', 1, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `PurchaseReturnItem`
--

CREATE TABLE IF NOT EXISTS `PurchaseReturnItem` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `purchaseReturn_id` int(11) DEFAULT NULL,
  `purchaseItem_id` int(11) DEFAULT NULL,
  `subTotal` double NOT NULL,
  `replaceQuantity` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PurchaseReturnItem`
--

INSERT INTO `PurchaseReturnItem` (`id`, `quantity`, `price`, `purchaseReturn_id`, `purchaseItem_id`, `subTotal`, `replaceQuantity`) VALUES
  (2, 0, 100, 2, 1, 0, 10);

-- --------------------------------------------------------

--
-- Table structure for table `PurchaseVendorItem`
--

CREATE TABLE IF NOT EXISTS `PurchaseVendorItem` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `purchasePrice` decimal(10,0) DEFAULT NULL,
  `salesPrice` decimal(10,0) DEFAULT NULL,
  `webPrice` decimal(10,0) DEFAULT NULL,
  `subTotalPurchasePrice` decimal(10,0) DEFAULT NULL,
  `subTotalSalesPrice` decimal(10,0) DEFAULT NULL,
  `subTotalWebPrice` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `PurchaseVendorItem`
--

INSERT INTO `PurchaseVendorItem` (`id`, `purchase_id`, `name`, `quantity`, `purchasePrice`, `salesPrice`, `webPrice`, `subTotalPurchasePrice`, `subTotalSalesPrice`, `subTotalWebPrice`) VALUES
  (1, 1, 'test', 10, '100', '150', '0', '1000', '1500', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ReturnPurchase`
--

CREATE TABLE IF NOT EXISTS `ReturnPurchase` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Sales`
--

CREATE TABLE IF NOT EXISTS `Sales` (
  `id` int(11) NOT NULL,
  `paymentMethod` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subTotal` decimal(10,0) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `vat` decimal(10,0) DEFAULT NULL,
  `total` decimal(10,0) DEFAULT NULL,
  `due` decimal(10,0) DEFAULT NULL,
  `mobile` longtext COLLATE utf8_unicode_ci,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `paymentStatus` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salesBy_id` int(11) DEFAULT NULL,
  `salesCode` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `totalItem` smallint(6) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `chequeCardNo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `paymentCard_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Sales`
--

INSERT INTO `Sales` (`id`, `paymentMethod`, `subTotal`, `discount`, `vat`, `total`, `due`, `mobile`, `inventoryConfig_id`, `customer_id`, `paymentStatus`, `salesBy_id`, `salesCode`, `created`, `updated`, `totalItem`, `bank_id`, `chequeCardNo`, `paymentCard_id`) VALUES
  (1, 'Cash', '750', '0', '0', '750', '0', NULL, 2, NULL, 'Paid', 1, '0002000001', '2016-02-12 00:59:48', '2016-02-12 01:00:40', 1, NULL, NULL, NULL),
  (2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, 'Pending', 1, '0002000002', '2016-02-12 01:00:41', '2016-02-12 01:00:41', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `SalesItem`
--

CREATE TABLE IF NOT EXISTS `SalesItem` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `salesPrice` decimal(10,0) NOT NULL,
  `purchasePrice` decimal(10,0) NOT NULL,
  `estimatePrice` decimal(10,0) NOT NULL,
  `customPrice` tinyint(1) NOT NULL,
  `purchaseItem_id` int(11) DEFAULT NULL,
  `subTotal` decimal(10,0) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `SalesItem`
--

INSERT INTO `SalesItem` (`id`, `item_id`, `sales_id`, `quantity`, `salesPrice`, `purchasePrice`, `estimatePrice`, `customPrice`, `purchaseItem_id`, `subTotal`) VALUES
  (1, 1, 1, 5, '150', '100', '150', 0, 1, '750');

-- --------------------------------------------------------

--
-- Table structure for table `SalesReturn`
--

CREATE TABLE IF NOT EXISTS `SalesReturn` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) DEFAULT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `createdBy_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SalesReturnItem`
--

CREATE TABLE IF NOT EXISTS `SalesReturnItem` (
  `id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `salesReturn_id` int(11) DEFAULT NULL,
  `salesItem_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Scholarship`
--

CREATE TABLE IF NOT EXISTS `Scholarship` (
  `id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `establishment` date DEFAULT NULL,
  `registrationNo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `organizationName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPersonDesignation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skypeId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_syndicate`
--

CREATE TABLE IF NOT EXISTS `scholarship_syndicate` (
  `scholarship_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SiteContent`
--

CREATE TABLE IF NOT EXISTS `SiteContent` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isPage` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SiteSetting`
--

CREATE TABLE IF NOT EXISTS `SiteSetting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `uniqueCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL,
  `webTheme_id` int(11) DEFAULT NULL,
  `mobileTheme_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `SiteSetting`
--

INSERT INTO `SiteSetting` (`id`, `user_id`, `theme_id`, `uniqueCode`, `globalOption_id`, `webTheme_id`, `mobileTheme_id`) VALUES
  (2, 11, 1, NULL, 8, NULL, NULL),
  (3, 13, 1, NULL, 9, NULL, NULL),
  (4, 16, 1, NULL, 10, NULL, NULL),
  (5, 17, NULL, NULL, 11, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sitesetting_appmodule`
--

CREATE TABLE IF NOT EXISTS `sitesetting_appmodule` (
  `sitesetting_id` int(11) NOT NULL,
  `app_module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sitesetting_appmodule`
--

INSERT INTO `sitesetting_appmodule` (`sitesetting_id`, `app_module_id`) VALUES
  (5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sitesetting_module`
--

CREATE TABLE IF NOT EXISTS `sitesetting_module` (
  `sitesetting_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sitesetting_module`
--

INSERT INTO `sitesetting_module` (`sitesetting_id`, `module_id`) VALUES
  (5, 12);

-- --------------------------------------------------------

--
-- Table structure for table `sitesetting_syndicate`
--

CREATE TABLE IF NOT EXISTS `sitesetting_syndicate` (
  `sitesetting_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sitesetting_syndicatemodule`
--

CREATE TABLE IF NOT EXISTS `sitesetting_syndicatemodule` (
  `sitesetting_id` int(11) NOT NULL,
  `syndicate_module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SiteSlider`
--

CREATE TABLE IF NOT EXISTS `SiteSlider` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` longtext COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `sorting` smallint(6) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `StockItem`
--

CREATE TABLE IF NOT EXISTS `StockItem` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `createdBy_id` int(11) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `process` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `purchaseItem_id` int(11) DEFAULT NULL,
  `salesItem_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `StockItem`
--

INSERT INTO `StockItem` (`id`, `item_id`, `vendor_id`, `category_id`, `quantity`, `created`, `createdBy_id`, `country_id`, `brand_id`, `process`, `inventoryConfig_id`, `purchaseItem_id`, `salesItem_id`) VALUES
  (1, 1, 6, 628, 10, '2016-02-09 09:06:14', 1, NULL, NULL, 'purchase', 2, 1, NULL),
  (2, 1, 6, 628, 10, '2016-02-09 12:06:50', 1, NULL, NULL, 'purchase', 2, 1, NULL),
  (9, 1, 7, 628, -10, '2016-02-11 15:30:09', 1, NULL, NULL, 'purchaseReturn', 2, 1, NULL),
  (20, 1, 7, 628, 10, '2016-02-11 19:26:44', 1, NULL, NULL, 'purchaseReturnReplace', 2, 1, NULL),
  (21, 1, 7, 628, 0, '2016-02-11 19:36:14', 1, NULL, NULL, 'purchaseReturnReplace', 2, 1, NULL),
  (22, 1, 6, 628, -5, '2016-02-12 01:00:40', 1, NULL, NULL, 'sales', 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Student`
--

CREATE TABLE IF NOT EXISTS `Student` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dateOfBirth` date NOT NULL,
  `bloodGroup` smallint(6) NOT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `addressLine1` longtext COLLATE utf8_unicode_ci NOT NULL,
  `addressLine2` longtext COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `StudyAbroad`
--

CREATE TABLE IF NOT EXISTS `StudyAbroad` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `establishment` date DEFAULT NULL,
  `registrationNo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPersonDesignation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skypeId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weeklyOffDay` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studyabroad_syndicate`
--

CREATE TABLE IF NOT EXISTS `studyabroad_syndicate` (
  `studyabroad_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SubscribeEmail`
--

CREATE TABLE IF NOT EXISTS `SubscribeEmail` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8_unicode_ci,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `companyName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL,
  `vendorCode` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) DEFAULT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `name`, `address`, `mobile`, `email`, `status`, `companyName`, `inventoryConfig_id`, `vendorCode`, `country_id`, `code`) VALUES
  (6, 'GB', NULL, NULL, NULL, 1, 'GB', 2, 'GB', NULL, '006'),
  (7, 'CM', NULL, NULL, NULL, 1, 'CM', 2, 'CM', NULL, '007'),
  (8, 'CL', NULL, NULL, NULL, 1, 'CL', 2, 'CL', NULL, '008'),
  (9, 'IS', NULL, NULL, NULL, 1, 'IS', 2, 'IS', NULL, '009'),
  (10, 'RM', NULL, NULL, NULL, 1, 'RM', 2, 'RM', NULL, '010'),
  (11, '', NULL, NULL, NULL, 1, '', 2, '', NULL, '011'),
  (12, 'EX', NULL, NULL, NULL, 1, 'EX', 2, 'EX', NULL, '012'),
  (13, 'HT', NULL, NULL, NULL, 1, 'HT', 2, 'HT', NULL, '013'),
  (14, 'AB', NULL, NULL, NULL, 1, 'AB', 2, 'AB', NULL, '014'),
  (15, 'SF', NULL, NULL, NULL, 1, 'SF', 2, 'SF', NULL, '015'),
  (16, 'FC', NULL, NULL, NULL, 1, 'FC', 2, 'FC', NULL, '016'),
  (17, 'IND-MT', NULL, NULL, NULL, 1, 'IND-MT', 2, 'IND-MT', NULL, '017'),
  (18, 'NK', NULL, NULL, NULL, 1, 'NK', 2, 'NK', NULL, '018'),
  (19, 'IND-SS', NULL, NULL, NULL, 1, 'IND-SS', 2, 'IND-SS', NULL, '019'),
  (20, 'IND-DIV', NULL, NULL, NULL, 1, 'IND-DIV', 2, 'IND-DIV', NULL, '020'),
  (21, 'NP', NULL, NULL, NULL, 1, 'NP', 2, 'NP', NULL, '021'),
  (22, 'JK', NULL, NULL, NULL, 1, 'JK', 2, 'JK', NULL, '022'),
  (23, 'SB', NULL, NULL, NULL, 1, 'SB', 2, 'SB', NULL, '023'),
  (24, 'CLC', NULL, NULL, NULL, 1, 'CLC', 2, 'CLC', NULL, '024'),
  (25, 'BB', NULL, NULL, NULL, 1, 'BB', 2, 'BB', NULL, '025'),
  (26, 'IND-ISS', NULL, NULL, NULL, 1, 'IND-ISS', 2, 'IND-ISS', NULL, '026'),
  (27, 'XM', NULL, NULL, NULL, 1, 'XM', 2, 'XM', NULL, '027'),
  (28, 'IND-IBL', NULL, NULL, NULL, 1, 'IND-IBL', 2, 'IND-IBL', NULL, '028'),
  (29, 'VF', NULL, NULL, NULL, 1, 'VF', 2, 'VF', NULL, '029'),
  (30, 'CT', NULL, NULL, NULL, 1, 'CT', 2, 'CT', NULL, '030'),
  (31, 'LS', NULL, NULL, NULL, 1, 'LS', 2, 'LS', NULL, '031'),
  (32, 'RB', NULL, NULL, NULL, 1, 'RB', 2, 'RB', NULL, '032'),
  (33, 'BT', NULL, NULL, NULL, 1, 'BT', 2, 'BT', NULL, '033'),
  (34, 'FO', NULL, NULL, NULL, 1, 'FO', 2, 'FO', NULL, '034'),
  (35, 'JF', NULL, NULL, NULL, 1, 'JF', 2, 'JF', NULL, '035'),
  (36, 'JE', NULL, NULL, NULL, 1, 'JE', 2, 'JE', NULL, '036'),
  (37, 'AF', NULL, NULL, NULL, 1, 'AF', 2, 'AF', NULL, '037'),
  (38, 'SS', NULL, NULL, NULL, 1, 'SS', 2, 'SS', NULL, '038'),
  (39, 'MK', NULL, NULL, NULL, 1, 'MK', 2, 'MK', NULL, '039'),
  (40, 'IND-IBY', NULL, NULL, NULL, 1, 'IND-IBY', 2, 'IND-IBY', NULL, '040'),
  (41, 'IND-FU', NULL, NULL, NULL, 1, 'IND-FU', 2, 'IND-FU', NULL, '041'),
  (42, 'FN', NULL, NULL, NULL, 1, 'FN', 2, 'FN', NULL, '042'),
  (43, 'FG', NULL, NULL, NULL, 1, 'FG', 2, 'FG', NULL, '043'),
  (44, 'EPZ', NULL, NULL, NULL, 1, 'EPZ', 2, 'EPZ', NULL, '044'),
  (45, 'IND-IN', NULL, NULL, NULL, 1, 'IND-IN', 2, 'IND-IN', NULL, '045'),
  (46, 'IND-AW', NULL, NULL, NULL, 1, 'IND-AW', 2, 'IND-AW', NULL, '046'),
  (47, 'AK', NULL, NULL, NULL, 1, 'AK', 2, 'AK', NULL, '047');

-- --------------------------------------------------------

--
-- Table structure for table `sylius_adjustment`
--

CREATE TABLE IF NOT EXISTS `sylius_adjustment` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `is_neutral` tinyint(1) NOT NULL,
  `is_locked` tinyint(1) NOT NULL,
  `origin_id` int(11) DEFAULT NULL,
  `origin_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sylius_cart`
--

CREATE TABLE IF NOT EXISTS `sylius_cart` (
  `id` int(11) NOT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `items_total` int(11) NOT NULL,
  `adjustments_total` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sylius_order_comment`
--

CREATE TABLE IF NOT EXISTS `sylius_order_comment` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `notify_customer` tinyint(1) NOT NULL,
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sylius_order_identity`
--

CREATE TABLE IF NOT EXISTS `sylius_order_identity` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `syndicate`
--

CREATE TABLE IF NOT EXISTS `syndicate` (
  `id` int(11) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `path` varchar(3000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `entityName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domainProperty` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `syndicate`
--

INSERT INTO `syndicate` (`id`, `parent`, `name`, `status`, `level`, `path`, `slug`, `entityName`, `domainProperty`) VALUES
  (1, NULL, 'Education / Sports / Clubs / Associations', 1, 1, '1/', 'education-sports-clubs-associations', 'Education', 'education'),
  (2, NULL, 'Health Care / Health Services', 1, 1, '2', 'health-care-health-services', 'Medical', 'medical'),
  (3, NULL, 'Cars Vehicles Transport', 1, 1, '3', 'cars-vehicles-transport', 'Transport', 'transport'),
  (11, NULL, 'Hotels Restaurants &Tourism', 1, 1, '11/', 'hotels-restaurants-tourism', 'Reservation', 'reservation'),
  (20, NULL, 'Product Manufacturing', 1, 1, '20', 'product-manufacturing', 'Ecommerce', 'ecommerce'),
  (21, NULL, 'Apartment Management  ', 1, 1, '21', 'apartment', 'Apartment', ''),
  (22, NULL, 'Building / Construction/ Property', 1, 1, '22/', 'building-construction-property', 'Property', 'property'),
  (23, 22, 'Architect', 1, 2, '22/23/', 'architect', 'Property', 'property-1'),
  (24, NULL, 'Retailers / Commerce', 1, 1, '24/', 'retailers-commerce', NULL, 'ecommerce'),
  (25, 24, 'Fashion House', 1, 2, '24/25/', 'fashion-house', NULL, 'ecommerce'),
  (26, NULL, 'Professional Services', 1, 1, '26/', 'professional-services', NULL, 'service'),
  (27, NULL, 'Trades / Property Maintenance', 1, 1, '27/', 'trades-property-maintenance', NULL, 'property'),
  (28, NULL, 'Businesses', 1, 1, '28/', 'businesses', NULL, 'businesses'),
  (29, NULL, 'Creative Services and Media', 1, 1, '29/', 'creative-services-and-media', NULL, 'service'),
  (30, NULL, 'Entertainment / Leisure', 1, 1, '30/', 'entertainment-leisure', NULL, 'service'),
  (31, NULL, 'Financial / Legal Services', 1, 1, '31/', 'financial-legal-services', NULL, 'service'),
  (32, NULL, 'Other', 1, 1, '32/', 'other', NULL, 'others'),
  (33, 22, 'Building Contractor', 1, 2, '22/33/', 'building-contractor', NULL, 'application'),
  (34, 22, 'Building Maintenance / Property Manager', 1, 2, '22/34/', 'building-maintenance-property-manager', NULL, NULL),
  (35, 22, 'Civil Engineer / Engineering Consultant', 1, 2, '22/35/', 'civil-engineer-engineering-consultant', NULL, NULL),
  (36, 22, 'Developer / Construction Company', 1, 2, '22/36/', 'developer-construction-company', NULL, NULL),
  (37, 22, 'Estate agent', 1, 2, '22/37/', 'estate-agent', NULL, NULL),
  (38, 22, 'Other Building Services', 1, 2, '22/38/', 'other-building-services', NULL, NULL),
  (39, 22, 'Building / Construction services', 1, 2, '22/39/', 'building-construction-services', NULL, NULL),
  (40, 22, 'Building maintenance', 1, 2, '22/40/', 'building-maintenance', NULL, NULL),
  (41, NULL, 'Construction Equipment', 1, 1, '41/', 'construction-equipment', NULL, NULL),
  (42, 22, 'Construction Company', 1, 2, '22/42/', 'construction-company', NULL, NULL),
  (43, 22, 'Cleaning Services', 1, 2, '22/43/', 'cleaning-services', NULL, NULL),
  (44, 22, 'Home Staging', 1, 2, '22/44/', 'home-staging', NULL, NULL),
  (45, 22, 'Interior Design / Renovations', 1, 2, '22/45/', 'interior-design-renovations', NULL, NULL),
  (46, 22, 'Plant hire and tool rental', 1, 2, '22/46/', 'plant-hire-and-tool-rental', NULL, NULL),
  (47, NULL, 'Property Developer', 1, 1, '47/', 'property-developer', NULL, NULL),
  (48, 22, 'Real Estate Agent', 1, 2, '22/48/', 'real-estate-agent', NULL, NULL),
  (49, 22, 'Shop fittings', 1, 2, '22/49/', 'shop-fittings', NULL, NULL),
  (50, 22, 'Surveyor', 1, 2, '22/50/', 'surveyor', NULL, NULL),
  (52, 3, 'Car Dealer', 1, 2, '3/52/', 'car-dealer', NULL, NULL),
  (53, 22, 'Courier Service', 1, 2, '22/53/', 'courier-service', NULL, NULL),
  (54, 3, 'Driving School', 1, 2, '3/54/', 'driving-school', NULL, NULL),
  (55, 21, 'Garage Service & Repairs', 1, 2, '21/55/', 'garage-service-repairs', NULL, NULL),
  (56, 3, 'Haulage & Goods Transportation', 1, 2, '3/56/', 'haulage-goods-transportation', NULL, NULL),
  (57, 3, 'Motorcycle & Scooter Dealer', 1, 2, '3/57/', 'motorcycle-scooter-dealer', NULL, NULL),
  (58, 3, 'Moving / Relocation / Storage Services', 1, 2, '3/58/', 'moving-relocation-storage-services', NULL, NULL),
  (59, 3, 'Other Transportation', 1, 2, '3/59/', 'other-transportation', NULL, NULL),
  (60, 3, 'Taxis & Private Hire Vehicles', 1, 2, '3/60/', 'taxis-private-hire-vehicles', NULL, NULL),
  (61, 3, 'Recreational Vehicles', 1, 2, '3/61/', 'recreational-vehicles', NULL, NULL),
  (62, 3, 'Transportation Logistics', 1, 2, '3/62/', 'transportation-logistics', NULL, NULL),
  (63, 3, 'Other', 1, 2, '3/63/', 'other-2', NULL, NULL),
  (64, 1, 'Community Organizations / Local Initiatives', 1, 2, '1/64/', 'community-organizations-local-initiatives', NULL, NULL),
  (65, 1, 'Day Nursery / Crèche', 1, 2, '1/65/', 'day-nursery-creche', NULL, NULL),
  (66, 1, 'Generic Business Type', 1, 2, '1/66/', 'generic-business-type', NULL, NULL),
  (67, 1, 'Gym / Fitness / Recreational Sports Centre', 1, 2, '1/67/', 'gym-fitness-recreational-sports-centre', NULL, NULL),
  (68, 1, 'Local Authorities / Social Services', 1, 2, '1/68/', 'local-authorities-social-services', NULL, NULL),
  (69, 1, 'Local Government Groups', 1, 2, '1/69/', 'local-government-groups', NULL, NULL),
  (70, 1, 'Non-Profit Group / Charity', 1, 2, '1/70/', 'non-profit-group-charity', NULL, NULL),
  (71, 1, 'Other Culture / Arts', 1, 2, '1/71/', 'other-culture-arts', NULL, NULL),
  (72, 1, 'School / College', 1, 2, '1/72/', 'school-college', NULL, NULL),
  (73, 1, 'Sports Club', 1, 2, '1/73/', 'sports-club', NULL, NULL),
  (74, 1, 'Adult education', 1, 2, '1/74/', 'adult-education', NULL, NULL),
  (75, 1, 'Educational Books & Software', 1, 2, '1/75/', 'educational-books-software', NULL, NULL),
  (76, 1, 'eLearning', 1, 2, '1/76/', 'elearning', NULL, NULL),
  (77, 1, 'Montessori', 1, 2, '1/77/', 'montessori', NULL, NULL),
  (78, 1, 'Playgroups', 1, 2, '1/78/', 'playgroups', NULL, NULL),
  (79, 1, 'Professional Education', 1, 2, '1/79/', 'professional-education', NULL, NULL),
  (80, 1, 'Professional Education', 1, 2, '1/80/', 'professional-education-1', NULL, NULL),
  (81, 1, 'Tutoring Services', 1, 2, '1/81/', 'tutoring-services', NULL, NULL),
  (82, 1, 'Other', 1, 2, '1/82/', 'other-3', NULL, NULL),
  (83, 2, 'Chemist / Pharmacy', 1, 2, '2/83/', 'chemist-pharmacy', NULL, NULL),
  (84, 2, 'Chiropractor / Osteopath', 1, 2, '2/84/', 'chiropractor-osteopath', NULL, NULL),
  (85, 2, 'Dentist / Hygienist', 1, 2, '2/85/', 'dentist-hygienist', NULL, NULL),
  (86, 2, 'Doctor', 1, 2, '2/86/', 'doctor', NULL, NULL),
  (87, 2, 'Optician', 1, 2, '2/87/', 'optician', NULL, NULL),
  (88, 2, 'Other Health', 1, 2, '2/88/', 'other-health', NULL, NULL),
  (89, 2, 'Physiotherapist / Massage', 1, 2, '2/89/', 'physiotherapist-massage', NULL, NULL),
  (90, 2, 'Retirement Home / Residential / Nursing', 1, 2, '2/90/', 'retirement-home-residential-nursing', NULL, NULL),
  (91, 2, 'Beauty Salon', 1, 2, '2/91/', 'beauty-salon', NULL, NULL),
  (92, 2, 'Homeopathy', 1, 2, '2/92/', 'homeopathy', NULL, NULL),
  (93, 2, 'Hospitals', 1, 2, '2/93/', 'hospitals', NULL, NULL),
  (94, 2, 'Nursing', 1, 2, '2/94/', 'nursing', NULL, NULL),
  (95, 2, 'Nutritionist', 1, 2, '2/95/', 'nutritionist', NULL, NULL),
  (96, 11, 'Amusement & Recreation Services', 1, 2, '11/96/', 'amusement-recreation-services', NULL, NULL),
  (97, 11, 'Bed &amp; Breakfast / Guest House', 1, 2, '11/97/', 'bed-amp-breakfast-guest-house', NULL, NULL),
  (98, 11, 'Caterer', 1, 2, '11/98/', 'caterer', NULL, NULL),
  (99, 11, 'Fast Food & Take Away', 1, 2, '11/99/', 'fast-food-take-away', NULL, NULL),
  (100, 11, 'Holiday Rentals', 1, 2, '11/100/', 'holiday-rentals', NULL, NULL),
  (101, 11, 'Hotel', 1, 2, '11/101/', 'hotel', NULL, NULL),
  (102, 11, 'Other Tourism', 1, 2, '11/102/', 'other-tourism', NULL, NULL),
  (103, 11, 'Restaurant', 1, 2, '11/103/', 'restaurant', NULL, NULL),
  (104, 11, 'Travel Agency / Tourist Information', 1, 2, '11/104/', 'travel-agency-tourist-information', NULL, NULL),
  (105, 20, 'General Manufacturing / Production', 1, 2, '20/105/', 'general-manufacturing-production', NULL, NULL),
  (106, 26, 'Accountant / Tax Auditor', 1, 2, '26/106/', 'accountant-tax-auditor', NULL, NULL),
  (107, 26, 'Advertising Agency', 1, 2, '26/107/', 'advertising-agency', NULL, NULL),
  (108, 26, 'Beauty Salon / Nail Studio', 1, 2, '26/108/', 'beauty-salon-nail-studio', NULL, NULL),
  (109, 26, 'Commercial Art', 1, 2, '26/109/', 'commercial-art', NULL, NULL),
  (110, 26, 'Commercial Cleaning Services', 1, 2, '26/110/', 'commercial-cleaning-services', NULL, NULL),
  (111, 26, 'Copying & Printing Services', 1, 2, '26/111/', 'copying-printing-services', NULL, NULL),
  (112, 26, 'Counselling Services', 1, 2, '26/112/', 'counselling-services', NULL, NULL),
  (113, 26, 'DJ / Band', 1, 2, '26/113/', 'dj-band', NULL, NULL),
  (114, 26, 'Domestic Cleaning Services', 1, 2, '26/114/', 'domestic-cleaning-services', NULL, NULL),
  (115, 26, 'Employment Agency', 1, 2, '26/115/', 'employment-agency', NULL, NULL),
  (116, 26, 'Graphic Design', 1, 2, '26/116/', 'graphic-design', NULL, NULL),
  (117, 26, 'Graphic Design', 1, 2, '26/117/', 'graphic-design-1', NULL, NULL),
  (118, 26, 'Hairdresser / Barber', 1, 2, '26/118/', 'hairdresser-barber', NULL, NULL),
  (119, 26, 'Insurance Agent / Broker', 1, 2, '26/119/', 'insurance-agent-broker', NULL, NULL),
  (120, 26, 'Interior Design / Renovations', 1, 2, '26/120/', 'interior-design-renovations-1', NULL, NULL),
  (121, 26, 'Investment Advisor', 1, 2, '26/121/', 'investment-advisor', NULL, NULL),
  (122, 26, 'IT Consulting / Services', 1, 2, '26/122/', 'it-consulting-services', NULL, NULL),
  (123, 26, 'Life Coach / Business Coach', 1, 2, '26/123/', 'life-coach-business-coach', NULL, NULL),
  (124, 26, 'Management Consultant', 1, 2, '26/124/', 'management-consultant', NULL, NULL),
  (125, 26, 'Mobile Website Design', 1, 2, '26/125/', 'mobile-website-design', NULL, NULL),
  (126, 26, 'Model / Actress / Actor / Entertainer', 1, 2, '26/126/', 'model-actress-actor-entertainer', NULL, NULL),
  (127, 26, 'Photography Studio', 1, 2, '26/127/', 'photography-studio', NULL, NULL),
  (128, 26, 'Pool / Hot Tub Services', 1, 2, '26/128/', 'pool-hot-tub-services', NULL, NULL),
  (129, 26, 'PR / Marketing / Communications Services', 1, 2, '26/129/', 'pr-marketing-communications-services', NULL, NULL),
  (130, 26, 'Solicitor', 1, 2, '26/130/', 'solicitor', NULL, NULL),
  (131, 26, 'Staffing Services', 1, 2, '26/131/', 'staffing-services', NULL, NULL),
  (132, 26, 'Surveyor', 1, 2, '26/132/', 'surveyor-1', NULL, NULL),
  (133, 26, 'Tailor / Dressmaker', 1, 2, '26/133/', 'tailor-dressmaker', NULL, NULL),
  (134, 26, 'Talent Management', 1, 2, '26/134/', 'talent-management', NULL, NULL),
  (135, 26, 'Translation Services', 1, 2, '26/135/', 'translation-services', NULL, NULL),
  (136, 26, 'Architecture / surveying', 1, 2, '26/136/', 'architecture-surveying', NULL, NULL),
  (137, 26, 'Civil engineering', 1, 2, '26/137/', 'civil-engineering', NULL, NULL),
  (138, 26, 'Consulting', 1, 2, '26/138/', 'consulting', NULL, NULL),
  (139, 26, 'Engineering', 1, 2, '26/139/', 'engineering', NULL, NULL),
  (140, 26, 'Estate agent', 1, 2, '26/140/', 'estate-agent-1', NULL, NULL),
  (141, 26, 'Hardware consultant', 1, 2, '26/141/', 'hardware-consultant', NULL, NULL),
  (142, 26, 'Legal Services', 1, 2, '26/142/', 'legal-services', NULL, NULL),
  (143, 26, 'Real Estate Agent', 1, 2, '26/143/', 'real-estate-agent-1', NULL, NULL),
  (144, 26, 'Software consultant', 1, 2, '26/144/', 'software-consultant', NULL, NULL),
  (145, 26, 'Veterinary Services', 1, 2, '26/145/', 'veterinary-services', NULL, NULL),
  (146, 26, 'Web Developer / Designer', 1, 2, '26/146/', 'web-developer-designer', NULL, NULL),
  (147, 29, 'Antique Shop', 1, 2, '29/147/', 'antique-shop', NULL, NULL),
  (148, 29, 'Author', 1, 2, '29/148/', 'author', NULL, NULL),
  (149, 29, 'Bakery / Cake Maker', 1, 2, '29/149/', 'bakery-cake-maker', NULL, NULL),
  (150, 29, 'Book Shop', 1, 2, '29/150/', 'book-shop', NULL, NULL),
  (151, 29, 'Carpet / Floor Covering Retailer', 1, 2, '29/151/', 'carpet-floor-covering-retailer', NULL, NULL),
  (152, 29, 'Clothes Shop', 1, 2, '29/152/', 'clothes-shop', NULL, NULL),
  (153, 29, 'Computer Consultant', 1, 2, '29/153/', 'computer-consultant', NULL, NULL),
  (154, 29, 'Distributor / Wholesale', 1, 2, '29/154/', 'distributor-wholesale', NULL, NULL),
  (155, 29, 'DIY / Builders Merchant / Hardware Store', 1, 2, '29/155/', 'diy-builders-merchant-hardware-store', NULL, NULL),
  (156, 29, 'Electrical Appliance Repair Services', 1, 2, '29/156/', 'electrical-appliance-repair-services', NULL, NULL),
  (157, 29, 'Farmers'' Market / Farm Produce', 1, 2, '29/157/', 'farmers-market-farm-produce', NULL, NULL),
  (158, 29, 'Florist', 1, 2, '29/158/', 'florist', NULL, NULL),
  (159, 29, 'Furniture Retailer', 1, 2, '29/159/', 'furniture-retailer', NULL, NULL),
  (160, 29, 'Grocers / Greengrocers', 1, 2, '29/160/', 'grocers-greengrocers', NULL, NULL),
  (161, 29, 'Home Appliance Store', 1, 2, '29/161/', 'home-appliance-store', NULL, NULL),
  (162, 29, 'Jeweller / Handicrafts', 1, 2, '29/162/', 'jeweller-handicrafts', NULL, NULL),
  (163, 29, 'Shoe Shop', 1, 2, '29/163/', 'shoe-shop', NULL, NULL),
  (164, 29, 'Soft Furnishing / Lighting / Decorator', 1, 2, '29/164/', 'soft-furnishing-lighting-decorator', NULL, NULL),
  (165, 29, 'Specialist Retailer', 1, 2, '29/165/', 'specialist-retailer', NULL, NULL),
  (166, 29, 'Sport and Leisure Goods', 1, 2, '29/166/', 'sport-and-leisure-goods', NULL, NULL),
  (167, 29, 'Stationery / Office Supply', 1, 2, '29/167/', 'stationery-office-supply', NULL, NULL),
  (168, 29, 'Toy / Model Retailer', 1, 2, '29/168/', 'toy-model-retailer', NULL, NULL),
  (169, 21, 'Bathroom Fitter', 1, 2, '21/169/', 'bathroom-fitter', NULL, NULL),
  (170, 27, 'Carpenter / Joiner', 1, 2, '27/170/', 'carpenter-joiner', NULL, NULL),
  (171, 27, 'Electrician', 1, 2, '27/171/', 'electrician', NULL, NULL),
  (172, 27, 'Equipment Rental & Leasing Services', 1, 2, '27/172/', 'equipment-rental-leasing-services', NULL, NULL),
  (173, 27, 'Garden Services / Landscaping', 1, 2, '27/173/', 'garden-services-landscaping', NULL, NULL),
  (174, 27, 'Handyman / Maintenance', 1, 2, '27/174/', 'handyman-maintenance', NULL, NULL),
  (175, 27, 'Kitchen Fitter', 1, 2, '27/175/', 'kitchen-fitter', NULL, NULL),
  (176, 27, 'Locksmith', 1, 2, '27/176/', 'locksmith', NULL, NULL),
  (177, 27, 'Other Handicrafts', 1, 2, '27/177/', 'other-handicrafts', NULL, NULL),
  (178, 27, 'Painter / Decorator', 1, 2, '27/178/', 'painter-decorator', NULL, NULL),
  (179, 27, 'Plasterer', 1, 2, '27/179/', 'plasterer', NULL, NULL),
  (180, 27, 'Plumber', 1, 2, '27/180/', 'plumber', NULL, NULL),
  (181, 27, 'Roofing Contractor', 1, 2, '27/181/', 'roofing-contractor', NULL, NULL),
  (182, 27, 'Tiler', 1, 2, '27/182/', 'tiler', NULL, NULL),
  (183, 27, 'Ventilation / Air Conditioning', 1, 2, '27/183/', 'ventilation-air-conditioning', NULL, NULL),
  (184, 27, 'Renewable Energy', 1, 2, '27/184/', 'renewable-energy', NULL, NULL),
  (185, 28, 'Accounting', 1, 2, '28/185/', 'accounting', NULL, NULL),
  (186, 28, 'Art Gallery', 1, 2, '28/186/', 'art-gallery', NULL, NULL),
  (187, 28, 'Artist / Painter / Sculptor', 1, 2, '28/187/', 'artist-painter-sculptor', NULL, NULL),
  (188, 28, 'Auctioneering', 1, 2, '28/188/', 'auctioneering', NULL, NULL),
  (189, 28, 'Business Services / Consulting', 1, 2, '28/189/', 'business-services-consulting', NULL, NULL),
  (190, 28, 'Cleaning', 1, 2, '28/190/', 'cleaning', NULL, NULL),
  (191, 28, 'Consulting Services', 1, 2, '28/191/', 'consulting-services', NULL, NULL),
  (192, 28, 'Courier Services', 1, 2, '28/192/', 'courier-services', NULL, NULL),
  (193, 28, 'Events and Exhibitions Services', 1, 2, '28/193/', 'events-and-exhibitions-services', NULL, NULL),
  (194, 28, 'Financial Services', 1, 2, '28/194/', 'financial-services', NULL, NULL),
  (195, 28, 'Flower Shop', 1, 2, '28/195/', 'flower-shop', NULL, NULL),
  (196, 28, 'Hairdresser / Barber', 1, 2, '28/196/', 'hairdresser-barber-1', NULL, NULL),
  (197, 28, 'Hardware shop', 1, 2, '28/197/', 'hardware-shop', NULL, NULL),
  (198, 28, 'Importers / Exporters', 1, 2, '28/198/', 'importers-exporters', NULL, NULL),
  (199, 28, 'Insurance', 1, 2, '28/199/', 'insurance', NULL, NULL),
  (200, 28, 'Interior Designer', 1, 2, '28/200/', 'interior-designer', NULL, NULL),
  (201, 28, 'Investigative Services', 1, 2, '28/201/', 'investigative-services', NULL, NULL),
  (202, 28, 'Jewellery Designer', 1, 2, '28/202/', 'jewellery-designer', NULL, NULL),
  (203, 28, 'Kennels', 1, 2, '28/203/', 'kennels', NULL, NULL),
  (204, 28, 'Non Profit / Charity', 1, 2, '28/204/', 'non-profit-charity', NULL, NULL),
  (205, 28, 'Packaging / Printing Services', 1, 2, '28/205/', 'packaging-printing-services', NULL, NULL),
  (206, 28, 'Pet shop', 1, 2, '28/206/', 'pet-shop', NULL, NULL),
  (207, 28, 'Personal Assistant Services', 1, 2, '28/207/', 'personal-assistant-services', NULL, NULL),
  (208, 28, 'Real Estate Agent', 1, 2, '28/208/', 'real-estate-agent-2', NULL, NULL),
  (209, 28, 'Recruitment services', 1, 2, '28/209/', 'recruitment-services', NULL, NULL),
  (210, 28, 'Security Services', 1, 2, '28/210/', 'security-services', NULL, NULL),
  (211, 28, 'Veterinary Services', 1, 2, '28/211/', 'veterinary-services-1', NULL, NULL),
  (212, 28, 'Wedding / Party Services', 1, 2, '28/212/', 'wedding-party-services', NULL, NULL),
  (213, 31, 'Debt Recovery Services', 1, 2, '31/213/', 'debt-recovery-services', NULL, NULL),
  (214, 31, 'Accounting / Book-keeping / Tax Services', 1, 2, '31/214/', 'accounting-book-keeping-tax-services', NULL, NULL),
  (215, 31, 'Financial Advise / consulting', 1, 2, '31/215/', 'financial-advise-consulting', NULL, NULL),
  (216, 31, 'Insurance Services', 1, 2, '31/216/', 'insurance-services', NULL, NULL),
  (217, 31, 'Legal Services', 1, 2, '31/217/', 'legal-services-1', NULL, NULL),
  (218, 31, 'Money Lending Services', 1, 2, '31/218/', 'money-lending-services', NULL, NULL),
  (219, 31, 'Solicitors'' Services', 1, 2, '31/219/', 'solicitors-services', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `SyndicateContent`
--

CREATE TABLE IF NOT EXISTS `SyndicateContent` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `syndicate_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoGallery_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SyndicateModule`
--

CREATE TABLE IF NOT EXISTS `SyndicateModule` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `moduleClass` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `menu` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuSlug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `isHome` tinyint(1) NOT NULL,
  `isSingle` tinyint(1) NOT NULL,
  `slug` varchar(128) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `SyndicateModule`
--

INSERT INTO `SyndicateModule` (`id`, `name`, `status`, `moduleClass`, `menu`, `menuSlug`, `description`, `isHome`, `isSingle`, `slug`) VALUES
  (1, 'Event Calender', 1, 'EventCalender', 'Event Calender', 'event-calender', NULL, 0, 0, 'event-calender'),
  (2, 'Class Routine', 1, 'ClassRoutine', 'Class Routine', 'class-routine', 'Class Routine', 0, 1, 'class-routine'),
  (3, 'Admission', 1, 'Admission', 'Admission', 'admission', 'Admission', 1, 0, 'admission'),
  (4, 'Product', 1, 'Product', 'product', 'product', 'product', 0, 0, 'product'),
  (5, 'Category Grouping', 1, 'CategoryGrouping', 'Category Grouping', 'category-grouping', 'CategoryGrouping', 0, 0, 'category-grouping'),
  (6, 'Collection', 1, 'Collection', 'Collections', 'collection', 'Collection...', 0, 0, 'collection'),
  (7, 'Add Cart', 1, 'Cart', 'Cart', 'cart', NULL, 0, 0, 'cart');

-- --------------------------------------------------------

--
-- Table structure for table `syndicatemodule_syndicate`
--

CREATE TABLE IF NOT EXISTS `syndicatemodule_syndicate` (
  `syndicatemodule_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `TemplateCustomize`
--

CREATE TABLE IF NOT EXISTS `TemplateCustomize` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logoDisplayWebsite` tinyint(1) NOT NULL,
  `siteBgColor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bgImage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteFontFamily` longtext COLLATE utf8_unicode_ci,
  `siteFontSize` smallint(6) DEFAULT NULL,
  `anchorColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `anchorHoverColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buttonBgColor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `buttonBgColorHover` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteH1TextSize` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteH2TextSize` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteH3TextSize` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteH4TextSize` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headerBgColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headerBgImage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuBgColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuLiAColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuLiHoverAColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuFontSize` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bodyColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteTitleBgColor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subPageBgColor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `footerBgColor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `footerTextColor` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `globalOption_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `TemplateCustomize`
--

INSERT INTO `TemplateCustomize` (`id`, `logo`, `logoDisplayWebsite`, `siteBgColor`, `bgImage`, `siteFontFamily`, `siteFontSize`, `anchorColor`, `anchorHoverColor`, `buttonBgColor`, `buttonBgColorHover`, `siteH1TextSize`, `siteH2TextSize`, `siteH3TextSize`, `siteH4TextSize`, `headerBgColor`, `headerBgImage`, `menuBgColor`, `menuLiAColor`, `menuLiHoverAColor`, `menuFontSize`, `bodyColor`, `siteTitleBgColor`, `subPageBgColor`, `footerBgColor`, `footerTextColor`, `globalOption_id`) VALUES
  (2, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8),
  (3, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9),
  (4, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10),
  (5, '5687b03fc4d60.apple-icon-144x144px.png', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 11);

-- --------------------------------------------------------

--
-- Table structure for table `Templating`
--

CREATE TABLE IF NOT EXISTS `Templating` (
  `id` int(11) NOT NULL,
  `backgroundColor` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `headerColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `footerColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bodyColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `menuColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `menuLiColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `menuLiHoverColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `siteFontSize` smallint(6) NOT NULL,
  `siteFontFamily` longtext COLLATE utf8_unicode_ci NOT NULL,
  `anchorColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `anchorHoverColor` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Testimonial`
--

CREATE TABLE IF NOT EXISTS `Testimonial` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL,
  `isFeature` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Theme`
--

CREATE TABLE IF NOT EXISTS `Theme` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folderName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Theme`
--

INSERT INTO `Theme` (`id`, `name`, `folderName`, `status`, `path`) VALUES
  (1, 'Default', 'Default', 1, 'theme1.png');

-- --------------------------------------------------------

--
-- Table structure for table `theme_syndicate`
--

CREATE TABLE IF NOT EXISTS `theme_syndicate` (
  `theme_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Thread`
--

CREATE TABLE IF NOT EXISTS `Thread` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permalink` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_commentable` tinyint(1) NOT NULL,
  `num_comments` int(11) NOT NULL,
  `last_comment_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Timeline`
--

CREATE TABLE IF NOT EXISTS `Timeline` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Transaction`
--

CREATE TABLE IF NOT EXISTS `Transaction` (
  `id` int(11) NOT NULL,
  `process` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `totalAmount` decimal(10,0) NOT NULL,
  `created` datetime NOT NULL,
  `createdBy` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updatedBy` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tutor`
--

CREATE TABLE IF NOT EXISTS `Tutor` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `name` longtext COLLATE utf8_unicode_ci,
  `dateOfBirth` date DEFAULT NULL,
  `bloodGroup` longtext COLLATE utf8_unicode_ci,
  `gender` longtext COLLATE utf8_unicode_ci,
  `nationality` longtext COLLATE utf8_unicode_ci,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `job` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skypeId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebookId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitterId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `blogUrl` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permanentAddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `presentAddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currentPosition` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tutorial`
--

CREATE TABLE IF NOT EXISTS `Tutorial` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `facebookPage` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tutor_syndicate`
--

CREATE TABLE IF NOT EXISTS `tutor_syndicate` (
  `tutor_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `description` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `thana_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `designation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profession` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about` longtext COLLATE utf8_unicode_ci,
  `address` longtext COLLATE utf8_unicode_ci,
  `permanentAddress` longtext COLLATE utf8_unicode_ci,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `additionalPhone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` datetime DEFAULT NULL,
  `bloodGroup` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interest` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `joiningDate` datetime DEFAULT NULL,
  `leaveDate` datetime DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domainUser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `district_id`, `thana_id`, `name`, `mobile`, `email`, `designation`, `profession`, `about`, `address`, `permanentAddress`, `postalCode`, `additionalPhone`, `nid`, `dob`, `bloodGroup`, `interest`, `joiningDate`, `leaveDate`, `path`, `domainUser_id`) VALUES
  (1, 1, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (2, 2, NULL, NULL, 'Sayed Opu', '(01828) 148-248', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (3, 4, NULL, NULL, 'Shoshi', '(01827) 264-133', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (4, 6, NULL, NULL, 'Shoshi', '(01827) 164-144', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (5, 8, NULL, NULL, 'Shoshi', '(01827) 164-145', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (6, 9, NULL, NULL, 'Shoshi', '(01827) 164-146', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (7, 10, NULL, NULL, 'Shoshi', '(01827) 164-101', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (8, 11, NULL, NULL, 'Shoshi', '(01827) 164-102', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (9, 13, NULL, NULL, 'Shoshi', '(01828) 148-184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (10, 16, NULL, NULL, 'Tipu', '(01828) 148-148', NULL, NULL, NULL, NULL, 'axz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
  (11, 17, NULL, NULL, 'Sayed Opu', '(01552) 496-139', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_user_group`
--

CREATE TABLE IF NOT EXISTS `user_user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Vendor`
--

CREATE TABLE IF NOT EXISTS `Vendor` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `establishment` date DEFAULT NULL,
  `registrationNo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hotline` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `skypeId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `endHour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weeklyOffDay` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPerson` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postalCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contactPersonDesignation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `overview` longtext COLLATE utf8_unicode_ci,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `WareHouse`
--

CREATE TABLE IF NOT EXISTS `WareHouse` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `isWareHouse` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `inventoryConfig_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `WareHouse`
--

INSERT INTO `WareHouse` (`id`, `name`, `status`, `parent`, `isWareHouse`, `created`, `updated`, `inventoryConfig_id`) VALUES
  (1, 'Shop', 1, NULL, 1, '0000-00-00 00:00:00', '2016-01-10 00:01:35', NULL),
  (2, 'Web store', 1, NULL, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
  (3, 'Web', 1, NULL, 1, '2016-01-10 00:04:00', '2016-01-10 00:04:00', 2),
  (5, 'Top right', 1, 3, 0, '2016-01-10 00:11:04', '2016-01-10 00:11:04', 2);

-- --------------------------------------------------------

--
-- Table structure for table `WebTheme`
--

CREATE TABLE IF NOT EXISTS `WebTheme` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `folderName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `webtheme_syndicate`
--

CREATE TABLE IF NOT EXISTS `webtheme_syndicate` (
  `webtheme_id` int(11) NOT NULL,
  `syndicate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Academic`
--
ALTER TABLE `Academic`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_B922F95E208F64F1` (`tutor_id`);

--
-- Indexes for table `AcademicMeta`
--
ALTER TABLE `AcademicMeta`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_F308C6CD208F64F1` (`tutor_id`);

--
-- Indexes for table `Admission`
--
ALTER TABLE `Admission`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_764A80E9989D9B62` (`slug`),
ADD KEY `IDX_764A80E9A76ED395` (`user_id`),
ADD KEY `IDX_764A80E9DA4CCD60` (`courseLevel_id`),
ADD KEY `IDX_764A80E9591CC992` (`course_id`),
ADD KEY `IDX_764A80E9AB61B33C` (`createUser_id`);

--
-- Indexes for table `AdmissionComment`
--
ALTER TABLE `AdmissionComment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_8F56234475C9C554` (`admission_id`);

--
-- Indexes for table `admission_branch`
--
ALTER TABLE `admission_branch`
ADD PRIMARY KEY (`admission_id`,`branch_id`),
ADD KEY `IDX_8115CF5575C9C554` (`admission_id`),
ADD KEY `IDX_8115CF55DCD6CC49` (`branch_id`);

--
-- Indexes for table `AdsTool`
--
ALTER TABLE `AdsTool`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_F3823813DC938C82` (`globalOption_id`);

--
-- Indexes for table `Advertisment`
--
ALTER TABLE `Advertisment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_91209A3FDC938C82` (`globalOption_id`);

--
-- Indexes for table `Apartment`
--
ALTER TABLE `Apartment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_CF8FEAF7A76ED395` (`user_id`);

--
-- Indexes for table `AppModule`
--
ALTER TABLE `AppModule`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_D592C0EC989D9B62` (`slug`);

--
-- Indexes for table `Bank`
--
ALTER TABLE `Bank`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Blackout`
--
ALTER TABLE `Blackout`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_F53A2983A76ED395` (`user_id`);

--
-- Indexes for table `Blog`
--
ALTER TABLE `Blog`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_6027FE7DA76ED395` (`user_id`),
ADD KEY `IDX_6027FE7DBD85B63` (`photoGallery_id`);

--
-- Indexes for table `BlogComment`
--
ALTER TABLE `BlogComment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_B129A665DAE07E97` (`blog_id`);

--
-- Indexes for table `Branch`
--
ALTER TABLE `Branch`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_BC2A1E29A76ED395` (`user_id`);

--
-- Indexes for table `Branding`
--
ALTER TABLE `Branding`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branding_category`
--
ALTER TABLE `branding_category`
ADD PRIMARY KEY (`branding_id`,`category_id`),
ADD KEY `IDX_EDEE8C5560BC00E` (`branding_id`),
ADD KEY `IDX_EDEE8C512469DE2` (`category_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_F0FE25278D9F6D38` (`order_id`),
ADD KEY `IDX_F0FE25274584665A` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_3AF34668989D9B62` (`slug`),
ADD KEY `IDX_3AF346683D8E604F` (`parent`),
ADD KEY `IDX_3AF346683CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `CategoryGrouping`
--
ALTER TABLE `CategoryGrouping`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_55233F30A76ED395` (`user_id`);

--
-- Indexes for table `categorygrouping_category`
--
ALTER TABLE `categorygrouping_category`
ADD PRIMARY KEY (`categorygrouping_id`,`category_id`),
ADD KEY `IDX_66CB1BC1188B8278` (`categorygrouping_id`),
ADD KEY `IDX_66CB1BC112469DE2` (`category_id`);

--
-- Indexes for table `ClassRoutine`
--
ALTER TABLE `ClassRoutine`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Collection`
--
ALTER TABLE `Collection`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_B31066E23D8E604F` (`parent`);

--
-- Indexes for table `ColorSize`
--
ALTER TABLE `ColorSize`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_48CDE5823CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `Comment`
--
ALTER TABLE `Comment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_5BC96BF0E2904019` (`thread_id`),
ADD KEY `IDX_5BC96BF0F675F31B` (`author_id`);

--
-- Indexes for table `ContactMessage`
--
ALTER TABLE `ContactMessage`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_DDC5A139A76ED395` (`user_id`);

--
-- Indexes for table `ContactPage`
--
ALTER TABLE `ContactPage`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_82F5EE0BA76ED395` (`user_id`),
ADD UNIQUE KEY `UNIQ_82F5EE0BDC938C82` (`globalOption_id`),
ADD KEY `IDX_82F5EE0BA51DB16` (`thana_id`),
ADD KEY `IDX_82F5EE0BB08FA272` (`district_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Course`
--
ALTER TABLE `Course`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_11326A8F989D9B62` (`slug`);

--
-- Indexes for table `CourseLevel`
--
ALTER TABLE `CourseLevel`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_4830C1DD989D9B62` (`slug`);

--
-- Indexes for table `course_courselevel`
--
ALTER TABLE `course_courselevel`
ADD PRIMARY KEY (`course_id`,`courselevel_id`),
ADD KEY `IDX_7B0351DA591CC992` (`course_id`),
ADD KEY `IDX_7B0351DA233AAF36` (`courselevel_id`);

--
-- Indexes for table `Customer`
--
ALTER TABLE `Customer`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_784FEC5FB08FA272` (`district_id`),
ADD KEY `IDX_784FEC5FA51DB16` (`thana_id`),
ADD KEY `IDX_784FEC5FDC938C82` (`globalOption_id`);

--
-- Indexes for table `DomainUser`
--
ALTER TABLE `DomainUser`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_94324237A76ED395` (`user_id`),
ADD KEY `IDX_94324237DC938C82` (`globalOption_id`);

--
-- Indexes for table `Education`
--
ALTER TABLE `Education`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_59FBDC71A76ED395` (`user_id`),
ADD KEY `IDX_59FBDC7164D218E` (`location_id`);

--
-- Indexes for table `education_courselevel`
--
ALTER TABLE `education_courselevel`
ADD PRIMARY KEY (`education_id`,`courselevel_id`),
ADD KEY `IDX_4AEFC18C2CA1BD71` (`education_id`),
ADD KEY `IDX_4AEFC18C233AAF36` (`courselevel_id`);

--
-- Indexes for table `education_institutelevel`
--
ALTER TABLE `education_institutelevel`
ADD PRIMARY KEY (`education_id`,`institutelevel_id`),
ADD KEY `IDX_2DD14F332CA1BD71` (`education_id`),
ADD KEY `IDX_2DD14F33D4073482` (`institutelevel_id`);

--
-- Indexes for table `EmailBox`
--
ALTER TABLE `EmailBox`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_7E16875A76ED395` (`user_id`);

--
-- Indexes for table `Event`
--
ALTER TABLE `Event`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_FA6F25A3A76ED395` (`user_id`),
ADD KEY `IDX_FA6F25A3BD85B63` (`photoGallery_id`);

--
-- Indexes for table `EventCalender`
--
ALTER TABLE `EventCalender`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Faq`
--
ALTER TABLE `Faq`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_D0B2332C989D9B62` (`slug`),
ADD KEY `IDX_D0B2332CA76ED395` (`user_id`);

--
-- Indexes for table `FooterSetting`
--
ALTER TABLE `FooterSetting`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_4170B0EDDC938C82` (`globalOption_id`);

--
-- Indexes for table `fos_user`
--
ALTER TABLE `fos_user`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
ADD UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`);

--
-- Indexes for table `GalleryImage`
--
ALTER TABLE `GalleryImage`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_D2F7A834BD85B63` (`photoGallery_id`);

--
-- Indexes for table `GlobalOption`
--
ALTER TABLE `GlobalOption`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_8B2648A1989D9B62` (`slug`),
ADD UNIQUE KEY `UNIQ_8B2648A13C7323E0` (`mobile`),
ADD UNIQUE KEY `UNIQ_8B2648A1E7927C74` (`email`),
ADD UNIQUE KEY `UNIQ_8B2648A1A7A91E0B` (`domain`),
ADD UNIQUE KEY `UNIQ_8B2648A1C6799318` (`subDomain`),
ADD UNIQUE KEY `UNIQ_8B2648A1A76ED395` (`user_id`),
ADD KEY `IDX_8B2648A164D218E` (`location_id`),
ADD KEY `IDX_8B2648A14C37717D` (`syndicate_id`);

--
-- Indexes for table `HomeBlock`
--
ALTER TABLE `HomeBlock`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_BC28FB51CACCD446` (`homePage_id`),
ADD KEY `IDX_BC28FB51CCD7E912` (`menu_id`);

--
-- Indexes for table `HomePage`
--
ALTER TABLE `HomePage`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_92601886A76ED395` (`user_id`),
ADD UNIQUE KEY `UNIQ_92601886DC938C82` (`globalOption_id`),
ADD KEY `IDX_92601886BD85B63` (`photoGallery_id`);

--
-- Indexes for table `homepage_module`
--
ALTER TABLE `homepage_module`
ADD PRIMARY KEY (`homePage_id`,`module_id`),
ADD KEY `IDX_717671BECACCD446` (`homePage_id`),
ADD KEY `IDX_717671BEAFC2B591` (`module_id`);

--
-- Indexes for table `homepage_syndicate`
--
ALTER TABLE `homepage_syndicate`
ADD PRIMARY KEY (`homePage_id`,`syndicate_id`),
ADD KEY `IDX_DFFAE166CACCD446` (`homePage_id`),
ADD KEY `IDX_DFFAE1664C37717D` (`syndicate_id`);

--
-- Indexes for table `homepage_syndicatemodule`
--
ALTER TABLE `homepage_syndicatemodule`
ADD PRIMARY KEY (`homePage_id`,`syndicate_module_id`),
ADD KEY `IDX_A833A0CECACCD446` (`homePage_id`),
ADD KEY `IDX_A833A0CEFFC40B63` (`syndicate_module_id`);

--
-- Indexes for table `HomeSlider`
--
ALTER TABLE `HomeSlider`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_6F403EDA76ED395` (`user_id`),
ADD KEY `IDX_6F403EDC4663E4` (`page_id`);

--
-- Indexes for table `instituteLevel`
--
ALTER TABLE `instituteLevel`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_A310F179989D9B62` (`slug`),
ADD KEY `IDX_A310F1793D8E604F` (`parent`);

--
-- Indexes for table `InventoryConfig`
--
ALTER TABLE `InventoryConfig`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_D475348DC938C82` (`globalOption_id`);

--
-- Indexes for table `Item`
--
ALTER TABLE `Item`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_BF298A20F9038C4` (`sku`),
ADD KEY `IDX_BF298A203CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_BF298A20F92F3E70` (`country_id`),
ADD KEY `IDX_BF298A2044F5D008` (`brand_id`),
ADD KEY `IDX_BF298A20F603EE73` (`vendor_id`),
ADD KEY `IDX_BF298A207ADA1FB5` (`color_id`),
ADD KEY `IDX_BF298A20498DA827` (`size_id`),
ADD KEY `IDX_BF298A20D5012B6F` (`masterItem_id`);

--
-- Indexes for table `ItemColor`
--
ALTER TABLE `ItemColor`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_B073D1AE3CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `ItemGallery`
--
ALTER TABLE `ItemGallery`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_71C05C3D126F525E` (`item_id`);

--
-- Indexes for table `ItemInventory`
--
ALTER TABLE `ItemInventory`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_C26F0ECB126F525E` (`item_id`);

--
-- Indexes for table `ItemSize`
--
ALTER TABLE `ItemSize`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_4FA8BCA3CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `ItemTypeGrouping`
--
ALTER TABLE `ItemTypeGrouping`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_1C3E218B3CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `itemtypegrouping_category`
--
ALTER TABLE `itemtypegrouping_category`
ADD PRIMARY KEY (`itemtypegrouping_id`,`category_id`),
ADD KEY `IDX_7C5DE5EAEE02FE58` (`itemtypegrouping_id`),
ADD KEY `IDX_7C5DE5EA12469DE2` (`category_id`);

--
-- Indexes for table `item_category`
--
ALTER TABLE `item_category`
ADD PRIMARY KEY (`item_id`,`category_id`),
ADD KEY `IDX_6A41D10A126F525E` (`item_id`),
ADD KEY `IDX_6A41D10A12469DE2` (`category_id`);

--
-- Indexes for table `item_master`
--
ALTER TABLE `item_master`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_ABF0D619989D9B62` (`slug`),
ADD KEY `IDX_ABF0D6193CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_ABF0D61912469DE2` (`category_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_17E64ABA3D8E604F` (`parent`);

--
-- Indexes for table `MegaMenu`
--
ALTER TABLE `MegaMenu`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `megamenu_advertisment`
--
ALTER TABLE `megamenu_advertisment`
ADD PRIMARY KEY (`megamenu_id`,`advertisment_id`),
ADD KEY `IDX_98A52DEAB2F1DFC3` (`megamenu_id`),
ADD KEY `IDX_98A52DEA71731BCA` (`advertisment_id`);

--
-- Indexes for table `megamenu_branding`
--
ALTER TABLE `megamenu_branding`
ADD PRIMARY KEY (`megamenu_id`,`branding_id`),
ADD KEY `IDX_B23FFCD5B2F1DFC3` (`megamenu_id`),
ADD KEY `IDX_B23FFCD5560BC00E` (`branding_id`);

--
-- Indexes for table `megamenu_category`
--
ALTER TABLE `megamenu_category`
ADD PRIMARY KEY (`megamenu_id`,`category_id`),
ADD KEY `IDX_6286B43B2F1DFC3` (`megamenu_id`),
ADD KEY `IDX_6286B4312469DE2` (`category_id`);

--
-- Indexes for table `megamenu_collection`
--
ALTER TABLE `megamenu_collection`
ADD PRIMARY KEY (`megamenu_id`,`collection_id`),
ADD KEY `IDX_4BF38F8FB2F1DFC3` (`megamenu_id`),
ADD KEY `IDX_4BF38F8F514956FD` (`collection_id`);

--
-- Indexes for table `megamenu_globaloption`
--
ALTER TABLE `megamenu_globaloption`
ADD PRIMARY KEY (`megamenu_id`,`globaloption_id`),
ADD KEY `IDX_850FFA42B2F1DFC3` (`megamenu_id`),
ADD KEY `IDX_850FFA425E620E21` (`globaloption_id`);

--
-- Indexes for table `megamenu_syndicate`
--
ALTER TABLE `megamenu_syndicate`
ADD PRIMARY KEY (`megamenu_id`,`syndicate_id`),
ADD KEY `IDX_554D586CB2F1DFC3` (`megamenu_id`),
ADD KEY `IDX_554D586C4C37717D` (`syndicate_id`);

--
-- Indexes for table `Menu`
--
ALTER TABLE `Menu`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_DD3795ADC4663E4` (`page_id`),
ADD KEY `IDX_DD3795ADA76ED395` (`user_id`),
ADD KEY `IDX_DD3795ADAFC2B591` (`module_id`),
ADD KEY `IDX_DD3795AD3CBC22` (`syndicateModule_id`),
ADD KEY `IDX_DD3795AD4C37717D` (`syndicate_id`),
ADD KEY `IDX_DD3795AD8AFE69CA` (`siteSetting_id`);

--
-- Indexes for table `MenuGroup`
--
ALTER TABLE `MenuGroup`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `MenuGrouping`
--
ALTER TABLE `MenuGrouping`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_152EC2753D8E604F` (`parent`),
ADD KEY `IDX_152EC275A76ED395` (`user_id`),
ADD KEY `IDX_152EC275CCD7E912` (`menu_id`),
ADD KEY `IDX_152EC275FBB147D2` (`menuGroup_id`);

--
-- Indexes for table `MobileIcon`
--
ALTER TABLE `MobileIcon`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_4DF26A06DC938C82` (`globalOption_id`);

--
-- Indexes for table `MobileTheme`
--
ALTER TABLE `MobileTheme`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_6F3F2F225E237E06` (`name`);

--
-- Indexes for table `mobiletheme_syndicate`
--
ALTER TABLE `mobiletheme_syndicate`
ADD PRIMARY KEY (`mobiletheme_id`,`syndicate_id`),
ADD KEY `IDX_3399870BCDE0580F` (`mobiletheme_id`),
ADD KEY `IDX_3399870B4C37717D` (`syndicate_id`);

--
-- Indexes for table `Module`
--
ALTER TABLE `Module`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_B88231E5E237E06` (`name`),
ADD UNIQUE KEY `UNIQ_B88231ECB93038C` (`moduleClass`),
ADD UNIQUE KEY `UNIQ_B88231E989D9B62` (`slug`);

--
-- Indexes for table `News`
--
ALTER TABLE `News`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_BDE1366E989D9B62` (`slug`),
ADD KEY `IDX_BDE1366EA76ED395` (`user_id`);

--
-- Indexes for table `NoticeBoard`
--
ALTER TABLE `NoticeBoard`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_106CCE7EA76ED395` (`user_id`);

--
-- Indexes for table `page`
--
ALTER TABLE `page`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_140AB6203D8E604F` (`parent`),
ADD KEY `IDX_140AB620BD85B63` (`photoGallery_id`),
ADD KEY `IDX_140AB620A76ED395` (`user_id`);

--
-- Indexes for table `PaymentCard`
--
ALTER TABLE `PaymentCard`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `photo_gallery`
--
ALTER TABLE `photo_gallery`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_72CB6FB7A76ED395` (`user_id`);

--
-- Indexes for table `Product`
--
ALTER TABLE `Product`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_1CF73D31989D9B62` (`slug`),
ADD KEY `IDX_1CF73D31A76ED395` (`user_id`),
ADD KEY `IDX_1CF73D31B0D2661D` (`parentCategory_id`),
ADD KEY `IDX_1CF73D31514956FD` (`collection_id`),
ADD KEY `IDX_1CF73D3144F5D008` (`brand_id`);

--
-- Indexes for table `ProductCustomAttribute`
--
ALTER TABLE `ProductCustomAttribute`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_3CD340A24584665A` (`product_id`);

--
-- Indexes for table `ProductGallery`
--
ALTER TABLE `ProductGallery`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_DEBD9F594584665A` (`product_id`);

--
-- Indexes for table `ProductReview`
--
ALTER TABLE `ProductReview`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_E6F558C54584665A` (`product_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
ADD PRIMARY KEY (`product_id`,`category_id`),
ADD KEY `IDX_CDFC73564584665A` (`product_id`),
ADD KEY `IDX_CDFC735612469DE2` (`category_id`);

--
-- Indexes for table `product_import`
--
ALTER TABLE `product_import`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Purchase`
--
ALTER TABLE `Purchase`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_9861B36D3CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_9861B36DF603EE73` (`vendor_id`),
ADD KEY `IDX_9861B36D3174800F` (`createdBy_id`),
ADD KEY `IDX_9861B36DFACFC38A` (`approvedBy_id`);

--
-- Indexes for table `PurchaseItem`
--
ALTER TABLE `PurchaseItem`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_334FA427126F525E` (`item_id`),
ADD KEY `IDX_334FA427558FBEB9` (`purchase_id`),
ADD KEY `IDX_334FA4277F14552F` (`purchaseVendorItem_id`);

--
-- Indexes for table `PurchaseOrder`
--
ALTER TABLE `PurchaseOrder`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `PurchaseReturn`
--
ALTER TABLE `PurchaseReturn`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_33A52D573CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_33A52D573174800F` (`createdBy_id`),
ADD KEY `IDX_33A52D57F603EE73` (`vendor_id`);

--
-- Indexes for table `PurchaseReturnItem`
--
ALTER TABLE `PurchaseReturnItem`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_B5C64132A787C244` (`purchaseReturn_id`),
ADD KEY `IDX_B5C64132FF8E8E14` (`purchaseItem_id`);

--
-- Indexes for table `PurchaseVendorItem`
--
ALTER TABLE `PurchaseVendorItem`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_8AA73017558FBEB9` (`purchase_id`);

--
-- Indexes for table `ReturnPurchase`
--
ALTER TABLE `ReturnPurchase`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Sales`
--
ALTER TABLE `Sales`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_AA405F403CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_AA405F409395C3F3` (`customer_id`),
ADD KEY `IDX_AA405F40CB9492CE` (`salesBy_id`),
ADD KEY `IDX_AA405F4011C8FB41` (`bank_id`),
ADD KEY `IDX_AA405F40597ADFA9` (`paymentCard_id`);

--
-- Indexes for table `SalesItem`
--
ALTER TABLE `SalesItem`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_85A7BE63126F525E` (`item_id`),
ADD KEY `IDX_85A7BE63FF8E8E14` (`purchaseItem_id`),
ADD KEY `IDX_85A7BE63A4522A07` (`sales_id`);

--
-- Indexes for table `SalesReturn`
--
ALTER TABLE `SalesReturn`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_5AD2E8C43CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_5AD2E8C4A4522A07` (`sales_id`),
ADD KEY `IDX_5AD2E8C43174800F` (`createdBy_id`);

--
-- Indexes for table `SalesReturnItem`
--
ALTER TABLE `SalesReturnItem`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_D9476D1EC3F94932` (`salesReturn_id`),
ADD KEY `IDX_D9476D1E615DE49` (`salesItem_id`);

--
-- Indexes for table `Scholarship`
--
ALTER TABLE `Scholarship`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_89A359E8989D9B62` (`slug`),
ADD KEY `IDX_89A359E864D218E` (`location_id`);

--
-- Indexes for table `scholarship_syndicate`
--
ALTER TABLE `scholarship_syndicate`
ADD PRIMARY KEY (`scholarship_id`,`syndicate_id`),
ADD KEY `IDX_4F27AF3128722836` (`scholarship_id`),
ADD KEY `IDX_4F27AF314C37717D` (`syndicate_id`);

--
-- Indexes for table `SiteContent`
--
ALTER TABLE `SiteContent`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_3E79CF3E989D9B62` (`slug`),
ADD KEY `IDX_3E79CF3E3D8E604F` (`parent`);

--
-- Indexes for table `SiteSetting`
--
ALTER TABLE `SiteSetting`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_5FC8470FA76ED395` (`user_id`),
ADD UNIQUE KEY `UNIQ_5FC8470FDC938C82` (`globalOption_id`),
ADD KEY `IDX_5FC8470F3C935932` (`webTheme_id`),
ADD KEY `IDX_5FC8470F34963A59` (`mobileTheme_id`),
ADD KEY `IDX_5FC8470F59027487` (`theme_id`);

--
-- Indexes for table `sitesetting_appmodule`
--
ALTER TABLE `sitesetting_appmodule`
ADD PRIMARY KEY (`sitesetting_id`,`app_module_id`),
ADD KEY `IDX_931E01D9C5A36A1A` (`sitesetting_id`),
ADD KEY `IDX_931E01D97ADEAA4` (`app_module_id`);

--
-- Indexes for table `sitesetting_module`
--
ALTER TABLE `sitesetting_module`
ADD PRIMARY KEY (`sitesetting_id`,`module_id`),
ADD KEY `IDX_CB2BF790C5A36A1A` (`sitesetting_id`),
ADD KEY `IDX_CB2BF790AFC2B591` (`module_id`);

--
-- Indexes for table `sitesetting_syndicate`
--
ALTER TABLE `sitesetting_syndicate`
ADD PRIMARY KEY (`sitesetting_id`,`syndicate_id`),
ADD KEY `IDX_952A98B2C5A36A1A` (`sitesetting_id`),
ADD KEY `IDX_952A98B24C37717D` (`syndicate_id`);

--
-- Indexes for table `sitesetting_syndicatemodule`
--
ALTER TABLE `sitesetting_syndicatemodule`
ADD PRIMARY KEY (`sitesetting_id`,`syndicate_module_id`),
ADD KEY `IDX_8E07CDC6C5A36A1A` (`sitesetting_id`),
ADD KEY `IDX_8E07CDC6FFC40B63` (`syndicate_module_id`);

--
-- Indexes for table `SiteSlider`
--
ALTER TABLE `SiteSlider`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `StockItem`
--
ALTER TABLE `StockItem`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_6DDA4EF615DE49` (`salesItem_id`),
ADD KEY `IDX_6DDA4EF126F525E` (`item_id`),
ADD KEY `IDX_6DDA4EFF603EE73` (`vendor_id`),
ADD KEY `IDX_6DDA4EF3174800F` (`createdBy_id`),
ADD KEY `IDX_6DDA4EFF92F3E70` (`country_id`),
ADD KEY `IDX_6DDA4EF44F5D008` (`brand_id`),
ADD KEY `IDX_6DDA4EF3CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_6DDA4EF12469DE2` (`category_id`),
ADD KEY `IDX_6DDA4EFFF8E8E14` (`purchaseItem_id`);

--
-- Indexes for table `Student`
--
ALTER TABLE `Student`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `StudyAbroad`
--
ALTER TABLE `StudyAbroad`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_716E7441A76ED395` (`user_id`),
ADD KEY `IDX_716E744164D218E` (`location_id`);

--
-- Indexes for table `studyabroad_syndicate`
--
ALTER TABLE `studyabroad_syndicate`
ADD PRIMARY KEY (`studyabroad_id`,`syndicate_id`),
ADD KEY `IDX_C8D9B6FBB9D52860` (`studyabroad_id`),
ADD KEY `IDX_C8D9B6FB4C37717D` (`syndicate_id`);

--
-- Indexes for table `SubscribeEmail`
--
ALTER TABLE `SubscribeEmail`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_1E3168BE7927C74` (`email`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_9B2A6C7E3CA07BD1` (`inventoryConfig_id`),
ADD KEY `IDX_9B2A6C7EF92F3E70` (`country_id`);

--
-- Indexes for table `sylius_adjustment`
--
ALTER TABLE `sylius_adjustment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_ACA6E0F28D9F6D38` (`order_id`),
ADD KEY `IDX_ACA6E0F2E415FB15` (`order_item_id`);

--
-- Indexes for table `sylius_cart`
--
ALTER TABLE `sylius_cart`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_D0AA6D3E96901F54` (`number`);

--
-- Indexes for table `sylius_order_comment`
--
ALTER TABLE `sylius_order_comment`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_8EA9CF098D9F6D38` (`order_id`);

--
-- Indexes for table `sylius_order_identity`
--
ALTER TABLE `sylius_order_identity`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_5757A18E8D9F6D38` (`order_id`);

--
-- Indexes for table `syndicate`
--
ALTER TABLE `syndicate`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_56FBDE12989D9B62` (`slug`),
ADD KEY `IDX_56FBDE123D8E604F` (`parent`);

--
-- Indexes for table `SyndicateContent`
--
ALTER TABLE `SyndicateContent`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_2FB0B3C2A76ED395` (`user_id`),
ADD KEY `IDX_2FB0B3C24C37717D` (`syndicate_id`),
ADD KEY `IDX_2FB0B3C2BD85B63` (`photoGallery_id`);

--
-- Indexes for table `SyndicateModule`
--
ALTER TABLE `SyndicateModule`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_C2C0BC8CCB93038C` (`moduleClass`),
ADD UNIQUE KEY `UNIQ_C2C0BC8C989D9B62` (`slug`);

--
-- Indexes for table `syndicatemodule_syndicate`
--
ALTER TABLE `syndicatemodule_syndicate`
ADD PRIMARY KEY (`syndicatemodule_id`,`syndicate_id`),
ADD KEY `IDX_CF3B593D82CD3E81` (`syndicatemodule_id`),
ADD KEY `IDX_CF3B593D4C37717D` (`syndicate_id`);

--
-- Indexes for table `TemplateCustomize`
--
ALTER TABLE `TemplateCustomize`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_8A804D56DC938C82` (`globalOption_id`);

--
-- Indexes for table `Templating`
--
ALTER TABLE `Templating`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Testimonial`
--
ALTER TABLE `Testimonial`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_60214220A76ED395` (`user_id`);

--
-- Indexes for table `Theme`
--
ALTER TABLE `Theme`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theme_syndicate`
--
ALTER TABLE `theme_syndicate`
ADD PRIMARY KEY (`theme_id`,`syndicate_id`),
ADD KEY `IDX_9838049F59027487` (`theme_id`),
ADD KEY `IDX_9838049F4C37717D` (`syndicate_id`);

--
-- Indexes for table `Thread`
--
ALTER TABLE `Thread`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Timeline`
--
ALTER TABLE `Timeline`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Transaction`
--
ALTER TABLE `Transaction`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_F4AB8A063CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `Tutor`
--
ALTER TABLE `Tutor`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_58C6694CA76ED395` (`user_id`),
ADD KEY `IDX_58C6694C64D218E` (`location_id`);

--
-- Indexes for table `Tutorial`
--
ALTER TABLE `Tutorial`
ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tutor_syndicate`
--
ALTER TABLE `tutor_syndicate`
ADD PRIMARY KEY (`tutor_id`,`syndicate_id`),
ADD KEY `IDX_4B177820208F64F1` (`tutor_id`),
ADD KEY `IDX_4B1778204C37717D` (`syndicate_id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_8F02BF9D5E237E06` (`name`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_6BBD61303C7323E0` (`mobile`),
ADD UNIQUE KEY `UNIQ_6BBD613043E84A97` (`nid`),
ADD UNIQUE KEY `UNIQ_6BBD6130A76ED395` (`user_id`),
ADD UNIQUE KEY `UNIQ_6BBD6130FE830143` (`domainUser_id`),
ADD KEY `IDX_6BBD6130B08FA272` (`district_id`),
ADD KEY `IDX_6BBD6130A51DB16` (`thana_id`);

--
-- Indexes for table `user_user_group`
--
ALTER TABLE `user_user_group`
ADD PRIMARY KEY (`user_id`,`group_id`),
ADD KEY `IDX_28657971A76ED395` (`user_id`),
ADD KEY `IDX_28657971FE54D947` (`group_id`);

--
-- Indexes for table `Vendor`
--
ALTER TABLE `Vendor`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_F28E36C0A76ED395` (`user_id`),
ADD KEY `IDX_F28E36C064D218E` (`location_id`);

--
-- Indexes for table `WareHouse`
--
ALTER TABLE `WareHouse`
ADD PRIMARY KEY (`id`),
ADD KEY `IDX_AF83265B3D8E604F` (`parent`),
ADD KEY `IDX_AF83265B3CA07BD1` (`inventoryConfig_id`);

--
-- Indexes for table `WebTheme`
--
ALTER TABLE `WebTheme`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `UNIQ_36E5D7B45E237E06` (`name`);

--
-- Indexes for table `webtheme_syndicate`
--
ALTER TABLE `webtheme_syndicate`
ADD PRIMARY KEY (`webtheme_id`,`syndicate_id`),
ADD KEY `IDX_391C457FC5E53B64` (`webtheme_id`),
ADD KEY `IDX_391C457F4C37717D` (`syndicate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Academic`
--
ALTER TABLE `Academic`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `AcademicMeta`
--
ALTER TABLE `AcademicMeta`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Admission`
--
ALTER TABLE `Admission`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `AdmissionComment`
--
ALTER TABLE `AdmissionComment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `AdsTool`
--
ALTER TABLE `AdsTool`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Advertisment`
--
ALTER TABLE `Advertisment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Apartment`
--
ALTER TABLE `Apartment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `AppModule`
--
ALTER TABLE `AppModule`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `Bank`
--
ALTER TABLE `Bank`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Blackout`
--
ALTER TABLE `Blackout`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Blog`
--
ALTER TABLE `Blog`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `BlogComment`
--
ALTER TABLE `BlogComment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Branch`
--
ALTER TABLE `Branch`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Branding`
--
ALTER TABLE `Branding`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=669;
--
-- AUTO_INCREMENT for table `CategoryGrouping`
--
ALTER TABLE `CategoryGrouping`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ClassRoutine`
--
ALTER TABLE `ClassRoutine`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Collection`
--
ALTER TABLE `Collection`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `ColorSize`
--
ALTER TABLE `ColorSize`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=124;
--
-- AUTO_INCREMENT for table `Comment`
--
ALTER TABLE `Comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ContactMessage`
--
ALTER TABLE `ContactMessage`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ContactPage`
--
ALTER TABLE `ContactPage`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=250;
--
-- AUTO_INCREMENT for table `Course`
--
ALTER TABLE `Course`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `CourseLevel`
--
ALTER TABLE `CourseLevel`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `Customer`
--
ALTER TABLE `Customer`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `DomainUser`
--
ALTER TABLE `DomainUser`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Education`
--
ALTER TABLE `Education`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `EmailBox`
--
ALTER TABLE `EmailBox`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Event`
--
ALTER TABLE `Event`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `EventCalender`
--
ALTER TABLE `EventCalender`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Faq`
--
ALTER TABLE `Faq`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `FooterSetting`
--
ALTER TABLE `FooterSetting`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `fos_user`
--
ALTER TABLE `fos_user`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `GalleryImage`
--
ALTER TABLE `GalleryImage`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `GlobalOption`
--
ALTER TABLE `GlobalOption`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `HomeBlock`
--
ALTER TABLE `HomeBlock`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `HomePage`
--
ALTER TABLE `HomePage`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `HomeSlider`
--
ALTER TABLE `HomeSlider`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `instituteLevel`
--
ALTER TABLE `instituteLevel`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `InventoryConfig`
--
ALTER TABLE `InventoryConfig`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `Item`
--
ALTER TABLE `Item`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ItemColor`
--
ALTER TABLE `ItemColor`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ItemGallery`
--
ALTER TABLE `ItemGallery`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ItemInventory`
--
ALTER TABLE `ItemInventory`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ItemSize`
--
ALTER TABLE `ItemSize`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ItemTypeGrouping`
--
ALTER TABLE `ItemTypeGrouping`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `item_master`
--
ALTER TABLE `item_master`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=348;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1468;
--
-- AUTO_INCREMENT for table `MegaMenu`
--
ALTER TABLE `MegaMenu`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Menu`
--
ALTER TABLE `Menu`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `MenuGroup`
--
ALTER TABLE `MenuGroup`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `MenuGrouping`
--
ALTER TABLE `MenuGrouping`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `MobileIcon`
--
ALTER TABLE `MobileIcon`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `MobileTheme`
--
ALTER TABLE `MobileTheme`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `Module`
--
ALTER TABLE `Module`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `News`
--
ALTER TABLE `News`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `NoticeBoard`
--
ALTER TABLE `NoticeBoard`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `PaymentCard`
--
ALTER TABLE `PaymentCard`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `photo_gallery`
--
ALTER TABLE `photo_gallery`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Product`
--
ALTER TABLE `Product`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ProductCustomAttribute`
--
ALTER TABLE `ProductCustomAttribute`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ProductGallery`
--
ALTER TABLE `ProductGallery`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ProductReview`
--
ALTER TABLE `ProductReview`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `product_import`
--
ALTER TABLE `product_import`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1630;
--
-- AUTO_INCREMENT for table `Purchase`
--
ALTER TABLE `Purchase`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `PurchaseItem`
--
ALTER TABLE `PurchaseItem`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `PurchaseOrder`
--
ALTER TABLE `PurchaseOrder`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `PurchaseReturn`
--
ALTER TABLE `PurchaseReturn`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `PurchaseReturnItem`
--
ALTER TABLE `PurchaseReturnItem`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `PurchaseVendorItem`
--
ALTER TABLE `PurchaseVendorItem`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `ReturnPurchase`
--
ALTER TABLE `ReturnPurchase`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Sales`
--
ALTER TABLE `Sales`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `SalesItem`
--
ALTER TABLE `SalesItem`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `SalesReturn`
--
ALTER TABLE `SalesReturn`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SalesReturnItem`
--
ALTER TABLE `SalesReturnItem`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Scholarship`
--
ALTER TABLE `Scholarship`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SiteContent`
--
ALTER TABLE `SiteContent`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SiteSetting`
--
ALTER TABLE `SiteSetting`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `SiteSlider`
--
ALTER TABLE `SiteSlider`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `StockItem`
--
ALTER TABLE `StockItem`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `Student`
--
ALTER TABLE `Student`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `StudyAbroad`
--
ALTER TABLE `StudyAbroad`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SubscribeEmail`
--
ALTER TABLE `SubscribeEmail`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `sylius_adjustment`
--
ALTER TABLE `sylius_adjustment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sylius_cart`
--
ALTER TABLE `sylius_cart`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sylius_order_comment`
--
ALTER TABLE `sylius_order_comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sylius_order_identity`
--
ALTER TABLE `sylius_order_identity`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `syndicate`
--
ALTER TABLE `syndicate`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=220;
--
-- AUTO_INCREMENT for table `SyndicateContent`
--
ALTER TABLE `SyndicateContent`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `SyndicateModule`
--
ALTER TABLE `SyndicateModule`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `TemplateCustomize`
--
ALTER TABLE `TemplateCustomize`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `Templating`
--
ALTER TABLE `Templating`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Testimonial`
--
ALTER TABLE `Testimonial`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Theme`
--
ALTER TABLE `Theme`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `Timeline`
--
ALTER TABLE `Timeline`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Transaction`
--
ALTER TABLE `Transaction`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Tutor`
--
ALTER TABLE `Tutor`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `Tutorial`
--
ALTER TABLE `Tutorial`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `Vendor`
--
ALTER TABLE `Vendor`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `WareHouse`
--
ALTER TABLE `WareHouse`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `WebTheme`
--
ALTER TABLE `WebTheme`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Academic`
--
ALTER TABLE `Academic`
ADD CONSTRAINT `FK_B922F95E208F64F1` FOREIGN KEY (`tutor_id`) REFERENCES `Tutor` (`id`);

--
-- Constraints for table `AcademicMeta`
--
ALTER TABLE `AcademicMeta`
ADD CONSTRAINT `FK_F308C6CD208F64F1` FOREIGN KEY (`tutor_id`) REFERENCES `Tutor` (`id`);

--
-- Constraints for table `Admission`
--
ALTER TABLE `Admission`
ADD CONSTRAINT `FK_764A80E9591CC992` FOREIGN KEY (`course_id`) REFERENCES `Course` (`id`),
ADD CONSTRAINT `FK_764A80E9A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_764A80E9AB61B33C` FOREIGN KEY (`createUser_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_764A80E9DA4CCD60` FOREIGN KEY (`courseLevel_id`) REFERENCES `CourseLevel` (`id`);

--
-- Constraints for table `AdmissionComment`
--
ALTER TABLE `AdmissionComment`
ADD CONSTRAINT `FK_8F56234475C9C554` FOREIGN KEY (`admission_id`) REFERENCES `Admission` (`id`);

--
-- Constraints for table `admission_branch`
--
ALTER TABLE `admission_branch`
ADD CONSTRAINT `FK_8115CF5575C9C554` FOREIGN KEY (`admission_id`) REFERENCES `Admission` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_8115CF55DCD6CC49` FOREIGN KEY (`branch_id`) REFERENCES `Branch` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `AdsTool`
--
ALTER TABLE `AdsTool`
ADD CONSTRAINT `FK_F3823813DC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `Advertisment`
--
ALTER TABLE `Advertisment`
ADD CONSTRAINT `FK_91209A3FDC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `Apartment`
--
ALTER TABLE `Apartment`
ADD CONSTRAINT `FK_CF8FEAF7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `Blackout`
--
ALTER TABLE `Blackout`
ADD CONSTRAINT `FK_F53A2983A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `Blog`
--
ALTER TABLE `Blog`
ADD CONSTRAINT `FK_6027FE7DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_6027FE7DBD85B63` FOREIGN KEY (`photoGallery_id`) REFERENCES `photo_gallery` (`id`);

--
-- Constraints for table `BlogComment`
--
ALTER TABLE `BlogComment`
ADD CONSTRAINT `FK_B129A665DAE07E97` FOREIGN KEY (`blog_id`) REFERENCES `Blog` (`id`);

--
-- Constraints for table `Branch`
--
ALTER TABLE `Branch`
ADD CONSTRAINT `FK_BC2A1E29A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `branding_category`
--
ALTER TABLE `branding_category`
ADD CONSTRAINT `FK_EDEE8C512469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_EDEE8C5560BC00E` FOREIGN KEY (`branding_id`) REFERENCES `Branding` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
ADD CONSTRAINT `FK_F0FE25274584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`),
ADD CONSTRAINT `FK_F0FE25278D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `sylius_cart` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
ADD CONSTRAINT `FK_3AF346683CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_3AF346683D8E604F` FOREIGN KEY (`parent`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `CategoryGrouping`
--
ALTER TABLE `CategoryGrouping`
ADD CONSTRAINT `FK_55233F30A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `categorygrouping_category`
--
ALTER TABLE `categorygrouping_category`
ADD CONSTRAINT `FK_66CB1BC112469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_66CB1BC1188B8278` FOREIGN KEY (`categorygrouping_id`) REFERENCES `CategoryGrouping` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Collection`
--
ALTER TABLE `Collection`
ADD CONSTRAINT `FK_B31066E23D8E604F` FOREIGN KEY (`parent`) REFERENCES `Collection` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ColorSize`
--
ALTER TABLE `ColorSize`
ADD CONSTRAINT `FK_48CDE5823CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `Comment`
--
ALTER TABLE `Comment`
ADD CONSTRAINT `FK_5BC96BF0E2904019` FOREIGN KEY (`thread_id`) REFERENCES `Thread` (`id`),
ADD CONSTRAINT `FK_5BC96BF0F675F31B` FOREIGN KEY (`author_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `ContactMessage`
--
ALTER TABLE `ContactMessage`
ADD CONSTRAINT `FK_DDC5A139A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `ContactPage`
--
ALTER TABLE `ContactPage`
ADD CONSTRAINT `FK_82F5EE0BA51DB16` FOREIGN KEY (`thana_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_82F5EE0BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_82F5EE0BB08FA272` FOREIGN KEY (`district_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_82F5EE0BDC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `course_courselevel`
--
ALTER TABLE `course_courselevel`
ADD CONSTRAINT `FK_7B0351DA233AAF36` FOREIGN KEY (`courselevel_id`) REFERENCES `CourseLevel` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_7B0351DA591CC992` FOREIGN KEY (`course_id`) REFERENCES `Course` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Customer`
--
ALTER TABLE `Customer`
ADD CONSTRAINT `FK_784FEC5FA51DB16` FOREIGN KEY (`thana_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_784FEC5FB08FA272` FOREIGN KEY (`district_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_784FEC5FDC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `DomainUser`
--
ALTER TABLE `DomainUser`
ADD CONSTRAINT `FK_94324237A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_94324237DC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `Education`
--
ALTER TABLE `Education`
ADD CONSTRAINT `FK_59FBDC7164D218E` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_59FBDC71A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education_courselevel`
--
ALTER TABLE `education_courselevel`
ADD CONSTRAINT `FK_4AEFC18C233AAF36` FOREIGN KEY (`courselevel_id`) REFERENCES `CourseLevel` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_4AEFC18C2CA1BD71` FOREIGN KEY (`education_id`) REFERENCES `Education` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education_institutelevel`
--
ALTER TABLE `education_institutelevel`
ADD CONSTRAINT `FK_2DD14F332CA1BD71` FOREIGN KEY (`education_id`) REFERENCES `Education` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_2DD14F33D4073482` FOREIGN KEY (`institutelevel_id`) REFERENCES `instituteLevel` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `EmailBox`
--
ALTER TABLE `EmailBox`
ADD CONSTRAINT `FK_7E16875A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `Event`
--
ALTER TABLE `Event`
ADD CONSTRAINT `FK_FA6F25A3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_FA6F25A3BD85B63` FOREIGN KEY (`photoGallery_id`) REFERENCES `photo_gallery` (`id`);

--
-- Constraints for table `Faq`
--
ALTER TABLE `Faq`
ADD CONSTRAINT `FK_D0B2332CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `FooterSetting`
--
ALTER TABLE `FooterSetting`
ADD CONSTRAINT `FK_4170B0EDDC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `GalleryImage`
--
ALTER TABLE `GalleryImage`
ADD CONSTRAINT `FK_D2F7A834BD85B63` FOREIGN KEY (`photoGallery_id`) REFERENCES `photo_gallery` (`id`);

--
-- Constraints for table `GlobalOption`
--
ALTER TABLE `GlobalOption`
ADD CONSTRAINT `FK_8B2648A14C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`),
ADD CONSTRAINT `FK_8B2648A164D218E` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_8B2648A1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `HomeBlock`
--
ALTER TABLE `HomeBlock`
ADD CONSTRAINT `FK_BC28FB51CACCD446` FOREIGN KEY (`homePage_id`) REFERENCES `HomePage` (`id`),
ADD CONSTRAINT `FK_BC28FB51CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `Menu` (`id`);

--
-- Constraints for table `HomePage`
--
ALTER TABLE `HomePage`
ADD CONSTRAINT `FK_92601886A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_92601886BD85B63` FOREIGN KEY (`photoGallery_id`) REFERENCES `photo_gallery` (`id`),
ADD CONSTRAINT `FK_92601886DC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `homepage_module`
--
ALTER TABLE `homepage_module`
ADD CONSTRAINT `FK_717671BEAFC2B591` FOREIGN KEY (`module_id`) REFERENCES `Module` (`id`),
ADD CONSTRAINT `FK_717671BECACCD446` FOREIGN KEY (`homePage_id`) REFERENCES `HomePage` (`id`);

--
-- Constraints for table `homepage_syndicate`
--
ALTER TABLE `homepage_syndicate`
ADD CONSTRAINT `FK_DFFAE1664C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`),
ADD CONSTRAINT `FK_DFFAE166CACCD446` FOREIGN KEY (`homePage_id`) REFERENCES `HomePage` (`id`);

--
-- Constraints for table `homepage_syndicatemodule`
--
ALTER TABLE `homepage_syndicatemodule`
ADD CONSTRAINT `FK_A833A0CECACCD446` FOREIGN KEY (`homePage_id`) REFERENCES `HomePage` (`id`),
ADD CONSTRAINT `FK_A833A0CEFFC40B63` FOREIGN KEY (`syndicate_module_id`) REFERENCES `SyndicateModule` (`id`);

--
-- Constraints for table `HomeSlider`
--
ALTER TABLE `HomeSlider`
ADD CONSTRAINT `FK_6F403EDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_6F403EDC4663E4` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`);

--
-- Constraints for table `instituteLevel`
--
ALTER TABLE `instituteLevel`
ADD CONSTRAINT `FK_A310F1793D8E604F` FOREIGN KEY (`parent`) REFERENCES `instituteLevel` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `InventoryConfig`
--
ALTER TABLE `InventoryConfig`
ADD CONSTRAINT `FK_D475348DC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `Item`
--
ALTER TABLE `Item`
ADD CONSTRAINT `FK_BF298A203CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_BF298A2044F5D008` FOREIGN KEY (`brand_id`) REFERENCES `Branding` (`id`),
ADD CONSTRAINT `FK_BF298A20498DA827` FOREIGN KEY (`size_id`) REFERENCES `ItemSize` (`id`),
ADD CONSTRAINT `FK_BF298A207ADA1FB5` FOREIGN KEY (`color_id`) REFERENCES `ItemColor` (`id`),
ADD CONSTRAINT `FK_BF298A20D5012B6F` FOREIGN KEY (`masterItem_id`) REFERENCES `item_master` (`id`),
ADD CONSTRAINT `FK_BF298A20F603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `supplier` (`id`),
ADD CONSTRAINT `FK_BF298A20F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Constraints for table `ItemColor`
--
ALTER TABLE `ItemColor`
ADD CONSTRAINT `FK_B073D1AE3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `ItemGallery`
--
ALTER TABLE `ItemGallery`
ADD CONSTRAINT `FK_71C05C3D126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`);

--
-- Constraints for table `ItemInventory`
--
ALTER TABLE `ItemInventory`
ADD CONSTRAINT `FK_C26F0ECB126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`);

--
-- Constraints for table `ItemSize`
--
ALTER TABLE `ItemSize`
ADD CONSTRAINT `FK_4FA8BCA3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `ItemTypeGrouping`
--
ALTER TABLE `ItemTypeGrouping`
ADD CONSTRAINT `FK_1C3E218B3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `itemtypegrouping_category`
--
ALTER TABLE `itemtypegrouping_category`
ADD CONSTRAINT `FK_7C5DE5EA12469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_7C5DE5EAEE02FE58` FOREIGN KEY (`itemtypegrouping_id`) REFERENCES `ItemTypeGrouping` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_category`
--
ALTER TABLE `item_category`
ADD CONSTRAINT `FK_6A41D10A12469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_6A41D10A126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_master`
--
ALTER TABLE `item_master`
ADD CONSTRAINT `FK_ABF0D61912469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
ADD CONSTRAINT `FK_ABF0D6193CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
ADD CONSTRAINT `FK_17E64ABA3D8E604F` FOREIGN KEY (`parent`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `megamenu_advertisment`
--
ALTER TABLE `megamenu_advertisment`
ADD CONSTRAINT `FK_98A52DEA71731BCA` FOREIGN KEY (`advertisment_id`) REFERENCES `Advertisment` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_98A52DEAB2F1DFC3` FOREIGN KEY (`megamenu_id`) REFERENCES `MegaMenu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `megamenu_branding`
--
ALTER TABLE `megamenu_branding`
ADD CONSTRAINT `FK_B23FFCD5560BC00E` FOREIGN KEY (`branding_id`) REFERENCES `Branding` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_B23FFCD5B2F1DFC3` FOREIGN KEY (`megamenu_id`) REFERENCES `MegaMenu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `megamenu_category`
--
ALTER TABLE `megamenu_category`
ADD CONSTRAINT `FK_6286B4312469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_6286B43B2F1DFC3` FOREIGN KEY (`megamenu_id`) REFERENCES `MegaMenu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `megamenu_collection`
--
ALTER TABLE `megamenu_collection`
ADD CONSTRAINT `FK_4BF38F8F514956FD` FOREIGN KEY (`collection_id`) REFERENCES `Collection` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_4BF38F8FB2F1DFC3` FOREIGN KEY (`megamenu_id`) REFERENCES `MegaMenu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `megamenu_globaloption`
--
ALTER TABLE `megamenu_globaloption`
ADD CONSTRAINT `FK_850FFA425E620E21` FOREIGN KEY (`globaloption_id`) REFERENCES `GlobalOption` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_850FFA42B2F1DFC3` FOREIGN KEY (`megamenu_id`) REFERENCES `MegaMenu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `megamenu_syndicate`
--
ALTER TABLE `megamenu_syndicate`
ADD CONSTRAINT `FK_554D586C4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_554D586CB2F1DFC3` FOREIGN KEY (`megamenu_id`) REFERENCES `MegaMenu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Menu`
--
ALTER TABLE `Menu`
ADD CONSTRAINT `FK_DD3795AD3CBC22` FOREIGN KEY (`syndicateModule_id`) REFERENCES `SyndicateModule` (`id`),
ADD CONSTRAINT `FK_DD3795AD4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`),
ADD CONSTRAINT `FK_DD3795AD8AFE69CA` FOREIGN KEY (`siteSetting_id`) REFERENCES `SiteSetting` (`id`),
ADD CONSTRAINT `FK_DD3795ADA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_DD3795ADAFC2B591` FOREIGN KEY (`module_id`) REFERENCES `Module` (`id`),
ADD CONSTRAINT `FK_DD3795ADC4663E4` FOREIGN KEY (`page_id`) REFERENCES `page` (`id`);

--
-- Constraints for table `MenuGrouping`
--
ALTER TABLE `MenuGrouping`
ADD CONSTRAINT `FK_152EC2753D8E604F` FOREIGN KEY (`parent`) REFERENCES `MenuGrouping` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `FK_152EC275A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_152EC275CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `Menu` (`id`),
ADD CONSTRAINT `FK_152EC275FBB147D2` FOREIGN KEY (`menuGroup_id`) REFERENCES `MenuGroup` (`id`);

--
-- Constraints for table `MobileIcon`
--
ALTER TABLE `MobileIcon`
ADD CONSTRAINT `FK_4DF26A06DC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `mobiletheme_syndicate`
--
ALTER TABLE `mobiletheme_syndicate`
ADD CONSTRAINT `FK_3399870B4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_3399870BCDE0580F` FOREIGN KEY (`mobiletheme_id`) REFERENCES `MobileTheme` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `News`
--
ALTER TABLE `News`
ADD CONSTRAINT `FK_BDE1366EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `NoticeBoard`
--
ALTER TABLE `NoticeBoard`
ADD CONSTRAINT `FK_106CCE7EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `page`
--
ALTER TABLE `page`
ADD CONSTRAINT `FK_140AB6203D8E604F` FOREIGN KEY (`parent`) REFERENCES `page` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `FK_140AB620A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_140AB620BD85B63` FOREIGN KEY (`photoGallery_id`) REFERENCES `photo_gallery` (`id`);

--
-- Constraints for table `photo_gallery`
--
ALTER TABLE `photo_gallery`
ADD CONSTRAINT `FK_72CB6FB7A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `Product`
--
ALTER TABLE `Product`
ADD CONSTRAINT `FK_1CF73D3144F5D008` FOREIGN KEY (`brand_id`) REFERENCES `Branding` (`id`),
ADD CONSTRAINT `FK_1CF73D31514956FD` FOREIGN KEY (`collection_id`) REFERENCES `Collection` (`id`),
ADD CONSTRAINT `FK_1CF73D31A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_1CF73D31B0D2661D` FOREIGN KEY (`parentCategory_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `ProductCustomAttribute`
--
ALTER TABLE `ProductCustomAttribute`
ADD CONSTRAINT `FK_3CD340A24584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`);

--
-- Constraints for table `ProductGallery`
--
ALTER TABLE `ProductGallery`
ADD CONSTRAINT `FK_DEBD9F594584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`);

--
-- Constraints for table `ProductReview`
--
ALTER TABLE `ProductReview`
ADD CONSTRAINT `FK_E6F558C54584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`);

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
ADD CONSTRAINT `FK_CDFC735612469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_CDFC73564584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Purchase`
--
ALTER TABLE `Purchase`
ADD CONSTRAINT `FK_9861B36D3174800F` FOREIGN KEY (`createdBy_id`) REFERENCES `DomainUser` (`id`),
ADD CONSTRAINT `FK_9861B36D3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_9861B36DF603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `supplier` (`id`),
ADD CONSTRAINT `FK_9861B36DFACFC38A` FOREIGN KEY (`approvedBy_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `PurchaseItem`
--
ALTER TABLE `PurchaseItem`
ADD CONSTRAINT `FK_334FA427126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`),
ADD CONSTRAINT `FK_334FA427558FBEB9` FOREIGN KEY (`purchase_id`) REFERENCES `Purchase` (`id`),
ADD CONSTRAINT `FK_334FA4277F14552F` FOREIGN KEY (`purchaseVendorItem_id`) REFERENCES `PurchaseVendorItem` (`id`);

--
-- Constraints for table `PurchaseReturn`
--
ALTER TABLE `PurchaseReturn`
ADD CONSTRAINT `FK_33A52D57F603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `supplier` (`id`),
ADD CONSTRAINT `FK_33A52D573174800F` FOREIGN KEY (`createdBy_id`) REFERENCES `DomainUser` (`id`),
ADD CONSTRAINT `FK_33A52D573CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `PurchaseReturnItem`
--
ALTER TABLE `PurchaseReturnItem`
ADD CONSTRAINT `FK_B5C64132A787C244` FOREIGN KEY (`purchaseReturn_id`) REFERENCES `PurchaseReturn` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_B5C64132FF8E8E14` FOREIGN KEY (`purchaseItem_id`) REFERENCES `PurchaseItem` (`id`);

--
-- Constraints for table `PurchaseVendorItem`
--
ALTER TABLE `PurchaseVendorItem`
ADD CONSTRAINT `FK_8AA73017558FBEB9` FOREIGN KEY (`purchase_id`) REFERENCES `Purchase` (`id`);

--
-- Constraints for table `Sales`
--
ALTER TABLE `Sales`
ADD CONSTRAINT `FK_AA405F4011C8FB41` FOREIGN KEY (`bank_id`) REFERENCES `Bank` (`id`),
ADD CONSTRAINT `FK_AA405F403CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_AA405F40597ADFA9` FOREIGN KEY (`paymentCard_id`) REFERENCES `PaymentCard` (`id`),
ADD CONSTRAINT `FK_AA405F409395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `Customer` (`id`),
ADD CONSTRAINT `FK_AA405F40CB9492CE` FOREIGN KEY (`salesBy_id`) REFERENCES `DomainUser` (`id`);

--
-- Constraints for table `SalesItem`
--
ALTER TABLE `SalesItem`
ADD CONSTRAINT `FK_85A7BE63126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`),
ADD CONSTRAINT `FK_85A7BE63A4522A07` FOREIGN KEY (`sales_id`) REFERENCES `Sales` (`id`),
ADD CONSTRAINT `FK_85A7BE63FF8E8E14` FOREIGN KEY (`purchaseItem_id`) REFERENCES `PurchaseItem` (`id`);

--
-- Constraints for table `SalesReturn`
--
ALTER TABLE `SalesReturn`
ADD CONSTRAINT `FK_5AD2E8C43174800F` FOREIGN KEY (`createdBy_id`) REFERENCES `DomainUser` (`id`),
ADD CONSTRAINT `FK_5AD2E8C43CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_5AD2E8C4A4522A07` FOREIGN KEY (`sales_id`) REFERENCES `Sales` (`id`);

--
-- Constraints for table `SalesReturnItem`
--
ALTER TABLE `SalesReturnItem`
ADD CONSTRAINT `FK_D9476D1E615DE49` FOREIGN KEY (`salesItem_id`) REFERENCES `SalesItem` (`id`),
ADD CONSTRAINT `FK_D9476D1EC3F94932` FOREIGN KEY (`salesReturn_id`) REFERENCES `SalesReturn` (`id`);

--
-- Constraints for table `Scholarship`
--
ALTER TABLE `Scholarship`
ADD CONSTRAINT `FK_89A359E864D218E` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `scholarship_syndicate`
--
ALTER TABLE `scholarship_syndicate`
ADD CONSTRAINT `FK_4F27AF3128722836` FOREIGN KEY (`scholarship_id`) REFERENCES `Scholarship` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_4F27AF314C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `SiteContent`
--
ALTER TABLE `SiteContent`
ADD CONSTRAINT `FK_3E79CF3E3D8E604F` FOREIGN KEY (`parent`) REFERENCES `SiteContent` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `SiteSetting`
--
ALTER TABLE `SiteSetting`
ADD CONSTRAINT `FK_5FC8470F34963A59` FOREIGN KEY (`mobileTheme_id`) REFERENCES `MobileTheme` (`id`),
ADD CONSTRAINT `FK_5FC8470F3C935932` FOREIGN KEY (`webTheme_id`) REFERENCES `WebTheme` (`id`),
ADD CONSTRAINT `FK_5FC8470F59027487` FOREIGN KEY (`theme_id`) REFERENCES `Theme` (`id`),
ADD CONSTRAINT `FK_5FC8470FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_5FC8470FDC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `sitesetting_appmodule`
--
ALTER TABLE `sitesetting_appmodule`
ADD CONSTRAINT `FK_931E01D97ADEAA4` FOREIGN KEY (`app_module_id`) REFERENCES `AppModule` (`id`),
ADD CONSTRAINT `FK_931E01D9C5A36A1A` FOREIGN KEY (`sitesetting_id`) REFERENCES `SiteSetting` (`id`);

--
-- Constraints for table `sitesetting_module`
--
ALTER TABLE `sitesetting_module`
ADD CONSTRAINT `FK_CB2BF790AFC2B591` FOREIGN KEY (`module_id`) REFERENCES `Module` (`id`),
ADD CONSTRAINT `FK_CB2BF790C5A36A1A` FOREIGN KEY (`sitesetting_id`) REFERENCES `SiteSetting` (`id`);

--
-- Constraints for table `sitesetting_syndicate`
--
ALTER TABLE `sitesetting_syndicate`
ADD CONSTRAINT `FK_952A98B24C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`),
ADD CONSTRAINT `FK_952A98B2C5A36A1A` FOREIGN KEY (`sitesetting_id`) REFERENCES `SiteSetting` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sitesetting_syndicatemodule`
--
ALTER TABLE `sitesetting_syndicatemodule`
ADD CONSTRAINT `FK_8E07CDC6C5A36A1A` FOREIGN KEY (`sitesetting_id`) REFERENCES `SiteSetting` (`id`),
ADD CONSTRAINT `FK_8E07CDC6FFC40B63` FOREIGN KEY (`syndicate_module_id`) REFERENCES `SyndicateModule` (`id`);

--
-- Constraints for table `StockItem`
--
ALTER TABLE `StockItem`
ADD CONSTRAINT `FK_6DDA4EF3174800F` FOREIGN KEY (`createdBy_id`) REFERENCES `DomainUser` (`id`),
ADD CONSTRAINT `FK_6DDA4EF12469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
ADD CONSTRAINT `FK_6DDA4EF126F525E` FOREIGN KEY (`item_id`) REFERENCES `Item` (`id`),
ADD CONSTRAINT `FK_6DDA4EF3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_6DDA4EF44F5D008` FOREIGN KEY (`brand_id`) REFERENCES `Branding` (`id`),
ADD CONSTRAINT `FK_6DDA4EF615DE49` FOREIGN KEY (`salesItem_id`) REFERENCES `SalesItem` (`id`),
ADD CONSTRAINT `FK_6DDA4EFF603EE73` FOREIGN KEY (`vendor_id`) REFERENCES `supplier` (`id`),
ADD CONSTRAINT `FK_6DDA4EFF92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`),
ADD CONSTRAINT `FK_6DDA4EFFF8E8E14` FOREIGN KEY (`purchaseItem_id`) REFERENCES `PurchaseItem` (`id`);

--
-- Constraints for table `StudyAbroad`
--
ALTER TABLE `StudyAbroad`
ADD CONSTRAINT `FK_716E744164D218E` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_716E7441A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `studyabroad_syndicate`
--
ALTER TABLE `studyabroad_syndicate`
ADD CONSTRAINT `FK_C8D9B6FB4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_C8D9B6FBB9D52860` FOREIGN KEY (`studyabroad_id`) REFERENCES `StudyAbroad` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier`
--
ALTER TABLE `supplier`
ADD CONSTRAINT `FK_9B2A6C7E3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_9B2A6C7EF92F3E70` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`);

--
-- Constraints for table `sylius_adjustment`
--
ALTER TABLE `sylius_adjustment`
ADD CONSTRAINT `FK_ACA6E0F28D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `sylius_cart` (`id`),
ADD CONSTRAINT `FK_ACA6E0F2E415FB15` FOREIGN KEY (`order_item_id`) REFERENCES `cart_item` (`id`);

--
-- Constraints for table `sylius_order_comment`
--
ALTER TABLE `sylius_order_comment`
ADD CONSTRAINT `FK_8EA9CF098D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `sylius_cart` (`id`);

--
-- Constraints for table `sylius_order_identity`
--
ALTER TABLE `sylius_order_identity`
ADD CONSTRAINT `FK_5757A18E8D9F6D38` FOREIGN KEY (`order_id`) REFERENCES `sylius_cart` (`id`);

--
-- Constraints for table `syndicate`
--
ALTER TABLE `syndicate`
ADD CONSTRAINT `FK_D40A5CB13D8E604F` FOREIGN KEY (`parent`) REFERENCES `syndicate` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `SyndicateContent`
--
ALTER TABLE `SyndicateContent`
ADD CONSTRAINT `FK_2FB0B3C24C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`),
ADD CONSTRAINT `FK_2FB0B3C2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_2FB0B3C2BD85B63` FOREIGN KEY (`photoGallery_id`) REFERENCES `photo_gallery` (`id`);

--
-- Constraints for table `syndicatemodule_syndicate`
--
ALTER TABLE `syndicatemodule_syndicate`
ADD CONSTRAINT `FK_CF3B593D4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_CF3B593D82CD3E81` FOREIGN KEY (`syndicatemodule_id`) REFERENCES `SyndicateModule` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `TemplateCustomize`
--
ALTER TABLE `TemplateCustomize`
ADD CONSTRAINT `FK_8A804D56DC938C82` FOREIGN KEY (`globalOption_id`) REFERENCES `GlobalOption` (`id`);

--
-- Constraints for table `Testimonial`
--
ALTER TABLE `Testimonial`
ADD CONSTRAINT `FK_60214220A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `theme_syndicate`
--
ALTER TABLE `theme_syndicate`
ADD CONSTRAINT `FK_9838049F4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_9838049F59027487` FOREIGN KEY (`theme_id`) REFERENCES `Theme` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Transaction`
--
ALTER TABLE `Transaction`
ADD CONSTRAINT `FK_F4AB8A063CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`);

--
-- Constraints for table `Tutor`
--
ALTER TABLE `Tutor`
ADD CONSTRAINT `FK_58C6694C64D218E` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_58C6694CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutor_syndicate`
--
ALTER TABLE `tutor_syndicate`
ADD CONSTRAINT `FK_4B177820208F64F1` FOREIGN KEY (`tutor_id`) REFERENCES `Tutor` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_4B1778204C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
ADD CONSTRAINT `FK_6BBD6130A51DB16` FOREIGN KEY (`thana_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_6BBD6130A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_6BBD6130B08FA272` FOREIGN KEY (`district_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_6BBD6130FE830143` FOREIGN KEY (`domainUser_id`) REFERENCES `DomainUser` (`id`);

--
-- Constraints for table `user_user_group`
--
ALTER TABLE `user_user_group`
ADD CONSTRAINT `FK_28657971A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`),
ADD CONSTRAINT `FK_28657971FE54D947` FOREIGN KEY (`group_id`) REFERENCES `user_group` (`id`);

--
-- Constraints for table `Vendor`
--
ALTER TABLE `Vendor`
ADD CONSTRAINT `FK_F28E36C064D218E` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
ADD CONSTRAINT `FK_F28E36C0A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `WareHouse`
--
ALTER TABLE `WareHouse`
ADD CONSTRAINT `FK_AF83265B3CA07BD1` FOREIGN KEY (`inventoryConfig_id`) REFERENCES `InventoryConfig` (`id`),
ADD CONSTRAINT `FK_AF83265B3D8E604F` FOREIGN KEY (`parent`) REFERENCES `WareHouse` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `webtheme_syndicate`
--
ALTER TABLE `webtheme_syndicate`
ADD CONSTRAINT `FK_391C457F4C37717D` FOREIGN KEY (`syndicate_id`) REFERENCES `syndicate` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `FK_391C457FC5E53B64` FOREIGN KEY (`webtheme_id`) REFERENCES `WebTheme` (`id`) ON DELETE CASCADE;
