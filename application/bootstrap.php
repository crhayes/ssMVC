<?php

// --------------------------------------------------------------
// Load system configuration library.
// --------------------------------------------------------------
require_once SYSPATH.'config'.EXT;
Config::load('default');

// --------------------------------------------------------------
// Load required system files.
// --------------------------------------------------------------
require_once SYSPATH.'router'.EXT;
require_once SYSPATH.'controller'.EXT;
require_once SYSPATH.'model'.EXT;
require_once SYSPATH.'view'.EXT;

// --------------------------------------------------------------
// Instantiate necessary class objects.
// --------------------------------------------------------------
$router =  new Router();

// --------------------------------------------------------------
// Set the default time zone.
// --------------------------------------------------------------
date_default_timezone_set('America/Toronto');

// --------------------------------------------------------------
// Set up autoloading for system files, models, and libraries.
// --------------------------------------------------------------
function autoload_system($class_name)
{
    $class = strtolower($class_name);
    if (file_exists($path = SYSPATH.$class_name.EXT))
        require_once $path;
}

function autoload_model($class_name)
{
    $class = strtolower($class_name);
    if (file_exists($path = APPPATH.'models'.DS.$class_name.EXT))
        require_once $path;
}

function autoload_library($class_name)
{
    $class = strtolower($class_name);
    if (file_exists($path = APPPATH.'libraries'.DS.$class_name.EXT))
        require_once $path;
}

spl_autoload_register('autoload_system');
spl_autoload_register('autoload_model');
spl_autoload_register('autoload_library');

// --------------------------------------------------------------
// And we're set! Let's route the request.
// --------------------------------------------------------------
$router->route();
