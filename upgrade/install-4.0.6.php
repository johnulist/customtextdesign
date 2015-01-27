<?php
	/**
	* 2010-2014 Tuni-Soft
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Academic Free License (AFL 3.0)
	* It is available through the world-wide-web at this URL:
	* http://opensource.org/licenses/afl-3.0.php
	* If you did not receive a copy of the license and are unable to
	* obtain it through the world-wide-web, please send an email
	* to tunisoft.solutions@gmail.com so we can send you a copy immediately.
	*
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade this module to newer
	* versions in the future. If you wish to customize the module for your
	* needs please refer to
	* http://doc.prestashop.com/display/PS15/Overriding+default+behaviors
	* for more information.
	*
	* @author    Tunis-Soft <tunisoft.solutions@gmail.com>
	* @copyright 2010-2014 Tuni-Soft
	* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*/

if (!defined('_PS_VERSION_'))
	exit;
function upgrade_module_4_0_6($module)
{
	$queries = array(
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_custom_product` ADD  `product_width` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_custom_product` ADD  `product_height` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_custom_product` ADD  `product_color` varchar(20) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_custom_product` ADD  `custom_price` decimal(20,6) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `hide_text` TINYINT(1) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize` TINYINT(1) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_price` decimal(20,6) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_minw` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_minh` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_maxw` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_maxh` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_initw` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customsize_inith` decimal(20,2) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `customcolor` TINYINT(1) NOT NULL;',
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `initial_color` varchar(20) NOT NULL;',
		'INSERT INTO `'._DB_PREFIX_."customtextdesign_product` (`id_product`, `active`, `alpha`, `curve`, `initial_curve`
		, `picker`, `upload`, `upload_max`, `upload_price`, `colors`, `fonts`, `materials`, `image_groups`, `attributes`
		, `text_price`, `image_price`, `design_price`, `min_size`, `max_length`, `show_price`, `hide_colors`, `hide_fonts`
		, `hide_materials`, `expanded`, `id_default_img`, `show_btn`, `images_first`, `show_download_btn`, `popup`, `use_tax`
		, `colors_all`, `fonts_all`, `materials_all`, `image_groups_all`, `attributes_all`, `url_upload`, `hide_text`
		, `customsize`, `customsize_price`, `customsize_minw`, `customsize_minh`, `customsize_maxw`, `customsize_maxh`
		, `customsize_initw`, `customsize_inith`, `customcolor`, `initial_color`)
		VALUES (0, 0, 1, 1, 0, 1, 1, 2, '100.000000', '', '', '', '', '', '0.000000', '0.000000', '0.000000', '0.000000'
		, 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0, '0.000000', '1.00', '1.00', '0.00', '0.00', '0.00', '0.00', 0, '#11daf5');"
	);

	$success = true;

	$success &= $module->uninstallOverrides();
	$success &= $module->installOverrides();

	$errors = '';
	foreach ($queries as $query)
	{
		try
		{
			Db::getInstance()->Execute($query);
		}
		catch(Exception $e)
		{
			$errors .= $e->getMessage().'<br>';
		}
	}

	return $success;
}