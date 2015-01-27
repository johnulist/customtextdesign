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

<!-- Custom Text Design Module -->
<div class="dbg"></div>
<script type="text/javascript" id="ctd_script">
	var customtextdesign_config = {
	{foreach $customtextdesign_config key=k item=value}
	"{$k|htmlspecialchars}":"{$value|htmlspecialchars}",
	{/foreach}
	"module_dir":"{$module_dir|htmlspecialchars}"
	};

	var customtextdesign_price = {
	{foreach $materials item=material}
	"{$material.id|intval}":"{$material.price|htmlspecialchars}",
	{/foreach}
	};

	var customtextdesign_category = {$customtextdesign_category|intval};
	var customtextdesign_id_product = {$id_product|intval};
	var ctd_config = JSON.parse('{$ctd_config|json_encode}');
	var customtextdesign_measures =	('{$measures|json_encode}');
	var customtextdesign_overlays =	('{$overlays|json_encode}');
	var customtextdesign_masks =	('{$masks|json_encode}');
	var customtextdesign_replaces =	('{$replaces|json_encode}');
	var customtextdesign_width = ('{$original_width|json_encode}');
	var customtextdesign_fields = ('{$custom_fields|json_encode}');
	var customtextdesign_attributes = '{$ctd_product.attributes|htmlspecialchars}'.split('-');
	var customtextdesign_delete = "{l s='Delete' mod='customtextdesign' js=1}";
	var customtextdesign_addcart = "{l s='Add to cart' mod='customtextdesign' js=1}";
	var customtextdesign_ttc = "{l s='taxes included' mod='customtextdesign' js=1}";
	var customtextdesign_hc = "{l s='taxes excluded' mod='customtextdesign' js=1}";
	var customtextdesign_delete_confirm = "{l s='Are you sure you want to delete this element?' mod='customtextdesign' js=1}";
	var customtextdesign_preview = "{l s='Preview' mod='customtextdesign' js=1}";
	var customtextdesign_front = "{l s='Bring to front' mod='customtextdesign' js=1}";
	var customtextdesign_back = "{l s='Send to back' mod='customtextdesign' js=1}";
	var customtextdesign_rotate = "{l s='Rotate (press SHIFT to make rotation easier)' mod='customtextdesign' js=1}";
	var customtextdesign_resize = "{l s='Resize (press SHIFT to maintain aspect ratio)' mod='customtextdesign' js=1}";
	var customtextdesign_usertitle = "{l s='User Upload' mod='customtextdesign' js=1}";
	{if $ctd_product.show_price}
	var customtextdesign_userprice = " - ({Tools::displayprice(Tools::convertprice($ctd_product.upload_price))|htmlspecialchars} {l s='per m²' mod='customtextdesign' js=1})";
	{else}
	var customtextdesign_userprice = "";
	{/if}
	var customtextdesign_design_total = "{l s='Design Total' mod='customtextdesign' js=1}";
	var customtextdesign_product_price = "{l s='Product Price' mod='customtextdesign' js=1}";
	var customtextdesign_total = "{l s='Total' mod='customtextdesign' js=1}";
	var customtextdesign_total_wt = "{l s='Total' mod='customtextdesign' js=1}";
	var customtextdesign_error = {ldelim}
		'1' : "{l s='An error has occured !' mod='customtextdesign' js=1}",
		'empty' : "{l s='Please add some items first.' mod='customtextdesign' js=1}",
		'nomeasure' : "{l s='No measure found for this product! Please choose another option.' mod='customtextdesign' js=1}",
		'noattr' : "{l s='Please choose another combination!' mod='customtextdesign'}",
		'max_length' : "{l s='The number of characters exceeds _MAX_ characters! Please remove _REM_ character(s).' mod='customtextdesign' js=1}",
		'no_img_url' : "{l s='Please enter an image url' mod='customtextdesign' js=1}",
		'in_cart' : "{l s='This customization is already added to your cart' mod='customtextdesign' js=1}",
		'not_valid_url' : "{l s='Please enter a valid image url' mod='customtextdesign' js=1}",
		'minwidth' : "{l s='The product width must be between' mod='customtextdesign' js=1} {(float)$ctd_product.customsize_minw|floatval}cm {l s='and' mod='customtextdesign' js=1} {(float)$ctd_product.customsize_maxw|floatval}cm",
		'maxwidth' : "{l s='The product width must be less than' mod='customtextdesign' js=1} {$ctd_product.customsize_maxw|htmlspecialchars} cm",
		'minheight' : "{l s='The product height must be bigger than' mod='customtextdesign' js=1} {$ctd_product.customsize_minh|htmlspecialchars} cm",
		'maxheight' : "{l s='The product width must be less than' mod='customtextdesign' js=1} {$ctd_product.customsize_maxh|htmlspecialchars} cm",
		'outbound' : "{l s='There are elements outside the design area! Please move the highlighted element and retry.' mod='customtextdesign' js=1}"
	{rdelim}
	var customtextdesign_message = {ldelim}
		'in_cart' : "{l s='This customization was successfully added to your cart' mod='customtextdesign' js=1}",
		'outbound' : "{l s='There are elements outside the design area! Are you sure you want to continue?' mod='customtextdesign' js=1}"
	{rdelim}
	var customtextdesign_customsize = {$ctd_product.customsize|intval};
	var customtextdesign_customcolor = {$ctd_product.customcolor|intval};
	var customtextdesign_minw = {$ctd_product.customsize_minw|floatval};
	var customtextdesign_maxw = {$ctd_product.customsize_maxw|floatval};
	var customtextdesign_minh = {$ctd_product.customsize_minh|floatval};
	var customtextdesign_maxh = {$ctd_product.customsize_maxh|floatval};
	var customtextdesign_initw = {$ctd_product.customsize_initw|floatval};
	var customtextdesign_inith = {$ctd_product.customsize_inith|floatval};
	var customtextdesign_initial_color = "{if $ctd_product.initial_color}{$ctd_product.initial_color|htmlspecialchars}{else}#11daf5{/if}";
	var customtextdesign_initial_img_color = "{if $ctd_product.initial_img_color}{$ctd_product.initial_img_color|htmlspecialchars}{else}#11daf5{/if}";
	{if $ctd_product.id_default_img}
	if(typeof idDefaultImage == 'undefined') idDefaultImage = "{$ctd_product.id_default_img|intval}";
	{/if}
	var customtextdesign_min_size = {$ctd_product.min_size|intVal};
	var customtextdesign_initial_curve = {$ctd_product.initial_curve|intVal};
	var customtextdesign_max_length = {$ctd_product.max_length|intVal};
	var customtextdesign_download = "{$customtextdesign_download|stripslashes}";
	var customtextdesign_popup = {$ctd_product.popup|intval};
	var customtextdesign_attributes_all = {$ctd_product.attributes_all|intval};
	var customtextdesign_custom_fields = {$ctd_product.custom_fields|intVal};
	var customtextdesign_disable_drag = {$ctd_product.disable_drag|intVal};
	var customtextdesign_disable_resize = {$ctd_product.disable_resize|intVal};
	var customtextdesign_show_rotator = {$ctd_product.show_rotator|intVal};
	var customtextdesign_check_bounds = {$ctd_product.check_bounds|intVal};
	var customtextdesign_required_bounds = {$ctd_product.required_bounds|intVal};
	var customtextdesign_required = {$ctd_product.required|intVal};
	var customtextdesign_active = 1;
	var customtextdesign_initialized = 0;
