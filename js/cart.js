	/**
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
	*/

$(function(){

	$('.ctd_order_custom_product.ctd_level_1').each(function(){
		var $custom_product = $(this);
		var id = $(this).prop('id');
		var id_custom_product = $(this).data('id');

		var attr = id.replace('product_','');

		function ctdAddCustomizations(){
			var tr_id = $(this).prop('id');
			var values = tr_id.split('_');
			var id_product = values[1];
			var id_product_attribute = values[2];
			var $details = $custom_product;

			$li = $(this).find('ul.typedText li:contains("['+id_custom_product+']")');
			if($li.length){
				$li.text($li.text().replace('['+id_custom_product+']',''));
				$details.appendTo($li).show();
			}else{
				return;
			}

			var $img = $(this).find('td.cart_product img');
			if($img.get(0) && $img.get(0).complete){
				var w = $img.width();
				var h = $img.height();
				$img.css({width: w, height: h}).prop('src',$details.find('img.ctd_preview_img').prop('src'));
			}else{
				$img.on('load',function(){
					var w = $img.width();
					var h = $img.height();
					$(this).css({width: w, height: h}).prop('src',$details.find('img.ctd_preview_img').prop('src'));
					$(this).off('load');
				});
			}
		}

		$('#cart_summary tr[class^=product_customization_for_'+attr+']').each(ctdAddCustomizations);
		$('#cart_summary tr.cart_item[id^='+id+'_]').each(ctdAddCustomizations);
	});

	$('.ctd_order_custom_product.ctd_level_0').each(function(){
		var id = $(this).prop('id');
		$('#cart_summary tr[id^=product_]').each(function(){
			var tr_id = $(this).prop('id');
			var values = tr_id.split('_');
			var id_product = values[1];
			var id_product_attribute = values[2];
			$(this).find('span[style="text-decoration:line-through;"]').hide().next('br').hide();
			$(this).find('.old-price').hide();
			$(this).find('.price-percent-reduction').hide();
			$(this).find('.special-price').removeClass('special-price');
			var $details = $('.ctd_order_custom_product#product_'+id_product+'_'+id_product_attribute);
			$details.appendTo($(this).find('td.cart_description')).show();

			$details.each(function(){
				$this = $(this);
				var dh = $this.height();
				var bh = $this.data('height');
				if(bh) return true;
				$this.data('height',dh);
				var $header = $this.find('.ctd_design_header');
				var rh = $header.height();
				$this.css('height',rh + 'px');
				$header.off('click').on('click',function(){
					$details = $(this).closest('.ctd_order_custom_product');
					var dh = $details.height();
					var ddh = $details.data('height');
					if(dh < ddh){
						$details.css('height','auto');
					}else{
						rh = $(this).height();
						$details.css({height:rh});
					}
				});
			});


			var $img = $(this).find('td.cart_product img');
			$img.on('load',function(){
				var w = $img.width();
				var h = $img.height();
				$(this).css({width: w, height: h}).prop('src',$details.find('img.ctd_preview_img').prop('src'));
				$(this).off('load');
			});
		});
	});

});