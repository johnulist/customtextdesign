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
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Add a new font" mod='customtextdesign'}</legend>
		<label for="font_name">{l s='Font Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="font_name" type="text" name="font_name" value="">
		</div>
		<br class="clear">
		<label for="font_file">{l s='Font File' mod='customtextdesign'} (.ttf):&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="font_file" type="file" name="font_file" onchange="var f =$(this).val(); !$('#font_name').val() && $('#font_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\.ttf$/i,'').replace(/[_-]/g,' ').toProperCase())">
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitNewFont" value="{l s='Add' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{else}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Edit font" mod='customtextdesign'}</legend>
		<input type="hidden" name="id_font" value="{$font.id|intval}" />
		<input type="hidden" name="font_file_old" value="{$font.file|escape:'htmlall':'UTF-8'}" />
		<label for="font_name">{l s='Font Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="font_name" type="text" name="font_name" value="{$font.name|escape:'htmlall':'UTF-8'}">
		</div>
		<br class="clear">
		<label for="font_file">{l s='Font File' mod='customtextdesign'} (.ttf):&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="font_file" type="file" name="font_file" onchange="var f =$(this).val(); !$('#font_name').val() && $('#font_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\.ttf$/i,'').toProperCase())">
            <p>{l s='Current font file' mod='customtextdesign'}: {$font.file|escape:'htmlall':'UTF-8'}</p>
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitEditFont" value="{l s='Save' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{/if}