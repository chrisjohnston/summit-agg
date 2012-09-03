<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * View
 *
 * @author Scott Darby
 */
Class Fl_View {

	protected $_vars = array(); //vars passed to view
	protected $_content; //rendered view content
	protected $_view_dir; //directory which contains view files
	protected $_view; //view file to include
	protected $_view_suffix = '.php';

	public function __construct($view)
	{
		//set base view directory
		$this->_view_dir = (Fl_Config::get('mvc', 'view_dir') !== FALSE ) ? Fl_Config::get('mvc', 'view_dir') : '';

		//set up view
		$this->_view = $view;
	}

	// --------------------------------------------------------------------

	/**
	 * Bind vars to view
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function bind($key, $value='')
	{
		if(is_array($key))
		{
			$this->_vars = array_merge($this->_vars, $key);
		}
		else
		{
			$this->_vars[$key] =& $value;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get vars bound to current view
	 */
	public function get_vars()
	{
		return $this->_vars;
	}

	// --------------------------------------------------------------------

	/**
	 * Get current view file
	 */
	public function get_view()
	{
		return $this->_view;
	}

	// --------------------------------------------------------------------

	/**
	 * Render output
	 *
	 * Output the rendered view
	 *
	 * @param str $view
	 * @param arr $data
	 */
	public function render_output()
	{
		$this->_render();
		echo $this->_content;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Output
	 *
	 * Return the rendered output of a view
	 *
	 * @param str $view
	 * @param arr $data
	 */
	public function get_output()
	{
		$this->_render();
		return $this->_content;
	}

	// --------------------------------------------------------------------

	/**
	 * Render
	 *
	 * Render the view
	 */
	private function _render()
	{
		//get variables
		extract($this->_vars);

		ob_start();

		if (!include $this->_view_dir.$this->_view.$this->_view_suffix)
		{
			throw new Exception('Could not load requested view');
		}

		$this->_content = ob_get_contents();
		ob_end_clean();
	}

}
