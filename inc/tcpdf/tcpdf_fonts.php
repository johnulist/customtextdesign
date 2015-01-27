<?php
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

/**
 * @file
 * Unicode data and font methods for TCPDF library.
 * @author Nicola Asuni
 * @package com.tecnick.tcpdf
 */

/**
 * @class TCPDF_FONTS
 * Font methods for TCPDF library.
 * @package com.tecnick.tcpdf
 * @version 1.0.013
 * @author Nicola Asuni - info@tecnick.com
 */
class TcpdfFonts {

	/**
	 * Return fonts path
	 * @return string
	 * @public static
	 */
	public static function getfontpath()
	{
		if (!defined('K_PATH_FONTS') && is_dir($fdir = realpath(dirname(__FILE__).'/../fonts')))
		{
			if (Tools::substr($fdir, -1) != '/')
				$fdir .= '/';
			define('K_PATH_FONTS', $fdir);
		}
		return defined('K_PATH_FONTS') ? K_PATH_FONTS : '';
	}

	/**
	 * Return font full path
	 * @param $file (string) Font file name.
	 * @param $fontdir (string) Font directory (set to false fto search on default directories)
	 * @return string Font full path or empty string
	 * @author Nicola Asuni
	 * @since 6.0.025
	 * @public static
	 */
	public static function getFontFullPath($file, $fontdir = false)
	{
		$fontfile = '';
		// search files on various directories
		if (($fontdir !== false) && file_exists($fontdir.$file))
			$fontfile = $fontdir.$file;
		elseif (file_exists(self::getfontpath().$file))
			$fontfile = self::getfontpath().$file;
		elseif (file_exists($file))
			$fontfile = $file;
		return $fontfile;
	}

}
