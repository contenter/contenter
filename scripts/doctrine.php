#!/usr/bin/php
<?php
/**
 * Doctrine CLI script
 */

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
require_once APPLICATION_PATH . '/configs/environment.php';
define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
 
// utilities
require_once LIBRARY_PATH . '/utils.php';

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));
 
require_once 'Zend/Application.php';
require_once 'FinalView/Application.php';  
// Create application, bootstrap, and run
$application = new FinalView_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
 
$application->getBootstrap()->bootstrap('FinalViewNamespace');
$application->getBootstrap()->bootstrap('Doctrine');
 
$cli = new FinalView_Doctrine_Cli($application->getOption('doctrine'));
$cli->run($_SERVER['argv']);
