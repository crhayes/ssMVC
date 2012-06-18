<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Export helper utility. 
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Export {
    
    /**
     * Create a CSV export from a MySQL resource.
     * 
     * Example Usage:
     * 
     *      $users = DB::query("SELECT * FROM users")
     *          ->execute()
     *          ->result();
     *  
     *      Export::csv_from_mysql_resource($users, 'test.csv');
     * 
     * @param   resource    $resource   MySQL Resource
     * @param   string      $file_name  Name used to save file
     */
    function csv_from_mysql_resource($resource, $file_name)
    {
        $output = "";
        $headers_printed = false;
        
        // Loop through each result row
        while ($row = mysql_fetch_array($resource, MYSQL_ASSOC))
        {
            // Print out column names as the first row
            if (!$headers_printed)
            {
                $output .= Export::csv_headers($row);
                $headers_printed = true;
            }

            // Remove newlines from all the fields and surround them with quotes
            foreach ($row as &$value)
            {
                $value = '"'.str_replace("\r\n", "", $value).'"';
            }

            $output .= join(',', $row) . "\n";
        }
        
        Export::send_output($output, $file_name);
    }
    
    /**
     * Create a CSV export from a MySQL result array.
     * 
     * Example Usage:
     * 
     *      $users = DB::query("SELECT * FROM users")
     *          ->execute()
     *          ->fetch_all('array');
     *   
     *      Export::csv_from_mysql_array($users, 'test.csv');
     * 
     * @param   array   $resource   MySQL result array
     * @param   string  $file_name  Name used to save file
     */
    public static function csv_from_mysql_array($resource, $file_name)
    {
        $output .= Export::csv_headers($resource[0]);

        foreach ($resource as $row)
        {
            // remove newlines from all the fields and surround them with quotes
            foreach ($row as &$value)
            {
                $value = '"'.str_replace("\r\n", "", $value).'"';
            }

            $output .= join(',', $row) . "\n";
        }
        
        Export::send_output($output, $file_name);
    }
    
    /**
     * Create the CSV headers from the first resource row.
     * 
     * @param   array   $row
     * @return  string 
     */
    private static function csv_headers($row)
    {        
        return join(',', str_replace('_', ' ', array_keys($row))) . "\n";
    }
    
    /**
     * Send output to the user so they can download the file.
     * 
     * @param   string  $output     Contents of the CSV file sent
     * @param   string  $file_name  Name used to save file
     */
    private static function send_output($output, $file_name)
    {
        // Get the filesize
        $size_in_bytes = strlen($output);
        
        // Set the headers
        header("Content-type: application/csv");
        header("Content-disposition:  attachment; filename=$file_name; size=$size_in_bytes");

        echo $output;
    }

}