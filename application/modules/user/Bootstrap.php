<?php

class User_Bootstrap extends FinalView_Application_Module_Bootstrap 
{
    
    /**
    * load helpers 
    * 
    */
    public function init()
    {       
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/modules/user/controllers/helpers', 
            'User_Controller_Helper'
        );
        
        Zend_Controller_Action_HelperBroker::getStaticHelper('Access');
    }
    
     protected function _initAutoload() 
     {
        require_once APPLICATION_PATH . '/modules/user/Encrypt.php';         
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'User',
            'basePath'  => APPLICATION_PATH . '/modules/user',
        ));
        $autoloader->addResourceTypes(array(
            'auth' => array
                (
                    'path' => 'auth', 
                    'namespace' => 'Auth', 
                ) 
        ));
     }
    
}