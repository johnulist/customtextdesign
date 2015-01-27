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

{if $new}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Add a new image" mod='customtextdesign'}</legend>
		<label for="image_name">{l s='Image Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="image_name" type="text" name="image_name" value="">
		</div>
		<br class="clear">
		<label for="image_file">{l s='Image File' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="image_file" type="file" name="image_file" onchange="var f =$(this).val(); !$('#image_name').val() && $('#image_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\.(.*)$/i,'').replace(/[_-]/g,' ').toProperCase())">
		</div>
		<br class="clear">
		<label for="image_price">{l s='Image Price' mod='customtextdesign'} {l s='(per m²)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="image_price" type="text" name="image_price" value="0.00" onchange="noComma('image_price');">{$currency->suffix|escape:'htmlall':'UTF-8'}
		</div>
		<br class="clear">
		<label for="image_quantity">{l s='Image Quantity' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="image_quantity" type="text" name="image_quantity" value="-1">
			<p>{l s='"-1": unlimited.' mod='customtextdesign'}</p>
		</div>
		<br class="clear">
		<label for="image_group">{l s='Image Group' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{foreach from=$groups item=group}
				<input type="radio" name="image_group" value="{$group.id|intval}">{$group.name|escape:'htmlall':'UTF-8'}<br>
			{/foreach}
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitNewimage" value="{l s='Add' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{else}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Edit image" mod='customtextdesign'}</legend>
		<input type="hidden" name="id_image" value="{$image.id|intval}" />
		<input type="hidden" name="image_file_old" value="{$image.file|escape:'htmlall':'UTF-8'}" />
		<label for="image_name">{l s='Image Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="image_name" type="text" name="image_name" value="{$image.name|escape:'htmlall':'UTF-8'}">
		</div>
		<br class="clear">
		<label for="image_file">{l s='Image File' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="image_file" type="file" name="image_file" onchange="var f =$(this).val(); !$('#image_name').val() && $('#image_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\.(.*)$/i,'').toProperCase())">
            <p>{l s='Current image file' mod='customtextdesign'}: {$image.file|escape:'htmlall':'UTF-8'}</p>
		</div>
		<br class="clear">
		<label for="image_price">{l s='Image Price' mod='customtextdesign'} {l s='(per m²)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="image_price" type="text" name="image_price" value="{Tools::ps_round($image.price,2)}" onchange="noComma('image_price');">{$currency->suffix|escape:'htmlall':'UTF-8'}
		</div>
		<br class="clear">
		<label for="image_quantity">{l s='Image Quantity' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="image_quantity" type="text" name="image_quantity" value="{$image.quantity|intVal}">
			<p>{l s='"-1": unlimited.' mod='customtextdesign'}</p>
		</div>
		<br class="clear">
		<label for="image_group">{l s='Image Group' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<!--<select name="image_group">-->
			{foreach from=$groups item=group}
				<!--<option {if $image.id_group == $group.id}selected="selected"{/if} value="{$group.id|intval}">{$group.name|escape:'htmlall':'UTF-8'}</option>-->
				<input {if $image.id_group == $group.id}checked="checked"{/if} type="radio" name="image_group" value="{$group.id|intval}">{$group.name|escape:'htmlall':'UTF-8'}<br>
			{/foreach}
			<!--</select>-->
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitEditimage" value="{l s='Save' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{/if}