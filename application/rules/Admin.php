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
    
    public function cmsPageExistsRule()
    {        
        return (bool)Doctrine::getTable('CmsPage')->countByParams(array(
            'page_name' =>  $this->_params['page_name']
        ));
    }
    
    public function addPageAllowedRule()
    {
        return (bool)Config::get('cms', 'allow_to_add_page', false);
    }
    
    public function deletePageAllowedRule()
    {
        return (bool)Config::get('cms', 'allow_to_delete_page', false);
    }
    
    public function changePageNameAllowedRule()
    {
        return $this->addPageAllowedRule() || (bool)Config::get('cms', 'allow_to_change_page_name', false);
    }
    
    public function changePageRouteAllowedRule()
    {
        return (bool)Config::get('cms', 'allow_to_change_page_route', false);
    }
}