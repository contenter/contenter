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
            
            try{
                $requestAllowed = $resource->getAccessRule()->check($request->getParams());
            }catch(Exception $e){
                $this->_throwException($e);
                return;
            }            

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
        $this->_throwException(new FinalView_Application_Exception(
            __(FinalView_Controller_Action_Helper_Error::PAGE_NOT_FOUND_MESSAGE), 
            404
        ));
    }
    
    protected function _forbiddenHandler()
    {
        $this->_throwException(new FinalView_Application_Exception(
            __(FinalView_Controller_Action_Helper_Error::PAGE_FORBIDDEN_MESSAGE), 
            403
        ));
    }
    
    protected function _throwException(Exception $e)
    {
        $error = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        $error->exception = $e;
        $error->type = Zend_Controller_Plugin_ErrorHandler::EXCEPTION_OTHER;
        $error->request = clone $this->_request;

        $this->_request
            ->setModuleName('default')
            ->setControllerName('error')
            ->setActionName('error')
            ->setParam('error_handler', $error)
            ->setDispatched(true);    
    }
}
