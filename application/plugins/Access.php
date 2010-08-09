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
}
