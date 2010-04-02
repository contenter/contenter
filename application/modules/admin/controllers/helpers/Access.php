<?php

class Admin_Controller_Helper_Access 
    extends FinalView_Controller_Action_Helper_Access
{ 
    protected $users;   
    
    public function preDispatch()
    {
        $this->users = $this->getActionController()->getHelper('User');
        parent::preDispatch();
    }    
    
    protected function _adminIndexIndexAccess(array $params = array()) 
    {
        if (!$this->users->isLogged(Roles::USER_ADMIN)) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }
    }
    
    protected function _adminAuthLoginAccess(array $params = array()) 
    {
        if ($this->users->isLogged(Roles::USER_ADMIN) ) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }        
    }
    
    protected function _adminAuthLogoutAccess(array $params = array()) 
    {
        if (!$this->users->isLogged(Roles::USER_ADMIN)) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }        
    }    
}
