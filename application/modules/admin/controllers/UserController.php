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
            
            $this->_helper->redirector->gotoUrl($this->getRequest()->getRequestUri());
        }
        
        $this->view->grid = new Admin_Grid_Users();
    }
    
    private function delete()
    {
        Doctrine::getTable('User')->findByParams(array(
            'ids'   =>  $this->getRequest()->getParam('ids', array() ),
            'role'  =>  Roles::USER_FRONTEND
        ))->delete();
    }
    
    public function editAction()
    {
        //edit action for user
    }
}
