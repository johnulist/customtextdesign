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

	$token = Tools::getValue('token');
	$front_token = Tools::getToken(false);
	if ($front_token != $token)
	{
		exit( Tools::jsonEncode(array(
			'authorized'=> 0
		)));
	}

	$module = Module::getInstanceByName('customtextdesign');
	$module->deleteOldProducts();
	$uri = $module->getPath();
	$cfg = $module->getConfigKeys();

	function getMaterial($id_material, $materials)
	{
		foreach ($materials as $material)
			if ($material['id'] == $id_material) return $material;
	}

	$query = 'SELECT * FROM '._DB_PREFIX_.$module->name.'_material WHERE 1 ORDER BY position ASC';
	$materials = Db::getInstance()->ExecuteS($query);
	$materialcount = count($materials);

	if (Tools::getValue('action') == 'saveproduct')
	{
		$text = Tools::getValue('text');
		$size = (int)Tools::getValue('size');
		$material = getMaterial((int)Tools::getValue('material'), $materials);
		$upload = Tools::getValue('upload');
		$price = (float)Tools::getValue('price');
		$width = (float)Tools::getValue('width');
		$height = (float)Tools::getValue('height');
		$area = $width * $height / 10000;

		$id_material = isset($material['id']) ? (int)$material['id'] : 0;
		$areaprice = (float)$material['price'];

		$unitprice = $area * $areaprice;
		$qty = (int)Tools::getValue('qty');
		$category = (int)Tools::getValue('category');
		$cache = Tools::getValue('cache');
		$cache = '../img/custom.png';
		$area = Tools::ps_round($area, 4);
		$text = 'Produit perso';
		$url = Tools::str2url($text);
		$width = Tools::ps_round($width, 2);

		$ht_text = Tools::htmlentitiesUTF8($text);
		$nl = ' ';
		if (strpos($ht_text, "\n") !== false)
		{
			$nl = '<br />';
			$ht_text = nl2br($ht_text);
		}

		$id_lang = Context::getContext()->cookie->id_lang;
		$languages = Language::getLanguages();

		$product = new Product(0);
		$product_name = array();
		$product_description = array();
		$product_link_rewrite = array();
		$source = 'ajaxhandler';

		foreach ($languages as $lang)
		{
			$product_name["{$lang['id_lang']}"] = preg_replace('/[<>;=#{}]*/u', '', Tools::substr(str_replace("\n", '', $text), 0, 127));
			$product_link_rewrite["{$lang['id_lang']}"] = Tools::substr($url, 0, 50);
			$iso_lang = $lang['iso_code'];

			$description  = '';
			$description .= "<strong><span>{$module->_translate('Matière', $iso_lang, $source)}:</span></strong>&nbsp;
			<span>{$material['id']} - {$material['name']}</span><br>";
			$description .= "<strong><span>{$module->_translate('Image', $iso_lang, $source)}:</span></strong>&nbsp;
			<span><a href='{$uri}/data/uploads/{$upload}' target='_blank'><img style='height:30px' src='{$uri}/data/uploads/{$upload}'/></a></span><br>";
			$description .= "<strong><span>{$module->_translate('Width', $iso_lang, $source)}:</span></strong>&nbsp;
			<span>{$width} cm</span><br>";
			$description .= "<strong><span>{$module->_translate('Height', $iso_lang, $source)}:</span></strong>&nbsp;
			<span>{$height} cm</span><br>";
			$description .= "<strong><span>{$module->_translate('Area', $iso_lang, $source)}:</span></strong>&nbsp;
			<span>{$area} m²</span><br>";
			$product_description["{$lang['id_lang']}"] = $description;
		}

		$product->name = $product_name;
		$product->id_category_default = $category;
		$product->category = array($category);
		$product->link_rewrite = $product_link_rewrite;
		$product->description = $product_description;
		$product->quantity = $qty;
		$product->on_sale = 0;
		$product->available_for_order = 1;
		$product->visibility = 'none';
		$product->price = round($unitprice, 2);
		$product->out_of_stock = 0;
		$product->id_tax_rules_group = (int)$cfg['used_tax'];

		$product->active = 1;
		$product->save();
		$product->updateCategories($product->category, true);
		StockAvailable::setQuantity($product->id, 0, $qty * 100);


		$image = new Image();
		$image->id_product = (int)$product->id;
		$image->position = Image::getHighestPosition($product->id) + 1;
		$image->cover = 1;
		if (!$image->add())
			exit(Tools::jsonEncode(array('error' => Tools::displayError('Error while creating additional image'))));
		else
		{
			$new_path = $image->getPathForCreation();
			$cache = realpath($cache);

			ImageManager::resize($cache, $new_path.'.'.$image->image_format);
			$images_types = ImageType::getImagesTypes('products');
			foreach ($images_types as $image_type)
			{
				if (!ImageManager::resize($cache, $new_path.'-'.Tools::stripslashes($image_type['name']).'.'.$image->image_format,
					$image_type['width'], $image_type['height'], $image->image_format))
					exit(Tools::jsonEncode(array('error' =>
						Tools::displayError('An error occurred while copying image:').' '.Tools::stripslashes($image_type['name']))));
			}
			$image->update();
		}

		exit(Tools::jsonEncode(array('id_product' => $product->id)));
	}

?>