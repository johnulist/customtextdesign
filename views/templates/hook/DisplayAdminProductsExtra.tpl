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
	var ctd_measures =	$.parseJSON('{$measures|json_encode}');
	var ctd_overlays =	$.parseJSON('{$overlays|json_encode}');
	var ctd_masks =	$.parseJSON('{$masks|json_encode}');
	var ctd_masks2 =	$.parseJSON('{$masks2|json_encode}');
	var ctd_replaces =	$.parseJSON('{$replaces|json_encode}');
	var ctd_custom_fields =	$.parseJSON('{$custom_fields|json_encode}');
	var ctd_custom_fields_trans =	$.parseJSON('{$cf_lang|json_encode}');
	var ctd_id_lang = {$languages[0].id_lang|intVal};
	var ctd_module_dir = "{$module_dir|htmlspecialchars}";
	var ctd_str_none = "{l s='None' mod='customtextdesign'}";
	var ctd_str_sure = "{l s='Are you sure you want to delete this item?' mod='customtextdesign'}";
	var ctd_str_check_all = "{l s='All' mod='customtextdesign'}";
	var ctd_str_check_none = "{l s='None' mod='customtextdesign'}";
	var ctd_str_default_img = "{l s='Default image changed successfully' mod='customtextdesign'}";
	var ctd_message = {
		"confirm_del_cf": "{l s='Please confirm the deletion of this field' mod='customtextdesign' js=1}",
	};
	ctd_admin.addCheckGroupButtons();
</script>

<h4>
	<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/logo.png" border="0" width="32" height="32" alt="logo">
	&nbsp;{l s='Product Customization Options' mod='customtextdesign'}
	<a href="#" title="{l s='Reload this tab' mod='customtextdesign'}" onclick="$('#product-tab-content-ModuleCustomtextdesign').addClass('not-loaded');tabs_manager.display('ModuleCustomtextdesign',0);return false;"><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/reload.png"/></a>
</h4>
<div>
	<div class="alert alert-warning warn" id="ctd_ajax_error_msg" style="display:none">{l s='Permission denied, please duplicate this product to edit freely, thank you' mod='customtextdesign'}</div>
	<div class="alert alert-warning warn" id="ctd_ajax_msg" style="visibility:hidden">{l s='Saving data...' mod='customtextdesign'}</div>
	<div class="alert alert-success conf" id="ctd_ajax_success_msg" style="display: none;">{l s='Data saved' mod='customtextdesign'}</div>
