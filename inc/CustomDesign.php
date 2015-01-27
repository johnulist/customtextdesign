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

	class CustomDesign
	{

		public static $context;
		/** @var customtextdesign */
		public static $module;
		public static $currency;
		public static $name;
		public static $options;
		public static $measure;
		public static $ratio = 1;
		public static $id_product;
		public static $product;
		public static $id_attribute;
		public static $quantity;
		public static $id_image;
		public static $hash;
		public static $base_price;
		public static $panel_width;

		public static $custom_width;
		public static $custom_height;
		public static $custom_color;

		public static $cache;

		public static $materials;

		public static function init($id_product, $id_attribute, $id_image)
		{
			self::$context = Context::getContext();
			self::$module = Module::getInstanceByName('customtextdesign');
			self::$name = self::$module->name;
			self::$options = self::$module->getProductOptions($id_product);
			self::$measure = self::$module->getProductMeasures($id_product, $id_image);
			$images_type = self::$module->getImagePath($id_image, Configuration::get(self::$module->name.'image_type'), $id_product);
			$image_size = getimagesize($images_type);
			$panel_original_width = $image_size[0];

			$width_ratio = 1;
			if (self::$panel_width)
				$width_ratio = $panel_original_width / self::$panel_width;

			if (self::$options['customsize'] && (self::$custom_width * self::$custom_height))
				self::$ratio = self::$custom_width / self::$panel_width;
			else
			{
				if (self::$measure)
					self::$ratio = self::$measure['size'] / self::$measure['width'] * $width_ratio;
				else
					self::exitMsg('error', 'nomeasure');
			}

			self::$product = new Product($id_product);
			self::$id_product = $id_product;
			self::$id_attribute = $id_attribute;
			self::$id_image = $id_image;

			self::addCart();

			$address_id = Validate::isLoadedObject(self::$context->cart) ? self::$context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')} : 0;
			$address = new Address($address_id);
			$no_tax = Tax::excludeTaxeOption() || !self::$product->getTaxesRate($address);
			$price_display_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
			if ($price_display_method == PS_TAX_EXC)
				$no_tax = true;

			$id_cart = null;
			if (Validate::isLoadedObject(self::$context->cart))
				$id_cart = self::$context->cart->id;
			else
				self::$context->cart = new Cart();
			$id_address = $address->id;

			self::$base_price = self::$product->getPriceStatic($id_product, !$no_tax, $id_attribute, 6, null, false, true,
				1, false, null, $id_cart, $id_address);

			if (self::$options['customsize'] && (float)self::$options['customsize_price'] && (self::$custom_width * self::$custom_height))
			{
				$customsize_price = (float)self::$options['customsize_price'];
				$area = self::$custom_width * self::$custom_height / 10000;
				$tax_rate = self::$module->getTax(self::$id_product);
				self::$base_price = $customsize_price * $area * (1 + $tax_rate / 100);
			}

			self::$materials = self::$module->getMaterial(0);
			self::$materials = self::$module->organizeBy('id', self::$materials);
		}

		public static function getItemCost($item)
		{
			self::$options = self::$module->getProductOptions(self::$id_product);

			if ((int)self::$options['free_design']) return 0;

			$width = $item['width'] * $item['scalex'] * self::$ratio;
			$height = $item['height'] * $item['scaley'] * self::$ratio;
			$area = $width * $height / 10000;
			$type = $item['type'];
			$item_cost = 0;
			if ($type == 'text')
			{
				if ((float)self::$options['text_price'] > 0)
					$item_cost = (float)self::$options['text_price'];
				else
				{
					//remove trailing newlines
					$text_size = CustomImage::measureText($item);
					$width = $text_size['width'] * $item['scalex'] * self::$ratio;
					$height = $text_size['height'] * $item['scaley'] * self::$ratio;
					$area = $width * $height / 10000;
					$material = isset(self::$materials[(int)$item['material']]) ? self::$materials[(int)$item['material']] : null;
					$item_cost = (isset($material['price']) ? $material['price'] : 0) * $area;
				}
			}
			elseif ($type == 'image')
			{
				$id_image = $item['id_image'];
				if ((int)self::$options['image_fixed'])
				{
					if ($id_image)
					{
						$image = self::$module->getImage($id_image);
						$item_cost = (float)$image['price'];
					}
					else
						$item_cost = (float)self::$options['upload_price'];
				}
				elseif ((float)self::$options['image_price'] > 0)
					$item_cost = (float)self::$options['image_price'];
				else
				{
					$image_cost = 0;
					if ($id_image)
					{
						$image = CustomImage::getImage($id_image);
						$image_cost = (float)$image['price'];
					}
					else
						$image_cost = (float)self::$options['upload_price'];
					$width = $item['width'] * $item['scalex'] * self::$ratio;
					$height = $item['height'] * $item['scaley'] * self::$ratio;
					$area = $width * $height / 10000;
					$item_cost = $image_cost * $area;
				}
			}

			return $item_cost;
		}

		public static function getCost($items)
		{
			self::$options = self::$module->getProductOptions(self::$id_product);
			$cost_report = array();

			$tax_rate = self::$module->getTax(self::$id_product);

			if ($tax_rate)
				$cost_report['TTC'] = 1;
			else
				$cost_report['TTC'] = 2;

			if (! Validate::isLoadedObject(self::$context->country) || ! self::$context->country->display_tax_label)
				$cost_report['TTC'] = 0;

			$context = Context::getContext();

			$id_currency = $context->cookie->id_currency;
			self::$currency = new Currency($id_currency);
			$sub_total = 0;
			if ((float)self::$options['design_price'] == 0)
			{
				foreach ($items as $item)
				{
					//Get Item Cost
					$item_cost = self::getItemCost($item) * (1 + $tax_rate / 100);
					$sub_total += $item_cost;
					$price = self::price($item_cost);
					$cost_report[$item['id']] = array('id_item'=>$item['id'], 'price' => $item_cost, 'str_price' => $price);
				}
			}

			$original_product_price = self::$base_price;

			if ((float)self::$options['design_price'] > 0)
			{
				$cost_report = array();
				$sub_total = (float)self::$options['design_price'];
			}

			if ((int)self::$options['free_design']) $sub_total = 0;

			//total of items
			$cost_report['sub_total'] = $sub_total;
			$cost_report['str_sub_total'] = self::price($sub_total);

			//product price
			$cost_report['product_price'] = $original_product_price;
			$cost_report['str_product_price'] = self::price($original_product_price);

			//total price
			$total = $original_product_price + $sub_total;
			//$total = Tools::ps_round(Tools::convertPrice(self::$base_price, 2)) + Tools::ps_round(Tools::convertPrice($sub_total, 2));
			$cost_report['total'] = $total;
			$cost_report['str_total'] = self::price($total);

			return $cost_report;
		}

		public static function saveItems($items)
		{
			self::$options = self::$module->getProductOptions(self::$id_product);

			$table_custom_item = self::$module->name.'_custom_item';
			$table_custom_product = self::$module->name.'_custom_product';
			$id_product = self::$product->id;
			$id_attribute = self::$id_attribute;
			$id_image = self::$id_image;
			$hash = self::$hash;
			$panel_width = self::$panel_width;
			$id_cart = Validate::isLoadedObject(self::$context->cart) ? (int)self::$context->cart->id : 0;
			if (! $id_cart)
				$id_cart = self::addCart(true);

			$id_customer = self::$context->cookie ? (int)self::$context->cookie->id_customer : 0;
			$id_guest = self::$context->cookie ? (int)self::$context->cookie->id_guest : 0;

			$exists = true;
			$uniqid = '';
			//prevent inifinte loop
			$maxtries = 10;
			while ($exists && $maxtries)
			{
				//generate a uniqid to prevent users from navigating by ?id_custom_product
				$uniqid = uniqid();
				$uniqid = Tools::substr($uniqid, 0, 13);
				$exists = self::$module->getCustomProduct(0, $uniqid);
				$maxtries--;
			}

			$custom_price = 0;
			if (self::$options['customsize'] && (float)self::$options['customsize_price'] && (self::$custom_width * self::$custom_height))
			{
				$customsize_price = (float)self::$options['customsize_price'];
				$area = self::$custom_width * self::$custom_height / 10000;
				$custom_price = $customsize_price * $area;
			}

			$data = array(
				'id_product' => (int)$id_product,
				'id_attribute' => (int)$id_attribute,
				'id_image' => (int)$id_image,
				'id_cart' => (int)$id_cart,
				'id_customer' => (int)$id_customer,
				'id_guest' => (int)$id_guest,
				'hash' => $hash,
				'width' => (int)$panel_width,
				'uniqid' => $uniqid,
				'product_width' => (float)self::$custom_width,
				'product_height' => (float)self::$custom_height,
				'product_color' => self::$custom_color,
				'custom_price' => $custom_price,
				'version' => self::$module->version,
				'price' => 0,
				'preview' => '',
			);
			$insert = Db::getInstance()->insert($table_custom_product, $data);
			if (! $insert) self::exitMsg('error', 1, '1');

			$id_custom_product = Db::getInstance()->Insert_ID();
			if (! $id_custom_product) self::exitMsg('error', 1, '2');

			$items_bkp = $items;

			$total = 0;
			foreach ($items as &$item)
			{
				if ((float)self::$options['design_price'] > 0)
					$item['price'] = 0;
				else
					$item['price'] = self::getItemCost($item);
				$item['id_custom_product'] = $id_custom_product;
				$total += (float)$item['price'];
				$item['text'] = pSQL($item['text']);
				if (isset($item['forpanel']))
					unset($item['forpanel']);
				if (!isset($item['id_image'])) $item['id_image'] = 0;
				Db::getInstance()->insert($table_custom_item, $item);
			}

			if ((float)self::$options['design_price'] > 0)
				$total = (float)self::$options['design_price'];

			Db::getInstance()->update($table_custom_product, array('price' => $total), "id_custom_product = $id_custom_product");

			$custom_product = self::$module->getCustomProduct($id_custom_product);
			$tax_rate = self::$module->getTax($custom_product['id_product']);
			$total *= (1 + $tax_rate / 100);
			$total_price = self::$base_price + $total;
			$total_price = Tools::ps_round(Tools::convertPrice($total_price, self::$currency), 2);

			$str_attributes = self::$module->getProductAttributes($id_product, $id_attribute);
			$str_attributes = Tools::truncate($str_attributes, 30);
			$str_attributes = Tools::safeOutput($str_attributes);

			if (!self::$context->link)
				self::$context->link = new Link();

			//add to cart

			$id_cart = self::addCart();
			$id_customization_field = self::$module->addCustomField($id_product);
			$data = array(
				'id_product_attribute' => $id_attribute,
				'id_address_delivery' => (int)self::$context->cart->id_address_delivery,
				'id_cart' => $id_cart,
				'id_product' => $id_product,
				'quantity' => self::$quantity,
				'in_cart' => 0
			);
			Db::getInstance()->insert('customization', $data);
			$id_customization = Db::getInstance()->Insert_ID();
			self::$module->storeCustomization($id_customization);
			$data = array(
				'id_customization' => (int)$id_customization,
				'type' => Product::CUSTOMIZE_TEXTFIELD,
				'index' => (int)$id_customization_field,
				'value' => '['.$id_custom_product.']'
			);
			Db::getInstance()->insert('customized_data', $data);

			self::$product->customizable = 1;
			self::$product->update();

			exit(Tools::jsonEncode(array(
				'success'=> 1,
				'preview'=> self::renderPreview($custom_product, $items_bkp),
				'id_attribute'=> $id_attribute,
				'attributes'=> $str_attributes,
				'id_image'=> $id_image,
				'price'=> self::price($total),
				'total_price'=> Tools::displayPrice($total_price, self::$currency),
				'id_custom_product'=> $uniqid,
				'link'=> self::$context->link->getModuleLink(self::$module->name, 'Preview', array('id_custom_product'=>$uniqid)),
				'id_cart'=> $id_cart
			)));
		}

		public static function addCart($new = false)
		{
			$context = Context::getContext();

			if (!self::$context)
				self::$context = $context;

			if (Validate::isLoadedObject($context->cart) && (int)$context->cookie->id_cart)
			{
				$id_cart = (int)$context->cookie->id_cart;
				if (! Validate::isLoadedObject($context->cart))
				{
					$context->cart = new Cart($id_cart);
					self::$context->cart = $context->cart;
				}

				return $id_cart;
			}

			if (! $new) return null;

			if (! Validate::isLoadedObject($context->cart))
			{
				$context->cart = new Cart();
				$id_currency = $context->cookie->id_currency;
				if (! $id_currency)
					$id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
				$context->cart->id_currency = $id_currency;
			}

			if (!self::$context->cookie->id_guest)
				Guest::setNewGuest(self::$context->cookie);

			if (self::$context->cookie->id_guest)
			{
				$guest = new Guest(self::$context->cookie->id_guest);
				self::$context->cart->mobile_theme = $guest->mobile_theme;
			}

			if (self::$context->cookie->id_customer)
			{
				$id_address = Address::getFirstCustomerAddressId(self::$context->cookie->id_customer);
				self::$context->cart->id_address_delivery = $id_address;
				self::$context->cart->id_address_invoice = $id_address;
			}
			self::$context->cart->add();
			if (self::$context->cart->id)
			{
				self::$context->cookie->id_cart = (int)self::$context->cart->id;
				$context->cookie->id_cart = (int)self::$context->cart->id;
				$context->cookie->write();
				$context->cart->id = (int)self::$context->cart->id;
			}
			return (int)self::$context->cart->id;
		}

		public static function renderPreview($custom_product, $items = null)
		{
			if (self::checkCache($custom_product) && _CTD_CACHE_)
			{
				self::updateCustomPreview($custom_product['id_custom_product'], self::$cache);
				return self::$cache;
			}
			$image_path = self::$module->getImagePath(self::$id_image, Configuration::get(self::$module->name.'image_type'), self::$id_product);
			$image_type = self::$module->exifImageType($image_path);
			list($width, $height) = getimagesize($image_path);
			$old_width = $width;
			$old_height = $height;
			if (self::$options['customsize'] && (self::$custom_width * self::$custom_height))
			{
				$proportion = self::$custom_width / self::$custom_height;
				if ($proportion > 1)
				{
					if ($width >= $height * $proportion)
						$width = $height * $proportion;
					else
						$height = $width / $proportion;
				}
				elseif ($proportion < 1)
				{
					if ($height >= $width / $proportion)
						$height = $width / $proportion;
					else
						$width = $height * $proportion;
				}
			}
			$image = imagecreatetruecolor($width, $height);
			$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
			imagefill($image, 0, 0, $transparent);
			imagecolortransparent($image, $transparent);
			imagealphablending($image, 1);
			imagesavealpha($image, 1);
			foreach ($items as $item)
				CustomImage::pasteImage($image, self::$panel_width, $item);
			self::saveImage($image, 1);

			if (self::$options['customcolor'] && self::$custom_color)
			{
				$data = array();
				$data['customcolor'] = self::$custom_color;
				$data['id_product'] = (int)self::$id_product;
				$data['id_image'] = (int)self::$id_image;
				$image_path = CustomImage::colorize($data, false, $image_path);
			}

			if ($width != $old_width || $height != $old_height)
				$base_image = CustomImage::resize($image_path, $width, $height);
			else
			{
				$image_type = self::$module->exifImageType($image_path);
				$base_image = ImageManager::create($image_type, $image_path);
			}
			imagealphablending($base_image, true);
			imagesavealpha($base_image, true);

			CustomImage::pastePNG($base_image, $image);
			//paste mask
			$mask = self::$module->getProductMasks($custom_product['id_product'], $custom_product['id_image']);
			if ($mask)
				CustomImage::pasteMask($base_image, $mask);
			self::saveImage($base_image);
			self::updateCustomPreview($custom_product['id_custom_product'], self::$cache);
			return self::$cache;
		}

		public static function renderFastPreview($custom_product, $items = null, $sub_only = false)
		{
			$rows = 1;
			$custom_product['rows'] = $rows;
			$custom_product['sub'] = (int)$sub_only;
			if (self::checkCache($custom_product) && _CTD_CACHE_)
				return self::$cache;
			elseif ($sub_only && _CTD_CACHE_ && self::checkSub())
				return basename(self::$cache.'_sub.png');
			$image_path = self::$module->getImagePath(self::$id_image, Configuration::get(self::$module->name.'image_type'), self::$id_product);
			$image_type = self::$module->exifImageType($image_path);
			list($width, $height) = getimagesize($image_path);
			$image = imagecreatetruecolor($width, $height * $rows);
			if ($rows == 1) $height = 0;
			$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
			imagefill($image, 0, 0, $transparent);
			imagecolortransparent($image, $transparent);
			imagealphablending($image, 1);
			imagesavealpha($image, 1);

			if (self::$options['customcolor'] && self::$custom_color && !$sub_only)
			{
				$data = array();
				$data['customcolor'] = self::$custom_color;
				$data['id_product'] = (int)self::$id_product;
				$data['id_image'] = (int)self::$id_image;
				$image_path = CustomImage::colorize($data, false, $image_path);
			}

			$base_image = ImageManager::create($image_type, $image_path);
			imagealphablending($base_image, true);
			imagesavealpha($base_image, true);

			if ($rows == 1 && !$sub_only)
				CustomImage::pastePNG($image, $base_image, $height);

			foreach ($items as $item)
				CustomImage::pasteImage($image, self::$panel_width, $item);
			self::saveImage($image, 1);

			//paste mask
			$mask = self::$module->getProductMasks($custom_product['id_product'], self::$id_image);
			$mask2 = self::$module->getProductMasks2($custom_product['id_product'], self::$id_image);
			if ($mask2 && ($rows > 1 || $sub_only))
			{
				$tmp = $image;
				$image = CustomImage::substitueImage($image, $mask2, 'mask2', false);
				imagedestroy($tmp);
				$sub_path = self::saveImage($image, 1, 1);
				if ($sub_only) return basename($sub_path);
			}
			if ($rows > 1 && !$sub_only)
			{
				CustomImage::pastePNG($image, $base_image, $height);
				CustomImage::pastePNG($image, $image, $height);
			}
			if ($mask)
				CustomImage::pasteMask($image, $mask, false, $height);
			self::saveImage($image);
			return self::$cache;
		}

		public static function getSub($id_custom_product)
		{
			self::$module = Module::getInstanceByName('customtextdesign');
			$custom_product = self::$module->getCustomProduct($id_custom_product);
			$items = self::$module->getItems($id_custom_product);
			return self::renderFastPreview($custom_product, $items, true);
		}

		public static function getCache($custom_product)
		{
			$temp = $custom_product;
			$temp['preview'] = '';
			$dir = dirname(__FILE__);
			$fontstat = stat($dir.'/../data/font');
			$colorstat = stat($dir.'/../data/texture');
			$imagestat = stat($dir.'/../data/image');
			$temp['font_mtime'] = $fontstat['mtime'];
			$temp['color_mtime'] = $colorstat['mtime'];
			$temp['image_mtime'] = $imagestat['mtime'];
			$cache = 'preview_'.md5(serialize($temp)).'.png';
			return $cache;
		}

		public static function checkCache($custom_product)
		{
			$dir = dirname(__FILE__);
			self::$cache = self::getCache($custom_product);
			return file_exists($dir.'/../data/cache/'.self::$cache);
		}

		public static function checkSub()
		{
			$dir = dirname(__FILE__);
			return file_exists($dir.'/../data/cache/'.self::$cache.'_sub.png');
		}

		public static function updateCustomPreview($id_custom_product, $cache)
		{
			Db::getInstance()->update(self::$module->name.'_custom_product', array('preview'=>$cache), 'id_custom_product = '.(int)$id_custom_product);
		}

		public static function saveImage($image, $without_base_img = 0, $substitute = 0)
		{
			$dir = dirname(__FILE__);
			$image_path = $dir.'/../data/cache/'.self::$cache;
			$image_path_bkp = $image_path;
			if ($without_base_img)
				$image_path .= '_cad.png';
			if ($substitute)
				$image_path = $image_path_bkp.'_sub.png';
			imagepng($image, $image_path);
			chmod($image_path, 0774);
			return $image_path;
		}

		public static function price($price)
		{
			return Tools::displayPrice(Tools::convertPrice($price, self::$currency), self::$currency);
		}

		public static function exitMsg($name, $value, $extra = 'no')
		{
			exit(Tools::jsonEncode(array(
				"$name"=> "$value",
				'extra' => "$extra"
			)));
		}

	}
