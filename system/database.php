<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database utility to make it easier to connect to and query a database.
 * This class also handles escaping data.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Database {

    private $query;
    
    /**
     * Store the result we get from the database.
     * 
     * @var array 
     */
    private $result;

    /**
     * Store the number of rows returned.
     * 
     * @var int 
     */
    private $num_rows;
    
    private $bound_values = array();

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
     * 
     *      Database::query("SELECT * FROM user WHERE id = :id")
     *          ->bind(':id', 5)
     *          ->execute()
     *          ->fetch_all();
     * 
     * @param   string  $field
     * @param   string  $value
     * @return \Database 
     */
    public function bind($field, $value)
    {
        $this->bound_values[$field] = $value;
        
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
        $bound_values = $this->bound_values;
        
        // Loop through the bound field and replace with values.
        foreach ($bound_values as $field => $value)
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
        $this->result = mysql_query(mysql_real_escape_string($query));
        $this->num_rows = mysql_num_rows($this->result);
        
        return $this;
    }

    /**
     * Fetch a SQL query result row.
     *
     * @param 	string  $return     Whether to return an object or array.
     * @return	object              Return object by default, or array if specified.
     */
    public function fetch($return = 'object')
    {
        $result = $this->result;

        if ($return == 'object')
            return mysql_fetch_object($result);
        else
            return mysql_fetch_assoc($result);
    }
    
    /**
     * Return SQL query result as an array of objects.
     * 
     * @param   string  $return     Whether to return an object or array.
     * @return  mixed 
     */
    public function fetch_all($return = 'object')
    {
        $return = array();
        
        while ($row = $this->fetch($return))
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
