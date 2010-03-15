<?php

class FinalView_Payment_AuthorizeNet_Capture extends 
    FinalView_Payment_AuthorizeNet
{
    
    /**
    * User transaction id
    * 
    * @var integer
    */
    private $_trans_id;
    
    
    public function __construct($data) 
    {
        $this->_trans_id = $data;
    }
    
    /**
    * Get payment params
    * 
    * @return array
    */
    protected function _getPaymentParams() 
    {
        return 
        array
        (
            'x_trans_id' => $this->_trans_id,
        );
    }
    
    /**
    * Return current transaction type
    * 
    * @return string
    */
    protected function _getTransactionType() 
    {
        return self::CAPTURE;
    }
    
}