<?php

class FinalView_View_Helper_IsAccessible extends Zend_View_Helper_Abstract
{
    
    public function isAccessible($route, array $params = array(), 
        $use_params_from_request = false) 
    {
        return 
            Zend_Controller_Action_HelperBroker::getStaticHelper('Access')
            ->isAccessible($route, $params, $use_params_from_request);
    }
    
}