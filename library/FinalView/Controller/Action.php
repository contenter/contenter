<?php

abstract class FinalView_Controller_Action extends Zend_Controller_Action 
{
    
    /**
    * Request uri
    * 
    * @var string
    */
    protected $_request_uri; //depricated don't use this
    
    /**
     * must be eliminated 
     *     Initialize object
     *
     * @return void
     */
    public function init()
    {
        $this->_request_uri = $this->getRequest()->getRequestUri();
    }
}
