<?php

class FinalView_Controller_Action_Helper_IsAllowed
    extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($resource, $params = array(), $head = null, $context = null)
    {
       return $this->isAllowed($resource, $params, $head, $context);
    }

    public function isAllowed($resource, $params = array(), $head = null, $context = null)
    {
        $resource = FinalView_Application_Resources::get(
            $resource,
            $head
        );

        if (is_null($resource)) {
        	return FinalView_Access_Rules::$options['default_behavior'];
        }

        return $resource->getAccessRule($context)->check($params);
    }
}
