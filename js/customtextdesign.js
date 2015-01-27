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

var ctd_interval = null;
var ctd_interval_count = 0;
var ctd_interval_first = 0;

$(function(){

	try{
		if(window != top){
			var autoIframeHeight = function(){
				var url = location.href;
				$(top.jQuery.find('iframe[src="'+ url +'"]')).css('height', $('body').height()+50);
			}
			$(window).on('resize',autoIframeHeight);
			autoIframeHeight();
		}
	}catch(ex){}

	var preview = {
		on : 0,
		first : 1,
		text : '',
		color: 0,
		iscolor: 0,
		bg: 0,
		font:0,
		size:0,
		material:0,
		price:0,
		mirror:0,
		width:0,
		height:0,
		rwidth:0,
		qty:0,
		category:customtextdesign_category,
		cache:'',
		action:'saveproduct',
		token: static_token,
		id_page_config: id_page_config,
	};

	Number.prototype._toFixed = function(precision){
		return ps_round(this,precision);
	}

	function _parseInt(n){
		return isNaN(parseInt(n))?0:parseInt(n);
	}

	function _parseFloat(n){
		return isNaN(parseFloat(n))?0:parseFloat(n);
	}

	function saveProduct(){
		var $this = $(this);
		$this.addClass('exclusive_disabled').removeClass('exclusive').attr('disabled','disabled');
		preview.on = 0;
		$.getJSON(customtextdesign_config.module_dir+'inc/ajaxhandler.php',preview,function(data){
			if(data.error){
				alert(data.error);
				return false;
			}
			$this.addClass('exclusive').removeClass('exclusive_disabled').removeAttr('disabled');
			preview.on = 1;
			$('#product_page_product_id').val(data.id_product);
			//$('#addtocarthidden').click();
			ajaxCart.add(data.id_product, null, true, null, preview.qty, null);
			ctd_interval = setInterval(function(){
				if($('#customtextdesign #addtocart').hasClass('exclusive_disabled')){
					ctd_interval_first = true;
					$('#customtextdesign #addtocart').addClass('exclusive').removeClass('exclusive_disabled').removeAttr('disabled');
					ctd_interval_count = 0;
				}else{
					if(ctd_interval_first){
						ctd_interval_count ++;
					}
					if(ctd_interval_count > 1){
						clearInterval(ctd_interval);
						ctd_interval = null;
					}
				}
				}, 500);
		});
		return false;
	}

	function updatePrice(){
		var height = preview.size;
		var width = preview.width * (preview.size / preview.height);
		preview.rwidth = width;
		var area = (width * height / 10000);
		var specific_price = material_prices_rows[preview.material+'_'+preview.size];
		if( ! preview.price ) preview.price = 0;
		if(!specific_price) specific_price = preview.price;
		var unitprice = (area * parseFloat(specific_price));
		var base_price = _parseFloat(customtextdesign_config.base_price);
		var price_reduction = _parseFloat(customtextdesign_config.price_reduction);
		var tax_rate = _parseFloat(customtextdesign_config.tax_rate);
		if(base_price){
			$('#div_base_price').show();
		}
		if(base_price && (_parseFloat(unitprice) > 0)){
			unitprice += base_price;
		}
		if(price_reduction){
			unitprice = unitprice * (1 - price_reduction / 100);
			base_price = base_price * (1 - price_reduction / 100);
		}

		var totalprice = (unitprice * preview.qty);
		width = ps_round(width,2);

		if(!preview.text){
			width = 0;
			height = 0;
			area = 0;
			unitprice = 0;
			totalprice = 0;
			$('#customtextdesign #addtocart').addClass('exclusive_disabled').removeClass('exclusive').attr('disabled','disabled');
		}else{
			$('#customtextdesign #addtocart').addClass('exclusive').removeClass('exclusive_disabled').removeAttr('disabled');
		}

		$('#width-span').text(width);
		$('#height-span').text(height);
		$('#area-span').text(ps_round(area,2));
		$('#baseprice-span').text(ps_round(base_price,2));
		$('#unitprice-span').text(ps_round(unitprice,2));
		$('#totalprice-span').text(ps_round(totalprice,2));
		$('#customtextdesign-ourprice').text(ps_round(totalprice,2));

		if(tax_rate){
			var totalprice_wt = totalprice * (1 + tax_rate / 100);
			$('#totalpricewt-span').text(ps_round(totalprice_wt,2));
			$('#customtextdesign-ourprice').text(ps_round(totalprice_wt,2));
			$('.ctd_wt').show();
			$('.ctd_tax_span').text(ctd_tax0);
		}else{
			$('.ctd_tax_span').text(ctd_tax0);
		}

	}

	function showPreview(getsize){
		if(!preview.on) return;
		$('#customtextdesign #addtocart').addClass('exclusive_disabled').removeClass('exclusive').attr('disabled','disabled');
		preview.text = $('#customtextdesign #customtextdesign-text').val();
		if(preview.color == 0){
			$('ul.customtextdesign-colors li a:eq(0)').click();
			return;
		}
		if(preview.size == 0)
			preview.size = slider.slider('value');
		if(preview.price == 0 || preview.material == 0){
			preview.material = $('#customtextdesign .customtextdesign-materials').val();
			preview.price = customtextdesign_price[preview.material];
		}
		if(preview.qty == 0)
			preview.qty = spinner.spinner('value');

		$('#quantity_wanted').val(preview.qty);

		$loader = $('#customtextdesign img.img-loader').show();
		var src = customtextdesign_config.module_dir+
		"inc/preview.php?font="+preview.font+
		"&size="+preview.size+
		"&text="+encodeURIComponent(preview.text)+
		"&color="+preview.color+
		"&rwidth="+preview.rwidth+
		"&ignore_space="+customtextdesign_config.ignore_space;

		if(preview.mirror)
			src += "&mirror=1";

		if(!getsize)
			src += "&type=img";
		else
			src += "&type=txt";

		if(getsize){
			$.getJSON(src,function(data){
				preview.width = data.width;
				preview.height = data.height;
				preview.cache = data.cache;
				updatePrice();
				if(preview.on && preview.text)
					$('#customtextdesign #addtocart').addClass('exclusive').removeClass('exclusive_disabled').removeAttr('disabled');
			})
		}else{
			$('#bigpic').attr('src',src);
			showPreview(1);
		}
	}

	$('#customtextdesign #bigpic').on('load',function(){
		$('#customtextdesign img.img-loader').fadeOut();
		$('#customtextdesign #preview-div').jScrollPane({});
	});

	$('#customtextdesign #customtextdesign-text').on('change',function(){
		preview.text = $(this).val();
		showPreview();
	});

	$('#customtextdesign #customtextdesign-text').on('keydown',function(event){
		$('#customtextdesign #addtocart').addClass('exclusive_disabled').removeClass('exclusive').attr('disabled','disabled');
		if(event.keyCode == 13 && +customtextdesign_config.num_text_lines==1){
			if(event.shiftKey) return;
			preview.text = $(this).val();
			showPreview();
			return false;
		}
	});

	$('#customtextdesign #preview-button').on('click',function(){
		preview.text = $('#customtextdesign #customtextdesign-text').val();
		showPreview();
		return false;
	})

	$('#customtextdesign .customtextdesign-colors').on('click','li>a',function(){
		$this = $(this);
		preview.color = $this.data('color');
		preview.iscolor = $this.data('iscolor');
		showPreview();
		$('#customtextdesign ul.customtextdesign-colors li').removeClass('active');
		$(this).parent().addClass('active');
		return false;
	});

	$('#customtextdesign .customtextdesign-fonts').ddslick({
		width: 300,
		imagePosition: "left",
		selectText: "",
		background: "#fff",
		width: 360,
		onSelected: function (data) {
			preview.font = data.selectedItem.find('.dd-desc').text();
			showPreview();
		}
	});

	$('#customtextdesign #mirror-effect').on('change',function(){
		preview.mirror = $(this).is(':checked') * 1;
		showPreview();
	});

	$('#customtextdesign .customtextdesign-materials').on('change',function(){
		preview.material = $('#customtextdesign .customtextdesign-materials').val();
		preview.price = customtextdesign_price[preview.material];
		updatePrice();
	});

	$('#customtextdesign #addtocart').off('click').on('click',saveProduct);
	$('#customtextdesign #addtocart').on('mouseenter',function(){
		$(this).off('click').on('click',saveProduct);
	});

	$('#customtextdesign #preview-div').jScrollPane({});

	$('<a>',{
		'class' : "ctd_picker",
		'href' : "#",
		'title' : customtextdesign_picker
	})
	.appendTo('#preview-div')
	.ColorPicker({
		color: '#FFFFFF',
		onChange: function (hsb, hex, rgb) {
			$('#preview-div, .ctd_picker').css('backgroundColor', '#' + hex);
		}
	});

	var slider = $( "#customtextdesign #slider" ).slider({
		min: _parseInt(customtextdesign_config.num_size_min),
		max: _parseInt(customtextdesign_config.num_size_max),
		value: _parseInt(customtextdesign_config.num_size_init),
		create: function( event, ui ){
			var n = $(this).slider('value');
			$('#slider-value').text(n);
		},
		slide: function( event, ui ){
			var n = ui.value;
			$('#slider-value').text(n);
		},
		change: function( event, ui ){
			preview.size = ui.value;
			showPreview();
		}
	});

	var spinner = $( "#customtextdesign #spinner" ).spinner({
		min: 1,
		spin: function( event, ui ) {
			preview.qty = ui.value;
			$('#quantity_wanted').val(preview.qty);
			updatePrice();
		}
	});

	$( "#customtextdesign #spinner" ).on('change', function(){
		preview.qty = $( "#customtextdesign #spinner" ).val();
		$('#quantity_wanted').val(preview.qty);
		updatePrice();
	});

	preview.on = 1;
	showPreview();

});