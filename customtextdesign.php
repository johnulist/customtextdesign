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

	if (!defined('_PS_VERSION_'))
		exit;
	define('_CTD_CACHE_', 1);
	require_once('inc/CustomDesign.php');
	require_once('inc/CustomImage.php');

	class customtextdesign extends Module
	{
		protected $admin = null;
		protected $errors = array();
		protected $warnings = array();
		public $path = null;
		public $localpath = null;
		public $config_keys = null;
		public $category = null;
		public $languages = null;
		public $customdesign = null;
		public $customimage = null;

		public $currentIndex = 'index.php?controller=AdminModules';

		public $options = null;
		public $custom_products = null;
		public $custom_product = null;
		public $id_customization_field = null;
		public $items = null;
		public $module_pages = null;

		public $extra_str = null;

		public $colors = null;
		public $fonts = null;
		public $materials = null;

		public $config = array();

		public $html_content = '';

		public function __construct()
		{
			$this->name = 'customtextdesign';
			$this->tab = 'front_office_features';
			$this->author = 'Tuni-Soft';
			$this->version = '4.1.8';
			$this->need_instance = 0;
			$this->secure_key = Tools::encrypt($this->name);
			$this->module_key = 'f56556ea4b068729810a735ab33a4078';
			$this->path = $this->_path;
			$image_type = 'thickbox';
			$image_type .= '_default';
			$this->config_keys = array(
				'_text_init' => '',
				'num_text_length' => 0,
				'num_text_lines' => 1,
				'ignore_space' => '1',
				'font_color' => '#17DAFF',
				'num_size_init' => 10,
				'num_size_min' => 5,
				'num_size_max' => 30,
				'mirror_show' => '',
				'login_required' => '',
				'page_accessible' => 1,
				'base_price' => 0,
				'used_tax' => 0,
				'image_type' => $image_type, //default image type
				'del_number' => 1,
				'del_span' => 1,
				'show_prices' => 0,
				'show_base_img' => 1,
				'a_carriers' => '',
			);
			parent::__construct();
			$this->displayName = $this->l('Product Customization');
			$this->description = $this->l('Allow your customers to preview and order their customized products');
			$this->confirmUninstall = $this->l('All data associated with the module will be lost! Are you sure you want to proceed ?');
			$this->extra_str = array($this->l('Custom'), $this->l('Tax incl.'), $this->l('Download'), $this->l('Area'), $this->l('Dimensions'),
				$this->l('Color'), $this->l('Material'), $this->l('Font'), $this->l('Text'), $this->l('Product Customization'),
				$this->l('This product adds the total customizations cost to your cart'));
			$this->getCart();
			$id_lang = $this->context->language->id;
			$this->languages = Language::getLanguages();
			$cat_search = Category::searchByName($id_lang, $this->name);
			if (isset($cat_search[0]))
				$this->category = new Category($cat_search[0]['id_category'], $id_lang);
			else
				$this->createCustomCategory();
			$this->customdesign = new CustomDesign();
			$this->customimage = new CustomImage();
			$this->loadConfig();
		}

		public function install()
		{
			if (!extension_loaded('curl'))
			{
				$this->_errors[] = 'Please activate the PHP extension \'curl\' to use the \'Product Customization\' module';
				return false;
			}
			if (!parent::install())	return false;
			if (!(
				$this->registerHook('displayHeader')
				&& $this->registerHook('displayBackOfficeHeader')
				&& $this->registerHook('displayFooterProduct')
				&& $this->registerHook('customTextDesign')
				&& $this->registerHook('displayProductButtons')
				&& $this->registerHook('displayAdminProductsExtra')
				&& $this->registerHook('displayTop')
				&& $this->registerHook('displayOrderDetail')
				&& $this->registerHook('actionCartSave')
				&& $this->registerHook('actionValidateOrder')
			)) return false;

			if (!$this->execSQL()) return false;

			foreach ($this->config_keys as $key => $value)
			{
				if (Tools::substr($key, 0, 1) == '_')
				{
					foreach ($this->languages as $lang)
					{
						$id_language = $lang['id_lang'];
						$akey = Tools::substr($key, 1).'_'.$id_language;
						Configuration::updateValue($this->name.$akey, $value);
					}
					continue;
				}
				Configuration::updateValue($this->name.$key, $value);
			}

			$this->languages = Language::getLanguages();

			//Admin Pdf Output
			$tab = new Tab();
			foreach ($this->languages as $lang)
				$tab->name[$lang['id_lang']] = 'Admin Pdf Output';
			$tab->class_name = 'AdminPdfOutput';
			$tab->id_parent = -1;
			$tab->module = $this->name;
			$tab->active = 1;
			if (! $tab->add()) return false;

			//Admin Ajax Module
			$tab = new Tab();
			foreach ($this->languages as $lang)
				$tab->name[$lang['id_lang']] = 'Admin Ajax Module';
			$tab->class_name = 'AdminAjaxModule';
			$tab->id_parent = -1;
			$tab->module = $this->name;
			$tab->active = 1;
			if (! $tab->add()) return false;

			//Admin Upload Module
			$tab = new Tab();
			foreach ($this->languages as $lang)
				$tab->name[$lang['id_lang']] = 'Admin Upload Module';
			$tab->class_name = 'AdminUploadModule';
			$tab->id_parent = -1;
			$tab->module = $this->name;
			$tab->active = 1;
			if (! $tab->add()) return false;

			$id_lang = $this->context->language->id;
			$cat_search = Category::searchByName($id_lang, $this->name);
			if (!isset($cat_search[0]))
				$this->createCustomCategory();
			return true;
		}

		public function execSQL()
		{
			if (!file_exists(dirname(__FILE__).'/install.sql'))
				return (false);
			elseif (!$sql = Tools::file_get_contents(dirname(__FILE__).'/install.sql'))
				return (false);
			$sql = str_replace('__PREFIX', _DB_PREFIX_.$this->name, $sql);
			$sql = str_replace('_MYSQL_ENGINE_', _MYSQL_ENGINE_, $sql);
			$sql = preg_split('/;\s*[\r\n]+/', $sql);
			foreach ($sql as $query)
			{
				if (!Db::getInstance()->Execute(trim($query)))
					return false;
			}
			return true;
		}

		public function createCustomCategory()
		{
			$this->category = new Category(0);
			$root = $this->category->getRootCategory()->id;
			$category_name = array();
			$category_link_rewrite = array();
			$category_description = array();
			foreach ($this->languages as $lang)
			{
				$category_name["{$lang['id_lang']}"] = $this->name;
				$category_link_rewrite["{$lang['id_lang']}"] = $this->name;
				$category_description["{$lang['id_lang']}"] = 'Hidden category used by the module Custom Text Design';
			}
			$this->category->name = $category_name;
			$this->category->link_rewrite = $category_link_rewrite;
			$this->category->description = $category_description;
			$this->category->id_parent = $root;
			$this->category->active = 0;
			$this->category->add();
		}

		public function checkAddress()
		{
			$this->hookActionCartSave();
			$id_cart = $this->context->cart ? $this->context->cart->id : 0;
			if (!$id_cart) return;
			$id_customer = $this->context->cookie ? $this->context->cookie->id_customer : 0;
			$id_address_delivery = 0;
			if ($id_customer)
			{
				$id_address_delivery = $this->context->cart ? $this->context->cart->id_address_delivery : 0;
				if ($id_cart)
				{
					Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'customization` SET `id_address_delivery` = '.(int)$id_address_delivery
						.' WHERE `id_cart` = '.(int)$id_cart.' AND `id_address_delivery`=0');
				}
			}
		}

		public function addCustomField($id_product, $no_add = false)
		{
			if (_CTD_CACHE_ && isset($this->id_customization_field[$id_product])) return $this->id_customization_field[$id_product];

			$this->checkAddress();

			$field_table = $this->name.'_field';
			$sql = new DbQuery();
			$sql->from($field_table);
			$sql->where('id_product = '.(int)$id_product);
			$row = Db::getInstance()->getRow($sql, false);

			if ($row)
			{
				//check if customization really exists
				$sql = new DbQuery();
				$sql->from('customization_field');
				$sql->where('id_customization_field = '.(int)$row['id_customization_field']);
				$customization_field = Db::getInstance()->getRow($sql, false);
				if (! $customization_field)
				{
					Db::getInstance()->delete($field_table, 'id_product = '.(int)$id_product);
					return $this->addCustomField($id_product);
				}
				else
				{
					$options = $this->getProductOptions($id_product);
					if ((int)$customization_field['required'] != (int)$options['required'])
					{
						Db::getInstance()->update('customization_field', array('required' => $options['required']),
							'id_customization_field = '.(int)$row['id_customization_field']);
						if ($no_add) return;
					}

					$product = new Product($id_product);
					$sql = new DbQuery();
					$sql->from('customization_field');
					$sql->where('id_product = '.(int)$id_product);
					$customization_fields = Db::getInstance()->executeS($sql, true, false);
					$count = count($customization_fields);

					if (!$product->customizable || (int)$product->text_fields != $count)
					{
						$product->text_fields = $count;
						$product->customizable = 1;
						$product->update();
					}
					return $this->id_customization_field[$id_product] = $row['id_customization_field'];
				}
			}

			if ($no_add) return;

			$options = $this->getProductOptions($id_product);
			$data = array(
				'id_product' => (int)$id_product,
				'type' => Product::CUSTOMIZE_TEXTFIELD,
				'required' => (int)$options['required']
			);

			Db::getInstance()->insert('customization_field', $data);
			$id_customization_field = Db::getInstance()->Insert_ID();
			Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', 1);

			$field_label = array(
				'en' => 'Customization',
				'fr' => 'Personnalisation'
			);

			foreach ($this->languages as $lang)
			{
				$iso = $lang['iso_code'];
				$id_lang = $lang['id_lang'];
				$label = isset($field_label[$iso]) ? $field_label[$iso] : $field_label['en'];
				$data = array(
					'id_customization_field' => (int)$id_customization_field,
					'id_lang' => (int)$id_lang,
					'name' => $label,
				);
				Db::getInstance()->insert('customization_field_lang', $data);
			}

			$data = array(
				'id_product' => (int)$id_product,
				'id_customization_field' => (int)$id_customization_field
			);

			Db::getInstance()->insert($field_table, $data);
			$product = new Product($id_product);

			$sql = new DbQuery();
			$sql->from('customization_field');
			$sql->where('id_product = '.(int)$id_product);
			$customization_fields = Db::getInstance()->executeS($sql, true, false);
			$count = count($customization_fields);

			if (!$product->customizable || (int)$product->text_fields != $count)
			{
				$product->text_fields = $count;
				$product->customizable = 1;
				$product->update();
			}
			return $this->id_customization_field[$id_product] = $id_customization_field;
		}

		public function getSpecialProduct()
		{
			$id_special = (int)Configuration::get($this->name.'_special', null, $this->context->shop->id_shop_group, $this->context->shop->id);
			$product = new Product($id_special, false, null, $this->context->shop->id, $this->context);
			if (Validate::isLoadedObject($product))
				return $id_special;

			$translations = array();
			$source = $this->name;
			foreach ($this->languages as $language)
			{
				$id_lang = $language['id_lang'];
				$iso_lang = $language['iso_code'];
				$translations['name'][$id_lang] = $this->translate('Product Customization', $iso_lang, $source);
				$translations['description'][$id_lang] = $this->translate('This product adds the total customizations cost to your cart', $iso_lang, $source);
				$translations['link_rewrite'][$id_lang] = Tools::str2url($translations['name'][$id_lang]);
			}
			$root_category = Category::getRootCategory();
			$product = new Product();
			$product->name = $translations['name'];
			$product->link_rewrite = $translations['link_rewrite'];
			$product->id_category_default = $root_category->id_category;
			$product->category = array($root_category->id_category);
			$product->description = $translations['description'];
			$product->description_short = $translations['description'];
			$product->available_for_order = 1;
			$product->visibility = 'search';
			$product->price = 0;
			$product->out_of_stock = 2;
			$product->active = 1;
			$product->show_price = 0;
			$product->indexed = 0;
			$product->id_shop_list = array($this->context->shop->id);
			$product->id_shop_default = $this->context->shop->id;
			$product->save();
			Configuration::updateValue($this->name.'_special', (int)$product->id, false, $this->context->shop->id_shop_group, $this->context->shop->id);
			$product->updateCategories($product->category, true);
			StockAvailable::setQuantity($product->id, 0, 10000, $this->context->shop->id);
			StockAvailable::setProductOutOfStock($product->id, 1, $this->context->shop->id);

			$image = new Image();
			$image->id_product = (int)$product->id;
			$image->id_shop_list = array($this->context->shop->id);
			$image->position = Image::getHighestPosition($product->id) + 1;
			$image->cover = 1;
			if (!$image->add())
				$this->displayError('Error while creating additional image');
			else
			{
				$new_path = $image->getPathForCreation();
				$image_path = realpath($this->getDir().'img/custom_product.jpg');

				ImageManager::resize($image_path, $new_path.'.'.$image->image_format);
				$images_types = ImageType::getImagesTypes('products');
				foreach ($images_types as $image_type)
				{
					if (!ImageManager::resize($image_path, $new_path.'-'.Tools::stripslashes($image_type['name']).'.'.
						$image->image_format, $image_type['width'], $image_type['height'], $image->image_format))
						exit(Tools::jsonEncode(array('error' => Tools::displayError('An error occurred while copying image:').
							' '.Tools::stripslashes($image_type['name']))));
				}
				$image->update();
			}
			return $product->id;
		}

		public function cartHasCustomization()
		{
			if (!$id_cart = $this->getCart())
				return false;

			$customized_datas = Product::getAllCustomizedDatas($id_cart, $this->context->language->id, true);
			$custom_products = $this->getCustomProducts(null, null, $id_cart);
			$in_cart = 0;
			if (is_array($customized_datas))
			{
				foreach ($customized_datas as $attribute_customization)
				{
					foreach ($attribute_customization as $address_customization)
					{
						foreach ($address_customization as $customization)
						{
							foreach ($customization as $customization_datas)
							{
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
											$in_cart = 1;
											break 5;
										}
									}
								}
							}
						}
					}
				}
			}
			return $in_cart;
		}

		public function hasCustomization($custom_product)
		{
			$id_customization_field = $this->addCustomField($custom_product['id_product']);
			$sql = new DbQuery();
			$sql->from('customized_data', 'cd');
			$sql->innerJoin('customization', 'cc', 'cc.id_customization = cd.id_customization');
			$sql->where('cd.`index` = '.(int)$id_customization_field." AND cd.`value`='[".$custom_product['id_custom_product']."]' AND cc.in_cart = 1");
			$sql->select('value');
			$result = Db::getInstance()->getValue($sql);
			return $result;
		}

		public function storeCustomization($id_customization)
		{
			return Db::getInstance()->insert($this->name.'_customization', array('id_customization' => $id_customization));
		}

		public function addCustomization($id_customization)
		{
			$sql = 'UPDATE `'._DB_PREFIX_.'customization` set in_cart = 1 WHERE id_customization = '.(int)$id_customization;
			$return = DB::getInstance()->execute($sql);
			DB::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customtextdesign_customization WHERE id_customization = '.(int)$id_customization);
			return $return;
		}

		public function hookDisplayHeader()
		{
			if (! method_exists($this->context->controller, 'addJquery'))
				return false;
			$ctd_active = false;
			$controller_name = Tools::getValue('controller');
			$this->context->controller->addJquery();

			$output = '';
			$this->smarty->assign('ctd_version', $this->version);
			$output .= $this->display(__FILE__, 'views/templates/hook/DisplayGlobal.tpl');

			if ($controller_name == 'cms')
			{
				$link = new Link();
				$default_link = $link->getModuleLink($this->name, '_link_', array('content_only' => 1));
				$this->smarty->assign('default_link', $default_link);
				$this->context->controller->addCSS($this->_path.'css/cms_embed.css');
				$this->context->controller->addJS($this->_path.'js/cms_embed.js');
				$output .= $this->display(__FILE__, 'views/templates/hook/DisplayHeader.tpl');
				return $output;
			}

			if ($controller_name == 'history')
			{
				$this->context->controller->addCSS($this->_path.'css/history.css');
				$this->context->controller->addCSS($this->_path.'css/bo_style.css');
			}

			if ($controller_name == 'order' || $controller_name == 'order-opc' || $controller_name == 'orderopc' || $controller_name == 'checkout_klarna')
			{
				$this->checkAddress();
				$this->context->controller->addCSS($this->_path.'css/bo_style.css');
				$this->context->controller->addJS($this->_path.'js/cart.js');
			}

			if ($controller_name == 'product')
			{
				$id_product = (int)Tools::getValue('id_product');
				$tproducts = $this->name.'_product';
				$sql = new DbQuery();
				$sql->from($tproducts);
				$sql->where('id_product = '.(int)$id_product);
				$ctd_product = Db::getInstance()->getRow($sql, false);
				$ctd_active = ($ctd_product && $ctd_product['active']);
				$this->context->controller->addJS($this->_path.'js/cust-form.js');
			}

			if ($ctd_active)
			{
				$this->context->controller->addCSS(_THEME_CSS_DIR_.'product.css');
				//___$this->context->controller->addCSS($this->_path.'css/ui/jquery-ui-1.10.3.custom.css');
				$this->context->controller->addCSS($this->_path.'css/jquery.jscrollpane.css');
				//___$this->context->controller->addJS($this->_path.'js/ui/jquery-ui-1.10.3.custom.min.js');
				$this->context->controller->addJqueryUI(array('ui.resizable', 'ui.draggable', 'ui.slider'));
				$this->context->controller->addJS($this->_path.'js/resizable-rotation.patch.js');
				if (! $ctd_active)
				{
					$this->context->controller->addCSS($this->_path.'css/style.css');
					$this->context->controller->addJS($this->_path.'js/customtextdesign.js');
				}
				else
				{
					$this->context->controller->addCSS($this->_path.'css/style_product.css');
					$this->context->controller->addCSS($this->_path.'css/jquery.ui.rotatable.css');
					$this->context->controller->addCSS($this->_path.'css/colorpicker.css');
					$this->context->controller->addJS($this->_path.'js/jquery.ui.rotatable.min.js');
					$this->context->controller->addJS($this->_path.'js/jquery.ui.touch-punch.min.js');
					$this->context->controller->addJS($this->_path.'js/colorpicker/js/colorpicker.js');
					$this->context->controller->addJS($this->_path.'js/customtextdesign_product.js');
				}
				$this->context->controller->addJS($this->_path.'js/jquery.ddslick.min.js');
				$this->context->controller->addJS($this->_path.'js/jquery.mousewheel.js');
				$this->context->controller->addJS($this->_path.'js/jquery.jscrollpane.min.js');
			}

			return $output;
		}

		public function hookDisplayBackOfficeHeader()
		{
			if (! method_exists($this->context->controller, 'addJquery'))
				return false;
			$this->context->controller->addJquery();

			$output = '';
			$controller_name = $this->context->controller->controller_name;

			if (empty($controller_name))
				$controller_name = Tools::getValue('controller');

			$id_lang = $this->context->language->id;
			if ($controller_name == 'AdminProducts' && Tools::getIsset('id_product'))
			{
				$this->context->controller->addJqueryUI('ui.tabs');
				$this->context->smarty->assign(array(
					'id_product' => (int)Tools::getValue('id_product'),
					'secure_key' => $this->secure_key,
					'languages' => $this->languages,
					'id_lang' => $id_lang,
					'link' => new Link(),
				));
				$this->context->controller->addCSS($this->_path.'css/colorpicker.css');
				$this->context->controller->addJS($this->_path.'js/colorpicker/js/colorpicker.js');
				$this->context->controller->addCSS($this->_path.'css/admin-products.css');
				$this->context->controller->addJS($this->_path.'js/admin-products.js');
				$this->context->controller->addJqueryUI('ui.draggable');
				$this->context->controller->addJqueryUI('ui.resizable');
				$output .= $this->display(__FILE__, 'views/templates/hook/DisplayBackOfficeHeader.tpl');
			}

			if ($controller_name == 'AdminProducts' && ! Tools::getIsset('id_product'))
			{
				$tproducts = _DB_PREFIX_.$this->name.'_product';
				$ids = Db::getInstance()->ExecuteS("SELECT id_product FROM $tproducts WHERE active = 1");
				$this->context->smarty->assign(array(
					'ctd_ids' => $ids,
				));
				$output .= $this->display(__FILE__, 'views/templates/hook/DisplayBackOfficeProductsTable.tpl');
			}

			if ($controller_name == 'AdminModules' && Tools::getIsset('submitGlobalConfig'))
			{
				$token = Tools::getAdminTokenLite('AdminModules');
				$this->context->controller->addJqueryUI('ui.tabs');
				$this->context->smarty->assign(array(
					'id_product' => 0,
					'secure_key' => $this->secure_key,
					'languages' => $this->languages,
					'id_lang' => $id_lang,
					'link' => new Link(),
					'token' => $token,
				));
				$this->context->controller->addCSS($this->_path.'css/colorpicker.css');
				$this->context->controller->addJS($this->_path.'js/colorpicker/js/colorpicker.js');
				$this->context->controller->addCSS($this->_path.'css/admin-products.css');
				$this->context->controller->addJS($this->_path.'js/admin-products-global.js');
				$output .= $this->display(__FILE__, 'views/templates/hook/DisplayBackOfficeHeader.tpl');
			}

			$output .= '<link href="'.$this->_path.'css/bo_style.css" rel="stylesheet" type="text/css" media="all" />';
			return $output;
		}

		public function hookCustomTextDesign()
		{
			return $this->hookDisplayFooterProduct();
		}

		public function hookDisplayFooterProduct()
		{
			//do some cleaning
			$this->deleteOldUploads();
			$this->checkAddress();

			$id_cart = (int)$this->context->cart->id;
			$id_product = (int)Tools::getValue('id_product');

			$ps_admin = new Cookie('psAdmin');
			if ($ps_admin->id_employee)
			{
				$table_design = $this->name.'_design';
				$sql = new DbQuery();
				$sql->from($table_design);
				$sql->where('id_product = '.(int)$id_product);
				$ctd_product = Db::getInstance()->getRow($sql, false);

				if ($ctd_product)
				{
					$link = new Link();
					$href = htmlspecialchars($link->getModuleLink($this->name, 'Pdf', array('id_product' => $id_product)));
					return "<div style='color: #1ABCFC;font-weight: bold;'><strong><span>PDF:</span></strong> <span>
					<a style='color: #1ABCFC;' href='$href' target='_blank'>{$this->l('Download')}</a></span></div>";
				}
			}

			$table_product = $this->name.'_product';
			$sql = new DbQuery();
			$sql->from($table_product);
			$sql->where('id_product = '.(int)$id_product);
			$ctd_product = Db::getInstance()->getRow($sql, false);

			if (! $ctd_product || ! $ctd_product['active']) return;

			$id_lang = (int)$this->context->language->id;
			$table_product_trans = $this->name.'_product_trans';
			$sql = new DbQuery();
			$sql->from($table_product_trans);
			$sql->where("id_product = $id_product AND id_lang = $id_lang");
			$product_trans = Db::getInstance()->getRow($sql, false);

			$cfg = $this->getConfigKeys();
			if ($product_trans && isset($product_trans['text_init']))
				$cfg['text_init_'.$id_lang] = $product_trans['text_init'];
			if ($product_trans && isset($product_trans['title']))
				$cfg['title_'.$id_lang] = $product_trans['title'];
			if ($product_trans && isset($product_trans['instructions']))
				$cfg['instructions_'.$id_lang] = $product_trans['instructions'];

			$currency = $this->context->currency;
			$this->context->smarty->assign('currency', $currency);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_color WHERE 1 ORDER BY position ASC';
			$colors = Db::getInstance()->ExecuteS($query);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_font WHERE 1 ORDER BY position ASC';
			$fonts = Db::getInstance()->ExecuteS($query);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_group WHERE 1 ORDER BY position ASC';
			$groups = Db::getInstance()->ExecuteS($query);

			if (is_array($groups))
			{
				foreach ($groups as &$group)
				{
					$query = 'SELECT SUM(quantity) as image_total FROM '._DB_PREFIX_.$this->name.'_image WHERE id_group = '.(int)$group['id'];
					$group['image_total'] = Db::getInstance()->getValue($query);
				}
			}

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_image WHERE 1 ORDER BY position ASC';
			$images = Db::getInstance()->ExecuteS($query);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_material WHERE 1 ORDER BY position ASC';
			$materials = Db::getInstance()->ExecuteS($query);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_material_prices WHERE 1';
			$material_prices_rows = Db::getInstance()->ExecuteS($query);

			$custom_fields = $this->getProductFields($id_product);

			$cftrans = $this->name.'_custom_field_trans';
			$sql = new DbQuery();
			$sql->from($cftrans);
			$sql->where('id_product = '.(int)$id_product.' AND id_lang = '.(int)$id_lang);
			$cf_translations = Db::getInstance()->executeS($sql);
			$cf_lang = $this->organizeDoubleBy('id_custom_field', 'id_lang', $cf_translations, true);

			if (is_array($custom_fields))
			{
				foreach ($custom_fields as &$custom_field)
				{
					$id_custom_field = (int)$custom_field['id_custom_field'];
					if (isset($cf_lang[$id_custom_field][$id_lang]))
						$custom_field['label'] = $cf_lang[$id_custom_field][$id_lang]['label'];
					else
						$custom_field['label'] = '';
				}
			}

			$custom_fields = $this->organizeDoubleBy('id_image', 'id_custom_field', $custom_fields, true);

			$this->context->smarty->assign(array(
				'module'=> $this->name,
				'colors' => $colors,
				'fonts' => $fonts,
				'image_groups' => $groups,
				'ctd_images' => $images,
				'materials' => $materials,
				'material_prices_rows' => $material_prices_rows,
				'custom_fields' => $custom_fields,
				'customtextdesign_config' => $cfg,
				'customtextdesign_category' => $this->category->id_category,
				'languages' => $this->languages,
				'default_lang' => $this->context->language->id,
			));

			$output = '';

			$table_custom_product = $this->name.'_custom_product';
			$table_custom_item = $this->name.'_custom_item';
			$sql = new DbQuery();
			$sql->from($table_custom_product);

			$where = array(
				'id_product' => (int)$id_product,
				'id_cart' => (int)$id_cart
			);

			$wh = '';
			$id_guest = (int)$this->context->cookie->id_guest;
			$id_customer = (int)$this->context->cookie->id_customer;

			if ($id_customer && $id_guest)
				$wh = " AND (id_customer = $id_customer OR id_guest = $id_guest)";
			elseif ($id_customer)
				$wh = " AND id_customer = $id_customer";
			elseif ($id_guest)
				$wh = " AND id_guest = $id_guest";

			$str_wh = $this->implodeWithKeys($where).$wh;
			$sql->where($str_wh);
			$custom_products = Db::getInstance()->executeS($sql, true, false);
			$custom_products = $this->applyTax($custom_products);

			if (is_array($custom_products))
			{
				foreach ($custom_products as &$custom_product)
				{
					$sql = new DbQuery();
					$sql->from($table_custom_item);
					$where = array(
						'id_custom_product' => (int)$custom_product['id_custom_product']
					);
					$sql->where($this->implodeWithKeys($where));
					$items = Db::getInstance()->executeS($sql, true, false);

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
					$custom_product['price'] = Tools::displayPrice(Tools::convertPrice((float)$custom_product['price']));
					$custom_product['attributes'] = $this->getProductAttributes($id_product, $id_product_attribute);
					$custom_product['has_custom'] = !($this->hasCustomization($custom_product));
				}
			}

			$this->context->smarty->assign(array(
				'id_product' => $id_product,
				'custom_products' => $custom_products,
				'module' => $this->name,
				'link' => $this->context->link,
			));

			$output .= $this->display(__FILE__, 'views/templates/hook/DisplayFooterProduct.tpl');

			$original_width = array();
			$product = new Product($id_product);
			$product_images = $product->getImages($this->context->language->id);

			$color_list = $this->assignAttributesGroups($product);

			if (is_array($product_images))
			{
				foreach ($product_images as $product_image)
				{
					$image_id = (int)$product_image['id_image'];
					$image = $this->getImagePath($image_id, Configuration::get($this->name.'image_type'));
					list($width) = getimagesize($image);
					$original_width[$image_id] = $width;
				}
			}

			$measures = $this->getProductMeasures($id_product);
			$measures = $this->organizeBy('id_image', $measures);

			$overlays = $this->getProductOverlays($id_product);
			$overlays = $this->organizeBy('id_image', $overlays);

			$masks = $this->getProductMasks($id_product);
			$masks = $this->organizeBy('id_image', $masks);

			$replaces = $this->getProductReplaces($id_product);
			$replaces = $this->organizeBy('id_image', $replaces);

			$this->context->smarty->assign(array(
				'ctd_product' => $ctd_product,
				'original_width' => $original_width,
				'measures' => $measures,
				'overlays' => $overlays,
				'masks' => $masks,
				'replaces' => $replaces,
				'color_list' => $color_list,
				'col_img_dir' => _PS_COL_IMG_DIR_,
				'customtextdesign_config' => $cfg,
				'customtextdesign_download' => $this->context->link->getModuleLink($this->name, 'Download',
					array('force' => '__image__', 'id_product' => '__id_product__'))
			));
			$output .= $this->display(__FILE__, 'views/templates/hook/DisplayCustomDesignPanel.tpl');

			return $output;
		}

		public function hookDisplayProductButtons()
		{
			$id_product = (int)Tools::getValue('id_product');
			$ctd_product = $this->getProductOptions($id_product);
			if (! $ctd_product || ! (int)$ctd_product['active'] || ! (int)$ctd_product['show_btn']) return;

			$this->smarty->assign(array(
				'is_ps15' => version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.5', '='),
				'is_ps16' => version_compare(Tools::substr(_PS_VERSION_, 0, 3), '1.6', '=')
			));

			return $this->display(__FILE__, 'views/templates/hook/DisplayProductButtons.tpl');
		}

		public function hookDisplayLeftColumnProduct()
		{
			return $this->hookDisplayProductButtons();
		}

		public function hookDisplayRightColumnProduct()
		{
			return $this->hookDisplayProductButtons();
		}

		public function hookDisplayAdminProductsExtra()
		{
			$id_product = (int)Tools::getValue('id_product');
			if (! $id_product) return false;
			if ((int)$id_product == (int)$this->getSpecialProduct())
				return $this->l('This product was added by the module "Product Customization" to represent the customizations total cost in the client cart.');

			$product = new Product($id_product, false, $this->context->language->id);
			$images_product = $product->getImages($this->context->language->id);

			$is_rewrite_active = (bool)Configuration::get('PS_REWRITING_SETTINGS');
			$product_link = $this->context->link->getProductLink(
				$product,
				$product->link_rewrite,
				Category::getLinkRewrite($product->id_category_default, $this->context->language->id),
				null,
				null,
				Context::getContext()->shop->id,
				0,
				$is_rewrite_active
			);

			$tproducts = $this->name.'_product';
			$sql = new DbQuery();
			$sql->from($tproducts);
			$sql->where('id_product = '.(int)$id_product);
			$ctd_product = Db::getInstance()->getRow($sql, false);

			if (! $ctd_product)
			{
				$sql = new DbQuery();
				$sql->from($tproducts);
				$sql->where('id_product = 0');
				$ctd_product_global = Db::getInstance()->getRow($sql, false);
				if ($ctd_product_global)
				{
					$ctd_product_global['id'] = 0;
					$ctd_product_global['id_product'] = (int)$id_product;
					Db::getInstance()->insert($tproducts, $ctd_product_global);
					$insert_id = Db::getInstance()->Insert_ID();
					$ctd_product = $ctd_product_global;
					$ctd_product['id'] = $insert_id;
				}
			}

			$ttrans = $this->name.'_product_trans';
			$sql = new DbQuery();
			$sql->from($ttrans);
			$sql->where('id_product = '.(int)$id_product);
			$product_translations = Db::getInstance()->executeS($sql);
			if (! $product_translations || ! count($product_translations))
			{
				$sql = new DbQuery();
				$sql->from($ttrans);
				$sql->where('id_product = 0');
				$product_translations_global = Db::getInstance()->executeS($sql);
				if (is_array($product_translations_global) && count($product_translations_global))
				{
					foreach ($product_translations_global as &$translation)
						$translation['id_product'] = (int)$id_product;

					$product_translations = $product_translations_global;
					foreach ($product_translations as $product_translation)
						Db::getInstance()->insert($ttrans, $product_translation);

				}
			}
			$product_lang = $this->organizeBy('id_lang', $product_translations);

			$measures = $this->getProductMeasures($id_product);
			$overlays = $this->getProductOverlays($id_product);
			$masks = $this->getProductMasks($id_product);
			$masks2 = $this->getProductMasks2($id_product);
			$replaces = $this->getProductReplaces($id_product);
			$custom_fields = $this->getProductFields($id_product);

			$cftrans = $this->name.'_custom_field_trans';
			$sql = new DbQuery();
			$sql->from($cftrans);
			$sql->where('id_product = '.(int)$id_product);
			$cf_translations = Db::getInstance()->executeS($sql);
			$cf_lang = $this->organizeDoubleBy('id_custom_field', 'id_lang', $cf_translations);

			$sql = new DbQuery();
			$sql->from($this->name.'_group');
			$image_groups = Db::getInstance()->executeS($sql);

			$colors = $this->getColor(0);
			$fonts = $this->getFont(0);
			$materials = $this->getMaterial(0);

			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

			$image_type = ImageType::getByNameNType($this->config_keys['image_type'], 'products');

			$attributes = $product->getAttributesResume($this->context->language->id);

			$link = new Link();
			$edit_url = $link->getAdminLink('AdminModules');

			$this->context->smarty->assign(array(
				'product' => $product,
				'images_product' => $images_product,
				'measures' => $measures,
				'overlays' => $overlays,
				'masks' => $masks,
				'masks2' => $masks2,
				'replaces' => $replaces,
				'custom_fields' => $custom_fields,
				'ctd_product' => $ctd_product,
				'colors' => $colors,
				'fonts' => $fonts,
				'materials' => $materials,
				'attributes' => $attributes,
				'image_groups' => $image_groups,
				'product_lang' => $product_lang,
				'cf_lang' => $cf_lang,
				'currency' => $currency,
				'prod_image_info' => $image_type,
				'product_link' => $product_link,
				'edit_url' => $edit_url
			));
			return $this->display(__FILE__, 'views/templates/hook/DisplayAdminProductsExtra.tpl');
		}

		public function hookDisplayTop()
		{
			$output = '';

			$id_cart = isset($this->context->cart->id) ? $this->context->cart->id : 0;

			$controller_name = Tools::getValue('controller');

			if ($controller_name == 'order' || $controller_name == 'order-opc' || $controller_name == 'orderopc' || $controller_name == 'checkout_klarna')
			{
				if ($id_cart)
				{

					$custom_products = $this->getCustomProducts(null, null, (int)$id_cart);
					$this->dbg($custom_products);
					$colors = $this->getColor(0);
					$fonts = $this->getFont(0);
					$materials = $this->getMaterial(0);

					if (is_array($custom_products))
					{
						foreach ($custom_products as $custom_product)
						{

							$id_product = $custom_product['id_product'];
							$id_product_attribute = $custom_product['id_attribute'];
							$id_image = $custom_product['id_image'];
							$table_custom_item = $this->name.'_custom_item';
							$sql = new DbQuery();
							$sql->from($table_custom_item);
							$where = array(
								'id_custom_product' => (int)$custom_product['id_custom_product']
							);
							$sql->where($this->implodeWithKeys($where));
							$items = Db::getInstance()->executeS($sql, true, false);

							$id_image = $custom_product['id_image'];
							$panel_width = $custom_product['width'];
							CustomDesign::$panel_width = $panel_width;
							CustomDesign::$custom_width = $custom_product['product_width'];
							CustomDesign::$custom_height = $custom_product['product_height'];
							CustomDesign::$custom_color = $custom_product['product_color'];
							CustomDesign::init($id_product, $id_product_attribute, $id_image);
							$custom_product['preview'] = CustomDesign::renderPreview($custom_product, $items);

							$measure = $this->getProductMeasures($id_product, $id_image);
							$images_type = $this->getImagePath($id_image, Configuration::get($this->name.'image_type'), $id_product);
							$image_size = getimagesize($images_type);
							$original_width = $image_size[0];

							$width_ratio = $original_width / $custom_product['width'];
							//from px to cm
							$options = $this->getProductOptions($id_product);
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
										$data['image_src'] = $this->getBaseDir().$item['text'];
										$item['text'] = $this->getCacheDir().basename(CustomImage::colorizeImage($data, 0));
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
								'module' => $this->name,
								'ctd_module_dir' => $module_dir,
								'custom_product' => $custom_product,
								'oos_items' => $this->checkOOS($custom_product),
								'items' => $items,
								'colors' => $colors,
								'fonts' => $fonts,
								'materials' => $materials,
								'mlink' => new Link(),
								'params' => array('id_custom_product' => $custom_product['uniqid'])
							));

							$output .= $this->display(__FILE__, 'views/templates/hook/DisplayTop.tpl');
						}
					}
				}
			}

			return $output;
		}

		public function hookDisplayOrderDetail($params)
		{
			$output = '';
			$id_order = (int)$params['order']->id;
			$id_cart = (int)$params['order']->id_cart;

			$order = new Order($id_order);
			$products = $order->getProducts();
			if ($id_cart)
			{
				foreach ($products as $id_order_detail => $product)
				{
					$this->smarty->assign(array(
						'id_order_detail' => $id_order_detail
					));
					$id_product = $product['product_id'];
					$id_product_attribute = $product['product_attribute_id'];

					$custom_products = $this->getCustomProducts($id_product, $id_product_attribute, $id_cart);
					if (is_array($custom_products))
					{
						foreach ($custom_products as $custom_product)
						{

							$id_custom_product = $custom_product['id_custom_product'];
							$output .= $this->getCustomProductSummary($id_custom_product, -1, 'history');
						}
					}
				}

				$output .= $this->display(__FILE__, 'views/templates/hook/DisplayOrderDetailScript.tpl');
			}

			return $output;
		}

		public function hookActionCartSave()
		{
			if (! Validate::isLoadedObject($this->context->cart)) return;

			$id_special = $this->getSpecialProduct();
			$product_special = new Product($id_special, false, $this->context->language->id, $this->context->shop->id, $this->context);
			$quantity = $this->context->cart->containsProduct($id_special);
			$quantity = isset($quantity['quantity']) ? (int)$quantity['quantity'] : 0;
			if ($product_special->active && $this->cartHasCustomization())
			{
				if (!$quantity)
					$this->context->cart->updateQty(1, $id_special);
				elseif ($quantity > 1)
					$this->context->cart->updateQty($quantity - 1, $id_special, null, false, 'down');
			}
			else
			{
				if ($quantity)
					$this->context->cart->deleteProduct($id_special);
			}

			$id_customer = isset($this->context->cookie->id_customer) ? (int)$this->context->cookie->id_customer : 0;
			$id_guest = isset($this->context->cookie->id_guest) ? (int)$this->context->cookie->id_guest : 0;
			$id_cart = (int)$this->context->cart->id;

			$where = '';
			$id_guest = (int)$this->context->cookie->id_guest;
			$id_customer = (int)$this->context->cookie->id_customer;

			if ($id_customer && $id_guest)
				$where = "AND (id_customer = $id_customer OR id_guest = $id_guest)";
			elseif ($id_customer)
				$where = "AND id_customer = $id_customer";
			elseif ($id_guest)
				$where = "AND id_guest = $id_guest";

			if (! empty($where))
			{
				$sql = 'UPDATE `'._DB_PREFIX_."customtextdesign_custom_product` SET `id_cart` = $id_cart WHERE `id_cart` = 0 $where";
				Db::getInstance()->execute($sql);
			}

		}

		public function hookActionValidateOrder($params)
		{
			$id_cart = (int)$params['cart']->id;
			$custom_products = $this->getCustomProducts(null, null, $id_cart);
			if (is_array($custom_products))
			{
				foreach ($custom_products as $custom_product)
				{
					$items = $this->getItems($custom_product['id_custom_product']);
					if (is_array($items))
					{
						foreach ($items as $item)
						{
							if ($item['type'] == 'image')
							{
								$id_image = $item['id_image'];
								$image = $this->getImage($id_image);
								$quantity = (int)$image['quantity'];
								if ($quantity == -1) continue;
								elseif ($quantity > 0)
								{
									$query = 'UPDATE `'._DB_PREFIX_.$this->name.'_image` SET quantity = quantity - 1 WHERE id = '.(int)$id_image;
									Db::getInstance()->execute($query);
								}
							}
						}
					}
				}
			}
		}

		public function assignAttributesGroups($product)
		{
			$colors = array();
			$groups = array();

			$combinations = array();
			$attributes_groups = $product->getAttributesGroups($this->context->language->id);
			if (is_array($attributes_groups) && $attributes_groups)
			{
				$combination_images = $product->getCombinationImages($this->context->language->id);
				$combination_prices_set = array();
				foreach ($attributes_groups as $row)
				{
					// Color management
					if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color'])
					|| (file_exists(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')))
					{
						$colors[$row['id_attribute']]['value'] = $row['attribute_color'];
						$colors[$row['id_attribute']]['name'] = $row['attribute_name'];
						if (!isset($colors[$row['id_attribute']]['attributes_quantity']))
							$colors[$row['id_attribute']]['attributes_quantity'] = 0;
						$colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
					}
					if (!isset($groups[$row['id_attribute_group']]))
						$groups[$row['id_attribute_group']] = array(
							'group_name' => $row['group_name'],
							'name' => $row['public_group_name'],
							'group_type' => $row['group_type'],
							'default' => -1,
						);

					$groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
					if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1)
						$groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
					if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
						$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
					$groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];

					$combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
					$combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
					$combinations[$row['id_product_attribute']]['price'] = (float)$row['price'];

					// Call getPriceStatic in order to set $combination_specific_price
					if (!isset($combination_prices_set[(int)$row['id_product_attribute']]))
					{
						$combination_specific_price = null;
						Product::getPriceStatic((int)$product->id, false, $row['id_product_attribute'], 6, null, false, true,
						1, false, null, null, null, $combination_specific_price);
						$combination_prices_set[(int)$row['id_product_attribute']] = true;
						$combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
					}
					$combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
					$combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
					$combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
					$combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
					$combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
					$combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
					if ($row['available_date'] != '0000-00-00')
					{
						$combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
						$combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
					}
					else
						$combinations[$row['id_product_attribute']]['available_date'] = '';

					if (!isset($combination_images[$row['id_product_attribute']][0]['id_image']))
						$combinations[$row['id_product_attribute']]['id_image'] = -1;
					else
					{
						$combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$combination_images[$row['id_product_attribute']][0]['id_image'];
						if ($row['default_on'])
						{
							if (isset($this->context->smarty->tpl_vars['cover']->value))
								$current_cover = $this->context->smarty->tpl_vars['cover']->value;

							if (is_array($combination_images[$row['id_product_attribute']]))
							{
								foreach ($combination_images[$row['id_product_attribute']] as $tmp)
									if (isset($current_cover) && $tmp['id_image'] == $current_cover['id_image'])
									{
										$combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$tmp['id_image'];
										break;
									}
							}

							if ($id_image > 0)
							{
								if (isset($this->context->smarty->tpl_vars['images']->value))
									$product_images = $this->context->smarty->tpl_vars['images']->value;
								if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image]))
								{
									$product_images[$id_image]['cover'] = 1;
									$this->context->smarty->assign('mainImage', $product_images[$id_image]);
									if (count($product_images))
										$this->context->smarty->assign('images', $product_images);
								}
								if (isset($this->context->smarty->tpl_vars['cover']->value))
									$cover = $this->context->smarty->tpl_vars['cover']->value;
								if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images))
								{
									$product_images[$cover['id_image']]['cover'] = 0;
									if (isset($product_images[$id_image]))
										$cover = $product_images[$id_image];
									$cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($product->id.'-'.$id_image) : (int)$id_image);
									$cover['id_image_only'] = (int)$id_image;
									$this->context->smarty->assign('cover', $cover);
								}
							}
						}
					}
				}

				// wash attributes list (if some attributes are unavailables and if allowed to wash it)
				if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0)
				{
					foreach ($groups as &$group)
						foreach ($group['attributes_quantity'] as $key => &$quantity)
							if ($quantity <= 0)
								unset($group['attributes'][$key]);

					foreach ($colors as $key => $color)
						if ($color['attributes_quantity'] <= 0)
							unset($colors[$key]);
				}
				foreach ($combinations as $id_product_attribute => $comb)
				{
					$attribute_list = '';
					foreach ($comb['attributes'] as $id_attribute)
						$attribute_list .= '\''.(int)$id_attribute.'\',';
					$attribute_list = rtrim($attribute_list, ',');
					$combinations[$id_product_attribute]['list'] = $attribute_list;
				}

				/*$this->context->smarty->assign(array(
					'groups' => $groups,
					'colors' => (count($colors)) ? $colors : false,
					'combinations' => $combinations,
					'combinationImages' => $combination_images
				));*/
			}
			return $colors;
		}

		public function checkOOS($custom_product)
		{
			$oos_items = array();
			$items = $this->getItems($custom_product['id_custom_product']);
			if (is_array($items))
			{
				foreach ($items as $item)
				{
					if ($item['type'] == 'image')
					{
						$id_image = $item['id_image'];
						if ((int)$id_image)
						{
							$image = $this->getImage($id_image);
							$quantity = (int)$image['quantity'];
							if (!(int)$quantity)
								$oos_items[] = $image;
						}
					}
				}
			}
			return $oos_items;
		}

		public function getConfigKeys($id_page_config = '')
		{
			if (!(int)$id_page_config) $id_page_config = '';
			$module_name = $this->name;
			if ((int)$id_page_config)
				$module_name = 'ctd_';
			$c_keys = array();
			foreach ($this->config_keys as $key => &$value)
			{
				if (Tools::substr($key, 0, 1) == '_')
				{
					foreach ($this->languages as $lang)
					{
						$id_language = $lang['id_lang'];
						$akey = Tools::substr($key, 1).'_'.$id_language;
						$value = Configuration::get($module_name.$akey.$id_page_config);
						$c_keys[$akey] = $value;
					}
					continue;
				}
				$c_keys[$key] = Configuration::get($module_name.$key.$id_page_config);
			}

			return $c_keys;
		}

		public function getColor($id = 0)
		{
			if (_CTD_CACHE_ && isset($this->colors[$id])) return $this->colors[$id];
			$sql = new DbQuery();
			$sql->from($this->name.'_color');
			if ($id)
			{
				$sql->where('id = '.(int)$id);
				return $this->colors[$id] = Db::getInstance()->getRow($sql, false);
			}
			else
			{
				$colors = Db::getInstance()->executeS($sql, true, false);
				return $this->colors[$id] = $this->organizeBy('id', $colors);
			}
		}

		public function getFont($id = 0)
		{
			if (_CTD_CACHE_ && isset($this->fonts[$id])) return $this->fonts[$id];
			$sql = new DbQuery();
			$sql->from($this->name.'_font');
			if ($id)
			{
				$sql->where('id = '.(int)$id);
				return $this->fonts[$id] = Db::getInstance()->getRow($sql, false);
			}
			else
				return $this->fonts[$id] = Db::getInstance()->executeS($sql, true, false);

		}

		public function getMaterial($id = 0)
		{
			if (_CTD_CACHE_ && isset($this->materials[$id])) return $this->materials[$id];
			$sql = new DbQuery();
			$sql->from($this->name.'_material');
			if ($id)
			{
				$sql->where('id = '.(int)$id);
				return $this->materials[$id] = Db::getInstance()->getRow($sql, false);
			}
			else
				return $this->materials[$id] = Db::getInstance()->executeS($sql, true, false);
		}

		public function getImage($id = 0, $id_group = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_image');
			if ($id)
			{
				$sql->where('id = '.(int)$id);
				return Db::getInstance()->getRow($sql, false);
			}
			elseif ($id_group)
			{
				$sql->where('id_group = '.(int)$id_group);
				return Db::getInstance()->executeS($sql, true, false);
			}
			else
				return Db::getInstance()->executeS($sql, true, false);
		}

		public function getCart()
		{
			if (Validate::isLoadedObject($this->context->cart))
				return $this->context->cart->id;
			else
			{
				if (isset($this->context->cookie->id_cart) && (int)$this->context->cookie->id_cart)
				{
					$this->context->cart = new Cart($this->context->cookie->id_cart);
					return $this->context->cookie->id_cart;
				}
				else
					return false;
			}
		}

		public function getTax($id_product, $force_tax = false)
		{
			//$options = $this->getProductOptions($id_product);
			//if (!$options['use_tax']) return 0;

			if (Validate::isLoadedObject($this->context->cart))
			{
				if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
					$address_id = (int)$this->context->cart->id_address_invoice;
				else
					$address_id = (int)$this->context->cart->id_address_delivery; // Get delivery address of the product from the cart
				if (!Address::addressExists($address_id))
					$address_id = null;
			}
			else
			{
				if ($id_customer = $this->context->cookie->id_customer)
					$address_id = Address::getFirstCustomerAddressId($id_customer, true);
				else
					$address_id = 0;
			}

			if (Tax::excludeTaxeOption()) return 0;
			$tax_calculation_method = Group::getPriceDisplayMethod(Group::getCurrent()->id);
			if ($tax_calculation_method == PS_TAX_EXC && !$force_tax) return 0;

			if (Validate::isLoadedObject($this->context->cart))
				$address = new Address($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
			else
				$address = new Address(0);
			$product_tax_rate = (float)Tax::getProductTaxRate((int)$id_product, (int)$address->id, $this->context);
			return $product_tax_rate;
		}

		public function applyTax($custom_products, $request_level = 0)
		{
			$level = 0;
			foreach ($custom_products as &$custom_product)
			{
				$tax_rate = $this->getTax($custom_product['id_product']);
				$custom_product['price_ht'] = $custom_product['price'];
				$custom_product['custom_price_ht'] = $custom_product['custom_price'];
				$custom_product['price'] *= (1 + $tax_rate / 100);
				$custom_product['custom_price'] *= (1 + $tax_rate / 100);
				$custom_product['level'] = 0;
				if (version_compare($custom_product['version'], '4.0.6', '>=')) $custom_product['level'] = 1;
			}

			if ($custom_products && $request_level)
				$custom_products['level'] = $level / count($custom_products);

			return $custom_products;
		}

		public function getCustomProducts($id_product = null, $id_product_attribute = null, $id_cart, $request_level = 0)
		{
			$cache_key = "{$id_product}_{$id_product_attribute}_{$id_cart}_{$request_level}";
			if (_CTD_CACHE_ && isset($this->custom_products[$cache_key])) return $this->custom_products[$cache_key];

			if (!$id_cart && Validate::isLoadedObject($this->context->cart))
				$id_cart = $this->context->cart->id;

			if (! $id_cart) return false;

			$table_custom_product = $this->name.'_custom_product';
			$sql = new DbQuery();
			$sql->from($table_custom_product);
			$where = array(
				'id_cart' => (int)$id_cart
			);
			if ((int)$id_product) $where['id_product'] = (int)$id_product;
			if ((int)$id_product_attribute) $where['id_attribute'] = (int)$id_product_attribute;
			$sql->where($this->implodeWithKeys($where));
			$custom_products = Db::getInstance()->executeS($sql, true, false);
			if (! count($custom_products)) return false;
			$custom_products = $this->organizeBy('id_custom_product', $custom_products);

			$level = 0;
			if (is_array($custom_products))
			{
				foreach ($custom_products as &$custom_product)
				{
					$tax_rate = $this->getTax($custom_product['id_product']);
					$custom_product['price_ht'] = $custom_product['price'];
					$custom_product['custom_price_ht'] = $custom_product['custom_price'];
					$custom_product['price'] *= (1 + $tax_rate / 100);
					$custom_product['custom_price'] *= (1 + $tax_rate / 100);
					$custom_product['level'] = 0;
					if (version_compare($custom_product['version'], '4.0.6', '>=')) $custom_product['level'] = 1;
					$level += $custom_product['level'];
				}
			}

			if ($custom_products && $request_level)
				$custom_products['level'] = $level / count($custom_products);

			return $this->custom_products[$cache_key] = $custom_products;
		}

		public function getCustomProduct($id_custom_product, $uniqid = null, $force_tax = false)
		{
			$cache_key = "{$id_custom_product}_{$uniqid}";
			if (_CTD_CACHE_ && isset($this->custom_product[$cache_key])) return $this->custom_product[$cache_key];
			$table_custom_product = $this->name.'_custom_product';

			$sql = new DbQuery();
			$sql->from($table_custom_product);
			if ((int)$id_custom_product)
			{
				$where = array(
					'id_custom_product' => (int)$id_custom_product
				);
				$sql->where($this->implodeWithKeys($where));
			}
			else
				$sql->where("uniqid = '$uniqid'");
			$custom_product = Db::getInstance()->getRow($sql, false);
			if (! $custom_product) return false;

			$tax_rate = $this->getTax($custom_product['id_product'], $force_tax);
			$custom_product['price_ht'] = $custom_product['price'];
			$custom_product['custom_price_ht'] = $custom_product['custom_price'];
			$custom_product['price'] *= (1 + $tax_rate / 100);
			$custom_product['custom_price'] *= (1 + $tax_rate / 100);
			$custom_product['level'] = 0;
			if (version_compare($custom_product['version'], '4.0.6', '>=')) $custom_product['level'] = 1;

			return $this->custom_product[$cache_key] = $custom_product;
		}

		public function getItems($id_custom_product)
		{
			if (_CTD_CACHE_ && isset($this->items[$id_custom_product])) return $this->items[$id_custom_product];

			$custom_product = $this->getCustomProduct($id_custom_product);
			$tax_rate = $this->getTax($custom_product['id_product']);

			$table_custom_item = $this->name.'_custom_item';
			$sql = new DbQuery();
			$sql->from($table_custom_item);
			$where = array(
				'id_custom_product' => (int)$id_custom_product
			);
			$sql->where($this->implodeWithKeys($where));
			$items = Db::getInstance()->executeS($sql);

			foreach ($items as &$item)
			{
				$item['price_ht'] = $item['price'];
				$item['price'] *= (1 + $tax_rate / 100);
			}

			return $this->items[$id_custom_product] = $items;
		}

		public function getProductOptions($id_product)
		{
			if (_CTD_CACHE_ && isset($this->options[$id_product])) return $this->options[$id_product];
			$sql = new DbQuery();
			$sql->from($this->name.'_product');
			$sql->where('id_product = '.(int)$id_product);
			return $this->options[$id_product] = Db::getInstance()->getRow($sql, false);
		}

		public function getCustomProductSummary($id_custom_product, $custom_total = -1, $type = '')
		{
			$colors = $this->getColor(0);
			$colors = $this->organizeBy('id', $colors);
			$fonts = $this->getFont(0);
			$fonts = $this->organizeBy('id', $fonts);
			$materials = $this->getMaterial(0);
			$materials = $this->organizeBy('id', $materials);
			$custom_product = $this->getCustomProduct($id_custom_product);
			if (! $custom_product) return null;

			$items = $this->getItems($custom_product['id_custom_product']);
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

			$measure = $this->getProductMeasures($id_product, $id_image);
			$images_type = $this->getImagePath($id_image, Configuration::get($this->name.'image_type'), $id_product);
			$image_size = getimagesize($images_type);
			$original_width = $image_size[0];

			$width_ratio = $original_width / $custom_product['width'];
			//from px to cm
			$options = $this->getProductOptions($id_product);
			if ($options['customsize'] && ($custom_product['product_width'] * $custom_product['product_height']))
				$cmratio = $custom_product['product_width'] / $custom_product['width'];
			elseif ($measure)
				$cmratio = $measure['size'] / $measure['width'];

			foreach ($items as &$item)
			{
				if ($item['type'] == 'text')
				{
					$item['ignore_space'] = 0;
					$item['size'] = 100;
					$item['forpanel'] = 1;
					$item['preview'] = CustomImage::preview($item, 0);
				}
				else
				{
					if ($item['clr'] || (int)$item['color'])
					{
						$data = array();
						$data['imagecolor'] = !(int)$item['color'] ? $item['clr'] : (int)$item['color'];
						$data['image_src'] = $this->getBaseDir().$item['text'];
						$item['text'] = $this->getCacheDir().basename(CustomImage::colorizeImage($data, 0));
					}
				}
				$item['x'] = ($measure['x_origin'] - $item['x'] * $width_ratio) * $cmratio;
				$item['y'] = ($measure['y_origin'] - $item['y'] * $width_ratio) * $cmratio;
				$item['width'] = $item['width'] * $item['scalex'] * $width_ratio * $cmratio;
				$item['height'] = $item['height'] * $item['scaley'] * $width_ratio * $cmratio;
			}

			$module_dir = $this->getPath();

			$mask = $this->getProductMasks($custom_product['id_product'], $custom_product['id_image']);
			$custom_product['has_mask'] = is_array($mask);

			$mask2 = $this->getProductMasks2($custom_product['id_product'], $custom_product['id_image']);
			$custom_product['has_mask2'] = is_array($mask2);

			if (!empty($custom_product['product_color']))
			{
				if (Tools::substr($custom_product['product_color'], 0, 1) == '_')
				{
					$custom_product['has_listed_color'] = true;
					$custom_product['product_color'] = str_replace('_', '', $custom_product['product_color']);
				}
			}

			$this->smarty->assign(array(
				'ctd_module_dir' => $module_dir,
				'custom_product' => $custom_product,
				'oos_items' => $this->checkOOS($custom_product),
				'items' => $items,
				'colors' => $colors,
				'fonts' => $fonts,
				'materials' => $materials,
				'is_employee' => isset($this->context->employee->id) && (int)$this->context->employee->id,
				'link' => new Link(),
				'module' => $this->name,
				'custom_total' => $custom_total,
				'params' => array('id_custom_product' => $custom_product['uniqid'])
			));

			if ($type == 'history')
				return $this->display(__FILE__, 'views/templates/hook/DisplayOrderDetail.tpl');

			if ($type == 'top')
				return $this->display(__FILE__, 'views/templates/hook/DisplayTop.tpl');
			return $this->display(__FILE__, 'views/templates/admin/getProductsDetail.tpl');
		}

		public function getShortSummary($id_custom_product, $quantity = 1, $type = 'no_image')
		{
			$custom_product = $this->getCustomProduct($id_custom_product);
			if (! $custom_product) return null;

			$custom_total = (float)$custom_product['price'] + (float)$custom_product['custom_price'];

			$output = '';
			$ctd_total = $custom_total * $quantity;
			$output .= "$quantity x ".Tools::displayPrice(Tools::convertPrice($custom_total)).' ('.Tools::displayPrice(Tools::convertPrice($ctd_total)).')';

			if (! $type == 'no_image')
			{
				$preview_src = $this->context->shop->getBaseURL().'modules/customtextdesign/data/cache/'.$custom_product['preview'];
				$output .= '
				<div>
				<br>
				<img width="100" src="'.$preview_src.'" />
				<hr>
				<br>
				</div>
				';
			}
			return $output;
		}

		public function getProductMeasures($id_product, $id_image = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_measure');
			$where = 'id_product = '.(int)$id_product;
			if ($id_image)
				$where .= ' AND id_image = '.(int)$id_image;
			$sql->where($where);
			if ($id_image)
				return Db::getInstance()->getRow($sql, false);
			else
				return Db::getInstance()->executeS($sql, true, false);
		}

		public function getProductOverlays($id_product, $id_image = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_overlay');
			$where = 'id_product = '.(int)$id_product;
			if ($id_image)
				$where .= ' AND id_image = '.(int)$id_image;
			$sql->where($where);
			if ($id_image)
				return Db::getInstance()->getRow($sql, false);
			else
				return Db::getInstance()->executeS($sql, true, false);
		}

		public function getProductMasks($id_product, $id_image = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_mask');
			$where = 'id_product = '.(int)$id_product;
			if ($id_image)
				$where .= ' AND id_image = '.(int)$id_image;
			$sql->where($where);
			if ($id_image)
				return Db::getInstance()->getRow($sql, false);
			else
				return Db::getInstance()->executeS($sql, true, false);
		}

		public function getProductMasks2($id_product, $id_image = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_mask2');
			$where = 'id_product = '.(int)$id_product;
			if ($id_image)
				$where .= ' AND id_image = '.(int)$id_image;
			$sql->where($where);
			if ($id_image)
				return Db::getInstance()->getRow($sql, false);
			else
				return Db::getInstance()->executeS($sql, true, false);
		}

		public function getProductReplaces($id_product, $id_image = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_replace');
			$where = 'id_product = '.(int)$id_product;
			if ($id_image)
				$where .= ' AND id_image = '.(int)$id_image;
			$sql->where($where);
			if ($id_image)
				return Db::getInstance()->getRow($sql, false);
			else
				return Db::getInstance()->executeS($sql, true, false);
		}

		public function getProductFields($id_product, $id_image = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_custom_field');
			$where = 'id_product = '.(int)$id_product;
			if ($id_image)
				$where .= ' AND id_image = '.(int)$id_image;
			$sql->where($where);
			return Db::getInstance()->executeS($sql);
		}

		public function copyProductParams($id_product_old, $id_product_new)
		{
			$options = $this->getProductOptions($id_product_old);
			if (is_array($options))
			{
				unset($options['id']);
				$options['id_product'] = $id_product_new;
				Db::getInstance()->insert($this->name.'_product', $options);
			}
		}

		public function copyImageParams($id_product_old, $id_product_new, $image_old, $image_new)
		{
			$measure = $this->getProductMeasures($id_product_old, $image_old);
			$overlay = $this->getProductOverlays($id_product_old, $image_old);
			$mask = $this->getProductMasks($id_product_old, $image_old);
			$mask2 = $this->getProductMasks2($id_product_old, $image_old);
			$replace = $this->getProductReplaces($id_product_old, $image_old);
			$custom_fields = $this->getProductFields($id_product_old, $image_old);

			if (is_array($measure))
			{
				unset($measure['id_measure']);
				$measure['id_product'] = $id_product_new;
				$measure['id_image'] = $image_new;
				Db::getInstance()->insert($this->name.'_measure', $measure);
			}

			if (is_array($custom_fields))
			{
				foreach ($custom_fields as $custom_field)
				{
					unset($custom_field['id_custom_field']);
					$custom_field['id_product'] = $id_product_new;
					$custom_field['id_image'] = $image_new;
					Db::getInstance()->insert($this->name.'_custom_field', $custom_field);
				}
			}

			if (is_array($mask))
			{
				unset($mask['id_mask']);
				$mask['id_product'] = $id_product_new;
				$mask['id_image'] = $image_new;
				$extension = pathinfo($mask['image'], PATHINFO_EXTENSION);
				$filename = time().'_'.rand().'.'.Tools::strtolower($extension);
				$dir = realpath($this->getDir().'data/mask/');
				copy($dir.'/'.$mask['image'], $dir.'/'.$filename);
				$mask['image'] = $filename;
				Db::getInstance()->insert($this->name.'_mask', $mask);
			}

			if (is_array($overlay))
			{
				unset($overlay['id_overlay']);
				$overlay['id_product'] = $id_product_new;
				$overlay['id_image'] = $image_new;
				$extension = pathinfo($overlay['image'], PATHINFO_EXTENSION);
				$filename = time().'_'.rand().'.'.Tools::strtolower($extension);
				$dir = realpath($this->getDir().'data/overlay/');
				copy($dir.'/'.$overlay['image'], $dir.'/'.$filename);
				$overlay['image'] = $filename;
				Db::getInstance()->insert($this->name.'_overlay', $overlay);
			}

			if (is_array($mask2))
			{
				unset($mask2['id_mask']);
				$mask2['id_product'] = $id_product_new;
				$mask2['id_image'] = $image_new;
				$extension = pathinfo($mask2['image'], PATHINFO_EXTENSION);
				$filename = time().'_'.rand().'.'.Tools::strtolower($extension);
				$dir = realpath($this->getDir().'data/mask2/');
				copy($dir.'/'.$mask2['image'], $dir.'/'.$filename);
				$mask2['image'] = $filename;
				Db::getInstance()->insert($this->name.'_mask2', $mask2);
			}

			if (is_array($replace))
			{
				unset($replace['id_replace']);
				$replace['id_product'] = $id_product_new;
				$replace['id_image'] = $image_new;
				$extension = pathinfo($replace['image'], PATHINFO_EXTENSION);
				$filename = time().'_'.rand().'.'.Tools::strtolower($extension);
				$dir = realpath($this->getDir().'data/replace/');
				copy($dir.'/'.$replace['image'], $dir.'/'.$filename);
				$replace['image'] = $filename;
				Db::getInstance()->insert($this->name.'_replace', $replace);
			}
		}

		public function getModulePages($id_page_config = 0)
		{
			if (_CTD_CACHE_ && isset($this->module_pages[$id_page_config])) return $this->module_pages[$id_page_config];
			if ($id_page_config && isset($this->module_pages[0]))
			{
				$pages = $this->organizeBy('id_page_config', $this->module_pages[0]);
				return $this->module_pages[$id_page_config] = $pages[$id_page_config];
			}
			$pages = array();
			$table = $this->name.'_page_config';
			$files = glob(_PS_MODULE_DIR_.$this->name.'/controllers/front/*.php');
			foreach ($files as $file)
			{
				$contents = Tools::file_get_contents($file);
				if (strpos($contents, 'CustomTextDesignModulePage') !== false)
				{
					$pagename = Tools::str2url(basename($file, '.php'));
					$sql = new DbQuery();
					$sql->from($table);
					$sql->where("pagename = '$pagename'");
					$row = Db::getInstance()->getRow($sql, false);
					if (!$row)
					{
						$row = array(
							'pagename' => $pagename,
							'colors' => '',
							'fonts' => '',
							'materials' => '',
						);
						Db::getInstance()->insert($table, $row, false, false);
						$insert_id = Db::getInstance()->Insert_ID();
						$row['id_page_config'] = $insert_id;
						foreach ($this->config_keys as $key => $value)
						{
							if (Tools::substr($key, 0, 1) == '_')
							{
								foreach ($this->languages as $lang)
								{
									$id_language = $lang['id_lang'];
									$akey = Tools::substr($key, 1).'_'.$id_language;
									Configuration::updateValue('ctd_'.$akey.$insert_id, $value);
								}
								continue;
							}
							Configuration::updateValue('ctd_'.$key.$insert_id, $value);
						}

					}
					$row = Db::getInstance()->getRow($sql, false);
					$pages[$pagename] = $row;

				}
			}
			if ($id_page_config)
			{
				$pages = $this->organizeBy('id_page_config', $pages);
				return $this->module_pages[$id_page_config] = $pages[$id_page_config];
			}

			return $this->module_pages[$id_page_config] = $pages;
		}

		public function getFontList()
		{
			$items = array();
			$table = $this->name.'_font';
			$files = glob(_PS_MODULE_DIR_.$this->name.'/data/font/*.{ttf,TTF}', GLOB_BRACE);
			foreach ($files as $file)
			{
				$file = pSQL($file);
				$sql = new DbQuery();
				$sql->from($table);
				$filename = basename($file);
				$sql->where("file = '$filename'");
				$row = Db::getInstance()->getRow($sql, false);
				if (! $row)
				{
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					$filename = basename($file, '.'.$ext);
					$count = 0;
					$new_filename = str_replace(array('[', ']'), '-', $filename, $count);
					if ($count)
					{
						$new_path = _PS_MODULE_DIR_.$this->name.'/data/font/'.$new_filename.'.'.$ext;
						$rename = rename($file, $new_path);
						if ($rename)
							$file = $new_path;
					}
					$key = ucwords(Tools::strtolower(basename($file, '.'.$ext)));
					$items[$key] = basename($file);
				}
			}
			return $items;
		}

		public function getTextureList()
		{
			$items = array();
			$table = $this->name.'_color';
			$files = glob(_PS_MODULE_DIR_.$this->name.'/data/texture/*.{png,PNG,jpg,JPG,gif,GIF,jpeg,JPEG}', GLOB_BRACE);
			foreach ($files as $file)
			{
				$file = pSQL($file);
				$sql = new DbQuery();
				$sql->from($table);
				$filename = basename($file);
				$sql->where("texture = '$filename'");
				$row = Db::getInstance()->getRow($sql, false);
				if (! $row)
				{
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					$filename = basename($file, '.'.$ext);
					$count = 0;
					$new_filename = str_replace(array('[', ']'), '-', $filename, $count);
					if ($count)
					{
						$new_path = _PS_MODULE_DIR_.$this->name.'/data/texture/'.$new_filename.'.'.$ext;
						$rename = rename($file, $new_path);
						if ($rename)
							$file = $new_path;
					}
					$key = ucwords(Tools::strtolower(basename($file, '.'.$ext)));
					$items[$key] = basename($file);
				}
			}
			return $items;
		}

		public function getImageList()
		{
			$items = array();
			$table = $this->name.'_image';
			$files = glob(_PS_MODULE_DIR_.$this->name.'/data/image/*.{png,PNG,jpg,JPG,gif,GIF,jpeg,JPEG}', GLOB_BRACE);
			foreach ($files as $file)
			{
				$file = pSQL($file);
				$sql = new DbQuery();
				$sql->from($table);
				$filename = basename($file);
				$sql->where("file = '$filename'");
				$row = Db::getInstance()->getRow($sql, false);
				if (! $row)
				{
					$ext = pathinfo($file, PATHINFO_EXTENSION);
					$filename = basename($file, '.'.$ext);
					$count = 0;
					$new_filename = str_replace(array('[', ']'), '-', $filename, $count);
					if ($count)
					{
						$new_path = _PS_MODULE_DIR_.$this->name.'/data/image/'.$new_filename.'.'.$ext;
						$rename = rename($file, $new_path);
						if ($rename)
							$file = $new_path;
					}
					$key = ucwords(Tools::strtolower(basename($file, '.'.$ext)));
					$items[$key] = basename($file);
				}
			}
			return $items;
		}

		public function getModulePage($pagename)
		{
			$table = $this->name.'_page_config';
			$sql = new DbQuery();
			$sql->from($table);
			$sql->where("pagename = '$pagename'");
			return Db::getInstance()->getRow($sql, false);
		}

		public function saveModulePage($pagename, $data)
		{
			$table = $this->name.'_page_config';
			$sql = new DbQuery();
			$sql->from($table);
			$sql->where("pagename = '$pagename'");
			$data['pagename'] = $pagename;
			if (Db::getInstance()->getRow($sql, false))
				Db::getInstance()->update($table, $data, "pagename = '$pagename'");
			else
				Db::getInstance()->insert($table, $data);
			return true;
		}

		public function deleteOldProducts()
		{
			$del_number = Configuration::get($this->name.'del_number');
			$del_span = Configuration::get($this->name.'del_span');
			if (!$del_number) return false;

			$products = $this->category->getProductsWs();

			$del_span_option = array('day', 'month', 'year');
			$timestr = $del_number.' '.$del_span_option[$del_span];

			foreach ($products as $product)
			{
				$prod = new Product($product['id']);
				$date_add = $prod->date_add;
				$diff = strtotime($timestr, strtotime($date_add));
				if (($diff !== false && $diff <= time()))
				{
					$sql = new DbQuery();
					$sql->from('order_detail');
					$sql->select('product_id');
					$sql->where('product_id = '.(int)$prod->id);
					$result = Db::getInstance()->getRow($sql, false);
					//delete only if not used in an order
					if (!$result)
					{
						//delete product
						$prod->delete();
					}
				}
			}
		}

		public function deleteOldUploads()
		{
			$files = glob(_PS_MODULE_DIR_.$this->name.'/data/uploads/*');

			$sql = new DbQuery();
			$sql->from($this->name.'_custom_item');
			$sql->select('text');
			$items = Db::getInstance()->executeS($sql, true, false);
			$names = array();
			if (is_array($items))
				foreach ($items as $item)
					$names[] = basename($item['text']);
			//older than one week
			$week = 7 * 24 * 3600;
			foreach ($files as $file)
			{
				if (basename($file) == 'index.php') continue;
				$age = time() - filemtime($file);
				if ($age > $week && ! in_array(basename($file), $names))
				{
					if (file_exists($file))
						unlink($file);
				}
			}
		}

		public function deleteOldCache()
		{
			$files = glob(_PS_MODULE_DIR_.$this->name.'/data/cache/*');
			//older than one month
			$month = 30 * 24 * 3600;
			foreach ($files as $file)
			{
				if (basename($file) == 'index.php') continue;
				$age = time() - filemtime($file);
				if ($age > $month)
				{
					if (file_exists($file))
						unlink($file);
				}
			}
		}

		public function getPath()
		{
			$this->localpath = $this->local_path;
			return $this->path = $this->_path;
		}

		public function getDir()
		{
			return realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR;
		}

		public function getBaseDir()
		{
			return $_SERVER['DOCUMENT_ROOT'];
		}

		public function getCacheDir()
		{
			return $this->context->shop->getBaseURL().'modules/customtextdesign/data/cache/';
		}

		public function getURL()
		{
			return Context::getContext()->shop->getBaseURL().'modules/'.$this->name.'/';
		}

		public function emptyCache()
		{
			$success = true;
			$files = glob(_PS_MODULE_DIR_.$this->name.'/data/cache/*');
			foreach ($files as $file)
			{
				if (basename($file) == 'index.php' || basename($file) == 'pixel.png') continue;
				if (file_exists($file))
					$success &= unlink($file);
			}
			return $success;
		}

		public function prepareGlobalConfig()
		{
			$id_product = 0;

			$tproducts = $this->name.'_product';
			$sql = new DbQuery();
			$sql->from($tproducts);
			$sql->where('id_product = '.(int)$id_product);
			$ctd_product = Db::getInstance()->getRow($sql, false);

			$ttrans = $this->name.'_product_trans';
			$sql = new DbQuery();
			$sql->from($ttrans);
			$sql->where('id_product = '.(int)$id_product);
			$product_translations = Db::getInstance()->executeS($sql, true, false);
			$product_lang = $this->organizeBy('id_lang', $product_translations);

			$sql = new DbQuery();
			$sql->from($this->name.'_group');
			$image_groups = Db::getInstance()->executeS($sql, true, false);

			$colors = $this->getColor(0);
			$fonts = $this->getFont(0);
			$materials = $this->getMaterial(0);

			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
			$image_type = ImageType::getByNameNType($this->config_keys['image_type'], 'products');

			$link = new Link();
			$edit_url = $link->getAdminLink('AdminModules');

			$this->context->smarty->assign(array(
				'ctd_product' => $ctd_product,
				'colors' => $colors,
				'fonts' => $fonts,
				'materials' => $materials,
				'image_groups' => $image_groups,
				'product_lang' => $product_lang,
				'currency' => $currency,
				'edit_url' => $edit_url,
				'prod_image_info' => $image_type
			));

			$this->context->smarty->assign(array('new' => 1, 'urlhash' => '#global_form'));
			$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminGlobal.tpl');
		}

		public function getContent()
		{
			$this->displayForm();
			return $this->html_content;
		}

		private function postProcess()
		{
			//free some cache space
			$this->deleteOldCache();

			if (Tools::getIsset('colorstatus'))
			{
				$id_color = (int)Tools::getValue('id_color');
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$this->name.'_color` SET displayed = !displayed WHERE `id` = '.(int)$id_color);
			}

			if (Tools::getIsset('deletecolor'))
			{
				$id_color = (int)Tools::getValue('id_color');
				$item = $this->getColor($id_color);
				$file = _PS_MODULE_DIR_.$this->name.'/data/texture/'.$item['texture'];
				if (is_file($file)) unlink($file);
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->name.'_color` WHERE `id` = '.(int)$id_color);
			}

			if (Tools::getIsset('fontstatus'))
			{
				$id_font = (int)Tools::getValue('id_font');
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$this->name.'_font` SET displayed = !displayed WHERE `id` = '.(int)$id_font);
			}

			if (Tools::getIsset('groupstatus'))
			{
				$id_group = (int)Tools::getValue('id_group');
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$this->name.'_group` SET displayed = !displayed WHERE `id` = '.(int)$id_group);
			}

			if (Tools::getIsset('imagestatus'))
			{
				$id_image = (int)Tools::getValue('id_image');
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$this->name.'_image` SET displayed = !displayed WHERE `id` = '.(int)$id_image);
			}

			if (Tools::getIsset('deletefont'))
			{
				$id_font = (int)Tools::getValue('id_font');
				$item = $this->getFont($id_font);
				$file = _PS_MODULE_DIR_.$this->name.'/data/font/'.$item['file'];
				if (is_file($file)) unlink($file);
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->name.'_font` WHERE `id` = '.(int)$id_font);
			}

			if (Tools::getIsset('deletegroup'))
			{
				$id_group = (int)Tools::getValue('id_group');
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->name.'_group` WHERE `id` = '.(int)$id_group);
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->name.'_image` WHERE `id_group` = '.(int)$id_group);
			}

			if (Tools::getIsset('deleteimage'))
			{
				$id_image = (int)Tools::getValue('id_image');
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->name.'_image` WHERE `id` = '.(int)$id_image);
			}

			if (Tools::getIsset('materialstatus'))
			{
				$id_material = (int)Tools::getValue('id_material');
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$this->name.'_material` SET displayed = !displayed WHERE `id` = '.(int)$id_material);
			}

			if (Tools::getIsset('deletematerial'))
			{
				$id_material = (int)Tools::getValue('id_material');
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$this->name.'_material` WHERE `id` = '.(int)$id_material);
			}

			if (Tools::getIsset('submitImportTextureFiles'))
			{
				$texture_files = Tools::getValue('texture_files');
				if (is_array($texture_files))
				{
					foreach ($texture_files as $texture_file)
					{
						$ext = pathinfo($texture_file, PATHINFO_EXTENSION);
						$texture_name = ucwords(Tools::strtolower(basename($texture_file, '.'.$ext)));
						$max = $this->getMax('color') + 1;
						$new_filename = Tools::str2url(basename($texture_file, '.'.$ext)).'.'.$ext;
						$rename = rename(_PS_MODULE_DIR_.$this->name.'/data/texture/'.$texture_file, _PS_MODULE_DIR_.$this->name.'/data/texture/'.$new_filename);
						if ($rename)
							$texture_file = $new_filename;
						$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_color (`name`, `texture`, `position`, `displayed`, `is_color`)
						VALUES ("'.pSQL($texture_name).'", "'.pSQL($texture_file).'", '.$max.', 1, 0)';
						Db::getInstance()->Execute($query);
					}
				}
				else
					return false;

			}

			if (Tools::getIsset('submitNewColor'))
			{
				$color_name = Tools::getValue('color_name');
				$color_alpha = (int)Tools::getValue('color_alpha');
				if (!$this->validate($color_name, $this->l('Color Name'))) return false;
				$max = $this->getMax('color') + 1;
				if ($image = $this->uploadFile('color_file', 'texture', 'jpg|jpeg|png|gif'))
				{
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_color (`name`, `is_color`, `alpha`, `texture`, `position`, `displayed`)
					VALUES ("'.pSQL($color_name).'", 0, '.(int)$color_alpha.', "'.pSQL($image).'", '.$max.', 1)';
				}
				else
				{
					$color_code = Tools::getValue('color_code');
					if (!$this->validate($color_code, $this->l('Color Code'))) return false;
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_color (`name`, `is_color`, `color`, `alpha`, `position`, `displayed`)
					VALUES ("'.pSQL($color_name).'", 1, "'.pSQL($color_code).'", "'.pSQL($color_alpha).'", '.$max.', 1)';
				}
				Db::getInstance()->Execute($query);
			}

			if (Tools::getIsset('submitEditColor'))
			{
				$id_color = (int)Tools::getValue('id_color');
				$color_name = Tools::getValue('color_name');
				$color_alpha = (int)Tools::getValue('color_alpha');
				if (!$this->validate($color_name, $this->l('Color Name'))) return false;
				$color_code = Tools::getValue('color_code');
				if (!$this->validate($color_code, $this->l('Color Code'))) return false;
				if ($image = $this->uploadFile('color_file', 'texture', 'jpg|jpeg|png|gif'))
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_color SET `name` = "'.pSQL($color_name).'", `color` = "'.pSQL($color_code).'",
					`alpha` = '.(int)$color_alpha.', `is_color` = 0, `texture` = "'.pSQL($image).'" WHERE `id` = '.(int)$id_color;
				}
				else
				{
					$delete_texture = Tools::getValue('delete_texture');
					if (!$delete_texture)
						$query = 'UPDATE '._DB_PREFIX_.$this->name.'_color SET `name` = "'.pSQL($color_name).'", `color` = "'.pSQL($color_code).'",
						`alpha` = "'.pSQL($color_alpha).'" WHERE `id` = '.(int)$id_color;
					else
					{
						$query = 'UPDATE '._DB_PREFIX_.$this->name.'_color SET `name` = "'.pSQL($color_name).'", `color` = "'.pSQL($color_code).'",
						`alpha` = "'.pSQL($color_alpha).'", `is_color` = 1 WHERE `id` = '.(int)$id_color;
						$filename = Tools::getValue('color_texture');
						if (!empty($filename) && file_exists(_PS_MODULE_DIR_.$this->name.'/data/texture/'.$filename))
							unlink(_PS_MODULE_DIR_.$this->name.'/data/texture/'.$filename);
					}
				}
				Db::getInstance()->Execute($query);
			}

			if (Tools::getIsset('submitImportFontFiles'))
			{
				$font_files = (array)Tools::getValue('font_files');
				if (is_array($font_files))
				{
					foreach ($font_files as $font_file)
					{
						$ext = pathinfo($font_file, PATHINFO_EXTENSION);
						$font_name = ucwords(Tools::strtolower(basename($font_file, '.'.$ext)));
						$max = $this->getMax('font') + 1;
						$new_filename = Tools::str2url(basename($font_file, '.'.$ext)).'.'.$ext;
						$rename = rename(_PS_MODULE_DIR_.$this->name.'/data/font/'.$font_file, _PS_MODULE_DIR_.$this->name.'/data/font/'.$new_filename);
						if ($rename)
							$font_file = $new_filename;
						$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_font (`name`, `file`, `position`, `displayed`)
						VALUES ("'.pSQL($font_name).'", "'.pSQL($font_file).'", '.$max.', 1)';
						Db::getInstance()->Execute($query);
					}
				}
				else
					return false;

			}

			if (Tools::getIsset('submitNewFont'))
			{
				$max = $this->getMax('font') + 1;
				$font_name = Tools::getValue('font_name');
				if (!$this->validate($font_name, $this->l('Font Name'))) return false;
				if ($file = $this->uploadFile('font_file', 'font', 'ttf'))
				{
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_font (`name`, `file`, `position`, `displayed`)
					VALUES ("'.pSQL($font_name).'", "'.pSQL($file).'", '.$max.', 1)';
					Db::getInstance()->Execute($query);
				}
				elseif (!$this->invalid($this->l('Font File'), 'file'))
					return false;
			}

			if (Tools::getIsset('submitEditFont'))
			{
				$id_font = (int)Tools::getValue('id_font');
				$font_name = Tools::getValue('font_name');
				if (!$this->validate($font_name, $this->l('Font Name'))) return false;
				if ($file = $this->uploadFile('font_file', 'font', 'ttf'))
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_font SET `name` = "'.pSQL($font_name).'", `file` = "'.pSQL($file).'" WHERE `id` = '.(int)$id_font;
					$filename = Tools::getValue('font_file_old');
					if (!empty($filename) && file_exists(_PS_MODULE_DIR_.$this->name.'/data/font/'.$filename))
						unlink(_PS_MODULE_DIR_.$this->name.'/data/font/'.$filename);
				}
				else
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_font SET `name` = "'.pSQL($font_name).'" WHERE `id` = '.(int)$id_font;

				Db::getInstance()->Execute($query);
			}

			if (Tools::getIsset('submitNewgroup'))
			{
				$max = $this->getMax('group') + 1;
				$group_name = Tools::getValue('group_name');
				$group_colorize = (int)Tools::getIsset('group_colorize');
				if (!$this->validate($group_name, $this->l('Group Name'))) return false;
				if ($file = $this->uploadFile('group_file', 'group', 'jpg|jpeg|png|gif'))
				{
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_group (`name`, `file`, `position`, `displayed`, `colorize`)
					VALUES ("'.pSQL($group_name).'", "'.pSQL($file).'", '.$max.', 1, '.(int)$group_colorize.')';
					Db::getInstance()->Execute($query);
				}
				elseif (!$this->invalid($this->l('Group Icon'), 'file'))
					return false;
			}

			if (Tools::getIsset('submitEditgroup'))
			{
				$id_group = (int)Tools::getValue('id_group');
				$group_name = Tools::getValue('group_name');
				$group_colorize = (int)Tools::getIsset('group_colorize');
				if (!$this->validate($group_name, $this->l('Group Name'))) return false;
				if ($file = $this->uploadFile('group_file', 'group', 'jpg|jpeg|png|gif'))
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_group SET `name` = "'.pSQL($group_name).'", `colorize` = '.(int)$group_colorize.',
					`file` = "'.pSQL($file).'" WHERE `id` = '.(int)$id_group;
					$filename = Tools::getValue('group_file_old');
					if (!empty($filename) && file_exists(_PS_MODULE_DIR_.$this->name.'/data/group/'.$filename))
						unlink(_PS_MODULE_DIR_.$this->name.'/data/group/'.$filename);
				}
				else
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_group SET `name` = "'.pSQL($group_name).'",
					`colorize` = '.(int)$group_colorize.' WHERE `id` = '.(int)$id_group;

				Db::getInstance()->Execute($query);
			}

			if (Tools::getIsset('submitImportImageFiles'))
			{
				$image_files = Tools::getValue('image_files');
				if (is_array($image_files))
				{
					$image_prices = (array)Tools::getValue('image_price');
					$image_quantities = (array)Tools::getValue('image_quantity');
					$image_groups = (array)Tools::getValue('image_group');
					foreach ($image_files as $image_file)
					{
						$ext = pathinfo($image_file, PATHINFO_EXTENSION);
						$image_name = ucwords(Tools::strtolower(basename($image_file, '.'.$ext)));
						$max = $this->getMax('image') + 1;
						$image_price = $image_prices[$image_file];
						$image_quantity = $image_quantities[$image_file];
						$image_group = $image_groups[$image_file];
						$new_filename = Tools::str2url(basename($image_file, '.'.$ext)).'.'.$ext;
						$rename = rename(_PS_MODULE_DIR_.$this->name.'/data/image/'.$image_file, _PS_MODULE_DIR_.$this->name.'/data/image/'.$new_filename);
						if ($rename)
							$image_file = $new_filename;
						$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_image (`id_group`, `name`, `price`, `file`, `position`, `displayed`, `quantity`)
						VALUES ('.(int)$image_group.', "'.pSQL($image_name).'", '.(float)$image_price.', "'.pSQL($image_file).'", '.(int)$max.', 1,
						'.(int)$image_quantity.')';
						Db::getInstance()->Execute($query);
					}
				}
				else
					return false;
			}

			if (Tools::getIsset('submitNewimage'))
			{
				$max = $this->getMax('image') + 1;
				$image_name = Tools::getValue('image_name');
				$image_price = (float)Tools::getValue('image_price');
				$image_quantity = (int)Tools::getValue('image_quantity');
				$image_group = (int)Tools::getValue('image_group');
				if (!$this->validate($image_name, $this->l('Image Name'))) return false;
				if ($file = $this->uploadFile('image_file', 'image', 'jpg|jpeg|png|gif'))
				{
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_image (`id_group`, `name`, `price`, `file`, `position`, `displayed`, `quantity`)
					VALUES ('.(int)$image_group.', "'.pSQL($image_name).'", '.(float)$image_price.', "'.pSQL($file).'",	'.(int)$max.', 1,
					'.(int)$image_quantity.')';
					Db::getInstance()->Execute($query);
				}
				elseif (!$this->invalid($this->l('Image File'), 'file'))
					return false;
			}

			if (Tools::getIsset('submitEditimage'))
			{
				$id_image = (int)Tools::getValue('id_image');
				$image_name = Tools::getValue('image_name');
				$image_price = (float)Tools::getValue('image_price');
				$image_quantity = (int)Tools::getValue('image_quantity');
				$image_group = (int)Tools::getValue('image_group');
				if (!$this->validate($image_name, $this->l('Image Name'))) return false;
				if ($file = $this->uploadFile('image_file', 'image', 'jpg|jpeg|png|gif'))
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_image SET `id_group` = '.(int)$image_group.', `name` = "'.pSQL($image_name).'",
					`price` = '.(float)$image_price.',`quantity` = "'.(int)$image_quantity.'", `file` = "'.pSQL($file).'" WHERE `id` = '.(int)$id_image;
					$filename = Tools::getValue('image_file_old');
					if (!empty($filename) && file_exists(_PS_MODULE_DIR_.$this->name.'/data/image/'.$filename))
						unlink(_PS_MODULE_DIR_.$this->name.'/data/image/'.$filename);
				}
				else
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_image SET `id_group` = '.(int)$image_group.', `name` = "'.pSQL($image_name).'",
					`price` = "'.pSQL($image_price).'", `quantity` = "'.(int)$image_quantity.'" WHERE `id` = '.(int)$id_image;
				}
				Db::getInstance()->Execute($query);
			}

			if (Tools::getIsset('submitNewMaterial'))
			{
				$max = $this->getMax('material') + 1;
				$material_name = Tools::getValue('material_name');
				if (!$this->validate($material_name, $this->l('Material Name'))) return false;
				$material_price = (float)Tools::getValue('material_price');
				if (!$this->validate($material_price, $this->l('Material Price'), 'number')) return false;
				if ($file = $this->uploadFile('material_image', 'material', 'jpg|jpeg|png|gif'))
				{
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_material (`name`, `price`, `file`, `position`, `displayed`)
					VALUES ("'.pSQL($material_name).'", '.(float)$material_price.', "'.pSQL($file).'", '.$max.', 1)';
					Db::getInstance()->Execute($query);
				}
				else
				{
					$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_material (`name`, `price`, `position`, `displayed`)
					VALUES ("'.pSQL($material_name).'", '.pSQL($material_price).', '.$max.', 1)';
					Db::getInstance()->Execute($query);
				}
			}

			if (Tools::getIsset('submitEditMaterial'))
			{
				$id_material = (int)Tools::getValue('id_material');
				$material_name = Tools::getValue('material_name');
				if (!$this->validate($material_name, $this->l('Material Name'))) return false;
				$material_price = (float)Tools::getValue('material_price');
				if (!$this->validate($material_price, $this->l('Material Price'), 'number')) return false;
				if ($file = $this->uploadFile('material_file', 'material', 'jpg|jpeg|png|gif'))
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_material SET `name` = "'.pSQL($material_name).'",
					`price` = '.(float)$material_price.', `file` = "'.pSQL($file).'" WHERE `id` = '.(int)$id_material;
					$filename = Tools::getValue('material_file_old');
					if (!empty($filename) && file_exists(_PS_MODULE_DIR_.$this->name.'/data/material/'.$filename))
						unlink(_PS_MODULE_DIR_.$this->name.'/data/material/'.$filename);
				}
				else
				{
					$query = 'UPDATE '._DB_PREFIX_.$this->name.'_material SET `name` = "'.pSQL($material_name).'",
					`price` = '.pSQL($material_price).' WHERE `id` = '.(int)$id_material;
				}
				Db::getInstance()->Execute($query);
			}

			if (Tools::getIsset('submitUpdateConfig'))
			{
				foreach ($this->config_keys as $key => $value)
				{
					if (Tools::substr($key, 0, 1) == '_')
					{
						foreach ($this->languages as $lang)
						{
							$id_language = $lang['id_lang'];
							$akey = Tools::substr($key, 1).'_'.$id_language;
							$cvalue = Tools::getValue($akey);
							$value = Configuration::get($this->name.$akey);
							if ($value != $cvalue)
								Configuration::updateValue($this->name.$akey, $cvalue);
						}
						continue;
					}

					$cvalue = Tools::getValue($key);
					if (Tools::substr($key, 0, 4) == 'num_')
					{
						$keyname = preg_replace('/^num_/', '', $key);
						if (!$this->validate($cvalue, $keyname, 'number')) continue;
					}
					if (Tools::substr($key, 0, 2) == 'a_')
					{
						$bvalue = $cvalue;
						$cvalue = array();
						if (is_array($bvalue))
						{
							foreach ($bvalue as $id => $checkbox)
							{
								if ($checkbox == 'on')
									$cvalue[] = $id;
							}
						}
						$cvalue = implode(',', $cvalue);
					}
					Configuration::updateValue($this->name.$key, $cvalue);
				}
			}

			if (Tools::getIsset('submitUpdateModuleConfigPages'))
			{
				Tools::safePostVars();
				foreach ($_POST as $key => $array)
				{
					if (strpos($key, 'colors_') !== 0) continue;
					if (strpos($key, 'colors_min_') === 0)
					{
						$pagename = str_replace('colors_min_', '', $key);
						$min = $array;
						$max = Tools::getValue('colors_max_'.$pagename);
						$this->saveModulePage($pagename, array('colors' => "$min, $max"));
						continue;
					}
					if (! is_array($array)) continue;
					$pagename = str_replace('colors_', '', $key);
					$colors = array();
					foreach ($array as $id_item => $on)
					{
						if ($on != 'on') continue;
						$colors[] = $id_item;
					}
					$colors = implode(',', $colors);
					$this->saveModulePage($pagename, array('colors' => $colors));
				}

				foreach ($_POST as $key => $array)
				{
					if (strpos($key, 'fonts_') !== 0) continue;
					if (strpos($key, 'fonts_min_') === 0)
					{
						$pagename = str_replace('fonts_min_', '', $key);
						$min = $array;
						$max = Tools::getValue('fonts_max_'.$pagename);
						$this->saveModulePage($pagename, array('fonts' => "$min,$max"));
						continue;
					}
					if (! is_array($array)) continue;
					$pagename = str_replace('fonts_', '', $key);
					$fonts = array();
					foreach ($array as $id_item => $on)
					{
						if ($on != 'on') continue;
						$fonts[] = $id_item;
					}
					$fonts = implode(',', $fonts);
					$this->saveModulePage($pagename, array('fonts' => $fonts));
				}

				foreach ($_POST as $key => $array)
				{
					if (strpos($key, 'materials_') !== 0) continue;
					if (! is_array($array)) continue;
					$pagename = str_replace('materials_', '', $key);
					$materials = array();
					foreach ($array as $id_item => $on)
					{
						if ($on != 'on') continue;
						$materials[] = $id_item;
					}
					$materials = implode(',', $materials);
					$this->saveModulePage($pagename, array('materials' => $materials));
				}
			}

			if (Tools::getIsset('submitUpdatePageConfig'))
			{
				$id_page_config = (int)Tools::getValue('id_page_config');
				foreach ($this->config_keys as $key => $value)
				{
					if (Tools::substr($key, 0, 1) == '_')
					{
						foreach ($this->languages as $lang)
						{
							$id_language = $lang['id_lang'];
							$akey = Tools::substr($key, 1).'_'.$id_language;
							$cvalue = Tools::getValue($akey);
							$value = Configuration::get('ctd_'.$akey.$id_page_config);
							if ($value != $cvalue)
								Configuration::updateValue('ctd_'.$akey.$id_page_config, $cvalue);
						}
						continue;
					}

					$cvalue = Tools::getValue($key);
					if (Tools::substr($key, 0, 4) == 'num_')
					{
						$keyname = preg_replace('/^num_/', '', $key);
						if (!$this->validate($cvalue, $keyname, 'number')) continue;
					}
					if (Tools::substr($key, 0, 2) == 'a_')
					{
						$bvalue = $cvalue;
						$cvalue = array();
						if (is_array($bvalue))
						{
							foreach ($bvalue as $id => $checkbox)
							{
								if ($checkbox == 'on')
									$cvalue[] = $id;
							}
						}
						$cvalue = implode(',', $cvalue);
					}
					Configuration::updateValue('ctd_'.$key.$id_page_config, $cvalue);
				}
			}

			if (Tools::getIsset('submitCancel'))
				$this->redirect();
		}

		private function displayForm()
		{
			$admin_link = $this->getAdminLink();
			$this->context->smarty->assign('req', $admin_link);

			$this->context->smarty->assign('num_size_min', Configuration::get($this->name.'num_size_min'));
			$this->context->smarty->assign('num_size_max', Configuration::get($this->name.'num_size_max'));

			$this->context->controller->addCSS($this->_path.'css/admin.css');
			$this->context->controller->addCSS($this->_path.'css/multi-select.css');
			$this->context->controller->addJQueryUI('ui.slider');
			$this->context->controller->addJS(_MODULE_DIR_.$this->name.'/js/admin.js');
			$this->context->controller->addJS(_MODULE_DIR_.$this->name.'/js/admin-tools.js');
			$this->context->controller->addJS(_MODULE_DIR_.$this->name.'/js/jquery.multi-select.js');
			$this->context->controller->addJqueryPlugin('tablednd');
			$this->context->controller->addJqueryPlugin('colorpicker');

			$this->languages = Language::getLanguages();
			$default_lang = Configuration::get('PS_LANG_DEFAULT');

			$this->context->smarty->assign(array('languages' => $this->languages, 'default_lang' => $default_lang));
			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
			$this->context->smarty->assign('currency', $currency);

			$this->postProcess();

			if (Tools::getIsset('submitGlobalConfig'))
			{
				$this->prepareGlobalConfig();
				return;
			}

			if (Tools::getIsset('submitEmptyCache'))
			{
				$success = $this->emptyCache();
				if ($success)
					$this->html_content .= $this->displayConfirmation($this->l('Cache successfully reset'));
				else
					$this->html_content .= $this->displayError($this->l('Some files were not deleted! Please manually check /data/cache inside the module folder'));
			}

			if (Tools::getIsset('submitImportTexture'))
			{
				$this->context->smarty->assign(array('urlhash' => '#colors_form', 'items'=> $this->getTextureList()));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayImportTexture.tpl');
				return;
			}

			if (Tools::getIsset('submitAddNewColor'))
			{
				$this->context->controller->addJqueryPlugin('colorpicker');
				$this->context->smarty->assign(array('new' => 1, 'urlhash' => '#colors_form'));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminColor.tpl');
				return;
			}

			if (Tools::getIsset('updatecolor'))
			{
				$this->context->controller->addJqueryPlugin('colorpicker');
				$id_color = (int)Tools::getValue('id_color');
				$color = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.$this->name.'_color` WHERE `id` = '.(int)$id_color);
				$this->context->smarty->assign(array('new' => 0, 'urlhash' => '#colors_form', 'color' => isset($color[0])?$color[0]:array()));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminColor.tpl');
				return;
			}

			if (Tools::getIsset('submitImportFont'))
			{
				$this->context->smarty->assign(array('urlhash' => '#fonts_form', 'items'=> $this->getFontList()));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayImportFont.tpl');
				return;
			}

			if (Tools::getIsset('submitAddNewFont'))
			{
				$this->context->smarty->assign(array('new' => 1, 'urlhash' => '#fonts_form'));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminFont.tpl');
				return;
			}

			if (Tools::getIsset('updatefont'))
			{
				$id_font = (int)Tools::getValue('id_font');
				$a_font = array();
				$font = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.$this->name.'_font` WHERE `id` = '.(int)$id_font);
				$a_font = isset($font[0])?$font[0]:array();
				$this->context->smarty->assign(array('new' => 0, 'urlhash' => '#colors_form', 'font' => $a_font));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminFont.tpl');
				return;
			}

			if (Tools::getIsset('submitAddNewgroup'))
			{
				$this->context->smarty->assign(array('new' => 1, 'urlhash' => '#groups_form'));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminGroup.tpl');
				return;
			}

			if (Tools::getIsset('updategroup'))
			{
				$id_group = (int)Tools::getValue('id_group');
				$a_group = array();
				$group = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.$this->name.'_group` WHERE `id` = '.(int)$id_group);
				$a_group = isset($group[0])?$group[0]:array();
				$this->context->smarty->assign(array('new' => 0, 'urlhash' => '#groups_form', 'group' => $a_group));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminGroup.tpl');
				return;
			}

			if (Tools::getIsset('submitImportImage'))
			{
				$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_group WHERE 1 ORDER BY position ASC';
				$groups = Db::getInstance()->ExecuteS($query);
				$this->context->smarty->assign(array('urlhash' => '#images_form', 'items'=> $this->getImageList(), 'groups' => $groups));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayImportImage.tpl');
				return;
			}

			if (Tools::getIsset('submitAddNewimage'))
			{
				$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_group WHERE 1 ORDER BY position ASC';
				$groups = Db::getInstance()->ExecuteS($query);
				$this->context->smarty->assign(array('new' => 1, 'urlhash' => '#images_form', 'groups' => $groups));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminImage.tpl');
				return;
			}

			if (Tools::getIsset('updateimage'))
			{
				$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_group WHERE 1 ORDER BY position ASC';
				$groups = Db::getInstance()->ExecuteS($query);
				$id_image = (int)Tools::getValue('id_image');
				$a_image = array();
				$image = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.$this->name.'_image` WHERE `id` = '.(int)$id_image);
				$a_image = isset($image[0])?$image[0]:array();
				$this->context->smarty->assign(array('new' => 0, 'urlhash' => '#images_form', 'image' => $a_image, 'groups' => $groups));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminImage.tpl');
				return;
			}

			if (Tools::getIsset('submitAddNewMaterial'))
			{
				$this->context->smarty->assign(array('new' => 1, 'urlhash' => '#materials_form'));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminMaterial.tpl');
				return;
			}

			if (Tools::getIsset('updatematerial'))
			{
				$id_material = (int)Tools::getValue('id_material');
				$a_material = array();
				$material = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.$this->name.'_material` WHERE `id` = '.(int)$id_material);
				$a_material = isset($material[0])?$material[0]:array();
				$this->context->smarty->assign(array('new' => 0, 'urlhash' => '#materials_form', 'material' => $a_material));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminMaterial.tpl');
				return;
			}

			if (Tools::getIsset('configurepages'))
			{

				$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_color WHERE 1 ORDER BY position ASC';
				$colors = Db::getInstance()->ExecuteS($query);

				$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_font WHERE 1 ORDER BY position ASC';
				$fonts = Db::getInstance()->ExecuteS($query);

				$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_material WHERE 1 ORDER BY position ASC';
				$materials = Db::getInstance()->ExecuteS($query);

				$module_pages = $this->getModulePages();

				$urlhash = '';
				Tools::safePostVars();
				foreach ($_POST as $key => $value)
				{
					if (strpos($key, 'submitUpdateModulePages') === 0)
					{
						$urlhash = str_replace('submitUpdateModulePages', '', $key);
						break;
					}
				}

				$this->context->smarty->assign(array('pagehash' => $urlhash, 'link'=> new Link(), 'module_name' => $this->name, 'pages' => $module_pages,
					'colors' => $colors, 'fonts' => $fonts, 'materials' => $materials, 'urlhash' => '#pagelink'));
				$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminPages.tpl');
				return;
			}

			if (Tools::getIsset('submitConfigurePage'))
			{
				$pagename = Tools::getValue('pagename');
				if ($pagename)
				{
					$module_pages = $this->getModulePages();
					$module_page = $module_pages[$pagename];
					$this->context->smarty->assign(array(
						'link'=> new Link(),
						'secure_key' => $this->secure_key,
						'module_pages' => $this->getModulePages(),
						'module_page' => $module_page,
						'fonts' => $this->getFont(0),
						'tax_rules_groups' => TaxRulesGroup::getTaxRulesGroups(true),
						'tax_exclude_taxe_option' => Tax::excludeTaxeOption(),
						'carriers' => Carrier::getCarriers($this->context->language->id, true),
						'urlhash' => '#config',
						'pagename' => $pagename
					));
					$id_page_config = $module_page['id_page_config'];
					foreach ($this->config_keys as $key => $value)
					{
						if (Tools::substr($key, 0, 1) == '_')
						{
							foreach ($this->languages as $lang)
							{
								$id_language = $lang['id_lang'];
								$akey = Tools::substr($key, 1).'_'.$id_language;
								$value = Configuration::get('ctd_'.$akey.$id_page_config);
								$this->context->smarty->assign($akey, $value);
							}
							continue;
						}
						$this->context->smarty->assign($key, Configuration::get('ctd_'.$key.$id_page_config));
					}
					$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayConfigPage.tpl');
					return;
				}
			}

			if (Tools::getIsset('submitSaveSpecificPrices'))
			{
				//search post for material_{material_id}_{material_size}
				Tools::safePostVars();
				foreach ($_POST as $key => $value)
				{
					if (strpos($key, 'material_specific_') === false) continue;
					$matches = array();
					preg_match_all('/material_specific_(\d+)_(\d+)/', $key, $matches);
					if (!isset($matches[1][0]) || !isset($matches[2][0])) continue;

					$material_id    = pSQL($matches[1][0]);
					$material_size  = pSQL($matches[2][0]);
					$material_price = pSQL($value);

					$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_material_prices WHERE material_id = '.$material_id.' AND material_size = '.$material_size;
					$material_prices_rows = Db::getInstance()->ExecuteS($query);
					if (!count($material_prices_rows))
						$query = 'INSERT INTO '._DB_PREFIX_.$this->name.'_material_prices (material_id, material_size, material_price)
						VALUES('.$material_id.', '.$material_size.', '.$material_price.')';
					else
						$query = 'UPDATE '._DB_PREFIX_.$this->name.'_material_prices SET material_price = '.$material_price.'
						WHERE material_id = '.$material_id.' AND material_size = '.$material_size;
					Db::getInstance()->Execute($query);
				}
				$this->context->smarty->assign(array('urlhash' => '#materials_form'));
			}

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_color WHERE 1 ORDER BY position ASC';
			$colors = Db::getInstance()->ExecuteS($query, true, false);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_font WHERE 1 ORDER BY position ASC';
			$fonts = Db::getInstance()->ExecuteS($query, true, false);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_group WHERE 1 ORDER BY position ASC';
			$groups = Db::getInstance()->ExecuteS($query, true, false);
			$sorted_groups = $this->organizeBy('id', $groups);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_image WHERE 1 ORDER BY id_group ASC';
			$images = Db::getInstance()->ExecuteS($query, true, false);

			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_material WHERE 1 ORDER BY position ASC';
			$materials = Db::getInstance()->ExecuteS($query, true, false);

			// get specific material prices
			$material_prices = array();
			$query = 'SELECT * FROM '._DB_PREFIX_.$this->name.'_material_prices WHERE 1';
			$material_prices_rows = Db::getInstance()->ExecuteS($query, true, false);
			foreach ($material_prices_rows as $material_prices_row)
				$material_prices[$material_prices_row['material_id']][$material_prices_row['material_size']] = $material_prices_row['material_price'];
			$this->context->smarty->assign('material_prices', $material_prices);

			$token = Tools::getAdminTokenLite('AdminModules');
			$default = '&amp;configure='.$this->name.'&amp;tab_module=front_office_features&amp;module_name='.$this->name;

			$this->context->smarty->assign(array(
				'colors' => $colors,
				'fonts' => $fonts,
				'groups' => $groups,
				'sorted_groups' => $sorted_groups,
				'images' => $images,
				'materials' => $materials,
				'tax_rules_groups' => TaxRulesGroup::getTaxRulesGroups(true),
				'carriers' => Carrier::getCarriers($this->context->language->id, true),
				'image_types' => ImageType::getImagesTypes('products'),
				'tax_exclude_taxe_option' => Tax::excludeTaxeOption(),
				'token' => $token,
				'secure_key' => $this->secure_key,
				'default' => $default,
				'name' => $this->name,
				'errors' => $this->errors,
				'warnings' => $this->warnings,
				'link' => $this->context->link,
				'module_pages' => $this->getModulePages()
			));

			foreach ($this->config_keys as $key => $value)
			{
				if (Tools::substr($key, 0, 1) == '_')
				{
					foreach ($this->languages as $lang)
					{
						$id_language = $lang['id_lang'];
						$akey = Tools::substr($key, 1).'_'.$id_language;
						$value = Configuration::get($this->name.$akey);
						$this->context->smarty->assign($akey, $value);
					}
					continue;
				}
				$this->context->smarty->assign($key, Configuration::get($this->name.$key));
			}

			$this->html_content .= $this->display(__FILE__, 'views/templates/admin/DisplayAdminForm.tpl');
		}

		public function getAdminLink($add = '')
		{
			return $this->currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').$add;
		}

		public function checkAdmin()
		{
			$cookie = new Cookie('psAdmin');
			if ($cookie->id_employee) return true;
			return false;
		}

		public function redirect($add = '')
		{
			Tools::redirectAdmin($this->getAdminLink($add));
		}

		public function validate($var, $name, $type = '')
		{
			switch ($type)
			{
				case '':
					if (empty($var))
					{
						$this->invalid($name, $type);
						return false;
					}
					return true;
				case 'number':
					if (!is_numeric($var))
					{
						$this->invalid($name, $type);
						return false;
					}
					return true;
				case 'file':
					if (!$var || empty($var))
					{
						$this->invalid($name, $type);
						return false;
					}
					return true;
			}
			return true;
		}

		public function invalid($name, $type = '')
		{
			switch ($type)
			{
				case '':
				case 'file':
					$this->errors[] = sprintf($this->l('"%s" is a required field.'), $name);
					return false;
				case 'number':
					$this->errors[] = sprintf($this->l('"%s" is not a valid number.'), $name);
					return false;
			}
			return false;
		}

		public function getMax($table)
		{
			$query = 'SELECT max(position) as maxposition FROM '._DB_PREFIX_.$this->name.'_'.$table;
			$max_position = Db::getInstance()->ExecuteS($query, true, false);
			$max_position = $max_position[0]['maxposition'];
			return $max_position == null ? 0 : (int)$max_position;
		}

		public function organizeBy($key, $source)
		{
			$return = array();
			if (is_array($source))
			{
				foreach ($source as $row)
				{
					$akey = $row[$key];
					$return[$akey] = $row;
				}
			}
			return $return;
		}

		public function organizeDoubleBy($key1, $key2, $source, $unset = false)
		{
			$return = array();
			if (is_array($source))
			{
				foreach ($source as $row)
				{
					$akey1 = $row[$key1];
					$akey2 = $row[$key2];
					if ($unset)
					{
						unset($row[$key1]);
						unset($row[$key2]);
					}
					$return[$akey1][$akey2] = $row;
				}
			}
			return $return;
		}

		public function implodeWithKeys($array, $glue = 'AND', $equal = '=')
		{
			$where = array();
			foreach ($array as $key => $value)
			{
				$value = (int)$value;
				$where[] = "$key $equal $value";
			}
			return implode(" $glue ", $where);
		}

		public function loadConfig()
		{
			$ctd_config = array();
			require_once('inc/config.php');
			$this->config = $ctd_config;
			$this->smarty->assign('ctd_config', $this->config);
		}

		public function requireClass($class)
		{
			$class_path = "inc/$class.php";
			require_once($class_path);
		}

		public function exifImageType($path)
		{
			$image_info = getimagesize($path);
			return isset($image_info[2]) ? $image_info[2] : 0;
		}

		public function urlToPath($url)
		{
			$path = str_replace(_PS_BASE_URL_, $_SERVER['DOCUMENT_ROOT'], $url);
			$path = str_replace(_PS_BASE_URL_SSL_, $_SERVER['DOCUMENT_ROOT'], $url);
			if (file_exists($path) && $realpath = realpath($path))
				return $realpath;
			else
				return $url;
			return $url;
		}

		public function getImagePath($id_image, $type, $id_product = 0)
		{
			$sql = new DbQuery();
			$sql->from($this->name.'_replace');
			$where = 'id_image = '.(int)$id_image;
			if ((int)$id_product)
				$where .= ' AND id_product = '.(int)$id_product;
			$sql->where($where);
			$row = Db::getInstance()->getRow($sql, false);
			if ($row)
			{
				$image = $row['image'];
				return realpath(dirname(__FILE__).'/data/replace/'.$image);
			}
			$image = new Image($id_image);
			return _PS_PROD_IMG_DIR_.$image->getImgPath().'-'.$type.'.'.$image->image_format;
		}

		public function getProductAttributes($id_product, $id_product_attribute)
		{
			$id_lang = (int)$this->context->language->id;
			$product = new Product((int)$id_product, false, $id_lang);
			$attributes = $product->getAttributeCombinationsById((int)$id_product_attribute, $id_lang);
			$product_attributes = array();
			foreach ($attributes as $attribute)
				$product_attributes[] = $attribute['attribute_name'];
			if (! count($product_attributes))
				return '-';
			return implode(' - ', $product_attributes);
		}

		public function uploadFile($field, $dir, $ext)
		{
			if (!isset($_FILES[$field])) return false;
			$filename = $_FILES[$field]['name'];
			if (empty($filename))
				return false;
			if (!preg_match('/.+\.('.$ext.')$/', Tools::strtolower($filename)))
				return false;
			if (!move_uploaded_file($_FILES[$field]['tmp_name'], dirname(__FILE__).'/data/'.$dir.'/'.$filename))
				return false;
			return $filename;
		}

		public function translate($string, $iso_lang, $source, $js = false)
		{
			$file = dirname(__FILE__).'/translations/'.$iso_lang.'.php';
			if (!file_exists($file)) return $string;
			$_MODULE = array();
			include($file);
			$key = md5(str_replace("\'", '\\\'', $string));

			$current_key = Tools::strtolower('<{'.$this->name.'}'._THEME_NAME_.'>'.$source).'_'.$key;
			$default_key = Tools::strtolower('<{'.$this->name.'}prestashop>'.$source).'_'.$key;
			$ret = $string;
			if (isset($_MODULE[$current_key]))
				$ret = Tools::stripslashes($_MODULE[$current_key]);
			elseif (isset($_MODULE[$default_key]))
				$ret = Tools::stripslashes($_MODULE[$default_key]);

			if ($js)
				$ret = addslashes($ret);

			return $ret;
		}

		public function uninstall()
		{
			$errors = '';
			try{
				$field_table = $this->name.'_field';
				$sql = new DbQuery();
				$sql->from($field_table);
				$rows = Db::getInstance()->executeS($sql, true, false);
				if (is_array($rows))
				{
					foreach ($rows as $row)
					{
						$id_customization_field = $row['id_customization_field'];
						DB::getInstance()->delete('customization_field', 'id_customization_field = '.$id_customization_field);
						DB::getInstance()->delete('customization_field_lang', 'id_customization_field = '.$id_customization_field);
					}
				}
			}
			catch(Exception $e)
			{
				$errors .= $e->getMessage();
			}

			if (!Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_color')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_font')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_group')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_image')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_material')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_material_prices')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_page_config')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_custom_product')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_custom_item')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_product')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_product_trans')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_measure')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_overlay')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_mask')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_mask2')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_replace')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_field')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_design')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_custom_field')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_custom_field_trans')
				|| !Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_customization'))
				return false;

			foreach ($this->config_keys as $key => $value)
			{
				if (Tools::substr($key, 0, 1) == '_')
				{
					foreach ($this->languages as $lang)
					{
						$id_language = $lang['id_lang'];
						$akey = Tools::substr($key, 1).'_'.$id_language;
						$value = Configuration::get($this->name.$akey);
						if ($value !== false)
							Configuration::deleteByName($this->name.$akey);
					}
					continue;
				}
				if ($value !== false)
					Configuration::deleteByName($this->name.$key);
			}

			$this->emptyCache();

			// Uninstall Tabs
			$tab = new Tab((int)Tab::getIdFromClassName('AdminPdfOutput'));
			$tab->delete();
			$tab = new Tab((int)Tab::getIdFromClassName('AdminAjaxModule'));
			$tab->delete();
			$tab = new Tab((int)Tab::getIdFromClassName('AdminUploadModule'));
			$tab->delete();

			return parent::uninstall();
		}

		public function dbg($var, $exit = false)
		{
			if (Tools::getIsset('ctd'))
			{
				echo '<pre>ctd->dbg: ';
				var_dump($var);
				echo '</pre>';
			}
			if (Tools::getIsset('ctdx') || $exit)
			{
				echo '<pre>ctd->dbg: ';
				var_dump($var);
				echo '</pre>';
				exit();
			}
		}

		public function dbgg($var, $exit = false)
		{
			echo '<pre>ctd->dbg: ';
			var_dump($var);
			echo '</pre>';

			if ($exit)
				exit();
		}

		public function log($var, $var_name = '')
		{
			$log = '<pre>'.time().': '.$var_name.($var_name ? '=>' : '');
			$log .= is_string($var) ? $var : print_r($var, true);
			$log .= "</pre>\r\n";
			file_put_contents($this->getDir().'log.html', $log, FILE_APPEND);
		}
	}
?>