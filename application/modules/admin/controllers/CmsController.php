<?php

class Admin_CmsController extends FinalView_Controller_Action
{ 
    
    private $_form;
    
    public function indexAction() 
    {
        $this->view->grid = new Admin_Grid_CmsPages();
    }
    
    public function editPageAction()
    {
        $page = Doctrine::getTable('CmsPage')->findOneByParams(array(
            'page_name' =>  $this->_getParam('page_name')
        ));
        
        if (!$this->getRequest()->isPost()) {
            $this->_getPageForm()->populate(
                $page->toArray()
            );        	
        }
        
        if ($page = $this->_savePage($page)) {
            $page->save();
            
            $this->_helper->redirector->gotoRoute(array(), 'AdminCmsIndex' );
        }
    }
    
    public function addPageAction()
    {                
        if ($page = $this->_savePage()) {
            $page->save();
            
            $this->_helper->redirector->gotoRoute(array(), 'AdminCmsIndex' );            
        }
    }
    
    private function _getPageForm()
    {
        if ($this->_form === null) {
        	$this->_form = new Admin_Form_CmsPage(); 
        }
        return $this->_form; 
    }
    
    private function _savePage($page = null)
    {
        if ($this->getRequest()->isPost()) {
        	if ($this->_getPageForm()->isValid($this->getRequest()->getPost() ) ) {
                if (is_null($page) ) {
                	$page = Doctrine::getTable('CmsPage')->create();
                }
                $page->merge($this->_getPageForm()->getValues());
                return $page;
            }
        }
        
        $this->view->form = $this->_getPageForm();
    }    
}
