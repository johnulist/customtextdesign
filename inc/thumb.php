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

	include_once('../../../config/config.inc.php');
	include_once('../../../init.php');
	include_once('resize-class.php');

	$cache = 1;

	$module = Module::getInstanceByName('customtextdesign');
	$module_path = $module->getPath();
	$hash = md5(serialize($_GET));
	$cacheimage = 'data/cache/'.$hash.'.png';

	if ($cache && file_exists('../'.$cacheimage))
	{
		Tools::redirect(_PS_BASE_URL_.$module_path.$cacheimage);
		exit();
	}

	$cacheimage = '../'.$cacheimage;

	$img = Tools::getValue('img');
	$w = (int)Tools::getValue('w');
	$h = (int)Tools::getValue('h');
	$m = Tools::getValue('m');

	$path = $module->getPath();
	$imgpath = str_replace($path, '../', $img);

	if (empty($imgpath) || !file_exists($imgpath))
	{
		Tools::redirect(_PS_BASE_URL_.$module_path.'img/pixel.png');
		exit();
	}

	list($width,$height) = getimagesize($imgpath);
	if (!$w || $w > $width) $w = $width;
	if (!$h || $h > $height) $h = $height;
	if (!$m) $m = 'exact';

	$resize_obj = new ResizeImg($imgpath);
	$resize_obj->resizeImage($w, $h, $m);
	$resize_obj->saveImage($cacheimage, 100);

	header('Content-type: image/png');
	if (! $cache)
		readfile($cacheimage);
	else
		Tools::redirect(_PS_BASE_URL_.$module_path.str_replace('../', '', $cacheimage));
	exit();
?>
