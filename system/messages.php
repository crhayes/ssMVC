<?php

/**
 * Message Library.
 *
 * @author Chris Hayes
 */
class Message {

    /**
     * Store all of the messages files we have loaded.
     * @var type 
     */
    public static $loaded_files = array();

    /**
     * Load a message file and store it in an array.
     * 
     * @param   string  $file_name  Name of the config file to load.
     */
    public static function load($file_name)
    {
        if (file_exists($path = APPPATH.'messages'.DS.$file_name.EXT))
        {
            static::$loaded_files[$file_name] = require_once($path);
        }
    }

    /**
     * Get a message item from an array using "dot" notation.
     * 
     * @param   string  $keys   Path using dot notation.
     * @param   mixed   $default   
     * @return  mixed 
     */
    public function get($keys, $default = null)
    {
        $message = static::$loaded_files;

        // If there are no parameters we send back the whole config array.
        if (is_null($keys))
            return $message;

        return Arr::get_from_string($keys, $message, $default);
    }

}