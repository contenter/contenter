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
            'User_ActionHelper'
        );
    }
    

    protected function _initAutoload() 
    {        
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'User',
            'basePath'  => APPLICATION_PATH . '/modules/user',
        ));
        $autoloader->addResourceTypes(array(
            'auth' => array(
                'path' => 'auth',
                'namespace' => 'Auth',
            )
        ));
        $autoloader->addResourceTypes(array(
            'utils' => array
                (
                    'path' => 'utils',
                    'namespace' => 'Utils',
                )
        ));
        return $autoloader;
    }
}
