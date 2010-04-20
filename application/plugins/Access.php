<?php

class Application_Plugin_Access extends FinalView_Controller_Plugin_Access
{
    protected function _notDetectedAdminHandler()
    {
        $this->_redirectToAdminLogin();
    }
    
    protected function _redirectToAdminLogin()
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoRoute(array(), 'AdminAuthLogin' );
    }
}
