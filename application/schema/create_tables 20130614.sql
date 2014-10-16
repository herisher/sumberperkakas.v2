-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 14. Juni 2013 jam 14:26
-- Versi Server: 5.5.16
-- Versi PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sumberperkakas`
--
CREATE DATABASE `sumberperkakas` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sumberperkakas`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_admin`
--

CREATE TABLE IF NOT EXISTS `dtb_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `account` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `lastlogin_date` datetime DEFAULT NULL,
  `update_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `dtb_admin`
--

INSERT INTO `dtb_admin` (`id`, `type`, `account`, `password`, `lastlogin_date`, `update_date`, `create_date`) VALUES
(1, 1, 'admin', 'test', '2013-05-30 00:00:00', '2013-05-30 00:00:00', '2013-05-30 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_brand`
--

CREATE TABLE IF NOT EXISTS `dtb_brand` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `image_url` varchar(200) DEFAULT NULL,
  `thumb_url` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data untuk tabel `dtb_brand`
--

INSERT INTO `dtb_brand` (`id`, `name`, `disp_flag`, `image_url`, `thumb_url`, `url`, `update_date`, `create_date`) VALUES
(4, 'Bosch', 1, '/upload/brand/bosch-logo_311371105000.jpg', '/upload/brand/thumb/t_bosch-logo_311371105000.jpg', 'www.bosch.com', '2013-06-13 13:30:00', '2013-06-13 13:30:00'),
(5, 'karcher', 1, '/upload/brand/karcher_141371105710.jpg', '/upload/brand/thumb/t_karcher_141371105710.jpg', 'www.karcher.com', '2013-06-13 13:41:51', '2013-06-13 13:32:34');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_category`
--

CREATE TABLE IF NOT EXISTS `dtb_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data untuk tabel `dtb_category`
--

