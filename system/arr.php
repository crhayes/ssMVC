<?php

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
     * Given a "dot notation" string as an array key get an array value if it 
     * exists, otherwise return a default value.
     * 
     * @param type $keys
     * @param type $array 
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
    
}