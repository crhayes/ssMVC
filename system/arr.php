<?php

class Arr {
    
    /**
     * If an array key exists return it's value, otherwise return a 
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
    
}