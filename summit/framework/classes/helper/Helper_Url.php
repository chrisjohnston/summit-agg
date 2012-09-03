<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Helper_Url {

	/**
	 * Full Url
	 * 
	 * Return full url of current http request
	 * 
	 * @return str
	 */
	public static function full_url()
	{
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos(strtolower($_SERVER["SERVER_PROTOCOL"]), "/")).$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
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
			'&\#\d+?;'=>'',
			'&\S+?;'=>'',
			'\s+'=>$replace,
			'[^a-z0-9\-\._]'=>'',
			$replace.'+'=>$replace,
			$replace.'$'=>$replace,
			'^'.$replace=>$replace,
			'\.+$'=>''
		);

		$str = strip_tags($str);

		foreach($trans as $key=>$val)
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