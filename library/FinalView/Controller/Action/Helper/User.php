<?php

class FinalView_Controller_Action_Helper_User
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_users;
    
    /**
    * Return whether current user is logged
    * 
    * @return boolean
    */
    public function isAuthorized($role = null) 
    {
        $isAuthorized = FinalView_Auth::getInstance()->hasIdentity();
        
        $isRole = true;
        if ($isAuthorized && !is_null($role)) {
            $isRole = $this->authorized->isRole($role);	
        }
        
        return $isAuthorized && $isRole;
    }
    
    protected function _getUser($type)
    {
        if (!isset($this->users[$type])) {
            switch ($type) {
                case 'authorized':
                    if ($this->isAuthorized()) {
                        $this->_users['authorized'] = FinalView_Auth::getInstance()->getAuthEntity();	
                    }                       
            	break;
            	case 'contextual':
                    if ($user_id = $this->getRequest()->getParam('user_id', null)) {
                        $contextualUser = Doctrine::getTable('User')->findOneByParams(array(
                            'id'    =>   $user_id 
                        ));
                        
                        if ($contextualUser) {
                             $this->_users['contextual'] = $contextualUser;
                        }
                    }            	
            	break;
            	case 'current':
                    switch (true) {
                        case !is_null($this->contextual):
                            $this->_users['current'] = $this->contextual;
                        break;
                        case !is_null($this->authorized):
                            $this->_users['current'] = $this->authorized;
                        break;
                    }
            	break;
            	default:
            	   return null;
            	break;
            }
        }        
        
        return @$this->_users[$type];        
    }
    
    public function __get($type)
    {        
        return $this->_getUser($type);
    }
}
