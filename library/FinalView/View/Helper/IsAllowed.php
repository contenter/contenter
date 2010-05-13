<?php

class FinalView_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    
    public function isAllowed($resource, $params = array(), $head = null) 
    {
        return 
            Zend_Controller_Action_HelperBroker::getStaticHelper('IsAllowed')
            ->isAllowed($resource, $params, $head);
    }
    
}
