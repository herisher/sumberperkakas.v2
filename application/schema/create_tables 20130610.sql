-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 10. Juni 2013 jam 13:20
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `dtb_brand`
--

INSERT INTO `dtb_brand` (`id`, `name`, `disp_flag`, `image_url`, `thumb_url`, `url`, `update_date`, `create_date`) VALUES
(1, 'Karcher', 1, '', '', 'http://www.karcherindonesia.com/', '2013-05-31 15:21:34', '2013-05-31 15:19:36');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data untuk tabel `dtb_category`
--

INSERT INTO `dtb_category` (`id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(18, 'Pertama', 2, 1, '2013-05-31 20:15:55', '2013-05-31 20:02:32'),
(24, 'Kedua', 3, 1, '2013-06-04 20:04:24', '2013-06-04 20:04:24');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data untuk tabel `dtb_product`
--

INSERT INTO `dtb_product` (`id`, `brand_id`, `category_id`, `sub_category_id`, `item_number`, `name`, `type`, `status`, `disp_flag`, `description`, `call_us`, `price`, `call_price`, `promo_price`, `promo_period_start`, `promo_period_end`, `image_url1`, `image_url2`, `image_url3`, `th082_url1`, `th082_url2`, `th082_url3`, `th155_url1`, `th155_url2`, `th155_url3`, `th270_url1`, `th270_url2`, `th270_url3`, `update_date`, `create_date`) VALUES
(1, 1, 18, NULL, '', 'aaa', '', NULL, 1, 'aaa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', '', '', '', '', '', '', '', '', '', '', '', '2013-06-10 14:19:09', '2013-06-10 14:19:09'),
(2, 1, 24, NULL, '', 'wewew', '', NULL, 1, 'aa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 14:26:19', '2013-06-10 14:26:19'),
(3, 1, 18, NULL, '', 'reerere', '', NULL, 1, 'ee', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 14:32:02', '2013-06-10 14:32:02'),
(4, 1, 18, NULL, '', 'aaa', '', NULL, 1, 'aaa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 14:32:30', '2013-06-10 14:32:30'),
(5, 1, 18, NULL, '', 'www', '', NULL, 1, 'aa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 14:34:08', '2013-06-10 14:34:08'),
(6, 1, 18, NULL, '', 'weee', '', NULL, 1, 'aa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 15:07:20', '2013-06-10 15:07:20'),
(7, 1, 18, NULL, '', 'ererer', '', NULL, 1, 'aaa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 15:14:51', '2013-06-10 15:14:51'),
(8, 1, 18, NULL, '', 'wwewew', '', NULL, 1, 'aa', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 15:15:17', '2013-06-10 15:15:17'),
(9, 1, 18, NULL, '', 'qq', '', NULL, 0, 'ccc', 1, NULL, 0, 0, '0000-00-00', '0000-00-00', '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '', NULL, NULL, '2013-06-10 15:42:50', '2013-06-10 15:42:50');

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
(1, 18, 'Sub Kategori Pertama', 2, 1, '2013-06-04 19:41:17', '2013-06-04 18:58:28'),
(2, 18, 'Sub Kategori Kedua', 1, 1, '2013-06-04 19:41:17', '2013-06-04 18:58:47');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `dtb_sub_category1`
--

INSERT INTO `dtb_sub_category1` (`id`, `category_id`, `sub_category_id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(1, 18, 1, 'Sub Kategori I Pertama Pertama', 1, 1, '2013-06-10 13:38:18', '2013-06-10 13:38:18');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data untuk tabel `dtb_sub_category2`
--

INSERT INTO `dtb_sub_category2` (`id`, `category_id`, `sub_category_id`, `sub_category1_id`, `name`, `disp_order`, `disp_flag`, `update_date`, `create_date`) VALUES
(1, 18, 1, 1, 'Sub Kategori II Pertama Pertama', 1, 1, '2013-06-10 17:27:41', '2013-06-10 17:27:41');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