</script>
<style type="text/css">
	{if !$ctd_product.show_price}
		.ctd_extra_details{
			display:none !important;
			visibility: hidden !important;
		}
	{/if}
</style>
{if $ctd_product.popup}
	<div class="ctd_bg ctd_no_show"></div>
{/if}
{if isset($customtextdesign_config["instructions_{$default_lang|intval}"])}
	{$instructions = $customtextdesign_config["instructions_{$default_lang|intval}"]}
	{if $instructions}
		<p class="alert alert-info warning">{$instructions|ucfirst}</p>
	{/if}
{/if}
<div class="ctd_product_panel {if ! $ctd_product.expanded}collapsed{/if} {if $ctd_product.popup}ctd_popup ctd_no_show{/if}" style="display:none" id="ctd_panel">
	{$title = ''}
	{if isset($customtextdesign_config["title_{$default_lang|intval}"])}
		{$title = $customtextdesign_config["title_{$default_lang|intval}"]}
	{/if}
	{if $ctd_product.popup}
	<a href="#" class="ctd_close_popup" title="{l s='Close' mod='customtextdesign'}"></a>
	{/if}
	<div class="ctd_panel_title">
		<a href="#" class="ctd_toggle_panel">{if strlen($title)}{$title|htmlspecialchars}{else}{l s='Customize this product' mod='customtextdesign'}{/if}</a>
	</div>
	<div id="customtextdesign" class="ctd_panel_content">
		<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/loader.gif" class="ctd_loader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">

		<div class="ctd_preview">
			<img class="ctd_img_mask2 ctd_prv" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
			<img class="ctd_img ctd_prv" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
			<img class="ctd_img_mask ctd_prv" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
			<img class="ctd_img_overlay ctd_prv" src="{$module_dir|escape:'htmlall':'UTF-8'}img/pixel.png" />
			<div class="ctd_cf" id="ctd_cf_clone">
				<span>label</span>
			</div>
			<div class="ctd_details"><div class="ctd_prices"></div></div>
			{if $ctd_product.show_rotator}
			<div class="ctd_rotator_container">
				<span id="ctd_rotator_value">0°</span>
				<div class="ctd_rotator_buttons">
					<a href="#" class="ctd_rotator_btn" data-angle="0"	>0°</a>
					<a href="#" class="ctd_rotator_btn" data-angle="90"	>90°</a>
					<a href="#" class="ctd_rotator_btn" data-angle="180">180°</a>
					<a href="#" class="ctd_rotator_btn" data-angle="270">270°</a>
				</div>
				<div id="ctd_rotator" title="{l s='Use SHIFT key to change the step to 45 degrees' mod='customtextdesign'}"></div>
			</div>
			{else}
			<style type="text/css">
				{literal}.ui-rotatable-handle{display: none !important}{/literal}
			</style>
			{/if}
		</div>

		{if !$ctd_product.show_stack}
			<style type="text/css">
				{literal}.ft-front{display: none !important} .ft-back{display: none !important}{/literal}
			</style>
		{/if}

		<div class="ctd_add_to_cart">
			{if isset($color_list) && count($color_list)}
			<div class="ctd_color_list">
				<ul id="ctd_color_list">
				{foreach from=$color_list key=id_attribute item=color}
					{assign var='img_color_exists' value=file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
					<li>
						<a href="#" data-id="{$id_attribute|intval}" id="ctd_color_{$id_attribute|intval}" style="background-color: {$color.value|escape:'htmlall':'UTF-8'};" title="{$color.name|escape:'htmlall':'UTF-8'}">
							{if $img_color_exists}
								<img src="{$img_col_dir}{$id_attribute|intval}.jpg" alt="{$color.name|escape:'html':'UTF-8'}" title="{$color.name|escape:'html':'UTF-8'}" width="20" height="20" />
							{/if}
						</a>
					</li>
				{/foreach}
				</ul>
			</div>
			{/if}
			{if $ctd_product.show_download_btn}
				<a href="#" class="button ctd_download_btn" style="margin-bottom: 3px;">{l s='Download as Image' mod='customtextdesign'}</a>
			{/if}
			{if $ctd_product.show_price}
			<a href="#" class="button ctd_show_price">{l s='Show prices' mod='customtextdesign'}</a>
			{else}
			<a href="#" class="button ctd_add_cart">{l s='Add to cart' mod='customtextdesign'}</a>
			{/if}
		</div>
		<div class="ctd_details_buttons">
			<a href="#" class="button green ctd_back_design" style="margin-bottom: 3px;">{l s='Back to Design' mod='customtextdesign'}</a>
			<a href="#" class="button green ctd_add_cart">{l s='Add to cart' mod='customtextdesign'}</a>
		</div>

		<div class="ctd_design">
			{$show_images = ! empty($ctd_product.image_groups) OR $ctd_product.upload OR $ctd_product.url_upload OR $ctd_product.image_groups_all}
			{$images_first = $ctd_product.images_first && $show_images}
			{$clicked = false}
			<div class="ctd_design_title" title="{l s='Drag to move' mod='customtextdesign'}">
				{if $ctd_product.customsize OR $ctd_product.customcolor}
				<a href="#" class="ctd_a_dimension {if !$images_first}{$clicked = true}active{/if}" rel="ctd_dimensions" title="{l s='Product settings' mod='customtextdesign'}"></a>
				{/if}
				{if ! $ctd_product.hide_text}
				<a href="#" class="ctd_a_text {if !$images_first && !$clicked}{$clicked = true}active{/if}" rel="ctd_text" title="{l s='Text' mod='customtextdesign'}"></a>
				{/if}
				{if ! empty($ctd_product.image_groups) OR $ctd_product.upload OR $ctd_product.image_groups_all}
				<a href="#" class="ctd_a_image {if $images_first OR !$clicked}{$clicked = true}active{/if}" rel="ctd_images" title="{l s='Image' mod='customtextdesign'}"></a>
				{/if}
				<a href="#" class="ctd_a_help {if !$clicked}{$clicked = true}active{/if}" rel="ctd_help" title="{l s='Help' mod='customtextdesign'}"></a>
			</div>
			<div class="ctd_design_content">
				{$clicked = false}
				{if $ctd_product.customsize OR $ctd_product.customcolor}
				<div class="ctd_dimensions" {if !$images_first}{$clicked = true}style="display:block;"{/if}>
					<div class="ctd_customsize_controls">
						{if $ctd_product.customsize}
						<label class="ctd_label">{l s='Width' mod='customtextdesign'} (cm):</label>
						<input type="text" class="text" id="ctd_customsize_width" onchange="noComma(this);" value="{if (float)$ctd_product.customsize_initw}{(float)$ctd_product.customsize_initw}{else}{(float)$ctd_product.customsize_minw}{/if}"/>
						<div class="clr" style="margin-bottom:10px"></div>
						<label class="ctd_label">{l s='Height' mod='customtextdesign'} (cm):</label>
						<input type="text" class="text" id="ctd_customsize_height" onchange="noComma(this);" value="{if (float)$ctd_product.customsize_inith}{(float)$ctd_product.customsize_inith}{else}{(float)$ctd_product.customsize_minh}{/if}" />
						<div class="clr" style="margin-bottom:10px"></div>
						{/if}
						{if $ctd_product.customcolor}
						<input type="hidden" id="ctd_customcolor_data">
						<label style="vertical-align: top;" class="ctd_label">{l s='Color' mod='customtextdesign'}:</label>
						{if $ctd_product.custompicker}
						<input type="text" class="text" id="ctd_customcolor_input" value="{if $ctd_product.initial_color}{$ctd_product.initial_color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" style="vertical-align: top;"/>
						<a data-color="0" data-customcolor="" class="ctd_customcolor" style="background-color:{if $ctd_product.initial_color}{$ctd_product.initial_color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" href="#"></a>
						{/if}
						{$check = explode('-',$ctd_product.customcolors)}
						{$ctd_customcolors = array()}
						{foreach $colors item=color}
						{if in_array($color.id,$check) OR $ctd_product.customcolors_all}
							{$ctd_customcolors[] = $color}
						{/if}
						{/foreach}
						<ul id="ctd_customcolor">
							{foreach $colors item=color}
							{if $color.displayed && (in_array($color.id,$check) OR $ctd_product.customcolors_all)}
							{if $color.is_color}
							<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="1" href="#" style="background-color:{$color.color|escape:'htmlall':'UTF-8'}"></a></li>
							{else}
							<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="0" href="#" style="background-image:url('{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($color.texture, 32, 32,'texture')}')"></a></li>
							{/if}
							{/if}
							{/foreach}
						</ul>

						{/if}
						<div style="text-align: right;width: 214px;">
							<a href="#" class="button ctd_customsize_save">{l s='Apply' mod='customtextdesign'}</a>
						</div>
					</div>
				</div>
				{/if}
				{if ! $ctd_product.hide_text}
				<div class="ctd_text" {if $images_first OR $clicked}style="display:none"{else}{$clicked = true}style="display:block"{/if}>
					<textarea id="ctd_text" {if intVal($ctd_product.max_length)}maxlength="{$ctd_product.max_length|intVal}"{/if} class="autogrow" rows="1">{$customtextdesign_config["text_init_{$default_lang}"]|htmlspecialchars}</textarea>

					{$check = explode('-',$ctd_product.fonts)}
					{$ctd_fonts = array()}
					{foreach $fonts item=font}
					{if in_array($font.id,$check) OR $ctd_product.fonts_all}
						{$ctd_fonts[] = $font}
					{/if}
					{/foreach}
					{if $ctd_product.hide_fonts}
					<style type="text/css">{literal}#ctd_font{display: none}{/literal}</style>
					{/if}
					<select id="ctd_font" title="{l s='Font' mod='customtextdesign'}" >
						{foreach $fonts item=font}
						{if $font.displayed AND (in_array($font.id,$check) OR $ctd_product.fonts_all)}
						<option data-font="{$font.id|intval}" data-imagesrc="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::render($font.name, $font.id, $customtextdesign_config.font_color)}" data-description="{$font.id|intval}" value="{$font.id|intval}"></option>
						{/if}
						{/foreach}
					</select>

					{$check = explode('-',$ctd_product.materials)}
					{$ctd_materials = array()}
					{foreach $materials item=material}
					{if in_array($material.id,$check) OR $ctd_product.materials_all}
						{$ctd_materials[] = $material}
					{/if}
					{/foreach}
					<select id="ctd_material" title="{l s='Material' mod='customtextdesign'}" style="{if !count($ctd_materials) || $ctd_product.hide_materials} display: none;{/if}">
						{foreach $materials item=material}
						{if in_array($material.id,$check) OR $ctd_product.materials_all}
						<option value="{$material.id|intval}">{$material.name|ucfirst|escape:'htmlall':'UTF-8'}{if $customtextdesign_config.show_prices}  - ({Tools::displayprice(Tools::convertprice($material.price))|escape:'htmlall':'UTF-8'} {l s='per m²' mod='customtextdesign'}){/if}</option>
						{/if}
						{/foreach}
					</select>

					{$check = explode('-',$ctd_product.colors)}
					{$ctd_colors = array()}
					{foreach $colors item=color}
					{if in_array($color.id,$check) OR $ctd_product.colors_all}
						{$ctd_colors[] = $color}
					{/if}
					{/foreach}
					<ul id="ctd_color" {if $ctd_product.hide_colors}style="display:none"{/if}>
						{foreach $colors item=color}
						{if $color.displayed && (in_array($color.id,$check) OR $ctd_product.colors_all)}
						{if $color.is_color}
						<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="1" href="#" style="background-color:{$color.color|escape:'htmlall':'UTF-8'}"></a></li>
						{else}
						<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="0" href="#" style="background-image:url('{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($color.texture, 32, 32,'texture')}')"></a></li>
						{/if}
						{/if}
						{/foreach}
						{if $ctd_product.picker}
						<li title="{l s='Color picker' mod='customtextdesign'}"><a data-color="0" class="ctd_picker" href="#"></a></li>
						{else}
						<a style="display:none;background-color:#FFF" data-color="0" class="ctd_picker" href="#"></a>
						{/if}
					</ul>

					{if  $ctd_product.curve}
					<div class="ctd_curve">
						<label style="float:left" class="ctd_label">{l s='Curvature' mod='customtextdesign'}:</label>
						<span class="ctd_slider_value" id="ctd_curve_slider_value">{$ctd_product.initial_curve|intVal}%</span>
						<a class="ctd_slider_reset" id="ctd_curve_reset" href="#">{l s='reset' mod='customtextdesign'}</a>
						<div class="clr"></div>
						<div id="ctd_curve_slider"></div>
					</div>
					{/if}

					{if $ctd_product.alpha}
					<div class="ctd_alpha">
						<label style="float:left" class="ctd_label">{l s='Transparency' mod='customtextdesign'}:</label>
						<span class="ctd_slider_value" id="ctd_alpha_slider_value">0%</span>
						<a class="ctd_slider_reset" id="ctd_alpha_reset" href="#">{l s='reset' mod='customtextdesign'}</a>
						<div class="clr"></div>
						<div id="ctd_alpha_slider"></div>
					</div>
					{/if}
					{if $ctd_product.extra_btns}
					<label class="ctd_label" for="ctd_mirror"><input type="checkbox" id="ctd_mirror" /> {l s='Mirror' mod='customtextdesign'}</label>
					<label class="ctd_label" for="ctd_center" title="{l s='Centered multiline text' mod='customtextdesign'}"><input type="checkbox" id="ctd_center" /> {l s='Centered' mod='customtextdesign'}</label>
					{/if}
					<div id="ctd_buttons">
						<a class="ctd_button" id="ctd_apply" title="{l s='Apply to selected text' mod='customtextdesign'}" href="#">{l s='Apply' mod='customtextdesign'}</a>
						<a class="ctd_button" id="ctd_add" title="{l s='Add a new text' mod='customtextdesign'}" href="#">{l s='Add' mod='customtextdesign'}</a>
					</div>
				</div>
				{/if}
				{if ! empty($ctd_product.image_groups) OR $ctd_product.upload OR $ctd_product.image_groups_all}
				<div class="ctd_images" style="min-height:200px;{if $images_first OR !$clicked}{$clicked = true}display:block;{/if}">
					<div class="ctd_images_container">
						{if $ctd_product.upload OR $ctd_product.url_upload}
						<img src="{$module_dir|escape:'htmlall':'UTF-8'}img/loader.gif" class="ctd_uploader" title="{l s='loading..' mod='customtextdesign'}" alt="{l s='loading..' mod='customtextdesign'}">
						<div class="ctd_img_send_container">
							{if $ctd_product.upload}
							<a href="#" class="ctd_img_btn" id="ctd_img_upload" style="margin-bottom:2px">{l s='Upload image' mod='customtextdesign'}</a>
							{/if}
							{if $ctd_product.url_upload}
							<a href="#" class="ctd_img_btn" id="ctd_img_url" style="margin-bottom:2px">{l s='Add from url' mod='customtextdesign'}</a>
							{/if}
							<div class="clr"></div>
							{if $ctd_product.upload}
							<form id="ctd_upload" action="{$module_dir|escape:'htmlall':'UTF-8'}inc/userupload.php" method="post" target="ctd_iframe" enctype="multipart/form-data">
								<input type="hidden" name="id_product" value="{$id_product|intval}" />
								<span>{l s='Upload an image' mod='customtextdesign'}:</span>
								<input type="file" name="user_image" style="display: block;margin: 10px 0px;">
								<input type="submit" class="button" value="{l s='Upload' mod='customtextdesign'}">
								<input type="submit" class="button ctd_img_cancel" value="{l s='Return' mod='customtextdesign'}">
							</form>
							{/if}
							{if $ctd_product.url_upload}
							<form id="ctd_url" action="{$module_dir|escape:'htmlall':'UTF-8'}inc/userupload.php" method="post">
								<input type="hidden" name="id_product" value="{$id_product|intval}" />
								<span>{l s='Image URL' mod='customtextdesign'}:</span>
								<input type="text" id="ctd_url_input" name="ctd_url_input" placeholder="http://" onclick="this.select()">
								<input type="submit" class="button" value="{l s='Add image' mod='customtextdesign'}">
								<input type="submit" class="button ctd_img_cancel" value="{l s='Return' mod='customtextdesign'}">
							</form>
							{/if}
						</div>
						{/if}
						{$check = explode('-',$ctd_product.image_groups)}
						{$ctd_image_groups = array()}
						{foreach $image_groups item=image_group}
						{if in_array($image_group.id,$check) OR $ctd_product.image_groups_all}
							{$ctd_image_groups[] = $image_group}
						{/if}
						{/foreach}
						<div class="ctd_img_container">
						<div style="margin:5px"><a class="ctd_close_image_group" href="#" title="{l s='Back' mod='customtextdesign'}"></a></div>
						{$group_colorize = array()}
						{foreach $image_groups item=image_group}
						{if (int)$image_group.colorize}{$group_colorize[] = (int)$image_group.id}{/if}
						{if $image_group.displayed AND $image_group.image_total != 0 AND (in_array($image_group.id,$check) OR $ctd_product.image_groups_all)}
						{$src = "{$module_dir|escape:'htmlall':'UTF-8'}data/group/{$image_group.file|escape:'htmlall':'UTF-8'}"}
						<a href="#" class="ctd_image_group" data-id_image_group="{$image_group.id|intval}">
							<img data-src="{$src}" src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($image_group.file, 0, 50, 'group')}" style="max-height:50px" />
							<label>{$image_group.name|escape:'htmlall':'UTF-8'}</label>
						</a>
						{/if}
						{/foreach}
						{foreach $ctd_images item=image}
						{if $image.displayed && $image.quantity != 0}
						{$src = "{$module_dir|escape:'htmlall':'UTF-8'}data/image/{$image.file|escape:'htmlall':'UTF-8'}"}
						<a href="#" title="{$image.name|escape:'htmlall':'UTF-8'}" class="ctd_image ctd_group_{$image.id_group|intval}">
							<img data-src="{$src|escape:'htmlall':'UTF-8'}" data-id_image="{$image.id|intval}" src="{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($image.file, 0, 50, 'image', 'auto', in_array((int)$image.id_group, $group_colorize))}" style="max-height:50px" />
						</a>
						{/if}
						{/foreach}
						<div style="margin:5px"><a class="ctd_close_image_group" href="#" title="{l s='Back' mod='customtextdesign'}"></a></div>
						</div>
					</div>
					{if $ctd_product.imagecolor}
					<div id="ctd_imagecolor_div" style="margin-left: 5px;">
						<label>{l s='Image color' mod='customtextdesign'}:</label>
						{$check = explode('-',$ctd_product.imagecolors)}
						{$ctd_img_colors = array()}
						{foreach $colors item=color}
						{if in_array($color.id,$check) OR $ctd_product.imagecolors_all}
							{$ctd_img_colors[] = $color}
						{/if}
						{/foreach}
						<ul id="ctd_imagecolor">
							<li title="{l s='Original Color' mod='customtextdesign'}"><a data-color="0" class="ctd_original_imagecolors" data-iscolor="1" href="#" style="background-color:#fff"></a></li>
							{foreach $colors item=color}
							{if $color.displayed && (in_array($color.id,$check) OR $ctd_product.imagecolors_all)}
							{if $color.is_color}
							<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="1" href="#" style="background-color:{$color.color|escape:'htmlall':'UTF-8'}"></a></li>
							{else}
							<li title="{$color.name|escape:'htmlall':'UTF-8'}"><a data-color="{$color.id|intval}" data-iscolor="0" href="#" style="background-image:url('{$module_dir|escape:'htmlall':'UTF-8'}data/cache/{CustomImage::thumb($color.texture, 32, 32,'texture')}')"></a></li>
							{/if}
							{/if}
							{/foreach}
							{if $ctd_product.imagepicker}
								<li title="{l s='Color picker' mod='customtextdesign'}"><a data-color="0" style="background-color:{if $ctd_product.initial_img_color}{$ctd_product.initial_img_color|escape:'htmlall':'UTF-8'}{else}#11daf5{/if}" class="ctd_imagepicker" href="#"></a></li>
							{else}
								<a style="display:none;background-color:#FFF" data-color="0" class="ctd_imagepicker" href="#"></a>
							{/if}
						</ul>
					</div>
					{/if}
				</div>
				{/if}
				<div class="ctd_help" {if !$clicked}style="display:block;"{/if}>
					<p>- {l s='You can use the SHIFT key to preserve the element aspect ratio and to make rotation easier.' mod='customtextdesign'}</p>
					{if $ctd_product.show_stack}
					<p>- {l s='You can use Bring to front' mod='customtextdesign'} <a title="{l s='Bring to front' mod='customtextdesign'}" class="ft-front ft-static"></a>  {l s='and' mod='customtextdesign'} {l s=' Send to back' mod='customtextdesign'} <a title="{l s='Send to back' mod='customtextdesign'}" class="ft-back ft-static"></a> {l s=' to control the stack order of elements.' mod='customtextdesign'}</p>
					{/if}
				</div>
			</div>
		</div>
		<iframe style="display:none" name="ctd_iframe" id="ctd_iframe"></iframe>
		<div class="clr"></div>
	</div>
</div>
<!-- /Custom Text Design Module -->