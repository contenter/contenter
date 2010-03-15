<?php

abstract class FinalView_Payment_FirstData 
{
    
    /**
    * Transaction types
    * 
    */
    const PREAUTH   = 'Preauth';
    const POSTAUTH  = 'Postauth';
    const VOID      = 'Void';
    const SALE      = 'Sale';
    
    /**
    * Mapping transactions types on methods
    * 
    * @var array
    */
    static private $_map = array
    (
        self::PREAUTH   => 'preauth', 
        self::POSTAUTH  => 'postauth', 
        self::VOID      => 'void', 
        self::SALE      => 'sale', 
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
    * Handle request. Return transaction response.
    * 
    * @return array
    */
    protected function _proccess() 
    {
        require_once LIBRARY_PATH . '/lphp.php';
        
        $processor = new lphp;
        // in case of failure attempt to connect this mega lib return xml 
        // string without parsing it to array
        if (is_string($response = $processor->curl_process
        (
            $this->_getPaymentParams() + 
            $this->_getRequestParams()
        ))) 
        {
            // so parse it eventually
            $response = $processor->decodeXML($response);
        }
        
        return $response + array('type' => $this->_getTransactionType());
    }
    
    /**
    * Return request params
    * 
    * @return array
    */
    private function _getRequestParams() 
    {
        return 
        array
        (
            // Merchant Info Entity
            'host'          => $this->_config()->host, 
            'port'          => $this->_config()->port, 
            /*
                This field contains the path and filename of the digital 
                certificate (or PEM file) issued for a given store
            */
            'keyfile'       => $this->_config()->keyfile,
            /*
                This field should contain the merchant store name or store number, 
                which is generally a six- to ten-digit number assigned when the 
                account is set up. 
            */
            'configfile'    => $this->_config()->configfile,
            
            
            // orderoptions
            /*
                The type of transaction. The possible values are Sale, Preauth 
                (for an Authorize Only transaction), Postauth (for a Forced 
                Ticket or Ticket Only transaction), Void, Credit, 
                Calcshipping (for shipping charge calculations), 
                and Calctax (for sales tax calculations).
            */
            'ordertype' => $this->_getTransactionType(), 
            
            /*
                This field puts the account in live mode or test mode. Set to 
                Live for live mode, Good for an approved response in test mode, 
                Decline for a declined response in test mode, or Duplicate for a 
                duplicate response in test mode.
            */
            //'result' => $this->_config()->is_test_mode ? 'Good' : 'Live',
        );
    }
    
    /**
    * Return payment config
    * 
    * @return stdClass
    */
    private function _config() 
    {
        static $cach;
        
        if (is_null($cach)) {
            $application = new Zend_Application(
                APPLICATION_ENV, 
                APPLICATION_PATH . '/configs/application.ini'
            );
            $cach = (object)$application->getOption('firstdata');
        }
        
        return $cach;
    }
    
}
