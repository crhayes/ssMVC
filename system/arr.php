<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Array helper to make it easier to work with arrays.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Arr {
    
    /**
     * Given an array key get it's value if it exists, otherwise return a 
     * default value.
     * 
     * @param   string  $key    Array key to search for.
     * @param   array   $array  Array to search through.
     * @param   string  $default
     * @return  mixed 
     */
    public static function get($key, $array, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }
    
    /**
     * Given an array key in "dot notation" get an array value if it 
     * exists, otherwise return a default value.
     * 
     * @param   string  $keys   Array key as a dot notated string.
     * @param   array   $array  Array to search through.
     * @return  string
     */
    public static function get_from_string($keys, $array, $default = null)
    {
        foreach (explode('.', $keys) as $key)
        {
            if (!is_array($array) or !array_key_exists($key, $array))
            {
                return $default;
            }

            $array = $array[$key];
        }
        
        return $array;
    }
    
    /**
     * Given an array key in dot notation create and set a value in an array.
     * 
     * @param   string  $keys   Array key as a dot notated string.   
     * @param   $value  mixed   Value to set the array key.
     * @return  array 
     */
    public static function set_from_string($keys, $value)
    {
        $array = $value;
        
        foreach (array_reverse(explode('.', $keys)) as $key)
        {
            $value = $array;
            unset($array);
            
            $array[$key] = $value;
        }
        
        return $array;
    }
    
}