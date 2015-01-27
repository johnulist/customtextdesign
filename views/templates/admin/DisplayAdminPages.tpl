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

<form action="" method="post" class="ctd_fix_form">
	<input type="hidden" name="submitUpdateModuleConfigPages">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/cms.gif">{l s="Configure module pages" mod='customtextdesign'}</legend>
		<label>&nbsp;</label>
		<div class="margin-form">
			<a href="#" class="ctd_chk_btn ctd_chk_all_total">{l s='Check All' mod='customtextdesign'}</a> - <a href="#" class="ctd_chk_btn ctd_chk_none_total">{l s='Uncheck All' mod='customtextdesign'}</a><br>
		</div>
		<br class="clear">
		<hr>
		{foreach from=$pages key=pagename item=pageconfig}
		<label id="{$pagename|escape:'htmlall':'UTF-8'}">{l s='Page Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{$pagename|escape:'htmlall':'UTF-8'}
			{$design=(strpos($pagename,'design')===0)}
		</div>

		<br class="clear">
		{if $design}
		<label>{l s='Available Colors' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form ctd_chkgroup_container">
			<a href="#" class="ctd_chk_btn ctd_chk_all">{l s='All' mod='customtextdesign'}</a> - <a href="#" class="ctd_chk_btn ctd_chk_none">{l s='None' mod='customtextdesign'}</a><br>
			{if isset($pageconfig.colors)}
				{$check = explode(',',$pageconfig.colors)}
			{/if}
			{foreach from=$colors item=color}
			<input type="checkbox" {if isset($check) && in_array($color.id,$check)}checked="checked"{/if} name="colors_{$pagename|escape:'htmlall':'UTF-8'}[{$color.id|intval}]">{$color.name|escape:'htmlall':'UTF-8'}<br>
			{/foreach}
			<input style="display:none" type="checkbox" checked="checked" name="colors_{$pagename|escape:'htmlall':'UTF-8'}[0]">
		</div>
		{else}
		{$min = 0}
		{$max = 100}
		{if isset($pageconfig.colors)}
			{$explode = explode(",",$pageconfig.colors)}
			{if isset($explode[0])}{$min = $explode[0]}{else}{$min=0}{/if}
			{if isset($explode[1])}{$max = $explode[1]}{else}{$max=100}{/if}
		{/if}
		<!--<label>{l s='Width' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form ctd_chkgroup_container">
			min: <input type="text" name="colors_min_{$pagename|escape:'htmlall':'UTF-8'}" value="{$min|intval}"/>&nbsp;
			max: <input type="text" name="colors_max_{$pagename|escape:'htmlall':'UTF-8'}" value="{$max|intval}"/>
		</div>-->
		{/if}

		{if $design}
		<br class="clear">
		<label>{l s='Available Fonts' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form ctd_chkgroup_container">
			<a href="#" class="ctd_chk_btn ctd_chk_all">{l s='All' mod='customtextdesign'}</a> - <a href="#" class="ctd_chk_btn ctd_chk_none">{l s='None' mod='customtextdesign'}</a><br>
			{if isset($pageconfig.fonts)}
				{$check = explode(',',$pageconfig.fonts)}
			{/if}
			{foreach from=$fonts item=font}
			<input type="checkbox" {if isset($check) && in_array($font.id,$check)}checked="checked"{/if} name="fonts_{$pagename|escape:'htmlall':'UTF-8'}[{$font.id|intval}]">{$font.name|escape:'htmlall':'UTF-8'}<br>
			{/foreach}
			<input style="display:none" type="checkbox" checked="checked" name="fonts_{$pagename|escape:'htmlall':'UTF-8'}[0]">
		</div>
		{else}
		{$min = 0}
		{$max = 100}
		{if isset($pageconfig.fonts)}
			{$explode = explode(",",$pageconfig.fonts)}
			{if isset($explode[0])}{$min = $explode[0]}{else}{$min=0}{/if}
			{if isset($explode[1])}{$max = $explode[1]}{else}{$max=100}{/if}
		{/if}
		<!--<label>{l s='Height' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form ctd_chkgroup_container">
			min: <input type="text" name="fonts_min_{$pagename|escape:'htmlall':'UTF-8'}" value="{$min|intval}"/>&nbsp;
			max: <input type="text" name="fonts_max_{$pagename|escape:'htmlall':'UTF-8'}" value="{$max|intval}"/>
		</div>-->
		{/if}

		<br class="clear">
		<label>{l s='Available Materials' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form ctd_chkgroup_container">
			<a href="#" class="ctd_chk_btn ctd_chk_all">{l s='All' mod='customtextdesign'}</a> - <a href="#" class="ctd_chk_btn ctd_chk_none">{l s='None' mod='customtextdesign'}</a><br>
			{if isset($pageconfig.materials)}
				{$check = explode(',',$pageconfig.materials)}
			{/if}
			{foreach from=$materials item=material}
			<input type="checkbox" {if isset($check) && in_array($material.id,$check)}checked="checked"{/if} name="materials_{$pagename|escape:'htmlall':'UTF-8'}[{$material.id|intval}]">{$material.name|escape:'htmlall':'UTF-8'}<br>
			{/foreach}
			<input style="display:none" type="checkbox" checked="checked" name="materials_{$pagename|escape:'htmlall':'UTF-8'}[0]">
		</div>
		<br class="clear">
		<label for="page_name">{l s='Page Link' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input style="width:300px" onclick="this.select()" type="text" value="{$link->getModuleLink($module_name,$pagename)|htmlspecialchars}"> {l s='or' mod='customtextdesign'}
			<input style="width:300px" onclick="this.select()" type="text" value="{literal}{$link->getModuleLink({/literal}'{$module_name|escape:'htmlall':'UTF-8'}', '{$pagename|escape:'htmlall':'UTF-8'}'{literal})}{/literal}">
			<br>
			<a href="{$link->getModuleLink($module_name,$pagename)|htmlspecialchars}" target="_blank">{l s='Open' mod='customtextdesign'}</a>
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitUpdateModulePages{$pagename|escape:'htmlall':'UTF-8'}" value="{l s='Save' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Return' mod='customtextdesign'}">
		</div>
		<br class="clear">
		<hr>
		{/foreach}
	</fieldset>
</form>
<script type="text/javascript">
	var urlhash = "{$pagehash|htmlspecialchars}";
</script>
{literal}
<script type="text/javascript">



	$(function(){
		$('.ctd_chk_all').on('click',function(){
			$(this).closest('.ctd_chkgroup_container').find('[type="checkbox"]').prop('checked',1).eq(0).trigger('change');
			return false;
		});
		$('.ctd_chk_none').on('click',function(){
			$(this).closest('.ctd_chkgroup_container').find('[type="checkbox"]').prop('checked',0).eq(0).trigger('change');
			return false;
		});

		$('.ctd_chk_all_total').on('click',function(){
			$('.ctd_chkgroup_container').each(function(){
				$(this).find('[type="checkbox"]').prop('checked',1).eq(0).trigger('change');
			});
			return false;
		});
		$('.ctd_chk_none_total').on('click',function(){
			$('.ctd_chkgroup_container').each(function(){
				$(this).find('[type="checkbox"]').prop('checked',0).eq(0).trigger('change');
			})
			return false;
		});

		if (urlhash.length && $('#'+urlhash).length){
			$('html, body').stop().animate({
			scrollTop: $('#'+urlhash).offset().top - 100
			}, 0);
		}
	});


</script>
{/literal}
