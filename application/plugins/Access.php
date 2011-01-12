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
}
