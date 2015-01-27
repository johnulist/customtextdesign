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
	/** @var customtextdesign */
	$module = Module::getInstanceByName('customtextdesign');

	$token 			= Tools::getValue('token');
	$action 		= Tools::getValue('action');
	$id_product 	= (int)Tools::getValue('id_product');
	$id_attribute 	= (int)Tools::getValue('id_attribute');
	$quantity 		= (int)Tools::getValue('quantity', 1);
	$id_image 		= (int)Tools::getValue('id_image');
	$id_custom_product	= Tools::getValue('id_custom_product');
	$hash 			= Tools::getValue('hash');
	$panel_width	= (float)Tools::getValue('width');
	$custom_width 	= (float)Tools::getValue('custom_width', 0);
	$custom_height 	= (float)Tools::getValue('custom_height', 0);
	$custom_color 	= Tools::getValue('custom_color', '');

	$front_token = Tools::getToken(false);
	if ($front_token != $token)
	{
		exit(Tools::jsonEncode(array(
			'error'=> 'noauth'
		)));
	}

	if ($action == 'remove_custom_product')
	{
		$custom_product = $module->getCustomProduct(0, $id_custom_product);
		if ($custom_product)
		{
			Db::getInstance()->delete($module->name.'_custom_product', 'uniqid = "'.$id_custom_product.'"');
			Db::getInstance()->delete($module->name.'_custom_item', 'id_custom_product = '.(int)$custom_product['id_custom_product']);

			$id_customization_field = $module->addCustomField($custom_product['id_product']);
			$sql = new DbQuery();
			$sql->from('customized_data');
			$sql->where('`index` = '.(int)$id_customization_field." AND `value`='[".(int)$custom_product['id_custom_product']."]'");
			$data = Db::getInstance()->getRow($sql);

			if ($data)
			{
				$id_customization = (int)$data['id_customization'];
				DB::getInstance()->delete('customization', 'id_customization = '.$id_customization);
				DB::getInstance()->delete('customized_data', 'id_customization = '.$id_customization);
			}
		}
		exit(Tools::jsonEncode(array(
			'success'=> '1'
		)));
	}

	if ($action == 'addcart_custom_product')
	{
		$custom_product = $module->getCustomProduct(0, $id_custom_product);
		if ($custom_product)
		{
			$id_customization_field = $module->addCustomField($custom_product['id_product']);
			$sql = new DbQuery();
			$sql->from('customized_data', 'cd');
			$sql->innerJoin('customization', 'cc', 'cc.id_customization = cd.id_customization');
			$sql->where('cd.`index` = '.(int)$id_customization_field." AND cd.`value`='[".(int)$custom_product['id_custom_product']."]'");
			$data = Db::getInstance()->getRow($sql);

			if ($data && isset($data['in_cart']) && !(int)$data['in_cart'])
			{
				$module->storeCustomization($data['id_customization']);
				exit(Tools::jsonEncode(array(
					'success'=> '1'
				)));
			}

			if ($data)
				exit(Tools::jsonEncode(array(
					'error'=> 'in_cart'
				)));
			else
			{
				CustomDesign::$context = Context::getContext();
				$id_cart = CustomDesign::addCart($new = true);

				$data = array(
					'id_product_attribute' => (int)$custom_product['id_attribute'],
					'id_address_delivery' => (int)CustomDesign::$context->cart->id_address_delivery,
					'id_cart' => (int)$id_cart,
					'id_product' => (int)$custom_product['id_product'],
					'quantity' => (int)$quantity,
					'in_cart' => 0
				);
				Db::getInstance()->insert('customization', $data);
				$id_customization = Db::getInstance()->Insert_ID();
				$module->storeCustomization($id_customization);
				$data = array(
					'id_customization' => (int)$id_customization,
					'type' => Product::CUSTOMIZE_TEXTFIELD,
					'index' => (int)$id_customization_field,
					'value' => '['.(int)$custom_product['id_custom_product'].']'
				);
				Db::getInstance()->insert('customized_data', $data);
			}
		}
		exit(Tools::jsonEncode(array(
			'success'=> '1'
		)));
	}

	if ($action == 'checkcart')
	{
		$custom_product = $module->getCustomProduct(0, $id_custom_product);
		if ($custom_product)
		{
			$id_customization_field = $module->addCustomField($custom_product['id_product']);
			$sql = new DbQuery();
			$sql->from('customized_data', 'cd');
			$sql->innerJoin('customization', 'cc', 'cc.id_customization = cd.id_customization');
			$sql->where('cd.`index` = '.(int)$id_customization_field." AND cd.`value`='[".(int)$custom_product['id_custom_product']."]' AND cc.in_cart = 1");
			$data = Db::getInstance()->getRow($sql);

			if ($data)
			{
				exit(Tools::jsonEncode(array(
					'result'=> '1'
				)));
			}
			else
			{
				exit(Tools::jsonEncode(array(
					'result'=> '0'
				)));
			}
		}
		exit(Tools::jsonEncode(array(
			'error'=> '1',
			'extra' => '3'
		)));
	}

	$context = Context::getContext();

	$id_currecny = $context->cookie->id_currency;
	$currency = new Currency($id_currecny);

	CustomDesign::$panel_width = $panel_width;
	CustomDesign::$custom_width = $custom_width;
	CustomDesign::$custom_height = $custom_height;
	CustomDesign::$custom_color = $custom_color;
	CustomDesign::$quantity = $quantity;
	CustomDesign::$hash = $hash;
	CustomDesign::init($id_product, $id_attribute, $id_image);

	$items = array();
	$c = 0;
	while (Tools::getIsset('item_'.$c))
	{
		$items[] = (array)Tools::getValue('item_'.$c);
		$c++;
	}

	if (!count($items) && !$custom_color)
	{
		exit(Tools::jsonEncode(array(
			'error'=> 'empty'
		)));
	}

	$items_cost = CustomDesign::getCost($items);

	if ($action == 'calculate_price')
		exit(Tools::jsonEncode($items_cost));

	if ($action == 'download_image')
	{
		$custom_product = $module->getProductOptions($id_product);
		$preview = CustomDesign::renderFastPreview(array_merge($custom_product, $items), $items);
		exit(Tools::jsonEncode(array(
			'success'=> 1,
			'preview'=> $preview,
			'id_product'=> $id_product,
		)));
	}

	if ($action == 'add_to_cart')
	{
		$attributes = CustomDesign::$options['attributes'];
		$array = explode('-', $attributes);
		$attr_test = trim(str_replace('-', '', $attributes));
		if (! in_array($id_attribute, $array) && ! empty($attr_test) && !CustomDesign::$options['attributes_all'])
		{
			exit(Tools::jsonEncode(array(
				'error'=> 'noattr',
				'attributes'=> $attr_test
			)));
		}

		CustomDesign::saveItems($items);
	}
?>
