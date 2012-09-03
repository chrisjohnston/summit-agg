<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Helper_Text {

	/**
	 * Character Limiter
	 *
	 * Limits the string based on the character count.  Preserves complete words
	 * so the character count may not be exactly as specified.
	 *
	 * @access	public
	 * @param	string
	 * @param	integer
	 * @param	string	the end character. Usually an ellipsis
	 * @return	string
	 */
	public static function character_limiter($str, $n = 500, $end_char = '&#8230;', $stupid_mode = FALSE)
	{
		if(strlen($str) < $n)
		{
			return $str;
		}

		$str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

		if(strlen($str) <= $n)
		{
			return $str;
		}

		if($stupid_mode == TRUE)
		{
			return substr($str, 0, $n).$end_char;
		}

		$out = "";
		foreach(explode(' ', trim($str)) as $val)
		{
			$out .= $val.' ';

			if(strlen($out) >= $n)
			{
				$out = trim($out);
				return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
			}
		}
	}
	
	//--------------------------------------------------------------------------
	
	/**
	 * Wrap Links
	 * 
	 * Wraps all links in <a> tags and applies link as href
	 * 
	 * @param str
	 * @return str
	 */
	public static function wrap_links($str)
	{
		$regex = '/((?:http|https):\/\/[a-z0-9\/\?=_#&%~-]+(\.[a-z0-9\/\?=_#&%~-]+)+)|(www(\.[a-z0-9\/\?=_#&%~-]+){2,})/i';
		return preg_replace($regex, '<a href="$1">$1</a>', $str);
	}

}