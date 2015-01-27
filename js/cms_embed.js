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

	$('.ctd').each(function(){
		var controller = $(this).attr('id') || 'default';
		var url = ctd_default_link.replace('_link_', controller);
		var $iframe = $('<iframe>',{
			class: 'ctd_cms_iframe',
			src : url,
			frameBorder : 0,
			seamless : 'seamless'
		}).css('visibility', 'hidden').on('load', function(){
			$(this).css('visibility', 'visible');
		});
		$(this).replaceWith($iframe);
	});

});