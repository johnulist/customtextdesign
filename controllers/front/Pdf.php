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

	class CustomTextDesignPdfModuleFrontController extends ModuleFrontController{

		/** @var customtextdesign */
		public $module;

		public function __construct()
		{
			parent::__construct();
			$this->context = Context::getContext();
		}

		public function initContent()
		{
			$ps_admin = new Cookie('psAdmin');
			if (!$ps_admin->id_employee)
				Tools::redirectLink('404');
			$this->module->requireClass('CustomImage');
			$this->module->requireClass('CustomDesign');
			$uniqid = pSQL(Tools::getValue('id'));
			$id_product = pSQL(Tools::getValue('id_product'));

			$sql = new DbQuery();
			$sql->from($this->module->name.'_design');
			if ($uniqid)
				$sql->where("uniqid = '$uniqid'");
			else
				$sql->where('id_product = '.(int)$id_product);
			$design = Db::getInstance()->getRow($sql);

			if (is_array($design))
			{

				$data = array();
				$data['text'] = $design['text'];
				$data['font'] = $design['font'];
				$data['color'] = $design['color'];
				$data['clr'] = '';
				$data['alpha'] = 0;
				$data['ignore_space'] = 0;
				$data['mirror'] = $design['mirror'];
				$data['center'] = 0;
				$data['size'] = 10;
				$data['type'] = 'img';
				$data['forpanel'] = 0;
				$data['curve'] = 0;

				$this->module->getPath();
				$preview = CustomImage::preview($data, false);
				$preview_path = $this->module->localpath.'data/cache/'.$preview.'.png';

				list($width, $height) = getimagesize($preview_path);

				$full_height = (float)$design['size'];
				$full_width = $full_height / $height * $width;

				$color = $this->module->getColor($design['color']);
				$rgb = CustomImage::hex2rgb($color['color']);
				$font = $this->module->getFont($design['font']);
				$fontfile = $this->module->localpath.'data/font/'.$font['file'];

				require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');
				require_once($this->module->localpath.'inc/tcpdf/tcpdf_fonts.php');
				require_once($this->module->localpath.'inc/tcpdf_patch.php');

				$pdf = new TcpdfPatch('L', 'cm', array($full_width + 10, $full_height + 10));
				$pdf->SetTitle('Custom Product '.$design['id_product']);
				$pdf->setPrintHeader(false);

				$fontname = $pdf->addTTFfont($fontfile, 'TrueType', '', 32);
				$pt = 28.3464567 * (int)$design['size'];

				$pdf->SetMargins(0, 0, 0);
				$pdf->AddFont($fontname, '', $fontname.'.php');
				$pdf->SetFont($fontname, '', $pt, '', 'false');
				$pdf->SetTextColor($rgb[0], $rgb[1], $rgb[2]);
				$pdf->AddPage();

				$pdf->Text(1, 1, $design['text']);
				$pdf->Output('Custom Product '.$design['id_product'].'.pdf', 'D');
				exit();
			}
		}
	}