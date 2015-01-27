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
	var ctd_link = '{$link->getAdminLink('AdminAjaxModule')|strip_tags}';
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
{if !$page_accessible OR $page_accessible=='0'}
<div class="leadin bootstrap">
	<div class="alert alert-warning warn">
		<span style="float:right">
			<a id="hideWarn" href=""><img alt="X" src="../img/admin/close.png"></a>
		</span>
		<p>
			{l s="The module page is not currently visible." mod='customtextdesign'}
			<a href="#page_accessible">{l s="click here to make it accessible" mod='customtextdesign'}</a>
		</p>
	</div>
</div>
{/if}
{if count($errors)}
<div class="leadin bootstrap">
	<div class="alert alert-danger error" id="submit-errors">
		<span style="float:right">
			<a id="hideError" href=""><img alt="X" src="../img/admin/close.png"></a>
		</span>
		{foreach from=$errors item=error}
		<p>
			{$error|escape:'htmlall':'UTF-8'}
		</p>
		{/foreach}
	</div>
	<script type="text/javascript">
		location.hash = "#submit-errors";
	</script>
</div>
{/if}
<form action="{$req|escape:'htmlall':'UTF-8'}&submitGlobalConfig" method="get" id="global_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/manufacturers.gif">{l s="Default Configuration" mod='customtextdesign'}</legend>
		<p>
			{l s='For a faster configuration, click the button to select the default settings for all your products' mod='customtextdesign'}.<br>
			{l s='These settings will be loaded by default in the product customization tab' mod='customtextdesign'}.<br>
		</p>
		<a href="{$req|escape:'htmlall':'UTF-8'}&submitGlobalConfig" class="button" name="submitGlobalConfig" id="submitGlobalConfig">{l s='Default configuration' mod='customtextdesign'}</a>
	</fieldset>
</form>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}&submitEmptyCache" method="post" id="cache_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/copy_files.gif">{l s="Cache control" mod='customtextdesign'}</legend>
		<p>
			{l s='The module uses file caching to speed up rendering and thumbnail creation' mod='customtextdesign'}.<br>
			{l s='If some images/fonts are not displaying correclty, use the button below to reset the cache' mod='customtextdesign'}.<br>
		</p>
		<input type="submit" value="{l s='Empty module cache' mod='customtextdesign'}" class="button" name="submitEmptyCache" id="submitEmptyCache" >
	</fieldset>
