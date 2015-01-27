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
<script type="text/javascript">
	var customtextdesign_config = {
	{foreach $customtextdesign_config key=k item=value}
	"{$k|htmlspecialchars}":"{$value|htmlspecialchars}",
	{/foreach}
	"module_dir":"{$module_dir|htmlspecialchars}"
	};

	var customtextdesign_price = {
		{foreach $materials item=material}
		"{$material.id|intval}":"{Tools::convertPrice($material.price)|htmlspecialchars}",
		{/foreach}
	};

	var material_prices_rows = {
		{foreach $material_prices_rows item=material_prices_row}
			"{$material_prices_row.material_id|intval}_{$material_prices_row.material_size|intval}":"{$material_prices_row.material_price|floatval}",
		{/foreach}
	};

	var customtextdesign_category = {$customtextdesign_category|intval};
	var customtextdesign_picker = "{l s='Change background color to make preview clearer' mod='customtextdesign'}";

	var ctd_tax1 = "{l s='Tax incl.' mod='customtextdesign'}";
	var ctd_tax0 = "{l s='Tax excl.' mod='customtextdesign'}";

	var id_page_config = {$id_page_config|intVal};

</script>
<div id="customtextdesign">
	<form method="post">
		<div class="div-row">
			<label>{l s='Text' mod='customtextdesign'}:</label>
			<div class="div-control">
				{if $customtextdesign_config.num_text_lines==1}
				<input type="text" {if $customtextdesign_config.num_text_length}maxlength="{$customtextdesign_config.num_text_length}"{/if} value="{$customtextdesign_config["text_init_{$default_lang}"]|htmlspecialchars}" class="customtextdesign-text round" id="customtextdesign-text">
				{else}
				<textarea rows="{(int)$customtextdesign_config.num_text_lines}" {if $customtextdesign_config.num_text_length}maxlength="{$customtextdesign_config.num_text_length}"{/if} class="customtextdesign-text round" id="customtextdesign-text">{$customtextdesign_config["text_init_{$default_lang}"]|htmlspecialchars}</textarea>
				<div class="clearBoth"></div>
				<a id="preview-button" href="#" class="button">{l s='Preview' mod='customtextdesign'}</a>
				<p>&nbsp;</p>
				{/if}
			</div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row">
			<label>{l s='Color' mod='customtextdesign'}:</label>
			<!--<div class="clr"></div>-->
			<div class="div-control">
				<ul class="customtextdesign-colors">
					{if isset($pageconfig.colors)}
						{$check = explode(',',$pageconfig.colors)}
					{/if}
					{foreach $colors item=color}
					{if isset($check) && in_array($color.id,$check) || !isset($pageconfig.colors) || !$pageconfig.colors}
					{if $color.displayed}
					{if $color.is_color}
					<li title="{$color.name|escape:'htmlall':'UTF-8'}{if $color.alpha} ({Tools::ps_round($color.alpha/127*100)|escape:'htmlall':'UTF-8'}% {l s='Transparency' mod='customtextdesign'}){/if}"><a data-color="{$color.id|intval}" data-iscolor="1" href="#" style="background-color:{$color.color}"></a></li>
					{else}
					<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="0" href="#" style="background-image:url('{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($color.texture, 32, 32,'texture')}')"></a></li>
					{/if}
					{/if}
					{/if}
					{/foreach}
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
		<div class="div-row" style="min-height: 70px;">
			<label>{l s='Fonts' mod='customtextdesign'}:</label>
			<div style="float: left; max-width: 100%;">
				<select class="customtextdesign-fonts">
					{if isset($pageconfig.fonts)}
						{$check = explode(',',$pageconfig.fonts)}
					{/if}
					{foreach $fonts item=font}
					{if isset($check) && in_array($font.id,$check) || !isset($pageconfig.fonts) || !$pageconfig.fonts}
					{if $font.displayed}
					<option data-font="{$font.id|intval}" data-imagesrc="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::render($font.name, $font.id, $customtextdesign_config.font_color)}" data-description="{$font.id|intval}" value="{$font.file|htmlspecialchars}"></option>
					{/if}
					{/if}
					{/foreach}
				</select>
			</div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row">
			<label>{l s='Height' mod='customtextdesign'}:</label>
			<div id="slider-div" class="div-control">
				<div id="slider"></div>
				<div id="slider-info">
					<span id="slider-value"></span> cm
				</div>
			</div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row" {if $customtextdesign_config.mirror_show!='1'}style="display:none"{/if}>
			<label>{l s='Mirror' mod='customtextdesign'}:</label>
			<div id="mirror-div" class="div-control">
				<input id="mirror-effect" type="checkbox" title="{l s='Mirror Effect' mod='customtextdesign'}">
			</div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row">
			<label>{l s='Preview' mod='customtextdesign'}:</label>
			<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/loader.gif" class="img-loader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
			<div class="clr"></div>
			<div id="preview-div" class="div-control">
				<img id="bigpic" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
			</div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row">
			<label>{l s='Size' mod='customtextdesign'}:</label>
			<div class="info-div div-control">
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
			<select class="customtextdesign-materials div-control">
				{if isset($pageconfig.materials)}
					{$check = explode(',',$pageconfig.materials)}
				{/if}
				{foreach $materials item=material}
				{if isset($check) && in_array($material.id,$check) || !isset($pageconfig.materials) || !$pageconfig.materials}
				{if $material.displayed}
				{$price = round(Tools::convertPrice($material.price, $currency),2)}
				<option value="{$material.id|intval}">{$material.name|escape:'htmlall':'UTF-8'}{if $customtextdesign_config.show_prices} ({$currency->prefix|escape:'htmlall':'UTF-8'}{$price}{$currency->suffix|escape:'htmlall':'UTF-8'} {l s='per m²' mod='customtextdesign'}){/if}</option>
				{/if}
				{/if}
				{/foreach}
			</select>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row">
			<label>{l s='Quantity' mod='customtextdesign'}:</label>
			<div id="spinner-div div-control">
				<input id="spinner" value="1"/>
			</div>
			<div class="clr"></div>
		</div>
		<div class="clr"></div>
		<div class="div-row">
			<label>{l s='Price' mod='customtextdesign'}:</label>
			<div class="info-div div-control">
				<div id="div_base_price" style="display: none">
				<span class="equal">{l s='Base Price' mod='customtextdesign'}:</span>{$currency->prefix|escape:'htmlall':'UTF-8'}<span class="equal2" id="baseprice-span">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}&nbsp;<span style="width: 100px;" class="equal2 ctd_tax_span"></span><br>
				</div>
				<span class="equal">{l s='Unit Price' mod='customtextdesign'}:</span>{$currency->prefix|escape:'htmlall':'UTF-8'}<span class="equal2" id="unitprice-span">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}&nbsp;<span style="width: 100px;" class="equal2 ctd_tax_span"></span><br>
				<span class="equal">{l s='Total Price' mod='customtextdesign'}:</span>{$currency->prefix|escape:'htmlall':'UTF-8'}<span class="equal2" id="totalprice-span">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}&nbsp;<span style="width: 100px;" class="equal2 ctd_tax_span"></span><br>
				<div class="ctd_wt" style="display:none"><span class="equal">{l s='Total Price' mod='customtextdesign'}:</span>{$currency->prefix|escape:'htmlall':'UTF-8'}<span class="equal2" id="totalpricewt-span">--</span>{$currency->suffix|escape:'htmlall':'UTF-8'}&nbsp;<span style="width: 100px;" class="equal2">{l s='Tax incl.' mod='customtextdesign'}</span></div><br>
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
	</form>
</div>
<div class="clr"></div>
<!-- extra translations -->
<div style="display: none">
	{l s='Text' mod='customtextdesign'}
	{l s='Color' mod='customtextdesign'}
	{l s='Texture' mod='customtextdesign'}
	{l s='Font' mod='customtextdesign'}
	{l s='Material' mod='customtextdesign'}
	{l s='Mirror' mod='customtextdesign'}
	{l s='yes' mod='customtextdesign'}
	{l s='Width' mod='customtextdesign'}
	{l s='Height' mod='customtextdesign'}
	{l s='Area' mod='customtextdesign'}
</div>
<!-- /Custom Text Design Module -->
