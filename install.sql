CREATE TABLE IF NOT EXISTS `__PREFIX_color` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '',
`is_color` tinyint(4) NOT NULL DEFAULT 1,
`color` varchar(20) NOT NULL DEFAULT '',
`alpha` int(11) NOT NULL DEFAULT 0,
`texture` varchar(100) NOT NULL DEFAULT '',
`position` int(11) NOT NULL DEFAULT 0,
`displayed` tinyint(4) NOT NULL DEFAULT 1,
PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `__PREFIX_color` (`name`, `is_color`, `color`, `alpha`, `texture`, `position`, `displayed`) VALUES
('Red', 1, '#FF0000', 0, '', 1, 1),
('Wood', 0, '', 0, 'wood.jpg', 2, 1),
('Green', 1, '#00FF00', 32, '', 3, 1),
('Aluminium', 0, '', 0, 'aluminum.jpg', 4, 1),
('Cosmos', 0, '', 0, 'cosmos.jpg', 5, 1),
('Gold', 0, '', 0, 'gold.jpg', 6, 1),
('Asphalt', 0, '', 0, 'asphalt.jpg', 7, 1),
('Multicolors', 0, '', 0, 'colors.jpg', 8, 1),
('Semi-Transparent', 1, '#FF5459', 64, '', 9, 1),
('Vivid', 1, '#24E4FF', 0, '', 10, 1),
('Grass', 0, '', 0, 'grass.jpg', 11, 1);

CREATE TABLE IF NOT EXISTS `__PREFIX_font` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '',
`file` varchar(100) NOT NULL DEFAULT '',
`position` int(11) NOT NULL DEFAULT 0,
`displayed` tinyint(4) NOT NULL DEFAULT 1,
PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `__PREFIX_font` (`name`, `file`, `position`, `displayed`) VALUES
('Base', 'base.ttf', 1, 1),
('28 Days Later', '28-days-later.ttf', 2, 1),
('Graffiti', 'graffiti.ttf', 3, 1),
('Gub', 'gub.ttf', 4, 1),
('Ketchum', 'ketchum.ttf', 5, 1),
('Pulse', 'pulse.ttf', 6, 1),
('Disko', 'disko.ttf', 7, 1);


CREATE TABLE IF NOT EXISTS `__PREFIX_material` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '',
`price` decimal(20,6) NOT NULL DEFAULT 0,
`file` varchar(100) NOT NULL DEFAULT '',
`position` int(11) NOT NULL DEFAULT 0,
`displayed` tinyint(4) NOT NULL DEFAULT 1,
PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `__PREFIX_material` (`name`, `price`, `file`, `position`, `displayed`) VALUES
('Vinyl', 250, '', 1, 1),
('Print', 150, '', 2, 1),
('Engraving', 500, '', 2, 1);