</form>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}" method="post" id="colors_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/color.png">{l s="Colors/Textures" mod='customtextdesign'}</legend>
		<table id="colors_table" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:10px;">
			<colgroup>
				<col style="display:none" width="10px">
				<col width="30px">
				<col>
				<col width="100px">
				<col width="500px">
				<col width="40px">
				<col width="70px">
				<col width="52px">
			</colgroup>
			<thead>
				<tr class="nodrag nodrop" style="height: 40px">
					<th class="center" style="display:none">
						<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, 'colorBox[]', this.checked)">
					</th>
					<th class=""><span class="title_box">ID</span></th>
					<th class=""><span class="title_box">{l s="Name" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Preview" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Alpha" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Position" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Displayed" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Actions" mod='customtextdesign'}</span></th>
				</tr>
			</thead>
			<tbody>
				{$c = 0}
				{$colorcount = count($colors)}
				{foreach from=$colors key=k item=color}
				{$editlink = "index.php?controller=AdminModules{$default}&amp;id_color={$color.id|intval}&amp;updatecolor&amp;token={$token|escape:'htmlall':'UTF-8'}"}
				<tr id="tr_{$color.id|intval}" class="row_hover">
					<td class="center" onclick="location = '{$editlink|strip_tags}'" style="display:none">
						<input type="checkbox" name="colorBox[]" value="3" class="noborder">
					</td>
					<td class="pointer center" onclick="location = '{$editlink|strip_tags}'">
						{$color.id|intval}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'">
						{if !empty($color.name)}{$color.name|escape:'htmlall':'UTF-8'}{else}{$color.color|escape:'htmlall':'UTF-8'}{/if}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'">
						<div class="color-preview" style="
							{if $color.is_color}
							background-color:{$color.color|escape:'htmlall':'UTF-8'};
							{else}
							background-image:url('{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($color.texture, 64, 16,'texture')}');
							{/if}" >
						</div>
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'">
						{if $color.alpha}
						{$color.alpha|escape:'htmlall':'UTF-8'} / 127 ({Tools::ps_round($color.alpha/127*100,2)}%)
						{/if}
						&nbsp;
					</td>
					<td id="td_{$color.id|escape:'htmlall':'UTF-8'}" class="pointer dragHandle center">
						<a style="{if !($colorcount>1 && $c<$colorcount-1)}display:none;{/if}" class="arrow-down" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_color={$color.id|intval}&amp;down&amp;pos={$color.position|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/down.gif" alt="{l s='Down' mod='customtextdesign'}" title="{l s='Down' mod='customtextdesign'}">
						</a>
						<a style="{if !($colorcount>1 && $c>0)}display:none{/if}" class="arrow-up" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_color={$color.id|intval}&amp;up&amp;pos={$color.position|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/up.gif" alt="{l s='Up' mod='customtextdesign'}" title="{l s='Up' mod='customtextdesign'}">
						</a>
					</td>
					<td class="pointer center">
						<a href="index.php?controller=AdminModules{$default}&amp;id_color={$color.id|intval}&amp;colorstatus&amp;token={$token|escape:'htmlall':'UTF-8'}" title="Enabled">
							{if $color.displayed}
							<img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='customtextdesign'}">
							{else}
							<img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='customtextdesign'}">
							{/if}
						</a>
					</td>
					<td class="center" style="white-space: nowrap;">
						<a href="{$editlink|strip_tags}" class="edit" title="{l s='Edit' mod='customtextdesign'}">
							<img src="../img/admin/edit.gif" alt="{l s='Edit' mod='customtextdesign'}">
						</a>
						<a href="index.php?controller=AdminModules{$default}&amp;id_color={$color.id|intval}&amp;deletecolor&amp;token={$token|escape:'htmlall':'UTF-8'}" onclick="if (confirm('{l s='Are you sure to delete this item?' mod='customtextdesign'}')){ return true; }else{ event.stopPropagation(); event.preventDefault(); return false;};" class="delete" title="{l s='Delete' mod='customtextdesign'}">
							<img src="../img/admin/delete.gif" alt="{l s='Delete' mod='customtextdesign'}">
						</a>
					</td>
				</tr>
				{$c = $c+1}
				{/foreach}
			</tbody>
		</table>
		<input type="submit" class="button" name="submitAddNewColor" value="{l s='Add a new color' mod='customtextdesign'}" id="submitAddNewColor">
		<input type="submit" class="button" name="submitImportTexture" value="{l s='Import textures from folder' mod='customtextdesign'}" id="submitImportTexture">
	</fieldset>
</form>
<style type="text/css">
	.color-preview{
	width:64px;
	height:16px;
	background-size:100% 100%;
	}
</style>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}" method="post" id="fonts_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Fonts" mod='customtextdesign'}</legend>
		<table id="fonts_table" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:10px;">
			<colgroup>
				<col width="20px">
				<col>
				<col>
				<col width="500px">
				<col width="40px">
				<col width="70px">
				<col width="52px">
			</colgroup>
			<thead>
				<tr class="nodrag nodrop" style="height: 40px">
					<th class=""><span class="title_box">ID</span></th>
					<th class=""><span class="title_box">{l s="Name" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="File" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Preview" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Position" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Displayed" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Actions" mod='customtextdesign'}</span></th>
				</tr>
			</thead>
			<tbody>
				{$c = 0}
				{$fontcount = count($fonts)}
				{foreach from=$fonts key=k item=font}
				{$editlink="index.php?controller=AdminModules{$default}&amp;id_font={$font.id|intval}&amp;updatefont&amp;token={$token|escape:'htmlall':'UTF-8'}"}
				<tr id="tr_{$font.id|intval}" class="row_hover">
					<td class="pointer center" onclick="location = '{$editlink|strip_tags}'">
						{$font.id|intval}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$font.name|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$font.file|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'">
						<div class="font-preview" style="">
							<img style="height:20px" src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::render($font.name, $font.id, $font_color)}" />
						</div>
					</td>
					<td id="td_{$font.id|intval}" class="pointer dragHandle center">
						<a style="{if !($fontcount>1 && $c<$fontcount-1)}display:none;{/if}" class="arrow-down" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_font={$font.id|intval}&amp;down&amp;pos={$font.position|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/down.gif" alt="{l s='Down' mod='customtextdesign'}" title="{l s='Down' mod='customtextdesign'}">
						</a>
						<a style="{if !($fontcount>1 && $c>0)}display:none{/if}" class="arrow-up" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_font={$font.id|intval}&amp;up&amp;pos={$font.position|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/up.gif" alt="{l s='Up' mod='customtextdesign'}" title="{l s='Up' mod='customtextdesign'}">
						</a>
					</td>
					<td class="pointer center">
						<a href="index.php?controller=AdminModules{$default}&amp;id_font={$font.id|intval}&amp;fontstatus&amp;token={$token|escape:'htmlall':'UTF-8'}" title="Enabled">
							{if $font.displayed}
							<img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='customtextdesign'}">
							{else}
							<img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='customtextdesign'}">
							{/if}
						</a>
					</td>
					<td class="center" style="white-space: nowrap;">
						<a href="{$editlink|strip_tags}" class="edit" title="{l s='Edit' mod='customtextdesign'}">
							<img src="../img/admin/edit.gif" alt="{l s='Edit' mod='customtextdesign'}">
						</a>
						<a href="index.php?controller=AdminModules{$default}&amp;id_font={$font.id|intval}&amp;deletefont&amp;token={$token|escape:'htmlall':'UTF-8'}" onclick="if (confirm('{l s='Are you sure to delete this item?' mod='customtextdesign'}')){ return true; }else{ event.stopPropagation(); event.preventDefault(); return false;};" class="delete" title="{l s='Delete' mod='customtextdesign'}">
							<img src="../img/admin/delete.gif" alt="{l s='Delete' mod='customtextdesign'}">
						</a>
					</td>
				</tr>
				{$c=$c+1}
				{/foreach}
			</tbody>
		</table>
		<input type="submit" class="button" name="submitAddNewFont" value="{l s='Add a new font' mod='customtextdesign'}" id="submitAddNewFont">
		<input type="submit" class="button" name="submitImportFont" value="{l s='Import fonts from folder' mod='customtextdesign'}" id="submitImportFont">
	</fieldset>
