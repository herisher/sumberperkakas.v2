alter table `dtb_category` add column `url` varchar(200) DEFAULT NULL AFTER disp_flag;

alter table `dtb_sub_category` 
add column `url` varchar(200) DEFAULT NULL AFTER `disp_flag`,
add column `image_url` varchar(200) DEFAULT NULL AFTER `url`;

alter table `dtb_product` 
add column `url` varchar(200) DEFAULT NULL AFTER `disp_flag`;
