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

<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Import Images" mod='customtextdesign'}</legend>
		<p>
			{l s='Place your images in' mod='customtextdesign'} <b>modules/customtextdesign/data/image/</b> {l s='and press Import' mod='customtextdesign'}.
			<br>
			{l s='The image name will follow the name of the image file' mod='customtextdesign'}.
		</p>
		<br class="clear">
		<label for="image_files">{l s='Image Files' mod='customtextdesign'}</label>
		<div class="margin-form">
			<div class="ctd_image_row" style="background: #81E7FF;">
				<span style="margin-left: 32px">{l s='All' mod='customtextdesign'}</span>
				<label>{l s='Image Price' mod='customtextdesign'} {l s='(per m²)' mod='customtextdesign'}:</label>
				{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="all-image_price" type="text" value="0.00" onchange="noComma('all-image_price');applyToAll(this, 'image_price');">{$currency->suffix|escape:'htmlall':'UTF-8'}
				&nbsp; - &nbsp;
				<label>{l s='Image Group' mod='customtextdesign'}:</label>
				<select onchange="applyToAll(this, 'image_group');">
				{foreach from=$groups item=group}
					<option value="{$group.id|intval}">{$group.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				</select>
				&nbsp; - &nbsp;
				<label>{l s='Image Quantity' mod='customtextdesign'}:</label>
				<input id="all-image_quantity" title="{l s='"-1": unlimited.' mod='customtextdesign'}" type="text" value="-1" onchange="applyToAll(this, 'image_quantity');">
			</div>
			{foreach from=$items key=filename item=path}
			<input type="hidden" name="image_files[]" value="{$path|escape:'htmlall':'UTF-8'}">
			<div class="ctd_image_row">
				<img src="{$module_dir|escape:'htmlall':'UTF-8'}data/image/{$path|escape:'htmlall':'UTF-8'}" alt="" width="25">
				<span>{$filename|truncate:25:"...":true}</span>
				<label>{l s='Image Price' mod='customtextdesign'} {l s='(per m²)' mod='customtextdesign'}:</label>
				{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="image_price-{$path@iteration|intval}" type="text" name="image_price[{$path|escape:'htmlall':'UTF-8'}]" value="0.00" onchange="noComma('image_price-{$path@iteration|intval}');">{$currency->suffix|escape:'htmlall':'UTF-8'}
				&nbsp; - &nbsp;
				<label>{l s='Image Group' mod='customtextdesign'}:</label>
				<select id="image_group-{$path|escape:'htmlall':'UTF-8'}" name="image_group[{$path|escape:'htmlall':'UTF-8'}]">
				{foreach from=$groups item=group}
					<option value="{$group.id|intval}">{$group.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
				</select>
				&nbsp; - &nbsp;
				<label>{l s='Image Quantity' mod='customtextdesign'}:</label>
				<input id="image_quantity-{$path@iteration|intval}" title="{l s='"-1": unlimited.' mod='customtextdesign'}" type="text" name="image_quantity[{$path|escape:'htmlall':'UTF-8'}]" value="-1" >
			</div>
			{/foreach}
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitImportImageFiles" value="{l s='Import Images' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>