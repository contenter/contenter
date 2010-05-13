<?php

abstract class FinalView_Controller_Plugin_Access extends Zend_Controller_Plugin_Abstract 
{

    private $_resource;
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {       
        $resource = FinalView_Application_Resources::get(
            $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName(), 
            'Request'
        );

        if (is_null($resource)) {
        	$this->_defaultHandler();
        }else{
            $this->setResource($resource);        
            
            $requestAllowed = $resource->getAccessRule()->check($request->getParams());

            if (!$requestAllowed) {            
                if ($handler = $resource->getResource('handler')) {
                    $this->_call($handler);	
                }else{
                    $this->_defaultHandler();
                }            
            }        
        }
    }
    
    protected function _defaultHandler()
    {
        $this->_notFoundHandler();
    }   
    
    /**
    * Call access denied handler
    * 
    * @param string $route
    * @param array $params
    */
    private function _call($handler) 
    {
        $filter = new Zend_Filter_Word_UnderscoreToCamelCase();
        
        if (method_exists($this, $method = '_' . $filter->filter($handler) . 'Handler')) {
            call_user_func(array($this, $method));
        }else{
            throw new FinalView_Exception('handler '.$handler.' not found in Access Plugin');
        }
    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }
    
    public function getResource()
    {
        return $this->_resource;
    }
    
    protected function _notFoundHandler()
    {
        dump('404');
        exit;
    }
    
    protected function _forbiddenHandler()
    {
        dump('403');
        exit;    
    }
}
