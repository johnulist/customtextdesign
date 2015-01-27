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
<script type="text/javascript">
	$('#order-detail-content tr.item td:nth-child(2) label[for]').each(function(){
		var id_order_detail = $(this).prop('for').replace('cb_','');
		var $details = $('.ctd_order_custom_product#id_order_detail_' + id_order_detail).insertAfter(this);
	});
</script>

<style type="text/css">
.ctd_order_custom_product {
	border: 1px solid #A8A7A7;
	padding: 5px;
}
.ctd_order_custom_product hr {
	border: none;
	border-bottom: 1px solid #A8A7A7;
}
</style>

<!-- /Custom Text Design Module -->