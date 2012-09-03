<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Helper_String {

	/**
	 * Create a Random String
	 *
	 * Useful for generating passwords or hashes.
	 *
	 * @access	public
	 * @param	string	type of random string.  basic, alpha, alunum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	integer	number of characters
	 * @return	string
	 */
	public static function random_string($type = 'alnum', $len = 8)
	{
		switch($type)
		{
			case 'basic' : return mt_rand();
				break;
			case 'alnum' :
			case 'numeric' :
			case 'nozero' :
			case 'alpha' :

				switch($type)
				{
					case 'alpha' : $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum' : $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric' : $pool = '0123456789';
						break;
					case 'nozero' : $pool = '123456789';
						break;
				}

				$str = '';
				for($i = 0; $i < $len; $i++)
				{
					$str .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
				}
				return $str;
				break;
			case 'unique' :
			case 'md5' :

				return md5(uniqid(mt_rand()));
				break;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Create URL Title
	 *
	 * Takes a "title" string as input and creates a
	 * human-friendly URL string with either a dash
	 * or an underscore as the word separator.
	 *
	 * @access	public
	 * @param	string	the string
	 * @param	string	the separator: dash, or underscore
	 * @return	string
	 */
	public static function url_title($str, $separator = 'dash', $lowercase = FALSE)
	{
		if($separator == 'dash')
		{
			$search = '_';
			$replace = '-';
		}
		else
		{
			$search = '-';
			$replace = '_';
		}

		$trans = array(
			'&\#\d+?;' => '',
			'&\S+?;' => '',
			'\s+' => $replace,
			'[^a-z0-9\-\._]' => '',
			$replace.'+' => $replace,
			$replace.'$' => $replace,
			'^'.$replace => $replace,
			'\.+$' => ''
		);

		$str = strip_tags($str);

		foreach($trans as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		if($lowercase === TRUE)
		{
			$str = strtolower($str);
		}

		return trim(stripslashes($str));
	}

}