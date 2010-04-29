<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
// Define application environment
require_once APPLICATION_PATH . '/configs/environment.php';
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
           
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
require_once 'FinalView/Application.php';  
// Create application, bootstrap, and run
$application = new FinalView_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);


$application->bootstrap()->run();
