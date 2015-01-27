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

function noComma(elem)
{
 	$('#'+elem).val($('#'+elem).val().replace(new RegExp(',', 'g'), '.'));
}
$(function(){

	if(window != top){
		var autoIframeHeight = function(){
			var url = location.href;
			$(top.jQuery.find('iframe[src="'+ url +'"]')).css('height', $('body').height()+30);
		}
		$(window).on('resize',autoIframeHeight);
		autoIframeHeight();
	}

	var preview = {
		on : 0,
		first : 1,
		text : '',
		color: 0,
		iscolor: 0,
		bg: 0,
		font:0,
		size:0,
		width:0,
		height:0,
		material:0,
		price:0,
		mirror:0,
		width:0,
		height:0,
		qty:0,
		category:customtextdesign_category,
		cache:'',
		upload:'',
		action:'saveproduct',
		token: static_token
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
		$.getJSON(customtextdesign_config.module_dir+'inc/ajaxhandler_custom.php',preview,function(data){
			if(data.error){
				alert(data.error);
				return false;
			}
			$this.addClass('exclusive').removeClass('exclusive_disabled').removeAttr('disabled');
			preview.on = 1;
			$('#product_page_product_id').val(data.id_product);
			//$('#addtocarthidden').click();
			ajaxCart.add(data.id_product, null, true, null, preview.qty, null);
		});
		return false;
	}

	function updatePrice(){

		var width = _parseInt($('#width-input').val());
		var height = _parseInt($('#height-input').val());
		var area = (width * height / 10000);
		if( ! preview.price ) preview.price = 0;
		var specific_price = preview.price;
		var unitprice = (area * parseFloat(specific_price));
		if(price_reduction = _parseFloat(customtextdesign_config.price_reduction)){
			unitprice = unitprice * (1 - price_reduction / 100);
		}
		var totalprice = (unitprice * preview.qty);

		$('#customtextdesign #addtocart').addClass('exclusive').removeClass('exclusive_disabled').removeAttr('disabled');

		$('#width-span').text(width);
		$('#height-span').text(height);
		$('#area-span').text(ps_round(area,4));
		$('#unitprice-span').text(ps_round(unitprice,2));
		$('#totalprice-span').text(ps_round(totalprice,2));
		$('#customtextdesign-ourprice').text(ps_round(totalprice,2));

		if(tax_rate = _parseFloat(customtextdesign_config.tax_rate)){
			var totalprice_wt = totalprice * (1 + tax_rate / 100);
			$('#totalpricewt-span').text(ps_round(totalprice_wt,2));
			$('.ctd_wt').show();
			$('.ctd_tax_span').text(ctd_tax1);
		}else{
			$('.ctd_tax_span').text(ctd_tax0);
		}

	}

	function showPreview(getsize){
		if(!preview.on) return;
		$('#customtextdesign #addtocart').addClass('exclusive_disabled').removeClass('exclusive').attr('disabled','disabled');

		if(preview.width == 0)
			preview.width = _parseInt($('#width-input').val());
		if(preview.height == 0)
			preview.height = _parseInt($('#height-input').val());
		if(preview.price == 0 || preview.material == 0){
			preview.material = $('#customtextdesign .customtextdesign-materials').val();
			preview.price = customtextdesign_price[preview.material];
		}

		preview.qty = 1;

		$('#quantity_wanted').val(preview.qty);

		updatePrice();

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

	$('#width-input').on('change',function(){
		showPreview();
	});

	$('#height-input').on('change',function(){
		showPreview();
	});

	$('#customtextdesign')
	.on('submit','#ctd_upload_form_uploads',uploadImageStart)
	.on('click','.ctd_delete_uploads',deleteImage);

	var spinner = $( "#customtextdesign #spinner" ).spinner({
		min: 1,
		spin: function( event, ui ) {
			preview.qty = ui.value;
			$('#quantity_wanted').val(preview.qty);
			updatePrice();
		}
	});

	preview.on = 1;
	showPreview();



	function deleteImage(){
		if( ! confirm(ctd_str_sure)) return false;
		var imagesrc = ctd_module_dir + 'img/pixel.png';
		$('#ctd_current_uploads').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
		$('.ctd_delete_uploads').hide();
		$('#ctd_uploads').val('');
		$('#ctd_upload_form_uploads').get(0).reset();
		preview.upload = '';
		$('#customtextdesign .uploader .filename').text('');
		return false;
	};

	function uploadImageStart(){
		$('#ctd_iframe_uploads').off('load').on('load',uploadImageComplete);
		//$('.ctd_uploader').fadeIn();
	};

	function uploadImageComplete(){
		$('.ctd_uploader').fadeOut();
		$doc = $('#ctd_iframe_uploads').contents();
		var json = $doc.find('body').text();
		var $json = {};
		try{
			$json = $.parseJSON(json);
		}catch(ex){}
		if( ! $json) return;
		if($json.error == 0){
			var filename = $json.filename;
			var imagesrc = ctd_module_dir + 'data/uploads/' + filename;
			$('#ctd_current_uploads').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
			$('.ctd_delete_uploads').show().css('display', 'inline-block');
			$('#ctd_uploads').val(filename);
			preview.upload = filename;
		}else if($json.error){
			alert($json.error);
		}
		//$('#ctd_upload_form_uploads').get(0).reset();
	};

});