{**
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
*}

<!-- Custom Text Design Module -->
<div class="ctd_order_custom_product ctd_sl_fix" id="id_order_detail_{$id_order_detail|intval}" style="display:none1">
	<div>
		<a href="{$link->getModuleLink($module, 'Preview', $params)|escape:'htmlall':'UTF-8'}" target="_blank">
			<img src="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}" height="50" class="ctd_preview_img" />
		</a>
	</div>
	<div>
		<label>{l s='Design Cost' mod='customtextdesign'}:</label>
		{Tools::displayPrice(Tools::convertPrice($custom_product.price))|escape:'htmlall':'UTF-8'}
	</div>
</div>
<!-- /Custom Text Design Module -->