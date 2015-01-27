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
	var ctd_tab_text = '{l s='Product Customization' mod='customtextdesign'}';
	var ctd_tab_link = '{$module_dir|htmlspecialchars}backoffice_tab.php?id_product={$id_product|intval}&id_lang={$id_lang|intval}&token={$token|htmlspecialchars}';
	var ctd_link = '{$link->getAdminLink('AdminAjaxModule')|strip_tags}';
	var ctd_langs = [
		{foreach $languages item=lang}
			{ldelim}
				id_lang : "{$lang.id_lang|htmlspecialchars}",
				name : "{$lang.name|htmlspecialchars}",
				active : "{$lang.active|htmlspecialchars}",
				iso_code : "{$lang.iso_code|htmlspecialchars}"
			{rdelim},
		{/foreach}
	];
	{if isset($token)}
	var token = '{$token|htmlspecialchars}';
	{/if}
</script>
