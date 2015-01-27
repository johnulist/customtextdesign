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

<div class="ctd_custom_product" {if ! count($custom_products)}style="display:none"{/if}>
	<strong style="margin-bottom: 5px;display: block;">{l s='Customizations' mod='customtextdesign'}:</strong>
	<table>
		<thead>
			<th>{l s='Preview' mod='customtextdesign'}</th>
			<th class="ctd_extra_details">{l s='Option' mod='customtextdesign'}</th>
			<th class="ctd_extra_details" width="70">{l s='Unit price' mod='customtextdesign'}</th>
			<th width="50"></th>
		</thead>
		<tbody>
			{foreach from=$custom_products item=prod}
			{$request = array()}
			{$request['id_custom_product'] = $prod.uniqid}
			<tr>
				<td>
					<a target="_blank" href="{$link->getModuleLink($module, 'Preview', $request)|escape:'htmlall':'UTF-8'}"><img style="width:150px" src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{$prod.preview|escape:'htmlall':'UTF-8'}" /></a>
				</td>
				<td class="ctd_extra_details">
					{$prod.attributes|escape:'htmlall':'UTF-8'|truncate:30:"..."}
				</td>
				<td class="ctd_extra_details">
					{$prod.price|escape:'htmlall':'UTF-8'}
				</td>
				<td>
					{if $prod.level}
					<a href="#" data-id_custom_product="{$prod.uniqid|escape:'htmlall':'UTF-8'}" class="addcart_custom_product {if $prod.has_custom}ctd_hilite{/if}" title="{l s='Add to cart' mod='customtextdesign'}"></a>&nbsp;
					{/if}
					<a target="_blank" href="{$link->getModuleLink($module, 'Preview', $request)|escape:'htmlall':'UTF-8'}" class="preview_custom_product" title="{l s='Preview' mod='customtextdesign'}"></a>&nbsp;
					<a href="#" data-id_custom_product="{$prod.uniqid|escape:'htmlall':'UTF-8'}" class="remove_custom_product" title="{l s='Delete' mod='customtextdesign'}"></a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>