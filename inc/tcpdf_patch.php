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

	class TcpdfPatch extends TCPDF{

		/**
		* Output fonts.
		* @author Nicola Asuni
		* @protected
		*/
		protected function _putfonts()
		{
			$nf = $this->n;
			foreach ($this->diffs as $diff)
			{
				//Encodings
				$this->_newobj();
				$this->_out('<< /Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.'] >>'."\n".'endobj');
			}
			foreach ($this->FontFiles as $file => $info)
			{
				// search and get font file to embedd
				$fontfile = TcpdfFonts::getFontFullPath($file, $info['fontdir']);
				if (!$this->emptyString($fontfile))
				{
					$font = Tools::file_get_contents($fontfile);
					$compressed = (Tools::substr($file, -2) == '.z');
					if ((!$compressed) && (isset($info['length2'])))
					{
						$header = (ord($font[0]) == 128);
						if ($header)
							// strip first binary header
							$font = Tools::substr($font, 6);
						if ($header && (ord($font[$info['length1']]) == 128))
							// strip second binary header
							$font = Tools::substr($font, 0, $info['length1']).Tools::substr($font, ($info['length1'] + 6));
					}
					$this->_newobj();
					$this->FontFiles[$file]['n'] = $this->n;
					$stream = $this->_getrawstream($font);
					$out = '<< /Length '.Tools::strlen($stream);
					if ($compressed)
						$out .= ' /Filter /FlateDecode';
					$out .= ' /Length1 '.$info['length1'];
					if (isset($info['length2']))
						$out .= ' /Length2 '.$info['length2'].' /Length3 0';
					$out .= ' >>';
					$out .= ' stream'."\n".$stream."\n".'endstream';
					$out .= "\n".'endobj';
					$this->_out($out);
				}
			}
			foreach ($this->fontkeys as $k)
			{
				//Font objects
				$font = $this->getFontBuffer($k);
				$type = $font['type'];
				$name = $font['name'];
				if ($type == 'core')
				{
					// standard core font
					$out = $this->_getobj($this->font_obj_ids[$k])."\n";
					$out .= '<</Type /Font';
					$out .= ' /Subtype /Type1';
					$out .= ' /BaseFont /'.$name;
					$out .= ' /Name /F'.$font['i'];
					if ((Tools::strtolower($name) != 'symbol') && (Tools::strtolower($name) != 'zapfdingbats'))
						$out .= ' /Encoding /WinAnsiEncoding';
					if ($k == 'helvetica')
						// add default font for annotations
						$this->annotation_fonts[$k] = $font['i'];
					$out .= ' >>';
					$out .= "\n".'endobj';
					$this->_out($out);
				}
				elseif (($type == 'Type1') || ($type == 'TrueType'))
				{
					// additional Type1 or TrueType font
					$out = $this->_getobj($this->font_obj_ids[$k])."\n";
					$out .= '<</Type /Font';
					$out .= ' /Subtype /'.$type;
					$out .= ' /BaseFont /'.$name;
					$out .= ' /Name /F'.$font['i'];
					$out .= ' /FirstChar 32 /LastChar 255';
					$out .= ' /Widths '.($this->n + 1).' 0 R';
					$out .= ' /FontDescriptor '.($this->n + 2).' 0 R';
					if ($font['enc'])
					{
						if (isset($font['diff']))
							$out .= ' /Encoding '.($nf + $font['diff']).' 0 R';
						else
							$out .= ' /Encoding /WinAnsiEncoding';
					}
					$out .= ' >>';
					$out .= "\n".'endobj';
					$this->_out($out);
					// Widths
					$this->_newobj();
					$s = '[';
					for ($i = 32; $i < 256; ++$i)
					{
						if (isset($font['cw'][$i]))
							$s .= $font['cw'][$i].' ';
						else
							$s .= $font['dw'].' ';
					}
					$s .= ']';
					$s .= "\n".'endobj';
					$this->_out($s);
					//Descriptor
					$this->_newobj();
					$s = '<</Type /FontDescriptor /FontName /'.$name;
					foreach ($font['desc'] as $fdk => $fdv)
					{
						if (is_float($fdv))
							$fdv = sprintf('%F', $fdv);
						$s .= ' /'.$fdk.' '.$fdv.'';
					}
					if (!$this->emptyString($font['file']))
						$s .= ' /FontFile'.($type == 'Type1' ? '' : '2').' '.$this->FontFiles[$font['file']]['n'].' 0 R';
					$s .= '>>';
					$s .= "\n".'endobj';
					$this->_out($s);
				}
				else
				{
					// additional types
					$mtd = '_put'.Tools::strtolower($type);
					if (!method_exists($this, $mtd))
						$this->Error('Unsupported font type: '.$type);
					$this->$mtd($font);
				}
			}
		}

		/**
		* Determine whether a string is empty.
		* @param $str (string) string to be checked
		* @return boolean true if string is empty
		* @since 4.5.044 (2009-04-16)
		* @public static
		*/
		public function emptyString($str)
		{
			return (is_null($str) || (is_string($str) && (Tools::strlen($str) == 0)));
		}

	}
?>
