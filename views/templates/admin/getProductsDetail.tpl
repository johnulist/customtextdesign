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
<div class="ctd_order_custom_product ctd_sl_fix">
	<div>
		<label>{l s='Preview' mod='customtextdesign'} :</label>
		<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}" target="_blank" style="text-decoration:none">
			<img src="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}" height="100" />
		</a>
		<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}_cad.png" target="_blank" style="text-decoration:none">
			<img src="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}_cad.png" height="100" />
		</a>
		{if $custom_product.has_mask2}
		<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$custom_product.preview|escape:'htmlall':'UTF-8'}_sub.png" target="_blank" style="text-decoration:none">
			<img src="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomDesign::getSub($custom_product.id_custom_product)}" height="100" />
		</a>
		{/if}
	</div>
	<div>
		<label>{l s='Design Cost' mod='customtextdesign'}:</label>
		{Tools::displayPrice(Tools::convertPrice($custom_product.price))|escape:'htmlall':'UTF-8'}
		{if (float)$custom_product.custom_price > 0}
			&nbsp;+ {l s='Custom size' mod='customtextdesign'}: {Tools::displayPrice(Tools::convertPrice($custom_product.custom_price))|escape:'htmlall':'UTF-8'}
		{/if}
	</div>
	{if (float)$custom_product.product_width * (float)$custom_product.product_height}
	<div>
		<label>{l s='Product width' mod='customtextdesign'}:</label>
		<b>{(float)$custom_product.product_width|floatval} cm</b>
	</div>
	<div>
		<label>{l s='Product height' mod='customtextdesign'}:</label>
		<b>{(float)$custom_product.product_height|floatval} cm</b>
	</div>
	{/if}
	{if ! empty($custom_product.product_color)}
	<div>
		<label>{l s='Product color' mod='customtextdesign'}:</label>
		{if !isset($custom_product.has_listed_color)}
		<b>#{$custom_product.product_color|escape:'htmlall':'UTF-8'}</b>
		{else}
		{$color = $colors[$custom_product.product_color]}
		<b>{$color.id|intVal}-{$color.name|escape:'htmlall':'UTF-8'} {$color.color|escape:'htmlall':'UTF-8'}</b>
		{if $color.texture}
		<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/texture/{$color.texture|escape:'htmlall':'UTF-8'}" target="_blank">
			<span class="ctd_order_color" style="background-image:url({$ctd_module_dir|escape:'htmlall':'UTF-8'}data/texture/{$color.texture|escape:'htmlall':'UTF-8'})"></span>
		</a>
		{else}
		<span class="ctd_order_color" style="background-color:{$color.color|escape:'htmlall':'UTF-8'}"></span>
		{/if}
		{/if}
	</div>
	{/if}
	<div style="display:none">
		<label>{l s='Total Cost' mod='customtextdesign'}:</label>
		{Tools::displayPrice(Tools::convertPrice($custom_product.price))|escape:'htmlall':'UTF-8'}
	</div>
	{if count($items)}
	<h4>{l s='Design Items' mod='customtextdesign'}</h4>
	{foreach from=$items item=item name=items}
	<div class="ctd_order_custom_item">
		{if $item.type == 'text'}
		<div class="ctd_sl_fix">
			<label>{l s='Preview' mod='customtextdesign'}:</label>
			<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$item.preview|escape:'htmlall':'UTF-8'}.png" target="_blank">
				<img height="30" src="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/cache/{$item.preview|escape:'htmlall':'UTF-8'}.png" />
			</a>
		</div>
		<div class="ctd_sl_fix">
			<label>{l s='Text' mod='customtextdesign'}:</label>
			<input class="ctd_sl_fix" type="text" onclick="this.select()" value="{$item.text|htmlspecialchars}" />
		</div>
		<div class="ctd_sl_fix">
			<label>{l s='Color' mod='customtextdesign'}:</label>
			{if (int)$item.color}
			{$color = $colors[$item.color]}
			{$color.id|intval}-{$color.name|escape:'htmlall':'UTF-8'} {$color.color|escape:'htmlall':'UTF-8'}
			{if $color.texture}
			<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/texture/{$color.texture|escape:'htmlall':'UTF-8'}" target="_blank">
				<span class="ctd_order_color" style="background-image:url({$ctd_module_dir|escape:'htmlall':'UTF-8'}data/texture/{$color.texture|escape:'htmlall':'UTF-8'})"></span>
			</a>
			{else}
			<span class="ctd_order_color" style="background-color:{$color.color|escape:'htmlall':'UTF-8'}"></span>
			{/if}
			{else}
			{l s='Color Picker' mod='customtextdesign'} #{$item.clr|escape:'htmlall':'UTF-8'}
			<a class="ctd_order_color" style="background-color:#{$item.clr|escape:'htmlall':'UTF-8'}"></a>
			{/if}
		</div>
		{if (int)$item.alpha}
		<div class="ctd_sl_fix">
			<label>{l s='Transparency' mod='customtextdesign'}:</label>
			<b>{Tools::ps_round($item.alpha / 127 * 100)|escape:'htmlall':'UTF-8'} %</b>
		</div>
		{/if}
		<div class="ctd_sl_fix">
			<label>{l s='Font' mod='customtextdesign'}:</label>
			{$font = $fonts[$item.font]}
			<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/font/{$font.file|escape:'htmlall':'UTF-8'}">{$font.id|intval}-{$font.name|escape:'htmlall':'UTF-8'}</a>
		</div>
		{if (int)$item.material}
		<div class="ctd_sl_fix">
			<label>{l s='Material' mod='customtextdesign'}:</label>
			{$material = $materials[$item.material]}
			{$material.id|intval}-{$material.name|escape:'htmlall':'UTF-8'}
		</div>
		{/if}
		{if (int)$item.mirror}
		<div class="ctd_sl_fix">
			<label>{l s='Mirror' mod='customtextdesign'}:</label>
			<b>{l s='yes' mod='customtextdesign'}</b>
		</div>
		{/if}
		{if (int)$item.center}
		<div class="ctd_sl_fix">
			<label>{l s='Centered text' mod='customtextdesign'}:</label>
			<b>{l s='yes' mod='customtextdesign'}</b>
		</div>
		{/if}
		{else}
		<div class="ctd_sl_fix">
			<label>{l s='Preview' mod='customtextdesign'}:</label>
			<a href="{$item.text|escape:'htmlall':'UTF-8'}" target="_blank">
				<img height="30" src="{$item.text|escape:'htmlall':'UTF-8'}" />
			</a>
		</div>
		{if $item.clr || (int)$item.color}
		<div class="ctd_sl_fix">
			<label>{l s='Color' mod='customtextdesign'}:</label>
			{if (int)$item.color}
			{$color = $colors[$item.color]}
			{$color.id|intval}-{$color.name|escape:'htmlall':'UTF-8'} {$color.color|escape:'htmlall':'UTF-8'}
			{if $color.texture}
			<a href="{$ctd_module_dir|escape:'htmlall':'UTF-8'}data/texture/{$color.texture|escape:'htmlall':'UTF-8'}" target="_blank">
				<span class="ctd_order_color" style="background-image:url({$ctd_module_dir|escape:'htmlall':'UTF-8'}data/texture/{$color.texture|escape:'htmlall':'UTF-8'})"></span>
			</a>
			{else}
			<span class="ctd_order_color" style="background-color:{$color.color|escape:'htmlall':'UTF-8'}"></span>
			{/if}
			{else}
			{l s='Color Picker' mod='customtextdesign'} #{$item.clr|replace:'_':''}
			<a class="ctd_order_color" style="background-color:#{$item.clr|replace:'_':''}"></a>
			{/if}
		</div>
		{/if}
		{/if}
		<div class="ctd_sl_fix">
			<label>{l s='Position' mod='customtextdesign'} (cm):</label>
			x: {Tools::ps_round($item.x,1)|escape:'htmlall':'UTF-8'}, y: {Tools::ps_round($item.y, 1)|escape:'htmlall':'UTF-8'}
		</div>
		<div class="ctd_sl_fix">
			<label>{l s='Size' mod='customtextdesign'} (cm):</label>
			{l s='width' mod='customtextdesign'}: {Tools::ps_round($item.width, 1)|escape:'htmlall':'UTF-8'}, {l s='height' mod='customtextdesign'}: {Tools::ps_round($item.height, 1)|escape:'htmlall':'UTF-8'}
		</div>
		<div class="ctd_sl_fix">
			<label>{l s='Angle' mod='customtextdesign'}:</label>
			{Tools::ps_round($item.angle,2)|escape:'htmlall':'UTF-8'}
		</div>
		{if (int)$item.letterspace}
		<div class="ctd_sl_fix">
			<label>{l s='Letterspace' mod='customtextdesign'}:</label>
			<b>{$item.letterspace|escape:'htmlall':'UTF-8'}%</b>
		</div>
		{/if}
		{if (int)$item.curve}
		<div class="ctd_sl_fix">
			<label>{l s='Curve' mod='customtextdesign'}:</label>
			<b>{$item.curve|escape:'htmlall':'UTF-8'}%</b>
		</div>
		{/if}
		<div class="ctd_sl_fix">
			<label>{l s='Item Cost' mod='customtextdesign'}:</label>
			{Tools::displayPrice(Tools::convertPrice($item.price))|escape:'htmlall':'UTF-8'}
		</div>
	</div>
	{if ! $smarty.foreach.items.last}<hr class="ctd_sl_fix">{/if}
	{/foreach}
	{/if}
	<hr class="ctd_sl_fix">
	{if $is_employee}
	<a class="ctd_sl_fix" href="{$link->getAdminLink('AdminPdfOutput')|escape:'htmlall':'UTF-8'}&id_custom_product={$custom_product.id_custom_product|intval}">{l s='Download as PDF' mod='customtextdesign'}</a>
	{if $custom_product.has_mask2}
		<br><a class="ctd_sl_fix" href="{$link->getAdminLink('AdminPdfOutput')|escape:'htmlall':'UTF-8'}&id_custom_product={$custom_product.id_custom_product|intval}&sub">{l s='Download as PDF' mod='customtextdesign'} ({l s='Design only' mod='customtextdesign'})</a>
	{/if}
	{/if}
</div>
{$rand = rand()}
<script id="{$rand|escape:'htmlall':'UTF-8'}" type="text/javascript">
	$(function(){
		$('#{$rand|htmlspecialchars}').closest('td').next('td').text("{Tools::displayPrice(Tools::convertPrice($custom_product.price + $custom_product.custom_price))}");
		$('#{$rand|htmlspecialchars}').closest('tr').find('td.total_product').text("{Tools::displayPrice(Tools::convertPrice($custom_total))}");
	});
</script>
<!-- /Custom Text Design Module -->
<a>