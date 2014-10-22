-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 22, 2014 at 11:21 AM
-- Server version: 5.1.73
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `app_myvillage`
--

-- --------------------------------------------------------

--
-- Table structure for table `dtb_category`
--

CREATE TABLE IF NOT EXISTS `dtb_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `url` varchar(200) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `dtb_category`
--

INSERT INTO `dtb_category` (`id`, `name`, `disp_order`, `disp_flag`, `url`, `update_date`, `create_date`) VALUES
(1, 'MESIN POTONG RUMPUT', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5769/mesin-potong-rumput.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(2, 'POMPA AIR', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5771/pompa-air.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(3, 'MESIN BOR', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5772/mesin-bor.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(4, 'ACCESSORIES', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5773/accessories.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(5, 'JET CLEANER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5774/jet-cleaner.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(6, 'MESIN PERTUKANGAN DAN INDUSTRI', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5775/mesin-pertukangan-dan-industri.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(7, 'ALAT PERTANIAN / MESIN PERTANIAN', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5776/alat-pertanian-mesin-pertanian.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(8, 'FAN,  EXHASUT,  AIR CURTAIN,  KIPAS KABUT', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5777/fan-exhasut-air-curtain-kipas-kabut.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(9, 'PRODUK LAINNYA', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5778/produk-lainnya.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(10, 'ALAT UKUR', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/ggroup+5788/alat-ukur.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(11, 'MESIN FOGGING', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+78524/mesin-fogging.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(12, 'CHAIN SAW ( MESIN....', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+76582/chain-saw-mesin-potongtebang-kayu.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(13, 'GENSET / GENERATOR', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75551/genset-generator.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(14, 'KARCHER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+89017/karcher.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(15, 'DIGITAL DOORLOCK YALE / ....', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+194359/digital-doorlock-yale-kunci-digital-yale.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(16, 'KOMPRESOR / AIR....', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75552/kompresor-air-compressor.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(17, 'SEPATU INDUSTRI / ....', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+78987/sepatu-industri-safety-shoes.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(18, 'RANTAI DEREK / KATROL ( ....', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+80906/rantai-derek-katrol-chain-hoist-lever-hoist-gear-trolley.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(19, 'RANTAI DEREK ( chain....', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+76397/rantai-derek-chain-hoist-lever-hoist-gear-trolley-nlg.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(20, 'MESIN POLES MOBIL', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+134375/mesin-poles-mobil.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(21, 'STABILIZER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+184356/stabilizer.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36'),
(22, 'LIGHT TOWER FIRMAN', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+216598/light-tower-firman.htm', '2014-10-22 10:20:36', '2014-10-22 10:20:36');
