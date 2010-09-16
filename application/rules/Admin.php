<?php
class Application_Rules_Admin extends FinalView_Access_Rules_Abstract
{    
    
    public function adminLoggedInRule()
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
