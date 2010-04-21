<?php
class Application_Rules_Admin
{    
    
    public function adminLoggedInRule($params)
    {        
        $admin = FinalView_Auth::getInstance()->getAuthEntity(array(
            'role'  =>  Roles::USER_ADMIN 
        ));
        
        if ($admin) {
        	return true;
        }
        
        return false;
    }   
}
