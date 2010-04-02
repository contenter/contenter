<?php
class User_Controller_Helper_Access 
    extends FinalView_Controller_Action_Helper_Access
{
    protected $users;   
    
    public function preDispatch()
    {
        $this->users = $this->getActionController()->getHelper('User');
        parent::preDispatch();
    }    
    
    protected function _userIndexIndexAccess(array $params = array()) 
    {
        if (!$this->users->requested && !$this->users->isLogged(Roles::USER)) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }
    }
    
    protected function _userRegisterRegisterAccess(array $params = array()) 
    {
        if ($this->users->isLogged(Roles::USER) ) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }        
    }
    
    protected function _userAuthLoginAccess(array $params = array()) 
    {
        if ($this->users->isLogged(Roles::USER)) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }        
    }
    
    protected function _userAuthLogoutAccess(array $params = array()) 
    {
        if (!$this->users->isLogged(Roles::USER)) {
            $this->_status_code = self::STATUS_FORBIDDEN;
        }        
    }    
}
