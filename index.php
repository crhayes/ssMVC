<?php

// --------------------------------------------------------------
// Application directory.
// --------------------------------------------------------------
$paths['application'] = 'application';

// --------------------------------------------------------------
// System directory.
// --------------------------------------------------------------
$paths['system'] = 'system';

// --------------------------------------------------------------
// Views directory.
// --------------------------------------------------------------
$paths['libraries'] = 'libraries';

// --------------------------------------------------------------
// Create an alias for the directory separator for ease of use.
// --------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------
// Define file extension to be used for all files.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Define the base application path.
// --------------------------------------------------------------
define('BASEPATH', realpath(dirname(__FILE__)).DS);

// --------------------------------------------------------------
// Define the absolute paths for configured directories.
// --------------------------------------------------------------
define('SYSPATH', BASEPATH.$paths['system'].DS);
define('APPPATH', BASEPATH.$paths['application'].DS);
define('LIBPATH', BASEPATH.$paths['libraries'].DS);

// --------------------------------------------------------------
// Let's get started!
// --------------------------------------------------------------
require_once APPPATH.'bootstrap'.EXT;