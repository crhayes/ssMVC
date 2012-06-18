<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database utility to make it easier to connect to and query a database.
 * This class also handles escaping data.
 * 
 * Example Usages:
 * ===============
 * 
 *  Query the Database:
 *  -------------------
 *    $users = DB::query("SELECT * FROM users")
 *      ->execute();
 * 
 *  Fetch the first result row:
 *  ---------------------------
 *    $row = $users->fetch();
 * 
 *  Fetch a specific result row:
 *  ----------------------------
 *    $row = $users->fetch_row(3);
 * 
 *  Fetch all result rows:
 *  ----------------------
 *    $rows = $users->fetch_all();
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Database {

    /**
     * Store the query.
     * 
     * @var string 
     */
    private $query;
    
    /**
     * Store any bound fields used in the query.
     * 
     * @var array 
     */
    private $bound_fields = array();
    
    /**
     * Store the result we get from the database.
     * 
     * @var array 
     */
    public $result;

    /**
     * Store the number of rows returned.
     * 
     * @var int 
     */
    private $num_rows;

    /**
     * Get the database configurtation details and connect
     * and select the database, and store the query.
     * 
     * @return void 
     */
    function __construct($query)
    {
        Config::load('database');
        
        // Get Database config information
        $host = Config::get('database.host');
        $user = Config::get('database.user');
        $pass = Config::get('database.password');
        $db = Config::get('database.name');

        // Connect and select the database
        self::connect_db($host, $user, $pass);
        self::select_db($db);
        
        // Store the query.
        $this->query = $query;
    }

    /**
     * Connect to a database.
     *
     * @param   string  $host   Hostname of database.
     * @param   string  $user	Username of database.
     * @param 	string  $pass	Password of database.
     */
    private static function connect_db($host, $user, $pass)
    {
        mysql_connect($host, $user, $pass) or die('bad connection');
    }

    /**
     * Select a database.
     *
     * @param   string  $db     Database to connect to.
     */
    private static function select_db($db)
    {
        mysql_select_db($db);
    }

    /**
     * Create a new database object and pass the query to the constructor
     * to be stored.
     *
     * @param   string  $query	MySQL query string.
     * @return	\Database
     */
    public static function query($query)
    {        
        return new static($query);
    }

    /**
     * Bind a field with a value. This makes it much cleaner to write queries.
     * 
     * Example usage:
     * ==============
     * 
     *  Database::query("SELECT * FROM user WHERE id = :id")
     *    ->bind(':id', 5)
     *    ->execute()
     *    ->fetch_all();
     * 
     * @param   string  $field
     * @param   string  $value
     * @return \Database 
     */
    public function bind($field, $value)
    {
        $this->bound_fields[$field] = mysql_real_escape_string($value);
        
        return $this;
    }
    
    /**
     * Replace bound fields in the query string with their values.
     * 
     * @param   string  $query
     * @return  string 
     */
    private function replace_bound_fields($query)
    {
        // Import the bound fields locally.
        $bound_fields = $this->bound_fields;
        
        // Loop through the bound field and replace with values.
        foreach ($bound_fields as $field => $value)
        {
            $query = str_replace($field, "'$value'", $query);
        }
        
        return $query;
    }
    
    /**
     * Execute the query.
     * 
     * @return \Database 
     */
    public function execute()
    {
        // Replace bound values in the query.
        $query = $this->replace_bound_fields($this->query);
        
        // Run the query, store the result, and store the number of rows.
        $this->result = mysql_query($query);
        $this->num_rows = mysql_num_rows($this->result);
        
        return $this;
    }
    
    /**
     * Return a MySQL result resource.
     * 
     * @return MySQL Resource 
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * Fetch a SQL query result row.
     *
     * @param 	string  $type   Whether to return an object or array.
     * @return	mixed           Return object by default, or array if specified.
     */
    public function fetch($type = 'object')
    {
        $result = $this->result;

        if ($type == 'object')
            return mysql_fetch_object($result);
        else
            return mysql_fetch_assoc($result);
    }
    
    /**
     * Fetch a specific row identified by a row number.
     * 
     * @param   int     $row    Row number
     * @param   string  $type   Whether to return an object or array.
     * @return  mixed 
     */
    public function fetch_row($row, $type = 'object')
    {
        mysql_data_seek($this->result, $row-1);
        
        return $this->fetch($type);
    }
    
    /**
     * Return SQL query result as an array of objects.
     * 
     * @param   string  $type   Whether to return an object or array.
     * @return  mixed 
     */
    public function fetch_all($type = 'object')
    {
        $return = array();
        
        // Return the pointer back to the first row
        mysql_data_seek($this->result, 0);
        
        while ($row = $this->fetch($type))
        {
            array_push($return, $row);
        }
        
        return $return;
    }

    /**
     * Get number of resulting rows for the last query.
     *
     * @return 	int     Number of result rows.
     */
    public function num_rows()
    {
        return $this->num_rows;
    }

}
