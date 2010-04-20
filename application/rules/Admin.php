<?php
class Application_Rules_Admin
{    
    
    public function adminLoggedInRule($params)
    {        
        $storage = Zend_Auth::getInstance()->getStorage()->read();
        $user_id = $storage->id;        

        $loggedInUser = Doctrine::getTable('User')->findOneByParams(array(
            'id'    =>  $user_id,
            'role'  =>  Roles::USER_ADMIN 
        ));
        
        if ($loggedInUser) {
        	return true;
        }
        
        return false;
    }    
}
