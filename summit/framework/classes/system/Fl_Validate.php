<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Validation Class
 *
 * Checks passed values against functions to see if the value meets the
 * needed conditions
 *
 * @category	Libraries
 * @author		Arron Coda
 * @link		None
 */
class Fl_Validate {

	private $_variable_data = array();
	private $_validation_passed = FALSE;

	//error message delimiters
	private $_error_prefix = '<div class="form-error-msg">';
	private $_error_suffix = '</div>';

	// --------------------------------------------------------------------

	/**
	 * Set Error Prefix
	 *
	 * @param string $str
	 */
	public function set_error_prefix($str)
	{
		$this->_error_prefix = $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Error Suffix
	 *
	 * @param string $str
	 */
	public function set_error_suffix($str)
	{
		$this->_error_suffix = $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Var
	 *
	 * Add a variable to be checked / filtered
	 *
	 * @access	public
	 * @param	string	name
	 * @param	mixed	value
	 * @param	string	rules
	 * @return	void
	 */
	public function set_var($name, $value, $filters)
	{
		$this->_variable_data[$name] = array(
			'value'=>$value,
			'filters'=>explode('|', $filters),
			'error_msg'=>'',
			'is_error'=>FALSE
		);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get Var
	 *
	 * Get a processed variable
	 *
	 * @access	public
	 * @param	string	name
	 * @return	mixed
	 */
	public function get_var($name)
	{
		// check it exists
		if(!array_key_exists($name, $this->_variable_data))
		{
			return FALSE;
		}

		return array(
			'value'=>$this->_variable_data[$name]['value'],
			'error_msg'=>$this->_variable_data[$name]['error_msg'],
			'is_error'=>$this->_variable_data[$name]['is_error']
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Error
	 *
	 * Manually pass error and message to class.
	 *
	 * @access	public
	 * @param	string	name
	 * @param	string	error_msg
	 * @return	void
	 */
	public function set_error($name, $error_msg)
	{
		if(!array_key_exists($name, $this->_variable_data))
		{
			$msg = 'Trying to set error on unknown variable ('.$name.') in validation class';
			throw new Exception($msg);
		}

		// set field values
		$this->_variable_data[$name]['error_msg'] .= $this->_error_prefix.'<p>'.$error_msg.'</p>'.$this->_error_suffix;
		$this->_variable_data[$name]['is_error'] = TRUE;

		// also validation must fail
		$this->_validation_passed = FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Error
	 *
	 * Get a processed variables error message
	 *
	 * @access	public
	 * @param	string	name
	 * @return	string
	 */
	public function get_error($name)
	{
		if(!array_key_exists($name, $this->_variable_data))
		{
			$msg = 'Trying to get error on unknown variable ('.$name.') in validation class';
			throw new Exception($msg);
		}

		return $this->_variable_data[$name]['error_msg'];
	}

	// --------------------------------------------------------------------

	/**
	 * Is Error
	 *
	 * Does a processed variable have an error
	 *
	 * @access	public
	 * @param	string	name
	 * @return	boolean
	 */
	public function is_error($name)
	{
		if(!array_key_exists($name, $this->_variable_data))
		{
			$msg = 'Trying to check error on unknown variable ('.$name.') in validation class';
			throw new Exception($msg);
		}

		return $this->_variable_data[$name]['is_error'];
	}

	// --------------------------------------------------------------------

	/**
	 * Get Value
	 *
	 * Get a processed variables value
	 *
	 * @access	public
	 * @param	string	name
	 * @return	string
	 */
	public function get_value($name)
	{
		if(!array_key_exists($name, $this->_variable_data))
		{
			$msg = 'Trying to get value on unknown variable ('.$name.') in validation class';
			throw new Exception($msg);
		}

		return $this->_variable_data[$name]['value'];
	}

	// --------------------------------------------------------------------

	/**
	 * Set Value
	 *
	 * Manually pass value to class.
	 *
	 * @access	public
	 * @param	string	name
	 * @param	mixed	value
	 * @return	void
	 */
	public function set_value($name, $value)
	{
		if(!array_key_exists($name, $this->_variable_data))
		{
			$msg = 'Trying to set error on unknown variable ('.$name.') in validation class';
			throw new Exception($msg);
		}

		// set field value
		$this->_variable_data[$name]['value'] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Process
	 *
	 * process set values against filters
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function process()
	{
		// if no filters set we are done
		if(empty($this->_variable_data))
		{
			return TRUE;
		}

		$is_error = 0;
		// loop through set variables
		foreach($this->_variable_data as $name=>&$variable)
		{
			//if field is not required and empty, no validation required
			if(!in_array('required', $variable['filters']) && strlen($variable['value']) === 0)
			{
				$variable['is_error'] = FALSE;
			}
			else
			{
				// loop through set filters for value
				foreach($variable['filters'] as $filter)
				{
					// see if filter has a param and set if there
					$param = NULL;
					if(preg_match("/(.*?)\[(.*?)\]/", $filter, $match))
					{
						$filter = $match[1];
						$param = $match[2];
					}

					// see if the filter is in this class and call it
					if(method_exists($this, $filter))
					{
						$result = (is_null($param)) ? $this->$filter($variable['value']) : $this->$filter($variable['value'], $param);

						if($result === FALSE)
						{
							$is_error++;
							$variable['error_msg'] .= $this->_set_error($filter);
							$variable['is_error'] = TRUE;
						}
					}
					// see if the filter is a function and call it
					else if(!method_exists($this, $filter) AND function_exists($filter))
					{
						// the result could be a boolean if the function was a check.
						// it could also be the returned value if it was just a filter,
						// for example trim, htmlentities etc.
						$result = (is_null($param)) ? $filter($variable['value']) : $filter($variable['value'], $param);

						if($result === FALSE)
						{
							$is_error++;
							$variable['is_error'] = TRUE;
						}
						else
						{
							if($result !== TRUE)
							{
								$variable['value'] = $result;
							}
						}
					}
					// must be an unknown function show error
					else
					{
						$msg = 'Call to undefined function ('.$filter.') in validation class';
						throw new Exception($msg);
					}
				}
			}
		}

		$this->_validation_passed = ($is_error > 0) ? FALSE : TRUE;

		return $this->_validation_passed;
	}

	// --------------------------------------------------------------------

	/**
	 * Passed
	 *
	 * Check to see if a validation routine passed without re-processing
	 * same data.
	 *
	 * @access	public
	 * @return	bool
	 */
	public function passed()
	{
		return $this->_validation_passed;
	}

	// --------------------------------------------------------------------

	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function required($str)
	{
		if(!is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return (!empty($str));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Match one variable to another
	 *
	 * @access	public
	 * @param	string
	 * @param	variable
	 * @return	bool
	 */
	public function matches($str, $var)
	{
		if(!isset($this->_variable_data[$var]))
		{
			return FALSE;
		}

		$field = $this->_variable_data[$var]['value'];

		return ($str !== $field) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function min_length($str, $val)
	{
		if(preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if(function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if(preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if(function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if(preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if(function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_email($str)
	{
		return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Emails
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		if(strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach(explode(',', $str) as $email)
		{
			if(trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return (!preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function alpha_numeric($str)
	{
		return (!preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return (!preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return 	bool
	 */
	public function is_numeric($str)
	{
		return (!is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Is Date e.g. 20/12/2008
	 *
	 * @access	public
	 * @param	string
	 * @return 	bool
	 */
	public function valid_date($str)
	{
		$date_array = explode("/", $str);
		return @checkdate($date_array[1], $date_array[0], $date_array[2]);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid url
	 *
	 * @access	public
	 * @param	string
	 * @return 	bool
	 */
	public function valid_url($str)
	{

		$pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
		if(!preg_match($pattern, $str))
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * MX Lookup
	 *
	 * Check email address contains a valid domain
	 *
	 * @access	public
	 * @param	string
	 * @return 	bool
	 */
	public function mx_lookup($data)
	{
		list($username, $domain) = split('@', $data);
		$mxhosts = array();
		if(!getmxrr($domain, $mxhosts))
		{
			if(!fsockopen($domain, 25, $errno, $errstr, 30))
			{
				return FALSE;
			}
			return TRUE;
		}
		else
		{
			// mx records found
			foreach($mxhosts as $host)
			{
				if(fsockopen($host, 25, $errno, $errstr, 30))
				{
					return TRUE;
				}
			}
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Error
	 *
	 * returns error for methods within this class
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	private function _set_error($method)
	{
		$error_messages = array(
			'required'=>'<p>This field is <strong>required.</strong></p>',
			'matches'=>'<p>Fields don\'t match.</p>',
			'min_length'=>'<p>This field is too short.</p>',
			'max_length'=>'<p>This field is too long.</p>',
			'exact_length'=>'<p>This field must be a set length.</p>',
			'valid_email'=>'<p>This email address isn\'t valid.</p>',
			'valid_emails'=>'<p>The email addresses are not valid.</p>',
			'valid_ip'=>'<p>The IP address isn\'t valid.</p>',
			'alpha'=>'<p>This field can only contain letters.</p>',
			'alpha_numeric'=>'<p>This field can only contain letters and numbers.</p>',
			'alpha_dash'=>'<p>This field can only contain letters, numbers, underscores and dashes.</p>',
			'numeric'=>'<p>This field can only contain numbers.</p>',
			'valid_date'=>'<p>This field is not a valid date.</p>',
			'valid_url'=>'<p>This field must be a valid URL. e.g. http://www.example.com/test.html</p>',
			'mx_lookup'=>'<p>Domain must be a valid URL. e.g. http://www.example.com/test.html</p>'
		);

		if(array_key_exists($method, $error_messages))
		{
			return $this->_error_prefix.$error_messages[$method].$this->_error_suffix;
		}

		return '';
	}

}