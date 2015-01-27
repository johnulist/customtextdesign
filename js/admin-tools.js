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

	if($(".tableDnD").length){
		$(".tableDnD").tableDnD({
			onDragStart: function(table, row) {
				originalOrder = $.tableDnD.serialize();
				reOrder = ':even';
				if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
					reOrder = ':odd';
			},
			dragHandle: 'dragHandle',
			onDragClass: 'myDragClass',
			onDrop: function(table, row) {
				var tableDrag = $('#' + table.id);
				$.ajax({
					type: 'POST',
					async: false,
					url: ctd_link + '&' + $.tableDnD.serialize(),
					data: 'action=dnd',
					success: function(data) {
						tableDrag.find('tbody tr').removeClass('alt_row');
						tableDrag.find('tbody tr').removeClass('myDragClass');
						tableDrag.find('tbody tr').removeClass('row_hover');
						var l = tableDrag.find('tbody tr').length
						tableDrag.find('tbody tr').each(function(i,el){
							if(i==0){
								$(el).find('.arrow-up').hide();
								$(el).find('.arrow-down').show();
							}
							else if(i==l-1){
								$(el).find('.arrow-down').hide();
								$(el).find('.arrow-up').show();
							}
							else{
								$(el).find('.arrow-up').show();
								$(el).find('.arrow-down').show();
							}
						});
					}
				});
			}
		});
	}

	function material_slider_handler(event, ui){
		var value = ui.value;
		var div = $(event.srcElement).closest('.material-slider-div');
		div.find('.input_material_size').hide();
		div.find('.input_material_size_'+value).show();
		div.find('.material-specific-size').text(value + ' cm');
	}

	function slider_color_alpha(event, ui){
		var value = ui.value;
		$('#color_alpha').val(value);
		var percent = (value / 127 * 100).toFixed(2);
		$('#label_color_alpha').text(value + '/127 ('+ percent +'%)');
	}

	if($('.material-slider').length)
		$('.material-slider').slider({
			min: num_size_min,
			max: num_size_max,
			slide: material_slider_handler,
			stop : material_slider_handler
		});

	$('.material-slider-div .input_material_size').change(function(){
		$(this).attr('name',$(this).data('name'));
	});

	if($('.slider_color_alpha').length){

		$.fn.mColorPicker.init = {
			replace: '.mColorPicker',
			index: 0,
			enhancedSwatches: true,
			allowTransparency: false,
			checkRedraw: 'DOMUpdated', // Change to 'ajaxSuccess' for ajax only or false if not needed
			liveEvents: false,
			showLogo: false
		};

		$('.mColorPickerContainer').each(function(){
			var _this = $(this);
			var _inp = $(this).find('.mColorPicker');
			_inp.css({position : 'absolute'})
			var mColorPickerTransparent = $(this).find('.mColorPickerTransparent');
			mColorPickerTransparent.css({
				width : _inp.width() + 8,
				height : _inp.height() + 4,
				top : _inp.position().top + 2,
				left : _inp.position().left + 2,
			});
			_this.find('.mColorPickerTrigger').css({
				position : 'absolute',
				left : _inp.width(),
			});
		});

		var value = $('#color_alpha').val();
		$('.slider_color_alpha').slider({
			min: 0,
			max: 127,
			value : value,
			slide: slider_color_alpha,
			stop : slider_color_alpha
		});
		var percent = (value / 127 * 100).toFixed(2);
		$('#label_color_alpha').text(value + '/127 ('+ percent +'%)');
	}

	if($('.ctd_multiselect').length){
		$('.ctd_multiselect').multiSelect();
		$('#ctd-select-all').click(function(){
			$('.ctd_multiselect').multiSelect('select_all');
			return false;
		});
		$('#ctd-deselect-all').click(function(){
			$('.ctd_multiselect').multiSelect('deselect_all');
			return false;
		});
	}

	if($('#ctd_page_list').length){
		$('#ctd_page_list').on('change',function(){
			var href = $('#ctd_page_list_link').prop('href');
			var id_page_config = $('#ctd_page_list').val();
			href = href.replace(/pagename\=\d*$/, 'pagename='+id_page_config);
			$('#ctd_page_list_link').prop('href', href);
		});
		$('#ctd_page_list').trigger('change');
	}

});

String.prototype.toProperCase = function() {
	var aStr = this.split(' ');
	var aProp = [];
	for (str in aStr) {
		aProp.push(aStr[str].charAt(0).toUpperCase() + aStr[str].slice(1));
	}
	return aProp.join(' ');
};

var wait = null;
function showPreview(inp){
	if(wait){
		clearTimeout(wait);
		wait = null;
	}
	wait = setTimeout(renderPreview,333);
}

function renderPreview(){
	var $img = $('#color_code_preview');
	var src = $img.prop('src').split('&clr=')[0] + '&clr=';
	src += $('#color_code').val().replace('#','');
	$img.prop('src',src);
}

function applyToAll(el, prefix){
	var value = $(el).val();
	$('[id^="'+ prefix +'"]').val(value);
}
