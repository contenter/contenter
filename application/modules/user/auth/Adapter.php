<?php

class User_Auth_Adapter extends FinalView_Auth_Adapter
{

    const AUTHORIZE_UNCONFIRMED = 'AUTHORIZE_UNCONFIRMED';    
    
    /**
     * Performs an authentication attempt
     *
     * @throws User_Auth_Exception 
     */
    protected function _authenticate() 
    {
        parent::_authenticate();
        
        if (!$this->_account->confirmed) {
            throw new User_Auth_Exception
            (
                __(self::AUTHORIZE_UNCONFIRMED, $this->_account->email, Roles::USER_FRONTEND),
                Zend_Auth_Result::FAILURE
            );
        }        
    }
    
}
