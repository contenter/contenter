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
        $this->bootstrap('ApplicationAutoLoader');
        
        $this->bootstrap('frontcontroller');
        
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_SecureRequest, 3);
    }
    
    /**
    * Init your project View Helpers
    * 
    */        
    protected function _initViewHelpers() 
    {
        return parent::_initViewHelpers();
    }
    
    protected function _initApplicationAutoLoader()
    {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'namespace' => 'Application_',
            'basePath'  => APPLICATION_PATH,
        ));
        
        $loader->addResourceType('plugins', '/plugins', 'Plugin');
        
        return $loader;
    }

    protected function _initRoles()
    {
        require_once APPLICATION_PATH.'/Roles.php';
    }
    
    protected function _initAuthUserTable()
    {
        $this->bootstrap('Doctrine');
        
        FinalView_Auth::setAuthEntityTable('User');
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
        
        $this->bootstrap('ApplicationAutoLoader');
        $loader = $this->getResource('ApplicationAutoLoader');
        $loader->addResourceType('rules', '/rules', 'Rules'); 
    }
    
    protected function _initResources()
    {        
        $this->bootstrap('Doctrine');
        $resources = Doctrine_Parser::load(APPLICATION_PATH . '/configs/resources.yml', 'yml');
        
        FinalView_Application_Resources::setResources($resources);

        $this->bootstrap('ApplicationAutoLoader');
        $this->bootstrap('frontcontroller');
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Access);       
    }    
    
}
