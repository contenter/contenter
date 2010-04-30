<?php

class Admin_UserController extends FinalView_Controller_Action
{
       
    public function indexAction() 
    {
        if ($this->getRequest()->isPost()) {
            
            switch (true) {
                case $this->getRequest()->has('delete'):
                    $this->delete();
                break;
            }
            
            $this->_helper->redirector->gotoUrl($this->_request_uri);
        }
        
        $this->view->grid = new Admin_Grid_Users($this->getGridParams());        
    }
    
    private function getGridParams()
    {
        $gridParams = array_intersect_key(
            $this->getRequest()->getParams(), 
            array_flip(array('sort', 'direction', 'page'))
        );
        
        return $gridParams;    
    }
    
    private function delete()
    {
        Doctrine::getTable('User')->findByParams(array(
            'ids' => $this->getRequest()->getParam('users_ids', array() )
        ))->delete();
    }
    
    public function editAction()
    {
        //edit action for user
    }
}