</form>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}" method="post" id="groups_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/tab-categories.gif">{l s="Image Groups" mod='customtextdesign'}</legend>
		<table id="groups_table" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:10px;">
			<colgroup>
				<col width="20px">
				<col>
				<col>
				<col width="100px">
				<col width="40px">
				<col width="70px">
				<col width="52px">
			</colgroup>
			<thead>
				<tr class="nodrag nodrop" style="height: 40px">
					<th class=""><span class="title_box">ID</span></th>
					<th class=""><span class="title_box">{l s="Name" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="File" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Preview" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Position" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Displayed" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Actions" mod='customtextdesign'}</span></th>
				</tr>
			</thead>
			<tbody>
				{$c = 0}
				{$groupcount = count($groups)}
				{foreach from=$groups key=k item=group}
				{$editlink="index.php?controller=AdminModules{$default|strip_tags}&amp;id_group={$group.id|intval}&amp;updategroup&amp;token={$token|escape:'htmlall':'UTF-8'}"}
				<tr id="tr_{$group.id|intval}" class="row_hover">
					<td class="pointer center" onclick="location = '{$editlink|strip_tags}'">
						{$group.id|intval}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$group.name|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$group.file|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'">
						<div class="group-preview" style="">
							<img style="height:20px" src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($group.file, 0, 50, 'group')}" />
						</div>
					</td>
					<td id="td_{$group.id|intval}" class="pointer dragHandle center">
						<a style="{if !($groupcount>1 && $c<$groupcount-1)}display:none;{/if}" class="arrow-down" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_group={$group.id|intval}&amp;down&amp;pos={$group.position|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/down.gif" alt="{l s='Down' mod='customtextdesign'}" title="{l s='Down' mod='customtextdesign'}">
						</a>
						<a style="{if !($groupcount>1 && $c>0)}display:none{/if}" class="arrow-up" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_group={$group.id|intval}&amp;up&amp;pos={$group.position|intval}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/up.gif" alt="{l s='Up' mod='customtextdesign'}" title="{l s='Up' mod='customtextdesign'}">
						</a>
					</td>
					<td class="pointer center">
						<a href="index.php?controller=AdminModules{$default}&amp;id_group={$group.id|intval}&amp;groupstatus&amp;token={$token|escape:'htmlall':'UTF-8'}" title="Enabled">
							{if $group.displayed}
							<img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='customtextdesign'}">
							{else}
							<img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='customtextdesign'}">
							{/if}
						</a>
					</td>
					<td class="center" style="white-space: nowrap;">
						<a href="{$editlink|strip_tags}" class="edit" title="{l s='Edit' mod='customtextdesign'}">
							<img src="../img/admin/edit.gif" alt="{l s='Edit' mod='customtextdesign'}">
						</a>
						<a href="index.php?controller=AdminModules{$default}&amp;id_group={$group.id|intval}&amp;deletegroup&amp;token={$token|escape:'htmlall':'UTF-8'}" onclick="if (confirm('{l s='Are you sure to delete this item?' mod='customtextdesign'}')){ return true; }else{ event.stopPropagation(); event.preventDefault(); return false;};" class="delete" title="{l s='Delete' mod='customtextdesign'}">
							<img src="../img/admin/delete.gif" alt="{l s='Delete' mod='customtextdesign'}">
						</a>
					</td>
				</tr>
				{$c=$c+1}
				{/foreach}
			</tbody>
		</table>
		<input type="submit" class="button" name="submitAddNewgroup" value="{l s='Add a new group' mod='customtextdesign'}" id="submitAddNewgroup">
	</fieldset>
