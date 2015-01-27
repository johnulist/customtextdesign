<?php

	class Order extends OrderCore{

		public static $ctd_active = -1;
		public static $customtextdesign = null;

		public function getProductsDetail()
		{
			$context = Context::getContext();

			$return = parent::getProductsDetail();

			if (!self::isActiveCustomTextDesign() || ! (Validate::isLoadedObject($context->employee) && (int)$context->employee->id))
				return $return;

			$id_cart = $this->id_cart;

			foreach ($return as &$product)
			{

				$id_product = $product['id_product'];
				$id_product_attribute = $product['product_attribute_id'];
				$custom_products = self::$customtextdesign->getCustomProducts($id_product, $id_product_attribute, $id_cart, 1);

				if ($custom_products && !$custom_products['level'])
				{
					unset($custom_products['level']);
					$output = '';
					foreach ($custom_products as $custom_product)
						$output .= self::$customtextdesign->getCustomProductSummary($custom_product['id_custom_product']);
					$product['product_name'] .= $output;
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

?>
