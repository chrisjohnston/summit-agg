<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Remote Request
 *
 * Get a remote resource
 *
 * @author Scott Darby
 */
class Fl_Remote {

	private static $_curl_handler;
	private static $_document;

	// ------------------------------------------------------------------------

	/**
	 * Get Remote
	 *
	 * @param str $url
	 * @return mixed
	 */
	public static function curl_get($url, $cache=FALSE, $port=80)
	{
		if(!is_numeric($port))
		{
			throw new Exception('Var is NAN');
		}

		//should we use the cache?
		if($cache == TRUE)
		{
			$cache = new Fl_Cache($url);
			if($cache->check())
			{
				return $cache->get();
			}
			else
			{
				$file_contents = self::_execute_curl($url, $port);
				
				//only write file if curl has actually grabbed some data
				if($file_contents != '' OR $file_contents != FALSE)
				{
					$cache->write_file($file_contents);
					return $file_contents;
				}
				else
				{
					$cache_file = $cache->get(); //try to send the cache file as remote resource not responding
					
					if($cache_file)
					{
						return $cache_file;
					}
					throw new Exception('Could not load cache file from disk');
				}
			}
		}
		else
		{
			return self::_execute_curl($url, $port);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Excecute Curl
	 * 
	 * @param string $url
	 * @return string
	 */
	private static function _execute_curl($url, $port)
	{
		self::$_curl_handler = curl_init($url);
		curl_setopt(self::$_curl_handler, CURLOPT_FAILONERROR, 1);
		curl_setopt(self::$_curl_handler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt(self::$_curl_handler, CURLOPT_PORT, $port);
		curl_setopt(self::$_curl_handler, CURLOPT_TIMEOUT, 30);
		curl_setopt(self::$_curl_handler, CURLOPT_HEADER, FALSE);
		curl_setopt(self::$_curl_handler, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		self::$_document = curl_exec(self::$_curl_handler);

		if(self::$_document == 0)
		{
			curl_error(self::$_curl_handler);
		}

		curl_close(self::$_curl_handler);
		
		return self::$_document;
	}

}
