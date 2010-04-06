<?php

class FinalView_Controller_Action_Helper_Access 
    extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
    * Status available codes
    * 
    */
    const STATUS_OK =           200;
    const STATUS_FORBIDDEN =    403;
    const STATUS_NOT_FOUND =    404;
    
    /**
    * Status code
    * 
    * @var integer
    */
    protected $_status_code;
    
    /**
    * Status message
    * 
    * @var string
    */
    protected $_status_message;
    
    /**
     * Called before action
     *
     * @return void
     */
    public function preDispatch()
    {      
        $route = Zend_Controller_Front::getInstance()
            ->getRouter()->getCurrentRouteName();
            // @todo определить имя роута через связку модуль-контроллер-action
        $params = $this->getRequest()->getParams();
               
        $this->_call($route, $params);
        
        $this->_processStatus();
        
        $this->_resetStatus();
    }
    
    /**
    * Call action accessor
    * 
    * @param string $route
    * @param array $params
    */
    private function _call($route, array $params = array()) 
    {
        if (method_exists($this, $method = '_' . lcfirst($route) . 'Access')) {
            call_user_func(array($this, $method), $params);
        }
        
        if (is_null($this->_status_code)) {
            $this->_status_code = self::STATUS_OK;
        }
    }
    
    /**
    * Process status: varify [and take action]
    * 
    */
    protected function _processStatus() 
    {
        if (self::STATUS_OK != $this->_status_code) {
            switch($this->_status_code) 
            {
                case self::STATUS_NOT_FOUND : 
                    $this->getActionController()->abort($this->_status_message);
                    break;
                
                case self::STATUS_FORBIDDEN : 
                    $this->getActionController()->deny($this->_status_message);
                    break;
                
                default : 
                    trigger_error('Unknown access status', E_USER_ERROR);
            }
        }
    }
    
    /**
    * Nullify status and message on exit
    * 
    */
    private function _resetStatus() 
    {
        $this->_status_code = null; 
        $this->_status_message = null;
    }
    
    /**
    * Interface method
    * 
    * @param string $route
    * @param array $params
    * @return boolean
    */
    public function isAccessible($route, array $params = array(), 
        $use_params_from_request = false) 
    {
        if ((bool)$use_params_from_request) {
            $params= $this->getRequest()->getParams();
        }
        $this->_call($route, $params);
        $accessable = self::STATUS_OK == $this->_status_code;
        
        $this->_resetStatus();
        
        return $accessable;
    }
    
}
