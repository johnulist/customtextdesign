<?php

	class Product extends ProductCore{

		public static $ctd_active = -1;
		public static $customtextdesign = null;

		public static function getPriceStatic($id_product, $usetax = true, $id_product_attribute = null, $decimals = 6, $divisor = null,
			$only_reduc = false, $usereduc = true, $quantity = 1, $force_associated_tax = false, $id_customer = null, $id_cart = null,
			$id_address = null, &$specific_price_output = null, $with_ecotax = true, $use_group_reduction = true, Context $context = null,
			$use_customer_price = true)
		{
			$return = parent::getPriceStatic($id_product, $usetax, $id_product_attribute, $decimals, $divisor,
				$only_reduc, $usereduc, $quantity, $force_associated_tax, $id_customer, $id_cart,
				$id_address, $specific_price_output, $with_ecotax, $use_group_reduction, $context,
				$use_customer_price);

			if (!self::isActiveCustomTextDesign())
				return $return;

			if (!$context)
				$context = Context::getContext();

			$id_special = self::$customtextdesign->getSpecialProduct();
			if ($id_product == $id_special)
			{
				$id_cart = $id_cart ? $id_cart : self::$customtextdesign->getCart();
				if (!$id_cart) return 0;
				$customized_datas = Product::getAllCustomizedDatas($id_cart, $context->language->id, true);
				$custom_products = self::$customtextdesign->getCustomProducts(null, null, $id_cart);
				$ctd_total = 0;
				if (is_array($customized_datas))
				{
					foreach ($customized_datas as $id_product => $attribute_customization)
					{
						foreach ($attribute_customization as $address_customization)
						{
							foreach ($address_customization as $customization)
							{
								foreach ($customization as $customization_datas)
								{
									$quantity = (int)$customization_datas['quantity'];
									$datas = isset($customization_datas['datas']) ? $customization_datas['datas'] : null;
									if (is_array($datas) && isset($datas[Product::CUSTOMIZE_TEXTFIELD]))
									{
										$custom_texts = $datas[Product::CUSTOMIZE_TEXTFIELD];
										foreach ($custom_texts as $custom_data)
										{
											preg_match ('/^\[(\d+)\]/', $custom_data['value'], $match);
											$id_custom_product = isset($match[1]) ? (int)$match[1] : 0;
											if (isset($custom_products[$id_custom_product]))
											{
												$custom_product = $custom_products[$id_custom_product];
												if ($usetax)
													$price = (float)$custom_product['price'] + (float)$custom_product['custom_price'];
												else
													$price = (float)$custom_product['price_ht'] + (float)$custom_product['custom_price_ht'];
												$ctd_total += $price * $quantity;

												$discounts = SpecificPrice::getQuantityDiscounts(
													$id_product,
													$context->shop->id,
													$context->currency->id,
													$context->country->id,
													$context->customer->id_default_group,
													$id_product_attribute,
													false,
													$context->customer->id
												);

												$new_total = $ctd_total;
												if (is_array($discounts))
												{
													foreach ($discounts as $discount)
													{
														if ($discount['reduction_type'] == 'percentage' && (int)$quantity >= (int)$discount['from_quantity'])
														{
															$reduction = (float)$discount['reduction'];
															$new_total = $ctd_total * (1 - $reduction);
															break;
														}
													}
													$ctd_total = $new_total;
												}
											}
										}
									}
								}

							}
						}
					}
				}

				if (! (Validate::isLoadedObject($context->currency) && (int)$context->currency->id))
				$context->currency = new Currency($context->cookie->id_currency);

				return Tools::ps_round(Tools::convertPrice($ctd_total, $context->currency), 2);
			}

			return $return;
		}

		public function hasAllRequiredCustomizableFields(Context $context = null)
		{
			if (!self::isActiveCustomTextDesign())
				return parent::hasAllRequiredCustomizableFields($context);

			if (!Customization::isFeatureActive())
				return true;

			if (!$context)
				$context = Context::getContext();

			$fields = $context->cart->getProductCustomization($this->id, null, true);
			if (($required_fields = $this->getRequiredCustomizableFields()) === false)
				return false;

			$fields_present = array();
			foreach ($fields as $field)
			{
				self::$customtextdesign->addCustomization($field['id_customization']);
				$fields_present[] = array('id_customization_field' => $field['index'], 'type' => $field['type']);
			}
			foreach ($required_fields as $required_field)
				if (!in_array($required_field, $fields_present))
					return false;
			return true;
		}

		public function getCustomizationFields($id_lang = false)
		{
			if (!self::isActiveCustomTextDesign())
				return parent::getCustomizationFields($id_lang);

			if (!Customization::isFeatureActive())
				return false;

			if (!$result = Db::getInstance()->executeS('
				SELECT cf.`id_customization_field`, cf.`type`, cf.`required`, cfl.`name`, cfl.`id_lang`
				FROM `'._DB_PREFIX_.'customization_field` cf
				NATURAL JOIN `'._DB_PREFIX_.'customization_field_lang` cfl
				WHERE cf.`id_product` = '.(int)$this->id.($id_lang ? ' AND cfl.`id_lang` = '.(int)$id_lang : '').'
			ORDER BY cf.`id_customization_field`', true, false))
				return false;

			$id_customization_field = self::$customtextdesign->addCustomField($this->id);

			if ($id_lang)
			{
				$customization_fields = array();
				foreach ($result as $key => $row)
				{
					if ($row['id_customization_field'] == $id_customization_field)
						unset($result[$key]);
				}
				return $result;
			}

			$customization_fields = array();
			foreach ($result as $key => $row)
				$customization_fields[(int)$row['type']][(int)$row['id_customization_field']][(int)$row['id_lang']] = $row;

			return $customization_fields;
		}

		public static function getAllCustomizedDatas($id_cart, $id_lang = null, $only_in_cart = true)
		{
			if (!self::isActiveCustomTextDesign())
				return parent::getAllCustomizedDatas($id_cart, $id_lang, $only_in_cart);
			if (!Customization::isFeatureActive())
				return false;

			// No need to query if there isn't any real cart!
			if (!$id_cart)
				return false;
			if (!$id_lang)
				$id_lang = Context::getContext()->language->id;

			if (!$result = Db::getInstance()->executeS('
				SELECT cd.`id_customization`, c.`id_address_delivery`, c.`id_product`, cfl.`id_customization_field`, c.`id_product_attribute`,
				cd.`type`, cd.`index`, cd.`value`, cfl.`name`
				FROM `'._DB_PREFIX_.'customized_data` cd
				NATURAL JOIN `'._DB_PREFIX_.'customization` c
				LEFT JOIN `'._DB_PREFIX_.'customization_field_lang` cfl ON (cfl.id_customization_field = cd.`index` AND id_lang = '.(int)$id_lang.')
				WHERE c.`id_cart` = '.(int)$id_cart.
				($only_in_cart ? ' AND c.`in_cart` = 1' : '').'
			ORDER BY `id_product`, `id_product_attribute`, `type`, `index`', true, false))
				return false;

			$trace = debug_backtrace(false);
			$function = '';
			if (isset($trace[1]))
				$function = $trace[1]['class'].'::'.$trace[1]['function'];
			$customized_datas = array();

			foreach ($result as $row)
				$customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['datas'][(int)$row['type']][] = $row;

			if (!$result = Db::getInstance()->executeS(
				'SELECT `id_product`, `id_product_attribute`, `id_customization`, `id_address_delivery`, `quantity`, `quantity_refunded`, `quantity_returned`
				FROM `'._DB_PREFIX_.'customization`
				WHERE `id_cart` = '.(int)$id_cart.($only_in_cart ? ' AND `in_cart` = 1' : ''), true, false))
				return false;

			foreach ($result as $row)
			{
				$customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['quantity'] = (int)$row['quantity'];
				$customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['quantity_refunded'] = (int)$row['quantity_refunded'];
				$customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['quantity_returned'] = (int)$row['quantity_returned'];
				if ($function == 'PaymentModuleCore::validateOrder' || $function == 'MailAlerts::hookActionValidateOrder')
				{
					$customizations = $customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['datas'][Product::CUSTOMIZE_TEXTFIELD];
					if (is_array($customizations))
					{
						foreach ($customizations as $id => $customization)
						{
							preg_match ('/^\[(\d+)\]/', $customization['value'], $match);
							$id_custom_product = isset($match[1]) ? (int)$match[1] : 0;
							if ($id_custom_product && $custom_product = self::$customtextdesign->getCustomProduct($id_custom_product, null, true))
							{
								self::$customtextdesign->getTax($custom_product['id_product'], true);
								$ctd_price = (float)$custom_product['price'] + (float)$custom_product['custom_price'];
								$ctd_price_ht = (float)$custom_product['price_ht'] + (float)$custom_product['custom_price_ht'];
								$quantity = (int)$row['quantity'] - (int)$row['quantity_refunded'] - (int)$row['quantity_returned'];
								$ctd_total = $ctd_price * $quantity;
								$customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['datas'][Product::CUSTOMIZE_TEXTFIELD][$id]['value']
								= $customization['value'].' '.Tools::displayPrice(Tools::convertPrice($ctd_price_ht)).' ('.Tools::displayPrice(Tools::convertPrice($ctd_total))
								.' '.self::$customtextdesign->l('Tax incl.').')';
							}
						}
					}
				}
				if ($function == 'BlockCart::assignContentVars')
				{
					$customizations = $customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['datas'][Product::CUSTOMIZE_TEXTFIELD];
					if (is_array($customizations))
					{
						foreach ($customizations as $id => $customization)
						{
							preg_match ('/^\[(\d+)\]/', $customization['value'], $match);
							$id_custom_product = isset($match[1]) ? (int)$match[1] : 0;
							if ($id_custom_product && $custom_product = self::$customtextdesign->getCustomProduct($id_custom_product))
							{
								self::$customtextdesign->getTax($custom_product['id_product']);
								$ctd_price = (float)$custom_product['price'] + (float)$custom_product['custom_price'];
								$quantity = (int)$row['quantity'] - (int)$row['quantity_refunded'] - (int)$row['quantity_returned'];
								$ctd_total = $ctd_price * $quantity;
								$customized_datas[(int)$row['id_product']][(int)$row['id_product_attribute']][(int)$row['id_address_delivery']][(int)$row['id_customization']]['datas'][Product::CUSTOMIZE_TEXTFIELD][$id]['value']
								= self::$customtextdesign->l('Custom')." $quantity x ".Tools::displayPrice(Tools::convertPrice($ctd_price)).' = '
								.Tools::displayPrice(Tools::convertPrice($ctd_total));
							}
						}
					}
				}
			}

			return $customized_datas;
		}

		public static function addCustomizationPrice(&$products, &$customized_datas)
		{
			if (!self::isActiveCustomTextDesign())
				return parent::addCustomizationPrice($products, $customized_datas);

			if (!$customized_datas)
				return;

			foreach ($products as &$product_update)
			{
				if (!Customization::isFeatureActive())
				{
					$product_update['customizationQuantityTotal'] = 0;
					$product_update['customizationQuantityRefunded'] = 0;
					$product_update['customizationQuantityReturned'] = 0;
				}
				else
				{
					$customization_quantity = 0;
					$customization_quantity_refunded = 0;
					$customization_quantity_returned = 0;
					$customtextdesign_total = 0;

					/* Compatibility */
					$product_id = isset($product_update['id_product']) ? (int)$product_update['id_product'] : (int)$product_update['product_id'];
					$product_attribute_id = isset($product_update['id_product_attribute'])
					? (int)$product_update['id_product_attribute'] : (int)$product_update['product_attribute_id'];
					$id_address_delivery = (int)$product_update['id_address_delivery'];
					$product_quantity = isset($product_update['cart_quantity']) ? (int)$product_update['cart_quantity'] : (int)$product_update['product_quantity'];
					$price = isset($product_update['price']) ? $product_update['price'] : $product_update['product_price'];
					if (isset($product_update['price_wt']) && $product_update['price_wt'])
						$price_wt = $product_update['price_wt'];
					else /* calculate tax */
						$price_wt = $price * (1 + ((isset($product_update['tax_rate']) ? $product_update['tax_rate'] : $product_update['rate']) * 0.01));

					if (isset($customized_datas[$product_id][$product_attribute_id][$id_address_delivery]))
					{
						foreach ($customized_datas[$product_id][$product_attribute_id][$id_address_delivery] as $customization)
						{
							$customization_quantity += (int)$customization['quantity'];
							$customization_quantity_refunded += (int)$customization['quantity_refunded'];
							$customization_quantity_returned += (int)$customization['quantity_returned'];
						}
					}

					if (isset($product_update['customizedDatas']))
					{
						$custom_datas = $product_update['customizedDatas'];
						$context = Context::getContext();
						$id_cart = 0;
						if (Validate::isLoadedObject($context->cart))
							$id_cart = (int)$context->cart->id;
						$custom_products = self::$customtextdesign->getCustomProducts($product_id, $product_attribute_id, $id_cart);
						foreach ($custom_datas as $id_address_delivery => $customizations)
						{

							foreach ($customizations as $id_customization => $datas)
							{

								if (is_array($datas['datas'][Product::CUSTOMIZE_TEXTFIELD]))
								{
									foreach ($datas['datas'][Product::CUSTOMIZE_TEXTFIELD] as $id => $custom_data)
									{
										preg_match ('/^\[(\d+)\]/', $custom_data['value'], $match);
										$id_custom_product = isset($match[1]) ? (int)$match[1] : 0;
										if (isset($custom_products[$id_custom_product]))
										{
											$custom_product = $custom_products[$id_custom_product];
											if (self::$customtextdesign->getTax($custom_product['id_product']))
												$ctd_price = (float)$custom_product['price'] + (float)$custom_product['custom_price'];
											else
												$ctd_price = (float)$custom_product['price_ht'] + (float)$custom_product['custom_price_ht'];
											$custom_total = $ctd_price * ((int)$datas['quantity'] - (int)$datas['quantity_refunded'] - (int)$datas['quantity_returned']);
											$product_update['customizedDatas'][$id_address_delivery][$id_customization]['datas'][Product::CUSTOMIZE_TEXTFIELD][$id]['value']
											= self::$customtextdesign->getCustomProductSummary($id_custom_product, $custom_total);
										}

										$ps_admin = new Cookie('psAdmin');
										if ($ps_admin->id_employee)
										{
											$table_design = self::$customtextdesign->name.'_design';
											$sql = new DbQuery();
											$sql->from($table_design);
											$sql->where('id_product = '.(int)$product_update['id_product']);
											$ctd_product = Db::getInstance()->getRow($sql, false);

											if ($ctd_product)
											{
												$link = new Link();
												$href = htmlspecialchars($link->getModuleLink(self::$customtextdesign->name, 'Pdf', array('id_product' => (int)$product_update['id_product'])));
												$product_update['customizedDatas'][$id_address_delivery][$id_customization]['datas'][Product::CUSTOMIZE_TEXTFIELD][$id]['value'] .= "<div style='color: #1ABCFC;font-weight: bold;'>
												<strong><span>PDF:</span></strong> <span><a style='color: #1ABCFC;'
												href='$href' target='_blank'>".self::$customtextdesign->l('Download').'</a></span></div>';
											}
										}
									}
								}
							}
						}
					}

					$product_update['customizationQuantityTotal'] = $customization_quantity;
					$product_update['customizationQuantityRefunded'] = $customization_quantity_refunded;
					$product_update['customizationQuantityReturned'] = $customization_quantity_returned;

					if ($customization_quantity)
					{
						$product_update['total_wt'] = $price_wt * ($product_quantity - $customization_quantity) + $customtextdesign_total;
						$product_update['total_customization_wt'] = $price_wt * $customization_quantity + $customtextdesign_total;
						$product_update['total'] = $price * ($product_quantity - $customization_quantity) + $customtextdesign_total;
						$product_update['total_customization'] = $price * $customization_quantity + $customtextdesign_total;
					}
				}
			}
		}

		private static function isActiveCustomTextDesign()
		{
			if (self::$ctd_active == -1)
			{
				//This override is part of the customtextdesign module
				/** @var customtextdesign */
				self::$customtextdesign = Module::getInstanceByName('customtextdesign');
				self::$ctd_active = self::$customtextdesign->active;
			}
			return self::$ctd_active;
		}

	}
?>