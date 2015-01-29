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

	class CustomImage {

		public static $module = 'customtextdesign';
		/** @var customtextdesign */
		public static $instance = null;
		public static $image = null;
		public static $color = null;
		public static $text = '';
		public static $font = '';
		public static $size = 0;
		public static $type = 'img';
		public static $center = 0;
		public static $curve = 0;
		public static $letterspace = 0;

		public static $output = 0;
		public static $debug = 0;
		public static $trace = 0;

		public static $x = 0;
		public static $y = 0;
		public static $width = 0;
		public static $height = 0;
		public static $rect = null;

		public static $cache = '';
		public static $hash = '';

		public static $forpanel = 0;
		public static $coeff = 1.1;

		public static function colorizeImage($data, $output = 1)
		{
			$imagecolor = $data['imagecolor'];
			$image_src = $data['image_src'];

			if (! self::$instance)
				self::$instance = Module::getInstanceByName(self::$module);

			$data['image_stats'] = md5($image_src);
			$hash = md5(serialize($data)).'_clr';

			$cache = self::checkColorizeCache($hash, $output);
			if (! $output && _CTD_CACHE_ && file_exists($cache))
				return Context::getContext()->shop->getBaseURL().'modules/'.self::$module.'/data/cache/'.$hash.'.png';
			$image_type = self::$instance->exifImageType($image_src);
			$image = ImageManager::create($image_type, $image_src);
			imagesavealpha($image, 1);

			if (Tools::substr($imagecolor, 0, 1) != '_')
			{
				$color = self::$instance->getColor((int)$imagecolor);
				if ((int)$color['is_color'])
					$imagecolor = str_replace('#', '', $color['color']);
				else
					$imagecolor = self::$instance->getDir().'data/texture/'.$color['texture'];
			}
			else
				$imagecolor = str_replace('_', '', $imagecolor);

			$result = self::textureScale($image, $imagecolor, false);
			self::$image = $result;
			self::$cache = $cache;
			if ($output)
				self::output();
			self::save();
			return $cache;
		}

		public static function colorize($data, $output = 1, $image_path = '')
		{
			$customcolor = $data['customcolor'];
			$id_product = isset($data['id_product']) ? (int)$data['id_product'] : 0;
			$id_image = isset($data['id_image']) ? (int)$data['id_image'] : 0;

			if (! self::$instance)
				self::$instance = Module::getInstanceByName(self::$module);

			if (empty($image_path))
				$image_path = self::$instance->getImagePath($id_image, Configuration::get(self::$instance->name.'image_type'), $id_product);

			$data['image_stats'] = stat($image_path);
			$hash = md5(serialize($data)).'_clr';

			$cache = self::checkColorizeCache($hash, $output);
			if (! $output && _CTD_CACHE_ && file_exists($cache))
				return Context::getContext()->shop->getBaseURL().'modules/'.self::$module.'/data/cache/'.$hash.'.png';
			$image_type = self::$instance->exifImageType($image_path);
			$image = ImageManager::create($image_type, $image_path);
			imagesavealpha($image, 1);
			if (Tools::substr($customcolor, 0, 1) == '_')
			{
				$customcolor = (int)Tools::substr($customcolor, 1);
				$color = self::$instance->getColor($customcolor);
				if ((int)$color['is_color'])
					$customcolor = str_replace('#', '', $color['color']);
				else
					$customcolor = self::$instance->getDir().'data/texture/'.$color['texture'];
			}
			$result = self::textureScale($image, $customcolor, false);
			self::$image = $result;
			self::$cache = $cache;
			if ($output)
				self::output();
			self::save();
			return $cache;
		}

		public static function preview($data, $output = 1)
		{
			self::$output = (int)$output;
			$dir = dirname(__FILE__);
			$is_cached = self::checkCache($data, $output);
			if ($is_cached && ! $output)
				return self::$hash;
			self::$type = $data['type'];
			if (!trim($data['text']))
				return self::outputPixel();
			if (isset($data['forpanel']) && (int)$data['forpanel'])
				self::$forpanel = 1;

			if (! $output)
				self::$type = 'img';
			self::$text = $data['text'];

			$font = self::getFont($data['font']);
			$color = self::getColor($data['color']);
			self::$curve = (int)$data['curve'];
			self::$letterspace = (int)$data['letterspace'];
			// Get Hex Color
			$r = 0;
			$g = 0;
			$b = 0;
			$a = 0;
			$is_color = true;

			self::$letterspace = (int)$data['letterspace'];
			// Get Hex Color
			$r = 0;
			$g = 0;
			$b = 0;
			$a = 0;
			$is_color = true;

			if (! (int)$data['color'])
			{
				$rgb = self::hex2rgb($data['clr']);
				$r = $rgb[0];
				$g = $rgb[1];
				$b = $rgb[2];
				$a = isset($data['alpha']) ? (int)$data['alpha'] : 0;
			}
			else
			{
				$is_color = $color['is_color'];
				if ($is_color)
				{
					$rgb = self::hex2rgb($color['color']);
					$r = $rgb[0];
					$g = $rgb[1];
					$b = $rgb[2];
					$a = (int)$color['alpha'];
					if (! $a)
						$a = isset($data['alpha']) ? (int)$data['alpha'] : 0;
				}
				else
					$img_texture = $dir.'/../data/texture/'.$color['texture'];
			}

			self::$size = 10 * (int)$data['size'];
			self::$center = (int)$data['center'];
			self::$font = $dir.'/../data/font/'.$font['file'];
			if (!is_file(self::$font))
				return self::outputPixel();

			self::measure((int)$data['ignore_space']);

			self::create($r, $g, $b, $a);

			if ($is_color)
				self::drawText();
			else
			{
				self::drawText(0, 'textured');
				self::textureScale(self::$image, $img_texture);
			}

			if ((int)$data['mirror'])
				self::imageFlip(2);

			self::output($output);
			return self::$hash;
		}

		public static function create($r, $g, $b, $a = 0)
		{
			self::checkSize();
			self::$image = imagecreatetruecolor(self::$width, self::$height);
			self::$color = imagecolorallocatealpha(self::$image, $r, $g, $b, $a);
			$transparent = imagecolorallocatealpha(self::$image, 0, 0, 0, 127);
			imagecolortransparent(self::$image, $transparent);
			imagealphablending(self::$image, 0);
			imagesavealpha(self::$image, 1);
			imagefill(self::$image, 0, 0, $transparent);
		}

		public static function measureText($item)
		{
			$text = rtrim($item['text'], "\n");
			$line_count = substr_count($text, "\n") + 1;
			$text_line_height = $item['height'] / $line_count;
			$id_font = $item['font'];
			$font = self::getFont($id_font);
			$font_file = dirname(__FILE__).'/../data/font/'.$font['file'];

			$maxwidth = 0;
			$text_width = 0;

			$lines = explode("\n", $text);
			foreach ($lines as $line)
			{
				$box = imagettfbbox(50, 0, $font_file, $line);
				$line_width = $box[2] - $box[0];
				if ($line_width > $maxwidth)
					$maxwidth = $line_width;
				$text_width += $line_width;
			}

			$width_ratio = $item['width'] / $maxwidth;
			$width = $text_width * $width_ratio;
			$height = $text_line_height;

			return array('width' => $width, 'height' => $height);
		}

		public static function drawText($angle = 0, $textured = '')
		{
			if ($textured == 'textured')
				self::$color = imagecolorallocate(self::$image, 127, 127, 127);
			if (self::$curve)
				return self::drawTextOnArc();
			// Else if to prevent conflict between functions until solution is found
			else if (self::$letterspace)
				return self::drawTextOnLetterspace();
			if (! self::$center)
			{
				imagealphablending(self::$image, 1);
				imagesavealpha(self::$image, 1);
				imagettftext(self::$image, self::$size, $angle, self::$x, self::$y, self::$color, self::$font, self::$text);
			}
			else
				self::drawTextCentered();
		}

		public static function drawTextCentered()
		{
			$lines = explode("\n", self::$text);
			$y = 5;
			foreach ($lines as $key => $line)
			{
				$box = imagettfbbox(self::$size, 0, self::$font, $line);
				$width  = $box[2] - $box[0] + 10;
				$x = (self::$width - $width) / 2;
				$y = - $box[5] + 5;
				//preserve original text and original line height
				if ($key > 0)
					for ($i = 0; $i < $key; $i++)
						$line = "\n".$line;
				imagettftext(self::$image, self::$size, 0, $x, $y, self::$color, self::$font, $line);
			}
		}

		public static function calculateTextBox($text, $fontfile, $size, $angle)
		{
			$rect = imagettfbbox($size, $angle, $fontfile, $text);
			$min_x = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
			$max_x = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
			$min_y = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
			$max_y = max(array($rect[1], $rect[3], $rect[5], $rect[7]));

			return array(
				'left' => abs($min_x) - 1,
				'top' => abs($min_y) - 1,
				'width' => $max_x - $min_x,
				'height' => $max_y - $min_y,
				'box' => $rect
			);
		}

		public static function imagettftext($image, $size, $angle, $tx, $ty, $color, $fontfile, $text, $fast = 0)
		{
			//safe margin for rotation
			$sf = 5;
			$sf2 = $sf / 2;
			$box = self::calculateTextBox($text, $fontfile, $size, 0);
			if ($box['width'] == 0) $box['width'] = 1;
			if ($box['height'] == 0) $box['height'] = 1;
			$tmp = imagecreatetruecolor($box['width'], $box['height']);
			$bg = imagecolorallocate($tmp, 0, 0, 0);
			if ($bg == $color)
			{
				imagecolordeallocate($tmp, $bg);
				$bg = imagecolorallocate($tmp, 1, 1, 1);
			}
			imagealphablending($tmp, 0);
			imagesavealpha($tmp, 0);
			imagefill($tmp, 0, 0, $bg);
			$x = $box['left'];
			$y = $box['top'];
			imagettftext($tmp, $size, 0, $x, $y, $color, $fontfile, $text);

			$x_correction_1 = 0;
			$x_correction_2 = 0;
			$y_correction_1 = 0;
			$y_correction_2 = 0;

			$y_start = $box['height'] / 2;
			for ($ix = 0; $ix < $box['width']; $ix++)
			{
				$c = imagecolorat($tmp, $ix, $y_start);
				if ($c != $bg)
				{
					$x_correction_1 = -$ix;
					break;
				}
			}

			for ($ix = $box['width'] - 1; $ix >= 0; $ix--)
			{
				$c = imagecolorat($tmp, $ix, $y_start);
				if ($c != $bg)
				{
					$x_correction_2 = $box['width'] - $ix - 1;
					break;
				}
			}

			if ($fast == 0)
			{
				$x_start = $box['width'] / 2;
				for ($iy = 0; $iy < $box['height']; $iy++)
				{
					$c = imagecolorat($tmp, $x_start, $iy);
					if ($c != $bg)
					{
						$y_correction_1 = -$iy;
						break;
					}
				}

				for ($iy = $box['height'] - 1; $iy >= 0; $iy--)
				{
					$c = imagecolorat($tmp, $x_start, $iy);
					if ($c != $bg)
					{
						$y_correction_2 = $box['height'] - $iy - 1;
						break;
					}
				}
			}

			$x_correction = $x_correction_1 + $x_correction_2;
			$y_correction = $y_correction_1 + $y_correction_2;
			imagecolordeallocate($tmp, $bg);
			imagedestroy($tmp);

			if ($fast)
			{
				return array(
					'x_correction' => $x_correction,
					'rect' => $box
				);
			}

			$tmp = imagecreatetruecolor($box['width'] + $sf, $box['height'] + $sf);
			$bg = imagecolorallocatealpha($tmp, 0, 0, 0, 127);

			imagealphablending($tmp, 1);
			imagesavealpha($tmp, 1);
			imagefill($tmp, 0, 0, $bg);
			$text_pos = array('x' => $box['left'] + $x_correction + $sf2, 'y' => $box['top'] + $y_correction + $sf2);
			imagettftext($tmp, $size, 0, $text_pos['x'], $text_pos['y'], $color, $fontfile, $text);
			if (self::$trace)
			{
				$lime = imagecolorallocate($tmp, 0, 0xFF, 0);
				imagefilledellipse($tmp, $text_pos['x'], $text_pos['y'], 5, 5, $lime);
				imageline($tmp, $text_pos['x'], 0, $text_pos['x'], $box['height'], $lime);
				imageline($tmp, 0, $text_pos['y'], $box['width'], $text_pos['y'], $lime);
			}
			imagecolordeallocate($tmp, $bg);
			$tx_bkp = $tx;
			$ty_bkp = $ty;
			$tx -= $x_correction;
			$ty -= $y_correction;
			if ($angle)
			{
				$center = array('x' => $box['width'] / 2, 'y' => $box['height'] / 2);
				$r = sqrt(pow($center['x'] - $text_pos['x'], 2) + pow($center['y'] - $text_pos['y'], 2));
				$math_x = $text_pos['x'] - $center['x'];
				$math_y = $center['y'] - $text_pos['y'];
				$theta = atan2($math_y, $math_x);
				$rad_angle = deg2rad($angle);
				$new_angle = $theta + $rad_angle;
				$new_x = $r * cos($new_angle) + $center['x'];
				$new_y = - $r * sin($new_angle) + $center['y'];
				$dx = $new_x - $text_pos['x'];
				$dy = $new_y - $text_pos['y'];
				$bg_color = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
				$tmp = imagerotate($tmp, $angle, $bg_color);
				imagecolordeallocate($tmp, $bg_color);
				$x_diff = imagesx($tmp) - $box['width'];
				$y_diff = imagesy($tmp) - $box['height'];
				$tx -= $x_diff / 2 + $dx + $sf2 * cos($rad_angle);
				$ty -= $y_diff / 2 + $dy + $sf2 * sin($rad_angle);
			}
			$width = imagesx($tmp);
			$height = imagesy($tmp);
			imagealphablending($image, 1);
			imagesavealpha($image, 1);
			$ty -= $box['top'] - $sf2;
			$tx -= $box['left'] - $sf2;
			imagecopyresampled($image, $tmp, $tx, $ty, 0, 0, $width, $height, $width, $height);
			if (self::$trace)
			{
				imagefilledellipse($image, $tx_bkp, $ty_bkp, 5, 5, $lime);
				imagecolordeallocate($image, $lime);
			}
			return array(
				'width' => $width - $x_correction,
				'height' => $box['height'],
				'x_correction' => $x_correction,
				'base_line' => $y_correction,
				'img' => $tmp,
				'rect' => $box
			);
		}

		public static function drawTextOnLetterspace($pad = 0)
		{
// For testing, spacing is taken from curve input
			$letterspace = self::$letterspace;
// Set variable width to be manipulated by input
			$temp_x = $x;
// Store text to get individual letters in loop
			$text = self::$text;
// Run loop for each letter, changing width as desired by user
			for ($i = 0; $i < strlen($text); $i++)
			{
			    $bbox = imagettftext(self::$image, 16, 0, $temp_x, 16, self::$color, self::$font, $text[$i]);
			    $temp_x += $letterspace + ($bbox[2] - $bbox[0]);
			}
// Return image
			$tr = imagecolorallocatealpha(self::$image, 0, 0, 0, 127);
			return self::imagetrim(self::$image, $tr);
		}
		public static function drawTextOnArc($pad = 0)
		{
			if (self::$curve > 0)
			{
				$s = 180;
				$e = 360;
			}
			else
			{
				$s = 0;
				$e = 180;
			}
			$cx = 1;
			$cy = 1;
			$tr = imagecolorallocatealpha(self::$image, 0, 0, 0, 127);
			$tlen = Tools::strlen(self::$text);
			$arccentre = ($e + $s) / 2;
			$txt_size = self::calculateTextBox(self::$text, self::$font, self::$size, 0);
			$c = self::$curve;

			$total_width = ((int)$txt_size['width']) - ($tlen - 1) * $pad;
			$theta = (359 / 99) * (abs($c) - 100) + 360;

			$r = ($total_width * 360) / (2 * M_PI * $theta);
			$textangle = rad2deg($total_width / $r);
			if (self::$curve > 0)
			{
				$s = $arccentre - $textangle / 2;
				$e = $arccentre + $textangle / 2;
			}
			else
			{
				$e = $arccentre - $textangle / 2;
				$s = $arccentre + $textangle / 2;
			}
			$bounds = array();
			if (self::$trace)
				$red = imagecolorallocate(self::$image, 0xFF, 0, 0);
			$text_width = 0;
			self::$text = utf8_decode(self::$text);
			for ($i = 0, $theta = deg2rad($s); $i < $tlen; $i++)
			{
				$ch = self::$text{$i};

				$tx = $cx + $r * cos($theta);
				$ty = $cy + $r * sin($theta);
				$measure = self::preciseMeasure($ch, self::$font, self::$size, 1);
				if (self::$curve > 0)
				{
					$dtheta = ((int)$measure['w']) / $r;
					$angle = rad2deg(M_PI * 3 / 2 - ($dtheta / 2 + $theta));
				}
				else
				{
					$dtheta = ((int)$measure['w'] + $pad) / $r;
					$angle = rad2deg(M_PI / 2 - ($theta - $dtheta / 2));
				}

				$box = self::imagettftext(self::$image, self::$size, $angle, $tx, $ty, self::$color, self::$font, $ch, 1);
				$width = $measure['w'] - $box['x_correction'];
				$text_width += $width;
				$box_w = $box['rect']['width'];
				$box_h = $box['rect']['height'];

				$space = $box_w + $box_h;
				$bounds['min_h'] = isset($bounds['min_h']) ? min($box_h, $bounds['min_h']):$box_h;
				$bounds['max_h'] = isset($bounds['max_h']) ? max($box_h, $bounds['max_h']):$box_h;
				$bounds['min_x'] = isset($bounds['min_x']) ? min($tx - $space, $bounds['min_x']):$tx - $space;
				$bounds['min_y'] = isset($bounds['min_y']) ? min($ty - $space, $bounds['min_y']):$ty - $space;
				$bounds['max_x'] = isset($bounds['max_x']) ? max($tx + $space, $bounds['max_x']):$tx + $space;
				$bounds['max_y'] = isset($bounds['max_y']) ? max($ty + $space, $bounds['max_y']):$ty + $space;

				if (self::$curve > 0)
					$theta += $dtheta;
				else
					$theta -= $dtheta;
			}

			$redraw = true;
			if ($bounds['min_x'] < 0)
			{
				$redraw = true;
				$correction = ceil(abs($bounds['min_x']));
				$bounds['min_x'] += $correction;
				$bounds['max_x'] += $correction;
				$cx += $correction;
			}
			if ($bounds['min_y'] < 0)
			{
				$redraw = true;
				$correction = ceil(abs($bounds['min_y']));
				$bounds['min_y'] += $correction;
				$bounds['max_y'] += $correction;
				$cy += $correction;
			}

			if ($bounds['min_x'] > 100)
			{
				$correction = floor($bounds['min_x'] - 100);
				$bounds['min_x'] = 100;
				$bounds['max_x'] -= $correction;
				$cx -= $correction;
			}

			if ($bounds['min_y'] > 100)
			{
				$correction = floor($bounds['min_y'] - 100);
				$bounds['min_y'] = 100;
				$bounds['max_y'] -= $correction;
				$cy -= $correction;
			}

			$total_width = $text_width - ($tlen - 1) * $pad;
			$theta = (359 / 99) * (abs($c) - 100) + 360;
			$r = ($total_width * 360) / (2 * M_PI * $theta);
			$textangle = rad2deg($total_width / $r);
			if (self::$curve > 0)
			{
				$s = $arccentre - $textangle / 2;
				$e = $arccentre + $textangle / 2;
			}
			else
			{
				$e = $arccentre - $textangle / 2;
				$s = $arccentre + $textangle / 2;
			}

			if ($redraw)
			{
				imagedestroy(self::$image);
				$w = ceil(abs($bounds['max_x'] - $bounds['min_x']));
				$h = ceil(abs($bounds['max_y'] - $bounds['min_y']));
				if ($w < $bounds['max_x'])
					$w = ceil($bounds['max_x']) + 10;
				if ($h < $bounds['max_y'])
					$h = ceil($bounds['max_y']) + 10;
				self::$image = imagecreatetruecolor($w, $h);

				imagefill(self::$image, 0, 0, $tr);
				imagealphablending(self::$image, 0);
				imagesavealpha(self::$image, 1);
				if (self::$trace)
				{
					imagearc(self::$image, $cx, $cy, $r * 2, $r * 2, $s, $e, $red);
					imagecolordeallocate(self::$image, $red);
				}

				for ($i = 0, $theta = deg2rad($s); $i < $tlen; $i++)
				{
					$ch = self::$text{$i};
					if (self::$curve > 0)
						$bounds['min_h'] = 0;
					$tx = $cx + ($r + $bounds['min_h']) * cos($theta);
					$ty = $cy + ($r + $bounds['min_h']) * sin($theta);
					$measure = self::preciseMeasure($ch, self::$font, self::$size, 1);
					if (self::$curve > 0)
					{
						$dtheta = ((int)$measure['w']) / $r;
						$angle = rad2deg(M_PI * 3 / 2 - ($dtheta / 2 + $theta));
					}
					else
					{
						$dtheta = ((int)$measure['w'] + $pad) / $r;
						$angle = rad2deg(M_PI / 2 - ($theta - $dtheta / 2));
					}
					$box = self::imagettftext(self::$image, self::$size, $angle, $tx, $ty, self::$color, self::$font, $ch);
					$width = $measure['w'] - $box['x_correction'];
					if (self::$curve > 0)
					{
						$dtheta = ($width) / $r;
						$angle = rad2deg(M_PI * 3 / 2 - ($dtheta / 2 + $theta));
					}
					else
					{
						$dtheta = ($width + $pad) / $r;
						$angle = rad2deg(M_PI / 2 - ($theta - $dtheta / 2));
					}
					if (self::$curve > 0)
						$theta += $dtheta;
					else
						$theta -= $dtheta;
				}
				return self::imagetrim(self::$image, $tr);
			}
			return self::imagetrim(self::$image, $tr);
		}

		public static function textureScale($im, $texture, $fast = true)
		{
			$w = imagesx($im);
			$h = imagesy($im);
			//same size as $im
			$tmp = imagecreatetruecolor($w, $h);
			$tx = null;
			if (file_exists($texture))
			{
				$sz = getimagesize($texture);
				switch ($sz['mime'])
				{
					case 'image/png':
						$tx = imagecreatefrompng($texture);
						break;
					case 'image/jpeg':
						$tx = imagecreatefromjpeg($texture);
						break;
					case 'image/gif':
						$tx = imagecreatefromgif ($texture);
						break;
				}
				//fill with texture
				imagesettile($tmp, $tx);
				imagefilledrectangle($tmp, 0, 0, $w, $h, IMG_COLOR_TILED);
				imagedestroy($tx);
				if (!$fast)
					return self::texturize($im, $tmp);
			}
			else
			{
				//if it's a color
				$color = self::hex2rgb($texture);
				imagesavealpha($im, true);
				imagefilter($im, IMG_FILTER_GRAYSCALE);
				imagefilter($im, IMG_FILTER_COLORIZE, $color[0] - 255, $color[1] - 255, $color[2] - 255);
				return $im;
			}

			$img = imagecreatetruecolor($w, $h);
			$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
			imagefill($img, 0, 0, $transparent);
			imagesavealpha($img, true);

			for ($x = 0; $x < $w; $x++)
			{
				for ($y = 0; $y < $h; $y++)
				{
					$tmpcolor_index = imagecolorat($tmp, $x, $y);
					$tmpcolor = imagecolorsforindex($tmp, $tmpcolor_index);
					$color = imagecolorsforindex($im, imagecolorat($im, $x, $y));
					if ($color['alpha'] == 127)
						continue;
					elseif ($color['alpha'] == 0)
						$new_color = $tmpcolor_index;
					else
						$new_color = imagecolorallocatealpha($img, $tmpcolor['red'], $tmpcolor['green'], $tmpcolor['blue'],	$color['alpha']);
					imagesetpixel($img, $x, $y, $new_color);
				}
			}
			if (self::$image)
				imagedestroy(self::$image);
			self::$image = $img;
			return $img;
		}

		public static function texturize($im, $texture)
		{
			$w = imagesx($im);
			$h = imagesy($im);
			imagesavealpha($im, true);
			for ($x = 0; $x < $w; $x++)
			{
				$x = $x;
				for ($y = 0; $y < $h; $y++)
				{
					$tmpcolor = imagecolorsforindex($texture, imagecolorat($texture, $x, $y));
					$color = imagecolorsforindex($im, imagecolorat($im, $x, $y));
					$luminance = (($color['red'] * 0.299) + ($color['green'] * 0.587) + ($color['blue'] * 0.114)) / 255;
					$new_color = imagecolorallocatealpha($im,
						$tmpcolor['red'] * $luminance,
						$tmpcolor['green'] * $luminance,
						$tmpcolor['blue'] * $luminance,
						$color['alpha']);
					imagesetpixel($im, $x, $y, $new_color);
				}
			}
			return $im;
		}

		public static function pasteImage($image, $original_width, $item)
		{
			$dir = dirname(__FILE__);
			$panel_original_width = imagesx($image);
			$ratio = $panel_original_width / $original_width;
			$x = round($ratio * (float)$item['x']);
			$y = round($ratio * (float)$item['y']);
			$width  = round((int)$item['width'] * $item['scalex'] * $ratio);
			$height = round((int)$item['height'] * $item['scaley'] * $ratio);
			$angle = - (float)$item['angle'];
			$type = $item['type'];

			if (!self::$instance)
				self::$instance = Module::getInstanceByName(self::$module);

			if ($type == 'text')
			{
				$item['type'] = 'img';
				$item['size'] = 20;
				$item['forpanel'] = 1;
				$item['ignore_space'] = 0;
				//get the preview file
				$src = self::preview($item, 0);
				$src = realpath($dir.'/../data/cache/'.$src.'.png');
			}
			elseif ($item['type'] == 'image')
			{
				$src = self::$instance->getBaseDir().$item['text'];
				if ($item['clr'] || (int)$item['color'])
				{
					$data = array();
					$data['imagecolor'] = !(int)$item['color'] ? $item['clr'] : (int)$item['color'];
					$data['image_src'] = $src;
					$src = self::colorizeImage($data, 0);
				}
			}

			$image_type = self::$instance->exifImageType($src);
			$temp_img = ImageManager::create($image_type, $src);

			$small_img = self::resize($temp_img, $width, $height);

			$temp_img = self::changeRatio($temp_img, $width / $height);

			if ($angle)
			{
				$bg_color = imagecolorallocatealpha($temp_img, 0, 0, 0, 127);
				$temp_img = imagerotate($temp_img, $angle, $bg_color);
				imagecolordeallocate($temp_img, $bg_color);

				$bg_color = imagecolorallocatealpha($small_img, 0, 0, 0, 127);
				$small_img = imagerotate($small_img, $angle, $bg_color);
				imagecolordeallocate($small_img, $bg_color);
			}

			$new_width = imagesx($small_img);
			$new_height = imagesy($small_img);

			$big_width = imagesx($temp_img);
			$big_height = imagesy($temp_img);

			imagealphablending($temp_img, true);
			imagesavealpha($temp_img, true);

			imagecopyresampled($image, $temp_img, $x, $y, 0, 0, $new_width, $new_height, $big_width, $big_height);

			imagedestroy($temp_img);
			if (is_resource($small_img) && get_resource_type($small_img) == 'gd')
				imagedestroy($small_img);
		}

		public static function pasteMask($image, $mask, $canresize = true, $y_offset = 0)
		{
			$dir = dirname(__FILE__);
			$panel_original_width = imagesx($image);
			$panel_original_height = imagesy($image);
			$image_mask_path = $mask['image'];
			$image_mask_path = realpath($dir.'/../data/mask/'.$image_mask_path);
			$module = Module::getInstanceByName(self::$module);
			list($mask_width, $mask_height) = getimagesize($image_mask_path);
			$image_mask = null;
			if (($mask_width != $panel_original_width || $mask_height != $panel_original_height) && $canresize)
				$image_mask = self::resize($image_mask_path, $panel_original_width, $panel_original_height);
			else
			{
				$image_type = $module->exifImageType($image_mask_path);
				$image_mask = ImageManager::create($image_type, $image_mask_path);
			}
			imagecopyresampled($image, $image_mask, 0, 0 + $y_offset, 0, 0, $panel_original_width, $panel_original_height,
			$panel_original_width, $panel_original_height);
			imagedestroy($image_mask);
		}

		public static function pastePNG($image, $paste, $y_offset = 0)
		{
			$width = imagesx($image);
			$height = imagesy($image);
			imagecopyresampled($image, $paste, 0, 0 + $y_offset, 0, 0, $width, $height, $width, $height);
		}

		public static function substitueImage($image, $mask, $mask_dir = 'mask', $canresize = true)
		{
			$dir = dirname(__FILE__);
			$image_mask_path = $mask['image'];
			$image_mask_path = realpath($dir.'/../data/'.$mask_dir.'/'.$image_mask_path);
			$module = Module::getInstanceByName(self::$module);

			$width = imagesx($image);
			$height = imagesy($image);
			list($mask_width, $mask_height) = getimagesize($image_mask_path);

			if (($mask_width != $width || $mask_height != $height) && $canresize)
				$image_mask = self::resize($image_mask_path, $width, $height);
			else
			{
				$image_type = $module->exifImageType($image_mask_path);
				$image_mask = ImageManager::create($image_type, $image_mask_path);
			}

			imagesavealpha($image_mask, true);
			imagealphablending($image_mask, true);

			$tmp = imagecreatetruecolor($width, $height);
			$transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
			imagefill($tmp, 0, 0, $transparent);
			imagecolortransparent($tmp, $transparent);
			imagealphablending($tmp, 1);
			imagesavealpha($tmp, 1);

			for ($y = 0; $y < $height; $y++)
			{
				for ($x = 0; $x < $width; $x++)
				{
					$color = imagecolorat($image, $x, $y);
					$color_rgb = imagecolorsforindex($image, $color);
					if ($x < $mask_width && $y < $mask_height)
					{
						$mask_color = imagecolorat($image_mask, $x, $y);
						$alpha = (($mask_color >> 24) & 0x7F);
						$new_color = imagecolorallocatealpha($tmp, $color_rgb['red'], $color_rgb['green'], $color_rgb['blue'], max(127 - $alpha, $color_rgb['alpha']));
						imagesetpixel($tmp, $x, $y, $new_color);
						imagecolordeallocate($tmp, $new_color);
					}
					else
						imagesetpixel($tmp, $x, $y, $color);
				}
			}

			imagecolordeallocate($tmp, $transparent);
			imagedestroy($image_mask);
			return $tmp;
		}

		public static function checkCache($data, $output = 1)
		{
			$dir = dirname(__FILE__);

			$type = $data['type'];
			if ($type != 'txt') $type = 'img';
			$data['type'] = 'img';

			$md5data = array();
			$md5data['text'] = $data['text'];
			$md5data['font'] = (int)$data['font'];
			$md5data['color'] = (int)$data['color'];
			$md5data['clr'] = $data['clr'];
			$md5data['alpha'] = (float)$data['alpha'];
			$md5data['mirror'] = (int)$data['mirror'];
			$md5data['center'] = (int)$data['center'];
			$md5data['size'] = (int)$data['size'];
			$md5data['type'] = 'img';
			$md5data['forpanel'] = 0;
			$md5data['curve'] = (int)$data['curve'];
			$md5data['letterspace'] = (int)$data['letterspace'];

			$hash = md5(serialize($md5data));
			self::$hash = $hash;
			self::$cache = $dir.'/../data/cache/'.$hash.'.png';
			if (_CTD_CACHE_ && file_exists(self::$cache) && $type == 'img')
			{
				if ($output)
				{
					header('Content-type: image/png');
					readfile(self::$cache);
					exit();
				}
				else
					return self::$hash;
			}
			return false;
		}

		public static function checkColorizeCache($hash, $output = 1)
		{
			$dir = dirname(__FILE__);
			$cache = $dir.'/../data/cache/'.$hash.'.png';
			if (_CTD_CACHE_ && file_exists($cache))
			{
				if ($output)
				{
					header('Content-type: image/png');
					readfile($cache);
					exit();
				}
				else
					return $cache;
			}
			return $cache;
		}

		public static function hex2rgb($hex)
		{
			$hex = str_replace('#', '', $hex);
			if (Tools::strlen($hex) == 3)
			{
				$r = hexdec(Tools::substr($hex, 0, 1).Tools::substr($hex, 0, 1));
				$g = hexdec(Tools::substr($hex, 1, 1).Tools::substr($hex, 1, 1));
				$b = hexdec(Tools::substr($hex, 2, 1).Tools::substr($hex, 2, 1));
			}
			else
			{
				$r = hexdec(Tools::substr($hex, 0, 2));
				$g = hexdec(Tools::substr($hex, 2, 2));
				$b = hexdec(Tools::substr($hex, 4, 2));
			}
			$rgb = array($r, $g, $b);
			return $rgb; // returns an array with the rgb values
		}

		public static function imageFlip($mode)
		{
			$width = imagesx (self::$image);
			$height = imagesy (self::$image);
			$src_x = 0;
			$src_y = 0;
			$src_width = $width;
			$src_height = $height;
			switch ($mode)
			{
				case '1': //vertical
					$src_y = $height - 1;
					$src_height = -$height;
					break;
				case '2': //horizontal
					$src_x = $width - 1;
					$src_width = -$width;
					break;
				case '3': //both
					$src_x = $width - 1;
					$src_y = $height - 1;
					$src_width = -$width;
					$src_height = -$height;
					break;
				default:
					break;
			}
			$imgdest = imagecreatetruecolor ($width, $height);
			imagealphablending($imgdest, 0);
			imagesavealpha($imgdest, 1);
			if (imagecopyresampled ($imgdest, self::$image, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height))
				self::$image = $imgdest;
		}

		public static function getFont($id_font)
		{
			if (! (int)$id_font)
				return false;
			$query = 'SELECT * FROM '._DB_PREFIX_.self::$module.'_font WHERE id = '.(int)$id_font.' ORDER BY position ASC';
			return Db::getInstance()->getRow($query);
		}

		public static function getColor($id_color)
		{
			if (! (int)$id_color)
				return false;
			$query = 'SELECT * FROM '._DB_PREFIX_.self::$module.'_color WHERE id = '.(int)$id_color;
			return Db::getInstance()->getRow($query);
		}

		public static function getImage($id_image)
		{
			if (! (int)$id_image)
				return false;
			$query = 'SELECT * FROM '._DB_PREFIX_.self::$module.'_image WHERE id = '.(int)$id_image;
			return Db::getInstance()->getRow($query);
		}

		public static function measure($ignore_space = 1)
		{
			if (self::$type != 'img')
			{
				self::$text = str_replace("\n", '', self::$text);
				if ($ignore_space == '1')
					self::$text = str_replace(' ', '', self::$text);
			}

			if (self::$forpanel)
			{
				$images_type = ImageType::getByNameNType(Configuration::get(self::$module.'image_type'), 'products');
				$original_width = (int)$images_type['width'];
				$rect = imagettfbbox(self::$size, 0, self::$font, self::$text);
				$width  = $rect[2] - $rect[0];
				while ($width > $original_width * self::$coeff)
				{
					self::$size = self::$size - 1;
					$rect = imagettfbbox(self::$size, 0, self::$font, self::$text);
					$width  = $rect[2] - $rect[0];
				}
			}

			$rect = self::calculateTextBox(self::$text, self::$font, self::$size, 0);
			self::$y = $rect['top'] + 5;
			self::$x = $rect['left'] + 5;
			self::$width  = $rect['width'] + 10;
			self::$height = $rect['height'] + 10;
			self::$rect = $rect['box'];
			$r = 255;
			$g = 0;
			$b = 0;
			$a = 0;
			self::$image = imagecreatetruecolor(self::$width, self::$height);
			self::$color = imagecolorallocatealpha(self::$image, $r, $g, $b, $a);
			$transparent = imagecolorallocatealpha(self::$image, 0, 0, 0, 127);
			imagecolortransparent(self::$image, $transparent);
			imagealphablending(self::$image, 0);
			imagesavealpha(self::$image, 0);
			imagefill(self::$image, 0, 0, $transparent);

			self::$x = 0;
			self::$width -= 10;
			self::$height -= 10;
			imagettftext(self::$image, self::$size, 0, self::$x, self::$y, self::$color, self::$font, self::$text);

			if (self::$width && self::$height)
				self::trimImage();
			if (is_resource(self::$image) && get_resource_type(self::$image) == 'gd')
				imagedestroy(self::$image);
		}

		public static function preciseMeasure($txt, $font, $size, $fast = 0, $destroy = 1, $for_rotation = 0)
		{
			$bbox = imagettfbbox($size, 0, $font, $txt);
			$w = abs($bbox[4] - $bbox[0]) + 10;
			$h = abs($bbox[5] - $bbox[1]) + 10;
			$trim = str_replace(' ', '', trim($txt));
			if (empty($trim) || $fast)
				return array('w' => $w - 10, 'h' => $h - 10);
			$im = imagecreatetruecolor($w, $h);
			$transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
			$red = imagecolorallocate($im, 0xFF, 0, 0);
			imagealphablending($im, 0);
			imagesavealpha($im, 0);
			imagefill($im, 0, 0, $transparent);

			$tx = 5;
			$ty = $h - 5;

			imagettftext($im, $size, 0, $tx, $ty, $red, $font, $txt);

			//top
			for ($y = 0; $y < $h; $y++)
			{
				$transparent_line = true;
				for ($x = 0; $x < $w; $x++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
				{
					$ty --;
					$h --;
				}
				else
					break;
			}

			//left
			for ($x = 0; $x < $w; $x++)
			{
				$transparent_line = true;
				for ($y = 0; $y < $h; $y++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
				{
					$tx --;
					$w --;
				}
				else
					break;
			}

			imagedestroy($im);
			$im = imagecreatetruecolor($w + 1, $h + 1);
			$transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
			$red = imagecolorallocate($im, 0xFF, 0, 0);
			imagealphablending($im, 0);
			imagesavealpha($im, 0);
			imagefill($im, 0, 0, $transparent);

			imagettftext($im, $size, 0, $tx, $ty, $red, $font, $txt);

			//bottom
			for ($y = $h - 1; $y >= 0; $y--)
			{
				$transparent_line = true;
				for ($x = 0; $x < $w; $x++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
					$h --;
				else
					break;
			}

			//right
			for ($x = $w - 1; $x >= 0; $x--)
			{
				$transparent_line = true;
				for ($y = 0; $y < $h; $y++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
					$w --;
				else
					break;
			}

			//right: size correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource($im) && get_resource_type($im) == 'gd')
					imagedestroy($im);
				self::checkSize($w, $h);
				$im = imagecreatetruecolor($w, $h);
				imagecolortransparent($im, $transparent);
				imagealphablending($im, 0);
				imagesavealpha($im, 0);
				imagefill($im, 0, 0, $transparent);
				imagettftext($im, $size, 0, $tx, $ty, $red, $font, $txt);
				$x = $w - 1;
				$transparent_line = true;
				for ($y = 0; $y < $h; $y++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
					$w ++;
			}
			$w --;

			//bottom: size correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource(self::$image) && get_resource_type(self::$image) == 'gd')
					imagedestroy(self::$image);
				self::checkSize($w, $h);
				$im = imagecreatetruecolor($w, $h);
				imagecolortransparent($im, $transparent);
				imagealphablending($im, 0);
				imagesavealpha($im, 0);
				imagefill($im, 0, 0, $transparent);
				imagettftext($im, $size, 0, $tx, $ty, $red, $font, $txt);
				$y = $h - 1;
				$transparent_line = true;
				for ($x = 0; $x < $w; $x++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
					$h ++;
			}
			$h --;

			//left edge correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource($im) && get_resource_type($im) == 'gd')
					imagedestroy($im);
				self::checkSize($w, $h);
				$im = imagecreatetruecolor($w, $h);
				imagecolortransparent($im, $transparent);
				imagealphablending($im, 0);
				imagesavealpha($im, 0);
				imagefill($im, 0, 0, $transparent);
				imagettftext($im, $size, 0, $tx, $ty, $red, $font, $txt);
				$x = 0;
				$transparent_line = true;
				for ($y = 0; $y < $h; $y++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
				{
					$tx ++;
					$w ++;
				}
			}
			$tx --;
			$w --;

			//top edge correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource($im) && get_resource_type($im) == 'gd')
					imagedestroy($im);
				self::checkSize($w, $h);
				$im = imagecreatetruecolor($w, $h);
				imagecolortransparent($im, $transparent);
				imagealphablending($im, 0);
				imagesavealpha($im, 0);
				imagefill($im, 0, 0, $transparent);
				imagettftext($im, $size, 0, $tx, $ty, $red, $font, $txt);
				$y = 0;
				$transparent_line = true;
				for ($x = 0; $x < $w; $x++)
				{
					$c = imagecolorat($im, $x, $y);
					$c = imagecolorsforindex($im, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
				{
					$ty ++;
					$h ++;
				}
			}
			$ty --;
			$h --;

			imagecolordeallocate($im, $transparent);
			imagecolordeallocate($im, $red);
			$w = imagesx($im);
			$h = imagesy($im);
			if ($for_rotation)
			{
				$padding = 10;
				$tmp_im = imagecreatetruecolor($w + $padding, $h + $padding);
				$tr = imagecolorallocatealpha($tmp_im, 0, 0, 0, 127);
				imagefill($tmp_im, 0, 0, $tr);
				imagealphablending($tmp_im, 0);
				imagesavealpha($tmp_im, 1);
				imagecopy($tmp_im, $im, $padding / 2, $padding / 2, 0, 0, $w, $h);
				imagecolordeallocate($tmp_im, $tr);
				imagedestroy($im);
				$im = $tmp_im;
				$w = $w + $padding;
				$h = $h + $padding;
			}
			if ($destroy)
				imagedestroy($im);
			return array('w' => $w, 'h' => $h, 'im' => $im);
		}

		public static function imagetrim(&$im, $bg, $pad = null)
		{
			$ymin = -1;
			// Calculate padding for each side.
			if (isset($pad))
			{
				$pp = explode(' ', $pad);
				if (isset($pp[3]))
					$p = array((int)$pp[0], (int)$pp[1], (int)$pp[2], (int)$pp[3]);
				elseif (isset($pp[2]))
					$p = array((int)$pp[0], (int)$pp[1], (int)$pp[2], (int)$pp[1]);
				elseif (isset($pp[1]))
					$p = array((int)$pp[0], (int)$pp[1], (int)$pp[0], (int)$pp[1]);
				else
					$p = array_fill(0, 4, (int)$pp[0]);
			}
			else
				$p = array_fill(0, 4, 0);

			// Get the image width and height.
			$imw = imagesx($im);
			$imh = imagesy($im);

			// Set the X variables.
			$xmin = $imw;
			$xmax = 0;
			$ymax = 0;

			//Start scanning for the edges.
			for ($iy = 0; $iy < $imh; $iy++)
			{
				$first = true;
				for ($ix = 0; $ix < $imw; $ix++)
				{
					$ndx = imagecolorat($im, $ix, $iy);
					if ($ndx != $bg)
					{
						if ($xmin > $ix)
							$xmin = $ix;
						if ($xmax < $ix)
							$xmax = $ix;
						if ($ymin < 0)
							$ymin = $iy;
						$ymax = $iy;
						if ($first)
						{
							$ix = $xmax;
							$first = false;
						}
					}
				}
			}

			// The new width and height of the image. (not including padding)
			$imw = 1 + $xmax - $xmin; // Image width in pixels
			$imh = 1 + $ymax - $ymin; // Image height in pixels

			// Make another image to place the trimmed version in.
			$im2 = imagecreatetruecolor($imw + $p[1] + $p[3], $imh + $p[0] + $p[2]);

			// Make the background of the new image the same as the background of the old one.
			$bg2 = imagecolorallocate($im2, ($bg >> 16) & 0xFF, ($bg >> 8) & 0xFF, $bg & 0xFF);
			imagefill($im2, 0, 0, $bg2);
			imagealphablending($im2, 0);
			imagesavealpha($im2, 1);
			// Copy it over to the new image.
			imagecopy($im2, $im, $p[3], $p[0], $xmin, $ymin, $imw, $imh);

			// To finish up, we replace the old image which is referenced.
			imagedestroy($im);
			$im = $im2;
			return $im;
		}

		public static function trimImage()
		{
			//top
			for ($y = 0; $y < self::$height; $y++)
			{
				$transparent_line = true;
				for ($x = 0; $x < self::$width; $x++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
				{
					self::$y --;
					self::$height --;
				}
				else
					break;
			}

			//left
			for ($x = 0; $x < self::$width; $x++)
			{
				$transparent_line = true;
				for ($y = 0; $y < self::$height; $y++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
				{
					self::$x --;
					self::$width --;
				}
				else
					break;
			}

			//bottom
			for ($y = self::$height - 1; $y >= 0; $y--)
			{
				$transparent_line = true;
				for ($x = 0; $x < self::$width; $x++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
					self::$height --;
				else
					break;
			}

			//right
			for ($x = self::$width - 1; $x >= 0; $x--)
			{
				$transparent_line = true;
				for ($y = 0; $y < self::$height; $y++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if ($transparent_line)
					self::$width --;
				else
					break;
			}

			//right: size correction
			$transparent = imagecolorallocatealpha(self::$image, 0, 0, 0, 127);
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource(self::$image) && get_resource_type(self::$image) == 'gd')
					imagedestroy(self::$image);
				self::checkSize();
				self::$image = imagecreatetruecolor(self::$width, self::$height);

				imagecolortransparent(self::$image, $transparent);
				imagealphablending(self::$image, 0);
				imagesavealpha(self::$image, 0);
				imagefill(self::$image, 0, 0, $transparent);
				imagettftext(self::$image, self::$size, 0, self::$x, self::$y, self::$color, self::$font, self::$text);
				$x = self::$width - 1;
				$transparent_line = true;
				for ($y = 0; $y < self::$height; $y++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
					self::$width ++;
			}
			self::$width --;

			//bottom: size correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource(self::$image) && get_resource_type(self::$image) == 'gd')
					imagedestroy(self::$image);
				self::checkSize();
				self::$image = imagecreatetruecolor(self::$width, self::$height);

				imagecolortransparent(self::$image, $transparent);
				imagealphablending(self::$image, 0);
				imagesavealpha(self::$image, 0);
				imagefill(self::$image, 0, 0, $transparent);
				imagettftext(self::$image, self::$size, 0, self::$x, self::$y, self::$color, self::$font, self::$text);
				$y = self::$height - 1;
				$transparent_line = true;
				for ($x = 0; $x < self::$width; $x++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
					self::$height ++;
			}
			self::$height --;

			//left edge correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource(self::$image) && get_resource_type(self::$image) == 'gd')
					imagedestroy(self::$image);
				self::checkSize();
				self::$image = imagecreatetruecolor(self::$width, self::$height);

				imagecolortransparent(self::$image, $transparent);
				imagealphablending(self::$image, 0);
				imagesavealpha(self::$image, 0);
				imagefill(self::$image, 0, 0, $transparent);
				imagettftext(self::$image, self::$size, 0, self::$x, self::$y, self::$color, self::$font, self::$text);
				$x = 0;
				$transparent_line = true;
				for ($y = 0; $y < self::$height; $y++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
				{
					self::$x ++;
					self::$width ++;
				}
			}
			self::$x --;
			self::$width --;

			//top edge correction
			$transparent_line = false;
			while (! $transparent_line)
			{
				if (is_resource(self::$image) && get_resource_type(self::$image) == 'gd')
					imagedestroy(self::$image);
				self::checkSize();
				self::$image = imagecreatetruecolor(self::$width, self::$height);

				imagecolortransparent(self::$image, $transparent);
				imagealphablending(self::$image, 0);
				imagesavealpha(self::$image, 0);
				imagefill(self::$image, 0, 0, $transparent);
				imagettftext(self::$image, self::$size, 0, self::$x, self::$y, self::$color, self::$font, self::$text);
				$y = 0;
				$transparent_line = true;
				for ($x = 0; $x < self::$width; $x++)
				{
					$c = imagecolorat(self::$image, $x, $y);
					$c = imagecolorsforindex(self::$image, $c);
					if ($c['alpha'] != 127 || $c['red'] + $c['green'] + $c['blue'] != 0)
					{
						$transparent_line = false;
						break;
					}
				}
				if (! $transparent_line)
				{
					self::$y ++;
					self::$height ++;
				}
			}
			self::$y --;
			self::$height --;

			imagecolordeallocate(self::$image, $transparent);
		}

		public static function resize($image, $width, $height)
		{
			if (! self::$instance)
				self::$instance = Module::getInstanceByName(self::$module);
			if (!is_resource($image) || get_resource_type($image) != 'gd')
			{
				$image = self::$instance->urlToPath($image);
				list($src_width, $src_height) = getimagesize($image);
				$image_type = self::$instance->exifImageType($image);
				$src_image = ImageManager::create($image_type, $image);
			}
			else
			{
				$src_width = imagesx($image);
				$src_height = imagesy($image);
				$src_image = $image;
			}
			if ($width == $src_width && $height == $src_height)
				return $src_image;
			self::checkSize($width, $height);
			$dst_image = imagecreatetruecolor($width, $height);
			imagealphablending($dst_image, false);
			imagesavealpha($dst_image, true);
			imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
			//imagedestroy($src_image);
			return $dst_image;
		}

		public static function changeRatio($image, $proportion)
		{
			$width = imagesx($image);
			$height = imagesy($image);
			if ($proportion > 1)
			{
				if ($width < $height * $proportion)
					$width = $height * $proportion;
				else
					$height = $width / $proportion;
			}
			elseif ($proportion < 1)
			{
				if ($height < $width / $proportion)
					$height = $width / $proportion;
				else
					$width = $height * $proportion;
			}
			return self::resize($image, $width, $height);
		}

		public static function render($text, $font, $clr, $color = 0, $size = 4, $alpha = 0, $ignore_space = 0,
			$mirror = 0, $center = 0, $curve = 0, $letterspace = 0, $type = 'img', $forpanel = 0)
		{
			$data = array();
			$data['text'] = $text;
			$data['font'] = (int)$font;
			$data['color'] = (int)$color;
			$data['clr'] = str_replace('#', '', $clr);
			$data['alpha'] = (float)$alpha;
			$data['ignore_space'] = $ignore_space;
			$data['mirror'] = (int)$mirror;
			$data['center']	= (int)$center;
			$data['size'] = (int)$size;
			$data['type'] = $type;
			$data['forpanel'] = $forpanel;
			$data['curve'] = (int)$curve;
			$data['letterspace'] = (int)$letterspace;

			return CustomImage::preview($data, 0).'.png';
		}

		public static function thumb($img, $w = 0, $h = 0, $dir = 'image', $m = 'auto', $colorize = false)
		{
			$dir = "data/$dir/";
			include_once(dirname(__FILE__).'/resize-class.php');
			$hash = md5("$img-$w-$h-$dir-$m");
			$cacheimage = dirname(__FILE__).'/../data/cache/'.$hash.'.png';

			if (_CTD_CACHE_ && file_exists($cacheimage))
			{
				if (!$colorize)
					return basename($cacheimage);
				else
				{
					if (! self::$instance)
						self::$instance = Module::getInstanceByName(self::$module);
					$cfg = self::$instance->getConfigKeys();
					$customcolor = $cfg['font_color'];
					return basename(self::colorize(array('customcolor' => $customcolor), 0, $cacheimage));
				}
			}

			$imgpath = dirname(__FILE__).'/../'.$dir.$img;

			if (empty($imgpath) || !file_exists($imgpath))
			{
				$pixel = dirname(__FILE__).'/../data/cache/pixel.png';
				if (!file_exists($pixel))
					copy(dirname(__FILE__).'/../img/pixel.png', $pixel);
				return 'pixel.png';
			}

			list($width, $height) = getimagesize($imgpath);
			if (!$w || $w > $width) $w = $width;
			if (!$h || $h > $height) $h = $height;
			if (!$m) $m = 'exact';

			$resize_obj = new ResizeImg($imgpath);
			$resize_obj->resizeImage($w, $h, $m);
			$resize_obj->saveImage($cacheimage, 100);

			if (!$colorize)
				return basename($cacheimage);
			else
			{
				if (! self::$instance)
					self::$instance = Module::getInstanceByName(self::$module);
				$cfg = self::$instance->getConfigKeys();
				$customcolor = $cfg['font_color'];
				return basename(self::colorize(array('customcolor' => $customcolor), 0, $cacheimage));
			}
		}

		public static function checkSize($w = -1, $h = -1)
		{
			if ($w < 0) $w = self::$width;
			if ($h < 0) $h = self::$height;
			if (!$w || !$h)
				self::outputPixel();
		}

		public static function outputPixel()
		{
			if (self::$type == 'img')
			{
				self::$image = ImageManager::create(IMAGETYPE_PNG, dirname(__FILE__).'/../img/pixel.png');
				self::output(self::$output);
				return self::$hash;
			}
			else
			{
				if (! self::$instance)
					self::$instance = Module::getInstanceByName(self::$module);
				self::$instance->dbg(debug_backtrace(false));
				exit(Tools::jsonEncode(array('width'=>1,'height'=>1,'cache'=>self::$hash)));
			}
		}

		public static function outputSize()
		{
			$width  = self::$rect[2] - self::$rect[0];
			$height = self::$rect[1] - self::$rect[5];
			$width = self::$width;
			$height = self::$height;
			if (self::$text == ' ')
				exit(Tools::jsonEncode(array('width' => 1, 'height' => 1, 'cache' => self::$hash)));
			exit(Tools::jsonEncode(array('width' => $width, 'height' => $height, 'cache' => self::$hash)));
		}

		public static function outputImage($exit = true)
		{
			if (! self::$debug)
				header('Content-type: image/png');
			imagepng(self::$image);
			imagedestroy(self::$image);
			if ($exit) exit();
		}

		public static function save()
		{
			imagepng(self::$image, self::$cache);
			chmod(self::$cache, 0774);
		}

		public static function output($output = 1)
		{
			if (self::$type != 'img')
				self::outputSize();
			else
				self::save();
			if ($output)
				self::outputImage();
		}

		public static function debug($img)
		{
			if (Tools::getIsset('nd')) return false;
			header('Content-type: image/png');
			imagepng($img);
			imagedestroy($img);
			exit();
		}

	}

?>
