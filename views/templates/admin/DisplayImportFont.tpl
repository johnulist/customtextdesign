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
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Import Fonts" mod='customtextdesign'}</legend>
		<p>
			{l s='Place your .ttf font files in' mod='customtextdesign'} <b>modules/customtextdesign/data/font/</b> {l s='and press Import' mod='customtextdesign'}.
			<br>
			{l s='The font name will follow the name of the font file' mod='customtextdesign'}.
		</p>
		<br class="clear">
		<label for="font_files">{l s='Font Files' mod='customtextdesign'}</label>
		<div class="margin-form">
			<a href='#' class="ctd_select_link" id='ctd-select-all'>Select all</a>
			&nbsp;-&nbsp;
			<a href='#' class="ctd_select_link" id='ctd-deselect-all'>Deselect all</a>
			<select id="font_files" name="font_files[]" multiple="multiple" class="ctd_multiselect">
				{foreach from=$items key=filename item=path}
				<option title="{$path|escape:'htmlall':'UTF-8'}" value="{$path|escape:'htmlall':'UTF-8'}">{$filename|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitImportFontFiles" value="{l s='Import Fonts' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>