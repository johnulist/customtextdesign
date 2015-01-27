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

	$ctd_product = array(
		'upload' => 1,
		'upload_max' => 10
	);

	if (! $ctd_product['upload'])
		exit('{"error":"'.$module->l('Sorry! Uploads are not permitted for this product').'"}');

	$field = 'user_image';
	$dir = 'uploads';
	$ext = 'jpg|jpeg|png|gif';
	$size = 1024 * 1000;
	$str_size = '1 MB';

	switch ($ctd_product['upload_max'])
	{
		case 0:
			$size = 1024 * 500;
			$str_size = '500 KB';
			break;
		default:
			$size = 1024 * 1000 * (int)$ctd_product['upload_max'];
			$str_size = $ctd_product['upload_max'].' MB';
			break;
	}

	if (!isset($_FILES[$field]))
		exit('{"error":"'.$module->l('An error has occured with the file upload.').'"}');

	$filename = $_FILES[$field]['name'];
	if (empty($filename))
		exit('{"error":"'.$module->l('An error has occured with the file upload.').'"}');

	if (!preg_match('/.+\.('.$ext.')$/', Tools::strtolower($filename)))
		exit('{"error":"'.$module->l('Sorry! This file type is not allowed.').'"}');

	if ($_FILES[$field]['size'] > $size)
		exit('{"error":"'.$module->l('Please upload a file with size less than').' '.$str_size.'"}');

	$extension = pathinfo($filename, PATHINFO_EXTENSION);
	$filename = time().'_'.rand().'.'.Tools::strtolower($extension);

	$path = dirname(__FILE__).'/../data/'.$dir.'/'.$filename;
	if (!move_uploaded_file($_FILES[$field]['tmp_name'], $path))
	exit('{"error":"'.$module->l('An error has occured with the file upload.').'"}');

	$image_type = $module->exifImageType($path);
	$img = ImageManager::create($image_type, $path);

	if (!$img)
		exit('{"error":"'.$module->l('Image format not recognized.').'"}');

	/* Delete old unused images */
	$module->deleteOldUploads();

	exit('{"error":"0","filename":"'.$filename.'"}');

?>
