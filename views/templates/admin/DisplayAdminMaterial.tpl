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
		<legend><img width="16" height="16" src="../img/admin/money.gif">{l s="Add a new material" mod='customtextdesign'}</legend>
		<label for="material_name">{l s='Material Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="material_name" type="text" name="material_name" value="">
		</div>
		<br class="clear">
		<label for="material_price">{l s='Material Price' mod='customtextdesign'} {l s='(per m²)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="material_price" type="text" name="material_price" value="0.00" onchange="noComma('material_price');">{$currency->suffix|escape:'htmlall':'UTF-8'}
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitNewMaterial" value="{l s='Add' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{else}
<form action="{$req|escape:'htmlall':'UTF-8'}{$urlhash|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend><img width="16" height="16" src="../img/admin/money.gif">{l s="Edit material" mod='customtextdesign'}</legend>
		<input type="hidden" name="id_material" value="{$material.id|intval}" />
		<input type="hidden" name="material_file_old" value="{$material.file|escape:'htmlall':'UTF-8'}" />
		<label for="material_name">{l s='Material Name' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			<input id="material_name" type="text" name="material_name" value="{$material.name|escape:'htmlall':'UTF-8'}">
		</div>
		<br class="clear">
		<label for="material_price">{l s='Material Price' mod='customtextdesign'} {l s='(per m²)' mod='customtextdesign'}:&nbsp;&nbsp;</label>
		<div class="margin-form">
			{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="material_price" type="text" name="material_price" value="{Tools::ps_round($material.price,2)}" onchange="noComma('material_price');">{$currency->suffix|escape:'htmlall':'UTF-8'}
		</div>
		<br class="clear">
		<div class="margin-form">
			<input class="button" type="submit" name="submitEditMaterial" value="{l s='Save' mod='customtextdesign'}">
			<input class="button" type="submit" name="submitCancel" value="{l s='Cancel' mod='customtextdesign'}">
		</div>
		<br class="clear">
	</fieldset>
</form>
{/if}