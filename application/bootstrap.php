<?php

// --------------------------------------------------------------
// Load default configuration.
// --------------------------------------------------------------
Config::load('application');

// --------------------------------------------------------------
// Set the default time zone.
// --------------------------------------------------------------
date_default_timezone_set(Config::get('application.timezone'));

// --------------------------------------------------------------
// PHP display errors configuration.
// --------------------------------------------------------------
ini_set('display_errors', 'On');
