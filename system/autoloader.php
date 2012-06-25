<?php defined('SYSPATH') or die('No direct script access.');

// --------------------------------------------------------------
// Set up autoloading for system files, models, and libraries.
// --------------------------------------------------------------
function autoload_system($class_name)
{
    $class = strtolower($class_name);
    if (file_exists($path = SYSPATH.$class.EXT))
        require $path;
}

function autoload_model($class_name)
{
    $class = strtolower($class_name);
    
    // By convention models are suffixed with '_Model', so we remove the
    // suffix here so we don't need it in the file name.
    $class = str_replace('_model', '', $class);
    
    if (file_exists($path = APPPATH.'models'.DS.$class.EXT))
        require $path;
}

function autoload_controller($class_name)
{
    $class = strtolower($class_name);
    
    // By convention models are suffixed with '_Controller', so we remove the
    // suffix here so we don't need it in the file name.
    $class = str_replace('_controller', '', $class);
    
    if (file_exists($path = APPPATH.'controllers'.DS.$class.EXT))
        require $path;
}

function autoload_library($class_name)
{
    $class = strtolower($class_name);
    if (file_exists($path = APPPATH.'libraries'.DS.$class.EXT))
        require $path;
}

spl_autoload_register('autoload_system');
spl_autoload_register('autoload_model');
spl_autoload_register('autoload_controller');
spl_autoload_register('autoload_library');