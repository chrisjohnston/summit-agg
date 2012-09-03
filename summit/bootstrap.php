<?php
/**
 * Framework Lite
 * 
 * A tiny pluggable MVC framework
 * 
 * @author Scott Darby 2011
 */

// ------------------------------------------------------------------------

define('BASEPATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// ------------------------------------------------------------------------

/**
 * Auto load classes
 */
function fl_autoload($class_name)
{
	$possibilities = array(
		BASEPATH.'framework/classes/'.$class_name.'.php',
		BASEPATH.'framework/classes/system/'.$class_name.'.php',
		BASEPATH.'framework/classes/helper/'.$class_name.'.php',
		BASEPATH.'framework/classes/model/'.$class_name.'.php',
		BASEPATH.'framework/classes/third_party/'.$class_name.'.php',
	);
	foreach($possibilities as $file)
	{
		if(file_exists($file))
		{
			require_once $file;
			return TRUE;
		}
	}
	return FALSE;
}
 
spl_autoload_register('fl_autoload');
 
// ------------------------------------------------------------------------
/**
 * Error reporting
 */
define('SHOW_ERRORS', TRUE);
 
// ------------------------------------------------------------------------
 
/**
 * Development Team IP
 */
define('DEV_IP', '192.168.1.41');
 
// ------------------------------------------------------------------------
 
/**
 * Session
 */
session_start();
 
// ------------------------------------------------------------------------
 
/**
 * Error reporting
 */
if(SHOW_ERRORS === TRUE AND $_SERVER['REMOTE_ADDR'] == DEV_IP)
{
	ini_set('display_errors', 1);
	error_reporting(E_ALL | E_STRICT);
}
 
function fl_exception_handler($e)
{
	$stack = $e->getTrace();
 
	echo '<div style="border: 2px solid red; font-size: 14px; font-family: Arial, Helvetica; color: red; padding: 20px; margin: 8px; background-color: #FFE2E2">';
	echo '<h2 style="font-weight: bold; margin: 0 0 10px;">Error</h2>';
	echo '<strong>File:</strong> '.$e->getFile();
	echo '<br />';
	echo '<br />';
	echo '<strong>Line:</strong> '.$e->getLine();
	echo '<br />';
	echo '<br />';
	echo '<strong>Message:</strong> '.$e->getMessage();
	echo '<br />';
	echo '<br />';
	echo '<strong>Stack Trace:</strong>';
	echo '<ol>';
	foreach($stack AS $trace)
	{
		if(isset($trace['file']))
		{
			echo '<li><strong>File:</strong> '.$trace['file'];
		}
		echo '<ul>';
		if(isset($trace['line']))
		{
			echo '<li><strong>Line:</strong> '.$trace['line'].'</li>';
		}
		if(isset($trace['function']))
		{
			echo '<li><strong>Function:</strong> '.$trace['function'].'</li>';
		}
		if(isset($trace['class']))
		{
			echo '<li><strong>Class:</strong> '.$trace['class'].'</li>';
		}
		if(isset($trace['type']))
		{
			echo '<li><strong>Type:</strong> '.$trace['type'].'</li>';
		}
		echo '<li><strong>Args:</strong> ';
		echo '<ol>';
		if(isset($trace['args']))
		{
			foreach($trace['args'] AS $arg)
			{
				echo '<li>';
				var_dump($arg);
				echo '</li>';
			}
		}
		echo '</ol>';
		echo '<br />';
		echo '</li>';
		echo '</ul>';
		echo '</li>';
	}
	echo '</ol>';
	echo '</div>';
}
 
set_exception_handler('fl_exception_handler');
 
// -------------------------------------------------------------------------
/**
 * Set the default time zone.
 */
date_default_timezone_set('Europe/London');
 
/**
 * Set the default locale.
 */
setlocale(LC_ALL, 'en_US.utf-8');
 
// -------------------------------------------------------------------------

