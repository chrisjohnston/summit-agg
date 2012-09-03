<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Database access
 *
 * MySQL database access
 *
 * @author Scott Darby
 */
class Fl_DB {

	//db connection settings
	private static $_host;
	private static $_database;
	private static $_user;
	private static $_pass;
	private static $_instance = FALSE;
	private static $_mysqli = FALSE;
	public static  $row_count, $last_error, $last_query;

	// --------------------------------------------------------------------

	/**
	 * Create new mysqli connection
	 *
	 * @return void
	 */
	private function __construct()
	{
		self::$_host     = Fl_Config::get('db', 'host');
		self::$_database = Fl_Config::get('db', 'database');
		self::$_user     = Fl_Config::get('db', 'user');
		self::$_pass     = Fl_Config::get('db', 'pass');

		self::$_mysqli = new mysqli(self::$_host, self::$_user, self::$_pass, self::$_database);
	}

	// --------------------------------------------------------------------

	/**
	 * Close db connection
	 *
	 * @return void
	 */
	public function __destruct()
	{
		self::$_mysqli->close();
	}

	// --------------------------------------------------------------------

	/**
	 * Connect
	 *
	 * Creates new instance of Fl_DB class if one is not already present
	 *
	 * @return void
	 */
	private static function connect()
	{
		if(self::$_instance === FALSE)
		{
			self::$_instance = new Fl_DB();
		}

		//check for an error establishing a connection
		if(!self::$_mysqli)
		{
			throw new Exception(self::$_mysqli->connect_error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Escape Data
	 *
	 * Escape data before using in mysql statement
	 *
	 * @param string $data
	 * @return string
	 */
	public static function escape_data($data)
	{
		self::connect();

		//Address Magic Quotes.
		if(ini_get('magic_quotes_gpc'))
		{
			$data = stripslashes($data);
		}

		$data = self::$_mysqli->real_escape_string(trim($data));

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Select
	 *
	 * @param resource $sql
	 * @return resource
	 */
	public static function select($sql)
	{
		self::connect();

		self::$last_query = $sql;

		$result = self::$_mysqli->query($sql);

		if(!$result)
		{
			throw new Exception(self::$_mysqli->error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		self::$row_count = $result->num_rows;

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Row
	 *
	 * @param mysql resouce $result
	 * @param string $type
	 * @return array
	 */
	public static function get_row($result, $type = MYSQLI_BOTH)
	{
		self::connect();

		if(!is_object($result))
		{
			throw new Exception('Invalid resource identifier passed to get_row() function.<br /><br /><strong>Last Query:</strong> '.self::$last_query);
			self::$last_error = "";
			return FALSE;
		}
		
		return $result->fetch_array($type);

	}

	// --------------------------------------------------------------------

	/**
	 * Get Result Array
	 *
	 * Get database rows as an array
	 *
	 * @param mysql resouce $result
	 * @param string $type
	 * @return array
	 */
	public static function get_result_array($result)
	{
		self::connect();

		if(!$result)
		{
			throw new Exception('Invalid resource identifier passed to get_row() function.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		$result_array = array();

		while($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$result_array[] = $row;
		}

		if(empty($result_array))
		{
			self::$last_error = 'Could not retrieve any rows';
		}

		return $result_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Select One
	 *
	 * Get one column
	 *
	 * @param string $sql
	 * @param const $type
	 * @return string
	 */
	public static function select_one($sql, $col)
	{
		self::connect();

		$sql .= " LIMIT 1";

		self::$last_query = $sql;

		$result = self::$_mysqli->query($sql);

		if(!$result)
		{
			throw new Exception(self::$_mysqli->error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		while($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$return = $row[$col];
		}

		$result->free();

		if(isset($return))
		{
			return $return;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Select One Row
	 *
	 * @param string $sql
	 * @param const $type
	 * @return string
	 */
	public static function select_one_row($sql, $type = MYSQLI_ASSOC)
	{
		self::connect();

		$sql .= " LIMIT 1";

		self::$last_query = $sql;

		$result = self::$_mysqli->query($sql);

		if(!$result)
		{
			throw new Exception(self::$_mysqli->error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		$return = FALSE;
		while($row = $result->fetch_array($type))
		{
			$return = $row;
		}

		$result->free();

		if(isset($return))
		{
			return $return;
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update
	 * 
	 * @param string $sql
	 * @return mixed
	 */
	public static function update($sql)
	{
		self::connect();

		self::$last_query = $sql;

		$result = self::$_mysqli->query($sql);
		
		if(!$result)
		{
			throw new Exception(self::$_mysqli->error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Insert SQL
	 *
	 * @param string $sql
	 * @return mixed
	 */
	public static function exec_sql($sql)
	{
		self::connect();

		self::$last_query = $sql;

		$result = self::$_mysqli->query($sql);

		if(!$result)
		{
			throw new Exception(self::$_mysqli->error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		return self::$_mysqli->insert_id;

		self::close();
	}

	// --------------------------------------------------------------------

	/**
	 * Update Array
	 *
	 * @param string $table
	 * @param string $data
	 * @param string $id_col_name
	 * @param int $row_id
	 * @return mixed
	 */
	public static function update_array($table, $data, $id_col_name, $row_id)
	{
		self::connect();

		if(empty($table))
		{
			throw new Exception('You must specify a table name.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}
		
		if(empty($data))
		{
			throw new Exception('You must pass an array to the update_array() function.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		if(empty($id_col_name))
		{
			throw new Exception('You must specify the id column you wish to update.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		if(empty($row_id))
		{
			throw new Exception('You must pass an id of the row you need to update to the update_array() function.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		$values = '';

		foreach($data as $key=>$value)
		{
			$key   = self::escape_data($key);
			$value = self::escape_data($value);

			$values .= "$key=";

			if(is_null($value))
			{
				$values .= "NULL,";
			}
			else
			{
				$values .= "'$value',";
			}
		}

		//remove trailing comma
		$values = substr($values, 0, -1);
		
		// insert values
		$sql = "UPDATE ".self::escape_data($table)." SET $values WHERE ".self::escape_data($id_col_name)." = ".self::escape_data($row_id);
		return self::exec_sql($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Insert Array
	 *
	 * @param string $table
	 * @param array $data
	 * @return mixed
	 */
	public static function insert_array($table, $data)
	{
		self::connect();

		if(empty($table) OR !is_string($table))
		{
			throw new Exception('You must specify a table name.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		if(empty($data) OR !is_array($data))
		{
			throw new Exception('You must pass an array to the insert_array() function.'.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		$cols = '(';
		$values = '(';

		foreach($data as $key=>$value)
		{
			$key   = self::escape_data($key);
			$value = self::escape_data($value);

			$cols .= "$key,";

			$col_type = 'string';

			if(is_null($value))
			{
				$values .= "NULL,";
			}
			else
			{
				$values .= "'$value',";
			}
		}

		$cols   = substr($cols, 0, -1);
		$values = substr($values, 0, -1);
		
		$cols .= ")";
		$values .= ")";

		// insert values
		$sql = "INSERT INTO ".self::escape_data($table)." ".self::escape_data($cols)." VALUES $values";
		return self::exec_sql($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Row
	 *
	 * @param string $table
	 * @param string $col
	 * @param string $value
	 * @return mixed
	 */
	public static function delete_row($table, $col, $value)
	{
		self::connect();

		$sql = "DELETE FROM ".self::escape_data($table)." WHERE ".self::escape_data($col)." = ".self::escape_data($value);

		self::$last_query = $sql;

		$result = self::$_mysqli->query($sql);
		if(!$result)
		{
			throw new Exception(self::$_mysqli->error.'<br /><br /><strong>Last Query:</strong> '.self::$last_query.'<br /><br /><strong>Last Query:</strong> '.self::$last_query);
		}

		if(self::$_mysqli->affected_rows == 1)
		{
			return TRUE;
		}

		self::close();
	}

	// --------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Close the database connection.
	 */
	private static function close()
	{
		self::$_mysqli->close(self::$_mysqli);
	}


}