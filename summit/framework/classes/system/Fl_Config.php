<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Simple Config Class
 *
 * @author Scott Darby
 */
class Fl_Config {

	private static $_config = array();

// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Get a single config item or array of items in group
	 *
	 * @param str $group
	 * @param str $item
	 * @return mixed
	 */
	public static function get($group, $item = FALSE)
	{
		if($item === FALSE)
		{
			return isset(self::$_config[$group]) ? self::$_config[$group] : FALSE;
		}
		return isset(self::$_config[$group][$item]) ? self::$_config[$group][$item] : FALSE;
	}

// ------------------------------------------------------------------------

	/**
	 * Set
	 *
	 * Set config item
	 *
	 * @param str $group
	 * @param str $key
	 * @param str $value
	 */
	public static function set($group, $key, $value)
	{
		self::$_config[$group][$key] = $value;
	}

}
