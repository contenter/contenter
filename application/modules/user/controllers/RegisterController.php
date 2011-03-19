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
            $values->user_id = $newUser->id;
            $country =  Doctrine::getTable('GeoCountry')->findOneByParams(array(
                  'code'    =>  $this->getForm()->getValue('country')
            ));
            $values->country_id  = $country->id;

            $q = Doctrine_Query::create()
                -> select ('*,g.gateway_title,g.gateway_ident')
                -> from('GatewayCountries x')
                ->innerJoin('x.Gateway g')
                ->innerJoin('x.Country c')
                ->andWhere('g.status = ?', 1)
                ->andWhere('c.id = ?', $country->id);
            $gateways = $q->execute();

            $newUserSettings = Doctrine::getTable('UserSettings')->create($values);
            $newUserSettings->gateway_id = $gateways[0]->Gateway->id;
            $newUserSettings->save();
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
                $ip = $this->getRealIpAddr();
                $country_code = geoip_country_code_by_name ($ip);
        	$this->_registerForm = new User_Form_User_Register(array('ip'=>$ip,'country_code'=>$country_code));
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

   public function getRealIpAddr()
  {
   $client = $this->getRequest()->getServer('HTTP_CLIENT_IP');
   $forwarded = $this->getRequest()->getServer('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $ip = $this->getRequest()->getServer('HTTP_X_FORWARDED_FOR');
    } else if (!empty($client)) {
        $ip = $this->getRequest()->getServer('HTTP_CLIENT_IP');
    } else {
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');
    }
    $ip = '87.250.251.11'; // HARDCODE IP !!!
    return $ip;

 }

}
