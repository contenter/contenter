<?php

/**
* Authentication
* 
*/
class User_AuthController extends FinalView_Controller_Action
{

    private $_loginForm;
    private $_forgotPswdForm;
    
    public $storage_params = array(
        'id'
    );
    
    /**
    * Login Peer
    * 
    */
    public function loginAction() 
    {
        if ($this->_login() == Zend_Auth_Result::SUCCESS) {
            $url = $this->getRequest()
                ->getParam('back_url', $this->view->url(array(), 'UserIndexIndex'));
            
            $this->_redirect($url);
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
                                
                $result = FinalView_Auth::getInstance()
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
            'role'  =>  Roles::USER_FRONTEND
        ));
    }
    
    protected function getLoginForm()
    {
        if (is_null($this->_loginForm)) {
            $this->_loginForm = new User_Form_Login(
                array('backUrl' => $this->getRequest()->getParam('back_url')));
            $this->_loginForm->setAction($this->view->url(array(), 'UserAuthLogin'));
        }
        return $this->_loginForm;
    } 
    
    /**
    * Logout
    * 
    */
    public function logoutAction() 
    {
        FinalView_Auth::getInstance()->clearIdentity();
        
        $this->_helper->redirector->gotoRoute(array(), 'UserAuthLogin');
    }

    /**
    * Forgot Password
    * 
    */
    
    public function forgotPasswordAction()
    {        
        
        if ($this->getRequest()->isPost()) {
            if ($this->getForgotPswdForm()->isValid($this->getRequest()->getPost())) {
                
                $user = $this->getForgotPswdAccount();
                if (false == $user->getConfirmation('forgot-password')) {
                      $user->createConfirmation('forgot-password');
                }                
                $this->_sendForgotPasswordLetter($user);
                
                $this->_helper->redirector->gotoRoute(array(
                    'hash'  =>  $user->getConfirmation('forgot-password')->hash  
                ), 'UserAuthForgotPasswordMailSent');
            }            
        }        
        
        $this->view->forgotPswdForm = $this->getForgotPswdForm();
    }
    
    protected function getForgotPswdAccount()
    {
        return Doctrine::getTable('User')->findOneByParams(array(
            'email'     =>  $this->getForgotPswdForm()->getValue('email'),
            'role'      =>  Roles::USER
        ));    
    }
    
    protected function getForgotPswdForm()
    {
        if (is_null($this->_forgotPswdForm)) {
        	$this->_forgotPswdForm = new User_Form_ForgotPswd;
        }
        return $this->_forgotPswdForm;          
    }
    
    public function forgotPasswordMailSentAction()
    {
        
    }
    
    protected function _sendForgotPasswordLetter($user)
    {
        $mail = new FinalView_Mail('user/forgot-password', array(
            'email' => $user->email, 
            'hash'  => $user->getConfirmation('forgot-password')->hash,
        ));
        $mail->send($user->email, $user->email); 
    }
    
}