CREATE TABLE IF NOT EXISTS `__PREFIX_material_prices` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`material_id` int(11) NOT NULL DEFAULT 0,
`material_size` int(11) NOT NULL DEFAULT 0,
`material_price` decimal(20,6) NOT NULL DEFAULT 0,
PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `__PREFIX_group` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL DEFAULT '',
`file` varchar(100) NOT NULL DEFAULT '',
`position` int(11) NOT NULL DEFAULT 0,
`displayed` tinyint(4) NOT NULL DEFAULT 1,
`colorize` tinyint(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `__PREFIX_group` (`id`,`name`, `file`, `position`, `displayed`) VALUES
(1, 'Images', 'light.png', 1, 1),
(2, 'Icons', 'game.png', 2, 1);

CREATE TABLE IF NOT EXISTS `__PREFIX_image` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`id_group` int(11) NOT NULL DEFAULT 0,
`name` varchar(50) NOT NULL DEFAULT '',
`price` decimal(20,6) NOT NULL DEFAULT 0,
`file` varchar(100) NOT NULL DEFAULT '',
`position` int(11) NOT NULL DEFAULT 0,
`displayed` tinyint(4) NOT NULL DEFAULT 1,
`quantity` int(11) NOT NULL DEFAULT -1,
PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `__PREFIX_image` (`id_group`, `name`, `price`, `file`, `position`, `displayed`) VALUES
(1, 'Android', '250.000000', 'android.png', 1, 1),
(2, 'Angry-bird', '250.000000', 'angry-bird.png', 2, 1),
(1, 'Colors', '250.000000', 'colors.png', 3, 1),
(2, 'Danger', '250.000000', 'danger.png', 4, 1),
(2, 'Fire', '250.000000', 'fire.png', 5, 1),
(2, 'Fist', '250.000000', 'fist.png', 6, 1),
(1, 'Fists', '250.000000', 'fists.png', 7, 1),
(2, 'Game', '250.000000', 'game.png', 8, 1),
(2, 'Laser', '250.000000', 'laser.png', 9, 1),
(2, 'Love', '250.000000', 'love.png', 10, 1),
(2, 'Minion1', '250.000000', 'minion1.png', 11, 1),
(2, 'Minion2', '250.000000', 'minion2.png', 12, 1),
(2, 'Minion3', '250.000000', 'minion3.png', 13, 1),
(2, 'Minion4', '250.000000', 'minion4.png', 14, 1),
(2, 'Minion5', '250.000000', 'minion5.png', 15, 1),
(2, 'Superman', '250.000000', 'superman.png', 16, 1),
(2, 'Target', '250.000000', 'target.png', 17, 1),
(1, 'Tiger2', '250.000000', 'tiger.png', 18, 1),
(1, 'Tiger2', '250.000000', 'light.png', 19, 1);

CREATE TABLE IF NOT EXISTS `__PREFIX_page_config` (
  `id_page_config` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(100) NOT NULL DEFAULT '',
  `colors` varchar(500) NOT NULL DEFAULT '',
  `fonts` varchar(500) NOT NULL DEFAULT '',
  `materials` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_page_config`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_custom_product` (
  `id_custom_product` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_attribute` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `id_cart` int(11) NOT NULL DEFAULT 0,
  `id_customer` int(11) NOT NULL DEFAULT 0,
  `id_guest` int(11) NOT NULL DEFAULT 0,
  `hash` varchar(50) NOT NULL DEFAULT '',
  `width` int(11) NOT NULL DEFAULT 0,
  `price` decimal(20,6) NOT NULL DEFAULT 0,
  `preview` varchar(50) NOT NULL DEFAULT '',
  `uniqid` varchar(13) NOT NULL DEFAULT '',
  `visible` tinyint(4) NOT NULL DEFAULT 1,
  `product_width` decimal(20,2) NOT NULL DEFAULT 0,
  `product_height` decimal(20,2) NOT NULL DEFAULT 0,
  `product_color` varchar(20) NOT NULL DEFAULT '',
  `custom_price` decimal(20,6) NOT NULL DEFAULT 0,
  `version` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_custom_product`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_custom_item` (
  `id_custom_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_custom_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `color` int(11) NOT NULL DEFAULT 0,
  `clr` varchar(10) NOT NULL DEFAULT '',
  `font` int(11) NOT NULL DEFAULT 0,
  `material` int(11) NOT NULL DEFAULT 0,
  `mirror` tinyint(1) NOT NULL DEFAULT 0,
  `center` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(10) NOT NULL DEFAULT '',
  `id` varchar(50) NOT NULL DEFAULT '',
  `x` int(11) NOT NULL DEFAULT 0,
  `y` int(11) NOT NULL DEFAULT 0,
  `scalex` decimal(20,6) NOT NULL DEFAULT 1,
  `scaley` decimal(20,6) NOT NULL DEFAULT 1,
  `angle` decimal(20,6) NOT NULL DEFAULT 0,
  `width` int(11) NOT NULL DEFAULT 0,
  `height` int(11) NOT NULL DEFAULT 0,
  `alpha` int(11) NOT NULL DEFAULT 0,
  `curve` int(11) NOT NULL DEFAULT 0,
  `price` decimal(20,6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_custom_item`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `alpha` tinyint(1) NOT NULL DEFAULT 0,
  `curve` tinyint(1) NOT NULL DEFAULT 0,
  `initial_curve` int(11) NOT NULL DEFAULT 0,
  `picker` tinyint(1) NOT NULL DEFAULT 0,
  `upload` tinyint(1) NOT NULL DEFAULT 0,
  `url_upload` tinyint(1) NOT NULL DEFAULT 0,
  `upload_max` int(3) NOT NULL DEFAULT 0,
  `upload_price` decimal(20,6) NOT NULL DEFAULT 0,
  `colors` text NOT NULL,
  `fonts` text NOT NULL,
  `materials` text NOT NULL,
  `image_groups` text NOT NULL,
  `attributes` text NOT NULL,
  `text_price` decimal(20,6) NOT NULL DEFAULT 0,
  `image_price` decimal(20,6) NOT NULL DEFAULT 0,
  `design_price` decimal(20,6) NOT NULL DEFAULT 0,
  `min_size` decimal(20,6) NOT NULL DEFAULT 0,
  `max_length` int(11) NOT NULL DEFAULT 0,
  `show_price` tinyint(1) NOT NULL DEFAULT 0,
  `hide_colors` tinyint(1) NOT NULL DEFAULT 0,
  `hide_fonts` tinyint(1) NOT NULL DEFAULT 0,
  `hide_materials` tinyint(1) NOT NULL DEFAULT 0,
  `expanded` tinyint(1) NOT NULL DEFAULT 0,
  `id_default_img` int(11) NOT NULL DEFAULT 0,
  `show_btn` tinyint(1) NOT NULL DEFAULT 0,
  `images_first` tinyint(1) NOT NULL DEFAULT 0,
  `show_download_btn` tinyint(1) NOT NULL DEFAULT 0,
  `popup` tinyint(1) NOT NULL DEFAULT 0,
  `use_tax` tinyint(1) NOT NULL DEFAULT 1,
  `colors_all` tinyint(1) NOT NULL DEFAULT 0,
  `fonts_all` tinyint(1) NOT NULL DEFAULT 0,
  `materials_all` tinyint(1) NOT NULL DEFAULT 0,
  `image_groups_all` tinyint(1) NOT NULL DEFAULT 0,
  `attributes_all` tinyint(1) NOT NULL DEFAULT 0,
  `hide_text` tinyint(1) NOT NULL DEFAULT 0,
  `customsize` tinyint(1) NOT NULL DEFAULT 0,
  `customsize_price` decimal(20,6) NOT NULL DEFAULT 0,
  `customsize_minw` decimal(20,2) NOT NULL DEFAULT 0,
  `customsize_minh` decimal(20,2) NOT NULL DEFAULT 0,
  `customsize_maxw` decimal(20,2) NOT NULL DEFAULT 0,
  `customsize_maxh` decimal(20,2) NOT NULL DEFAULT 0,
  `customsize_initw` decimal(20,2) NOT NULL DEFAULT 0,
  `customsize_inith` decimal(20,2) NOT NULL DEFAULT 0,
  `customcolor` tinyint(1) NOT NULL DEFAULT 0,
  `initial_color` varchar(20) NOT NULL DEFAULT '',
  `custom_fields` tinyint(1) NOT NULL DEFAULT 0,
  `disable_drag` tinyint(1) NOT NULL DEFAULT 0,
  `disable_resize` tinyint(1) NOT NULL DEFAULT 0,
  `show_rotator` tinyint(1) NOT NULL DEFAULT 0,
  `extra_btns` tinyint(1) NOT NULL DEFAULT 0,
  `custompicker` tinyint(1) NOT NULL DEFAULT 0,
  `customcolors` text NOT NULL,
  `customcolors_all` tinyint(1) NOT NULL DEFAULT 0,
  `show_stack` tinyint(1) NOT NULL DEFAULT 0,
  `image_fixed` tinyint(1) NOT NULL DEFAULT 0,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `check_bounds` tinyint(1) NOT NULL DEFAULT 0,
  `required_bounds` tinyint(1) NOT NULL DEFAULT 0,
  `imagecolor` tinyint(1) NOT NULL DEFAULT 0,
  `initial_img_color` varchar(20) NOT NULL DEFAULT '',
  `imagepicker` tinyint(1) NOT NULL DEFAULT 0,
  `imagecolors` text NOT NULL,
  `imagecolors_all` tinyint(1) NOT NULL DEFAULT 0,
  `free_design` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `__PREFIX_product` (`id_product`, `active`, `alpha`, `curve`, `initial_curve`, `picker`, `upload`, `upload_max`, `upload_price`, `colors`, `fonts`, `materials`, `image_groups`, `attributes`, `text_price`, `image_price`, `design_price`, `min_size`, `max_length`, `show_price`, `hide_colors`, `hide_fonts`, `hide_materials`, `expanded`, `id_default_img`, `show_btn`, `images_first`, `show_download_btn`, `popup`, `use_tax`, `colors_all`, `fonts_all`, `materials_all`, `image_groups_all`, `attributes_all`, `url_upload`, `hide_text`, `customsize`, `customsize_price`, `customsize_minw`, `customsize_minh`, `customsize_maxw`, `customsize_maxh`, `customsize_initw`, `customsize_inith`, `customcolor`, `initial_color`, `custom_fields`, `disable_drag`, `disable_resize`, `show_rotator`, `extra_btns`,`custompicker`,`customcolors`,`customcolors_all`, `show_stack`, `image_fixed`, `required`, `check_bounds`, `required_bounds`, `imagecolor`, `initial_img_color`, `imagepicker`, `imagecolors`, `imagecolors_all`, `free_design`)
VALUES (0, 0, 1, 1, 0, 1, 1, 2, '1000.000000', '', '', '', '', '', '0.000000', '0.000000', '0.000000', '0.000000', 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, '0.000000', '1.00', '1.00', '0.00', '0.00', '0.00', '0.00', 0, '#11daf5', 0, 0, 0, 1, 0, 1, '', 1, 1, 0, 0, 0, 0, 0, '#11daf5', 0, '', 1, 0);

CREATE TABLE IF NOT EXISTS `__PREFIX_product_trans` (
  `id_product` int(10) unsigned NOT NULL DEFAULT 0,
  `id_lang` int(10) unsigned NOT NULL DEFAULT 0,
  `title` varchar(100) NOT NULL DEFAULT '',
  `text_init` varchar(100) NOT NULL,
  `instructions` text NOT NULL
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

INSERT INTO `__PREFIX_product_trans` (`id_product`, `id_lang`, `title`, `text_init`, `instructions`) VALUES
(0, 1, '', 'Your text here', ''),
(0, 2, '', 'Votre texte ici', '');

CREATE TABLE IF NOT EXISTS `__PREFIX_measure` (
  `id_measure` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `size` int(11) NOT NULL DEFAULT 0,
  `width` int(11) NOT NULL DEFAULT 0,
  `x` int(11) NOT NULL DEFAULT 0,
  `y` int(11) NOT NULL DEFAULT 0,
  `x_origin` int(11) NOT NULL DEFAULT 0,
  `y_origin` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_measure`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_overlay` (
  `id_overlay` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_overlay`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_mask` (
  `id_mask` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_mask`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_mask2` (
  `id_mask` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_mask`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_replace` (
  `id_replace` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_replace`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `__PREFIX_field` (
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_customization_field` int(11) NOT NULL DEFAULT 0
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `__PREFIX_custom_field` (
  `id_custom_field` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `id_image` int(11) NOT NULL DEFAULT 0,
  `x` int(11) NOT NULL DEFAULT 0,
  `y` int(11) NOT NULL DEFAULT 0,
  `w` int(11) NOT NULL DEFAULT 0,
  `h` int(11) NOT NULL DEFAULT 0,
  `id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_custom_field`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `__PREFIX_custom_field_trans` (
  `id_product` int(10) unsigned NOT NULL DEFAULT 0,
  `id_custom_field` int(11) unsigned NOT NULL DEFAULT 0,
  `id_lang` int(10) unsigned NOT NULL DEFAULT 0,
  `label` varchar(100) NOT NULL DEFAULT ''
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `__PREFIX_design` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `color` int(11) NOT NULL DEFAULT 0,
  `font` int(11) NOT NULL DEFAULT 0,
  `material` int(11) NOT NULL DEFAULT 0,
  `size` int(11) NOT NULL DEFAULT 0,
  `mirror` tinyint(1) NOT NULL DEFAULT 0,
  `id_page_config` int(11) NOT NULL DEFAULT 0,
  `uniqid` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `__PREFIX_customization` (
  `id_customization` int(11) NOT NULL DEFAULT 0
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1;