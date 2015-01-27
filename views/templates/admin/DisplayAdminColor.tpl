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
		<legend><img width="16" height="16" src="../img/admin/color.png">{l s="Add a new color" mod='customtextdesign'}</legend>
		<label for="color_name">{l s='Color Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="color_name" type="text" name="color_name" value="">
		</div>
		<br class="clear">
		<label for="color_code">{l s='Color Code' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="text" size="33" data-hex="true" class="color_trans color mColorPickerInput mColorPicker" name="color_code" value="#ABCDEF" id="color_code" style="background-color: rgb(210, 214, 213); color: black;">
		</div>
		<br class="clear">
		<label for="color_code">{l s='Color Transparency' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form" style="max-width:300px">
			<div>
				<span style="display:block;float:left">{l s='Opaque' mod='customtextdesign'}</span>
				<span style="display:block;float:right">{l s='transparent' mod='customtextdesign'}</span>
				<br class="clear">
			</div>
			<div class="slider_color_alpha"></div>
			<input type="hidden" name="color_alpha" id="color_alpha" value="0" />
			<span style="display:block;margin-top:5px" id="label_color_alpha">0/127 (0%)</span>
		</div>
		<br class="clear">
		<label for="color_file">{l s='Texture' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="color_file" type="file" name="color_file" onchange="var f =$(this).val(); !$('#color_name').val() && $('#color_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\..*$/i,''))">
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitNewColor" value="{l s='Add' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{else}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/color.png">{l s="Edit color" mod='customtextdesign'}</legend>
		<input type="hidden" name="id_color" value="{$color.id|intval}" />
		<input type="hidden" name="color_texture" value="{$color.texture|htmlspecialchars}" />
		<label for="color_name">{l s='Color Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="color_name" type="text" name="color_name" value="{$color.name|htmlspecialchars}">
		</div>
		<br class="clear">
		<label for="color_code">{l s='Color Code' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="text" size="33" data-hex="true" class="color_trans color mColorPickerInput mColorPicker" name="color_code" value="{$color.color|htmlspecialchars}" id="color_code" style="background-color: {$color.color|escape:'htmlall':'UTF-8'}; color: white;">
		</div>
		<br class="clear">
		<label for="color_code">{l s='Color Transparency' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form" style="max-width:300px">
			<div>
				<span style="display:block;float:left">{l s='Opaque' mod='customtextdesign'}</span>
				<span style="display:block;float:right">{l s='transparent' mod='customtextdesign'}</span>
				<br class="clear">
			</div>
			<div class="slider_color_alpha"></div>
			<input type="hidden" name="color_alpha" id="color_alpha" value="{$color.alpha|escape:'htmlall':'UTF-8'}" />
			<span style="display:block;margin-top:5px" id="label_color_alpha">0/127 (0%)</span>
		</div>
		<br class="clear">
		<label for="color_file">{l s='Texture' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="color_file" type="file" name="color_file">
			{if !$color.is_color}
				<p><input type="checkbox" name="delete_texture" /> {l s='Remove Texture' mod='customtextdesign'}({$color.texture|escape:'htmlall':'UTF-8'})
			{/if}
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitEditColor" value="{l s='Edit' mod='customtextdesign' js=1}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign' js=1}">
		</div>
		<br class="clear">
	</fieldset>
</form>
<script type="text/javascript">
	$(function(){
		$('#color_code').val('{$color.color|htmlspecialchars}').trigger('change');
	})
</script>
{/if}