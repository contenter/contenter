<?php

abstract class FinalView_Controller_Action extends Zend_Controller_Action 
{
    
    /**
    * Request uri
    * 
    * @var string
    */
    protected $_request_uri;
    
    /**
     * Initialize object
     *
     * @return void
     */
    public function init()
    {
        $this->_request_uri = $this->getRequest()->getRequestUri();
    }
    
    /**
    * Abort request
    * 
    * @param string $message
    */
    public function abort($message = '') 
    {
        echo 404 . '<br />' . $message; exit;
        $this->_request
            ->setModuleName('default')
            ->setControllerName('error')
            ->setActionName('abort')
            ->setParam('message', $message)
            ->setDispatched(false);
    }
    
    /**
    * Deny request
    * 
    * @param string $message
    */
    public function deny($message = '') 
    {
        echo 403 . '<br />' . $message; exit;
        $this->_request
            ->setModuleName('default')
            ->setControllerName('error')
            ->setActionName('deny')
            ->setParam('message', $message)
            ->setDispatched(false);
    }
    
}