</form>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}" method="post" id="images_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Images" mod='customtextdesign'}</legend>
		<table id="images_table" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:10px;">
			<colgroup>
				<col width="20px">
				<col>
				<col width="100px">
				<col>
				<col width="100px">
				<col width="50px">
				<col width="400px">
				<!--<col width="40px">-->
				<col width="70px">
				<col width="52px">
			</colgroup>
			<thead>
				<tr class="nodrag nodrop" style="height: 40px">
					<th class=""><span class="title_box">ID</span></th>
					<th class=""><span class="title_box">{l s="Name" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Group" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="File" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Preview" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Price" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Quantity" mod='customtextdesign'}</span></th>
					<!--<th class="center"><span class="title_box">{l s="Position" mod='customtextdesign'}</span></th>-->
					<th class="center"><span class="title_box">{l s="Displayed" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Actions" mod='customtextdesign'}</span></th>
				</tr>
			</thead>
			<tbody>
				{$c = 0}
				{$imagecount = count($images)}
				{foreach from=$images key=k item=image}
				{$editlink="index.php?controller=AdminModules{$default|strip_tags}&amp;id_image={$image.id|intval}&amp;updateimage&amp;token={$token|escape:'htmlall':'UTF-8'}"}
				<tr id="tr_{$image.id|intval}" class="row_hover">
					<td class="pointer center" onclick="location = '{$editlink|strip_tags}'">
						{$image.id|intval}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$image.name|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$thisgroup = $sorted_groups[$image.id_group]}
						{$thisgroup.name|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$image.file|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'">
						<div class="image-preview" style="">
							<img style="height:20px" src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($image.file, 0, 50, 'image', 'auto', (int)$thisgroup.colorize)}" />
						</div>
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$currency->prefix|escape:'htmlall':'UTF-8'}{Tools::ps_round($image.price,2)}{$currency->suffix|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{if $image.quantity == -1}
							<span style="color:green">{l s='Unlimited' mod='customtextdesign'}</span>
						{elseif $image.quantity == 0}
							<span style="color:red;font-weight:bold;">{l s='Out of stock' mod='customtextdesign'}</span>
						{else}
							<span style="color:green">{$image.quantity|intVal}</span>
						{/if}
					</td>
					<td class="pointer center">
						<a href="index.php?controller=AdminModules{$default}&amp;id_image={$image.id|intval}&amp;imagestatus&amp;token={$token|escape:'htmlall':'UTF-8'}" title="Enabled">
							{if $image.displayed}
							<img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='customtextdesign'}">
							{else}
							<img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='customtextdesign'}">
							{/if}
						</a>
					</td>
					<td class="center" style="white-space: nowrap;">
						<a href="{$editlink|strip_tags}" class="edit" title="{l s='Edit' mod='customtextdesign'}">
							<img src="../img/admin/edit.gif" alt="{l s='Edit' mod='customtextdesign'}">
						</a>
						<a href="index.php?controller=AdminModules{$default}&amp;id_image={$image.id|intval}&amp;deleteimage&amp;token={$token|escape:'htmlall':'UTF-8'}" onclick="if (confirm('{l s='Are you sure to delete this item?' mod='customtextdesign'}')){ return true; }else{ event.stopPropagation(); event.preventDefault(); return false;};" class="delete" title="{l s='Delete' mod='customtextdesign'}">
							<img src="../img/admin/delete.gif" alt="{l s='Delete' mod='customtextdesign'}">
						</a>
					</td>
				</tr>
				{$c=$c+1}
				{/foreach}
			</tbody>
		</table>
		<input type="submit" class="button" name="submitAddNewimage" value="{l s='Add a new image' mod='customtextdesign'}" id="submitAddNewimage">
		<input type="submit" class="button" name="submitImportImage" value="{l s='Import images from folder' mod='customtextdesign'}" id="submitImportImage">
	</fieldset>
