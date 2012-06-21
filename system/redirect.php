<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Redriect class.
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Redirect {
        
    /**
     * Redirect to another page given either an absolute URL or a route.
     * 
     * @param   string  $destination    Either an absolute URL or a route.
     * @param   string  $status
     * @return  void
     */
    public static function to($destination, $status = 302)
    {
        // Redirect to an absolute URL.
        if (stristr($destination, 'http://') or stristr($destination, 'https://'))
        {
            self::_redirect($destination, $status);
        }
        
        // Redirect internally using a route.
        self::_redirect(URL::to_route($destination, $status));
    }
    
    /**
     * Redirect to the previous URL. 
     * 
     * @return  void
     */
    public static function back()
    {
        if (isset($_SERVER['HTTP_REFERER']))
        {
            self::_redirect($_SERVER['HTTP_REFERER'], '302');
        }
    }
    
    /**
     * Perform the actual redirect.
     * 
     * @param   string  $url
     * @param   string  $status
     * @return  void
     */
    private static function _redirect($url, $status)
    {
        header("Location: $url", true, $status);
        exit();
    }
    
}

?>
