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
function upgrade_module_4_0_2($module)
{
	$sql = 'CREATE TABLE IF NOT EXISTS `__PREFIX_mask2` (
	  `id_mask` int(11) NOT NULL AUTO_INCREMENT,
	  `id_product` int(11) NOT NULL,
	  `id_image` int(11) NOT NULL,
	  `image` varchar(100) NOT NULL,
	  PRIMARY KEY (`id_mask`)
	) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';

	$sql = str_replace('__PREFIX', _DB_PREFIX_.$module->name, $sql);
	$sql = str_replace('_MYSQL_ENGINE_', _MYSQL_ENGINE_, $sql);

	return Db::getInstance()->Execute(trim($sql));
}