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

class AdminPdfOutputController extends ModuleAdminController{

	/** @var customtextdesign */
	public $module;

	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
	}

	public function initContent()
	{
		parent::initContent();

		$cfg = $this->module->getConfigKeys();
		$module_dir = $this->module->getPath();

		$this->displayHeader(false);
		$this->displayFooter(false);

		$id_custom_product = Tools::getValue('id_custom_product');

		$custom_product = $this->module->getCustomProduct($id_custom_product);
		$items = $this->module->getItems($id_custom_product);

		$id_product = $custom_product['id_product'];
		$id_product_attribute = $custom_product['id_attribute'];
		$id_image = $custom_product['id_image'];
		$panel_width = $custom_product['width'];
		CustomDesign::$panel_width = $panel_width;
		CustomDesign::$custom_width = $custom_product['product_width'];
		CustomDesign::$custom_height = $custom_product['product_height'];
		CustomDesign::$custom_color = $custom_product['product_color'];
		CustomDesign::init($id_product, $id_product_attribute, $id_image);
		$custom_product['preview'] = CustomDesign::renderPreview($custom_product, $items);

		$measure = $this->module->getProductMeasures($id_product, $id_image);
		$image = $this->module->getImagePath($id_image, Configuration::get($this->module->name.'image_type'));
		list($original_width, $original_height) = getimagesize($image);

		//from px to cm
		if ($custom_product['product_width'] * $custom_product['product_height'])
			$cmratio = $custom_product['product_width'] / $custom_product['width'];
		elseif ($measure)
			$cmratio = $measure['size'] / $measure['width'];

		$width_ratio = $original_width / $custom_product['width'];

		foreach ($items as &$item)
		{
			if ($item['type'] == 'text')
			{
				$item['type'] = 'img';
				$item['size'] = 20;
				$item['forpanel'] = 1;
				$item['ignore_space'] = 0;
				$item['preview'] = CustomImage::preview($item, 0);
				$item['type'] = 'text';
			}

			if ((float)$item['angle'])
			{
				$angle = $item['angle'];
				$src = '';
				if ($item['type'] == 'text')
					$src = _PS_BASE_URL_.$module_dir.'data/cache/'.$item['preview'].'.png';
				else
					$src = _PS_BASE_URL_.$item['text'];
				$resz_image = CustomImage::resize($src, $item['width'], $item['height']);

				$image_type = $this->module->exifImageType($src);
				$temp_image = ImageManager::create($image_type, $src);

				$temp_image = CustomImage::changeRatio($temp_image, $item['width'] / $item['height']);

				$bg_color = imagecolorallocatealpha($temp_image, 0, 0, 0, 127);
				$temp_image = imagerotate($temp_image, -(float)$angle, $bg_color);
				imagecolordeallocate($temp_image, $bg_color);
				imagealphablending($temp_image, 0);
				imagesavealpha($temp_image, 1);

				$bg_color = imagecolorallocatealpha($resz_image, 0, 0, 0, 127);
				$resz_image = imagerotate($resz_image, -(float)$angle, $bg_color);
				imagecolordeallocate($resz_image, $bg_color);

				$new_width = imagesx($resz_image);
				$new_height = imagesy($resz_image);

				$item['width'] = $new_width;
				$item['height'] = $new_height;

				if ($item['type'] == 'text')
					$item['preview'] = $item['preview'].'_angle';
				else
					$item['preview'] = $item['id'].'_angle';
				imagepng($temp_image, dirname(__FILE__).'/../../data/cache/'.$item['preview'].'.png');
				imagedestroy($temp_image);
			}
			$item['x'] = ($item['x']) * $cmratio * $width_ratio;
			$item['y'] = ($item['y']) * $cmratio * $width_ratio;
			$item['width'] = $item['width'] * $cmratio * $width_ratio;
			$item['height'] = $item['height'] * $cmratio * $width_ratio;
		}

		$base_url = __PS_BASE_URI__;
		$module_dir = $base_url.'modules/customtextdesign/';

		$full_width = $original_width * $cmratio;
		$full_height = $original_height * $cmratio;

		if ($custom_product['product_width'] * $custom_product['product_height'])
		{
			$full_width = (float)$custom_product['product_width'];
			$full_height = (float)$custom_product['product_height'];
		}

		require_once($this->module->localpath.'../../tools/tcpdf/tcpdf.php');

		$pdf = new TCPDF('L', 'cm', array($full_width + 10, $full_height + 10));
		$pdf->SetTitle('Custom Product '.$id_custom_product);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->AddPage();

		if ((int)$cfg['show_base_img'] || !empty($custom_product['product_color']))
		{
			if (! empty($custom_product['product_color']))
			{
				$data = array();
				$data['customcolor'] = $custom_product['product_color'];
				$data['id_product'] = $id_product;
				$data['id_image'] = $id_image;
				$image = CustomImage::colorize($data, false, $image);
			}
			$pdf->Image($image, 0, 0, $full_width, $full_height);
		}
		if (!Tools::getIsset('sub'))
		{
			foreach ($items as &$item)
			{
				if ($item['type'] == 'text')
					$image = _PS_BASE_URL_.$module_dir.'data/cache/'.$item['preview'].'.png';
				else
				{
					$image = _PS_BASE_URL_.$item['text'];
					if ((float)$item['angle'])
						$image = _PS_BASE_URL_.$module_dir.'data/cache/'.$item['id'].'_angle.png';
					if ($item['clr'] || (int)$item['color'])
					{
						$data = array();
						$data['imagecolor'] = !(int)$item['color'] ? $item['clr'] : (int)$item['color'];
						$data['image_src'] = $image;
						$image = $this->module->getCacheDir().basename(CustomImage::colorizeImage($data, 0));
					}
				}
				$pdf->Image($image, $item['x'], $item['y'], $item['width'], $item['height']);
			}
			//add mask
			$mask = $this->module->getProductMasks($custom_product['id_product'], $custom_product['id_image']);
			if ($mask)
			{
				$image_mask = _PS_BASE_URL_.$module_dir.'data/mask/'.$mask['image'];
				$pdf->Image($image_mask, 0, 0, $full_width, $full_height);
			}
		}
		else
		{
			$sub_image = CustomDesign::getSub($id_custom_product);
			$sub_image = _PS_BASE_URL_.$module_dir.'data/cache/'.$sub_image;
			$pdf->Image($sub_image, 0, 0, $full_width, $full_height);
		}
		$pdf->Output('Custom Product '.$id_custom_product.'.pdf', 'D');

	}
}