<?php

/**
* Authentication
* 
*/
class User_AuthController extends FinalView_Controller_Action
{    
    
    private $_loginForm;
    
    public $storage_params = array(
        'id'
    );
    
    /**
    * Login Peer
    * 
    */
    public function loginAction() 
    {
        if ($this->_login() == Zend_Auth_Result::SUCCESS){
            $this->_helper->redirector->gotoRoute(array(), 'UserIndexIndex');            
        }
    }
    
    /**
    * Login any role
    * 
    * @param Zend_Form $form
    * @param Doctrine_Record $model
    */
    private function _login() 
    {
        $this->view->form = $this->getLoginForm();        
        
        if ($this->getRequest()->isPost()) {
            if ($this->getLoginForm()->isValid($this->getRequest()->getPost())) {
                                
                $result = Zend_Auth::getInstance()
                    ->authenticate(new User_Auth_Adapter(
                        $this->getLoginForm()->getValues(), 
                        $this->getLoginAccount(), 
                        $this->storage_params
                    )
                );
                
                if ($result->getCode() !== Zend_Auth_Result::SUCCESS) {
                	$this->getLoginForm()->addErrors($result->getMessages());
                }
                
                return $result->getCode();                
            }
        }
    }
    
    protected function getLoginAccount()
    {
        return Doctrine::getTable('User')->findOneByParams(array(
            'email' =>  $this->getLoginForm()->getValue('email'),
            'role'  =>  Roles::USER
        ));
    }
    
    protected function getLoginForm()
    {
        if (is_null($this->_loginForm)) {
        	$this->_loginForm = new User_Form_Login;
        }
        return $this->_loginForm;        
    } 
    
    /**
    * Logout
    * 
    */
    public function logoutAction() 
    {
        Zend_Auth::getInstance()->clearIdentity();
        
        $this->_helper->redirector->gotoRoute(array(), 'UserAuthLogin');
    }
    
}
