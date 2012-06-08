<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Asset helper class to make it easy to link to assets.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Asset {

    /**
     * Create an HTML link to a stylesheet.
     * 
     * @param   string  $href
     * @return  string 
     */
    public static function stylesheet($href)
    {
        $href = APPPATH.'assets'.DS.'css'.DS.$href;
        
        return "<link rel=\"stylesheet\" type=\"text/css\" href=\"$href\">";
    }
    
    /**
     * Create an HTML link to a javascript.
     * 
     * @param   string  $src
     * @return  string 
     */
    public static function script($src)
    {
        $href = APPPATH.'assets'.DS.'js'.DS.$src;
        
        return "<script type=\"text/javascript\" src=\"$href\"></script>";
    }
    
    /**
     * Create an HTML link to a LESS CSS file.
     * 
     * @param   string  $href
     * @return  string 
     */
    public static function less($href)
    {
        $href = APPPATH.'assets'.DS.'css'.DS.$href;
        
        return "<link rel=\"stylesheet/less\" type=\"text/css\" href=\"$href\">";
    }

}