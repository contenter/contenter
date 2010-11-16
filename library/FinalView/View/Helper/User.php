<?php

class FinalView_View_Helper_User extends Zend_View_Helper_Abstract
{
    
    public function user($type = 'authorized') 
    {
        return 
            Zend_Controller_Action_HelperBroker::getStaticHelper('User')
            ->$type;
    }
    
}
