<?php

class Application_Plugin_Access extends FinalView_Controller_Plugin_Access
{
    protected function _matchRequestToResource(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $contr = $request->getControllerName();
        $action = $request->getActionName();
        
        switch (true) {
            case FinalView_Application_Resources::hasResource($res = $module . '.' . $contr . '.' . $action):
            case FinalView_Application_Resources::hasResource($res = $module . '.' . $contr):
            case FinalView_Application_Resources::hasResource($res = $module):
                return $res;
            break;
        }
        return null;
    }
    
    protected function _defaultHandler()
    {
        $this->_notFoundHandler();
    }
}
