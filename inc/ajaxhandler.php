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

	$add_customization = false;

	/** @var customtextdesign */
	$module = Module::getInstanceByName('customtextdesign');
	$module->deleteOldProducts();
	$id_page_config = Tools::getValue('id_page_config', '');
	$cfg = $module->getConfigKeys($id_page_config);

	function getColor($id_color, $colors)
	{
		foreach ($colors as $color)
			if ($color['id'] == $id_color) return $color;
	}

	function getFont($id_font, $fonts)
	{
		foreach ($fonts as $font)
			if ($font['id'] == $id_font) return $font;
	}

	function getMaterial($id_material, $materials)
	{
		foreach ($materials as $material)
			if ($material['id'] == $id_material) return $material;
	}

	$query = 'SELECT * FROM '._DB_PREFIX_.$module->name.'_color WHERE 1 ORDER BY position ASC';
	$colors = Db::getInstance()->ExecuteS($query);
	$colorcount = count($colors);

	$query = 'SELECT * FROM '._DB_PREFIX_.$module->name.'_font WHERE 1 ORDER BY position ASC';
	$fonts = Db::getInstance()->ExecuteS($query);
	$fontcount = count($fonts);

	$query = 'SELECT * FROM '._DB_PREFIX_.$module->name.'_material WHERE 1 ORDER BY position ASC';
	$materials = Db::getInstance()->ExecuteS($query);
	$materialcount = count($fonts);

	if (Tools::getValue('action') == 'saveproduct')
	{
		$text = Tools::getValue('text');
		$color = getColor((int)Tools::getValue('color'), $colors);
		$iscolor = $color['is_color'];
		$font = getFont((int)Tools::getValue('font'), $fonts);
		$size = (int)Tools::getValue('size');
		$material = getMaterial((int)Tools::getValue('material'), $materials);
		$price = (float)Tools::getValue('price');
		$mirror = (int)Tools::getValue('mirror');
		$width = (float)Tools::getValue('width');
		$height = (float)Tools::getValue('height');
		$width = $width * $size / $height;
		$area = $width * $size / 10000;

		$id_material = isset($material['id']) ? (int)$material['id'] : 0;
		$query = 'SELECT * FROM '._DB_PREFIX_.$module->name.'_material_prices WHERE material_id = '.
		(int)$material['id'].' AND material_size = '.pSQL($size);
		$material_prices_row = Db::getInstance()->ExecuteS($query);
		if (count($material_prices_row))
			$areaprice = (float)$material_prices_row[0]['material_price'];
		else
			$areaprice = (float)$material['price'];
		$unitprice = $area * $areaprice;
		$unitprice += (float)$cfg['base_price'];
		$qty = (int)Tools::getValue('qty');
		$category = (int)Tools::getValue('category');
		$cache = Tools::getValue('cache');
		$cache = "../data/cache/$cache.png";
		$area = Tools::ps_round($area, 2);
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
		$source = 'default';
		$uniqid = uniqid();
		foreach ($languages as $lang)
		{
			$product_name["{$lang['id_lang']}"] = preg_replace('/[<>;=#{}]*/u', '', Tools::substr(str_replace("\n", '', $text), 0, 127));
			$product_link_rewrite["{$lang['id_lang']}"] = Tools::substr($url, 0, 50);
			$iso_lang = $lang['iso_code'];

			$description  = "<strong><span>{$module->translate('Text', $iso_lang, $source)}:</span></strong>$nl
			<span>$ht_text</span><br>";
			if ($iscolor)
				$description .= "<strong><span>{$module->translate('Color', $iso_lang, $source)}:</span></strong>&nbsp;
				<span>{$color['id']} - {$color['name']}</span><br>";
			else
				$description .= "<strong><span>{$module->translate('Texture', $iso_lang, $source)}:</span></strong>&nbsp;
				<span><a target='_blank' href='{$module->getPath()}data/texture/{$color['texture']}'>{$color['id']}&nbsp;
				- {$color['name']}</a></span><br>";

			$description .= "<strong><span>{$module->translate('Font', $iso_lang, $source)}:</span></strong>&nbsp;
			<span>{$font['id']} -&nbsp;
			<img src='{$module->getPath()}inc/preview.php?font={$font['id']}&size=50&type=img&text={$font['name']}&clr=17DAFF'
			style='position: relative;height: 15px;top: 2px;' /> ({$font['name']})</span><br>";
			$description .= "<strong><span>{$module->translate('Material', $iso_lang, $source)}:</span></strong> <span>#
			{$material['id']} - {$material['name']}</span><br>";
			if ($mirror)
				$description .= "<strong><span>{$module->translate('Mirror', $iso_lang, $source)}:</span></strong>&nbsp;
				<span>{$module->translate('yes', $iso_lang, $source)}</span><br>";
			$description .= "<strong><span>{$module->translate('Width', $iso_lang, $source)}:</span></strong> <span>{$width} cm</span><br>";
			$description .= "<strong><span>{$module->translate('Height', $iso_lang, $source)}:</span></strong> <span>$size cm</span><br>";
			$description .= "<strong><span>{$module->translate('Area', $iso_lang, $source)}:</span></strong> <span>$area m²</span><br>";
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
		$product->customizable = 1;
		$product->price = round($unitprice, 2);
		$product->out_of_stock = 0;
		$product->id_tax_rules_group = (int)$cfg['used_tax'];
		$product->weight = 0.001;

		$product->active = 1;
		$product->save();
		$product->updateCategories($product->category, true);
		StockAvailable::setQuantity($product->id, 0, $qty * 100);
		if (isset($cfg['a_carriers']))
		{
			$carriers = explode(',', $cfg['a_carriers']);
			$product->setCarriers($carriers);
		}

		$context = Context::getContext();
		$id_cart = CustomDesign::addCart(true);

		if ($add_customization)
		{
			Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', 1);
			$id_customization_field = $module->addCustomField((int)$product->id);
			$data = array(
				'id_product_attribute' => null,
				'id_address_delivery' => (int)$context->cart->id_address_delivery,
				'id_cart' => $id_cart,
				'id_product' => $product->id,
				'quantity' => $qty,
				'in_cart' => 1
			);
			Db::getInstance()->insert('customization', $data);
			$id_customization = Db::getInstance()->Insert_ID();

			$rwidth = Tools::ps_round((float)Tools::getValue('rwidth'));
			$summary = "<br>
			<b>{$module->l('Text')}:</b> $text <br>
			<b>{$module->l('Font')}:</b> {$font['name']} <br>
			<b>{$module->l('Material')}:</b> {$material['name']} <br>
			<b>{$module->l('Color')}:</b> {$color['name']} <br>";

			$summary .= "<b>{$module->l('Dimensions')}:</b> {$module->l('Height')} $size cm X {$module->l('Width')} $rwidth cm<br>";

			$data = array(
				'id_customization' => (int)$id_customization,
				'type' => Product::CUSTOMIZE_TEXTFIELD,
				'index' => (int)$id_customization_field,
				'value' => $summary
			);

			$sql = 'ALTER TABLE  `'._DB_PREFIX_.'customized_data` CHANGE  `value`  `value` VARCHAR( 1000 ) NOT NULL ;';
			Db::getInstance()->execute($sql);
			Db::getInstance()->insert('customized_data', $data);
		}

		$data = array(
			'id_product' => $product->id,
			'text' => $text,
			'color' => $color['id'],
			'font' => $font['id'],
			'material' => $material['id'],
			'size' => $size,
			'mirror' => (int)$mirror,
			'id_page_config' => $id_page_config,
			'uniqid' => $uniqid
		);

		Db::getInstance()->insert($module->name.'_design', $data);

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
				if (!ImageManager::resize($cache, $new_path.'-'.Tools::stripslashes($image_type['name']).'.'.
					$image->image_format, $image_type['width'], $image_type['height'], $image->image_format))
					exit(Tools::jsonEncode(array('error' => Tools::displayError('An error occurred while copying image:').
						' '.Tools::stripslashes($image_type['name']))));
			}
			$image->update();
		}

		exit(Tools::jsonEncode(array('id_product'=>$product->id)));
	}

?>
