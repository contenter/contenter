<?php

class Application_Plugin_Access extends FinalView_Controller_Plugin_Access
{
    protected function _notDetectedUserHandler()
    {
        $failedRule = $this->getResource()->getAccessRule();
        switch (true) {
            case $failedRule->isFailedRule('user_exist'): 
                $this->_denyHandler();
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
        Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')->gotoRoute(array(), 'UserAuthLogin');
    }
}
