<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * File-based Caching Class
 *
 * @author Scott Darby
 */
class Fl_Cache {

	private $_dir;
	private $_file;
	private $_cache_time = 300;

// ------------------------------------------------------------------------

	/**
	 * Contstructor
	 * 
	 * @param string $file 
	 */
	public function __construct($key)
	{
		if(Fl_Config::get('cache', 'dir') == FALSE)
		{
			throw new Exception('Cache config dir not set');
		}
		
		$this->_dir = Fl_Config::get('cache', 'dir');
		
		if(!is_writable($this->_dir))
		{
			throw new Exception('Cache dir not writable');
		}
		
		$last_char = substr($this->_dir, -1);
		
		if($last_char != DIRECTORY_SEPARATOR)
		{
			$this->_dir = $this->_dir.DIRECTORY_SEPARATOR;
		}
		
		$this->_file = sha1($key);
	}

// ------------------------------------------------------------------------
	
	/**
	 * Check
	 * 
	 * Check if cache file exists and is not outdated
	 * 
	 * @param string $content 
	 */
	public function check()
	{
		//check cache file exists and time it was generated
		if(file_exists($this->_dir.$this->_file) AND (time() - $this->_cache_time < filemtime($this->_dir.$this->_file)))
		{
			return TRUE;
		}
		return FALSE;
	}
	
// ------------------------------------------------------------------------
	
	/**
	 * Get
	 */
	public function get()
	{
		//check cache file exists and time it was generated
		if($this->check())
		{
			//serve cache file
			return file_get_contents($this->_dir.$this->_file);
		}
		return FALSE;
	}
	
// ------------------------------------------------------------------------
	
	/**
	 * Write File
	 * 
	 * @param string $content 
	 */
	public function write_file($content)
	{
		ob_start();
		echo $content;
		$file = fopen($this->_dir.$this->_file, 'w');
		fwrite($file, ob_get_contents());
		fclose($file);
		ob_end_clean();
	}

}
