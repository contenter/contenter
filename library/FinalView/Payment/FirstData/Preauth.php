<?php

/**
* A credit card transaction that reserves funds on a customer's credit card. 
* Transaction does not charge the card until you perform a Ticket Only (Postauth 
* transaction) and confirm shipment of the order. 
* 
* Note: Authorizations reserve funds for varying periods, depending on the issuing 
* credit card company's policy. The period may be as little as three days or as long as 
* several months. For your protection it is recommended that you confirm shipment as 
* soon as possible after authorization.
*/
class FinalView_Payment_FirstData_Preauth extends FinalView_Payment_FirstData
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
            // ------------------ Credit Card Entity
            
            /*
                The customer's credit card number
            */
            'cardnumber', 
            /*
                The numeric expiration month of the credit card (2-digit number 
                01 to 12)
            */
            'cardexpmonth', 
            /*
                The two-digit expiration year of the credit card (2-digit number 
                00 to 99)
            */
            'cardexpyear', 
            /*
                3 or 4-digit numeric value printed on the front or back of the 
                credit card (3 or 4-digit number from 0000 to 9999)
            */
            'cvmvalue', 
            /*
                Indicates whether the card code was supplied. Possible values: 
                - provided 
                - not_provided 
                - illegible 
                - not_present 
                - no_imprint
            */
            'cvmindicator', 
            
            
            // ------------------ Payment Entity
            
            /*
                The total dollar amount of this transaction including subtotal, 
                tax, and shipping
            */
            'chargetotal', 
            
            
            // ------------------ Billing Entity
            
            
            /*
                * string - up to 96 numbers and letters only (no symbols)
                Required for AVS and fraud blocking
            */
            'name', 
            
            /*
                string - up to 96 numbers and letters only (no symbols)
            */
            'address1', 
            
            /*
                *
            */
            'address2', 
            
            /*
                string - up to 96 numbers and letters only (no symbols)
            */
            'state', 
            
            /*
                string - up to 5 numbers only (no symbols or spaces)
            */
            'zip', 
            
            /*
                string - up to 32 numbers and letters only (no symbols or spaces)
            */
            'phone', 
            
            
            
            // * - optional 
        );
        
        $params = array();
        
        foreach ($auth_payment_map as $param) {
            $params[$param] = 
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
        return self::PREAUTH;
    }
    
}