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
    public function isAutorized($role = null) 
    {
        $isAutorized = FinalView_Auth::getInstance()->hasIdentity();
        
        $isRole = true;
        if ($isAutorized && !is_null($role)) {
            $isRole = $this->autorized->isRole($role);	
        }
        
        return $isLogged && $isRole;
    }
    
    protected function _getUser($type)
    {
        if (!isset($this->users[$type])) {
            switch ($type) {
                case 'autorized':
                    if ($this->isAutorized()) {
                        $this->_users['autorized'] = FinalView_Auth::getInstance()->getAuthEntity();	
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