</form>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}" method="post" id="materials_form">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/money.gif">{l s="Material Types / Pricing" mod='customtextdesign'}</legend>
		<p>{l s="You can use the slider to select specific prices for different available text sizes." mod='customtextdesign'}<br>
		{l s="The original material price is the price by default for all text sizes." mod='customtextdesign'}</p>
		<table id="materials_table" class="table tableDnD" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom:10px;">
			<colgroup>
				<col width="20px">
				<col>
				<col>
				<!--<col width="400px">-->
				<col width="40px">
				<col width="70px">
				<col width="52px">
			</colgroup>
			<thead>
				<tr class="nodrag nodrop" style="height: 40px">
					<th class=""><span class="title_box">ID</span></th>
					<th class=""><span class="title_box">{l s="Material Type" mod='customtextdesign'}</span></th>
					<th class=""><span class="title_box">{l s="Price (per m²)" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Position" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Displayed" mod='customtextdesign'}</span></th>
					<th class="center"><span class="title_box">{l s="Actions" mod='customtextdesign'}</span></th>
				</tr>
			</thead>
			<tbody>
				{$c = 0}
				{$materialcount = count($materials)}
				{foreach from=$materials key=k item=material}
				{$editlink="index.php?controller=AdminModules{$default|strip_tags}&amp;id_material={$material.id|intval}&amp;updatematerial&amp;token={$token|escape:'htmlall':'UTF-8'}"}
				<tr id="tr_{$material.id|intval}" class="row_hover">
					<td class="pointer center" onclick="location = '{$editlink|strip_tags}'">
						{$material.id|intval}
					</td>
					<td class="pointer " onclick="location = '{$editlink|strip_tags}'" >
						{$material.name|escape:'htmlall':'UTF-8'}
					</td>
					<td class="pointer " >
						<div class="material-slider-div">
							<div class="material-price">{$currency->prefix|escape:'htmlall':'UTF-8'}{Tools::ps_round($material.price,2)}{$currency->suffix|escape:'htmlall':'UTF-8'}</div>
							<div class="material-specific">
								<div class="material-specific-size"></div>
								{for $size=$num_size_min to $num_size_max}
									<input type="text" class="hide input_material_size input_material_size_{$size|intval}" data-name="material_specific_{$material.id|intval}_{$size|intval}" value="{if isset($material_prices[$material.id][$size]) AND $material_prices[$material.id][$size]>0}{Tools::ps_round($material_prices[$material.id][$size],2)}{else}{Tools::ps_round($material.price,2)}{/if}" />
								{/for}
							</div>
							<div class="clear"></div>
							<div class="material-slider" title="slider"></div>
						</div>
					</td>
					<td id="td_{$material.id|intval}" class="pointer dragHandle center">
						<a style="{if !($materialcount>1 && $c<$materialcount-1)}display:none;{/if}" class="arrow-down" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_material={$material.id|intval}&amp;down&amp;pos={$material.position}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/down.gif" alt="{l s='Down' mod='customtextdesign'}" title="{l s='Down' mod='customtextdesign'}">
						</a>
						<a style="{if !($materialcount>1 && $c>0)}display:none{/if}" class="arrow-up" href="#" hrf="index.php?controller=AdminModules{$default}&amp;id_material={$material.id|intval}&amp;up&amp;pos={$material.position}&amp;token={$token|escape:'htmlall':'UTF-8'}">
							<img src="../img/admin/up.gif" alt="{l s='Up' mod='customtextdesign'}" title="{l s='Up' mod='customtextdesign'}">
						</a>
					</td>
					<td class="pointer center">
						<a href="index.php?controller=AdminModules{$default}&amp;id_material={$material.id|intval}&amp;materialstatus&amp;token={$token|escape:'htmlall':'UTF-8'}" title="Enabled">
							{if $material.displayed}
							<img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='customtextdesign'}">
							{else}
							<img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='customtextdesign'}">
							{/if}
						</a>
					</td>
					<td class="center" style="white-space: nowrap;">
						<a href="{$editlink|strip_tags}" class="edit" title="{l s='Edit' mod='customtextdesign'}">
							<img src="../img/admin/edit.gif" alt="{l s='Edit' mod='customtextdesign'}">
						</a>
						<a href="index.php?controller=AdminModules{$default}&amp;id_material={$material.id|intval}&amp;deletematerial&amp;token={$token|escape:'htmlall':'UTF-8'}" onclick="if (confirm('{l s='Are you sure to delete this item?' mod='customtextdesign'}')){ return true; }else{ event.stopPropagation(); event.preventDefault(); return false;};" class="delete" title="{l s='Delete' mod='customtextdesign'}">
							<img src="../img/admin/delete.gif" alt="{l s='Delete' mod='customtextdesign'}">
						</a>
					</td>
				</tr>
				{$c=$c+1}
				{/foreach}
				<tr>
					<td></td>
					<td></td>
					<td><input type="submit" class="button" name="submitSaveSpecificPrices" value="{l s='Save specific prices' mod='customtextdesign'}" id="submitSaveSpecificPrices"/></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="button" name="submitAddNewMaterial" value="{l s='Add a new material' mod='customtextdesign'}" id="submitAddNewMaterial">
	</fieldset>
