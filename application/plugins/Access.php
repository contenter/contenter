<?php

class Application_Plugin_Access extends FinalView_Controller_Plugin_Access
{
    protected function _notDetectedUserHandler()
    {
        $failedRule = $this->getResource()->getAccessRule();
        switch (true) {
            case $failedRule->isFailedRule('user_exist'): 
                $this->_notFoundHandler();
            break;
            case $failedRule->isFailedRule('logged_in'):
                $this->_redirectToLogin();
            break;
        }
    }
    
    protected function _defaultHandler()
    {
        switch (true) {
            case is_null($this->getResource()):
                return;
            break;
        }
        
        parent::_defaultHandler();
    }
    
    protected function _redirectToLogin()
    {
        $option = isset($_SERVER['REQUEST_URI']) 
            ? array('back_url' => $_SERVER['REQUEST_URI'])
            : array();
        
        $urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        $addToUrlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('AddToUrl');
        
        $url = $addToUrlHelper->addToUrl($option, $urlHelper->url(array(), 'UserAuthLogin'));
        
        Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($url);
        
    }
    
    protected function _redirectToUserIndexHandler()
    {
        $urlHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        
        $url = $this->getRequest()->getParam('back_url', $urlHelper->url(array(), 'UserIndexIndex'));
        
        Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoUrl($url);
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
