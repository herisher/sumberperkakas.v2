DROP TABLE IF EXISTS `dtb_product`;

CREATE TABLE `dtb_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `sub_category_id` bigint(20) unsigned NOT NULL,
  `item_number` varchar(16) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `disp_flag` tinyint(4) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `promo_price` int(11) DEFAULT NULL,
  `promo_period_start` date DEFAULT NULL,
  `promo_period_end` date DEFAULT NULL,
  `image_url1` varchar(200) NOT NULL,
  `image_url2` varchar(200) NOT NULL,
  `image_url3` varchar(200) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dtb_brand`;

CREATE TABLE `dtb_brand` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disp_flag` tinyint(4) DEFAULT '1',
  `image_url` varchar(200) DEFAULT NULL,
  `thumb_url` varchar(200) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dtb_category`;

CREATE TABLE `dtb_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT  NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dtb_sub_category`;

CREATE TABLE `dtb_sub_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `disp_order` int(11) DEFAULT  NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dtb_admin`;

CREATE TABLE `dtb_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `account` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `lastlogin_date` datetime DEFAULT NULL, 
  `update_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dtb_news`;

CREATE TABLE `dtb_news` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;