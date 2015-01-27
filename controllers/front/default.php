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

	class CustomTextDesignDefaultModuleFrontController extends ModuleFrontController{

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

			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product.css');
			$this->context->controller->addCSS($this->module->path.'css/jquery.jscrollpane.css');
			$this->context->controller->addCSS($this->module->path.'css/colorpicker.css');
			$this->context->controller->addCSS($this->module->path.'css/style.css');

			$this->context->controller->addJqueryUI(array('ui.core', 'ui.widget', 'ui.button'));
			$this->context->controller->addJqueryUI(array('ui.resizable', 'ui.draggable', 'ui.slider'));
			$this->context->controller->addJS($this->module->path.'js/jquery.ui.spinner.min.js');
			$this->context->controller->addJS($this->module->path.'js/jquery.ddslick.min.js');
			$this->context->controller->addJS($this->module->path.'js/jquery.jscrollpane.min.js');
			$this->context->controller->addJS($this->module->path.'js/colorpicker/js/colorpicker.js');
			$this->context->controller->addJS($this->module->path.'js/customtextdesign.js');
			$this->context->controller->addJS($this->module->path.'js/jquery.ui.touch-punch.min.js');
			$this->context->controller->addCSS($this->module->path.'css/jquery.ui.spinner-base.css');
			$this->context->controller->addCSS($this->module->path.'css/jquery.ui.spinner-theme.css');
		}

		public function initContent()
		{
			parent::initContent();
			$cfg = $this->module->getConfigKeys();

			if (!$cfg['page_accessible'] || !$this->module->active)
			{
				Tools::redirect(__PS_BASE_URI__.'index.php?controller=404');
				exit();
			}

			if ($cfg['login_required'])
			{
				if (! $this->context->cookie->isLogged())
					Tools::redirect('authentication.php');
			}

			$this->context->smarty->assign('currency', $this->context->currency);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->module->name.'_color WHERE 1 ORDER BY position ASC';
			$colors = Db::getInstance()->ExecuteS($query);
			$colorcount = count($colors);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->module->name.'_font WHERE 1 ORDER BY position ASC';
			$fonts = Db::getInstance()->ExecuteS($query);
			$fontcount = count($fonts);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->module->name.'_material WHERE 1 ORDER BY position ASC';
			$materials = Db::getInstance()->ExecuteS($query);
			$materialcount = count($fonts);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->module->name.'_material_prices WHERE 1';
			$material_prices_rows = Db::getInstance()->ExecuteS($query);

			$module_dir = $this->module->path;

			$tax_rate = 0;
			$price_display_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
			if ($id_tax_rule_group = (int)$cfg['used_tax'] && $price_display_method != PS_TAX_EXC)
			{

				if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
					$address_id = (int)$this->context->cart->id_address_invoice;
				else
					$address_id = (int)$this->context->cart->id_address_delivery;
				if (!Address::addressExists($address_id))
					$address_id = null;

				$address = Address::initialize($address_id);

				$tax_manager = TaxManagerFactory::getManager($address, $id_tax_rule_group);
				$tax_calculator = $tax_manager->getTaxCalculator();

				$tax_rate = $tax_calculator->getTotalRate();
			}

			$cfg['tax_rate'] = $tax_rate;
			$price_reduction = Group::getReductionByIdGroup((int)Group::getCurrent()->id);
			$cfg['price_reduction'] = $price_reduction;

			$this->context->smarty->assign(array(
				'module'=>$this->module->name,
				'colors' => $colors,
				'colorcount' => $colorcount,
				'fonts' => $fonts,
				'fontcount' => $fontcount,
				'materials' => $materials,
				'materialcount' => $materialcount,
				'material_prices_rows' => $material_prices_rows,
				'customtextdesign_config' => $cfg,
				'customtextdesign_category' => $this->module->category->id_category,
				'languages' => $this->module->languages,
				'default_lang' => $this->context->language->id,
				'module_dir' => $module_dir,
				'id_page_config' => 0,
			));
			$this->setTemplate('default.tpl');
		}
	}