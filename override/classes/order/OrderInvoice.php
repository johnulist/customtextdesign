<?php

	class OrderInvoice extends OrderInvoiceCore{

		public static $ctd_active = -1;
		public static $customtextdesign = null;

		public function getProducts($products = false, $selected_products = false, $selected_qty = false)
		{
			$return = parent::getProducts($products, $selected_products, $selected_qty);

			if (!self::isActiveCustomTextDesign())
				return $return;

			$trace = debug_backtrace(false);
			$function = '';
			if (isset($trace[2]))
				$function = $trace[2]['class'].'::'.$trace[2]['function'];
			if (! $function == 'PDFCore::render')
				return $return;

			foreach ($return as &$order_detail)
			{

				$id_order = $order_detail['id_order'];
				$order = new Order((int)$id_order);
				$id_cart = $order->id_cart;
				$id_product = $order_detail['product_id'];
				$id_product_attribute = $order_detail['product_attribute_id'];
				$custom_products = self::$customtextdesign->getCustomProducts($id_product, $id_product_attribute, $id_cart, 1);

				if ($custom_products && $custom_products['level'] > 0)
				{
					unset($custom_products['level']);
					$custom_total = 0;
					$custom_total_wt = 0;
					if (isset($order_detail['customizedDatas']))
					{
						$custom_datas = $order_detail['customizedDatas'];
						foreach ($custom_datas as $id_address_delivery => $customizations)
						{

							foreach ($customizations as $id_customization => $datas)
							{

								foreach ($datas['datas'][Product::CUSTOMIZE_TEXTFIELD] as $id => $data)
								{
									preg_match ('/^\[(\d+)\]/', $data['value'], $match);
									$id_custom_product = isset($match[1]) ? (int)$match[1] : 0;
									if (isset($custom_products[$id_custom_product]))
									{
										$custom_product = $custom_products[$id_custom_product];
										$ctd_price = (float)$custom_product['price_ht'] + (float)$custom_product['custom_price_ht'];
										$ctd_price_wt = (float)$custom_product['price'] + (float)$custom_product['custom_price'];
										$quantity = (int)$datas['quantity'] - (int)$datas['quantity_refunded'] - (int)$datas['quantity_returned'];
										$custom_total += $ctd_price * $quantity;
										$custom_total_wt += $ctd_price_wt * $quantity;
										$order_detail['customizedDatas'][$id_address_delivery][$id_customization]['datas'][Product::CUSTOMIZE_TEXTFIELD][$id]['value']
										= self::$customtextdesign->getShortSummary($id_custom_product, $quantity);
									}
								}
							}
						}
					}
					$order_detail['total_price_tax_excl'] += $custom_total;
					$order_detail['total_price_tax_incl'] += $custom_total_wt;
				}
				else
				{
					unset($custom_products['level']);
					if (is_array($custom_products))
					foreach ($custom_products as $custom_product)
						$order_detail['product_name'] .= self::$customtextdesign->getShortSummary($custom_product['id_custom_product'], 1, 'image_only');
				}
			}
			return $return;
		}

		private static function isActiveCustomTextDesign()
		{
			if (self::$ctd_active == -1)
			{
				// This override is part of the customtextdesign module
				/** @var customtextdesign */
				self::$customtextdesign = Module::getInstanceByName('customtextdesign');
				self::$ctd_active = self::$customtextdesign->active;
			}
			return self::$ctd_active;
		}
	}