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

{if $is_ps15}
<p id="ctd_customize_button15" class="buttons_bottom_block">
	<span></span>
	<input type="submit" name="Submit" value="{l s='Customize' mod='customtextdesign'}" class="exclusive ctd_customize_button">
</p>
{/if}
{if $is_ps16}
<p class="buttons_bottom_block no-print">
	<a id="ctd_customize_button" class="ctd_customize_button" href="#" rel="nofollow" title="{l s='Customize' mod='customtextdesign'}">
		{l s='Customize' mod='customtextdesign'}
	</a>
</p>
{/if}