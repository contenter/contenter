<?php
require_once LIBRARY_PATH . '/FinalView/Bootstrap.php';

class Bootstrap extends FinalView_Bootstrap
{
    
    /**
    * Init plugin to use https for secure pages
    * 
    */
    protected function _initSecurePlugin() 
    {
        $this->bootstrap('AplicationAutoloader');
        
        $this->bootstrap('FrontController');
        
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_SecureRequest, 3);
    }
    
    protected function _initAccessRules()
    {
        $this->bootstrap('Doctrine');
        
        if (file_exists($filename = APPLICATION_PATH . '/configs/rules.yml')) {
            $rulesSchema  = Doctrine_Parser::load(APPLICATION_PATH . '/configs/rules.yml', 'yml');
            FinalView_Access_Rules::setSchema($rulesSchema);        	
        }
        
        $accessRulesConfig = $this->getOption('rules');
        if (!is_null($accessRulesConfig)) {
            if (array_key_exists('default_behavior', $accessRulesConfig)) {
                $accessRulesConfig['default_behavior'] = (bool)$accessRulesConfig['default_behavior'];
            }
            FinalView_Access_Rules::$options = $accessRulesConfig;	
        }
        
        $loader = $this->getResource('AplicationAutoloader');
        $loader->addResourceType('rules', '/rules', 'Rules'); 
    }
    
    protected function _initResources()
    {       
        $this->bootstrap('Doctrine');
        
        $resources = Doctrine_Parser::load(APPLICATION_PATH . '/configs/resources.yml', 'yml');
        
        FinalView_Application_Resources::setResources($resources);

        $this->bootstrap('FrontController');
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Access);       
    }    
    
    /**
     * Initializes ZFDebug console if application environment isn't production
     */
    protected function _initZFDebug()
    {
        if ($this->getOption('zf_debug')) {
            // Setup autoloader with namespace
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');

            // Ensure the front controller is initialized
            $this->bootstrap('FrontController');

            // Retrieve the front controller from the bootstrap registry
            $front = $this->getResource('FrontController');

            $options = array(
                'plugins' => array(
                    'Variables',
                    'FinalView_Controller_Plugin_Debug_Plugin_Doctrine',
                    'File' => array('base_path' => APPLICATION_PATH . '/../'),
                    'Memory',
                    'Time',
                    'Registry',
                    'Exception',
                    'Html',
                ),
                'jquery_path' => '/scripts/jquery.js'
            );

            $debug = new ZFDebug_Controller_Plugin_Debug($options);
            $front->registerPlugin($debug);
        }
    }
}
