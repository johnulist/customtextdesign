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

jQuery.fn.getByImageId = function(id) {
	var result = $.grep(this, function(e){ return e.id_image == id; });
	return $(result).get(0);
};

$(function(){

	$('#link-ModuleCustomtextdesign').text(ctd_tab_text).addClass('ctd-tab-row');
	ctd_admin.ajaxify();
	$(ctd_admin.id)
	.on('click','.ctd_sls',function(){this.select()})
	.on('click','.panel_remove',ctd_admin.removePanel)
	.on('focus','.ctd_lang_container',ctd_admin.expandLangs)
	.on('click','.ctd_lang_container',function(e){e.stopPropagation();})
	.on('change','.ctd_lang_input',ctd_admin.saveLangInput)
	.on('keyup','.ctd_lang input',ctd_admin.collapseLangs)
	.on('click','.ctd_toggle',ctd_admin.toggle)
	.on('click','.ctd_a_thumb',ctd_admin.showThumb)
	.on('click','.ctd_size_save',ctd_admin.saveSize)
	.on('click','.ctd_size_copy',ctd_admin.copySize)
	.on('change','.ctd_chkgroup',ctd_admin.checkGroup)
	.on('change','.ctd_chkgroup_all',ctd_admin.checkGroupGlobal)
	.on('click','.ctd_chk_all',ctd_admin.checkGroupAll)
	.on('click','.ctd_chk_none',ctd_admin.checkGroupNone)
	.on('click','.ctd_origin.active',ctd_admin.endOrigin)
	.on('click','.ctd_crosshair',ctd_admin.toggleOrigin)
	.on('mousemove','.ctd_origin',ctd_admin.moveOrigin)
	.on('submit','#ctd_upload_form_overlay',ctd_admin.uploadOverlayStart)
	.on('submit','#ctd_upload_form_mask',ctd_admin.uploadMaskStart)
	.on('submit','#ctd_upload_form_mask2',ctd_admin.uploadMask2Start)
	.on('submit','#ctd_upload_form_replace',ctd_admin.uploadReplaceStart)
	.on('click','.ctd_delete_overlay',ctd_admin.deleteOverlay)
	.on('click','.ctd_delete_mask',ctd_admin.deleteMask)
	.on('click','.ctd_delete_mask2',ctd_admin.deleteMask2)
	.on('click','.ctd_delete_replace',ctd_admin.deleteReplace)
	.on('click','.ctd_default_img',ctd_admin.makeDefaultImg)
	;
	$('html').click(ctd_admin.expandLangs);
});

