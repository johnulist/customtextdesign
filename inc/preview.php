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

	require_once(dirname(__FILE__).'/../../../config/config.inc.php');
	$module = Module::getInstanceByName('customtextdesign');

	if (Tools::getIsset('customcolor'))
	{
		$data = array();
		$data['customcolor'] = Tools::getValue('customcolor');
		$data['id_product'] = (int)Tools::getValue('id_product');
		$data['id_image'] = (int)Tools::getValue('id_image');
		$output = (int)Tools::getValue('output', 1);
		CustomImage::colorize($data, $output);
		exit();
	}

	if (Tools::getIsset('imagecolor'))
	{
		$data = array();
		$data['imagecolor'] = Tools::getValue('imagecolor');
		$data['image_src'] = Tools::getValue('image_src');
		$output = (int)Tools::getValue('output', 1);
		CustomImage::colorizeImage($data, $output);
		exit();
	}

	$data = array();
	$data['text'] = Tools::getValue('text');
	$data['font'] = (int)Tools::getValue('font');
	$data['color'] = (int)Tools::getValue('color');
	$data['clr'] = Tools::getValue('clr');
	$data['alpha'] = (int)Tools::getValue('alpha');
	$data['ignore_space'] = (int)Tools::getValue('ignore_space');
	$data['mirror'] = (int)Tools::getValue('mirror');
	$data['center'] = (int)Tools::getValue('center');
	$data['size'] = (int)Tools::getValue('size');
	$data['type'] = Tools::getValue('type');
	$data['forpanel'] = (int)Tools::getValue('forpanel');
	$data['curve'] = (int)Tools::getValue('curve');
	$data['letterspace'] = (int)Tools::getValue('letterspace');

	CustomImage::preview($data);
?>