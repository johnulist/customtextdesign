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
function upgrade_module_4_0_15($module)
{
	$queries = array(
		'ALTER TABLE `'._DB_PREFIX_.'customtextdesign_product` ADD  `custom_fields` tinyint(1) NOT NULL;',
		'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customtextdesign_custom_field` (
		`id_custom_field` int(11) NOT NULL AUTO_INCREMENT,
		`id_product` int(11) NOT NULL,
		`id_image` int(11) NOT NULL,
		`x` int(11) NOT NULL,
		`y` int(11) NOT NULL,
		`w` int(11) NOT NULL,
		`h` int(11) NOT NULL,
		`id` varchar(50) NOT NULL,
		PRIMARY KEY (`id_custom_field`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;',
		'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customtextdesign_custom_field_trans` (
		`id_product` int(10) unsigned NOT NULL,
		`id_custom_field` int(11) unsigned NOT NULL,
		`id_lang` int(10) unsigned NOT NULL,
		`label` varchar(100) NOT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;'
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