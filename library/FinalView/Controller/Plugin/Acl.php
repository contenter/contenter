<?php

abstract class FinalView_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract 
{
    
    /**
    * Zend_Acl object
    * 
    * @var Zend_Acl
    */
    private $_acl;
    
    
    public function __construct(Zend_Acl $acl) 
    {
        $this->_acl = $acl;
    }
    
    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * @param  Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $resource = $this->_getResource($request);
        
        // check ACL only 
        // - when current action can be dispatched 
        // - and ACL resource exists
        if (Zend_Controller_Front::getInstance()->getDispatcher()
            ->isDispatchable($request) && $this->_acl->has($resource) /*&& 
            $this->_acl->hasRole($this->_getRole())*/) 
        {
            switch(false) 
            {
                // login
                case Zend_Auth::getInstance()->hasIdentity() : 
                    $this->_login();
                    break;
                
                // forbidden
                case $this->_acl->isAllowed($this->_getRole(), $resource) : 
                    $this->_deny();
                    break;
            }
        }
    }
    
    /**
    * Tell whether given resource is dispatchable
    * 
    * @param FinalView_Acl_Resource $resource
    * @return boolean
    */
    public function isDispatchable(FinalView_Acl_Resource $resource) 
    {
        if ($this->_acl->has($resource)) 
        {
            switch(false) 
            {
                case Zend_Auth::getInstance()->hasIdentity() : 
                case $this->_acl->isAllowed($this->_getRole(), $resource) : 
                    return false;
            }
        }
        
        return true;
    }
    
    /**
    * Return acl resource
    * 
    * @param  Zend_Controller_Request_Abstract $request
    * @return FinalView_Acl_Resource
    */
    protected function _getResource() 
    {
        return new FinalView_Acl_Resource
            (
                $this->_request->getModuleName(), 
                $this->_request->getControllerName(), 
                $this->_request->getActionName()
            );
    }
    
    /**
    * Return acl role
    * 
    * @return Zend_Acl_Role
    */
    protected function _getRole() 
    {
        return new Zend_Acl_Role(Zend_Auth::getInstance()->getIdentity()->role);
    }
    
    /**
    * Deny access
    * 
    */
    abstract protected function _deny();
    
    /**
    * Redirect to the login action
    * 
    */
    abstract protected function _login();
    
    /**
    * Return whether user is logged
    * 
    * @return boolean
    */
    abstract protected function _isLogged();
    
}