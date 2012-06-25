<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Upload helper.
 * 
 * Remember to define your form with "enctype=multipart/form-data" or file
 * uploading will not work!
 * 
 * @package     ssMVC - Super Simple MVC
 * @author      Chris Hayes <chris at chrishayes.ca>
 * @copyright   (c) 2012 Chris Hayes
 */
class Upload {
    /**
     * Upload a file.
     * 
     * @param   array   $file
     * @param   string  $directory  Relative to default upload directory
     * @return  string              On success, relative path to image
     * @return  false               On failure 
     */
    public static function save($file, $directory = NULL)
    {
        // Ignore corrupted uploads
        if (!isset($file['tmp_name']) OR !is_uploaded_file($file['tmp_name']))
        {
            return FALSE;
        }
        
        if ($directory === NULL)
        {
            // Default upload directory
            $directory = Config::get('application.upload.directory');
        }
        
        // Make sure the directory exists and is writable.
        if ( ! is_dir($directory) OR ! is_writable(realpath($directory)))
		{
			return false;
		}

        // Produce a random number to prepend to image name for security reasons.
        $filename = uniqid() . $file['name'];

        // Remove spaces from the filename
        $filename = preg_replace('/\s+/u', '_', $filename);
        

        // Create our target image path with the prepended random number.
        $path = $directory . $filename;

        if (move_uploaded_file($file['tmp_name'], $path))
        {
            return $filename;
        }

        return false;
    }
    
    /**
     * Get a directory path relative to the default upload directory. Returns
     * the default directory if no parameters are specified.
     * 
     * @param   string  $relative
     * @return  string 
     */
    public static function directory($relative = NULL)
    {
        return Config::get('application.upload.directory') . $relative . DS;
    }

}