<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages utility. This class is used for loading multiple message
 * files and accessing message values.
 * 
 * The main functionality of the messages class is to provide messages
 * for validation errors as well as to make it provide multilingual 
 * functionality.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Message {

    /**
     * Store all of the messages files we have loaded.
     * 
     * @var array 
     */
    public static $loaded_files = array();

    /**
     * Load a message file and store it in an array.
     * 
     * @param   string  $file_name  Name of the config file to load.
     */
    public static function load($file_name)
    {
        if (file_exists($path = APPPATH.'messages'.DS.str_replace('.', '/', $file_name).EXT))
        {
            static::$loaded_files = static::$loaded_files + Arr::set_from_string($file_name, require_once($path));
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
        $messages = static::$loaded_files;

        // If there are no parameters we send back the whole messages array.
        if (is_null($keys)) return $config;

        return Arr::get_from_string($keys, $messages, $default);
    }

}