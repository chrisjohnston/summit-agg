<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Controller
 *
 * Inspired by code from http://lightvc.org/
 *
 * @author Scott Darby
 */
class Fl_Controller {

	protected $_default_controller;
	protected $_default_action;
	protected $_controller_name;
	protected $_action_name;
	protected $_action_params;
	protected $_additional_params;
	protected $_controller;
	protected $_routes;
	protected $_url;
	protected $_uri_segments;
	protected $_controller_suffix = '.php';
	protected $_is_default_controller = FALSE; //bool if controller is default

	public function __construct($routes=NULL)
	{
		//set default controller/action
		$this->_default_controller = Fl_Config::get('mvc', 'default_controller');
		$this->_default_action = Fl_Config::get('mvc', 'default_action');

		//get url
		$this->_url = isset($_GET['url']) ? $_GET['url'] : NULL;
		$this->_uri_segments = explode('/', $this->_url);

		//set up routes
		$this->_routes = $routes;
		$this->setup_routes();

		//run default controller/action if no controller passed
		if($this->_controller_name === NULL)
		{
			//url passed, assume first segment is method
			if(isset($this->_url))
			{
				$this->_controller_name = (isset($this->_uri_segments[0]) AND $this->_uri_segments[0] != '') ? $this->_uri_segments[0] : $this->_default_controller;
				$this->_action_name = (isset($this->_uri_segments[1]) AND $this->_uri_segments[1] != '') ? $this->_uri_segments[1] : $this->_default_action;
			}
			else
			{ //no url, assume default controller/method
				$this->_controller_name = $this->_default_controller;
				$this->_action_name = $this->_default_action;
			}
		}

		$this->_controller = $this->_get_controller();
		$this->_run_action();
	}

	// ------------------------------------------------------------------------

	/**
	 * Set Up Routes
	 *
	 * Define if controller action and params match any routes set in bootstrap
	 *
	 */
	protected function setup_routes()
	{
		$matches = array();
		foreach($this->_routes AS $regex => $value)
		{
			if(preg_match($regex, $this->_url, $matches))
			{
				// Get controller name if available
				if(isset($value['controller']))
				{
					if(is_int($value['controller']))
					{
						// Get the controller name from the regex matches
						$this->_controller_name = @$matches[$value['controller']];
					}
					else
					{
						// Use the constant value
						$this->_controller_name = $value['controller'];
					}
				}

				// Get action name if available
				if(isset($value['action']))
				{
					if(is_int($value['action']))
					{
						// Get the action from the regex matches
						$this->_action_name = @$matches[$value['action']];
					}
					else
					{
						// Use the constant value
						$this->_action_name = $value['action'];
					}
				}

				// Get action name if available
				if(isset($value['action_params']))
				{
					if(is_int($value['action_params']))
					{
						// Get the action from the regex matches
						$this->_action_params = @$matches[$value['action_params']];
					}
					else
					{
						// Use the constant value
						$this->_action_params = $value['action_params'];
					}
				}
				
				if(isset($value['additional_params']))
				{
					if(is_int($value['additional_params']))
					{
						// Get the action from the regex matches
						$this->_additional_params = @$matches[$value['additional_params']];
					}
					else
					{
						// Use the constant value
						$this->_additional_params = $value['additional_params'];
					}
				}
				return TRUE;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Controller
	 *
	 * Attempt to load controller
	 */
	private function _get_controller()
	{
		//capitalize first letter
		$this->_orig_controller_name = $this->_controller_name;
		$this->_controller_name = ucfirst($this->_controller_name);

		$file = Fl_Config::get('mvc', 'controller_dir').$this->_controller_name.$this->_controller_suffix;

		if(file_exists($file))
		{
			require_once($file);
			return new $this->_controller_name();
		}
		else //could not find controller, set to default
		{
			$file = Fl_Config::get('mvc', 'controller_dir').$this->_default_controller.$this->_controller_suffix;
			
			if(file_exists($file))
			{
				//remap uri string parts as using default controller
				$this->_controller_name = $this->_default_controller;
				
				$this->_action_name = ($this->_uri_segments[0] != '') ? $this->_uri_segments[0] : NULL;
				$this->_action_params = ($this->_uri_segments[1] != '') ? $this->_uri_segments[1] : NULL;
				$this->_additional_params = ($this->_uri_segments[2] != '') ? $this->_uri_segments[2] : NULL;

				require_once($file);
				return new $this->_default_controller();
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get action function name
	 */
	private function _get_action_function_name($action_name)
	{
		return 'action_'.strtolower($action_name);
	}

	// ------------------------------------------------------------------------

	/**
	 * Run Action
	 *
	 * Run controller action
	 */
	private function _run_action()
	{
		$function = $this->_get_action_function_name($this->_action_name);
		$controller = & $this->_controller;
		
		if(method_exists($controller, $function))
		{
			if($this->_action_params != '')
			{
				$controller->$function($this->_action_params, $this->_additional_params);
			}
			else
			{
				$controller->$function();
			}
		}
		else
		{
			if($this->_controller_name == $this->_default_controller)
			{
				$params_index = 0;
				$addition_params_index = 1;
			}
			else
			{
				$params_index = 1;
				$addition_params_index = 2;
			}
		
			//try and load default controller with method as params
			try
			{
				//remap uri
				$this->_action_name = $this->_default_action;
				$this->_action_params = $this->_uri_segments[$params_index];
				$this->_additional_params = $this->_uri_segments[$addition_params_index];
				
				$function = $this->_get_action_function_name($this->_action_name);
				
				$controller->$function($this->_action_params, $this->_additional_params);
			}
			catch(Exception $e)
			{
				throw new Exception($e->getmessage());
			}
		}
	}

	// ------------------------------------------------------------------------
}

/**
 * Controller which all controllers must extend
 */
class Page_controller {

	public function __construct()
	{
		
	}

}