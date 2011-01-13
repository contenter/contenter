<?php
class Application_Rules_Admin extends FinalView_Access_Rules_Abstract
{    
    
    public function adminLoggedInRule()
    {        
        if (!FinalView_Auth::getInstance()->hasIdentity()) {
            return false;
        }

        $admin = FinalView_Auth::getInstance()->getAuthEntity(array(
            'role'  =>  Roles::USER_BACKEND
        ));

        if ($admin) {
            return true;
        }
        
        return false;
    }   
}
