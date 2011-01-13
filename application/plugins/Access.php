<?php

class Application_Plugin_Access extends FinalView_Controller_Plugin_Access
{
    protected function _matchRequestToResource(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $contr = $request->getControllerName();
        $action = $request->getActionName();
        
        return $module . '.' . $contr . '.' . $action;
    }
    
    protected function _defaultHandler()
    {
        $this->_notFoundHandler();
    }
    protected function _notDetectedAdminHandler()
    {
        $this->_redirectToAdminLogin();
    }
    
    protected function _redirectToAdminLogin()
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoRoute(array(), 'AdminAuthLogin' );
    }
        
    protected function _notFoundIfAdminAuthorizedHandler()
    {
        $failedRule = $this->getResource()->getAccessRule();
        
        if ($failedRule->isFailedRule('admin_logged_in')) {
        	$this->_redirectToAdminLogin();
        }
        
        $this->_notFoundHandler();
    }

    protected function _forbiddenIfAdminAuthorizedHandler()
    {
        $failedRule = $this->getResource()->getAccessRule();
        
        if ($failedRule->isFailedRule('admin_logged_in')) {
        	$this->_redirectToAdminLogin();
        }
        
        $this->_forbiddenHandler();
    }               
}
