<?php

/**
 * Description of database
 *
 * @author Chris
 */
class Database {

    /**
     * Store singleton instance of database class.
     * 
     * @var Database 
     */
    private static $instance;

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

    /**
     * Get the database configurtation details and connect
     * and select the database.
     * 
     * @return void 
     */
    function __construct()
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
    }

    /**
     * Returns error messages for a given form field.
     *
     * @return 	Database    Instance of self.
     */
    public static function instance($table = null)
    {
        if (!self::$instance)
        {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Connect to a database.
     *
     * @param 	host	string	Hostname of database.
     * @param 	user	string	Username of database.
     * @param 	pass	string	Password of database.
     */
    private static function connect_db($host, $user, $pass)
    {
        mysql_connect($host, $user, $pass) or die('bad connection');
    }

    /**
     * Select a database.
     *
     * @param 	db	string	Database to connect to.
     */
    private static function select_db($db)
    {
        mysql_select_db($db);
    }

    /**
     * Query the selected database.
     *
     * @param 	query	string	MySQL query string.
     * @return	this
     */
    public function query($query)
    {
        $this->result = mysql_query($query);

        $this->num_rows = mysql_num_rows($this->result);
        return $this;
    }

    /**
     * Fetch a SQL query result row.
     *
     * @param 	return  string	Whether to return an object or array.
     * @return	object	Return object by default, or array if specified.
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
     * @return type 
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
     * @return 	int	Number of result rows.
     */
    public function num_rows()
    {
        return $this->num_rows;
    }

}
