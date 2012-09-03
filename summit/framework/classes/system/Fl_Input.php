<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Input
 *
 * Process input data
 *
 * @author Scott Darby
 */
class Fl_Input {

	/**
	* Fetch an item from the POST array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	public static function post($index = NULL, $xss_clean = TRUE)
	{
		// Check if a field has been provided
		if ($index === NULL AND !empty($_POST))
		{
			$post = array();

			// Loop through the full _POST array and return it
			foreach (array_keys($_POST) AS $key)
			{
				$post[$key] = self::_fetch_from_array($_POST, $key, $xss_clean);
			}
			return $post;
		}

		return self::_fetch_from_array($_POST, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	* Fetch an item from the GET array
	*
	* @access	public
	* @param	string
	* @param	bool
	* @return	string
	*/
	function get($index = NULL, $xss_clean = TRUE)
	{
		// Check if a field has been provided
		if ($index === NULL AND ! empty($_GET))
		{
			$get = array();

			// loop through the full _GET array
			foreach (array_keys($_GET) as $key)
			{
				$get[$key] = self::_fetch_from_array($_GET, $key, $xss_clean);
			}
			return $get;
		}

		return self::_fetch_from_array($_GET, $index, $xss_clean);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch from array
	 *
	 * This is a helper function to retrieve values from global arrays
	 *
	 * @access	private
	 * @param	array
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	private static function _fetch_from_array(&$array, $index = '', $xss_clean = TRUE)
	{
		if (!isset($array[$index]))
		{
			return FALSE;
		}

		if ($xss_clean === TRUE)
		{
			return Fl_Security::xss_clean($array[$index]);
		}

		return $array[$index];
	}

	// --------------------------------------------------------------------

	/**
	 * Is ajax Request?
	 *
	 * Test to see if a request contains the HTTP_X_REQUESTED_WITH header
	 *
	 * @return 	boolean
	 */
	public static function is_ajax_request()
	{
		return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
	}

	// --------------------------------------------------------------------

}
