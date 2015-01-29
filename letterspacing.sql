-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2015 at 12:35 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `prestashop`
--

-- --------------------------------------------------------

--
-- Table structure for table `ps_customtextdesign_product`
--

CREATE TABLE IF NOT EXISTS `ps_customtextdesign_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `alpha` tinyint(1) NOT NULL DEFAULT '0',
  `curve` tinyint(1) NOT NULL DEFAULT '0',
  `letterspace` tinyint(1) NOT NULL COMMENT 'user_added',
  `initial_curve` int(11) NOT NULL DEFAULT '0',
  `initial_letterspace` int(11) NOT NULL COMMENT 'user_added',
  `picker` tinyint(1) NOT NULL DEFAULT '0',
  `upload` tinyint(1) NOT NULL DEFAULT '0',
  `url_upload` tinyint(1) NOT NULL DEFAULT '0',
  `upload_max` int(3) NOT NULL DEFAULT '0',
  `upload_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `colors` text NOT NULL,
  `fonts` text NOT NULL,
  `materials` text NOT NULL,
  `image_groups` text NOT NULL,
  `attributes` text NOT NULL,
  `text_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `image_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `design_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `min_size` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `max_length` int(11) NOT NULL DEFAULT '0',
  `show_price` tinyint(1) NOT NULL DEFAULT '0',
  `hide_colors` tinyint(1) NOT NULL DEFAULT '0',
  `hide_fonts` tinyint(1) NOT NULL DEFAULT '0',
  `hide_materials` tinyint(1) NOT NULL DEFAULT '0',
  `expanded` tinyint(1) NOT NULL DEFAULT '0',
  `id_default_img` int(11) NOT NULL DEFAULT '0',
  `show_btn` tinyint(1) NOT NULL DEFAULT '0',
  `images_first` tinyint(1) NOT NULL DEFAULT '0',
  `show_download_btn` tinyint(1) NOT NULL DEFAULT '0',
  `popup` tinyint(1) NOT NULL DEFAULT '0',
  `use_tax` tinyint(1) NOT NULL DEFAULT '1',
  `colors_all` tinyint(1) NOT NULL DEFAULT '0',
  `fonts_all` tinyint(1) NOT NULL DEFAULT '0',
  `materials_all` tinyint(1) NOT NULL DEFAULT '0',
  `image_groups_all` tinyint(1) NOT NULL DEFAULT '0',
  `attributes_all` tinyint(1) NOT NULL DEFAULT '0',
  `hide_text` tinyint(1) NOT NULL DEFAULT '0',
  `customsize` tinyint(1) NOT NULL DEFAULT '0',
  `customsize_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
  `customsize_minw` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customsize_minh` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customsize_maxw` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customsize_maxh` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customsize_initw` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customsize_inith` decimal(20,2) NOT NULL DEFAULT '0.00',
  `customcolor` tinyint(1) NOT NULL DEFAULT '0',
  `initial_color` varchar(20) NOT NULL DEFAULT '',
  `custom_fields` tinyint(1) NOT NULL DEFAULT '0',
  `disable_drag` tinyint(1) NOT NULL DEFAULT '0',
  `disable_resize` tinyint(1) NOT NULL DEFAULT '0',
  `show_rotator` tinyint(1) NOT NULL DEFAULT '0',
  `extra_btns` tinyint(1) NOT NULL DEFAULT '0',
  `custompicker` tinyint(1) NOT NULL DEFAULT '0',
  `customcolors` text NOT NULL,
  `customcolors_all` tinyint(1) NOT NULL DEFAULT '0',
  `show_stack` tinyint(1) NOT NULL DEFAULT '0',
  `image_fixed` tinyint(1) NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `check_bounds` tinyint(1) NOT NULL DEFAULT '0',
  `required_bounds` tinyint(1) NOT NULL DEFAULT '0',
  `imagecolor` tinyint(1) NOT NULL DEFAULT '0',
  `initial_img_color` varchar(20) NOT NULL DEFAULT '',
  `imagepicker` tinyint(1) NOT NULL DEFAULT '0',
  `imagecolors` text NOT NULL,
  `imagecolors_all` tinyint(1) NOT NULL DEFAULT '0',
  `free_design` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
