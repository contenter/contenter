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
        require_once APPLICATION_PATH . '/plugins/SecureRequest.php';
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
          
    /**
    * Init Acl engine
    * 
    */
    protected function _initAcl() 
    {
        /*
        * @todo: We need to create a container ACL that will receive as a parameter 
        * Adapter (files, db), which will determine the method of storage Acl. 
        * Basic adapters can be written to storage acl in files and storage acl in the database. 
        *
        */                
        
        
        //Set here your project ACL. Your implementation might be a basic solution of Finalview  
        
        $acl = new Zend_Acl;
        return $acl; 
    }
    
    protected function _initRoles()
    {
        require_once APPLICATION_PATH.'/Roles.php';
    }
    
}