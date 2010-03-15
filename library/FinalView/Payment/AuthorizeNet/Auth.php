<?php

class FinalView_Payment_AuthorizeNet_Auth extends FinalView_Payment_AuthorizeNet
{
    
    /**
    * User payment data
    * 
    * @var array
    */
    private $_payment_data = array();
    
    
    public function __construct(array $payment_data) 
    {
        $this->_payment_data = $payment_data;
    }
    
    /**
    * Get payment params
    * 
    * @return array
    */
    protected function _getPaymentParams() 
    {
        $auth_payment_map = array
        (
            // required
            'card_num', 
            'exp_date', 
            'card_code', 
            'amount', 
            
            // optional
            'first_name', 
            'last_name', 
            'city', 
            'state', 
            'country', 
            'zip', 
        );
        
        $params = array();
        
        foreach ($auth_payment_map as $param) {
            $params['x_' . $param] = 
                array_key_exists($param, $this->_payment_data) 
                ? $this->_payment_data[$param]
                : null;
        }
        
        return $params;
    }
    
    /**
    * Return current transaction type
    * 
    * @return string
    */
    protected function _getTransactionType() 
    {
        return self::AUTH;
    }
    
}