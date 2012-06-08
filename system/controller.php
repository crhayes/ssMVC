<?php

/**
 * This is the base controller class. All application controllers will
 * extend this class.
 *
 * @author Chris Hayes
 */
class Controller {
    
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
