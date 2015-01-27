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

{$width_min = 0}
{$width_max = 100}
{if isset($pageconfig.colors)}
{$explode = explode(",",$pageconfig.colors)}
{if isset($explode[0])}{$width_min = $explode[0]}{else}{$width_min=0}{/if}
{if isset($explode[1])}{$width_max = $explode[1]}{else}{$width_max=100}{/if}
{/if}

{$height_min = 0}
{$height_max = 100}
{if isset($pageconfig.fonts)}
{$explode = explode(",",$pageconfig.fonts)}
{if isset($explode[0])}{$height_min = $explode[0]}{else}{$height_min=0}{/if}
{if isset($explode[1])}{$height_max = $explode[1]}{else}{$height_max=100}{/if}
{/if}

<script type="text/javascript">
	var customtextdesign_config = {
	{foreach $customtextdesign_config key=k item=value}
	"{$k|htmlspecialchars}":"{$value|htmlspecialchars}",
	{/foreach}
	"module_dir":"{$module_dir|htmlspecialchars}"
	};

	var ctd_module_dir = "{$module_dir|htmlspecialchars}";

	var customtextdesign_price = {
	{foreach $materials item=material}
	"{$material.id|intval}":"{Tools::convertPrice($material.price)}",
	{/foreach}
	};

	var material_prices_rows = {
	{foreach $material_prices_rows item=material_prices_row}
	"{$material_prices_row.material_id[intval]}_{$material_prices_row.material_size|intval}":"{$material_prices_row.material_price|htmlspecialchars}",
	{/foreach}
	};

	var customtextdesign_category = {$customtextdesign_category|intval};
	var customtextdesign_picker = "{l s='Change background color to make preview clearer' mod='customtextdesign'}";
	var ctd_str_sure = "{l s='Are you sure you want to delete this item?' mod='customtextdesign'}";

	var customtextdesign_width_min = "{$width_min|htmlspecialchars}";
	var customtextdesign_width_max = "{$width_max|htmlspecialchars}";

	var customtextdesign_height_min = "{$height_min|htmlspecialchars}";
	var customtextdesign_height_max = "{$height_max|htmlspecialchars}";

</script>
<div id="customtextdesign">
	<div class="div-row">
		<label>{l s='Width' mod='customtextdesign'}:</label>
		<input type="text" id="width-input" size="6" value="1" onchange="noComma('width-input');this.value = Math.min(this.value,99)"> cm
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	<div class="div-row">
		<label>{l s='Height' mod='customtextdesign'}:</label>
		<input type="text" id="height-input" size="6" value="1" onchange="noComma('height-input');this.value = Math.min(this.value,99)"> cm
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	<div class="div-row">
		<label>{l s='Size' mod='customtextdesign'}:</label>
		<div class="info-div">
			<span class="equal">{l s='Width' mod='customtextdesign'}:</span><span class="equal2" id="width-span">--</span> cm<br>
			<span class="equal">{l s='Height' mod='customtextdesign'}:</span><span class="equal2" id="height-span">--</span> cm<br>
			<span class="equal">{l s='Area' mod='customtextdesign'}:</span><span class="equal2" id="area-span">--</span> m²
			{if $customtextdesign_config.num_text_lines!=1}
			<p>({l s='The size of all the text in one line' mod='customtextdesign'})</p>
			{/if}
		</div>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	<div class="div-row">
		<label>{l s='Material Type' mod='customtextdesign'}:</label>
		<select class="customtextdesign-materials">
			{if isset($pageconfig.materials)}
			{$check = explode(',',$pageconfig.materials)}
			{/if}
			{foreach $materials item=material}
			{if isset($check) && in_array($material.id,$check) || !isset($pageconfig.materials) || !$pageconfig.materials}
			{if $material.displayed}
			{$price = round(Tools::convertPrice($material.price, $currency),2)}
			<option value="{$material.id|intval}">{$material.name|escape:'htmlall':'UTF-8'}{if $customtextdesign_config.show_prices} ({$currency->prefix|escape:'htmlall':'UTF-8'}{$price|escape:'htmlall':'UTF-8'}{$currency->suffix|escape:'htmlall':'UTF-8'} {l s='per m²' mod='customtextdesign'}){/if}</option>
			{/if}
			{/if}
			{/foreach}
		</select>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	<div class="div-row">
		<label>{l s='Image' mod='customtextdesign'}:</label>
		<input type="hidden" id="ctd_uploads" />
		<form id="ctd_upload_form_uploads" action="{$module_dir|escape:'htmlall':'UTF-8'}inc/customupload.php?target=uploads" method="post" target="ctd_iframe_uploads" enctype="multipart/form-data">
			<input type="file" name="user_image">
			<div style="display:inline-block;position: relative;top: 1px;">
				<input type="submit" class="button" value="{l s='Upload' mod='customtextdesign'}">
				<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/preloader.gif" class="ctd_uploader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
				<a id="ctd_current_uploads" href="#" target="_blank">
					<img height="30" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" alt="" id="">
				</a>
				<a href="#" class="ctd_delete_uploads"></a>
			</div>
		</form>
		<iframe style="display:none" name="ctd_iframe_uploads" id="ctd_iframe_uploads"></iframe>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	<div class="div-row">
		<label>{l s='Price' mod='customtextdesign'}:</label>
		<div class="info-div">
			<span class="equal">{l s='Unit Price' mod='customtextdesign'}:</span>{$currency->prefix|escape:'htmlall':'UTF-8'}<span class="equal2" id="unitprice-span">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}<br>
			<span style="display:none" class="equal">{l s='Total Price' mod='customtextdesign'}:</span><span style="display:none" class="equal2" id="totalprice-span">--</span>
			<div class="ctd_wt" style="display:none"><span class="equal">{l s='Total Price(tax incl.)' mod='customtextdesign'}:</span>{$currency->prefix|escape:'htmlall':'UTF-8'}<span class="equal2" id="totalpricewt-span">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}<br></div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	<div class="content_prices">
		<div class="price">
			<p class="our_price_display">
				<span id="our_price_display">{$currency->prefix|escape:'htmlall':'UTF-8'}<span id="customtextdesign-ourprice">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}</span><br>
			</p>
		</div>
		<p id="add_to_cart" class="buttons_bottom_block">
			<span></span>
			<input id="addtocart" type="submit" name="Submit" value="{l s='Add To Cart' mod='customtextdesign'}" class="exclusive_disabled" disabled="disabled">
			<input id="addtocarthidden" type="hidden" name="Submit" value="{l s='Add To Cart' mod='customtextdesign'}" class="exclusive">
			<input id="product_page_product_id" type="hidden" value="0"/>
			<input id="quantity_wanted" type="hidden" value="1"/>
		</p>
	</div>
	<div class="clr"></div>
</div>
<div class="clr"></div>
<!-- /Custom Text Design Module -->
