<?php
class Confirmation_UserController extends FinalView_Controller_Action
{
    public function forgotPasswordAction()
    {
        $confirmation = $this->_helper->confirmation();
        
        if ($this->getRequest()->getParam('reply') === Confirmation::ACTION_ACCEPT) {
                                    
            $user = $confirmation->Entity;
            $new_password = substr(md5($user->id . time()), 0, 8);            
            $user->password = $new_password;
            $user->save();
            
            $this->_sendNewPassword($user, $new_password);
            
            $this->view->reply = Confirmation::ACTION_ACCEPT;	
        }else{
            $this->view->reply = Confirmation::ACTION_DECLINE;
        }         
        
        $confirmation->delete();
    }
    
    public function _sendNewPassword($user, $new_password)
    {
        $mail = new FinalView_Mail('user/new-password', array(
            'email'     => $user->email, 
            'password'  => $new_password,
        ));
        $mail->send($user->email, $user->email);
    }
}
