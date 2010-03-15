<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

defined('MAGIC') or 
    define('MAGIC', ('development' == APPLICATION_ENV ? 'D:\usr\local\php\extras' : '/usr/share/file/magic'));
            
// utilities
require_once LIBRARY_PATH . '/utils.php';
set_include_paths
(
    // Ensure library/ is on include_path
    LIBRARY_PATH
);


/** Zend_Application */

function dump($smth)
{
    Zend_Debug::dump($smth);
}

require_once 'Zend/Application.php';  
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);


$application->bootstrap()->run();
