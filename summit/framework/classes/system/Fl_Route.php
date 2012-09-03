<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * HTTP Routing
 *
 * Simple interface for getting/setting regex application routes
 * 
 * @author Scott Darby
 */
class Fl_Route {

	private static $_routes = array();

	// ------------------------------------------------------------------------

	/**
	 * Get All
	 *
	 * @param str $item
	 * @return mixed
	 */
	public static function get_all()
	{
		return self::$_routes;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set
	 *
	 * @param str $key
	 * @param str $value
	 */
	public static function set($regex, $array)
	{
		self::$_routes[$regex] = $array;
	}

}
