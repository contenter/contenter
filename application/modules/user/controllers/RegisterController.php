<?php

/**
* Registration
*
*/
class User_RegisterController extends FinalView_Controller_Action
{

    const SUCCESS_REGISTRATION_MESSAGE = 'SUCCESS_REGISTRATION_MESSAGE';

    private $_registerForm;

    public function registerAction()
    {
        if ($newUser = $this->_register()) {
            $newUser->save();

            $newUser->createConfirmation('registration');
            $this->sendRegistrationConfirmationMail($newUser);

            $this->_helper->redirector->gotoRoute(array(), 'UserAuthLogin');
        }
    }

    public function confirmationAction()
    {
        $user = Doctrine::getTable('User')->findOneByParams(array(
            'email'     =>  $this->getRequest()->getParam('email'),
            'role'      =>  $this->getRequest()->getParam('role'),
        ));

        $this->sendRegistrationConfirmationMail($user);
    }


    /**
    * Simple registration
    */
    protected function _register()
    {
        $this->view->form = $this->getForm();

        if ($this->getRequest()->isPost()) {
            if ($this->getForm()->isValid($this->getRequest()->getPost())) {

                $newUser = Doctrine::getTable('User')->create($this->getForm()->getValues());
                $newUser->role = Roles::USER_FRONTEND;

                return $newUser;
            }
        }
    }

    protected function getForm()
    {
        if (is_null($this->_registerForm)) {
        	$this->_registerForm = new User_Form_User_Register;
        }
        return $this->_registerForm;
    }

    protected function sendRegistrationConfirmationMail($user)
    {
        $mail = new FinalView_Mail(new FinalView_Mail_Template_Doctrine('user/registration-confirmation'), array(
            'email' => $user->email,
            'hash'  => $user->getConfirmation('registration')->hash,
        ));
        $mail->send($user->email, $user->email);
    }
}
