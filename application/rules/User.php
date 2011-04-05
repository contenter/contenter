<?php
class Application_Rules_User extends FinalView_Access_Rules_Abstract
{

    protected $_user;
    
    public function userInParamsRule()
    {
        return array_key_exists('user_id', $this->_params);
    }
    
    public function userExistRule()
    {        
        $contextualUser = Doctrine::getTable('User')->findOneByParams(array(
            'id'    =>   $this->_params['user_id']
        ));
        
        if ($contextualUser) return true;
        
        return false;
    }
    
    public function loggedInRule()
    {
        return FinalView_Auth::getInstance()->hasIdentity();
    }   
    
    public function userExistByEmailAndRoleRule()
    {
        $this->_user = Doctrine::getTable('User')->findOneByParams(array(
            'email'     =>  $this->_params['email'],
            'role'      =>  (int)$this->_params['role']  
        ));

        if ($this->_user) return true;
        
        return false;        
}
    
    public function userNotConfirmedRule()
    {
        return !(bool)$this->_user->confirmed;
    }    
    
    public function forgotPswdHashRule()
    {
        $hash = Doctrine::getTable('Confirmation')->findOneByParams(array(
            'hash'  =>  $this->_params['hash'],
            'type'  =>  'forgot-password'
        ));
        
        return (bool)$hash;
    }

    public function urlInParamsRule()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        return $request->getQuery('url', false);
    }
}
