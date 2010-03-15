<?php

abstract class FinalView_Payment_AuthorizeNet 
{
    
    /**
    * Transaction types
    * 
    */
    const AUTH      = 'AUTH_ONLY';
    const CAPTURE   = 'PRIOR_AUTH_CAPTURE';
    const VOID      = 'VOID';
    
    /**
    * Mapping transactions types on methods
    * 
    * @var array
    */
    static private $_map = array
    (
        self::AUTH => 'auth', 
        self::CAPTURE => 'capture', 
        self::VOID => 'void', 
    );
    
    /**
    * Create payment transaction object and process transaction
    * 
    * @param string $type
    * @param mixed $payment_params
    */
    static public function factory($type, $payment_params) 
    {
        if (!in_array($type, array_keys(self::$_map))) {
            trigger_error('Unknown transaction type', E_USER_ERROR);
        }
        
        return 
            FinalView::factory(__CLASS__, self::$_map[$type], $payment_params)
            ->_proccess();
    }
    
    /**
    * Get payment params
    * 
    * @return array
    */
    abstract protected function _getPaymentParams();
    
    /**
    * Return current transaction type
    * 
    * @return string
    */
    abstract protected function _getTransactionType();
    
    /**
    * Handle request to the Authorize.Net. Return transaction response.
    * 
    * @return array
    */
    protected function _proccess() 
    {
        require_once LIBRARY_PATH . '/authorizenet.class.php';
        // create proccessor
        $processor = new authorizenet_class;
        
        // load it with params
        $params = $this->_getPaymentParams() + $this->_getRequestParams();
        foreach ($params as $param => $value) {
            $processor->add_field($param, $value);
        }
        
        // proccess transaction
        $code = $processor->process();
        
        // dummy: always tell about success transaction while capture
        if ($this->_getTransactionType() == self::CAPTURE && 
            $this->_config()->is_test_mode) 
        {
            $code = FinalView_Payment_Namespace::AUTHORIZENET_SUCCESS;
        }
        
        return 
        array
        (
            'remote_trans_id'   => $processor->response['Transaction ID'], 
            'type'              => $this->_getTransactionType(), 
            'response_code'     => $code, 
            'response_text'     => $processor->get_response_reason_text(), 
        );
    }
    
    /**
    * Return request params
    * 
    * @return array
    */
    protected function _getRequestParams() 
    {
        return 
        array
        (
                     'x_login' => $this->_config()->login,
                  'x_tran_key' => $this->_config()->tran_key,
                      'x_type' => $this->_getTransactionType(),
                   
                   'x_version' => '3.1',
                    'x_method' => 'CC', // credit card
              'x_test_request' => $this->_config()->is_test_mode ? 'TRUE' : 'FALSE',
            'x_relay_response' => 'FALSE',
                'x_delim_data' => 'TRUE',
                'x_delim_char' => '|',
                'x_encap_char' => '',
        );
    }
    
    /**
    * Return authorize.net payment config
    * 
    * @return Zend_Config_Ini
    */
    protected function _config() 
    {
        static $cach;
        
        if (is_null($cach)) {
            $file = APPLICATION_PATH . '/configs/payment.ini';
            if (file_exists($file) && is_file($file)) {
                $cach = new Zend_Config_Ini($file);
            } else {
                trigger_error('Set config to "' . APPLICATION_PATH . 
                    '/configs/payment.ini" path', E_USER_ERROR);
            }
        }
        
        return $cach->authorizenet;
    }
    
}