#!/usr/bin/php
<?php
/**
 * ConvertConfigsToYaml
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

require_once LIBRARY_PATH . '/utils.php';

iterate_resursive(APPLICATION_PATH . '/routes/', 'convertXmlToYaml');

//convertIniToYaml(APPLICATION_PATH . '/configs/application.ini');
convertIniToYaml(APPLICATION_PATH . '/configs/application.local.ini');

function convertXmlToYaml($file)
{
    if (pathinfo($file, PATHINFO_EXTENSION) != 'xml') return;
    
    $config = new Zend_Config_Xml($file);
    
    $writer = new Zend_Config_Writer_Yaml();
    
    $filename = basename($file, ".xml");
    $writer->write(APPLICATION_PATH . '/routes/' . $filename . '.yaml', $config);
}

function convertIniToYaml($file)
{
    if (pathinfo($file, PATHINFO_EXTENSION) != 'ini') return;

    $config = new Zend_Config_Ini($file);

    $writer = new Zend_Config_Writer_Yaml();

    $filename = basename($file, ".ini");
    $writer->write(APPLICATION_PATH . '/configs/' . $filename . '.yaml', $config);
}
