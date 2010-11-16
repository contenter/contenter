<?php

class Admin_Bootstrap extends FinalView_Application_Module_Bootstrap 
{

	/**
    * load helpers
    *
    */
    public function init()
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(APPLICATION_PATH . '/layouts/');
        
        $layout->setLayout('admin');
        
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/modules/admin/controllers/helpers', 
            'Admin_Controller_Helper'
        );         
    }
    
    protected function _initAutoload() 
    {        
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Admin',
            'basePath'  => APPLICATION_PATH . '/modules/admin',
        ));
        $autoloader->addResourceTypes(array(
            'grid' => array
                (
                    'path' => 'grid', 
                    'namespace' => 'Grid', 
                ),
            'auth' => array
                (
                    'path' => 'auth', 
                    'namespace' => 'Auth', 
                ), 
        ));
        
        return $autoloader;
    }    
}
