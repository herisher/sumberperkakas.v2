alter table `dtb_category` add column `url` varchar(200) DEFAULT NULL AFTER disp_flag;

alter table `dtb_sub_category` 
add column `url` varchar(200) DEFAULT NULL AFTER `disp_flag`,
add column `image_url` varchar(200) DEFAULT NULL AFTER `url`;

alter table `dtb_product` 
add column `url` varchar(200) DEFAULT NULL AFTER `disp_flag`;

alter table `dtb_admin` add column `last_login_date` datetime DEFAULT NULL AFTER `password`;
alter table `dtb_admin` drop column `lastlogin_date`;

CREATE TABLE `dtb_image_slider` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `image_url` varchar(200) DEFAULT NULL,
 `detail_image_url` varchar(200) DEFAULT NULL,
 `disp_order` int(11) DEFAULT NULL,
 `disp_flag` tinyint(4) DEFAULT '1',
 `update_date` datetime DEFAULT NULL,
 `create_date` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
