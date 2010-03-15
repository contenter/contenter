<?php

class Application_Plugin_SecureRequest extends Zend_Controller_Plugin_Abstract
{
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // skip ajax and errors
        if ($request->isXmlHttpRequest() || 
            'default' == $request->getModuleName() &&
            'error' == $request->getControllerName() && 
            'error' == $request->getActionName()) 
        {
            return;
        }
        
        $resource = array
        (
            'module' => $request->getModuleName(), 
            'controller' => $request->getControllerName(), 
            'action' => $request->getActionName(), 
        );
        
        switch(true) 
        {
            case in_array($resource, $this->_getSecurePages()) && !$request->isSecure() : 
                $scheme = Zend_Controller_Request_Http::SCHEME_HTTPS;
                break;
            
            case !in_array($resource, $this->_getSecurePages()) && $request->isSecure() : 
                $scheme = Zend_Controller_Request_Http::SCHEME_HTTP;
                break;
        }
        
         if (isset($scheme)) {
             Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector')
                 ->gotoUrl($scheme . '://' . $request->getHttpHost() . $request->getRequestUri());
         }
    }
    
    private function _getSecurePages() 
    {
        $secure_pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/secure_pages.xml');
        if (isset($secure_pages->secure_pages->resource)) {
            return $secure_pages->secure_pages->resource->toArray();	
        }
        return array();
    }
    
}