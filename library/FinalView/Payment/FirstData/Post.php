<?php

/**
* Transaction depending on the previously performed transaction: Postauth (for a 
* Forced Ticket or Ticket Only transaction), Void, Credit
* 
*/
abstract class FinalView_Payment_FirstData_Post 
    extends FinalView_Payment_FirstData
{
    
    /**
    * For void, credit, and post-authorization transactions, 
    * this field must be a valid Order ID from a prior Sale or pre-authorization 
    * transaction.
    * 
    * @var string
    */
    private $_order_id;
    
    
    public function __construct($params) 
    {
        $this->_order_id = is_array($params) ? $params['order_id'] : $params;
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
            // Transaction Details
            
            /*
                Up to 100 numbers and letters including dashes and underscores, 
                but no other symbols.
            */
            'oid' => $this->_order_id,  
        );
    }
    
}