</form>
<br>
<form action="" id="config" method="post">
	<fieldset style="">
		<legend><img width="16" height="16" src="../img/admin/prefs.gif">{l s="Configuration" mod='customtextdesign'}</legend>

		{if count($module_pages)}
		<label></label>
		<div class="margin-form">
			<select style="vertical-align: middle;width: 200px;" id="ctd_page_list">
				{foreach from=$module_pages item=module_page}
					<option value="{$module_page.pagename|htmlspecialchars}">{$module_page.pagename|escape:'htmlall':'UTF-8'}</option>
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

		<label for="show_base_img">{l s='Show base image on PDF' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input type="radio" name="show_base_img" id="show_base_img_on"  value="1" {if $show_base_img=='1'}checked="checked"{/if}>
			<label class="t" for="show_base_img_on"> <img src="../img/admin/enabled.gif" alt="Enabled" title="Enabled"></label>
			<input type="radio" name="show_base_img" id="show_base_img_off" value="0" {if !$show_base_img OR $show_base_img=='0'}checked="checked"{/if}>
			<label class="t" for="show_base_img_off"> <img src="../img/admin/disabled.gif" alt="Disabled" title="Disabled"></label>
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
		<label for="size_min">{l s='Delete old products' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{l s='Delete products that are' mod='customtextdesign'}
			<select name="del_number">
				<option {if $del_number==0}selected="selected"{/if} value="0">0</option>
				<option {if $del_number==1}selected="selected"{/if} value="1">1</option>
				<option {if $del_number==2}selected="selected"{/if} value="2">2</option>
				<option {if $del_number==3}selected="selected"{/if} value="3">3</option>
				<option {if $del_number==4}selected="selected"{/if} value="4">4</option>
				<option {if $del_number==5}selected="selected"{/if} value="5">5</option>
				<option {if $del_number==6}selected="selected"{/if} value="6">6</option>
			</select>
			<select name="del_span">
				<option {if $del_span==0}selected="selected"{/if} value="0">{l s='Day(s)' mod='customtextdesign' js=1}</option>
				<option {if $del_span==1}selected="selected"{/if} value="1">{l s='Month(s)' mod='customtextdesign' js=1}</option>
				<option {if $del_span==2}selected="selected"{/if} value="2">{l s='Year(s)' mod='customtextdesign' js=1}</option>
			</select>
			{l s='old' mod='customtextdesign'}
			<p>{l s='This module automatically creates new products and saves them in a hidden category.' mod='customtextdesign'}<br>
				{l s='Select 0 if you want to keep all custom design products.' mod='customtextdesign'}</p>
		</div>
		<br class="clear">

		<div class="margin-form">
			<input class="button" type="submit" name="submitUpdateConfig" value="{l s='Save Settings' mod='customtextdesign'}">
		</div>
		<br class="clear">

	</fieldset>
</form>
<br>
<form action="{$req|escape:'htmlall':'UTF-8'}" id="pagelink" method="post">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/next.gif">{l s="Module Page" mod='customtextdesign'}</legend>
		<a style="color: #008FFF;" target="_module_page" href="{$link->getModuleLink('customtextdesign')|escape:'htmlall':'UTF-8'}"><img width="16" height="16" src="../img/admin/page_world.png">{l s='Click here to access the module page' mod='customtextdesign'}</a>
		<p>
		<p>{l s='You can add this link to the menu or on your banners to make it available for users.' mod='customtextdesign'}</p><br>
		<input style="width:300px" onclick="this.select()" type="text" value="{$link->getModuleLink('customtextdesign')|htmlspecialchars}"> {l s='or' mod='customtextdesign'}
		<input style="width:300px" onclick="this.select()" type="text" value="{literal}{$link->getModuleLink('customtextdesign')}{/literal}">
		</p>
		<p>
		{$editlink="index.php?controller=AdminModules{$default|strip_tags}&configurepages&token={$token|escape:'htmlall':'UTF-8'}"}
		<a style="color: #008FFF;" href="{$editlink|strip_tags}"><img width="16" height="16" src="../img/admin/cms.gif">{l s="Configure module pages" mod='customtextdesign'}</a>
		</p>
		<p><a style="color: #008FFF;" target="_blank" href="http://prestashop.prestalife.net/modules/customtextdesign/docs/"><img width="16" height="16" src="../img/admin/help.png">{l s='Refer to the documentation for more help.' mod='customtextdesign'}</a></p>
	</fieldset>
</form>
<br>
<style type="text/css">
	.displayed_flag{
	position: relative;
	top: -10px;
	}
	.language_flags{

	}
</style>