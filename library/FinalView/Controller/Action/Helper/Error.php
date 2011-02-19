<?php

class FinalView_Controller_Action_Helper_Error
    extends Zend_Controller_Action_Helper_Abstract
{
    const PAGE_NOT_FOUND_MESSAGE = 'PAGE_NOT_FOUND_MESSAGE';
    const PAGE_FORBIDDEN_MESSAGE = 'PAGE_FORBIDDEN_MESSAGE';
    
    public function notFound($message = null)
    {
        if (is_null($message)) {
            $message = __(self::PAGE_NOT_FOUND_MESSAGE);
        }
        
        $e = new FinalView_Application_Exception($message, 404);
        throw $e;
        
        return $e;
    }
    
    public function forbidden($message = null)
    {
        if (is_null($message)) {
            $message = __(self::PAGE_FORBIDDEN_MESSAGE);
        }
        $e = new FinalView_Application_Exception($message, 403);
        throw $e;
        
        return $e;
    }    
}
