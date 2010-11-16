<?php
class Application_Rules_User extends FinalView_Access_Rules_Abstract
{

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
}
