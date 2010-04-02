<?php

class FinalView_Controller_Action_Helper_User
    extends Zend_Controller_Action_Helper_Abstract
{
    private $users;
    
    public function init()
    {
        if ($user_id = $this->getRequest()->getParam('user_id', null)) {
            $requestedUser = Doctrine::getTable('User')->findOneByParams(array(
                'id'    =>   $user_id 
            ));
            
            if ($requestedUser) {
                 $this->users['requested'] = $requestedUser;
            }
        }
        if ($this->isLogged()) {
            $storage = Zend_Auth::getInstance()->getStorage()->read();
            $user_id = $storage->id;        
    
            $loggedInUser = Doctrine::getTable('User')->findOneByParams(array(
                'id'    =>   $user_id 
            ));
            
            if ($loggedInUser) {
                $this->users['logged'] = $loggedInUser;
            }
        }
    }
    
    /**
    * Return whether current user is logged
    * 
    * @return boolean
    */
    public function isLogged($role = null) 
    {
        $isLogged = Zend_Auth::getInstance()->hasIdentity()
        
        $isRole = true;
        if (!is_null($role)) {
            $isRole = $this->logged->isRole($role);	
        }
        
        return $isLogged && $isRole;
    }
    
    public function __get($type)
    {
        if (isset($this->users[$type])) {
            return $this->users[$type];
        }
        
        return null;
    }
}
