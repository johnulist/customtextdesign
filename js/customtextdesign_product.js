/**
* 2010-2014 Tuni-Soft - modifié 2/10/2014 13:33
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

$.cssHooks.backgroundColor = {
	get: function(elem) {
		if (elem.currentStyle)
			var bg = elem.currentStyle["backgroundColor"];
		else if (window.getComputedStyle)
			var bg = document.defaultView.getComputedStyle(elem,
				null).getPropertyValue("background-color");
		if (bg.search("rgb") == -1)
			return bg;
		else {
			bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
			function hex(x) {
				return ("0" + parseInt(x).toString(16)).slice(-2);
			}
			return "#" + hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
		}
	}
};

function noComma(elem){elem.value = elem.value.replace(new RegExp(',', 'g'), '.');}

function ctd_alert(str){
	if(!!$.prototype.fancybox){
		str += "<br/><p class=\"submit\" style=\"text-align:right; padding-bottom: 0\"><input class=\"button\" type=\"button\" value=\"OK\" onclick=\"$.fancybox.close();\" /></p>";
		$.fancybox( str, {'autoDimensions': false, 'autoSize': false, 'width': 500, 'height': 'auto', 'openEffect': 'none', 'closeEffect': 'none'} );
	}
	else
		alert(str);
}

var updateDisplayBkp = null, ajaxCartAddBkp = null;

var ctdPanel = function(panel){

	var _panel = this;
	this.panel = $(panel);
	this.id_attribute = 0;
	this.id_image = idDefaultImage;
	this.hash = location.hash;
	this.src_image = $('#thumb_' + idDefaultImage).parent().prop('href');
	if(this.src_image.indexOf('javascript:') == 0){
		this.src_image = eval('__a = ' + $('#thumb_' + idDefaultImage).parent().attr('rel')+ '.largeimage');
	}
	this.attribute_images = {};

	this.texts = {};
	this.text = '';

	this.images = {};
	this.image = '';
	this.container = null;

	this.min_size = 1;
	this.id_cf = 0;

	this.coeff = 0.85;
	this.timeouts = {};

	this.tdata = {};
	this.lastSize = 0;
	this.lastPos = {};
	this.lastResize = {};
	this.colorUpdater = null;
	this.customcolorUpdater = null;
	this.imageColorUpdater = null;

	this.initialized = 0;

	this.init = function(){
		this.container = $('.ctd_panel_content .ctd_preview');
		this.initPanelToggle();
		this.initJSHook();
		this.initDesign();
		this.initialized = 1;
	}

	this.scrollToPanel = function(){
		$('html, body').stop().animate({
			scrollTop: _panel.panel.offset().top - 4
			}, 500);
	}

	this.initPanelToggle = function(){
		$('.ctd_panel_title').on('click',function(){
			if(customtextdesign_popup) return false;
			var collapsed = _panel.panel.hasClass('collapsed');
			if(collapsed){
				_panel.panel.removeClass('collapsed');
				_panel.scrollToPanel();
			}else{
				_panel.panel.addClass('collapsed');
			}
			return false;
		});
	}

	this.initJSHook = function(){
		if(typeof combinations != 'undefined'){
			for (var combination = 0; combination < combinations.length; ++combination){
				var c = combinations[combination];
				_panel.attribute_images[c.idCombination] = c.image;
				if(_panel.attribute_images[c.idCombination] == -1)
					_panel.attribute_images[c.idCombination] = idDefaultImage;
			}
		}else{
			_panel.attribute_images[_panel.id_attribute] = idDefaultImage;
		}
		updateDisplayBkp = updateDisplay;
		updateDisplay = function(){
			updateDisplayBkp();
			_panel.id_attribute = $('#idCombination').val();
			_panel.id_image = _panel.attribute_images[_panel.id_attribute];
			_panel.src_image = $('#thumb_' + _panel.id_image).parent().prop('href');
			if(_panel.src_image.indexOf('javascript:') == 0){
				_panel.src_image = eval('__a = ' + $('#thumb_' + idDefaultImage).parent().attr('rel')+ '.largeimage');
			}
			_panel.hash = location.hash;
			_panel.updateDisplay();
			$('body').addClass('ctd_allow_resize');
			$(window).trigger('resize');
		}
		try{
			if(typeof productHasAttributes != 'undefined' && productHasAttributes)
				updateDisplay();
		}catch(ex){}

		/*if(typeof ajaxCart != 'undefined' && typeof ajaxCart.updateLayer == 'function'){
			updateLayerBkp = ajaxCart.updateLayer;
			ajaxCart.updateLayer = function(product, jsonData, addedFromProductPage, callerElement){
				updateLayerBkp(product, jsonData, addedFromProductPage, callerElement);
				if(_panel.response && typeof _panel.response.total_price != 'undefined'){
					$('#layer_cart_product_price').text(_panel.response.total_price);
					$('.layer_cart_img')
					.html('<img class="layer_cart_img img-responsive" src="' + customtextdesign_config.module_dir + 'data/cache/' + _panel.response.preview + '" alt="' + product.name + '" title="' + product.name + '" />');
				}
			};
		}*/

	}

	this.initDesign = function(){

		if($(window).width() < 480){
			$('.ctd_design').hide();
		}

		$(window).on('focus',function() {
			$('.addcart_custom_product').each(function(){
				var $this = $(this);
				var data = {};
				data['action'] = 'checkcart';
				data['id_custom_product'] = $this.data('id_custom_product');
				data['token'] =  static_token;
				$.post(customtextdesign_config.module_dir+'inc/ajaxdesign.php',data,function(response){
					if(response.error){
						var error = response.error;
						if(error != "1" && customtextdesign_error[error]){
							ctd_alert(customtextdesign_error[error]);
						}
						return;
					}
					if(typeof response.result != 'undefined'){
						if(_panel.parseInt(response.result)){
							$this.removeClass('ctd_hilite');
						}else{
							$this.addClass('ctd_hilite');
						}
					}
					},'json');
			});
		});

		$(window).on('resize',function(event){
			if( event && event.target != window && ! $('body').hasClass('ctd_allow_resize')) return;
			$('body').removeClass('ctd_allow_resize');
			var $ctd_product_panel = $('.ctd_product_panel');

			var h = $(window).height() - 100;
			var w = $(window).width() - 2;
			h = Math.max(220,h);
			w = Math.max(320,w);

			var width = $ctd_product_panel.width();

			var width = Math.min(w, width);
			var width = Math.min(h, width);

			if( width < w - 20 && width < h ){
				width = Math.min(h, w);
			}

			$ctd_product_panel.css('max-width', width + 'px');

			$('.ctd_panel_content .ctd_preview img.ctd_prv').each(function(){
				$(this).css('max-width', (+width - 2) + 'px');
			});

			var img_width = $('.ctd_panel_content .ctd_preview .ctd_img').width();
			if(width > img_width + 2){
				width = img_width + 2;
				$ctd_product_panel.css('max-width', width + 'px');
			}

			var left = $ctd_product_panel.offset().left;
			if($('.ctd_design').css('position') == 'absolute'){
				var dw = $('.ctd_design').width();
				var dh = $('.ctd_design').height();
				var twidth = width + left;
				if(twidth < w - dw){
					$('.ctd_design').css({top: '-40px', left:  (width + 5) + 'px'});
				}else if(twidth > dh * 2 || twidth > dw * 2){
					$('.ctd_design').css({top: (width / 2 + 38) + 'px', left:  ((width - dw) / 2) + 'px'});
				}else{
					var hac = $('.ctd_add_to_cart').outerHeight() + 10;
					$('.ctd_design').css({top: (width + hac) + 'px', left:  ((width - dw) / 2) + 'px'});
				}
			}

			var cw = _panel.container.width();
			var measure = customtextdesign_measures[_panel.id_image];
			if(measure && customtextdesign_min_size){
				var wr = cw / +customtextdesign_width[_panel.id_image];
				var wx = +measure.width * wr;
				_panel.min_size = customtextdesign_min_size * wx / +measure.size;
			}else{
				_panel.min_size = 10;
			}

			$('.ft-container .ui-wrapper').each(function(){
				var $item = $(this).find('.ctd_item');
				$item.resizable('option','minWidth',_panel.min_size);
				$item.resizable('option','minHeight',_panel.min_size);

				if(_panel.lastSize){
					_panel.scaleItem(this,width / _panel.lastSize);
				}

			});

			if(customtextdesign_popup){
				$ctd_product_panel.draggable({ axis: "x", containment: "body" });
				var pw = $ctd_product_panel.width();
				var pl = $ctd_product_panel.offset().left;
				var dw = $('.ctd_design').width()
				var dl = $('.ctd_design').offset().left;
				var tw = Math.max(pw + pl, dw + dl) - Math.min(pl, dl);
				var x = ($(window).width() - tw - 8) / 2;
				$('.ctd_product_panel').css({left: x});
			}

			_panel.saveCustomSize(false);
			_panel.lastSize = width;
			_panel.updateCustomFields();
		});

		$('body').removeClass('ctd_allow_resize');

		$('.ctd_img').off('load').on('load', function(){
			$('.ctd_no_show').removeClass('ctd_no_show');
			if(customtextdesign_popup) return;

			var $ctd_product_panel = $('.ctd_product_panel');
			var h = $(window).height() - 100;
			var w = $(window).width() - 20;
			h = Math.max(220,h);
			w = Math.max(320,w);

			if( ! customtextdesign_popup){
				$ctd_product_panel.show();
			}
			var width = +customtextdesign_width[_panel.id_image];

			if(width > $('.ctd_product_panel').parent().width() - 2){
				width = $('.ctd_product_panel').parent().width() - 2;
			}

			if(width > h){
				width = h;
			}

			$ctd_product_panel.css('max-width', width + 'px');

			$('.ctd_panel_content .ctd_preview img.ctd_prv').each(function(){
				$(this).css('max-width', (+width-2) + 'px');
			});

			var img_width = $('.ctd_panel_content .ctd_preview .ctd_img').width();
			if(width > img_width + 2){
				width = img_width + 2;
				$ctd_product_panel.css('max-width', width + 'px');
			}

			var left = $ctd_product_panel.offset().left;
			if($('.ctd_design').css('position') == 'absolute'){
				var dw = $('.ctd_design').width();
				var dh = $('.ctd_design').height();
				var twidth = width + left;
				if(twidth < w - dw){
					$('.ctd_design').css({top: '-40px', left:  (width + 5) + 'px'});
				}else if(twidth > dh * 2 || twidth > dw * 2){
					$('.ctd_design').css({top: (width / 2 + 38) + 'px', left:  ((width - dw) / 2) + 'px'});
				}else{
					var hac = $('.ctd_add_to_cart').outerHeight() + 10;
					$('.ctd_design').css({top: (width + hac) + 'px', left:  ((width - dw) / 2) + 'px'});
				}
			}

			var cw = _panel.container.width();
			var measure = customtextdesign_measures[_panel.id_image];
			if(measure && customtextdesign_min_size){
				var wr = cw / +customtextdesign_width[_panel.id_image];
				var wx = +measure.width * wr;
				_panel.min_size = customtextdesign_min_size * wx / +measure.size;
			}else{
				_panel.min_size = 0;
			}

			_panel.saveCustomSize(false);
			_panel.lastSize = width;
			_panel.updateCustomFields();
		});

		$('.ctd_img').prop('src',_panel.src_image);

		$('.autogrow').keyup(function(e) {
			//  the following will help the text expand as typing takes place
			$textarea = $('#ctd_text');
			var lineheight = parseInt($textarea.css('line-height'))
			var lines = ($textarea.val().match(/\n/g)||[]).length;
			$textarea.css({height: lines * 22 + 22});
		});

		$("textarea[maxlength]").bind('input propertychange', function() {
			var maxLength = $(this).attr('maxlength');
			if ($(this).val().length > maxLength) {
				$(this).val($(this).val().substring(0, maxLength));
			}
		});

		if($('#ctd_font option').length){
			$('#ctd_font').ddslick({
				imagePosition: "left",
				selectText: "",
				background: "#fff",
				width: 200,
				onSelected: function (data) {_panel.applyText()}
			});
		}
		$('#ctd_material, #ctd_mirror').on('change', _panel.applyText);

		$('#ctd_center').on('change',function(){
			var checked = $(this).prop('checked');
			if(checked){
				$('#ctd_text').css('text-align','center');
			}else{
				$('#ctd_text').css('text-align','left');
			}
			_panel.applyText();
		})

		$('.ctd_design').draggable({
			handle : '.ctd_design_title'
		});

		$('.ctd_design_title').on('click','a[rel]',function(){
			var rel = $(this).prop('rel');
			$('.ctd_design_content > div').hide();
			$('.ctd_design_content > div.'+rel).show();
			if(rel == 'ctd_images'){
				//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
			}
			$(this).addClass('active').siblings().removeClass('active');
			return false;
		})
		.on('click', '.ctd_a_image', function(){
			$('.ctd_close_image_group').trigger('click');
			$('.ctd_img_cancel').trigger('click');
		});

		//Colors
		$('ul#ctd_color').on('click','li',function(){
			$(this).addClass('active').siblings().removeClass('active');
			_panel.applyText();
			return false;
		});
		$('ul#ctd_color li:eq(0)').click();

		//Custom colors
		$('ul#ctd_customcolor').on('click','li',function(){
			$(this).addClass('active').siblings().removeClass('active');
			return false;
		});
		$('ul#ctd_customcolor li:eq(0)').click();

		//Image colors
		$('ul#ctd_imagecolor').on('click','li',function(){
			$(this).addClass('active').siblings().removeClass('active');
			_panel.updateImageColor();
			return false;
		});
		$('ul#ctd_imagecolor li:eq(0)').click();

		$('#ctd_buttons')
		.on('click','#ctd_apply',_panel.applyText)
		.on('click','#ctd_add',_panel.addText);

		$('.ctd_details_buttons')
		.on('click','.ctd_add_cart',_panel.addToCart)
		.on('click','.ctd_back_design',function(){
			$('.ctd_details').fadeOut(500,function(){
				$('.ctd_details_buttons').hide();
				$('.ctd_add_to_cart').show();
			});
			$('.ctd_design').fadeIn(500);
			$('.ctd_preview').removeClass('ctd_details_shown');
			return false;
		});

		$('.ctd_add_to_cart')
		.on('click','.ctd_show_price',_panel.calculatePrice)
		.on('click','.ctd_add_cart',_panel.addToCart)
		.on('click','.ctd_download_btn',_panel.downloadImage);


		$('.ctd_images')
		.on('click','a.ctd_image_group',_panel.openImageGroup)
		.on('click','a.ctd_close_image_group',_panel.closeImageGroup)
		.on('click','a.ctd_image', function(){
			_panel.addImage(this);
			return false;
		});

		_panel.container
		.on('click touchstart',function(){
			$('.ctd_design').show();
			var $target = $(event.target);
			if(_panel.container.hasClass('ctd_preview_hover'))
			if($target.hasClass('ctd_img_mask')
			|| $target.hasClass('ctd_img_mask2')
			|| $target.hasClass('ctd_img')
			){
				event.preventDefault();
				event.stopPropagation();
				return false;
			}
		})
		.on('mousedown','.ft-container',function(){
			var $elem = $(this).find('img');
			var timestamp = $elem.prop('id');
			_panel.text = timestamp;
			$('.ft-container.selected').removeClass('selected');
			$(this).addClass('selected');
			var is_text = $elem.hasClass('ctd_text_preview');
			var is_image = $elem.hasClass('ctd_image_preview');
			if(is_text){
				_panel.setValues(_panel.texts[timestamp]);
			}
			var cf_id = $elem.data('cf');
			if(cf_id && $('#'+cf_id).length){
				_panel.id_cf = cf_id;
				$('#'+cf_id).addClass('ctd_cf_active').siblings('.ctd_cf.ctd_cf_active').removeClass('ctd_cf_active');
			}
			$('.ctd_outbound').removeClass('ctd_outbound');
		})
		.on('mouseenter','.ft-container',function(){
			var id_item = $(this).find('img:eq(0)').prop('id');
			clearTimeout(_panel.timeouts[id_item]);
			_panel.timeouts[id_item] = null;
			$(this).addClass('ft-hover');
		})
		.on('mouseleave','.ft-container',function(){
			var id_item = $(this).find('img:eq(0)').prop('id');
			_panel.timeouts[id_item] = setTimeout(function(){
				$.each(_panel.timeouts,function(id_item,tm){
					if(tm){
						$('#' + id_item).closest('.ft-container').removeClass('ft-hover');
					}
				})
				},2000);
		}).on('mouseenter touchstart',function(){
			var id_item = 'ctd_preview';
			clearTimeout(_panel.timeouts[id_item]);
			_panel.timeouts[id_item] = null;
			$(this).addClass('ctd_preview_hover');
			$('.ctd_img_mask').stop().fadeOut(333, function(){$(this).css('opacity',0)});
			$('.ctd_img_mask2').stop().fadeIn(333, function(){$(this).css('opacity',1)});
		})
		.on('mouseleave touchend',function(){
			var id_item = 'ctd_preview';
			_panel.timeouts[id_item] = setTimeout(function(){
				$.each(_panel.timeouts,function(id_item,tm){
					if(tm){
						$('.ctd_preview').removeClass('ctd_preview_hover');
						$('.ctd_img_mask').stop().fadeIn(333, function(){$(this).css('opacity',1)});
						$('.ctd_img_mask2').stop().fadeOut(333, function(){$(this).css('opacity',0)});
					}
				})
				},1000);
		})
		.on('click touchstart','.ft-front',_panel.bringToFront)
		.on('click touchstart','.ft-back',_panel.sendToBack)
		.on('click touchstart','.ft-suppr',_panel.removeItem)
		.on('click touchstart','.ctd_cf',function(){
			_panel.id_cf = $(this).prop('id');
			$(this).addClass('ctd_cf_active').siblings('.ctd_cf.ctd_cf_active').removeClass('ctd_cf_active');
		})
		.on('click','.ctd_rotator_btn',_panel.rotateItem);

		$('.ctd_design')
		.on('load','img.ctd_user_upload_img',function(){
			//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
		})
		.on('click','#ctd_img_upload', _panel.showUploadForm)
		.on('click','#ctd_img_url', _panel.showUrlForm)
		.on('click','.ctd_img_cancel', _panel.cancelImgForm)
		.on('submit','form#ctd_url', _panel.addImgURL)
		.on('click','.ctd_customsize_btn',function(){
			return false;
		})
		.on('click','.ctd_customsize_save',_panel.saveCustomSize)
		.on('keyup','.ctd_customsize_controls .text',function(e){
			if(e.keyCode == 13){
				$('.ctd_customsize_save').trigger('click');
			}
		})
		.on('change','#ctd_customcolor_input',_panel.applyCustomColor)
		.on('change','#ctd_text', _panel.applyText)
		;

		function ctd_alpha_slider(event, ui){
			var value = ui.value;
			var percent = ps_round(value / 127 * 100, 0);
			$('#ctd_alpha_slider_value').text(percent + '%');
		}

		function ctd_alpha_slider_stop(event, ui){
			ctd_alpha_slider(event, ui)
			_panel.applyText();
		}

		$('#ctd_alpha_slider').slider({
			min: 0,
			max: 126,
			slide: ctd_alpha_slider,
			stop:  ctd_alpha_slider_stop
		});

		$('#ctd_alpha_reset').on('click',function(){
			$('#ctd_alpha_slider').slider('value', 0);
			$('#ctd_alpha_slider_value').text('0%');
			_panel.applyText();
			return false;
		});

		function ctd_curve_slider(event, ui){
			var value = ui.value;
			var percent = value;
			$('#ctd_curve_slider_value').text(percent + '%');
		}

		function ctd_curve_slider_stop(event, ui){
			ctd_curve_slider(event, ui);
			_panel.applyText();
		}

		$('#ctd_curve_slider').slider({
			min: -100,
			max: 100,
			value: customtextdesign_initial_curve,
			slide: ctd_curve_slider,
			stop:  ctd_curve_slider_stop
		});

		$('#ctd_curve_reset').on('click',function(){
			$('#ctd_curve_slider').slider('value', 0);
			$('#ctd_curve_slider_value').text('0%');
			_panel.applyText();
			return false;
		});

		function ctd_letterspace_slider(event, ui){
			var value = ui.value;
			var percent = value;
			$('#ctd_letterspace_slider_value').text(percent + '%');
		}

		function ctd_letterspace_slider_stop(event, ui){
			ctd_letterspace_slider(event, ui);
			_panel.applyText();
		}

		$('#ctd_letterspace_slider').slider({
			min: -100,
			max: 100,
			value: customtextdesign_initial_letterspace,
			slide: ctd_letterspace_slider,
			stop:  ctd_letterspace_slider_stop
		});

		$('#ctd_letterspace_reset').on('click',function(){
			$('#ctd_letterspace_slider').slider('value', 0);
			$('.ctd_letterspace_slider_value').text('0%');
			_panel.applyText();
			return false;
		});

		if($('#ctd_rotator').length){

			function ctd_rotator(event, ui){
				var value = ui.value;
				$('#ctd_rotator_value').text(value + '°');
				_panel.rotateItem(value);
			}

			$('#ctd_rotator').slider({
				orientation: "vertical",
				min: 0,
				max: 360,
				slide: ctd_rotator,
				stop:  ctd_rotator
			}).on('keydown', function(event){
				if(event.shiftKey){
					$('#ctd_rotator').slider('option', 'step', 45);
				}
			}).on('keyup', function(event){
				$('#ctd_rotator').slider('option', 'step', 1);
			});
		}

		$('.ctd_picker').ColorPicker({
			color: '#11daf5',
			onChange: function (hsb, hex, rgb) {
				$('.ctd_picker').css('backgroundColor', '#' + hex);
				$('ul#ctd_color li').removeClass('active');
				$('.ctd_picker').parent().addClass('active');
				clearTimeout(_panel.colorUpdater);
				_panel.colorUpdater = setTimeout(_panel.applyText,1000);
			}
		});

		$('.ctd_customcolor').ColorPicker({
			color: customtextdesign_initial_color,
			onChange: function (hsb, hex, rgb) {
				$('#ctd_customcolor_input').val('#' + hex);
				$('.ctd_customcolor').css('backgroundColor', '#' + hex);
				$('ul#ctd_customcolor li').removeClass('active');
			}
		});

		$('.ctd_imagepicker').ColorPicker({
			color: customtextdesign_initial_img_color,
			onChange: function (hsb, hex, rgb) {
				$('.ctd_imagepicker').css('backgroundColor', '#' + hex);
				$('ul#ctd_imagecolor li').removeClass('active');
				$('.ctd_imagepicker').parent().addClass('active');
				clearTimeout(_panel.imageColorUpdater);
				_panel.imageColorUpdater = setTimeout(_panel.updateImageColor,1000);
			}
		});

		$('#ctd_upload').on('submit',function(){
			$('.ctd_uploader').fadeIn();
			_panel.closeImageGroup();
		});

		$('#ctd_iframe').on('load',function(){
			$('.ctd_uploader').fadeOut();
			$doc = $('#ctd_iframe').contents();
			var json = $doc.find('body').text();
			var $json = {};
			try{
				$json = $.parseJSON(json);
			}catch(ex){}
			if( ! $json) return;
			if($json.error == 0){
				var filename = $json.filename;
				var imagesrc = customtextdesign_config.module_dir + 'data/uploads/' + filename;
				var title = customtextdesign_usertitle + customtextdesign_userprice;
				var html = 	'<a href="#" title="'+title+'" class="ctd_image ctd_user_upload" style="display:block">';
				html +=			'<img class="ctd_user_upload_img" data-src="'+imagesrc+'" data-id_image="0" src="'+customtextdesign_config.module_dir+'inc/thumb.php?img='+imagesrc+'&w=50&m=auto" width="50" />';
				html +=		'</a>';
				$(html).insertAfter('.ctd_img_send_container');
				$('img.ctd_user_upload_img').off('load').on('load',function(){
					//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
				});
				//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
			}else if($json.error){
				ctd_alert($json.error);
			}
			$('#ctd_upload').get(0).reset();
		});

		$('.ctd_img').on('dragstart', function(event) { event.preventDefault(); });
		$('.ctd_img_overlay').on('dragstart', function(event) { event.preventDefault(); });
		$('.ctd_img_mask').on('dragstart', function(event) { event.preventDefault(); });
		$('.ctd_img_mask2').on('dragstart', function(event) { event.preventDefault(); });

		if(typeof customtextdesign_overlays[_panel.id_image] == 'object'){
			var overlay = customtextdesign_overlays[_panel.id_image];
			$('.ctd_img_overlay').prop('src',customtextdesign_config.module_dir + 'data/overlay/' + overlay.image);
		}else{
			$('.ctd_img_overlay').prop('src',customtextdesign_config.module_dir + 'img/pixel.png');
		}

		if(typeof customtextdesign_masks[_panel.id_image] == 'object'){
			var mask = customtextdesign_masks[_panel.id_image];
			$('.ctd_img_mask').prop('src',customtextdesign_config.module_dir + 'data/mask/' + mask.image);
			$('.ctd_img_mask2').prop('src',customtextdesign_config.module_dir + 'data/mask/' + mask.image);
		}else{
			$('.ctd_img_mask').prop('src',customtextdesign_config.module_dir + 'img/pixel.png');
			$('.ctd_img_mask2').prop('src',customtextdesign_config.module_dir + 'img/pixel.png');
		}

		if(typeof customtextdesign_replaces[_panel.id_image] == 'object'){
			var replace = customtextdesign_replaces[_panel.id_image];
			$('.ctd_img').prop('src',customtextdesign_config.module_dir + 'data/replace/' + replace.image);
		}

		if($('.ctd_customize_button').length){
			$('.ctd_customize_button').on('click',function(){
				if( ! customtextdesign_popup){
					ctd_scroll();
				}else{
					ctd_show_popup();
				}
				return false;
			});
			$('.ctd_close_popup').on('click', ctd_hide_popup);
		}

		if($('#ctd_color_list').length){
			$('#ctd_color_list').on('click', 'li>a', function(){
				$('#ctd_color_list li.selected').removeClass('selected');
				$(this).closest('li').addClass('selected');
				var id_attribute = $(this).data('id');
				$('#color_' + id_attribute).click();
				return false;
			});

			$(document).on('click', '.color_pick', function(e){
				var id_attribute = $(this).prop('id').replace('color_', '');
				if($('#ctd_color_' + id_attribute).length){
					$('#ctd_color_' + id_attribute).closest('li').addClass('selected').siblings('li').removeClass('selected');
				}
			});

			if($('#color_to_pick_list li.selected').length){
				var $a = $('#color_to_pick_list li.selected').find('a');
				var id_attribute = $a.prop('id').replace('color_', '');
				if($('#ctd_color_' + id_attribute).length){
					$('#ctd_color_' + id_attribute).closest('li').addClass('selected').siblings('li').removeClass('selected');
				}
			}
		}

	}

	this.openImageGroup = function(){
		var id_group = $(this).data('id_image_group');
		$('a.ctd_image_group').hide();
		$('a.ctd_image').hide();
		$('.ctd_close_image_group').show().css("display", "inline-block");
		$('a.ctd_group_'+id_group).show();
		//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
		//$('.ctd_images_container').data('jsp').scrollTo(0,$('.ctd_close_image_group:eq(0)').position().top,true)
		return false;
	}

	this.closeImageGroup = function(){
		$('a.ctd_image_group').show();
		$('a.ctd_image').hide();
		$('.ctd_close_image_group').hide();
		//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
		return false;
	}

	this.showUploadForm = function(){
		$('.ctd_img_container').hide();
		$('.ctd_img_send_container').addClass('ctd_upload');
		return false;
	}

	this.showUrlForm = function(){
		$('.ctd_img_container').hide();
		$('.ctd_img_send_container').addClass('ctd_url');
		return false;
	}

	this.cancelImgForm = function(){
		$('.ctd_img_container').show();
		$('.ctd_img_send_container').removeClass('ctd_upload').removeClass('ctd_url');
		return false;
	}

	this.addImgURL = function(){
		var action = $(this).prop('action');
		var url = $('#ctd_url_input').val();
		if( !url){
			ctd_alert(customtextdesign_error['no_img_url']);
			return false;
		}
		if(! _panel.checkURL(url)){
			ctd_alert(customtextdesign_error['not_valid_url']);
			return false;
		}
		var data = {};
		data['url'] = url;
		data['token'] =  static_token;
		data['action'] =  'img_from_url';
		data['id_product'] = $(this).find('[name=id_product]').val();
		$('.ctd_uploader').fadeIn();
		$.getJSON(action, data, function(response){
			$('.ctd_uploader').fadeOut();
			if(response.error){
				var error = response.error;
				if(customtextdesign_error[error])
					ctd_alert(customtextdesign_error[error]);
				else
					ctd_alert(error);
				return;
			}
			if(response.success){
				var filename = response.filename;
				var imagesrc = customtextdesign_config.module_dir + 'data/uploads/' + filename;
				var title = customtextdesign_usertitle + ' - ' + customtextdesign_userprice;
				var html = 	'<a href="#" title="'+title+'" class="ctd_image ctd_user_upload" style="display:block">';
				html +=			'<img class="ctd_user_upload_img" data-src="'+imagesrc+'" data-id_image="0" src="'+customtextdesign_config.module_dir+'inc/thumb.php?img='+imagesrc+'&w=50&m=auto" width="50" />';
				html +=		'</a>';
				$(html).insertAfter('.ctd_img_send_container');
				$('img.ctd_user_upload_img').off('load').on('load',function(){
					//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
				});
				//$('.ctd_images_container').jScrollPane({mouseWheelSpeed:20});
			}
		})
		return false;
	}

	this.checkURL = function(url){
		return /^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
	}

	this.initList = function(){
		$('.ctd_custom_product').on('click','.remove_custom_product',function(){
			var $this = $(this);
			var data = {};
			data['action'] = 'remove_custom_product';
			data['id_custom_product'] = $this.data('id_custom_product');
			data['token'] =  static_token;
			$('.ctd_loader').fadeIn();
			$.post(customtextdesign_config.module_dir+'inc/ajaxdesign.php',data,function(response){
				$('.ctd_loader').hide();
				if(response.error){
					var error = response.error;
					if(customtextdesign_error[error])
						ctd_alert(customtextdesign_error[error]);
					return;
				}
				if(response.success){
					$this.closest('tr').hide('fast',function(){
						$(this).remove();
						if($('.ctd_custom_product tr').length < 2){
							$('.ctd_custom_product').hide();
						}
					});
					try{ajaxCart.refresh()}catch(ex){}
				}
				},'json');
			return false;
		});

		$('.ctd_custom_product').on('click','.addcart_custom_product',function(){
			var $this = $(this);
			var data = {};
			data['action'] = 'addcart_custom_product';
			data['id_custom_product'] = $this.data('id_custom_product');
			data['quantity'] = $('#quantity_wanted').val();
			data['token'] =  static_token;
			$('.ctd_loader').fadeIn();
			$.post(customtextdesign_config.module_dir+'inc/ajaxdesign.php',data,function(response){
				$('.ctd_loader').hide();
				if(response.error){
					var error = response.error;
					if(customtextdesign_error[error])
						ctd_alert(customtextdesign_error[error]);
					return;
				}
				if(response.success){
					if(customtextdesign_message[response.success]){
						ctd_alert(customtextdesign_message[response.success]);
					}
					$this.removeClass('ctd_hilite');
					$('#add_to_cart [type=submit]').click();
				}
				},'json');
			return false;
		});
	}

	this.parseInt = function(num){
		return isNaN(parseInt(num))? 0 : parseInt(num);
	}

	this.parseFloat = function(num){
		return isNaN(parseFloat(num))? 0 : parseFloat(num);
	}

	this.updateDisplay = function(){
		$('.ctd_img').prop('src',_panel.src_image);

		if(typeof customtextdesign_overlays[_panel.id_image] == 'object'){
			var overlay = customtextdesign_overlays[_panel.id_image];
			$('.ctd_img_overlay').prop('src',customtextdesign_config.module_dir + 'data/overlay/' + overlay.image);
		}else{
			$('.ctd_img_overlay').prop('src',customtextdesign_config.module_dir + 'img/pixel.png');
		}

		if(typeof customtextdesign_masks[_panel.id_image] == 'object'){
			var mask = customtextdesign_masks[_panel.id_image];
			$('.ctd_img_mask').prop('src',customtextdesign_config.module_dir + 'data/mask/' + mask.image);
			$('.ctd_img_mask2').prop('src',customtextdesign_config.module_dir + 'data/mask/' + mask.image);
		}else{
			$('.ctd_img_mask').prop('src',customtextdesign_config.module_dir + 'img/pixel.png');
			$('.ctd_img_mask2').prop('src',customtextdesign_config.module_dir + 'img/pixel.png');
		}

		if(typeof customtextdesign_replaces[_panel.id_image] == 'object'){
			var replace = customtextdesign_replaces[_panel.id_image];
			$('.ctd_img').prop('src',customtextdesign_config.module_dir + 'data/replace/' + replace.image);
		}

		if($.inArray(_panel.id_attribute, customtextdesign_attributes)>-1 || customtextdesign_attributes_all){
			//$('.ctd_product_panel').show();
		}else{
			$('.ctd_product_panel').hide();
		}

	}

	this.getCustomColorSrc = function(customcolor){
		var src = customtextdesign_config.module_dir +
		"inc/preview.php" 	+
		"?customcolor="		+	customcolor	+
		"&id_product="		+	id_product +
		"&id_image="		+	_panel.id_image;

		return src;
	}

	this.getImageColorSrc = function(image_src, imagecolor){
		var src = customtextdesign_config.module_dir +
		"inc/preview.php" 	+
		"?imagecolor="		+	imagecolor	+
		"&image_src="		+	image_src;

		return src;
	}

	this.getSrc = function(values, getsize){
		var src = customtextdesign_config.module_dir +
		"inc/preview.php" 	+
		"?font="			+	values.font	+
		"&size="			+	"20" +
		"&text="			+	encodeURIComponent(values.text)	+
		"&color="			+	values.color +
		"&clr="				+	values.clr +
		"&alpha="			+	values.alpha +
		"&material="		+	values.material +
		"&curve="			+	values.curve +
		"&letterspace="		+	values.letterspace +
		"&mirror="			+	values.mirror +
		"&center="			+	values.center +
		"&forpanel="		+	values.forpanel +
		"&ignore_space="	+	customtextdesign_config.ignore_space;

		if(!getsize)
			src += "&type=img";
		else
			src += "&type=txt";

		return src;
	}

	this.getValues = function(){
		var values = {};
		values.text = $('#ctd_text').val();
		values.color = $('#ctd_color li.active a').data('color');
		values.clr = $('.ctd_picker').css('background-color').replace('#','');
		values.alpha = $('#ctd_alpha_slider').length ? $('#ctd_alpha_slider').slider('value') : 0;
		values.curve = $('#ctd_curve_slider').length ? $('#ctd_curve_slider').slider('value') : 0;
		values.letterspace = $('#ctd_letterspace_slider').length ? $('#ctd_letterspace_slider').slider('value') : 0;
		values.font = $('#ctd_font .dd-selected').find('.dd-desc').text();
		values.material = $('#ctd_material').val();
		values.mirror = _panel.parseInt($('#ctd_mirror').prop('checked') * 1);
		values.center = _panel.parseInt($('#ctd_center').prop('checked') * 1);
		values.forpanel = 1;
		return values;
	}

	this.setValues = function(values){
		$('#ctd_text').val(values.text);
		$('#ctd_color li a[data-color="' + values.color + '"]').closest('li').addClass('active').siblings().removeClass('active');
		$('.ctd_picker').css('background-color', '#' + values.clr)
		if( $('#ctd_font .dd-selected').find('.dd-desc').text() != values.font){
			$('#ctd_font li[data-val="'+values.font+'"] a').click();
		}
		$('#ctd_material').val(values.material);
		$('#ctd_mirror').prop('checked',values.mirror);
		$('#ctd_center').prop('checked',values.center);
		if(values.center){
			$('#ctd_text').css('text-align','center');
		}else{
			$('#ctd_text').css('text-align','left');
		}
		if($('#ctd_alpha_slider').length){
			$('#ctd_alpha_slider').slider('value', values.alpha);
			$('#ctd_alpha_slider_value').text(values.alpha + '%');
		}
		if($('#ctd_curve_slider').length){
			$('#ctd_curve_slider').slider('value', values.curve);
			$('#ctd_curve_slider_value').text(values.curve + '%');
		}
		if($('#ctd_letterspace_slider').length){
			$('#ctd_letterspace_slider').slider('value', values.letterspace);
			$('#ctd_letterspace_slider_value').text(values.letterspace + '%');
		}
		return values;
	}

	this.applyText = function(){
		if(!_panel.initialized) return false;
		var values = _panel.getValues();
		var timestamp = _panel.text;
		if( ! timestamp || ! _panel.texts[timestamp]) return false;
		_panel.texts[timestamp] = values;
		_panel.updateText(values);
		return false;
	}

	this.addText = function(){
		var values = _panel.getValues();
		var timestamp = new Date().getTime();
		_panel.texts[timestamp] = values;
		_panel.text = timestamp;
		_panel.updateText(values);
		return false;
	}

	this.updateText = function(values){
		var id_text = _panel.text;
		if( ! id_text){
			_panel.addText();
			return;
		}
		if( ! values.text)
			return;
		if( customtextdesign_max_length && values.text.length > customtextdesign_max_length){
			var rem = values.text.length - customtextdesign_max_length;
			ctd_alert(
				customtextdesign_error['max_length']
				.replace('_MAX_', customtextdesign_max_length)
				.replace('_REM_', rem)
			);
			return false;
		}
		var src = _panel.getSrc(values);
		var $text = $('#'+id_text);
		var cf_id = null;
		var prev_pos = null;
		if($text.length){
			cf_id = $text.data('cf');
			prev_pos = $text.closest('.draggable-wrapper').position();
			$text.closest('.draggable-wrapper').fadeOut('fast', function(){$(this).remove();});
		}

		$cf = $('.ctd_cf.ctd_cf_active').length ? $('.ctd_cf.ctd_cf_active') : null;

		$('.ctd_loader').fadeIn();

		var pos = {top: 25, left: 25};
		if($cf && $cf.length){
			pos = $cf.position();
			pos.top++;
			pos.left++;
		}else if(prev_pos){
			pos = prev_pos;
		}

		$markup = $('<div class="draggable-wrapper" style="top: '+pos.top+'px; left: '+pos.left+'px;"><div class="ft-container"></div></div>');

		$text = $('<img>',{
			'id' : id_text,
			'class' : 'ctd_text_preview ctd_item elem-wrapper'
		})
		.hide()
		.off('load').on('load', function(){
			var $item = $(this);
			_panel.checkSize(this, prev_pos);
			$item.fadeIn('slow');

			var drWr = $item.closest('.draggable-wrapper');
			$item.resizable({
				//aspectRatio: true,
				handles:     'ne, nw, se, sw, n, w, s, e',
				minWidth : _panel.min_size,
				minHeight : _panel.min_size
			});
			if(customtextdesign_disable_resize){
				$item.resizable('disable');
			}

			drWr.draggable();
			drWr.find('.ui-resizable-handle').attr('title', customtextdesign_resize);
			if(customtextdesign_disable_drag){
				drWr.draggable( 'disable' )
			}

			$item.parent().rotatable({
				autoHide: false
			});
			var $wr = $item.closest('.ui-wrapper');
			$wr.data('ratio',$item.width() / $item.height());
			if($cf && $cf.length){
				var cfw = $cf.width() - 2;
				var cfh = $cf.height() - 2;
				if(!ctd_config.stretch_field){
					var wrw = $item.width();
					var wrh = $item.height();
					var proportions = wrw / wrh;
					if(proportions >= 1){
						if(wrw >= cfw){
							wrh = cfw / wrw * wrh;
							wrw = cfw;
							if(wrh > cfh){
								wrw = cfh / wrh * wrw;
								wrh = cfh;
							}
						}
						if(wrh >= cfh){
							wrw = cfh / wrh * wrw;
							wrh = cfh;
						}
					}else{
						if(wrh >= cfh){
							wrw = cfh / wrh * wrw;
							wrh = cfh;
							if(wrw > cfw){
								wrh = cfw / wrw * wrh;
								wrw = cfw;
							}
						}
						if(wrw >= cfw){
							wrh = cfw / wrw * wrh;
							wrw = cfw;
						}
					}
					$wr.css({width: wrw, height: wrh});
					$item.css({width: wrw, height: wrh});
					var new_pos = {left: (cfw - wrw) / 2 + pos.left, top: (cfh - wrh) / 2 + pos.top};
					drWr.css(new_pos);
				}else{
					$wr.css({width: cfw, height: cfh});
					$item.css({width: cfw, height: cfh});
					var new_pos = {left: pos.left + 1, top: pos.top + 1};
					drWr.css(new_pos);
				}
				$item.data('cf', $cf.prop('id'));
				$cf.data('item', id_text);
			}
			$('.ft-container.selected').removeClass('selected');
			$item.closest('.ft-container').addClass('selected');
			$('.ctd_loader').fadeOut();
			$item.off('load');
			if($cf && $cf.length){
				$nextcf = $cf.next();
				if(!$nextcf.length){
					$nextcf = $('.ctd_cf:not(#ctd_cf_clone):eq(0)');
				}
				$nextcf.trigger('click');
			}
		}).prop('src',src).appendTo($markup.find('.ft-container'));
		$markup.insertBefore('.ctd_img');
		$('.ft-container.selected').removeClass('selected');
		$markup.find('.ft-container').addClass('selected');
		return false;
	}

	this.addImage = function(img, color){
		var fixed_groups = [];
		var unique_groups = [];
		var $this = $(img);
		var is_fixed = false;
		$.each(fixed_groups, function(i, id_group){
			if($this.hasClass('ctd_group_' + id_group)) is_fixed = true;
		});

		var g_id_group = 0;
		var fixed_class = '';
		$.each(unique_groups, function(i, id_group){
			if($this.hasClass('ctd_group_' + id_group)){
				g_id_group = id_group;
				fixed_class = ' ctd_fxd';
				$('.ctd_preview').find('.ctd_grp_' + id_group).each(function(){
					var id_image = $(this).find('img').prop('id');
					delete _panel.images[id_image];
					$(this).remove();
				});
			}
		});

		var src = $this.find('img').data('src');
		var id_image = $this.find('img').data('id_image');

		var timestamp = new Date().getTime();
		if(is_fixed)
			timestamp = 1;
		var image = {};
		image['text'] = src;
		image['id_image'] = id_image;
		image['clr'] = '';
		image['color'] = 0;
		_panel.images[timestamp] = image;
		_panel.image = timestamp;
		$cf = $('.ctd_cf.ctd_cf_active').length ? $('.ctd_cf.ctd_cf_active') : null;

		$('.ctd_loader').fadeIn();

		var pos = {top: 25, left: 25};
		if($cf && $cf.length){
			pos = $cf.position();
			pos.top++;
			pos.left++;
		}

		$markup = $('<div class="draggable-wrapper ctd_grp_' + g_id_group + fixed_class + '" style="top: '+pos.top+'px; left: '+pos.left+'px;"><div class="ft-container"></div></div>');

		$text = $('<img>',{
			'id' : timestamp,
			'class' : 'ctd_image_preview ctd_item'
		})
		.hide()
		.off('load').on('load', function(){
			var $item = $(this);
			_panel.checkSize(this);
			$item.show();

			var drWr = $item.closest('.draggable-wrapper');

			$item.resizable({
				//aspectRatio: true,
				handles:     'ne, nw, se, sw, n, w, s, e',
				minWidth : _panel.min_size,
				minHeight : _panel.min_size,

				start : function(event, ui){
					var $src = $(ui.helper.find('.ctd_item'));
					var cf = $src.data('cf');
					ctdPanelInst.lastResize[cf] = {
						width: ui.size.width,
						height: ui.size.height,
						left: ui.position.left,
						top: ui.position.top
					}
				},

				resize : function(event, ui){
					var $src = $(ui.helper.find('.ctd_item'));
					var cf_id = $src.data('cf');
					$cf = $('#' + cf_id);
					if($cf.length)
					{
						console.log('position', ui.position.top, ui.position.left);
						var el_bounds = $src.offset();
						var container_offset = ctdPanelInst.container.offset();

						var d_width = ui.size.width - ctdPanelInst.lastResize[cf_id].width;
						var d_height = ui.size.height - ctdPanelInst.lastResize[cf_id].height;

						var d_left = ui.position.left - ctdPanelInst.lastResize[cf_id].left;
						var d_top = ui.position.top - ctdPanelInst.lastResize[cf_id].top;

						el_bounds = {left: parseInt(el_bounds.left - container_offset.left + d_left), top: parseInt(el_bounds.top - container_offset.top + d_top)}
						var cf_bounds = $cf.position();
						cf_bounds = {left: parseInt(cf_bounds.left), top: parseInt(cf_bounds.top + 1)};

						var el_width = $src.width() + d_width + 1;
						var el_height = $src.height() + d_height + 1;

						var cf_width = $cf.width() + 2;
						var cf_height = $cf.height() + 2;

						el_bounds.right = parseInt(el_bounds.left + el_width);
						el_bounds.bottom = parseInt(el_bounds.top + el_height);

						cf_bounds.right = parseInt(cf_bounds.left + cf_width);
						cf_bounds.bottom = parseInt(cf_bounds.top + cf_height);

						$(this).resizable( "option", "maxWidth", 0);
						$(this).resizable( "option", "maxHeight", 0);

						if(el_bounds.left < parseInt(cf_bounds.left)){
							ui.size.width = ctdPanelInst.lastResize[cf_id].width;
							$(this).resizable( "option", "maxWidth", ctdPanelInst.lastResize[cf_id].width);
						}

						if(el_bounds.top < parseInt(cf_bounds.top)){
							ui.size.height = ctdPanelInst.lastResize[cf_id].height;
							$(this).resizable( "option", "maxHeight", ctdPanelInst.lastResize[cf_id].height);

						}

						if(el_bounds.right > cf_bounds.right){
							ui.size.width = ctdPanelInst.lastResize[cf_id].width;
							$(this).resizable( "option", "maxWidth", ctdPanelInst.lastResize[cf_id].width);
						}

						if(el_bounds.bottom > cf_bounds.bottom){
							ui.size.height = ctdPanelInst.lastResize[cf_id].height;
							$(this).resizable( "option", "maxHeight", ctdPanelInst.lastResize[cf_id].height);
						}

						ctdPanelInst.lastResize[cf_id].width = ui.size.width;
						ctdPanelInst.lastResize[cf_id].height = ui.size.height;
						ctdPanelInst.lastResize[cf_id].left = ui.position.left;
						ctdPanelInst.lastResize[cf_id].top = ui.position.top;

					}
				}
			});

			if(customtextdesign_disable_resize || is_fixed)
				$item.resizable('disable');

			drWr.draggable({

				start : function(event, ui){
					var $src = $(ui.helper.find('.ctd_item'));
					var cf = $src.data('cf');
					ctdPanelInst.lastPos[cf] = {
						left: ui.position.left,
						top: ui.position.top
					}
					$('.ft-hover').removeClass('ft-hover');
				},

				drag : function(event, ui){
					var $src = $(ui.helper.find('.ctd_item'));
					var cf_id = $src.data('cf');
					$cf = $('#' + cf_id);
					if($cf.length)
					{
						var el_bounds = $src.offset();
						var container_offset = ctdPanelInst.container.offset();

						var d_left = ui.position.left - ctdPanelInst.lastPos[cf_id].left;
						var d_top = ui.position.top - ctdPanelInst.lastPos[cf_id].top;

						el_bounds = {left: parseInt(el_bounds.left - container_offset.left + d_left), top: parseInt(el_bounds.top - container_offset.top + d_top)}
						var cf_bounds = $cf.position();
						cf_bounds = {left: parseInt(cf_bounds.left), top: parseInt(cf_bounds.top + 1)};

						var el_width = $src.width() + 1;
						var el_height = $src.height() + 1;

						var cf_width = $cf.width() + 2;
						var cf_height = $cf.height() + 2;

						el_bounds.right = parseInt(el_bounds.left + el_width);
						el_bounds.bottom = parseInt(el_bounds.top + el_height);

						cf_bounds.right = parseInt(cf_bounds.left + cf_width);
						cf_bounds.bottom = parseInt(cf_bounds.top + cf_height);

						if(el_bounds.left < parseInt(cf_bounds.left)){
							ui.position.left = ctdPanelInst.lastPos[cf_id].left;
						}

						if(el_bounds.top < parseInt(cf_bounds.top)){
							ui.position.top = ctdPanelInst.lastPos[cf_id].top;
						}

						if(el_bounds.right > cf_bounds.right){
							ui.position.left = ctdPanelInst.lastPos[cf_id].left;
						}

						if(el_bounds.bottom > cf_bounds.bottom){
							ui.position.top = ctdPanelInst.lastPos[cf_id].top;
						}

						ctdPanelInst.lastPos[cf_id].left = ui.position.left;
						ctdPanelInst.lastPos[cf_id].top = ui.position.top;
					}
				}

			});

			drWr.find('.ui-resizable-handle').attr('title', customtextdesign_resize);
			if(customtextdesign_disable_drag || is_fixed){
				drWr.draggable( 'disable' )
			}

			$item.parent().rotatable({
				autoHide: false
			});

			if(is_fixed){
				$item.parent().find('.ui-rotatable-handle, .ft-front, .ft-back').hide();
			}

			var $wr = $item.closest('.ui-wrapper');
			$wr.data('ratio',$item.width() / $item.height());
			if($cf && $cf.length){
				var cfw = $cf.width() - 2;
				var cfh = $cf.height() - 2;
				if(!ctd_config.stretch_field){
					var wrw = $item.width();
					var wrh = $item.height();
					var proportions = wrw / wrh;
					if(proportions >= 1){
						if(wrw >= cfw){
							wrh = cfw / wrw * wrh;
							wrw = cfw;
							if(wrh > cfh){
								wrw = cfh / wrh * wrw;
								wrh = cfh;
							}
						}
						if(wrh >= cfh){
							wrw = cfh / wrh * wrw;
							wrh = cfh;
						}
					}else{
						if(wrh >= cfh){
							wrw = cfh / wrh * wrw;
							wrh = cfh;
							if(wrw > cfw){
								wrh = cfw / wrw * wrh;
								wrw = cfw;
							}
						}
						if(wrw >= cfw){
							wrh = cfw / wrw * wrh;
							wrw = cfw;
						}
					}
					$wr.css({width: wrw, height: wrh});
					$item.css({width: wrw, height: wrh});
					var new_pos = {left: (cfw - wrw) / 2 + pos.left, top: (cfh - wrh) / 2 + pos.top};
					drWr.css(new_pos);
				}else{
					$wr.css({width: cfw + 2, height: cfh + 2});
					$item.css({width: cfw + 2, height: cfh + 2});
				}
				$item.data('cf', $cf.prop('id'));
				$cf.data('item', timestamp);
			}

			$('.ft-container.selected').removeClass('selected');
			$item.closest('.ft-container').addClass('selected');
			$('.ctd_loader').fadeOut();
			$item.off('load');
			if($cf && $cf.length){
				$nextcf = $cf.next();
				if(!$nextcf.length){
					$nextcf = $('.ctd_cf:not(#ctd_cf_clone):eq(0)');
				}
				$nextcf.trigger('click');
			}
		}).prop('src',src).appendTo($markup.find('.ft-container'));
		$markup.insertBefore('.ctd_img');
		if(is_fixed){
			$text.closest('.draggable-wrapper').addClass('ctd_fixed_wrapper');
		}
		$('.ft-container.selected').removeClass('selected');
		$markup.find('.ft-container').addClass('selected');
		$('ul#ctd_imagecolor li:eq(0)').click();
		return false;
	}

	this.updateImageColor = function(){
		var timestamp = $('.ft-container.selected img').prop('id');
		if( ! timestamp || ! _panel.images[timestamp]) return false;
		var $image = $('#' + timestamp);
		if(!$image.length) return false;
		var image_src = $image.prop('src');

		var src_bkp = $image.data('src_bkp');
		if(!src_bkp){
			src_bkp = image_src;
			$image.data('src_bkp', src_bkp);
		}

		var $el = $('#ctd_imagecolor li.active a');

		if($el.hasClass('ctd_original_imagecolors')){
			$image.prop('src', src_bkp);
			return false;
		}

		var imagecolor = $('#ctd_imagecolor li.active a').data('color');
		if($el.hasClass('ctd_imagepicker')){
			imagecolor = '_' + $('.ctd_imagepicker').css('background-color').replace('#','');
			_panel.images[timestamp]['clr'] = imagecolor;
			_panel.images[timestamp]['color'] = 0;
		}else{
			_panel.images[timestamp]['clr'] = '';
			_panel.images[timestamp]['color'] = imagecolor;
		}

		$('.ctd_loader').fadeIn();
		$image
		.off('load.imagecolor').on('load.imagecolor',function(){
			$('.ctd_loader').fadeOut();
		}).prop('src', _panel.getImageColorSrc(image_src, imagecolor));


	}

	this.bringToFront = function(evt){
		evt.preventDefault();
		var $container = $(this).closest('.ft-container');
		var $elem = $container.find('img');
		var id_elem = $elem.prop('id');
		var is_text = $elem.hasClass('ctd_text_preview');
		var is_image = $elem.hasClass('ctd_image_preview');
		$elem = $elem.closest('.draggable-wrapper');
		$elem.insertBefore('.ctd_img');
		$('.ft-container.selected').removeClass('selected');
		return false;
	}

	this.sendToBack = function(evt){
		evt.preventDefault();
		var $container = $(this).closest('.ft-container');
		var $elem = $container.find('img');
		var id_elem = $elem.prop('id');
		var is_text = $elem.hasClass('ctd_text_preview');
		var is_image = $elem.hasClass('ctd_image_preview');
		$elem = $elem.closest('.draggable-wrapper');
		$elem.insertAfter('.ctd_img_mask2');
		$('.ft-container.selected').removeClass('selected');
		return false;
	}

	this.rotateItem = function(angle){
		if(isNaN(angle)){
			angle = $(this).data('angle');
			$('#ctd_rotator').slider('value', angle);
			$('#ctd_rotator_value').text(angle + '°');
		}

		var $container = $('.ft-container.selected');
		if(! $container.length) return false;
		var $wr = $container.find('.ui-wrapper');
		var n = "rotate(" + (-angle) + "deg)";
		$wr.css({
			"-moz-transform": n,
			"-o-transform": n,
			"-webkit-transform": n,
			"-ms-transform": n,
			transform: n
		});
		$wr.data('angle', -angle);
		return false;
	}

	this.removeItem = function(evt){
		evt.preventDefault();
		if( ! confirm(customtextdesign_delete_confirm)) return false;
		var $container = $(this).closest('.draggable-wrapper');
		var $elem = $container.find('img');
		var id_elem = $elem.prop('id');
		var is_text = $elem.hasClass('ctd_text_preview');
		var is_image = $elem.hasClass('ctd_image_preview');
		if(is_text){
			_panel.texts[id_elem] = null;
			delete(_panel.texts[id_elem]);
		}else{
			_panel.images[id_elem] = null;
			delete(_panel.images[id_elem]);
		}
		$container.remove();
		return false;
	}

	this.scaleItem = function(item, ratio, nomove){
		var $item = $(item);
		var width = $item.width();
		var height = $item.height();

		var new_width = width * ratio;
		var new_height = height * ratio;

		var modif = true;
		if(width < height){
			if(width == _panel.min_size){
				new_width = _panel.min_size;
				new_height = height;
				modif = false;
			}
		}else{
			if(height == _panel.min_size){
				new_height = _panel.min_size;
				new_width = width;
				modif = false;
			}
		}

		if(new_width < new_height){
			if( new_width < _panel.min_size && modif){
				var aspect_ratio = $item.data('ratio');
				new_width = _panel.min_size;
				new_height = _panel.min_size / aspect_ratio;
			}
		}else{
			if( new_height < _panel.min_size && modif){
				var aspect_ratio = $item.data('ratio');
				new_height = _panel.min_size;
				new_width = _panel.min_size * aspect_ratio;
			}
		}

		var $drg = $item.closest('.draggable-wrapper');
		var pos = $drg.position();
		if( ! nomove){
			pos = {left: pos.left * ratio, top: pos.top * ratio};
		}else{
			if(modif){
				var x_delta = (width - new_width) / 2;
				var y_delta = (height - new_height) / 2;
				if(Math.abs(x_delta)){
					x_delta = x_delta < 0 ? Math.ceil(x_delta):Math.floor(x_delta);
				}
				if(Math.abs(y_delta)){
					y_delta = y_delta < 0 && y_delta ? Math.ceil(y_delta):Math.floor(y_delta);
				}
				pos = {left: pos.left + x_delta, top: pos.top + y_delta};
			}
		}
		$drg.css(pos);

		var objCss = {width: new_width, height: new_height};
		$item.css(objCss);
		$item.find('.ctd_item').css(objCss);
	}

	this.checkBounds = function(msg){
		if(!customtextdesign_check_bounds || !customtextdesign_custom_fields) return true;

		var result = true;

		$('.ctd_item').each(function(){
			$elem = $(this);
			var cf_id = $elem.data('cf');
			if(cf_id && $('#'+cf_id).length){
				var el_bounds = $elem.get(0).getBoundingClientRect();
				var cf_bounds = $('#'+cf_id).get(0).getBoundingClientRect();

				var in_bound =
				el_bounds.left   >= cf_bounds.left &&
				el_bounds.top    >= cf_bounds.top &&
				el_bounds.right  <= cf_bounds.right &&
				el_bounds.bottom <= cf_bounds.bottom;

				if(!in_bound){
					result = false;
					$('.ctd_outbound').removeClass('ctd_outbound');
					$elem.closest('.ui-wrapper').addClass('ctd_outbound');
					return false;
				}
			}
		});

		if(msg && !result){
			if(customtextdesign_required_bounds){
				ctd_alert(customtextdesign_error['outbound']);
				return false;
			}else{
				if(confirm(customtextdesign_message['outbound'])){
					$('.ctd_outbound').removeClass('ctd_outbound');
					return true;
				}else{
					return false;
				};
			}
		}

		return result;
	}

	this.checkSize = function(elem, positioned){
		var $elem = $(elem);

		var cw = _panel.container.width();
		var w = $elem.width();

		var w_bkp = w;
		if( w > cw * _panel.coeff){
			w = cw * _panel.coeff;
			w = Math.max(20, w);
			$elem.css({width: w});
		}
		var actual_width = $('.ctd_img').width();
		var width_ratio = actual_width / customtextdesign_width[_panel.id_image];
		if(w == w_bkp){
			w = w * width_ratio;
			w = Math.max(20, w);
			$elem.css({width: w});
		}

		var measure = customtextdesign_measures[_panel.id_image];
		$cf = $('.ctd_cf.ctd_cf_active');
		if(measure && !$cf.length){
			var new_width = measure.width * width_ratio;
			if(!positioned){
				var new_x = measure.x_origin * width_ratio;
				var new_y = measure.y_origin * width_ratio;
				$elem.closest('.draggable-wrapper').css({left: new_x, top: new_y});
			}
			if(w > new_width){
				w = new_width;
				$elem.css({width: w});
			}
		}
	}

	this.selectData = function(orig,id_item){
		var $item = $('#'+id_item);
		var angle = $item.parent().data('angle');
		var drg = $item.closest('.draggable-wrapper').position();
		var pos1 = $item.position();
		var pos2 = $item.closest('.ui-wrapper').position();
		var pos3 = $item.closest('.ft-container').position();
		orig['x'] = drg.left + pos1.left + pos2.left + pos3.left;
		orig['y'] = drg.top + pos1.top + pos2.top + pos3.top;
		orig['scalex'] = 1;
		orig['scaley'] = 1;
		orig['angle'] = (isNaN(angle) ? 0 : angle) % 360;
		if(orig['angle']<0) orig['angle'] = 360 + orig['angle'];
		orig['angle'] = orig['angle'] % 360
		orig['width'] = $item.width();
		orig['height'] = $item.height();
	}

	this.collectData = function(action){
		var data = {};
		data['action'] = action;
		data['id_product'] = id_product;
		data['id_attribute'] = _panel.id_attribute;
		data['quantity'] = $('#quantity_wanted').val();
		data['id_image'] = _panel.id_image;
		data['hash'] = _panel.hash;
		data['width'] = $('.ctd_img').width();
		data['custom_width'] = _panel.parseFloat($('#ctd_customsize_width').data('width'));
		data['custom_height'] = _panel.parseFloat($('#ctd_customsize_height').data('height'));
		data['custom_color'] = $('#ctd_customcolor_data').data('customcolor') || '';
		data['token'] = static_token;
		$items = $('.ctd_item');
		var ln = $items.length;

		$items = $('.draggable-wrapper:not(.ctd_fxd) .ctd_item');
		$items.each(function(i,item){
			var $item = $(item);
			var id_item = $item.prop('id');
			var is_text = $item.hasClass('ctd_text_preview');
			var item_data = null;

			var k = i;
			if($item.closest('.ctd_fxd').length){
				k = ln;
			}

			if(is_text){
				item_data = _panel.texts[id_item];
				data['item_'+k] = item_data;
				data['item_'+k]['type'] = 'text';
			}else{
				item_data = _panel.images[id_item];
				data['item_'+k] = {};
				data['item_'+k] = item_data;
				data['item_'+k]['type'] = 'image';
			}
			data['item_'+k]['id'] = id_item;
			_panel.selectData(data['item_'+k],id_item);
		});

		$items = $('.draggable-wrapper.ctd_fxd .ctd_item');
		$items.each(function(i,item){
			var $item = $(item);
			var id_item = $item.prop('id');
			var is_text = $item.hasClass('ctd_text_preview');
			var item_data = null;

			var k = i;
			if($item.closest('.ctd_fxd').length){
				k = ln;
			}

			if(is_text){
				item_data = _panel.texts[id_item];
				data['item_'+k] = item_data;
				data['item_'+k]['type'] = 'text';
			}else{
				item_data = _panel.images[id_item];
				data['item_'+k] = {};
				data['item_'+k] = item_data;
				data['item_'+k]['type'] = 'image';
			}
			data['item_'+k]['id'] = id_item;
			_panel.selectData(data['item_'+k],id_item);
		});

		return data;
	}

	this.calculatePrice = function(){
		if( ! _panel.saveCustomSize()) return false;
		var data = _panel.collectData('calculate_price');
		$('.ctd_loader').fadeIn();
		$.post(customtextdesign_config.module_dir+'inc/ajaxdesign.php',data,function(response){
			$('.ctd_loader').hide();
			if(response.error){
				var error = response.error;
				if(customtextdesign_error[error])
					ctd_alert(customtextdesign_error[error]);
				return;
			}
			$('.ctd_prices').empty();
			var new_html = '';
			$.each(response,function(id_item,item){
				html = '';
				if(parseInt(id_item)){
					var $item = $('#'+id_item);
					var src = $item.prop('src');
					html += '<div>';
					html += '<span>'+item['str_price']+'</span>';
					html += '<img style="max-height:30px;max-width:200px;vertical-align: text-bottom;" src="'+src+'">';
					html += '</div>';
				}else if(id_item == 'str_sub_total'){
					html = '';
					html += '<div>';
					html += '<span><b>'+customtextdesign_design_total+'</b><hr>'+item+'</span>';
					html += '</div>';
				}else if(id_item == 'str_product_price'){
					html = '';
					html += '<div>';
					html += '<span><b>'+customtextdesign_product_price+'</b><hr>'+item+'</span>';
					html += '</div>';
				}else if(id_item == 'str_total'){
					html = '';
					html += '<div>';
					html += '<span><b>'+customtextdesign_total+'</b><hr>'+item+'</span>';
					html += '</div>';
				}else if(id_item == 'str_total_wt'){
					html = '';
					html += '<div>';
					html += '<span><b>'+customtextdesign_total_wt+'</b><hr>'+item+'</span>';
					html += '</div>';
				}
				new_html += html;
			});
			if(response.TTC == 1){
				new_html += '<div class="ctd_ttc">*'+customtextdesign_ttc+'</div>'
			}else if(response.TTC == 2){
				new_html += '<div class="ctd_ttc">*'+customtextdesign_hc+'</div>'
			}
			$(new_html).appendTo('.ctd_prices');
			$('.ctd_details').fadeIn(500,function(){
				$('.ctd_add_to_cart').hide();
				$('.ctd_details_buttons').show();
			});
			$('.ctd_design').fadeOut(500);
			$('.ctd_preview').addClass('ctd_details_shown');
			},'json');
		return false;
	}

	this.response = null;
	this.addToCart = function(){
		if( ! _panel.saveCustomSize()) return false;
		if( ! _panel.checkBounds(true)) return false;
		var data = _panel.collectData('add_to_cart');
		$('.ctd_loader').fadeIn();
		$.post(customtextdesign_config.module_dir+'inc/ajaxdesign.php',data,function(response){
			$('.ctd_loader').hide();
			if(response.error){
				var error = response.error;
				if(customtextdesign_error[error])
					ctd_alert(customtextdesign_error[error]);
				return;
			}else if(response.success){
				_panel.response = response;
				var ctd_dir = customtextdesign_config.module_dir
				$('.ctd_custom_product').show();
				var $row = $('<tr>').css('display','none');
				var $cell = $('<td>').css({'max-width': '150px','overflow': 'hidden'});
				var $preview = $('<a target="_blank" href="' + response.link + '"><img style="width:150px" src="' + ctd_dir + 'data/cache/' + response.preview + '"/></a>').appendTo($cell);
				$cell.appendTo($row);
				$('<td>',{class : 'ctd_extra_details'}).html(response.attributes).appendTo($row);
				$('<td>',{class : 'ctd_extra_details'}).html(response.price).appendTo($row);
				$('<td>').html(
					'<a data-id_custom_product="'+response.id_custom_product+'" class="addcart_custom_product" title="'+customtextdesign_addcart+'" href="#"></a>&nbsp;'+
					'<a target="_blank" href="' + response.link + '" class="preview_custom_product" title="'+ customtextdesign_preview+'"></a>&nbsp;'+
					'<a data-id_custom_product="'+response.id_custom_product+'" class="remove_custom_product" title="'+customtextdesign_delete+'" href="#"></a>'
				).appendTo($row);
				$row.appendTo('.ctd_custom_product tbody').show('fast');
				if(customtextdesign_popup){
					ctd_hide_popup();
				}
				if($('#image-block').length){
					$("html, body").animate({ scrollTop: $('#image-block').offset().top - 10 },'slow');
				}
				$('#add_to_cart [type=submit]').click();
			}
			},'json');
		return false;
	}

	this.downloadImage = function(){
		var data = _panel.collectData('download_image');
		$('.ctd_loader').fadeIn();
		$.post(customtextdesign_config.module_dir+'inc/ajaxdesign.php',data,function(response){
			$('.ctd_loader').hide();
			if(response.error){
				var error = response.error;
				if(customtextdesign_error[error])
					ctd_alert(customtextdesign_error[error]);
				return;
			}else if(response.success){
				var ctd_dir = customtextdesign_config.module_dir
				if(response.preview){
					var force = customtextdesign_download.replace('__image__', response.preview).replace('__id_product__', response.id_product);
					location.href = force;
				}
			}
			},'json');
		return false;
	}

	this.saveCustomSize = function( can_alert ){

		if(! customtextdesign_customsize && ! customtextdesign_customcolor) return true;

		can_alert = typeof can_alert != 'undefined' ? can_alert: true;

		if(customtextdesign_customsize){
			var width = _panel.parseFloat($('#ctd_customsize_width').val());
			var height = _panel.parseFloat($('#ctd_customsize_height').val());

			if(!width){
				if(can_alert){
					ctd_alert(customtextdesign_error['minwidth']);
					$('.ctd_a_dimension').trigger('click')
					$('#ctd_customsize_width').focus().select();
				}
				return false;
			}
			if(!height){
				if(can_alert){
					ctd_alert(customtextdesign_error['minheight']);
					$('.ctd_a_dimension').trigger('click')
					$('#ctd_customsize_height').focus().select();
				}
				return false;
			}

			if(customtextdesign_minw && width < customtextdesign_minw){
				if(can_alert){
					ctd_alert(customtextdesign_error['minwidth']);
					$('.ctd_a_dimension').trigger('click')
					$('#ctd_customsize_width').focus().select();
				}
				return false;
			}
			if(customtextdesign_maxw && width > customtextdesign_maxw){
				if(can_alert) {
					ctd_alert(customtextdesign_error['maxwidth']);
					$('.ctd_a_dimension').trigger('click')
					$('#ctd_customsize_width').focus().select();
				}
				return false;
			}
			if(customtextdesign_minh && height < customtextdesign_minh){
				if(can_alert){
					ctd_alert(customtextdesign_error['minheight']);
					$('.ctd_a_dimension').trigger('click')
					$('#ctd_customsize_height').focus().select();
				}
				return false;
			}
			if(customtextdesign_maxh && height > customtextdesign_maxh){
				if(can_alert){
					ctd_alert(customtextdesign_error['maxheight']);
					$('.ctd_a_dimension').trigger('click')
					$('#ctd_customsize_height').focus().select();
				}
				return false;
			}

			$('#ctd_customsize_width').data('width', width);
			$('#ctd_customsize_height').data('height', height);

			var w = $('.ctd_img').width();
			var h = $('.ctd_img').height();

			if(width >= height){
				w = Math.max(w, h);
				var h = w / width * height;
				var css = {width: w, height: h};
				$('.ctd_preview img.ctd_prv').css(css);
			}else{
				h = Math.max(w, h);
				var w = h / height * width;
				var css = {width: w, height: h};
				$('.ctd_preview img.ctd_prv').css(css);
			}

			var $ctd_product_panel = $('.ctd_product_panel');
			$ctd_product_panel.css('max-width', (+w+2) + 'px');
			$('.ctd_panel_content .ctd_preview img.ctd_prv').each(function(){
				$(this).css('max-width', w + 'px');
			});
		}

		if($(this).is('.ctd_customsize_save')){
			var customcolor = "";
			if($('#ctd_customcolor li.active').length){
				customcolor = '_'+$('#ctd_customcolor li.active a').data('color');
			}else if($('.ctd_customcolor').length){
				customcolor = $('.ctd_customcolor').css('background-color').replace('#','');
			}else return false;

			$('#ctd_customcolor_data').data('customcolor', customcolor);
			$('.ctd_loader').fadeIn();
			$('.ctd_img')
			.off('load').on('load',function(){
				$('.ctd_loader').fadeOut();
			}).prop('src', _panel.getCustomColorSrc(customcolor));
			return false;
		}

		return true;
	}

	this.updateCustomColor = function(){
		var customcolor = $('.ctd_customcolor').css('background-color').replace('#','');
	}

	this.applyCustomColor = function(){
		var customcolor = $('#ctd_customcolor_input').val().trim();
		if(customcolor.indexOf('#') != 0){
			customcolor = '#' + customcolor;
		}
		$('.ctd_customcolor')
		.css('background-color', customcolor)
		.ColorPickerSetColor(customcolor);
	}

	this.updateCustomFields = function(){
		if( ! customtextdesign_custom_fields) return;
		var original_width = +customtextdesign_width[_panel.id_image];
		var current_width = $('.ctd_img').width();
		var ratio = current_width / original_width;
		var fields = customtextdesign_fields[ctdPanelInst.id_image];
		$('.ctd_cf:not(.ctd_cf_'+_panel.id_image+')').hide();
		if(fields){
			$.each(fields, function(id_custom_field, field){
				if($('#cf' + field.id).length){
					$cf = $('#cf' + field.id);
					$cf.css({
						left: Math.ceil(+this.x * ratio),
						top: Math.ceil(+this.y * ratio),
						width: Math.ceil(this.w * ratio),
						height: Math.ceil(this.h * ratio),
						borderColor: customtextdesign_initial_color
					}).show();
				}else{
					var lbl = this.label || '';
					$cf = $('#ctd_cf_clone').clone().prop('id', 'cf' + field.id).addClass('ctd_cf_'+_panel.id_image).css({
						left: Math.ceil(+field.x * ratio),
						top: Math.ceil(+field.y * ratio),
						width: Math.ceil(+field.w * ratio),
						height: Math.ceil(+field.h * ratio),
						borderColor: customtextdesign_initial_color
					})
					.appendTo('.ctd_preview').show()
					.find('span').text(lbl);
				}
			});
		}else{
			$('.ctd_cf_active').removeClass('ctd_cf_active');
			return;
		}
		if(!$('.ctd_cf.ctd_cf_'+_panel.id_image+'.ctd_cf_active').length){
			$('.ctd_cf.ctd_cf_'+_panel.id_image+':eq(0)').trigger('click');
		}
	}

	return this.init();
}

