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

	class CustomTextDesignPreviewModuleFrontController extends ModuleFrontController{

		/** @var customtextdesign */
		public $module;

		public function __construct()
		{
			parent::__construct();
			$this->context = Context::getContext();
		}

		public function setMedia()
		{
			parent::setMedia();
			$this->module->getPath();
			$this->context->controller->addCSS($this->module->path.'css/preview.css');
		}

		public function initContent()
		{
			parent::initContent();
			$this->module->requireClass('CustomImage');
			$this->module->requireClass('CustomDesign');
			$preview = '';
			$uniqid = Tools::getValue('id_custom_product');

			$custom_product = null;
			if ($uniqid)
				$custom_product = $this->module->getCustomProduct(0, $uniqid);

			if ($custom_product)
			{
				$colors = $this->module->getColor(0);
				$fonts = $this->module->getFont(0);
				$materials = $this->module->getMaterial(0);

				$id_product 	= $custom_product['id_product'];
				$id_attribute 	= $custom_product['id_attribute'];
				$id_image 		= $custom_product['id_image'];
				$hash 			= $custom_product['hash'];
				$panel_width	= $custom_product['width'];

				$product = new Product((int)$id_product);
				$items = $this->module->getItems($custom_product['id_custom_product']);
				CustomDesign::$hash = $hash;
				CustomDesign::$panel_width = $panel_width;
				CustomDesign::$custom_width = $custom_product['product_width'];
				CustomDesign::$custom_height = $custom_product['product_height'];
				CustomDesign::$custom_color = $custom_product['product_color'];
				CustomDesign::init($id_product, $id_attribute, $id_image);
				$preview = CustomDesign::renderPreview($custom_product, $items);

				$measure = $this->module->getProductMeasures($id_product, $id_image);
				$images_type = $this->module->getImagePath($id_image, Configuration::get($this->module->name.'image_type'), $id_product);
				$image_size = getimagesize($images_type);
				$original_width = $image_size[0];

				$width_ratio = $original_width / $custom_product['width'];
				//from px to cm
				$options = $this->module->getProductOptions($id_product);
				if ($options['customsize'] && ($custom_product['product_width'] * $custom_product['product_height']))
					$cmratio = $custom_product['product_width'] / $custom_product['width'];
				elseif ($measure)
					$cmratio = $measure['size'] / $measure['width'];

				foreach ($items as &$item)
				{
					if ($item['type'] == 'text')
					{
						$item['ignore_space'] = 0;
						$item['size'] = 20;
						$item['forpanel'] = 1;
						$item['preview'] = CustomImage::preview($item, 0);
					}
					else
					{
						if ($item['clr'] || (int)$item['color'])
						{
							$data = array();
							$data['imagecolor'] = !(int)$item['color'] ? $item['clr'] : (int)$item['color'];
							$data['image_src'] = $this->module->getBaseDir().$item['text'];
							$item['text'] = $this->module->getCacheDir().basename(CustomImage::colorizeImage($data, 0));
						}
					}
					$item['x'] = ($measure['x_origin'] - $item['x'] * $width_ratio) * $cmratio;
					$item['y'] = ($measure['y_origin'] - $item['y'] * $width_ratio) * $cmratio;
					$item['width'] = $item['width'] * $item['scalex'] * $width_ratio * $cmratio;
					$item['height'] = $item['height'] * $item['scaley'] * $width_ratio * $cmratio;
				}

				$base_url = __PS_BASE_URI__;
				$module_dir = $base_url.'modules/customtextdesign/';

				$this->context->smarty->assign(array(
					'ctd_module_dir' => $module_dir,
					'custom_product' => $custom_product,
					'items' => $items,
					'colors' => $colors,
					'fonts' => $fonts,
					'materials' => $materials,
					'product_price' => Tools::ps_round($product->price, 2),
					'total_price' => Tools::ps_round($product->price, 2) + Tools::ps_round($custom_product['price'], 2)
				));
			}

			$this->context->smarty->assign(array(
				'module_dir' => $this->module->getPath(),
				'preview' => $preview
			));
			$this->setTemplate('preview.tpl');
		}
	}