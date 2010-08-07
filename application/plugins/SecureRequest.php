<?php

class Application_Plugin_SecureRequest extends Zend_Controller_Plugin_Abstract
{
    
    protected $_secure_pages;
    
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
        if (is_null($this->_secure_pages)) {
            switch (true) {
                case file_exists(APPLICATION_PATH . '/configs/secure_pages.xml'):
                    
                    $this->_secure_pages = array();
                    
                    $secure_pages = new Zend_Config_Xml(APPLICATION_PATH . '/configs/secure_pages.xml');
                    
                    if (isset($secure_pages->secure_pages->resource)) {
                        $this->_secure_pages = $secure_pages->secure_pages->resource->toArray();
                        if (!is_array(reset($this->_secure_pages))) {
                        	$this->_secure_pages = array($this->_secure_pages);
                        }	
                    }
                break;
                case file_exists(APPLICATION_PATH . '/configs/secure_pages.yml'):
                    $this->_secure_pages = array();
                    
                    $secure_pages = Doctrine_Parser::load(APPLICATION_PATH . '/configs/secure_pages.yml', 'yml');

                    $this->_secure_pages = $secure_pages;                    
                break;
                default:
            	   $this->_secure_pages = array();
            	break;
            }
        }

        return $this->_secure_pages;
    }
    
}
