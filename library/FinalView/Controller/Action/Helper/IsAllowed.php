<?php

class FinalView_Controller_Action_Helper_IsAllowed
    extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($resource, $params = array(), $mode = FinalView_Application_Resources::ACCESS_MODE_SOFT)
    {
       return $this->isAllowed($resource, $params, $mode);
    }

    public function isAllowed($resource, $params = array(), $mode = FinalView_Application_Resources::ACCESS_MODE_SOFT)
    {
        $resource = FinalView_Application_Resources::get(
            $resource,
            $mode
        );

        if (is_null($resource)) {
            return FinalView_Access_Rules::$options['default_behavior'];
        }

        return $resource->getAccessRule()->check($params);
    }
}
