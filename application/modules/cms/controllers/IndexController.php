<?php

class Cms_IndexController extends FinalView_Controller_Action
{ 
    
    public function indexAction() 
    {
        $page = Doctrine::getTable('CmsPage')->findOneByParams(array(
           'page_name'  =>  $this->_getParam('page_name') 
        ));  
        
        $this->view->page = $page;
    }
}
