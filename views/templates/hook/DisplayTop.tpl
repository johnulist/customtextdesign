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

<div class="ctd_order_custom_product ctd_level_{$custom_product.level|intval} ctd_sl_fix" data-id="{$custom_product.id_custom_product|intval}" id="product_{$custom_product.id_product|intval}_{$custom_product.id_attribute|intval}" style="display:none;margin-bottom:10px">
	<div class="ctd_design_header">
	<div>
		<a href="{$mlink->getModuleLink($module, 'Preview', $params)|escape:'htmlall':'UTF-8'}" target="_blank">
			<img src="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}" style="height:50px !important" class="ctd_preview_img" />
		</a>
	</div>
	<div>
		<label style="width: auto !important;">{l s='Design Cost' mod='customtextdesign'}:</label>
		{Tools::displayPrice(Tools::convertPrice($custom_product.price + $custom_product.custom_price))|escape:'htmlall':'UTF-8'}
	</div>
	{if count($oos_items)}
	<div>
		{foreach from=$oos_items item=oos_item}
			<span class="ctd_oos_item"><img src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($oos_item.file, 0, 20)}" /> {l s='The item ' mod='customtextdesign'} "{$oos_item.name}" {l s='is out of stock' mod='customtextdesign'}<br></span>
		{/foreach}
	</div>
	{/if}
	</div>
</div>
<!-- /Custom Text Design Module -->