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

	var ctd_img_src = '{$module_dir|htmlspecialchars}img/logo.png';
	var ctd_ids = [
		{foreach $ctd_ids item=ctd_id}
			{$ctd_id.id_product|intval},
		{/foreach}
	];
	$(function(){

		var $ctd_table = $('#product');
		if( ! $ctd_table.length){
			$ctd_table = $('#table-product');
		}
		if( ! $ctd_table.length) return;
		$.each(ctd_ids,function(i,ctd_id){
			var $checkbox = $ctd_table.find('input[type=checkbox][value='+ ctd_id +']');
			if( ! $checkbox.length){
				$tr = $ctd_table.find('tr[id]');
				if($tr.length == 1 && $tr.attr('id').indexOf('_' + ctd_id + '_') > -1){
					$checkbox = $tr.find('td:eq(0)');
				}else{
					return true;
				}
			};

			var $titleCell = $checkbox.closest('tr').find('td:eq(3)');
			var $img = $('<img>',{
				src : ctd_img_src,
				title : '{l s='Product Customization is active for this product' mod='customtextdesign'}'
			}).prependTo($titleCell);

		});

	});
</script>
