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
    
    protected function _notAllowedWorkWithCmsPage()
    {
        $failedRule = $this->getResource()->getAccessRule();
        
        if ($failedRule->isFailedRule('admin_logged_in')) {
        	$this->_redirectToAdminLogin();
        }
        
        if ($failedRule->isFailedRule('cms_page_exists')) {
        	$this->_notFoundHandler();
        	return;
        }
        
        $this->_forbiddenHandler();                
    }               
}
