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
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Add a new group" mod='customtextdesign'}</legend>
		<label for="group_name">{l s='Group Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="group_name" type="text" name="group_name" value="">
		</div>
		<br class="clear">
		<label for="group_file">{l s='Group Icon' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="group_file" type="file" name="group_file" onchange="var f =$(this).val(); !$('#group_name').val() && $('#group_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\.(.*)$/i,'').replace(/[_-]/g,' ').toProperCase())">
		</div>
		<br class="clear">
		<label for="group_colorize">&nbsp;</label>
		<div class="margin-form">
			<input id="group_colorize" type="checkbox" name="group_colorize" > {l s='Colorize group for better visibility' mod='customtextdesign'}
			<p>{l s='Check this option if the group consists of white images' mod='customtextdesign'}</p>
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitNewgroup" value="{l s='Add' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{else}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/choose.gif">{l s="Edit group" mod='customtextdesign'}</legend>
		<input type="hidden" name="id_group" value="{$group.id|intval}" />
		<input type="hidden" name="group_file_old" value="{$group.file|intval}" />
		<label for="group_name">{l s='Group Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="group_name" type="text" name="group_name" value="{$group.name|escape:'htmlall':'UTF-8'}">
		</div>
		<br class="clear">
		<label for="group_file">{l s='Group Icon' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="group_file" type="file" name="group_file" onchange="var f =$(this).val(); !$('#group_name').val() && $('#group_name').val(f.substring(f.lastIndexOf('\\')+1).replace(/\.(.*)$/i,'').toProperCase())">
            <p>{l s='Current group file' mod='customtextdesign'}: {$group.file|escape:'htmlall':'UTF-8'}</p>
		</div>
		<br class="clear">
		<label for="group_colorize">&nbsp;</label>
		<div class="margin-form">
			<input id="group_colorize" type="checkbox" name="group_colorize" {if (int)$group.colorize} checked="checked"{/if}> {l s='Colorize group for better visibility' mod='customtextdesign'}
			<p>{l s='Check this option if the group consists of white images' mod='customtextdesign'}</p>
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitEditgroup" value="{l s='Save' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{/if}