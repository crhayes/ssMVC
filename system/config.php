<?php

/**
 * Description of config
 *
 * @author Chris
 */
class Config {
    
    /**
     * Store all of the configuration files we have loaded.
     * 
     * @var array 
     */
    public static $loaded_files = array();

    /**
     * Load a configuration file and store it in an array.
     * 
     * @param   stringq$file_name  Name of the config file to load.
     */
    public static function load($file_name)
    {
        if (file_exists($path = APPPATH.'config'.DS.$file_name.EXT))
        {
            static::$loaded_files[$file_name] = require_once($path);
        }
    }

    /**
     * Get a configuration item from an array using "dot" notation.
     * 
     * @param   string  $keys   Path using dot notation.
     * @param   mixed   $default   
     * @return  mixed 
     */
    public static function get($keys, $default = null)
    {
        $config = static::$loaded_files;
        
        // If there are no parameters we send back the whole config array.
        if (is_null($keys)) return $config;
        
        return Arr::get_from_string($keys, $config, $default);
    }

}