var ctd_admin = {

	id : '#product-tab-content-ModuleCustomtextdesign',
	measures : {},
	overlays : {},
	masks : {},
	masks2 : {},
	replaces : {},
	origins : {},

	makeDefaultImg : function(){
		var id_image = $('.ctd_image img.ctd_preview').data('id_image');
		var data = {};
		data['action'] = 'save_product_options';
		data['name'] = 'id_default_img';
		data['value'] = id_image;
		data['id_product'] = id_product;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+id_image+']')
				.addClass('ctd_isdefault').siblings().removeClass('ctd_isdefault');
				alert(ctd_str_default_img);
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
		return false;
	},

	deleteOverlay : function(){
		if( ! confirm(ctd_str_sure)) return false;
		var id_image = $(this).data('id_image');
		var data = {};
		data['action'] = 'delete_overlay';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		ctd_admin.overlays[id_image] = null;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+id_image+']')
				.removeClass('ctd_overlayed');
				var imagesrc = ctd_module_dir + 'img/pixel.png';
				$('#ctd_current_overlay').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
				$('.ctd_delete_overlay').hide();
				$('.ctd_overlay').prop('src',imagesrc);
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
		return false;
	},

	uploadOverlayStart : function(){
		$('#ctd_iframe_overlay').off('load').on('load',ctd_admin.uploadOverlayComplete);
		//$('.ctd_uploader').fadeIn();
	},

	uploadOverlayComplete : function(){
		$('.ctd_uploader').fadeOut();
		$doc = $('#ctd_iframe_overlay').contents();
		var json = $doc.find('body').text();
		var $json = {};
		try{
			$json = $.parseJSON(json);
		}catch(ex){}
		if( ! $json) return;
		if($json.error == 0){
			var id_image = $json.id_image;
			var filename = $json.filename;
			var imagesrc = ctd_module_dir + 'data/overlay/' + filename;
			var id_image = $('.ctd_image img.ctd_preview').data('id_image');
			var data = {};
			data['action'] = 'save_overlay';
			data['id_product'] = id_product;
			data['id_image'] = id_image;
			data['image'] = filename;
			ctd_admin.overlays[id_image] = data;
			ctd_admin.post(data,function(data){
				if(data && data.success){
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_success_msg').show();
					$('.ctd_thumbs > a[data-id='+id_image+']')
					.addClass('ctd_overlayed');
					$('#ctd_current_overlay').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
					$('.ctd_delete_overlay').data('id_image',id_image).show();
					$('.ctd_overlay').prop('src',imagesrc);
				}else if(+data.success == 0){
					$('#ctd_ajax_success_msg').hide();
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_error_msg').show();
				}
			});
		}else if($json.error){
			alert($json.error);
		}
		$('#ctd_upload_form_overlay').get(0).reset();
	},

	deleteMask : function(){
		if( ! confirm(ctd_str_sure)) return false;
		var id_image = $(this).data('id_image');
		var data = {};
		data['action'] = 'delete_mask';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		ctd_admin.masks[id_image] = null;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+id_image+']')
				.removeClass('ctd_masked');
				var imagesrc = ctd_module_dir + 'img/pixel.png';
				$('#ctd_current_mask').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
				$('.ctd_delete_mask').hide();
				$('.ctd_mask').prop('src',imagesrc);
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
		return false;
	},

	uploadMaskStart : function(){
		$('#ctd_iframe_mask').off('load').on('load',ctd_admin.uploadMaskComplete);
		//$('.ctd_uploader').fadeIn();
	},

	uploadMaskComplete : function(){
		$('.ctd_uploader').fadeOut();
		$doc = $('#ctd_iframe_mask').contents();
		var json = $doc.find('body').text();
		var $json = {};
		try{
			$json = $.parseJSON(json);
		}catch(ex){}
		if( ! $json) return;
		if($json.error == 0){
			var id_image = $json.id_image;
			var filename = $json.filename;
			var imagesrc = ctd_module_dir + 'data/mask/' + filename;
			var id_image = $('.ctd_image img.ctd_preview').data('id_image');
			var data = {};
			data['action'] = 'save_mask';
			data['id_product'] = id_product;
			data['id_image'] = id_image;
			data['image'] = filename;
			ctd_admin.masks[id_image] = data;
			ctd_admin.post(data,function(data){
				if(data && data.success){
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_success_msg').show();
					$('.ctd_thumbs > a[data-id='+id_image+']')
					.addClass('ctd_masked');
					$('#ctd_current_mask').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
					$('.ctd_delete_mask').data('id_image',id_image).show();
					$('.ctd_mask').prop('src',imagesrc);
				}else if(+data.success == 0){
					$('#ctd_ajax_success_msg').hide();
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_error_msg').show();
				}
			});
		}else if($json.error){
			alert($json.error);
		}
		$('#ctd_upload_form_mask').get(0).reset();
	},

	deleteMask2 : function(){
		if( ! confirm(ctd_str_sure)) return false;
		var id_image = $(this).data('id_image');
		var data = {};
		data['action'] = 'delete_mask2';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		ctd_admin.masks2[id_image] = null;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+id_image+']')
				.removeClass('ctd_mask2ed');
				var imagesrc = ctd_module_dir + 'img/pixel.png';
				$('#ctd_current_mask2').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
				$('.ctd_delete_mask2').hide();
				$('.ctd_mask2').prop('src',imagesrc);
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
		return false;
	},

	uploadMask2Start : function(){
		$('#ctd_iframe_mask2').off('load').on('load',ctd_admin.uploadMask2Complete);
		//$('.ctd_uploader').fadeIn();
	},

	uploadMask2Complete : function(){
		$('.ctd_uploader').fadeOut();
		$doc = $('#ctd_iframe_mask2').contents();
		var json = $doc.find('body').text();
		var $json = {};
		try{
			$json = $.parseJSON(json);
		}catch(ex){}
		if( ! $json) return;
		if($json.error == 0){
			var id_image = $json.id_image;
			var filename = $json.filename;
			var imagesrc = ctd_module_dir + 'data/mask2/' + filename;
			var id_image = $('.ctd_image img.ctd_preview').data('id_image');
			var data = {};
			data['action'] = 'save_mask2';
			data['id_product'] = id_product;
			data['id_image'] = id_image;
			data['image'] = filename;
			ctd_admin.masks2[id_image] = data;
			ctd_admin.post(data,function(data){
				if(data && data.success){
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_success_msg').show();
					$('.ctd_thumbs > a[data-id='+id_image+']')
					.addClass('ctd_mask2ed');
					$('#ctd_current_mask2').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
					$('.ctd_delete_mask2').data('id_image',id_image).show();
					$('.ctd_mask2').prop('src',imagesrc);
				}else if(+data.success == 0){
					$('#ctd_ajax_success_msg').hide();
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_error_msg').show();
				}
			});
		}else if($json.error){
			alert($json.error);
		}
		$('#ctd_upload_form_mask2').get(0).reset();
	},

	deleteReplace : function(){
		if( ! confirm(ctd_str_sure)) return false;
		var id_image = $(this).data('id_image');
		var data = {};
		data['action'] = 'delete_replace';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		ctd_admin.replaces[id_image] = null;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$image = $('.ctd_thumbs > a[data-id='+id_image+']')
				.removeClass('ctd_replaced');
				var imagesrc = ctd_module_dir + 'img/pixel.png';
				$('#ctd_current_replace').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
				$('.ctd_delete_replace').hide();
				$('.ctd_preview').prop('src',$image.prop('href'));
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
		return false;
	},

	uploadReplaceStart : function(){
		$('#ctd_iframe_replace').off('load').on('load',ctd_admin.uploadReplaceComplete);
		//$('.ctd_uploader').fadeIn();
	},

	uploadReplaceComplete : function(){
		$('.ctd_uploader').fadeOut();
		$doc = $('#ctd_iframe_replace').contents();
		var json = $doc.find('body').text();
		var $json = {};
		try{
			$json = $.parseJSON(json);
		}catch(ex){}
		if( ! $json) return;
		if($json.error == 0){
			var id_image = $json.id_image;
			var filename = $json.filename;
			var imagesrc = ctd_module_dir + 'data/replace/' + filename;
			var id_image = $('.ctd_image img.ctd_preview').data('id_image');
			var data = {};
			data['action'] = 'save_replace';
			data['id_product'] = id_product;
			data['id_image'] = id_image;
			data['image'] = filename;
			ctd_admin.replaces[id_image] = data;
			ctd_admin.post(data,function(data){
				if(data && data.success){
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_success_msg').show();
					$('.ctd_thumbs > a[data-id='+id_image+']')
					.addClass('ctd_replaced');
					$('#ctd_current_replace').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
					$('.ctd_delete_replace').data('id_image',id_image).show();
					$('.ctd_preview').prop('src',imagesrc);
				}else if(+data.success == 0){
					$('#ctd_ajax_success_msg').hide();
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_error_msg').show();
				}
			});
		}else if($json.error){
			alert($json.error);
		}
		$('#ctd_upload_form_replace').get(0).reset();
	},

	init : function(){
		$.each(ctd_measures,function(){
			var id_image = this.id_image;
			var $img = $('.ctd_thumbs > a[data-id='+id_image+']')
			.addClass('ctd_measured');
			ctd_admin.measures[id_image] = this;
		});

		$.each(ctd_overlays,function(){
			var id_image = this.id_image;
			var $img = $('.ctd_thumbs > a[data-id='+id_image+']')
			.addClass('ctd_overlayed');
			ctd_admin.overlays[id_image] = this;
		});

		$.each(ctd_masks,function(){
			var id_image = this.id_image;
			var $img = $('.ctd_thumbs > a[data-id='+id_image+']')
			.addClass('ctd_masked');
			ctd_admin.masks[id_image] = this;
		});

		$.each(ctd_masks2,function(){
			var id_image = this.id_image;
			var $img = $('.ctd_thumbs > a[data-id='+id_image+']')
			.addClass('ctd_mask2ed');
			ctd_admin.masks2[id_image] = this;
		});

		$.each(ctd_replaces,function(){
			var id_image = this.id_image;
			var $img = $('.ctd_thumbs > a[data-id='+id_image+']')
			.addClass('ctd_replaced');
			ctd_admin.replaces[id_image] = this;
		});

		var cf_selected = false;
		$.each(ctd_custom_fields,function(){
			var id_image = +this.id_image;
			var id_image_current = $('#ctd_cf_container .ctd_preview').data('id_image');
			var $img = $('.ctd_thumbs > a[data-id='+id_image+']')
			.addClass('ctd_hasfields');
			var hidden = id_image != id_image_current;
			var lbl = '';
			if(ctd_custom_fields_trans[this.id_custom_field] && ctd_custom_fields_trans[this.id_custom_field][ctd_id_lang]){
				lbl = ctd_custom_fields_trans[this.id_custom_field][ctd_id_lang]['label'];
			}
			$cf = ctd_admin.addCF(this, hidden, lbl);
			if( ! hidden && !cf_selected){
				ctd_admin.select_CF($cf);
				cf_selected = true;
			}
		});

		$('.ctd_measure')
		.draggable({
			/*containment: ".ctd_image",*/
			stop: function( event, ui ) {
				$('input.size').focus();
			},
		})
		.resizable({
			/*containment: ".ctd_image",*/
			handles: "e, w",
			minWidth: 65,
			stop: function( event, ui ) {
				$('input.size').focus();
			},
		});
		$('.ctd_thumbs > a:eq(0)').click();

		var initial_color = $('#ctd_initial_color').val().trim();
		if( ! initial_color){
			initial_color = '#11daf5';
		}
		if(initial_color.indexOf('#') != 0){
			initial_color = '#' + initial_color;
		}
		$('.ctd_initial_color_picker').ColorPicker({
			color: initial_color,
			onChange: function (hsb, hex, rgb) {
				$('#ctd_initial_color').val('#' + hex);
				$('.ctd_initial_color_picker').css('backgroundColor', '#' + hex);
				clearTimeout(ctd_admin.colorUpdater);
				ctd_admin.colorUpdater = setTimeout(ctd_admin.applyColor,333);
			}
		});

		$('#ctd_initial_color').on('change', function(){
			var color = $('#ctd_initial_color').val().trim();
			if(color.indexOf('#') != 0){
				color = '#' + color;
			}
			$('.ctd_initial_color_picker')
			.css('background-color', color)
			.ColorPickerSetColor(color);
		});

		//-----

		var initial_img_color = $('#ctd_initial_img_color').val().trim();
		if( ! initial_img_color){
			initial_img_color = '#11daf5';
		}
		if(initial_img_color.indexOf('#') != 0){
			initial_img_color = '#' + initial_img_color;
		}
		$('.ctd_initial_img_color_picker').ColorPicker({
			color: initial_img_color,
			onChange: function (hsb, hex, rgb) {
				$('#ctd_initial_img_color').val('#' + hex);
				$('.ctd_initial_img_color_picker').css('backgroundColor', '#' + hex);
				clearTimeout(ctd_admin.colorImgUpdater);
				ctd_admin.colorImgUpdater = setTimeout(ctd_admin.applyImgColor,333);
			}
		});

		$('#ctd_initial_img_color').on('change', function(){
			var color = $('#ctd_initial_img_color').val().trim();
			if(color.indexOf('#') != 0){
				color = '#' + color;
			}
			$('.ctd_initial_img_color_picker')
			.css('background-color', color)
			.ColorPickerSetColor(color);
		});

		$('#ctd_add_cf').on('click', ctd_admin.addNewCF);
		$('#ctd_cf_container')
		.on('mousedown', '.ctd_cf', ctd_admin.selectCF)
		.on('click', '.ctd_del_cf', ctd_admin.deleteCF)
		.on('click', '.ctd_dup_cf', ctd_admin.duplicateCF);
	},

	duplicateCF: function(){
		var $cf = $(this).parent();
		var cf_data = ctd_admin.getCFdata($cf);
		cf_data.id = new Date().getTime();
		var cftrans = {};
		$('#ctd_cf_lang input.ctd_lang_input').each(function(){
			var id_lang = +$(this).data('lang');
			if($(this).val()){
				cftrans['l' + id_lang] = $(this).val();
			}
		});
		$cf = ctd_admin.addCF(cf_data, 0);
		ctd_admin.select_CF($cf);
		ctd_admin.saveCF(cf_data,function(){
			if(! $.isEmptyObject(cftrans)){
				$('#ctd_cf_lang input.ctd_lang_input').each(function(){
					var id_lang = +$(this).data('lang');
					if(cftrans['l' + id_lang]){
						$(this).val(cftrans['l' + id_lang]).trigger('change');
					}
				});
			}
		});



		return false;
	},

	deleteCF: function(){
		if( ! confirm(ctd_message.confirm_del_cf)) return false;
		var $cf = $(this).parent();
		var id = $cf.prop('id');

		var cf_data = {
			action: 'delete_cf',
			id: id,
			id_image: $('#ctd_cf_container .ctd_preview').data('id_image')
		};

		$('.ctd_thumbs > a[data-id='+cf_data.id_image+']').addClass('ctd_loading');
		ctd_admin.post(cf_data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$cf.remove();
				if( ! $('.ctd_cf:visible').length){
					$('.ctd_thumbs > a[data-id='+cf_data.id_image+']').removeClass('ctd_hasfields');
				}else{
					ctd_admin.select_CF($('.ctd_cf:visible:eq(0)'));
				}
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
			$('.ctd_thumbs > a[data-id='+cf_data.id_image+']').removeClass('ctd_loading');
		});
		return false;
	},

	selectCF: function(){
		ctd_admin.select_CF($(this));
	},

	select_CF: function($cf){
		if( ! $cf.length) return;
		ctd_admin.id_cf = $cf.prop('id');
		$cf.addClass('ctd_cf_active').siblings('.ctd_cf.ctd_cf_active').removeClass('ctd_cf_active');
		ctd_admin.setCFLang($cf);
	},

	setCFLang: function($cf){
		var cftrans = null;
		var id_custom_field = +$cf.data('id_custom_field');
		if(ctd_custom_fields_trans[ctd_admin.id_cf]){
			cftrans = ctd_custom_fields_trans[ctd_admin.id_cf];
		}else if(id_custom_field && ctd_custom_fields_trans[id_custom_field]){
			cftrans = ctd_custom_fields_trans[id_custom_field];
		}
		if(cftrans){
			$('#ctd_cf_lang input.ctd_lang_input').each(function(){
				var id_lang = $(this).data('lang');
				if(cftrans[id_lang]){
					$(this).val(cftrans[id_lang]['label']);
				}else{
					$(this).val('');
				}
			});
		}else{
			$('#ctd_cf_lang input.ctd_lang_input').val('');
		}
	},

	addNewCF: function(){
		var $ctd_image = $('#ctd_add_cf').closest('.ctd_image');
		var id_image = $('#ctd_cf_container .ctd_preview').data('id_image');
		var timestamp = new Date().getTime();
		if($('.ctd_cf_active').length){
			var data = ctd_admin.getCFdata($('.ctd_cf_active:eq(0)'));
			data.x = 5;
			data.y = 5;
			data.id_custom_field= 0;
			data.id = timestamp;
		}else{
			var max_w = $ctd_image.width();
			var max_h = $ctd_image.height();
			var size = Math.min(max_w, max_h) / 5;

			var data = {
				action: 'save_cf',
				id_custom_field: 0,
				id_product: id_product,
				id_image: id_image,
				x: 5,
				y: 5,
				w: size,
				h: size,
				id: timestamp
			};
		}
		$cf = ctd_admin.addCF(data);
		ctd_admin.select_CF($cf);
		ctd_admin.saveCF(data);
		return false;
	},

	addCF: function(data, hidden, lbl){
		var hidden = hidden || 0;
		var lbl = lbl || '';
		$ctd_image = $('#ctd_cf_container');
		var $cf = $('#ctd_cf_clone').clone().data('id_custom_field', data.id_custom_field).prop('id', data.id).addClass('ctd_cf_'+data.id_image).css({
			left: +data.x,
			top: +data.y,
			width: +data.w,
			height: +data.h
		}).appendTo($ctd_image);
		$cf.draggable({
			containment: "#ctd_cf_container",
			stop: function( event, ui ) {
				ctd_admin.saveCF(ctd_admin.getCFdata(event.target));
			},
		})
		.resizable({
			containment: "#ctd_cf_container",
			stop: function( event, ui ) {
				ctd_admin.saveCF(ctd_admin.getCFdata(event.target));
			},
		}).find('span').text(lbl);
		if( ! hidden){
			$cf.show();
		}else{
			$cf.hide();
		}
		return $cf;
	},

	getCFdata: function(target){
		var $cf = $(target);
		var data = {
			action: 'save_cf',
			id_product: id_product,
			id_image: $('#ctd_cf_container .ctd_preview').data('id_image'),
			x: +$cf.position().left,
			y: +$cf.position().top,
			w: $cf.width() + 2, //border * 2
			h: $cf.height() + 2,
			id: $cf.prop('id')
		};
		return data;
	},

	saveCF: function(cf_data, callback){
		$('.ctd_thumbs > a[data-id='+cf_data.id_image+']').addClass('ctd_loading');
		ctd_admin.post(cf_data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+cf_data.id_image+']').addClass('ctd_hasfields');
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
			$('.ctd_thumbs > a[data-id='+cf_data.id_image+']').removeClass('ctd_loading');
			if(typeof callback == 'function'){
				callback(data);
			}
		});
	},

	colorUpdater : null,
	colorImgUpdater : null,
	applyColor : function(){
		$('#ctd_initial_color').trigger('change');
	},

	applyImgColor : function(){
		$('#ctd_initial_img_color').trigger('change');
	},

	setOrigin : function(){
		var id_image = $('.ctd_image img.ctd_preview').data('id_image');
		var data = {};
		data['action'] = 'save_origin';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		data['x_origin'] = $('.ctd_x_line').position().left - 1;
		data['y_origin'] = $('.ctd_y_line').position().top - 1;
		ctd_admin.origins[id_image] = data;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+id_image+']')
				.addClass('ctd_origined');
				$('.ctd_origin').toggleClass('active');
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
		return false;
	},

	endOrigin : function(){
		$('.ctd_origin').removeClass('active');
		ctd_admin.saveSize();
	},

	toggleOrigin : function(){
		$('.ctd_origin').toggleClass('active');
		return false;
	},

	moveOrigin : function(e){
		$origin = $('.ctd_origin');
		if( ! $origin.hasClass('active')) return;
		if(e.offset === undefined){
			var pos = $('.ctd_origin').offset();
			var x = e.pageX - pos.left - 10;
			var y = e.pageY - pos.top - 10;
		}else{
			var x = e.offsetX - 10;
			var y = e.offsetY - 10;
		}
		if(x < 0) x = 0;
		if(y < 0) y = 0;
		$('.ctd_x_line').css({left:x});
		$('.ctd_y_line').css({top:y});
	},

	ajaxify : function(){
		$(ctd_admin.id).on('change','.ctd-ajx',function(){
			var $input = $(this);
			var obj = {};
			obj['action'] = 'save_product_options';
			obj['name'] = $input.data('name');
			obj['value'] = $input.val();
			obj['type'] = $input.prop('type');
			if(obj['type'] == 'checkbox') obj['value'] = ($input.prop('checked')*1);
			obj['id_product'] = id_product;

			if(ctd_admin.allow_toggle)
				$('[data-for=ctd_'+ $input.data('name') +']').toggleClass('active');
			ctd_admin.allow_toggle = true;
			$('#ctd_ajax_success_msg').hide();
			$('#ctd_ajax_msg').css('visibility','visible').show();
			if(obj['name'] == 'popup' && obj['value']){
				$('#ctd_expanded').prop('checked', true).trigger('change');
				$('#ctd_show_btn').prop('checked', true).trigger('change');
			}
			$.ajax({
				url : ctd_link,
				data : obj,
				type : 'POST',
				dataType : 'json'
			}).done(function(data){
				if(data && data.success){
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_success_msg').show();
				}else if(+data.success == 0){
					$('#ctd_ajax_success_msg').hide();
					$('#ctd_ajax_msg').hide();
					$('#ctd_ajax_error_msg').show();
				}
			});
		});
	},

	checkGroup : function(){
		var group = $(this).data('group');
		var $checkboxes = $('input[type=checkbox][data-group="'+group+'"]');
		var check_str = '';
		$checkboxes.each(function(){
			if( $(this).prop('checked') ){
				var value = $(this).data('name').replace(group + '_','');
				check_str += value + '-';
			}
		});
		var data = {};
		data['action'] = 'save_product_options';
		data['name'] = group;
		data['value'] = check_str;
		data['id_product'] = id_product;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});
	},

	addCheckGroupButtons : function(){
		var $containers = $('.ctd_chkgroup_container');

		var $check_all = $('<a href="#" class="ctd_chk_btn ctd_chk_all">'+ctd_str_check_all+'</a>');
		var $check_none = $('<a href="#" class="ctd_chk_btn ctd_chk_none">'+ctd_str_check_none+'</a>');
		var $clear = $('<div class="clear"></div>');

		$containers.each(function(){
			if($ctd_chkgroup_all = $(this).find('.ctd_chkgroup_all').closest('label').next('br')){
				$clear.clone().insertAfter($ctd_chkgroup_all);
				$check_none.clone().insertAfter($ctd_chkgroup_all);
				$check_all.clone().insertAfter($ctd_chkgroup_all);
			}else{
				$clear.clone().prependTo(this);
				$check_none.clone().prependTo(this);
				$check_all.clone().prependTo(this);
			}

		});
	},

	checkGroupAll : function(){
		if($(this).closest('.ctd_chkgroup_container').hasClass('ctd_chkgroup_disabled')){
			return false;
		}
		$(this).closest('.ctd_chkgroup_container').find('.ctd_chkgroup').prop('checked',1).eq(0).trigger('change');
		return false;
	},

	checkGroupNone : function(){
		if($(this).closest('.ctd_chkgroup_container').hasClass('ctd_chkgroup_disabled')){
			return false;
		}
		$(this).closest('.ctd_chkgroup_container').find('.ctd_chkgroup').prop('checked',0).eq(0).trigger('change');
		return false;
	},

	checkGroupGlobal: function(el){
		var $this = $(this);
		if( ! $(this).hasClass('ctd_chkgroup_all')){
			$this = $(el);
		}
		var value = $this.prop('checked');
		$ctd_chkgroup_container = $this.closest('.ctd_chkgroup_container');
		$this.closest('.ctd_chkgroup_container').toggleClass('ctd_chkgroup_disabled', value);
		$ctd_chkgroup_container.find('.ctd_chkgroup').prop('disabled', value);
	},

	removePanel : function(){
		var $panel = $(this).closest('.ctd_panel');
		var data = {}
		data['action'] = 'remove_panel';
		data['id_panel'] = $panel.data('id_panel');

		ctd_admin.post(data,function(data){
			if(data && data.success){
				$panel.hide('fast',function(){$(this).remove()});
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});

		return false;
	},

	saveLangInput : function(){
		var $input = $(this);
		var name = ctd_admin.getName($input.data('name'));
		var data = {}
		data['id_lang'] = $input.data('lang');
		data['value'] = $input.val();
		if(name == 'cflabel'){
			data['id_custom_field'] = $('.ctd_cf_active').prop('id');
			if( ! ctd_custom_fields_trans[data['id_custom_field']]) ctd_custom_fields_trans[data['id_custom_field']] = {};
			if( ! ctd_custom_fields_trans[data['id_custom_field']][data['id_lang']]) ctd_custom_fields_trans[data['id_custom_field']][data['id_lang']] = {};
			ctd_custom_fields_trans[data['id_custom_field']][data['id_lang']]['label'] = data['value'];
			if(data['id_lang'] == ctd_id_lang){
				$('.ctd_cf_active span').text(data['value']);
			}
		}
		data['action'] = 'save_lang';
		data['id_product'] = id_product;
		data['name'] = name;
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
		});

		return false;
	},

	expandLangs : function(e){
		if(e.type == 'focusin'){
			$(this).addClass('expanded');
			$('.ctd_lang_container.expanded').not(this).removeClass('expanded');
		}else{
			$('.ctd_lang_container.expanded').removeClass('expanded');
		}
	},

	collapseLangs : function(e){
		if(e.keyCode == 27){
			$(this).closest('.ctd_lang_container').removeClass('expanded');
		}
	},

	allow_toggle : false,
	toggle : function(){
		var $led = $(this);
		var $checkbox = $('#' + $led.data('for'));
		$led.toggleClass('active');
		ctd_admin.allow_toggle = false;
		$checkbox.prop("checked", !$checkbox.prop("checked")).trigger('change');
		return false;
	},

	showThumb : function(){
		$(this).addClass('ctd_selected').siblings().removeClass('ctd_selected');
		var href = $(this).prop('href');
		var id_image = $(this).data('id');
		$('.ctd_cf:not(.ctd_cf_'+id_image+')').hide();
		$sel = $('.ctd_cf_'+id_image).show().eq(0);
		if($sel.length){
			ctd_admin.select_CF($sel);
		}else{
			$('#ctd_cf_lang input.ctd_lang_input').val('');
		}
		$('.ctd_thumbs > a[data-id='+id_image+']').addClass('ctd_selected').siblings().removeClass('ctd_selected');
		$('.ctd_image img.ctd_preview').prop('src',href).data('id_image',id_image);
		$('.ctd_delete_overlay').data('id_image',id_image);
		$('.ctd_delete_mask').data('id_image',id_image);
		$('.ctd_delete_mask2').data('id_image',id_image);
		$('.ctd_delete_replace').data('id_image',id_image);
		var measure = ctd_admin.measures[id_image];
		var overlay = ctd_admin.overlays[id_image];
		var mask = ctd_admin.masks[id_image];
		var mask2 = ctd_admin.masks2[id_image];
		var replace = ctd_admin.replaces[id_image];

		if(overlay && overlay.image){
			var imagesrc = ctd_module_dir + 'data/overlay/' + overlay.image;
			$('#ctd_current_overlay').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
			$('.ctd_overlay').prop('src',imagesrc);
			$('.ctd_delete_overlay').show();
		}else{
			var imagesrc = ctd_module_dir + 'img/pixel.png';
			$('#ctd_current_overlay').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
			$('.ctd_overlay').prop('src',imagesrc);
			$('.ctd_delete_overlay').hide();
		}

		if(mask && mask.image){
			var imagesrc = ctd_module_dir + 'data/mask/' + mask.image;
			$('#ctd_current_mask').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
			$('.ctd_mask').prop('src',imagesrc);
			$('.ctd_delete_mask').show();
		}else{
			var imagesrc = ctd_module_dir + 'img/pixel.png';
			$('#ctd_current_mask').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
			$('.ctd_mask').prop('src',imagesrc);
			$('.ctd_delete_mask').hide();
		}

		if(mask2 && mask2.image){
			var imagesrc = ctd_module_dir + 'data/mask2/' + mask2.image;
			$('#ctd_current_mask2').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
			$('.ctd_mask2').prop('src',imagesrc);
			$('.ctd_delete_mask2').show();
		}else{
			var imagesrc = ctd_module_dir + 'img/pixel.png';
			$('#ctd_current_mask2').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
			$('.ctd_mask2').prop('src',imagesrc);
			$('.ctd_delete_mask2').hide();
		}

		if(replace && replace.image){
			var imagesrc = ctd_module_dir + 'data/replace/' + replace.image;
			$('#ctd_current_replace').prop('href',imagesrc).find('img').prop('src',imagesrc).show();
			$('.ctd_preview').prop('src',imagesrc);
			$('.ctd_delete_replace').show();
		}else{
			var imagesrc = ctd_module_dir + 'img/pixel.png';
			$('#ctd_current_replace').prop('href',imagesrc).find('img').prop('src',imagesrc).hide();
			$('.ctd_delete_replace').hide();
		}

		if( ! measure) return false;
		$('.ctd_measure').css({
			position: 'absolute',
			left 	: +measure.x,
			top 	: +measure.y,
			width 	: +measure.width /*- 4*/,
		});
		$('input#ctd_size').val(measure.size);
		$('.ctd_x_line').css({left: +measure.x_origin + 1});
		$('.ctd_y_line').css({top: +measure.y_origin + 1});

		return false;
	},

	saveSize : function(){
		var id_image = $('.ctd_image img.ctd_preview').data('id_image');
		var size = parseInt($('input#ctd_size').val());
		if( ! size) return false;
		var width = $('.ctd_measure').outerWidth();
		var pos = $('.ctd_measure').position();
		var data = {};
		data['action'] = 'save_size';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		data['size'] = size;
		data['width'] = width;
		data['x'] = pos.left;
		data['y'] = pos.top;
		data['x_origin'] = $('.ctd_x_line').position().left - 1;
		data['y_origin'] = $('.ctd_y_line').position().top - 1;
		ctd_admin.measures[id_image] = data;
		$('.ctd_thumbs > a[data-id='+id_image+']').addClass('ctd_loading');
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a[data-id='+id_image+']')
				.addClass('ctd_measured');
				$('.ctd_origin').removeClass('active');
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
			$('.ctd_thumbs > a[data-id='+id_image+']')
			.removeClass('ctd_loading');
		});
		return false;
	},

	copySize : function(){
		var id_image = $('.ctd_image img.ctd_preview').data('id_image');
		var size = parseInt($('input#ctd_size').val());
		if( ! size) return false;
		var width = $('.ctd_measure').outerWidth();
		var pos = $('.ctd_measure').position();
		var data = {};
		data['action'] = 'copy_size';
		data['id_product'] = id_product;
		data['id_image'] = id_image;
		data['size'] = size;
		data['width'] = width;
		data['x'] = pos.left;
		data['y'] = pos.top;
		data['x_origin'] = $('.ctd_x_line').position().left;
		data['y_origin'] = $('.ctd_y_line').position().top;
		data['ids'] = '';
		ctd_admin.measures[id_image] = data;
		$('.ctd_thumbs > a:not(.ctd_measured)')
		.addClass('ctd_loading')
		.each(function(){
			var id_image = $(this).data('id');
			data['ids'] += id_image + ',';
			ctd_admin.measures[id_image] = data;
		});
		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
				$('.ctd_thumbs > a:not(.ctd_measured)')
				.addClass('ctd_measured');
				$('.ctd_origin').removeClass('active');
			}else if(+data.success == 0){
				$('#ctd_ajax_success_msg').hide();
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_error_msg').show();
			}
			$('.ctd_thumbs > a.ctd_loading').removeClass('ctd_loading');
		});
		return false;
	},

	post : function(data,callback,url){
		if( ! url) url = ctd_link;
		$('#ctd_ajax_success_msg').hide();
		$('#ctd_ajax_msg').css('visibility','visible').show();
		$.post(url,data,callback,'json');
	},

	getName : function(name){
		return name.substr(0,name.lastIndexOf('_'));
	}

}

$(window).load(function(){
	//$('#link-ModuleCustomtextdesign').click();
})