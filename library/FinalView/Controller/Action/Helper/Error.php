<?php

class FinalView_Controller_Action_Helper_Error
    extends Zend_Controller_Action_Helper_Abstract
{
    const ABORT_MESSAGE = 'ABORT_MESSAGE';
    const DENY_MESSAGE = 'DENY_MESSAGE';
    
    public function abort($message = null)
    {
        if (is_null($message)) {
        	$message = __(self::ABORT_MESSAGE);
        }
        
        throw new FinalView_Exception($message, 404);
    }
    
    public function deny($message = null)
    {
        if (is_null($message)) {
        	$message = __(self::DENY_MESSAGE);
        }
        
        throw new FinalView_Exception($message, 403);
    }    
}
