<?php
class Application_Rules_User
{

    public function userInParamsRule($params)
    {
        return array_key_exists('user_id', $params);
    }
    
    public function userExistRule($params)
    {        
        $contextualUser = Doctrine::getTable('User')->findOneByParams(array(
            'id'    =>   $params['user_id']
        ));
        
        if ($contextualUser) return true;
        
        return false;
    }
    
    public function loggedInRule($params)
    {
        return FinalView_Auth::getInstance()->hasIdentity();
    }    
}
