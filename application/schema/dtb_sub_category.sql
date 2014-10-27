-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 23, 2014 at 03:38 PM
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
-- Table structure for table `dtb_sub_category`
--

CREATE TABLE IF NOT EXISTS `dtb_sub_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `url` varchar(200) DEFAULT NULL,
  `image_url` varchar(200) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `dtb_sub_category`
--

INSERT INTO `dtb_sub_category` (`id`, `category_id`, `name`, `disp_order`, `disp_flag`, `url`, `image_url`, `update_date`, `create_date`) VALUES
(1, 1, 'MESIN POTONG RUMPUT GENDONG / BRUSH CUTTER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75554', 'http://202.67.224.137/sgimage/54/s_75554_potongrumput.jpg', '2014-10-23 14:36:05', '2014-10-23 14:36:05'),
(2, 1, 'MESIN POTONG RUMPUT DORONG  / LAWN MOWERS ', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+91006', 'http://202.67.224.137/sgimage/06/s_91006_tasco1.jpg', '2014-10-23 14:36:05', '2014-10-23 14:36:05'),
(3, 1, 'ACCESSORIES POTONG RUMPUT', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+218337', 'http://202.67.224.137/sgimage/37/218337_pisau.jpg', '2014-10-23 14:36:05', '2014-10-23 14:36:05'),
(4, 2, 'JET PUMP GRUNDFOS', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+95577', 'http://202.67.224.137/sgimage/77/95577_booster-pump--inverter-uni-e-ch4-40-grundfos.jpg', '2014-10-23 14:36:06', '2014-10-23 14:36:06'),
(5, 2, 'CENTRIFUGAL PUMP GRUNDFOS', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+95578', 'http://202.67.224.137/sgimage/78/95578_centrifugal-pump-ns-basic-13-18m-grundfos.jpg', '2014-10-23 14:36:06', '2014-10-23 14:36:06'),
(6, 2, 'BOOSTER PUMP GRUNDFOS', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+95579', 'http://202.67.224.137/sgimage/79/s_95579_booster-pump-spa15-90-grundfos.jpg', '2014-10-23 14:36:06', '2014-10-23 14:36:06'),
(7, 2, 'POMPA CELUP ', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+95669', 'http://202.67.224.137/sgimage/69/95669_submersiblepump-pompacelup-grundfos.jpg', '2014-10-23 14:36:06', '2014-10-23 14:36:06'),
(8, 2, 'POMPA KOLAM RENANG', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+195742', 'http://www.indonetwork.co.id/images/id/no-image.gif', '2014-10-23 14:36:06', '2014-10-23 14:36:06'),
(9, 2, 'POMPA IRIGASI', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+90705', 'http://202.67.224.137/sgimage/05/s_90705_pomirigasi.jpg', '2014-10-23 14:36:06', '2014-10-23 14:36:06'),
(10, 3, 'MESIN BOR', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75139', 'http://202.67.224.137/sgimage/39/s_75139_logobor.jpg', '2014-10-23 14:36:08', '2014-10-23 14:36:08'),
(11, 3, 'BOR IMPACT / IMPACT DRILL', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75140', 'http://202.67.224.137/sgimage/40/s_75140_logobor.jpg', '2014-10-23 14:36:08', '2014-10-23 14:36:08'),
(12, 3, 'BOR HAMMER / ROTARY HAMMER DRILL', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75384', 'http://202.67.224.137/sgimage/84/s_75384_logobor.jpg', '2014-10-23 14:36:08', '2014-10-23 14:36:08'),
(13, 3, 'BOR TANPA KABEL / CORDLESS DRILL', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75546', 'http://202.67.224.137/sgimage/46/s_75546_logocord.jpg', '2014-10-23 14:36:08', '2014-10-23 14:36:08'),
(14, 3, ' MAHNETIK DRILL/MESIN BOR MAHNET', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+115762', 'http://202.67.224.137/sgimage/62/s_115762_bordudukhitachi23mm.jpg', '2014-10-23 14:36:08', '2014-10-23 14:36:08'),
(15, 4, 'ASESORIS MESIN / POWER TOOLS ACCESSORIES ( mata bo', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75872', 'http://202.67.224.137/sgimage/72/s_75872_logoacs.jpg', '2014-10-23 14:36:10', '2014-10-23 14:36:10'),
(16, 4, 'MATA BOR / DRILL BIT', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+79492', 'http://202.67.224.137/sgimage/92/s_79492_chisel.jpg', '2014-10-23 14:36:10', '2014-10-23 14:36:10'),
(17, 4, 'ACCESSORIES VACUUM CLEANER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+134464', 'http://202.67.224.137/sgimage/64/s_134464_2977610_filter-vacuum-cleaner-bosch1.jpg', '2014-10-23 14:36:10', '2014-10-23 14:36:10'),
(18, 4, 'ACCESSORIES JET CLEANER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+134466', 'http://202.67.224.137/sgimage/66/s_134466_2841207_high-pressure-extension-house-10m-k2-k81.jpg', '2014-10-23 14:36:10', '2014-10-23 14:36:10'),
(19, 5, 'JET CLEANER / HIGH PRESSURE CLEANER MULTI PRO', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75871', 'http://202.67.224.137/sgimage/71/s_75871_steanj.jpg', '2014-10-23 14:36:11', '2014-10-23 14:36:11'),
(20, 5, 'JET CLEANER / HIGH PRESSURE CLEANER FUJIYAMA', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+88739', 'http://202.67.224.137/sgimage/39/s_88739_logojet.jpg', '2014-10-23 14:36:11', '2014-10-23 14:36:11'),
(21, 5, 'JET ClLEANER TASCO,  KYODO &amp; KYODO', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+117477', 'http://202.67.224.137/sgimage/77/s_117477_jetcleanertascotpw-2800.jpg', '2014-10-23 14:36:11', '2014-10-23 14:36:11'),
(22, 6, 'MESIN GERINDA / GRINDER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75547', 'http://202.67.224.137/sgimage/47/s_75547_copy11ofpoertoolbosch2.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(23, 6, 'MESIN AMPLAS / SANDER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75548', 'http://202.67.224.137/sgimage/48/s_75548_logosander.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(24, 6, 'MESIN PROFILE / TRIMMER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75549', 'http://202.67.224.137/sgimage/49/s_75549_logoprofile.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(25, 6, 'MESIN SERUT / PLANER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75550', 'http://202.67.224.137/sgimage/50/s_75550_logoserut.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(26, 6, 'MESIN POTONG / CUTTER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75644', 'http://202.67.224.137/sgimage/44/s_75644_logocutoff.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(27, 6, 'HOT AIR GUN', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75868', 'http://202.67.224.137/sgimage/68/s_75868_logohotairgun.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(28, 6, 'MESIN POLES / POLISHER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75870', 'http://202.67.224.137/sgimage/70/s_75870_logopoles.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(29, 6, 'MESIN LAS / WELDING MACHINE', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+90195', 'http://202.67.224.137/sgimage/95/s_90195_telwintig-185dc.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(30, 6, 'MESIN LAS / WELDING ( TRAFO LAS ) WIM', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75553', 'http://202.67.224.137/sgimage/53/s_75553_travolaswim3.jpg', '2014-10-23 14:36:13', '2014-10-23 14:36:13'),
(31, 7, 'ALAT SEMPROT HAMA', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+81076', 'http://202.67.224.137/sgimage/76/s_81076_swan1.jpg', '2014-10-23 14:36:15', '2014-10-23 14:36:15'),
(32, 7, 'BLOWER TAMBAK / KOLAM IKAN,  UDANG', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+82732', 'http://202.67.224.137/sgimage/32/s_82732_blowertambak1.jpg', '2014-10-23 14:36:15', '2014-10-23 14:36:15'),
(33, 7, 'MESIN PERAHU', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+140658', 'http://202.67.224.137/sgimage/58/s_140658_3158601_yamaha75hp1.png', '2014-10-23 14:36:15', '2014-10-23 14:36:15'),
(34, 8, 'KIPAS (FAN) UNTUK RUMAH TANGGA', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+75966', 'http://202.67.224.137/sgimage/66/s_75966_logokipas.jpg', '2014-10-23 14:36:17', '2014-10-23 14:36:17'),
(35, 8, 'KIPAS INDUSTRI', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+95784', 'http://202.67.224.137/sgimage/84/s_95784_explosion-proof-30xpq-kdk.jpg', '2014-10-23 14:36:17', '2014-10-23 14:36:17'),
(36, 8, 'AIR CURTAIN/TIRAI UDARA', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+135008', 'http://202.67.224.137/sgimage/08/s_135008_aircurtain12eskkdk.jpg', '2014-10-23 14:36:17', '2014-10-23 14:36:17'),
(37, 8, 'MESIN KABUT / HUMIDIFIER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+81068', 'http://202.67.224.137/sgimage/68/s_81068_mesinkabut.jpg', '2014-10-23 14:36:17', '2014-10-23 14:36:17'),
(38, 8, 'TURBIN VENTILATOR ', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+191257', 'http://202.67.224.137/sgimage/57/191257_2691755_turbin-ventilator-cke.jpg', '2014-10-23 14:36:18', '2014-10-23 14:36:18'),
(39, 8, 'KIPAS KABUT / MISTY COOL', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+122801', 'http://202.67.224.137/sgimage/01/122801_product_13050173541.jpg', '2014-10-23 14:36:18', '2014-10-23 14:36:18'),
(40, 9, 'HAND PALLET', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+77451', 'http://202.67.224.137/sgimage/51/77451_kw0500045.jpg', '2014-10-23 14:36:19', '2014-10-23 14:36:19'),
(41, 9, 'MESIN / ENGINE', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+117878', 'http://202.67.224.137/sgimage/78/s_117878_153_894.jpg', '2014-10-23 14:36:19', '2014-10-23 14:36:19'),
(42, 9, 'NILFISK', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+141550', 'http://202.67.224.137/sgimage/50/s_141550_2340295_densin1.jpg', '2014-10-23 14:36:19', '2014-10-23 14:36:19'),
(43, 9, 'MESIN KONSTRUKSI', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+172501', 'http://202.67.224.137/sgimage/01/172501_s_3787590_mikasa-mvh-306gh.jpg', '2014-10-23 14:36:19', '2014-10-23 14:36:19'),
(44, 9, 'LAIN LAIN', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+117879', 'http://202.67.224.137/sgimage/79/117879_graphic1.jpg', '2014-10-23 14:36:19', '2014-10-23 14:36:19'),
(45, 9, 'DISPLAY MERK', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+201955', 'http://202.67.224.137/sgimage/55/s_201955_firman.jpg', '2014-10-23 14:36:19', '2014-10-23 14:36:19'),
(46, 10, 'CALIPER / SIGMAT MITUTOYO', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+100700', 'http://202.67.224.137/sgimage/00/s_100700_530-320.jpg', '2014-10-23 14:36:21', '2014-10-23 14:36:21'),
(47, 10, 'ALAT UKUR LASER', NULL, 1, 'http://sumberperkakas.indonetwork.co.id/group+115760', 'http://202.67.224.137/sgimage/60/s_115760_copy7ofpowertoolbosch7.jpg', '2014-10-23 14:36:21', '2014-10-23 14:36:21');
