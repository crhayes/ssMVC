<?php defined('SYSPATH') or die('No direct script access.');
/**
 * URL helper class to make it easy to create URLs.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Url {
    
    /**
     * Determine the base URL of the application.
     * 
     * @return   string 
     */
    public static function base()
    {
        // Determine whether to use HTTP or HTTPS
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https://" : "http://";
        
        // Get the host and remove any trailing slash.
        $host = rtrim($_SERVER['HTTP_HOST'], '/').DS;
        
        // Get the application basepath.
        $base = Config::get('application.base_path');
        $base = ($base == '') ? '' : $base.DS;
        
        return $protocol.$host.$base;
    }
    
    /**
     * Create a URL to a route.
     * 
     * @param   string  $route
     * @return  string 
     */
    public function to_route($route)
    {
        return self::base().$route;
    }

}