INSERT INTO `dtb_category` (`id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(1, 'Kategori Utama Pertama', 1, 1, '2013-06-13 20:05:31', '2013-06-13 13:43:09'),
(2, 'Kategori Utama Kedua', 2, 1, '2013-06-13 20:05:31', '2013-06-13 19:54:17'),
(3, 'Kategori Utama Ketiga', 3, 1, '2013-06-13 20:05:29', '2013-06-13 19:54:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_news`
--

CREATE TABLE IF NOT EXISTS `dtb_news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `image_url` varchar(200) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `disp_date` date NOT NULL,
  `disp_flag` tinyint(4) NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `dtb_news`
--

INSERT INTO `dtb_news` (`id`, `title`, `content`, `url`, `image_url`, `type`, `sort_order`, `disp_date`, `disp_flag`, `create_date`, `update_date`) VALUES
(1, 'test', '<p><a href="http://www.google.com" target="_blank">kalibata</a></p>', '', NULL, 1, 3, '2013-06-03', 1, '2013-06-03 15:43:04', '2013-06-03 15:43:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_product`
--

CREATE TABLE IF NOT EXISTS `dtb_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `sub_category_id` bigint(20) unsigned DEFAULT NULL,
  `sub_category1_id` bigint(20) unsigned DEFAULT NULL,
  `sub_category2_id` bigint(20) unsigned DEFAULT NULL,
  `item_number` varchar(16) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `type` varchar(200) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1',
  `disp_flag` tinyint(4) NOT NULL DEFAULT '1',
  `description` text,
  `call_us` tinyint(4) DEFAULT '0',
  `price` int(11) DEFAULT NULL,
  `call_price` tinyint(4) DEFAULT '0',
  `promo_price` int(11) DEFAULT NULL,
  `promo_period_start` date DEFAULT NULL,
  `promo_period_end` date DEFAULT NULL,
  `image_url1` varchar(200) DEFAULT NULL,
  `image_url2` varchar(200) DEFAULT NULL,
  `image_url3` varchar(200) DEFAULT NULL,
  `th082_url1` varchar(200) DEFAULT NULL,
  `th082_url2` varchar(200) DEFAULT NULL,
  `th082_url3` varchar(200) DEFAULT NULL,
  `th155_url1` varchar(200) DEFAULT NULL,
  `th155_url2` varchar(200) DEFAULT NULL,
  `th155_url3` varchar(200) DEFAULT NULL,
  `th270_url1` varchar(200) DEFAULT NULL,
  `th270_url2` varchar(200) DEFAULT NULL,
  `th270_url3` varchar(200) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `dtb_product`
--

INSERT INTO `dtb_product` (`id`, `brand_id`, `category_id`, `sub_category_id`, `sub_category1_id`, `sub_category2_id`, `item_number`, `name`, `type`, `status`, `disp_flag`, `description`, `call_us`, `price`, `call_price`, `promo_price`, `promo_period_start`, `promo_period_end`, `image_url1`, `image_url2`, `image_url3`, `th082_url1`, `th082_url2`, `th082_url3`, `th155_url1`, `th155_url2`, `th155_url3`, `th270_url1`, `th270_url2`, `th270_url3`, `update_date`, `create_date`) VALUES
(1, 5, 1, 1, 1, 1, '552266', 'JET CLEANER KARCHER / HIGH PRESSURE CLEANERS K 2.1', 'K 2.14', 1, 1, 'Pressure: Max. 100bar\r\nWater Flow Rate: Max. 340L/ H\r\nMax. Water Feed Temperature: 40dr celcius\r\nConected Load: 1.3kw', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '/upload/product/brosur_k214_big_1001371114938.jpg', '/upload/product/karcher_high_pre_4ca433685860d_211371114938.jpg', '/upload/product/karcher_201371114938.jpg', '/upload/product/thumb/t_brosur_k214_big_1001371114938.jpg', '/upload/product/thumb/t_karcher_high_pre_4ca433685860d_211371114938.jpg', '/upload/product/thumb/t_karcher_201371114938.jpg', '/upload/product/thumb/t_brosur_k214_big_1001371114938.jpg', '/upload/product/thumb/t_karcher_high_pre_4ca433685860d_211371114938.jpg', '/upload/product/thumb/t_karcher_201371114938.jpg', '/upload/product/thumb/t_brosur_k214_big_1001371114938.jpg', '/upload/product/thumb/t_karcher_high_pre_4ca433685860d_211371114938.jpg', '/upload/product/thumb/t_karcher_201371114938.jpg', '2013-06-13 17:12:54', '2013-06-13 16:15:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_sub_category`
--

CREATE TABLE IF NOT EXISTS `dtb_sub_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data untuk tabel `dtb_sub_category`
--

INSERT INTO `dtb_sub_category` (`id`, `category_id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(1, 1, 'Kategori dari Kategori Utama Pertama', 2, 1, '2013-06-13 20:06:19', '2013-06-13 13:43:29'),
(2, 1, 'Kategori Kedua dari Kategori Utama Pertama', 1, 1, '2013-06-13 20:06:19', '2013-06-13 19:57:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_sub_category1`
--

CREATE TABLE IF NOT EXISTS `dtb_sub_category1` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `sub_category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data untuk tabel `dtb_sub_category1`
--

INSERT INTO `dtb_sub_category1` (`id`, `category_id`, `sub_category_id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(1, 1, 1, 'Sub Kategori dari Kategori Pertama', 2, 1, '2013-06-14 10:17:03', '2013-06-13 13:43:48'),
(2, 1, 1, 'Sub Kategori I dari PERTAMA', 1, 1, '2013-06-14 10:17:03', '2013-06-13 19:59:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_sub_category2`
--

CREATE TABLE IF NOT EXISTS `dtb_sub_category2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `sub_category_id` bigint(20) unsigned NOT NULL,
  `sub_category1_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data untuk tabel `dtb_sub_category2`
--

INSERT INTO `dtb_sub_category2` (`id`, `category_id`, `sub_category_id`, `sub_category1_id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(1, 1, 1, 1, 'Sub Kategori II dari Sub Kategori', 2, 1, '2013-06-14 10:18:32', '2013-06-13 13:44:04'),
(2, 1, 1, 1, 'Sub Kategori II Pertama dari PERTAMA', 1, 1, '2013-06-14 10:18:32', '2013-06-13 20:03:31');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
