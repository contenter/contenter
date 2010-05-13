<?php

class FinalView_Auth_Adapter implements Zend_Auth_Adapter_Interface 
{
    
    const IDENTITY_NOT_FOUND = 'IDENTITY_NOT_FOUND'; 
    
    const AUTHORIZE_FAILED = 'AUTHORIZE_FAILED'; 
    
    /**
    * Verifying data
    * 
    * @var array
    */
    protected $_verifying_data;
    
    /**
    * Account
    * 
    * @var Doctrine_Record|bool
    */
    protected $_account;
    
    /**
    * Params to set into session storage after success login
    * 
    * @var array
    */
    protected $_storage_params;
    
    /**
    * Translator
    * 
    * @var Zend_Translate
    */
    protected $_translator;
    
    /**
    * Constructor
    * 
    * @param array $verifying_data
    * @param Doctrine_Record|bool $account
    * @param array $storage_params
    */
    public function __construct(array $verifying_data, $account, 
        array $storage_params = array('id')) 
    {
        $this->_verifying_data = $verifying_data;
        $this->_account = $account;
        $this->_storage_params = $storage_params;
        
        $this->_translator = Zend_Registry::get('Zend_Translate');
    }
    
    /**
     * Performs an authentication attempt
     *
     * @return Zend_Auth_Result
     */
    public function authenticate() 
    {
        try {
            $this->_authenticate();
        } catch(FinalView_Auth_Exception $e) {
            return new Zend_Auth_Result($e->getCode(), null, array($e->getMessage()));
        }
        
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, 
            (object)array_extract($this->_account->toArray(), $this->_storage_params));
    }
    
    /**
     * Performs an authentication attempt
     *
     * @throws FinalView_Auth_Exception 
     */
    protected function _authenticate() 
    {
        switch(false) 
        {
            case $this->_account : 
                throw new FinalView_Auth_Exception
                (
                    __(self::IDENTITY_NOT_FOUND), 
                    Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND
                );
                
            case FinalView_Auth_Encrypt::compare($this->_verifying_data['password'], 
                $this->_account->password) : 
                    throw new FinalView_Auth_Exception
                    (
                        __(self::AUTHORIZE_FAILED), 
                        Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID
                    );
        }
    }
    
}