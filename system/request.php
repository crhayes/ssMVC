<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Request class.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Request {
    
    /**
     * Instance of the Router class.
     * 
     * @var Router 
     */
    public static $route;
    
    /**
     * Root directory of the application.
     * 
     * @var string 
     */
    public static $root;
    
    /**
     * Determine whether or not the current request is a preview of 
     * the application.
     * 
     * @return  boolean 
     */
    public static function is_preview()
    {
        if (isset(self::$route->route[0]) && self::$route->route[0] == '~preview')
        {
            array_shift(self::$route->route);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the referring page.
     * 
     * @return  mixed 
     */
    public static function referrer()
    {
        if (isset($_SERVER['HTTP_REFERER']))
        {
            return $_SERVER['HTTP_REFERER'];
        }
        
        return false;
    }
}