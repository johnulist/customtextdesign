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

	id : '#ctd_admin_global',
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
				}
			});
		}else if($json.error){
			alert($json.error);
		}
		$('#ctd_upload_form_replace').get(0).reset();
	},

	init : function(){

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
	},

	colorUpdater : null,
	applyColor : function(){
		$('#ctd_initial_color').trigger('change');
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
		var x = e.offsetX - 10;
		var y = e.offsetY - 10;
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
			}
		});

		return false;
	},

	saveLangInput : function(){
		var $input = $(this);
		var data = {}
		data['action'] = 'save_lang';
		data['id_product'] = id_product;
		data['id_lang'] = $input.data('lang');
		data['name'] = ctd_admin.getName($input.data('name'));
		data['value'] = $input.val();

		ctd_admin.post(data,function(data){
			if(data && data.success){
				$('#ctd_ajax_msg').hide();
				$('#ctd_ajax_success_msg').show();
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