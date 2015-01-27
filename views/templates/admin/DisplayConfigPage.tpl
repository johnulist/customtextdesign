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

<script type="text/javascript">
	var secure_key = "{$secure_key|htmlspecialchars}";
	var module_dir = "{$module_dir|htmlspecialchars}";
	var languages = [
	{foreach from=$languages key=k item=lang}
	{
	id_lang: {$lang.id_lang|intval},
	is_default: "0{$lang.id_lang==$default_lang|intval}",
	iso_code: "{$lang.iso_code|htmlspecialchars}",
	name: "{$lang.name|htmlspecialchars}"
	},
	{/foreach}
	];
	var id_language = {$default_lang|intval};
	var defaultLanguage = {
	{foreach from=$languages key=k item=lang}
	{if $lang.id_lang==$default_lang}
	id_lang: {$lang.id_lang|intval},
	iso_code: "{$lang.iso_code|htmlspecialchars}",
	name: "{$lang.name|htmlspecialchars}"
	{/if}
	{/foreach}
	};
	$(function(){
		displayFlags(languages,id_language,0);
	});
	var num_size_min = {$num_size_min|intval};
	var num_size_max = {$num_size_max|intval};
	var token = '{$token|htmlspecialchars}';
</script>
<form action="" id="config" method="post">
	<fieldset style="">
		<legend><img width="16" height="16" src="../img/admin/prefs.gif">{l s="Configuration" mod='customtextdesign'} {l s="of" mod='customtextdesign'} "{$pagename|escape:'htmlall':'UTF-8'}"</legend>
		<input type="hidden" name="id_page_config" value="{$module_page.id_page_config|intVal}">
		{if count($module_pages)}
		<label></label>
		<div class="margin-form">
			<select style="vertical-align: middle;width: 200px;" id="ctd_page_list">
				{foreach from=$module_pages item=amodule_page}
					<option {if $amodule_page.pagename == $pagename}selected="selected"{/if} value="{$amodule_page.pagename|intVal}">{$amodule_page.pagename|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
			<a class="button" id="ctd_page_list_link" href="{$req|escape:'htmlall':'UTF-8'}&submitConfigurePage&pagename=">{l s="Configure Page" mod='customtextdesign'}</a>
			<p>{l s='Select another page to configure' mod='customtextdesign'}</p>
		</div>
		<div class="clear"></div>
		{/if}

		<label id="page_accessible" for="page_accessible">{l s='Visible' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="radio" name="page_accessible" id="page_accessible_on"  value="1" {if $page_accessible=='1'}checked="checked"{/if}>
			<label class="t" for="page_accessible_on"> <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled"></label>
			<input type="radio" name="page_accessible" id="page_accessible_off" value="0" {if !$page_accessible OR $page_accessible=='0'}checked="checked"{/if}>
			<label class="t" for="page_accessible_off"> <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled"></label>
			<p>{l s='controls whether the module page is accessible.' mod='customtextdesign'}</p>
			<p><a href="{$link->getModuleLink('customtextdesign', $pagename)}" target="{$pagename|htmlspecialchars}">{l s='View page' mod='customtextdesign'}</a></p>
		</div>
		<br class="clear">

		<div class="translatable" style="position:relative">
			<label for="text_init">{l s='Initial Text' mod='customtextdesign'}:&nbsp;&nbsp;</label>
			<div class="margin-form">
				{foreach from=$languages key=k item=lang}
				<div class="lang_{$lang.id_lang|intval}" style="{if $lang.id_lang!=$default_lang}display:none;{/if} float: left;">
					<input type="text" id="text_init_{$lang.id_lang|intval}" name="text_init_{$lang.id_lang|intval}" value="{$text_init_{$lang.id_lang}|htmlspecialchars}">
				</div>
				{/foreach}
				<div class="language_flags" style="top: 1px;margin-left: 420px;position: absolute;">
					{l s='Choose language' mod='customtextdesign'}:
					<br><br>
					{foreach from=$languages key=k item=lang}
					<img class="pointer" style="margin: 0px 2px;" src="../img/l/{$lang.id_lang|intval}.jpg" alt="{$lang.name|escape:'htmlall':'UTF-8'}">
					{/foreach}
				</div>
			</div>
		</div>

		<div class="clear"></div>
		<label for="num_text_length">{l s='Maximum Text Length' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="num_text_length" type="text" name="num_text_length" value="{$num_text_length|htmlspecialchars}">
			<p>{l s='0: unlimited' mod='customtextdesign'}</p>
		</div>
		<div class="clear"></div>
		<label for="num_text_length">{l s='Text Lines' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="num_text_lines" type="text" name="num_text_lines" value="{$num_text_lines|htmlspecialchars}">
			<p></p>
		</div>
		<label for="ignore_space" style="padding-top: 12px;">{l s='Ignore Spaces' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form" style="border-top: 1px solid #F90;border-bottom: 1px solid #F90;background-color: #FFEBCC;padding-top: 12px;">
			<input type="radio" name="ignore_space" id="ignore_space_on"  value="1" {if $ignore_space=='1'}checked="checked"{/if}>
			<label class="t" for="ignore_space_on"> <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled"></label>
			<input type="radio" name="ignore_space" id="ignore_space_off" value="0" {if !$ignore_space OR $ignore_space=='0'}checked="checked"{/if}>
			<label class="t" for="ignore_space_off"> <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled"></label>
			<p>{l s='If checked, spaces will not be included in width calculation' mod='customtextdesign'}</p>
		</div>
		<br class="clear">
		<br class="clear">

		<div class="clear"></div>
		<label for="font_color">{l s='Font List Color' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="color" onchange="showPreview(this)" size="33" data-color="{$font_color|escape:'htmlall':'UTF-8'}" data-hex="true" class="color mColorPickerInput mColorPicker" name="font_color" value="{$font_color|htmlspecialchars}" id="color_code" style="background-color: {$font_color|escape:'htmlall':'UTF-8'}; color: black;">
			<div><img id="color_code_preview" src="{$module_dir|escape:'htmlall':'UTF-8'}inc/preview.php?font={$fonts[0].id}&size=4&type=img&text={$fonts[0].name}&clr={$font_color|replace:"#":""|escape:'htmlall':'UTF-8'}" /></div>
		</div>
		<br class="clear">

		<label for="num_size_init">{l s='Initial Letter Height (cm)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="num_size_init" type="text" name="num_size_init" value="{$num_size_init|htmlspecialchars}">
		</div>
		<br class="clear">

		<label for="num_size_min">{l s='Minimum Letter Height (cm)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="num_size_min" type="text" name="num_size_min" value="{$num_size_min|htmlspecialchars}">
		</div>
		<br class="clear">

		<label for="num_size_max">{l s='Maximum Letter Height (cm)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="num_size_max" type="text" name="num_size_max" value="{$num_size_max|htmlspecialchars}">
		</div>
		<br class="clear">

		<label for="mirror_show">{l s='Show Mirror Option' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="radio" name="mirror_show" id="mirror_show_on"  value="1" {if $mirror_show=='1'}checked="checked"{/if}>
			<label class="t" for="mirror_show_on"> <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled"></label>
			<input type="radio" name="mirror_show" id="mirror_show_off" value="0" {if !$mirror_show OR $mirror_show=='0'}checked="checked"{/if}>
			<label class="t" for="mirror_show_off"> <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled"></label>
		</div>
		<br class="clear">

		<label for="login_required">{l s='Require Login' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="radio" name="login_required" id="login_required_on"  value="1" {if $login_required=='1'}checked="checked"{/if}>
			<label class="t" for="login_required_on"> <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled"></label>
			<input type="radio" name="login_required" id="login_required_off" value="0" {if !$login_required OR $login_required=='0'}checked="checked"{/if}>
			<label class="t" for="login_required_off"> <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled"></label>
		</div>
		<br class="clear">

		<label for="show_prices">{l s='Show material prices' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="radio" name="show_prices" id="show_prices_on"  value="1" {if $show_prices=='1'}checked="checked"{/if}>
			<label class="t" for="show_prices_on"> <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled"></label>
			<input type="radio" name="show_prices" id="show_prices_off" value="0" {if !$show_prices OR $show_prices=='0'}checked="checked"{/if}>
			<label class="t" for="show_prices_off"> <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled"></label>
		</div>
		<br class="clear">

		<label for="base_price">{l s='Base price (Tax excl.)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="base_price" type="text" name="base_price" value="{$base_price|htmlspecialchars}" onchange="noComma('base_price');">{$currency->suffix|escape:'htmlall':'UTF-8'}
		</div>
		<br class="clear">

		<label for="used_tax">{l s='Tax rule' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<select name="used_tax" id="used_tax" {if $tax_exclude_taxe_option}disabled="disabled"{/if} >
				<option value="0">{l s='No Tax' mod='customtextdesign'}</option>
				{foreach from=$tax_rules_groups item=tax_rules_group}
					<option value="{$tax_rules_group.id_tax_rules_group|intval}" {if $used_tax == $tax_rules_group.id_tax_rules_group}selected="selected"{/if} >
						{$tax_rules_group['name']|escape:'htmlall':'UTF-8'}
					</option>
				{/foreach}
			</select>
		</div>
		<br class="clear">

		<label for="a_carriers">{l s='Available Carriers' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
				{$a_carriers = explode(',', $a_carriers)}
				{foreach from=$carriers item=carrier}
					<input type="checkbox" name="a_carriers[{$carrier.id_carrier|intVal}]" {if in_array($carrier.id_carrier, $a_carriers)}checked="checked"{/if}> {$carrier.name|escape:'htmlall':'UTF-8'}<br>
				{/foreach}
		</div>
		<br class="clear">

		<div style="display: none">
		<label for="login_required">{l s='Image type' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="hidden" name="image_type" value="thickbox_default">
		</div>
		<br class="clear">
		</div>
		<br class="clear">

		<div class="margin-form">
			<input class="button" type="submit" name="submitUpdatePageConfig" value="{l s='Save Settings' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Return' mod='customtextdesign'}">
		</div>
		<br class="clear">

	</fieldset>
</form>
<style type="text/css">
	.displayed_flag{
	position: relative;
	top: -10px;
	}
	.language_flags{

	}
</style>