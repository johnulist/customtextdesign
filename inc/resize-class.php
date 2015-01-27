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

	class ResizeImg
	{
		private $image;
		private $width;
		private $height;
		private $image_resized;

		public function __construct($filename)
		{
			// *** Open up the file
			$this->image = $this->openImage($filename);

			// *** Get width and height
			$this->width = imagesx($this->image);
			$this->height = imagesy($this->image);
		}

		## --------------------------------------------------------

		private function openImage($file)
		{
			$sz = getimagesize($file);
			$img = false;
			switch ($sz['mime'])
			{
				case 'image/png':
					$img = imagecreatefrompng($file);
					break;
				case 'image/jpeg':
					$img = imagecreatefromjpeg($file);
					break;
				case 'image/gif':
					$img = imagecreatefromgif ($file);
					break;
			}
			return $img;
		}

		## --------------------------------------------------------

		public function resizeImage($newwidth, $newheight, $option = 'auto')
		{
			// *** Get optimal width and height - based on $option
			$optionarray = $this->getDimensions($newwidth, $newheight, $option);

			$optimalwidth = (int)$optionarray['optimalWidth'];
			$optimalheight = (int)$optionarray['optimalHeight'];
			if (! $optimalwidth) $optimalwidth = 1;
			if (! $optimalheight) $optimalheight = 1;

			// *** Resample - create image canvas of x, y size
			$this->image_resized = imagecreatetruecolor($optimalwidth, $optimalheight);
			imagealphablending($this->image_resized, 0);
			imagesavealpha($this->image_resized, 1);
			imagecopyresampled($this->image_resized, $this->image, 0, 0, 0, 0, $optimalwidth, $optimalheight, $this->width, $this->height);

			// *** if option is 'crop', then crop too
			if ($option == 'crop')
				$this->crop($optimalwidth, $optimalheight, $newwidth, $newheight);
		}

		## --------------------------------------------------------

		private function getDimensions($newwidth, $newheight, $option)
		{
			switch ($option)
			{
				case 'exact':
					$optimalwidth = $newwidth;
					$optimalheight = $newheight;
					break;
				case 'portrait':
					$optimalwidth = $this->getSizeByFixedHeight($newheight);
					$optimalheight = $newheight;
					break;
				case 'landscape':
					$optimalwidth = $newwidth;
					$optimalheight = $this->getSizeByFixedWidth($newwidth);
					break;
				case 'auto':
					$optionarray = $this->getSizeByAuto($newwidth, $newheight);
					$optimalwidth = $optionarray['optimalWidth'];
					$optimalheight = $optionarray['optimalHeight'];
					break;
				case 'crop':
					$optionarray = $this->getOptimalCrop($newwidth, $newheight);
					$optimalwidth = $optionarray['optimalWidth'];
					$optimalheight = $optionarray['optimalHeight'];
					break;
			}
			return array('optimalWidth' => $optimalwidth, 'optimalHeight' => $optimalheight);
		}

		## --------------------------------------------------------

		private function getSizeByFixedHeight($newheight)
		{
			$ratio = $this->width / $this->height;
			$newwidth = $newheight * $ratio;
			return $newwidth;
		}

		private function getSizeByFixedWidth($newwidth)
		{
			$ratio = $this->height / $this->width;
			$newheight = $newwidth * $ratio;
			return $newheight;
		}

		private function getSizeByAuto($newwidth, $newheight)
		{
			$optimalwidth = $newwidth;
			$optimalheight = $newheight;

			if ($newwidth != $this->width)
			{
				$optimalwidth = $newwidth;
				$optimalheight = $this->getSizeByFixedWidth($newwidth);
			}
			elseif ($newheight != $this->height)
			{
				$optimalwidth = $this->getSizeByFixedHeight($newheight);
				$optimalheight = $newheight;
			}

			return array('optimalWidth' => $optimalwidth, 'optimalHeight' => $optimalheight);
		}

		## --------------------------------------------------------

		private function getOptimalCrop($newwidth, $newheight)
		{
			$heightratio = $this->height / $newheight;
			$widthratio = $this->width / $newwidth;

			if ($heightratio < $widthratio)
				$optimalratio = $heightratio;
			else
				$optimalratio = $widthratio;

			$optimalheight = $this->height / $optimalratio;
			$optimalwidth = $this->width / $optimalratio;

			return array('optimalWidth' => $optimalwidth, 'optimalHeight' => $optimalheight);
		}

		## --------------------------------------------------------

		private function crop($optimalwidth, $optimalheight, $newwidth, $newheight)
		{
			// *** Find center - this will be used for the crop
			$cropstartx = ($optimalwidth / 2) - ($newwidth / 2);
			$cropstarty = ($optimalheight / 2) - ($newheight / 2);

			$crop = $this->image_resized;
			//imagedestroy($this->image_resized);

			// *** Now crop from center to exact requested size
			$this->image_resized = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresampled($this->image_resized, $crop, 0, 0, $cropstartx, $cropstarty, $newwidth, $newheight, $newwidth, $newheight);
		}

		## --------------------------------------------------------

		public function saveImage($save_path, $image_quality = '100')
		{
			// *** Get extension
			$extension = strrchr($save_path, '.');
			$extension = Tools::strtolower($extension);

			switch ($extension)
			{
				case '.jpg':
				case '.jpeg':
					if (imagetypes() & IMG_JPG)
						imagejpeg($this->image_resized, $save_path, $image_quality);
					break;

				case '.gif':
					if (imagetypes() & IMG_GIF)
						imagegif ($this->image_resized, $save_path);
					break;

				case '.png':
					// *** Scale quality from 0-100 to 0-9
					$scale_quality = round(($image_quality / 100) * 9);

					// *** Invert quality setting as 0 is best, not 9
					$invert_scale_quality = 9 - $scale_quality;

					if (imagetypes() & IMG_PNG)
						imagepng($this->image_resized, $save_path, $invert_scale_quality);
					break;

					// ... etc

				default:
					// *** No extension - No save.
					break;
			}

			imagedestroy($this->image_resized);
		}


		## --------------------------------------------------------

	}
?>