var ctdPanelInst = null;
$(function(){

	if(customtextdesign_initialized){
		console.log('ctd recalled');
		return;
	}
	customtextdesign_initialized = 1;

	customtextdesign_measures = $.parseJSON(customtextdesign_measures);
	customtextdesign_overlays = $.parseJSON(customtextdesign_overlays);
	customtextdesign_masks = $.parseJSON(customtextdesign_masks);
	customtextdesign_replaces = $.parseJSON(customtextdesign_replaces);
	customtextdesign_width = $.parseJSON(customtextdesign_width);
	customtextdesign_fields = $.parseJSON(customtextdesign_fields);

	$('.ctd_product_panel').each(function(){
		ctdPanelInst = new ctdPanel(this);
	});
	ctdPanelInst.initList();
});

function ctd_scroll(){
	$('html, body').stop().animate({
		scrollTop: ctdPanelInst.panel.offset().top - 4
		}, 500);
}

function ctd_show_popup(){
	if($('.ctd_no_show').length) return false;
	$('.ctd_product_panel.ctd_popup').show();
	$('div.ctd_bg').show().off('click').on('click', ctd_hide_popup);
	$('body').addClass('ctd_allow_resize');
	$(window).trigger('resize');
	return false;
}

function ctd_hide_popup(){
	$('.ctd_product_panel.ctd_popup').hide();
	$('div.ctd_bg').hide();
	return false;
}

var t_hide = null;
function dbg(obj){
	//return false;
	var html = $('.dbg').html();
	if(html)
		html = html + '<br>';
	html = "";
	$('.dbg').html(html + 'dbg: '+ (new Date().getTime()).toString().substring(10) +' -> ' + obj).show();
	clearTimeout(t_hide);
	t_hide = null;
	t_hide = setTimeout(function(){
		$('.dbg').html('').hide();
		},2000);
}