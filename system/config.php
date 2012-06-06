<?php

/**
 * Description of config
 *
 * @author Chris
 */
class Config {

    public static $loaded_files = array();

    /**
     * Load a configuration file and store it in an array.
     * 
     * @param   type    $file_name  Name of the config file to load.
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
        
        // To retrieve the array item using dot syntax, we'll iterate through
        // each segment in the key and look for that value. If it exists, we
        // will return it, otherwise we will set the depth of the array and
        // look for the next segment.
        foreach (explode('.', $keys) as $key)
        {
            if (!is_array($config) or !array_key_exists($key, $config))
            {
                return $default;
            }

            $config = $config[$key];
        }

        return $config;
    }

}