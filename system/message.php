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
     * @param   string  $language
     */
    public static function load($file_name, $language = 'en')
    {
        // Load the application message file if it exists.
        if (file_exists($path = APPPATH.'messages'.DS.$language.DS.str_replace('.', '/', $file_name).EXT))
        {
            self::$loaded_files = self::$loaded_files + Arr::set_from_string($file_name, require_once($path));
        }
        // Otherwise default to the system message file if it exists.
        else if (file_exists($path = SYSPATH.'messages'.DS.str_replace('.', '/', $file_name).EXT))
        {
            self::$loaded_files = self::$loaded_files + Arr::set_from_string($file_name, require_once($path));
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
        $messages = self::$loaded_files;

        // If there are no parameters we send back the whole messages array.
        if (is_null($keys)) return $config;

        return Arr::get_from_string($keys, $messages, $default);
    }

}