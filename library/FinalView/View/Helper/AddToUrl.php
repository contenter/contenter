<?php

/**
* Pagination
* 
*/
class FinalView_View_Helper_AddToUrl extends Zend_View_Helper_Abstract
{
    
    public function addToUrl(array $params = array(), $url = null) 
    {
        return 
            Zend_Controller_Action_HelperBroker::getStaticHelper('AddToUrl')
            ->addToUrl($params, $url);
    }    
}
