<?php
define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_ACTION', 'index');
define('DEFAULT_LAYOUT', 'default');

// Defines constant values for url parts
//define('URL_WITH_CONTROLLER_MIN_LENGTH', 2);
//define('CONTROLLER_NAME_URL_POSITION', 1);
//define('URL_WITH_ACTION_MIN_LENGTH', 3);
//define('ACTION_NAME_URL_POSITION', 2);
//define('URL_PARAMS_POSITION', 3);
define('URL_WITH_CONTROLLER_MIN_LENGTH', 4);
define('CONTROLLER_NAME_URL_POSITION', 3);
define('URL_WITH_ACTION_MIN_LENGTH', 5);
define('ACTION_NAME_URL_POSITION', 4);
define('URL_PARAMS_POSITION', 5);

// Database settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'photo-album');