</div>
<div class="ctd-admin" style="margin-bottom:100px">
	<div id="ctd_ui_tabs">
		<ul>
			<li><a href="#ctd_tab1">{l s='Settings' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab2">{l s='Design Options' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab3" id="a_ctd_tab3">{l s='Prices' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab4">{l s='Images' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab8">{l s='Images colors' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab5">{l s='Dynamic Product' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab6">{l s='Custom fields' mod='customtextdesign'}</a></li>
			<li><a href="#ctd_tab7">{l s='Extra options' mod='customtextdesign'}</a></li>
		</ul>
		<div id="ctd_tab1">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr>
						<td colspan="2">
							<p>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign&submitGlobalConfig" target="_blank">
									{l s='For a faster configuration, click here to select the default settings for all your products' mod='customtextdesign'}.
								</a><br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign&submitGlobalConfig" target="_blank">
									{l s='These settings will be selected by default in this tab' mod='customtextdesign'}.
								</a><br>
							</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<a href="#" class="ctd_toggle {if isset($ctd_product.active) AND $ctd_product.active}active{/if}" data-for="ctd_active"></a>
								<input type="checkbox" {if isset($ctd_product.active) AND $ctd_product.active}checked="checked"{/if} style="display:none !important" class="ctd-ajx" data-name="active" id="ctd_active"> {l s='Active for this product' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.required) AND $ctd_product.required}checked="checked"{/if} class="ctd-ajx" data-name="required" id="ctd_required"> {l s='Required' mod='customtextdesign'}
							</label>
							<p>{l s='If checked, the product will not be added to cart if not customized' mod='customtextdesign'}.</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.hide_text) AND $ctd_product.hide_text}checked="checked"{/if} class="ctd-ajx" data-name="hide_text" id="ctd_hide_text"> {l s='Hide text panel' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Max text length' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_max_length" type="text" data-name="max_length" value="{if isset($ctd_product.max_length)}{$ctd_product.max_length|intVal}{else}0{/if}" onchange="noComma('ctd_max_length');" class="ctd-ajx">
							<p>{l s='Set the value to 0 to ignore.' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.alpha) AND $ctd_product.alpha}checked="checked"{/if} class="ctd-ajx" data-name="alpha" id="ctd_alpha"> {l s='Allow transparency' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.letterspace) AND $ctd_product.letterspace}checked="checked"{/if} class="ctd-ajx" data-name="letterspace" id="ctd_letterspace"> {l s='Allow letter spacing' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Initial Letterspace' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_initial_letterspace" type="text" data-name="initial_letterspace" value="{if isset($ctd_product.initial_letterspace)}{$ctd_product.initial_letterspace|intVal}{else}0{/if}" onchange="noComma('ctd_initial_letterspace');" class="ctd-ajx">
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.curve) AND $ctd_product.curve}checked="checked"{/if} class="ctd-ajx" data-name="curve" id="ctd_curve"> {l s='Allow curved text' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Initial Curve' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_initial_curve" type="text" data-name="initial_curve" value="{if isset($ctd_product.initial_curve)}{$ctd_product.initial_curve|intVal}{else}0{/if}" onchange="noComma('ctd_initial_curve');" class="ctd-ajx">
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.picker) AND $ctd_product.picker}checked="checked"{/if} class="ctd-ajx" data-name="picker" id="ctd_picker"> {l s='Show color picker' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr class="ctd_border_top">
						<td valign="top" width="250"><label>{l s='Design panel' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.expanded) AND $ctd_product.expanded}checked="checked"{/if} class="ctd-ajx" data-name="expanded" id="ctd_expanded"> {l s='Start expanded' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.show_btn) AND $ctd_product.show_btn}checked="checked"{/if} class="ctd-ajx" data-name="show_btn" id="ctd_show_btn"> {l s='Show Customize button' mod='customtextdesign'}
							</label>
							<p>{l s='Shows a Customize button under product image' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr class="ctd_border_bottom">
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.popup) AND $ctd_product.popup}checked="checked"{/if} class="ctd-ajx" data-name="popup" id="ctd_popup"> {l s='Display as popup ' mod='customtextdesign'}
							</label>
							<p>"{l s='Show Customize button' mod='customtextdesign'}" {l s='should be checked' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.show_price) AND $ctd_product.show_price}checked="checked"{/if} class="ctd-ajx" data-name="show_price" id="ctd_show_price"> {l s='Show price list to clients' mod='customtextdesign'}
							</label>
							<p>{l s='Shows the detailed prices for the client before adding to cart' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.show_download_btn) AND $ctd_product.show_download_btn}checked="checked"{/if} class="ctd-ajx" data-name="show_download_btn" id="ctd_show_download_btn"> {l s='Show download PNG button' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.images_first) AND $ctd_product.images_first}checked="checked"{/if} class="ctd-ajx" data-name="images_first" id="ctd_images_first"> {l s='Show images tab first' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.upload) AND $ctd_product.upload}checked="checked"{/if} class="ctd-ajx" data-name="upload" id="ctd_upload"> {l s='Allow image upload' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.url_upload) AND $ctd_product.url_upload}checked="checked"{/if} class="ctd-ajx" data-name="url_upload" id="ctd_url_upload"> {l s='Allow image upload from URL' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Uploaded Image max size' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							{$upload_max = -1}
							{if isset($ctd_product.upload_max)}
							{$upload_max = $ctd_product.upload_max}
							{/if}
							<select data-name="upload_max" class="ctd-ajx" >
								<option {if $upload_max == 0}selected="selected" {/if}value="0">500 {l s='KB' mod='customtextdesign'}</option>
								<option {if $upload_max == 1}selected="selected" {/if}value="1">1 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 2}selected="selected" {/if}value="2">2 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 3}selected="selected" {/if}value="3">3 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 4}selected="selected" {/if}value="4">4 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 5}selected="selected" {/if}value="5">5 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 6}selected="selected" {/if}value="6">6 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 7}selected="selected" {/if}value="7">7 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 8}selected="selected" {/if}value="8">8 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 9}selected="selected" {/if}value="9">9 {l s='MB' mod='customtextdesign'}</option>
								<option {if $upload_max == 10}selected="selected" {/if}value="10">10 {l s='MB' mod='customtextdesign'}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Uploaded Image Cost' mod='customtextdesign'} ({$currency->prefix|escape:'htmlall':'UTF-8'}{$currency->suffix|escape:'htmlall':'UTF-8'} {l s='per m²' mod='customtextdesign'}):</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_upload_price" type="text" data-name="upload_price" value="{if isset($ctd_product.upload_price)}{Tools::ps_round($ctd_product.upload_price,2)}{else}0.00{/if}" onchange="noComma('ctd_upload_price');" class="ctd-ajx">
							<p>{l s='if' mod='customtextdesign'} <a onclick="$('#a_ctd_tab3').click(); return false;" href="#ctd_tab3">"{l s='Use image price' mod='customtextdesign'}"</a> {l s='is checked, this price will be applied regardless of the image size' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Minimum Item Size' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_min_size" type="text" data-name="min_size" value="{if isset($ctd_product.min_size)}{Tools::ps_round($ctd_product.min_size,2)}{else}0.00{/if}" onchange="noComma('ctd_min_size');" class="ctd-ajx"> cm
							<p>{l s='Applies for item height and / or width.' mod='customtextdesign'} {l s='Set the value to 0 to ignore.' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td><div class="clear"></div></td>
						<td style="text-align:right"><a href="#" title="{l s='Reload this tab' mod='customtextdesign'}" onclick="$('#product-tab-content-ModuleCustomtextdesign').addClass('not-loaded');tabs_manager.display('ModuleCustomtextdesign',0);return false;"><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/reload.png"/></a></td>
					</tr>
				</tbody>
			</table>
			<div class="ctd_group input_lang">
				<label>{l s='Design panel title' mod='customtextdesign'}:</label>
				<div class="ctd_lang_container">
					{foreach $languages item=lang}
					<div class="ctd_lang">
						<input type="text" value="{if isset($product_lang[$lang.id_lang])}{$product_lang[$lang.id_lang]['title']|htmlspecialchars}{/if}" data-name="title_{$lang.id_lang|intval}" data-lang="{$lang.id_lang|intval}" class="ctd_lang_input" size="50" >
						<img class="ctd_flag" title="{$lang.name|escape:'htmlall':'UTF-8'}" src="../img/l/{$lang.id_lang|intval}.jpg" />
					</div>
					{/foreach}
				</div>
			</div>
			<div class="ctd_group input_lang">
				<label>{l s='Initial text' mod='customtextdesign'}:</label>
				<div class="ctd_lang_container">
					{foreach $languages item=lang}
					<div class="ctd_lang">
						<input type="text" value="{if isset($product_lang[$lang.id_lang])}{$product_lang[$lang.id_lang]['text_init']|htmlspecialchars}{/if}" data-name="text_init_{$lang.id_lang|intval}" data-lang="{$lang.id_lang|intval}" class="ctd_lang_input" size="50" >
						<img class="ctd_flag" title="{$lang.name|escape:'htmlall':'UTF-8'}" src="../img/l/{$lang.id_lang|intval}.jpg" />
					</div>
					{/foreach}
				</div>
			</div>
			<div class="ctd_group input_lang">
				<label>{l s='Instructions' mod='customtextdesign'}:</label>
				<div class="ctd_lang_container">
					{foreach $languages item=lang}
					<div class="ctd_lang">
						<input type="text" value="{if isset($product_lang[$lang.id_lang])}{$product_lang[$lang.id_lang]['instructions']|htmlspecialchars}{/if}" data-name="instructions_{$lang.id_lang|intval}" data-lang="{$lang.id_lang|intval}" class="ctd_lang_input" size="50" >
						<img class="ctd_flag" title="{$lang.name|escape:'htmlall':'UTF-8'}" src="../img/l/{$lang.id_lang|intval}.jpg" />
					</div>
					{/foreach}
				</div>
			</div>
		</div>
		<div id="ctd_tab2">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr>
						<td valign="top"><label>{l s='Available Colors' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="margin-bottom: 0px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.colors_all) AND $ctd_product.colors_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="colors_all" id="colors_all" /> {l s='Use all colors' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.colors)}
							{$check = explode('-',$ctd_product.colors)}
							{/if}
							{foreach from=$colors item=color}
							<label style="float:none">
								<input type="checkbox" {if in_array($color.id,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="colors" data-name="colors_{$color.id|intval}" /> {$color.name|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which colors can be used for this product.' mod='customtextdesign'}<br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#colors_form" target="_blank">{l s='Edit Colors' mod='customtextdesign'}</a>
							</p>
						</td>
					</tr>
					<tr class="ctd_hr">
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:15px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.hide_colors) AND $ctd_product.hide_colors}checked="checked"{/if} class="ctd-ajx" data-name="hide_colors" id="ctd_hide_colors"> {l s='Hide colors' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top"><label>{l s='Available Fonts' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="margin-bottom:0px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.fonts_all) AND $ctd_product.fonts_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="fonts_all" id="fonts_all" /> {l s='Use all fonts' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.fonts)}
							{$check = explode('-',$ctd_product.fonts)}
							{/if}
							{foreach from=$fonts item=font}
							<label style="float:none">
								<input type="checkbox" {if in_array($font.id,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="fonts" data-name="fonts_{$font.id|intval}" /> {$font.name|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which fonts can be used for this product.' mod='customtextdesign'}<br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#fonts_form" target="_blank">{l s='Edit Fonts' mod='customtextdesign'}</a>
							</p>
						</td>
					</tr>
					<tr class="ctd_hr">
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:15px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.hide_fonts) AND $ctd_product.hide_fonts}checked="checked"{/if} class="ctd-ajx" data-name="hide_fonts" id="ctd_hide_fonts"> {l s='Hide fonts' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top"><label>{l s='Available Materials' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="margin-bottom:0px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.materials_all) AND $ctd_product.materials_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="materials_all" id="materials_all" /> {l s='Use all materials' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.materials)}
							{$check = explode('-',$ctd_product.materials)}
							{/if}
							{foreach from=$materials item=material}
							<label style="float:none">
								<input type="checkbox" {if in_array($material.id,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="materials" data-name="materials_{$material.id|intval}" /> {$material.name|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which materials can be used for this product.' mod='customtextdesign'}<br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#materials_form" target="_blank">{l s='Edit Materials' mod='customtextdesign'}</a>
							</p>
						</td>
					</tr>
					<tr class="ctd_hr">
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:15px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.hide_materials) AND $ctd_product.hide_materials}checked="checked"{/if} class="ctd-ajx" data-name="hide_materials" id="ctd_hide_materials"> {l s='Hide materials' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top"><label>{l s='Available Image Groups' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="padding-bottom:5px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.image_groups_all) AND $ctd_product.image_groups_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="image_groups_all" id="image_groups_all" /> {l s='Use all image groups' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.image_groups)}
							{$check = explode('-',$ctd_product.image_groups)}
							{/if}
							{foreach from=$image_groups item=image_group}
							<label style="float:none">
								<input type="checkbox" {if in_array($image_group.id,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="image_groups" data-name="image_groups_{$image_group.id|intval}" /> {$image_group.name|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which image groups can be used for this product.' mod='customtextdesign'}<br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#groups_form" target="_blank">{l s='Edit Image Groups' mod='customtextdesign'}</a>
							</p>
						</td>
					</tr>
					{if count($attributes)>1}
					<tr>
						<td valign="top"><label>{l s='Available Attributes' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="padding-bottom:5px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.attributes_all) AND $ctd_product.attributes_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="attributes_all" id="attributes_all" /> {l s='Use all attributes' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.attributes)}
							{$check = explode('-',$ctd_product.attributes)}
							{/if}
							{foreach from=$attributes item=attribute}
							<label style="float:none">
								<input type="checkbox" {if in_array($attribute.id_product_attribute,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="attributes" data-name="attributes_{$attribute.id_product_attribute|intval}" /> {$attribute.attribute_designation|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which attributes activate the design panel for this product.' mod='customtextdesign'}<br>
							</p>
						</td>
					</tr>
					{/if}
				</tbody>
			</table>
		</div>
		<div id="ctd_tab3">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr class="ctd_border_bottom" style="display:none">
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.use_tax) AND $ctd_product.use_tax}checked="checked"{/if} class="ctd-ajx" data-name="use_tax" id="ctd_use_tax"> {l s='Apply Taxes' mod='customtextdesign'}
							</label>
							<p>{l s='Applies product taxes to design cost' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Fixed Prices' mod='customtextdesign'}</label></td>
						<td style="padding-bottom:5px;"></td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Text Fixed Price' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="ctd_text_price" type="text" data-name="text_price" value="{if isset($ctd_product.text_price)}{Tools::ps_round($ctd_product.text_price,2)}{else}0.00{/if}" onchange="noComma('ctd_text_price');" class="ctd-ajx">{$currency->suffix|escape:'htmlall':'UTF-8'}
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Image Fixed Price' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="ctd_image_price" type="text" data-name="image_price" value="{if isset($ctd_product.image_price)}{Tools::ps_round($ctd_product.image_price,2)}{else}0.00{/if}" onchange="noComma('ctd_image_price');" class="ctd-ajx">{$currency->suffix|escape:'htmlall':'UTF-8'}
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.image_fixed) AND $ctd_product.image_fixed}checked="checked"{/if} class="ctd-ajx" data-name="image_fixed" id="ctd_image_fixed"> {l s='Use image price' mod='customtextdesign'}
							</label>
							<p>{l s='The' mod='customtextdesign'} <a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#images_form" target="_blank">{l s='image price' mod='customtextdesign'}</a> {l s='will be used as fixed price' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Whole Design Fixed Price' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							{$currency->prefix|escape:'htmlall':'UTF-8'}<input id="ctd_design_price" type="text" data-name="design_price" value="{if isset($ctd_product.design_price)}{Tools::ps_round($ctd_product.design_price,2)}{else}0.00{/if}" onchange="noComma('ctd_design_price');" class="ctd-ajx">{$currency->suffix|escape:'htmlall':'UTF-8'}
							<p>{l s='Use a fixed price instead of dynamic prices.' mod='customtextdesign'} {l s='Set the value to 0 to ignore.' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.free_design) AND $ctd_product.free_design}checked="checked"{/if} class="ctd-ajx" data-name="free_design" id="ctd_free_design"> {l s='Free design' mod='customtextdesign'}
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ctd_tab4">
			<div class="ctd_gallery">
				<h3 style="margin: 0px;">{l s='Please provide product measurement for each image' mod='customtextdesign'}:</h3>
				<p>{l s='Drag and Resize the ruler and provide the width in centimeters. Set the origin point which will be considered as coordinates (x0, y0). This is necessary to have exact measurement and price calculation when clients order a customized product' mod='customtextdesign'}.</p>
				<div class="ctd_thumbs">
					{foreach from=$images_product item=image}
					{$srcThumb = $link->getImageLink($product->link_rewrite, $image.id_image, 'home_default')}
					{$image_type = Configuration::get('customtextdesignimage_type')}
					{$src = $link->getImageLink($product->link_rewrite, $image.id_image, $image_type)}
					<a href="{$src|escape:'htmlall':'UTF-8'}" data-id="{$image.id_image|intval}" class="ctd_a_thumb">
						<span class="ctd_icon ctd_tick" title="{l s='Measured' mod='customtextdesign'}"></span>
						<span class="ctd_icon ctd_layer" title="{l s='Has overlay' mod='customtextdesign'}"></span>
						<span class="ctd_icon ctd_mask_icon" title="{l s='Has mask' mod='customtextdesign'}"></span>
						<span class="ctd_icon ctd_mask2_icon" title="{l s='Has mask 2' mod='customtextdesign'}"></span>
						<span class="ctd_icon ctd_replace_icon" title="{l s='Has replacement' mod='customtextdesign'}"></span>
						<span class="ctd_icon ctd_preloader" title="{l s='Loading...' mod='customtextdesign'}"></span>
						<img height="50" src="{$srcThumb|escape:'htmlall':'UTF-8'}"></a>
					{/foreach}
				</div>
				<div class="ctd_image">
					<div class="ctd_measure">
						<input id="ctd_size" type="text" size="2" title="{l s='Size (cm)' mod='customtextdesign'}">
						<a href="#" class="ctd_icon ctd_size_save" title="{l s='Save image measurement' mod='customtextdesign'}"></a>
						<a href="#" class="ctd_icon ctd_size_copy" title="{l s='Copy to unmeasured images' mod='customtextdesign'}"></a>
					</div>
					<div class="ctd_origin">
						<a href="#" class="ctd_crosshair active" title="{l s='Set origin point' mod='customtextdesign'}"></a>
						<div class="ctd_x_line"></div>
						<div class="ctd_y_line"></div>
					</div>
					<!--<img class="ctd_replace" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png"/>-->

					<img class="ctd_mask" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png"/>
					<img class="ctd_mask2" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png"/>
					{$image_type = Configuration::get('customtextdesignimage_type')}
					<img class="ctd_preview" src="{$link->getImageLink($product->link_rewrite, $images_product[0].id_image, $image_type)|escape:'htmlall':'UTF-8'}" data-id_image="{$images_product[0].id_image|intval}" alt="">
					<img class="ctd_overlay" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png"/>
				</div>
				<div class="clearBoth"></div>
				<div>
					<a href="#" class="ctd_default_img">{l s='Make as default image' mod='customtextdesign'}</a>
				</div>
				<br><br>
				<div class="ctd_replace_group ctd_upload_div">
					<h4>{l s='Replace this image' mod='customtextdesign'} ({l s='will be displayed in the design panel' mod='customtextdesign'})</h4>
					<form id="ctd_upload_form_replace" action="{$link->getAdminLink('AdminUploadModule')|strip_tags}&target=replace" method="post" target="ctd_iframe_replace" enctype="multipart/form-data">
						<input type="hidden" name="id_product" value="{$id_product|intval}" />
						<span class="ctd_title_span">{l s='Upload a replacement image' mod='customtextdesign'}:</span><br>
						<input type="file" name="admin_image">
						<div style="display:inline-block">
							<input type="submit" class="button" value="{l s='Upload' mod='customtextdesign'}">
							<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/preloader.gif" class="ctd_uploader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
						</div>
					</form>
					<div class="ctd_current_replace">
						<span>{l s='Current replacement' mod='customtextdesign'}:</span>
						<a id="ctd_current_replace" href="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" target="_blank">
							<img height="25" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
						</a>
						<a class="ctd_delete_replace" title="{l s='Delete replacement image' mod='customtextdesign'}" href="#"></a>
					</div>
					<iframe style="display:none" name="ctd_iframe_replace" id="ctd_iframe_replace"></iframe>
				</div>
				<div class="ctd_overlay_group ctd_upload_div">
					<h4>{l s='Overlay' mod='customtextdesign'}</h4>
					<form id="ctd_upload_form_overlay" action="{$link->getAdminLink('AdminUploadModule')|strip_tags}&target=overlay" method="post" target="ctd_iframe_overlay" enctype="multipart/form-data">
						<input type="hidden" name="id_product" value="{$id_product|intval}" />
						<span class="ctd_title_span">{l s='Upload an overlay image' mod='customtextdesign'}:</span><br>
						<input type="file" name="admin_image">
						<div style="display:inline-block">
							<input type="submit" class="button" value="{l s='Upload' mod='customtextdesign'}">
							<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/preloader.gif" class="ctd_uploader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
						</div>
					</form>
					<div class="ctd_current_overlay">
						<span>{l s='Current overlay' mod='customtextdesign'}:</span>
						<a id="ctd_current_overlay" href="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" target="_blank">
							<img height="25" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
						</a>
						<a class="ctd_delete_overlay" title="{l s='Delete overlay' mod='customtextdesign'}" href="#"></a>
					</div>
					<iframe style="display:none" name="ctd_iframe_overlay" id="ctd_iframe_overlay"></iframe>
				</div>

				<div class="ctd_mask_group ctd_upload_div">
					<h4>{l s='Mask' mod='customtextdesign'}</h4>
					<form id="ctd_upload_form_mask" action="{$link->getAdminLink('AdminUploadModule')|strip_tags}&target=mask" method="post" target="ctd_iframe_mask" enctype="multipart/form-data">
						<input type="hidden" name="id_product" value="{$id_product|intval}" />
						<span class="ctd_title_span">{l s='Upload a mask image' mod='customtextdesign'}:</span><br>
						<input type="file" name="admin_image">
						<div style="display:inline-block">
							<input type="submit" class="button" value="{l s='Upload' mod='customtextdesign'}">
							<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/preloader.gif" class="ctd_uploader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
						</div>
					</form>
					<div class="ctd_current_mask">
						<span>{l s='Current mask' mod='customtextdesign'}:</span>
						<a id="ctd_current_mask" href="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" target="_blank">
							<img height="25" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
						</a>
						<a class="ctd_delete_mask" title="{l s='Delete mask' mod='customtextdesign'}" href="#"></a>
					</div>
					<iframe style="display:none" name="ctd_iframe_mask" id="ctd_iframe_mask"></iframe>
				</div>

				<div class="ctd_mask_group ctd_upload_div">
					<h4>{l s='Mask 2' mod='customtextdesign'} ({l s='used only in backoffice to delimit design area' mod='customtextdesign'})</h4>
					<form id="ctd_upload_form_mask2" action="{$link->getAdminLink('AdminUploadModule')|strip_tags}&target=mask2" method="post" target="ctd_iframe_mask2" enctype="multipart/form-data">
						<input type="hidden" name="id_product" value="{$id_product|intval}" />
						<span class="ctd_title_span">{l s='Upload a mask image' mod='customtextdesign'}:</span><br>
						<input type="file" name="admin_image">
						<div style="display:inline-block">
							<input type="submit" class="button" value="{l s='Upload' mod='customtextdesign'}">
							<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/preloader.gif" class="ctd_uploader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
						</div>
					</form>
					<div class="ctd_current_mask2">
						<span>{l s='Current mask' mod='customtextdesign'}:</span>
						<a id="ctd_current_mask2" href="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" target="_blank">
							<img height="25" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
						</a>
						<a class="ctd_delete_mask2" title="{l s='Delete mask' mod='customtextdesign'}" href="#"></a>
					</div>
					<iframe style="display:none" name="ctd_iframe_mask2" id="ctd_iframe_mask2"></iframe>
				</div>
			</div>
		</div>
		<div id="ctd_tab5">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.customsize) AND $ctd_product.customsize}checked="checked"{/if} class="ctd-ajx" data-name="customsize" id="ctd_customsize"> {l s='Allow clients to enter a custom product size' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr class="ctd-toggle-customsize">
						<td valign="top" width="250"><label>{l s='Product Cost' mod='customtextdesign'} ({$currency->prefix|escape:'htmlall':'UTF-8'}{$currency->suffix|escape:'htmlall':'UTF-8'} {l s='per m²' mod='customtextdesign'}):</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_customsize_price" type="text" data-name="customsize_price" value="{if isset($ctd_product.customsize_price)}{Tools::ps_round($ctd_product.customsize_price,2)}{else}0.00{/if}" onchange="noComma('ctd_customsize_price');" class="ctd-ajx">
							<p>
							{l s='Please estimate product cost per m² to allow the module to calculate the total design price' mod='customtextdesign'}.<br>
							</p>
						</td>
					</tr>
					<tr class="ctd-toggle-customsize">
						<td valign="top" width="250"><label>{l s='Product Size Limits' mod='customtextdesign'} (cm):</label></td>
						<td style="padding-bottom:5px;" id="ctd_customsize_limits">
							<label class="ctd_fixed_label">{l s='Min Width' mod='customtextdesign'}:</label>
							<input type="text" class="ctd-ajx" data-name="customsize_minw" id="ctd_customsize_minw" value="{if isset($ctd_product.customsize_minw) AND $ctd_product.customsize_minw}{(float)$ctd_product.customsize_minw}{else}0{/if}" onchange="noComma('ctd_customsize_minw');">
							<div style="margin-bottom:3px"></div>
							<label class="ctd_fixed_label">{l s='Min Height' mod='customtextdesign'}:</label>
							<input type="text" class="ctd-ajx" data-name="customsize_minh" id="ctd_customsize_minh" value="{if isset($ctd_product.customsize_minh) AND $ctd_product.customsize_minh}{(float)$ctd_product.customsize_minh}{else}0{/if}" onchange="noComma('ctd_customsize_minh');">
							<p>{l s='Please select minimal values bigger than 0cm' mod='customtextdesign'}.</p>
							<div style="margin-bottom:10px"></div>
							<label class="ctd_fixed_label">{l s='Max Width' mod='customtextdesign'}:</label>
							<input type="text" class="ctd-ajx" data-name="customsize_maxw" id="ctd_customsize_maxw" value="{if isset($ctd_product.customsize_maxw) AND $ctd_product.customsize_maxw}{(float)$ctd_product.customsize_maxw}{else}0{/if}" onchange="noComma('ctd_customsize_maxw');">
							<div style="margin-bottom:3px"></div>
							<label class="ctd_fixed_label">{l s='Max Height' mod='customtextdesign'}:</label>
							<input type="text" class="ctd-ajx" data-name="customsize_maxh" id="ctd_customsize_maxh" value="{if isset($ctd_product.customsize_maxh) AND $ctd_product.customsize_maxh}{(float)$ctd_product.customsize_maxh}{else}0{/if}" onchange="noComma('ctd_customsize_maxh');">
							<div style="margin-bottom:10px"></div>
							<label class="ctd_fixed_label">{l s='Initial Width' mod='customtextdesign'}:</label>
							<input type="text" class="ctd-ajx" data-name="customsize_initw" id="ctd_customsize_initw" value="{if isset($ctd_product.customsize_initw) AND $ctd_product.customsize_initw}{(float)$ctd_product.customsize_initw}{else}0{/if}" onchange="noComma('ctd_customsize_initw');">
							<div style="margin-bottom:4px"></div>
							<label class="ctd_fixed_label">{l s='Initial Height' mod='customtextdesign'}:</label>
							<input type="text" class="ctd-ajx" data-name="customsize_inith" id="ctd_customsize_inith" value="{if isset($ctd_product.customsize_inith) AND $ctd_product.customsize_inith}{(float)$ctd_product.customsize_inith}{else}0{/if}" onchange="noComma('ctd_customsize_inith');">
							<p>{l s='If initial values are set to 0, minimal values will be used' mod='customtextdesign'}.</p>
							<div style="margin-bottom:10px"></div>
							<p>
								<img style="vertical-align: bottom;" src="{$module_dir|escape:'htmlall':'UTF-8'}img/help.png"/> {l s='For best results, please upload a product/replacement image cropped to fit the product' mod='customtextdesign'}.
							</p>
						</td>
					</tr>
					<tr style="border-top: 1px solid #BBB;">
						<td valign="top" width="250"><label>{l s='Product color' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.customcolor) AND $ctd_product.customcolor}checked="checked"{/if} class="ctd-ajx" data-name="customcolor" id="ctd_customcolor"> {l s='Allow clients to change product color' mod='customtextdesign'}
							</label>
							<p>
								<img style="vertical-align: bottom;" src="{$module_dir|escape:'htmlall':'UTF-8'}img/help.png"/> {l s='The module will change the product color based on the brightness of each pixel.' mod='customtextdesign'} {l s='Transparent pixels will not be affected.' mod='customtextdesign'}<br>
								<img style="vertical-align: bottom;" src="{$module_dir|escape:'htmlall':'UTF-8'}img/help.png"/> {l s='It is better to provide a white/bright product/replacement image to allow colors to apply correctly.' mod='customtextdesign'}<br>
							</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Initial Color' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_initial_color" type="text" style="vertical-align: top;" data-name="initial_color" value="{if isset($ctd_product.initial_color)}{$ctd_product.initial_color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" class="ctd-ajx">
							<a data-color="0" class="ctd_initial_color_picker" style="background-color:{if isset($ctd_product.initial_color)}{$ctd_product.initial_color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" href="#"></a>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:15px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.custompicker) AND $ctd_product.custompicker}checked="checked"{/if} class="ctd-ajx" data-name="custompicker" id="ctd_custompicker"> {l s='Show color picker' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top"><label>{l s='Available Colors' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="margin-bottom: 0px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.customcolors_all) AND $ctd_product.customcolors_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="customcolors_all" id="customcolors_all" /> {l s='Use all colors' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.customcolors)}
							{$check = explode('-',$ctd_product.customcolors)}
							{/if}
							{foreach from=$colors item=color}
							<label style="float:none">
								<input type="checkbox" {if in_array($color.id,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="customcolors" data-name="customcolors_{$color.id|intval}" /> {$color.name|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which colors can be used for this function' mod='customtextdesign'}.<br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#colors_form" target="_blank">{l s='Edit Colors' mod='customtextdesign'}</a>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ctd_tab6">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr>
						<td style="padding-bottom:5px;" colspan="2">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.custom_fields) AND $ctd_product.custom_fields}checked="checked"{/if} class="ctd-ajx" data-name="custom_fields" id="ctd_custom_fields"> {l s='Enable custom fields on this product' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td style="padding-bottom:5px;" colspan="2">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.check_bounds) AND $ctd_product.check_bounds}checked="checked"{/if} class="ctd-ajx" data-name="check_bounds" id="ctd_check_bounds"> {l s='Validate elements positions within fields' mod='customtextdesign'}
							</label>
							<p>{l s='If checked, a message will be displayed to the client if elements are outside the field' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td style="padding-bottom:5px;" colspan="2">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.required_bounds) AND $ctd_product.required_bounds}checked="checked"{/if} class="ctd-ajx" data-name="required_bounds" id="ctd_required_bounds"> {l s='Use a strict validation' mod='customtextdesign'}
							</label>
							<p>{l s='If checked, the design will not be added if elements are outside of the field' mod='customtextdesign'}</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="ctd_gallery">
				<div class="ctd_thumbs">
					{foreach from=$images_product item=image}
					{$srcThumb = $link->getImageLink($product->link_rewrite, $image.id_image, 'home_default')}
					{$image_type = Configuration::get('customtextdesignimage_type')}
					{$src = $link->getImageLink($product->link_rewrite, $image.id_image, $image_type)}
					<a href="{$src|htmlspecialchars}" data-id="{$image.id_image|intVal}" class="ctd_a_thumb">
						<span class="ctd_icon ctd_hasfields_icon" title="{l s='Has custom fields' mod='customtextdesign'}"></span>
						<span class="ctd_icon ctd_preloader" title="{l s='Loading...' mod='customtextdesign'}"></span>
						<img height="50" src="{$srcThumb|htmlspecialchars}"></a>
					{/foreach}
				</div>
				<div class="ctd_image" id="ctd_cf_container">
					<div id="ctd_cf_controls">
						<a id="ctd_add_cf" href="#ctd_tab6" title="">{l s='Add a custom field' mod='customtextdesign'}</a>
						<div class="clearBoth"></div>
						<div class="ctd_group input_lang" style="margin-top: 10px">
							<label style="display: inline;float: none;">{l s='Field label' mod='customtextdesign'}:</label><br>
							<div class="ctd_lang_container" id="ctd_cf_lang">
								{foreach $languages item=lang}
								<div class="ctd_lang">
									<input type="text" data-name="cflabel_{$lang.id_lang|intVal}" data-lang="{$lang.id_lang|intVal}" class="ctd_lang_input" size="50" >
									<img class="ctd_flag" title="{$lang.name|htmlspecialchars}" src="../img/l/{$lang.id_lang|intVal}.jpg" />
								</div>
								{/foreach}
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<img class="ctd_mask" src="{$module_dir|htmlspecialchars}img/pixel.png"/>
					<img class="ctd_mask2" src="{$module_dir|htmlspecialchars}img/pixel.png"/>
					{$image_type = Configuration::get('customtextdesignimage_type')}
					<img class="ctd_preview" src="{$link->getImageLink($product->link_rewrite, $images_product[0].id_image, $image_type)|htmlspecialchars}" data-id_image="{$images_product[0].id_image|intVal}" alt="">
					<img class="ctd_overlay" src="{$module_dir|htmlspecialchars}img/pixel.png"/>
					<div class="ctd_cf" id="ctd_cf_clone">
						<a href="#" class="ctd_cf_icon ctd_del_cf" title="{l s='Delete' mod='customtextdesign'}"></a>
						<a href="#" class="ctd_cf_icon ctd_dup_cf" title="{l s='Duplicate' mod='customtextdesign'}"></a>
						<span>label</span>
					</div>
				</div>
				<div class="clearBoth"></div>
			</div>
		</div>
		<div id="ctd_tab7">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.disable_drag) AND $ctd_product.disable_drag}checked="checked"{/if} class="ctd-ajx" data-name="disable_drag" id="ctd_disable_drag"> {l s='Disable dragging' mod='customtextdesign'}
							</label>
							<p>{l s='If checked, disables element positioning by the client' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.disable_resize) AND $ctd_product.disable_resize}checked="checked"{/if} class="ctd-ajx" data-name="disable_resize" id="ctd_disable_resize"> {l s='Disable resizing' mod='customtextdesign'}
							</label>
							<p>{l s='If checked, disables element resizing by the client' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.show_rotator) AND $ctd_product.show_rotator}checked="checked"{/if} class="ctd-ajx" data-name="show_rotator" id="ctd_show_rotator"> {l s='Show rotator' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.show_stack) AND $ctd_product.show_stack}checked="checked"{/if} class="ctd-ajx" data-name="show_stack" id="ctd_show_stack"> {l s='Show stack buttons' mod='customtextdesign'}
							</label>
							<p>{l s='Shows bring to front and send to back buttons' mod='customtextdesign'}</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.extra_btns) AND $ctd_product.extra_btns}checked="checked"{/if} class="ctd-ajx" data-name="extra_btns" id="ctd_extra_btns"> {l s='Show extra buttons' mod='customtextdesign'}
							</label>
							<p>{l s='Shows Mirror and Centered checkboxes' mod='customtextdesign'}</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="ctd_tab8">
			<table cellpadding="5" style="width:100%">
				<tbody>
					<tr>
						<td valign="top" width="250"><label>{l s='Images colors' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.imagecolor) AND $ctd_product.imagecolor}checked="checked"{/if} class="ctd-ajx" data-name="imagecolor" id="ctd_imagecolor"> {l s='Allow clients to change the image color' mod='customtextdesign'}
							</label>
							<p>
								<img style="vertical-align: bottom;" src="{$module_dir|escape:'htmlall':'UTF-8'}img/help.png"/> {l s='The module will change the image color based on the brightness of each pixel.' mod='customtextdesign'} {l s='Transparent pixels will not be affected.' mod='customtextdesign'}<br>
								<img style="vertical-align: bottom;" src="{$module_dir|escape:'htmlall':'UTF-8'}img/help.png"/> {l s='It is better to provide a white/bright image to allow colors to apply correctly.' mod='customtextdesign'}<br>
							</p>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>{l s='Initial Color' mod='customtextdesign'}:</label></td>
						<td style="padding-bottom:5px;">
							<input id="ctd_initial_img_color" type="text" style="vertical-align: top;" data-name="initial_img_color" value="{if isset($ctd_product.initial_img_color)}{$ctd_product.initial_img__color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" class="ctd-ajx">
							<a data-color="0" class="ctd_initial_img_color_picker" style="background-color:{if isset($ctd_product.initial_img_color)}{$ctd_product.initial_img_color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" href="#"></a>
						</td>
					</tr>
					<tr>
						<td valign="top" width="250"><label>&nbsp;</label></td>
						<td style="padding-bottom:15px;">
							<label style="float: none;">
								<input type="checkbox" {if isset($ctd_product.imagepicker) AND $ctd_product.imagepicker}checked="checked"{/if} class="ctd-ajx" data-name="imagepicker" id="ctd_imagepicker"> {l s='Show color picker' mod='customtextdesign'}
							</label>
						</td>
					</tr>
					<tr>
						<td valign="top"><label>{l s='Available Colors' mod='customtextdesign'}:</label></td>
						<td class="ctd_chkgroup_container" style="margin-bottom: 0px;">
							<label style="float:none">
								<input type="checkbox" {if isset($ctd_product.imagecolors_all) AND $ctd_product.imagecolors_all}checked="checked"{/if} class="ctd-ajx ctd_chkgroup_all" data-name="imagecolors_all" id="imagecolors_all" /> {l s='Use all colors' mod='customtextdesign'}
							</label><br>
							{$check = array()}
							{if isset($ctd_product.imagecolors)}
							{$check = explode('-',$ctd_product.imagecolors)}
							{/if}
							{foreach from=$colors item=color}
							<label style="float:none">
								<input type="checkbox" {if in_array($color.id,$check)}checked="checked"{/if} class="ctd_chkgroup" data-group="imagecolors" data-name="imagecolors_{$color.id|intval}" /> {$color.name|escape:'htmlall':'UTF-8'}
							</label><br>
							{/foreach}
							<p>
								{l s='Which colors can be used for this function' mod='customtextdesign'}.<br>
								<a href="{$edit_url|escape:'htmlall':'UTF-8'}&configure=customtextdesign#colors_form" target="_blank">{l s='Edit Colors' mod='customtextdesign'}</a>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="separation"></div>
	<div class="ctd_group">
		<a href="{$product_link|escape:'htmlall':'UTF-8'}" target="_preview">
			<img src="../img/admin/details.gif" style="position: relative;top: -2px;"/>
			<span>{l s='Preview Product' mod='customtextdesign'}</span>
		</a>
	</div>
</div>

<a href="#" title="{l s='Reload this tab' mod='customtextdesign'}" onclick="$('#product-tab-content-ModuleCustomtextdesign').addClass('not-loaded');tabs_manager.display('ModuleCustomtextdesign',0);return false;"><img src="{$module_dir|escape:'htmlall':'UTF-8'}img/reload.png"/></a>
<script type="text/javascript">
	ctd_admin.init();
	$( "#ctd_ui_tabs" ).tabs();
	$('.ctd_chkgroup_all').each(function(){
		ctd_admin.checkGroupGlobal(this);
	});
</script>