<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base controller class. All controllers must extend this class.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Controller {
    
    public $restful = false;
    
    /**
     * Magic method to catch any controller action calls that do not exist.
     * 
     * @param   string  $method Name of the method called.
     * @param   array   $args   Arguments passed to method. 
     */
    function __call($method, $args)
    {
        return View::make('error/404');
    }
}
