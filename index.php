<?php

// --------------------------------------------------------------
// Create aliases for ease of use.
// --------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);
define('EXT', '.php');

// --------------------------------------------------------------
// Define the base path.
// --------------------------------------------------------------
define('BASEPATH', realpath(dirname(__FILE__)).DS);

// --------------------------------------------------------------
// Define the system and library paths.
// --------------------------------------------------------------
define('SYSPATH', BASEPATH.'system'.DS);
define('LIBPATH', BASEPATH.'libraries'.DS);

// --------------------------------------------------------------
// Load core system files.
// --------------------------------------------------------------
require SYSPATH.'request'.EXT;
require SYSPATH.'router'.EXT;
require SYSPATH.'controller'.EXT;
require SYSPATH.'model'.EXT;
require SYSPATH.'view'.EXT;
require SYSPATH.'config'.EXT;
require SYSPATH.'arr'.EXT;

// --------------------------------------------------------------
// Create a new router instance for the request.
// --------------------------------------------------------------
Request::$route = new Router();

// --------------------------------------------------------------
// Define application path.
// --------------------------------------------------------------
//   We determine whether or not the current request is a
//   preview of the application by whether or not the first
//   parameter is '~preview'. If it is a preview, it means we 
//   have duplicated the application directory, renamed it
//   'preview', made changes to the application, and are now
//   previewing those changes (i.e. to a client).
// --------------------------------------------------------------
$application_path = (Request::is_preview()) ? 'preview' : 'application';
define('APPPATH', BASEPATH.$application_path.DS);

// --------------------------------------------------------------
// Bootstrap the application.
// --------------------------------------------------------------
require APPPATH.'bootstrap'.EXT;

// --------------------------------------------------------------
// Set up autoloader.
// --------------------------------------------------------------
require SYSPATH.'autoloader'.EXT;

// --------------------------------------------------------------
// And we're set! Let's route the request and get the response.
// --------------------------------------------------------------
$response = Request::$route->route();

// --------------------------------------------------------------
// Render the response.
// --------------------------------------------------------------
if ($response instanceOf View)
    $response->render();